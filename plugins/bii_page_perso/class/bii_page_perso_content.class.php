<?php

class bii_page_perso_content extends bii_item_page_perso {

	protected $id;
	protected $id_page_perso;
	protected $contenu;
	protected $options;

	static function supprimable() {
		return true;
	}

	static function getFormEmpty() {
		?>

		<?php

	}
	
	function contentFront(){
		$options = explode(",", $this->options());

		$value = $this->contenu;
		$r = $value;
		if (in_array("fadein", $options)) {
			$r = "[ult_animation_block animation='fadeIn' animation_duration='3' animation_delay='0' animation_iteration_count='1']$r"."[/ult_animation_block]";
		}

		return $r;
	}

}
