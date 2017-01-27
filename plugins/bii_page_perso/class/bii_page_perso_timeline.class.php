<?php

class bii_page_perso_timeline extends bii_item_page_perso {

	protected $id;
	protected $id_page_perso;
	protected $date;
	protected $contenu;
	protected $options;

	static function supprimable() {
		return true;
	}

	function page_perso() {
		return new bii_page_perso($this->id_page_perso);
	}

	static function optionsAffichage() {
		$a = [
			//Effets date
			"counter" => __("Afficher un compteur"),
			"countdown" => __("Afficher le compte à rebours"),
			"pie" => __("Afficher un graphique"),
			//Effets texte
			"fadein" => __('Animation "fade in" sur le texte'),
			//Effet Bloc
			"right" => __("Afficher la valeur à droite et le texte à gauche")
		];
		return $a;
	}

	function options() {
		$options = $this->options;
		if (!$this->id) {
			$options = "counter";
		}
		return $options;
	}

	function afficher_cbx($option_affichage, $index = 0) {
		$opt = explode(",", $this->options());
		$options = static::optionsAffichage();
		$selected = "";
		if (in_array($option_affichage, $opt)) {
			$selected = "checked='checked'";
		}
		$label = $options[$option_affichage];
		?>
		<div class="vc_col-xxs-12 vc_col-xs-12 vc_col-sm-6 vc_col-md-4 vc_col-lg-3 affichercbx">
			<div class="divlabel">
				<label for="cbx_optaffichage_<?= $index; ?>"><?= $label ?></label>
			</div>
			<div class="divcbx">
				<input type="checkbox" <?= $selected ?> id="cbx_optaffichage_<?= $index; ?>" class="bii_cbx bii_cbx_<?= $option_affichage ?>" data-change="#option_timeline_<?= $index; ?>" data-value="<?= $option_affichage ?>">
			</div>
		</div>
		<?php
	}

	static function emptyFormTodupl() {
		$item = new static();
		?>
		<div class="todupl hidden">
			<?php
			$item->form_edit_front("todupl");
			?>
		</div>
		<?php
	}

	function form_edit_front($index = 0) {
		$date = $this->date();
		$opt = $this->options();
		$optarray = explode(",", $opt);
		$value = utf8_encode($this->contenu());
		$affichages = array_keys(static::optionsAffichage());
		if ($index === "todupl") {
			$p = " +1todupl";
		} else {
			$p = $index + 1;
		}
		$date_timeline_unit = "";
		$add_class_dt = "";
		if (in_array("pie", $optarray)) {
			$date_timeline_unit = "%";
		}
		if (in_array("countdown", $optarray)) {
			$add_class_dt = "datepicker";
		}
		?>
		<div class="stuffbox stuffboxin vc_col-xxs-12 vc_col-xs-12 " id="id_timeline_<?= $index; ?>_div">
			<button class="btn btn-danger bii-container-btn-del del_timeline"><i class="fa fa-times-circle"></i></button>
			<h3>
				<label for="id_timeline_<?= $index; ?>"><?= __("Chiffre commenté") . " $p" ?></label>
			</h3>

			<div class="inside">
				<div class="vc_col-xxs-12 vc_col-xs-12 vc_col-sm-5 vc_col-md-3">
					<label for="date_timeline_<?= $index; ?>"><?= __("Choisissez une valeur") ?></label>	
					<div class="vc_col-xxs-11 vc_col-xs-11 date_timeline_container">
						<input id="date_timeline_<?= $index; ?>" name="date_timeline[]" class="form-control date_timeline <?= $add_class_dt ?>" value="<?= $date ?>" />
					</div>
					<div class="vc_col-xxs-1 vc_col-xs-1 date_timeline_unit_container">
						<span class="date_timeline_unit" id="date_timeline_unit_<?= $index; ?>"><?= $date_timeline_unit ?></span>
					</div>
				</div>
				<div class="vc_col-xxs-12 vc_col-xs-12 vc_col-sm-7 vc_col-md-9">
					<label for="timeline_<?= $index; ?>"><?= __("Contenu") ?></label>
					<textarea id="timeline_<?= $index; ?>" name="timeline[]" class="form-control "><?= $value ?></textarea>
				</div>
				<div class="clearfix"></div>
				<div class="vc_col-xxs-12 vc_col-xs-12 cbx_container">
					<div class="enroulederoule_click">
						<label><?= __("Options") ?> <i class="fa fa-caret-down"></i></label>
					</div>
					<div class="enroulederoule" style="display: none">
						<input type="hidden" id="option_timeline_<?= $index; ?>" name="option_timeline[]" class="form-control" value="<?= $opt ?>" />					
						<?php
						foreach ($affichages as $option_affichage) {
							$this->afficher_cbx($option_affichage, $index);
						}
						?>
					</div>
					<div class="clearfix"></div>
				</div>
				<div>
					<p></p>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="clearfix"></div>
		<?php
	}

