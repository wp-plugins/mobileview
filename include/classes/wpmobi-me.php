<?php

//!		\defgroup admin Administration Panel
//!		\defgroup advertising Advertising
//!		\defgroup clc ColorLabs API
//!		\defgroup compat Compatibility
//!		\defgroup debug Debugging
//!		\defgroup files	Files and Directories
//!		\defgroup wpmobiglobal Global
//!		\defgroup helpers Helpers
//!		\defgroup iconssets Icons and Sets
//!		\defgroup menus Menu Items
//!		\defgroup modules Modules and Add-ons
//!		\defgroup settings Storing and Retrieving Settings
//!		\defgroup templatetags Template tags 
//!		\defgroup internal WPMobi methods


class WPMobi {
	//! Contains the main settings object
	var $settings;
	
	//! Set to true when the user is surfing on a supported mobile device
	var $is_mobile_device;
	
	//! Set to true when WPMobi is showing a mobile theme
	var $showing_mobile_theme;
	
	//! Contains information about all the tabs in the administrative panel
	var $tabs;
	
	//! Contains information about the active user's mobile device
	var $active_device;
	
	//! Contains information about the active user's mobile device class
	var $active_device_class;
	
	//! A list of CSS files to be included in the css.  Can possibly be cached
	var $css_files;
	
	//! Contains information about the pre-menu in WPMobi
	var $pre_menu;
	
	//! Contains information about the pre-menu in WPMobi
	var $post_menu;
	
	//! Contains the CLC API object
	var $clc_api;
	
	//! Contains a list of installed modules
	var $modules;
	
	//! Used for updating the plugin via the WordPress update mechanism
	var $transient_set;
	
	//! Contains the version information while doing an update
	var $latest_version_info;
	
	//! Stores a debug log
	var $debug_log;
	
	//! Stores the current language locale
	var $locale;
	
	//! Stores a list of all custom WPMobi page templates
	var $custom_page_templates;
	
	//! Stores a hash map of icons to sets
	var $icon_to_set_map;
	
	//! Stores the post-processed POST variables
	var $post;
	
	//! Stores the post-processed GET variables
	var $get;
	
	//! Stores a list of all internal warnings
	var $warnings;
	
	//! A list of all the known plugin hooks
	var $plugin_hooks;
	
	//! Indicates whether or not we're executing a custom page template
	var $is_custom_page_template;
	
	//! The menu item for the custom page template
	var $custom_page_template_id;
	
	//! Keeps track of weather or not any directories were not created properly
	var $directory_creation_failure;
	
	//! Keeps track whether or not a settings restoration failed
	var $restore_failure;
	
    //! WPMOBI_ROOT_DIR/WPMOBI_ROOT_DIR.php
	var $plugin_name;
    
	function WPMobiPro() {
		$this->is_mobile_device = false;
		$this->showing_mobile_theme = false;
		$this->settings = false;
		$this->active_device = false;
		$this->active_device_class = false;
		$this->directory_creation_failure = false;
		
		$this->tabs = array();
		$this->css_files = array();
		
		$this->pre_menu = array();
		$this->post_menu = array();
		$this->modules = array();
		
		$this->debug_log = array();
		
		$this->transient_set = false;
		$this->latest_version_info = false;
		$this->is_admin = false;
		
		$this->locale = '';
		$this->custom_page_templates = array();
		$this->icon_to_set_map = false;
		
		$this->post = array();
		$this->get = array();
		
		$this->warnings = array();
		$this->plugin_hooks = array();
		
		$this->is_custom_page_template = false;
		$this->custom_page_template_id = WPMOBI_ICON_DEFAULT;
		
		$this->restore_failure = false;
        
        $this->plugin_name = WPMOBI_ROOT_DIR ."/".WPMOBI_ROOT_DIR.".php";
        //$this->plugin_name = WPMOBI_ROOT_DIR ."/wpmobi-me.php";
	}
	
	/*!		\brief Initializes the MobileView object
	 *
	 *		This method initializes the MobileView object.  It is meant to be called immediately after object creation.
	 *
	 *		\ingroup internal
	 */	 
	function initialize() {	
		if ( ( function_exists( 'piggy_should_be_shown' ) && piggy_should_be_shown() ) || defined( 'XMLRPC_REQUEST' ) || defined( 'APP_REQUEST'  ) ) {
			return false;
		}

		$this->check_directories();
					
		$this->load_modules();	
		
		$this->cleanup_post_and_get();

		$settings = $this->get_settings();
		
		if ( is_admin() ) {
			// Admin Panel Warnings
			require_once( WPMOBI_DIR . '/admin/template-tags/warnings.php' ); 
			
			// Administration Panel
			require_once( WPMOBI_DIR . '/admin/admin-panel.php' );		
			
			add_action( 'admin_menu', 'wpmobi_admin_menu' );	
			add_action( 'publish_page', array( &$this, 'invalidate_menu_tree_cache' ) );
//			add_action( 'admin_head', array( &$this, 'show_plugin_help_text' ) );
			
			add_action( 'wpmobi_settings_saved', array( &$this, 'create_settings_backup_file' ) );

		}

		// Set up debug log
		if ( $settings->debug_log ) {
			wpmobi_debug_enable( true );	
			wpmobi_debug_set_log_level( $settings->debug_log_level );
		}
		
		WPMOBI_DEBUG( WPMOBI_INFO, 'MobileView Initializations ' . WPMOBI_VERSION );			
				
		// These actions and filters are always loaded
		add_action( 'init', array( &$this, 'wpmobi_init' ) );			
		add_action( 'admin_init', array( &$this, 'initialize_admin_section' ) );
		add_action( 'admin_init', array( &$this, 'check_for_product_upgrade' ) );	
		add_action( 'admin_head', array( &$this, 'wpmobi_admin_head' ) );
		add_action( 'install_plugins_pre_plugin-information', array( &$this, 'show_plugin_info' ) );
		add_filter( 'wpmobi_available_icon_sets_post_sort', array( &$this, 'setup_custom_icons' ) );		
		add_filter( 'plugin_action_links', array( &$this, 'wpmobi_settings_link' ), 9, 2 );
		add_action( 'wp_ajax_wpmobi_client_ajax', array( &$this, 'handle_client_ajax' ) );
		add_action( 'wp_ajax_nopriv_wpmobi_client_ajax', array( &$this, 'handle_client_ajax' ) );
		add_action( 'wpmobi_settings_saved', array( &$this, 'check_for_restored_settings' ) );
		add_filter( 'wpmobi_admin_languages', array( &$this, 'setup_custom_languages' ) );
		add_action( 'wpmobi_pre_head', array( &$this, 'add_ignored_urls' ) );
		
		// iPad
		add_filter( 'wpmobi_supported_device_classes', array( &$this, 'setup_ipad_user_agents' ) );
		
		// WP Super Cache
		add_filter( 'cached_mobile_prefixes', array( &$this, 'filter_wp_super_cache_prefixes' ) );
		add_filter( 'cached_mobile_browsers', array( &$this, 'filter_wp_super_cache_browsers' ) );
		
		// ManageWP
		add_filter( 'mwp_premium_update_notification', array( &$this, 'mwp_update_notification' ) );
		add_filter( 'mwp_premium_perform_update', array( &$this, 'mwp_perform_update' ) );
		
		if ( wpmobi_is_multisite_secondary() ) {
			add_filter( 'wpmobi_default_settings', array( &$this, 'setup_inherited_multisite_settings' ) );
			add_action( 'wpmobi_later_admin_tabs', array( &$this, 'alter_admin_tabs_for_multisite' ) );
		}
		
		add_shortcode( 'wpmobi', array( &$this, 'handle_shortcode' ) );
					
		add_action( 'after_plugin_row_'.WPMOBI_ROOT_DIR.'/'.WPMOBI_ROOT_DIR.'.php', array( &$this, 'plugin_row' ) );			
				
		// Load root-functions always for now
		//if ( $this->wpmobi_is_wpmobi_page() || !is_admin()  ) {	
		if ( true ) {	
			$clear_settings = false;
			// Load the current theme functions.php, or the child root functions if it exists in WPMobi themes
			if ( file_exists( $this->get_current_theme_directory() . '/root-functions.php' ) ) {
				require_once( $this->get_current_theme_directory() . '/root-functions.php' );	
				
				// next time get_settings is called, the current theme defaults will be added in
				$clear_settings = true;
			}
			
			// Load the parent root-functions if it exists
			if ( $this->has_parent_theme() ) {
				$parent_info = $this->get_parent_theme_info();
				if ( file_exists( WP_CONTENT_DIR . $parent_info->location . '/root-functions.php' ) ) {
					require_once( WP_CONTENT_DIR . $parent_info->location . '/root-functions.php' );	
				}
				
				// next time get_settings is called, the current theme defaults will be added in
				$clear_settings = true;
			}
			
			// Load a custom functions.php file
			if ( file_exists( WPMOBI_BASE_CONTENT_DIR . '/functions.php' ) ) {
				require_once( WPMOBI_BASE_CONTENT_DIR . '/functions.php' );	
			}

			do_action( 'wpmobi_functions_loaded' );
			
			if ( $clear_settings ) {				
				// each theme can add it's own default settings, so we need to reset our internal settings object
				// so that the defaults will get merged in from the current theme
				$this->reload_settings();
			}
		}
				
		$this->check_user_agent();	

		if ( $settings->desktop_is_first_view && $this->is_mobile_device && !$this->showing_mobile_theme ) {
			add_action( 'wp_head', array( &$this, 'handle_desktop_redirect_for_webapp' ) );
		}	
		
		// Check if we're using a version of WordPress that supports themes
		if ( function_exists( 'add_theme_support' ) ) {
			add_theme_support( 'menus' );	
		}
	
		if ( $this->is_mobile_device && $this->showing_mobile_theme ) {		
			
			do_action( 'wpmobi_mobile_theme_showing' );

			// Remove the admin bar in MobileView themes for now
			if ( function_exists( 'show_admin_bar' ) ) {
				add_filter( 'show_admin_bar', '__return_false' );
			}

			// Theme functions
			require_once( WPMOBI_DIR . '/include/template-tags/theme.php' );	
			
			// Menu Tags
			require_once( WPMOBI_DIR . '/include/template-tags/menu.php' );
					
			// Compatibility
			require_once( WPMOBI_DIR . '/include/compat.php' );			
			
			add_action( 'wpmobi_functions_start', array( &$this, 'load_functions_file_for_desktop' ) );

			// These actions and filters are only loaded when WPMobi and a mobile theme are active	
			add_action( 'wp', array( &$this, 'check_for_redirect' ) );		
			add_filter( 'init', array( &$this, 'init_theme' ) );
			add_filter( 'excerpt_length', array( &$this, 'get_excerpt_length' ) );
			add_filter( 'excerpt_more', array( &$this, 'get_excerpt_more' ) );
		
			// New switch hooks
			add_filter( 'template_directory', array( &$this, 'get_template_directory' ) );
			add_filter( 'template_directory_uri', array( &$this, 'get_template_directory_uri' ) );
			add_filter( 'stylesheet_directory', array( &$this, 'get_stylesheet_directory' ) );
			add_filter( 'stylesheet_directory_uri', array( &$this, 'get_stylesheet_directory_uri' ) );
			
			add_action( 'wpmobi_post_head', array( &$this, 'add_mobile_header_info' ) );

			// This is used to add the RSS, email items, etc			
			add_filter( 'wpmobi_menu_items', array( &$this, 'add_static_menu_items' ) );
			
			if ( $settings->menu_disable_parent_as_child ) {
				add_filter( 'wpmobi_menu_items', array( &$this, 'remove_duplicate_menu_items' ) );
			}
			
			if ( $settings->make_links_clickable ) {
				add_filter( 'the_content', 'make_clickable' );	
			}
			
			// Check to see if we're a child theme so we can add the child stylesheet
			if ( $this->is_child_theme() ) {
				add_action( 'wpmobi_post_head', array( &$this, 'output_child_scripts' ), 999 );
			}
				
			if ( isset( $settings->remove_shortcodes ) && strlen( $settings->remove_shortcodes ) ) {
				$this->remove_shortcodes( $settings->remove_shortcodes );	
			}			
		}
		
		// Setup Post Thumbnails
		$create_thumbnails = apply_filters( 'wpmobi_create_thumbnails', $settings->post_thumbnails_enabled && function_exists( 'add_theme_support' ) );
		// Setup Post Thumbnails
		if ( $create_thumbnails ) {
			add_theme_support( 'post-thumbnails' );
			add_image_size( 'wpmobi-new-thumbnail', $settings->post_thumbnails_new_image_size, $settings->post_thumbnails_new_image_size, true );
            add_image_size( 'small-thumbnail', 100, 100, true );
            add_image_size( 'feat-thumbnail', 800, 0, true );
		}
		
		$this->custom_page_templates = apply_filters( 'wpmobi_custom_templates', $this->custom_page_templates );	
		
		if ( !$settings->has_migrated_icons ) {
			$this->check_old_version();
		
			$settings->has_migrated_icons = true;
			$this->save_settings( $settings );
		}
		
	}	
	
	function create_settings_backup_file() {
		$settings = wpmobi_get_settings();
		$backup_file = WPMOBI_CUSTOM_SETTINGS_DIRECTORY . '/' . time() . '-backup.txt';
		$backup_contents = wpmobi_get_encoded_backup_string( $settings );
		if ( $backup_contents ) {
			$f = fopen( $backup_file, 'w+t' );
			if ( $f ) {
				fwrite( $f, $backup_contents );
				fclose( $f );
			}
		}
		
		// Cleanup old backup files
		$all_backup_files = $this->get_files_in_directory( WPMOBI_CUSTOM_SETTINGS_DIRECTORY, '.txt' );
		if ( is_array( $all_backup_files ) && count( $all_backup_files ) > WPMOBI_PRO_MIN_BACKUP_FILES ) {
			$file_times = array();
			
			foreach( $all_backup_files as $one_file ) {
				$file_times[ filemtime( $one_file ) ] = $one_file;
			}
			
			// Sort in descending order 
			ksort( $file_times );
			$num_to_delete = count( $file_times ) - WPMOBI_PRO_MIN_BACKUP_FILES;
			
			if ( $num_to_delete ) {
				$files_to_delete = array_slice( $file_times, 0, $num_to_delete );
				
				// Delete all files
				foreach( $files_to_delete as $key => $delete_me ) {
					@unlink( $delete_me );
				}
			}
		}
	}
	
	function load_functions_file_for_desktop() {
		$settings = wpmobi_get_settings();
			
		// Check to see if we should include the functions.php file from the desktop theme
		if ( $settings->include_functions_from_desktop_theme ) {
			$desktop_theme_directory = get_theme_root() . '/'. get_template();	
			$desktop_functions_file = $desktop_theme_directory . '/functions.php';
			
			// Check to see if the theme has a functions.php file
			if ( file_exists( $desktop_functions_file ) ) {
				if ( $settings->functions_php_inclusion_method == 'translate' ) {
					require_once( $desktop_functions_file );
				} else {
					wpmobi_include_functions_file( $desktop_functions_file, dirname( $desktop_functions_file ), dirname( $desktop_functions_file ), 'require_once' );
				}
			}
		}		
	}
	
	function include_functions_file( $file_name, $template_path, $current_path ) {
		$path_info = pathinfo( $file_name );
		
		$original_name = $file_name;
		$file_name = $path_info['basename'];
		
		if ( !file_exists( $original_name ) ) {	
			$test_name = $current_path . '/' . $file_name;
			if ( !file_exists( $test_name ) ) {
				$test_name = ABSPATH . '/' . $file_name;
				if ( !file_exists( $test_name ) ) {
					$test_name = $current_path . '/' . $original_name;
					if ( !file_exists( $test_name ) ) {
						die( 'Unable to properly load functions.php from the desktop theme, problem with ' . $test_name );
					} else {
						$file_name = $test_name;
					}	
				} else {
					$file_name = $test_name;
				}
			} else {
				$file_name = $test_name;
			}
		} else {
			$file_name = $original_name;
		}	
						
		if ( strpos( $file_name, $template_path ) === FALSE ) {
			return;
		}
				
		$file_contents = trim( $this->load_file( $file_name ) );
		
		$already_included_list = array();
		
		// Replace certain files
		$replace_constants = array( 'TEMPLATEPATH', 'STYLESHEETPATH', 'get_template_directory()' );
		foreach( $replace_constants as $to_replace ) {
			$file_contents = str_replace( $to_replace, "'" . $template_path . "'", $file_contents );
		}			
		
		$file_contents = str_replace( ' bloginfo(', ' wpmobi_desktop_bloginfo(', $file_contents );
		$file_contents = str_replace( ' get_bloginfo(', ' wpmobi_get_desktop_bloginfo(', $file_contents );
		
		$include_params = array( 'include', 'include_once', 'require', 'require_once', 'locate_template' );
		foreach( $include_params as $include_param ) {
			$reg_ex = '#' . $include_param . ' *\((.*)\);#';
			if ( preg_match_all( $reg_ex, $file_contents, $match ) ) {
				for( $i = 0; $i < count( $match[0] ); $i++ ) {
					$statement_in_code_that_loads_file = $match[0][$i];
					
					$new_statement = str_replace( $include_param . ' (', $include_param . '(', $statement_in_code_that_loads_file );
					
					if ( $include_param == 'locate_template' ) {
						$new_statement = str_replace( $include_param . '(', 'wpmobi_locate_template(', $new_statement );
						
						$new_statement = str_replace( ');', ", '" . $template_path . "', '" . $current_path . "');", $new_statement );
						
						$file_contents = str_replace( $statement_in_code_that_loads_file, $new_statement, $file_contents );
					} else {
	
						$current_path = dirname( $file_name );
						$new_statement = str_replace( $include_param . '(', 'wpmobi_include_functions_file(', $new_statement );
	
						$new_statement = str_replace( ');', ", '" . $template_path . "', '" . $current_path . "', '" . $include_param . "');", $new_statement );
						
						$file_contents = str_replace( $statement_in_code_that_loads_file, $new_statement, $file_contents );
					}
				}
			}
		}			

		return $file_contents;
	}
	
	function load_and_expand_functions_file( $file_name, $template_path, $current_path, $count = 0 ) {			
		if ( !file_exists( $file_name ) ) {	
			$test_name = $current_path . '/' . $file_name;
			if ( !file_exists( $test_name ) ) {
				$test_name = ABSPATH . '/' . $file_name;
				if ( !file_exists( $test_name ) ) {
					die( 'Unable to properly load functions.php from the desktop theme, problem with ' . $test_name );
				} else {
					$file_name = $test_name;
				}
			} else {
				$file_name = $test_name;
			}
		}	
						
		if ( strpos( $file_name, $template_path ) === FALSE ) {
			return;
		}
				
		$file_contents = trim( $this->load_file( $file_name ) );
		
		// Strip PHP tags off
		if ( strpos( $file_contents, '<?php' ) == 0 ) {
			$file_contents = substr( $file_contents, 5, strlen( $file_contents ) );
		}
		
		if ( strpos( $file_contents, '?>' ) == ( strlen( $file_contents ) - 2 ) ) {
			$file_contents = substr( $file_contents, 0, strlen( $file_contents ) - 2 );
		}
		
		$already_included_list = array();
				
		$include_params = array( 'include', 'include_once', 'require', 'require_once' );
		foreach( $include_params as $include_param ) {
			$reg_ex = '#' . $include_param . ' *\((.*)\)#';
			if ( preg_match_all( $reg_ex, $file_contents, $match ) ) {
				for( $i = 0; $i < count( $match[0] ); $i++ ) {
					$file_to_include_directly = $match[1][$i];
					$statement_in_code_that_loads_file = $match[0][$i];
					echo $statement_in_code_that_loads_file . '<br />';
										
					$replace_constants = array( 'TEMPLATEPATH', 'STYLESHEETPATH' );
					foreach( $replace_constants as $to_replace ) {
						if ( strpos( $file_to_include_directly, $to_replace ) !== FALSE ) {
							$file_to_include_directly = str_replace( $to_replace, "'" . $template_path . "'", $file_to_include_directly );
						}
					}
					
					// This eval is deliberately here, and won't cause any issues
					$new_to_eval = '$my_string = ' . $file_to_include_directly . ";";	
					eval( $new_to_eval );				
				
					if ( strpos( $my_string, $template_path ) === FALSE ) {
						if ( strpos( $my_string, '/' ) !== FALSE ) {
							echo $my_string . ' ' . $template_path . '<br />';
							continue;
						}
					}
					
					if ( $my_string ) {
						$file_info = pathinfo( $my_string );
						$wpmobi_file_name = $file_info['dirname'] . '/.' . $file_info['basename'] . '.wpmobi';
						
						$create_cached_file = true;
						if ( file_exists( $wpmobi_file_name ) ) {
							$last_mod_time = filemtime( $wpmobi_file_name );
							if ( ( time() - $last_mod_time  ) < 1 ) {
								$create_cached_file = false;
							}
						}
						
						if ( $create_cached_file ) {
							$new_file_contents = $this->load_and_expand_functions_file( $my_string, $template_path, dirname( $file_name ), $count + 1 );
							if ( $new_file_contents ) {							
								$f = fopen( $wpmobi_file_name, 'w+t' );
								if ( $f ) {
									fwrite( $f, '<?php ' . $new_file_contents );
									fclose( $f );
								}
							}
						}
				
						$file_contents = str_replace( $statement_in_code_that_loads_file, $include_param . "('" . $wpmobi_file_name . "')", $file_contents );
					}
					
				}
			}
		}
		
		return $file_contents;
	}
	
	function check_for_product_upgrade() {
		$current_version = get_option( 'wpmobi_version', 0 );
		
		// Check to see if the version in the data
		if ( $current_version != WPMOBI_VERSION ) {
			// Execute wpmobi_upgrade action for plugins and themes to intercept
			// can be used for cleaning CSS caches, etc.
			do_action( 'wpmobi_upgrade' );
		
			// Store the new version in the database
			update_option( 'wpmobi_version', WPMOBI_VERSION );
		}
	}
	
//	function load_php_file_and_trap_output( $file_name ) {
//		ob_start();
//		require_once( $file_name );
//		$contents = ob_get_contents();
//		ob_end_clean();
//		
//		return $contents;	
//	}
//	
//	function show_plugin_help_text() {
//		if ( is_admin() && ( strpos( $_SERVER['REQUEST_URI'], WPMOBI_ROOT_DIR ) !== false ) ) {
//			$links = 'test';
//			$contents = $this->load_php_file_and_trap_output( WPMOBI_DIR . '/admin/html/help.php' );
//		    global $_registered_pages;
//		    if( !empty( $_registered_pages ) ) {
//		        foreach( array_keys( $_registered_pages ) as $hook ) {
//		            get_current_screen()->add_help_tab( $hook, $contents );
//		        }
//		    }
//	    }
//	}
	
	function invalidate_menu_tree_cache() {
		$settings = wpmobi_get_settings();
		
		if ( $settings->cache_menu_tree ) {
			require_once( WPMOBI_DIR . '/include/template-tags/menu.php' );
			wpmobi_menu_cache_flush();
		}
	}
	
