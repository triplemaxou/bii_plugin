<?php
class paypal_ign_item extends bii_migla_items {
	protected $id;
	protected $mc_gross;
	protected $protection_eligibility;
	protected $address_status;
	protected $payer_id;
	protected $tax;
	protected $address_street;
	protected $payment_date;
	protected $payment_status;
	protected $charset;
	protected $address_zip;
	protected $first_name;
	protected $option_selection1;
	protected $option_selection2;
	protected $option_selection3;
	protected $address_country_code;
	protected $address_name;
	protected $notify_version;
	protected $custom;
	protected $payer_status;
	protected $address_country;
	protected $address_city;
	protected $quantity;
	protected $verify_sign;
	protected $payer_email;
	protected $option_name1;
	protected $option_name2;
	protected $option_name3;
	protected $txn_id;
	protected $payment_type;
	protected $last_name;
	protected $address_state;
	protected $receiver_email;
	protected $shipping_discount;
	protected $insurance_amount;
	protected $pending_reason;
	protected $txn_type;
	protected $item_name;
	protected $discount;
	protected $mc_currency;
	protected $item_number;
	protected $residence_country;
	protected $test_ipn;
	protected $shipping_method;
	protected $transaction_subject;
	protected $payment_gross;
	protected $ipn_track_id;
	
	
	static function getListeProprietes() {
		$array = array(
			"id" => "id",
			"mc_gross" => "montant",
			"payer_id" => "payer_id",
			"payment_date" => "payment_date",
			"payment_status" => "payment_status",
			"custom" => "custom",
			"verify_sign" => "verify_sign",
			"txn_id" => "txn_id",
			"payment_type" => "payment_type",
			"ipn_track_id" => "ipn_track_id",
		);
		return $array;
	}
	
	static function from_transaction_subject($trid){
		$req = "transaction_subject = '$trid'";
		$id = 0;
		$list = static::all_id($req);
		if(count($list)){
			$id = $list[0];
		}
		return new static($id);
	}
	
	static function add($array) {
		$session_id = $array["transaction_subject"];
		$item = new static();
		if (!static::nb("transaction_subject = '$session_id'")) {
			$item->insert();
			$item->updateChamps($array);
		} else {
			$id = static::all_id("transaction_subject = '$session_id'")[0];
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
	
	
}