<!-- Depreciated in favor of add2home script, configure customizations in theme.js -->
<?php if ( mobile_view_show_webapp_notice() ) { ?>
	<div id="web-app-overlay">
		<img src="<?php mobileview_bloginfo( 'template_directory' ); ?>/images/web-app-bubble-arrow.png" alt="bubble-arrow" id="bubble-arrow" />
		<a href="#" id="close-wa-overlay">X</a>
		<img src="<?php  echo mobileview_get_site_menu_icon( MOBILEVIEW_ICON_BOOKMARK ); ?>" alt="bookmark-icon" id="bookmark-icon" />
		<h2><?php mobileview_bloginfo( 'site_title' ); ?></h2>
		<h3><?php _e( "is now web-app enabled!", "mobileviewlang" ); ?></h3>
		<p><?php echo sprintf( __( "Save %s as a web-app on your Home Screen.", "mobileviewlang" ), mobileview_get_bloginfo( 'site_title' ) ); ?></p>
		<p><?php _e( "Tap the center button below, then Add to Home Screen.", "mobileviewlang" ); ?></p>
	</div>
<?php } ?>
