<?php
/**
 * The template for displaying the footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Blossom_Travel
 * 
 */

//  do_action( 'blossom_travel_before_footer' );
echo (is_front_page()) ? '': '</div>';
// tjs pour fermer ce satané div.container
?>
<!-- =============================================================================================  -->
<footer class="footer-haut">
    <section class="container">
        <?php // 'contact' => 'menu pied de page',
              // 'bas' => 'menu bas de page',
        $contact = array(
            'theme_location'  => 'contact',
            'menu'            => '',
            'container'       => 'section',
            'container_class' => '',
            'container_id'    => '',
            'menu_class'      => '',  //%2$s
            'menu_id'         => 'menu-haut', //%1$s
            'echo'            => true,
            'fallback_cb'     => 'wp_page_menu',
            'before'          => '',
            'after'           => '',
            'link_before'     => '',
            'link_after'      => '',
            'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'depth'           => 0,
            'walker'          => ''
           );
            
           wp_nav_menu( $contact ); ?>
    </section>
</footer>
<footer class="footer-bas">  
    <?php
        $bas = array(
    'theme_location'  => 'bas',
    'menu'            => '',
    'container'       => 'section',
    'container_class' => 'container',
    'container_id'    => '',
    'menu_class'      => 'container',
    'menu_id'         => 'menu-bas',
    'echo'            => true,
    'fallback_cb'     => 'wp_page_menu',
    'before'          => '',
    'after'           => '',
    'link_before'     => '',
    'link_after'      => '',
    'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
    'depth'           => 0,
    'walker'          => ''
    );
    
    wp_nav_menu( $bas ); ?>
</footer>

<?php wp_footer();  ?>
</body>
</html>