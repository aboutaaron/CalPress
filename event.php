<?php get_header() ?>

		<div id="content" class="grid_8 alpha">

            <?php the_post() ?>

			<div id="post-<?php the_ID() ?>" class="<?php sandbox_post_class() ?>">
				
								
				<?php calpress_leadart(620); ?>
				
				<h2 class="entry-title"><?php the_title() ?></h2>
				
				<div class="entry-shortmeta">
				    <?php edit_post_link( __( 'Edit', 'sandbox' ), "\n\t\t\t\t\t<span class=\"edit-link\">", "</span>" ) ?>
				    <span class="entry-tags"><?php the_tags('Tagged: ',' , ',''); ?></span>
				</div>
				
				<div class="entry-content">
				    <?php calpress_inlines(); ?>
                    <?php the_content(); ?>
				</div>
				<div class="entry-tags"><?php the_tags('Tagged: ',' , ',''); ?></div>
				
			</div><!-- .post -->
            
            <hr />
            
            <div class="grid_6 alpha">
                <?php comments_template() ?>
            </div>

             <div class="grid_2 omega">
                 <div class="single-events">
                     <h3>More Events</h3>
                     <?php ec3_get_events('6', '<a href="%LINK%">%TITLE%</a>',''); ?>
                     <p class="right"><a href="/events/">All Events</a></p>
                     <div class="clear"></div>
                 </div>
             </div>
             <div class="clear"></div>
            

		</div><!-- #content -->
        <?php get_sidebar() ?>
        
<?php get_footer() ?>