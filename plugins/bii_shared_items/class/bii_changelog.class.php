<?php

class bii_changelog extends bii_shared_item {

	protected $id;
	protected $version;
	protected $contenu;

	public static function lastChangelogs($limit = 3) {
		$liste = static::all_id("1=1 order by version desc limit 0,$limit");
		foreach($liste as $id){
			$item = new static($id);
			$item->display();
		}
		
	}
	public static function lastVersion() {
		$liste = static::all_id("1=1 order by version desc limit 0,1");
		$item = new static($liste[0]);
		return $item->version();		
	}

	
	public static function getListeProprietesFormEdit() {
		$array = [			
			"version" => "Version",
			"contenu" => "Contenu",
		];
		return $array;
	}
	
	
	public function display() {
		$version = $this->version();
		$contenu = $this->contenu();
		$date = $this->date_modification();
		?>
		<div class="changelog">
			<h3><?= $version ?> : <?= $date ?></h3>
			<div class="changelog-description">
				<?= utf8_encode($contenu); ?>
			</div>
		</div>
		<?php
	}
	
	public function contenu_inputIA() {
		$contenu = $this->contenu();
		?>
		<div id="contenu_div" class="stuffbox <?= static::default_class_stuff(); ?> ">
			<h3><label for="contenu">Contenu</label></h3>
			<div class="inside">
				<?php wp_editor(utf8_encode($contenu), "contenu"); ?>
			</div>
		</div>
		<?php
	}
	
	public static function whereDefault() {
		return "1=1 order by version desc";
	}
}
