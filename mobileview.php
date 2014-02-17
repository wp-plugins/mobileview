<?php
/*
Plugin Name: MobileView
Plugin URI: http://colorlabsproject.com/plugins/mobileview/
Version: 1.4.3
Description: MobileView is a free wordpress plugin to transform your wordpress site into mobile-friendly theme for various devices including iPhone, Android, Blackberry and others.
Author: ColorLabs & Company
Text Domain: mobileviewlang
Domain Path: /lang
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html

# All admin and theme(s) Designs / Images / CSS
# are Copyright 2013 ColorLabs & Company.
# and cannot be re-used in conjuction with the GPL v2 usage of this software
# under the license terms of the GPL v2 without permission.
# 
# You may find MOBILEVIEW variable in MobileView Plugins file. It was actually variable name for MobileView.
# MobileView => MobileView
*/

global $mobileview;

// Should not have spaces in it, same as above
define( 'MOBILEVIEW_VERSION', '1.4.3' );

// Configuration
require_once( 'includes/config.php' );

// Load settings
require_once( 'includes/settings.php' );

// Load global functions
require_once( 'includes/globals.php' );

// Load array iterator, used everywhere
require_once( 'includes/classes/array-iterator.php' );

// Main MobileView Class
require_once( 'includes/classes/mobileviewlang.php' );

// Main Debug Class
require_once( 'includes/classes/debug.php' );

function mobileview_create_object() {
	global $mobileview;
	
	$mobileview = new MobileView;
	$mobileview->initialize();			
	
	do_action( 'mobileview_loaded' );
}

add_action( 'plugins_loaded', 'mobileview_create_object' );


?>