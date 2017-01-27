<?php

class departement_france extends bddcommune_items {

	protected $id;
	protected $numero;
	protected $nom;
	protected $nom_uppercase;
	protected $nom_soundex;
	protected $slug;
	protected $id_region;
	protected $taux_detpf;
	protected $map;

	static function getListeProprietes() {
		$array = array(
			"numero" => "Numéro",
			"nom" => "Nom",
		);
		return $array;
	}
	
	static function nom_classe_bdd() {
		return "departement";
	}

	static function nom_classe_admin() {
		return "département";
	}

	function get_ancienne_region() {
		return new ancienne_region_france($this->id_region);
	}

	function option_value(){
		return $this->numero." - ".$this->nom();
	}

	
	static function genOptionForm($where = "", $value = "",$method = "option_value") {
		$liste = static::all_id($where);
		$input = "";
		foreach ($liste as $id_fk) {
			$item = new static($id_fk);
			$input.= "<option value='$item->numero' ";
			if (is_array($value) && in_array($id_fk, $value)) {
				$input .= " selected='selected' ";
			} elseif ($value == $id_fk) {
				$input .= " selected='selected' ";
			}
			$input.= ">" . $item->$method() . "</option> ";
		}
		return $input;
	}
}
