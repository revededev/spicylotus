<?php
/**
 * Template Name: Mon profil
 *
 * Allow users to update their profiles from Frontend.
 * source : https://stackoverflow.com/questions/21312839/how-to-create-a-edit-profile-page-for-users-on-frontend-with-custom-fields-on-wo/32843969
 */

global $current_user, $wp_roles;
session_start();

$errors = []; // mdp1 != mdp2 - is_email & email exist

require "include/template/constantes.php";
// printr($_SERVER);
// die;
// DEBUT : init

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

if(!isset($_SESSION["token_profil"])) // Car à l'appel de l'api, le token est réinitialisé
	$_SESSION["token_profil"] = bin2hex(random_bytes(32));// Efficacité à tester...

$php_upload_errors= ["Aucune erreur, le téléchargement est correct.",
			"Le poids du fichier téléchargé excède la valeur de 'upload_max_filesize' configurée sur le serveur du site.",
			"Le poids du fichier téléchargé excède -2Mo.-La résolution conseillée est de -2Mpx.",
// mofifié : "La taille du fichier téléchargé excède la valeur de MAX_FILE_SIZE, qui a été spécifiée dans le formulaire HTML."
			"Le fichier n'a été que partiellement téléchargé.",
			"Aucun fichier n'a été téléchargé.",
			"Pas d'indice.",
			"Un dossier temporaire est manquant.",
			"Échec de l'écriture du fichier sur le disque.",
			"Une extension PHP a arrêté l'envoi de fichier.-PHP ne propose aucun moyen de déterminer quelle extension est en cause. L'examen du phpinfo() peut aider.",
			];
// FIN : init



