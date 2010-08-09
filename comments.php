<?php
    // this is drop-in replacement for Sandbox's comments.php. This supports comment threading and pagination. 
    // http://alicebob.cryptoland.net/sandbox-wordpress-theme-for-27-with-threaded-comments/
?>
<?php
	if ( 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']) )
		die ( 'Please do not load this page directly. Thanks.' );
?>
			<div id="comments">
<?php
	if ( post_password_required() ) :
?>
				<div class="nopassword"><?php _e( 'This post is protected. Enter the password to view any comments.', 'sandbox' ) ?></div>
			</div><!-- .comments -->
<?php
		return;
	endif;
?>
<?php if ( have_comments() ) : ?>
				<div id="comments-list" class="comments">
					<h3><?php comments_number('', __('<span>One</span> Comment', 'sandbox'), __('<span>%</span> Comments', 'sandbox') ); ?></h3>
					<div id="comments-nav-above" class="comments-navigation">
<?php paginate_comments_links($args); ?>
					</div>
					<ol class="commentlist">
<?php //wp_list_comments(); ?>
<?php 
    // get threaded comments.
    wp_list_comments('type=comment&callback=calpress_custom_comments'); 
?>
					</ol>
					<div id="comments-nav-below" class="comments-navigation">
<?php paginate_comments_links($args); ?>
					</div>
				</div><!-- #comments-list .comments -->
<?php endif // REFERENCE: if ( have_comments() ) ?>
<?php if ( comments_open() ) : ?>
<?php $req = get_option('require_name_email'); // Checks if fields are required. Thanks, Adam. ;-) ?>

				<div id="respond">
					<h3><?php comment_form_title( __( 'Post a Comment', 'sandbox' ), __( 'Post a Reply to %s', 'sandbox' ) ); ?></h3>

					<div id="cancel-comment-reply"><?php cancel_comment_reply_link() ?></div>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
					<p id="login-req"><?php printf(__('You must be <a href="%s" title="Log in">logged in</a> to post a comment.', 'sandbox'),
					wp_logout_url(get_permalink()) ) ?></p>

<?php else : ?>
					<div class="formcontainer">	
						<form id="commentform" action="<?php bloginfo('wpurl') ?>/wp-comments-post.php" method="post">

<?php if ( $user_ID ) : ?>
							<p id="login"><?php printf( __( '<span class="loggedin">Logged in as <a href="%1$s" title="Logged in as %2$s">%2$s</a>.</span> <span class="logout"><a href="%3$s" title="Log out of this account">Log out?</a></span>', 'sandbox' ),
								get_bloginfo('wpurl') . '/wp-admin/profile.php',
								wp_specialchars( $user_identity, 1 ),
								get_bloginfo('wpurl') . '/wp-login.php?action=logout&amp;redirect_to=' . get_permalink() ) ?></p>

<?php else : ?>

                            <?php calpress_hook_comment_message(); ?>

							

							<div class="form-label"><label for="author"><?php _e( 'Name', 'sandbox' ) ?></label> <?php if ($req) _e( '<span class="required">*</span>', 'sandbox' ) ?></div>
							<div class="form-input"><input id="author" name="author" class="text<?php if ($req) echo ' required'; ?>" type="text" value="<?php echo $comment_author ?>" size="30" maxlength="50" tabindex="3" /></div>

							<div class="form-label"><label for="email"><?php _e( 'Email', 'sandbox' ) ?></label> <?php if ($req) _e( '<span class="required">*</span>', 'sandbox' ) ?></div>
							<div class="form-input"><input id="email" name="email" class="text<?php if ($req) echo ' required'; ?>" type="text" value="<?php echo $comment_author_email ?>" size="30" maxlength="50" tabindex="4" /></div>

							<div class="form-label"><label for="url"><?php _e( 'Website', 'sandbox' ) ?></label></div>
							<div class="form-input"><input id="url" name="url" class="text" type="text" value="<?php echo $comment_author_url ?>" size="30" maxlength="50" tabindex="5" /></div>

<?php endif // REFERENCE: * if ( $user_ID ) ?>

							<div class="form-label"><label for="comment"><?php _e( 'Comment', 'sandbox' ) ?></label></div>
							<div class="form-textarea"><textarea id="comment" name="comment" class="text required" cols="45" rows="8" tabindex="6"></textarea></div>

							<div class="form-submit"><input id="submit" name="submit" class="button" type="submit" value="<?php _e( 'Post Comment', 'sandbox' ) ?>" tabindex="7" />
							<?php comment_id_fields(); ?>
							</div>

							<div class="form-option"><?php do_action( 'comment_form', $post->ID ) ?></div>

						</form><!-- #commentform -->
					</div><!-- .formcontainer -->
<?php endif // REFERENCE: if ( get_option('comment_registration') && !$user_ID ) ?>

				</div><!-- #respond -->
<?php endif // REFERENCE: if ( 'open' == $post->comment_status ) ?>

			</div><!-- #comments -->
