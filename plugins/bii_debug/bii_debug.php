<?php
/*
  Plugin Name: BiiDebug
  Description: Ajoute des fonctions de débug, invisibles pour le public
  Version: 2.9
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_debug_version', '2.9');

define('BiiDebug_path', plugin_dir_path(__FILE__));
define('bii_debug_path', plugin_dir_path(__FILE__));
define('bii_debug_url', plugin_dir_url(__FILE__));

define('bii_debug_logs_custom_url', plugin_dir_url(__FILE__) . "output/custom.log");
define('bii_debug_logs_custom_path', plugin_dir_path(__FILE__) . "output/custom.log");

require_once(BiiDebug_path . "/functions.php");

function bii_remove_script_version($src) {
	if (get_option("bii_disallow_querystrings")) {
		$parts = explode('?', $src);
		return $parts[0];
	} else {
		return $src;
	}
}

add_filter('script_loader_src', 'bii_remove_script_version', 15, 1);
add_filter('style_loader_src', 'bii_remove_script_version', 15, 1);

function biidebug_enqueueJS() {
	wp_enqueue_script('util', plugins_url('js/util.js', __FILE__), array('jquery'), false, true);
//
	wp_enqueue_script('lazyload2', plugins_url('js/lazyload.js', __FILE__), array('jquery'), false, true);
	wp_enqueue_script('manual-lazyload', plugins_url('js/manual-lazyload.js', __FILE__), array('jquery', 'lazyload2', 'util'), false, true);
}

add_action('admin_enqueue_scripts', "biidebug_enqueueJS");
add_action('wp_enqueue_scripts', "biidebug_enqueueJS");


if (!(get_option("bii_medium_width"))) {
	update_option("bii_medium_width", 1050);
}
if (!(get_option("bii_small_width"))) {
	update_option("bii_small_width", 985);
}
if (!(get_option("bii_xsmall_width"))) {
	update_option("bii_xsmall_width", 767);
}
if (!(get_option("bii_xxsmall_width"))) {
	update_option("bii_xxsmall_width", 479);
}
if (!(get_option("bii_ipallowed"))) {
	update_option("bii_ipallowed", "192.168.1.1");
}
if (get_option("bii_disallow_emojis") === false) {
	update_option("bii_disallow_emojis", "1");
}

function bii_showlogs() {
	$role = "user";
	if (current_user_can("Activate plugins")) {
		$role = "admin";
	}
	?>
	<script type="text/javascript" src="http://l2.io/ip.js?var=myip"></script>
	<script type="text/javascript" id="varsforbii">
		var ajaxurl = '<?= admin_url('admin-ajax.php'); ?>';
		var bloginfourl = '<?= get_bloginfo("url") ?>';
		var bii_showlogs = false;
		var bii_role = "<?= $role; ?>";
		var bii_lang = "fr";
		var ip_client = myip;
		var ip_allowed = "<?= get_option("bii_ipallowed"); ?>";
		if (ip_allowed.indexOf(ip_client) != -1) {
			bii_showlogs = true;
		}
		var bii_multilingual_activated = false;
		var bii_medium = "(max-width: <?= get_option("bii_medium_width"); ?>px";
		var bii_small = "(max-width: <?= get_option("bii_small_width"); ?>px";
		var bii_xsmall = "(max-width: <?= get_option("bii_xsmall_width"); ?>px";
		var bii_xxsmall = "(max-width: <?= get_option("bii_xxsmall_width"); ?>px";
	<?php do_action("bii_additionnal_js_var"); ?>
	</script>
	<?php
}

add_action('wp_head', 'bii_showlogs');
add_action('admin_head', 'bii_showlogs');

add_action("bii_informations", function() {
	?>
	<tr><td>Les emojis sont  </td><td><?= bii_makebutton("bii_disallow_emojis", 1, 0, true); ?></td></tr>
	<tr><td>Les query string des ressources sont  </td><td><?= bii_makebutton("bii_disallow_querystrings", 1, 1, true); ?></td></tr>

	<?php
});

function bii_canshow_debug() {
	$ipallowed = explode(',',get_option("bii_ipallowed"));	
	return in_array($_SERVER["REMOTE_ADDR"], $ipallowed);
}

/* Retirer emojis */
if (get_option("bii_disallow_emojis")) {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('wp_print_styles', 'print_emoji_styles');

	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('admin_print_styles', 'print_emoji_styles');
}
add_action("bii_options_title", function() {
	?>
	<li role="presentation" class="hide-relative active hide-publier" data-relative="pl-Informations"><i class="fa fa-info"></i> Informations</li>
	<li role="presentation" class="hide-relative " data-relative="pl-Biidebug"><i class="fa fa-cogs"></i> Biidebug</li>
	<li role="presentation" class="hide-relative hide-publier" data-relative="pl-Shortcodes"><i class="fa fa-cog"></i> Shortcodes</li>
	<?php
}, 1);