	function nullify_shortcode( $params ) {
		return '';	
	}
	
	function remove_shortcodes( $shortcodes ) {
		$all_short_codes = explode( ',', str_replace( ', ', ',', $shortcodes ) );
		if ( $all_short_codes ) {
			foreach( $all_short_codes as $code ) {
				add_shortcode( $code, array( &$this, 'nullify_shortcode' ) );
			}
		}
	}
	
	function get_template_directory( $directory, $template = false, $root = false ) {
		$theme_info = $this->get_current_theme_info();
		
		if ( $this->has_parent_theme() ) {
			$parent_info = $this->get_parent_theme_info();
			
			return WP_CONTENT_DIR . $parent_info->location . '/' . apply_filters( 'wpmobi_parent_device_class', $this->get_active_device_class() );
		} else {
			return WP_CONTENT_DIR . $theme_info->location . '/' . $this->get_active_device_class();
		}
	}	
	
	function output_child_scripts() {
		if ( file_exists( STYLESHEETPATH . '/style.min.css' ) ) {
			echo "<link rel='stylesheet' type='text/css' media='screen' href='" . $this->get_stylesheet_directory_uri( false ) . "/style.min.css?ver=" . wpmobi_refreshed_files() . "'>\n";	
		}	
		else if ( file_exists( STYLESHEETPATH . '/style.css' ) ) {
			echo "<link rel='stylesheet' type='text/css' media='screen' href='" . $this->get_stylesheet_directory_uri( false ) . "/style.css?ver=" . wpmobi_refreshed_files() . "'>\n";	
		}	
	}

	function get_template_directory_uri( $directory, $template = false, $root = false ) {
		$theme_info = $this->get_current_theme_info();
		
		if ( $this->has_parent_theme() ) {
			$parent_info = $this->get_parent_theme_info();
			
			return clc_wpmobi_sslize( WP_CONTENT_URL . $parent_info->location . '/' . apply_filters( 'wpmobi_parent_device_class', $this->get_active_device_class() ) );
		} else {
			return clc_wpmobi_sslize( WP_CONTENT_URL . $theme_info->location . '/' . $this->get_active_device_class() );
		}
	}
	
	function get_stylesheet_directory( $directory, $template = false, $root = false ) {
		$theme_info = $this->get_current_theme_info();
		
		return WP_CONTENT_DIR . $theme_info->location . '/' . $this->get_active_device_class();
	}
	
	function get_stylesheet_directory_uri( $directory, $template = false, $root = false ) {
		$theme_info = $this->get_current_theme_info();
		
		return clc_wpmobi_sslize( WP_CONTENT_URL . $theme_info->location . '/' . $this->get_active_device_class() );
	}		
	
	function has_parent_theme() {
		$theme_info = $this->get_current_theme_info();
		
		return ( isset( $theme_info->parent_theme ) && strlen( $theme_info->parent_theme ) );
	}
	
	function is_child_theme() {
		return $this->has_parent_theme();
	}
	
	function get_parent_theme_info() {
		$theme_info = $this->get_current_theme_info();
		
		if ( $theme_info ) {
			$themes = $this->get_available_themes();
			if ( isset( $themes[ $theme_info->parent_theme ] ) ) {
				return $themes[ $theme_info->parent_theme ];
			}
		}
		
		return false;
	}
	
	function filter_wp_super_cache_prefixes( $prefixes ) {
		return $prefixes;
	}
	
	function filter_wp_super_cache_browsers( $browsers ) {
		$supported_agents = $this->get_supported_user_agents();
		foreach( $supported_agents as $agent ) {
			if ( !isset( $browsers[ $agent ] ) ) {
				$browsers[] = $agent;
			}
		}
		
		return $browsers;
	}
	
	function setup_ipad_user_agents( $user_agents ) {
		$settings = $this->get_settings();
		
		if ( $settings->ipad_support == 'full' ) {
			// check for existance of the iPad folder
			if ( file_exists( $this->get_current_theme_directory() . '/ipad' ) ) {
				$user_agents['ipad'] = array( 'iPad', 'Kindle' );	
			}
		}
		
		return $user_agents;
	}
	
	function theme_supports_ipad() {
		return file_exists( $this->get_current_theme_directory() . '/ipad' );
	}
	
//  	function setup_ipad() {
//  		$settings = $this->get_settings();
//  
//  		// Quick check for the iPad
//  		if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) !== false && !is_admin() ) {		
//  			switch( $settings->ipad_support ) {
//  				case 'partial':
//  					wp_enqueue_script( 'jquery' );
//  					wp_enqueue_script( 'wpmobi-ipad-bar', WPMOBI_IPAD_URL . '/ipad-bar.js', 'jquery' );
//  					wp_enqueue_style( 'wpmobi-ipad-bar', WPMOBI_IPAD_URL . '/ipad-bar.css' );					
//  					
//  					add_action( 'wp_footer', array( &$this, 'show_ipad_bar' ) );				
//  					break;				
//  				case 'full':
//  					wp_enqueue_script( 'jquery' );
//  					wp_enqueue_script( 'wpmobi-ipad-bar', WPMOBI_IPAD_URL . '/ipad-bar.js', 'jquery' );
//  					wp_enqueue_style( 'wpmobi-ipad-bar', WPMOBI_IPAD_URL . '/ipad-bar.css' );	
//  					break;	
//  			}			
//  		}
//  	}
//  	
//  	function show_ipad_bar() {
//  		include( WPMOBI_IPAD_DIR . '/ipad-bar.php' );
//  	}
	
	function setup_custom_languages( $languages ) {
		$custom_lang_files = $this->get_files_in_directory( WPMOBI_CUSTOM_LANG_DIRECTORY, '.mo' );
		
		if ( $custom_lang_files && count( $custom_lang_files ) ) {
			foreach( $custom_lang_files as $lang_file ) {
				$languages[ basename( $lang_file, '.mo' ) ] = basename( $lang_file, '.mo' );
			}	
		}
		
		return $languages;
	}
	
	function get_root_settings() {
		global $wpdb;
		$settings = false;
		
		$result = $wpdb->get_row( $wpdb->prepare( 'SELECT option_value FROM ' . $wpdb->base_prefix . 'options WHERE option_name = %s', 'mobile-view' ) );
		if ( $result ) {
			$primary_settings = @unserialize( $result->option_value );	
			if ( !is_array( $primary_settings ) ) {
				$primary_settings = unserialize( $primary_settings );
				
				return $primary_settings;
			}
		}	
		
		return $settings;	
	}
	
	function setup_inherited_multisite_settings( $settings ) {
		global $wpdb;
		
		$result = $wpdb->get_row( $wpdb->prepare( 'SELECT option_value FROM ' . $wpdb->base_prefix . 'options WHERE option_name = %s', 'mobile-view' ) );
		if ( $result ) {
			$primary_settings = @unserialize( $result->option_value );	
			if ( !is_array( $primary_settings ) ) {
				$primary_settings = unserialize( $primary_settings );
				
			}
			
			$settings->multisite_disable_overview_pane = $primary_settings->multisite_disable_overview_pane;
			$settings->multisite_disable_manage_icons_pane = $primary_settings->multisite_disable_manage_icons_pane;
			$settings->multisite_disable_compat_pane = $primary_settings->multisite_disable_compat_pane;
			$settings->multisite_disable_debug_pane = $primary_settings->multisite_disable_debug_pane;
			$settings->multisite_disable_backup_pane = $primary_settings->multisite_disable_backup_pane;
			$settings->multisite_disable_theme_browser_tab = $primary_settings->multisite_disable_theme_browser_tab;
			$settings->multisite_disable_push_notifications_pane = $primary_settings->multisite_disable_push_notifications_pane;
			$settings->multisite_disable_advertising_pane = $primary_settings->multisite_disable_advertising_pane;
			$settings->multisite_disable_statistics_pane = $primary_settings->multisite_disable_statistics_pane;
			
			if ( $primary_settings->multisite_inherit_advertising ) {
				$settings->advertising_type = $primary_settings->advertising_type;
				$settings->advertising_location = $primary_settings->advertising_location;	
				$settings->custom_advertising_code = $primary_settings->custom_advertising_code;				
				$settings->admob_publisher_id = $primary_settings->admob_publisher_id;		
				$settings->adsense_id = $primary_settings->adsense_id;
				$settings->adsense_channel = $primary_settings->adsense_channel;
				$settings->admob_id = $primary_settings->admob_id;
				$settings->admob_channel = $primary_settings->admob_channel;
				$settings->advertising_pages = $primary_settings->advertising_pages;				
			} else if ( $primary_settings->multisite_disable_advertising_pane ) {
				$defaults = new WPMobiDefaultSettings;
				
				$settings->advertising_type = $defaults->advertising_type;
				$settings->advertising_location = $defaults->advertising_location;	
				$settings->custom_advertising_code = $defaults->custom_advertising_code;				
				$settings->admob_publisher_id = $defaults->admob_publisher_id;		
				$settings->adsense_id = $defaults->adsense_id;
				$settings->adsense_channel = $defaults->adsense_channel;
				$settings->admob_id = $defaults->admob_id;
				$settings->admob_channel = $defaults->admob_channel;
				$settings->advertising_pages = $defaults->advertising_pages;					
			}
			
			if ( $primary_settings->multisite_disable_push_notifications_pane ) {
				$defaults = new WPMobiDefaultSettings;

			}
			
			if ( $primary_settings->multisite_inherit_statistics ) {
				$settings->custom_stats_code = $primary_settings->custom_stats_code;
			} else if ( $primary_settings->multisite_disable_statistics_pane ) {
				$defaults = new WPMobiDefaultSettings;	
				
				$settings->custom_stats_code = $defaults->custom_stats_code;			
			}
			
			if ( $primary_settings->multisite_inherit_theme ) {
				$settings->current_theme_friendly_name = $primary_settings->current_theme_friendly_name;
				$settings->current_theme_location = $primary_settings->current_theme_location;
				$settings->current_theme_name = $primary_settings->current_theme_name;
				$settings->current_theme_skin = $primary_settings->current_theme_skin;
			} else if ( $primary_settings->multisite_disable_theme_browser_tab ) {
				$defaults = new WPMobiDefaultSettings;	
				
				$settings->current_theme_friendly_name = $defaults->current_theme_friendly_name;
				$settings->current_theme_location = $defaults->current_theme_location;
				$settings->current_theme_name = $defaults->current_theme_name;
				$settings->current_theme_skin = $defaults->current_theme_skin;				
			}
			
			if ( $primary_settings->multisite_inherit_compat ) {
				$settings->disable_shortcodes = $primary_settings->disable_shortcodes;
				$settings->disable_google_libraries = $primary_settings->disable_google_libraries;
				$settings->include_functions_from_desktop_theme = $primary_settings->include_functions_from_desktop_theme;
				$settings->dismissed_warnings = $primary_settings->dismissed_warnings;
				$settings->convert_menu_links_to_internal = $primary_settings->convert_menu_links_to_internal;
				$settings->plugin_hooks = $primary_settings->plugin_hooks;
			} else if ( $primary_settings->multisite_disable_compat_pane ) {
				$defaults = new WPMobiDefaultSettings;	
				
				$settings->disable_shortcodes = $defaults->disable_shortcodes;
				$settings->disable_google_libraries = $defaults->disable_google_libraries;
				$settings->include_functions_from_desktop_theme = $defaults->include_functions_from_desktop_theme;
				$settings->dismissed_warnings = $defaults->dismissed_warnings;
				$settings->convert_menu_links_to_internal = $defaults->convert_menu_links_to_internal;
				$settings->plugin_hooks = $defaults->plugin_hooks;				
			}
		}
		
		return $settings;	
	}
	
	function alter_admin_tabs_for_multisite() {
		$settings = $this->get_root_settings();
        
		if ( $settings->multisite_disable_overview_pane ) {
			unset( $this->tabs[ __( 'General', 'wpmobi-me' ) ]['settings'][ __( 'Overview', 'wpmobi-me' ) ] ); 
		}
		
		if ( $settings->multisite_disable_manage_icons_pane ) {
			unset( $this->tabs[ __( 'Menu + Icons', 'wpmobi-me' ) ]['settings'][ __( 'Manage Icons and Sets', 'wpmobi-me' ) ] ); 
		}
		
		if ( $settings->multisite_disable_compat_pane ) {
			unset( $this->tabs[ __( 'General', 'wpmobi-me' ) ]['settings'][ __( 'Compatibility', 'wpmobi-me' ) ] ); 
		}
		
		if ( $settings->multisite_disable_debug_pane ) {
			unset( $this->tabs[ __( 'General', 'wpmobi-me' ) ]['settings'][ __( 'Tools and Debug', 'wpmobi-me' ) ] );
		}
		
		if ( $settings->multisite_disable_backup_pane ) {
			unset( $this->tabs[ __( 'General', 'wpmobi-me' ) ]['settings'][ __( 'Backup/Import', 'wpmobi-me' ) ] );
		}
		
		if ( $settings->multisite_disable_push_notifications_pane ) {
			unset( $this->tabs[ __( 'General', 'wpmobi-me' ) ]['settings'][ __( 'Push Notifications', 'wpmobi-me' ) ] );
		}
		
		if ( $settings->multisite_disable_advertising_pane ) {
			unset( $this->tabs[ __( 'General', 'wpmobi-me' ) ]['settings'][ __( 'Advertising', 'wpmobi-me' ) ] );
		}
		
		if ( $settings->multisite_disable_statistics_pane ) {
			unset( $this->tabs[ __( 'General', 'wpmobi-me' ) ]['settings'][ __( 'Statistics', 'wpmobi-me' ) ] );
		}
		
		if ( $settings->multisite_disable_theme_browser_tab ) {
			unset( $this->tabs[ __( 'Theme Browser', 'wpmobi-me' ) ] );	
		}
	}

	/*!		\brief Checks to see if settings should be restored
	 *
	 *		This method checks to see if the user put in a string that should cause the settings to be restored
	 *
	 *		\ingroup helpers
	 */	 	
	function check_for_restored_settings() {
		$settings = $this->get_settings();
		
		if ( $settings->restore_string ) {
			if ( function_exists( 'gzuncompress' ) ) {
				$new_settings = @unserialize( gzuncompress( base64_decode( $settings->restore_string ) ) );	
				if ( is_object( $new_settings ) ) {
					$settings = $new_settings;
				} else {
					$this->restore_failure = true;
				}	
			}
			
			$settings->restore_string = '';
			
			$this->save_settings( $settings );				
		}
	}
		
	function handle_desktop_redirect_for_webapp() {
		include( WPMOBI_DIR . '/include/js/desktop-webapp.js' );
	}
	
	/*!		\brief Handles the wpmobi shortcode
	 *
	 *		This method handles the MobileView shortcode, wpmobi.  This shortcode allows content to be targeted for different situations
	 *
	 *		\param src_name the name of the source file
	 *		\param dst_name the name of the destination file
	 *
	 *		\ingroup helpers
	 */	 		
	function handle_shortcode( $attr, $content ) {
		$new_content = '';
		
		if ( isset( $attr['target'] ) ) {
			switch( $attr['target'] ) {
				case 'non-mobile':
					if ( !$this->is_mobile_device ) {
						$new_content = '<span class="wpmobi-shortcode-non-mobile">' . $content . '</span>';		
					}
					break;
				case 'desktop':
					if ( $this->is_mobile_device && !$this->showing_mobile_theme ) {
						$new_content = '<span class="wpmobi-shortcode-desktop">' . $content . '</span>';	
					}
					break;
				case 'non-webapp':
					if ( $this->is_mobile_device && $this->showing_mobile_theme ) {
						$new_content = '<span class="wpmobi-shortcode-mobile-only" style="display: none;">' . $content . '</span>';	
					}
					break;
				case 'webapp':
					if ( $this->is_mobile_device && $this->showing_mobile_theme ) {
						$new_content = '<span class="wpmobi-shortcode-webapp-only" style="display: none;">' . $content . '</span>';	
					}					
					break;	
				case 'mobile':
					if ( $this->is_mobile_device && $this->showing_mobile_theme ) {
						$new_content = '<span class="wpmobi-shortcode-webapp-mobile">' . $content . '</span>';	
					}									
					break;
			}	
		}
		
		return do_shortcode( $new_content );
	}
	
	function remove_duplicate_menu_items( $menu_items ) {
		$new_items = array();
		
		foreach( $menu_items as $key => $value ) {
			if ( isset( $value->submenu ) && count( $value->submenu ) ) {
				$value->submenu = $this->remove_duplicate_menu_items( $value->submenu );	
			}
			
			if ( !isset( $value->duplicate_link ) || ( isset( $value->duplicate_link ) && !$value->duplicate_link ) ) {
				$new_items[ $key ] = $value;	
			}				
		}
		
		return $new_items;
	}
	
	/*!		\brief Used to copy a file between two locations
	 *
	 *		This method can be used to copy a file between two locations.
	 *
	 *		\param src_name the name of the source file
	 *		\param dst_name the name of the destination file
	 *
	 *		\ingroup helpers
	 */	 	
	function copy_file( $src_name, $dst_name ) {
		$src = fopen( $src_name, 'rb' );
		if ( $src ) {
			$dst = fopen( $dst_name, 'w+b' );
			if ( $dst ) {
				while ( !feof( $src ) ) {
					$contents = fread( $src, 8192 );
					fwrite( $dst, $contents );
				}	
				fclose( $dst );	
			} else {
				WPMOBI_DEBUG( WPMOBI_ERROR, 'Unable to open ' . $dst_name . ' for writing' );	
			}
			
			fclose( $src );		
		}
	}
	
	/*!		\brief Checks the old version of WPMobi for custom icons, and copies them to the new version
	 *
	 *		This method checks the old version of WPMobi for custom icons.  If it finds any, they are added to the new version.  
	 *		
	 *		\note Currently only PNG (.png) files are supported
	 *
	 *		\ingroup helpers
	 */	 		
	function check_old_version() {
		$upload_dir = wp_upload_dir();
		if ( $upload_dir && isset( $upload_dir['basedir'] ) ) {
			$base_dir = $upload_dir['basedir'];	
			$old_wpmobi_custom_dir = $base_dir . '/wpmobi/custom-icons';
			
			$files = $this->get_files_in_directory( $old_wpmobi_custom_dir, '.png' );
			if ( $files && count( $files ) ) {
				foreach( $files as $some_file ) {
					$file_name = basename( $some_file );
					$dest_file = WPMOBI_CUSTOM_ICON_DIRECTORY . '/' . $file_name;
					
					if ( !file_exists( $dest_file ) ) {
						$this->copy_file( $some_file, $dest_file );
					}
				}
			}
		}
	}
	
	/*!		\brief Used to determine the friendly name for a plugin
	 *
	 *		This method can be used to convert a plugin slug into a friendly name for the plugin.
	 *
	 *		\param name the name of the plugin, usually the slug represented by the plugin's directory on disk
	 *
	 *		\returns A string representing the friendly name for a plugin
	 *
	 *		\ingroup internal
	 */	 	
	function get_friendly_plugin_name( $name ) {
		$plugin_file = WP_PLUGIN_DIR . '/' . $name . '/' . $name . '.php';
		if ( file_exists( $plugin_file ) ) {
			$contents = $this->load_file( $plugin_file );
			if ( $contents ) {
				if ( preg_match( "#Plugin Name: (.*)\n#", $contents, $matches ) ) {
					return $matches[1];	
				}	
			}
		}
		
		$all_files = $this->get_files_in_directory( WP_PLUGIN_DIR . '/' . $name, '.php' );
		if ( $all_files ) {
			foreach( $all_files as $some_file ) {
				if ( file_exists( $some_file ) ) {
					$contents = $this->load_file( $some_file );
					if ( $contents ) {
						if ( preg_match( "#Plugin Name: (.*)\n#", $contents, $matches ) ) {
							return $matches[1];	
						}	
					}
				}				
			}	
		}
		
		return str_replace( '_' , ' ', $name );
	}
	
	/*!		\brief Pre-processes the $_POST and $_GET data on a form submission
	 *
	 *		This method does preprocessing of the $_GET and $_POST data on form submissions.  It removes slashes on
	 *		servers that have magic quotes enabled.  
	 *
	 *		\ingroup internal
	 */	 	
	function cleanup_post_and_get() {		
		if ( count( $_GET ) ) {
			foreach( $_GET as $key => $value ) {
				if ( get_magic_quotes_gpc() ) {
					$this->get[ $key ] = @stripslashes( $value );	
				} else {
					$this->get[ $key ] = $value;
				}
			}	
		}	
		
		if ( count( $_POST ) ) {
			foreach( $_POST as $key => $value ) {
				if ( get_magic_quotes_gpc() ) {
					$this->post[ $key ] = @stripslashes( $value );	
				} else {
					$this->post[ $key ] = $value;	
				}
			}	
		}	
	}

	/*!		\brief Adds a static menu item to the main WPMobi menu
	 *
	 *		Adds a static menu item to the WPMobi menu.  An example would be a 
	 *		link to an email account or a Twitter feed.
	 *
	 *		\param menu_items an array representing the menu items to add
	 *
	 *		\ingroup menus
	 */	 
	function add_static_menu_items( $menu_items ) {
		$top_items = array();
		$bottom_items = array();
		
		$settings = $this->get_settings();
		
		// Add the Custom Page Templates here
		if ( count( $this->custom_page_templates ) ) {
			$count = 1;
			foreach( $this->custom_page_templates as $page_name => $page_info ) {
				$bottom_items[ $page_name ] = wpmobi_create_menu_item( WPMOBI_ICON_CUSTOM_PAGE_TEMPLATES - $count, 1, $page_name, 'link', false, 0, false, get_bloginfo( 'url' ) . '?wpmobi_page_template=' . $page_info[0] );
				$count++;
			}
		}
		
		// Add Home to the menu if it's enabled
		if ( $settings->menu_show_home ) {
			$top_items[ __( 'Home', 'wpmobi-me' ) ] = wpmobi_create_menu_item( WPMOBI_ICON_HOME, 1, __( 'Home', 'wpmobi-me'), 'link', false, 0, false, get_bloginfo( 'url' ) );	
		}	

		// Add email to the menu if it's enabled
		if ( $settings->menu_show_email ) {
			$email_address = get_option( 'admin_email' );
			if ( $settings->menu_custom_email_address ) {
				$email_address = $settings->menu_custom_email_address;	
			}
			
			$bottom_items[ __( 'Email', 'wpmobi-me' ) ] = wpmobi_create_menu_item( WPMOBI_ICON_EMAIL, 1, __( 'Email', 'wpmobi-me'), 'link', false, 0, false, 'mailto:' . $email_address, 'email' );
		}
		
		// Add RSS icon to the menu if it's enabled
		if ( $settings->menu_show_rss ) {
			$bottom_items[ __( 'RSS', 'wpmobi-me' ) ] = wpmobi_create_menu_item( WPMOBI_ICON_RSS, 1, __( 'RSS', 'wpmobi-me'), 'link', false, 0, false, wpmobi_get_bloginfo( 'rss_url'), 'feed' );		
		}
		
		for ( $i = 1; $i <= 3; $i++ ) {
			$text_name = 'custom_menu_text_' . $i;
			$link_name = 'custom_menu_link_' . $i;
			$link_spot = 'custom_menu_position_' . $i;
			$link_class = 'custom_menu_force_external_' . $i;
			if ( $settings->$text_name && $settings->$link_name ) {
				$custom_class = false;
				if ( isset( $settings->$link_class ) && $settings->$link_class ) {
					$custom_class = 'external';	
				}
				
				if ( $settings->$link_spot == 'top' ) {
					$top_items[ $settings->$text_name ] = wpmobi_create_menu_item( -100 - $i, 1, $settings->$text_name, 'link', false, 0, false, $settings->$link_name, $custom_class );	
				} else {
					$bottom_items[ $settings->$text_name ] = wpmobi_create_menu_item( -100 - $i, 1, $settings->$text_name, 'link', false, 0, false, $settings->$link_name, $custom_class );
				}
			}
		}
		
		// Make sure the top menu items override the bottom ones
		foreach ( $top_items as $key => $value ) {
			if ( isset( $menu_items[ $key ] ) ) {
				unset( $menu_items[ $key ] );	
			}
		}		
				
				
		// Make sure the top menu items override the bottom ones
		foreach ( $bottom_items as $key => $value ) {
			if ( isset( $menu_items[ $key ] ) ) {
				unset( $menu_items[ $key ] );	
			}
		}
	
		return array_merge( $top_items, $menu_items, $bottom_items );	
	}
	
