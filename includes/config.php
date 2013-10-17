<?php

define( 'MOBILEVIEW_INSTALLED', 1 );

function colabsplugin_mobileview_sslize( $ssl_string ) {
	// Hack to fix broken icons due to an old pre 2.6 bug
	$ssl_string = str_replace( WP_CONTENT_URL . 'http', 'http', $ssl_string );
	if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
		return str_replace( 'http://', 'https://', $ssl_string );
	} else {
		return $ssl_string;
	}	
}

//! Set this to 'true' to enable debugging
define( 'MOBILEVIEW_DEBUG', false );

//! Set this to 'true' to enable simulation of all warnings and conflicts
define( 'MOBILEVIEW_SIMULATE_ALL', false );

// Set up beta variable
	define( 'MOBILEVIEW_ROOT_DIR', 'mobileview' );

//! The key in the database for the MobileView settings
	define( 'MOBILEVIEW_SETTING_NAME', 'mobileview' );
	define( 'MOBILEVIEW_DIR', WP_PLUGIN_DIR . '/mobileview' );
	define( 'MOBILEVIEW_URL', colabsplugin_mobileview_sslize( WP_PLUGIN_URL . '/mobileview' ) );
	define( 'MOBILEVIEW_PRODUCT_NAME', 'MobileView' );


//! The MobileView user cookie
define( 'MOBILEVIEW_COOKIE', 'mobileview-view' );
define( 'COLABSPLUGIN_MOBILEVIEW_UNLIMITED', 9999 );

define( 'MOBILEVIEW_IPAD_DIR', MOBILEVIEW_DIR . '/includes/ipad' );
define( 'MOBILEVIEW_IPAD_URL', MOBILEVIEW_URL . '/includes/ipad' );

define( 'MOBILEVIEW_ADMIN_DIR', MOBILEVIEW_DIR . '/admin' );

define( 'MOBILEVIEW_BASE_CONTENT_DIR', WP_CONTENT_DIR . '/mobileview-source' );
define( 'MOBILEVIEW_BASE_CONTENT_URL', colabsplugin_mobileview_sslize( WP_CONTENT_URL . '/mobileview-source' ) );

define( 'MOBILEVIEW_TEMP_DIRECTORY', MOBILEVIEW_BASE_CONTENT_DIR . '/temp' );
define( 'MOBILEVIEW_TEMP_URL', MOBILEVIEW_BASE_CONTENT_URL . '/temp' );
define( 'MOBILEVIEW_CUSTOM_THEME_DIRECTORY', MOBILEVIEW_BASE_CONTENT_DIR .'/themes' );
define( 'MOBILEVIEW_CUSTOM_LANG_DIRECTORY', MOBILEVIEW_BASE_CONTENT_DIR . '/lang' );
define( 'MOBILEVIEW_CUSTOM_SETTINGS_DIRECTORY', MOBILEVIEW_BASE_CONTENT_DIR . '/settings' );
define( 'MOBILEVIEW_CHILD_THEME_TEMPLATE_DIRECTORY', MOBILEVIEW_DIR . '/includes/child-templates' );

define( 'MOBILEVIEW_DEBUG_DIRECTORY', MOBILEVIEW_BASE_CONTENT_DIR . '/debug' );
define( 'MOBILEVIEW_CACHE_DIRECTORY', MOBILEVIEW_BASE_CONTENT_DIR . '/cache' );

define( 'MOBILEVIEW_CACHE_URL', MOBILEVIEW_BASE_CONTENT_URL . '/cache' );

define( 'MOBILEVIEW_MIN_BACKUP_FILES', 30 );

global $mobileview_menu_items; 		//! the built menu item tree
global $mobileview_menu_iterator; 		//! the iterator for the main menu
global $mobileview_menu_item;			//! the current menu item

global $mobileview_icon_pack;
global $mobileview_icon_packs;
global $mobileview_icon_packs_iterator;

$mobileview_icon_pack = false;
$mobileview_icon_packs = false;
$mobileview_icon_packs_iterator = false;

// These all need to be negative so as not to conflict with real page numbers
define( 'MOBILEVIEW_ICON_HOME', -1 );
define( 'MOBILEVIEW_ICON_BOOKMARK', -2 );
define( 'MOBILEVIEW_ICON_DEFAULT', -3 );
define( 'MOBILEVIEW_ICON_EMAIL', -4 );
define( 'MOBILEVIEW_ICON_RSS', -5 );
define( 'MOBILEVIEW_ICON_TABLET_BOOKMARK', -6 );
define( 'MOBILEVIEW_ICON_CUSTOM_1', -101 );
define( 'MOBILEVIEW_ICON_CUSTOM_2', -102 );
define( 'MOBILEVIEW_ICON_CUSTOM_3', -103 );
define( 'MOBILEVIEW_ICON_CUSTOM_PAGE_TEMPLATES', -500 );

global $mobileview_device_classes;
$mobileview_device_classes[ 'iphone' ] = array( 
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

global $mobileview_exclusion_list;
$mobileview_exclusion_list = array(
	'SCH-I800',				// Samsung Galaxy Tab
	'Xoom',						// Motorola Xoom tablet
	'P160U'						// HP TouchPad
);

