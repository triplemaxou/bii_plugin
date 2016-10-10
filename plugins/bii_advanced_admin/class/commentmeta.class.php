<?php

class commentmeta extends global_class {

	protected $meta_id;
	protected $comment_id;
	protected $meta_key;
	protected $meta_value;

	public static function identifiant() {
		return "meta_id";
	}

	public static function delete_orphans(){
		$prefix = static::prefix_bdd();
		static::deleteWhere("comment_id not IN (select comment_ID from $prefix"."comments)");
	}
}
