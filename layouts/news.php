<?php $storyCounter = 0 ?>
<?php while ( have_posts() ) : the_post() ?>

    <?php if ($storyCounter === 0): // lead story ?>
        <div id="lead-story">
            <div id="fresh"><p>Fresh Content</p></div>

            <?php // show post with art, sized at 290px ?>
            <?php calpress_loop_content(true, 290); ?>

        </div><!-- #lead story -->
    
    <?php elseif ($storyCounter === 1 ): // story 2 ?>
        <div id="secondary-block">
            <h3>News</h3>
            <div id="secondary-stories">
                <?php // show post with art, sized at 290px ?>
                <?php calpress_loop_content(true, 290); ?>
                
    <?php elseif ($storyCounter === 2): //story 3 ?>
            
                <?php // show post with art, sized at 290px ?>
                <?php calpress_loop_content(true, 290); ?>
            
            </div><!-- #secondary-stories -->
            <div id="tertiary-stories">
                
    <?php elseif ($storyCounter > 2): ?>
        <?php //$art=true, $artsize=620, $arttease=false, $hed=true, $meta=true, $excerpt=true, $excerptlength = 0 ?>
            <?php calpress_loop_content(false, 0, true,true,true,false, 0); ?>
            
                    
    <?php endif; ?>
    
<?php $storyCounter++ ?>
<?php endwhile; ?>
    	<p class="more"><a href="#">More</a></p>
    </div><!-- #tertiary-stories -->
</div><!-- #secondary-block -->