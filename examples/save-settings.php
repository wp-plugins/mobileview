$settings = wpmobi_get_settings();

$settings->some_already_existing_setting = true;

wpmobi_save_settings( $settings ); 	// saves the new value to the database