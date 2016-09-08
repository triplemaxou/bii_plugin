<?php

class bii_taille_vetements extends bii_calc {

	protected $sexe;
	protected $taille;
	protected $unit;
	protected $id;

	public static function getListeProprietesFormEdit() {
		$array = [
			"sexe" => "sexe",
			"taille" => "taille",
			"unit" => "unit"
		];

		return $array;
	}
	
	public static function texte_bouton() {
		$text = "Convertir";
		$lang = apply_filters("bii_multilingual_current_language", "");
		if ($lang == "en") {
			$text = "Convert";
		}
		return $text;
	}

	public function id() {
		$val = $this->id;
		if (!$val) {
			$val = rand(0, 1000);
		}
		return $val;
	}

	public function sexe() {
		$val = $this->sexe;
		if (!$val) {
			$val = "femme";
		}
		return $val;
	}

	public function taille() {
		return strtoupper($this->taille);
	}

	public function unit() {
		$val = $this->unit;
		if (!$val) {
			$lang = apply_filters("bii_multilingual_current_language", "");
			$val = "FR";
			if ($lang == "en") {
				$val = "US";
			}
		}
		return $val;
	}

	public function sexe_input() {
		$value = $this->sexe();
		$values = static::sexe_values();
		$id = $this->id();
		$vousetes = "Vous êtes";
		$lang = apply_filters("bii_multilingual_current_language", "");
		if ($lang == "en") {
			$vousetes = "You are";
		}
		?>
		<div class="stuffbox vc_col-xs-12 " id="sexe_div">
			<!--<h3><label><?= $vousetes; ?></label></h3>-->
			<div class="inside">
				<input name="sexe" id="sexe<?= $id; ?>" type="hidden" value="<?= $value; ?>">
				<?php
				foreach ($values as $key => $val) {
					$checked = "";
					if ($value == $key) {
						$checked = 'checked="checked"';
					}
					?>
					<div class="stuffbox vc_col-xs-4 ">
						<label for="inp-<?= $key ?><?= $id; ?>"><?= $val ?></label>
						<input id="inp-<?= $key ?><?= $id; ?>" type="checkbox" class="cbx-data-change" data-value="<?= $key ?>" data-change="sexe<?= $id; ?>" <?= $checked ?>/>
					</div>
					<?php
				}
				?>

				<p></p>
			</div>
		</div>
		<?php
	}

	public function taille_input() {
		$votretaille = "Votre taille";
		$lang = apply_filters("bii_multilingual_current_language", "");
		if ($lang == "en") {
			$votretaille = "Your clothing size";
		}
		?>
		<div class="stuffbox vc_col-xs-12 vc_col-sm-10 " id="taille_div">
			<div class="inside">
				<input name="taille" id="taille" class="form-control" placeholder="<?= $votretaille; ?>">
				<p></p>
			</div>
		</div>
		<?php
	}

