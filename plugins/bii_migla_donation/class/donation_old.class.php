<?php

class donation extends bii_migla_items {

	protected $id;
	protected $chrono;
	protected $nom;
	protected $prenom;
//	protected $entreprise;
	protected $adresse;
	protected $ville;
	protected $code_postal;
	protected $montant;
	protected $email;
	protected $numero_transaction_paypal;
	protected $migla;
	protected $etat;
	protected $lien_recu;

	public static function getListeProprietes() {
		$array = [
			"id" => "id",
			"chrono" => "N°chrono",
			"date_insert" => "date",
			"nom" => "nom",
			"prenom" => "prénom",
			"adresse" => "adresse",
			"code_postal" => "code postal",
			"ville" => "ville",
			"montant" => "montant",
			"migla" => "migla",
			"numero_transaction_paypal" => "N° de Transaction Paypal",
			"etat" => "État",
			"lien_recu" => "Reçu Fiscal",
		];
		return $array;
	}

	public static function getListeProprietesExportCsv() {
		$array = [
			"chrono" => "N°chrono",
			"nom" => "nom",
			"prenom" => "prénom",
			"adresse" => "adresse",
			"code_postal" => "code postal",
			"ville" => "ville",
			"montant" => "montant",
			"numero_transaction_paypal" => "N° de Transaction Paypal"
		];
		return $array;
	}

	public function lien_recu_ligneIA() {
		$lr = $this->lien_recu;
		$etat = $this->etat;
		?>
		<td class="lien_recu">
			<?php
			if ($etat == utf8_decode("payé")) {
				if (!$lr) {
					$this->save_pdf();
					$lr = $this->lien_recu;
				}
				?><a href='<?= $lr; ?>' class='btn btn-info' title="télécharger le rapport"><i class="fa fa-download"></i></a><?php
			}
			?>
		</td>
		<?php
	}
	
	public static function nom_classe_admin() {
		return "don";
	}

	public static function exportable() {
		return true;
	}

	public static function editable() {
		return false;
	}

	public static function supprimable() {
		return false;
	}

	public static function feminin() {
		return true;
	}

	public static function display_filter() {
		return true;
	}

	public static function filters_form_arguments($array_selected = array()) {
		?>
		<option class="nb" value="id" data-oldval="id" >Id</option>
		<option class="text" value="chrono" data-oldval="chrono" >N°Chrono</option>
		<option class="text" value="nom" data-oldval="nom" >Nom</option>
		<option class="text" value="prenom" data-oldval="prenom" >Prénom</option>
		<option class="text" value="adresse" data-oldval="adresse" >Adresse</option>
		<option class="text" value="ville" data-oldval="ville" >Ville</option>

		<option class="nb" value="montant" data-oldval="nb" >Montant</option>
		<option class="text" value="numero_transaction_paypal" data-oldval="numero_transaction_paypal" >N°Transaction Paypal</option>
		<option class="text" value="etat" data-oldval="etat" >État</option>

		<?php
	}

	public function option_value() {
		return $this->chrono();
	}

	public function chrono() {
		$chrono = $this->chrono;
		if (!$chrono) {
			$chrono = $this->makechrono();
			$this->updateChamps($chrono, "chrono");
		}
		return $chrono;
	}

	public function makechrono() {
		$date = $this->date_insert_tmstp();
		$annee = date("Y", $date);
		$mois = date("m", $date);
		$id = $this->id;
		return "eD-$annee-$mois-$id";
	}

	public static function add($array = []) {
		$where = static::requestbuilder($array);
		$array = static::cleanArray($array);
		pre($where, "red");
		if (!static::exists($where)) {
			$ad = new static();
			$ad->insert();
			$ad->updateChamps($array);
		} else {
			$liste = static::all_id($where);
			$ad = new static($liste[0]);
		}
		return $ad;
	}

	public function as_array() {
		$array = $this->tabPropValeurs();
		unset($array["id"]);
		return $array;
	}

	public static function exists($where) {
		if (static::nb($where)) {
			return true;
		}
		return false;
	}

