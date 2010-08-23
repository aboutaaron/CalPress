<?php get_header() ?>

		<div id="content" class="grid_8 alpha">

            <?php // hook above loop ?>
            <?php calpress_hook_frontfeatures_above(); ?>

            <?php
            // determine which category to show on front page. defaults to all. 
            $category_to_use = get_option('cp_front_category');
            if (is_numeric($category_to_use) && $category_to_use > 0) {
                query_posts('cat='.$category_to_use);
            }
            ?>

            <?php // include the blog.php layout, or any optional layout selected in CalPress Producer ?>
            <?php require( $front_template ); ?>
            
            <?php //hook below loop ?>
            <?php calpress_hook_frontfeatures_below(); ?>
            
		</div><!-- #content -->
        <?php get_sidebar() ?>
        
<?php get_footer() ?>