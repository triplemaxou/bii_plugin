<?php

class bii_user_post extends bii_shared_item {

	protected $id;
	protected $id_bii_user;
	protected $id_bii_instance;
	protected $id_posts;

	static function addpost($id_user_wordpress, $id_post) {
		$id_userbii = bii_user::get_user($id_user_wordpress);
		$instance_id = bii_instance::get_my_id();
		$req = "id_bii_user ='$id_userbii' AND id_bii_instance ='$instance_id' AND id_posts = '$id_post'";
		$nb = static::nb($req);
		if(!$nb){
			$array_insert = [
				"id_bii_user"=>$id_userbii,
				"id_bii_instance"=>$instance_id,
				"id_posts"=>$id_post,				
			];
			
			$item = new static();
			$item->insert();
			$item->updateChamps($array_insert);
		}else{
			$item = static::all_items($req)[0];
		}
		return $item->id();
		
	}

	// */
}
