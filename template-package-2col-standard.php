<?php
/*
Template Name: 2 Column Standard Package
*/
remove_filter('the_content', 'wpautop');
?>
<?php get_header() ?>

		<div id="content" class="grid_8 alpha package">
		    

            <h2 class="page-title"><?php the_title() ?></h2>
            <?php the_content() ?>


		</div><!-- #content -->
        <?php get_sidebar() ?>
        
<?php get_footer() ?>