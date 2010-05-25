<?php
/*
Template Name: Author List
*/
?>
<?php get_header() ?>

		<div id="content" class="grid_8 alpha">

            <?php wp_list_authors('show_fullname=1&optioncount=1'); ?> 

		</div><!-- #content -->
        <?php get_sidebar() ?>
        
<?php get_footer() ?>