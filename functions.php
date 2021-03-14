<?php
// echo ' inside_function ';

// require 'include/init.php'; // ajout du 4 oct 2020
require 'include/myTools.php'; // ajout du 23 mars

// remove wp version
remove_action('wp_head', 'wp_generator');


function ur_theme_start_session()
{
    if (!session_id())
        session_start();
}
add_action("init", "ur_theme_start_session", 1);
// construction lien d activation :
// https://www.spicylotus.fr/wp-login.php?action=rp&key=aM8r2MljNMoQCzWdJsLp&login=testFREDEric
add_action( 'wp_enqueue_scripts', 'theme_enqueue_my_styles' );
function theme_enqueue_my_styles() {
    wp_enqueue_style( 'login-style',get_stylesheet_directory_uri().'/css/login.css',array('parent-style'),time() );
    // 'parent-style' charger avant 'my-style'
    wp_enqueue_style( 'my-style',get_stylesheet_directory_uri().'/css/myStyle.css',array('parent-style'),time() );
    // 'parent-style' charger avant 'my-style'
    wp_enqueue_style( 'header-style',get_stylesheet_directory_uri().'/css/header.css',array('parent-style','my-style'),time() );
    // 'parent-style' et 'my-style' charger avant 'header-style'
    wp_enqueue_style( 'decouvrez-style',get_stylesheet_directory_uri().'/css/decouvrez.css',array('parent-style','my-style'),time() );
    // 'parent-style' et 'my-style' charger avant 'decouvrez-style'
    wp_enqueue_style( 'actus-style',get_stylesheet_directory_uri().'/css/actus.css',array('parent-style','my-style'),time() );
    // 'parent-style' et 'my-style' charger avant 'actus-style'
    wp_enqueue_style( 'style-footer',get_stylesheet_directory_uri().'/css/footer.css',array('parent-style','my-style'),time() );
    // 'parent-style' et 'my-style' charger avant 'style-footer'
    wp_enqueue_style( 'parent-style',get_template_directory_uri().'/style.css',array(),time());
}

