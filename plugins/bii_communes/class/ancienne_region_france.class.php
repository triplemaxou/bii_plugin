<?php
class ancienne_region_france extends bddcommune_items {
	protected $id;
	protected $appartenance;
	protected $nom;
	protected $titre_google;
	protected $map;
	
	
	static function getListeProprietes() {
		$array = array(
			"nom" => "Nom",
		);
		return $array;
	}
	
	static function feminin() {
		return true;
	}
	static function nom_classe_admin() {
		return "région";
	}
	
	static function nom_classe_bdd() {
		return "region";
	}
}