	public function unit_input() {
		?>
		<div class="stuffbox vc_col-xs-12 vc_col-sm-10 " id="taille_div">
			<div class="inside">
				<select name="unit" id="unit" class="form-control" >
					<?php
					$tailles = static::unites_tableau();
					foreach ($tailles as $unite => $string) {
						?>
						<option value="<?= $unite ?>"><?= $string ?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<?php
	}

	public function calcul() {
		$nom_tableau = "tableau_" . $this->sexe;
		$tableau_unit = static::unites_tableau();
		$unit = $this->unit();
		$index_unit = 0;
		$currentunit = 0;
		$taille = $this->taille();
		$dsr = static::default_selected_reponse();
//		pre($tableau_unit);
		foreach ($tableau_unit as $key => $val) {
			if ($key == $unit) {
				$currentunit = $index_unit;
			}
			++$index_unit;
		}

		$tableau_taille = $this->$nom_tableau();
		$correspondance_ok = [];

//		pre($tableau_unit, "blue");
//		pre($currentunit, "blue");

		foreach ($tableau_taille as $correspondances) {
			if ($correspondances[$currentunit] == $taille) {
				$correspondance_ok[] = $correspondances;
			}
		}
//		pre($correspondance_ok, 'green');
		$nb = count($correspondance_ok);
		if ($nb) {
			$taillestmp = [];
			$tailles = [];
			foreach ($correspondance_ok as $cor) {
				$index = 0;
				foreach ($tableau_unit as $taille => $string) {
					if (isset($cor[$index]) && $cor[$index]) {
						$taillestmp[$taille][] = $cor[$index];
					}
					++$index;
				}
			}
			foreach ($taillestmp as $key => $val) {
				$tailles[$key] = array_unique($val);
			}
//			pre($tailles, "purple");
			?>
			<div class="bii_calc-tv-response bii-hide-or-see-container">
				<div class="vc_col-xs-12 ">
					<?php
					$tailles_select = [];
					foreach ($tailles as $taille => $values) {
						$tailles_select[] = $taille;
						$class = "hidden";
						if ($taille == $dsr) {
							$class = "";
						}

						$val = implode(", ", $values);
						$phrase = "La taille qui vous correspond est le $val";
						$nb = count($values);
						if ($nb > 1) {
							$phrase = "Les tailles qui vous correspondent sont les tailles suivantes : $val";
						}
						if ($taille == "cm") {
							$what = static::measure();
							$phrase = "Votre $what mesure " . $values[0] . " cm";
						}
						?>
						<div class="bii-hide-or-see taille-<?= $taille ?> <?= $taille ?> <?= $class ?>">
							<p><?= $phrase ?></p>

						</div>
						<?php
					}
					?>
				</div>
				<div class="vc_col-xs-12">
					<select id="unit-resp" class="form-control bii-hide-or-see-changer" >
						<?php
						$tailles = static::unites_tableau();
						foreach ($tailles as $unite => $string) {
							if (in_array($unite, $tailles_select)) {
								$selected = "";
								if ($unite == $dsr) {
									$selected = "selected='selected'";
								}
								?>
								<option value="<?= $unite ?>" <?= $selected ?>><?= $string ?></option>
								<?php
							}
						}
						?>
					</select>
				</div>
			</div>
			<?php
		} else {
			?>
			<p><?= static::nofound() ?></p>
			<?php
		}
	}

	public static function default_selected_reponse() {
		$lang = apply_filters("bii_multilingual_current_language", "");
		$value = "US";
		if ($lang == "en") {
			$value = "FR";
		}
		return $value;
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


		$values = ["homme" => $homme, "femme" => $femme,];
		return $values;
	}

	public static function nofound() {
		$texte = "Il n'y a pas de taille correspondant à votre choix";
		return $texte;
	}

	public static function measure() {
		return "";
	}

	public static function unites_tableau() {
		$lang = apply_filters("bii_multilingual_current_language", "");
		$inputfr = "Taille Française";
		$inputes = "Taille Espagnole";
		$inputru = "Taille Anglaise";
		$inputus = "Taille Américaine";
		$inputit = "Taille Italienne";
		$inputde = "Taille Allemande";
		$inputjp = "Taille Japonaise";
		$inputun = "Taille Universelle";
		if ($lang == "en") {
			$inputfr = "French size";
			$inputes = "Spanish size";
			$inputru = "English size";
			$inputus = "American size";
			$inputit = "Italian size";
			$inputde = "German size";
			$inputjp = "Japanese size";
			$inputun = "Universal size";
		}

		return [
			"FR" => $inputfr,
			"ES" => $inputes,
			"RU" => $inputru,
			"US" => $inputus,
			"IT" => $inputit,
			"DE" => $inputde,
			"JP" => $inputjp,
			"UN" => $inputun,
		];
	}

	public static function tableau_femme() {
		return [
			['34', '34', '6', '4', '38', '32', '3', "XS"],
			['36', '36', '8', '6', '40', '34', '5', "S"],
			['38', '38', '10', '8', '42', '36', '7', "M"],
			['40', '40', '12', '10', '44', '38', '9', "L"],
			['42', '42', '14', '12', '46', '40', '11', "XL"],
			['44', '44', '16', '14', '48', '42', '13', "XXL"],
			['46', '46', '18', '16', '50', '44', '15', "XXL"],
			['48', '48', '20', '18', '52', '46', '17', "XXXL"],
			['50', '50', '22', '20', '54', '48', '19', "XXXXL"],
		];
	}

	public static function tableau_homme() {
		return static::tableau_femme();
	}

	public static function tableau_enfant() {
		return [];
	}

}
