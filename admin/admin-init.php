<?php

function mobileview_admin_menu() {
	
		if ( !defined( 'MOBILEVIEW_MENU' ) ) {
			define( 'MOBILEVIEW_MENU', true );
			
			// Add the main plugin menu for MobileView 
			add_menu_page( 
				'MobileView', 
				'MobileView', 
				'manage_options', 
				__FILE__, 
				'mobileview_admin_panel', 
				get_mobileview_url() . '/admin/images/mobileview-admin-icon.png' 
			);
		}
		
}

function mobileview_admin_panel() {	
	/* Administration panel bootstrap */
	require_once( MOBILEVIEW_ADMIN_DIR.'/themes.php' );
	require_once( MOBILEVIEW_ADMIN_DIR.'/tabs-function.php' );
	mobileview_setup_tabs();
	mobileview_generate_tabs();
}

//! Can be used to add a tab to the settings panel
function mobileview_add_tab( $tab_name, $class_name, $settings, $custom_page = false, $icon_url = '') {
	global $mobileview;
	
    if(empty($icon_url)) $icon_url = 'cog.png'; //MOBILEVIEW_URL . '/admin/images/cog.png';
    
	$mobileview->tabs[ $tab_name ] = array(
		'page' => $custom_page,
		'settings' => $settings,
		'class_name' => $class_name,
        'icon_url' => $icon_url
	);
}

function mobileview_generate_tabs() {
	include( 'admin-interface.php' );
}

function mobileview_string_to_class( $string ) {
	return strtolower( str_replace( '--', '-', str_replace( '+', '', str_replace( ' ', '-', $string ) ) ) );
}	

function mobileview_show_tab_settings() {
	include( 'tabs.php' );
}

function mobileview_admin_get_languages() {
	$languages = array(
		'auto' => __( 'Auto-detect', 'mobileviewlang' ),
		'en_US' => 'English',
		'fr_FR' => 'Français',
		'it_IT' => 'Italiano',
		'es_ES' => 'Español',
		'de_DE' => 'Deutsch',
		'nb_NO' => 'Norsk',
		'pt_BR' => 'Português',
		'nl_NL' => 'Nederlands',
		'sv_SE' => 'Svenska',
		'ru_RU' => 'русский',
		'ja_JP' => '日本語',
		'zh_CN' => '简体字',
		'hu_HU' => 'Magyar'
	);	
	
	return apply_filters( 'mobileview_admin_languages', $languages );
}

function mobileview_save_reset_notice() {
	if ( isset( $_POST[ 'mobileview-submit' ] ) ) {
		echo( '<div class="saved">' );
		echo __( 'Settings saved!', "mobileviewlang" );
		echo('</div>');
	} elseif ( isset( $_POST[ 'mobileview-submit-reset' ] ) ) {
		echo ( '<div class="reset">' );
		echo __( 'Defaults restored', "mobileviewlang" );
		echo( '</div>' );
	}
}

function mobileview_get_available_theme_variants() {
	$variants = array( 'iphone' => __( 'Mobile', 'mobileviewlang' ) );
	
	global $mobileview;
	$available_classes = $mobileview->get_supported_theme_device_classes();
	foreach( $available_classes as $device_class => $device_info ) {
		if ( !isset( $variants[ $device_class ] ) ) {
			$variants[ $device_class ] = $device_class;	
		}	
	}
	
	if ( isset( $variants[ 'ipad' ] ) ) {
		$variants[ 'ipad' ] = __( 'iPad', 'mobileviewlang' );	
	}
	
	return apply_filters( 'mobileview_developer_mode_theme_variants', $variants );
}

