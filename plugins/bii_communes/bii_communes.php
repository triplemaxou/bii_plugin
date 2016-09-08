<?php

/*
  Plugin Name: Bii Communes
  Description: Gestion de la base de données des communes
  Version: 0.1
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_communes_version', '0.1');
define('bii_communes_path', plugin_dir_path(__FILE__));
define('bii_communes_url', plugin_dir_url(__FILE__));

function bii_include_class_communes() {
//	bii_write_log("bii_after_include_class");
	require_once(bii_communes_path . "class/bddcommune_items.class.php");
	require_once(bii_communes_path . "class/ancienne_region_france.class.php");
	require_once(bii_communes_path . "class/departement_france.class.php");
	require_once(bii_communes_path . "class/villes_france.class.php");
	require_once(bii_communes_path . "class/countries.class.php");
}

function bii_communes_menu() {
	if (class_exists("ancienne_region_france")) {
		ancienne_region_france::displaySousMenu();
		departement_france::displaySousMenu();
		villes_france::displaySousMenu();
		countries::displaySousMenu();
	}else{
		trigger_error("Erreur classe des communes");
	}	
}

//bii_write_log("bii_use_bddcommunes ? ");
if (get_option("bii_use_bddcommunes") && get_option("bii_host_bddplugin")) {
//	bii_write_log("yes bii_use_bddcommunes");
	add_action("bii_after_include_class", "bii_include_class_communes",10);
	add_action("bii_add_menu_pages", "bii_communes_menu");
}