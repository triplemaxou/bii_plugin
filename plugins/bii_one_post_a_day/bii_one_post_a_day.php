<?php
/*
  Plugin Name: bii_one_post_a_day
  Description: Gestion d'un système d'actualités journalier
  Version: 0.2
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */

define('bii_one_post_a_day_items_version', '0.2');
define('bii_one_post_a_day_items_path', plugin_dir_path(__FILE__));
define('bii_one_post_a_day_items_url', plugin_dir_url(__FILE__));


add_action("bii_informations", function() {
	?>
	<tbody id="bii_one_post_a_day">
		<tr><th colspan="2">Un article par jour</th>
		<tr><td>Un article par jour est </td><td><?= bii_makebutton("bii_use_opad"); ?></td></tr>
	</tbody>
	<?php
});

function bii_include_class_one_post_a_day() {
	$liste_class = [
		"bii_item_opad",
		"bii_posts_opad",
		"bii_opad_spe_date",
	];
//	bii_write_log($liste_class);
	foreach ($liste_class as $class) {
		require_once(bii_one_post_a_day_items_path . "class/$class.class.php");
		if ($class != "bii_item_opad" && class_exists($class)) {
//			bii_custom_log($class);
			if (!$class::table_exists()) {
				$class::autoTable(1);
			}
		}
	}
}

function bii_one_post_a_day_menu() {
	if (class_exists("bii_opad_spe_date")) {
		bii_opad_spe_date::displaySousMenu();
		bii_posts_opad::displaySousMenu();
	}
}

function bii_one_post_a_day_save_post($post_id) {
	$categories = wp_get_post_categories($post_id);
	$find = false;
	foreach ($categories as $id_cat) {
		$cat = get_category($id_cat);
//		pre($cat);
		if (($cat->slug == "une-page-par-jour") || ($cat->slug == "one-page-a-day")) {
			$find = true;
			$catfind = $cat->slug;
		}
	}
	if ($find) {
		$lang = "en";
		if ($catfind == "une-page-par-jour") {
			$lang = "fr";
		}
		bii_posts_opad::add_post($post_id, $lang);
	} else {
		bii_posts_opad::remove_id($post_id);
	}
}

function bii_one_post_a_day_index() {
	return bii_opad_spe_date::get_post_of_the_day();
}

if (get_option("bii_use_opad") && get_option("bii_useclasses")) {
	add_action("bii_after_include_class", "bii_include_class_one_post_a_day", 10);
	add_action("bii_add_menu_pages", "bii_one_post_a_day_menu");


	add_action("save_post", "bii_one_post_a_day_save_post");

	// save_post 
}