<textarea rows="5" class="textarea" readonly>
<?php
	$settings = wpmobi_get_settings();
	
	if ( function_exists( 'gzcompress' ) ) {
		echo wpmobi_get_encoded_backup_string( $settings );
	}
?>
</textarea>