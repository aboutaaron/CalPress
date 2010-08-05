<?php get_header() ?>

		<div id="content" class="grid_8 alpha">

<?php the_post() ?>

			
            <h2 class="page-title author"><?php printf( __( '<span class="author-name">%s</span>', 'sandbox' ), "$authordata->display_name" ); ?></h2>

            <div class="rail-4col-right">
                <div class="rail-element">
                    <h3 class="contact">Contact</h3>
                    <ul class="contact">
                        <li class="email">Email: <?php the_author_meta('email'); ?></li>
                        <li class="website">Website: <a href="<?php the_author_meta('user_url'); ?>"><?php the_author_meta('user_url'); ?></a></li>
                        <li class="twitter">Twitter: <a href="http://twitter.com/<?php the_author_meta('twitter'); ?>/"><?php the_author_meta('twitter'); ?></a></li>
                    </ul>   
                </div>
            </div>
            
                <?php 
                    //if there is an author description, print it
                    $authordesc = $authordata->user_description; 
                    if ( !empty($authordesc) ){
                    ?>
                        <div id="author-info">
                            <div class="element">
                                <p class="bio"> <?php echo($authordesc); ?> </p> 
                            </div>
                            
                            
                        </div>
                <?php
                    }
                ?> 
            

            <div class="clear"></div>

<?php rewind_posts() ?>

            <h2 class="page-element stories">Stories</h2>
            
            <?php while ( have_posts() ) : the_post() ?>

			<div id="post-<?php the_ID() ?>" class="<?php calpress_post_class() ?>">
				<?php 
				    if (calpress_showteaseart() != false) {
				        echo('<div class="entry-image">');
    				        calpress_teaseart(140);
    				    echo("</div>");
				    }
				?>
				<h4 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php printf( __( 'Permalink to %s', 'sandbox' ), the_title_attribute('echo=0') ) ?>" rel="bookmark"><?php the_title() ?></a></h4>
				<div class="entry-date"><abbr class="published" title="<?php the_time('Y-m-d\TH:i:sO') ?>"><?php unset($previousday); printf( __( '%1$s &#8211; %2$s', 'sandbox' ), the_date( '', '', '', false ), get_the_time() ) ?></abbr></div>
				<div class="entry-content">
<?php the_excerpt(__( 'Read More <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?>

				</div>
				<div class="entry-meta">
					<span class="cat-links"><?php printf( __( 'Posted in %s', 'sandbox' ), get_the_category_list(', ') ) ?></span>
					<span class="meta-sep">|</span>
					<?php the_tags( __( '<span class="tag-links">Tagged ', 'sandbox' ), ", ", "</span>\n\t\t\t\t\t<span class=\"meta-sep\">|</span>\n" ) ?>
<?php edit_post_link(__('Edit', 'sandbox'), "\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t<span class=\"meta-sep\">|</span>\n"); ?>
					<span class="comments-link"><?php comments_popup_link( __( 'Comments (0)', 'sandbox' ), __( 'Comments (1)', 'sandbox' ), __( 'Comments (%)', 'sandbox' ) ) ?></span>
				</div>
			</div><!-- .post -->

<?php endwhile; ?>

			<div id="nav-below" class="navigation">
				<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&laquo;</span> Older posts', 'sandbox' )) ?></div>
				<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?></div>
			</div>

		</div><!-- #content -->
        <?php get_sidebar() ?>
        
<?php get_footer() ?>