	/*!		\brief Used to obtain the icon set object for a given icon
	 *
	 *		This method returns an object representing the icon set containing the icon passed in as a parameter
	 *
	 *		\param icon_path the full path on disk of the icon within the set
	 *
	 *		\returns An object representing the icon set
	 *
	 *		\ingroup internal
	 *		\ingroup iconssets
	 */	 	
	function get_set_with_icon( $icon_path ) {
		if ( !$this->icon_to_set_map ) {
			$this->icon_to_set_map = array();
			$icon_packs = $this->get_available_icon_packs();
			
			if ( $icon_packs ) {
				foreach( $icon_packs as $pack_name => $pack_info ) {
					$icons = $this->get_icons_from_packs( $pack_name );
					if ( $icons ) {
						foreach( $icons as $icon_name => $icon_info ) {
							$hash = md5( $icon_info->location );
							$this->icon_to_set_map[ $hash ] = $pack_info;
						}
					}
				}	
			}
		}	
		
		$hash = md5( $icon_path );
		if ( isset( $this->icon_to_set_map[ $hash ] ) ) {
			return $this->icon_to_set_map[ $hash ];
		} else {
			return false;	
		}
	}

	/*!		\brief Used to update the plugin information on the WordPress plugins page
	 *
	 *		This method updates the plugin information on the WordPress plugins page.  It gives users an opportunity to download a new
 	 *		version of MobileView.
	 *
	 *		\param plugin_name the name of the plugin	 
	 *
	 *		\ingroup internal
	 */	 	
    function plugin_row( $plugin_name ) {
		$plugin_name = $this->plugin_name;
            
		if ( false ) {
			echo '</tr><tr class="plugin-update-tr"><td colspan="5" class="plugin-update"><div class="update-message">';
			echo __( 'There is a new version of MobileView available.', 'wpmobi-me' ) . ' <a href="plugin-install.php?tab=plugin-information&plugin=wpmobi-me&TB_iframe=true&width=640&height=521">' . __( 'View version details' , 'wpmobi-me' ) . '</a>';	
			echo '</div></td>';
		}
    }

	/*!		\brief Adds a "Settings" link beside Deactivate and Edit on the plugins WP admin page
	 *
	 *		This function is used internally.
	 *
	 *		\ingroup internal	 
	 */
	 
	function wpmobi_settings_link( $links, $file ) {
	 	if( $file == $this->plugin_name && function_exists( "admin_url" ) ) {
			$settings_link = '<a href="' . admin_url( 'admin.php?page='.WPMOBI_ROOT_DIR.'/admin/admin-panel.php' ) . '">' . __('Settings') . '</a>';
			array_push( $links, $settings_link ); // after other links
		}
		return $links;
	}
	
	function remove_transient_info() {
    	$clc_api = $this->get_clc_api();	
    	
    	$plugin_name = $this->plugin_name;
    	
		if ( function_exists( 'is_super_admin' ) ) {
			$option = get_site_transient( "update_plugins" );
		} else {
			$option = function_exists( 'get_transient' ) ? get_transient("update_plugins") : get_option("update_plugins");
		} 	
		
		unset( $option->response[ $plugin_name ] );	
		   
		if ( function_exists( 'is_super_admin' ) ) {
			$this->transient_set = true; 
			set_site_transient( 'update_plugins', $option );
		} else {
			if ( function_exists( 'set_transient' ) ) {
				$this->transient_set = true;
				set_transient( 'update_plugins', $option );
			}
		}	
	}

	/*!		\brief Forces MobileView to look for a version update on the server
	 *
	 *		This function is used internally to check the MobileView servers for an updated version.
	 *
	 *		\ingroup internal	 
	 */		    
    function check_for_update() {
    	$clc_api = $this->get_clc_api();
    	  	
    		$plugin_name = $this->plugin_name;
    		$latest_info = $clc_api->get_product_version( 'wpmobi-me' );
    	
        // Check for WordPress 3.0 function
		if ( function_exists( 'is_super_admin' ) ) {
			$option = get_site_transient( "update_plugins" );
		} else {
			$option = function_exists( 'get_transient' ) ? get_transient("update_plugins") : get_option("update_plugins");
		}
    	
    	if ( $latest_info && $latest_info['version'] != WPMOBI_VERSION && isset( $latest_info['upgrade_url'] ) ) {    	  		   		
	        $wpmobi_option = $option->response[ $plugin_name ];
	
	        if ( empty( $wpmobi_option ) ) {
	            $option->response[ $plugin_name ] = new stdClass();
	        }
	
			$option->response[ $plugin_name ]->url = " ";
			
			$option->response[ $plugin_name ]->package = $latest_info['upgrade_url'];
			$option->response[ $plugin_name ]->new_version = $latest_info['version'];
			$option->response[ $plugin_name ]->id = "0";
			
			$option->response[ $plugin_name ]->slug = "wpmobi-me";

	        $this->latest_version_info = $latest_info;
    	} else {
    		unset( $option->response[ $plugin_name ] );	
    	}
    		
        if ( !$this->transient_set ) {      
        	// WordPress 3.0 changed some stuff, so we check for a WP 3.0 function
			if ( function_exists( 'is_super_admin' ) ) {
				$this->transient_set = true; 
				set_site_transient( 'update_plugins', $option );
			} else {
				if ( function_exists( 'set_transient' ) ) {
					$this->transient_set = true;
					set_transient( 'update_plugins', $option );
				}
			}
        }
        	
    }

	/*!		\brief Shows the MobileView changelog information during an automatic upgrade
	 *
	 *		This method echos the current changelog information from MobileView.  It is used during the automatic upgrade process
	 *		to give information to the user about what's changed in the new version. 
	 *
	 *		\ingroup internal	 
	 */	
    function show_plugin_info() {
    	
		switch( $_REQUEST[ 'plugin' ] ) {
			case 'wpmobi-me-beta':
				echo "<h2 style='font-family: Georgia, sans-serif; font-style: italic; font-weight: normal'>" . __( "MobileView Beta Changelog", "wpmobi-me" ) . "</h2>";
				$latest_info = $this->clc_api->get_product_version( 'wpmobi-me', true );
				if ( $latest_info ) {
					echo $latest_info['update_info'];	
				}
				exit;
				break;
			case 'wpmobi-me':
				echo "<h2 style='font-family: Georgia, sans-serif; font-style: italic; font-weight: normal'>" . __( "MobileView Changelog", "wpmobi-me" ) . "</h2>";
				$latest_info = $this->clc_api->get_product_version( 'wpmobi-me', false );
				if ( $latest_info ) {
					echo $latest_info['update_info'];	
				}				
				exit;
				break;
			default:
				break;
		}
    }

	/*!		\brief Returns a list of the MobileView module directories
	 *
	 *		MobileView modules are self-contained pieces of code with a paricular functionality. For example, a plugin developer
	 *		may wish to write an add-on module for MobileView that enables certain functionality for BuddyPress.  The output of this method can
	 *		be filtered using the WordPress filter \em wpmobi_module_directories.
	 *
	 *		\returns an array of active module directories 
	 *
	 *		\ingroup modules	 	 
	 */	    
    function get_module_directories() {
		$module_dirs = array( 
			get_wpmobi_directory() . '/modules',
			WPMOBI_BASE_CONTENT_DIR . '/modules'
		);
		
		return apply_filters( 'wpmobi_module_directories', $module_dirs );   	
    }

	 
	/*!		\brief This function causes all add-on modules to be loaded
	 *
	 *		This function is used internally to pre-load all the available add-on modules for MobileView.  This method 	 
	 *		triggers the WordPress action \em wpmobi_module_init after the modules are loaded.
	 *
	 *		\ingroup modules	 
	 */		    	
	function load_modules() {
		$module_dirs = $this->get_module_directories();
		
		// Load all modules
		foreach( $module_dirs as $dir ) {
			$all_files = $this->get_files_in_directory( $dir, '.php' );
			if ( $all_files ) {
				foreach( $all_files as $module_file ) {
					require_once( $module_file );	
				}	
			}
		}
		
		do_action( 'wpmobi_module_init', $this );
	}
	
	 
	/*!		\brief Used to register an add-on module
	 *
	 *		This method is used internally to register a module.  Each module is responsible for registering itself using
	 *		the appropriate action hook.
	 *
	 *		\param module_name The friendly name of the module to register
	 *		\param module_settings The settings for the associated module
	 *
	 *		\ingroup modules	 
	 */			
	function register_module( $module_name, $module_settings ) {
		$this->modules[ $module_name ] = $module_settings;
	}
	
	 
	/*!		\brief Used to determine if any add-on modules have been installed
	 *
	 *		This method is used internally to determine if any add-on modules have been installed.
	 *
	 *		\returns The number of modules that have been installed
	 *
	 *		\ingroup modules		 
	 */			
	function has_modules() {
		return count( $this->modules );	
	}
	
	 
	/*!		\brief Returns a list of add-on modules
	 *
	 *		This method returns a list of all the currently installed modules.
	 *
	 *		\returns An array of installed modules
	 *
	 *		\ingroup modules		 
	 */			
	function get_modules() {
		return $this->modules;	
	}
	
	/*!		\brief Reads a piece of information from a readme or text file
	 *
	 *		This method can be used to retrieve an information fragment from an external file.  An example of an information
	 *		fragment is the plugin author's name in a PHP file, or the name of an icon set in a readme.txt file.
	 *
	 *		\param style_info The information to search for the fragment
	 *		\param fragment The fragment to search for in the text file
	 *
	 *		\returns the information represented by the fragmnent
	 *
	 *		\ingroup internal	 
	 */			
	function get_information_fragment( &$style_info, $fragment ) {
		if ( preg_match( '#' . $fragment . ': (.*)#i', $style_info, $matches ) ) {
			return $matches[1];
		} else {
			return false;	
		}
	}

	/*!		\brief Returns information about a theme
	 *
	 *		The method is used to obtain information about a given theme.
	 *
	 *		\param theme_location The full path to the theme
	 *		\param theme_url A URL representing the full path
	 *		\param custom Indicates that the theme represents a custom theme
	 *
	 *		\ingroup internal	 
	 *
	 *		\returns An object representing the theme information	 
	 */		
	function get_theme_information( $theme_location, $theme_url, $custom = false ) {
		$style_file = $theme_location . '/readme.txt';
		if ( file_exists( $style_file ) ) {
			$style_info = $this->load_file( $style_file );
			
			$theme_info = new stdClass;
			
			// todo: should probably check to make sure some of these are valid
			$theme_info->name = $this->get_information_fragment( $style_info, 'Theme Name' );
			$theme_info->theme_url = $this->get_information_fragment( $style_info, 'Theme URI' );
			$theme_info->description = $this->get_information_fragment( $style_info, 'Description' );
			$theme_info->author = $this->get_information_fragment( $style_info, 'Author' );
			$theme_info->version = $this->get_information_fragment( $style_info, 'Version' );
			$features = $this->get_information_fragment( $style_info, 'Features' );
			if ( $features ) {
				$theme_info->features = explode( ',', str_replace( ', ', ',', $features ) );
			} else {
				$theme_info->features = false;	
			}
			
			$parent_info = $this->get_information_fragment( $style_info, 'Parent' );
			if ( $parent_info ) {
				$theme_info->parent_theme = $parent_info;	
			} 
			
			$theme_info->tags = explode( ',', str_replace( ', ', ',', $this->get_information_fragment( $style_info, 'Tags' ) ) );
			$theme_info->screenshot = $theme_url . '/screenshot.png';
			$theme_info->location = str_replace( WP_CONTENT_DIR, '', $theme_location );
			$theme_info->skins_dir = $theme_location . '/skins';
			$theme_info->custom_theme = $custom;
			
			
			return $theme_info;
		}
		
		return false;
	}

	/*!		\brief Returns a list of files in a particular directory
	 *
	 *		The method can be used to retrieve a list of files in a particular directory
	 *
	 *		\param directory_name The full path of the directory
	 *		\param extension The file extension to look for in the directory
	 *
	 *		\ingroup internal	 
	 *
	 *		\returns An array of files in the specified directory.  All files will have the full path prepended to their name.	 
	 */			
	function get_files_in_directory( $directory_name, $extension ) {
		$files = array();
		
		$dir = @opendir( $directory_name );
		
		if ( $dir ) {
			while ( ( $f = readdir( $dir ) ) !== false ) {
				
				// Skip common files in each directory
				if ( $f == '.' || $f == '..' || $f == '.svn' || $f == '._.DS_Store' || $f == '.DS_Store' ) {
					continue;	
				}
				
				if ( !$extension || strpos( $f, $extension ) !== false ) {
					$files[] = $directory_name . '/' . $f;	
				}	
			}	
			
			closedir( $dir );	
		}
		
		return $files;
	}

	/*!		\brief Returns a list of available theme directories
	 *
	 *		The method can be used to obtain a list of available theme directories.  It is possible that a plugin or module
	 *		can add an additional directory.  The output of this method can be filtered using the WordPress filter \em wpmobi_theme_directories.
	 *
	 *		\ingroup internal	 
	 *
	 *		\returns An array of  theme directories
	 */		
	function get_theme_directories() {
		array();
		
		$theme_directories[] = array( get_wpmobi_directory() . '/themes', get_wpmobi_url() . '/themes' );		
		$theme_directories[] = array( WPMOBI_BASE_CONTENT_DIR . '/themes', WPMOBI_BASE_CONTENT_URL . '/themes' );	
		
		return apply_filters( 'wpmobi_theme_directories', $theme_directories );
	}
	
	/*!		\brief Returns a list of available themes
	 *
	 *		The method can be used to obtain a list of available themes. The list of themes is generated by reading the theme information
	 *		files in each of the directories returned by get_theme_directories(). The output of this function can be filtered using the 
	 *		WordPress filter \em wpmobi_available_themes.
	 *
	 *		\returns An array of objects representing all available themes
	 *
	 *		\ingroup internal	 
	 */			
	function get_available_themes() {
		$themes = array();
		$theme_directories = $this->get_theme_directories();

		$custom = false;
		foreach( $theme_directories as $theme_dir ) {
			$list_dir = @opendir( $theme_dir[0] );
			
			if ( $list_dir ) {
				while ( ( $f = readdir( $list_dir ) ) !== false ) {
					// Skip common files in each directory
					if ( $f == '.' || $f == '..' || $f == '.svn' || $f == '._.DS_Store' || $f == 'core' ) {
						continue;	
					}
					
					$theme_info = $this->get_theme_information( $theme_dir[0] . '/' . $f, $theme_dir[1] . '/' . $f, $custom );
				
					if ( $theme_info && file_exists( $theme_info->skins_dir ) ) {
						// Load skins here
						$skins = $this->get_files_in_directory( $theme_info->skins_dir, 'css' );
						
						if ( count( $skins ) ) {
							$all_skin_info = array();
							
							foreach( $skins as $skin ) {
								$style_info = $this->load_file( $skin );	
								
								$skin_name = $this->get_information_fragment( $style_info, 'Skin Name' );
								
								if ( $skin_name ) {
									$skin_info = new stdClass;
									$skin_info->skin_location = $skin;
									$skin_info->skin_url = $theme_dir[1] . '/' . $f . '/skins/' . basename( $skin );
									$skin_info->name = $skin_name;
									$skin_info->basename = basename( $skin );
									
									$all_skin_info[ basename( $skin ) ] = $skin_info;
								}
							}	
							
							if ( count ( $all_skin_info ) ) {
								$theme_info->skins = $all_skin_info;
							}
						}
					}
					
					if ( $theme_info ) {
						$themes[ $theme_info->name ] = $theme_info;
					}
				}
				
				closedir( $list_dir );
			}
			
			if ( !$custom ) {
				$custom = true;
			}

		}	
		
		ksort( $themes );	
						
		return apply_filters( 'wpmobi_available_themes', $themes );		
	}

	/*!		\brief Returns information about the currently active theme.
	 *
	 *		This method returns information about the currently active theme (the theme the user has selected in the Theme Browser in the administration
	 * 		panel.  
	 *
	 *		\returns An object representing the currently active theme, or false if no theme is currently active
	 *
	 *		\ingroup internal	 
	 */			
	function get_current_theme_info() {
		$settings = $this->get_settings();
		
		$themes = $this->get_available_themes();
		
		if ( isset( $themes[ $settings->current_theme_friendly_name ] ) ) {
			return $themes[ $settings->current_theme_friendly_name ];	
		} else {
			// check to see if we can find it using the path, in the case where the Theme Friendly Name has changed
			$active_theme_location = $settings->current_theme_location . '/' . $settings->current_theme_name;
			foreach( $themes as $name => $theme_info ) {
				if ( $theme_info->location == $active_theme_location ) {
					return $theme_info;
				}
			}
		}
		
		return false;
	}

	/*!		\brief Retrieves the current news items regarding WPMobi from ColorLabs
	 *
	 *		This method returns a list of recent entries from ColorLabs regarding MobileView.
	 *
	 *		\param quantity The number of entries to return	 
	 *
	 *		\returns An array of RSS entries, or false if an error occurs
	 *
	 *		\ingroup internal	 
	 */		
	function get_latest_news( $quantity = 8 ) {
		if ( !function_exists( 'fetch_feed' ) ) {
			require_once( ABSPATH . WPINC . '/feed.php' );
		}
		
		$rss = fetch_feed( ' ' );
		if ( !is_wp_error( $rss ) ) {
			$max_items = $rss->get_item_quantity( $quantity ); 
			$rss_items = $rss->get_items( 0, $max_items ); 
			
			return $rss_items;	
		} else {	
			return false;
		}
	}

	/*!		\brief Creates an object representing information about an icon set
	 *
	 *		This method can be used to create an object that represents a particular icon set.
	 *
	 *		\param name The name of the icon set
	 *		\param desc A description for the icon set
	 *		\param author The author of the icon set
	 *		\param author_url The URL for the author of the set
	 *		\param url The URL where additional information about the set can be found
	 *		\param location The location of the icon set on disk
	 *		\param dark Indicates whether or not the icon set looks best on a dark background
	 *
	 *		\returns An array of RSS entries, or false if an error occurs
	 *
	 *		\ingroup internal	 
	 */		
	function create_icon_set_info( $name, $desc, $author, $author_url, $url, $location, $dark = false ) {
		$icon_pack_info = new stdClass;
		
		$icon_pack_info->name = $name;
		$icon_pack_info->description = $desc;
		
		// Check to see if we have an author.  It's not required that you do, i.e. in the case of Custom
		if ( $author ) {
			$icon_pack_info->author = $author;
			$icon_pack_info->author_url = $author_url;
		}
		
		$icon_pack_info->url = $url;
		$icon_pack_info->location = $location;
		$icon_pack_info->class_name = $this->convert_to_class_name( $icon_pack_info->name );
		$icon_pack_info->dark_background = $dark;
		
		return $icon_pack_info;			
	}
	
	/*!		\brief Retrieves information about a particular icon set
	 *
	 *		This method returns information about a particular icon set.  The icon set information
	 *		is stored in each icon set's directory in a file called \em wpmobi.info.
	 *
	 *		\param icon_pack_location The full location of the icon set on disk
	 *		\param icon_pack_url The full URL for the icon set 
	 *
	 *		\returns An object representing the icon set information, or false if the icon set or associated info file cannot be found
	 *
	 *		\ingroup internal	 
	 */		
	function get_icon_set_information( $icon_pack_location, $icon_pack_url ) {
		$info_file = $icon_pack_location . '/wpmobi.info';

		if ( file_exists( $info_file ) ) {
			$icon_info = $this->load_file( $info_file );
			
			$dark = false;
			$background_type = $this->get_information_fragment( $icon_info, 'Background' );
			if ( $background_type == 'Dark' ) {
				$dark = true;
			}	
			
			// Create icon set information
			$icon_pack_info = $this->create_icon_set_info( 
				$this->get_information_fragment( $icon_info, 'Name' ),
				$this->get_information_fragment( $icon_info, 'Description' ),
				$this->get_information_fragment( $icon_info, 'Author' ),
				$this->get_information_fragment( $icon_info, 'Author URL' ),
				$icon_pack_url,
				$icon_pack_location,
				$dark
			);
					
			return $icon_pack_info;
		}
		
		return false;
	}

	/*!		\brief Creates an object representing a site-wide icon
	 *
	 *		This method is used to create an object representing a side-wide icon. The created icon can be filtered using the 
	 *		WordPress filter \em wpmobi_create_site_icon.
	 *
	 *		\param name The friendly name of the icon
	 *		\param icon The location for the icon on disk
	 *		\param icon_id A globally unique ID for the icon 
	 *		\param class_name The CSS class to use for the icon
	 *
	 *		\returns An object representing the site-wide icon
	 *
	 *		\ingroup iconssets
	 *		\ingroup internal	 
	 */		
	function create_site_icon( $name, $icon, $icon_id, $class_name ) {
		$icon_info = new stdClass;
		
		$icon_info->name = $name;
		$icon_info->icon = $icon;
		$icon_info->id = $icon_id;
		$icon_info->class_name = $class_name;
		$icon_info->url = clc_wpmobi_sslize( WP_CONTENT_URL . $icon );
		
		return apply_filters( 'wpmobi_create_site_icon', $icon_info );
	} 

