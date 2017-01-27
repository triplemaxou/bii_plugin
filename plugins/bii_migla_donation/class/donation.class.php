<?php

class donation extends bii_migla_items {

	protected $id;
	protected $miglad_session_id;
	protected $miglad_anonymous;
	protected $miglad_repeating;
	protected $miglad_session_id_;
	protected $miglad_amount;
	protected $miglad_campaign;
	protected $miglad_mg_add_to_milist;
	protected $miglad_firstname;
	protected $miglad_lastname;
	protected $miglad_address;
	protected $miglad_country;
	protected $miglad_state;
	protected $miglad_province;
	protected $miglad_city;
	protected $miglad_postal_code;
	protected $miglad_email;
	protected $miglad_date;
	protected $miglad_time;

	function have_paiement() {
		return paypal_ign_item::nb("transaction_subject = $this->miglad_session_id");
	}
	
	function get_paiement(){
		return paypal_ign_item::from_transaction_subject($this->miglad_session_id);
	}
	
	function get_status(){
		return $this->get_paiement()->payment_status();
	}

	static function add($array) {
		$session_id = $array["miglad_session_id"];
		$item = new static();
		if (!static::nb("miglad_session_id = '$session_id'")) {
			$item->insert();
			$item->updateChamps($array);
		} else {
			$id = static::all_id("miglad_session_id = '$session_id'")[0];
			$item = new static($id);
		}
		return $item;
	}
	
	static function editable() {
		return false;
	}
	static function supprimable() {
		return false;
	}
	
	
	static function getListeProprietes() {
		$array = array(
			"id" => "id",
			"miglad_session_id" => "session_id",
			"miglad_amount" => "montant",
			"miglad_campaign" => "campagne",
			"miglad_firstname" => "nom",
			"miglad_lastname" => "prénom",
			"miglad_email" => "mail",
			"get_status" => "statut",
		);
		return $array;
	}

}
