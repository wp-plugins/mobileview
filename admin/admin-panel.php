<?php

function wpmobi_admin_menu() {
	$settings = wpmobi_get_settings();
	
	if ( $settings->put_wpmobi_in_appearance_menu ) {
		add_submenu_page( 
			'themes.php', 
			__( "MobileView", "wpmobi-me" ), 
			__( "MobileView", "wpmobi-me" ), 
			'manage_options', 
			__FILE__, 
			'wpmobi_admin_panel' 
		);	
	} else {
		// Check to see if another plugin created the ColorLabs menu
		if ( !defined( 'WPMOBI_MENU' ) ) {
			define( 'WPMOBI_MENU', true );
			
			// Add the main plugin menu for MobileView 
			add_menu_page( 
				'MobileView', 
				'MobileView', 
				'manage_options', 
				__FILE__, 
				'', 
				get_wpmobi_url() . '/admin/images/wpmobi-admin-icon.png' 
			);
		}
		
		add_submenu_page( 
			__FILE__, 
			__( "Settings", "wpmobi-me" ), 
			__( "Settings", "wpmobi-me" ), 
			'manage_options', 
			__FILE__, 
			'wpmobi_admin_panel' 
		);	
	}
}

function wpmobi_admin_panel() {	
	/* Administration panel bootstrap */
	require_once( 'template-tags/themes.php' );
	require_once( 'template-tags/tabs.php' );
	
	// Setup administration tabs
	wpmobi_setup_tabs();
	
	// Generate tabs	
	wpmobi_generate_tabs();
}

//! Can be used to add a tab to the settings panel
function wpmobi_add_tab( $tab_name, $class_name, $settings, $custom_page = false ) {
	global $wpmobi;
	
	$wpmobi->tabs[ $tab_name ] = array(
		'page' => $custom_page,
		'settings' => $settings,
		'class_name' => $class_name
	);
}

function wpmobi_generate_tabs() {
	include( 'html/admin-form.php' );
}

function wpmobi_string_to_class( $string ) {
	return strtolower( str_replace( '--', '-', str_replace( '+', '', str_replace( ' ', '-', $string ) ) ) );
}	

function wpmobi_show_tab_settings() {
	include( 'html/tabs.php' );
}

