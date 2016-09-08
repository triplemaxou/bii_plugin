<?php

class bii_page_perso_layout extends bii_item_page_perso {

	protected $id;
	protected $titre_fr;
	protected $titre_en;
	protected $description_fr;
	protected $description_en;
	protected $image;
	protected $layout;

	static function getPDO() {
		return ppdo::getInstance();
	}

	static function prefix_bdd() {
		return "";
	}

	static function supprimable() {
		return true;
	}

	function titre($lang = "fr") {
		$nom_methode = "titre";
		return $this->call_lang($nom_methode, $lang);
	}

	static function nom_classe_admin() {
		return "Layout";
	}

	function option_value() {
		$lang = apply_filters("bii_multilingual_current_language");
		return utf8_encode($this->{"titre_$lang"}() . " <em>" . $this->{"description_$lang"}() . "</em>");
	}

	function get_layoutform() {
		$layout = $this->layout();
		ob_start();
		?>
		<div class='bii_inside_layout'>
		<?= do_shortcode($layout); ?>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	///*
	function layout_inputIA() {
		$value = $this->layout();
		?>
		<div id="layout_div" class="stuffbox col-xxs-12 col-xs-12 ">
			<h3>
				<label for="layout">Layout</label>
			</h3>
			<div class="inside">
				<textarea id="layout" name="layout" class="form-control " rows="20" type="text"><?= $value ?></textarea>
				<p></p>
			</div>
		</div>
		<?php
	}

	public static function autoTable($is_autoinserted = false) {
		$scriptSQL = "";
		if (!static::onBase()) {
			$scriptSQL = "CREATE TABLE IF NOT EXISTS `bii_page_perso_layout` (
				`id` int(11) NOT NULL,
				  `titre_fr` varchar(255) DEFAULT NULL,
				  `titre_en` varchar(255) DEFAULT NULL,
				  `description_fr` varchar(255) DEFAULT NULL,
				  `description_en` varchar(255) DEFAULT NULL,
				  `image` varchar(255) DEFAULT NULL,
				  `layout` text DEFAULT NULL,
				  `date_insert` int(11) DEFAULT NULL,
				  `date_modification` int(11) DEFAULT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
			if ($is_autoinserted) {
				$pdo = static::getPDO();
				$pdo->query($scriptSQL);
				bii_custom_log("[TABLE CREATED] $scriptSQL");
				update_option("bii_table_" + static::nom_classe_bdd() + "_created", "1");
			}
		}
		return $scriptSQL;
	}

	// */
}
