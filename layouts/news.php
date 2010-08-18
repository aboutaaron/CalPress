<?php
// Default news layout that ships with CalPress. This layout will automatically appear in all child themes. 
// For use in CalPress or child themes, simply select "News" in the layout selector in the CalPress Producer. 

// This theme can be replaced with another "news" layout by placing a custom "news.php" file in the 
// child theme's /layout/ folder. Or, even better, you can suppliment this theme another layout using a 
// a different file name (eg: /layouts/breaking-news.php). Then you can select from either news layout.

// Also, CalPress includes a custom body class selector to denote which layout you are currently using. 
// The selector takes the form: front-layout-yourfilenameminusthephp. For example, this file (/layout/news.php)
// adds a body selector like so: body class="front-layout-news". A custom child theme layout of /layout/breaking-news.php
// would do this: body class="front-layout-breaking-news"

// This file makes extensive use of the calpress_loop_content function. This is the default CalPress loop, and it lives in 
// /loops/loop-content.php. The function has the following signature and default values:
//
// calpress_loop_content($art=true, $artsize=620, $multimedia=true, $hed=true, $meta=true, $excerpt=true, $excerptlength = 0)
//
// And the functions documentation:
//
// @param boolean $art - show lead story art if available
// @param boolean $arttease - show full fledge multimedia options (video, soundslides, youtube, etc). If false, just an image will appear
// @param int $artsize - width of art. The default is 620px, which is full column lead story size
// @param boolean $hed - show headline
// @param boolean $meta - show story meta
// @param boolean $excerpt - show story excerpt
// @param int $excerptlength - for any number greater than zero, truncate excerpt to that length
//  @return string
?>
<?php $storyCounter = 0 ?>

<?php // Only show news design page on the very front page. Once we start paging, go to a basic view ?>
<?php if (!is_paged()): ?>
    <?php while ( have_posts() ) : the_post() ?>

        <?php if ($storyCounter === 0): // lead story ?>
            <div id="lead-story">
                <?php // show post with art, sized at 300px ?>
                <?php calpress_loop_content(true, 300, true, true, true, true, 70); ?>
                <p class="more"><a href="<?php the_permalink() ?>">More</a></p>
            </div><!-- #lead story -->
            <div class="clear"></div>
        <?php elseif ($storyCounter === 1 ): // story 2 ?>
            <div id="secondary-block">
                <h3>News</h3>
                <div id="secondary-stories">
                    <?php // show post with art, sized at 300px ?>
                    <?php calpress_loop_content(true, 300, true, true, true, true, 15); ?>

        <?php elseif ($storyCounter === 2): //story 3 ?>

                    <?php // show post with art, sized at 300px ?>
                    <?php calpress_loop_content(true, 300, true, true, true, true, 15); ?>

                </div><!-- #secondary-stories -->
                <div id="tertiary-stories">         
        <?php elseif ($storyCounter > 2): ?>
                <?php calpress_loop_content(false, 0, false,true,true,false, 0); ?>
        <?php endif; ?>

    <?php $storyCounter++ ?>
    <?php endwhile; ?>
        </div><!-- #tertiary-stories -->
            <div id="nav-below" class="navigation">
            	<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&laquo;</span> Older posts', 'sandbox' )) ?></div>
            	<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?></div>
            </div>
        </div><!-- #secondary-block -->

<?php else: ?>    
    
    <div id="nav-above" class="navigation">
    	<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&laquo;</span> Older posts', 'sandbox' )) ?></div>
    	<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?></div>
    </div>

    <?php while ( have_posts() ) : the_post() ?>
        <?php // show post with art, sized at 620px ?>
        <?php calpress_loop_content(); ?>

    <?php endwhile; ?>

    <div id="nav-below" class="navigation">
    	<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&laquo;</span> Older posts', 'sandbox' )) ?></div>
    	<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?></div>
    </div>
    
<?php endif ?>


    
    
