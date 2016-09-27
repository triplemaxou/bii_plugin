<?php

class bii_shared_shortcode extends bii_shared_item {

	protected $id;
	protected $shortname;
	protected $shortcode_body;
	protected $commentaire;

	static function nom_classe_admin() {
		return "Shortcodes";
	}

	static function explained_shortcodes() {
		$items = static::all_items();
		foreach ($items as $item) {

			$item->shortcode_explained();
		}
	}

	static function do_shortcode_shared($atts = [], $content = "", $tag = "") {
		$body = "";
		if (isset($atts["name"])) {
			$shortname = $atts["name"];
//			pre($shortname);
			$item = static::getfromshortname($shortname);
			if ($item) {
				$body = $item->shortcode_body();
				
				$body = str_replace('[CONTENT]', $content, $body);
			}
		}
//		pre($body);
		return do_shortcode($body);
	}

	function shortcode_explained() {
		$basename = "bii_shared_shortcode name='";
		$shortcodename = $basename . $this->shortname() . "'";
		?>
		<tr>
			<td><strong>[<?= $shortcodename ?>]</strong></td>
			<td><?= utf8_encode($this->commentaire()); ?></td>
		</tr>
		<?php
	}

	static function getfromshortname($shortname) {
		$items = static::all_items("shortname = '$shortname'");
		$ret = null;
		if (count($items)) {
			$ret = $items[0];
		}
		return $ret;
	}

	// */
}
