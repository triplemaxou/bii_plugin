<?php

class villes_france extends bddcommune_items {

	protected $ville_id;
	protected $ville_departement;
	protected $ville_slug;
	protected $ville_nom;
	protected $ville_nom_simple;
	protected $ville_nom_reel;
	protected $ville_nom_soundex;
	protected $ville_nom_metaphone;
	protected $ville_code_postal;
	protected $ville_commune;
	protected $ville_code_commune;
	protected $ville_arrondissement;
	protected $ville_canton;
	protected $ville_amdi;
	protected $ville_population_2010;
	protected $ville_population_1999;
	protected $ville_population_2012;
	protected $ville_densite_2010;
	protected $ville_surface;
	protected $ville_longitude_deg;
	protected $ville_latitude_deg;
	protected $ville_longitude_grd;
	protected $ville_latitude_grd;
	protected $ville_longitude_dms;
	protected $ville_latitude_dms;
	protected $ville_zmin;
	protected $ville_zmax;
	protected $ville_population_2010_order_france;
	protected $ville_densite_2010_order_france;
	protected $ville_surface_order_france;
	protected $ville_population_2010_order_dpt;
	protected $ville_densite_2010_order_dpt;
	protected $ville_surface_order_dpt;

	static function identifiant() {
		return "ville_id";
	}

	static function getListeProprietes() {
		$array = array(
			"ville_code_commune" => "Code Insee",
			"ville_nom_reel" => "Nom",
			"ville_departement" => "Département",
			"ville_code_postal" => "Code Postal",
			"ville_longitude_deg" => "Longitude",
			"ville_latitude_deg" => "Latitude",
			"ville_population_2012" => "Population",
			"ville_surface" => "Surface",
		);
		return $array;
	}

	static function filters_form_arguments($array_selected = array()) {
		?>
		<option class="nb" value="ville_code_commune" data-oldval="ville_code_commune" >Code insee</option>
		<option class="text" value="ville_nom_reel" data-oldval="ville_nom_reel" >Nom</option>
		<option class="nb" value="ville_departement" data-oldval="ville_departement" >Département</option>
		<option class="text" value="ville_code_postal" data-oldval="ville_code_postal" >Code postal</option>
		<option class="nb" value="ville_latitude_deg" data-oldval="ville_longitude_deg" >Latitude</option>
		<option class="nb" value="ville_longitude_deg" data-oldval="ville_longitude_deg" >Longitude</option>
		<?php
	}

	function nom() {
		return $this->ville_nom_reel;
	}

	public static function titre_page_admin_liste() {
		return "Villes";
	}

	public static function feminin() {
		return true;
	}

	public function option_value() {
		return $this->nom();
	}

	public static function nomXml() {
		return "agence";
	}

	public static function display_pagination() {
		return true;
	}

	public static function display_filter() {
		return true;
	}

	public function lat() {
		return $this->ville_latitude_deg;
	}

	public function long() {
		return $this->ville_longitude_deg;
	}

	public function villes_autour_id($nbkm = 0) {
		if ($nbkm > 0) {
			$nom_bdd = $this->nom_classe_bdd();
			$latslug = "ville_latitude_deg";
			$longslug = "ville_longitude_deg";

			$formule = "(6366*acos(cos(radians(" . $this->lat() . "))*cos(radians($nom_bdd.$latslug))*cos(radians($nom_bdd.$longslug) -radians(" . $this->long() . "))+sin(radians(" . $this->lat() . "))*sin(radians($nom_bdd.$latslug))))";
			$req = $formule . " <= $nbkm";
//			debugEcho($req);
			return static::all_id($req);
		} else {
			return [$this->id()];
		}
	}

	public function villes_autour($nbkm = 0) {
		$liste = $this->villes_autour_id($nbkm);
		$liste_item = [];
		foreach ($liste as $id) {
			$liste_item[] = new static($id);
		}
		return $liste_item;
	}
	
	public function insee_autour($nbkm = 0){
		$liste = $this->villes_autour_id($nbkm);
		$liste_item = "";
		foreach ($liste as $id) {
			$item = new static($id);
			$liste_item[] = $item->codeInsee();
		}
//		var_dump($liste_item);
		return $liste_item;
	}

	public function liste_villes_autour($nbkm = 0, $is_likeComparator = false, $parenthesis = true) {
		$liste = $this->villes_autour_id($nbkm);
		$liste_item = "";
		if ($parenthesis) {
			$liste_item = "(";
		}
		$sep = "";
		foreach ($liste as $id) {
			$item = new static($id);
			$liste_item.= $sep;
			$liste_item.= $item->getNomForListe($is_likeComparator);
			$sep = ",";
		}
		if ($parenthesis) {
			$liste_item .= ")";
		}
		return $liste_item;
	}

	public static function fromCodeInsee($code) {
		$where = "ville_code_commune = '$code'";
		$liste = static::all_id($where);
		$item = new static();
		foreach ($liste as $id) {
			$item = new static($id);
		}
		return $item;
	}

	public static function fromNom($nom) {
		$nom = trim($nom);
		$nom_maj = stripAccentsToMaj($nom);
		$nom_min = stripAccents(strtolower($nom));
		$nom_min = str_replace("-", " ", $nom_min);
		$nom_min = str_replace("'", " ", $nom_min);
		$nom_min = str_replace("st ", "saint ", $nom_min);
		$nom_min = str_replace("ste ", "sainte ", $nom_min);


		$where = "ville_departement IN (76,27,14) AND (ville_nom LIKE \"%$nom_maj%\" or ville_nom_simple LIKE \"%$nom_min%\" or ville_code_postal LIKE \"%$nom%\" )";
//		echo $where;
		$liste = static::all_id($where);
		if(count($liste) == 0){
			throw new Exception;
		}
		foreach ($liste as $id) {
			$item = new static($id);
		}
		return $item;
	}

	public function getNomForListe($is_likeComparator = false) {
		$nom = utf8_encode($this->nom());

		$nom_nopct = strtoupper($nom);
		$nom_nopct = str_replace("-", " ", $nom_nopct);
		$nom_nopct = str_replace("'", " ", $nom_nopct);
		$nom_maj = strtoupper($nom);
		$nom_ssacc = strtoupper(stripAccents($nom));


		$c = "";
		if ($is_likeComparator) {
			$c = "%";
		}

		$cps = $this->codesPostaux();
		$sep = "";
		$string = "";
		$arr_val = ["nom_nopct", "nom_maj", "nom_ssacc"];
		foreach ($cps as $cp) {
			foreach ($arr_val as $val) {
				$string .=$sep . $c . $$val . " ($cp)" . $c;
				$sep = ",";
			}
		}

		return $string;
	}

	public function codesPostaux() {
		$cp = $this->ville_code_postal;
		return explode("-", $cp);
	}
	
	public function codeInsee(){
		return $this->ville_code_commune;
	}

	static function nom_classe_admin(){
		return "Ville";
	}
	
	static function getDepartement(){
		return new departement_france($this->ville_departement);
	}
	static function getRegion(){
		return $this->getDepartement()->get_ancienne_region();
	}
	
}
