<?php

class bii_user_instance extends bii_shared_item {

	protected $id;
	protected $id_bii_user;
	protected $id_wp_user;
	protected $id_bii_instance;
	protected $is_sync;
	protected $is_active;

	static function add_user($id_bii, $id_wp,$is_sync = 0) {
		$id_instance = bii_instance::get_my_id();
		
		$req = "id_bii_instance = $id_instance and id_wp_user = $id_wp";
		if (!static::nb($req)) {

			$item = new static();
			$array_insert = ["id_bii_instance" => $id_instance, "id_bii_user" => $id_bii, "id_wp_user" => $id_wp, "is_active" => 1, "is_sync" => $is_sync];
			$item->insert();
			$item->updateChamps($array_insert);
			$id = $item->id();
		} else {
			$id = static::all_id($req)[0];
		}
		return $id;
	}
	static function add_synced_user($id_bii, $id_wp){
		return add_user($id_bii, $id_wp,1);
	}
	
	static function users_not_in_my_instance(){
		$instance_id = bii_instance::get_my_id();
		$class_name = static::prefix_bdd() . static::nom_classe_bdd();
		$req = "select distinct id_bii_user from $class_name where id_bii_instance NOT IN('$instance_id') and is_sync = 0";
		$pdo = static::getPDO();
		
		$select = $pdo->query($req);
		$liste = array();
		bii_write_log($req);
//		pre($req,"red");
		while ($row = $select->fetch()) {
			$liste[] = $row['id_bii_user'];
		}

		$pdo = null;
		return $liste;
	}


	// */
}