	/*!		\brief Returns a list of the site icons
	 *
	 *		This method returns a list of all the available site icons. The output from this method can be filtered using the WordPress
	 *		filter \em wpmobi_site_icons.
	 *
	 *		\returns An array of objects representing the site icons available to MobileView
	 *
	 *		\ingroup internal
	 *		\ingroup iconssets	 
	 */			
	function get_site_icons() {
		$settings = $this->get_settings();
		
		$site_icons = array();
		
		$site_icon[ WPMOBI_ICON_HOME ] = $this->create_site_icon( __( 'Logo/Header', 'wpmobi-me' ), '/plugins/' . WPMOBI_ROOT_DIR . '/resources/icons/classic/Home.png', WPMOBI_ICON_HOME, 'home' );
		$site_icon[ WPMOBI_ICON_BOOKMARK ] = $this->create_site_icon( __( 'iPhone/Android Homescreen', 'wpmobi-me' ), '/plugins/' . WPMOBI_ROOT_DIR . '/include/images/wpmobi_bookmark_icon.png', WPMOBI_ICON_BOOKMARK , 'bookmark' );
		
		$site_icon[ WPMOBI_ICON_TABLET_BOOKMARK ] = $this->create_site_icon( __( 'iPad Homescreen', 'wpmobi-me' ), '/plugins/' . WPMOBI_ROOT_DIR . '/include/images/wpmobi_ipad_bookmark_icon.png', WPMOBI_ICON_TABLET_BOOKMARK , 'tablet-bookmark' );
		
		if ( $settings->menu_show_email  ) {
			$site_icon[ WPMOBI_ICON_EMAIL ] = $this->create_site_icon( __( 'Email', 'wpmobi-me' ), '/plugins/' . WPMOBI_ROOT_DIR . '/resources/icons/classic/Mail.png', WPMOBI_ICON_EMAIL, 'email' );	
		}		
		
		if ( $settings->menu_show_rss  ) {
			$site_icon[ WPMOBI_ICON_RSS ] = $this->create_site_icon( __( 'RSS', 'wpmobi-me' ), '/plugins/' . WPMOBI_ROOT_DIR . '/resources/icons/classic/RSS.png', WPMOBI_ICON_RSS, 'rss' );	
		}
		
		// Add custom menu items here		
		for ( $i = 1; $i <= 3; $i++ ) {
			$text_name = 'custom_menu_text_' . $i;
			$link_name = 'custom_menu_link_' . $i;
			$link_spot = 'custom_menu_position_' . $i;
			if ( $settings->$text_name && $settings->$link_name ) {
				$site_icon[ (-100 - $i) ] = $this->create_site_icon( $settings->$text_name, '/plugins/' . WPMOBI_ROOT_DIR . '/resources/icons/classic/Default.png', (-100 - $i) , 'custom_' . $i );
			}
		}	
			
		if ( count( $this->custom_page_templates ) ) {
			$count = 1;
			foreach( $this->custom_page_templates as $page_name => $page_info ) {
				$site_icon[ WPMOBI_ICON_CUSTOM_PAGE_TEMPLATES - $count ] = $this->create_site_icon( $page_name, '/plugins/' . WPMOBI_ROOT_DIR . '/resources/icons/classic/Default.png', WPMOBI_ICON_CUSTOM_PAGE_TEMPLATES - $count , 'custom-' . (-$count) );	
				$count++;
			}
		}				
		
		$site_icon[ WPMOBI_ICON_DEFAULT ] = $this->create_site_icon( __( "Default Page", 'wpmobi-me' ), '/plugins/' . WPMOBI_ROOT_DIR . '/resources/icons/classic/Default.png', WPMOBI_ICON_DEFAULT , 'default-prototype' );

				
		return apply_filters( 'wpmobi_site_icons', $site_icon );	
	}

	/*!		\brief Returns the maximum upload size for the current server
	 *
	 *		This method returns the maximum upload size for the current server.  It does this by checking various PHP options that are set 
	 *		on different versions.
	 *
	 *		\returns The maximum upload size on the system, usually expressed in megabytes, i.e. 64M
	 *
	 *		\ingroup internal	 
	 */			
	function get_max_upload_size() {
		$max_upload_info = array();
		if ( ini_get( 'post_max_size' ) ) {
			$max_upload_info[] = (int)ini_get( 'post_max_size' );	
		}
		
		if ( ini_get( 'max_file_size' ) ) {
			$max_upload_info[] = (int)ini_get( 'max_file_size' );	
		}
		
		if ( ini_get( 'upload_max_filesize' ) ) {
			$max_upload_info[] = (int)ini_get( 'upload_max_filesize' );	
		}
		
		return min( $max_upload_info );	
	}
	

	/*!		\brief Returns a list of the available icon sets
	 *
	 *		This method returns a list of all the available icon sets. The output from this method can be filtered using the WordPress
	 *		filters \em wpmobi_available_icon_sets_pre_sort and \em wpmobi_available_icon_sets_post_sort.
	 *
	 *		\returns An array of objects representing the icon sets
	 *
	 *		\ingroup internal	
	 *		\ingroup iconssets 
	 */				
	function get_available_icon_packs() {
		$icon_packs = array();
		$icon_pack_directories = array();
		$icon_pack_directories[] = array( get_wpmobi_directory() . '/resources/icons', get_wpmobi_url() . '/resources/icons' );		
		$icon_pack_directories[] = array( WPMOBI_BASE_CONTENT_DIR . '/icons', WPMOBI_BASE_CONTENT_URL . '/icons' );
		
		foreach( $icon_pack_directories as $some_key => $icon_dir ) {
			$list_dir = @opendir( $icon_dir[0] );
			if ( $list_dir ) {
				while ( ( $f = readdir( $list_dir ) ) !== false ) {
					// Skip common files in each directory
					if ( $f == '.' || $f == '..' || $f == '.svn' || $f == '._.DS_Store' ) {
						continue;	
					}
					
					$icon_pack_info = $this->get_icon_set_information( $icon_dir[0] . '/' . $f, $icon_dir[1] . '/' . $f );
					
					if ( $icon_pack_info ) {
						$icon_packs[ $icon_pack_info->name ] = $icon_pack_info;
					}
				}
			}
		}
			
		$icon_packs = apply_filters( 'wpmobi_available_icon_sets_pre_sort', $icon_packs );
		
		ksort( $icon_packs );
				
		return apply_filters( 'wpmobi_available_icon_sets_post_sort', $icon_packs );			
	}

	/*!		\brief Called internally to set up the custom icon directory
	 *
	 *		This method is used to set up the custom icon directory.  Currently is adds "Custom Icons" to the list, associating it with the
	 *		directory in /wp-content/wpmobi-data
	 *
	 *		\returns A array representing all the icon sets as well as the Custom Icon directory
	 *
	 *		\ingroup internal
	 *		\ingroup iconssets	 
	 */			
	function setup_custom_icons( $icon_pack_info ) {
		$icon_info = array();
		$icon_info[ __( 'Custom Icons', 'wpmobi-me' ) ] = $this->create_icon_set_info(
			__( 'Custom Icons', 'wpmobi-me' ),
			'Custom Icons',
			false,
			'',
			WPMOBI_CUSTOM_ICON_URL,
			WPMOBI_CUSTOM_ICON_DIRECTORY
		);
		
		return array_merge( $icon_pack_info, $icon_info );	
	}
	
	/*!		\brief Returns icon set information
	 *
	 *		This method is returns icon set information for the set with the requested name.  
	 *
	 *		\param set_name The name of the icon set to return information for
	 *
	 *		\returns An object representing the icon set, or false if the set is not defined
	 *
	 *		\ingroup internal
	 *		\ingroup iconssets	 
	 */			
	function get_icon_pack( $set_name ) {
		$available_packs = $this->get_available_icon_packs();
		
		if ( isset( $available_packs[ $set_name ] ) ) {
			return $available_packs[ $set_name ];
		} else {
			return false;	
		}
	}

	/*!		\brief Indicates whether or not a given file is an image file
	 *
	 *		This method can be used to determine whether or not a file is an image file.  It makes this determination based on the file extension.  The
	 *		allowable file extensions are currently png, jpg, jpeg, and gif, but can be filtered using the WordPress filter \em wpmobi_image_file_types.
	 *
	 *		\param file_name The name of the file to check against
	 *
	 *		\returns True if the file is an image, otherwise false
	 *
	 *		\ingroup internal
	 *		\ingroup helpers	 
	 */			
	function is_image_file( $file_name ) {
		$file_name = strtolower( $file_name );
		$allowable_extensions = apply_filters( 'wpmobi_image_file_types', array( '.png', '.jpg', '.gif', '.jpeg' ) );
		
		$is_image = false;
		foreach( $allowable_extensions as $ext ) {
			if ( strpos( $file_name, $ext ) !== false ) {
				$is_image = true;
				break;	
			}
		}
		
		return $is_image;
	}

	/*!		\brief Retrieves a list of icons that are available in a particular set
	 *
	 *		This method can be used to obtain a list of available icons in a particular icon set. 
	 *
	 *		\param setname The name of icon set
	 *
	 *		\returns An array of icons, or an empty array if no icons are found
	 *
	 *		\ingroup internal
	 *		\ingroup iconssets	 
	 */	
	function get_icons_from_packs( $setname ) {		
		$settings = $this->get_settings();
		$icon_packs = $this->get_available_icon_packs();
		
		$icons = array();
			
		if ( isset( $icon_packs[ $setname ] ) ) {
			$pack = $icon_packs[ $setname ];
			$dir = @opendir( $pack->location );
			
			$class_name = $this->convert_to_class_name( $setname );
			
			if ( $dir ) {
				while ( $f = readdir( $dir ) ) {
					if ( $f == '.' || $f == '..' || $f == '.svn' || !$this->is_image_file( $f ) ) continue;
					
					$icon_info = new stdClass;
					$icon_info->location = $pack->location . '/' . $f;
					$icon_info->short_location = str_replace( WP_CONTENT_DIR, '', $icon_info->location );
					$icon_info->url = $pack->url . '/' . $f;
					$icon_info->name = $f;
					$icon_info->set = $setname;
					$icon_info->class_name = $class_name;
					
					$short_name_array = explode( '.', $f );
					$short_name = $short_name_array[0];
					$icon_info->short_name = $short_name;
					
					// add image size information if the user has the GD library installed
					if ( function_exists( 'getimagesize' ) ) {
						$icon_info->image_size = getimagesize( $pack->location . '/' . $f );	
					}
					
					$icons[ $f . '/' . $setname ] = $icon_info;	
				}
			
				closedir( $dir );	
			}
		}
		
		ksort( $icons );
		
		return $icons;
	}
	
	function wpmobi_is_wpmobi_page() {
		return is_admin() && ( strpos( $_SERVER['REQUEST_URI'], WPMOBI_ROOT_DIR ) !== false );	
	}

	/*!		\brief Outputs the WPMobi scripts in the administration panel header
	 *
	 *		This method is called internally to determine the proper scripts to use for the administration panel.  To add additional content here, use the
	 *		WordPress action \em wpmobi_admin_head.
	 *
	 *		\ingroup internal	
	 *		\ingroup admin 
	 */	
	function wpmobi_admin_head() {		
//		$current_scheme = get_user_option('admin_color');
		$settings = $this->get_settings();
		
		if ( strpos( $_SERVER['REQUEST_URI'], WPMOBI_ROOT_DIR ) !== false ) {
			$version_string = md5( WPMOBI_VERSION );
			$minfile = WPMOBI_DIR . '/admin/css/wpmobi-admin.min.css';
			
			if ( file_exists( $minfile ) ) {
				echo "<link rel='stylesheet' type='text/css' href='" . WPMOBI_URL . "/admin/css/wpmobi-admin.min.css?ver=" . $version_string . "' />\n";
			} else {
				echo "<link rel='stylesheet' type='text/css' href='" . WPMOBI_URL . "/admin/css/wpmobi-admin.css?ver=" . $version_string . "' />\n";			
			}
			
//			if ( $current_scheme === 'fresh' ) {
//				echo "<link rel='stylesheet' type='text/css' href='" . WPMOBI_URL . "/admin/css/wpmobi-admin-" . $current_scheme . ".css?ver=" . $version_string . "' />\n";		
//			}		

//			echo "<!--[if lte IE 8]>\n";
//			echo "<link rel='stylesheet' type='text/css' href='" . WPMOBI_URL . "/admin/css/wpmobi-admin-ie.css?ver=" . $version_string . "' />\n";
//			echo "<![endif]-->\n";

			do_action( 'wpmobi_admin_head' );
		}
	}

	/*!		\brief Converts a string into a format that can be used as a CSS class name
	 *
	 *		This method converts an arbitrary string into a format the can be used in a CSS class name.  Various characters such as spaces and quotes
	 *		are converted into dashes.
	 *
	 *		\param $name The string to convert into a class name
	 *
	 *		\returns A string which can be used in a CSS class
	 *
	 *		\ingroup internal
	 *		\ingroup helpers	 
	 */		
	function convert_to_class_name( $name ) {
		$class_name = str_replace( ' ', '-', str_replace( '"', '', str_replace( '.', '-', str_replace( '\'', '', strtolower( $name ) ) ) ) );	
		return $class_name;
	}
	
	/*!		\brief Used to display an Admob mobile advertisement
	 *
	 *		This method can be used to display an Admob mobile advertisement
	 *
	 *		\returns The HTML representation of the Admob ad to display
	 *
	 *		\ingroup internal
	 *		\ingroup advertising	 
	 */		
	function get_admob_ad() {
		ob_start();
		
		if ( $this->active_device == 'iphone' ) {
			include( get_wpmobi_directory() . '/include/advertising/admob.php' );
		} 
				
		$advertising = ob_get_contents();
		ob_end_clean();	
		
		return $advertising;
	}

	/*!		\brief Used to display a Google Adsense mobile advertisement
	 *
	 *		This method can be used to display an Google Adsense mobile advertisement.  
	 *
	 *		\note Currently only supports iPhone/Webkit-based devices.
	 *
	 *		\returns The HTML representation of the Google Adsense advertisment to display
	 *
	 *		\ingroup internal
	 *		\ingroup advertising	 
	 */			
	function get_google_ad() {
		ob_start();
		if ( $this->get_active_device_class() == 'iphone' ) {
			include( get_wpmobi_directory() . '/include/advertising/adsense-iphone-new.php' );
		} 
		
		$advertising = ob_get_contents();
		ob_end_clean();	
		
		return $advertising;
	}
	
	/*!		\brief Used to display advertising in MobileView themes. 
	 *
	 *		This method is called internally to display a mobile advertisement. To add a custom advertisement type, intercept the WordPress filter
	 *		\em wpmobi_advertising_types to define a new type. To render the new advertisement, intercept the WordPress action \em wpmobi_advertising_{new_type}
	 *		(where {new_type} is the new advertisement type, such as my_advertisement) and output the HTML fragment representing the adverisement.
	 *
	 *		\note Currently only Google Adsense and Admob advertisements are supported
	 *	 
	 *		\ingroup internal
	 *		\ingroup advertising	 
	 *
	 *		\par Custom Advertising:
	 *		To add a custom adverising type, you could do the following:
	 *
	 *		\include custom-advertising.php
	 */		
	function handle_advertising( $content = false ) {
		$settings = $this->get_settings();
		
		$can_show_ads = false;
		switch( $settings->advertising_pages ) {
			case 'ads_single':
				$can_show_ads = is_single() && !is_page();
				break;
			case 'main_single_pages':
				$can_show_ads = is_front_page() || is_home() || is_single() || is_page();
				break;
			case 'all_views':
				// show for everything
				$can_show_ads = true;
				break;	
			case 'home_page_only':
				$can_show_ads = $this->is_front_page();
				break;
		}
		
		if ( $can_show_ads ) {
				switch( $settings->advertising_type ) {
					case 'admob':
						echo $this->get_admob_ad();
						break;
					case 'google':
						echo $this->get_google_ad();
						break;
					case 'custom':
						echo '<div class="wpmobi-custom-ad">' . $settings->custom_advertising_code . '</div>';
						break;
					default:
						// Try to get this advertising type from a plugin
						do_action( 'wpmobi_advertising_' . $settings->advertising_type );
						break;
				}
		}
	}

	/*!		\brief Used to inject the custom statistics code into the footer of a MobileView theme
	 *
	 *		This method is called internally to inject custom statistics code into the footer of a MobileView mobile theme.  
	 *		The custom statistics code is defined in the user setting \em custom_stats_code.  The output from this function
	 *		can be filtered using the WordPress filter \em wpmobi_custom_stats_code.
	 *	 
	 *		\ingroup internal	 
	 */		
	function put_stats_in_footer() {
		$settings = $this->get_settings();
		
		echo apply_filters( 'wpmobi_custom_stats_code', $settings->custom_stats_code );
	}
	
	/*!		\brief Used to display the number of queries and page loading time in the footer of a mobile theme
	 *
	 *		This method is called internally to display queries and page loading time in the footer. The output of this function is wrapped in 
	 *		an HTML div with an ID of \em wpmobi-query.  The output of this function can be filtered using the WordPress filter
	 *		\em wpmobi_footer_load_time.
	 *	 
	 *		\ingroup internal	 
	 */			
	function show_footer_load_time() {
		echo apply_filters( 'wpmobi_footer_load_time', '<div id="wpmobi-query">' . sprintf( __( "%d queries in %0.1f ms", 'wpmobi-me' ), get_num_queries(), 1000*timer_stop( 0, 4 ) ) . '</div>' );	
	}
	
	/*!		\brief Used to display the custom footer message
	 *
	 *		This method is called internally to display a custom footer message in a MobileView mobile theme.  The custom footer message is 
	 *		defined in the user setting \em footer_message, and can be filtered using the WordPress filter \em wpmobi_custom_footer_message.
	 *	 
	 *		\ingroup internal	 
	 */			
	function show_custom_footer_message() {
		$settings = $this->get_settings();
		echo apply_filters( 'wpmobi_custom_footer_message', $settings->footer_message );	
	}
	
	function handle_client_ajax() {
		$nonce = $this->post['wpmobi_nonce'];
		if ( !wp_verify_nonce( $nonce, 'wpmobi-ajax' ) ) {
			die( 'Security' );
		}
		
		if ( isset( $this->post['wpmobi_action'] ) ) {
			do_action( 'wpmobi_ajax_' . $this->post['wpmobi_action'] );	
			exit;
		}
		
		die;
	}
	
	/*!		\brief Initializes all theme components
	 *
	 *		This method is called internally from the \em wp_init action, and is used to setup the majority of filters and action hooks 
	 *		that are required for the mobile themes.  The following actions are initiated from this method: \em wpmobi_init, \em wpmobi_theme_init, and 
	 *		\em wpmobi_theme_language.  The plugins that have been disabled by the user in the administration panel are also disabled from this
	 *		method.
	 *	 
	 *		\ingroup internal	 
	 */			
	function init_theme() {	
		$settings = $this->get_settings();
			
		if ( $settings->footer_message ) {
			add_action( 'wp_footer', array( &$this, 'show_custom_footer_message' ) );	
		}		
			
		if ( $settings->show_wpmobi_in_footer ) {		
			add_action( 'wp_footer', array( &$this, 'show_wpmobi_message_in_footer') );	
		}		

		if ( $settings->custom_stats_code ) {
			add_action( 'wp_footer', array( &$this, 'put_stats_in_footer' ) );	
		}	
		
		if ( $settings->show_footer_load_times ) {
			add_action( 'wp_footer', array( &$this, 'show_footer_load_time' ) );	
		}
		
		if ( $settings->custom_css_file ) {
			add_action( 'wp_footer', array( &$this, 'inject_custom_css_in_footer' ) );	
		}
		
		// Setup advertising
		if ( $settings->advertising_type != 'none' ) {
			switch ( $settings->advertising_location ) {
				case 'footer':
					add_action( 'wpmobi_advertising_bottom', array( &$this, 'handle_advertising' ) );
					break;
				case 'header':
					add_action( 'wpmobi_advertising_top', array( &$this, 'handle_advertising' ) );
					break;
				default:
					WPMOBI_DEBUG( WPMOBI_WARNING, 'Unknown advertising location: ' . $settings->advertising_location );
					break;
			}	
		}
		
		wp_enqueue_script( 'wpmobi-ajax', get_wpmobi_url() . '/include/js/wpmobi.js', array( 'jquery' ), md5( WPMOBI_VERSION ) );
		$localize_params = 	array( 
			'ajaxurl' => get_bloginfo( 'wpurl' ) . '/wp-admin/admin-ajax.php',
			'siteurl' => str_replace( array( 'http://' . $_SERVER['SERVER_NAME'] . '','https://' . $_SERVER['SERVER_NAME'] . '' ), '', get_bloginfo( 'url' ) . '/' ),
			'SITETITLE' => str_replace( ' ', '', get_bloginfo( 'title' ) ),
			'security_nonce' => wp_create_nonce( 'wpmobi-ajax' ),
			'expiryDays' => $settings->hipnews_webapp_notice_expiry_days
		);
	
		wp_localize_script( 'wpmobi-ajax', 'WPMobi', apply_filters( 'wpmobi_localize_scripts', $localize_params  ) );		

		do_action( 'wpmobi_init' );
		
		// Do the theme init
		do_action( 'wpmobi_theme_init' );		
		
		// Load the language file
		if ( $this->locale ) {
			do_action( 'wpmobi_theme_language', $this->locale );
		}
		
		// Do custom page templates
		if ( isset( $this->get['wpmobi_page_template'] ) ) {
			$page_name = false;
			foreach( $this->custom_page_templates as $name => $template_name ) {
				if ( $template_name[0] == $this->get['wpmobi_page_template'] ) {
					$page_name = $name;
					break;
				}	
			}
			
			if ( $page_name ) {
				$this->is_custom_page_template = true;
				
				$menu_items = $this->add_static_menu_items( array() );
				
				if ( isset( $menu_items[ $page_name ] ) ) {
					$this->custom_page_template_id = $menu_items[ $page_name ]->page_id;
				}
				
				$template_file = basename( $this->get['wpmobi_page_template'] );
				
				if ( !wpmobi_do_template( $template_file . '.php' ) ) {
					echo( "Unable to locate template file " . $template_file );	
				}
			}

			die;	
		}
		
		$this->disable_plugins();
	}

	/*!		\brief Injects a link to a custom CSS file into the footer.
	 *
	 *		This method injects a link to a custom CSS file into the footer.  This routine is tied to the setting \em custom_css_file.
	 *	 
	 *		\ingroup internal	
	 */			
	function inject_custom_css_in_footer() {
		$settings = wpmobi_get_settings();
		
		if ( $settings->custom_css_file ) {
			echo "\n <link type='text/css' rel='stylesheet' href='" . $settings->custom_css_file . "' media='screen' />\n";
		}
	}
	
	/*!		\brief Adds a warning to the \em Compatibility section in the administration panel
	 *
	 *		This method adds a warning message to the MobileView administrational panel.  If there is one or more warning messages,
	 *		a notification message is shown in the WPMobiBoard area with a link to the \em Compatibility section.
	 *
	 *		\param area_or_plugin The area of plugin name the warning is associated with
	 *		\param warning_desc The description of the warning
	 *		\param link A URL to a webpage that describes the warning in more detail.
	 *
	 *		\note The link parameter is currently not used, but will be added in future versions
	 *	 
	 *		\ingroup internal	
	 *		\ingroup compat 
	 */		
	function add_warning( $area_or_plugin, $warning_desc, $link = false ) {
		$this->warnings[ $area_or_plugin ] = array( $area_or_plugin, $warning_desc, $link );	
	}

