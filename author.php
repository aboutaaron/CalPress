<?php get_header() ?>

		<div id="content" class="grid_8 alpha">

<?php 
the_post();
// by default, author.php's $authordata is only populated for 
// users with a published story. This remedies that!
// http://codex.wordpress.org/Author_Templates
$authordata = get_userdata(intval($author));
?>

			
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
            
            <?php userphoto($authordata) ?>
            <div class="clear"></div>


        
            <?php 
            // get author comments
            $author_comments = calpress_author_comments_by_id($authordata->ID);
            if ($author_comments) {
                echo '<h2 class="page-element comments">Comments</h2>';
                echo "<ul>";
                foreach ($author_comments as $comment){
                    $commenturl = get_permalink( $comment->comment_post_ID ) . "#comment-$comment->comment_ID";
                    echo '<li><a href="' . $commenturl . '" rel="bookmark" title="Permanent Link to ' . $comment->post_title . '">'.$comment->comment_content.'</a></li>';
                }
                echo "</ul>";
            } 	
            ?>
            
            <?php 
            rewind_posts();
            if ( have_posts() ){
                echo '<h2 class="page-element stories">Stories</h2>';
                while ( have_posts() ) { 
                    the_post();
                    // show post with art, sized at 300px 
                    calpress_loop_content(true, 300, true, true, true, true, 100);
                }
            ?>
                <div id="nav-below" class="navigation">
    				<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&laquo;</span> Older posts', 'sandbox' )) ?></div>
    				<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?></div>
    			</div>
            <?php    
            } 
            ?>



			

		</div><!-- #content -->
        <?php get_sidebar() ?>
        
<?php get_footer() ?>