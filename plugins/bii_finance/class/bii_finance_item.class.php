<?php


class bii_finance_item extends global_class {

	protected $date_insert;
	protected $date_modification;

	
	static function getPDO() {
		return ppdo::getInstance();
	}

	static function prefix_bdd() {
		return "";
	}
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
		$class = "bii_migla_items";
		if (get_called_class() == $class) {
			$class = "global_class";
		}
		return $class;
	}

	function modifie_par_default() {

		$par = "admin";

		return $par;
	}

	function date_insert_inputIA() {
		
	}

	function date_modification_inputIA() {
		
	}

}