	/*!		\brief Generates an exhaustive list of plugins and their associated hooks
	 *
	 *		This method is used internally to generate a list of all plugins on the system, and also which WordPress actions and filters the plugin
	 *		uses.  This information can be used to selectively remove plugins while MobileView is running, improving the end user experience
	 *		on sites with plugins that do not natively work with MobileView.
	 *
	 *		A plugin can automatically add themselves to a whitelist of working plugins by filtering the WordPress filter \em wpmobi_plugin_whitelist
	 *		and adding its slug to the array of plugins. 
	 *
	 *		\note Not all plugins can be disabled with this method
	 *
	 *		\param update_list When set to true, the entire list is regenerated.  When false, the list is loaded from settings
	 *	 
	 *		\ingroup internal	 
	 *		\ingroup compat
	 */		
	function generate_plugin_hook_list( $update_list = false ) {
		$settings = $this->get_settings();
	
		if ( $update_list ) {
			$php_files = $this->get_all_recursive_files( WP_PLUGIN_DIR, "php" );
			
			$plugin_whitelist = apply_filters( 'wpmobi_plugin_whitelist', array( 'akismet', 'wpmobi', 'wpmobi-me', 'wpmobi-me-beta' ) );
			
			foreach( $php_files as $plugin_file ) {
				$path_info = explode( '/', $plugin_file );
				
				if ( count( $path_info ) > 2 ) {		
					$plugin_slug = $path_info[1];
					
					if ( in_array( $plugin_slug, $plugin_whitelist ) ) {
						continue;	
					}
						
					$plugin_file_path = WP_PLUGIN_DIR . $plugin_file;
					
					$contents = $this->load_file( $plugin_file_path );
					
					if ( !isset( $this->plugin_hooks[ $plugin_slug ] ) ) {
						$this->plugin_hooks[ $plugin_slug ] = new stdClass;
					}
					
					// Default actions
					if ( preg_match_all( "#add_action\([ ]*[\'\"]+(.*)[\'\"]+,[ ]*[\'\"]+(.*)[\'\"]+[ ]*(\s*[,]\s*+(.*))*\)\s*;#iU", $contents, $matches ) ) {
						for( $i = 0; $i < count( $matches[0] ); $i++ ) {						
							if ( strpos( $matches[2][$i], ' ' ) === false ) {
								$info = new stdClass;
								$info->hook = $matches[1][$i];
								$info->hook_function = $matches[2][$i];
								
								if ( isset( $matches[4][$i] ) && $matches[4][$i] > 0 ) {
								    $info->priority = $matches[4][$i];   
								} else {
								    $info->priority = false;   
								}
								
								$this->plugin_hooks[ $plugin_slug ]->actions[] = $info;
							}
						}
					}
					
					// Default filters
					if ( preg_match_all( "#add_filter\([ ]*[\'\"]+(.*)[\'\"]+,[ ]*[\'\"]+(.*)[\'\"]+[ ]*(\s*[,]\s*+(.*))*\)\s*;#iU", $contents, $matches ) ) {
						for( $i = 0; $i < count( $matches[0] ); $i++ ) {
							if ( strpos( $matches[2][$i], ' ' ) === false ) {
								$info = new stdClass;
								$info->hook = $matches[1][$i];
								$info->hook_function = $matches[2][$i];
								
								if ( isset( $matches[4][$i] ) && $matches[4][$i] > 0 ) {
								    $info->priority = $matches[4][$i];   
								} else {
								    $info->priority = false;   
								}								
								
								$this->plugin_hooks[ $plugin_slug ]->filters[] = $info;
							}
						}
					}				
				}
			}
			
			ksort( $this->plugin_hooks );
			$settings->plugin_hooks = $this->plugin_hooks;
			
			$this->save_settings( $settings );			
			
		} else {
			$this->plugin_hooks = $settings->plugin_hooks;
		}
		
		$this->reload_settings();		
	}

	/*!		\brief Used to check for known plugin incompatibilties
	 *
	 *		This method is used internally to check for known plugins that conflict with MobileView.  When detected, a warning is added
	 *		to the \em Compatibility section of the MobileView administration panel.
	 *	 
	 *		\ingroup internal	 
	 *		\ingroup compat
	 */			
	function check_plugins_for_warnings() {
		$settings = $this->get_settings();
		
		if ( WPMOBI_SIMULATE_ALL || ini_get('safe_mode' ) ) {
			$this->add_warning( 'PHP Safe Mode', __( 'MobileView will not work fully in safe mode. The ability to save custom icons/sets or themes, and write files like the debug log are not available.', 'wpmobi-me' ) );
		}
		
		if ( WPMOBI_SIMULATE_ALL || function_exists( 'wp_super_cache_init' ) ) {
			$this->add_warning( 'WP Super Cache', __('Configuration is required to work with MobileView. It must configured to exclude the user agents that MobileView is enabled for (iphone, ipod, aspen, incognito, webmate, android, dream, cupcake, froyo, blackberry9500, blackberry9520, blackberry9530, blackberry9550, blackberry 9800, blackberry 9850, blackberry 9860, webos, s8000, bada)', 'wpmobi-me' ),  '' );	
		}
		
		if ( WPMOBI_SIMULATE_ALL || class_exists( 'W3_Plugin_TotalCache' ) ) {
			$this->add_warning( 'W3 Total Cache', __('Extra configuration is required. It must be configured to exclude the user agents that MobileView is enabled for (iphone, ipod, aspen, incognito, webmate, android, dream, cupcake, froyo, blackberry9500, blackberry9520, blackberry9530, blackberry9550, blackberry 9800, blackberry 9850, blackberry 9860, webos, s8000, bada)', 'wpmobi-me' ), '' );	
		}
		
		if ( WPMOBI_SIMULATE_ALL || function_exists( 'hyper_activate' ) ) {
			$this->add_warning( 'Hyper Cache', __('Extra configuration is required. You must enable the "Detect mobile devices" option, and add these user agents that MobileView is enabled for (iphone, ipod, aspen, incognito, webmate, android, dream, cupcake, froyo, blackberry9500, blackberry9520, blackberry9530, blackberry9550, blackberry 9800, blackberry 9850, blackberry 9860, webos, s8000, bada) to the Mobile agent list.', 'wpmobi-me' ) );	
		}
		
		if ( WPMOBI_SIMULATE_ALL || class_exists( 'WPMinify' ) ) {
			$this->add_warning( 'WPMinify', __( 'Extra configuration is required. Add paths to your active MobileView theme CSS and Javascript files as files to ignore in WPMinify.', 'wpmobi-me' ) );	
		}
		
		if ( WPMOBI_SIMULATE_ALL || function_exists( 'lightbox_styles' ) ) {
			$this->add_warning( 'Lightbox 2', __( 'This plugin will not work correctly in MobileView, and should be disabled below in the Plugin Compatibility section.', 'wpmobi-me' ) );
		}
		
		if ( WPMOBI_SIMULATE_ALL || function_exists( 'cfmobi_check_mobile' ) ) {
			$this->add_warning( 'WP Mobile Edition', __( 'WP Mobile edition should be configured to exclude the user agents that MobileView is enabled for ("iphone", "ipod", "aspen", "incognito", "webmate", "dream", "android", "cupcake", "froyo", "blackberry9500", "blackberry9530", "blackberry9520", "blackberry9550", "webos").', 'wpmobi-me' ) );
		}
		
		if ( WPMOBI_SIMULATE_ALL || function_exists( 'wpmobi_init' ) ) {
			$this->add_warning( 'MobileView 1.x', __( 'MobileView cannot co-exist with MobileView 1.x.  Disable it first in the WordPress Plugins settings.', 'wpmobi-me' ) );
		}

		if ( WPMOBI_SIMULATE_ALL || ( function_exists( 'gallery_styles' ) && !$settings->plugin_disable_featured_content_gallery ) ) {
			$this->add_warning( 'Featured Content Gallery', __( 'The Featured Content Gallery plugin does not work correctly with MobileView. Please disable it below in the Plugin Compatibility section.', 'wpmobi-me' ) );
		}
		
//		if ( WPMOBI_SIMULATE_ALL || ( function_exists( 'id_activate_hooks' ) && !$settings->plugin_disable_intensedebate ) ) {
//			$this->add_warning( 'IntenseDebate', __( 'IntenseDebate is not fully supported in MobileView at this time.', 'wpmobi-me' ) );
//		}

		$permalink_structure = get_option('permalink_structure');
		if ( WPMOBI_SIMULATE_ALL || !$permalink_structure ) {
			$this->add_warning( 'WordPress Permalinks', sprintf( __( 'MobileView requires pretty permalinks to be enabled within WordPress. %sMore Info%s', 'wpmobi-me' ), '<a href="http://codex.wordpress.org/Using_Permalinks" target="_blank">', ' &raquo;</a>' ) );			
		}
		
	}
	
	/*!		\brief Used to remove the WordPress filters and actions from incompatible plugins
	 *
	 *		This method is used internally to remove the WordPress filters and actions for plugins known to interfere with
	 *		MobileView or any mobile themes.  The information used to disable these plugins is obtained from the generate_plugin_hook_list() method.
	 *	 
	 *		\ingroup internal	 
	 *		\ingroup compat
	 */		
	function disable_plugins() {
		$settings = $this->get_settings();
		
		if ( $settings->plugin_hooks && count( $settings->plugin_hooks ) ) {
			foreach( $settings->plugin_hooks as $name => $hook_info ) {
				$proper_name = "plugin_disable_" . str_replace( '-', '_', $name );
				
				if ( isset( $settings->$proper_name ) && $settings->$proper_name ) {

					if ( isset( $hook_info->filters ) && count( $hook_info->filters ) ) {
						foreach( $hook_info->filters as $hooks ) {
							WPMOBI_DEBUG( WPMOBI_VERBOSE, "Disable filter [" . $hooks->hook . "] with function [" . $hooks->hook_function . "]" );
							if ( $hooks->priority ) {
								remove_filter( $hooks->hook, $hooks->hook_function, $hooks->priority );
							} else { 
								remove_filter( $hooks->hook, $hooks->hook_function );	
							}
						}
					}
					
					if ( isset( $hook_info->actions ) && count( $hook_info->actions ) ) {
						foreach( $hook_info->actions as $hooks ) {
							WPMOBI_DEBUG( WPMOBI_VERBOSE, "Disable action [" . $hooks->hook . "] with function [" . $hooks->hook_function . "]" );
							if ( $hooks->priority ) {
								remove_action( $hooks->hook, $hooks->hook_function, $hooks->priority );
							} else {
								remove_action( $hooks->hook, $hooks->hook_function );
							}
						}
					}
				}	
			}
		}
	}
	
	/*!		\brief Returns a list of all children page IDs	 
	 *
	 *		This method is returns sa list of all the page IDs for the children of the specified parent.
	 *
	 *		\param parent The page ID for the parent
	 *	 
	 *		\ingroup menus	 
	 */		
	function parent_and_children_menu_ids( $parent ) {
		global $wpdb;
		
		$all_ids = array( $parent );
		
		$sql = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'page'", $parent );
		$result = $wpdb->get_results( $sql );
		if ( $result ) {
			foreach( $result as $title ) {
				$all_ids = array_merge( $all_ids, $this->parent_and_children_menu_ids( $title->ID ) );
			}
		}
				
		return $all_ids;	
	}
	
	/*!		\brief Removes a directory
	 *
	 *		This method removes all the files in a particular directory, and then removes the directory. 
	 *
	 *		\param dir_name The name of the directory to delete
	 *
	 *		\note This method does not recurse into subdirectories.  It is assume that the directory is only one level deep.
	 *	 
	 *		\ingroup internal	 
	 *		\ingroup files
	 */		
	function remove_directory( $dir_name ) {
		// Check permissions
		if ( current_user_can( 'manage_options' ) ) {
			$dir = @opendir( $dir_name );
			if ( $dir ) {
				while ( $f = readdir( $dir ) ) {
					if ( $f == '.' || $f == '..' ) continue;
					
					if ( $f == '__MACOSX' ) {
						$this->remove_directory( $dir_name . '/' . $f );	
					}
					
					@unlink( $dir_name . '/' . $f );	
				}
				
				closedir( $dir );
				
				rmdir( $dir_name );
			}	
		}
	}

	/*!		\brief Used to setup the text translation for internationalization.
	 *
	 *		This function is called internally to setup the langage translations.  The currently selected language can be filtered
	 *		using the WordPress filter \em wpmobi_language.
	 *
	 */	
	function setup_languages() {		
		$current_locale = get_locale();
		
		// Check for language override
		$settings = wpmobi_get_settings();
		if ( $settings->force_locale != 'auto' ) {
			$current_locale = $settings->force_locale;
		}
		
		if ( !empty( $current_locale ) ) {
			$current_locale = apply_filters( 'wpmobi_language', $current_locale );
			
			$use_lang_file = false;
			$custom_lang_file = WPMOBI_CUSTOM_LANG_DIRECTORY . '/wpmobi-me-' . $current_locale . '.mo';
			
			if ( file_exists( $custom_lang_file ) && is_readable( $custom_lang_file ) ) {
				$use_lang_file = $custom_lang_file;
				$use_lang_rel_path = WPMOBI_ROOT_DIR.'/../../wpmobi-data/lang';
			} else {
				$lang_file = get_wpmobi_directory() . '/lang/wpmobi-me-' . $current_locale . '.mo';
				if ( file_exists( $lang_file ) && is_readable( $lang_file ) ) {
					$use_lang_file = $lang_file;
					$use_lang_rel_path = WPMOBI_ROOT_DIR.'/lang';
				}
			}
			
			add_filter( 'plugin_locale', array( &$this, 'get_wordpress_locale' ), 10, 2 );
			
			$this->locale = $current_locale;			
					
			if ( $use_lang_file ) {
				load_plugin_textdomain( 'wpmobi-me', false, $use_lang_rel_path );

				WPMOBI_DEBUG( WPMOBI_INFO, 'Loading language file ' . $use_lang_file );
			}

			
			do_action( 'wpmobi_language_loaded', $this->locale );
		}
	}
	
	function get_wordpress_locale( $locale, $domain ) {
		if ( $domain == 'wpmobi-me' ) {
			return $this->locale;
		} else {
			return $locale;
		}
	}

	/*!		\brief Basic initialization functions for WPMobi
	 *
	 *		This function is called internally to initialize WPMobi.  Currently only the language conversions occur here.
	 *
	 */	
	function wpmobi_init() {	
		$is_wpmobi_page = ( strpos( $_SERVER['REQUEST_URI'], WPMOBI_ROOT_DIR ) !== false );
		
		// Only process POST settings on wpmobi-me pages
		if ( $is_wpmobi_page && $this->in_admin_panel() ) {
			$this->process_submitted_settings();	
		}		
		
		do_action( 'wpmobi_settings_processed' );
			
		$this->setup_languages();
	}
	
	/*!		\brief Retrives the MobileView settings object
	 *
	 *		This method can be used to retrieve the main MobileView settings object from the database.  To reduce database load,
	 *		the settings object is cached internally after it is first retrieved from the database; all subsequent calls to this method
	 *		will return the cached copy of the settings.  The save_settings() method will automatically update the internal cache.
	 *
	 *		The settings object is updated dynamically based on the default WPMobiDefaultSettings object; if a setting exists in
	 *		the WPMobiDefaultSettings object but not in the stored settings, the settings object is automatically updated with the default setting.
	 *		The default settings can be filtered with the WordPress filter \em wpmobi_default_settings, which is the mechanism MobileView mobile
	 *		themes are expected to use to configure default settings for each theme.  The global settings object can also be filtered with
	 *		the WordPress filter \em wpmobi_settings.
	 *
	 *		\returns The MobileView settings object
	 *
	 *		\par Adding Default Settings:
	 *		\include wpmobi-default-settings.php
	 *
	 *		\ingroup settings
	 */
	function get_settings() {
		// check to see if we've already loaded the settings
		if ( $this->settings ) {
			return apply_filters( 'wpmobi_settings', $this->settings );	
		}
		
		//update_option( WPMOBI_SETTING_NAME, false );
		
		WPMOBI_DEBUG( WPMOBI_VERBOSE, 'Loading settings from database' );	
		$this->settings = get_option( WPMOBI_SETTING_NAME, false );
		if ( !is_object( $this->settings ) ) {
			$this->settings = unserialize( $this->settings );	
		}

		if ( !$this->settings ) {
			// Return default settings
			$this->settings = new WPMobiSettings;
			
			$defaults = apply_filters( 'wpmobi_default_settings', new WPMobiDefaultSettings );

			foreach( (array)$defaults as $name => $value ) {
				$this->settings->$name = $value;	
			}

			return apply_filters( 'wpmobi_settings', $this->settings );	
		} else {
			// first time pulling them from the database, so update new settings with defaults
			$defaults = apply_filters( 'wpmobi_default_settings', new WPMobiDefaultSettings );
			
			// Merge settings with defaults
			foreach( (array)$defaults as $name => $value ) {
				if ( !isset( $this->settings->$name ) ) {
					$this->settings->$name = $value;	
				}
			}

			return apply_filters( 'wpmobi_settings', $this->settings );	
		}
	}
	
	function reload_settings() {
		$this->settings = false;
		
		return $this->get_settings();
	}	

	/*!		\brief Adds a menu to the main WPMobi menu
	 *
	 *		Adds a menu to the main WPMobi menu.  The menu can be a nested list of
	 *		arrays to create subments.
	 *
	 *		\param menu_type The position on the root WPMobi menu.  Options are currently 'pre' or 'post'.
	 *		\param menu an array representing the menu to add
	 *
	 *		\ingroup menus
	 */
	function add_to_menu( $menu_type, $menu ) {
		switch( $menu_type ) {
			case 'pre':
				$this->pre_menu[] = $menu;
				break;
			case 'post':
				$this->post_menu[] = $menu;
				break;	
		}	
	}


	/*! 	\brief Used to determine the supported device classes within MobileView
	 *
	 *		This method is used to determine the supported device classes within MobileView.  These classes can ultimately be modified by themes using
	 *		various WordPress filters.  The appropriate filters are detailed in the get_supported_theme_device_classes() method.
	 *
	 *		\returns an array of WPMobi supported device classes  
	 */		
	function get_supported_device_classes() {
		global $wpmobi_device_classes;
		
		$supported_classes = apply_filters( 'wpmobi_supported_device_classes', $wpmobi_device_classes );
		
		foreach( $wpmobi_device_classes as $device_class => $device_info ) {
			$supported_classes[] = $device_class;	
		}	
		
		return $supported_classes;
	}
	

	/*! 	\brief Used to determine the supported device classes for a theme.
	 *
	 *		This method can be used to determine the supported device classes for a theme. To indicate which device classes a particular theme
	 *		supports, a theme would modify the data via the WordPress \em wpmobi_supported_device_classes, adding or removing device classes.
	 *		Each supported device class must also have an associated subdirectory within the theme folder.  For example, if a theme were to support
	 *		the "ipad" device class, it would need to add "ipad" using the filter \em wpmobi_theme_device_classes, and also have an ipad directory
	 *		containing template files within its main theme directory.
	 *
	 *	 	The WordPress filter \em wpmobi_supported_device_classes can also be used to modify the support device classes at a global scope.  Using this filter
	 *		it would be possible to disable a particular class of devices, such as iPads or Blackberries. 
	 *
	 *		\returns an array of supported device classes  
	 */	
	function get_supported_theme_device_classes() {		
		global $wpmobi_device_classes;

		// Get a list of all supported mobile device classes
		$supported_device_classes = apply_filters( 'wpmobi_theme_device_classes', $this->get_supported_device_classes() );
		
		$device_listing = array();
		foreach( $wpmobi_device_classes as $class_name => $class_info ) {
			if ( in_array( $class_name, $supported_device_classes ) ) {
				if ( file_exists( $this->get_current_theme_directory() . '/' . $class_name ) ) {
					$device_listing[ $class_name ] = $class_info;	
				}
			} 	
		}
		
		// We have a complete list of device classes and device user agents
		// but we'll give themes and plugins a chance to modify them
		return apply_filters( 'wpmobi_supported_device_classes', $device_listing );		
	}
	
	/*! 	\brief Used to determine the supported user agents.
	 *
	 *		This method can be used to determine which user agents are supported by WPMobi and the active theme.  This method can be 
	 *		filtered using the WordPress filter \em wpmobi_supported_agents.
	 *
	 *		\returns an array of supported mobile user agent strings.  
	 */
	function get_supported_user_agents() {
		// Get a list of the supported theme device classes
		$device_listing = $this->get_supported_theme_device_classes();
		
		// Now we'll create a master list of user agents
		$useragents = array();
		foreach( $device_listing as $device_class => $device_user_agents ) {
			$useragents = array_merge( $useragents, $device_user_agents );	
		}
		
		return apply_filters( 'wpmobi_supported_agents', $useragents );
	}

	/*! 	\brief Checks to see if the user's device is a supported device 
	 *
	 *		This method can be used to determine if a user's device is a device supported by WPMobi and also the active theme.
	 *
	 * 		\returns True if the user's device is support, otherwise false.
	 *
	 *		\note This method always returns true when developer mode is enabled  
	 */	
	function is_supported_device() {
		global $wpmobi_exclusion_list;
		
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$settings = $this->get_settings();

		// If we're in developer mode, always say it's a supported device
		if ( $this->is_in_developer_mode() ) {
			return true;	
		}
		
		// Now that developer mode is out of the way, let's figure out the proper list of user agents
		$supported_agents = $this->get_supported_user_agents();	
		
		// Figure out the active device type and the active device class
		foreach( $supported_agents as $agent ) {
			$friendly_agent = preg_quote( $agent );
			if ( preg_match( "#$friendly_agent#i", $user_agent ) ) {
				$agent_ok = true;
				
				$exclusion_list = apply_filters( 'wpmobi_exclusion_list', $wpmobi_exclusion_list );

				foreach( $exclusion_list as $exclude_user_agent ) {
					$friendly_exclude = preg_quote( $exclude_user_agent );
					if ( preg_match( "#$friendly_exclude#i", $user_agent ) ) {
						$agent_ok = false;
						break;
					}
				}
				
				if ( !$agent_ok ) {
					continue;	
				}
				
				$this->active_device = strtolower( $agent );
				
				$supported_device_classes = $this->get_supported_theme_device_classes();
				foreach ( $supported_device_classes as $device_class => $device_user_agents ) {
					if ( in_array( $agent, $device_user_agents ) ) {
						$this->active_device_class = $device_class;	
					}	
				}
				
				return true;	
			} else {
				$this->active_device = $this->active_device_class = false;	
			}
		}
		
		return false;
	}	
	
	function is_in_developer_mode() {
		$settings = $this->get_settings();	
		return ( $settings->developer_mode == 'on' || ( $settings->developer_mode == 'admins' && current_user_can( 'manage_options' ) ) );
	}

