<?php

class bii_calcul_puissance_chauffage extends bii_calc {

	protected $surfsol = 25;
	protected $hauteur = 2.5;
	protected $dept = 76;
	protected $type_isolation = "forte";
	protected $altitude = 20;
	protected $is_sdb = 0;
	protected $is_chambre = 0;

	public static function getListeProprietesFormEdit() {
		$array = [
			"surfsol" => "Surface au sol",
			"hauteur" => "Hauteur Moyenne sous plafond",
			"dept" => "Département d'habitation",
			"type_isolation" => "Type d'isolation",
			"altitude" => "Altitude",
			"is_sdb" => "La pièce est elle une salle de bains, ou a-t-elle une surface importante de vitres (véranda) ?",
			"is_chambre" => "La pièce est elle une chambre à coucher ?",
		];

		return $array;
	}

	public function dept_input() {
		$depts = departement_france::genOptionForm("numero in('76','27','14','60','80','50','61')", $this->dept);
		?>
		<div class="stuffbox vc_col-xs-12 " id="dept_div">
			<h3><label for="dept">Département d'habitation</label></h3>
			<div class="inside">
				<select class="form-control " name="dept" id="dept"><?= $depts; ?></select>
				<p></p>
			</div>
		</div>
		<?php
	}

	public function type_isolation_input() {
		$types = ["RT2012","forte", "moyenne", "faible"];
		?>
		<div class="stuffbox vc_col-xs-12 " id="type_isolation_div">
			<h3><label for="type_isolation">Isolation</label></h3>
			<div class="inside">
				<select class="form-control " name="type_isolation" id="type_isolation">
					<?php
					foreach ($types as $type) {
						$selected = "";
						if ($type == $this->type_isolation) {
							$selected = "selected='selected'";
						}
						?><option value="<?= $type ?>" <?= $selected; ?>><?= ucfirst($type); ?></option><?php
					}
					?>
				</select>
				<p></p>
			</div>
		</div>
		<?php
	}

	public function calcul() {

		$volume = $this->volume();
		$wm3 = $this->wattmetrecube();
		$maj = $this->majoration_altitude() * $this->majoration_sdb();
		$min = $this->minoration_chambre();
		$resultat = $volume * $wm3 * $maj * $min;
		return "<p>Le radiateur pour cette pièce devrait avoir une puissance de " . $resultat . " W</p>";
	}

	public function volume() {
		return $this->surfsol * $this->hauteur;
	}

	public function wattmetrecube() {
		$zone_climatique = $this->zone_climatique_simplifiee();
		switch ($zone_climatique) {
			case 1: $wm3 = 35;
				break;
			case 2: $wm3 = 38;
				break;
			case 3: $wm3 = 42;
				break;
			case 4: $wm3 = 45;
				break;
		}
		
		$wm3+= 5;
		if ($this->type_isolation == "forte") {
			return $wm3;
		}
		$wm3+= 5;
		if ($this->type_isolation == "moyenne") {
			return $wm3;
		}
		$wm3+= 5;
		if ($this->type_isolation == "RT2012") {
			switch ($zone_climatique) {
				case 1: $wm3 = 15;
					break;
				case 2: $wm3 = 18;
					break;
				case 3: $wm3 = 22;
					break;
				case 4: $wm3 = 25;
					break;
			}
			return $wm3;
		}
		return $wm3;
	}

	public function zone_climatique_simplifiee() {
		$zone = "2";
		$dept = substr($this->dept, 0, 2);
		if ($dept == 50) {
			$zone = "1";
		}
		return $zone;
	}

	public function majoration_altitude() {
		$majoration = 1;
		if ($this->altitude > 500) {
			$nbtranche500 = floor($this->altitude / 500);
			for ($i = 1; $i <= $nbtranche500; ++$i) {
				$majoration*= 1.1;
			}
		}
		return $majoration;
	}

	public function majoration_sdb() {
		$majoration = 1;
		if ($this->is_sdb) {
			$majoration = 1.1;
		}
		return $majoration;
	}

	public function minoration_chambre() {
		$minoration = 1;
		if ($this->is_chambre) {
			$minoration = 0.95;
		}
		return $minoration;
	}

}
