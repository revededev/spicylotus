<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Blossom_Travel
 */
    /**
     * Doctype Hook
     * 
     * @hooked blossom_travel_doctype
    */

    do_action( 'blossom_travel_doctype' );

echo '<head itemscope itemtype="http://schema.org/WebSite">';

    /**
     * Before wp_head
     * 
     * @hooked blossom_travel_head
    */
    do_action( 'blossom_travel_before_wp_head' );
    
    wp_head();
echo '</head>';

?>


<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
    <?php
    global $current_user, $wp_roles;
    $display_name = get_the_author_meta("display_name",$current_user->ID);
    if ( is_user_logged_in()) { ?>
        <head>
            <style>
                div.log {
                    display: flex;
                    position: absolute;
                    top: 0px;
                    right: 5px;
                    padding-top:5px;
                    flex-direction: column;
                    justify-content: center;
                    align-items: end;
                    z-index: 100;
                    font-size: 1.6rem;
                }
                @media screen and (max-width: 768px) {
                    div.log{font-size: 90%;}
                    /* div.log a:first-child{display: none;} */
                }
                @media screen and (max-width: 576px) {
                    div.log{font-size: 95%;justify-content: flex-start;}
                    div.log a:first-child{display: none;}
                }
                @media screen and (max-width: 360px) {
                    div.log{font-size: 85%;justify-content: flex-start;}
                    div.log a:first-child{display: none;}
                }
                @media screen and (max-width: 340px) {
                    div.log{font-size: 80%;justify-content: flex-start;}
                    div.log a:first-child{display: none;}
                }

            </style>
        </head>
    <?php
    }

    wp_body_open(); 
    ?>

    <div class="log">

        <?php    
        /* Get user info. */


        $url = home_url();

        if ($current_user->ID !== 0){
            echo "<a>Bonjour ".$display_name."</a>" ;?>
            <a href="<?php echo wp_logout_url( $url); ?>" title="Se déconnecter">
                Se déconnecter
            </a>
            <a href="<?php echo home_url('/profil/#update-user') ?>" title="Mon compte">
                Mon compte
            </a>
            <?php
            if (in_array('administrator',$current_user->roles)||in_array('editor',$current_user->roles)){?>
                <a href="<?php echo home_url('/wp-admin/admin.php?page=validation-des-inscriptions'); ?>" title='Mon compte'>
                    Valider des inscriptions
                </a>
            <?php
            }
        } else { ?>
            <a href=" <?php echo home_url('/wp-login.php') ?>" title="Se connecter">
                Se connecter
            </a>
            <a href="<?php echo home_url('/wp-login.php?action=register') ?>" title="S'inscrire">
                S'inscrire 
            </a>
        <?php } ?>

    </div>

<?php
    /**
     * Before Header
     * 
     * @hooked blossom_travel_page_start - 20  
    */
    do_action( 'blossom_travel_before_header' );
    
    /**
     * Header
     * 
     * @hooked blossom_travel_header - 20       
     * @hooked blossom_travel_responsive_nav - 30    
    */
    do_action( 'blossom_travel_header' );
    
    /**
     * Before Content
     * 
     * @hooked blossom_travel_banner  - 25
     * @hooked blossom_travel_trending_section  - 30
     * @hooked blossom_travel_featured_section  - 35
    */
    do_action( 'blossom_travel_after_header' );

    /**
     * Content
     * 
     * @hooked blossom_travel_content_start
    */
    if (
           get_the_permalink() === 'http://localhost:8888/spicylotus/profil/'
        || get_the_permalink() === 'http://localhost:8888/spicylotus/se-connecter/'
        || get_the_permalink() === 'http://localhost:8888/spicylotus/sinscrire/'
        || get_the_permalink() === 'https://www.spicylotus.fr/profil/'
        || get_the_permalink() === 'https://www.spicylotus.fr/se-connecter/'
        || get_the_permalink() === 'https://www.spicylotus.fr/sinscrire/'
        ){
        // echo '<div class="container">';
        // echo '<br>coucou<br>';
        ;
    }else{
        do_action( 'blossom_travel_content' );
    }
    // do_action( 'blossom_travel_content' );
?>