	/*! 	\brief Used to determine the active device class. 
	 *
	 *		This method can be used to determinie the active device class.  When in developer mode, this method returns "iphone" by default.
	 *		To override this behavior, use the WordPress filter	\em wpmobi_developer_mode_device_class.
	 *
	 * 		\returns The active device class for mobile users.  
	 */	
	function get_active_device_class() {
		$settings = $this->get_settings();
		
		if ( $this->is_in_developer_mode() ) {
			// the default theme for developer mode is the iphone
			// a developer could override this by implementing the following filter in the functions.php file of the active theme
			return apply_filters( 'wpmobi_developer_mode_device_class', $settings->developer_mode_device_class );	
		} else {
			return $this->active_device_class;	
		}
	}

	/*!		\brief Attempts to activate a support and auto-upgrade license for the current site
	 *
	 *		This method attempts to activate a support and auto-upgrade license for the current site using the CLCAPI object.
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup clc
	 */				
	function activate_license() {
		$clc_api = $this->get_clc_api();
		if ( $clc_api ) {
			$clc_api->user_add_license( 'wpmobi-me' );
			
			$settings = wpmobi_get_settings();
			
			// Force a license check next time
			$settings->last_clcid_time = 0;
			$this->save_settings( $settings );
		}
	}
	
	/*!		\brief Attempts to remove a support and auto-upgrade license
	 *
	 *		This method attempts to activate a support and auto-upgrade license for the current site using the CLCAPI object.  
	 *
	 *		\param site The site to remove. If not set, the $_POST['site'] parameter will be used instead.
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup clc
	 */					
	function remove_license( $site = false ) {
		$clc_api = $this->get_clc_api();
		if ( $clc_api ) {
			if ( !$site ) {
				$site = $this->post['site'];
			}
			
			$clc_api->user_remove_license( 'wpmobi-me', $site );	
		}
	}

	/*!		\brief Retrieves the active mobile device
	 *
	 *		This method is used to retreive the active mobile device, such as "iphone" or "ipod".
	 *
	 *		\returns A string representing the active mobile device
	 *
	 *		\ingroup wpmobiglobal
	 */					
	function get_active_mobile_device() {
		return $this->active_device;
	}	
	
	/*!		\brief Echos the active mobile device
	 *
	 *		This method is used to echo the active mobile device, such as "iphone" or "ipod".
	 *
	 *		\ingroup wpmobiglobal
	 */	
	function active_mobile_device() {
		echo $this->get_active_mobile_device();
	}
	
	/*!		\brief Retrieves the CLCAPI object
	 *
	 *		This method can be used to retrieve the CLCAPI object for communication with the CLC server.
	 *
	 *		\returns The CLCAPI object
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup clc
	 */		
	function get_clc_api() {
		// Can probably do lazy initialization here instead of up top?
		
		return $this->clc_api;	
	}

	function has_site_license() {
		$api = $this->get_clc_api();
		$licenses = $api->user_list_licenses( 'wpmobi-me' );	
		$this_site = $_SERVER['HTTP_HOST'];
		return ( in_array( $this_site, (array)$licenses['licenses'] ) );
	}
	
	/*!		\brief Initializes the CLCAPI object
	 *
	 *		This method creates and initializes the CLCAPI object.  The user's license key and CLCID are retrieved from the setting's object.
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup clc
	 */		
	function setup_clcapi( $clcid = 'default', $key = 'default' ) {
		if ( !$this->clc_api ) {
			// CLC API
			require_once( WPMOBI_DIR . '/include/classes/clc-api.php' );
			require_once( WPMOBI_DIR . '/include/template-tags/clcid.php' );
			
			$settings = $this->get_settings();
			
			if ( $clcid == 'default' ) {
				$clcid = $settings->clcid;	
			}
			
			if ( $key == 'default' ) {
				$key = $settings->wpmobi_license_key;
			}
			
			$this->clc_api = new CLCAPI( $clcid, $key );
		}
	}
	
	/*!		\brief Shows the MobileView banner message in the footer
	 *
	 *		This method displays a message in the footer indicating that the website is proudly running MobileView.  
	 *
	 *		\ingroup wpmobiglobal
	 */		
	function show_wpmobi_message_in_footer() {
		echo '<p>'.sprintf( __( "Powered by %1\$s %2\$s. ", "wpmobi-me" ) . __( "Designed by <a href='http://colorlabsproject.com/' target='_blank'>ColorLabs & Company</a>. ", "wpmobi-me" ), '<a href="http://wordpress.org/extend/plugins/mobileview/" target="_blank">MobileView</a>', WPMOBI_VERSION ).'</p>';
		//echo _e( "<p>By ColorLabs</p>", "wpmobi-me" );
        echo _e( "<p>&copy;2012 <a href='http://colorlabsproject.com/'>ColorLabs & Company</a>. All Rights Reserved.</p>", "wpmobi-me" );
	}

	/*!		\brief Redirects the user to another page
	 *
	 *		This method performs a redirect to another page.
	 *
	 *		\note Requires that no headers have been sent previously  
	 *
	 *		\ingroup wpmobiglobal
	 */			
	function redirect_to_page( $url ) {
		header( 'Location: ' . urldecode($url) );
		die;	
	}
	
	/*!		\brief Performs a check to see if a redirect is required in a mobile theme, and if so, performs the redirect
	 *
	 *		This method checks to see whether or not a redirect is needed in a mobile theme.  Many blogs have a custom home page template for non-mobile users.
	 *		Because this template does not exist on mobile, many users redirect their home page to their blog page (or any other page in WordPress) for users on
	 *		mobile devices.
	 *
	 *		\ingroup wpmobiglobal
	 */			
	function check_for_redirect() {
		$settings = $this->get_settings();
		if ( $settings->enable_home_page_redirect && $this->is_front_page() ) {
			if ( $settings->home_page_redirect_target ) {
				if ( $settings->home_page_redirect_target == 'custom' ) {
					$redirect_target = $settings->home_page_redirect_custom;
				} else {
					$redirect_target = get_permalink( $settings->home_page_redirect_target );
				}
				
				if ( $redirect_target ) {
					$can_do_redirect = true;
					if ( get_option( 'show_on_front', false ) == 'page' ) {
						$front_page = get_option( 'page_on_front' );
						if ( $front_page == $settings->home_page_redirect_target ) {
							$can_do_redirect = false;	
						}
					}
					
					if ( $can_do_redirect ) {
						$this->redirect_to_page( $redirect_target );	
					}
				}	
			}
		}
	}

	/*!		\brief Modified function to determine if we're on the front page
	 *
	 *		This is a modified function to determine if we're on the front page.  Takes into account a few weird corner cases with WordPress.
	 *
	 *		\ingroup wpmobiglobal
	 */		
	function is_front_page() {
		$front_option = get_option( 'show_on_front', false );
		if ( $front_option == 'page' ) {
			$front_page = get_option( 'page_on_front' );
			if ( $front_page ) {
				return is_front_page();	
			} else {
				return is_home();
			}
		} else {
			// user hasn't defined a dedicated front page, so we return true when on the blog page
			return is_home();	
		}	
	}
	
	/*!		\brief Performs a quick check to determine if the user is in the administration panel
	 *
	 *		This method checks to see if the user is in the administration panel in WordPress.
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup admin
	 */		
	function in_admin_panel() {
		return ( strpos( $_SERVER['REQUEST_URI'], '/admin/' ) !== false );	
	}
	
	/*!		\brief Performs initialization for WPMobi for when the administration panel is showing
	 *
	 *		This method performs initialization for WPMobi when the WordPress administration panel is showing. Currently is checks to see
	 *		if any settings have been updated, and handles the POST form submission.  It also checks for plugin updates, queues Javascript scripts,
	 *		localizes Javascript text, and also sets up the Ajax handlers.
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup admin
	 */		
	function initialize_admin_section() {	
		$is_wpmobi_page = ( strpos( $_SERVER['REQUEST_URI'], WPMOBI_ROOT_DIR ) !== false );
		$is_plugins_page = ( strpos( $_SERVER['REQUEST_URI'], 'plugins.php' ) !== false );
							
		// We need the CLCAPI for checking for plugin updates and all the wpmobi-me admin functions
		if ( $is_wpmobi_page || $is_plugins_page ) {
			$this->setup_clcapi();
			$this->check_for_update();
		}

		// only load admin scripts when we're looking at the MobileView page
		if ( $is_wpmobi_page ) {		
			$this->check_plugins_for_warnings();
			$this->generate_plugin_hook_list();
			$minfile = WPMOBI_DIR . '/admin/js/wpmobi-admin.min.js';
			$localize_params = 	array( 
				'wordpress_url' => get_bloginfo( 'wpurl' ),
				'admin_url' => get_bloginfo('wpurl') . '/wp-admin',
				'wpmobi_url' => WPMOBI_URL,
				'admin_nonce' => wp_create_nonce( 'wpmobi_admin' ),
				'upload_header' => __( 'Uploading...', 'wpmobi-me' ),
				'upload_status' => __( 'Your file is currently being uploaded, please wait.', 'wpmobi-me' ),
				'upload_processing_header' => __( 'Upload complete, processing file...', 'wpmobi-me' ),
				'upload_processing_status' => __( 'Your upload has completed, please wait while your file is processed.', 'wpmobi-me' ),
				'upload_done_header' => __( 'Upload completed.', 'wpmobi-me' ),
				'upload_done_set_status' => __( 'Upload completed.', 'wpmobi-me' ) . ' ' . __( 'Your new set is available below.', 'wpmobi-me' ),
				'upload_done_icon_status' => __( 'Upload completed.', 'wpmobi-me' ) . ' ' . __( 'Your new icon is available below.', 'wpmobi-me' ),
				'upload_unzip_header' => __( 'Unzipping icon set...', 'wpmobi-me' ),
				'upload_unzip_status' => __( 'Icon set uploaded, currently unpackaging...', 'wpmobi-me' ),
				'upload_invalid_header' => __( 'Invalid file format.', 'wpmobi-me' ),
				'upload_invalid_status' => __( 'Please upload only .PNG (single image) or .ZIP (icon set) file types.', 'wpmobi-me' ),
				'upload_describe_set' => __( 'Please enter the set information below and click save', 'wpmobi-me' ),
				'are_you_sure_set' => __( 'Delete this set?', 'wpmobi-me' ) . ' ' . __( 'This operation cannot be undone.', 'wpmobi-me' ),
				'are_you_sure_delete' => __( 'Delete this theme and all its files?', 'wpmobi-me' ) . ' ' . __( 'This operation cannot be undone.', 'wpmobi-me' ),
				'reset_admin_settings' => __( 'Reset all MobileView admin settings?', 'wpmobi-me' ) . ' ' . __( 'This operation cannot be undone.', 'wpmobi-me' ),
				'reset_icon_menu_settings' => __( 'Reset Menu Page and Icon settings?', 'wpmobi-me' ) . ' ' . __( 'This operation cannot be undone.', 'wpmobi-me' ),
				'forum_topic_title' => __( 'Please enter a topic title for the support posting.', 'wpmobi-me' ),
				'forum_topic_tags' => __( 'Please enter at least one tag for the support posting.', 'wpmobi-me' ),
				'forum_topic_text' => __( 'Please enter a description for the support posting.', 'wpmobi-me' ),
				'forum_topic_failed' => __( 'There seems to have been a problem posting your support question.  Please try again later.', 'wpmobi-me' ),
				'forum_topic_success' => __( 'Your support question has been posted!', 'wpmobi-me' ),
				'activating_license' => __( 'Activating license, please wait...', 'wpmobi-me' ),
				'copying_text' => __( 'Your Backup Key was copied to the clipboard.', 'wpmobi-me' ),
				'reset_license_text' => __( 'All of your licenses will be reset.  Are you sure?', 'wpmobi-me' ),
				'reset_license_error' => __( 'There was an error resetting your licenses. Please contact customer support.', 'wpmobi-me' )
			);
						
            $localize_params[ 'plugin_url' ] = get_bloginfo('wpurl') . '/wp-admin/admin.php?page='.WPMOBI_ROOT_DIR.'/admin/admin-panel.php';

			wp_enqueue_script( 'jquery-plugins', WPMOBI_URL . '/admin/js/wpmobi-plugins-min.js', 'jquery', md5( WPMOBI_VERSION ) );	

			if ( file_exists( $minfile ) ) {
				wp_enqueue_script( 'wpmobi-me-custom', WPMOBI_URL . '/admin/js/wpmobi-admin.min.js', array( 'jquery-plugins', 'jquery-ui-draggable', 'jquery-ui-droppable' ), md5( WPMOBI_VERSION ) );
			} else {
				wp_enqueue_script( 'wpmobi-me-custom', WPMOBI_URL . '/admin/js/wpmobi-admin.js', array( 'jquery-plugins', 'jquery-ui-draggable', 'jquery-ui-droppable' ), md5( WPMOBI_VERSION ) );			
			}

			// Set up AJAX requests here
			wp_localize_script( 'jquery-plugins', 'WPMobiCustom', $localize_params );
		}	
		
			wp_enqueue_script( 'jquery-ui-draggable');
			wp_enqueue_script( 'jquery-ui-droppable');

		$this->setup_wpmobi_admin_ajax();
	}
	
	/*!		\brief Adds the appropriate actions for handling WPMobi administration Ajax calls
	 *
	 *		This method sets up the appropriate actions for handling the WPMobi administrational panel Ajax calls that use the admin-ajax script
	 *		that is built into WordPress.
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup admin
	 */			
	function setup_wpmobi_admin_ajax() {
		add_action( 'wp_ajax_wpmobi_ajax', array( &$this, 'admin_ajax_handler' ) );	
	}
	

	/*!		\brief Makes an empty file on disk, similar to Linux's touch command
	 *
	 *		This method creates an empty file. 
	 *
	 *		\ingroup wpmobiglobal
	 */				
	function touch( $file_name ) {
		if ( !file_exists( $file_name ) ) {
			$f = fopen( $file_name, 'w+t' );
			if ( $f ) {
				fclose( $f );	
			}	
		}
	}
	
	/*!		\brief Handles all MobileView Ajax calls
	 *
	 *		This method handles all Ajax requests in the administrational panel for WPMobi.  It  checks to make sure the user has the appropriate permissions,
	 *		and also verifies that the security NONCE is valid.  
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup admin
	 */			
	function admin_ajax_handler() {
		if ( current_user_can( 'manage_options' ) ) {
			// Check security nonce
			$wpmobi_nonce = $this->post['wpmobi_nonce'];
			
			if ( !wp_verify_nonce( $wpmobi_nonce, 'wpmobi_admin' ) ) {
				WPMOBI_DEBUG( WPMOBI_SECURITY, 'Invalid security nonce for AJAX call' );			
				exit;	
			}
			
			$this->setup_clcapi();
			header( 'HTTP/1.1 200 OK' );		
			
			$wpmobi_ajax_action = $this->post['wpmobi_action'];
			switch( $wpmobi_ajax_action ) {
				case 'support-posting':
                    $result = $this->clc_api->post_support_topic( $this->post['title'], $this->post['tags'], $this->post['desc'] );
					
					if ( $result ) {
						echo 'ok';
					} 
					break;
				case 'profile':
					include( WPMOBI_ADMIN_AJAX_DIR . '/profile.php' );
					break;	
				case 'regenerate-plugin-list':
					$this->generate_plugin_hook_list( true );
					echo 'ok';
					break;
				case 'activate-license':
					$this->activate_license();
					include( WPMOBI_ADMIN_AJAX_DIR . '/profile.php' );
					break;
				case 'remove-license':
					$this->remove_license();
					include( WPMOBI_ADMIN_AJAX_DIR . '/profile.php' );
					break;
				case 'update-icon-pack':
					require_once( WPMOBI_ADMIN_DIR . '/template-tags/icons.php' );
					include( WPMOBI_ADMIN_AJAX_DIR . '/icon-area.php' );
					break;	
				case 'set-menu-icon':
					$settings = $this->get_settings();
					
					// Clean up SSL links
					if ( strpos( $this->post['icon'], WP_CONTENT_URL ) !== false ) {
						$icon_location = clc_wpmobi_sslize( str_replace( WP_CONTENT_URL, '', $this->post['icon'] ) );
					} else {
					$icon_location = clc_wpmobi_sslize( str_replace( WP_CONTENT_URL, '', $this->post['icon'] ) );
				//		$icon_location = clc_wpmobi_sslize( str_replace( str_replace( 'http://', 'https://', WP_CONTENT_URL ), '', $this->post['icon'] ) );
					}
					
					$settings->temp_menu_icons[ $this->post['title'] ] = $icon_location;
					$this->save_settings( $settings );
					break;
				case 'reset-menu-icons':
					require_once( WPMOBI_ADMIN_DIR . '/template-tags/icons.php' );
					
					$settings = $this->get_settings();
					$settings->temp_menu_icons = $settings->menu_icons = array();
					$settings->temp_disabled_menu_items = $settings->disabled_menu_items = array();
					$this->save_settings( $settings );
					
					require_once( WPMOBI_DIR . '/include/template-tags/menu.php' );
					
					echo wpmobi_get_site_menu_icon( WPMOBI_ICON_DEAULT );
					break;
				case 'enable-menu-item':
					$settings = $this->get_settings();
					$title = (int)$this->post['title'];
					if ( isset( $settings->temp_disabled_menu_items[ $title ] ) ) {
						unset( $settings->temp_disabled_menu_items[ $title ] );
						$this->save_settings( $settings );	
					} 
					break;
				case 'disable-menu-item':
					$items_to_disable = $this->parent_and_children_menu_ids( $this->post['title'] );
					if ( count( $items_to_disable ) ) {
						$settings = $this->get_settings();	
						foreach( $items_to_disable as $key => $item ) {
							$settings->temp_disabled_menu_items[ $item ] = 1;
						}
						
						$this->save_settings( $settings );
					}
					break;
				case 'remove-menu-icon':
					require_once( WPMOBI_ADMIN_DIR . '/template-tags/icons.php' );
					$settings = $this->get_settings();
					if ( isset( $settings->temp_menu_icons[ $this->post['title'] ] ) ) {
						unset( $settings->temp_menu_icons[ $this->post['title'] ] );	
						$this->save_settings( $settings );	
					}
					
					require_once( WPMOBI_DIR . '/include/template-tags/menu.php' );
					
					echo wpmobi_get_site_menu_icon( WPMOBI_ICON_DEFAULT );
					break;
				case 'manage-upload':
					switch ( $_FILES['userfile']['type'] ) {
						case 'image/png':
						case 'image/x-png':
							move_uploaded_file( $_FILES['userfile']['tmp_name'], WPMOBI_CUSTOM_ICON_DIRECTORY . '/' . str_replace( ' ', '-', $_FILES['userfile']['name'] ) );
							echo 'icon-done';
							break;
						case 'application/x-zip-compressed':
						case 'application/zip':
							move_uploaded_file( $_FILES['userfile']['tmp_name'], WPMOBI_TEMP_DIRECTORY . '/' . $_FILES['userfile']['name'] );
							$settings = $this->get_settings();
							$settings->temp_icon_file_to_unzip = WPMOBI_TEMP_DIRECTORY . '/' . $_FILES['userfile']['name'];
							$this->save_settings( $settings );
							echo 'zip';
							break;
						default:
							WPMOBI_DEBUG( WPMOBI_WARNING, 'Unknown file mime type ' . $_FILES['userfile']['type'] );
							echo 'invalid';
							break;	
					}
					break;
				case 'manage-unzip-set':
					$settings = $this->get_settings();
					$directory_name = basename( strtolower( $settings->temp_icon_file_to_unzip ), '.zip' ) . '-' . time();
					@$this->create_directory_if_not_exist( WPMOBI_CUSTOM_SET_DIRECTORY . '/' . $directory_name );
					
					$destination_file = WPMOBI_CUSTOM_SET_DIRECTORY . '/' . $directory_name . '/' . basename( strtolower( $settings->temp_icon_file_to_unzip ) );
					@rename( $settings->temp_icon_file_to_unzip, $destination_file );
					
					ob_start();
					system( 'unzip -d "' . WPMOBI_CUSTOM_SET_DIRECTORY . '/' . $directory_name . '" "' . $destination_file . '"' );
					ob_end_clean();
					
					@unlink( $destination_file );
					@unlink( $settings->temp_icon_file_to_unzip );
					
					if ( file_exists( WPMOBI_CUSTOM_SET_DIRECTORY . '/' . $directory_name . '/wpmobi.info' ) ) {
						echo 'done';							
					} else {
						$settings->temp_icon_set_for_readme = WPMOBI_CUSTOM_SET_DIRECTORY . '/' . $directory_name . '/wpmobi.info';
						$this->save_settings( $settings );
						echo 'create-readme';	
					}

					break;
				case 'delete-icon-pack':
					$pack = $this->get_icon_pack( $this->post['set'] ); 
					if ( $pack ) {
						$this->remove_directory( $pack->location );
					}
					break;
				case 'delete-icon':
					$icon_to_delete = clc_wpmobi_sslize( str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $this->post['icon'] ) );
					@unlink( $icon_to_delete );
					break;
				case 'activate-theme':	
					$settings = wpmobi_get_settings();
					
					$theme_location = $this->post[ 'location' ];
					$theme_name = $this->post[ 'name' ];

					if ( $settings->current_theme_location != $theme_location ) {
						
						$paths = explode( '/', ltrim( rtrim( $theme_location, '/' ), '/' ) );
					
						$settings->current_theme_name = $paths[ count( $paths ) - 1 ];	
						unset( $paths[ count( $paths ) - 1 ] );
						
						$settings->current_theme_location = '/' . implode( '/', $paths );
						$settings->current_theme_friendly_name = $theme_name;
						
						remove_all_filters( 'wpmobi_theme_menu' );
						remove_all_filters( 'wpmobi_default_settings' );
						
						$this->save_settings( $settings );
					}
					break;
				case 'copy-theme':
					$copy_src = WP_CONTENT_DIR . $this->post[ 'location' ];
					$theme_name = $this->convert_to_class_name( $this->post[ 'name' ] );
					
					$num = $this->get_theme_copy_num( $theme_name );
					$copy_dest = WPMOBI_CUSTOM_THEME_DIRECTORY . '/' . $theme_name . '-copy-' . $num;
					
					@$this->create_directory_if_not_exist( $copy_dest );
						
					$this->recursive_copy( $copy_src, $copy_dest );
					
					$readme_file = $copy_dest . '/readme.txt';
					$readme_info = $this->load_file( $readme_file );
					if ( $readme_info ) {
						if ( preg_match( '#Theme Name: (.*)#', $readme_info, $matches ) ) {
							$readme_info = str_replace( $matches[0], 'Theme Name: ' . $matches[1] . ' Copy #' . $num, $readme_info );
							$f = fopen( $readme_file, "w+t" );
							if ( $f ) {
								fwrite( $f, $readme_info );
								fclose( $f );
							}
						}
					} else {
						WPMOBI_DEBUG( WPMOBI_ERROR, "Unable to modify readme.txt file after copy" );	
					}
				
					break;	
				case 'make-child-theme':
					$copy_src = WP_CONTENT_DIR . $this->post[ 'location' ];
					$theme_name = $this->convert_to_class_name( $this->post[ 'name' ] );
					
					$num = $this->get_theme_copy_num( $theme_name );
					$copy_dest = WPMOBI_CUSTOM_THEME_DIRECTORY . '/' . $theme_name . '-child-' . $num;
					
					@$this->create_directory_if_not_exist( $copy_dest );
					
					$this->copy_file( $copy_src . '/readme.txt', $copy_dest . '/readme.txt' );
					$this->copy_file( $copy_src . '/screenshot.png', $copy_dest . '/screenshot.png' );
					
					$readme_file = $copy_dest . '/readme.txt';
					
					if ( file_exists( $readme_file ) ) {
						$readme_info = $this->load_file( $readme_file );
						if ( $readme_info ) {
							if ( preg_match( '#Theme Name: (.*)#', $readme_info, $matches ) ) {
								$readme_info = str_replace( $matches[0], 'Theme Name: ' . $matches[1] . ' Child #' . $num, $readme_info );
								
								$readme_info = $readme_info . "\nParent: " . $this->post[ 'name' ];
								$f = fopen( $readme_file, "w+t" );
								if ( $f ) {
									fwrite( $f, $readme_info );
									fclose( $f );
								}
								
								@$this->copy_file( WPMOBI_CHILD_THEME_TEMPLATE_DIRECTORY . '/root-functions.php', $copy_dest . '/root-functions.php' );
							}
							
							$all_files = $this->get_files_in_directory( $copy_src, false );
							if ( $all_files ) {
								foreach( $all_files as $file_name  ) {
									if ( is_dir( $file_name ) ) {
										@$this->create_directory_if_not_exist( $copy_dest . '/' . basename( $file_name ) );	
										
										@$this->copy_file( WPMOBI_CHILD_THEME_TEMPLATE_DIRECTORY . '/style.css', $copy_dest . '/' . basename( $file_name ) . '/style.css' );
										@$this->copy_file( WPMOBI_CHILD_THEME_TEMPLATE_DIRECTORY . '/functions.php', $copy_dest . '/' . basename( $file_name ) . '/functions.php' );
									}
								}	
							}
						} else {
							WPMOBI_DEBUG( WPMOBI_ERROR, "Unable to modify readme.txt file after copy" );	
						}	
					}
								
					break;
				case 'delete-theme':
					$delete_src = WP_CONTENT_DIR . $this->post[ 'location' ];
					
					$this->recursive_delete( $delete_src );	
					@rmdir( $delete_src );
					
					break;
				case 'dismiss-warning':
					$settings = $this->get_settings();
					if ( $this->post['plugin'] ) {
						if ( !in_array( $this->post['plugin'], $settings->dismissed_warnings ) ) {
							$settings->dismissed_warnings[] = $this->post['plugin'];
							
							$this->save_settings( $settings );
						}	
					}
					
					echo wpmobi_get_plugin_warning_count();
					
					break;
				case 'reset-all-licenses':
					if ( $this->clc_api->reset_all_licenses( 'wpmobi-me' ) ) {
						echo 'ok';	
					} else {
						echo 'fail';	
					}
					break;
				default:
					//echo WPMOBI_ADMIN_AJAX_DIR . '/' . basename( $wpmobi_ajax_action ) . '.php';
					if ( file_exists( WPMOBI_ADMIN_AJAX_DIR . '/' . basename( $wpmobi_ajax_action ) . '.php' ) ) {
						include( WPMOBI_ADMIN_AJAX_DIR . '/' . basename( $wpmobi_ajax_action ) . '.php' );
					} 
					break;
			}	
		} else {
			WPMOBI_DEBUG( WPMOBI_SECURITY, 'Insufficient security privileges for AJAX call' );	
		}		
		