function wpmobi_admin_get_languages() {
	$languages = array(
		'auto' => __( 'Auto-detect', 'wpmobi-me' ),
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
	
	return apply_filters( 'wpmobi_admin_languages', $languages );
}

function wpmobi_save_reset_notice() {
	if ( isset( $_POST[ 'wpmobi-submit' ] ) ) {
		echo( '<div class="saved">' );
		echo __( 'Settings saved!', "wpmobi-me" );
		echo('</div>');
	} elseif ( isset( $_POST[ 'wpmobi-submit-reset' ] ) ) {
		echo ( '<div class="reset">' );
		echo __( 'Defaults restored', "wpmobi-me" );
		echo( '</div>' );
	}
}

function wpmobi_get_available_theme_variants() {
	$variants = array( 'iphone' => __( 'Mobile', 'wpmobi-me' ) );
	
	global $wpmobi;
	$available_classes = $wpmobi->get_supported_theme_device_classes();
	foreach( $available_classes as $device_class => $device_info ) {
		if ( !isset( $variants[ $device_class ] ) ) {
			$variants[ $device_class ] = $device_class;	
		}	
	}
	
	if ( isset( $variants[ 'ipad' ] ) ) {
		$variants[ 'ipad' ] = __( 'iPad', 'wpmobi-me' );	
	}
	
	return apply_filters( 'wpmobi_developer_mode_theme_variants', $variants );
}

function wpmobi_setup_general_tab() {
	global $wpmobi;
	$settings = $wpmobi->get_settings();
	
	$active_plugins = get_option( 'active_plugins' );
	$new_plugin_list = array();
	foreach( $active_plugins as $plugin ) {
		$dir = explode( '/', $plugin );
		$new_plugin_list[] = $dir[0];
	}

	$plugin_compat_settings = array();
	
	$plugin_compat_settings[] = array( 'section-start', 'warnings-and-conflicts', __( 'Warnings or Conflicts', 'wpmobi-me' ) );
	$plugin_compat_settings[] = array( 'plugin-compat' );
	$plugin_compat_settings[] = array( 'section-end' );	
	$plugin_compat_settings[] = array( 'spacer' );		
	$plugin_compat_settings[] = array( 'section-start', 'plugin-compat-options', __( 'Theme &amp; Page Compatibility', 'wpmobi-me' ) );
	$plugin_compat_settings[] = array( 'checkbox', 'include_functions_from_desktop_theme', __( 'Include functions.php from the active desktop theme', 'wpmobi-me' ), __( 'This option will include and load the functions.php from the active WordPress theme.  This may be required for themes with custom field features like post images, etc.', 'wpmobi-me' ) );
	$plugin_compat_settings[] = array( 
		'list', 
		'functions_php_inclusion_method', 
		__( 'Method to use when included functions.php', 'wpmobi-me' ), 
		__( 'The direct method loads functions.php directly, and is the recommended option.  If that method fails, an alternative method is provided which attempts to clean up the functions.php for inclusion - this method requires write access to your desktop theme directory and will cause hidden files to be written there.' , 'wpmobi-me' ),
		array( 
			'direct' => __( 'Include file directly', 'wpmobi-me' ), 
			'translate' => __( 'Translate constants and create new files', 'wpmobi-me' )
		) 
	);	
	$plugin_compat_settings[] = array( 'checkbox', 'convert_menu_links_to_internal', __( 'Convert permalinks into internal URLs', 'wpmobi-me' ), __( 'This option reduces the loading time for pages, but may cause issues with the menu when permalinks are non-standard or on another domain.', 'wpmobi-me' ) );
	$plugin_compat_settings[] = array( 'text', 'remove_shortcodes', __( 'Remove these shortcodes when MobileView is active', 'wpmobi-me' ), __( 'Enter a comma separated list of shortcodes to remove.', 'wpmobi' ) );
	$plugin_compat_settings[] = array( 'spacer' );
	$plugin_compat_settings[] = array( 'textarea', 'ignore_urls', __( 'Do not use MobileView on these URLs/Pages', 'wpmobi-me' ), __( 'Each permalink URL fragment should be on its own line and relative, e.g. "/about" or "/products/store"', 'wpmobi-me' ) );
	$plugin_compat_settings[] = array( 'checkbox', 'enable_buddypress_mobile_support', __( 'Allow BuddyPress Mobile AJAX to bypass MobileView', 'wpmobi-me' ), '' );	
	$plugin_compat_settings[] = array( 'section-end' );
	$plugin_compat_settings[] = array( 'spacer' );		
	$plugin_compat_settings[] = array( 'section-start', 'plugin-compatibility', __( 'Plugin Compatibility', 'wpmobi-me' ) );
		
	if ( $wpmobi->plugin_hooks && count( $wpmobi->plugin_hooks ) ) {
		
		$plugin_compat_settings[] = array( 'copytext', 'plugin-compat-copy', __( "MobileView will attempt to disable selected plugin hooks when MobileView and your mobile theme are active. Check plugins to disable:", "wpmobi-me" ) ); 
				
		foreach( $wpmobi->plugin_hooks as $plugin_name => $hooks ) {
			if ( in_array( $plugin_name, $new_plugin_list ) ) {
				$proper_name = "plugin_disable_" . str_replace( '-', '_', $plugin_name );
				$plugin_compat_settings[] = array( 'checkbox', $proper_name, $wpmobi->get_friendly_plugin_name( $plugin_name ) );
			}
		}
	} else {
		$plugin_compat_settings[] = array( 'copytext', 'plugin-compat-copy-none', __( "There are currently no active plugins to disable.", "wpmobi-me" ) .  "<br />" . __( "If you have recently installed or reset MobileView, it must gather active plugin information first.", "wpmobi-me" ) ); 
	}
		
	$plugin_compat_settings[] = array( 'copytext', 'plugin-compat-refresh', sprintf( __( "%sRegenerate Plugin List%s", "wpmobi-me" ), '<a href="#" class="regenerate-plugin-list round-24">', ' &raquo;</a>' ) ); 
	$plugin_compat_settings[] = array( 'section-end' );	
	
	$wpmobi_advertising_types = array(
		'none' => __( 'No advertising', 'wpmobi-me' ),
		'google' => __( 'Google Adsense', 'wpmobi-me' ),
		'admob' => __( 'Admob Ads', 'wpmobi-me' ),
		'custom' => __( 'Custom', 'wpmobi-me' )
	);
	
	$wpmobi_advertising_types = apply_filters( 'wpmobi_advertising_types', $wpmobi_advertising_types );
	
	wpmobi_add_tab( __( 'General', 'wpmobi-me' ), 'general',
		array(
			__( 'Global General', 'wpmobi-me' ) => array ( 'general-options', 
				array(
					array( 'section-start', 'site-branding', __( 'Site Branding', 'wpmobi-me' ) ),
					array( 'text', 'site_title', __( 'MobileView site title', 'wpmobi-me' ), __( 'If the title of your site is long, you can shorten it for display within MobileView.', 'wpmobi-me' ) ),		
					array( 'checkbox', 'show_wpmobi_in_footer', __( 'Display "Powered by MobileView" in footer', 'wpmobi-me' ) ),						
					array( 'section-end' ),
					array( 'spacer' ),
					array( 'section-start', 'language-text', __( 'Regionalization', 'wpmobi-me' ) ),
					array( 
						'list', 
						'force_locale', 
						__( 'MobileView language', 'wpmobi-me' ), 
						__( 'The MobileView admin panel / supported themes will be shown in this locale', 'wpmobi-me' ), 
						wpmobi_admin_get_languages()
					),
					array( 'section-end' ),
					array( 'spacer' ),			
					array( 'section-start', 'landing-page', __( 'MobileView Landing Page', 'wpmobi-me' ) ),
					array( 'checkbox', 'enable_home_page_redirect', __( 'Enable landing redirect (overrides default WordPress settings for landing page)', 'wpmobi-me' ), __( 'When checked MobileView overrides your WordPress homepage settings, and uses another page you select for its homepage.', 'wpmobi-me' ) ),
					array( 'redirect' ),
					array( 'text', 'home_page_redirect_custom', __( 'Custom home page URL', 'wpmobi-me' ), '' ),
					array( 'section-end' ),
					array( 'spacer' ),			
					array( 'section-start', 'switch-link', __( 'Switch Link', 'wpmobi-me' ) ),
					array( 'checkbox', 'show_switch_link', __( 'Show switch link', 'wpmobi-me' ), __( 'When unchecked MobileView will not show a switch link allowing users to switch between the MobileView and your regular theme view', 'wpmobi-me' ) ),
					array( 
						'list', 
						'home_page_redirect_address', 
						__( 'Switch link destination', 'wpmobi-me' ), 
						__( 'Choose between the same URL from which a user chooses to switch, or your Homepage as the switch link destination.', 'wpmobi-me' ), 
						array(
							'same' => __( 'Same URL', 'wpmobi-me'),
							'homepage' => __( 'Site Homepage', 'wpmobi-me')
						)
					),
					array( 'textarea', 'desktop_switch_css', __( 'Theme switch styling', 'wpmobi-me' ), __( 'Here you can edit the CSS output to style the switch link appearance in the footer of your regular theme.', 'wpmobi-me' ) ),	
					array( 'section-end' ),
					array( 'spacer' ),			
					array( 'section-start', 'welcome-four-footer', __( 'Welcome, 404, Footer', 'wpmobi-me' ) ),
					array( 'textarea', 'welcome_alert', __( 'Welcome message shown on 1st visit (HTML is OK)', 'wpmobi-me' ), __( 'The welcome message shows below the header for visitors until dismissed.', 'wpmobi-me' ) ),
					array( 'textarea', 'fourohfour_message', __( 'Custom 404 message (HTML is OK)', 'wpmobi-me' ), __( 'Change this to whatever you\'d like for your 404 page message.', 'wpmobi-me' ) ),
					array( 'textarea', 'footer_message', __( 'Custom footer content (HTML is OK)', 'wpmobi-me' ), __( 'Enter additional content to be displayed in the MobileView footer. Everything here is wrapped in a paragraph tag.', 'wpmobi-me' ) ),
					array( 'section-end' ),
					array( 'spacer' ),
					array( 'section-start', 'misc', __( 'Advanced', 'wpmobi-me' ) ),
					array( 'checkbox', 'desktop_is_first_view', __( '1st time visitors see desktop theme', 'wpmobi-me' ), __( 'Your regular theme will be shown to 1st time mobile visitors first, with the MobileView switch link available in the footer.', 'wpmobi-me' ) ),
					array( 'checkbox', 'multisite_force_enable', __( 'Force multisite detection', 'wpmobi-me' ), __( 'This option will force  the WordPress multisite panels to be displayed. This option should only be used on an actual multisite installation.', 'wpmobi-me' ) ),					
					array( 'checkbox', 'make_links_clickable', __( 'Convert all plain-text links in post content to clickable links', 'wpmobi-me' ), __( 'Normally links posted into post content are plain-text and cannot be clicked.  Enabling this option will make these links clickable, similar to the P2 theme.', 'wpmobi-me' ) ),	
					array( 'checkbox', 'respect_wordpress_date_format', __( 'Respect WordPress setting for date format in themes', 'wpmobi-me' ), __( 'When checked MobileView will use the WordPress date format in themes that support it (set in WordPress -> Settings - > General).', 'wpmobi-me' ) ),
					array( 'text', 'custom_css_file', __( 'URL to a custom CSS file', 'wpmobi-me' ), __( 'Full URL to a custom CSS file to be loaded last in themes. Will override existing styles, preserving updateability of themes.', 'wpmobi-me' ) ),	
					array( 'section-end' )
				)
			),
			__( 'Compatibility', 'wpmobi-me' ) => array( 'compatibility',
				$plugin_compat_settings
			),
			__( 'Tools and Debug', 'wpmobi-me' ) => array ( 'tools-and-debug',
				array(
					array( 'section-start', 'tools-and-development', __( 'General', 'wpmobi-me' ) ),
					array( 'checkbox', 'show_footer_load_times', __( 'Show load times and query counts in the footer', 'wpmobi-me' ), __( 'MobileView will show the load time and query count to help you find slow pages/posts on your site.', 'wpmobi-me' ) ),
					array( 'checkbox', 'always_refresh_css_js_files', __( 'Always refresh theme JS and CSS files', 'wpmobi-me' ), __( 'Useful when developing. Will make sure MobileView browser cache of Javascript and CSS files is updated on every page refresh.', 'wpmobi-me' ) ),
					array( 'checkbox', 'put_wpmobi_in_appearance_menu', __( 'Move MobileView admin settings to Appearance menu', 'wpmobi-me' ),  __( 'Moves MobileView admin settings from the top-level to the WordPress Appearance settings. Refresh your browser after saving.', 'wpmobi-me' ) ),
					array(
						'list', 
						'developer_mode', 
						__( 'Developer mode', 'wpmobi-me' ),
						__( 'Shows MobileView in ALL browsers when enabled. Please remember to disable this option when finished!', 'wpmobi-me' ),
						array(
							'off' => __( 'Disabled', 'wpmobi-me' ),
							'admins' => __( 'Enabled for admins only', 'wpmobi-me' ),
							'on' => __( 'Enabled for all users', 'wpmobi-me' )
						)
					),
					array( 'list', 'developer_mode_device_class', __( '&harr; Developer Mode for', 'wpmobi-me' ), '', wpmobi_get_available_theme_variants() ),
					array( 'section-end' ),
					array( 'spacer' ),
					array( 'section-start', 'clientmode', __( 'Client Mode', 'wpmobi-me' ) ),
					array( 'checkbox', 'admin_client_mode_hide_licenses', __( 'Hide Licenses tab, and other license related content', 'wpmobi-me' ),  __( 'Hides all license settings and references. Allows client to see and upgrade the plugin, adjust active theme and global settings, but not see and/or change license and domain settings.', 'wpmobi-me' ) ),
					array( 'checkbox', 'admin_client_mode_hide_browser', __( 'Hide Theme Browser tab', 'wpmobi-me' ),  __( 'Hides the theme browser tab, and prevents theme switching', 'wpmobi-me' ) ),
					array( 'checkbox', 'admin_client_mode_hide_tools', __( 'Hide Tools and Debug section', 'wpmobi-me' ),  __( 'Hides the Tools and Debug settings completely. Once checked only resetting MobileView settings will show them again.', 'wpmobi-me' ) ),
					array( 'section-end' ),
					array( 'spacer' ),			
					array( 'section-start', 'debugging', __( 'Debugging', 'wpmobi-me' ) ),
					array( 'sysinfo' ),				
					array( 'checkbox', 'debug_log', __( 'Debug log', 'wpmobi-me' ), __( 'Creates a debug file to help diagnose issues with MobileView. This file is located in ...wp-content/wpmobi-data/debug. ', 'wpmobi-me' ) ),	
					array( 
						'list', 
						'debug_log_level', 
						__( 'Debug log level', 'wpmobi-me' ), 
						__( 'Increasing this above Level 1 (Errors) should only be done when troubleshooting.', 'wpmobi-me' ), 
						array(
							WPMOBI_ERROR => __( 'Errors (1)', 'wpmobi-me' ),
							WPMOBI_SECURITY => __( 'Security (2)', 'wpmobi-me' ),
							WPMOBI_WARNING => __( 'Warnings (3)','wpmobi-me' ),
							WPMOBI_INFO => __( 'Information (4)','wpmobi-me' ),
							WPMOBI_VERBOSE => __( 'Verbose (5)','wpmobi-me' ),
						)	
					),				
					array( 'section-end' )
				)
			),
			__( 'Backup/Import', 'wpmobi-me' ) => array( 'backup-restore' ,
				array(
					array( 'section-start', 'site_backup_restore', __( 'Settings Backup and Import', 'wpmobi-me' ) ),
					array( 
						'list', 
						'backup_or_restore', 
						__( '&harr; On this site I want to', 'wpmobi-me' ), 
						'', 
						array(
							'backup' => __( 'Backup Settings', 'wpmobi-me' ),
							'restore' => __( 'Import Settings', 'wpmobi-me' )	
						)
					),
					array( 'section-end' ),
					array( 'spacer' ),			
					array( 'section-start', 'backup', __( 'Backup', 'wpmobi-me' ) ),
					array( 'copytext', 'backup-instructions', __( 'This key represents a backup of all MobileView settings.<br />You can cut and paste it into another installation, or save the data to restore at a later time.', 'wpmobi-me' ) ),
					array( 'backup' ),
					array( 'copytext', 'backup-copy-all', sprintf( __( '%sCopy Backup Key To Clipboard%s', 'wpmobi-me' ), '<a id="copy-text-button" class="ajax-button">', '</a>' ) ),
					array( 'copytext', 'backup-instructions-2', sprintf( __( '%sNOTE: A settings backup/restore does NOT include saved files, icons or themes inside the "wp-content/wpmobi-data/" directory.%s', 'wpmobi-me' ), '<small>', '</small>' ) ),
					array( 'section-end' ),
					array( 'section-start', 'import', __( 'Import', 'wpmobi-me' ) ),
					array( 'restore', 'restore_string', sprintf( __( 'Paste a backup key, then save: %s(Right click in textarea, choose "Paste")%s', 'wpmobi-me' ), '<small>', '</small>') ),
					array( 'section-end' )
				)
			)
		)
	);
}

function wpmobi_setup_theme_browser_tab() {
	global $wpmobi;	
	$settings = wpmobi_get_settings();
	
	if ( !$settings->admin_client_mode_hide_browser ) {
		wpmobi_add_tab( __( 'Skins', 'wpmobi-me' ), 'theme-browser', 
			array(
				__( 'Skin Browser', 'wpmobi-me' ) => array ( 'installed-themes',
					array(
						array( 'section-start', 'installed-themes', '&nbsp;' ),
						array( 'theme-browser' ),
						array( 'section-end' )
					)
				)
			)
		);		
	}
	
	$theme_menu = apply_filters( 'wpmobi_theme_menu', array() );
	
	$current_theme = $wpmobi->get_current_theme_info();
	
	// Check for skins
	if ( isset( $current_theme->skins ) && count( $current_theme->skins ) ) {
		$skin_options = array( 'none' => __( 'None', 'wpmobi-me' ) );
		foreach( $current_theme->skins as $skin ) {
			$skin_options[ $skin->basename ] = $skin->name;	
		}
		
		$skin_menu =  array(
			__( 'Theme Skins', 'wpmobi-me' ) => array ( 'theme-skins',
				array(
					array( 'section-start', 'available-skins', __( 'Available Skins', 'wpmobi-me' ) ),
					array( 
						'list', 
						'current_theme_skin', 
						__( 'Active skin', 'wpmobi-me' ), 
						__( 'Skins are alternate stylesheets which change the look and feel of a theme.', 'wpmobi-me' ), 
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
		$settings = $wpmobi->get_settings();
		
		wpmobi_add_tab( __( "Active Skin", 'wpmobi-me' ), 'custom_theme', $theme_menu );
	}
}

function wpmobi_get_custom_menu_list() {
	$custom_menu = array(
		'none' => __( 'WordPress Pages', 'wpmobi-me' )
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

function wpmobi_setup_menu_icons_tab() {
	wpmobi_add_tab( __( 'Menu', 'wpmobi-me' ), 'menu_and_icons', 
		array(
			__( 'General Settings', 'wpmobi-me' ) => array( 'general-settings',
				array(
					array( 'section-start', 'general-menu-options', __( 'Drop-Down Menu', 'wpmobi-me' ) ),
					array( 
						'list', 
						'custom_menu_name', 
						__( 'MobileView Menu', 'wpmobi-me' ), 
						'', 
						wpmobi_get_custom_menu_list()
					),
					array( 
						'list', 
						'menu_sort_order', 
						__( 'Menu sort order (WordPress Pages menu only)', 'wpmobi-me' ), 
						__( 'Used to adjust the menu sort order for MobileView themes ', 'wpmobi-me' ), 
						array(
							'wordpress' => __( 'Sort by admin page order', 'wpmobi-me' ),
							'alpha' => __( 'Sort alphabetically', 'wpmobi-me' )
						) 
					),
					array( 'section-end' )	,
					array( 'spacer' ),
					array( 'section-start', 'additional-menu-items', __( 'Additional Menu Items', 'wpmobi-me' ) ),
					array( 'checkbox', 'menu_show_home', sprintf( __( 'Add a link to %sHome%s in the menu', 'wpmobi-me' ), '<strong>', '</strong>' ), '' ),	
					array( 'checkbox', 'menu_show_rss', sprintf( __( 'Add a link to the site %sRSS%s feed in the menu', 'wpmobi-me' ), '<strong>', '</strong>' ), '' ),	
					array( 'text', 'menu_custom_rss_url', __( '&harr; Use this RSS feed URL', 'wpmobi-me' ), __( 'You can enter a custom RSS URL here, such as a FeedBurner feed. When left blank, the default website RSS Feed is used.', 'wpmobi-me' ), '', 'menu_custom_rss_url', true ),		
					array( 'checkbox', 'menu_show_email', __( 'Add a link for the admin <strong>Email</strong> in the menu', 'wpmobi-me' ), '' ),
					array( 'text', 'menu_custom_email_address', __( '&harr; Use this e-mail address', 'wpmobi-me' ), __( 'You can enter a custom email address here. When left blank the default admin email is used.', 'wpmobi-me' ), '', 'menu_show_email', true ),					
					array( 'section-end' )	,
					array( 'spacer' ),
					array( 'section-start', 'advanced-menu-options', __( 'Advanced', 'wpmobi-me' ) ),
					array( 'checkbox', 'enable_menu_icons', __( 'Use menu icons', 'wpmobi-me' ), __( 'When unchecked no icons will be shown beside menu items, even if configured in Menu + Icons.', 'wpmobi-me' ) ),
					array( 'checkbox', 'glossy_bookmark_icon', __( 'Use glossy bookmark icon', 'wpmobi-me' ), __( 'If unchecked your bookmark icon will be set as "apple-touch-icon-precomposed", and not have the glossy effect applied to it.', 'wpmobi-me' ) ),
					array( 'checkbox', 'menu_disable_parent_as_child', __( 'Disable duplicate parent as 1st child link', 'wpmobi-me' ), __( 'Check this to disable showing each menu parent as a clickable child item. NOTE: Parent link will not be accessible with this option enabled.', 'wpmobi-me' ) ),		
					array( 'checkbox', 'disable_menu', __( 'Disable drop-down menu completely', 'wpmobi-me' ), __( 'Check this to disable the MobileView menus altogether (useful for custom themes with menus built outside our code that do not use  MobileView settings).', 'wpmobi-me' ) ),		
					array( 'checkbox', 'cache_menu_tree', __( 'Cache menu items to reduce database queries', 'wpmobi-me' ), '' ),
					array( 'text', 'cache_time', __( 'Number of seconds to cache menu tree items for', 'wpmobi-me' ), '' ),
					array( 'section-end' )		
				)			
			),		
			__( 'Custom Menu Items', 'wpmobi-me' ) => array( 'custom-menu-icons',
				array(
					array( 'section-start', 'custom-menu-items', __( 'Custom Menu Items', 'wpmobi-me' ) ),
					array( 'copytext', 'copytext-menu-items', __( 'You can enter up to 3 custom menu links to be shown in the MobileView header menu.', 'wpmobi-me' ) ),
					array( 'text', 'custom_menu_text_1', sprintf( __( 'Custom menu title %s', 'wpmobi-me' ), 1 ) ),				
					array( 'text', 'custom_menu_link_1', sprintf( __( 'Custom menu URL %s', 'wpmobi-me' ), 1 ) ),
					array( 
						'list', 
						'custom_menu_position_1', 
						sprintf( __( 'Custom menu position %s', 'wpmobi-me' ), 1 ), 
						__( 'Select whether the link is shown above or below the WP pages in your menu', 'wpmobi-me' ), 
						array( 
							'top' => __( 'Top', 'wpmobi-me' ), 
							'bottom' => __( 'Bottom', 'wpmobi-me' ) 
						) 
					),
					array( 'checkbox', 'custom_menu_force_external_1', __( 'Force URL to leave Web-App Mode', 'wpmobi-me' ), __( 'URL will be opened in Mobile Safari.  This feature is sometimes required for external links.', 'wpmobi-me' ) ),						
					array( 'spacer' ),
					array( 'text', 'custom_menu_text_2', sprintf( __( 'Custom menu title %s', 'wpmobi-me' ), 2 ) ),						
					array( 'text', 'custom_menu_link_2', sprintf( __( 'Custom menu URL %s', 'wpmobi-me' ), 2 ) ),
					array( 
						'list', 
						'custom_menu_position_2', 
						sprintf( __( 'Custom menu position %s', 'wpmobi-me' ), 2 ), 
						'', 
						array( 
							'top' => __( 'Top', 'wpmobi-me' ), 
							'bottom' => __( 'Bottom', 'wpmobi-me' ) 
						) 
					),
					array( 'checkbox', 'custom_menu_force_external_2', __( 'Force URL to leave Web-App Mode', 'wpmobi-me' ) ),						
					array( 'spacer' ),
					array( 'text', 'custom_menu_text_3', sprintf( __( 'Custom menu title %s', 'wpmobi-me' ), 3 ) ),						
					array( 'text', 'custom_menu_link_3', sprintf( __( 'Custom menu URL %s', 'wpmobi-me' ), 3 ) ),
					array( 
						'list', 
						'custom_menu_position_3', 
						sprintf( __( 'Custom menu position %s', 'wpmobi-me' ), 3 ), 
						'', 
						array( 
							'top' => __( 'Top', 'wpmobi-me' ), 
							'bottom' => __( 'Bottom', 'wpmobi-me' ) 
						) 
					),								
					array( 'checkbox', 'custom_menu_force_external_3', __( 'Force URL to leave Web-App Mode', 'wpmobi-me' ) ),						
					array( 'section-end' )		
				)	
			)					
		)
	);		
}

function wpmobi_setup_clcid_account_tab() {
	$support_panel = array(
		__( 'Account + Key', 'wpmobi-me' ) => array( 'clcid',
			array(	
				array( 'section-start', 'account-information', __( 'Account Information', 'wpmobi-me' ) ),
				array( 'ajax-div', 'wpmobi-mefile-ajax', 'profile' ),		
				array( 'text', 'clcid', __( 'Account E-Mail', 'wpmobi-me' ) ),			
				array( 'text', 'wpmobi_license_key', __( 'License Key', 'wpmobi-me' ) ),
				array( 'license-check', 'license-check' ),
				array( 'section-end' )
			)
		)
	);
	
	global $blog_id;
	$settings = wpmobi_get_settings();
	
}

function wpmobi_setup_multisite_tab() {
	if ( wpmobi_is_multisite_enabled() && wpmobi_is_multisite_primary() ) {
		wpmobi_add_tab( __( 'Multisite', 'wpmobi-me' ), 'multisite', 
			array(
				__( 'General', 'wpmobi-me' ) => array ( 'multisite-general',
					array(
						array( 'section-start', 'multisite-admin-panel', __( 'Secondary Admin Panels', 'wpmobi-me' ) ),
						array( 'checkbox', 'multisite_disable_theme_browser_tab', __( 'Disable Theme Browser tab', 'wpmobi-me' ) ), 
						array( 'checkbox', 'multisite_disable_push_notifications_pane', __( 'Disable Push Notifications pane', 'wpmobi-me' ) ),
						array( 'checkbox', 'multisite_disable_overview_pane', __( 'Disable Overview pane', 'wpmobi-me' ) ),
						array( 'checkbox', 'multisite_disable_advertising_pane', __( 'Disable Advertising pane', 'wpmobi-me' ) ),
						array( 'checkbox', 'multisite_disable_statistics_pane', __( 'Disable Statistics pane', 'wpmobi-me' ) ),
						array( 'checkbox', 'multisite_disable_manage_icons_pane', __( 'Disable Manage Icons pane', 'wpmobi-me' ) ), 
						array( 'checkbox', 'multisite_disable_compat_pane', __( 'Disable Compatability pane', 'wpmobi-me' ) ), 
						array( 'checkbox', 'multisite_disable_debug_pane', __( 'Disable Tools and Debug pane', 'wpmobi-me' ) ), 
						array( 'checkbox', 'multisite_disable_backup_pane', __( 'Disable Backup/Import pane', 'wpmobi-me' ) ), 						
						array( 'section-end' )
					)
				),
				__( 'Inherited Settings', 'wpmobi-me' ) => array( 'multisite-inherited',
					array(
						array( 'section-start', 'multisite-inherit', __( 'Inherited Settings', 'wpmobi-me' ) ),
						array( 'checkbox', 'multisite_inherit_advertising', __( 'Inherit advertising settings', 'wpmobi-me' ) ),
						array( 'checkbox', 'multisite_inherit_statistics', __( 'Inherit Statistics settings', 'wpmobi-me' ) ),
						array( 'checkbox', 'multisite_inherit_theme', __( 'Inherit active theme', 'wpmobi-me' ) ),
						array( 'checkbox', 'multisite_inherit_compat', __( 'Inherit compatability settings', 'wpmobi-me' ) ),
						array( 'section-end' )
					)
				)
			)
		);	
	}
}

function wpmobi_setup_plugins() {
	global $wpmobi;	
	$modules = $wpmobi->get_modules();
	ksort( $modules );
	
	wpmobi_add_tab( __( 'Modules', 'wpmobi-me' ), 'modules', $modules );	
}

function wpmobi_setup_tabs() {
	global $wpmobi;
	$settings = $wpmobi->get_settings();
		
	wpmobi_setup_general_tab();	
	
	if ( $wpmobi->has_modules() ) {
		wpmobi_setup_plugins();
	}	
		
	do_action( 'wpmobi_admin_tab' );
	
	wpmobi_setup_multisite_tab();	

	wpmobi_setup_theme_browser_tab();

	wpmobi_setup_menu_icons_tab();
	
	do_action( 'wpmobi_later_admin_tabs' );
}
