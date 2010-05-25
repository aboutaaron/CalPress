<?php
/*
Template Name: Full Page Template - Does Not Remove Auto Paragraph
*/
remove_filter('the_content', 'wpautop');
?>
<?php get_header() ?>

		<div id="content" class="grid_12 alpha omega">
			<div id="wide-content">
			    <h2 class="page-title"><?php the_title() ?></h2>
			    <?php the_content() ?>
			    <?php if ( get_post_custom_values('comments') ) comments_template() // Add a key/value of "comments" to enable comments on pages! ?>
			</div><!-- #wide-content -->
		</div><!-- #content -->
        
<?php get_footer() ?>