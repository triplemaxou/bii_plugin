<?php

class bii_posts_opad extends bii_item_opad {

	protected $id;
	protected $id_posts;
	protected $lang;
	
	static function remove_id($id_post){
		$req = "id_posts = $id_post";
		static::deleteWhere($req);
	}
	
	static function add_post($id_post,$lang){
		$req = "id_posts = $id_post AND lang ='$lang'";
		if(!static::nb($req)){
			$item = new static();
			$item->insert();
			$item->updateChamps($id_post, "id_posts");
			$item->updateChamps($lang, "lang");
		}
	}
}
