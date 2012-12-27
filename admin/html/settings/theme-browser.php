<?php if ( wpmobi_has_themes() ) { ?>
	<?php while ( wpmobi_has_themes() ) { ?>
		<?php wpmobi_the_theme(); ?>
		
		<div class="<?php wpmobi_the_theme_classes( 'theme-wrap round-3' ); ?>">

			<input type="hidden" class="theme-location" value="<?php wpmobi_the_theme_location(); ?>" />
			<input type="hidden" class="theme-name" value="<?php wpmobi_the_theme_title(); ?>" />
					
			<div class="wpmobi-theme-left-wrap round-3">
				<img src="<?php wpmobi_the_theme_screenshot(); ?>" alt="<?php echo sprintf( __( '%s Theme Image', 'wpmobi-me' ), wpmobi_get_theme_title() ); ?>" />
			</div>
			<div class="wpmobi-theme-right-wrap">
				<ul class="option-list">
				<?php if ( !wpmobi_is_theme_active() ) { ?>					
					<li><a href="#" class="activate-theme ajax-button"><?php _e( 'Activate', 'wpmobi-me' ); ?></a></li>
				<?php } ?>
				<?php /*if ( !wpmobi_is_theme_custom() ) { ?>
				<?php if ( !wpmobi_is_theme_child() ) { ?>
					<li><a href="#" class="make-child-theme ajax-button"><?php _e( 'Copy As Child', 'wpmobi-me' ); ?></a></li>
				<?php } ?>
					<li><a href="#" class="copy-theme ajax-button"><?php _e( 'Copy As New', 'wpmobi-me' ); ?></a></li>
				<?php }*/ ?>
				<?php /*if ( wpmobi_is_theme_custom() ) { ?>
					<li><a href="#" class="delete-theme ajax-button"><?php _e( 'Delete', 'wpmobi-me' ); ?></a></li>
				<?php }*/ ?>
				</ul>
				<h4>
					<?php wpmobi_the_theme_title(); ?>
					<span><?php echo sprintf( __( '(%s)', 'wpmobi-me' ), wpmobi_get_theme_version() ); ?></span>
				</h4>
				<p class="wpmobi-theme-author green-text"><?php echo sprintf( __( 'By %s', 'wpmobi-me' ), wpmobi_get_theme_author() ); ?></p>
				<p class="wpmobi-theme-description"><?php wpmobi_the_theme_description(); ?></p>
				<?php if ( wpmobi_theme_has_features() ) { ?>
					<p class="wpmobi-theme-features"><?php echo sprintf( __( 'Features: %s', 'wpmobi-me' ), implode( wpmobi_get_theme_features(), ', ' ) ); ?></p>
				<?php } ?>		
				<?php if ( wpmobi_is_theme_custom() ) { ?>
					<p class="location"><?php echo sprintf( __( 'Theme Location (relative to wp-content):<br />%s', 'wpmobi-me' ), wpmobi_get_theme_location() ); ?></p>
				<?php } ?>
			</div>
			<br class="clearer" />	
		</div>
	<?php } ?>
<?php } else { ?>
	<?php _e( "There are currently no themes installed.", "wpmobi-me" ); ?>
<?php }