		die;
	}

	/*!		\brief Obtains a list of files by recursively traversing a directory
	 *
	 *		This method can be used to obtain a list of files within and off of a particular directory.  
	 *
	 *		\param dir The directory to search for files in
	 *		\param file_types A string or array representing the file extensions to search for.  These extensions should include the period, i.e. .php or .txt
	 *		\param rel_path The relative path for the files; this parameter is not required in most scenarios, and is used primarily internally for searching
	 *		the directory tree
	 *
	 *		\ingroup wpmobiglobal
	 */		
	function get_all_recursive_files( $dir, $file_types, $rel_path = '' ) {
		$files = array();
		
		if ( !is_array( $file_types ) ) {
			$file_types = array( $file_types );	
		}
				
		$d = opendir( $dir );
		if ( $d ) {
			while ( ( $f = readdir( $d ) ) !== false ) {
				if ( $f == '.' || $f == '..' || $f == '.svn' ) continue;
				
				if ( is_dir( $dir . '/' . $f ) ) {
					$files = array_merge( $files, $this->get_all_recursive_files( $dir . '/' . $f, $file_types, $rel_path . '/' . $f ) );	
				} else {					
					foreach( $file_types as $file_type ) {
						if ( strpos( $f, $file_type ) !== false ) {
							$files[] = $rel_path . '/' . $f;
							break;	
						}	
					}
				}
			}
			
			closedir( $d );	
		}
		
		return $files;	
	}
	
	/*!		\brief Creates a directory if it does not already exist
	 *
	 *		This method checks to see if a directory exists on disk.  If it does not, an attempt will be made to create it.
	 *
	 *		\param dir The directory to check and possibly create	 
	 *
	 *		\ingroup wpmobiglobal
	 */			
	function create_directory_if_not_exist( $dir ) {
		if ( !file_exists( $dir ) ) {
			WPMOBI_DEBUG( WPMOBI_INFO, 'Creating directory ' . $dir );
			
			// Try and make the directory
			if ( !wp_mkdir_p( $dir ) ) {
				$this->directory_creation_failure = true;

				WPMOBI_DEBUG( WPMOBI_ERROR, 'Unable to create directory ' . $dir );
			}	
		}	
	}
	
	/*!		\brief Checks to make sure all the required WPMobi directories exist
	 *
	 *		This method checks to make sure all the required WPMobi directories exist, and if not, attempts to create them.	 
	 *
	 *		\ingroup wpmobiglobal
	 */		
	function check_directories() {		
		$this->create_directory_if_not_exist( WPMOBI_BASE_CONTENT_DIR );		
		$this->create_directory_if_not_exist( WPMOBI_TEMP_DIRECTORY );
		$this->create_directory_if_not_exist( WPMOBI_BASE_CONTENT_DIR . '/cache' );
		$this->create_directory_if_not_exist( WPMOBI_BASE_CONTENT_DIR . '/themes' );	
		$this->create_directory_if_not_exist( WPMOBI_BASE_CONTENT_DIR . '/modules' );
		$this->create_directory_if_not_exist( WPMOBI_CUSTOM_SET_DIRECTORY );
		$this->create_directory_if_not_exist( WPMOBI_CUSTOM_ICON_DIRECTORY );
		$this->create_directory_if_not_exist( WPMOBI_CUSTOM_LANG_DIRECTORY );
		$this->create_directory_if_not_exist( WPMOBI_CUSTOM_SETTINGS_DIRECTORY );
		$this->create_directory_if_not_exist( WPMOBI_DEBUG_DIRECTORY );
		
		if ( $this->directory_creation_failure ) {
			$this->add_warning( 
				__( "Directory Problem", "wpmobi-me" ), 
				__( "One or more required directories could not be created", "wpmobi-me" ),
				' '
			);
		}
	}

	/*!		\brief Instructs WordPress on the length to use for excerpts
	 *
	 *		This is the main hook that instructs WordPress on the length to use for excerpts.  The default excerpt length is 24 words, and can be 
	 *		adjusted using the WordPress filter \em wpmobi_excerpt_mode.	 
	 *
	 *		\returns The length of the excerpt in words
	 *
	 *		\ingroup wpmobiglobal
	 */		
	function get_excerpt_length( $length ) {
		$settings = $this->get_settings();
		
		return apply_filters( 'wpmobi_excerpt_length', 24 );	
	}
	
	/*!		\brief Instructs WordPress on the text to use for "more" in excerpts
	 *
	 *		This is the main hook that instructs WordPress what text to use for "more" in the excerpts.  The default text is " ...", and can be
	 *		adjusted using the WordPress filter \em wpmobi_excerpt_more.
	 *
	 *		\returns A string representing the text to use for "more" in the excerpts
	 *
	 *		\ingroup wpmobiglobal
	 */		
	function get_excerpt_more( $more ) {
		$settings = $this->get_settings();
		
		return apply_filters( 'wpmobi_excerpt_more', ' ...' );		
	}

	/*!		\brief Loads a file from disk
	 *
	 *		This method loads a file from diskk
	 *
	 *		\returns The contents of the file loaded from disk, otherwise an empty string
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup helpers
	 */		
	function load_file( $file_name ) {
		$contents = '';
		
		$f = fopen( $file_name, 'rb' );
		if ( $f ) {
			while ( !feof( $f ) ) {
				$new_contents = fread( $f, 8192 );
				$contents = $contents . $new_contents;	
			}
			
			fclose( $f );
		}
		
		return $contents;	
	}
	
	/*!		\brief Returns a the active theme directory
	 *
	 *		This method returns the directory of the active theme directory
	 *
	 *		\returns A string representing the active theme directory
	 *
	 *		\ingroup wpmobiglobal
	 */			
	function get_current_theme_directory() {
		return WP_CONTENT_DIR . $this->get_current_theme_location();
	}
	
	/*!		\brief Returns a valid URL to the active theme directory.
	 *
	 *		This method returns a valid URL to the active theme directory.
	 *
	 *		\returns A string representing the active theme directory's URL
	 *
	 *		\ingroup wpmobiglobal
	 */			
	function get_current_theme_uri() {
		return clc_wpmobi_sslize( WP_CONTENT_URL . $this->get_current_theme_location() );	
	}
	
	/*!		\brief Used to determine the current theme name
	 *
	 *		This method returns the current theme name, for example \em Classic.
	 *
	 *		\returns A string representing the currently activated theme name
	 *
	 *		\ingroup wpmobiglobal
	 */			
	function get_current_theme() {
		$settings = $this->get_settings();
		
		return $settings->current_theme_name;		
	}
	
	/*!		\brief Used to determine the current theme location
	 *
	 *		This method returns the current theme location.  It does not take into account the mobile device class.  For example
	 *		this method will return ../themes/theme-name instead of ../themes/theme-name/iphone.  This location is relative to the user's
	 *		\em wp-content directory.
	 *
	 *		\returns A string representing the currently activated theme location.
	 *
	 *		\ingroup wpmobiglobal
	 */			
	function get_current_theme_location() {
		$settings = $this->get_settings();
		
		return $settings->current_theme_location . '/' . $settings->current_theme_name;			
	}
	
	function add_ignored_urls() {
		$ignored_urls = $this->get_ignored_url_list();
		
		if ( $ignored_urls ) {
			$new_urls = array();
			foreach( $ignored_urls as $url ) {
				$new_urls[] = "'" . $url . "'";	
			}
			
			echo "<script type='text/javascript'>var wpmobi_ignored_urls = [" . implode( ',', $new_urls ) . "];</script>\n";	
		}			
}
	
	/*!		\brief Adds the heading information to the HEAD area of the active mobile theme
	 *
	 *		This method is called internally to add the HEAD information for the currently active mobile theme.  The main style.css is added,
	 *		the currently active skin, the CSS files that were added using enqueue_css, and the iPhone bookmark icon.
	 *
	 *		The CSS files can be filtered using the WordPress filter \em wpmobi_theme_css_files, and the bookmark icon HTML code can be
	 *		filtered using the WordPress filter \em wpmobi_bookmark_meta.
	 *
	 *		\ingroup wpmobiglobal
	 */		
	function add_mobile_header_info() {
		$settings = $this->get_settings();
		
		if ( $this->get_active_device_class() == "ipad" ) {
			require_once( WPMOBI_DIR . '/include/template-tags/menu.php' );
			
			if ( $settings->glossy_bookmark_icon ) {
				$bookmark_icon = "<link rel='apple-touch-icon' href='" . wpmobi_get_site_menu_icon( WPMOBI_ICON_TABLET_BOOKMARK ) . "' />\n";
			} else {
				$bookmark_icon = "<link rel='apple-touch-icon-precomposed' href='" . wpmobi_get_site_menu_icon( WPMOBI_ICON_TABLET_BOOKMARK ) . "' />\n";		
			}	
		} else {
			if ( $settings->glossy_bookmark_icon ) {
				$bookmark_icon = "<link rel='apple-touch-icon' href='" . wpmobi_get_site_menu_icon( WPMOBI_ICON_BOOKMARK ) . "' />\n";
			} else {
				$bookmark_icon = "<link rel='apple-touch-icon-precomposed' href='" . wpmobi_get_site_menu_icon( WPMOBI_ICON_BOOKMARK ) . "' />\n";
			}
		}	
		
		echo apply_filters( 'wpmobi_bookmark_meta', $bookmark_icon );

		// Add the default stylesheet to the end, use min if available
		$minfile = TEMPLATEPATH . '/style.min.css';
		if ( file_exists( $minfile ) ) {		
			$this->css_files[] = wpmobi_get_bloginfo( 'template_directory' ) . '/style.min.css?ver=' . wpmobi_refreshed_files();
		} else {
			$this->css_files[] = wpmobi_get_bloginfo( 'template_directory' ) . '/style.css?ver=' . wpmobi_refreshed_files();
		}
		// Check for an active skin
		if ( $settings->current_theme_skin != 'none' ) {
			$current_theme = $this->get_current_theme_info();
			if ( isset( $current_theme->skins[ $settings->current_theme_skin ] ) ) {
				$this->css_files[] = $current_theme->skins[ $settings->current_theme_skin ]->skin_url;	
			}
		}
		
		$this->css_files = apply_filters( 'wpmobi_theme_css_files', $this->css_files );
		
		foreach( $this->css_files as $css ) {
			echo "<link rel='stylesheet' type='text/css' media='screen' href='$css' />\n";	
		}
	}
	
	/*!		\brief Determines the name of the current WPMobi theme skin
	 *
	 *		This method can be used to determine the name of the currently active WPMobi theme skin.
	 *
	 *		\returns A string representing the name of the skin, or false if no skin is active
	 *
	 *		\ingroup wpmobiglobal
	 */		
	function get_current_theme_skin() {
		$settings = $this->get_settings();
		
		if ( $settings->current_theme_skin != 'none' ) {
			return $settings->current_theme_skin;
		} else {
			return false;	
		}	
	}
	
	function get_ignored_url_list() {
		$settings = $this->get_settings();
		$url_list = false;
		
		if ( $settings->ignore_urls ) {
			$temp_list = explode( "\n", trim( strtolower( $settings->ignore_urls ) ) );	
			$url_list = array();
			foreach( $temp_list as $list ) {
				$url_list[] = trim( $list );
			}
		}
		
		return $url_list;
	}
	
	/*!		\brief Checks the user agent and COOKIE to see which type of theme should be shown. 
	 *
	 *		This method is called internally to check the user agent and COOKIE value to see which type of theme should be shown, desktop
	 *		or mobile.  The \em wpmobi_switch COOKIE is checked to determine if the user has previously selected the type of theme they would want, 
	 *		and the user agent of the device is also checked.  This method calls is_supported_device() to
	 *		determine whether or not the user's browser is supported by the active WPMobi theme.
	 *
	 *		\ingroup wpmobiglobal
	 */		
	function check_user_agent() {	
		// check and set cookie
		if ( isset( $this->get['wpmobi_switch'] ) ) {
			setcookie( WPMOBI_COOKIE, $this->get['wpmobi_switch'] );
			$this->redirect_to_page( $this->get['redirect'] );
		}
		
		// If we're in the admin, we're not a mobile device
		if ( is_admin() ) {
			$this->is_mobile_device = false;
			$this->showing_mobile_theme = false;
			
			return;	
		}
		
		// Settings are reloaded inside this function
		$this->is_mobile_device = $this->is_supported_device();		
		
		if ( $this->is_mobile_device ) {
			if ( !isset( $_COOKIE[ WPMOBI_COOKIE ] ) ) {
				$settings = $this->get_settings();
				
				if ( $settings->desktop_is_first_view ) {
					// Show desktop theme initially
					$this->showing_mobile_theme = false;	
				} else {
					$this->showing_mobile_theme = true;	
				}
			} else {
				// If Cookie is set, check value
				if ( $_COOKIE[WPMOBI_COOKIE] === 'mobile' ) {
					$this->showing_mobile_theme = true;
				} else {
					$this->showing_mobile_theme = false;
				}
			}
			
			if ( $this->showing_mobile_theme ) {
				// check ignore list
				$settings = $this->get_settings();
				if ( $settings->ignore_urls ) {
					$url_list = explode( "\n", trim( strtolower( $settings->ignore_urls ) ) );
					
					foreach( $url_list as $url ) {
						$server_url = strtolower( $_SERVER['REQUEST_URI'] );
						
						if ( strpos( $server_url, trim( $url ) ) !== false ) {
							$this->showing_mobile_theme = false;
							$this->is_mobile_device = false;
							break;		
						}
					}	
				}
				
				if ( $settings->enable_buddypress_mobile_support ) {
					if ( strpos( $server_url, '/wp-load.php' ) !== false && isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {
						$this->showing_mobile_theme = false;
						$this->is_mobile_device = false;	
					}
				}
			}
		}
			
		// Filter to programmatically disable MobileView on a certain page
		$this->showing_mobile_theme = apply_filters( 'wpmobi_should_show_mobile_theme', $this->showing_mobile_theme );
		
		// Add switch link for desktop    
		if ( !$this->showing_mobile_theme && $this->is_mobile_device ) {
			add_action( 'wp_footer', array( $this, 'show_desktop_switch_link' ) );	
			add_action( 'wp_head', array( $this, 'include_desktop_switch_css' ) );
		}
	}
	
	function get_class_for_webapp_ignore( $link_url ) {
		$settings = $this->get_settings();
		if ( $settings->ignore_urls ) {
			$url_list = explode( "\n", trim( strtolower( $settings->ignore_urls ) ) );
			
			foreach( $url_list as $url ) {
				$server_url = strtolower( $link_url );
				
				if ( strpos( $server_url, trim( $url ) ) !== false ) {
					return 'ignored';	
				}
			}
		}
	}
	
	/*!		\brief Adds the desktop switch HTML to the desktop theme	 
	 *
	 *		This method is called internally to add the HTML code for the desktop to mobile switching.  It currently reads HTML from a file in the include/html 
	 *		directory called desktop-switch.php.  The HTML code can be modified using the WordPress filter \em wpmobi_desktop_switch_html.
	 *
	 *		\ingroup wpmobiglobal
	 */		
	function show_desktop_switch_link() {
		if ( file_exists( get_wpmobi_directory() . '/include/html/desktop-switch.php' ) ) {
			ob_start();
			include( get_wpmobi_directory() . '/include/html/desktop-switch.php' );
			$switch_html = ob_get_contents();
			ob_end_clean();
			
			echo apply_filters( 'wpmobi_desktop_switch_html', $switch_html );
		}
	}

	/*!		\brief Adds the CSS code for the switch link in the desktop theme	 
	 *
	 *		This method is called internally to add the CSS code for the switch link in the desktop theme
	 *
	 *		\ingroup wpmobiglobal
	 */		
	function include_desktop_switch_css() {
		$settings = $this->get_settings();
		echo "<!--Start Desktop Switch CSS --><style type='text/css'>\n";	
		echo $settings->desktop_switch_css;	
		echo "</style>\n";	
	}

	/*!		\brief Verifies that the administration NONCE is valid
	 *
	 *		This method is called internally from process_submitted_settings() to verify that the administration nonces are valid.
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup admin
	 */		
	function verify_post_nonce() {
		$nonce = $this->post['wpmobi-admin-nonce'];
		if ( !wp_verify_nonce( $nonce, 'wpmobi-post-nonce' ) ) {
			WPMOBI_DEBUG( WPMOBI_SECURITY, "Unable to verify MobileView post nonce" );
			die( 'Unable to verify MobileView post nonce' );	
		}		

		return true;
	}

	/*!		\brief Processes the submission of the settings form in the administration panel
	 *
	 *		This method is used internally to process the submitted settings.  It verifies that the security NONCE is valid and also that the proper
	 *		submit button was pressed.
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup admin
	 */			
	function process_submitted_settings() {
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
			return;	
		}
		
		if ( isset( $this->post['wpmobi-set-info-submit'] ) ) {
			$this->verify_post_nonce();
			
			// this is how we change the set information for a new set
			$settings = $this->get_settings();
			if ( isset( $settings->temp_icon_set_for_readme ) && strlen( $settings->temp_icon_set_for_readme ) ) {
				$f = fopen( $settings->temp_icon_set_for_readme, 'w+t' );
				if ( $f ) { 
					$set_name = $this->post['wpmobi-set-name'];
					
					$set_info = "Name: {$set_name}\nDescription: {$set_name}\n";
					fwrite( $f, $set_info );
					fclose( $f );		
					
					$settings->temp_icon_set_for_readme = '';
					$this->save_settings( $settings );
				}
			}
		} else if ( isset( $this->post['wpmobi-submit'] ) ) {
			$this->verify_post_nonce();
			
			$settings = $this->get_settings();
			
			// The license key information has changed
			if ( $settings->clcid != $this->post['clcid'] || $settings->wpmobi_license_key != $this->post['wpmobi_license_key'] ) {				
				// Clear the CLCID cache whenever we save information
				// will force a proper API call next load
				$settings->last_clcid_result = false;
				$settings->last_clcid_licenses = false;
				$settings->clcid_had_license = false;	
				
				$this->setup_clcapi( $this->post['clcid'], $this->post['wpmobi_license_key'] );
				$this->clc_api->invalidate_all_tokens();	
						
				$settings->last_clcid_time = 0;					
			}

			
			foreach( (array)$settings as $name => $value ) {
				if ( isset( $this->post[ $name ] ) ) {
					
					// Remove slashes if they exist
					if ( is_string( $this->post[ $name ] ) ) {						
						$this->post[ $name ] = htmlspecialchars_decode( $this->post[ $name ] );
					}	
					
					$settings->$name = apply_filters( 'wpmobi_setting_filter_' . $name, $this->post[ $name ] );	
				} else {
					// Remove checkboxes if they don't exist as data
					if ( isset( $this->post[ $name . '-hidden' ] ) ) {
						$settings->$name = false;
					}
					
					// check to see if the hidden fields exist
					if ( isset( $this->post[ $name . '_1' ] ) ) {
						// this is an array field
						$setting_array = array();
						
						$count = 1;							
						while ( true ) {
							if ( !isset( $this->post[ $name . '_' . $count ] ) ) {
								break;	
							}	
							
							// don't add empty strings
							if ( $this->post[ $name . '_' . $count ] ) {
								$setting_array[] = $this->post[ $name . '_' . $count ];
							}
							
							$count++;
						}
						
						$settings->$name = $setting_array;	
					}
				}
			}
			
			if ( isset( $this->post['hidden-menu-items'] ) ) {
				$settings->disabled_menu_items = array();
				
				$disable_these = explode( ",", rtrim( $this->post['hidden-menu-items'], "," ) );
				
				if ( count( $disable_these ) ) {
					foreach( $disable_these as $menu_id ) {		
						if ( is_numeric( $menu_id ) ) {
							$settings->disabled_menu_items[ $menu_id ] = 1;
						}
					}	
				} 
				
				$settings->temp_disabled_menu_items = $settings->disabled_menu_items;		
			} 
			
			$settings->menu_icons = $settings->temp_menu_icons;
			
			$this->save_settings( $settings );
			
			do_action( 'wpmobi_settings_saved' );
			
		} else if ( isset( $this->post['wpmobi-submit-reset'] ) ) {
			$this->verify_post_nonce();
			
			WPMOBI_DEBUG( WPMOBI_INFO, "Settings are being reset" );
			
			// remove the setting from the DB
			delete_option( WPMOBI_SETTING_NAME );
			$this->reload_settings();
			
			require_once( WPMOBI_DIR . '/include/template-tags/menu.php' );
			wpmobi_menu_cache_flush();
			
		} else {
			// This code path is probably dead now
			WPMOBI_DEBUG( WPMOBI_WARNING, "Reset failed." );
		
			$settings = $this->get_settings();
			$do_redirect = false;
						
			// Reset the menu icons in the back panel 
			$settings->temp_menu_icons = $settings->menu_icons;
			$settings->temp_disabled_menu_items = $settings->disabled_menu_items;
										
			$this->save_settings( $settings );
			
			if ( $do_redirect ) {
				$this->redirect_to_page( $_SERVER['REQUEST_URI'] );
			}
		}		
	}

	/*!		\brief Determines how many copies of a particular theme exist
	 *
	 *		This method is used internally to determine the number of copies that exist for a theme.  This number is then used to update
	 *		the theme name when it is copied such that no two themes will have the same name.
	 *
	 *		\param base The base theme name, for example "skeleton".
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup helpers
	 */				
	function get_theme_copy_num( $base ) {
		$num = 1;
		while( true ) {
			if ( !file_exists( WPMOBI_CUSTOM_THEME_DIRECTORY . '/' . $base . '-copy-' . $num ) ) {
				break;
			}	
			
			$num++;
		}	
		
		return $num;
	}

	/*!		\brief Saves the MobileView settings object into the WordPress database
	 *
	 *		This method will save the settings object into the WordPress database. Modification of the settings object itself does not result in 
	 * 		persistent settings changes - this method must be called after all modifications are made.
	 *
	 *		\param settings The settings object to save to the database
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup settings
	 */			
	function save_settings( $settings ) {
		$settings = apply_filters( 'wpmobi_update_settings', $settings );

		$serialized_data = serialize( $settings );
					
		WPMOBI_DEBUG( WPMOBI_VERBOSE, 'Saving settings to database' );	
		
		//delete_option( WPMOBI_SETTING_NAME );
		//add_option( WPMOBI_SETTING_NAME, $serialized_data, '', 'no' );	

		update_option( WPMOBI_SETTING_NAME, $serialized_data );
		
		require_once( WPMOBI_DIR . '/include/template-tags/menu.php' );
		wpmobi_menu_cache_flush();		
		
		$this->settings = $settings;
	}

	/*!		\brief Enqueues a CSS file for use in a MobileView mobile theme.
	 *
	 *		This method will enqueue CSS files.  Currently this method just results in the CSS files being injected into the header of a mobile theme,
	 *		but will hopefully cause CSS files to be merged and optimized in future versions of MobileView.
	 *
	 *		\param css The URL for the CSS file
	 *
	 *		\ingroup wpmobiglobal
	 */		
	function enqueue_css( $css ) {
		$this->css_files[] = $css;	
	}
	
	/*!		\brief Converts a full URL into a relative URL
	 *
	 *		This method is called internally to convert long URLs into short URLs that are relative to the user's home URL
	 *
	 *		\param url The long URL, usually contains http://
	 *
	 *		\returns A short URL relative to the user's home directory.  For example, http://somesite.com/somelink will become /somelink.
	 *
	 *		\ingroup wpmobiglobal
	 */			
	function convert_to_internal_url( $url ) {
		$settings = $this->get_settings();
		if ( !$settings->convert_menu_links_to_internal ) {
			// If the user has disabled converting links to internal URLs
			// simply return the default URL
			return $url;
		}
		
		
		$home = rtrim( get_bloginfo( 'url' ), "/" );		
		$url_info = parse_url( $home );
		
		if ( isset( $url_info['scheme'] ) && isset( $url_info['host'] ) ) {
			$root_location = $url_info['scheme'] . '://' . $url_info['host'];
			
			$new_url = str_replace( $root_location, '', $url );	
			
			if ( strlen( $new_url ) == 0 ) {
				$new_url = "/";
			}
		
			return $new_url;
		} else {
			return $url;
		}	
	}

	/*!		\brief Used to determine the URL for an existing icon
	 *
	 *		This method is called internally to determine the URL for an existing icon	 
	 *
	 *		\param short_icon_name The parital path for the existing icon.  Must be relative to the user's wp-content directory.
	 *
	 *		\returns The full URL for the icon if it exists, otherwise false
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup iconssets
	 */		
	function get_url_for_this_icon( $short_icon_name ) {
		if ( file_exists( get_wpmobi_directory() . '/resources/icons/sets/' . $short_icon_name ) ) {
			return get_wpmobi_url() . '/resources/icons/sets/' . $short_icon_name;
		}
	}

	/*!		\brief Performs a recursive copy from one directory to another
	 *
	 *		This method can be used to recursively copy an entire directory. 
	 *
	 *		\param source_dir The source directory for the copy.
	 *		\param dest_dir The destination directory for the copy. This directory will be created if it does not exist.  
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup files
	 */			
	function recursive_copy( $source_dir, $dest_dir ) {
		$src_dir = @opendir( $source_dir );
		if ( $src_dir ) {
			while ( ( $f = readdir( $src_dir ) ) !== false ) {
				if ( $f == '.' || $f == '..' ) {
					continue;
				}		
				
				$cur_file = $source_dir . '/' . $f;
				if ( is_dir( $cur_file ) ) {
					if ( !wp_mkdir_p( $dest_dir . '/' . $f ) ) {
						WPMOBI_DEBUG( WPMOBI_WARNING, "Unable to create directory " . $dest_dir . '/' . $f );	
					}
					
					$this->recursive_copy( $source_dir . '/' . $f, $dest_dir . '/' . $f );
				} else {
					$dest_file = $dest_dir . '/' . $f;
					
					$src = fopen( $cur_file, 'rb' );
					if ( $src ) {
						$dst = fopen( $dest_file, 'w+b' );
						if ( $dst ) {
							while ( !feof( $src ) ) {
								$contents = fread( $src, 8192 );
								fwrite( $dst, $contents );
							}	
							fclose( $dst );	
						} else {
							WPMOBI_DEBUG( WPMOBI_ERROR, 'Unable to open ' . $dest_file . ' for writing' );	
						}
						
						fclose( $src );
					} else {
						WPMOBI_DEBUG( WPMOBI_ERROR, 'Unable to open ' . $cur_file . ' for reading' );
					}
				}	
			}
			
			closedir( $src_dir );	
		}
	}

	/*!		\brief Performs a recursive delete on a directory
	 *
	 *		This method can be used to recursively delete a directory.  Care must be taken when using this method, as it 
	 *		will completely remove all nested subdirectories.
	 *
	 *		\param source_dir The directory to completely remove
	 *
	 *		\note Will only delete directories located off of the base MobileView content directory
	 *
	 *		\ingroup wpmobiglobal
	 *		\ingroup files
	 */		
	function recursive_delete( $source_dir ) {
		// Only allow a delete to occur for directories in the main WPMobi data directory
		if ( strpos( $source_dir, '..' ) !== false || strpos( $source_dir, WPMOBI_BASE_CONTENT_DIR ) === false ) {
			WPMOBI_DEBUG( WPMOBI_SECURITY, 'Not deleting directory ' . $source_dir . ' due to possibly security risk' );
			return;
		}
		
		$src_dir = @opendir( $source_dir );
		if ( $src_dir ) {
			while ( ( $f = readdir( $src_dir ) ) !== false ) {
				if ( $f == '.' || $f == '..' ) {
					continue;
				}		
				
				$cur_file = $source_dir . '/' . $f;
				if ( is_dir( $cur_file ) ) {
					$this->recursive_delete( $cur_file );
					@rmdir( $cur_file );
				} else {
					@unlink( $cur_file );
				}	
			}
			
			closedir( $src_dir );
			
			@rmdir( $src_dir );	
		}
	}	

	/*!		\brief Adds a layer of caching for the ManageWP functions
	 *
	 *		This method is called internally to get updated plugin information for the ManageWP functions.  It provides an added layer of caching
	 *		to help improve admin panel responsiveness
	 */	
	function mwp_get_latest_info() {
    	$latest_info = false;	
    	
    	// Do some basic caching
    	$mwp_info = get_option( 'wpmobi_mwp', false );
    	if ( !$mwp_info || !is_object( $mwp_info ) ) {
    		$mwp_info = new stdClass;
    		$mwp_info->last_check = 0;
    		$mwp_info->last_result = false;
    	}
    	    	
    	$time_since_last_check = time() - $mwp_info->last_check;
    	if ( $time_since_last_check > 300 ) {	
    		$this->setup_clcapi();
	    	$clc_api = $this->get_clc_api();
	    	if ( $clc_api ) {
	    		$latest_info = $clc_api->get_product_version( 'wpmobi-me' );	
	    		if ( $latest_info ) {
	    			$mwp_info->last_result = $latest_info;
	    			$mwp_info->last_check = time();
	    			
	    			// Save the result
	    			update_option( 'wpmobi_mwp', $mwp_info );
	    		}
	    	}   	
    	} else {
    		// Use the cached copy
    		$latest_info = $mwp_info->last_result;
    	}
    	
    	return $latest_info;
	}	
	
	/*!		\brief Filter for obtaining plugin version information for ManageWP
	 *
	 *		This method is called internally to get updated plugin version information for the ManageWP functions. 
	 */		
	function mwp_update_notification( $premium_updates ) {
		if( !function_exists( 'get_plugin_data' ) ) {
			include_once( ABSPATH.'wp-admin/includes/plugin.php');
		}
			
		$myplugin = get_plugin_data( WPMOBI_DIR . '/'.WPMOBI_ROOT_DIR.'.php' );  
		$myplugin['type'] = 'plugin';
		
		$latest_info = $this->mwp_get_latest_info();
		if ( $latest_info ) {		
			// Check to see if a new version is available
			if ( $latest_info['version'] != WPMOBI_VERSION ) {
				$myplugin['new_version'] = $latest_info['version'];
				
				array_push( $premium_updates, $myplugin ) ;
				
				$this->remove_transient_info();
			}
		}
		
		return $premium_updates;
	}	
	
	/*!		\brief Filter for obtaining plugin update information for ManageWP
	 *
	 *		This method is called internally to get the updated plugin URL information for the ManageWP functions. 
	 */		
	function mwp_perform_update( $update ){
		if( !function_exists( 'get_plugin_data' ) ) {
			include_once( ABSPATH.'wp-admin/includes/plugin.php');
		}
			
		$my_addon = get_plugin_data(  WPMOBI_DIR . '/'.WPMOBI_ROOT_DIR.'.php' );  	
		$my_addon[ 'type' ] = 'plugin';
		$latest_info = $this->mwp_get_latest_info();
		if ( $latest_info ) {
			// Check for a new version
			if ( $latest_info['version'] != WPMOBI_VERSION ) {
				$my_addon['url'] = $latest_info['upgrade_url'];
				
				array_push( $update, $my_addon );
			}
		}
		
		return $update;
	}		
}