if (  isset($_SERVER["REQUEST_METHOD"]) && "POST" === $_SERVER["REQUEST_METHOD"]
	&& isset( $_POST["action"]) && !empty( $_POST["action"])){
		
	switch($_POST["action"]){
		case "update-user-metas":
		// debut : Update user password
			if ( isset($_POST["pass1"]) && !empty($_POST["pass1"]) && isset( $_POST["pass2"])&& !empty( $_POST["pass2"]) ) {
				if ( $_POST["pass1"] === $_POST["pass2"] ){
					wp_update_user( array( "ID" => $current_user->ID, "user_pass" => esc_attr( $_POST["pass1"] ) ) );
				}
				else{
					array_push($errors,"Les deux mots de passe ne sont pas identiques : Votre mot de passe n'a pas été modifié.");
				}
			}elseif( isset($_POST["pass1"]) && !empty($_POST["pass1"]) || isset( $_POST["pass2"])&& !empty( $_POST["pass2"]) ){
				array_push($errors,"Vous devez saisir le nouveau mot de passe deux fois.");
			}
			// $type_mail =gettype( email_exists(esc_attr( trim($_POST["user_email"] ))));
			if ( isset($_POST["user_email"]) && !empty(trim($_POST["user_email"])) ){
				if (!is_email(esc_attr( trim($_POST["user_email"] )))){
					array_push($errors,"L'email n'est pas valide.");
					// echo $_POST["user_email"];
				}elseif( email_exists(esc_attr( trim($_POST["user_email"] )))&&email_exists(esc_attr( trim($_POST["user_email"] ))) !== $current_user->ID){
					// echo email_exists(esc_attr( trim($_POST["user_email"] )))." vs ".$current_user->ID."<br>";
					array_push($errors,"Cet email est déjà utilisé pour un autre compte.");
					// die;
				}else{
					// echo $_POST["user_email"]."<br>";
					// echo esc_attr( trim($_POST["user_email"] ))."<br>";
					// echo email_exists(esc_attr( trim($_POST["user_email"] )))." vs update ".$current_user->ID."<br>";
					// die;
					wp_update_user( array ("ID" => $current_user->ID, "user_email" => esc_attr( trim($_POST["user_email"] ))));
					// echo "res : ".$res."<br>";
				}
			}
		// FIN : Update user password
		// DEBUT :  lien display_name, first-name, last-name avec $pn = prenom nom / ex : $pn = frederic opdebeck
			if ( get_the_author_meta("display_name",$current_user->ID) ){
				$pn = explode(" ",get_the_author_meta("display_name",$current_user->ID));
				if ( isset( $_POST["first-name"]) && !empty( trim($_POST["first-name"])) && $pn[0]!==trim($_POST["first-name"])){
					$p = str_replace(" ", "-",esc_attr(trim($_POST["first-name"])));
					update_user_meta( $current_user->ID, "first-name", $p);
				}else{
					$p = $pn[0];
				}// get $p = prenom
				if ( isset( $_POST["last-name"]) && !empty(trim($_POST["last-name"]))&& $pn[1]!==trim($_POST["last-name"])){
					$n = str_replace(" ", "-",esc_attr(trim($_POST["last-name"])));
					update_user_meta($current_user->ID, "last-name", $n);
				}else{
					$n = $pn[1];
				}// get $n = nom
				$pn = $p." ".$n;
				wp_update_user( array( "ID" => $current_user->ID, "display_name" => $pn ) );
			}
		// FIN : lien display_name, first-name, last-name
		// debut : Update user tel
			if (isset($_POST["tel"]) && !empty( trim($_POST["tel"]))
				&& (
					(count(str_split (preg_replace("/[^0-9]/", "",trim($_POST["tel"])))) ===10
					// str_split => transforme string en array de une lettre + 
					// preg_replace => ne retient que les chiffres 
					// trim => retire les espaces avant et apres la saisie
					&& str_split(preg_replace("/[^0-9]/", "",trim($_POST["tel"])))[0] ==="0" )
					|| count(str_split (preg_replace("/[^0-9]/", "",trim($_POST["tel"])))) ===11
				
				)){
					update_user_meta( $current_user->ID, "tel", preg_replace("/[^0-9]/", "",trim($_POST["tel"])) );
			}else{
				if ( empty($_POST["tel"]) || (!empty( $_POST["tel"]) && trim($_POST["tel"]) == "") ) {
					array_push($errors,"Votre numéro de téléphone n'a pas été modifié.");
				}elseif(count(str_split (preg_replace("/[^0-9]/", "",trim($_POST["tel"])))) ===10
					 && str_split(preg_replace("/[^0-9]/", "",trim($_POST["tel"])))[0] !="0"){
							array_push($errors,"Votre numéro de téléphone doit commencer par zéro.");
				}else{
					array_push($errors,"Votre numéro de téléphone doit contenir 10 ou 11 chiffres.");
				}
			}// tel
		// FIN : Update user tel
		break;
		case "update-user-docs":
			require( dirname(__FILE__) . "/../../../wp-load.php" );
			if(!empty($_POST["submit"])&&in_array($_POST["submit"],$_SESSION["tab_items"] )){
				$suffix["item"] = $_POST["submit"];
				if( isset($_FILES[$suffix["item"]]) && isset($_FILES[$suffix["item"]]["tmp_name"]) && !empty($_FILES[$suffix["item"]]["tmp_name"])) {

					require "include/post_one_doc.php";
				}
			}elseif(in_array($_POST["submit"],$_SESSION["tab_supprime_items"] )){
				// echo "coucou supprime ".$_POST["submit"]."<br>";
				$suffix = explode("_",$_POST["submit"]);
				update_user_meta( $current_user->ID, "path_".$suffix[1], "" );
				if($suffix[1] !=="monAvatar"){
					update_user_meta( $current_user->ID, "is_".$suffix[1], "non fourni");
				}
				// add unlink file
			}else{
				echo "Ce cas n'est pas prévu : ".$_POST["submit"]."<br>";
			}
		break;
		case "delete-user":
			require_once(ABSPATH."wp-admin/includes/user.php");
			function_alert("Votre compte a été supprimé.");
			
    		$target = "/srv/data/web/vhosts/www.spicylotus.fr/htdocs/wp-content/themes/blossom-travel-child/img/documents/".$current_user->ID;
			
			// source : https://www.geeksforgeeks.org/how-to-recursively-delete-a-directory-and-its-entire-contents-files-sub-dirs-in-php/
			// Get the list of all of file names in the folder. 
			$files = glob($target . "/*"); 
			// Loop through the file list 
			foreach($files as $file) {
				if(is_file($file)) {// Check for file
					unlink($file);// Use unlink function to delete the file
				}
			}
			rmdir($target);// doit etre vide pour fonctionner

			// wp_delete_file( $file_path ); //delete file here.
			// die;
			wp_delete_user($current_user->ID,null);

		break;
		default:
			echo "Ce cas n'est pas prévu.";
	}
	// printr($_FILES);
	// printr($_SESSION["error"]);
	// die;
	// error upload php
	if(!empty($_FILES)){
		$keys = array_keys($_FILES);
		$key = $keys[0];
		echo " ".$key."<br>";

		if(!empty($_FILES[$key])&&$_FILES[$key]["name"]!==""&&$_FILES[$key]["error"]!=0){ // traite les erreurs php
			$_SESSION["error"][$key]=[];
			// echo $php_upload_errors[ $_FILES[$key]["error"] ];
			$texts = explode("-",$php_upload_errors[$_FILES[$key]["error"]]);
			// modif de la phrase pour monAvatar
			if($key==="monAvatar"&&$texts[1]==="2Mo."){// tableau de 4 elements => transforme en 2 elements
				$texts[0]=$texts[0]."&nbsp;500ko."; // remplace 2Mo
				unset($texts[1]);
				$texts[2]=$texts[2]."&nbsp;300x300 pixels.";// remplace 2Mpx
				unset($texts[3]);
			}
			foreach($texts as $text)
				array_push($_SESSION["error"][$key],$text);
			array_push($_SESSION["error"][$key],"Echec du téléchargement.");
			$suffix["item"]=$key;

		}
		// printr($_FILES);
		// printr($_SESSION["error"]);
		// die;
	}
	// die;
	if ( empty($errors) && (!isset($_SESSION["error"])
		|| empty($_SESSION["error"]))
		) {  // redirection prevPage 
        //action hook for plugins and extra fields saving
		do_action("edit_user_profile_update", $current_user->ID);
		// echo $current_user;
        wp_redirect( get_permalink() );
        exit;
	}
}