	protected static function cleanArray($array = []) {
		$newarray = [];
		foreach ($array as $key => $val) {
			if ($val) {
				$newarray[$key] = $val;
			}
		}
		return $newarray;
	}

	protected static function requestbuilder($array = []) {
		$where = "0=1";
		$item = new static();
		$tabprop = $item->tabPropValeurs();
		$tabkeys = array_keys($tabprop);
		$array = static::cleanArray($array);
		foreach ($array as $key => $item) {
			if (in_array($key, $tabkeys) && $item) {
				if ($where == "0=1") {
					$where = "1=1";
				}
				$where .= " AND $key = \"$item\"";
			}
		}
		return $where;
	}

	public static function whereDefault() {
		return "1=1";
	}

	public static function from_session($sessionid) {
		$where = "migla = '$sessionid'";
		$liste = static::all_id($where);
		if ((bool) $liste) {
			return new static($liste[0]);
		} else {
			return new static();
		}
	}

	public static function from_transaction($transactionid) {
		$where = "numero_transaction_paypal = '$transactionid'";
		$liste = static::all_id($where);
		if ((bool) $liste) {
			return new static($liste[0]);
		} else {
			return new static();
		}
	}

	public static function mappingArrayPaypal($post) {
		$array = [
			"prenom" => $post["miglad_firstname"],
			"nom" => $post["miglad_lastname"],
			"email" => $post["miglad_email"],
			"montant" => $post["miglad_amount"],
			"adresse" => $post["miglad_address"],
			"ville" => $post["miglad_city"],
			"code_postal" => $post["miglad_postalcode"],
			"migla" => $post["miglad_session_id"],
			"etat" => "en attente",
		];
		return $array;
	}

	public static function mappingArrayOffline($post) {
		$array = [
			"prenom" => $post["mfirstname"],
			"nom" => $post["mlastname"],
			"email" => $post["memail"],
			"montant" => $post["mamount"],
			"adresse" => $post["maddress"],
			"ville" => $post["mcity"],
			"code_postal" => $post["mzip"],
		];
		return $array;
	}

	public function addTransactionID($id) {
		$this->updateChamps($id, "numero_transaction_paypal");
	}

	public static function newDonateurPaypal($array) {
		$champs = static::mappingArrayPaypal($array);
		$item = new static();
		$item->insert();
		$item->updateChamps($champs);
		return $item;
	}

	public static function newDonateurPaypalIPN($array) {
		$champs = static::mappingArrayPaypalIPN($array);
		$item = new static();
		$item->insert();
		$item->updateChamps($champs);
		return $item;
	}

	public static function newDonateurOffline($array) {
		$champs = static::mappingArrayOffline($array);
		$item = new static();
		$item->insert();
		$item->updateChamps($champs);
		return $item;
	}

	public function changeEtat($etat = "payé") {
		if ($etat != 'invalide') {
			$this->updateChamps($etat, "etat");
		}
	}

	public static function checkDonation() {
		if (isset($_REQUEST["thanks"]) && $_REQUEST["thanks"] == "thanks" && isset($_REQUEST["id"])) {
			$migla = $_REQUEST["id"];
			if (isset($_REQUEST["cotisation"])) {
				$cotisation = cotisation::from_session($migla);
				$cotisation->changeEtat("payé");
				if (isset($_REQUEST["txn_id"])) {
					$cotisation->addTransactionID($_REQUEST["txn_id"]);
				}
			} else {
				$donation = static::from_session($migla);
				$donation->changeEtat("payé");
				if (isset($_REQUEST["txn_id"])) {
					$donation->addTransactionID($_REQUEST["txn_id"]);
				}
			}
		}
//		logQueryVars();
//			consoleLog("Request");
//			logRequestVars();
		if (isset($_REQUEST["migla_listener"])) {
			consoleLog("sendmail");
			if (isset($_REQUEST["cotisation"])) {
				cotisation::sendmailIPN();
			} else {
				static::sendmailIPN();
			}
		}
	}

//<editor-fold desc="PDF">
	public function year() {
		$date = $this->date_insert_tmstp();
		return date("Y", $date);
	}

