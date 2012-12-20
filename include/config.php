<?php

define( 'WPMOBI_PRO_INSTALLED', 1 );

function clc_wpmobi_sslize( $ssl_string ) {
	// Hack to fix broken icons due to an old pre 2.6 bug
	$ssl_string = str_replace( WP_CONTENT_URL . 'http', 'http', $ssl_string );
	if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
		return str_replace( 'http://', 'https://', $ssl_string );
	} else {
		return $ssl_string;
	}	
}

//! Set this to 'true' to enable debugging
define( 'WPMOBI_DEBUG', false );

//! Set this to 'true' to enable simulation of all warnings and conflicts
define( 'WPMOBI_SIMULATE_ALL', false );

// Set up beta variable
	define( 'WPMOBI_PRO_BETA', false );	
	define( 'WPMOBI_ROOT_DIR', 'mobile-view' );

//! The key in the database for the WPMobi settings
	define( 'WPMOBI_SETTING_NAME', 'wpmobi-me' );
	define( 'WPMOBI_DIR', WP_PLUGIN_DIR . '/mobileview' );
	define( 'WPMOBI_URL', clc_wpmobi_sslize( WP_PLUGIN_URL . '/mobileview' ) );
	define( 'WPMOBI_PRODUCT_NAME', 'MobileView' );


//! The MobileView user cookie
define( 'WPMOBI_COOKIE', 'wpmobi-me-view' );
define( 'WPMOBI_CLCID_CACHE_TIME', 3600 );
define( 'CLC_WPMOBI_UNLIMITED', 9999 );

define( 'WPMOBI_IPAD_DIR', WPMOBI_DIR . '/include/ipad' );
define( 'WPMOBI_IPAD_URL', WPMOBI_URL . '/include/ipad' );

define( 'WPMOBI_ADMIN_DIR', WPMOBI_DIR . '/admin' );
define( 'WPMOBI_ADMIN_AJAX_DIR', WPMOBI_ADMIN_DIR . '/html/ajax' );
define( 'WPMOBI_BASE_CONTENT_DIR', WP_CONTENT_DIR . '/wpmobi-me-data' );
define( 'WPMOBI_BASE_CONTENT_URL', clc_wpmobi_sslize( WP_CONTENT_URL . '/wpmobi-me-data' ) );

define( 'WPMOBI_TEMP_DIRECTORY', WPMOBI_BASE_CONTENT_DIR . '/temp' );
define( 'WPMOBI_TEMP_URL', WPMOBI_BASE_CONTENT_URL . '/temp' );
define( 'WPMOBI_CUSTOM_SET_DIRECTORY', WPMOBI_BASE_CONTENT_DIR .'/icons' );		
define( 'WPMOBI_CUSTOM_ICON_DIRECTORY', WPMOBI_BASE_CONTENT_DIR . '/icons/custom' );
define( 'WPMOBI_CUSTOM_THEME_DIRECTORY', WPMOBI_BASE_CONTENT_DIR .'/themes' );
define( 'WPMOBI_CUSTOM_LANG_DIRECTORY', WPMOBI_BASE_CONTENT_DIR . '/lang' );
define( 'WPMOBI_CUSTOM_SETTINGS_DIRECTORY', WPMOBI_BASE_CONTENT_DIR . '/settings' );
define( 'WPMOBI_CHILD_THEME_TEMPLATE_DIRECTORY', WPMOBI_DIR . '/include/child-templates' );

define( 'WPMOBI_DEBUG_DIRECTORY', WPMOBI_BASE_CONTENT_DIR . '/debug' );
define( 'WPMOBI_CACHE_DIRECTORY', WPMOBI_BASE_CONTENT_DIR . '/cache' );

define( 'WPMOBI_CACHE_URL', WPMOBI_BASE_CONTENT_URL . '/cache' );
define( 'WPMOBI_CUSTOM_ICON_URL', WPMOBI_BASE_CONTENT_URL .'/icons/custom' );

define( 'WPMOBI_PRO_MIN_BACKUP_FILES', 30 );

global $wpmobi_menu_items; 		//! the built menu item tree
global $wpmobi_menu_iterator; 		//! the iterator for the main menu
global $wpmobi_menu_item;			//! the current menu item

global $wpmobi_icon_pack;
global $wpmobi_icon_packs;
global $wpmobi_icon_packs_iterator;

$wpmobi_icon_pack = false;
$wpmobi_icon_packs = false;
$wpmobi_icon_packs_iterator = false;

// These all need to be negative so as not to conflict with real page numbers
define( 'WPMOBI_ICON_HOME', -1 );
define( 'WPMOBI_ICON_BOOKMARK', -2 );
define( 'WPMOBI_ICON_DEFAULT', -3 );
define( 'WPMOBI_ICON_EMAIL', -4 );
define( 'WPMOBI_ICON_RSS', -5 );
define( 'WPMOBI_ICON_TABLET_BOOKMARK', -6 );
define( 'WPMOBI_ICON_CUSTOM_1', -101 );
define( 'WPMOBI_ICON_CUSTOM_2', -102 );
define( 'WPMOBI_ICON_CUSTOM_3', -103 );
define( 'WPMOBI_ICON_CUSTOM_PAGE_TEMPLATES', -500 );

global $wpmobi_device_classes;
$wpmobi_device_classes[ 'iphone' ] = array( 
	'iPhone', 					// iPhone
	'iPod', 						// iPod touch
	'incognito', 				// iPhone alt browser
	'webmate', 				// iPhone alt browser
	'Android', 					// Android
	'dream', 					// Android
	'CUPCAKE', 				// Android
	'froyo', 						// Android
	'BlackBerry9500', 		// Storm 1
	'BlackBerry9520', 		// Storm 1
	'BlackBerry9530', 		// Storm 2
	'BlackBerry9550', 		// Storm 2
	'BlackBerry 9800', 	// Torch
	'BlackBerry 9850', 	// Torch 2
	'BlackBerry 9860', 	// Torch 2
	'BlackBerry 9780', 	// Bold 3
	'webOS',					// Palm Pre/Pixi
	's8000',					// Samsung s8000
	'bada',						// Samsung Bada Phone
	"IEMobile/7.0",			// Windows Phone OS 7
	'Googlebot-Mobile',	// Google's mobile Crawler
	'AdsBot-Google'		// Google's Ad Bot Crawler
);

global $wpmobi_exclusion_list;
$wpmobi_exclusion_list = array(
	'SCH-I800',				// Samsung Galaxy Tab
	'Xoom',						// Motorola Xoom tablet
	'P160U'						// HP TouchPad
);