function mobileview_setup_general_tab() {
	global $mobileview;
	$settings = $mobileview->get_settings();
	
	$active_plugins = get_option( 'active_plugins' );
	$new_plugin_list = array();
	foreach( $active_plugins as $plugin ) {
		$dir = explode( '/', $plugin );
		$new_plugin_list[] = $dir[0];
	}

	$plugin_compat_settings = array();
	
	$plugin_compat_settings[] = array( 'section-start', 'warnings-and-conflicts', __( 'Warnings or Conflicts', 'mobileviewlang' ) );
	$plugin_compat_settings[] = array( 'plugin-compat' );
	$plugin_compat_settings[] = array( 'section-end' );	
	$plugin_compat_settings[] = array( 'spacer' );		
	$plugin_compat_settings[] = array( 'section-start', 'plugin-compat-options', __( 'Theme &amp; Page Compatibility', 'mobileviewlang' ) );
	$plugin_compat_settings[] = array( 'checkbox', 'include_functions_from_desktop_theme', __( 'Include functions.php from the active desktop theme', 'mobileviewlang' ), __( 'This option will include and load the functions.php from the active WordPress theme.  This may be required for themes with custom field features like post images, etc.', 'mobileviewlang' ) );
	$plugin_compat_settings[] = array( 
		'list', 
		'functions_php_inclusion_method', 
		__( 'Method to use when included functions.php', 'mobileviewlang' ), 
		__( 'The direct method loads functions.php directly, and is the recommended option.  If that method fails, an alternative method is provided which attempts to clean up the functions.php for inclusion - this method requires write access to your desktop theme directory and will cause hidden files to be written there.' , 'mobileviewlang' ),
		array( 
			'direct' => __( 'Include file directly', 'mobileviewlang' ), 
			'translate' => __( 'Translate constants and create new files', 'mobileviewlang' )
		) 
	);	
	$plugin_compat_settings[] = array( 'checkbox', 'convert_menu_links_to_internal', __( 'Convert permalinks into internal URLs', 'mobileviewlang' ), __( 'This option reduces the loading time for pages, but may cause issues with the menu when permalinks are non-standard or on another domain.', 'mobileviewlang' ) );
	$plugin_compat_settings[] = array( 'text', 'remove_shortcodes', __( 'Remove these shortcodes when MobileView is active', 'mobileviewlang' ), __( 'Enter a comma separated list of shortcodes to remove.', 'mobileviewlang' ) );
	$plugin_compat_settings[] = array( 'spacer' );
	$plugin_compat_settings[] = array( 'textarea', 'ignore_urls', __( 'Do not use MobileView on these URLs/Pages', 'mobileviewlang' ), __( 'Each permalink URL fragment should be on its own line and relative, e.g. "/about" or "/products/store"', 'mobileviewlang' ) );
	$plugin_compat_settings[] = array( 'checkbox', 'enable_buddypress_mobile_support', __( 'Allow BuddyPress Mobile AJAX to bypass MobileView', 'mobileviewlang' ), '' );	
	$plugin_compat_settings[] = array( 'section-end' );
	$plugin_compat_settings[] = array( 'spacer' );		
	$plugin_compat_settings[] = array( 'section-start', 'plugin-compatibility', __( 'Plugin Compatibility', 'mobileviewlang' ) );
		
	if ( $mobileview->plugin_hooks && count( $mobileview->plugin_hooks ) ) {
		
		$plugin_compat_settings[] = array( 'copytext', 'plugin-compat-copy', __( "MobileView will attempt to disable selected plugin hooks when MobileView and your mobile theme are active. Check plugins to disable:", "mobileviewlang" ) ); 
				
		foreach( $mobileview->plugin_hooks as $plugin_name => $hooks ) {
			if ( in_array( $plugin_name, $new_plugin_list ) ) {
				$proper_name = "plugin_disable_" . str_replace( '-', '_', $plugin_name );
				$plugin_compat_settings[] = array( 'checkbox', $proper_name, $mobileview->get_friendly_plugin_name( $plugin_name ) );
			}
		}
	} else {
		$plugin_compat_settings[] = array( 'copytext', 'plugin-compat-copy-none', __( "There are currently no active plugins to disable.", "mobileviewlang" ) .  "<br />" . __( "If you have recently installed or reset MobileView, it must gather active plugin information first.", "mobileviewlang" ) ); 
	}
		
	$plugin_compat_settings[] = array( 'copytext', 'plugin-compat-refresh', sprintf( __( "%sRegenerate Plugin List%s", "mobileviewlang" ), '<a href="#" class="regenerate-plugin-list round-24">', ' &raquo;</a>' ) ); 
	$plugin_compat_settings[] = array( 'section-end' );	
	
	
	mobileview_add_tab( __( 'General', 'mobileviewlang' ), 'general',
		array(
			__( 'Global General', 'mobileviewlang' ) => array ( 'general-options', 
				array(
					array( 'section-start', 'site-branding', __( 'Site Branding', 'mobileviewlang' ) ),
					array( 'text', 'site_title', __( 'MobileView site title', 'mobileviewlang' ), __( 'If the title of your site is long, you can shorten it for display within MobileView.', 'mobileviewlang' ) ),		
					array( 'checkbox', 'show_mobileview_in_footer', __( 'Display "Powered by MobileView" in footer', 'mobileviewlang' ) ),						
					array( 'section-end' ),
					array( 'spacer' ),
					array( 'section-start', 'general-menu-options', __( 'Menu Settings', 'mobileviewlang' ) ),
					array( 
						'list', 
						'custom_menu_name', 
						__( 'MobileView Menu', 'mobileviewlang' ), 
						'', 
						mobileview_get_custom_menu_list()
					),				
					array( 'section-end' ),
					array( 'spacer' ),
					array( 'section-start', 'landing-page', __( 'MobileView Landing Page', 'mobileviewlang' ) ),
					array( 'checkbox', 'enable_home_page_redirect', __( 'Enable landing redirect (overrides default WordPress settings for landing page)', 'mobileviewlang' ), __( 'When checked MobileView overrides your WordPress homepage settings, and uses another page you select for its homepage.', 'mobileviewlang' ) ),
					array( 'redirect' ),
					array( 'text', 'home_page_redirect_custom', __( 'Custom home page URL', 'mobileviewlang' ), '' ),
					array( 'section-end' ),
					array( 'spacer' ),			
					array( 'section-start', 'switch-link', __( 'Switch Link', 'mobileviewlang' ) ),
					array( 'checkbox', 'show_switch_link', __( 'Show switch link', 'mobileviewlang' ), __( 'When unchecked MobileView will not show a switch link allowing users to switch between the MobileView and your regular theme view', 'mobileviewlang' ) ),
					array( 
						'list', 
						'home_page_redirect_address', 
						__( 'Switch link destination', 'mobileviewlang' ), 
						__( 'Choose between the same URL from which a user chooses to switch, or your Homepage as the switch link destination.', 'mobileviewlang' ), 
						array(
							'same' => __( 'Same URL', 'mobileviewlang'),
							'homepage' => __( 'Site Homepage', 'mobileviewlang')
						)
					),
					array( 'textarea', 'desktop_switch_css', __( 'Theme switch styling', 'mobileviewlang' ), __( 'Here you can edit the CSS output to style the switch link appearance in the footer of your regular theme.', 'mobileviewlang' ) ),	
					array( 'section-end' ),
					array( 'spacer' ),			
					array( 'section-start', 'welcome-four-footer', __( 'Notification, 404, Footer', 'mobileviewlang' ) ),
					array( 'textarea', 'welcome_alert', __( 'Notification bar shown (HTML is OK)', 'mobileviewlang' ), __( 'The notification bar shows below the header for visitors until dismissed.', 'mobileviewlang' ) ),
					array( 'textarea', 'fourohfour_message', __( 'Custom 404 message (HTML is OK)', 'mobileviewlang' ), __( 'Change this to whatever you\'d like for your 404 page message.', 'mobileviewlang' ) ),
					array( 'textarea', 'footer_message', __( 'Custom footer content (HTML is OK)', 'mobileviewlang' ), __( 'Enter additional content to be displayed in the MobileView footer. Everything here is wrapped in a paragraph tag.', 'mobileviewlang' ) ),
					array( 'section-end' ),
					array( 'spacer' ),
					array( 'section-start', 'misc', __( 'Advanced', 'mobileviewlang' ) ),
					array( 'checkbox', 'desktop_is_first_view', __( '1st time visitors see desktop theme', 'mobileviewlang' ), __( 'Your regular theme will be shown to 1st time mobile visitors first, with the MobileView switch link available in the footer.', 'mobileviewlang' ) ),
//					array( 'checkbox', 'multisite_force_enable', __( 'Force multisite detection', 'mobileviewlang' ), __( 'This option will force  the WordPress multisite panels to be displayed. This option should only be used on an actual multisite installation.', 'mobileviewlang' ) ),					
					array( 'checkbox', 'make_links_clickable', __( 'Convert all plain-text links in post content to clickable links', 'mobileviewlang' ), __( 'Normally links posted into post content are plain-text and cannot be clicked.  Enabling this option will make these links clickable, similar to the P2 theme.', 'mobileviewlang' ) ),	
					array( 'checkbox', 'respect_wordpress_date_format', __( 'Respect WordPress setting for date format in themes', 'mobileviewlang' ), __( 'When checked MobileView will use the WordPress date format in themes that support it (set in WordPress -> Settings - > General).', 'mobileviewlang' ) ),
					array( 'text', 'custom_css_file', __( 'URL to a custom CSS file', 'mobileviewlang' ), __( 'Full URL to a custom CSS file to be loaded last in themes. Will override existing styles, preserving updateability of themes.', 'mobileviewlang' ) ),	
					array( 'section-end' ),
					array( 'spacer' ),
					array( 'section-start', 'misc', __( 'Switch Colour', 'mobileviewlang' ) ),
					array( 'colorpicker', 'switch_colour', __( 'Select color for switcher button', 'mobileviewlang' ), __( 'Default "#4FD065" ', 'mobileviewlang' ) ),
					array( 'section-end' )
				)
			),
			__( 'Compatibility', 'mobileviewlang' ) => array( 'compatibility',
				$plugin_compat_settings
			),
			__( 'Tools and Debug', 'mobileviewlang' ) => array ( 'tools-and-debug',
				array(
					array( 'section-start', 'tools-and-development', __( 'General', 'mobileviewlang' ) ),
					array( 'checkbox', 'show_footer_load_times', __( 'Show load times and query counts in the footer', 'mobileviewlang' ), __( 'MobileView will show the load time and query count to help you find slow pages/posts on your site.', 'mobileviewlang' ) ),
					array( 'checkbox', 'always_refresh_css_js_files', __( 'Always refresh theme JS and CSS files', 'mobileviewlang' ), __( 'Useful when developing. Will make sure MobileView browser cache of Javascript and CSS files is updated on every page refresh.', 'mobileviewlang' ) ),
//					array( 'checkbox', 'put_mobileview_in_appearance_menu', __( 'Move MobileView admin settings to Appearance menu', 'mobileviewlang' ),  __( 'Moves MobileView admin settings from the top-level to the WordPress Appearance settings. Refresh your browser after saving.', 'mobileviewlang' ) ),
					array(
						'list', 
						'developer_mode', 
						__( 'Developer mode', 'mobileviewlang' ),
						__( 'Shows MobileView in ALL browsers when enabled. Please remember to disable this option when finished!', 'mobileviewlang' ),
						array(
							'off' => __( 'Disabled', 'mobileviewlang' ),
							'admins' => __( 'Enabled for admins only', 'mobileviewlang' ),
							'on' => __( 'Enabled for all users', 'mobileviewlang' )
						)
					),
					array( 'list', 'developer_mode_device_class', __( '&harr; Developer Mode for', 'mobileviewlang' ), '', mobileview_get_available_theme_variants() ),
					array( 'section-end' ),
					array( 'spacer' ),			
					array( 'section-start', 'debugging', __( 'Debugging', 'mobileviewlang' ) ),
					array( 'sysinfo' ),				
					array( 'checkbox', 'debug_log', __( 'Debug log', 'mobileviewlang' ), __( 'Creates a debug file to help diagnose issues with MobileView. This file is located in ...wp-content/mobileview-data/debug. ', 'mobileviewlang' ) ),	
					array( 
						'list', 
						'debug_log_level', 
						__( 'Debug log level', 'mobileviewlang' ), 
						__( 'Increasing this above Level 1 (Errors) should only be done when troubleshooting.', 'mobileviewlang' ), 
						array(
							MOBILEVIEW_ERROR => __( 'Errors (1)', 'mobileviewlang' ),
							MOBILEVIEW_SECURITY => __( 'Security (2)', 'mobileviewlang' ),
							MOBILEVIEW_WARNING => __( 'Warnings (3)','mobileviewlang' ),
							MOBILEVIEW_INFO => __( 'Information (4)','mobileviewlang' ),
							MOBILEVIEW_VERBOSE => __( 'Verbose (5)','mobileviewlang' ),
						)	
					),				
					array( 'section-end' )
				)
			),
			__( 'Backup/Import', 'mobileviewlang' ) => array( 'backup-restore' ,
				array(
					array( 'section-start', 'site_backup_restore', __( 'Settings Backup and Import', 'mobileviewlang' ) ),
					array( 
						'list', 
						'backup_or_restore', 
						__( '&harr; On this site I want to', 'mobileviewlang' ), 
						'', 
						array(
							'backup' => __( 'Backup Settings', 'mobileviewlang' ),
							'restore' => __( 'Import Settings', 'mobileviewlang' )	
						)
					),
					array( 'section-end' ),
					array( 'spacer' ),			
					array( 'section-start', 'backup', __( 'Backup', 'mobileviewlang' ) ),
					array( 'copytext', 'backup-instructions', __( 'This key represents a backup of all MobileView settings.<br />You can cut and paste it into another installation, or save the data to restore at a later time.', 'mobileviewlang' ) ),
					array( 'backup' ),
					array( 'copytext', 'backup-copy-all', sprintf( __( '%sCopy Backup Key To Clipboard%s', 'mobileviewlang' ), '<a id="copy-text-button" class="ajax-button">', '</a>' ) ),
					array( 'copytext', 'backup-instructions-2', sprintf( __( '%sNOTE: A settings backup/restore does NOT include saved files, icons or themes inside the "wp-content/mobileview-data/" directory.%s', 'mobileviewlang' ), '<small>', '</small>' ) ),
					array( 'section-end' ),
					array( 'section-start', 'import', __( 'Import', 'mobileviewlang' ) ),
					array( 'restore', 'restore_string', sprintf( __( 'Paste a backup key, then save: %s(Right click in textarea, choose "Paste")%s', 'mobileviewlang' ), '<small>', '</small>') ),
					array( 'section-end' )
				)
			)
		), false, 'general.png'
	);
}

