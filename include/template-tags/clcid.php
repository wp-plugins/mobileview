<?php

function wpmobi_has_license() {
	// Move this internally
	global $wpmobi;
	$settings = $wpmobi->get_settings();
	
	if ( time() > ( $settings->last_clcid_time + WPMOBI_CLCID_CACHE_TIME ) ) {
		$result = $wpmobi->clc_api->internal_check_token();	
		if ( $result ) {
			$settings->last_clcid_time = time();
			$settings->last_clcid_result = $wpmobi->clc_api->verify_site_license( 'wpmobi-me' );
			$settings->last_clcid_licenses = $wpmobi->clc_api->get_total_licenses( 'wpmobi-me' );
			
			if ( $settings->last_clcid_result ) {
				$setting->clcid_had_license = true;	
			}
		} else {
			$settings->last_clcid_time = 0;
			$settings->last_clcid_result = false;			
			$settings->last_clcid_licenses = 0;
		}		
			
		$wpmobi->save_settings( $settings );		
	}
	
	return $settings->last_clcid_result;
}

function wpmobi_was_username_invalid() {
	global $wpmobi;
	
	return ( $wpmobi->clc_api->get_response_code() == 408 );
}

function wpmobi_user_has_no_license() {
	global $wpmobi;
	
	return ( $wpmobi->clc_api->get_response_code() == 412 );	
}

function wpmobi_credentials_invalid() {
	global $wpmobi;
	return $wpmobi->clc_api->credentials_invalid;
}

function wpmobi_api_server_down() {
	global $wpmobi;
	
	$wpmobi->clc_api->verify_site_license( 'wpmobi-me' );	
	return $wpmobi->clc_api->server_down;
}

function wpmobi_has_proper_auth() {
	wpmobi_has_license();
	
	$settings = wpmobi_get_settings();
	return $settings->last_clcid_licenses;
}

function wpmobi_is_upgrade_available() {
	global $wpmobi;

		$latest_info = $wpmobi->clc_api->get_product_version( 'wpmobi-me' );
		if ( $latest_info && !strpos( WPMOBI_VERSION, 'b' ) ) {
			return ( $latest_info['version'] != WPMOBI_VERSION );
		} else {
			return false;
		}
}

global $wpmobi_site_license;
global $wpmobi_site_license_info;
global $wpmobi_site_license_iterator;
$wpmobi_site_license_iterator = false;

function wpmobi_has_site_licenses() {
	global $wpmobi;
	global $wpmobi_site_license_info;
	global $wpmobi_site_license_iterator;
	
	if ( !$wpmobi_site_license_iterator ) {
		$wpmobi_site_license_info = $wpmobi->clc_api->user_list_licenses( 'wpmobi-me' );
		$wpmobi_site_license_iterator = new WPMobiArrayIterator( $wpmobi_site_license_info['licenses'] );
	}	
	
	return $wpmobi_site_license_iterator->have_items();
}

function wpmobi_the_site_license() {
	global $wpmobi_site_license;
	global $wpmobi_site_license_iterator;
	
	$wpmobi_site_license = $wpmobi_site_license_iterator->the_item();
}

function wpmobi_the_site_licenses_remaining() {
	echo wpmobi_get_site_licenses_remaining();
}

function wpmobi_get_site_licenses_remaining() {
	global $wpmobi_site_license_info;	
		
	if ( $wpmobi_site_license_info && isset( $wpmobi_site_license_info['remaining'] ) ) {
		return $wpmobi_site_license_info['remaining'];
	}
	
	return 0;
}

function wpmobi_the_site_license_name() {
	echo wpmobi_get_site_license_name();
}

function wpmobi_get_site_license_name() {
	global $wpmobi_site_license;
	return $wpmobi_site_license;
}

function wpmobi_is_licensed_site() {
	global $wpmobi;
	return $wpmobi->has_site_license();
}

function wpmobi_get_site_license_number() {
	global $wpmobi_site_license_iterator;
	return $wpmobi_site_license_iterator->current_position();
}

function wpmobi_can_delete_site_license() {
	return ( wpmobi_get_site_license_number() > 1 );	
}

?>