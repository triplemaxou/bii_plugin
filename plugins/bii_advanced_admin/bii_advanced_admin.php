<?php
/*
  Plugin Name: Bii Advanced Admin
  Description: Ajoute des fonctionnalités dans l'interface d'admin
  Version: 2.2
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_advanced_admin_version', '2.2');
define('bii_advanced_admin_path', plugin_dir_path(__FILE__));
define('bii_advanced_admin_url', plugin_dir_url(__FILE__));

function bii_option_page() {
	$we_use_bii = true;
	wp_enqueue_script('bii-options', bii_advanced_admin_url . 'admin/js/bii_options.js', array('jquery'), null, true);
	wp_enqueue_style('bii-admin-css', bii_advanced_admin_url . 'admin/css/admin.css');
	require_once(bii_advanced_admin_path . "/admin/bii_options.php");
}

function bii_add_admin_pages() {
	add_options_page(Bii_menu_title, Bii_menu_title, Bii_min_role, Bii_menu_slug . "_options", "bii_option_page");
	if (get_option("bii_usedashboard")) {
		add_menu_page(__("Tableau de bord de " . Bii_plugin_name), __(Bii_menu_title), Bii_min_role, Bii_menu_slug, Bii_dashboard_page, Bii_dashicon_menu);
		do_action("bii_add_menu_pages");
	}
}

function bii_enqueueJSAdmin($hook) {
	wp_enqueue_media();
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-autocomplete');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('bii_advanced-admin', plugins_url('js/admin.js', __FILE__), array('jquery'), false, true);
}

function bii_delete_not_approved() {
	include("ajax/ajax_delete_not_approved.php");
	die();
}

function bii_get_post_ajax() {
	include("ajax/getPost.php");
	die();
}

function bii_ajax_changewpoption() {
	include("ajax/ajax_change_wp_option.php");
	die();
}

function bii_get_attachment_id_from_url($attachment_url = '') {
	global $wpdb;
	$attachment_id = false;
	if ('' == $attachment_url) {
		return;
	}
	$upload_dir_paths = wp_upload_dir();
	if (false !== strpos($attachment_url, $upload_dir_paths['baseurl'])) {
		$attachment_url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url);
		$attachment_url = str_replace($upload_dir_paths['baseurl'] . '/', '', $attachment_url);
		$attachment_id = $wpdb->get_var($wpdb->prepare("SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url));
	}
	return $attachment_id;
}

function bii_options_page_title($param, $param2) {
	return "<h1 class='faa-parent animated-hover'><span class='fa fa-rocket faa-passing'></span> Plugin Bii_Plugin version " . Bii_plugin_version . " </h1>";
}

function bii_options_page_link($param = null) {
	return get_admin_url() . "options-general.php?page=" . Bii_menu_slug . "_options";
}

function bii_listeClass($val1 = null) {
	$list = [
		"global_class",
		"options",
		"posts",
		"terms",
		"postmeta",
		"users",
		"usermeta",
		"comments",
		"commentmeta",
	];
	return $list;
}

function bii_includeClass($val1 = null) {

	do_action("bii_before_include_class");
	$liste = apply_filters("bii_liste_class", $val1);
	foreach ($liste as $item) {
		require_once(bii_advanced_admin_path . "class/$item.class.php");
	}
	do_action("bii_after_include_class");
}

function bii_advanced_admin_informations() {
	?>
	<tbody id="bii_advanced_admin">
		<tr><th colspan="2">Bii_Admin</th>
		<tr><td>Le dashboard est </td><td><?= bii_makebutton("bii_usedashboard"); ?></td></tr>
		<tr><td>Les classes sont </td><td><?= bii_makebutton("bii_useclasses", 1, 1); ?></td></tr>
	</tbody>
	<?php
}

function bii_dashboard() {
	$we_use_bii = true;
	wp_enqueue_script('admin-init', plugins_url('/admin/js/dashboard.js', __FILE__), array('jquery'), null, true);
	wp_enqueue_style('bii-admin-css', plugins_url('/admin/css/admin.css', __FILE__));
	include('admin/bii_dashboard.php');
}

function bii_dashboard_content() {
	?>
	<div class="bii-tools">
		<h2>Outils <button class="btn btn-default bii-make-this-visible" data-selector=".bii-tools-inner"><i class="fa fa-plus"></i></button></h2>
		<div class="bii-tools-inner bii-invisible" >
			<?php if (get_option("bii_useclasses")) { ?>
			<a class="btn btn-info bii_action_ajax" data-action="bii_delete_not_approved" data-success="log" href="#"><span class="fa-stack fa-lg">
					<i class="fa fa-comment-o fa-stack-1x"></i>
					<i class="fa fa-ban fa-stack-2x text-danger"></i>
				</span> Supprimmer les commentaires non approuvés</a>
			<?php } ?>
			<?php do_action("bii_tools"); ?>
		</div>
	</div>
	<?php
}



add_action('admin_menu', 'bii_add_admin_pages');
add_action('admin_enqueue_scripts', 'bii_enqueueJSAdmin');
add_action('wp_ajax_bii_get_post', 'bii_get_post_ajax');
add_action('wp_ajax_bii_change_wp_option', 'bii_ajax_changewpoption');
add_filter('bii_options_page_title', 'bii_options_page_title', 1, 2);
add_filter('bii_options_page_link', 'bii_options_page_link', 1, 1);
add_filter("bii_liste_class", "bii_listeClass", 10, 1);
add_action("bii_include_class", "bii_includeClass", 1, 1);
add_action("bii_informations", "bii_advanced_admin_informations", 1);

if (get_option("bii_useclasses")) {
	do_action("bii_include_class");

	add_action('wp_ajax_bii_delete_not_approved', 'bii_delete_not_approved');
}
if (get_option("bii_usedashboard")) {
	add_action("bii_dashboard_content", "bii_dashboard_content", 1);
}