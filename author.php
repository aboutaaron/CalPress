<?php 
            get_header();
            
            // get page vars
            the_post();
            
            // by default, author.php's $authordata is only populated for 
            // users with a published story. This remedies that!
            // http://codex.wordpress.org/Author_Templates
            $authordata = get_userdata(intval($author));

            // we probably have two main types of accounts: authors/staff or commentors
            // we will treat them a little differently
            $is_author = false;
            $class_meta = "community-profile";
            if ($authordata->wp_user_level >= 7) {
                $is_author = true;
                $class_meta = "author-profile";
            }
            
            // get author comments
            $author_comments = calpress_author_comments_by_id($authordata->ID);
            
            // TODO: add profile type to body class via calpress hook
            add_action('calpress_hook_bodyclass', 'add_profile_type_body_class');
            function add_profile_type_body_class(){
                echo $class_meta;
            }


            echo '<div id="content" class="grid_8 alpha ' . $class_meta .'">';
            //print_r($authordata);


            if ($is_author) {
                echo '<h2 class="page-title"><span class="author-name">'.$authordata->display_name.'</span></h2>';
                if (!empty($authordata->title)) {
                    echo '<h3 class="sub-hed title">' . $authordata->title . '</h3>';
                }
            } else {
                echo '<h2 class="page-title">Profile: <span class="author-name">'.$authordata->nickname.'</span></h2>';
            }
            
            // This page is essentially two seperate pages seperated at this conditional: an author page and a non-author page
            
            // Author Page    
            if ($is_author) {
             
                // Author pages may be "paged" with old stories. Only show bios on the first page.
                if (!is_paged()) {
                    //photo
                    if ( userphoto_exists($authordata) ) {
                        $profile_pic = calpress_sizedimageurl(WP_CONTENT_URL . '/uploads/userphoto/'.$authordata->userphoto_image_file, 220);
                        echo '<div id="author-photo">';
                            echo '<img class="photo" src="' . $profile_pic .'" alt="Profile photo: ' . $authordata->display_name .'" />';
                        echo '</div>';
                    }

                    echo '<div id="author-meta">'; 
                        //bio
                        $authordesc = $authordata->user_description; 
                        if ( !empty($authordesc) ){
                            echo '<div id="author-bio">';
                            echo "<p>$authordesc</p>";
                            echo '</div>';
                        }

                        echo '<h3 class="contact">Contact ' . $authordata->first_name . '</h3>';

                        echo '<div class="person vcard">';
                            // email
                            if (!empty($authordata->user_email)) {
                                printf ('<p class="author-profile aux email">Email: <a href="mailto:%s">%s</a></p>', $authordata->user_email, $authordata->user_email);
                            }

                            // twitter
                            if (!empty($authordata->twitter)) {
                                printf ('<p class="author-profile aux twitter">Twitter: <a href="http://twitter.com/%s">@%s</a></p>', $authordata->twitter, $authordata->twitter);
                            }

                            // website
                            if (!empty($authordata->user_url)) {
                                printf ('<p class="author-profile aux website">Website: <a href="%s">%s</a></p>', $authordata->user_url, $authordata->user_url);
                            }
                        echo '</div><!-- .person -->';

                        echo '<div class="company vcard">';
                            // organization
                            if (!empty($authordata->org)) {
                                printf ('<p class="author-profile aux organization">%s</p>', $authordata->org);
                            }

                            // Address
                            if (!empty($authordata->address_1)) {
                                printf ('<p class="author-profile aux address adddress_1">%s</p>', $authordata->address_1);
                                if (!empty($authordata->address_2)) {
                                    printf ('<p class="author-profile aux address adddress_2">%s</p>', $authordata->address_2);
                                }
                            }

                            // City / State / Zip
                            if (!empty($authordata->address_city)) {
                                printf ('<p class="author-profile aux address city-state-zip">%s %s, %s</p>', $authordata->address_city, $authordata->address_state, $authordata->address_zip);
                            }
                        echo '</div><div class="clear"></div><!-- .company -->';
                    echo '</div><!-- #author-meta -->';      
                    
                    echo '<div id="contributed-content">';
                    calpress_hook_authorpage_precontributedcontent();
                        // comments
                        if ($author_comments) {
                            
                            echo '<div id="author-comments">';
                            echo '<h3 class="page-element comments">Recent Comments</h3><ul>';
                            foreach ($author_comments as $comment){
                                $posturl = get_permalink( $comment->comment_post_ID );
                                $commenturl = $posturl . "#comment-$comment->comment_ID";
                                $commenttime =  date( 'F j, Y \a\t g:i a', strtotime($comment->comment_date) );
                                echo '<li><p class="comment-post">Posted on: <em><a href="' . $posturl .'" title="Permanent Link to ' . $comment->post_title . '">' . $comment->post_title . '</a></em></p>';
                                echo '<p class="comment-content">'.$comment->comment_content.' <a href="' . $commenturl . '" rel="bookmark" title="Permanent Link to ' . $comment->post_title . '">#</a></p>';
                                echo '<p class="comment-time">' . $commenttime . '</p></li>';
                            }
                            echo "</ul></div><!-- #author-comments -->";
                        }
                    
                    
                }// if !is_paged() for show author bio
                
                
                // articles
                rewind_posts();
                if ( have_posts() ){
                    echo '<div id="author-stories">';
                    echo '<h3 class="page-element stories">Stories</h3>';
                    while ( have_posts() ) { 
                        the_post();
                        // show post with art, sized at 300px 
                        calpress_loop_content(false, 300, true, true, true, true, 15);
                    }
                    ?>
                    <div id="nav-below" class="navigation">
        				<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&laquo;</span> Older posts', 'sandbox' )) ?></div>
        				<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?></div>
        			</div>
                <?php    
                    echo "</div><!-- #author-stories -->";  
                                        
                if (get_the_author_meta('twitter')){
                    echo '<div id="author-twitter">';
                    echo '<h3 class="page-element twitter">Twitter</h3>';
                        calpress_twitterprofile(get_the_author_meta('twitter'));
                    echo "</div><!-- #author-twitter -->";  
                }   
                
                
                // if grunion contact form plugin is enabled, show a contact form for the user
                if (function_exists('contact_form_init') && !empty($authordata->user_email)) {
                    $contactformmarkup = '[contact-form to="' . $authordata->user_email . '"]';
                    echo '<div id="author-contact">';
                        echo '<h3 class="page-element contact">Contact</h3>';
                        echo do_shortcode($contactformmarkup);
                    echo "</div><!-- #author-contact -->";
                } 
                
                    //close out contributed content only if not a paged view
                    if (!is_paged()) {
                        echo '</div><!-- #contributed-content -->';   
                    }
                }
                
            // end author page    
            } else {
            // A non-author page
                
                //photo
                if ( userphoto_exists($authordata) ) {
                    $profile_pic = calpress_sizedimageurl(WP_CONTENT_URL . '/uploads/userphoto/'.$authordata->userphoto_image_file, 380);
                    echo '<div id="author-photo">';
                        echo '<img class="photo" src="' . $profile_pic .'" alt="Profile photo: ' . $authordata->display_name .'" />';
                    echo '</div>';
                }
                
                echo '<div id="author-meta">'; 
                
                echo '<p class="author-profile aux">Real name: '. $authordata->first_name . ' '. $authordata->last_name . '</p>';
                printf ('<p class="author-profile aux">Joined: %s</p>', date( 'F j, Y', strtotime($authordata->user_registered)) );
                
                // twitter
                if (!empty($authordata->twitter)) {
                    printf ('<p class="author-profile aux twitter">Twitter: <a href="http://twitter.com/%s">@%s</a></p>', $authordata->twitter, $authordata->twitter);
                }

                // website
                if (!empty($authordata->user_url)) {
                    printf ('<p class="author-profile aux website">Website: <a href="%s">%s</a></p>', $authordata->user_url, $authordata->user_url);
                }
                
                // participation
                printf ('<p class="author-profile aux participation participation-count-%s">Participation: %s comment%s, %s stor%s</p>', count($author_comments), count($author_comments), pluralize(count($author_comments)), count($posts), pluralize( count($posts), 'ies', 'y') );
                
                //bio
                $authordesc = $authordata->user_description; 
                if ( !empty($authordesc) ){
                    echo '<div id="author-bio">';
                    echo "<p>$authordesc</p>";
                    echo '</div>';
                }
                
                echo '</div><!-- #author-meta -->';
                
                if ($author_comments) {
                    echo '<h3 class="page-element comments">Comments</h3>';
                    echo '<div class="comments"><ul>';
                    foreach ($author_comments as $comment){
                        $posturl = get_permalink( $comment->comment_post_ID );
                        $commenturl = $posturl . "#comment-$comment->comment_ID";
                        $commenttime =  date( 'F j, Y \a\t g:i a', strtotime($comment->comment_date) );
                        echo '<li><p class="comment-post">Posted on: <em><a href="' . $posturl .'" title="Permanent Link to ' . $comment->post_title . '">' . $comment->post_title . '</a></em></p>';
                        echo '<p class="comment-content">'.$comment->comment_content.' <a href="' . $commenturl . '" rel="bookmark" title="Permanent Link to ' . $comment->post_title . '">#</a></p>';
                        echo '<p class="comment-time">' . $commenttime . '</p></li>';
                    }
                    echo "</ul></div><!-- .comments -->";
                }
            }
             

            ?>
            
            <?php if ($is_author): ?>
                
            <?php endif ?>
			

		</div><!-- #content -->
        <?php get_sidebar() ?>
        
<?php get_footer() ?>