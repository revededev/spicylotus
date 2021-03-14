<?php
include "../../../../wp-load.php"; // pour pouvoir utiliser $wpdb etc...
require "template/constantes.php";

global $wpdb,$current_user, $wp_roles;
session_start();
$infos = [];
$infos["debugs"] = [];// Liste les infos de debug pour le développeur
$infos["infos"] = []; // Liste les msg à afficher pour un user
$current_user_ID = $current_user->ID;
// $infos["method"] = []; // Affiche $_GET etc... option en bas de cette page
// printr($_SESSION["token_token"]);

// NB : $_SESSION["tab_supprime_items"] et $_SESSION["tab_items"] sont disponibles

// Token, Source (site du zéro, 2013) : http://sdz.tdct.org/sdz/securisation-des-failles-csrf.html
// Création token, source (mai 2017) : https://webdevdesigner.com/q/best-practice-to-generate-random-token-for-forgot-password-73732/

// refactorisation
	/**
	 * Verifie la cohérence : id, token et http_referer
	 * @param int $current_user_ID
	 * @param int $current_user_ID_from_profil
	 * @param string $token token from profil
	 * @return bool
	 */
	function is_ID_token_referer($current_user_ID, $current_user_ID_from_profil, $token){

		$is_ID_token = false;
		$current_user_ID_from_profil = (int)$current_user_ID_from_profil;
		$current_user_ID = (int)$current_user_ID;
		$http_referer_authorises =["localhost"=> ["http://localhost/spicylotus/profil/","http://localhost/spicylotus/wp-admin/admin.php?page=validation-des-inscriptions"],
									"www.spicylotus.fr"=> ["https://www.spicylotus.fr/profil/","https://www.spicylotus.fr/wp-admin/admin.php?page=validation-des-inscriptions"]
									];
		
		// check : id + token + http_referer
		if($current_user_ID_from_profil === $current_user_ID
		&& $_SESSION["token_profil"] === $token
		&& in_array($_SERVER["HTTP_REFERER"], $http_referer_authorises[$_SERVER["SERVER_NAME"]]) // Non fiable
		){ 
			$is_ID_token = true;
		}
		return $is_ID_token;
	}
	/**
	 * Liste les erreurs...
	 * @param int $current_user_ID
	 * @param int $current_user_ID_from_profil
	 * @param string $token token from profil
	 * @return array|bool array|false
	 */		
	function is_ID_token_referer_info_debug($current_user_ID, $current_user_ID_from_profil, string $token){


		// die('die');
		$badID		= "<li>ID non identique.</li>";
		// $noAction	= "<li>Action est non déclarée ou vide.</li>";
		$badToken	= "<li>Token non identique.</li>";
		$badReferer = "<li>HTTP_REFERER non conforme.</li>";
		$infoDebug = [];
		$current_user_ID_from_profil = (int)$current_user_ID_from_profil;
		$current_user_ID = (int)$current_user_ID;
		$http_referer_authorises =["localhost"=> ["http://localhost/spicylotus/profil/","http://localhost/spicylotus/wp-admin/admin.php?page=validation-des-inscriptions"],
									"www.spicylotus.fr"=> ["https://www.spicylotus.fr/profil/","https://www.spicylotus.fr/wp-admin/admin.php?page=validation-des-inscriptions"]
									];
		
		if($current_user_ID_from_profil !== $current_user_ID){
			array_push($infoDebug,$badID);
			array_push($infoDebug,"ID from profil: ".$current_user_ID_from_profil);
			array_push($infoDebug,"ID from current_user->ID : ".$current_user_ID);
		}
		if($_SESSION["token_profil"] !== $token){
			array_push($infoDebug,$badToken);
			array_push($infoDebug,"Token from profil: ".$token);
			array_push($infoDebug,"Token from session : ".$_SESSION["token_profil"]);	
		}
		if(in_array($_SERVER["HTTP_REFERER"], $http_referer_authorises[$_SERVER["SERVER_NAME"]])){
			array_push($infoDebug,$badReferer);
			// array_push($infoDebug,"<li>HTTP_REFERER valide : /profil/ <i>OU</i> /wp-admin/admin.php?page=validation-des-inscriptions/</li>");
			array_push($infoDebug,"SERVER['HTTP_REFERER'] : ".$_SERVER["HTTP_REFERER"]);	
		}
		if(!empty($infoDebug)){
			return $infoDebug;
		}else{
			return false;
		}
	}
	/**
	 * Construction d'un msg d'erreur...
	 * @param array $global $_GET ou $_POST ou $_DELETE
	 * @return string msg
	 */
	function stringMsg($global){// $global = $_GET ou $_POST ou $_DELETE
		$msg = ": empty or not set.";
		if(empty($_SERVER["HTTP_REFERER"])) $msg = "- HTTP_REFERER ".$msg;
		if(empty(trim($global["action"]))) $msg = "- action ".$msg;
		if(empty(trim($global["current_user_ID"]))) $msg = "- current_user_ID ".$msg;
		if(empty(trim($global["token"]))) $msg = "- token ".$msg;
		$msg = "Methode : ".$_SERVER["REQUEST_METHOD"]." | ".$msg;
		return $msg;
	}
	function is_base64($base64){
		// $is_base64 = base64_encode(base64_decode(str_replace(" ","+",$base64), true)) === $base64 ? true : false;
		// $base64 = explode( ',', $base64 ); // base64 look like : {data:"typeMime";base64,"base64 caracteres"}
		// $base64 = $base64[1];
		// $base64 = str_replace(" ","+",$base64); // from js replace " " "+"
		$base64 = str_replace(" ","+",explode( ',', $base64 )[1]);
		$is_base64 = base64_encode(base64_decode($base64, true)) === $base64 ? true : false;

		return $is_base64;
	}
