<?php
/*
  Plugin Name: Bii_wc
  Description: Gestion d'un système de compte unique à plusieurs wordpress
  Version: 0.2
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */

define('bii_wc_version', '0.2');
define('bii_wc_path', plugin_dir_path(__FILE__));
define('bii_wc_url', plugin_dir_url(__FILE__));

function bii_wc_savepost($post_id) {
	$post_gb = new posts($post_id);
	if ($post_gb->post_type() == "product") {
		$product_image_gallery = get_post_meta($post_id, "_product_image_gallery", true);

		add_post_meta($post_id, "bii_mini_galerie", bii_wc_SC_gallery_3img(["ids" => $product_image_gallery]));
	}
}

function bii_wc_SC_gallery_3img($args = [], $content = "") {
	$contents = "";
	if ($args["id_post"]) {
		$ids = explode(",", get_post_meta($args["id_post"], "_product_image_gallery", true));
//		pre($ids);
	}

	if ($args['ids']) {
		$ids = explode(",", $args['ids']);
	}

	if ($ids) {
		$count = count($ids);
		$class = "vc_col-xs-4 col-xs-4";

		if ($count > 3) {
			$ids = [$ids[0], $ids[1], $ids[2]];
		}
		if ($count) {
			$basesrc = "src='" . Bii_url . "/img/loader250x250.gif' data-original";
			if ($args["no-lazyload"]) {
				$basesrc = "src";
			}
			ob_start();
			?>
			<div class="bii-mini-gallery">
				<?php
				$i = 1;
				foreach ($ids as $id_post) {
					$src = wp_get_attachment_image_src($id_post, 'thumbnail', true)[0];
					if ($count == 1) {
						?>
						<div class="bii-mini-gallery-item <?= $class ?> id-dummy">
							<div class="bii-mini-gallery-thumbnail">
							</div>
						</div>
						<?php
					}
					?>
					<div class="bii-mini-gallery-item <?= $class ?> id-<?= $id_post ?>">
						<div class="bii-mini-gallery-thumbnail">
							<img class="lazyquick" height="150" width="150" alt="galerie-image-<?= $i ?>" <?= $basesrc ?>="<?= $src ?>"/>
						</div>
					</div>
					<?php
					if ($count == 1 || ($count == 2 && $i == 1)) {
						?>
						<div class="bii-mini-gallery-item <?= $class ?> id-dummy">
							<div class="bii-mini-gallery-thumbnail">
							</div>
						</div>
						<?php
					}
					++$i;
				}
				?>

			</div>

			<?php
			$contents = ob_get_contents();
			ob_end_clean();
		}
	}
	return do_shortcode($contents);
}

function bii_wc_informations() {
	if (class_exists("WooCommerce")) {	
	?>
	<tbody id="bii_wc">
		<tr><th colspan="2">Bii_woocommerce</th>
		<tr><td>Les options supplémentaires pour woocommerce sont </td><td><?= bii_makebutton("bii_add_wc_options", 1, 1); ?></td></tr>
	</tbody>
	<?php
}
}

add_action("bii_informations", "bii_wc_informations");

function bii_WC_testZone() {
//	echo "test";
//	echo do_shortcode("[bii-mini-gallery-3 genfrompost=1]");
}

function bii_WC_column_post($columns) {
	$toremove = ["wpseo-title", "product_tag", "sku", "wpseo-score", "wpseo-score-readability", "wpseo-metadesc", "wpseo-focuskw", "product_type"];
	foreach ($toremove as $remove) {
		if (isset($columns[$remove])) {
			unset($columns[$remove]);
		}
	}

	return $columns;
}

function bii_WC_Carturl($url) {
	$bloginfourl = get_bloginfo("url");

	if (strpos($bloginfourl, "-market") === false) {
//		pre($bloginfourl);
		$url = bii_instance::get_market()->url();
		$url = "$url/panier/";
//		pre($url);
	}
	return $url;
}

function bii_WC_product_link($link, $post) {
//	pre($link);
	if ($post->post_type == "product" && get_option("bii_use_shared_items")) {
		
	}
	return $link;
}

function bii_WC_maproduct_link($title, $var = "") {

	if (get_option("bii_use_shared_items")) {

		$urlmarket = bii_instance::get_market()->url();
		$title = str_replace(get_bloginfo("url"), $urlmarket, $title);
//				pre($title);
	}
	return $title;
}

function bii_WC_dashboard() {
	?>
	<div class="bii_WC_dashboard col-xxs-12 col-sm-6">
		<h2>Statistiques</h2>
		<ul>
			<li>Nombre de vendeurs :  <?= users::nb("ID in (select distinct user_id FROM " . usermeta::nom_classe_bdd() . " where meta_key = 'wp_biimarket_capabilities' AND meta_value like '%publish_products%')") * 1 - 1 ?></li>

			<li>Nombre de produits : <?= posts::nb("post_type = 'product' AND post_status = 'publish'") ?></li>
			<li>Nombre de produits en attente : <?= posts::nb("post_type = 'product' AND post_status = 'pending'") ?></li>
			<li>Nombre de produits en brouillon : <?= posts::nb("post_type = 'product' AND post_status = 'draft'") ?></li>

		</ul>
	</div>
	<?php
}

add_filter('manage_product_posts_columns', 'bii_WC_column_post', 12, 1);
add_filter('woocommerce_get_cart_url', 'bii_WC_Carturl', 12, 1);

if (get_option("bii_add_wc_options") && get_option("bii_useclasses")) {
	add_shortcode("bii-mini-gallery-3", "bii_wc_SC_gallery_3img");
	add_action('bii_plugin_test_zone', 'bii_WC_testZone');
	add_action('save_post', 'bii_wc_savepost');
	add_filter('post_link', 'bii_WC_product_link', 10, 2);
	add_filter('ma/product/get/title', 'bii_WC_maproduct_link', 10, 2);

	add_action("bii_dashboard_content", "bii_WC_dashboard");
}
