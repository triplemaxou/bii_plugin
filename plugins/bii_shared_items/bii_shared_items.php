<?php
/*
  Plugin Name: Bii_shared_items
  Description: Gestion d'un système de compte unique à plusieurs wordpress
  Version: 0.4
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */

define('bii_shared_items_version', '0.4');
define('bii_shared_items_path', plugin_dir_path(__FILE__));
define('bii_shared_items_url', plugin_dir_url(__FILE__));


add_action("bii_informations", function() {
	?>
	<tbody id="bii_shared_items">
		<tr><th colspan="2">Bii_shared_items</th>
		<tr><td>Les comptes uniques sont </td><td><?= bii_makebutton("bii_use_shared_items", 1); ?></td></tr>
	</tbody>
	<?php
});

function bii_include_class_shared_items() {
	$liste_class = [
		"bii_shared_item",
		"bii_shared_product",
		"bii_instance",
		"bii_user",
		"bii_user_instance",
		"bii_user_meta",
		"bii_user_post",
		"bii_ambassador",
		"bii_changelog",
	];
//	bii_write_log($liste_class);
	foreach ($liste_class as $class) {
		require_once(bii_shared_items_path . "class/$class.class.php");
		if ($class != "bii_shared_item" && class_exists($class)) {
//			bii_custom_log($class);
			if (!$class::table_exists()) {
				$class::autoTable(1);
			}
		}
	}
	require_once( ABSPATH . WPINC . '/pluggable.php' );
//	bii_shared_items_my_instance();
//	bii_shared_product::checklangs();
//	bii_user::synchronize_all();
}

function bii_shared_items_my_instance() {
	return bii_instance::get_me();
}

function bii_shared_items_my_instance_id() {
	return bii_instance::get_my_id();
}

function bii_shared_items_user_update($user_id) {
	$instance = bii_shared_items_my_instance();
}

function bii_add_shared_items_option_title() {
	?>
	<li role="presentation" class="hide-relative" data-relative="pl-Instance"><i class="fa fa-pencil"></i> Instances</li>
	<?php
}

function bii_add_shared_items_options() {
	$instance = bii_instance::get_me();
	update_option("bii_add_shared_items_color", $instance->color());
	if (!get_option("bii_add_shared_items_color")) {

		update_option("bii_add_shared_items_color", "#2F94D7");
	}
	?>
	<div class="col-xxs-12 pl-Instance bii_option hidden">
		<?php
		for ($i = 1; $i < 30; ++$i) {
			$j = $i / 10;
			?>
			<div style="display:inline-block;height:30px;width:30px;background-color:<?= $instance->color($j); ?> "></div>
			<?php
		}
		?>
		<?= bii_makestuffbox("bii_add_shared_items_color", "Couleur", "text", "col-xxs-12", [], ""); ?>
	</div>
	<?php
}

function bii_shared_items_option_submit() {
	$tableaucheck = ["bii_add_shared_items_color"];
	$instance = bii_instance::get_me();
//	ini_set('display_errors', '1');
	foreach ($tableaucheck as $itemtocheck) {
		if (isset($_POST[$itemtocheck])) {
			$newcolor = $_POST[$itemtocheck];
			$instance->updateChamps($newcolor, "color");

			$betheme_options = get_option("betheme");
			$um_options = get_option("um_options");
			if ($betheme_options) {
				$replacemain = [
					'background-search', 'color-menu-a-active', 'color-overlay-menu-button', 'background-overlay-menu', 'color-menu-responsive-icon', 'color-theme',
					'color-a', 'color-fancy-link-hover', 'background-highlight', 'background-highlight-section', 'color-hr', 'color-footer-theme', 'color-footer-a',
					'color-sliding-top-theme', 'color-sliding-top-a', 'color-tab-title', 'color-contentlink', "color-counter", "background-getintouch", "color-iconbar",
					"color-iconbox", "background-imageframe-link", "color-list-icon", "color-pricing-price", "background-pricing-featured", "background-progressbar",
					"color-quickfact-number", "background-slidingbox-title", "background-trailer-subtitle"
				];
				foreach ($replacemain as $rep) {
					if (isset($betheme_options[$rep])) {
						$betheme_options[$rep] = $newcolor;
					}
				}
				$replace03 = [
					"color-a-hover", "background-fancy-link-hover", 'color-footer-a-hover', "color-sliding-top-a-hover"
				];
				$color03 = $instance->color(1.3);
				foreach ($replace03 as $rep) {
					if (isset($betheme_options[$rep])) {
						$betheme_options[$rep] = $color03;
					}
				}
				if (isset($betheme_options["background-fancy-link"])) {
					$betheme_options["background-fancy-link"] = $instance->color(0.9);
				}
				if (isset($betheme_options["color-form-focus"])) {
					$betheme_options["color-form-focus"] = $instance->color(1.5);
				}
				if (isset($betheme_options["background-overlay-menu-a-active"])) {
					$betheme_options["background-overlay-menu-a-active"] = $instance->color(0.5);
				}
//				pre($betheme_options);
				update_option("betheme", $betheme_options);
			}
			if ($um_options) {
				$um_options["active_color"] = $newcolor;
				$um_options["secondary_color"] = $instance->secondary_color();
				$um_options["primary_btn_color"] = $newcolor;
				$um_options["primary_btn_hover"] = $instance->emphasis_color();

				update_option("um_options", $um_options);
			}
//			pre($um_options, "blue");
		}
	}
}

