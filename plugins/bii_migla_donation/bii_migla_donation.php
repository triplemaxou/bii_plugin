<?php
/*
  Plugin Name: bii_migla_donation
  Description: Ajoute des fonctions supplémentaires au plugin total donation
  Version: 0.4
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_migla_donation_version', '0.4');
define('bii_migla_donation_path', plugin_dir_path(__FILE__));
define('bii_migla_donation_url', plugin_dir_url(__FILE__));

function bii_migla_checkdonation() {
//	logRequestVars();
	bii_migla_autoinsert();
}

function bii_migla_include_classes() {
	require_once(bii_migla_donation_path . "class/bii_migla_items.class.php");
	require_once(bii_migla_donation_path . "class/donation.class.php");
	require_once(bii_migla_donation_path . "class/paypal_ign_item.class.php");
}

function bii_migla_menu() {
	if (class_exists("donation")) {
		if(!donation::table_exists()){
			donation::autoTable(1);
		}
		donation::displaySousMenu();		
	}
	if (class_exists("paypal_ign_item")) {
		if(!paypal_ign_item::table_exists()){
			paypal_ign_item::autoTable(1);
		}
		paypal_ign_item::displaySousMenu();
	}
}

function bii_migla_ajax_userinfo(){
	include bii_migla_donation_path."ajax/bii_migla_current_user.php";
	die();
}

add_action("bii_informations", function() {
	?>
	<tbody id="bii_bdd">
		<tr><th colspan="2">Bii_migla</th>
		<tr><td>Les options supplémentaires pour Total Donation sont </td><td><?= bii_makebutton("bii_use_migla_donation", 1, 1); ?></td></tr>
	</tbody>
	<?php
}, 12);

function bii_migla_enqueueJS() {
	wp_enqueue_script('bii_migla', bii_migla_donation_url . "js/bii_migla_donation.js", array('jquery', 'util'), false, true);
}

function bii_migla_autoinsert(){
	$ids = options::all_id("(`option_name` like 't_migla%' OR `option_name` like 'migla_paypal_ipn%')");
	foreach($ids as $id){
		$array = options::static_get_option($id);
		$nom_classe = "";
		if(isset($array['mc_gross'])){
			$nom_classe = "paypal_ign_item";			
		}
		if(isset($array['miglad_amount'])){
			$nom_classe = "donation";			
		}
		if($nom_classe){
			$nom_classe::add($array);
		}
	}
	
}

if (get_option("bii_use_migla_donation")&& get_option("bii_useclasses")) {
	add_action("bii_after_include_class", "bii_migla_include_classes", 11);
	add_action("admin_init", "bii_migla_checkdonation",200);
	add_action('wp_enqueue_scripts', "bii_migla_enqueueJS");

	add_action("bii_add_menu_pages", "bii_migla_menu");
	
	add_action('wp_ajax_nopriv_bii_migla_ajax_userinfo', 'bii_migla_ajax_userinfo');
	add_action('wp_ajax_bii_migla_ajax_userinfo', 'bii_migla_ajax_userinfo');
	
}