<?php
/**
 * The template for displaying all single posts
 * càd : les articles
 * Catégories : actualites, disciplines, le-studio ...
 * Problème !! Je ne souhaite que seules les actualités soient affichées avec ce modèle... D'où les conditions ci-dessous
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Blossom_Travel
 */
get_header(); ?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main">
            <?php 
            $category = get_the_category()[0]->slug;
            // printL('cat',$category);
            $is_actu = ($category =='actualites') ? '1': '0';
            // echo ' is_actu ? :'.$is_actu.'<br>';
            while ( have_posts() ) {
                if ($is_actu) { // modele blossom_travel : 'single.php'
                    the_title();
                    the_post();
                    get_template_part( 'template-parts/content', get_post_type() );
                } else {        // modele blossom_travel : 'page.php'
                    the_post();
                    get_template_part( 'template-parts/content', 'page' );
                    // do_action( 'blossom_travel_after_page_content' );
                }
    		} // End of the loop.
    		?>
		</main><!-- #main -->
        <?php
        /**
         * @hooked blossom_travel_author               - 15
         * @hooked blossom_travel_navigation           - 20
         * @hooked blossom_travel_related_posts        - 35
         * @hooked blossom_travel_comment              - 45
        */
        if ($is_actu) {        // modele blossom_travel : 'single.php'
             do_action( 'blossom_travel_after_post_content' );
        }?>
	</div><!-- #primary -->
<?php
// get_sidebar();
get_footer();