	public function montant_fr() {
		$montant = $this->montant() * 1;
		return bii_cvnbst($montant);
	}

	protected function en_tete() {
		ob_start();
		?>
		<font face="Calibri" size="14pt">
		<cell width="3.6cm" left="15cm" top="1cm"  align="right">N° Ordre du reçu</cell>
		<cell width="3.6cm" left="15cm" top="1.4cm"  align="right">Don N°<?= $this->chrono(); ?></cell>
		</font>
		<img src="http://liguehavraise.fr/wp-content/uploads/2016/04/ligue-havraise-pour-pdf-300x173.jpg" top="2cm" left="8cm" height="3cm"/>
		<font face="Calibri" size="14pt">
		<cell width="7.5cm" left="1.6cm" top="5.7cm"  align="center">Reçu aux oeuvres</cell>
		</font>
		<font face="CalibriBold" size="14pt">
		<cell width="7.5cm" left="1.6cm" top="6.3cm"  align="center">DON <?= $this->year(); ?></cell>
		</font>
		<font face="Calibri" size="9pt">
		<cell width="7.5cm" left="1.6cm" top="6.8cm"  align="center">(Article 200 et 238bis du Code Général des impôts)</cell>
		</font>
		<?php if ($this->nom && $this->adresse && $this->ville && $this->code_postal) { ?>
			<font face="Calibri" size="10pt">
			<cell width="5cm" left="14.1cm" top="5.5cm"  align="left">M ou Mme <?= $this->nom; ?></cell>
			</font>
			<font face="CalibriItalic" size="10pt">
			<cell width="5cm" left="14.1cm" top="6.1cm"  align="left"><?= $this->adresse; ?></cell>

			<cell width="5cm" left="14.1cm" top="7.1cm"  align="left"><?= $this->code_postal; ?> <?= strtoupper($this->ville); ?></cell>
			</font>
		<?php } ?>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	protected function beneficiaire() {
		ob_start();
		?>
		<rect top='8cm' left='1.6cm' border='1' width='18cm' height='0.5cm' fillcolor="#D9D9D9">
			<font face="Calibri" size="11pt"><cell width="100%" left="1.6" top='8.1cm' align="center">Bénéficiaire des versements</cell></font>
		</rect>
		<div top='8.5cm' left='1.6cm' border='1' width='18cm' height='7.2cm'>
			<font face="Calibri" size="11pt">
				<cell left="1.9cm" top="9.1cm" align="left">Nom ou dénomination :</cell>
				<cell left="1.9cm" top="10.6cm" align="left">Adresse :</cell>
				<cell left="1.9cm" top="11.1cm" align="left">N° :</cell>
				<cell left="1.9cm" top="11.6cm" align="left">Code Postal :</cell>
				<cell left="5.2cm" top="11.6cm" align="left">Commune :</cell>
				<cell left="1.9cm" top="12.6cm" align="left">Objet :</cell>
				<cell left="1.9cm" top="14.4cm" align="center" width='18cm'>« Association ou fondation reconnue d'utilité publique par décret en date du 25 septembre 1985</cell>
				<cell left="1.9cm" top="14.9cm" align="center" width='18cm'>Publié au Journal Officiel du 22 novembre 1985 ».</cell>
			</font>
			<font face="CalibriBold" size="11pt">
				<cell left="2.4cm" top="10.1cm" align="left">Reconnue d'utilité publique par Décret du 25 juillet  1930</cell>
				<cell left="2.55cm" top="11.1cm" align="left">75/79 rue Emile Zola</cell>
				<cell left="4cm" top="11.6cm" align="left">76600</cell>
				<cell left="7.05cm" top="11.6cm" align="left">Le Havre</cell>
				<cell left="1.9cm" top="13cm" align="left">« La défense des intérêts moraux et matériels des enfants, adolescents et adultes handicapés et de leur </cell>
				<cell left="1.9cm" top="13.5cm" align="left">famille »</cell>
			</font>
			<font face="CalibriItalic" size="14pt">
				<cell left="2.4cm" top="9.6cm" align="left">LIGUE HAVRAISE POUR L'AIDE AUX PERSONNES HANDICAPÉES</cell>
			</font>
			

		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	protected function donateur() {
		ob_start();
		?>
		<div top='15.8cm' left='1.6cm' border='1' width='18cm' height='0.5cm' fillcolor="#D9D9D9">
			<font face="Calibri" size="11pt"><cell width="18cm" left="1.6cm" top='15.9cm' align="center">Donateur</cell></font>
		</div>
		<div top='16.3cm' left='1.6cm' border='1' width='18cm' height='2.4cm'>
			<font face="Calibri" size="11pt">
			<?php if ($this->nom) { ?>
			<cell width="100%" left="1.9cm" top="16.8cm" align="left">Nom :</cell>
			<?php }if ($this->adresse) { ?>
			<cell width="100%" left="1.9cm" top="17.3cm" align="left">Adresse :</cell>
			<?php }if ($this->code_postal) { ?>
			<cell width="100%" left="1.9cm" top="17.8cm" align="left">Code Postal :</cell>
			<?php }if ($this->ville) { ?>
			<cell width="100%" left="9.4cm" top="17.8cm" align="left">Commune :</cell>
			<?php } ?>
			</font>
			<font face="CalibriBold" size="11pt">
			<?php if ($this->nom) { ?>
			<cell width="100%" left="2.9cm" top="16.8cm" align="left"><?= $this->nom ?></cell>
			<?php }if ($this->adresse) { ?>
			<cell width="100%" left="3.5cm" top="17.3cm" align="left"><?= $this->adresse ?></cell>
			<?php }if ($this->code_postal) { ?>
			<cell width="100%" left="4cm" top="17.8cm" align="left"><?= $this->code_postal ?></cell>
			<?php }if ($this->ville) { ?>
			<cell width="100%" left="11.3cm" top="17.8cm" align="left"><?= $this->ville ?></cell>
			<?php } ?>
			</font>
		</div>
		<div top='16.3cm' left='1.6cm' border='1' width='18cm' height='8.2cm'>
			<font face="Calibri" size="11pt">
			<cell width="100%" left="1.9cm" top="19.2cm" align="left">Le bénéficiaire reconnait avoir reçu au titre des versements ouvrant droit à réduction d'impôts, la somme de :</cell>
			<cell width="100%" left="1.9cm" top="20.2cm" align="left"><?= strtoupper($this->montant_fr()); ?></cell>
			<cell width="100%" left="1.9cm" top="21.2cm" align="left">Date du paiement :</cell>
			<cell width="100%" left="1.9cm" top="22.2cm" align="left">Mode de versement : Paiement en ligne sur notre site </cell>
			<cell width="100%" left="14.3cm" top="23cm" align="left">Le Havre, le </cell>
			<cell width="100%" left="14.6cm" top="23.5cm" align="left">La Présidente,</cell>
			<cell width="100%" left="14.6cm" top="25.4cm" align="left">Christine LALLART</cell>
			</font>
			<font face="CalibriBold" size="11pt">
			<cell width="100%" left="5.3cm" top="21.2cm" align="left"><?= $this->date_insert(); ?></cell>
			<cell width="100%" left="11.2cm" top="22.2cm" align="left"><?= get_bloginfo("url"); ?></cell>
			<cell width="100%" left="16.3cm" top="23cm" align="left"><?= date("d/m/Y") ?></cell>

			</font>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	public function texte_pdf() {
		$date = $this->date_format("date_insert");
		$heure = $this->date_format("date_insert", "H:i:s");
		$texte = $this->en_tete() . $this->beneficiaire() . $this->donateur();

		return utf8_decode($texte);
	}

	public function display_pdf() {
		return pdf_template::displayPDML("Test", "Test", $this->texte_pdf());
	}

	public function nom_pdf() {
		$nom = stripAccentsLiens($this->nom());
		$chrono = $this->chrono();
		$nom_fichier = "$nom-$chrono.pdf";
		return $nom_fichier;
	}

	public function lien_pdf() {
		$nom_fichier = $this->nom_pdf();
		return get_bloginfo("url") . "/wp-content/plugins/bii_donations/archives_pdf/$nom_fichier";
	}

	public function save_pdf() {
		$nom_fichier = $this->nom_pdf();

		$pdf = $this->display_pdf();

		$myfile = fopen("/web/clients/lhavrais/www.liguehavraise.fr/wp-content/plugins/bii_donations/archives_pdf/$nom_fichier", "w+");
//		@chmod($myfile,0755);
		fputs($myfile, $pdf, strlen($pdf));
		fclose($myfile);
		$this->updateChamps($this->lien_pdf(), "lien_recu");
	}

//</editor-fold>
	public static function mappingArrayPaypalIPN($post) {
		$array = [
			"prenom" => $post["first_name"],
			"nom" => $post["first_name"],
			"email" => $post["miglad_email"],
			"montant" => $post["mc_gross"],
			"adresse" => $post["miglad_address"],
			"ville" => $post["address_city"],
			"code_postal" => $post["address_zip"],
			"migla" => $post["miglad_session_id"],
			"numero_transaction_paypal" => $post["txn_id"],
			"etat" => "en attente",
		];
		return $array;
	}

	public static function sendmailIPN() {
		// lecture du post de PayPal et ajout de 'cmd'
		$req = 'cmd=_notify-validate';

		foreach ($_POST as $key => $value) {
			$value = trim(urlencode(stripslashes($value)));
			$req .= "&$key=$value";
		}



		// post back to PayPal system for validation
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

		$fp = fsockopen('ssl://www.paypal.com', 443, $errno, $errstr, 30);
		$etat_commande = "erreur";
		// turn the original post data into an object we can work with
		$txn = (object) $_POST;
		if (!$fp) {
			
		} else {
			fputs($fp, $header . $req);
			while (!feof($fp)) {
				$res = fgets($fp, 1024);
				// proceed if transaction is verified
				if (strcmp($res, "VERIFIED") == 0) {
					$etat_commande = "payé";
				} else if (strcmp($res, "INVALID") == 0) {
					$etat_commande = "invalide";
				}
				$miglaid = $txn->custom;
			}
			$don = static::from_session($miglaid);
			$don->updateChamps($etat_commande, "etat");
			bii_write_log($miglaid);
			
		}
		serialize($_POST);
		update_option("bii_last_don", $miglaid);
		$don->save_pdf();
		fclose($fp);
		exit;
	}

	public static function sumDonation($where = "1=1") {
		$class_name = static::prefix_bdd() . static::nom_classe_bdd();
		$req = "select sum(montant) as sum from $class_name where $where";
		$pdo = static::getPDO();
		$select = $pdo->query($req);
		$sum = 0;
		while ($row = $select->fetch()) {
			$sum = $row["sum"];
		}
		return $sum;
	}

	public static function static_message_remerciement($migla){
		$item = static::from_session($migla);
		return $item->message_remerciement();
	}
	
	
	protected function message_remerciement(){
		$montant = $this->montant();
		$date = $this->date_insert();
		$nom = $this->nom();
		$prenom = $this->prenom();
		$don = $this->nom_classe_admin();
		$ce = "ce";
		$lien = $this->lien_recu();
		if(static::feminin()){
			$ce = "cette";
		}
		$string = "$prenom $nom,"
			. "<br /><br />"
			. "Merci pour votre $don de $montant € du $date. Votre aide nous est particulièrement utile et votre générosité est très appréciée. "
			. "<br />Nous souhaitons vous exprimer nos sincères remerciements pour $ce $don."
			. "<br />Vous pouvez télécharger votre reçu fiscal ici : <a class='theme-button' target='_blank' href='$lien' title='Télécharger le reçu'>Télécharger le reçu</a>";
	
		return $string;
		}
}