add_action('login_enqueue_scripts', 'login_enqueue_my_styles');
function login_enqueue_my_styles(){
    wp_enqueue_style( 'mylogin',get_stylesheet_directory_uri().'/css/myLogin.css',array(),time());
}
// https://codex.wordpress.org/Customizing_the_Registration_Form
add_action( 'register_form', 'names_register_form' );
function names_register_form() {
    // Get and set any values already sent
    $prenom = ( isset( $_POST["prenom"] ) ) ? $_POST["prenom"] : "";
    $nom = ( isset( $_POST["nom"] ) ) ? $_POST["nom"] : "";
    $tel = ( isset( $_POST["tel"] ) ) ? $_POST["tel"] : "";

    ?>
    <p>
        <label for="prenom">Prénom
            <input placeholder="" value="<?php echo $prenom;?>" required type="text" name="prenom" id="prenom" class="input" size="25" />
        </label>
    </p>
    <p>
        <label for="nom">Nom
            <input placeholder="" value="<?php echo $nom;?>" required type="text" name="nom" id="nom" class="input" size="25" />
        </label>
    </p>
    <p>
        <label for="tel">Téléphone
            <input placeholder="" value="<?php echo $tel;?>" required type="text" name="tel" id="tel" class="input" size="25" />
        </label>
    </p>
    <div class="question_yes">
        <p>Etes-vous d accord?</p>
        <p>
            <label class="form-check-label" for="reponse1">Oui
                <input class="form-check-input" type="radio" name="question" id="reponse1" value="oui">
            </label>
        </p>
        <p>
            <label class="form-check-label" for="reponse2">Non
                <input class="form-check-input" type="radio" name="question" id="reponse2" value="non" checked>
            </label>
        </p>
    </div>
<?php
}
add_filter( 'registration_errors', 'names_registration_errors', 10, 3 );
function names_registration_errors( $errors ) {
    if ( empty( $_POST['question'] ) || (! empty( $_POST['question'] ) && trim( $_POST['question'] ) === 'oui' ) ) {// antibot
        $errors->add( 'question_error', sprintf('<strong>%s</strong>: %s',__( 'Erreur', 'mydomain' ),__( 'Erreur.', 'mydomain' ) ) );
    }
    if ( empty( $_POST['nom'] ) || (! empty( $_POST['nom'] ) && trim( $_POST['nom'] ) == '' ) ) {
        $errors->add( 'nom_error', sprintf('<strong>%s</strong>: %s',__( 'Erreur', 'mydomain' ),__( 'Vous devez enter le nom de votre nom.', 'mydomain' ) ) );
    }
    if ( empty( $_POST['prenom'] ) || (! empty( $_POST['prenom'] ) && trim( $_POST['prenom'] ) == '' ) ) {
        $errors->add( 'prenom_error', sprintf('<strong>%s</strong>: %s',__( 'Erreur', 'mydomain' ),__( 'Vous devez enter le nom de votre prenom.', 'mydomain' ) ) );
    }
    if ( empty( $_POST['tel'] ) || (! empty( $_POST['tel'] ) && trim( $_POST['tel'] ) == '' ) ) {
        $errors->add( 'tel_error', sprintf('<strong>%s</strong>: %s',__( 'Erreur', 'mydomain' ),__( 'Vous devez enter votre numéro de téléphone.', 'mydomain' ) ) );
    }elseif( empty( $_POST['tel'] ) || (! empty( $_POST['tel'] ) 
        && count(str_split (preg_replace("/[^0-9]/", "",trim($_POST['tel'])))) !==10 && count(str_split (preg_replace("/[^0-9]/", "",trim($_POST['tel'])))) !==11) ) {
            $errors->add( 'tel_error', sprintf('<strong>%s</strong>: %s',__( 'Erreur', 'mydomain' ),__( 'Votre numéro de téléphone doit contenir 10 ou 11 chiffres.', 'mydomain' ) ) );
        
            if ( empty( $_POST['tel'] ) || (! empty( $_POST['tel'] ) && count(str_split (preg_replace("/[^0-9]/", "",trim($_POST['tel'])))) ===10 
                && str_split(preg_replace("/[^0-9]/", "",trim($_POST['tel'])))[0] !=="0"  ) ) {
                    // printr(str_split(preg_replace("/[^0-9]/", "",trim($_POST['tel']))));
                $errors->add( 'tel_error', sprintf('<strong>%s</strong>: %s',__( 'Erreur', 'mydomain' ),__( 'Votre numéro de téléphone doit commencer par 0.', 'mydomain' ) ) );
            }
    }
    return $errors;
}
//3. Finally, save our extra registration user meta.
add_action( 'user_register', 'names_registration_save', 10, 1 );
function names_registration_save( $user_id ) {
    $prenom =  str_replace(' ', '-',trim(esc_attr( $_POST['prenom'])));
    $nom = str_replace(' ', '-',trim(esc_attr( $_POST['nom'])));
    $display_name = $prenom.' '.$nom;
    $tel = str_replace(' ', '-', preg_replace("/[^0-9]/", "",trim($_POST['tel'])) ); // ici une regex qui ne garde que les chiffres
    if ( isset( $display_name ) ){
        update_user_meta($user_id, 'first_name', $prenom);
        update_user_meta($user_id, 'last_name', $nom);
        update_user_meta($user_id, 'show_admin_bar_front', 'false');
        update_user_meta($user_id, 'account_actived', '0');
        add_user_meta($user_id,'is_certif', 'non fourni');
        add_user_meta($user_id,'is_licence', 'non fourni');
        add_user_meta($user_id,'is_yoga', 'non fourni');
        add_user_meta($user_id,'is_assurance', 'non fourni');
        add_user_meta($user_id,'tel', $tel);
        wp_update_user( array( 'ID' => $user_id, 'display_name' => $display_name ) );
    }
}
// apply_filters( 'registration_redirect', site_url('/profil/') );
add_filter('login_redirect', 'this_page',10,3);
function this_page($redirect_to, $requested_redirect_to, $user) {
// source model : https://stackoverflow.com/questions/41320760/how-to-redirect-after-login-to-a-particular-page-in-wordpress
    // URL to redirect to
    $redirect_url = (in_array('administrator',$user->roles)||in_array('editor',$user->roles)) ? home_url() : home_url('/profil/');

    // active le compte car user a suivi le lien pour changer son mdp
    // !! Rappel : type(value_key) = long text
    if(get_user_meta( $user->id, 'account_actived', true )=== '0'){
        update_user_meta($user->id, 'account_actived', '1');
    }
    // If they're on the login page, don't do anything
    if( !isset( $user->user_login ) ){
        return $redirect_to;
    }else{
        return $redirect_url;
    }
}
  
