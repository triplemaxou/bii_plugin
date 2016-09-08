<?php

class bii_user_instance extends bii_shared_item {

	protected $id;
	protected $id_bii_user;
	protected $id_wp_user;
	protected $instance;
	protected $hashed_password;
	protected $is_active;

	static function add_user($instance,$id_bii,$id_wp,$pwd) {
		$item = new static();
		$array_insert = ["instance" => $instance,"id_bii_user"=>$id_bii,"id_wp_user"=>$id_wp,"hashed_password"=>$pwd,"is_active"=>1];
		$item->insert();
		$item->updateChamps($array_insert);
	}

	static function get_user_in_instance($instance,$id_wp){
		$return = 0;
		$req = "instance = $instance and id_wp_user = $id_wp";
		if(static::nb($req)){
			$ids = static::all_id($req);
			pre($ids);
			$item = new static($ids[0]);
			$return = $item->id_bii_user();
		}
		return $return;
	}

	// */
}
