<?php

class options extends global_class {

	protected $option_id;
	protected $option_name;
	protected $option_value;
	protected $autoload;

	public static function identifiant() {
		return "option_id";
	}
	
	public static function static_get_option($id){
		$item = new static($id);
		return get_option($item->option_name);
	}
}
