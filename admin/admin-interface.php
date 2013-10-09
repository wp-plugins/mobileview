<?php global $mobileview; $current_scheme = get_user_option('admin_color'); $settings = mobileview_get_settings(); ?>

<form method="post" action="" id="colabsplugin-form" enctype="multipart/form-data" class="<?php if ( $mobileview->locale ) echo 'locale-' . strtolower( $mobileview->locale ); ?>">
	<div id="colabsplugin" class="<?php echo $current_scheme; ?> <?php echo 'normal'; ?> wrap">
		<?php if ( $settings->developer_mode != 'off' ) { ?>
			<div id="message" class="error"><p><?php _e( "MobileView Developer Mode: ON", "colabsthemes" ); ?></p></div>
		<?php } ?>		
		
		<div class="mobileview_twitter_stream">

			<div class="stream-label"><?php _e('News On Twitter:','colabsthemes');?></div>				

		  <?php 
			  $mobileview_twit = new mobileview_twitter();
			  $user_timeline = $mobileview_twit->mobileview_get_user_timeline( 'colorlabs', 5 );
			  if( isset( $user_timeline['error'] ) ) : ?>
				<p><?php echo $user_timeline['error']; ?></p>
			  <?php 
			  else : 
				$mobileview_twit->mobileview_build_twitter_markup( $user_timeline );
			  endif; 
		  ?>


		</div>
		<!-- .colabs_twitter-stream -->

		<div id="mobileview-admin-form">
			<div class="mobile-view-admin-header">
			<div id="mobileview-main-top">
				<h3>
					<img src="<?php echo MOBILEVIEW_URL; ?>/admin/images/logo.png">
					<a href="http://colorlabsproject.com/plugins/mobileview/" target="_blank" title="ColorLabs & Company"><?php echo MOBILEVIEW_PRODUCT_NAME ;?></a> <span class="version"><?php echo MOBILEVIEW_VERSION; ?></span>
				</h3>
			</div>
			<ul id="mobileview-top-menu">
			
				<?php do_action( 'mobileview_pre_menu' ); ?>
				
				<?php $pane = 1; ?>
				<?php foreach( $mobileview->tabs as $name => $value ) { ?>
					<li>
						<a id="pane-<?php echo $pane; ?>" class="pane-<?php echo mobileview_string_to_class( $name ); ?>" href="#">
						<img src="<?php echo MOBILEVIEW_URL; ?>/admin/images/<?php echo $value['icon_url']; ?>">
						<span><?php echo $name; ?></span>
						</a>
					</li>
					<?php $pane++; ?>
				<?php } ?>
		
				<?php do_action( 'mobileview_post_menu' ); ?>
				<li>
					<a id="mobileview-documentation" class="mobileview-documentation" href="http://colorlabsproject.com/documentation/mobileview/" target='_blank'>
					<img src="<?php echo MOBILEVIEW_URL; ?>/admin/images/book.png">
					<span><?php _e('Documentation','mobileviewlang')?></span>
					</a>
				</li>
			</ul>
			<div class="loading-ajax">
					<div class="mobileview-ajax-results" id="ajax-loading" style="display:none"><?php _e( "Loading...", "mobileviewlang" ); ?></div>
					<div class="mobileview-ajax-results" id="ajax-saving" style="display:none"><?php _e( "Saving...", "mobileviewlang" ); ?></div>
					<div class="mobileview-ajax-results" id="ajax-saved" style="display:none"><?php _e( "Done", "mobileviewlang" ); ?></div>
					<div class="mobileview-ajax-results" id="ajax-fail" style="display:none"><?php _e( "Oops! Try saving again.", "mobileviewlang" ); ?></div>
					<br class="clearer" />
				</div>
			</div>		
			<div id="mobileview-tabbed-area"  class="main-panel">
				<?php mobileview_show_tab_settings(); ?>
				
			</div>
			
			<br class="clearer" />
			
			<input type="hidden" name="mobileview-admin-tab" id="mobileview-admin-tab" value="" />
			<input type="hidden" name="mobileview-admin-menu" id="mobileview-admin-menu" value="" />
		</div>
		<input type="hidden" name="mobileview-admin-nonce" value="<?php echo wp_create_nonce( 'mobileview-post-nonce' ); ?>" />
		
		<div class="mobileview-button-wrap">
			<p class="submit" id="colabsplugin-submit">
				<input class="button-primary" type="submit" name="mobileview-submit" title="Save" tabindex="1" value="<?php _e( "Save Changes", "mobileviewlang" ); ?>" />
			</p>
		
			<p class="submit" id="colabsplugin-submit-reset">
				<input class="button" type="submit" name="mobileview-submit-reset" title="Reset" tabindex="2" value="<?php _e( "Reset Settings", "mobileviewlang" ); ?>" />
				<span id="saving-ajax">
					<?php _e( "Saving", "mobileviewlang" ); ?>&hellip; <img src="<?php echo MOBILEVIEW_URL . '/admin/images/ajax-loader.gif'; ?>" alt="ajax image" />
				</span>
			</p>
		</div>

		<p id="colabsplugin-trademark"><a href="http://colorlabsproject.com/" target="_blank" title="ColorLabs & Company"><img src="<?php echo MOBILEVIEW_URL . '/admin/images/colorlabs.png'; ?>" alt="ColorLabs & Company" /></a></p>
		<div class="poof">&nbsp;</div>
	</div> <!-- mobileview-admin-area -->
</form>
