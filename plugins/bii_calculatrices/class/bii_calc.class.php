<?php

abstract class bii_calc {

	function __construct($post) {
		foreach ($post as $key => $val) {
			$this->$key = $val;
		}
	}

	public static function afficher_calculatrice() {
		$item = new static([]);
		echo $item->form_edit();
		
	}

	public static function afficher_resulatat($post) {
		$item = new static($post);
		$resultat = $item->calcul($post);
		return $resultat;
	}

	static function texte_bouton(){
		return "Calculer";
	}
	
	function calcul() {
		
	}

	static function getListeProprietesFormEdit() {
		$item = new static();
		$liste = array();
		$tabprop = $item->tabPropValeurs();
		foreach ($tabprop as $prop => $value) {
			$liste[$prop] = $prop; //Value est forcément = 0, on le le récupère pas
		}
		return $liste;
	}

	function tabPropValeurs() {
		$array = get_object_vars($this);
//		unset($array["class_name"]);
		return $array;
	}

	function input($champ, $nom_champ = "", $value = 0, $options = array()) {
		$method_class = $champ . "_class_stuff";
		if (method_exists($this, $method_class)) {
			$class_stuff = $this->$method_class();
		} else {
			$class_stuff = static::default_class_stuff();
		}
		$method_champ = $champ . "_input";
		if (method_exists($this, $method_champ)) {
			$input = $this->$method_champ();
			$label = "";
		} else {
			$class = "";
			if ($nom_champ == "") {
				$nom_champ = $champ;
			}
			$nom_champ = ucfirst($nom_champ);
			if (isset($options["class"])) {
				if (is_array($options["class"])) {
					foreach ($options["class"] as $item) {
						$class.= $item;
					}
				} else {
					$class = $options["class"];
				}
			}
			$description = "<p>";
			if (isset($options["description"])) {
				$description .= $options["description"];
			}
			$description .= "</p>";

			$div = '<div id="' . $champ . '_div" class="stuffbox ' . $class_stuff . ' ">';

			$label = $div . "<h3><label for='$champ'>$nom_champ</label></h3>";
			$input = "<div class='inside' >";

			if (strpos($champ, "id_") !== false && $champ != "id_analytics") {
				$fk = substr($champ, 3);
				if (class_exists($fk)) {
					$input .= "<select id='$champ' name='$champ' class='form-control $class' >";
					foreach ($fk::all_id() as $id_fk) {
						$item = new $fk($id_fk);
						$input.= "<option value='$id_fk' ";
						if ($value == $id_fk) {
							$input .= " selected='selected' ";
						}
						$input.= ">" . utf8_encode($item->option_value()) . "</option> ";
					}
					$input .= "</select>";
				}
			} elseif (strpos($champ, "is_") !== false) {
				$checked = "";
				if ($value == 1) {
					$checked = "checked='checked'";
				}
				$input .= "<input type='hidden'  id='$champ' name='$champ' value='$value' />";
				$input .= "<input type='checkbox'  id='$champ-cbx' name='$champ-cbx' class='cbx-data-change form-control' data-change='$champ' $checked />";
			} else {
				$type = "type='text'";
				$value = utf8_encode($value);
				if (strpos($champ, "nb_") !== false) {
					$type = "type='number'";
				}
				$input .= "<input id='$champ' name='$champ' class='form-control $class' $type value='$value' />  ";
			}
			if (strpos($champ, "prix") !== false) {
//				$input .= "&euro;";
			}
			$input.= $description;



			$input .= "</div>";
			$input .= "</div>";
			if (isset($options["separator"])) {
				$input.=$options["separator"];
			}
		}

		if (isset($options["echo"])) {
			echo $label . $input;
		}
		return $label . $input;
	}

	function form_edit($do_not_display = array()) {
		$liste_prop = static::getListeProprietesFormEdit();

		if (isset($liste_prop["image"])) {
			//On met $liste_prop["image"] en dernière position
			$val = $liste_prop["image"];
			unset($liste_prop["image"]);
			$liste_prop["image"] = $val;
		}
//		var_dump($liste_prop);
		unset($liste_prop["id"]);
		$i = 1;
//		pre($liste_prop);
		foreach ($liste_prop as $prop => $val) {
			if (!in_array($prop, $do_not_display)) {
				$options = array("echo" => 1);
				if (strpos($prop, "date") !== false) {
					$options["class"] = "datepicker";
				}
				if (strpos($prop, "couleur") !== false) {
					$options["class"] = "input-colorpicker";
				}
				if ($i % 2 == 0) {
//					$options["separator"] = "<div class='clearfix'></div>";
				}
//				pre($prop);
				$this->input($prop, $val, $this->$prop(), $options);
			}
			$i++;
		}
	}

	function __call($name, $arguments) {
		//setteur    	
		if (property_exists($this, $name)) { //getteur
			$varname = strtolower($name);
			return $this->{$varname};
		} else {
			throw new Exception('Mauvaise méthode. (gueteur) : ' . $name, 500);
		}
	}

	protected static function default_class_stuff() {
		return "vc_col-xs-12";
	}

}
