<?php
// Default blog layout that ships with CalPress. This layout will automatically appear in all child themes. 
// For use in CalPress or child themes, simply select "Blog" in the layout selector in the CalPress Producer. 

// This theme can be replaced with another "blog" layout by placing a custom "blog.php" file in the 
// child theme's /layout/ folder. Or, even better, you can suppliment this theme another layout using a 
// a different file name (eg: /layouts/art-top.php). Then you can select from either layout.

// Also, CalPress includes a custom body class selector to denote which layout you are currently using. 
// The selector takes the form: front-layout-yourfilenameminusthephp. For example, this file (/layout/blog.php)
// adds a body selector like so: body class="front-layout-blog". A custom child theme layout of /layout/breaking-news.php
// would do this: body class="front-layout-breaking-news"

// This file makes extensive use of the calpress_loop_content function. This is the default CalPress loop, and it lives in 
// /loops/loop-content.php. The function has the following signature and default values:
//
// calpress_loop_content($art=true, $artsize=620, $artcrop=0, $multimedia=true, $hed=true, $meta=true, $excerpt=true, $excerptlength = 0)
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
<div id="nav-above" class="navigation">
	<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&laquo;</span> Older posts', 'sandbox' )) ?></div>
	<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?></div>
</div>


<?php $storyCounter = 0 ?>
<?php while ( have_posts() ) : the_post() ?>
    
    <?php if ( $storyCounter === 0 && $leadstoryoverride): // lead story is overriden by calpress producer option ?>
        <div class="hentry p1 post publish category-front front-override">
          <?php  echo(stripslashes($leadstoryoverride_content)); ?>
        </div>
    <?php else: // not lead story?>
        <?php // show post with art, sized at 620px ?>
        <?php calpress_loop_content(); ?>
    <?php endif; ?>
    
<?php $storyCounter++ ?>
<?php endwhile; ?>

<div id="nav-below" class="navigation">
	<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&laquo;</span> Older posts', 'sandbox' )) ?></div>
	<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?></div>
</div>