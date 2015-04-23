<?php global $mobileview; ?>
<?php if ( mobileview_show_switch_link() ) { ?>
	<div id="mobileview-desktop-switch">	
		<?php if ( $mobileview->active_device_class == 'ipad' ) { ?>
		<?php _e( "Desktop Version", "mobileviewlang" ); ?> | <a href="<?php mobileview_the_desktop_switch_link(); ?>"><?php _e( "Switch To iPad Version", "mobileviewlang" ); ?></a>
		<?php } else { ?>
		<?php _e( "Desktop Version", "mobileviewlang" ); ?> | <a href="<?php mobileview_the_desktop_switch_link(); ?>"><?php _e( "Switch To Mobile Version", "mobileviewlang" ); ?></a>
		<?php } ?>
	</div>
<?php } ?>
