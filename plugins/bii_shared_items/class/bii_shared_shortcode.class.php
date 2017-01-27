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
			$instance = bii_instance::get_me();

			$shortname = $atts["name"];
//			pre($shortname);
			$item = static::getfromshortname($shortname);
			if ($item) {
				$body = $item->shortcode_body();

				$body = str_replace('[CONTENT]', $content, $body);

				$percent = 1;
				if (isset($atts["percentcolor"])) {
					$percent = $atts["percentcolor"];
				}
				if (strpos($body, '|bii-mycolor|') !== false) {
					$body = str_replace('|bii-mycolor|', $instance->color($percent), $body);
				}
				if (strpos($body, '|bii-myshortname|')!== false) {
					$body = str_replace('|bii-myshortname|', $instance->anchorname(), $body);
				}
				if (strpos($body, '|bii-mybiiname|')!== false) {
					$body = str_replace('|bii-mybiiname|', $instance->shortcode_name(), $body);
				}
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
	
	function shortcode_body_inputIA(){
		$value = $this->shortcode_body();
		?>
		<div id="shortcode_body_div" class="stuffbox col-xxs-12 col-xs-12 ">
			<h3>
				<label for="shortcode_body">
					Shortcode_body
				</label>
			</h3>
			<div class="inside">
				<textarea id="shortcode_body" name="shortcode_body" class="form-control"><?= utf8_encode($value); ?></textarea>
				<p>
				<ul>				
					<li>Entrez |bii-mycolor| pour mettre la couleur actuelle du site</li>
					<li>Entrez |bii-myshortname| pour mettre le nom slug du site</li>
					<li>Entrez |bii-mybiiname| pour mettre le nom du site exemple <strong>Bii</strong>Tech</li>
				</ul>
				</p>
			</div>
		</div>
		<?php
	}
	function commentaire_inputIA(){
		$value = $this->commentaire();
		?>
		<div id="commentaire_div" class="stuffbox col-xxs-12 col-xs-12 ">
			<h3>
				<label for="commentaire">
					Commentaire
				</label>
			</h3>
			<div class="inside">
				<textarea id="commentaire" name="commentaire" class="form-control"><?= utf8_encode($value); ?></textarea>
				<p></p>
			</div>
		</div>
		<?php
	}

	// */
}
