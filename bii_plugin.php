<?php

/*
  Plugin Name: Bii_plugin
  Description: Bii_plugin : Plugin de développement de biilink. Ce plugin ajoute des fonctions de débug en cours de développement, de gestion de l'interface d'admin, de débug SEO et des fonctionnalités front office
  Version: 0.8.1
  Author: BiilinkAgency
  GitHub Plugin URI: https://github.com/poissont/bii_plugin
  GitHub Branch:     master
 */

define('Bii_plugin_version', '0.8.1');
define('Bii_path', plugin_dir_path(__FILE__));
define('Bii_url', plugin_dir_url(__FILE__));
define('Bii_file', __FILE__);

define("bii_current_template_dir",get_template_directory());



define('Bii_plugin_slug', "Biilinkplugin");
define('Bii_menu_title', __("Bii Options"));
define('Bii_dashicon_menu', get_bloginfo("url") . "/wp-content/plugins/bii_plugin/img/smallbiilink.png");
define('Bii_menu_slug', "bii_plugin");
define('Bii_plugin_name', "bii_plugin");
define('Bii_dashboard_page', "bii_dashboard");
define('Bii_min_role', "publish_pages");

if (!get_option("bii_plugin_installed")) {
	update_option("bii_plugin_installed", 1);
}
//Attention à l'ordre des plugins
/*
 * bii_debug et bii_bdd en premier
 * mini plugins utilisant des classes ensuite
 * bii_advanced_admin ensuite
 * miniplugins simples après
 */
$liste_bii_plugins = [
	"bii_debug",
	"bii_bdd",
	//Plugins utilisant des classes
	"bii_communes",	
	"bii_calculatrices",
	"bii_multilingual",
	"bii_page_perso",
	"bii_um",
	"bii_migla_donation",
	"bii_finance",
	"bii_shared_items",
	"bii_one_post_a_day",
	//Bii advanced admin
	"bii_advanced_admin",
	//Plugins de CSS, js shortcodes... etc
	"bii_css",
	"bii_check_seo",
	"bii_advanced_shortcodes",
	"bii_restricted_content",
	"bii_social",	
];

foreach ($liste_bii_plugins as $plugin) {
	if (file_exists(Bii_path . "plugins/$plugin/$plugin.php")) {
		require_once(Bii_path . "plugins/$plugin/$plugin.php");
	}
}

//Include du config
require_once(Bii_path . "config.php");
require_once(Bii_path . "functions.php");
require_once(Bii_path . "shortcodes.php");