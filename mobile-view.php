<?php
/*
Plugin Name: MobileView
Version: 1.0.0
Description: MobileView is a free wordpress plugin to transform your wordpress site into mobile-friendly theme for various devices including iPhone, Android, Blackberry and others.
Author: ColorLabs & Company
Text Domain: wpmobi-me
Domain Path: /lang
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html

# All admin and theme(s) Designs / Images / CSS
# are Copyright 2011 - 2012 ColorLabs & Company.
# 'MobileView (WPMobi.me)' are unregistered trademarks of ColorLabs & Company., 
# and cannot be re-used in conjuction with the GPL v2 usage of this software 
# under the license terms of the GPL v2 without permission.
# 
# You may find WPMOBI variable in MobileView Plugins file. It was actually variable name for MobileView.
# MobileView = WPMobi
*/

global $wpmobi;

// Should not have spaces in it, same as above
define( 'WPMOBI_VERSION', '1.0.0' );

// Configuration
require_once( 'include/config.php' );

// Load settings
require_once( 'include/settings.php' );

// Load global functions
require_once( 'include/globals.php' );

// Load array iterator, used everywhere
require_once( 'include/classes/array-iterator.php' );

// Main WPMobi Class
require_once( 'include/classes/wpmobi-me.php' );

// Main Debug Class
require_once( 'include/classes/debug.php' );

function wpmobi_create_object() {
	global $wpmobi;
	
	$wpmobi = new WPMobi;
	$wpmobi->initialize();			
	
	do_action( 'wpmobi_loaded' );
}

add_action( 'plugins_loaded', 'wpmobi_create_object' );

/*! \mainpage MobileView Documentation
 *
 * \section intro_sec Introduction
 *
 * This documentation is auto-generated from the MobileView code-base, and is refreshed periodically throughout the day.  This documentation
 * focuses exclusively on the WPMobi code, detailing the usage of most of the functions as well as the parameters required.
 *
 * \section intro_index Documentation
 *
 * You can browse the available documentation sections using the sidebar on the right.
 *
 */

?>