	function date_front() {
		$options = explode(",", $this->options());

		$date = $this->date;
		$r = "[vc_column_text]" . $date . "[/vc_column_text]";
		if (in_array("counter", $options)) {
			$counter_decimal = ".";

			$r = "[stat_counter icon_size='32' counter_value='$date' speed='3'  counter_sep='' counter_decimal='$counter_decimal']";
		}
		if (in_array("countdown", $options) && substr_count($date, "/") == 2) {
			$lang = apply_filters("bii_multilingual_current_language", "");
			$more = "";
			if ($lang == "fr") {
				$more = 'string_days="Jour" string_days2="Jours" string_weeks="Semaine" string_weeks2="Semaines" string_months="Mois" string_months2="Mois" string_years="An" string_years2="Ans" string_hours="Heure" string_hours2="Heures" string_minutes="Minute" string_minutes2="Minutes" string_seconds="Seconde" string_seconds2="Secondes"';
				$datexpl = explode("/", $date);
				$date = $datexpl[2] . "/" . $datexpl[1] . "/" . $datexpl[0];
			}
			$datexpl = explode("/", $date);
			$year = $datexpl[0];
			$month = $datexpl[1];
			$day = $datexpl[2];
			$time = time();
			$datetmstp = mktime(0, 0, 0, $month, $day, $year);
			$diff = $datetmstp - $time;
			$countdown_opts = "smonth,sday";
			if ($diff / 31556926 > 1) {
				//différence > 1 an
				$countdown_opts = "syear,smonth";
			}
			if ($diff / 2629743 < 1) {
				//différence > 1 mois
				$countdown_opts = "sday,shr";
			}




			$r = "[ult_countdown datetime='$date 00:00:00' countdown_opts='$countdown_opts' $more]";
		}
		if (in_array("pie", $options)) {
			$r = "[vc_pie value='$date' units='%']";
		}

		return $r;
	}

	function contenu_front() {
		$options = explode(",", $this->options());

		$value = $this->contenu;
		$r = "[vc_column_text]$value" . "[/vc_column_text]";
		if (in_array("fadein", $options)) {
			$r = "[ult_animation_block animation='fadeIn' animation_duration='3' animation_delay='0' animation_iteration_count='1']$r" . "[/ult_animation_block]";
		}

		return $r;
	}

	function timeline_front() {
		$options = explode(",", $this->options());
		$date = $this->date_front();
		$value = utf8_encode($this->contenu_front());
		$text = '[vc_row el_class="timeline"]'
			. '[vc_column width="1/4" el_class="timeline-date"]'
			. $date
			. '[/vc_column]'
			. '[vc_column width="3/4" el_class="timeline-content"]'
			. $value
			. '[/vc_column]'
			. '[/vc_row]';
		if (in_array("right", $options)) {
			$text = '[vc_row el_class="timeline"]'
				. '[vc_column width="3/4" el_class="timeline-content"]'
				. $value
				. '[/vc_column]'
				. '[vc_column width="1/4" el_class="timeline-date"]'
				. $date
				. '[/vc_column]'
				. '[/vc_row]';
		}
		pre($text);
		return $text;
	}

}
