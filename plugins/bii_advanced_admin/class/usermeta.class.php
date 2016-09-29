<?php

class usermeta extends global_class {

	protected $umeta_id;
	protected $user_id;
	protected $meta_key;
	protected $meta_value;

	public static function nom_classe_bdd() {		
		if(defined('CUSTOM_USER_META_TABLE')){
			$nom_class = CUSTOM_USER_META_TABLE;
		}else{
			$nom_class = parent::nom_classe_bdd();
		}
		return $nom_class;
	}
	public static function prefix_bdd() {
		if (defined('CUSTOM_USER_META_TABLE')) {
			$prefix = "";
		} else {
			$prefix = parent::prefix_bdd();
		}
		return $prefix;
	}
	
	public static function identifiant() {
		return "umeta_id";
	}

	protected function value_ajax() {
		return $this->meta_key();
	}

	public static function add($user_id, $key, $value, $replace = true) {

		$id = 0;
		if (!static::exists($user_id, $value)) {
			$id = static::insertDefault($user_id, $key, $value);
			echo "new $id ";
			return $id;
		} else {
			$id = static::from_id_key($user_id, $key);
			if ($replace) {
				$value = ["user_id" => $user_id, "meta_key" => $key, "meta_value" => $value];

				$item = new static($id);
				$item->updateChamps($value);
				return $id;
			}
			return $value;
		}
	}

	public static function insertDefault($user_id, $key, $value) {
		$values = ["user_id" => $user_id, "meta_key" => $key, "meta_value" => $value];
//		echo "<br /><pre style='color:green'>";
//					var_dump($values);
//					echo "</pre><br />";
		$item = new static();
		$id = $item->insert();
		$item->updateChamps($values);
		return $id;
	}

	public static function exists($user_id, $value) {
		if(is_a($value, 'usermeta')){
			$value = $value->meta_value();
		}

		$bool = (bool) static::nb("user_id = '$user_id' AND meta_value = '$value'");
		return $bool;
	}

	
	
	public static function from_id_key($user_id, $key) {
		$liste = static::all_items("user_id = '$user_id' AND meta_key = '$key'");
//		pre("user_id = '$user_id' AND meta_key = '$key'");
		return $liste[0];
	}

	public static function multiple_from_id_key($user_id, $key) {
		$liste = static::all_id("user_id = '$user_id' AND meta_key = '$key'");
		return $liste;
	}

	public function liste_biens() {
		$where = $this->display_request();
//		pre($where,"green");
		$list = annonce::all_id($where);
//		pre($list,"red");
		return $list;
	}

	public function display_request() {
		$req = "0=1 ";


		if ($this->meta_key == "requete_sauvegardee") {
			$item = unserialize($this->meta_value);
			if ($item["sendmail"] == 1) {
				$req = $this->requestBuilder($item);
			}
		}
		return $req;
	}

	private function requestBuilder($item = array()) {
		$timestamp = registred_dates::fromkey("date_envoi_mail")->date_tmstp();
		$req = "is_archive = 0 ";
//		$req .=	"and date_maj > $timestamp ";
		$distance = $item["distance"];
		unset($item["max-price-all"]);
		unset($item["max-area-all"]);
		unset($item["ajoute"]);
		unset($item["distance"]);
		unset($item["sendmail"]);

		foreach ($item as $key => $value) {
			if ($value && $value != "any") {
				if (!is_array($value)) {
					$comparateur = "=";
					$aft = "\"";
					$bef = "\"";
					if (strpos($key, "min") !== false) {
						$comparateur = ">=";
					}
					if (strpos($key, "max") !== false) {
						$comparateur = "<=";
					}
					if ("keyword" == $key) {
						$this->keywordville($value, $comparateur, $aft, $bef, $distance);
					} elseif ("bathrooms" == $key) {
						$comparateur = ">=";
					}
					$trad = annonce::trad_rs($key);
					$req.= "AND $trad $comparateur $bef$value$aft ";
				} else {
					foreach ($value as $val) {
						$comparateur = ">";
						$valuesql = "0";
						$rep = $val;
						if (strpos($val, "type-de-chauffage") !== false) {
							$comparateur = "like";
							$rep = "type-de-chauffage";
							$valuesql = "'%" . str_replace("type-de-chauffage-", "", $val) . "%'";
						} elseif (strpos($val, "plain-pied-de-vie") !== false) {
							$comparateur = "like";
							$rep = "plain-pied-de-vie";
							$valuesql = "'%de vie%'";
						} elseif (strpos($val, "plain-pied") !== false) {
							$comparateur = "like";
							$rep = "plain-pied-de-vie";
							$valuesql = "'%plain pied%'";
						} /* elseif (strpos($val, "mitoyennete") !== false) {
						  $rep = "mitoyennete";
						  $replace = str_replace("mitoyennete-", "", $val);
						  if ($replace == "non") {
						  $comparateur = "in";
						  $valuesql = "('','non','Indépendant')";
						  } else {
						  $comparateur = ">";
						  }
						  } // */
						$trad = annonce::trad_rs($rep);

						$req.= "AND $trad $comparateur $valuesql ";
					}
				}
			}
		}
		return $req;
	}

	private function keywordville(&$value, &$comparateur, &$aft, &$bef, $distance = 0) {
		$comparateur = "=";
		$ville = villes_france::fromNom($value);
		if ($distance == 0) {
			$value = $ville->codeInsee();
		} else {
			$comparateur = "IN";
			$aft = "";
			$bef = "";
			$value = "(";
			$autour = $ville->insee_autour($distance);
			$sep = "";
			foreach ($autour as $insee) {
				$value .= "$sep'$insee'";
				$sep = ",";
			}
			$value .= ")";
		}
	}

	static function supprimable_ajax($id) {
		$item = new static($id);
		$datatocheck = $item->meta_key();
//		pre($item);
		if ($datatocheck == "requete_sauvegardee" || utf8_encode($datatocheck) == "requete_sauvegardee"|| utf8_decode($datatocheck) == "requete_sauvegardee" ) {
			return true;
		}
		return false;
	}

	public function getID_from_user_and_value($user_id, $value) {
		$liste = static::all_id("user_id = '$user_id' AND meta_value = '$value'");
		return $liste[0];
	}

	function alerteMail($value = 1) {
		if ($this->value_ajax() == "requete_sauvegardee") {

			if ($value != 1) {
				$value = 0;
			}
			$userval = $this->meta_value();
			$us = unserialize($userval);
			$us["sendmail"] = $value;
			$userval = serialize($us);
//			echo $userval;
			$idadd = static::add($this->user_id, $this->meta_key, $userval, true);
			static::deleteStatic($this->id());
			return $idadd;
		}
	}

}
