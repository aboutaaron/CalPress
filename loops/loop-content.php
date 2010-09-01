<?php
/**
 * calpress_loop_content()
 *
 * The content of most generic loops can be built from these options. This call needs to be wrapped in a loop have_posts.
 * @since 0.7
 * @param boolean $art - show lead story art if available
 * @param boolean $arttease - show full fledge multimedia options (video, soundslides, youtube, etc). If false, just an image will appear
 * @param int $artsize - width of art. The default is 620px, which is full column lead story size
 * @param int $artcrop - If > 0, crop the photo to this height
 * @param boolean $hed - show headline
 * @param boolean $meta - show story meta
 * @param boolean $excerpt - show story excerpt
 * @param int $excerptlength - for any number greater than zero, truncate excerpt to that length
 * @return string
 */

function calpress_loop_content($art=true, $artsize=620, $artcrop=0, $multimedia=true, $hed=true, $meta=true, $excerpt=true, $excerptlength = 0){
    global $post, $authordata;
?>  
  
  <div id="post-<?php the_ID() ?>" class="<?php calpress_post_class() ?>">
      <?php calpress_hook_loopcontent_above(); ?>

  	<?php 
  	if ($art) { // attempt to show art

        if (!$multimedia) { // show tease art only (just images, not videos, etc)
            if (calpress_showteaseart()) { // only show tease if one is availble to show. otherwise, show nothing. 
                echo('<div class="entry-image">');
                    //cropped version?
                    if ($artcrop > 0) {
                        calpress_teaseart_cropped($artsize,$artcrop);
                    } else{
                        calpress_teaseart($artsize);
                    }
                    
                echo("</div>");
            }
        } else { // show fully functional lead art (videos, etc)
            echo('<div class="entry-image">');
                
                // if we need to show a cropped lead art ($artcrop > 0 && $multimedia==true), we need to determine if the art is croppable. For now, only photos are supported.
                // using output buffering and a class string search is a horribly fragile hack...
                
                //grab default lead art
                ob_start();
            	calpress_leadart($artsize);
            	$leadart = ob_get_contents();
                ob_end_clean();
                
                // see if calpress is planning on printing a photo (as opposed to entry-leadvideo, entry-leadyoube, etc)
                if ( strpos($leadart, "entry-leadphoto") !== false && $artcrop > 0) {
                    calpress_teaseart_cropped($artsize,$artcrop); //show cropped photo
                } else {
                    echo $leadart; // show unchanged lead art (video, youtube, etc)
                }

            echo("</div>");
        }
    }
  	?>

  	<h2 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php printf( __('Permalink to %s', 'sandbox'), the_title_attribute('echo=0') ) ?>" rel="bookmark"><?php the_title() ?></a></h2>

    <?php if ($meta): ?>

        <div class="entry-meta">
      	    <span class="author vcard"><?php printf( __( 'By %s', 'sandbox' ), '<a class="url fn n" href="' . get_author_link( false, $authordata->ID, $authordata->user_nicename ) . '" title="' . sprintf( __( 'View all posts by %s', 'sandbox' ), $authordata->display_name ) . '">' . get_the_author() . '</a>' ) ?></span>
      		<span class="meta-sep">|</span>
      		<span class="entry-date">
      		    <?php
      		        /*
      		        if (get_the_modified_time() != get_the_time()){
      		            ?>
      		            <abbr class="entry-updated" title="<?php get_the_modified_time('Y-m-d\TH:i:sO') ?>"><?php unset($previousday); printf( __( 'Updated: %1$s &#8211; %2$s', 'sandbox' ), the_date( '', '', '', false ), get_the_modified_time() ) ?></abbr>
      		            <?php
      		        }else{ ?>
      		            <abbr class="published" title="<?php the_time('Y-m-d\TH:i:sO') ?>"><?php unset($previousday); printf( __( '%1$s &#8211; %2$s', 'sandbox' ), the_modified_time('F j, Y'), get_the_time() ) ?></abbr>
      		        <?php 
      		        }*/
      		        ?>
      		        <abbr class="published" title="<?php the_time('Y-m-d\TH:i:sO') ?>"><?php unset($previousday); printf( __( '%1$s &#8211; %2$s', 'sandbox' ), the_modified_time('F j, Y'), get_the_time() ) ?></abbr>
      		</span>
      	</div>
        
    <?php endif ?>


  	<?php if ($excerpt): ?>
  	    <div class="entry-content">
      	    <p>
      	    <?php if ($excerptlength > 0): ?>
      	        <?php calpress_trim_excerpt($excerptlength); ?>
      	    <?php else : ?>
                <?php the_excerpt(); ?>
            <?php endif; ?>    
            </p>
              <?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'sandbox' ) . '&after=</div>') ?>
      	</div>
  	<?php endif ?>
  	<?php calpress_hook_loopcontent_below(); ?>
  </div><!-- .post -->
  
<?php   
}
?>