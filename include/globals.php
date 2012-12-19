<?php

define( 'WPMOBI_PRO_DESKTOP_FCN_CACHE_TIME', 3600 );

add_action( 'wpmobi_loaded', 'wpmobi_load_extensions' );

function wpmobi_is_mobile_theme_showing() {
	global $wpmobi;
	
	return ( $wpmobi->is_mobile_device && $wpmobi->showing_mobile_theme );	
}

function wpmobi_load_extensions() {
	global $wpmobi;
	$php_files = $wpmobi->get_all_recursive_files( WPMOBI_DIR . '/include/extensions/', 'php' );
	
	if ( $php_files && count( $php_files ) ) {
		foreach( $php_files as $php_file ) {
			require_once( WPMOBI_DIR . '/include/extensions' . $php_file );
		}
	}
	
}

/*!		\brief Determines whether or not the active theme supports iPad/Tablet devices
 *
 *		This function can be used to determine if the active theme supports the iPad or Tablet devices.
 *
 *		\returns TRUE if iPad is supported, otherwise FALSE
 *
 *		\ingroup templatetags 
 */
function wpmobi_theme_supports_ipad() {
	global $wpmobi;
	
	return $wpmobi->theme_supports_ipad();	
}

/*!		\brief Respects the admin setting to show/hide the switch links in WPMobi and regular theme footers
 *
 *		Boolean depending on the admin setting.
 *
 *		\returns TRUE or FALSE depending on the admin setting.
 *
 *		\note If this setting is unchecked, users will NOT be able to switch from mobile to desktop, or vice versa. 
 *
 *		\ingroup templatetags 
 */

function wpmobi_show_switch_link() {
	$settings = wpmobi_get_settings();
	if ( $settings->show_switch_link ) {
		return true;
	} else {
		return false;
	}
}

/*!		\brief Echos the mobile/desktop switch link URL for desktop theme
 *
 *		This function echos the mobile/desktop switch link URL.  It echos the result from wpmobi_get_desktop_switch_link().
 *
 *		\ingroup templatetags 
 */
function wpmobi_the_desktop_switch_link() {
	echo wpmobi_get_desktop_switch_link();
}

/*!		\brief Retrieves the mobile/desktop switch link URL for desktop them
 *
 *		This function can be used to retrieve the mobile/desktop switch link URL.  It can be filtered using the WordPress filter \em wpmobi_desktop_switch_link.
 *
 *		\returns The URL for the desktop switch link, and respects the admin setting whether the URL re-direct is the request URI, or homepage.
 *
 *		\note Visiting this URL alters the mobile switch COOKIE and redirects back to the current page.
 *
 *		\ingroup templatetags 
 */
function wpmobi_get_desktop_switch_link() {
	$settings = wpmobi_get_settings();
	if ( $settings->show_switch_link ) {
		if ( $settings->home_page_redirect_address == 'same' ) {
			return apply_filters( 'wpmobi_desktop_switch_link', get_bloginfo( 'url' ) . '?wpmobi_switch=mobile&amp;redirect=' . urlencode( $_SERVER['REQUEST_URI'] ) );
		} else {
			return apply_filters( 'wpmobi_desktop_switch_link', get_bloginfo( 'url' ) . '?wpmobi_switch=mobile&amp;redirect=' . get_bloginfo( 'url' ) );
		}
	}
}

function wpmobi_locate_template( $param1, $param2, $param3, $param4 = false, $param5 = false ) {
	$template_path = false;
	$current_path = false;
	$require_once = true;
	
	if ( $param4 ) {
		if ( $param5 ) {
			// 5 parameters
			$template_path = $param4;
			$current_path = $param5;
			$require_once = $param3;
		} else {
			// 4 parameters
			$template_path = $param3;
			$current_path = $param4;	
		}
	} else {
		// 3 parameters
		$template_path = $param2;
		$current_path = $param3;		
	}

	$template_file = $template_path . '/' . $param1;
	if ( !file_exists( $template_file ) ) {
		$template_file = $current_path . '/' . $param1;
	} 
	
	if ( file_exists( $template_path ) ) {
		$current_path = dirname( $template_file );
		if ( $require_once ) {
			wpmobi_include_functions_file( $template_file, $template_path, $current_path, 'require_once' );
		} else {
			wpmobi_include_functions_file( $template_file, $template_path, $current_path, 'require' );
		}
	} else {
		// add debug statement
	}
}

function wpmobi_include_functions_file( $file_name, $template_path, $current_path, $load_type ) {
	global $wpmobi;
	
	// Figure out real name of the source file
	$source_file = $file_name;

	if ( !file_exists( $source_file ) ) {
		$source_file = $current_path . '/' . $file_name;
		if ( !file_exists( $source_file ) ) {
			$source_file = $template_path . '/' . $file_name;
			if ( !file_exists( $source_file ) ) {
				echo 'Unable to load desktop functions file';
				die;
			}
		}
	}
	
	// Determine name of cached file
	$file_info = pathinfo( $source_file );
	$cached_file = $file_info['dirname'] . '/.' . $file_info['basename'] . '.wpmobi';	
	
	// Basic caching for generating new functions files
	$generate_new_cached_file = true;
	if ( file_exists( $cached_file ) ) {
		$cached_file_mod_time = filemtime( $cached_file );
		$time_since_last_update = time() - $cached_file_mod_time;
		
		// Only update once an hour
		if ( $time_since_last_update < WPMOBI_PRO_DESKTOP_FCN_CACHE_TIME ) {
			$generate_new_cached_file = false;
		}
	}
	
	// Only generate cached file when it's stale or unavailable
	if ( $generate_new_cached_file ) {
		$contents = $wpmobi->include_functions_file( $file_name, $template_path, $current_path );
		
		$f = fopen( $cached_file, 'wt+' );
		if ( $f ) {	
			fwrite( $f, $contents );
			fclose( $f );
		}
	}
	
	// Load cached file
	switch( $load_type ) {
		case 'include':
			include( $cached_file );
			break;
		case 'include_once';
			include_once( $cached_file );
			break;
		case 'require';
			require( $cached_file );
			break;
		case 'require_once';
			require_once( $cached_file );
			break;
		default:
			break;
	}	
}

function wpmobi_get_desktop_bloginfo( $param ) {
	switch( $param ) {
		case 'stylesheet_directory':
		case 'template_url':
		case 'template_directory':
			return WP_CONTENT_URL . '/themes/' . get_option( 'template' );
		case 'stylesheet_url':
			//return WP_CONTENT_URL . '/themes/' . get_option( 'template' ) . '/style.css';
			break;
		default:
			return get_bloginfo( $param );
	}
}

function wpmobi_desktop_bloginfo( $param ) {
	echo wpmobi_get_desktop_bloginfo( $param );
}

function wpmobi_get_encoded_backup_string( $settings ) {
	return base64_encode( gzcompress( serialize( $settings ), 9 ) );
}