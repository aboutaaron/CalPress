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