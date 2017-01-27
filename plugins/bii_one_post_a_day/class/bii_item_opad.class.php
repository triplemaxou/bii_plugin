<?php

class bii_item_opad extends global_class {

	protected $date_insert;
	protected $date_modification;
	

	//<editor-fold desc="Dates">
	

	protected function date_format($property, $format = "d/m/Y") {
		$retour = "";
		if (property_exists($this, $property)) {
			if ($this->$property != 0) {
				$retour = date($format, $this->$property);
			}
		}
		return $retour;
	}

	protected function date_format_langue($property, $lang = "fr") {
		$retour = "";

		if (property_exists($this, $property)) {
			if ($this->$property != 0) {
				// $retour = date("d/m/Y",$this->date_contact);
				$liste_mois = static::liste_mois($lang);
				$jour = date("j", $this->$property);
				$an = date("Y", $this->$property);
				$mois = $liste_mois[date("n", $this->$property)];

				if ($lang == "fr") {
					$cardinal = "";
					if ($jour == 1) {
						$cardinal = "<sup>er</sup>";
					}
					$retour = $jour . $cardinal . " " . $mois . " " . $an;
				} else {
					$cardinal = "<sup>th</sup>";
					if ($jour == 1 || $jour == 21 || $jour == 31) {
						$cardinal = "<sup>er</sup>";
					}
					if ($jour == 2 || $jour == 22) {
						$cardinal = "<sup>nd</sup>";
					}
					if ($jour == 3 || $jour == 23) {
						$cardinal = "<sup>rd</sup>";
					}
					$retour = $mois . ", " . $jour . $cardinal . " " . $an;
				}
			}
		}
		return $retour;
	}

	function date_modification() {
		return $this->date_format("date_modification");
	}

	function heure_modification() {
		return $this->date_format("date_modification", "H:i:s");
	}

	function date_modification_tmstp() {
		return $this->date_modification;
	}

	function date_modification_fr() {
		return $this->date_format_langue("date_modification", "fr");
	}

	function date_insert() {
		return $this->date_format("date_insert");
	}

	function heure_insert() {
		return $this->date_format("date_insert", "H:i:s");
	}

	function date_insert_tmstp() {
		return $this->date_insert;
	}

	function date_insert_fr() {
		return $this->date_format_langue("date_insert", "fr");
	}

//</editor-fold>


	public static function autoTable($is_autoinserted = false) {
		$scriptSQL = "";
		if (!static::onBase()) {
			$class_name = static::nom_classe_bdd();
			$prefix = static::prefix_bdd();
			$item = new static();
			$tab = $item->tabPropValeurs();
			$scriptSQL = "CREATE TABLE IF NOT EXISTS `$prefix$class_name` (";
			$virg = "";
			$identifiant = static::identifiant();
			foreach ($tab as $prop => $val) {
				$scriptSQL.= $virg;
				$scriptSQL .= static::typeBDD($prop);
				$virg = ",";
			}
			$scriptSQL .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
			$scriptSQL .= "ALTER TABLE `$prefix$class_name` ADD PRIMARY KEY (`$identifiant`);";
			$scriptSQL .= "ALTER TABLE `$prefix$class_name` MODIFY `$identifiant` int(11) NOT NULL AUTO_INCREMENT;";
			if ($is_autoinserted) {
				$pdo = static::getPDO();
				$pdo->query($scriptSQL);
				bii_custom_log("[TABLE CREATED] $scriptSQL");
				update_option("bii_table_" + static::nom_classe_bdd() + "_created", "1");
			}
		}
		return $scriptSQL;
	}

	public static function alterTable($prop, $operation = "CHANGE", $is_autoinserted = false) {
		$scriptSQL = " ";

		$class_name = static::nom_classe_bdd();
		$prefix = static::prefix_bdd();

		if ($operation == 'CHANGE') {
			//ALTER TABLE `msac_bii_vendeur` CHANGE `pdo_host` `pdo_host` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
			$null = " NULL";
			$charset = " CHARACTER SET latin1 COLLATE latin1_swedish_ci";
			$scriptSQL.= "ALTER TABLE `$prefix$class_name` CHANGE `$prop`";
			$scriptSQL .= static::typeBDD($prop, false, $null, false, $charset);
			$scriptSQL .= $charset . $null;
			if (" NULL" == $null) {
				$scriptSQL .= " DEFAULT NULL";
			}
			if ($prop == static::identifiant()) {
				$scriptSQL .= " AUTO_INCREMENT";
			}
			$scriptSQL .= "; ";
		}
		if ($operation == 'ADD') {
			//ALTER TABLE `msac_bii_vendeur` ADD `test` INT NOT NULL ;
			$scriptSQL.= "ALTER TABLE `$prefix$class_name` ADD " . static::typeBDD($prop) . "; ";
		}
		if ($operation == "DROP") {
			//ALTER TABLE `msac_bii_vendeur` DROP `test`;
			$scriptSQL.= "ALTER TABLE `$prefix$class_name` DROP `$prop`; ";
		}
		if ($is_autoinserted) {
			$pdo = static::getPDO();
			$pdo->query($scriptSQL);
		}

		return $scriptSQL;
	}

