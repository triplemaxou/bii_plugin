<?php
/*
  Plugin Name: Bii check seo
  Description: Ajoute des scripts premettant de vérifier l'optimisation SEO des pages
  Version: 1.2
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */

define('bii_check_seo_version', '1.2');

function biicheckseo_enqueueJS() {
	if (!get_option("bii_hideseo")) {
		update_option("bii_hideseo", 0);
		wp_enqueue_script('seoscript', plugins_url('js/seo.js', __FILE__), array('jquery', 'util'), false, true);
	}
}

add_action('wp_enqueue_scripts', "biicheckseo_enqueueJS");



function bii_check_seo_submit(){
	$tableaucheck = ["bii_seo_list_id","bii_seo_ApplicationName","bii_seo_DeveloperKey","bii_seo_ClientId","bii_seo_ClientSecret","bii_seo_RedirectUri"];
	foreach ($tableaucheck as $itemtocheck) {
		if (isset($_POST[$itemtocheck])) {
			update_option($itemtocheck, $_POST[$itemtocheck]);
		}
	}
}

function bii_check_seo_debug() {
	?>
	<div class="col-xxs-12 pl-Biitracking bii_option hidden">
		<?php
		if (!get_option("bii_seo_RedirectUri")) {
			$url = get_bloginfo("url") . "/wp-admin/admin.php?page=bii_plugin";
			update_option("bii_seo_RedirectUri", $url);
		}

		bii_makestuffbox("bii_seo_list_id", "Liste des id_google_analytics à analyser (séparer par des virgules)", "text", "col-xxs-12 col-xs-12");
		?>
		<div class="bii_analytics">	
			<h3>Google analytics API</h3>
			<?php
			bii_makestuffbox("bii_seo_ApplicationName", "ApplicationName", "text", "col-xxs-12 col-sm-4");
			bii_makestuffbox("bii_seo_DeveloperKey", "DeveloperKey", "text", "col-xxs-12 col-sm-4");
			bii_makestuffbox("bii_seo_ClientId", "ClientId", "text", "col-xxs-12 col-sm-4");
			bii_makestuffbox("bii_seo_ClientSecret", "ClientSecret", "text", "col-xxs-12 col-sm-6");
			bii_makestuffbox("bii_seo_RedirectUri", "RedirectUri", "text", "col-xxs-12 col-sm-6");
			?>
		</div>
	</div>
	<?php
}

add_action("bii_informations", function() {
	?>
	<tr><td>SEO Debug est  </td><td><?= bii_makebutton("bii_hideseo", 0, 0, true); ?></td></tr>
	<tr><td>SEO tracking est  </td><td><?= bii_makebutton("bii_tracking", 0, 0); ?></td></tr>
	<?php
});

function bii_seo_tracking_info() {
	?>
	<li role="presentation" class="hide-relative" data-relative="pl-Biitracking"><i class="fa fa-google"></i> Tracking Google</li>
	<?php
}

if (get_option("bii_tracking")) {

	add_filter("bii_options_debug_tableau_check_more", "bii_check_seo_valuestoadd");

	add_action("bii_options", "bii_check_seo_debug");
	add_action("bii_options_submit","bii_check_seo_submit");
	add_action("bii_options_title", "bii_seo_tracking_info");
}