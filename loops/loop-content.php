<?php
/**
 * calpress_loop_content()
 *
 * The content of most generic loops can be built from these options. This call needs to be wrapped in a loop have_posts.
 * @since 0.7
 * @param boolean $art - show lead story art if available
 * @param boolean $arttease - show full fledge multimedia options (video, soundslides, youtube, etc). If false, just an image will appear
 * @param int $artsize - width of art. The default is 620px, which is full column lead story size
 * @param boolean $hed - show headline
 * @param boolean $meta - show story meta
 * @param boolean $excerpt - show story excerpt
 * @param int $excerptlength - for any number greater than zero, truncate excerpt to that length
 * @return string
 */

function calpress_loop_content($art=true, $artsize=620, $multimedia=true, $hed=true, $meta=true, $excerpt=true, $excerptlength = 0){
    global $post, $authordata;
?>  
  
  <div id="post-<?php the_ID() ?>" class="<?php calpress_post_class() ?>">

  	<?php 
  	if ($art) { // attempt to show art

        if (!$multimedia) { // show tease art only (just images, not videos, etc)
            if (calpress_showteaseart()) { // only show tease if one is availble to show. otherwise, show nothing. 
                echo('<div class="entry-image">');
                    calpress_teaseart($artsize);
                echo("</div>");
            }
        } else { // show fully functional lead art (videos, etc)
            echo('<div class="entry-image">');
                calpress_leadart($artsize);
            echo("</div>");
        }
    }
  	?>

  	<h2 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php printf( __('Permalink to %s', 'sandbox'), the_title_attribute('echo=0') ) ?>" rel="bookmark"><?php the_title() ?></a></h2>

  	<div class="entry-meta">
  	    <span class="author vcard"><?php printf( __( 'By %s', 'sandbox' ), '<a class="url fn n" href="' . get_author_link( false, $authordata->ID, $authordata->user_nicename ) . '" title="' . sprintf( __( 'View all posts by %s', 'sandbox' ), $authordata->display_name ) . '">' . get_the_author() . '</a>' ) ?></span>
  		<span class="meta-sep">|</span>
  		<span class="entry-date">
  		    <?php
  		        if (get_the_modified_time() != get_the_time()){
  		            ?>
  		            <abbr class="entry-updated" title="<?php get_the_modified_time('Y-m-d\TH:i:sO') ?>"><?php unset($previousday); printf( __( 'Updated: %1$s &#8211; %2$s', 'sandbox' ), the_date( '', '', '', false ), get_the_modified_time() ) ?></abbr>
  		            <?php
  		        }else{ ?>
  		            <abbr class="published" title="<?php the_time('Y-m-d\TH:i:sO') ?>"><?php unset($previousday); printf( __( '%1$s &#8211; %2$s', 'sandbox' ), the_modified_time('F j, Y'), get_the_time() ) ?></abbr>
  		        <?php } ?>
  		</span>
  	</div>
  	<?php if ($excerpt): ?>
  	    <div class="entry-content">
      	    
      	    <?php if ($excerptlength > 0): ?>
      	        <?php calpress_trim_excerpt($excerptlength); ?>
      	    <?php else : ?>
                <?php the_excerpt(); ?>
            <?php endif; ?>    

              <?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'sandbox' ) . '&after=</div>') ?>
      	</div>
  	<?php endif ?>
  	
  </div><!-- .post -->
  
<?php   
}
?>