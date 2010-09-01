<?php get_header() ?>

		<div id="content" class="grid_8 alpha">
		    
			<h2 class="page-title"><span><?php single_cat_title() ?></span></h2>
			<?php $categorydesc = category_description(); if ( !empty($categorydesc) ) echo apply_filters( 'archive_meta', '<div class="archive-meta">' . $categorydesc . '</div>' ); ?>


			<div id="nav-above" class="navigation">
				<div class="nav-previous"><?php next_posts_link(__( 'Older posts <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?></div>
            	<div class="nav-next"><?php previous_posts_link(__( '<span class="meta-nav">&laquo;</span>Newer posts', 'sandbox' )) ?></div>
			</div>

<?php $storyCounter = 0; while ( have_posts() ) : the_post() ?>

            <?php calpress_loop_content(true, 140, 96, false, true, true, true, 25); ?>

<?php $storyCounter++; endwhile; ?>

            <div id="nav-below" class="navigation">
            	<div class="nav-previous"><?php next_posts_link(__( 'Older posts <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?></div>
            	<div class="nav-next"><?php previous_posts_link(__( '<span class="meta-nav">&laquo;</span>Newer posts', 'sandbox' )) ?></div>
            </div>

		</div><!-- #content -->
        <?php get_sidebar() ?>
        
<?php get_footer() ?>