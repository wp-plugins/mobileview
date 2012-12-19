<?php
	global $wpmobi;
	$wpmobi->clc_api->verify_site_license( 'wpmobi-me' );
	$settings = wpmobi_get_settings();
?>

	
<?php if ( wpmobi_has_proper_auth() && !$settings->admin_client_mode_hide_licenses ) { ?>
	<?php { ?>
	<p class="license-valid round-6"><span><?php _e( 'License accepted, thank you for supporting Mobile View!', 'wpmobi-me' ); ?></span></p>	
	
	<?php } ?>
<?php } ?>