/*!		\brief Echos the current directory for MobileView
 *
 *		This method can be used to echo the current MobileView directory. Internally this method calls get_wpmobi_directory().
 *
 *		\ingroup wpmobiglobal
 */
function wpmobi_directory() {
	echo get_wpmobi_directory();
}

/*!		\brief Retrieves the current directory for MobileView
 *
 *		This method can be used to retrieve the current MobileView directory
 * 
 *		\returns A string containing the directory on disk for the main MobileView directory
 *
 *		\ingroup wpmobiglobal
 */
function get_wpmobi_directory() {
	return WPMOBI_DIR;
}

/*!		\brief Echos the URL for the main MobileView directory
 *
 *		This method can be used to echo the current URL for the MobileView directory. Internally this method calls get_wpmobi_url().
 *
 *		\ingroup wpmobiglobal
 */
function wpmobi_url() {
	echo get_wpmobi_url();
}

/*!		\brief Returns the URL for the main MobileView  directory
 *
 *		This method can be used to determine the URL for the MobileView  directory. 
 * 
 *		\returns A string representing the URL
 *
 *		\ingroup wpmobiglobal
 */
function get_wpmobi_url() {
	return WPMOBI_URL;	
}

/*!		\brief Can be used to enqueue a CSS script from WPMobi.
 *
 *		This method can be used to enqueue a CSS script from WPMobi.  CSS scripts will hopefully be merged into one in future
 *		releases of MobileView.
 * 
 *		\param css_url The full URL of the CSS file the should be added
 *
 *		\ingroup wpmobiglobal
 */
function wpmobi_enqueue_css( $css_url ) {
	global $wpmobi;
	$wpmobi->enqueue_css( $css_url );	
}

/*!		\brief A substitute for WordPress' bloginfo function.
 *
 *		The method echos a configuration parameter for WPMobi.  If the parameter isn't WPMobi specific, the WordPress configuration 
 *		parameter will be returned.  Internally this function calls wpmobi_get_bloginfo().
 * 
 *		\param setting_name The associated setting name to retrieve.  Please see wpmobi_get_bloginfo() for a complete list.
 *
 *		\returns The associated setting parameter
 *
 *		\ingroup wpmobiglobal
 */
function wpmobi_bloginfo( $setting_name ) {
	echo wpmobi_get_bloginfo( $setting_name );
}

/*!		\brief A substitute for WordPress' get_bloginfo function.
 *
 *		The method returns a configuration parameter for WPMobi.  If the parameter isn't WPMobi specific, the WordPress configuration 
 *		parameter will be returned.
 * 
 *		\param setting_name The associated setting name to retrieve.  The currently supported parameters are:
 *		\arg \c template_directory The currently active WPMobi theme directory
 *		\arg \c template_url Same as template_directory
 *		\arg \c max_upload_size The maximum upload size supported on the server
 *		\arg \c wpmobi_directory The current server directory for WPMobi
 *		\arg \c wpmobi_url The URL associated with the current WPMobi directory on the server
 *		\arg \c version The currently version for MobileView
 *		\arg \c theme_count The number of currently installed themes
 *		\arg \c icon_set_count The number of currently installed icon sets
 *		\arg \c icon_count The number of available icons
 *		\arg \c support_licenses_remaining The number of remaining support and upgrade licenses
 *		\arg \c active_theme_friendly_name The currently active theme's friendly name
 *		\arg \c rss_url The MobileView RSS feed URL.  Takes into account the user's custom settings
 *		\arg \c warnings The number of MobileView compatibility warnings
 *		\arg \c url If a custom redirect target is enabled, it returns that, otherwise the default WordPress url
 *		\arg \c theme_root_directory The root theme directory for the current theme
 *		\arg \c child_theme_directory_uri The child theme directory URI 
 *
 *		\note All other parameters are proxied to get_bloginfo, and will return the WordPress configuration parameters. 
 *
 *		\returns The associated setting parameter
 *		
 *		\ingroup wpmobiglobal
 */
function wpmobi_get_bloginfo( $setting_name ) {
	global $wpmobi;
	$settings = $wpmobi->get_settings();
	
	$setting = false;
	
	switch( $setting_name ) {
		case 'template_directory':
		case 'template_url':
			$setting = $wpmobi->get_template_directory_uri( false );
			break;
		case 'child_theme_directory_uri':
			$setting = $wpmobi->get_stylesheet_directory_uri( false );
			break;
		case 'theme_root_directory':
			$setting = $wpmobi->get_current_theme_directory();
			break;
		case 'max_upload_size':
			$setting = $wpmobi->get_max_upload_size();	
			break;
		case 'site_title':
			$setting = $settings->site_title;
			break;
		case 'wpmobi_directory':
			$setting = get_wpmobi_directory();
			break;
		case 'wpmobi_url':
			$setting = get_wpmobi_url();
			break;
		case 'version':
			$setting = WPMOBI_VERSION;
			break;
		case 'theme_count':
			$themes = $wpmobi->get_available_themes();
			$setting = count( $themes );
			break;
		case 'icon_set_count':
			$icon_sets = $wpmobi->get_available_icon_packs();
			// Remove the custom icon count
			$setting = count( $icon_sets ) - 1;
			break;
		case 'icon_count':
			$icon_sets = $wpmobi->get_available_icon_packs();
			$total_icons = 0;	
			foreach( $icon_sets as $setname => $set ) {
				if ( $setname == "Custom Icons" ) continue;
				
				$icons = $wpmobi->get_icons_from_packs( $setname );
				$total_icons += count( $icons );
			}
			$setting = $total_icons;
			break;
		case 'support_licenses_remaining':
			$licenses = $wpmobi->clc_api->user_list_licenses( 'wpmobi-me' );
			if ( $licenses ) {
				$setting = $licenses['remaining'];	
			} else {
				$setting = 0;	
			}
			break;
		case 'support_licenses_total':
			$licenses = $wpmobi->clc_api->get_total_licenses( 'wpmobi-me' );
			if ( $licenses ) {
				$setting = $licenses;
			} else {
				$setting = 0;	
			}
			break;
		case 'active_theme_friendly_name':
			$theme_info = $wpmobi->get_current_theme_info();
			if ( $theme_info ) {
				$setting = $theme_info->name;
			}
			break;
		case 'rss_url':
			if ( $settings->menu_custom_rss_url ) {
				$setting = $settings->menu_custom_rss_url;	
			} else {
				$setting = get_bloginfo( 'rss2_url' );
			}
			break;
		case 'warnings':
			$setting = wpmobi_get_plugin_warning_count();
			break;
		case 'url':
			if ( $settings->enable_home_page_redirect ) {
				if ( $settings->home_page_redirect_target == 'custom' ) {
					$setting = $settings->home_page_redirect_custom;
				} else {
					$setting = get_permalink( $settings->home_page_redirect_target );
				}
			} else {
				$setting = get_bloginfo( $setting_name );	
			}
			break;	
		case 'search_url':
			if ( function_exists( 'home_url' ) ) {
				$setting = home_url();
			} else {
				$setting = get_bloginfo( 'home' );
			}
			break;
		default:
			// proxy other values to the original get_bloginfo function
			$setting = get_bloginfo( $setting_name );
			break;	
	}
	
	return $setting;	
}

/*!		\brief Retrives the MobileView settings object
 *
 *		This method can be used to retrieve the MobileView settings object.
 * 
 *		\returns The MobileView settings object
 *
 *		\ingroup wpmobiglobal
 *		\ingroup settings
 */
function wpmobi_get_settings() {
	global $wpmobi;
	
	return $wpmobi->get_settings();	
}

/*!		\brief Saves the MobileView settings object to the database
 *
 *		This method can be used to save the MobileView settings object to the database.  Internally this method calls WPMobi::save_settings().
 *
 *		\param settings The settings object to save
 *
 *		\par Typical Usage:
 *		\include save-settings.php
 *
 *		\ingroup wpmobiglobal
 *		\ingroup settings
 */
function wpmobi_save_settings( $settings ) {
	global $wpmobi;
	
	$wpmobi->save_settings( $settings );	
}

/*!		\brief Retrieves an AJAX parameter for a client-side AJAX call
 *
 *		This method can be used to retrive a client-side AJAX parameter for AJAX routines that are initiaited from the JS function WPMobiAjax.
 *
 *		\param name The AJAX parameter name to retreive
 *
 *		\returns The AJAX parameter, or false is it has not been set
 *
 *		\ingroup wpmobiglobal
 */
function wpmobi_get_ajax_param( $name ) {
	global $wpmobi;
	
	if ( isset( $wpmobi->post[ $name ] ) ) {
		return $wpmobi->post[ $name ];	
	}
	
	return false;	
}

/*!	\brief Echos the AJAX parameter for a client-side AJAX call
 *
 *		This method can be used to echo a client-side AJAX parameter for AJAX routines that are initiaited from the JS function WPMobiAjax.
 *
 *		\param name The AJAX parameter name to retreive
 *
 *		\ingroup wpmobiglobal
 */
function wpmobi_the_ajax_param( $name ) {
	global $wpmobi;
	
	if ( isset( $wpmobi->post[ $name ] ) ) {
		return $wpmobi->post[ $name ];	
	}
	
	return false;	
}

/*!	\brief Determines whether or not WordPress 3.x is in multisite mode
 *
 *		This method can be used to determine whether or not WordPress 3.x is configured in multisite mode.
 *
 *		\version 2.0.5
 *		\ingroup wpmobiglobal
 */
function wpmobi_is_multisite_enabled() {
	$settings = wpmobi_get_settings();
	if ( $settings->multisite_force_enable ) {
		return true;	
	} else {
		return ( defined( 'MULTISITE' ) && MULTISITE );
	}
}

/*!	\brief Determines whether or not the primary site in a WordPress 3.x multisite install is showing
 *
 *		This method can be used to determine the primary site in a WordPress 3.x multisite install is showing
 *
 *		\version 2.0.5
 *		\ingroup wpmobiglobal
 */
function wpmobi_is_multisite_primary() {
	global $blog_id;
	return ( $blog_id == 1 );
}	

/*!	\brief Determines whether or not the restoration key was valid
 *
 *		This method can be used to determine whether or not the restoration key in the backup/restore section was valid.
 *
 *		\version 2.0.7
 *		\ingroup wpmobiglobal
 */
function wpmobi_restore_failed() {
	global $wpmobi;
	return ( $wpmobi->restore_failure );
}

/*!	\brief Determines whether or not the current site is a multisite sub-blog
 *
 *		This method can be used to determine whether the current site is a multi-site sub-blog.
 *
 *		\version 2.0.9
 *		\ingroup wpmobiglobal
 */
function wpmobi_is_multisite_secondary() {
	if ( wpmobi_is_multisite_enabled() ) {
		global $blog_id;
		
		return ( $blog_id > 1 );
	} else {
		return false;	
	}
}

/*!	\brief Gets either a hash of the current MobileView version, or hash + time
 *
 *		This function is used in MobileView themes to add a string to the end of theme JS and CSS files. When the Tools setting for refreshing JS and CSS files is enabled, the string added will always be different, ensuring fresh copies of the files are used by the browser. Useful in development.
 *
 *		\ingroup wpmobiglobal
 */
 	function wpmobi_refreshed_files() {
		global $wpmobi;
		$settings = $wpmobi->get_settings();
		$version_string = md5( WPMOBI_VERSION );
		$current_time = time();
		
		if ( $settings->always_refresh_css_js_files ) {
			return ( $version_string . $current_time );
		} else {
			return ( $version_string );	
		}
	}

?>