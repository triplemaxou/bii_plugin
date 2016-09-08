<?php
/*
  Plugin Name: Bii advanced shortcodes
  Description: Ajoute des shortcodes avancés
  Version: 1.1
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_advanced_shortcodes', '1.1');

function bii_SC_displaywhenrequest($atts, $content = null) {
	$display = true;
	foreach ($atts as $attr => $value) {
		$display = false;
		if (isset($_REQUEST[$attr]) && ($_REQUEST[$attr] == $value || $value == "all")) {
			$display = true;
		}
	}
	$return = "";
	if ($display) {
		$return = do_shortcode($content);
	}
	return $return;
}

function bii_SC_notdisplaywhenrequest($atts, $content = null) {
	foreach ($atts as $attr => $value) {
		$display = true;
		if (isset($_REQUEST[$attr]) && ($_REQUEST[$attr] == $value || $value == "all")) {
			$display = false;
		}
	}
	$return = "";
	if ($display) {
		$return = do_shortcode($content);
	}
	return $return;
}

function bii_loremipsum($atts, $content = null) {
	if (!isset($atts["lines"])) {
		$atts["lines"] = 10;
	}
	$lines = $atts["lines"];
	$content = file_get_contents("http://loripsum.net/api/$lines/decorate/link/ul");
	return $content;
}

function bii_SC_image_une($atts) {
	$id = null;
	$size = "full";
	if (isset($atts["id"])) {
		$id = $atts["id"];
	}
	if (isset($atts["size"])) {
		$size = $atts["size"];
	}

	return get_the_post_thumbnail($id, $size);
}

function bii_SC_image_une_src($atts) {
	$id = null;
	if (isset($atts["id"])) {
		$id = $atts["id"];
	}
	return wp_get_attachment_thumb_url($id);
}

function bii_SC_tower_titles($atts = [], $content = '') {
	$contents = "";
	if ($atts["titres"]) {

		ob_start();
		?>
		<div class="bii_tower_titles vc_col-xs-5 vc_col-sm-4 vc_col-md-3">
			<?php
			$explode = explode(",", $atts["titres"]);
			foreach ($explode as $titre) {
				?>
				<div class="bii_tower_title <?= strtolower(stripAccents($titre)); ?>" data-affiche="<?= strtolower(stripAccents($titre)); ?>">
					<a href="#"><?= $titre; ?></a>
				</div>
				<?php
			}
			?>

		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
}

function bii_SC_tower_items($atts = [], $content = '') {
	$contents = "";
	ob_start();
	?>
	<div class="bii_tower_items vc_col-xs-7 vc_col-sm-8 vc_col-md-9">
		<?= do_shortcode($content); ?>
	</div> 
	<?php
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function bii_SC_tower_item($atts = [], $content = '') {
	$contents = "";
	if ($atts["titre"]) {
		ob_start();
		?>
		<div class="bii_tower_item <?= strtolower(stripAccents($atts["titre"])); ?> hidden">
			<?= do_shortcode($content); ?>
		</div> 
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
	}
	return $contents;
}
function bii_SC_get_bloginfo($atts = [], $content = '') {
	$contents = "";
	if ($atts["info"]) {		
		$contents = get_bloginfo($atts["info"]);
	}
	return $contents;
}

add_shortcode('bii_displaywhenrequest', 'bii_SC_displaywhenrequest');
add_shortcode('bii_notdisplaywhenrequest', 'bii_SC_notdisplaywhenrequest');
add_shortcode('bii_loremipsum', 'bii_loremipsum');
add_shortcode('bii_imageune', 'bii_SC_image_une');
add_shortcode('bii_imageune_src', 'bii_SC_image_une_src');
add_shortcode('bii_tower_titles', 'bii_SC_tower_titles');
add_shortcode('bii_tower_items', 'bii_SC_tower_items');
add_shortcode('bii_tower_item', 'bii_SC_tower_item');

add_shortcode('bii_getblog', 'bii_SC_get_bloginfo');

add_action("bii_base_shortcodes", function() {
	?>
	<tr>
		<td><strong>[bii_displaywhenrequest cle="valeur"] contenu [/bii_displaywhenrequest]</strong></td>
		<td>Affiche contenu lorsque cle est égal à valeur (si valeur est égal à "all", alors contenu est affiché si cle existe)</td>
	</tr>
	<tr>
		<td><strong>[bii_notdisplaywhenrequest cle="valeur"] contenu [/bii_notdisplaywhenrequest]</strong></td>
		<td>Affiche contenu <strong>sauf</strong> lorsque cle est égal à valeur (si valeur est égal à "all", alors contenu n'est pas affiché si cle existe)</td>
	</tr>
	<tr>
		<td><strong>[bii_imageune] || [bii_imageune id="ID du post" size="full|large|medium|thumbnail"]
			</strong></td>
		<td>Affiche l'image à la une</td>
	</tr>
	<tr>
		<td><strong>[bii_loremipsum]</strong></td>
		<td>Génère du lorem ipsum</td>
	</tr>
	<?php
}, 1);
