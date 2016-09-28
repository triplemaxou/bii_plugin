<?php

class users extends global_class {

	protected $ID;
	protected $user_login;
	protected $user_pass;
	protected $user_nicename;
	protected $user_email;
	protected $user_url;
	protected $user_registred;
	protected $user_activation_key;
	protected $user_status;
	protected $display_name;

	public function user_email() {
		return $this->user_email;
	}

	public static function identifiant() {
		return "ID";
	}

	public function get_rsItems() {
		$liste = usermeta::multiple_from_id_key($this->id(), "requete_sauvegardee");
		pre($liste,'red');
		$listeRS = [];
		if ((bool) $liste) {
			foreach ($liste as $id) {
				$item = new usermeta($id);
				$listeRS[] = $item;
			}
		}
//		pre($listeRS,"#A4B0CA");
		return $listeRS;
	}

	public static function users_alert() {
		$users = static::all_id();
		$alerts = [];
		foreach ($users as $user_id) {
			$user = new static($user_id);
			$alerts[$user_id] = $user->get_rsItems();
		}
		return $alerts;
	}

	public static function users_search() {
		$users = static::all_id();
		$alerts = [];
		foreach ($users as $user_id) {
			$user = new static($user_id);
			$alerts[$user_id] = $user->getBiensSearched();
		}
		return $alerts;
	}

	public function getBiensSearched() {
		$liste_biens = [];
		$rs_list = $this->get_rsItems();
		foreach ($rs_list as $rsItem) {
			$liste_biens = array_merge($liste_biens, $rsItem->liste_biens());
		}
		
		return $liste_biens;
	}

	public static function sendmailToAll() {
		$users = static::all_id();
		foreach ($users as $user_id) {
			$user = new static($user_id);
			$user->sendmail();
		}
//		registred_dates::insertorupdate("date_envoi_mail");
	}

	public function sendmail() {
		$liste = $this->getBiensSearched();
		
		if (count($liste)) {
			$to_email = $this->user_email;
			$from_email = "contact@lemaistre-immo.com";
			$email_subject = "Votre alerte mail sur " . get_bloginfo("name");


			$email_body = annonce::mailFromListe($liste,10);

			$header = 'Content-type: text/html; charset=utf-8' . "\r\n";

			$header .= 'From: ' . get_bloginfo("name") . " <" . $from_email . "> \r\n";

//			wp_mail($to_email, $email_subject, $email_body, $header);
			pre($to_email,"red");
			debugEcho($email_body);
		}
	}

	public function option_value() {
		return $this->user_login;
	}
	
	public function set_commission_percentage() {
		$date = get_user_meta($this->ID(), 'date_crea_entreprise', true);

		if ($date) {
			$time = time();
			$date_expl = explode("-", $date);
			$jour = $date_expl[1];
			$mois = $date_expl[2];
			$annee = $date_expl[0];
			$val = mktime(0, 0, 0, $mois, $jour, $annee);
			$twoyears = $time - (2 * 365 * 24 * 3600);
			$datetwoyears = $time - $twoyears;
			if ($val < $datetwoyears) {
				update_user_meta($this->ID(), "_vendor_commission_percentage", "0");
				update_user_meta($this->ID(), "_vendor_commission_fixed_with_percentage", "0");
			}
		}
	}
}
