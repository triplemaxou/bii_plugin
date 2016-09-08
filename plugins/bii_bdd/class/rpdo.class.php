<?php

class wpdbExtended extends wpdb {

	public function __construct(wpdb $wpdbItem) {
		$dbuser = $wpdbItem->dbuser;
		$dbpassword = $wpdbItem->dbpassword;
		$dbname = $wpdbItem->dbname;
		$dbhost = $wpdbItem->dbhost;

		parent::__construct($dbuser, $dbpassword, $dbname, $dbhost);
	}

	public function connexionArray() {
		$connexion = array();
		$connexion["host"] = $this->dbhost;
		$connexion["name"] = $this->dbname;
		$connexion["user"] = $this->dbuser;
		$connexion["pwd"] = $this->dbpassword;
		return $connexion;
	}

}

class cpdo {

	private static $instance = null;

	public static function getInstance() {
		//singleton
		if (!get_option("bii_use_bddcommunes")) {
			throw new Exception("Vous essayez d'utiliser la base de données des communes alors qu'elle n'est pas initialisée", "1719");
		}
		if (!get_option("bii_host_bddcommunes") || !get_option("bii_name_bddcommunes") || !get_option("bii_user_bddcommunes") || !get_option("bii_pwd_bddcommunes")) {
			throw new Exception("Vous essayez d'utiliser la base de données des communes alors qu'elle n'est pas configurée", "1720");
		}
		if (!self::$instance) {

			$rpdo_host = get_option("bii_host_bddcommunes");
			$rpdo_name = get_option("bii_name_bddcommunes");
			$rpdo_user = get_option("bii_user_bddcommunes");
			$rpdo_pwd = get_option("bii_pwd_bddcommunes ");

			$db = new PDO('mysql:host=' . $rpdo_host . ';dbname=' . $rpdo_name, $rpdo_user, $rpdo_pwd);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			self::$instance = $db;
		}
		return self::$instance;
	}

}

class ppdo {

	private static $instance = null;

	public static function getInstance() {
		//singleton
		if (!get_option("bii_use_bddplugin")) {
			throw new Exception("Vous essayez d'utiliser la base de données du plugin alors qu'elle n'est pas initialisée", "1721");
		}
		if (!get_option("bii_host_bddplugin") || !get_option("bii_name_bddplugin") || !get_option("bii_user_bddplugin") || !get_option("bii_pwd_bddplugin")) {
			throw new Exception("Vous essayez d'utiliser la base de données du plugin alors qu'elle n'est pas configurée", "1722");
		}
		if (!self::$instance) {

			$rpdo_host = get_option("bii_host_bddplugin");
			$rpdo_name = get_option("bii_name_bddplugin");
			$rpdo_user = get_option("bii_user_bddplugin");
			$rpdo_pwd = get_option("bii_pwd_bddplugin");


			$db = new PDO('mysql:host=' . $rpdo_host . ';dbname=' . $rpdo_name, $rpdo_user, $rpdo_pwd);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			self::$instance = $db;
		}
		return self::$instance;
	}

}

class rpdo {

//Base de connées du site
	private static $instance = null;

	public static function getInstance() {
		//singleton
		if (!self::$instance) {
			global $wpdb;
			$wpextended = new wpdbExtended($wpdb);
			$connexionArray = $wpextended->connexionArray();

			$rpdo_host = $connexionArray["host"];
			$rpdo_name = $connexionArray["name"];
			$rpdo_user = $connexionArray["user"];
			$rpdo_pwd = $connexionArray["pwd"];

			$db = new PDO('mysql:host=' . $rpdo_host . ';dbname=' . $rpdo_name, $rpdo_user, $rpdo_pwd);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

			self::$instance = $db;
		}
		return self::$instance;
	}

	public static function getConnexionVendeur($vendeur) {
		$db = new PDO('mysql:host=' . $vendeur->pdo_host() . ';dbname=' . $vendeur->pdo_name(), $vendeur->pdo_user(), $vendeur->pdo_pwd());
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		return $db;
	}

}
