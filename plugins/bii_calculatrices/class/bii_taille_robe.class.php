<?php

class bii_taille_robe extends bii_taille_vetements {

	public static function unites_tableau() {
		return [
			"EU" => "Taille Européenne",
			"UK" => "Taille Anglaise",
			"US" => "Taille Américaine",
			"GE" => "Taille Allemagne, Autriche, Pays bas, Suède",
			"IT" => "Taille Italienne"];
	}

	public static function tableau_femme() {
		return [
			['32', '4', '0', 30, 36],
			['34', '6', '2', 32, 38],
			['36', '8', '4', 34, 40],
			['38', '10', '6', 36, 42],
			['40', '12', '8', 38, 44],
			['42', '14', '10', 40, 46],
			['44', '16', '12', 42, 48],
			['46', '18', '14', 44, 50],
			['48', '20', '16', 46, 52],
			['50', '22', '18', 48, 54],
			['52', '24', '20', 50, 56],
		];
	}

	public static function sexe_values() {
		$homme = "Homme";
		$femme = "Femme";
		$enfant = "Enfant";
		$lang = apply_filters("bii_multilingual_current_language", "");
		if ($lang == "en") {
			$homme = "Man";
			$femme = "Woman";
			$enfant = "Child";
		}


		$values = [ "femme" => $femme];
		return $values;
	}

}
