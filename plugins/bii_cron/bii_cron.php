<?php
/*
  Plugin Name: Bii_cron
  Description: Ajout des tâches cron.
  Version:  1.0

 */

add_filter("imcron_interval_id", "bii_set_interval");

function bii_set_interval() {
	if (!get_option("bii_interval")) {
		update_option("bii_interval", "hourly");
	}
	return get_option("bii_interval");
}

add_action("bii_informations", function() {
	?>
	<tbody>
		<tr><th colspan="2">CRON</th></tr>
		<tr><td>Les tâches cron sont </td><td><?= bii_makebutton("bii_desactivate_cron", 1, 1, 1); ?></td></tr>
	</tbody>
	<?php
});
if (get_option("bii_desactivate_cron") == 0) {
	add_action("bii_options_title", function() {
		?>
		<li role="presentation" class="hide-relative " data-relative="pl-cron"><i class="fa fa-clock-o"></i> Tâches planifiées</li>
		<?php
	});
	add_action("bii_options", function() {

//		pre(get_option("bii_interval"));
		$options_bii_interval = [
			"every_minute" => "Toutes les minutes",
			"every_5minutes" => "Toutes les 5 minutes",
			"every30minutes" => "Toutes les 30 minutes",
			"hourly" => "Toutes les heures",
			"4timesaday" => "Toutes les 6 heures",
			"twicedaily" => "Toutes les 12 heures",
			"daily" => "Toutes les jours",
			"weekly" => "Toutes les semaines",
		];
		?>
		<div class="col-xxs-12 pl-cron bii_option hidden">
		<?php
		bii_makestuffbox("bii_interval", "Intervalle de passage du robot (permet d'éviter d'oublier un cron)", "select", "col-xxs-12 col-sm-6", $options_bii_interval);
		?>
		</div>
			<?php
		});
	}
	add_action("bii_options_submit", function() {
		$tableaucheck = [
			"bii_interval",
		];
		foreach ($tableaucheck as $itemtocheck) {
			if (isset($_POST[$itemtocheck])) {
				update_option($itemtocheck, $_POST[$itemtocheck]);
			}
		}
	}, 5);

	function bii_add_new_intervals($schedules) {
		$schedules['every_5minutes'] = array(
			'interval' => 300,
			'display' => __('Youtes les 5 minutes')
		);
		$schedules['4timesaday'] = array(
			'interval' => 21600,
			'display' => __('4 fois par jour')
		);
		$schedules['every30minutes'] = array(
			'interval' => 1800,
			'display' => __('Toutes les demi-heures')
		);
		return $schedules;
	}

	add_filter('cron_schedules', 'bii_add_new_intervals');

//add_action('wp', 'bii_cron');
	function bii_cron() {
		if (!wp_next_scheduled('bii_4daily_event')) {
			wp_schedule_event(time(), '4timesaday', 'bii_4daily_event');
		}
	}
	function bii_cron_2() {
		if (!wp_next_scheduled('bii_4daily_event_2')) {
			wp_schedule_event(time(), '4timesaday', 'bii_4daily_event_2');
		}
	}
	function bii_cron_3() {
		if (!wp_next_scheduled('bii_4daily_event_3')) {
			wp_schedule_event(time(), '4timesaday', 'bii_4daily_event_3');
		}
	}

	add_action('bii_4daily_event', 'bii_autoimport');
	
	function bii_autoimport() {
		update_option("bii_last_paserelle_try", time());
		if (get_option("bii_desactivate_cron") == 1) {
			bii_write_log("[INFO BII_CRON] bii_cron est désactivé");
		} else {
			bii_write_log("[INFO BII_CRON] Passerelle Cron");
			if (is_it_night_or_day() == "night" && get_option("bii_last_paserelle_0_330") < time() - 43200) {
				bii_write_log("[INFO BII_CRON] Passerelle 0 330 started : " . date("d/m/Y H:i:s", time()));

				//import de nuit + dernier import datant de plus de 12h

				do_action("bii_import", 0, 330);
				bii_write_log("[INFO BII_CRON] Passerelle 0 330 : " . date("d/m/Y H:i:s", time()));
			} else {
				bii_write_log("[INFO BII_CRON] Pas de passerelle : " . is_it_night_or_day() . " date dernière passerelle : " . date("d/m/Y H:i:s", get_option("bii_last_paserelle")));
			}
		}
	}
	