function mobileview_setup_theme_browser_tab() {
	global $mobileview;	
	$settings = mobileview_get_settings();
	
	if ( !$settings->admin_client_mode_hide_browser ) {
		mobileview_add_tab( __( 'Skins', 'mobileviewlang' ), 'theme-browser', 
			array(
				__( 'Skin Browser', 'mobileviewlang' ) => array ( 'installed-themes',
					array(
						array( 'section-start', 'installed-themes', '&nbsp;' ),
						array( 'theme-browser' ),
						array( 'section-end' )
					)
				)
			), false, 'skins.png'
		);		
	}
	
	$theme_menu = apply_filters( 'mobileview_theme_menu', array() );
	
	$current_theme = $mobileview->get_current_theme_info();
	
	// Check for skins
	if ( isset( $current_theme->skins ) && count( $current_theme->skins ) ) {
		$skin_options = array( 'none' => __( 'None', 'mobileviewlang' ) );
		foreach( $current_theme->skins as $skin ) {
			$skin_options[ $skin->basename ] = $skin->name;	
		}
		
		$skin_menu =  array(
			__( 'Theme Skins', 'mobileviewlang' ) => array ( 'theme-skins',
				array(
					array( 'section-start', 'available-skins', __( 'Available Skins', 'mobileviewlang' ) ),
					array( 
						'list', 
						'current_theme_skin', 
						__( 'Skin Settings', 'mobileviewlang' ), 
						__( 'Skins are alternate stylesheets which change the look and feel of a theme.', 'mobileviewlang' ), 
						$skin_options
					),				
					array( 'section-end' )
				)
			)
		);
		
		$theme_menu = array_merge( $theme_menu, $skin_menu );
	}
	
	// Add the skins menu
	if ( $theme_menu ) {
		$settings = $mobileview->get_settings();
		
		mobileview_add_tab( __( "Skin Settings", 'mobileviewlang' ), 'custom_theme', $theme_menu, false, 'skin-settings.png' );
	}
}

