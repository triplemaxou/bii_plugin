<?php

class bii_shared_product extends bii_shared_item {

	protected $id;
	protected $id_posts;
	protected $lang;
	protected $id_bii_instance;
	protected $link;
	
	

	function option_value() {
		
		return utf8_encode($this->id_posts);
	}
	
	function getInstance(){
		return new bii_instance($this->id_bii_instance);
	}
	
	function update_shared_product($id_posts,$lang,$id_bii_instance,$link){
		$req = "$id_posts = '$id_posts' AND $lang = 'lang'";		
		
	}
	
	
	function synchronize_to_satell(){
		$instance = $this->getInstance();
		$pdo = $instance->get_bdd();
	}
	

}