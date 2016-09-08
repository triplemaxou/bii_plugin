<?php
$lang = apply_filters("bii_multilingual_current_language");
if (!isset($nom_classe)) {
	$nom_classe = "bii_page_perso";
}
$page_perso = bii_page_perso::get_page_perso();
$id = $page_perso->id();
//pre($id, "red");
ini_set('display_errors', '1');
if ($nom_classe::editable()) {
//	pre($_POST, "blue");
//	pre($_FILES, "green");
	if (is_admin() && isset($_REQUEST["id_edit"])) {
		$id = $_REQUEST["id_edit"];
	}
	$action_bouton = __("Publier");

	if ($page_perso->date_insert_tmstp() != 0) {
		$action_bouton = __("Modifier");
	}
	$action_bouton2 = $action_bouton . __(" et rester");
	$item = $nom_classe::get_page_perso();
	if (isset($_GET['edit'])) {
		$dlh = $nom_classe::front_redirectError();
		$message = "Une erreur est survenue, vous allez être redirigé vers la page précédente";
		pre($_POST, "green");
		if (isset($_POST['id'])) {
			$message = "Page perso enregistrée, vous allez être redirigé sous peu";

			$id_post = $_POST['id'];
			$stay = $_POST['publishandstay'];
			unset($_POST['id']);
			unset($_POST['publishandstay']);
			$item = new bii_page_perso($id_post);

			$item->updateChamps($_POST);
//			pre($item, "blue");
			$item->insertTC($_POST);
			$item->buildpost();
//			pre($item, "green");
			if ($stay) {
				$dlh = $nom_classe::front_redirectStay();
			} else {
				$dlh = $nom_classe::front_redirectEdit();
			}
		}
		?>
		<div class="bs-callout bs-callout-info">
			<p class=""><?= __($message); ?></p>
			<p class=""><?= __("Cliquez sur le lien suiavnt si vous n'êtes pas redirigé dans 5 secondes"); ?></p>
			<p class=""><a href="<?= $dlh ?>"><?= $dlh ?></a></p>
		</div>
		<script>
			setTimeout(function () {
				document.location.href = "<?= $dlh ?>";
			}, 3000);

		</script>
		<?php
	} else {
		?>
		<h2><?= $item->front_titreEdit(); ?></h2> 
		<?php if (isset($_GET["erreur_edit"])) { ?>
			<div class="updated below-h2 warning" id="message" style="display:none;">
				<p>Votre fiche n'a pas pu être éditée, veuillez réésayer</p>
			</div>
			<?php
		}

		$action = bii_page_perso::front_redirectStay();
		if (strpos($action, "?")) {
			$action .= "&";
		} else {
			$action .= "?";
		}
		$action .= "edit=1";
		?>
		<div id="poststuff" class="metabox-holder has-right-sidebar poststuff-bii">
			<div id="post-body ">
				<div id="post-body-content ">
					<form method="post" class="col-xxs-12" id="edit-item" action="<?= $action ?>">
						<input type="hidden" id="publishandstay" name="publishandstay" value="0" />
						<input type="hidden"  name="lang" value="<?= $lang ?>" />
						<input type="hidden"  name="id" value="<?= $id ?>" />
						<?php $item->form_edit(); ?>
						<input type="submit" id="edit-item-submit" />
					</form>
				</div>
			</div>

			<div class="clearfix"></div>
			<button type="button" class="publier-rester btn btn-info" ><span class="fa fa-save"></span> <?php echo $action_bouton2; ?></button>
			<button class="publier btn btn-success" accesskey="p" tabindex="5" ><span class="fa fa-save"></span> <?php echo $action_bouton; ?></button>

		</div>

		<div class="clearfix"></div>
		<?php
	}
}