add_action("bii_options", function() {
	?>
	<div class="col-xxs-12 pl-Informations bii_option">
		<table>
			<?php do_action("bii_informations"); ?>			
		</table>
	</div>
	<div class="col-xxs-12 pl-Biidebug bii_option hidden">
		<?php
		bii_makestuffbox("bii_medium_width", "Pixels maximum md", "number", "col-xxs-12 col-sm-6 col-md-3");
		bii_makestuffbox("bii_small_width", "Pixels maximum sm", "number", "col-xxs-12 col-sm-6 col-md-3");
		bii_makestuffbox("bii_xsmall_width", "Pixels maximum xs", "number", "col-xxs-12 col-sm-6 col-md-3");
		bii_makestuffbox("bii_xxsmall_width", "Pixels maximum xxs", "number", "col-xxs-12 col-sm-6 col-md-3");
		bii_makestuffbox("bii_ipallowed", "Adresses IP de débug", "text", "col-xxs-12 col-sm-6 col-md-3");
		bii_makestuffbox("bii_bodyclass_list", "Liste des classes de body possibles (séparer par des virgules)", "text", "col-xxs-12 col-sm-6 col-md-6");
		bii_makestuffbox("bii_provider", "Bii provider", "text", "col-xxs-12 col-sm-6 col-md-3");

		bii_makestuffbox("bii_analytics_tracking_code", "Code tracking analytics", "textarea", "col-xxs-12");
		?>
		<?php do_action("bii_options_debug"); ?>		
	</div>
	<div class="col-xxs-12 pl-Shortcodes bii_option hidden">
		<div class="col-xxs-12">
			<h3>Base</h3>
			<table>
				<?php do_action("bii_base_shortcodes"); ?>						

				<?php do_action("bii_specific_shortcodes"); ?>						
			</table>
		</div>
	</div>
	<?php
}, 1);
add_filter("bii_options_debug_tableau_check", "bii_options_debug_tableau_check");

function bii_options_debug_tableau_check($toadd = []) {
	$tableaucheck = ["bii_medium_width", "bii_small_width", "bii_xsmall_width", "bii_xxsmall_width", "bii_bodyclass_list", "bii_provider", "bii_ipallowed", "bii_analytics_tracking_code"];
	$toadd2 = apply_filters("bii_options_debug_tableau_check_more", "");
	if (count($toadd)) {
		$tableaucheck = array_merge($tableaucheck, $toadd);
	}
	if (count($toadd2)) {
		$tableaucheck = array_merge($tableaucheck, $toadd2);
	}
	return $tableaucheck;
}

add_filter("bii_options_debug_tableau_check_more", function($v = "") {
	return [];
});

add_action("bii_options_submit", function() {
	$tableaucheck = apply_filters("bii_options_debug_tableau_check", []);
	foreach ($tableaucheck as $itemtocheck) {
		if (isset($_POST[$itemtocheck])) {
			update_option($itemtocheck, $_POST[$itemtocheck]);
		}
	}
}, 5);
if (bii_canshow_debug()) {
	add_action("bii_options_title", function() {
		?>
		<li role="presentation" class="hide-relative hide-publier" data-relative="pl-zdt"><i class="fa fa-wrench"></i> Zone de test</li>
		<?php
	}, 99);
}


add_action("bii_analytics_tracking", "bii_analytics_tracking");

function bii_analytics_tracking() {
	if (get_option("bii_analytics_tracking_code")) {
		echo stripslashes(get_option("bii_analytics_tracking_code"));
	}
}

function bii_custombodyclasses_metaboxes() {
	add_meta_box("bii_custombodyclasses", "Custom Body Classes", "bii_MB_custombodyclasses", ["post", "page"], "side", "low");
}

