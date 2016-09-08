<?php
/*
  Plugin Name: Bii Satellite
  Description: Gestion d'un site satellite d'un site principal
  Version: 0.1
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */

define('bii_satellite_version', '0.1');
define('bii_satellite_path', plugin_dir_path(__FILE__));
define('bii_satellite_url', plugin_dir_url(__FILE__));


function bii_include_class_satellite() {
//	bii_write_log("bii_after_include_class");
//	require_once(bii_communes_path . "class/bddcommune_items.class.php");
	
}

function bii_satellite_menu() {
	
}

if (get_option("bii_use_satellite")) {

	add_action("bii_after_include_class", "bii_include_class_satellite",10);
//	add_action("bii_add_menu_pages", "bii_satellite_menu");
}