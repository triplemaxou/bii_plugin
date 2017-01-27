<?php
ini_set('display_errors', '1');
if (isset($_GET["file"])) {
	$file = $_GET["file"];
	if (file_exists($file)) {
		$PDML_FileName = $file;
		$PDML_Content_disposition = "attachement";
		require_once('../inc/fpdf/fpdf.php');
		require_once('../inc/fpdf/pdml.php');
		readfile($file);
	}
}