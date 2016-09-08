<?php
/*
  Plugin Name: Bii page_perso
  Description: Gestion d'un système de page personnelle
  Version: 0.2
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */

define('bii_page_perso_version', '0.2');
define('bii_page_perso_path', plugin_dir_path(__FILE__));
define('bii_page_perso_url', plugin_dir_url(__FILE__));

define('bii_page_perso_template_path', plugin_dir_path(__FILE__) . "/templates");



add_action("bii_informations", function() {
	?>
	<tbody id="bii_bdd">
		<tr><th colspan="2">Bii_page_perso</th>
		<tr><td>Les pages persos sont </td><td><?= bii_makebutton("bii_use_page_perso", 1, 1); ?></td></tr>
	</tbody>
	<?php
});

function bii_include_class_page_perso() {
	$liste_class = [
		"bii_item_page_perso",
		"bii_page_perso",
		"bii_page_perso_timeline",
		"bii_page_perso_image",
		"bii_page_perso_content",
		"bii_page_perso_layout"
	];
//	bii_write_log($liste_class);
	foreach ($liste_class as $class) {
		require_once(bii_page_perso_path . "class/$class.class.php");
		if ($class != "bii_item_page_perso" && class_exists($class)) {
//			bii_custom_log($class);
			if (!$class::table_exists()) {
				$class::autoTable(1);
			}
		}
	}
}

function bii_page_perso_menu() {
	bii_page_perso::displaySousMenu();
	bii_page_perso_layout::displaySousMenu();
}

function bii_page_perso_after_init() {
	$list = array(
		'page',
		'post',
		'page-pro'
	);
	vc_set_default_editor_post_types($list);
}

function bii_SC_page_perso_form_edit($args = []) {
	$user_id = get_current_user_id();
	$lang = apply_filters("bii_multilingual_current_language");
	$filename = "echec_page_perso.php";
	if ($user_id && $lang) {
		$filename = "edit_page_perso.php";
	}
	$file = bii_current_template_dir . "/templates/bii_plugin/$filename";
	if (!file_exists($file)) {
		$file = bii_page_perso_template_path . "/$filename";
	}
	ob_start();
	require_once($file);
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function bii_page_perso_enqueue_script() {
	
}

function bii_page_perso_profile_tabs($tabs) {
//	pre($tabs);
	$user_id = get_current_user_id();
	$displayed_id = um_profile_id();
	if ($user_id == $displayed_id) {
		$tabs["page_pro_edit"] = [
			"name" => __("Modifier ma page pro"),
			"icon" => "um-faicon-certificate",
		];

		if (bii_page_perso::has_page_perso($displayed_id)) {
			$tabs["page_pro"] = [
				"name" => __("Voir ma page pro"),
				"icon" => "um-faicon-eye",
			];
			$tabs["boutique"] = [
				"name" => __("Voir ma boutique"),
				"icon" => "um-faicon-shopping-cart",
			];
		}
		$tabs["photos_edit"] = [
			"name" => __("Mes images"),
			"icon" => "um-faicon-picture-o",
		];
	} else {
		if (bii_page_perso::has_page_perso($displayed_id)) {
			$tabs["page_pro"] = [
				"name" => __("Voir sa page pro"),
				"icon" => "um-faicon-eye",
			];
		}
	}
	unset($tabs["posts"]);
	unset($tabs["comments"]);

	return $tabs;
}

function bii_page_pro_edit_default($args = []) {
//	pre($args);
	echo bii_SC_page_perso_form_edit();
}

function bii_page_pro_edit_images($args = []) {
//	pre($args);
	echo do_shortcode("[nm-wp-file-uploader]");
}

function bii_page_pro_boutique($args = []) {
	$lang = apply_filters("bii_multilingual_current_language", "");
	$link = "http://demo.biilink.com/bii-market/en/wcmp_vendor_dashboard/";
	if ($lang == "fr") {
		$link = "http://demo.biilink.com/bii-market/fr/tableau-bord-vendeur/";
	}
	
	?>
	<div>
		<p>
			Connectez vous à votre boutique en cliquant sur le lien suivant
			<a href="<?= $link ?>" target="_blank">Cliquez ici</a>
			
			
		</p>
		
		
	</div>
		
	<?php
}

function bii_page_pro_nfum_select_file_label($text = "") {
	$lang = apply_filters("bii_multilingual_current_language", "");
	if ($lang == "fr") {
		$text = "Télécharger des fichiers";
	}
	return $text;
}

if (get_option("bii_use_page_perso")) {
	add_action("bii_after_include_class", "bii_include_class_page_perso", 10);
	add_action("bii_add_menu_pages", "bii_page_perso_menu");
	add_action("init", "bii_page_perso_after_init");


	add_shortcode("bii_page_perso_form_edit", "bii_SC_page_perso_form_edit");

	add_filter('um_user_profile_tabs', "bii_page_perso_profile_tabs");
	add_filter('bii_page_pro_nfum_select_file_label', "nfum_select_file_label");
	add_action('um_profile_content_page_pro_edit_default', "bii_page_pro_edit_default");
	add_action('um_profile_content_photos_edit_default', "bii_page_pro_edit_images");
	add_action('um_profile_content_boutique_default', "bii_page_pro_boutique");
}