// function theme_enqueue_my_scripts() {
//     wp_enqueue_script( 'my-script',get_stylesheet_directory_uri().'/js/monScript.js',array(),time() );
// }
// add_action( 'wp_enqueue_scripts', 'theme_enqueue_my_scripts' );
// register_activation_hook( __FILE__.'gestion-validation', 'gestion-validation' );
add_action( "admin_menu", "validation_page" );
// "Validation des documents d'inscription",// $page_title
// "Inscriptions...", // $menu_title
// "manage_options",// $capability
// // plugin_dir_path(__FILE__) . 'admin/view.php',
// // null,
// "validation-des-inscriptions",// $menu_slug, => .../wp-admin/admin.php?page=$menu_slug
// "validation_page_html",//'wporg_options_page_html',// callable $function = '',
// "",//  string $icon_url = '',
// 2 // position ds le menu
function validation_page() {
    add_menu_page(
        "Validation des documents d'inscription",
        "Inscriptions...",
        "manage_options",
        "validation-des-inscriptions",
        "validation_page_html",
        "",
        2
    );
}
function validation_page_html() {//
    global  $wpdb,$current_user, $wp_roles;
    session_start();

    if(!isset($_SESSION["token_profil"])) {$_SESSION["token_profil"] = bin2hex(random_bytes(32));}// Efficacité à tester...
    // $_SESSION["token_profil"] = bin2hex(random_bytes(32));
    if ( "POST" === $_SERVER["REQUEST_METHOD"] && !empty( $_POST["action"] )){
        if( $_POST["action"] && $_POST["action"]==="create-list" ){
            if (isset($_POST["searchs"])){

                $is_select_list = true;
                switch($_POST["searchs"]){
                    case "n ont fourni aucun document":
                        $sql = "SELECT user_id FROM NICE_usermeta WHERE
                        ((meta_key = 'is_assurance' AND meta_value = 'non fourni')
                        OR (meta_key = 'is_yoga' AND meta_value = 'non fourni')
                        OR (meta_key = 'is_licence' AND meta_value = 'non fourni')
                        OR (meta_key = 'is_certif' AND meta_value = 'non fourni'))
                        GROUP BY user_id
                        HAVING  count(user_id)=4
                        ";
                        // SOURCE pour OBJECT_K : https://www.php.net/manual/fr/function.array-unique.php
                        // ajouter prepare() avant mise en ligne
                        $get_IDs = $wpdb->get_results($sql,OBJECT_K); // supprime les doublons
                        $get_IDs = array_keys ($get_IDs); // retient les keys (càd les user_ID) et les met en tableau
                        // $get_IDs contient ts les id 
                        // // create file json  (AJAX)
                        // require 'include/create_users_info.php';
                    break;
                    case "ont des documents à vérifier":
                        $sql = "SELECT user_id FROM NICE_usermeta WHERE 
                            (meta_key  LIKE 'is_%' AND meta_value = 'en attente')
                        ";
                        $get_IDs = $wpdb->get_results($sql,OBJECT_K);
                        $get_IDs = array_keys ($get_IDs);
                    break;
                    case "ont un document obsolète":
                        $sql = "SELECT user_id FROM NICE_usermeta WHERE 
                            (meta_key  LIKE 'is_%' AND meta_value = 'obsolete')
                        ";
                        $get_IDs = $wpdb->get_results($sql,OBJECT_K);
                        $get_IDs = array_keys ($get_IDs);
                    break;
                    case "n ont pas validé leurs 4 documents":
                        $sql = "SELECT user_id FROM NICE_usermeta WHERE 
                            (meta_key = 'is_assurance' AND meta_value != 'valide')
                            OR (meta_key = 'is_assurance' AND meta_value != 'valide')
                            OR (meta_key = 'is_certif' AND meta_value != 'valide')
                            OR (meta_key = 'is_licence' AND meta_value != 'valide')
                            OR (meta_key = 'is_yoga' AND meta_value != 'valide')
                        ";
                        $get_IDs = $wpdb->get_results($sql,OBJECT_K);
                        $get_IDs = array_keys ($get_IDs);
                    break;
                    case "ont validé leurs 4 documents":
                        $sql = "SELECT user_id FROM NICE_usermeta WHERE
                        (meta_key LIKE 'is_%' AND meta_value = 'valide')
                        GROUP BY user_id
                        HAVING  count(user_id)=4
                        ";
                        $get_IDs = $wpdb->get_results($sql,OBJECT_K);
                        $get_IDs = array_keys ($get_IDs);
                    break;
                    default:$is_select_list = false;
                } // switch select
            }
        }
    }
    
    ?>
    <head>
        <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri().'/css/login_copy.css'; ?>">
    </head>
    <div class="entry-content entry">
    
        <div id="gestion">
        <!-- <h2>session: <?php printr($_SESSION)?></h2> -->
            <form id="raz" method="GET" action=""> <!-- raz = remise à zéro -->
                <!-- <h2>Cahnger valide to obsolete</h2> -->

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="raz" id="raz1" value="valideToObsolete">
                    <label class="form-check-label" for="raz1" >
                        Changer tous les status "valide" par "obsolete"  (non actif pour le moment)
                    </label>
                </div>
                <div class="form-check disabled">
                    <input checked class="form-check-input" type="radio" name="raz" id="raz2" value="rien">
                    <label class="form-check-label" for="raz2">
                        Ne rien modifier
                    </label>
                </div>
                <p type="submit" id="raz_go" class='supprime btn btn-secondary'>Valider</p>
            </form>
            <form method="POST" action="<?php the_permalink(); ?>">
                <div class="form-group">
                    <label for="search-select">Etablir la liste des lotus qui...</label>
                    <select name="searchs" id="search-select">
                        <option value="">-- Choisir une option --</option>
                        <option value="n ont fourni aucun document">n'ont fourni aucun document</option>
                        <option value="ont des documents à vérifier">ont au moins un document en attente de validation</option>
                        <option selected="selected" value="n ont pas validé leurs 4 documents">n'ont pas validé leurs 4 documents</option>
                        <option value="ont validé leurs 4 documents">ont validé leurs 4 documents</option>
                        <option value="ont un document obsolète">ont au moins un document obsolète</option>
                    </select>
                    <?php if (isset($is_select_list) && !$is_select_list){
                        echo '<br><i class="erreur">Vous devez sélectionner une option</i>';
                    }?>
                </div>
                <div>
                    <input type="hidden" name="action" value="create-list" size="25" />
                    <button type="submit" class="btn">Valider</button>
                </div>
            </form> <!-- select list -->
            <div class="liste">
                <?php if (isset($_POST['searchs'])){
                    echo "<p>Résultats de recherche pour les lotus qui ". $_POST["searchs"] ." :</p>";
                    
                }else{echo '<p></p>';}
                echo "<div id='display_errors'></div>";
                ?>
                <ul class="flex-colomn">
                    <!-- tableau-liste header -->
                    <?php 
                    require "include/template/list_header.gestion.php";
                    // User list Loop
                    if ( isset($get_IDs) && !empty($get_IDs) ) {
                        foreach ( $get_IDs as $get_ID) {
                        echo "
                        <li>
                            <ul class='flex-row'>
                                <li class='grand' title='Prénom+nom'>" . 
                                    get_the_author_meta('display_name',$get_ID)
                                ."</li>
                                <li class='grand' title='Mail'>" . 
                                    get_the_author_meta('user_email',$get_ID)
                                ."</li>
                                <li class='petit ".get_the_author_meta('is_assurance',$get_ID)."' title='Assurance'>" . 
                                    get_the_author_meta('is_assurance',$get_ID)
                                ."</li>
                                <li class='petit ".get_the_author_meta('is_certif',$get_ID)."' title='Certif médical'>" . 
                                    get_the_author_meta('is_certif',$get_ID)
                                ."</li>
                                <li class='petit ".get_the_author_meta('is_licence',$get_ID)."' title='F.F.Danse'>" . 
                                    get_the_author_meta('is_licence',$get_ID) 
                                ."</li>
                                <li class='petit ".get_the_author_meta('is_yoga',$get_ID)."' title='Attestation Yoga'>" . 
                                    get_the_author_meta('is_yoga',$get_ID)
                                ."</li>
                                <li title='id (n° de client)' id='numID-" . $get_ID . "'>" . $get_ID . "</li>
                            </ul>
                        </li> <!-- loop lignes -->
                        ";
                        }
                    } else {
                        echo "<li class='erreur'>Aucun résultat.</li>";
                    }
                    // User list Loop
                    require "include/template/list_header.gestion.php";
                    ?>
                </ul> <!-- list : Tableau complet -->
            </div><!-- liste -->
        </div> <!-- id="gestion" -->
        <!-- Modal seeUser -->
        <form id="seeUser">
        </form>
        <!-- Modal seeUser -->
    </div><!-- .entry-content.entry -->
   
    <input id="token_profil" type="hidden" name="token_profil" value="<?php echo $_SESSION["token_profil"] ?>" />
    <input id="current_user_ID" type="hidden" name="current_user_ID" value="<?php echo $current_user->ID; ?>" />
     <?php
     echo "<script type='module' src='".get_stylesheet_directory_uri()."/js/api_user_admin.js'></script>";

//
}
// add_action("login_form","anti_bot");
function anti_bot(){// fired after input password
    printr($_SERVER);
    // $_SESSION["anti_bot"] = (!empty($_SESSION["anti_bot"])&&!empty($_SESSION["anti_bot"])&&$_SERVER["REMOTE_ADDR"]===$anti_bot) ?
    ?>
    <div class="question_yes">
        <p>
            <label class="form-check-label" for="reponse1">
                <input class="form-check-input" type="radio" name="question" id="reponse1" value="oui">
            </label>
        </p>
        <p>
            <label class="form-check-label" for="reponse2">
                <input class="form-check-input" type="radio" name="question" id="reponse2" value="non" checked>
            </label>
        </p>
    </div>
    <div class="form-check">
        <label class="form-check-label" for="add" ></label>
        <input class="form-check-input" type="text" name="add" id="add">
    </div>
<?php
}

add_image_size( 'micro_vignette', 98, 98, true );
add_image_size( 'carousel-accueil', 340, 340, true );

register_nav_menus( array(
    'main' => 'menu principal',
    'contact' => 'menu pied de page',
    'bas' => 'menu bas de page',
) );