	public static function updateTable($is_autoinserted = false) {
		$scriptSQL = "";

		$class_name = static::nom_classe_bdd();
		$prefix = static::prefix_bdd();
		$item = new static();
		$tab = $item->tabPropValeurs();
		$req = "SHOW COLUMNS FROM `$prefix$class_name`";
		$pdo = static::getPDO();
		$select = $pdo->query($req);
		$fieldonbase = [];
		while ($row = $select->fetch()) {
			$fieldonbase[] = $row["Field"];
		}

		$propinserted = [];
		foreach ($tab as $prop => $val) {
			$null = " NULL";
			$charset = " CHARACTER SET latin1 COLLATE latin1_swedish_ci";
			if (in_array($prop, $fieldonbase)) {
				$scriptSQL .= static::alterTable($prop, "CHANGE", false);
			} else {
				$scriptSQL .= static::alterTable($prop, "ADD", false);
			}
			$propinserted[] = $prop;
		}
		foreach ($fieldonbase as $field) {
			if (!in_array($field, $propinserted)) {
				$scriptSQL .= static::alterTable($prop, "DROP", false);
			}
		}

		if ($is_autoinserted) {
			$pdo = static::getPDO();
			$pdo->query($scriptSQL);
		}

		return $scriptSQL;
	}

	protected static function typeBDD($prop, $addnull = true, &$null = " NULL", $addcharset = false, &$charset = " CHARACTER SET latin1 COLLATE latin1_swedish_ci") {
		$identifiant = static::identifiant();
		if (!$addcharset) {
			$charset = "";
		}
		$scriptSQL = " ";
		if ($prop == $identifiant) {
			$scriptSQL.= "`$identifiant` int(11)";
			$null = " NOT NULL";
			$charset = "";
		} else {
			if (strpos($prop, "id_") !== false || strpos($prop, "nb_") !== false || strpos($prop, "date_") !== false) {
				$scriptSQL .= "`$prop` int(11)";
				$charset = "";
			} elseif (strpos($prop, "is_") !== false) {
				$scriptSQL .= "`$prop` tinyint(1)";
				$charset = "";
			} elseif (strpos($prop, "prix_") !== false) {
				$scriptSQL .= "`$prop` double";
				$charset = "";
			} elseif (strpos($prop, "contenu") !== false) {
				$scriptSQL .= "`$prop` text";
			} else {
				$scriptSQL .= "`$prop` varchar(255)";
			}
		}
		if ($addnull) {
			$scriptSQL .= $null;
		}
		if ($addcharset) {
			$scriptSQL .= $charset;
		}
		return $scriptSQL;
	}

	public static function attr_tri_unique() {
		return static::identifiant();
	}

	public static function exists($id) {
		$where = static::attr_tri_unique() . " = '" . $id . "'";
		$nb = static::nb($where);
		if ($nb) {
			return true;
		}
		return false;
	}

	public static function onBase() {
		return get_option("bii_table_" + static::nom_classe_bdd() + "_created");
	}

	static function classe_heritee() {
		$class = "bii_item_opad";
		if (get_called_class() == $class) {
			$class = "global_class";
		}
		return $class;
	}

	function updateChamps($value, $champs = false, $where = false, $callsingle = false) {
		if ($callsingle) {
			parent::updateChamps($value, $champs, $where);
		} else {
			$new_value = $this->before_updateChamps($value, $champs, $where, true);
			parent::updateChamps($new_value, $champs, $where);
			$this->after_updateChamps($new_value, $champs, $where, true);
		}
	}

	function before_updateChamps($value, $champs = false, $where = false, $callsingle = true) {
		return $value;
	}

	function after_updateChamps($value, $champs = false, $where = false, $callsingle = true) {
		
	}


	function date_insert_inputIA() {
		
	}

	function date_modification_inputIA() {
		
	}

	function modifie_par_inputIA() {
		
	}

	function instance_inputIA() {
		
	}

	public function contenu_inputIA() {
		$contenu = $this->contenu();
		?>
		<div id="contenu_div" class="stuffbox <?= static::default_class_stuff(); ?> ">
			<h3><label for="contenu"><span class='fa fa-book'></span> Contenu</label><button class="btn btn-info enroule"><i class="fa fa-minus-square"></i></button></h3>
			<div class="inside">
				<?php wp_editor(utf8_encode($contenu), "contenu"); ?>
			</div>
		</div>
		<?php
	}


	static function whereDefault() {
		$where = parent::whereDefault();
	}


}
