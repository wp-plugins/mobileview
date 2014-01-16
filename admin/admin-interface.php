<?php 
global $mobileview,$_wp_admin_css_colors; $current_scheme = get_user_option('admin_color'); $settings = mobileview_get_settings(); 

$base_color = $_wp_admin_css_colors[ $current_scheme ]->colors[1];
$highlight_color = $_wp_admin_css_colors[ $current_scheme ]->colors[2];
$notification_color = $_wp_admin_css_colors[ $current_scheme ]->colors[3];
$text_color = $_wp_admin_css_colors[ $current_scheme ]->icon_colors['base'];
$text_current = $_wp_admin_css_colors[ $current_scheme ]->icon_colors['current'];

if('fresh'==$current_scheme){
	$base_color = '#0099CC';
	$text_color = '#FFFFFF';
}

echo '<style>';
echo '#colabsplugin .mobile-view-admin-header, #colabsplugin #mobileview-tabbed-area, #colabsplugin #mobileview-tabbed-area .left-area {background:'.$base_color.'}';
echo '#colabsplugin ul#mobileview-top-menu li a, #colabsplugin .left-area ul li a{color:'.$text_color.'}';
echo '#colabsplugin ul#mobileview-top-menu li a.active, #colabsplugin .left-area ul li a.active{color:'.$text_current.'; font-weight: 600;}';
echo '.left-area .menu-hover, #colabsplugin ul#mobileview-top-menu li a.active, #colabsplugin ul#mobileview-top-menu li a:hover{background:'.$highlight_color.'}';
echo '#colabsplugin .left-area ul li a:hover{color:'.$text_current.'}';
if('fresh'==$current_scheme){
echo '#colabsplugin .button-primary{background-color: #FFB101; border-color:#DA903B;box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2), 0 1px 0 0 rgba(255, 255, 255, 0.6) inset;}';
echo '#colabsplugin .button-primary:hover{background-color:#FFCA00}';
echo 'div.updated, .login .message, .press-this #message{border-color: #FFB101;}';
}
echo '</style>';
?>

<form method="post" action="" id="colabsplugin-form" enctype="multipart/form-data" class="<?php if ( $mobileview->locale ) echo 'locale-' . strtolower( $mobileview->locale ); ?>">
	<div id="colabsplugin" class="<?php echo $current_scheme; ?> <?php echo 'normal'; ?> wrap">
		<?php if ( $settings->developer_mode != 'off' ) { ?>
			<div id="message" class="error"><p><?php _e( "MobileView Developer Mode: ON", "colabsthemes" ); ?></p></div>
		<?php } ?>		
		
		<div class="mobileview_twitter_stream updated">

			<div class="stream-label"><i class="icon icon-twitter"></i><?php _e('News On Twitter:','colabsthemes');?></div>				

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
			
			<a href="#" class="dropdown-mobile">
				<div></div>
				<div></div>
				<div></div>
			</a>

			<ul id="mobileview-top-menu">

				<?php do_action( 'mobileview_pre_menu' ); ?>
				
				<?php $pane = 1; ?>
				<?php foreach( $mobileview->tabs as $name => $value ) { ?>
					<li>
						<a id="pane-<?php echo $pane; ?>" class="pane-<?php echo mobileview_string_to_class( $name ); ?>" href="#">
						<?php $icon = 'cogs'; if($value['icon'])$icon = $value['icon'];?>
						<i class="icon icon-<?php echo $icon;?>"></i>
						<span><?php echo $name; ?></span>
						</a>
					</li>
					<?php $pane++; ?>
				<?php } ?>
		
				<?php do_action( 'mobileview_post_menu' ); ?>
				<li>
					<a id="mobileview-documentation" class="mobileview-documentation" href="http://colorlabsproject.com/documentation/mobileview/" target='_blank'>
					<i class="icon icon-book"></i>
					<span><?php _e('Documentation','mobileviewlang')?></span>
					</a>
				</li>
			</ul>
			
			</div>		
			<div id="mobileview-tabbed-area"  class="main-panel">
				<?php mobileview_show_tab_settings(); ?>
				<div class="loading-ajax">
					<div class="mobileview-ajax-results" id="ajax-loading" style="display:none"><?php _e( "Loading...", "mobileviewlang" ); ?></div>
					<div class="mobileview-ajax-results" id="ajax-saving" style="display:none"><?php _e( "Saving...", "mobileviewlang" ); ?></div>
					<div class="mobileview-ajax-results" id="ajax-saved" style="display:none"><?php _e( "Done", "mobileviewlang" ); ?></div>
					<div class="mobileview-ajax-results" id="ajax-fail" style="display:none"><?php _e( "Oops! Try saving again.", "mobileviewlang" ); ?></div>
					<br class="clearer" />
				</div>
			</div>
			
			<br class="clearer" />
			
			<input type="hidden" name="mobileview-admin-tab" id="mobileview-admin-tab" value="" />
			<input type="hidden" name="mobileview-admin-menu" id="mobileview-admin-menu" value="" />
		</div>
		<input type="hidden" name="mobileview-admin-nonce" value="<?php echo wp_create_nonce( 'mobileview-post-nonce' ); ?>" />
		
		<div class="mobileview-button-wrap">
			<p class="submit" id="colabsplugin-submit">
				<button class="button-primary button" type="submit" name="mobileview-submit" title="Save" tabindex="1" value="<?php _e( "Save Changes", "mobileviewlang" ); ?>">
					<i class="icon icon-download-alt"></i><span class="button-text"><?php _e( "Save Changes", "mobileviewlang" ); ?></span>
				</button>
			</p>
		
			<p class="submit" id="colabsplugin-submit-reset">
				<button class="button" type="submit" name="mobileview-submit-reset" title="Reset" tabindex="2" value="<?php _e( "Reset Settings", "mobileviewlang" ); ?>">
					<i class="icon icon-spinner"></i><span class="button-text"><?php _e( "Reset Settings", "mobileviewlang" ); ?></span>
				</button>
				<span id="saving-ajax">
					<?php _e( "Saving", "mobileviewlang" ); ?>&hellip; <img src="<?php echo MOBILEVIEW_URL . '/admin/images/ajax-loader.gif'; ?>" alt="ajax image" />
				</span>
			</p>
		</div>

		<p id="colabsplugin-trademark"><a href="http://colorlabsproject.com/" target="_blank" title="ColorLabs & Company"><img src="<?php echo MOBILEVIEW_URL . '/admin/images/colorlabs.png'; ?>" alt="ColorLabs & Company" /></a></p>
	</div> <!-- mobileview-admin-area -->
</form>
