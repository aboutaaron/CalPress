<?php get_header() ?>

		<div id="content" class="grid_8 alpha">

			<h2 class="page-title"><?php _e( 'Tag Archives:', 'sandbox' ) ?> <span><?php single_tag_title() ?></span></h2>

			<div id="nav-above" class="navigation">
				<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&laquo;</span> Older posts', 'sandbox' ) ) ?></div>
				<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&raquo;</span>', 'sandbox' ) ) ?></div>
			</div>


            <div id="tag-list" class="rail-3col-right">
                <div class="rail-element">
                    <h3>Topics Cloud</h3>
                <?php wp_tag_cloud('smallest=6&largest=26&number=100&order=rand'); ?>
                </div>
            </div>
            

            <?php $storyCounter = 0; while ( have_posts() ) : the_post() ?>

                        <?php calpress_loop_content(true, 380, 0, false, true, true, true, 25); ?>

            <?php $storyCounter++; endwhile; ?>

            <div id="nav-below" class="navigation">
            	<div class="nav-previous"><?php next_posts_link(__( 'Older posts <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?></div>
            	<div class="nav-next"><?php previous_posts_link(__( '<span class="meta-nav">&laquo;</span>Newer posts', 'sandbox' )) ?></div>
            </div>

		</div><!-- #content -->
        <?php get_sidebar() ?>
        
<?php get_footer() ?>