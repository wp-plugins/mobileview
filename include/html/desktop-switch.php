<?php global $wpmobi; ?>
<?php if ( wpmobi_show_switch_link() ) { ?>
	<div id="wpmobi-desktop-switch">	
		<?php if ( $wpmobi->active_device_class == 'ipad' ) { ?>
		<?php _e( "Desktop Version", "wpmobi-me" ); ?> | <a href="<?php wpmobi_the_desktop_switch_link(); ?>"><?php _e( "Switch To iPad Version", "wpmobi-me" ); ?></a>
		<?php } else { ?>
		<?php _e( "Desktop Version", "wpmobi-me" ); ?> | <a href="<?php wpmobi_the_desktop_switch_link(); ?>"><?php _e( "Switch To Mobile Version", "wpmobi-me" ); ?></a>
		<?php } ?>
	</div>
<?php } ?>