// Fin refactorisation

// $_SERVER = ["__FILE__" => __FILE__] + $_SERVER;
if (is_user_logged_in() 
&& !empty(trim($_SESSION["token_profil"]))
&& in_array($_SERVER["REQUEST_METHOD"],["GET","POST","DELETE"])
){
	if (!empty($_GET)
	&& !empty(trim($_GET["action"]))
	&& !empty(trim($_GET["current_user_ID"])) && !empty(trim($_GET["token"]))
	&& !empty($_SERVER["HTTP_REFERER"])
	&& is_ID_token_referer($current_user_ID,$_GET["current_user_ID"],$_GET["token"], $token)
	){
		switch($_GET["action"]){

			case "get_user_action": //

				$user_id = $_GET["user_id"];
				$sql ="SELECT display_name,user_email,meta_key,meta_value,user_id
				FROM NICE_usermeta JOIN NICE_users ON NICE_users.id = user_id
				WHERE 1
				AND (meta_key LIKE 'is_%' OR meta_key LIKE 'path_%' OR meta_key = 'tel')
				AND user_id =".$user_id."
				ORDER BY meta_key ASC
				";
				$s_u_park = $wpdb->get_results($sql,ARRAY_A);
				$selected_user =["name" => $s_u_park[0]["display_name"],"email" => $s_u_park[0]["user_email"]];

				for ($j=0;$j<count($s_u_park);$j++){ 
					$selected_user = $selected_user + [$s_u_park[$j]['meta_key'] => $s_u_park[$j]["meta_value"]];
				}
				if(isset($selected_user['path_monAvatar'])&&!empty($selected_user["path_monAvatar"])){
					$img = "<img id='path_avatar' class='avatar' src='".$selected_user['path_monAvatar']."' alt='Photo de profil'>";
				}else{
					$img = "<li>Pas de photo de profil</li>";
				}
				// calcul de l'ordre d'affichage, ex : style="order:'1';" (peut fctner sans => include plutot que require)
				include 'template/calc_css_order.api_user.php';
				// Construction : Reponse de la requete
				require "template/construct_response_request.api_user.php";

			break;
			default: // infos debug
				array_push($infos["debugs"], "Cette action n'est pas prévu.");
		}
	}elseif(!empty($_GET)){// infos debug
		if(!empty(trim($_GET["action"]))	&& !empty(trim($_GET["current_user_ID"])) && !empty(trim($_GET["token"])) && !empty($_SERVER["HTTP_REFERER"])){
			array_push($infos["debugs"],"GET / line : ".__LINE__);
			array_push($infos["debugs"],is_ID_token_referer_info_debug($current_user_ID,$_GET["current_user_ID"],$_GET["token"]));
		}else{// infos debug
			// array_push($infos["debugs"],stringMsg($_GET));// Exmeple de msg : " 'nomVariable' : empty or not set."
			array_push($infos["debugs"],$_GET["action"]);
			array_push($infos["debugs"],$_GET["current_user_ID"]);
			array_push($infos["debugs"],$_GET["token"]);
			array_push($infos["debugs"],$_SERVER["HTTP_REFERER"]);
		}
	}

	if (!empty($_POST)
	&& !empty(trim($_POST["action"]))
	&& !empty(trim($_POST["current_user_ID"])) && !empty(trim($_POST["token"]))
	&& !empty($_SERVER["HTTP_REFERER"])
	&& is_ID_token_referer($current_user_ID, $_POST["current_user_ID"],$_POST["token"], $token)
	){
		if($_SERVER["SERVER_NAME"]==="localhost"){
			$racine = "C:/wamp64/www/spicylotus";
		}else{
			$racine = "/srv/data/web/vhosts/www.spicylotus.fr/htdocs";
		}

		$folder = $racine."/wp-content/themes/blossom-travel-child/img/documents/".$current_user->ID;

		switch($_POST["action"]){
			
			case "upload":

				if(!empty(trim($_POST["base64"]))&&!empty(trim($_POST["suffixe_item"]))
				&& is_base64($_POST["base64"])
				&& in_array($_POST["suffixe_item"],$_SESSION["tab_items"]) // élément de sécurité : name file + database
				){
					
					$suffixe_item = trim($_POST["suffixe_item"]); // ex : $suffixe_item = certif ou assurance...

					$dossier = wp_mkdir_p(  $folder );// create folder $folder if not exists
					if(!$dossier) array_push($infos["infos"],"Erreur à la création du dossier.");

					// recherche extension
					$encodedData = str_replace(" ","+",$_POST["base64"]);// provient du js => " " par "+"
					$data = explode( ',', $encodedData ); // $data[ 0 ] == "data:image/{jpeg,png,gif,pdf};base64" , $data[1]
					$extension = explode( "/", $data[0] );// ex : data:image/  +  jpeg;base64
					$ext = explode( ';', $extension[1] );

					$ext[0] = strtolower($ext[0]);// transforme en  minuscules
					if($ext[0] ==="jpeg") $ext[0] = "jpg"; // trnasforme jpeg en jpg

					$accept = $suffixe_item === "monAvatar" ? ["jpg","gif"] : ["jpg","gif","pdf"];
					$acceptString =  $suffixe_item === "monAvatar" ? "jpg,&nbsp;gif" : "jpg,&nbsp;gif,&nbsp;pdf";
					// S'assurer que l'ext est correct :
					if(in_array($ext[0],$accept)) {
						//
						// delete file about $suffixe_item before create new file
						array_map("unlink", glob($folder."/*".$suffixe_item."*.*"));

						$t = time();// evite pb cache
						$nomFichier = "id_".$current_user->ID."_".$suffixe_item."_".$t;
						$target = $folder."/".$nomFichier.".".$ext[0];
						// unlink($target); // delete file if exists

						$file = fopen( $target, "w+" );
						if(!$file) array_push($infos["infos"],"Erreur à l'ouverture du fichier.");

						$write = fwrite( $file, base64_decode($data[1]));
						if(!$write) array_push($infos["infos"],"Erreur à l'écriture du fichier.");

						$close = fclose($file);
						if(!$close) array_push($infos["infos"],"Erreur à la fermeture du fichier.");

						// insidpensable pour afficher les pdf

						if($ext[0]==="pdf"){// Sur https, wp crée automatiquement un jpg à partir du pdf
							$target = $folder."/".$nomFichier."-pdf.jpg";
							$i=0;
							while(!file_exists($target)&&$i<12){// 12 = max 3 secondes
								// il faut attendre pour que wordpress crée son jpg a partir du pdf...
								sleep(.25); 
								// En local wp ne crée pas de jpg a partir du pdf
								$i++;
							}
							$target_http = get_stylesheet_directory_uri()."/img/documents/".$current_user->ID."/".$nomFichier."-pdf.jpg";

						}else{
							$i=0;
							while(!file_exists($target)&&$i<8){// 8 = max 2 secondes
								// il faut attendre un peu sur Wamp...
								sleep(.25); 
								// 
								$i++;
							}
							$target_http = get_stylesheet_directory_uri()."/img/documents/".$current_user->ID."/".$nomFichier.".".$ext[0];
						}
						if(empty($infos["infos"])){

							$update_path = update_user_meta( $current_user->ID, 'path_'.$suffixe_item, $target_http);
							if(!$update_path){
								array_push($infos["infos"],"Erreur de mise à jour.");
							}else{
								if($suffixe_item !== "monAvatar"){
									update_user_meta( $current_user->ID, "is_".$suffixe_item, "en attente");
									$x=1200;
								}else{
									$x=370; // fixe la largueur des images apres avoir été redimensionnées
								}
								// if($ext[0]==="jpg"){// Ne pas faire confiance au user...
									// 	// redimensionnement des images - source : https://code-boxx.com/resize-images-php/
									// 	$chemin = $target; // le chemin en absolu
										
									// 	list($l, $h) = getimagesize($chemin);
									// 	// echo " l = ".$l." h = ".$h."<br>";
									// 	$original = imagecreatefromjpeg($chemin);
									// 	$resized = imagecreatetruecolor($x, $x*$h/$l);
									// 	imagecopyresampled($resized, $original, 0, 0, 0, 0,$x, $x*$h/$l, $l, $h);
									// 	imagejpeg($resized, $chemin);
								// }
								sleep(.15);
								array_map('unlink', glob($target = $folder."/".$nomFichier."-*"));// efface les fichiers wp créés automatiquement
							}
							if(empty($infos["infos"])){
								$infos["src"] = $target_http;
							}else{// infos user
								array_push($infos["infos"],"Aucune modication n'a été enregistrée. Recommencez.");
							}
						
						}else{// infos user
							array_push($infos["infos"],"Aucune modication n'a été enregistrée. Recommencez.");
						}
					}else{
						array_push($infos["infos"],"Vous devez choisir l'une de ces extensions : ".$acceptString);
						array_push($infos["infos"],"Aucune modication n'a été enregistrée. Recommencez.");
					}

				}else{// infos debug
					if(empty(trim($_POST["base64"]))){
						array_push($infos["debugs"],"POST['base64'] est non déclaré ou vide.");
					}elseif(!is_base64($_POST["base64"])){
						array_push($infos["debugs"],"POST['base64'] n'est pas codé en base 64.");
					}
					if(empty(trim($_POST["suffixe_item"])))
						array_push($infos["debugs"],"POST['suffixe_item'] est non déclaré ou vide.");
					if(!in_array($_POST["suffixe_item"],$_SESSION["tab_items"]))
						array_push($infos["debugs"],"POST['suffixe_item'] est non déclaré ou non valide.");
					array_push($infos["debugs"],"Echec du téléchargement.");
					array_push($infos["debugs"],"Aucune modification n'a été enregistrée.");
					// infos user
					array_push($infos["infos"],"Aucune modication n'a été enregistrée. Recommencez.");
				}
			break;
			case "delete":

				if(!empty(trim($_POST["suffixe_item"]))
				&& in_array($_POST["suffixe_item"],$_SESSION["tab_items"]) // élément de sécurité (database)
				){
					$suffixe_item = trim($_POST["suffixe_item"]); // ex : $suffixe_item = certif ou assurance...

					update_user_meta( $current_user->ID, "path_".$suffixe_item, "");
					if($suffixe_item !=="monAvatar")
						update_user_meta( $current_user->ID, "is_".$suffixe_item, "non fourni");

					$list_folders_del = glob($folder."/*".$suffixe_item."*.*");
					array_map('unlink', glob($folder."/*".$suffixe_item."*.*"));
					$infos["is_delete"] = true;
				}else{// infos debug
					if(empty(trim($_POST["suffixe_item"]))){
						array_push($infos["debugs"],"POST['suffixe_item'] est non déclaré ou vide.");
					}elseif(!in_array($_POST["suffixe_item"],$_SESSION["tab_items"])){
						array_push($infos["debugs"],"POST['suffixe_item'] est déclaré mais non valide.");
					}
					array_push($infos["debugs"],"Echec du téléchargement.");
					array_push($infos["debugs"],"Aucune modification n'a été enregistrée.");
				}
			break;
			case "update_status": 
				array_push($infos["debugs"],"Coucou update status");
				foreach($_POST as $key => $value){
				array_push($infos["debugs"],"inside $_POST");
				
					if (in_array(explode("_",$key)[1],$_SESSION["tab_items"])&&($value === 'valide' || $value === "non valide")){
						 $the_key = explode("_",$key); // exple : explode("_", statut_certif) = certif 
						 $the_meta = "is_".$the_key[1];
						 $MaJ = update_user_meta( $_POST["user_id"], $the_meta, $value);
						 if($MaJ){
							  array_push($infos["infos"],"Le statut (".$the_meta.") a été changé.");
						 }else{
							  array_push($infos["infos"],"Une erreur est survenue : Le statut (".$the_key[1].") n'a pas été changé.");
						 }
					}else{
						 array_push($errors,"Le statut (".$the_meta.") n'a pas été changé.");
					}
			  }



			break;
			default: // infos debug
				array_push($infos["debugs"],"Cette action n'est pas prévue.");
		}
	}elseif(!empty($_POST)){// infos debug
		if(!empty(trim($_POST["action"]))	&& !empty(trim($_POST["token"])) && !empty($_SERVER["HTTP_REFERER"])){
			array_push($infos["debugs"],"POST / line : ".__LINE__ );
			array_push($infos["debugs"],
				is_ID_token_referer_info_debug($current_user_ID,$_POST["current_user_ID"],$_POST["token"])
						  );
		}else{// infos debug
			array_push($infos["debugs"],stringMsg($_POST));// Exmeple de msg : " 'nomVariable' : empty or not set."
		}
	}
}else{// infos debug
	array_push($infos["debugs"],$_SERVER["REQUEST_METHOD"]." / line : ".__LINE__ );
	if(!is_user_logged_in()) array_push($infos["debugs"],"Vous devez vous connecter.");

	if(empty($_SESSION["token_profil"])) array_push($infos["debugs"],"Le token n'a pas été définie dans la page profil.".$_SESSION["token_profil"]);
	if(!in_array($_SERVER["REQUEST_METHOD"],["GET","POST","DELETE"])) array_push($infos["debugs"],"Cette méthode n'est pas autorisée.");
}

/* =====			JSON		======= */

// if(!empty($infos["debugs"])){// options...

	if(!empty($_GET))		$infos["method"] = $_GET;
	if(!empty($_POST))	$infos["method"] = $_POST;
	if(!empty($_DELETE))	$infos["method"] = $_DELETE;
	// if(!empty($list_folders_del))		$infos["debugs"] = $infos["debugs"] + $list_folders_del;
	// $infos["server"] = $_SERVER;

		// Tests
	if(empty($infos["debugs"])) array_push($infos["debugs"],"infos > debugs > <i>vide</i>.");
	if(empty($infos["infos"])) array_push($infos["infos"], "infos > infos > <i>vide</i>.");
	if(empty($infos["src"])) array_push($infos["src"], "src > <i>vide</i>.");

	// $infos["session"] = $_SESSION;

	// En prod :
	// unset($infos["debugs"]);$infos["debugs"] = [];

// }
// Transforme en json
$json = json_encode($infos);
echo $json;
?>