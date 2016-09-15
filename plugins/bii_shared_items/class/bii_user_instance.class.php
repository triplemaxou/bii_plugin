<?php

class bii_user_instance extends bii_shared_item {

	protected $id;
	protected $id_bii_user;
	protected $id_wp_user;
	protected $id_bii_instance;
	protected $is_sync;
	protected $is_active;

	static function add_user($id_bii, $id_wp) {
		$id_instance = bii_instance::get_my_id();
		
		$req = "id_bii_instance = $id_instance and id_wp_user = $id_wp";
		if (!static::nb($req)) {

			$item = new static();
			$array_insert = ["id_bii_instance" => $id_instance, "id_bii_user" => $id_bii, "id_wp_user" => $id_wp, "is_active" => 1, "is_sync" => 0];
			$item->insert();
			$item->updateChamps($array_insert);
			$id = $item->id();
		} else {
			$id = static::all_id($req)[0];
		}
		return $id;
	}


	// */
}