function bii_shared_items_colorpicker() {
//	wp_enqueue_script('bootstrap-js', bii_css_url . 'js/bootstrap.min.js', array('jquery'));
//	wp_enqueue_script('bootstrap-colorpicker', bii_css_url . 'js/bootstrap-colorpicker.min', array('jquery'));
}

function bii_shared_items_menu() {
	if (class_exists("bii_ambassador")) {
		bii_ambassador::displaySousMenu();
		bii_instance::displaySousMenu();
		bii_changelog::displaySousMenu();
	}
}

function bii_shared_items_SC_galaxies() {
	$instances = bii_instance::all_items("is_demo = 0");
	$current = bii_instance::get_my_id();
	ob_start();
	?>
	<div class="galaxies-footer">
		<ul class="galaxies-footer-wrapper">
			<?php
			foreach ($instances as $instance) {
				$currentclass = "";
				if ($instance->id() == $current) {
					$currentclass = "current";
				}
				?>
				<li class="galaxie-item <?= $currentclass ?> <?= $instance->name(); ?>-item"><a href="<?= $instance->url(); ?>"><?= $instance->shortcode_name(); ?></a></li>
				<?php
			}
			?>

		</ul>
	</div>
	<?php
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function bii_shared_items_get_instances_of_post($post_id, $lang = "") {

	$instancespostin = [];
//	pre($type,'violet');


	$categories = get_the_terms($post_id, 'product_cat');
//		pre($categories,'orange');
	$i = 1;
	foreach ($categories as $id_cat) {
		$cat = get_category($id_cat);
		$nomvar = "instancespostin$i";
		$$nomvar = bii_instance::fromCategory($cat->slug);
		$instancespostin = array_merge($instancespostin, $$nomvar);
		++$i;
	}

	$instancespostin = array_unique($instancespostin);
	pre($instancespostin, "blue");
	return $instancespostin;
}

function bii_shared_items_save_post($post_id) {
	$post = get_post($post_id);

//	pre($post->guid);
	$type = $post->post_type;
	$instances = [];
	$lang = apply_filters("bii_multilingual_current_language", '');
	$id_user = $post->post_author;
	bii_user_post::addpost($id_user, $post_id);
	

	if ($type == "product") {
		
	}
	if ($type == "product") {
		$instance = bii_instance::get_me();


		$id_trad = icl_translations::get_trad_base_of($post_id);
		$id_sharedproduct = bii_shared_product::update_shared_product($post_id, $instance, $lang, 0, $id_trad);

		$instances = bii_shared_items_get_instances_of_post($post_id, $lang);
		if (count($instances)) {
			$shared_product = new bii_shared_product($id_sharedproduct);
			foreach ($instances as $otherinstance) {
				$shared_product->postToInstance($otherinstance);
			}
		}
	}
//	if($type == ""){
//		
//	}
}

function bii_shared_items_delete_post($post_id) {
	$post = get_post($post_id);
//	pre($post->guid);
	$type = $post->post_type;
	if ($type == "product") {
		bii_shared_product::delete_product($post_id);
	}
}

function bii_shared_items_test_zone() {
//	bii_user::synchronize_all();
}

function bii_shared_items_dashboard_content() {
	?>
	<div class="changelogs col-xxs-12 col-sm-6">
		<h2>Changelog</h2>
		<?php
		bii_changelog::lastChangelogs(9);
		?>
	</div>
	<?php
}

function bii_shared_items_current_user() {
	
}

function bii_shared_items_current_user_id() {
	
}

function bii_shared_items_add_user($user_id) {
	$user = new users($user_id);
//	$mail_user = $user->user_email();
	$id_user_bii = bii_user::get_user($user_id);
}

function bii_shared_items_update_user($user_id, $old_user_data) {
	
}

function bii_shared_items_update_user_meta($meta_id, $object_id, $meta_key, $_meta_value) {
	
}

function bii_shared_items_remove_user($user_id) {
	
}

function bii_shared_itemsreturn1() {
	return 1;
}

add_filter("bii_shared_items_my_instance_id", "bii_shared_itemsreturn1", 10);
if (get_option("bii_use_shared_items") && get_option("bii_useclasses")) {
	add_action("bii_options_title", "bii_add_shared_items_option_title", 10);
	add_action("bii_options", "bii_add_shared_items_options");
	add_action("bii_dashboard_content", "bii_shared_items_dashboard_content");
	add_action("bii_options_submit", "bii_shared_items_option_submit", 10);

	add_action("bii_after_include_class", "bii_include_class_shared_items", 11);
	remove_filter("bii_shared_items_my_instance_id", "bii_shared_itemsreturn1");
	add_filter("bii_shared_items_my_instance", "bii_shared_items_my_instance", 10);
	add_filter("bii_shared_items_my_instance_id", "bii_shared_items_my_instance_id", 10);

	add_action("bii_add_menu_pages", "bii_shared_items_menu");

	add_shortcode("bii_galaxies", "bii_shared_items_SC_galaxies");

	
	add_action('bii_plugin_test_zone', 'bii_shared_items_test_zone');
/*
	add_action("save_post", "bii_shared_items_save_post");
	add_action("delete_post", "bii_shared_items_delete_post");

	add_action("user_register", "bii_shared_items_add_user");
	add_action("delete_user", "bii_shared_items_remove_user");
	add_action('profile_update', 'bii_shared_items_update_user', 10, 2);
	add_action('updated_user_meta', 'bii_shared_items_update_user_meta', 10, 4);
	 * 
	 */
}
