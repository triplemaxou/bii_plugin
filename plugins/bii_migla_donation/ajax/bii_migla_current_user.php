<?php
$id = get_current_user_id();
$nom = get_user_meta($id,"last_name");
$prenom = get_user_meta($id,"first_name");
$adresse = get_user_meta($id,"adresse");
$cp = get_user_meta($id,"code_postal");
$ville = get_user_meta($id,"ville");
$user_info = get_userdata($id);
$mail = $user_info->user_email;
$array = [
	"nom"=>$nom,
	"prenom"=>$prenom,
	"mail"=>$mail,
	"ville"=>$ville,
	"code_postal"=>$cp,
	"adresse"=>$adresse,
	
];

echo json_encode($array);