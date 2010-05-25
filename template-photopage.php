<?php
/*
Template Name: Photo Category Page
*/
remove_filter('the_content', 'wpautop');
?>
<?php get_header() ?>

		<div id="content" class="grid_8 alpha">
		    
            <?php the_content() ?>


            <?php
                   //get custom category from page
                   $cats = calpress_customcategories();
                   $query = 'cat='.$cats;
                   //pull from those category/ies
                   $catPosts = new WP_Query();
                   $catPosts->query($query);
                   $storyCounter = 0;
            ?>

                <?php while ($catPosts->have_posts()) : $catPosts->the_post(); ?>
                    <div id="post-<?php the_ID() ?>" class="<?php sandbox_post_class() ?>">
                        <?php 
        				    if (calpress_showleadart() != false) {
        				        echo('<div class="entry-image">');
            				    calpress_leadart(620);
            				    echo("</div>");
        				    }
        				?>
        				<h3 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php printf( __( 'Permalink to %s', 'sandbox' ), the_title_attribute('echo=0') ) ?>" rel="bookmark"><?php the_title() ?></a></h3>
        				<div class="entry-date"><abbr class="published" title="<?php the_time('Y-m-d\TH:i:sO') ?>"><?php unset($previousday); printf( __( '%1$s &#8211; %2$s', 'sandbox' ), the_date( '', '', '', false ), get_the_time() ) ?></abbr></div>
        				<div class="entry-author"><?php printf( __( 'By %s', 'sandbox' ), '<a class="url fn n" href="' . get_author_link( false, $authordata->ID, $authordata->user_nicename ) . '" title="' . sprintf( __( 'View all posts by %s', 'sandbox' ), $authordata->display_name ) . '">' . get_the_author() . '</a>' ) ?></div>
        				<div class="entry-content">
        <?php the_excerpt(__( 'Read More <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?>

        				</div>
        				<div class="entry-meta">
        					<span class="author vcard"><?php printf( __( 'By %s', 'sandbox' ), '<a class="url fn n" href="' . get_author_link( false, $authordata->ID, $authordata->user_nicename ) . '" title="' . sprintf( __( 'View all posts by %s', 'sandbox' ), $authordata->display_name ) . '">' . get_the_author() . '</a>' ) ?></span>
        					<span class="meta-sep">|</span>
        <?php if ( $cats_meow = sandbox_cats_meow(', ') ) : // Returns categories other than the one queried ?>
        					<span class="cat-links"><?php printf( __( 'Also posted in %s', 'sandbox' ), $cats_meow ) ?></span>
        					<span class="meta-sep">|</span>
        <?php endif ?>
        					<?php the_tags( __( '<span class="tag-links">Tagged: ', 'sandbox' ), ", ", "</span>\n\t\t\t\t\t<span class=\"meta-sep\">|</span>\n" ) ?>
        <?php edit_post_link( __( 'Edit', 'sandbox' ), "\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t<span class=\"meta-sep\">|</span>\n" ) ?>
        					<span class="comments-link"><?php comments_popup_link( __( 'Comments (0)', 'sandbox' ), __( 'Comments (1)', 'sandbox' ), __( 'Comments (%)', 'sandbox' ) ) ?></span>
        				</div><div class="clearleft"></div>
        			</div><!-- .post -->
                    

                <?php $storyCounter++; endwhile; ?>

			<div id="nav-below" class="navigation">
				<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&laquo;</span> Older posts', 'sandbox' ) ) ?></div>
				<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&raquo;</span>', 'sandbox' ) ) ?></div>
			</div>

		</div><!-- #content -->
        <?php get_sidebar() ?>
        
<?php get_footer() ?>