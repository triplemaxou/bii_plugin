<?php
$lang = apply_filters("bii_multilingual_current_language");

switch ($lang) {
	case 'fr':
		$message = "Vous n'avez pas les droits suffisants pour afficher cette page";
		break;
	default:
		$message = "You do not have permission to view this page";
		break;
}
?><p class=bii_alert"><?= $message ?></p><?php
	