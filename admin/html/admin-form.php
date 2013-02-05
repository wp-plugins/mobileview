<?php global $wpmobi; $current_scheme = get_user_option('admin_color'); $settings = wpmobi_get_settings(); ?>

<form method="post" action="" id="clc-form" class="<?php if ( $wpmobi->locale ) echo 'locale-' . strtolower( $wpmobi->locale ); ?>">
	<div id="clc" class="<?php echo $current_scheme; ?> <?php echo 'normal'; ?> wrap">
		<?php if ( $settings->developer_mode != 'off' ) { ?>
			<div id="dev-notice"><?php _e( "MobileView Developer Mode: ON", "wpmobi-me" ); ?></div>
		<?php } ?>
		<div id="wpmobi-api-server-check"></div>
		<div id="wpmobi-main-top">
			<h3>
				<?php echo WPMOBI_PRODUCT_NAME . ' <span class="version">' . WPMOBI_VERSION; ?></span>
			</h3>
			<?php //wpmobi_save_reset_notice(); ?>
		</div>		
			
		<div id="wpmobi-admin-form">		
			<ul id="wpmobi-top-menu">
			
				<?php do_action( 'wpmobi_pre_menu' ); ?>
				
				<?php $pane = 1; ?>
				<?php foreach( $wpmobi->tabs as $name => $value ) { ?>
					<li><a id="pane-<?php echo $pane; ?>" class="pane-<?php echo wpmobi_string_to_class( $name ); ?>" href="#"><?php echo $name; ?></a></li>
					<?php $pane++; ?>
				<?php } ?>
		
				<?php do_action( 'wpmobi_post_menu' ); ?>
				
				<li>
					<div class="wpmobi-ajax-results blue-text" id="ajax-loading" style="display:none"><?php _e( "Loading...", "wpmobi-me" ); ?></div>
					<div class="wpmobi-ajax-results blue-text" id="ajax-saving" style="display:none"><?php _e( "Saving...", "wpmobi-me" ); ?></div>
					<div class="wpmobi-ajax-results green-text" id="ajax-saved" style="display:none"><?php _e( "Done", "wpmobi-me" ); ?></div>
					<div class="wpmobi-ajax-results red-text" id="ajax-fail" style="display:none"><?php _e( "Oops! Try saving again.", "wpmobi-me" ); ?></div>
					<br class="clearer" />
				</li>
			</ul>
					
			<div id="wpmobi-tabbed-area"  class="round-3 <?php if ( wpmobi_get_bloginfo( 'support_licenses_total' ) >= 5 ){ echo 'developer'; } if ( $settings->admin_client_mode_hide_tools ) { echo ' client-mode'; } ?>">
				<?php wpmobi_show_tab_settings(); ?>
			</div>
			
			<br class="clearer" />
			
			<input type="hidden" name="wpmobi-admin-tab" id="wpmobi-admin-tab" value="" />
			<input type="hidden" name="wpmobi-admin-menu" id="wpmobi-admin-menu" value="" />
		</div>
		<input type="hidden" name="wpmobi-admin-nonce" value="<?php echo wp_create_nonce( 'wpmobi-post-nonce' ); ?>" />

		<p class="submit" id="clc-submit">
			<input class="button-primary" type="submit" name="wpmobi-submit" tabindex="1" value="<?php _e( "Save Changes", "wpmobi-me" ); ?>" />
		</p>
		
		<p class="submit" id="clc-submit-reset">
			<input class="button" type="submit" name="wpmobi-submit-reset" tabindex="2" value="<?php _e( "Reset Settings", "wpmobi-me" ); ?>" />
			<span id="saving-ajax">
				<?php _e( "Saving", "wpmobi-me" ); ?>&hellip; <img src="<?php echo WPMOBI_URL . '/admin/images/ajax-loader.gif'; ?>" alt="ajax image" />
			</span>
		</p>

		<p id="clc-trademark"><?php echo sprintf( __( "Copyright &copy; 2013 by ColorLabs & Company. ", "wpmobi-me" ), '<em>', '</em>' ); ?></p>
		<div class="poof">&nbsp;</div>
	</div> <!-- wpmobi-admin-area -->
</form>
