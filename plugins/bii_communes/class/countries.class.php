<?php

class countries extends bddcommune_items {

	protected $code;
	protected $alpha2;
	protected $alpha3;
	protected $langCS;
	protected $langEN;
	protected $langES;
	protected $langFR;
	protected $langIT;
	protected $langNL;

	static function getListeProprietes() {
		$array = array(
			"code" => "code",
			"alpha2" => "alpha2",
			"alpha3" => "alpha3",
			"langEN" => "Nom en",
			"langFR" => "Nom fr",
		);
		return $array;
	}

	static function filters_form_arguments($array_selected = array()) {
		?>
		<option class="nb" value="code" data-oldval="code" >code</option>

		<option class="text" value="alpha2" data-oldval="alpha2" >alpha2</option>
		<option class="text" value="alpha3" data-oldval="alpha3" >alpha3</option>
		<option class="text" value="langEN" data-oldval="langEN" >Nom en</option>
		<option class="text" value="langEN" data-oldval="langFR" >Nom fr</option>

		<?php
	}

	function nom() {
		$lang = "fr";
		if (get_option("bii_use_multilingual")) {
			$lang = apply_filters("bii_multilingual_current_language");
		}
		return $this->nom_lang($lang);
	}

	function nom_lang($lang) {
		$lang = strtoupper($lang);
		$method = "lang$lang";
		if (property_exists($this, $method)) {
			return $this->$method();
		}
		return $this->langEN();
	}

	public static function titre_page_admin_liste() {
		return "Pays";
	}

	public function option_value() {
		return $this->nom();
	}

	static function identifiant() {
		return "code";
	}

	public static function display_pagination() {
		return true;
	}

	public static function display_filter() {
		return true;
	}

	static function genOptionForm($where = "", $value = "", $method = "langEN") {
		$liste = static::all_id($where);
		$input = "";
		$lang = apply_filters("bii_multilingual_current_language",null);
		if($lang == "fr"){
			$method = "langFR";
		}
		
		foreach ($liste as $id_fk) {
			$item = new static($id_fk);
			$alpha3 = $item->alpha3();
			$input.= "<option value='$alpha3' ";
			if (is_array($value) && in_array($alpha3, $value)) {
				$input .= " selected='selected' ";
			} elseif ($value == $alpha3) {
				$input .= " selected='selected' ";
			}
			$input.= ">" . utf8_encode($item->$method()) . "</option> ";
		}
		return $input;
	}

}
