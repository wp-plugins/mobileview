<?php

// Do not delete these lines
	if ( !empty( $_SERVER['SCRIPT_FILENAME'] ) && 'comments.php' == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
		die ('Please do not load this page directly. Thanks!');
	}
?>

<div id="comments">
<?php	if ( post_password_required() ) { return;  } ?>

<!-- You can start editing here. -->

<?php if ( have_comments() ) : ?>

	<h2 id="comments-title"><?php hipnews_mobile_com_toggle(); ?></h2>
		
	<?php if ( hipnews_wp_comments_nav_on() && !hipnews_is_ajax_enabled() ) { ?>
		<div class="navigation">
			<div class="alignleft"><?php previous_comments_link() ?></div>
			<div class="alignright"><?php next_comments_link() ?></div>
		</div>
	<?php } ?>
	
	<ol id="mobileview-comments" class="commentlist  <?php if ( hipnews_mobile_hide_responses() ) echo 'hidden'; ?>">
		<?php wp_list_comments('type=all&callback=hipnews_custom_comments'); ?>
		<?php if ( hipnews_is_ajax_enabled() ) { ?>
			<?php if ( hipnews_comments_newer() ) { ?>
				<li class="load-more-comments-link"><?php previous_comments_link(__( "Load More Comments&hellip;", "mobileviewlang" ) ); ?></li>
			<?php } else { ?>
				<li class="load-more-comments-link"><?php next_comments_link(__( "Load More Comments&hellip;", "mobileviewlang" ) ); ?></li>
			<?php } ?>
		<?php } ?>
	</ol>

	<?php if ( hipnews_wp_comments_nav_on() && !hipnews_is_ajax_enabled() ) { ?>
		<div class="navigation">
			<div class="alignleft"><?php previous_comments_link() ?></div>
			<div class="alignright"><?php next_comments_link() ?></div>
		</div>
	<?php } ?>
	
	
 
<?php else : // this is displayed if there are no comments so far ?>

	<?php if ( comments_open() ) : ?>
		<!-- If comments are open, but there are no comments. -->
	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments"><?php _e( "Comments are closed", "mobileviewlang" ); ?>.</p>
	<?php endif; ?>
	
<?php endif; ?>

<?php if ( comments_open() ) : ?>

	<div id="respond">
		<div class="cancel-comment-reply">
			<?php cancel_comment_reply_link( __( 'Cancel', 'mobileviewlang' ) ); ?>
		</div>	
		
		<h3><?php comment_form_title( __( 'Leave a Reply', 'mobileviewlang' ), __( 'Leave a Reply to %s', 'mobileviewlang' ) ); ?></h3>
		
		<?php if ( get_option( 'comment_registration' ) && !is_user_logged_in() ) : ?>
		<p><?php echo sprintf( __( "You must be %slogged in%s to post a comment.", "mobileviewlang" ), '<a class="login-req" href="' . wp_login_url( get_permalink() ) . '">', '</a>' ); ?></p>
		
		<?php else : ?>
		
		<form action="<?php mobileview_bloginfo('wpurl'); ?>/wp-comments-post.php" method="post" id="commentform">
			<?php if ( is_user_logged_in() ) : ?>
			
			<p><?php _e( "Logged in as", "mobileviewlang" ); ?> <?php echo $user_identity; ?>. <a href="<?php echo wp_logout_url( get_bloginfo( 'url' ) ); ?>" title="Log out"><?php _e( "Log out", "mobileviewlang" ); ?> &raquo;</a></p>
			
			<?php else : ?>
			
			<p><input type="text" name="author" id="author" value="<?php echo esc_attr( $comment_author ); ?>" size="22" <?php if ( $req ) echo "aria-required='true'"; ?> tabindex="10" />
			<label for="author"><?php _e( "Name", "mobileviewlang" ); ?><?php if ( $req ) echo "*"; ?></label></p>
			
			<p><input type="email" autocapitalize="off" name="email" id="email" value="<?php echo esc_attr($comment_author_email); ?>" size="22" <?php if ( $req ) echo "aria-required='true'"; ?> tabindex="11" />
			<label for="email"><?php _e( "E-Mail", "mobileviewlang" ); ?><?php if ( $req ) echo "*"; ?></label></p>
			
			<p><input type="url" autocapitalize="off" name="url" id="url" value="<?php echo esc_attr($comment_author_url); ?>" size="22" tabindex="12" />
			<label for="url"><?php _e( "Website", "mobileviewlang" ); ?></label></p>
					
			<?php endif; ?>
				
			<p><textarea name="comment" id="comment" cols="58" rows="10" tabindex="13"></textarea></p>
			
			<p><input name="submit" type="submit" id="submit" value="<?php _e( "Publish", "mobileviewlang" ); ?>" tabindex="14" /></p>
			
			<?php comment_id_fields(); ?>
			
			<?php do_action( 'comment_form', $post->ID ); ?>
		
		</form>
		
		<?php endif; // If registration required and not logged in ?>
	</div>

<?php endif; // if you delete this the sky will fall on your head ?>
</div><!-- #comments -->
