<?php
// A voir :
// les msg d erreur apres l upload = ok
// => mes erreurs coté serveur = ok
// => A voir erreur php ou wp = ok
// resize image coté serveur = ok
// resize image before upload = 1200x1700 via js

// https://rudrastyh.com/wordpress/how-to-add-images-to-media-library-from-uploaded-files-programmatically.html
// WordPress environment


// ex : $suffix['item'] = 'certif';
// $_SESSION['error'][$suffix['item']] = [];
$wordpress_upload_dir = wp_upload_dir();
// $wordpress_upload_dir = get_stylesheet_directory_uri();

// $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2017/05

$_SESSION['error'][$suffix['item']] =[];
$the_file = $_FILES[$suffix['item']];
$new_file_mime = $the_file['type'];
$taille_maxi = 2500000;//2097152 octets = 2,000Mo
// $taille_maxi = 3000;
$taille = filesize($the_file['tmp_name']);
$extensions = array('.pdf', '.gif', '.jpg', '.jpeg'); // permet de ne pas uploader
 // strrchr : derniere occurence // ex : strrchr('leNom.class.jgp') = '.jpg'
 // strtolower : transforme les lettres en minuscule
$extension = strtolower(strrchr($the_file['name'], '.'));
// $extension = mime_content_type( $the_file['tmp_name'] );

$nom_fichier = basename($the_file['name']);
$text = ($suffix['item']==='monAvatar') ? "La résolution conseillée est de 300x300 pixels" : "La résolution conseillée est de 2Mpx";


if($taille>$taille_maxi){
    array_push($_SESSION['error'][$suffix['item']],"Ce fichier est trop gros, il doit faire moins de 2Mo :");
    array_push($_SESSION['error'][$suffix['item']],$text);
}

if( !in_array($extension, $extensions) )
    array_push($_SESSION['error'][$suffix['item']],'Vous devez télécharger un fichier de type : pdf, png, gif, jpg ou jpeg');

// error upload php
if($the_file['error'] !=0)
    array_push($_SESSION['error'][$suffix['item']],$php_upload_errors[$the_file['error']]);

// printr($_SESSION['error'][$suffix['item']]);
// die;


if( empty($_SESSION['error'][$suffix['item']])){

    unset($_SESSION['error'][$suffix['item']]);
    // But : eviter le pb de cache => changer le nom à chaque fois :
    $t = time();
    $nom_fichier = "id_".$current_user->id."_is_".$suffix['item']."_".$t.$extension;

    $new_file_path = $wordpress_upload_dir['basedir'];
    $new_file_path = explode("/",$new_file_path);
    // si https : $_SERVER["DOCUMENT_ROOT"] = /srv/data/web/vhosts/www.spicylotus.fr/htdocs
    // local : = C:\wamp64\www\spicylotus\wp-content\themes\blossom-travel-child\img\documents
    // $target ="/srv/data/web/vhosts/www.spicylotus.fr/htdocs/wp-content/themes/blossom-travel-child/img/documents/".$current_user->ID."/";
    // 
    if($_SERVER["SERVER_NAME"]==="localhost"){
        $racine = "C:/wamp64/www/spicylotus";
    }else{
        $racine = "/srv/data/web/vhosts/www.spicylotus.fr/htdocs";
    }
    $target = $racine."/wp-content/themes/blossom-travel-child/img/documents/".$current_user->ID."/";

    wp_mkdir_p(  $target );
    $new_file_path = $racine."/wp-content/themes/blossom-travel-child/img/documents/".$current_user->ID."/". $nom_fichier;

    array_map('unlink', glob($target."/*".$suffix['item']."*"));
    // au cas ou le déplacement se passe mal :


    // looks like everything is OK
    // a remplacer par une promise avec resolve et reject
    if( move_uploaded_file( $the_file['tmp_name'], $new_file_path ) ) {

        $upload_id = wp_insert_attachment( array(
            'guid'           => $new_file_path, 
            'post_mime_type' => $new_file_mime,
            'post_title'     => preg_replace( '/\.[^.]+$/', '', $nom_fichier ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        ), $new_file_path );

    // wp_generate_attachment_metadata() won't work if you do not include this file
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
    // Generate and save the attachment metas into the database
        wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );

        if( in_array($extension, [".pdf",".PDF"])){
            $nom_fichier = "id_".$current_user->id."_is_".$suffix['item']."_".$t."-pdf.jpg";
            sleep(.25); // il faut attendre que wp créer sont jpg a partir du pdf...
            $i=0;
            while(!file_exists ($target."/".$nom_fichier)&&$i<8){
                // il faut attendre que wp créer sont jpg a partir du pdf...
                sleep(.25); 
                // En local wp ne crée pas de jpg a partir du pdf
                $i++;
            }
        }else{
            // sleep(.20); // sur mon WAMP, il faut attendre un peu...
            if(!empty($_SERVER["SERVER_NAME"]) && $_SERVER["SERVER_NAME"]==="localhost")
                sleep(.20);

            $i=0;
            while(!file_exists ($target."/".$nom_fichier)&&$i<8){// à priori ce while loop inutile en prod...
                sleep(.15); 
                $i++;
            }
        }

        update_user_meta( $current_user->ID, 'path_'.$suffix['item'], get_stylesheet_directory_uri().'/img/documents/'.$current_user->id."/" . $nom_fichier );
        // $jpg = [".jpg",".jpeg"];
        if($suffix['item'] !== 'monAvatar'){
            update_user_meta( $current_user->ID, 'is_'.$suffix['item'], 'en attente');
            $x=1200;// réso max pour l'oeil = 1600px pour un doc A4 de 20cm de large vu à 50cm
        }else{
            $x=300; // fixe la largueur des images apres avoir été redimensionnées
        }        
        if(in_array($extension,[".jpg",".jpeg"])){


            // redimensionnement des images - source : https://code-boxx.com/resize-images-php/
            $chemin = $target."/".$nom_fichier; // le chemin en absolu
            
            list($l, $h) = getimagesize($chemin);
            echo " l = ".$l." h = ".$h."<br>";
            $original = imagecreatefromjpeg($chemin);
            $resized = imagecreatetruecolor($x, $x*$h/$l);
            imagecopyresampled($resized, $original, 0, 0, 0, 0,$x, $x*$h/$l, $l, $h);
            imagejpeg($resized, $chemin);
        }
        sleep(1.5);
        // array_map('unlink', glob($target."/*".$suffix['item']."*"));
        array_map('unlink', glob($target."/".$nom_fichier."-*"));

    }else{
        update_user_meta( $current_user->ID, 'path_'.$suffix['item'], "" );
        if($suffix['item'] !== 'monAvatar'){
            update_user_meta( $current_user->ID, 'is_'.$suffix['item'], 'non fourni');
        }
        array_push($_SESSION['error'][$suffix['item']],"Le fichier n'a pas pu être déplacé vers le répertoir de destination.");
        array_push($_SESSION['error'][$suffix['item']],"Echec du téléchargement.");
    }
}else{
    array_push($_SESSION["error"][$suffix["item"]],"Echec du téléchargement.");
    // printMyErrors($_SESSION["error"][$suffix["item"]]);
}
?>