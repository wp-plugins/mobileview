<textarea rows="5" class="textarea" readonly>
<?php
	$settings = mobileview_get_settings();
	
	if ( function_exists( 'gzcompress' ) ) {
		echo mobileview_get_encoded_backup_string( $settings );
	}
?>
</textarea>