<?php

ini_set('display_errors', '1');

if (isset($_REQUEST["nom_calculatrice"])) {
	$nom_classe = $_REQUEST["nom_calculatrice"];
	unset($_REQUEST["nom_calculatrice"]);
		unset($_REQUEST["action"]);
	$item = new $nom_classe($_REQUEST);
	if (is_a($item, "bii_calc")) {
		
		echo $item->calcul();
	} else {
		echo "L'objet selectionné n'est pas une calculatrice";
	}
} else {
	echo "Pas de calculatrice selectionnée";
}
