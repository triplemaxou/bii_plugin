<?php

class bii_finance_entreprise extends bii_finance_item {

	protected $id;
	protected $symbol;
	protected $last_price;
	protected $change_rate;
	protected $name;
	protected $category_symbol;

	static function from_symbol($symbol) {
		$liste = static::all_items("symbol = '$symbol' order by date_insert desc");
		if (count($liste)) {
			return $liste[0];
		} else {
			return new static();
		}
	}

	static function add_new($symbol, $price = "", $change = "", $name = "",$category_symbol="") {
		$arraytoinsert = [];
		if (is_array($symbol)) {
			$arraytoinsert = $symbol;
		} else {
			$arraytoinsert["symbol"] = $symbol;
			$arraytoinsert["last_price"] = $price;
			$arraytoinsert["change_rate"] = $change;
			$arraytoinsert["name"] = $name;
			$arraytoinsert["category_symbol"] = $category_symbol;
		}
//		pre($arraytoinsert,"purple");
		$item = new static();
		$item->insert();
		$item->updateChamps($arraytoinsert);
		return $item->id();
	}

	static function delete_old($days = 3) {
		//On supprime tous les items qui ont plus de $days jours
		$now = time();
		$xdaystmstp = $days * 24 * 3600;
		$date_old = $now - $xdaystmstp;
		$where = "date_insert < $date_old or symbol IS NULL";
		static::deleteWhere($where);
	}
	
	static function get_Cac40Ivalues(){
		$category_symbol = "^FCHI";		
		return static::get_GroupValues($category_symbol);
	}
	static function get_DJIvalues(){
		$category_symbol = "^DJI";		
		return static::get_GroupValues($category_symbol,3,3,1,1);
	}
	
	static function get_GroupValues($category_symbol,$nb_best = 7,$nb_worst = 7,$nbtall = 2,$nb_small = 2){
		$best7 = static::get_top($category_symbol,"change_rate","desc",$nb_best);
		$worst7 = static::get_top($category_symbol,"change_rate","asc",$nb_worst);
		$taller = static::get_top($category_symbol,"last_price","desc",$nbtall);
		$smaller = static::get_top($category_symbol,"last_price","asc",$nb_small);
		
		return array_unique(array_merge($best7, $worst7,$taller,$smaller));
	}

	static function get_top($category_symbol,$type = "change_rate", $ordre = "desc", $limit = 7) {
		$what = "last_price";
		if ($type == "change_rate") {
			$what = $type;
		}
		$now = time();
		$xhourstmstp = 3 * 3600;
		$tmstp = $now-$xhourstmstp;
		
		$where = "category_symbol='$category_symbol' and date_insert > $tmstp ORDER BY $what $ordre limit 0,$limit";
		$liste = static::request($where);
		return $liste;
	}

	static function request($where = "") {
		$pdo = static::getPDO();
		$class_name = static::prefix_bdd() . static::nom_classe_bdd();
		$req =  "select distinct symbol from " . $class_name;
		if ($where != "") {
			$req .= " where " . $where;
		}
//		pre($req,"red");
		$select = $pdo->query($req);
		$liste = array();
		while ($row = $select->fetch()) {
			$liste[] = $row["symbol"];
		}
		return $liste;
		
	}
	

}
