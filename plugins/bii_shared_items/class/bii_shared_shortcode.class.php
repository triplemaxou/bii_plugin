<?php

class bii_shared_shortcode extends bii_shared_item {

	protected $id;
	protected $shortname;
	protected $shortcode_body;
	protected $commentaire;

	static function nom_classe_admin() {
		return "Shortcodes";
	}

	static function add_shortcodes() {
		$items = static::all_items();
		$basename = "bii_SC_";

		foreach ($items as $item) {
			$shortcodename = $basename . $item->shortname();

			add_shortcode($shortcodename, function() use($shortcodename) {
				bii_shared_shortcode::shortcodefunction($shortcodename);
			});
		}
	}

	static function explained_shortcodes() {
		$items = static::all_items();
		foreach ($items as $item) {

			$item->shortcode_explained();
		}
	}

	static function shortcodefunction($shortname) {
		$body = "";
		$item = static::getfromshortname($shortname);
		if ($item) {
			$body = $this->shortcode_body();			
		}
		return do_shortcode($body);
	}

	static function do_shortcode_shared($atts = [], $content = "", $tag = "") {
		$body = "";
		if (strpos($tag, "bii_SC_") !== false) {
			$shortname = str_replace("bii_SC_", "", $tag);
			$item = static::getfromshortname($shortname);
			if ($item) {

				$body = $this->shortcode_body();
				$body = str_replace('[CONTENT]', $content, $body);
			}
		}
		return do_shortcode($body);
	}

	function shortcode_explained() {
		$basename = "bii_SC_";
		$shortcodename = $basename . $this->shortname();
		?>
		<tr>
			<td><strong>[<?= $shortcodename ?>]</strong></td>
			<td><?= $this->commentaire(); ?></td>
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
