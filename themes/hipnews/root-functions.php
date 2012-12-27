<?php

// Add HipNews's custom user agents to the list of supported devices
add_filter( 'wpmobi_supported_device_classes', 'hipnews_supported_devices' );

// When the root-functions.php is loaded, load all the HipNews functions that should be global
add_filter( 'wpmobi_functions_loaded', 'hipnews_functions_loaded' );

// When the mobile theme is showing, load all the other relevant template functions
add_filter( 'wpmobi_mobile_theme_showing', 'hipnews_mobile_theme_showing' );

add_filter( 'wpmobi_create_thumbnails', 'hipnews_create_thumbnails' );

add_filter( 'wpmobi_settings_saved', 'hipnews_remove_static_css' );

add_action( 'wpmobi_upgrade', 'hipnews_remove_static_css' );

function hipnews_get_static_css_filename( $device ) {
	if ( wpmobi_is_multisite_enabled() ) {
		global $blog_id;
		return apply_filters( 'hipnews_static_css_filename', WPMOBI_TEMP_DIRECTORY . '/hipnews-' . $device . '-' . $blog_id . '.css' );	
	} else {
		return apply_filters( 'hipnews_static_css_filename', WPMOBI_TEMP_DIRECTORY . '/hipnews-' . $device . '.css' );	
	}
}

function hipnews_get_static_css_url( $device ) {
	if ( wpmobi_is_multisite_enabled() ) {
		global $blog_id;
		return apply_filters( 'hipnews_static_css_url', WPMOBI_TEMP_URL . '/hipnews-' . $device . '-' . $blog_id . '.css' );
	} else {
		return apply_filters( 'hipnews_static_css_url', WPMOBI_TEMP_URL . '/hipnews-' . $device . '.css' );
	}	
}

function hipnews_write_data_to_file( $file_name, $data ) {	
	if ( $data ) {
		$f = fopen( $file_name, 'w+t' );
		if ( $f ) {
			fwrite( $f, $data );
			fclose( $f );	
		}
	}	
}

function hipnews_remove_static_css() {
	$devices = array( 'iphone', 'ipad' );
	foreach( $devices as $device ) {
		$static_file = hipnews_get_static_css_filename( $device );
		if ( file_exists( $static_file ) ) {	
			@unlink( $static_file );
		}
	}	
}

function hipnews_generate_static_one_css( $device ) {
	ob_start();
	//include( dirname( __FILE__ ) . '/' . $device . '/dynamic-style.php' );
	$contents = ob_get_contents();
	ob_end_clean();
	
	hipnews_write_data_to_file( hipnews_get_static_css_filename( $device ), $contents );		
}

function hipnews_the_static_css_version( $device ) {
	$file_name = hipnews_get_static_css_filename( $device );
	if ( file_exists( $file_name ) ) {
		echo md5( filemtime( $file_name ) );	
	} else {
		echo 0;	
	}
}

function hipnews_the_static_css_url( $device ) {
	$file_name = hipnews_get_static_css_filename( $device );
	if ( !file_exists( $file_name ) ) {
		hipnews_generate_static_one_css( $device );
	}	
	
	echo hipnews_get_static_css_url( $device );
}

function hipnews_supported_devices( $devices ) {
	if ( isset( $devices['iphone'] ) ) {
		$settings = wpmobi_get_settings();
		
		$filtered_user_agents = trim( $settings->hipnews_custom_user_agents );

		if ( strlen( $filtered_user_agents ) ) {	
			// get user agents
			$agents = explode( "\n", str_replace( "\r\n", "\n", $filtered_user_agents ) );
			if ( count( $agents ) ) {	
				// add our custom user agents
				$devices['iphone'] = array_merge( $devices['iphone'], $agents );
			}
		}
	}
	
	return $devices;	
}

// Load the additional global HipNews functions
function hipnews_functions_loaded() {
	require_once( dirname( __FILE__ ) . '/includes/global.php' );
}

// Load the HipNews-specific templating functions
function hipnews_mobile_theme_showing() {
	require_once( dirname( __FILE__ ) . '/includes/theme.php' );
}

function hipnews_create_thumbnails( $thumbnails_enabled ) {
	$settings = wpmobi_get_settings();
	if ( $thumbnails_enabled ) {
		return ( $settings->hipnews_icon_type == 'thumbnails' );
	}
	
	return $thumbnails_enabled;
}

// mobile zoom option, needs to be in root functions to work
function hipnews_mobile_enable_zoom() {
	$settings = wpmobi_get_settings();
	return $settings->hipnews_mobile_enable_zoom;
}

function is_iOS_5(){
	if ( strpos( $_SERVER['HTTP_USER_AGENT'],'OS 5_' ) ) {
		return true;
	} else {
		return false;
	}
}