get_header();

if ( have_posts() ) { 
	while ( have_posts() ) { 
		the_post(); ?>
		<div id="post-<?php the_ID(); ?>">
			<div class="entry-content entry" id="update-user">
				<?php
				the_content();
				if ( !is_user_logged_in()) { ?>
					<p class="warning">
						<?php echo "Vous devez être connecté pour accéder à votre profil."; ?>
					</p><!-- .warning -->
					<a href=" <?php echo home_url("/wp-login.php") ?>" title="Se connecter">
                		Se connecter
					</a>
					<a href=" <?php echo home_url() ?>" title="Se connecter">
                		Retour à l'accueil
					</a>
				<?php 
				}elseif( empty(get_user_meta($current_user->ID,"account_actived"))
						    || get_user_meta($current_user->ID,"account_actived") === "0" ){?>
					<p class="warning">
						<?php _e("Pour accéder à votre profil, consultez l'email pour changer de mot de passe et/ou activer votre compte.", "profile"); ?>
					</p><!-- .warning -->
				<?php
				}else{

					$suffix = $suffixs["monAvatar"];
					require "include/template/display_doc.php";
					?>
					<form  action="<?php echo get_permalink()."#tags-six"; ?>" method="post" id="tags-six">
						<h3 class="w100">Informations sur votre profil </h3>
						
						<?php
						if ( !empty($errors)) {
							echo "<ul>";
							foreach($errors as $error)
								echo "<li class='erreur' style='list-style:none;'><i>".$error."</i></li>";
							echo "</ul>";
							unset($errors);
						} ?>
						<div>
							<div class="trois"> <!-- identifiant / mail mdp1 mdp2 -->
								
									<p class="form-"><label for="user_login">Identifiant </label>
										<input type="text" name="user_login" id="user_login" class="input" disabled value="<?php the_author_meta("user_login",$current_user->ID); ?>" >
									</p>
									<p class="form-"><label for="user_email">Email </label>
										<input type="text" name="user_email" id="user_email" value="<?php the_author_meta("user_email",$current_user->ID); ?>">
									</p>
									<p>
									<label for="pass1">Modifier votre mot de passe </label> 
										<input type="password" id="pass1" name="pass1" size="25"  value="" />
									</p>
									<p class="form-">
										<label for="pass2">Répéter votre mot de passe </label> 
										<input type="password" id="pass2" name="pass2" size="25"  value="" />
									</p>
							</div>
							<div class="trois"> <!-- prenom nom tel  -->
								<p class="form-"><label for="first-name">Prénom </label>
									<input type="text" name="first-name" id="first-name" class="input" value="<?php echo explode(" ",get_the_author_meta("display_name",$current_user->ID))[0]; ?>" >
								</p>
								<p class="form-"><label for="last-name">Nom </label>
									<input type="text" name="last-name" id="last-name" class="input" value="<?php echo explode(" ",get_the_author_meta("display_name",$current_user->ID))[1]; ?>" >
								</p>
								<p class="form-"><label for="tel">Téléphone </label>
									<input type="text" name="tel" id="tel" class="input" minlength="10" value="<?php the_author_meta("tel",$current_user->ID); ?>">
								</p>
							</div>
						</div>
						<div class="w100"> <!-- submit -->
							<input type="hidden" name="action" value="update-user-metas" size="25" />
							<input type="submit" name="submit" value="Mettre à jour" />
						</div>

					</form><!-- id mail mpd prenom nom tel -->
						<!-- photo de profil -->
					<div class="w100"> <!-- upload doc admin pour inscription -->
						<h3 class="w100">Documents administratifs à fournir </h3>
						<div class="flex-column">
							<?php
							foreach($suffixs as $suffix){
								if($suffix["item"]!=="monAvatar")
									require "include/template/display_doc.php"; 
							}
							?>
						</div> <!-- flex-column -->
					</div>
					<form class="w100" action="<?php echo get_permalink(); ?>" method="post" id="tags-six">	
						<input type="submit" hidden name="submit" value="Supprimer mon compte..." />
						<input type="hidden" name="action" value="delete-user" size="25" />
                		<p id="deleteMyProfil" class="supprime btn btn-secondary">Supprimer&nbsp;mon&nbsp;compte</p>
					</form>
				<?php
				}?>
			</div><!-- .entry-content -->
		</div><!-- .entry .post -->
	<?php
	}
}else{ ?>
    <p class="no-data">
        <?php _e("Désolé, aucune page ne répond à vos critères.", "profile"); ?>
    </p><!-- .no-data -->

<?php 
}
?>
<input id="token_profil" type="hidden" name="token_profil" value="<?php echo $_SESSION["token_profil"] ?>" />
<input id="current_user_ID" type="hidden" name="current_user_ID" value="<?php echo $current_user->ID; ?>" />

<script src="<?php echo get_stylesheet_directory_uri().'/js/upload.js' ?>" type="module"></script>
<script src="<?php echo get_stylesheet_directory_uri().'/js/del.js' ?>"></script>

<ul id="debug"></ul>

<?php
get_footer();