function bii_MB_custombodyclasses($post) {
	if (get_option("bii_bodyclass_list")) {
		$classes_used = get_post_meta($post->ID, 'bii_custombodyclass', false);
//		pre($classes_used);
		$classes = explode(',', get_option("bii_bodyclass_list"));
		foreach ($classes as $class) {
			$value = 0;
			$checked = "";
			if (array_search($class, $classes_used) !== false) {
				$value = 1;
				$checked = "checked='checked'";
			}
			?>
			<div class="col-sm-6 bii_box">
				<span>
					<label class="bii_label" for="bii_custombodyclass-<?= $class; ?>-cbx"><?= $class; ?></label>
				</span>
				<span>
					<input type='hidden'  id='bii_custombodyclass-<?= $class; ?>' name='bii_custombodyclass[<?= $class; ?>]' value='<?= $value; ?>' />
					<input type='checkbox'  id='bii_custombodyclass-<?= $class; ?>-cbx' name='bii_custombodyclass-<?= $class; ?>-cbx' class='cbx-data-change form-control' data-change='bii_custombodyclass-<?= $class; ?>' <?= $checked ?> />
				</span>
			</div>

		<?php } ?>
		<style>
			.bii_box{
				width:49%;
				display:inline-block;
			}
			.bii_box span:last-child{
				float:right;
			}

		</style>
		<script>
			jQuery(".cbx-data-change").on("click", function () {

				var id = jQuery(this).attr("data-change");

				//				console.log(id);
				var checked = jQuery(this).is(":checked");
				var value = 0;
				if (checked == true) {
					value = 1;

				}
				jQuery("#" + id).val(value);
				//				console.log(jQuery("#" + id));
			});
		</script>
		<?php
	}
}

function bii_bodyclass_list_save_metaboxes($post_ID) {
	if (isset($_POST["bii_custombodyclass"])) {

		$bii_custombodyclass = $_POST["bii_custombodyclass"];
//		bii_custom_log($bii_custombodyclass, "");
		delete_post_meta($post_ID, "bii_custombodyclass");
		foreach ($bii_custombodyclass as $key => $val) {
			if ($val) {
				bii_custom_log(add_post_meta($post_ID, "bii_custombodyclass", $key, false));
			}
		}
	}
}

function bii_bodyclass_add_class($classes, $class = "") {
	$id = get_the_ID();
	$classes_used = get_post_meta($id, 'bii_custombodyclass', false);
	if (is_array($classes_used)) {
		foreach ($classes_used as $key => $classe) {
			$classes[] = $classe;
		}
	}
	return $classes;
}

if (get_option("bii_bodyclass_list")) {
	add_action('add_meta_boxes', 'bii_custombodyclasses_metaboxes');
	add_action('save_post', 'bii_bodyclass_list_save_metaboxes');
	add_filter('body_class', 'bii_bodyclass_add_class', 10, 2);
}

function bii_debug_get_image_sizes() {
	global $_wp_additional_image_sizes;

	$sizes = array();

	foreach (get_intermediate_image_sizes() as $_size) {
		if (in_array($_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
			$sizes[$_size]['width'] = get_option("{$_size}_size_w");
			$sizes[$_size]['height'] = get_option("{$_size}_size_h");
			$sizes[$_size]['crop'] = (bool) get_option("{$_size}_crop");
		} elseif (isset($_wp_additional_image_sizes[$_size])) {
			$sizes[$_size] = array(
				'width' => $_wp_additional_image_sizes[$_size]['width'],
				'height' => $_wp_additional_image_sizes[$_size]['height'],
				'crop' => $_wp_additional_image_sizes[$_size]['crop'],
			);
		}
	}

	return $sizes;
}

function bii_debug_test_zone() {
//	pre(bii_debug_get_image_sizes());
}

add_action('bii_plugin_test_zone', 'bii_debug_test_zone');

function bii_debug_reduce_weight_thumnail_massive($return, $method) {
	$return = str_replace("style=", "style='background-image: url(" . Bii_url . "img/loader250x250.gif);' data-style=", $return);
	global $post;
	$thumbnailsrc = wp_get_attachment_image_src(get_post_meta($post->ID, '_thumbnail_id', true), "shop_catalog");
//	pre($thumbnailsrc);
	$formersrc = wp_get_attachment_url(get_post_meta($post->ID, '_thumbnail_id', true));
	$return = str_replace($formersrc, $thumbnailsrc[0], $return);
//	pre($formersrc);


	return $return;
}

add_filter("ma/product/build/thumbnail", "bii_debug_reduce_weight_thumnail_massive");


function bii_debug_wpb_getImageBySize( $params = array() ) {
//	pre($params);
	$params["thumbnail"] = str_replace("src=", 'src="data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs=" data-original=', $params["thumbnail"]);
	$params["thumbnail"] = str_replace("srcset=", 'data-srcset=', $params["thumbnail"]);
	return $params;
}

add_filter("vc_wpb_getimagesize", "bii_debug_wpb_getImageBySize",1);