function mobileview_get_custom_menu_list() {
	$custom_menu = array(
		'' => __( 'WordPress Pages', 'mobileviewlang' )
	);
	
	global $wpdb;
	$menus = $wpdb->get_results( "SELECT term_taxonomy_id,a.term_id,name FROM " . $wpdb->prefix . "term_taxonomy as a," . $wpdb->prefix . "terms as b WHERE a.taxonomy = 'nav_menu' AND a.term_id = b.term_id" );
	if ( $menus ) {
		foreach( $menus as $menu ) {
			$custom_menu[ $menu->term_taxonomy_id ] = $menu->name;	
		}	
	}
	
	return $custom_menu;
}	

function mobileview_setup_multisite_tab() {
	if ( mobileview_is_multisite_enabled() && mobileview_is_multisite_primary() ) {
		mobileview_add_tab( __( 'Multisite', 'mobileviewlang' ), 'multisite', 
			array(
				__( 'General', 'mobileviewlang' ) => array ( 'multisite-general',
					array(
						array( 'section-start', 'multisite-admin-panel', __( 'Secondary Admin Panels', 'mobileviewlang' ) ),
						array( 'checkbox', 'multisite_disable_theme_browser_tab', __( 'Disable Theme Browser tab', 'mobileviewlang' ) ), 
						array( 'checkbox', 'multisite_disable_overview_pane', __( 'Disable Overview pane', 'mobileviewlang' ) ),
						array( 'checkbox', 'multisite_disable_statistics_pane', __( 'Disable Statistics pane', 'mobileviewlang' ) ), 
						array( 'checkbox', 'multisite_disable_compat_pane', __( 'Disable Compatability pane', 'mobileviewlang' ) ), 
						array( 'checkbox', 'multisite_disable_debug_pane', __( 'Disable Tools and Debug pane', 'mobileviewlang' ) ), 
						array( 'checkbox', 'multisite_disable_backup_pane', __( 'Disable Backup/Import pane', 'mobileviewlang' ) ), 						
						array( 'section-end' )
					)
				),
				__( 'Inherited Settings', 'mobileviewlang' ) => array( 'multisite-inherited',
					array(
						array( 'section-start', 'multisite-inherit', __( 'Inherited Settings', 'mobileviewlang' ) ),
						array( 'checkbox', 'multisite_inherit_statistics', __( 'Inherit Statistics settings', 'mobileviewlang' ) ),
						array( 'checkbox', 'multisite_inherit_theme', __( 'Inherit active theme', 'mobileviewlang' ) ),
						array( 'checkbox', 'multisite_inherit_compat', __( 'Inherit compatability settings', 'mobileviewlang' ) ),
						array( 'section-end' )
					)
				)
			)
		);	
	}
}

function mobileview_setup_plugins() {
	global $mobileview;	
	$modules = $mobileview->get_modules();
	ksort( $modules );
	
	mobileview_add_tab( __( 'Modules', 'mobileviewlang' ), 'modules', $modules );	
}

function mobileview_setup_tabs() {
	global $mobileview;
	$settings = $mobileview->get_settings();
		
	mobileview_setup_general_tab();	
	
	if ( $mobileview->has_modules() ) {
		mobileview_setup_plugins();
	}	
		
	do_action( 'mobileview_admin_tab' );

	mobileview_setup_theme_browser_tab();
	
	$mobileview->save_settings( $settings );
	
	do_action( 'mobileview_later_admin_tabs' );
}
