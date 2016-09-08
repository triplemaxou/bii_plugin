<?php

class bii_user_meta extends bii_shared_item {

	protected $id;
	protected $id_user;
	
	protected $meta_key;
	protected $meta_value;
	

	function option_value() {
		
		return utf8_encode($this->meta_key);
	}

	static function add_or_replace($id_user,$key,$value = ""){
		$req = "id_user = $id_user AND meta_key = '$key'";
		$allids = static::all_id($req);
		$array_insert = ["meta_value"=>$value];
		$count = count($allids);
		$id = "";
		if(!$count){
			$array_insert["id_user"] = $id_user;
			$array_insert["meta_key"] = $key;
			$item = new static();
			$item->insert();
			$item->updateChamps($array_insert);
			$id = $item->id();
		}else{
			if($count > 1){
				static::deleteWhere($req);
				$id = static::add_or_replace($id_user, $key,$value);
			}else{
				$item = new static($array_insert[0]);
				$id = $item->id();
			}
		}
		return $id;		
	}
	
	
	
	// */
}
