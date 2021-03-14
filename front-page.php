<?php get_header();?>
</div>  <!--pour fermer le container -->
<?php
// printr('$_server',$_SERVER);
// printL("phrase",$_SESSION['phrase']);
?>
<section id="from-accueil" class="accueil container">
    <?php while ( have_posts() ) : 
        the_post();
        get_template_part( 'template-parts/content', get_post_type() );
    endwhile;?> <!-- End of the loop. -->
</section><!-- #from-accueil : ajout depuis wordPress -->




<script src="<?php echo get_stylesheet_directory_uri().'/js/monScript.js' ?>"></script>

<?php get_footer(); ?>