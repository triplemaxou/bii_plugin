<?php
/*
  Plugin Name: Bii Calculatrice
  Description: Ajoute des calculatrices
  Version: 0.3
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_calculatrices_version', '0.3');
define('bii_calculatrices_path', plugin_dir_path(__FILE__));
define('bii_calculatrices_url', plugin_dir_url(__FILE__));

function bii_calculatrices_SC_calc($atts, $content = null) {
	ini_set('display_errors', '1');
	$calc = "";
	$content = "";
	if (isset($atts["calc"])) {
		$calc = "bii_" . $atts["calc"];
	}
	if ($calc && class_exists($calc)) {
		ob_start();
		?>
		<div class="bii-calculatrice col-xs-12 vc_col-xs-12 <?= $calc ?>" data-calc="<?= $calc ?>">
			<form class="bii-calc-content">
				<?= $calc::afficher_calculatrice(); ?>	
				<div class="clearfix"></div>
				<button class="bii-calculer"><?= $calc::texte_bouton(); ?>	</button>
			</form>
			<div class="clearfix"></div>
			<div class="bii-result"></div>
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
	} else {
		trigger_error("Pas de calculatrice définie");
	}
	return $content;
}

function bii_calculatrice_noms($var = "") {
	return [
		"bii_taille_vetements",
		"bii_taille_chaussure",
		"bii_taille_robe",
	];
}

function bii_calculatrice_include_class() {
	require_once(bii_calculatrices_path . "class/bii_calc.class.php");
	$liste = apply_filters("bii_calculatrice_noms", null);
//	bii_write_log($liste);
	foreach ($liste as $item) {
		require_once(bii_calculatrices_path . "class/$item.class.php");
	}
}

function bii_calculatrices_list_shortcodes() {
	$listenoms = apply_filters("bii_calculatrice_noms");
	?>
	<tr>
		<td><strong>[bii_calculatrice calc="nom de la calculatrice"]</strong></td>
		<td>Affiche la calculatrice. Noms disponibles :
			<ul>
				<?php
				foreach ($listenoms as $nom) {
					$nom = str_replace("bii_", "", $nom);
					?><li><?= $nom ?></li><?php
				}
				?>

			</ul>
		</td>
	</tr>
	<?php
}

function bii_ajax_calc() {
	include(bii_calculatrices_path . "ajax/ajax_calcul.php");
	die();
}

function bii_calculatrices_enqueueJS() {
	wp_enqueue_script('bii_calculatrices', bii_calculatrices_url . "js/calculatrices.js", array('jquery', 'util'), false, true);
}

add_action("bii_informations", function() {
	?>
	<tbody id="bii_bdd">

		<tr><th colspan="2">Bii_calculatrices</th>
			<?php if (get_option("bii_useclasses")) { ?>
			<tr><td>Les calculatrices sont  </td><td><?= bii_makebutton("bii_use_calculatrice", 1, 1); ?></td></tr>
		<?php } else { ?>
			<tr><td colspan="2">Activez les classes pour voir les options</td></tr>
		<?php } ?>
	</tbody>
	<?php
}, 12);

if (get_option("bii_use_calculatrice") && get_option("bii_useclasses")) {

	add_shortcode('bii_calculatrice', 'bii_calculatrices_SC_calc');
	add_filter("bii_calculatrice_noms", "bii_calculatrice_noms");
	add_action("bii_after_include_class", "bii_calculatrice_include_class", 11);

	add_action('wp_enqueue_scripts', "bii_calculatrices_enqueueJS");

	add_action("bii_specific_shortcodes", "bii_calculatrices_list_shortcodes");

	add_action('wp_ajax_bii_ajax_calc', 'bii_ajax_calc');
	add_action('wp_ajax_nopriv_bii_ajax_calc', 'bii_ajax_calc');
}