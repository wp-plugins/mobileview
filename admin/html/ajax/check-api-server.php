<?php if ( wpmobi_api_server_down() ) { ?>
	<p class="api-warning round-3"><?php _e( "The license server could not be reached.", "wpmobi-me" ); ?><br /><?php _e( "Don't worry, it's temporary, and doesn't affect MobileView from working.", "wpmobi-me" ); ?></p>
<?php } ?>