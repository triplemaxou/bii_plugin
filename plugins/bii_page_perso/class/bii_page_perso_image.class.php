<?php
class bii_page_perso_image extends bii_item_page_perso {

	protected $id;
	protected $id_page_perso;
	protected $post_id_main;
	protected $post_id_sattel;
	protected $ordre;
	protected $image;
	protected $image_type;
	protected $is_synchro_en_fr;
//	
//	function image($lang = "fr"){
//		$nom_methode = "image";
//		$val = $this->call_lang($nom_methode, $lang);
//		if(!$val){
//			$lang2 = "fr";
//			if($lang == "fr"){
//				$lang2 = "en";				
//			}
//			$val = $this->call_lang($nom_methode, $lang2);
//		}
//	}
	
	protected function call_satell($methode, $is_satell) {
		$satell = "_main";
		if ($is_satell) {
			$satell = "_sattel";
		}
		$var = "$methode$satell";
		return $this->$var();
	}
	function post_id($satell = true){
		$val = $this->call_satell("post_id", $satell);
		return $val;
	}
	function id_post($satell = true){
		return $this->post_id($satell);
	}
//	
	
	
	static function supprimable() {
		return true;
	}
	
	
}