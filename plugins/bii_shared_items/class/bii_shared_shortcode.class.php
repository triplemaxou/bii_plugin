<?php

class bii_shared_shortcode extends bii_shared_item {

	protected $id;
	protected $shortname;
	protected $shortcode_body;
	protected $commentaire;

	
	static function nom_classe_admin(){
		return "Shortcodes";
	}
	
	static function add_shortcodes() {
		$items = static::all_items();
		$basename = "bii_SC_";

		foreach ($items as $item) {
			$shortcodename = $basename . $item->shortname();

			add_shortcode($shortcodename, function() use ($item) {
				return $item->shortcode();
			});
		}
	}

	static function explained_shortcodes() {
		$items = static::all_items();
		foreach ($items as $item) {

			$item->shortcode_explained();
		}
	}

	function do_shortcode($atts = [], $content = "") {
		$body = $this->shortcode_body();
		$body = str_replace('[CONTENT]', $content, $body);
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

	// */
}
