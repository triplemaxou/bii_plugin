<?php

class bii_ambassador extends bii_shared_item {

	protected $id;
	protected $name;
	protected $logo;
	protected $url;
	
	

	function option_value() {
		
		return utf8_encode($this->name);
	}

	// */
}