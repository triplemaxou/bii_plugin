<?php

class bii_shared_shortcode extends bii_shared_item {

	protected $id;
	protected $shortname;
	protected $shortcode_body;

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

	function do_shortcode($atts = [], $content = "") {
		$body = $this->shortcode_body();
		$body = str_replace('[CONTENT]', $content, $body);
		return do_shortcode($body);
	}

	// */
}
