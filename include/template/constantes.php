<?php

$suffixs=["monAvatar" =>["item" => "monAvatar", "alt" => "Photo de l'avatar"					, "noImage" => "Placer votre avatar ici..."				,"label" => "Télécharger mon avatar"],
		  "certif"    =>  ["item" => "certif"   , "alt" => "Photo du certificat médical"		, "noImage" => "Le certificat médical est à fournir."	,"label" => "Télécharger mon certificat médical"],
		  "assurance" =>  ["item" => "assurance", "alt" => "Photo de l'assurance"				, "noImage" => "L'assurance est à fournir."				,"label" => "Télécharger mon attestation d'assurance"],
		  "yoga"      =>  ["item" => "yoga"     , "alt" => "Photo de l'attestation de yoga"	, "noImage" => "L'attestation de yoga est à fournir." ,"label" => "Télécharger mon attestation de yoga"],
		  "licence"   =>  ["item" => "licence"  , "alt" => "Photo de la licence de danse"	, "noImage" => "La licence de danse est à fournir."	,"label" => "Télécharger ma licence de danse"]
		  ];
$_SESSION["tab_items"] = [];
$_SESSION["tab_supprime_items"] = [];
foreach($suffixs as $key => $value){
	array_push($_SESSION["tab_items"],$key);
	array_push($_SESSION["tab_supprime_items"], "supprime_".$key);
}
?>