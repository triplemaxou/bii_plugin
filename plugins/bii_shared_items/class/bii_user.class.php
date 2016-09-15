<?php

class bii_user extends bii_shared_item {

	protected $id;
	protected $username;
	protected $company;
	protected $name;
	protected $surname;
	protected $address;
	protected $address2;
	protected $zip;
	protected $city;
	protected $country;
	protected $url;
	protected $phone;
	protected $fax;
	protected $mail;
	protected $mail_paypal;
	protected $have_shop;
	protected $hashed_password;
	protected $registred;

	static function add_user($id_user) {
		$userwp = new users($id_user);
//		$userwpmeta = usermeta::all_items("user_id = $id_user");
		$array_meta_to_keep = [
			'first_name', 'last_name', 'description', 'company-name', 'country',
			'icl_admin_language', '_um_verified', 'facebook', 'twitter', 'googleplus',
			'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 'shipping_city',
			'shipping_postcode', 'shipping_country', 'shipping_state',
			'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 'billing_city',
			'billing_postcode', 'billing_country', 'billing_state',
		];
		$values_meta = [];

		foreach ($array_meta_to_keep as $slugmeta) {
			$array = usermeta::all_items("user_id = $id_user AND meta_key = '$slugmeta'");
			$value = "";
			if (is_array($array)) {
				if (isset($array[0])) {
					$value = $array[0]->meta_value();
				}
			}
			$values_meta[$slugmeta] = $value;
		}


//		pre($userwp, "blue");
//		pre($values_meta, "green");

		$array_insert = [
			"username" => $userwp->user_login(),
			"url" => $userwp->user_url(),
			"mail" => $userwp->user_email(),
			"company" => $values_meta["company-name"],
			"name" => $values_meta["first_name"],
			"surname" => $values_meta["last_name"],
			"country" => $values_meta["country"],
			"hashed_password" => $userwp->user_pass(),
			"registred" => $userwp->user_registred(),
		];
		$user = new static();
		$user->insert();
		$user->updateChamps($array_insert);
		$user_id = $user->id();

		foreach ($values_meta as $meta => $value) {
			bii_user_meta::add_or_replace($user_id, $meta, $value);
		}

		return $user_id;
	}

	static function get_user($id_wordpress) {
		$userwp = new users($id_wordpress);
		$mail = $userwp->user_email();
		if (static::count_from_mail($mail)) {
//			pre(static::get_from_mail($mail)->id());
			$ret = static::get_from_mail($mail)->id();
		} else {

			$ret = static::add_user($id_wordpress);
		}
		bii_user_instance::add_user($ret, $id_wordpress);
		return $ret;
	}

	function get_metas() {
		$usermeta = bii_user_meta::all_items("id_user = " . $this->id());
		return $usermeta;
	}
	
	function display_name(){
		$name = $this->name();
		$surname = $this->surname();
		$company = $this->company();
		if($company){
			$display = $company;
		}else if($name && $surname){
			$display = "$surname $name";
		}else{
			$display = $this->username();
		}
		return $display;
	}
	
	function arrayValuesToUpdate(){
		return [
			"user_url"=>$this->url(),
			"display_name"=>$this->display_name(),
			'user_pass'=>$this->hashed_password(),
			'user_registred'=>$this->registred(),
		];
	}

	function synchronize() {
		$username = $this->username();
		$password = $this->hashed_password();
		$email = $this->mail();
		$id_wordpress = wp_create_user($username, $password, $email);
		if (is_int($id_wordpress)) {
			$user = new users($id_wordpress);
			$user->updateChamps($this->arrayValuesToUpdate());
			$usermetas = $this->get_metas();
			foreach ($usermetas as $meta) {
				$meta->synchronize($id_wordpress);
			}
			bii_user_instance::add_synced_user($this->id, $id_wordpress);
		}else{
			pre($id_wordpress);
		}
	}

	static function synchronize_all() {
		$liste_id = bii_user_instance::users_not_in_my_instance();
		foreach ($liste_id as $id_user) {
			$item = new static($id_user);
			$item->synchronize();
		}
	}

	static function get_from_username($username) {
		return static::all_items("username = '$username'")[0];
	}

	static function get_from_mail($mail) {
		return static::all_items("mail = '$mail'")[0];
	}

	static function count_from_mail($mail) {
		return static::nb("mail = '$mail'");
	}

	static function nom_classe_admin() {
		$lang = apply_filters("bii_multilingual_current_language");
		$return = "Utilisateur";
		if ($lang == "en") {
			$return = "Users";
		}
		return $return;
	}

	function option_value() {
		return utf8_encode($this->name);
	}

	static function passerelle_user() {
		
	}

	// */
}
