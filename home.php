<?php get_header() ?>

		<div id="content" class="grid_8 alpha">

            <?php calpress_hook_frontfeatures_above(); ?>

            <?php require( $front_template ); ?>
            
            <?php calpress_hook_frontfeatures_below(); ?>
            
		</div><!-- #content -->
        <?php get_sidebar() ?>
        
<?php get_footer() ?>