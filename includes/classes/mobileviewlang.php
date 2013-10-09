<?php
//!		\defgroup admin Administration Panel
//!		\defgroup colabsplugin ColorLabs API
//!		\defgroup compat Compatibility
//!		\defgroup debug Debugging
//!		\defgroup files	Files and Directories
//!		\defgroup mobileviewglobal Global
//!		\defgroup helpers Helpers
//!		\defgroup menus Menu Items
//!		\defgroup modules Modules and Add-ons
//!		\defgroup settings Storing and Retrieving Settings
//!		\defgroup templatetags Template tags 
//!		\defgroup internal MobileView methods
class MobileView {
	//! Contains the main settings object
	var $settings;
	//! Set to true when the user is surfing on a supported mobile device
	var $is_mobile_device;
	//! Set to true when MobileView is showing a mobile theme
	var $showing_mobile_theme;
	//! Contains information about all the tabs in the administrative panel
	var $tabs;
	//! Contains information about the active user's mobile device
	var $active_device;
	//! Contains information about the active user's mobile device class
	var $active_device_class;
	//! A list of CSS files to be included in the css.  Can possibly be cached
	var $css_files;
	//! Contains information about the pre-menu in MobileView
	var $pre_menu;
	//! Contains information about the pre-menu in MobileView
	var $post_menu;
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
	//! Stores a list of all custom MobileView page templates
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
    //! MOBILEVIEW_ROOT_DIR/MOBILEVIEW_ROOT_DIR.php
	var $plugin_name;
	function MobileView() {
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
		$this->custom_page_template_id = MOBILEVIEW_ICON_DEFAULT;
		$this->restore_failure = false;
    $this->plugin_name = MOBILEVIEW_ROOT_DIR ."/".MOBILEVIEW_ROOT_DIR.".php";
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
			require_once( MOBILEVIEW_DIR . '/admin/warnings.php' ); 
			// Administration Panel
			require_once( MOBILEVIEW_DIR . '/admin/admin-init.php' );		
			add_action( 'admin_menu', 'mobileview_admin_menu' );
			add_action( 'mobileview_settings_saved', array( &$this, 'create_settings_backup_file' ) );
		}
		// Set up debug log
		if ( $settings->debug_log ) {
			mobileview_debug_enable( true );	
			mobileview_debug_set_log_level( $settings->debug_log_level );
		}
		MOBILEVIEW_DEBUG( MOBILEVIEW_INFO, 'MobileView Initializations ' . MOBILEVIEW_VERSION );			
		// These actions and filters are always loaded
		add_action( 'init', array( &$this, 'mobileview_init' ) );			
		add_action( 'admin_init', array( &$this, 'initialize_admin_section' ) );
		add_action( 'admin_head', array( &$this, 'mobileview_admin_head' ) );	
		add_filter( 'plugin_action_links', array( &$this, 'mobileview_settings_link' ), 9, 2 );
		add_action( 'wp_ajax_mobileview_client_ajax', array( &$this, 'handle_client_ajax' ) );
		add_action( 'wp_ajax_nopriv_mobileview_client_ajax', array( &$this, 'handle_client_ajax' ) );
		add_action( 'mobileview_settings_saved', array( &$this, 'check_for_restored_settings' ) );
		add_filter( 'mobileview_admin_languages', array( &$this, 'setup_custom_languages' ) );
		add_action( 'mobileview_pre_head', array( &$this, 'add_ignored_urls' ) );
		// iPad
		add_filter( 'mobileview_supported_device_classes', array( &$this, 'setup_ipad_user_agents' ) );
		// WP Super Cache
		add_filter( 'cached_mobile_prefixes', array( &$this, 'filter_wp_super_cache_prefixes' ) );
		add_filter( 'cached_mobile_browsers', array( &$this, 'filter_wp_super_cache_browsers' ) );
		add_shortcode( 'mobileview', array( &$this, 'handle_shortcode' ) );
		add_action( 'after_plugin_row_'.MOBILEVIEW_ROOT_DIR.'/'.MOBILEVIEW_ROOT_DIR.'.php', array( &$this, 'plugin_row' ) );			
		// Load root-functions always for now
		//if ( $this->mobileview_is_mobileview_page() || !is_admin()  ) {	
		if ( true ) {	
			$clear_settings = false;
			// Load the current theme functions.php, or the child root functions if it exists in MobileView themes
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
			if ( file_exists( MOBILEVIEW_BASE_CONTENT_DIR . '/functions.php' ) ) {
				require_once( MOBILEVIEW_BASE_CONTENT_DIR . '/functions.php' );	
			}
			do_action( 'mobileview_functions_loaded' );
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
			do_action( 'mobileview_mobile_theme_showing' );
			// Remove the admin bar in MobileView themes for now
			if ( function_exists( 'show_admin_bar' ) ) {
				add_filter( 'show_admin_bar', '__return_false' );
			}
			// Theme functions
			require_once( MOBILEVIEW_DIR . '/includes/theme.php' );	
			// Compatibility
			require_once( MOBILEVIEW_DIR . '/includes/compat.php' );			
			add_action( 'mobileview_functions_start', array( &$this, 'load_functions_file_for_desktop' ) );
			// These actions and filters are only loaded when MobileView and a mobile theme are active	
			add_action( 'wp', array( &$this, 'check_for_redirect' ) );		
			add_filter( 'init', array( &$this, 'init_theme' ) );
			add_filter( 'excerpt_length', array( &$this, 'get_excerpt_length' ) );
			add_filter( 'excerpt_more', array( &$this, 'get_excerpt_more' ) );
			// New switch hooks
			add_filter( 'template_directory', array( &$this, 'get_template_directory' ) );
			add_filter( 'template_directory_uri', array( &$this, 'get_template_directory_uri' ) );
			add_filter( 'stylesheet_directory', array( &$this, 'get_stylesheet_directory' ) );
			add_filter( 'stylesheet_directory_uri', array( &$this, 'get_stylesheet_directory_uri' ) );
			add_action( 'mobileview_post_head', array( &$this, 'add_mobile_header_info' ) );
			// This is used to add the RSS, email items, etc			
			add_filter( 'mobileview_menu_items', array( &$this, 'add_static_menu_items' ) );
			if ( $settings->make_links_clickable ) {
				add_filter( 'the_content', 'make_clickable' );	
			}
			// Check to see if we're a child theme so we can add the child stylesheet
			if ( $this->is_child_theme() ) {
				add_action( 'mobileview_post_head', array( &$this, 'output_child_scripts' ), 999 );
			}
			if ( isset( $settings->remove_shortcodes ) && strlen( $settings->remove_shortcodes ) ) {
				$this->remove_shortcodes( $settings->remove_shortcodes );	
			}			
		}
		// Setup Post Thumbnails
		$create_thumbnails = apply_filters( 'mobileview_create_thumbnails', $settings->post_thumbnails_enabled && function_exists( 'add_theme_support' ) );
		// Setup Post Thumbnails
		if ( $create_thumbnails ) {
			add_theme_support( 'post-thumbnails' );
			add_image_size( 'wpmobi-new-thumbnail', $settings->post_thumbnails_new_image_size, $settings->post_thumbnails_new_image_size, true );
            add_image_size( 'small-thumbnail', 100, 100, true );
            add_image_size( 'rectangle', 220, 130, true );
            add_image_size( 'medium-thumbnail', 200, 200, true );
            add_image_size( 'large-thumbnail', 400, 400, true );
            add_image_size( 'feat-thumbnail', 640, 420, true );
            add_image_size( 'mobileview-custom', $settings->post_thumbnails_width, $settings->post_thumbnails_height, true );
		}
		$this->custom_page_templates = apply_filters( 'mobileview_custom_templates', $this->custom_page_templates );	
	}	
	function create_settings_backup_file() {
		$settings = mobileview_get_settings();
		$backup_file = MOBILEVIEW_CUSTOM_SETTINGS_DIRECTORY . '/' . time() . '-backup.txt';
		$backup_contents = mobileview_get_encoded_backup_string( $settings );
		if ( $backup_contents ) {
			$f = fopen( $backup_file, 'w+t' );
			if ( $f ) {
				fwrite( $f, $backup_contents );
				fclose( $f );
			}
		}
		// Cleanup old backup files
		$all_backup_files = $this->get_files_in_directory( MOBILEVIEW_CUSTOM_SETTINGS_DIRECTORY, '.txt' );
		if ( is_array( $all_backup_files ) && count( $all_backup_files ) > MOBILEVIEW_MIN_BACKUP_FILES ) {
			$file_times = array();
			foreach( $all_backup_files as $one_file ) {
				$file_times[ filemtime( $one_file ) ] = $one_file;
			}
			// Sort in descending order 
			ksort( $file_times );
			$num_to_delete = count( $file_times ) - MOBILEVIEW_MIN_BACKUP_FILES;
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
		$settings = mobileview_get_settings();
		// Check to see if we should include the functions.php file from the desktop theme
		if ( $settings->include_functions_from_desktop_theme ) {
			$desktop_theme_directory = get_theme_root() . '/'. get_template();	
			$desktop_functions_file = $desktop_theme_directory . '/functions.php';
			// Check to see if the theme has a functions.php file
			if ( file_exists( $desktop_functions_file ) ) {
				if ( $settings->functions_php_inclusion_method != 'translate' ) {
					require_once( $desktop_functions_file );
				} else {
					mobileview_include_functions_file( $desktop_functions_file, dirname( $desktop_functions_file ), dirname( $desktop_functions_file ), 'require_once' );
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
		$file_contents = str_replace( ' bloginfo(', ' mobileview_desktop_bloginfo(', $file_contents );
		$file_contents = str_replace( ' get_bloginfo(', ' mobileview_get_desktop_bloginfo(', $file_contents );
		$include_params = array( 'include', 'include_once', 'require', 'require_once', 'locate_template' );
		foreach( $include_params as $include_param ) {
			$reg_ex = '#' . $include_param . ' *\((.*)\);#';
			if ( preg_match_all( $reg_ex, $file_contents, $match ) ) {
				for( $i = 0; $i < count( $match[0] ); $i++ ) {
					$statement_in_code_that_loads_file = $match[0][$i];
					$new_statement = str_replace( $include_param . ' (', $include_param . '(', $statement_in_code_that_loads_file );
					if ( $include_param == 'locate_template' ) {
						$new_statement = str_replace( $include_param . '(', 'mobileview_locate_template(', $new_statement );
						$new_statement = str_replace( ');', ", '" . $template_path . "', '" . $current_path . "');", $new_statement );
						$file_contents = str_replace( $statement_in_code_that_loads_file, $new_statement, $file_contents );
					} else {
						$current_path = dirname( $file_name );
						$new_statement = str_replace( $include_param . '(', 'mobileview_include_functions_file(', $new_statement );
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
						$mobileview_file_name = $file_info['dirname'] . '/.' . $file_info['basename'] . '.mobileview';
						$create_cached_file = true;
						if ( file_exists( $mobileview_file_name ) ) {
							$last_mod_time = filemtime( $mobileview_file_name );
							if ( ( time() - $last_mod_time  ) < 1 ) {
								$create_cached_file = false;
							}
						}
						if ( $create_cached_file ) {
							$new_file_contents = $this->load_and_expand_functions_file( $my_string, $template_path, dirname( $file_name ), $count + 1 );
							if ( $new_file_contents ) {							
								$f = fopen( $mobileview_file_name, 'w+t' );
								if ( $f ) {
									fwrite( $f, '<?php ' . $new_file_contents );
									fclose( $f );
								}
							}
						}
						$file_contents = str_replace( $statement_in_code_that_loads_file, $include_param . "('" . $mobileview_file_name . "')", $file_contents );
					}
				}
			}
		}
		return $file_contents;
	}
	function check_for_product_upgrade() {
		$current_version = get_option( 'mobileview_version', 0 );
		// Check to see if the version in the data
		if ( $current_version != MOBILEVIEW_VERSION ) {
			// Execute mobileview_upgrade action for plugins and themes to intercept
			// can be used for cleaning CSS caches, etc.
			do_action( 'mobileview_upgrade' );
			// Store the new version in the database
			update_option( 'mobileview_version', MOBILEVIEW_VERSION );
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
			return WP_CONTENT_DIR . $parent_info->location . '/' . apply_filters( 'mobileview_parent_device_class', $this->get_active_device_class() );
		} else {
			return WP_CONTENT_DIR . $theme_info->location . '/' . $this->get_active_device_class();
		}
	}	
	function output_child_scripts() {
		if ( file_exists( STYLESHEETPATH . '/style.min.css' ) ) {
			echo "<link rel='stylesheet' type='text/css' media='screen' href='" . $this->get_stylesheet_directory_uri( false ) . "/style.min.css?ver=" . mobileview_refreshed_files() . "'>\n";	
		}	
		else if ( file_exists( STYLESHEETPATH . '/style.css' ) ) {
			echo "<link rel='stylesheet' type='text/css' media='screen' href='" . $this->get_stylesheet_directory_uri( false ) . "/style.css?ver=" . mobileview_refreshed_files() . "'>\n";	
		}	
	}
	function get_template_directory_uri( $directory, $template = false, $root = false ) {
		$theme_info = $this->get_current_theme_info();
		if ( $this->has_parent_theme() ) {
			$parent_info = $this->get_parent_theme_info();
			return colabsplugin_mobileview_sslize( WP_CONTENT_URL . $parent_info->location . '/' . apply_filters( 'mobileview_parent_device_class', $this->get_active_device_class() ) );
		} else {
			return colabsplugin_mobileview_sslize( WP_CONTENT_URL . $theme_info->location . '/' . $this->get_active_device_class() );
		}
	}
	function get_stylesheet_directory( $directory, $template = false, $root = false ) {
		$theme_info = $this->get_current_theme_info();
		return WP_CONTENT_DIR . $theme_info->location . '/' . $this->get_active_device_class();
	}
	function get_stylesheet_directory_uri( $directory, $template = false, $root = false ) {
		$theme_info = $this->get_current_theme_info();
		return colabsplugin_mobileview_sslize( WP_CONTENT_URL . $theme_info->location . '/' . $this->get_active_device_class() );
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

	function setup_custom_languages( $languages ) {
		$custom_lang_files = $this->get_files_in_directory( MOBILEVIEW_CUSTOM_LANG_DIRECTORY, '.mo' );
		if ( $custom_lang_files && count( $custom_lang_files ) ) {
			foreach( $custom_lang_files as $lang_file ) {
				$languages[ basename( $lang_file, '.mo' ) ] = basename( $lang_file, '.mo' );
			}	
		}
		return $languages;
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
		include( MOBILEVIEW_DIR . '/includes/js/desktop-webapp.js' );
	}
	/*!		\brief Handles the mobileview shortcode
	 *
	 *		This method handles the MobileView shortcode, mobileview.  This shortcode allows content to be targeted for different situations
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
						$new_content = '<span class="mobileview-shortcode-non-mobile">' . $content . '</span>';		
					}
					break;
				case 'desktop':
					if ( $this->is_mobile_device && !$this->showing_mobile_theme ) {
						$new_content = '<span class="mobileview-shortcode-desktop">' . $content . '</span>';	
					}
					break;
				case 'non-webapp':
					if ( $this->is_mobile_device && $this->showing_mobile_theme ) {
						$new_content = '<span class="mobileview-shortcode-mobile-only" style="display: none;">' . $content . '</span>';	
					}
					break;
				case 'webapp':
					if ( $this->is_mobile_device && $this->showing_mobile_theme ) {
						$new_content = '<span class="mobileview-shortcode-webapp-only" style="display: none;">' . $content . '</span>';	
					}					
					break;	
				case 'mobile':
					if ( $this->is_mobile_device && $this->showing_mobile_theme ) {
						$new_content = '<span class="mobileview-shortcode-webapp-mobile">' . $content . '</span>';	
					}									
					break;
			}	
		}
		return do_shortcode( $new_content );
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
				MOBILEVIEW_DEBUG( MOBILEVIEW_ERROR, 'Unable to open ' . $dst_name . ' for writing' );	
			}
			fclose( $src );		
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
	/*!		\brief Adds a static menu item to the main MobileView menu
	 *
	 *		Adds a static menu item to the MobileView menu.  An example would be a 
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
				$bottom_items[ $page_name ] = mobileview_create_menu_item( MOBILEVIEW_ICON_CUSTOM_PAGE_TEMPLATES - $count, 1, $page_name, 'link', false, 0, false, get_bloginfo( 'url' ) . '?mobileview_page_template=' . $page_info[0] );
				$count++;
			}
		}
		// Add Home to the menu if it's enabled
		if ( $settings->menu_show_home ) {
			$top_items[ __( 'Home', 'mobileviewlang' ) ] = mobileview_create_menu_item( MOBILEVIEW_ICON_HOME, 1, __( 'Home', 'mobileviewlang'), 'link', false, 0, false, get_bloginfo( 'url' ) );	
		}	
		// Add email to the menu if it's enabled
		if ( $settings->menu_show_email ) {
			$email_address = get_option( 'admin_email' );
			if ( $settings->menu_custom_email_address ) {
				$email_address = $settings->menu_custom_email_address;	
			}
			$bottom_items[ __( 'Email', 'mobileviewlang' ) ] = mobileview_create_menu_item( MOBILEVIEW_ICON_EMAIL, 1, __( 'Email', 'mobileviewlang'), 'link', false, 0, false, 'mailto:' . $email_address, 'email' );
		}
		// Add RSS icon to the menu if it's enabled
		if ( $settings->menu_show_rss ) {
			$bottom_items[ __( 'RSS', 'mobileviewlang' ) ] = mobileview_create_menu_item( MOBILEVIEW_ICON_RSS, 1, __( 'RSS', 'mobileviewlang'), 'link', false, 0, false, mobileview_get_bloginfo( 'rss_url'), 'feed' );		
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
					$top_items[ $settings->$text_name ] = mobileview_create_menu_item( -100 - $i, 1, $settings->$text_name, 'link', false, 0, false, $settings->$link_name, $custom_class );	
				} else {
					$bottom_items[ $settings->$text_name ] = mobileview_create_menu_item( -100 - $i, 1, $settings->$text_name, 'link', false, 0, false, $settings->$link_name, $custom_class );
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
			echo __( 'There is a new version of MobileView available.', 'mobileviewlang' ) . ' <a href="plugin-install.php?tab=plugin-information&plugin=mobileviewlang&TB_iframe=true&width=640&height=521">' . __( 'View version details' , 'mobileviewlang' ) . '</a>';	
			echo '</div></td>';
		}
    }
	/*!		\brief Adds a "Settings" link beside Deactivate and Edit on the plugins WP admin page
	 *
	 *		This function is used internally.
	 *
	 *		\ingroup internal	 
	 */
	function mobileview_settings_link( $links, $file ) {
	 	if( $file == $this->plugin_name && function_exists( "admin_url" ) ) {
			$settings_link = '<a href="' . admin_url( 'admin.php?page='.MOBILEVIEW_ROOT_DIR.'/admin/admin-init.php' ) . '">' . __('Settings') . '</a>';
			array_push( $links, $settings_link ); // after other links
		}
		return $links;
	}
	/*!		\brief Returns a list of the MobileView module directories
	 *
	 *		MobileView modules are self-contained pieces of code with a paricular functionality. For example, a plugin developer
	 *		may wish to write an add-on module for MobileView that enables certain functionality for BuddyPress.  The output of this method can
	 *		be filtered using the WordPress filter \em mobileview_module_directories.
	 *
	 *		\returns an array of active module directories 
	 *
	 *		\ingroup modules	 	 
	 */	    
    function get_module_directories() {
		$module_dirs = array( 
			get_mobileview_directory() . '/modules',
			MOBILEVIEW_BASE_CONTENT_DIR . '/modules'
		);
		return apply_filters( 'mobileview_module_directories', $module_dirs );   	
    }
	/*!		\brief This function causes all add-on modules to be loaded
	 *
	 *		This function is used internally to pre-load all the available add-on modules for MobileView.  This method 	 
	 *		triggers the WordPress action \em mobileview_module_init after the modules are loaded.
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
		do_action( 'mobileview_module_init', $this );
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
			$theme_info->skins_dir = $theme_location . '/themes';
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
	 *		can add an additional directory.  The output of this method can be filtered using the WordPress filter \em mobileview_theme_directories.
	 *
	 *		\ingroup internal	 
	 *
	 *		\returns An array of  theme directories
	 */		
	function get_theme_directories() {
		array();
		$theme_directories[] = array( get_mobileview_directory() . '/themes', get_mobileview_url() . '/themes' );		
		$theme_directories[] = array( MOBILEVIEW_BASE_CONTENT_DIR . '/themes', MOBILEVIEW_BASE_CONTENT_URL . '/themes' );	
		return apply_filters( 'mobileview_theme_directories', $theme_directories );
	}
	/*!		\brief Returns a list of available themes
	 *
	 *		The method can be used to obtain a list of available themes. The list of themes is generated by reading the theme information
	 *		files in each of the directories returned by get_theme_directories(). The output of this function can be filtered using the 
	 *		WordPress filter \em mobileview_available_themes.
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
									$skin_info->skin_url = $theme_dir[1] . '/' . $f . '/themes/' . basename( $skin );
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
		return apply_filters( 'mobileview_available_themes', $themes );		
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
	/*!		\brief Retrieves the current news items regarding MobileView from ColorLabs
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
	 *		is stored in each icon set's directory in a file called \em mobileview.info.
	 *
	 *		\param icon_pack_location The full location of the icon set on disk
	 *		\param icon_pack_url The full URL for the icon set 
	 *
	 *		\returns An object representing the icon set information, or false if the icon set or associated info file cannot be found
	 *
	 *		\ingroup internal	 
	 */		
	function get_icon_set_information( $icon_pack_location, $icon_pack_url ) {
		$info_file = $icon_pack_location . '/mobileview.info';
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
	 *		WordPress filter \em mobileview_create_site_icon.
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
		$icon_info->url = colabsplugin_mobileview_sslize( WP_CONTENT_URL . $icon );
		return apply_filters( 'mobileview_create_site_icon', $icon_info );
	} 
	/*!		\brief Returns a list of the site icons
	 *
	 *		This method returns a list of all the available site icons. The output from this method can be filtered using the WordPress
	 *		filter \em mobileview_site_icons.
	 *
	 *		\returns An array of objects representing the site icons available to MobileView
	 *
	 *		\ingroup internal
	 *		\ingroup iconssets	 
	 */			
	function get_site_icons() {
		$settings = $this->get_settings();
		$site_icons = array();
		$site_icon[ MOBILEVIEW_ICON_HOME ] = $this->create_site_icon( __( 'Logo/Header', 'mobileviewlang' ), '/plugins/' . MOBILEVIEW_ROOT_DIR . '/resources/icons/classic/Home.png', MOBILEVIEW_ICON_HOME, 'home' );
		$site_icon[ MOBILEVIEW_ICON_BOOKMARK ] = $this->create_site_icon( __( 'iPhone/Android Homescreen', 'mobileviewlang' ), '/plugins/' . MOBILEVIEW_ROOT_DIR . '/includes/images/mobileview_bookmark_icon.png', MOBILEVIEW_ICON_BOOKMARK , 'bookmark' );
		$site_icon[ MOBILEVIEW_ICON_TABLET_BOOKMARK ] = $this->create_site_icon( __( 'iPad Homescreen', 'mobileviewlang' ), '/plugins/' . MOBILEVIEW_ROOT_DIR . '/includes/images/mobileview_ipad_bookmark_icon.png', MOBILEVIEW_ICON_TABLET_BOOKMARK , 'tablet-bookmark' );
		if ( $settings->menu_show_email  ) {
			$site_icon[ MOBILEVIEW_ICON_EMAIL ] = $this->create_site_icon( __( 'Email', 'mobileviewlang' ), '/plugins/' . MOBILEVIEW_ROOT_DIR . '/includes/images/Mail.png', MOBILEVIEW_ICON_EMAIL, 'email' );	
		}		
		if ( $settings->menu_show_rss  ) {
			$site_icon[ MOBILEVIEW_ICON_RSS ] = $this->create_site_icon( __( 'RSS', 'mobileviewlang' ), '/plugins/' . MOBILEVIEW_ROOT_DIR . '/includes/images/RSS.png', MOBILEVIEW_ICON_RSS, 'rss' );	
		}
		// Add custom menu items here		
		for ( $i = 1; $i <= 3; $i++ ) {
			$text_name = 'custom_menu_text_' . $i;
			$link_name = 'custom_menu_link_' . $i;
			$link_spot = 'custom_menu_position_' . $i;
			if ( $settings->$text_name && $settings->$link_name ) {
				$site_icon[ (-100 - $i) ] = $this->create_site_icon( $settings->$text_name, '/plugins/' . MOBILEVIEW_ROOT_DIR . '/includes/images/Default.png', (-100 - $i) , 'custom_' . $i );
			}
		}	
		if ( count( $this->custom_page_templates ) ) {
			$count = 1;
			foreach( $this->custom_page_templates as $page_name => $page_info ) {
				$site_icon[ MOBILEVIEW_ICON_CUSTOM_PAGE_TEMPLATES - $count ] = $this->create_site_icon( $page_name, '/plugins/' . MOBILEVIEW_ROOT_DIR . '/includes/images/Default.png', MOBILEVIEW_ICON_CUSTOM_PAGE_TEMPLATES - $count , 'custom-' . (-$count) );	
				$count++;
			}
		}				
		$site_icon[ MOBILEVIEW_ICON_DEFAULT ] = $this->create_site_icon( __( "Default Page", 'mobileviewlang' ), '/plugins/' . MOBILEVIEW_ROOT_DIR . '/includes/images/Default.png', MOBILEVIEW_ICON_DEFAULT , 'default-prototype' );
		return apply_filters( 'mobileview_site_icons', $site_icon );	
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
	 *		filters \em mobileview_available_icon_sets_pre_sort and \em mobileview_available_icon_sets_post_sort.
	 *
	 *		\returns An array of objects representing the icon sets
	 *
	 *		\ingroup internal	
	 *		\ingroup iconssets 
	 */				
	function get_available_icon_packs() {
		$icon_packs = array();
		$icon_pack_directories = array();
		$icon_pack_directories[] = array( get_mobileview_directory() . '/resources/icons', get_mobileview_url() . '/resources/icons' );		
		$icon_pack_directories[] = array( MOBILEVIEW_BASE_CONTENT_DIR . '/icons', MOBILEVIEW_BASE_CONTENT_URL . '/icons' );
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
		$icon_packs = apply_filters( 'mobileview_available_icon_sets_pre_sort', $icon_packs );
		ksort( $icon_packs );
		return apply_filters( 'mobileview_available_icon_sets_post_sort', $icon_packs );			
	}
	/*!		\brief Called internally to set up the custom icon directory
	 *
	 *		This method is used to set up the custom icon directory.  Currently is adds "Custom Icons" to the list, associating it with the
	 *		directory in /wp-content/mobileview-data
	 *
	 *		\returns A array representing all the icon sets as well as the Custom Icon directory
	 *
	 *		\ingroup internal
	 *		\ingroup iconssets	 
	 */			
	function setup_custom_icons( $icon_pack_info ) {
		$icon_info = array();
		$icon_info[ __( 'Custom Icons', 'mobileviewlang' ) ] = $this->create_icon_set_info(
			__( 'Custom Icons', 'mobileviewlang' ),
			'Custom Icons',
			false,
			'',
			MOBILEVIEW_CUSTOM_ICON_URL,
			MOBILEVIEW_CUSTOM_ICON_DIRECTORY
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
	 *		allowable file extensions are currently png, jpg, jpeg, and gif, but can be filtered using the WordPress filter \em mobileview_image_file_types.
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
		$allowable_extensions = apply_filters( 'mobileview_image_file_types', array( '.png', '.jpg', '.gif', '.jpeg' ) );
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
	function mobileview_is_mobileview_page() {
		return is_admin() && ( strpos( $_SERVER['REQUEST_URI'], MOBILEVIEW_ROOT_DIR ) !== false );	
	}
	/*!		\brief Outputs the MobileView scripts in the administration panel header
	 *
	 *		This method is called internally to determine the proper scripts to use for the administration panel.  To add additional content here, use the
	 *		WordPress action \em mobileview_admin_head.
	 *
	 *		\ingroup internal	
	 *		\ingroup admin 
	 */	
	function mobileview_admin_head() {		
//		$current_scheme = get_user_option('admin_color');
		$settings = $this->get_settings();
		if ( strpos( $_SERVER['REQUEST_URI'], MOBILEVIEW_ROOT_DIR ) !== false ) {
			$version_string = md5( MOBILEVIEW_VERSION );
			$minfile = MOBILEVIEW_DIR . '/admin/css/mobileview-admin.min.css';
			if ( file_exists( $minfile ) ) {
				echo "<link rel='stylesheet' type='text/css' href='" . MOBILEVIEW_URL . "/admin/css/mobileview-admin.min.css?ver=" . $version_string . "' />\n";
			} else {
				echo "<link rel='stylesheet' type='text/css' href='" . MOBILEVIEW_URL . "/admin/css/mobileview-admin.css?ver=" . $version_string . "' />\n";			
			}
//			if ( $current_scheme === 'fresh' ) {
//				echo "<link rel='stylesheet' type='text/css' href='" . MOBILEVIEW_URL . "/admin/css/mobileview-admin-" . $current_scheme . ".css?ver=" . $version_string . "' />\n";		
//			}		
//			echo "<!--[if lte IE 8]>\n";
//			echo "<link rel='stylesheet' type='text/css' href='" . MOBILEVIEW_URL . "/admin/css/mobileview-admin-ie.css?ver=" . $version_string . "' />\n";
//			echo "<![endif]-->\n";
			do_action( 'mobileview_admin_head' );
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
	/*!		\brief Used to inject the custom statistics code into the footer of a MobileView theme
	 *
	 *		This method is called internally to inject custom statistics code into the footer of a MobileView mobile theme.  
	 *		The custom statistics code is defined in the user setting \em custom_stats_code.  The output from this function
	 *		can be filtered using the WordPress filter \em mobileview_custom_stats_code.
	 *	 
	 *		\ingroup internal	 
	 */		
	function put_stats_in_footer() {
		$settings = $this->get_settings();
		echo apply_filters( 'mobileview_custom_stats_code', $settings->custom_stats_code );
	}
	/*!		\brief Used to display the number of queries and page loading time in the footer of a mobile theme
	 *
	 *		This method is called internally to display queries and page loading time in the footer. The output of this function is wrapped in 
	 *		an HTML div with an ID of \em mobileview-query.  The output of this function can be filtered using the WordPress filter
	 *		\em mobileview_footer_load_time.
	 *	 
	 *		\ingroup internal	 
	 */			
	function show_footer_load_time() {
		echo apply_filters( 'mobileview_footer_load_time', '<div id="mobileview-query">' . sprintf( __( "%d queries in %0.1f ms", 'mobileviewlang' ), get_num_queries(), 1000*timer_stop( 0, 4 ) ) . '</div>' );	
	}
	/*!		\brief Used to display the custom footer message
	 *
	 *		This method is called internally to display a custom footer message in a MobileView mobile theme.  The custom footer message is 
	 *		defined in the user setting \em footer_message, and can be filtered using the WordPress filter \em mobileview_custom_footer_message.
	 *	 
	 *		\ingroup internal	 
	 */			
	function show_custom_footer_message() {
		$settings = $this->get_settings();
		echo apply_filters( 'mobileview_custom_footer_message', $settings->footer_message );	
	}
	function handle_client_ajax() {
		$nonce = $this->post['mobileview_nonce'];
		if ( !wp_verify_nonce( $nonce, 'mobileview-ajax' ) ) {
			die( 'Security' );
		}
		if ( isset( $this->post['mobileview_action'] ) ) {
			do_action( 'mobileview_ajax_' . $this->post['mobileview_action'] );	
			exit;
		}
		die;
	}
	/*!		\brief Initializes all theme components
	 *
	 *		This method is called internally from the \em wp_init action, and is used to setup the majority of filters and action hooks 
	 *		that are required for the mobile themes.  The following actions are initiated from this method: \em mobileview_init, \em mobileview_theme_init, and 
	 *		\em mobileview_theme_language.  The plugins that have been disabled by the user in the administration panel are also disabled from this
	 *		method.
	 *	 
	 *		\ingroup internal	 
	 */			
	function init_theme() {	
		$settings = $this->get_settings();
		
		$this->inject_dynamic_javascript();
		
		if ( $settings->footer_message ) {
			add_action( 'wp_footer', array( &$this, 'show_custom_footer_message' ) );	
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
		wp_enqueue_script( 'mobileview-ajax', get_mobileview_url() . '/includes/js/mobileview.js', array( 'jquery' ), md5( MOBILEVIEW_VERSION ) );
		$localize_params = 	array( 
			'ajaxurl' => get_bloginfo( 'wpurl' ) . '/wp-admin/admin-ajax.php',
			'siteurl' => str_replace( array( 'http://' . $_SERVER['SERVER_NAME'] . '','https://' . $_SERVER['SERVER_NAME'] . '' ), '', get_bloginfo( 'url' ) . '/' ),
			'SITETITLE' => str_replace( ' ', '', get_bloginfo( 'title' ) ),
			'security_nonce' => wp_create_nonce( 'mobileview-ajax' ),
			'expiryDays' => $settings->mobileview_webapp_notice_expiry_days
		);
		wp_localize_script( 'mobileview-ajax', 'MobileView', apply_filters( 'mobileview_localize_scripts', $localize_params  ) );		
		do_action( 'mobileview_init' );
		// Do the theme init
		do_action( 'mobileview_theme_init' );		
		// Load the language file
		if ( $this->locale ) {
			do_action( 'mobileview_theme_language', $this->locale );
		}
		// Do custom page templates
		if ( isset( $this->get['mobileview_page_template'] ) ) {
			$page_name = false;
			foreach( $this->custom_page_templates as $name => $template_name ) {
				if ( $template_name[0] == $this->get['mobileview_page_template'] ) {
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
				$template_file = basename( $this->get['mobileview_page_template'] );
				if ( !mobileview_do_template( $template_file . '.php' ) ) {
					echo( "Unable to locate template file " . $template_file );	
				}
			}
			die;	
		}
		$this->disable_plugins();
	}
	function inject_dynamic_javascript() {
		$settings = $this->get_settings();
		$url_path = str_replace( array( 'http://' . $_SERVER['SERVER_NAME'] . '','https://' . $_SERVER['SERVER_NAME'] . '' ), '', get_bloginfo( 'url' ) . '/' );

		if ( isset( $this->get['mobileview'] ) ) {
			switch( $this->get['mobileview'] ) {
				case 'dismiss_welcome':
					setcookie( 'mobileview_welcome', '1', time()+60*60*24*365*5, $url_path );
					$this->redirect_to_page( $this->get['redirect'] );
					break;
			}	
		} 
				
	}
	/*!		\brief Injects a link to a custom CSS file into the footer.
	 *
	 *		This method injects a link to a custom CSS file into the footer.  This routine is tied to the setting \em custom_css_file.
	 *	 
	 *		\ingroup internal	
	 */			
	function inject_custom_css_in_footer() {
		$settings = mobileview_get_settings();
		if ( $settings->custom_css_file ) {
			echo "\n <link type='text/css' rel='stylesheet' href='" . $settings->custom_css_file . "' media='screen' />\n";
		}
	}
	/*!		\brief Adds a warning to the \em Compatibility section in the administration panel
	 *
	 *		This method adds a warning message to the MobileView administrational panel.  If there is one or more warning messages,
	 *		a notification message is shown in the MobileViewBoard area with a link to the \em Compatibility section.
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
	 *		A plugin can automatically add themselves to a whitelist of working plugins by filtering the WordPress filter \em mobileview_plugin_whitelist
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
			$plugin_whitelist = apply_filters( 'mobileview_plugin_whitelist', array( 'akismet', 'mobileview', 'mobileviewlang', 'mobileviewlang-beta' ) );
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
		if ( MOBILEVIEW_SIMULATE_ALL || ini_get('safe_mode' ) ) {
			$this->add_warning( 'PHP Safe Mode', __( 'MobileView will not work fully in safe mode. The ability to save custom icons/sets or themes, and write files like the debug log are not available.', 'mobileviewlang' ) );
		}
		if ( MOBILEVIEW_SIMULATE_ALL || function_exists( 'wp_super_cache_init' ) ) {
			$this->add_warning( 'WP Super Cache', __('Configuration is required to work with MobileView. It must configured to exclude the user agents that MobileView is enabled for (iphone, ipod, aspen, incognito, webmate, android, dream, cupcake, froyo, blackberry9500, blackberry9520, blackberry9530, blackberry9550, blackberry 9800, blackberry 9850, blackberry 9860, webos, s8000, bada)', 'mobileviewlang' ),  '' );	
		}
		if ( MOBILEVIEW_SIMULATE_ALL || class_exists( 'W3_Plugin_TotalCache' ) ) {
			$this->add_warning( 'W3 Total Cache', __('Extra configuration is required. It must be configured to exclude the user agents that MobileView is enabled for (iphone, ipod, aspen, incognito, webmate, android, dream, cupcake, froyo, blackberry9500, blackberry9520, blackberry9530, blackberry9550, blackberry 9800, blackberry 9850, blackberry 9860, webos, s8000, bada)', 'mobileviewlang' ), '' );	
		}
		if ( MOBILEVIEW_SIMULATE_ALL || function_exists( 'hyper_activate' ) ) {
			$this->add_warning( 'Hyper Cache', __('Extra configuration is required. You must enable the "Detect mobile devices" option, and add these user agents that MobileView is enabled for (iphone, ipod, aspen, incognito, webmate, android, dream, cupcake, froyo, blackberry9500, blackberry9520, blackberry9530, blackberry9550, blackberry 9800, blackberry 9850, blackberry 9860, webos, s8000, bada) to the Mobile agent list.', 'mobileviewlang' ) );	
		}
		if ( MOBILEVIEW_SIMULATE_ALL || class_exists( 'WPMinify' ) ) {
			$this->add_warning( 'WPMinify', __( 'Extra configuration is required. Add paths to your active MobileView theme CSS and Javascript files as files to ignore in WPMinify.', 'mobileviewlang' ) );	
		}
		if ( MOBILEVIEW_SIMULATE_ALL || function_exists( 'lightbox_styles' ) ) {
			$this->add_warning( 'Lightbox 2', __( 'This plugin will not work correctly in MobileView, and should be disabled below in the Plugin Compatibility section.', 'mobileviewlang' ) );
		}
		if ( MOBILEVIEW_SIMULATE_ALL || function_exists( 'cfmobi_check_mobile' ) ) {
			$this->add_warning( 'WP Mobile Edition', __( 'WP Mobile edition should be configured to exclude the user agents that MobileView is enabled for ("iphone", "ipod", "aspen", "incognito", "webmate", "dream", "android", "cupcake", "froyo", "blackberry9500", "blackberry9530", "blackberry9520", "blackberry9550", "webos").', 'mobileviewlang' ) );
		}
		if ( MOBILEVIEW_SIMULATE_ALL || function_exists( 'mobileview_init' ) ) {
			$this->add_warning( 'MobileView 1.x', __( 'MobileView cannot co-exist with MobileView 1.x.  Disable it first in the WordPress Plugins settings.', 'mobileviewlang' ) );
		}
		if ( MOBILEVIEW_SIMULATE_ALL || ( function_exists( 'gallery_styles' ) && !$settings->plugin_disable_featured_content_gallery ) ) {
			$this->add_warning( 'Featured Content Gallery', __( 'The Featured Content Gallery plugin does not work correctly with MobileView. Please disable it below in the Plugin Compatibility section.', 'mobileviewlang' ) );
		}
//		if ( MOBILEVIEW_SIMULATE_ALL || ( function_exists( 'id_activate_hooks' ) && !$settings->plugin_disable_intensedebate ) ) {
//			$this->add_warning( 'IntenseDebate', __( 'IntenseDebate is not fully supported in MobileView at this time.', 'mobileviewlang' ) );
//		}
		$permalink_structure = get_option('permalink_structure');
		if ( MOBILEVIEW_SIMULATE_ALL || !$permalink_structure ) {
			$this->add_warning( 'WordPress Permalinks', sprintf( __( 'MobileView requires pretty permalinks to be enabled within WordPress. %sMore Info%s', 'mobileviewlang' ), '<a href="http://codex.wordpress.org/Using_Permalinks" target="_blank">', ' &raquo;</a>' ) );			
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
							MOBILEVIEW_DEBUG( MOBILEVIEW_VERBOSE, "Disable filter [" . $hooks->hook . "] with function [" . $hooks->hook_function . "]" );
							if ( $hooks->priority ) {
								remove_filter( $hooks->hook, $hooks->hook_function, $hooks->priority );
							} else { 
								remove_filter( $hooks->hook, $hooks->hook_function );	
							}
						}
					}
					if ( isset( $hook_info->actions ) && count( $hook_info->actions ) ) {
						foreach( $hook_info->actions as $hooks ) {
							MOBILEVIEW_DEBUG( MOBILEVIEW_VERBOSE, "Disable action [" . $hooks->hook . "] with function [" . $hooks->hook_function . "]" );
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
	 *		using the WordPress filter \em mobileview_language.
	 *
	 */	
	function setup_languages() {		
		$current_locale = get_locale();
		// Check for language override
		$settings = mobileview_get_settings();
		if ( $settings->force_locale != 'auto' ) {
			$current_locale = $settings->force_locale;
		}
		if ( !empty( $current_locale ) ) {
			$current_locale = apply_filters( 'mobileview_language', $current_locale );
			$use_lang_file = false;
			$custom_lang_file = MOBILEVIEW_CUSTOM_LANG_DIRECTORY . '/mobileviewlang-' . $current_locale . '.mo';
			if ( file_exists( $custom_lang_file ) && is_readable( $custom_lang_file ) ) {
				$use_lang_file = $custom_lang_file;
				$use_lang_rel_path = MOBILEVIEW_ROOT_DIR.'/../../mobileview-data/lang';
			} else {
				$lang_file = get_mobileview_directory() . '/lang/mobileviewlang-' . $current_locale . '.mo';
				if ( file_exists( $lang_file ) && is_readable( $lang_file ) ) {
					$use_lang_file = $lang_file;
					$use_lang_rel_path = MOBILEVIEW_ROOT_DIR.'/lang';
				}
			}
			add_filter( 'plugin_locale', array( &$this, 'get_wordpress_locale' ), 10, 2 );
			$this->locale = $current_locale;			
			if ( $use_lang_file ) {
				load_plugin_textdomain( 'mobileviewlang', false, $use_lang_rel_path );
				MOBILEVIEW_DEBUG( MOBILEVIEW_INFO, 'Loading language file ' . $use_lang_file );
			}
			do_action( 'mobileview_language_loaded', $this->locale );
		}
	}
	function get_wordpress_locale( $locale, $domain ) {
		if ( $domain == 'mobileviewlang' ) {
			return $this->locale;
		} else {
			return $locale;
		}
	}
	/*!		\brief Basic initialization functions for MobileView
	 *
	 *		This function is called internally to initialize MobileView.  Currently only the language conversions occur here.
	 *
	 */	
	function mobileview_init() {	
		$is_mobileview_page = ( strpos( $_SERVER['REQUEST_URI'], 'mobileview' ) !== false );
		// Only process POST settings on mobileview pages
		if ( $is_mobileview_page && $this->in_admin_panel() ) {
			$this->process_submitted_settings();
		}		
		do_action( 'mobileview_settings_processed' );
		$this->setup_languages();
	}
	/*!		\brief Retrives the MobileView settings object
	 *
	 *		This method can be used to retrieve the main MobileView settings object from the database.  To reduce database load,
	 *		the settings object is cached internally after it is first retrieved from the database; all subsequent calls to this method
	 *		will return the cached copy of the settings.  The save_settings() method will automatically update the internal cache.
	 *
	 *		The settings object is updated dynamically based on the default MobileViewDefaultSettings object; if a setting exists in
	 *		the MobileViewDefaultSettings object but not in the stored settings, the settings object is automatically updated with the default setting.
	 *		The default settings can be filtered with the WordPress filter \em mobileview_default_settings, which is the mechanism MobileView mobile
	 *		themes are expected to use to configure default settings for each theme.  The global settings object can also be filtered with
	 *		the WordPress filter \em mobileview_settings.
	 *
	 *		\returns The MobileView settings object
	 *
	 *		\par Adding Default Settings:
	 *		\include mobileview-default-settings.php
	 *
	 *		\ingroup settings
	 */
	function get_settings() {
		// check to see if we've already loaded the settings
		if ( $this->settings ) {
			return apply_filters( 'mobileview_settings', $this->settings );	
		}
		
		MOBILEVIEW_DEBUG( MOBILEVIEW_VERBOSE, 'Loading settings from database' );	
		$this->settings = get_option( MOBILEVIEW_SETTING_NAME, false );
		if ( !is_object( $this->settings ) ) {
			$this->settings = unserialize( $this->settings );	
		}

		if ( !$this->settings ) {
			// Return default settings
			$this->settings = new MobileViewSettings;
			$defaults = apply_filters( 'mobileview_default_settings', new MobileViewDefaultSettings );
			foreach( (array)$defaults as $name => $value ) {
				$this->settings->$name = $value;	
			}
			return apply_filters( 'mobileview_settings', $this->settings );	
		} else {
			// first time pulling them from the database, so update new settings with defaults
			$defaults = apply_filters( 'mobileview_default_settings', new MobileViewDefaultSettings );
			// Merge settings with defaults
			foreach( (array)$defaults as $name => $value ) {
				if ( !isset( $this->settings->$name ) ) {
					$this->settings->$name = $value;	
				}
			}
			return apply_filters( 'mobileview_settings', $this->settings );	
		}
	}
	function reload_settings() {
		$this->settings = false;
		return $this->get_settings();
	}	
	/*!		\brief Adds a menu to the main MobileView menu
	 *
	 *		Adds a menu to the main MobileView menu.  The menu can be a nested list of
	 *		arrays to create subments.
	 *
	 *		\param menu_type The position on the root MobileView menu.  Options are currently 'pre' or 'post'.
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
	 *		\returns an array of MobileView supported device classes  
	 */		
	function get_supported_device_classes() {
		global $mobileview_device_classes;
		$supported_classes = apply_filters( 'mobileview_supported_device_classes', $mobileview_device_classes );
		foreach( $mobileview_device_classes as $device_class => $device_info ) {
			$supported_classes[] = $device_class;	
		}	
		return $supported_classes;
	}
	/*! 	\brief Used to determine the supported device classes for a theme.
	 *
	 *		This method can be used to determine the supported device classes for a theme. To indicate which device classes a particular theme
	 *		supports, a theme would modify the data via the WordPress \em mobileview_supported_device_classes, adding or removing device classes.
	 *		Each supported device class must also have an associated subdirectory within the theme folder.  For example, if a theme were to support
	 *		the "ipad" device class, it would need to add "ipad" using the filter \em mobileview_theme_device_classes, and also have an ipad directory
	 *		containing template files within its main theme directory.
	 *
	 *	 	The WordPress filter \em mobileview_supported_device_classes can also be used to modify the support device classes at a global scope.  Using this filter
	 *		it would be possible to disable a particular class of devices, such as iPads or Blackberries. 
	 *
	 *		\returns an array of supported device classes  
	 */	
	function get_supported_theme_device_classes() {		
		global $mobileview_device_classes;
		// Get a list of all supported mobile device classes
		$supported_device_classes = apply_filters( 'mobileview_theme_device_classes', $this->get_supported_device_classes() );
		$device_listing = array();
		foreach( $mobileview_device_classes as $class_name => $class_info ) {
			if ( in_array( $class_name, $supported_device_classes ) ) {
				if ( file_exists( $this->get_current_theme_directory() . '/' . $class_name ) ) {
					$device_listing[ $class_name ] = $class_info;	
				}
			} 	
		}
		// We have a complete list of device classes and device user agents
		// but we'll give themes and plugins a chance to modify them
		return apply_filters( 'mobileview_supported_device_classes', $device_listing );		
	}
	/*! 	\brief Used to determine the supported user agents.
	 *
	 *		This method can be used to determine which user agents are supported by MobileView and the active theme.  This method can be 
	 *		filtered using the WordPress filter \em mobileview_supported_agents.
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
		return apply_filters( 'mobileview_supported_agents', $useragents );
	}
	/*! 	\brief Checks to see if the user's device is a supported device 
	 *
	 *		This method can be used to determine if a user's device is a device supported by MobileView and also the active theme.
	 *
	 * 		\returns True if the user's device is support, otherwise false.
	 *
	 *		\note This method always returns true when developer mode is enabled  
	 */	
	function is_supported_device() {
		global $mobileview_exclusion_list;
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
				$exclusion_list = apply_filters( 'mobileview_exclusion_list', $mobileview_exclusion_list );
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
	 *		To override this behavior, use the WordPress filter	\em mobileview_developer_mode_device_class.
	 *
	 * 		\returns The active device class for mobile users.  
	 */	
	function get_active_device_class() {
		$settings = $this->get_settings();
		if ( $this->is_in_developer_mode() ) {
			// the default theme for developer mode is the iphone
			// a developer could override this by implementing the following filter in the functions.php file of the active theme
			return apply_filters( 'mobileview_developer_mode_device_class', $settings->developer_mode_device_class );	
		} else {
			return $this->active_device_class;	
		}
	}
	/*!		\brief Retrieves the active mobile device
	 *
	 *		This method is used to retreive the active mobile device, such as "iphone" or "ipod".
	 *
	 *		\returns A string representing the active mobile device
	 *
	 *		\ingroup mobileviewglobal
	 */					
	function get_active_mobile_device() {
		return $this->active_device;
	}	
	/*!		\brief Echos the active mobile device
	 *
	 *		This method is used to echo the active mobile device, such as "iphone" or "ipod".
	 *
	 *		\ingroup mobileviewglobal
	 */	
	function active_mobile_device() {
		echo $this->get_active_mobile_device();
	}	
	/*!		\brief Redirects the user to another page
	 *
	 *		This method performs a redirect to another page.
	 *
	 *		\note Requires that no headers have been sent previously  
	 *
	 *		\ingroup mobileviewglobal
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
	 *		\ingroup mobileviewglobal
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
	 *		\ingroup mobileviewglobal
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
	 *		\ingroup mobileviewglobal
	 *		\ingroup admin
	 */		
	function in_admin_panel() {
		return ( strpos( $_SERVER['REQUEST_URI'], '/admin/' ) !== false );	
	}
	/*!		\brief Performs initialization for MobileView for when the administration panel is showing
	 *
	 *		This method performs initialization for MobileView when the WordPress administration panel is showing. Currently is checks to see
	 *		if any settings have been updated, and handles the POST form submission.  It also checks for plugin updates, queues Javascript scripts,
	 *		localizes Javascript text, and also sets up the Ajax handlers.
	 *
	 *		\ingroup mobileviewglobal
	 *		\ingroup admin
	 */		
	function initialize_admin_section() {	
		$is_mobileview_page = ( strpos( $_SERVER['REQUEST_URI'], 'mobileview' ) !== false );
		$is_plugins_page = ( strpos( $_SERVER['REQUEST_URI'], 'plugins.php' ) !== false );
		// only load admin scripts when we're looking at the MobileView page
		if ( $is_mobileview_page ) {		
			$this->check_plugins_for_warnings();
			$this->generate_plugin_hook_list();
			$minfile = MOBILEVIEW_DIR . '/admin/js/mobileview-admin.min.js';
			$localize_params = 	array( 
				'wordpress_url' => get_bloginfo( 'wpurl' ),
				'admin_url' => get_bloginfo('wpurl') . '/wp-admin',
				'mobileview_url' => MOBILEVIEW_URL,
				'admin_nonce' => wp_create_nonce( 'mobileview_admin' ),
				'upload_header' => __( 'Uploading...', 'mobileviewlang' ),
				'upload_status' => __( 'Your file is currently being uploaded, please wait.', 'mobileviewlang' ),
				'upload_processing_header' => __( 'Upload complete, processing file...', 'mobileviewlang' ),
				'upload_processing_status' => __( 'Your upload has completed, please wait while your file is processed.', 'mobileviewlang' ),
				'upload_done_header' => __( 'Upload completed.', 'mobileviewlang' ),
				'upload_done_set_status' => __( 'Upload completed.', 'mobileviewlang' ) . ' ' . __( 'Your new set is available below.', 'mobileviewlang' ),
				'upload_done_icon_status' => __( 'Upload completed.', 'mobileviewlang' ) . ' ' . __( 'Your new icon is available below.', 'mobileviewlang' ),
				'upload_unzip_header' => __( 'Unzipping icon set...', 'mobileviewlang' ),
				'upload_unzip_status' => __( 'Icon set uploaded, currently unpackaging...', 'mobileviewlang' ),
				'upload_invalid_header' => __( 'Invalid file format.', 'mobileviewlang' ),
				'upload_invalid_status' => __( 'Please upload only .PNG (single image) or .ZIP (icon set) file types.', 'mobileviewlang' ),
				'upload_describe_set' => __( 'Please enter the set information below and click save', 'mobileviewlang' ),
				'are_you_sure_set' => __( 'Delete this set?', 'mobileviewlang' ) . ' ' . __( 'This operation cannot be undone.', 'mobileviewlang' ),
				'are_you_sure_delete' => __( 'Delete this theme and all its files?', 'mobileviewlang' ) . ' ' . __( 'This operation cannot be undone.', 'mobileviewlang' ),
				'reset_admin_settings' => __( 'Reset all MobileView admin settings?', 'mobileviewlang' ) . ' ' . __( 'This operation cannot be undone.', 'mobileviewlang' ),
				'reset_icon_menu_settings' => __( 'Reset Menu Page and Icon settings?', 'mobileviewlang' ) . ' ' . __( 'This operation cannot be undone.', 'mobileviewlang' ),
				'copying_text' => __( 'Your Backup Key was copied to the clipboard.', 'mobileviewlang' ),
			);
      $localize_params[ 'plugin_url' ] = get_bloginfo('wpurl') . '/wp-admin/admin.php?page='.MOBILEVIEW_ROOT_DIR.'/admin/admin-init.php';
			wp_enqueue_script( 'jquery-plugins', MOBILEVIEW_URL . '/admin/js/mobileview-plugins-min.js', 'jquery', md5( MOBILEVIEW_VERSION ) );	

			wp_enqueue_script( 'mobileview-custom', MOBILEVIEW_URL . '/admin/js/mobileview-admin.js', array( 'jquery-plugins', 'jquery-ui-draggable', 'jquery-ui-droppable', 'wp-color-picker' ), md5( MOBILEVIEW_VERSION ) );			

			// Set up AJAX requests here
			wp_localize_script( 'jquery-plugins', 'MobileViewCustom', $localize_params );
			if ( function_exists( 'wp_enqueue_media' ) ){
				wp_enqueue_media();
			} else {
				wp_enqueue_style( 'thickbox' );
				wp_enqueue_script( 'media-upload' );
				wp_enqueue_script( 'thickbox' );
			}
			
			//COLOR Picker 
			wp_enqueue_style('wp-color-picker');
			
		}	
			wp_enqueue_script( 'jquery-ui-draggable');
			wp_enqueue_script( 'jquery-ui-droppable');
			
		$this->setup_mobileview_admin_ajax();
	}
	/*!		\brief Adds the appropriate actions for handling MobileView administration Ajax calls
	 *
	 *		This method sets up the appropriate actions for handling the MobileView administrational panel Ajax calls that use the admin-ajax script
	 *		that is built into WordPress.
	 *
	 *		\ingroup mobileviewglobal
	 *		\ingroup admin
	 */			
	function setup_mobileview_admin_ajax() {
		add_action( 'wp_ajax_mobileview_ajax', array( &$this, 'admin_ajax_handler' ) );	
	}
	/*!		\brief Makes an empty file on disk, similar to Linux's touch command
	 *
	 *		This method creates an empty file. 
	 *
	 *		\ingroup mobileviewglobal
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
	 *		This method handles all Ajax requests in the administrational panel for MobileView.  It  checks to make sure the user has the appropriate permissions,
	 *		and also verifies that the security NONCE is valid.  
	 *
	 *		\ingroup mobileviewglobal
	 *		\ingroup admin
	 */			
	function admin_ajax_handler() {
		if ( current_user_can( 'manage_options' ) ) {
			// Check security nonce
			$mobileview_nonce = $this->post['mobileview_nonce'];
			if ( !wp_verify_nonce( $mobileview_nonce, 'mobileview_admin' ) ) {
				MOBILEVIEW_DEBUG( MOBILEVIEW_SECURITY, 'Invalid security nonce for AJAX call' );			
				exit;	
			}
			header( 'HTTP/1.1 200 OK' );		
			$mobileview_ajax_action = $this->post['mobileview_action'];
			switch( $mobileview_ajax_action ) {
				case 'activate-theme':	
					$settings = mobileview_get_settings();
					$theme_location = $this->post[ 'location' ];
					$theme_name = $this->post[ 'name' ];
					if ( $settings->current_theme_location != $theme_location ) {
						$paths = explode( '/', ltrim( rtrim( $theme_location, '/' ), '/' ) );
						$settings->current_theme_name = $paths[ count( $paths ) - 1 ];	
						unset( $paths[ count( $paths ) - 1 ] );
						$settings->current_theme_location = '/' . implode( '/', $paths );
						$settings->current_theme_friendly_name = $theme_name;
						remove_all_filters( 'mobileview_theme_menu' );
						remove_all_filters( 'mobileview_default_settings' );
						$this->save_settings( $settings );
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
					echo mobileview_get_plugin_warning_count();
					break;
				case 'update-theme':
					$cookie = json_decode($this->post[ 'cookie' ],true);
					if($cookie):
						$cookies = array();
						foreach ( $cookie as $name ) {
							$cookies[] = new WP_Http_Cookie( $name );
						}

						global $wp_filesystem;
						$filesystem = WP_Filesystem();
						$skin_name = trim($this->post[ 'name' ]);
						$file_url = 'http://colorlabsproject.com/member/downloads/mobileview-skins/'.$skin_name.'/'.$skin_name.'.zip';
						$tmpfname = wp_tempnam($file_url);
						$get_zip_file = wp_remote_get(
							$file_url,
							array(
								'timeout' => 30,
								'cookies' => $cookies,
								'stream' => true, 
								'filename' => $tmpfname
								)
						);
						
						$target_dir = $wp_filesystem->find_folder(MOBILEVIEW_CUSTOM_THEME_DIRECTORY);
						
						$do_unzip = unzip_file($get_zip_file['filename'], $target_dir);
						unlink($tmpfname);
						
						$message = '';
						if ( is_wp_error($do_unzip) ) {
							$error = $do_unzip->get_error_code();
							if('incompatible_archive' == $error) {
									$message = '<h2 class="updater-error">'. __("Failed: Incompatible archive","mobileviewlang").'</h2>';
							}
							if('empty_archive' == $error) {
									$message = '<h2 class="updater-error">'. __("Failed: Empty Archive","mobileviewlang").'</h2>';
							}
							if('mkdir_failed' == $error) {
									$message = '<h2 class="updater-error">'. __("Failed: mkdir Failure","mobileviewlang").'</h2>';
							}
							if('copy_failed' == $error) {
									$message = '<h2 class="updater-error">'. __("Failed: Copy Failed","mobileviewlang").'</h2>';
							}
						} else {
							$message = '<h2 class="updater-success"><strong>Well done!</strong> You successfully update ' . $skin_name . ' skin</h2>';
						}
						echo $message;
					endif;
					
					break;
				case 'mobileview-login':				
					$username = $this->post[ 'username' ];
					$password = $this->post[ 'password' ];
					$response = wp_remote_post(
						"http://colorlabsproject.com/member/login.php",
						array(
							'timeout' => 30,
							'redirection' => 0,
							'headers' => array(),
							'body' => array(
								'amember_login' => $this->post[ 'username' ],
								'amember_pass'  => $this->post[ 'password' ]
							)
							)
					);
					if( $response['cookies'][2] ) {
						echo json_encode($response['cookies']);
					}
					break;		
				default:
 
					break;
			}	
		} else {
			MOBILEVIEW_DEBUG( MOBILEVIEW_SECURITY, 'Insufficient security privileges for AJAX call' );	
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
	 *		\ingroup mobileviewglobal
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
	 *		\ingroup mobileviewglobal
	 */			
	function create_directory_if_not_exist( $dir ) {
		if ( !file_exists( $dir ) ) {
			MOBILEVIEW_DEBUG( MOBILEVIEW_INFO, 'Creating directory ' . $dir );
			// Try and make the directory
			if ( !wp_mkdir_p( $dir ) ) {
				$this->directory_creation_failure = true;
				MOBILEVIEW_DEBUG( MOBILEVIEW_ERROR, 'Unable to create directory ' . $dir );
			}	
		}	
	}
	/*!		\brief Checks to make sure all the required MobileView directories exist
	 *
	 *		This method checks to make sure all the required MobileView directories exist, and if not, attempts to create them.	 
	 *
	 *		\ingroup mobileviewglobal
	 */		
	function check_directories() {		
		$this->create_directory_if_not_exist( MOBILEVIEW_BASE_CONTENT_DIR );		
		$this->create_directory_if_not_exist( MOBILEVIEW_TEMP_DIRECTORY );
		$this->create_directory_if_not_exist( MOBILEVIEW_BASE_CONTENT_DIR . '/cache' );
		$this->create_directory_if_not_exist( MOBILEVIEW_BASE_CONTENT_DIR . '/themes' );	
		$this->create_directory_if_not_exist( MOBILEVIEW_BASE_CONTENT_DIR . '/modules' );
		$this->create_directory_if_not_exist( MOBILEVIEW_CUSTOM_LANG_DIRECTORY );
		$this->create_directory_if_not_exist( MOBILEVIEW_CUSTOM_SETTINGS_DIRECTORY );
		$this->create_directory_if_not_exist( MOBILEVIEW_DEBUG_DIRECTORY );
		if ( $this->directory_creation_failure ) {
			$this->add_warning( 
				__( "Directory Problem", "mobileviewlang" ), 
				__( "One or more required directories could not be created", "mobileviewlang" ),
				' '
			);
		}
	}
	/*!		\brief Instructs WordPress on the length to use for excerpts
	 *
	 *		This is the main hook that instructs WordPress on the length to use for excerpts.  The default excerpt length is 24 words, and can be 
	 *		adjusted using the WordPress filter \em mobileview_excerpt_mode.	 
	 *
	 *		\returns The length of the excerpt in words
	 *
	 *		\ingroup mobileviewglobal
	 */		
	function get_excerpt_length( $length ) {
		$settings = $this->get_settings();
		return apply_filters( 'mobileview_excerpt_length', 24 );	
	}
	/*!		\brief Instructs WordPress on the text to use for "more" in excerpts
	 *
	 *		This is the main hook that instructs WordPress what text to use for "more" in the excerpts.  The default text is " ...", and can be
	 *		adjusted using the WordPress filter \em mobileview_excerpt_more.
	 *
	 *		\returns A string representing the text to use for "more" in the excerpts
	 *
	 *		\ingroup mobileviewglobal
	 */		
	function get_excerpt_more( $more ) {
		$settings = $this->get_settings();
		return apply_filters( 'mobileview_excerpt_more', ' ...' );		
	}
	/*!		\brief Loads a file from disk
	 *
	 *		This method loads a file from diskk
	 *
	 *		\returns The contents of the file loaded from disk, otherwise an empty string
	 *
	 *		\ingroup mobileviewglobal
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
	 *		\ingroup mobileviewglobal
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
	 *		\ingroup mobileviewglobal
	 */			
	function get_current_theme_uri() {
		return colabsplugin_mobileview_sslize( WP_CONTENT_URL . $this->get_current_theme_location() );	
	}
	/*!		\brief Used to determine the current theme name
	 *
	 *		This method returns the current theme name, for example \em Classic.
	 *
	 *		\returns A string representing the currently activated theme name
	 *
	 *		\ingroup mobileviewglobal
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
	 *		\ingroup mobileviewglobal
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
			echo "<script type='text/javascript'>var mobileview_ignored_urls = [" . implode( ',', $new_urls ) . "];</script>\n";	
		}			
}
	/*!		\brief Adds the heading information to the HEAD area of the active mobile theme
	 *
	 *		This method is called internally to add the HEAD information for the currently active mobile theme.  The main style.css is added,
	 *		the currently active skin, the CSS files that were added using enqueue_css, and the iPhone bookmark icon.
	 *
	 *		The CSS files can be filtered using the WordPress filter \em mobileview_theme_css_files, and the bookmark icon HTML code can be
	 *		filtered using the WordPress filter \em mobileview_bookmark_meta.
	 *
	 *		\ingroup mobileviewglobal
	 */		
	function add_mobile_header_info() {
		$settings = $this->get_settings();
		if ( $this->get_active_device_class() == "ipad" ) {
			if ( $settings->glossy_bookmark_icon ) {
				$bookmark_icon = "<link rel='apple-touch-icon' href='" . mobileview_get_site_menu_icon( MOBILEVIEW_ICON_TABLET_BOOKMARK ) . "' />\n";
			} else {
				$bookmark_icon = "<link rel='apple-touch-icon-precomposed' href='" . mobileview_get_site_menu_icon( MOBILEVIEW_ICON_TABLET_BOOKMARK ) . "' />\n";		
			}	
		} else {
			if ( $settings->glossy_bookmark_icon ) {
				$bookmark_icon = "<link rel='apple-touch-icon' href='" . mobileview_get_site_menu_icon( MOBILEVIEW_ICON_BOOKMARK ) . "' />\n";
			} else {
				$bookmark_icon = "<link rel='apple-touch-icon-precomposed' href='" . mobileview_get_site_menu_icon( MOBILEVIEW_ICON_BOOKMARK ) . "' />\n";
			}
		}	
		echo apply_filters( 'mobileview_bookmark_meta', $bookmark_icon );
		// Add the default stylesheet to the end, use min if available
		$minfile = TEMPLATEPATH . '/style.min.css';
		if ( file_exists( $minfile ) ) {		
			$this->css_files[] = mobileview_get_bloginfo( 'template_directory' ) . '/style.min.css?ver=' . mobileview_refreshed_files();
		} else {
			$this->css_files[] = mobileview_get_bloginfo( 'template_directory' ) . '/style.css?ver=' . mobileview_refreshed_files();
		}
		// Check for an active skin
		if ( $settings->current_theme_skin != 'none' ) {
			$current_theme = $this->get_current_theme_info();
			if ( isset( $current_theme->skins[ $settings->current_theme_skin ] ) ) {
				$this->css_files[] = $current_theme->skins[ $settings->current_theme_skin ]->skin_url;	
			}
		}
		$this->css_files = apply_filters( 'mobileview_theme_css_files', $this->css_files );
		foreach( $this->css_files as $css ) {
			echo "<link rel='stylesheet' type='text/css' media='screen' href='$css' />\n";	
		}
	}
	/*!		\brief Determines the name of the current MobileView theme skin
	 *
	 *		This method can be used to determine the name of the currently active MobileView theme skin.
	 *
	 *		\returns A string representing the name of the skin, or false if no skin is active
	 *
	 *		\ingroup mobileviewglobal
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
	 *		or mobile.  The \em mobileview_switch COOKIE is checked to determine if the user has previously selected the type of theme they would want, 
	 *		and the user agent of the device is also checked.  This method calls is_supported_device() to
	 *		determine whether or not the user's browser is supported by the active MobileView theme.
	 *
	 *		\ingroup mobileviewglobal
	 */		
	function check_user_agent() {	
		// check and set cookie
		if ( isset( $this->get['mobileview_switch'] ) ) {
			setcookie( MOBILEVIEW_COOKIE, $this->get['mobileview_switch'] );
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
			if ( !isset( $_COOKIE[ MOBILEVIEW_COOKIE ] ) ) {
				$settings = $this->get_settings();
				if ( $settings->desktop_is_first_view ) {
					// Show desktop theme initially
					$this->showing_mobile_theme = false;	
				} else {
					$this->showing_mobile_theme = true;	
				}
			} else {
				// If Cookie is set, check value
				if ( $_COOKIE[MOBILEVIEW_COOKIE] === 'mobile' ) {
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
		$this->showing_mobile_theme = apply_filters( 'mobileview_should_show_mobile_theme', $this->showing_mobile_theme );
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
	 *		This method is called internally to add the HTML code for the desktop to mobile switching.  It currently reads HTML from a file in the includes/html 
	 *		directory called desktop-switch.php.  The HTML code can be modified using the WordPress filter \em mobileview_desktop_switch_html.
	 *
	 *		\ingroup mobileviewglobal
	 */		
	function show_desktop_switch_link() {
		if ( file_exists( get_mobileview_directory() . '/includes/desktop-switch.php' ) ) {
			ob_start();
			include( get_mobileview_directory() . '/includes/desktop-switch.php' );
			$switch_html = ob_get_contents();
			ob_end_clean();
			echo apply_filters( 'mobileview_desktop_switch_html', $switch_html );
		}
	}
	/*!		\brief Adds the CSS code for the switch link in the desktop theme	 
	 *
	 *		This method is called internally to add the CSS code for the switch link in the desktop theme
	 *
	 *		\ingroup mobileviewglobal
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
	 *		\ingroup mobileviewglobal
	 *		\ingroup admin
	 */		
	function verify_post_nonce() {
		$nonce = $this->post['mobileview-admin-nonce'];
		if ( !wp_verify_nonce( $nonce, 'mobileview-post-nonce' ) ) {
			MOBILEVIEW_DEBUG( MOBILEVIEW_SECURITY, "Unable to verify MobileView post nonce" );
			die( 'Unable to verify MobileView post nonce' );	
		}		
		return true;
	}
	/*!		\brief Processes the submission of the settings form in the administration panel
	 *
	 *		This method is used internally to process the submitted settings.  It verifies that the security NONCE is valid and also that the proper
	 *		submit button was pressed.
	 *
	 *		\ingroup mobileviewglobal
	 *		\ingroup admin
	 */			
	function process_submitted_settings() {
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
			return;	
		}
		
		if ( isset( $this->post['mobileview-set-info-submit'] ) ) {
			$this->verify_post_nonce();
			// this is how we change the set information for a new set
			$settings = $this->get_settings();
			if ( isset( $settings->temp_icon_set_for_readme ) && strlen( $settings->temp_icon_set_for_readme ) ) {
				$f = fopen( $settings->temp_icon_set_for_readme, 'w+t' );
				if ( $f ) { 
					$set_name = $this->post['mobileview-set-name'];
					$set_info = "Name: {$set_name}\nDescription: {$set_name}\n";
					fwrite( $f, $set_info );
					fclose( $f );		
					$settings->temp_icon_set_for_readme = '';
					$this->save_settings( $settings );
				}
			}
		} else if ( isset( $this->post['mobileview-submit'] ) ) {
			$this->verify_post_nonce();
			$settings = $this->get_settings();
			
			foreach( (array)$settings as $name => $value ) {
				if ( isset( $this->post[ $name ] ) ) {
					// Remove slashes if they exist
					if ( is_string( $this->post[ $name ] ) ) {						
						$this->post[ $name ] = htmlspecialchars_decode( $this->post[ $name ] );
					}	
					$settings->$name = apply_filters( 'mobileview_setting_filter_' . $name, $this->post[ $name ] );	
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
			do_action( 'mobileview_settings_saved' );
		} else if ( isset( $this->post['mobileview-submit-reset'] ) ) {
			$this->verify_post_nonce();
			MOBILEVIEW_DEBUG( MOBILEVIEW_INFO, "Settings are being reset" );
			// remove the setting from the DB
			delete_option( MOBILEVIEW_SETTING_NAME );
			$this->reload_settings();
		} else {
			// This code path is probably dead now
			MOBILEVIEW_DEBUG( MOBILEVIEW_WARNING, "Reset failed." );
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
	 *		\ingroup mobileviewglobal
	 *		\ingroup helpers
	 */				
	function get_theme_copy_num( $base ) {
		$num = 1;
		while( true ) {
			if ( !file_exists( MOBILEVIEW_CUSTOM_THEME_DIRECTORY . '/' . $base . '-copy-' . $num ) ) {
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
	 *		\ingroup mobileviewglobal
	 *		\ingroup settings
	 */			
	function save_settings( $settings ) {
		$settings = apply_filters( 'mobileview_update_settings', $settings );
		$serialized_data = serialize( $settings );
		MOBILEVIEW_DEBUG( MOBILEVIEW_VERBOSE, 'Saving settings to database' );	

		update_option( MOBILEVIEW_SETTING_NAME, $serialized_data );
		$this->settings = $settings;
	}
	/*!		\brief Enqueues a CSS file for use in a MobileView mobile theme.
	 *
	 *		This method will enqueue CSS files.  Currently this method just results in the CSS files being injected into the header of a mobile theme,
	 *		but will hopefully cause CSS files to be merged and optimized in future versions of MobileView.
	 *
	 *		\param css The URL for the CSS file
	 *
	 *		\ingroup mobileviewglobal
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
	 *		\ingroup mobileviewglobal
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
	 *		\ingroup mobileviewglobal
	 *		\ingroup iconssets
	 */		
	function get_url_for_this_icon( $short_icon_name ) {
		if ( file_exists( get_mobileview_directory() . '/resources/icons/sets/' . $short_icon_name ) ) {
			return get_mobileview_url() . '/resources/icons/sets/' . $short_icon_name;
		}
	}
	/*!		\brief Performs a recursive copy from one directory to another
	 *
	 *		This method can be used to recursively copy an entire directory. 
	 *
	 *		\param source_dir The source directory for the copy.
	 *		\param dest_dir The destination directory for the copy. This directory will be created if it does not exist.  
	 *
	 *		\ingroup mobileviewglobal
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
						MOBILEVIEW_DEBUG( MOBILEVIEW_WARNING, "Unable to create directory " . $dest_dir . '/' . $f );	
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
							MOBILEVIEW_DEBUG( MOBILEVIEW_ERROR, 'Unable to open ' . $dest_file . ' for writing' );	
						}
						fclose( $src );
					} else {
						MOBILEVIEW_DEBUG( MOBILEVIEW_ERROR, 'Unable to open ' . $cur_file . ' for reading' );
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
	 *		\ingroup mobileviewglobal
	 *		\ingroup files
	 */		
	function recursive_delete( $source_dir ) {
		// Only allow a delete to occur for directories in the main MobileView data directory
		if ( strpos( $source_dir, '..' ) !== false || strpos( $source_dir, MOBILEVIEW_BASE_CONTENT_DIR ) === false ) {
			MOBILEVIEW_DEBUG( MOBILEVIEW_SECURITY, 'Not deleting directory ' . $source_dir . ' due to possibly security risk' );
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
}
/*!		\brief Echos the current directory for MobileView
 *
 *		This method can be used to echo the current MobileView directory. Internally this method calls get_mobileview_directory().
 *
 *		\ingroup mobileviewglobal
 */
function mobileview_directory() {
	echo get_mobileview_directory();
}
/*!		\brief Retrieves the current directory for MobileView
 *
 *		This method can be used to retrieve the current MobileView directory
 * 
 *		\returns A string containing the directory on disk for the main MobileView directory
 *
 *		\ingroup mobileviewglobal
 */
function get_mobileview_directory() {
	return MOBILEVIEW_DIR;
}
/*!		\brief Echos the URL for the main MobileView directory
 *
 *		This method can be used to echo the current URL for the MobileView directory. Internally this method calls get_mobileview_url().
 *
 *		\ingroup mobileviewglobal
 */
function mobileview_url() {
	echo get_mobileview_url();
}
/*!		\brief Returns the URL for the main MobileView  directory
 *
 *		This method can be used to determine the URL for the MobileView  directory. 
 * 
 *		\returns A string representing the URL
 *
 *		\ingroup mobileviewglobal
 */
function get_mobileview_url() {
	return MOBILEVIEW_URL;	
}
/*!		\brief Can be used to enqueue a CSS script from MobileView.
 *
 *		This method can be used to enqueue a CSS script from MobileView.  CSS scripts will hopefully be merged into one in future
 *		releases of MobileView.
 * 
 *		\param css_url The full URL of the CSS file the should be added
 *
 *		\ingroup mobileviewglobal
 */
function mobileview_enqueue_css( $css_url ) {
	global $mobileview;
	$mobileview->enqueue_css( $css_url );	
}
/*!		\brief A substitute for WordPress' bloginfo function.
 *
 *		The method echos a configuration parameter for MobileView.  If the parameter isn't MobileView specific, the WordPress configuration 
 *		parameter will be returned.  Internally this function calls mobileview_get_bloginfo().
 * 
 *		\param setting_name The associated setting name to retrieve.  Please see mobileview_get_bloginfo() for a complete list.
 *
 *		\returns The associated setting parameter
 *
 *		\ingroup mobileviewglobal
 */
function mobileview_bloginfo( $setting_name ) {
	echo mobileview_get_bloginfo( $setting_name );
}
/*!		\brief A substitute for WordPress' get_bloginfo function.
 *
 *		The method returns a configuration parameter for MobileView.  If the parameter isn't MobileView specific, the WordPress configuration 
 *		parameter will be returned.
 * 
 *		\param setting_name The associated setting name to retrieve.  The currently supported parameters are:
 *		\arg \c template_directory The currently active MobileView theme directory
 *		\arg \c template_url Same as template_directory
 *		\arg \c max_upload_size The maximum upload size supported on the server
 *		\arg \c mobileview_directory The current server directory for MobileView
 *		\arg \c mobileview_url The URL associated with the current MobileView directory on the server
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
 *		\ingroup mobileviewglobal
 */
function mobileview_get_bloginfo( $setting_name ) {
	global $mobileview;
	$settings = $mobileview->get_settings();
	$setting = false;
	switch( $setting_name ) {
		case 'template_directory':
		case 'template_url':
			$setting = $mobileview->get_template_directory_uri( false );
			break;
		case 'child_theme_directory_uri':
			$setting = $mobileview->get_stylesheet_directory_uri( false );
			break;
		case 'theme_root_directory':
			$setting = $mobileview->get_current_theme_directory();
			break;
		case 'max_upload_size':
			$setting = $mobileview->get_max_upload_size();	
			break;
		case 'site_title':
			$setting = $settings->site_title;
			break;
		case 'mobileview_directory':
			$setting = get_mobileview_directory();
			break;
		case 'mobileview_url':
			$setting = get_mobileview_url();
			break;
		case 'version':
			$setting = MOBILEVIEW_VERSION;
			break;
		case 'theme_count':
			$themes = $mobileview->get_available_themes();
			$setting = count( $themes );
			break;
		case 'icon_set_count':
			$icon_sets = $mobileview->get_available_icon_packs();
			// Remove the custom icon count
			$setting = count( $icon_sets ) - 1;
			break;
		case 'icon_count':
			$icon_sets = $mobileview->get_available_icon_packs();
			$total_icons = 0;	
			foreach( $icon_sets as $setname => $set ) {
				if ( $setname == "Custom Icons" ) continue;
				$icons = $mobileview->get_icons_from_packs( $setname );
				$total_icons += count( $icons );
			}
			$setting = $total_icons;
			break;
		case 'active_theme_friendly_name':
			$theme_info = $mobileview->get_current_theme_info();
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
			$setting = mobileview_get_plugin_warning_count();
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
 *		\ingroup mobileviewglobal
 *		\ingroup settings
 */
function mobileview_get_settings() {
	global $mobileview;
	return $mobileview->get_settings();	
}
/*!		\brief Saves the MobileView settings object to the database
 *
 *		This method can be used to save the MobileView settings object to the database.  Internally this method calls MobileView::save_settings().
 *
 *		\param settings The settings object to save
 *
 *		\par Typical Usage:
 *		\include save-settings.php
 *
 *		\ingroup mobileviewglobal
 *		\ingroup settings
 */
function mobileview_save_settings( $settings ) {
	global $mobileview;
	$mobileview->save_settings( $settings );	
}
/*!		\brief Retrieves an AJAX parameter for a client-side AJAX call
 *
 *		This method can be used to retrive a client-side AJAX parameter for AJAX routines that are initiaited from the JS function MobileViewAjax.
 *
 *		\param name The AJAX parameter name to retreive
 *
 *		\returns The AJAX parameter, or false is it has not been set
 *
 *		\ingroup mobileviewglobal
 */
function mobileview_get_ajax_param( $name ) {
	global $mobileview;
	if ( isset( $mobileview->post[ $name ] ) ) {
		return $mobileview->post[ $name ];	
	}
	return false;	
}
/*!	\brief Echos the AJAX parameter for a client-side AJAX call
 *
 *		This method can be used to echo a client-side AJAX parameter for AJAX routines that are initiaited from the JS function MobileViewAjax.
 *
 *		\param name The AJAX parameter name to retreive
 *
 *		\ingroup mobileviewglobal
 */
function mobileview_the_ajax_param( $name ) {
	global $mobileview;
	if ( isset( $mobileview->post[ $name ] ) ) {
		return $mobileview->post[ $name ];	
	}
	return false;	
}
/*!	\brief Determines whether or not WordPress 3.x is in multisite mode
 *
 *		This method can be used to determine whether or not WordPress 3.x is configured in multisite mode.
 *
 *		\version 2.0.5
 *		\ingroup mobileviewglobal
 */
function mobileview_is_multisite_enabled() {
	$settings = mobileview_get_settings();
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
 *		\ingroup mobileviewglobal
 */
function mobileview_is_multisite_primary() {
	global $blog_id;
	return ( $blog_id == 1 );
}	
/*!	\brief Determines whether or not the restoration key was valid
 *
 *		This method can be used to determine whether or not the restoration key in the backup/restore section was valid.
 *
 *		\version 2.0.7
 *		\ingroup mobileviewglobal
 */
function mobileview_restore_failed() {
	global $mobileview;
	return ( $mobileview->restore_failure );
}
/*!	\brief Determines whether or not the current site is a multisite sub-blog
 *
 *		This method can be used to determine whether the current site is a multi-site sub-blog.
 *
 *		\version 2.0.9
 *		\ingroup mobileviewglobal
 */
function mobileview_is_multisite_secondary() {
	if ( mobileview_is_multisite_enabled() ) {
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
 *		\ingroup mobileviewglobal
 */
 	function mobileview_refreshed_files() {
		global $mobileview;
		$settings = $mobileview->get_settings();
		$version_string = md5( MOBILEVIEW_VERSION );
		$current_time = time();
		if ( $settings->always_refresh_css_js_files ) {
			return ( $version_string . $current_time );
		} else {
			return ( $version_string );	
		}
	}
/*-----------------------------------------------------------------------------------*/
/* Social share function
/*-----------------------------------------------------------------------------------*/	
function mobileview_shortcode_twitter($atts, $content = null) {
   	global $post;
   	extract(shortcode_atts(array(	'url' => '',
   									'style' => 'vertical',
   									'source' => '',
   									'text' => '',
   									'related' => '',
   									'lang' => '',
   									'float' => 'left', 
   									'use_post_url' => 'false',
   									'recommend' => '', 
   									'hashtag' => '', 
   									'size' => '', 
   									 ), $atts));
	$output = '';
	if ( $url )
		$output .= ' data-url="'.$url.'"';
	if ( $source )
		$output .= ' data-via="'.$source.'"';
	if ( $text )
		$output .= ' data-text="'.$text.'"';
	if ( $related )
		$output .= ' data-related="'.$related.'"';
	if ( $hashtag )
		$output .= ' data-hashtags="'.$hashtag.'"';
	if ( $size )
		$output .= ' data-size="'.$size.'"';
	if ( $lang )
		$output .= ' data-lang="'.$lang.'"';
	if ( $style != '' ) {
		$output .= 'data-count="'.$style.'"';
	}
	if ( 'true' == $use_post_url && '' == $url ) {
		$output .= ' data-url="' . get_permalink( $post->ID ) . '"';
	}
	$output = '<div class="mobileview-sc-twitter '.$float.'"><a href="http://twitter.com/share" class="twitter-share-button"'.$output.' data-count="'.$style.'">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>';
	return $output;
}
function mobileview_shortcode_google_plusone ( $atts, $content = null ) {
	global $post;
	$defaults = array(
						'size' => '',
						'language' => '',
						'count' => '',
						'href' => '',
						'callback' => '',
						'float' => 'none', 
						'annotation' => 'none'
					);
	$atts = shortcode_atts( $defaults, $atts );
	extract( $atts );
	$params = array();
	$allowed_floats = array( 'left' => ' fl', 'right' => ' fr', 'none' => '' );
	if ( ! in_array( $float, array_keys( $allowed_floats ) ) ) { $float = 'none'; }
	if ( ! in_array( $annotation, array( 'bubble', 'inline', 'none' ) ) ) { $annotation = 'none'; } 
	// A friendly-looking array of supported languages, along with their codes.
	$supported_languages = array(
		'ar' => 'Arabic', 
		'bg' => 'Bulgarian', 
		'ca' => 'Catalan', 
		'zh-CN' => 'Chinese (Simplified)', 
		'zh-TW' => 'Chinese (Traditional)', 
		'hr' => 'Croatian', 
		'cs' => 'Czech', 
		'da' => 'Danish', 
		'nl' => 'Dutch', 
		'en-US' => 'English (US)', 
		'en-GB' => 'English (UK)', 
		'et' => 'Estonian', 
		'fil' => 'Filipino', 
		'fi' => 'Finnish', 
		'fr' => 'French', 
		'de' => 'German', 
		'el' => 'Greek', 
		'iw' => 'Hebrew', 
		'hi' => 'Hindi', 
		'hu' => 'Hungarian', 
		'id' => 'Indonesian', 
		'it' => 'Italian', 
		'ja' => 'Japanese', 
		'ko' => 'Korean', 
		'lv' => 'Latvian', 
		'lt' => 'Lithuanian', 
		'ms' => 'Malay', 
		'no' => 'Norwegian', 
		'fa' => 'Persian', 
		'pl' => 'Polish', 
		'pt-BR' => 'Portuguese (Brazil)', 
		'pt-PT' => 'Portuguese (Portugal)', 
		'ro' => 'Romanian', 
		'ru' => 'Russian', 
		'sr' => 'Serbian', 
		'sv' => 'Swedish', 
		'sk' => 'Slovak', 
		'sl' => 'Slovenian', 
		'es' => 'Spanish', 
		'es-419' => 'Spanish (Latin America)', 
		'th' => 'Thai', 
		'tr' => 'Turkish', 
		'uk' => 'Ukrainian', 
		'vi' => 'Vietnamese'
	);
	$output = '';
	$tag_atts = '';
	// Make sure we only have Google +1 attributes in our array, after parsing the "float" parameter.
	unset( $atts['float'] );
	if ( '' == $atts['href'] & isset( $post->ID ) ) {
		$atts['href'] = get_permalink( $post->ID );
	}
	foreach ( $atts as $k => $v ) {
		if ( ${$k} != '' ) {
			$tag_atts .= ' data-' . $k . '="' . ${$k} . '"';
		}
	}
	$output = '<div class="shortcode-google-plusone' . $allowed_floats[$float] . '"><div class="g-plusone" ' . $tag_atts . '></div></div><!--/.shortcode-google-plusone-->' . "\n";
	// Parameters to pass to Google PlusOne JavaScript.
	if ( in_array( $atts['language'] , array_values( $supported_languages ) ) ) {
		$language = '';
		foreach ( $supported_languages as $k => $v ) {
			if ( $v == $atts['language'] ) {
				$language = $k;
				break;
			}
		}
		$params = array( 'language' => $language );
	}
	mobileview_shortcode_google_plusone_js( $params );
	return $output . "\n";
}
function mobileview_shortcode_google_plusone_js ( $params ) {
	echo '<script src="https://apis.google.com/js/plusone.js" type="text/javascript">' . "\n";
	if ( isset( $params['language'] ) && ( $params['language'] != '' ) ) {
		echo ' {lang: \'' . $params['language'] . '\'}' . "\n";
	}
	echo '</script>' . "\n";
	echo '<script type="text/javascript">gapi.plusone.go();</script>' . "\n";
}
function mobileview_shortcode_fblike($atts, $content = null) {
   	extract(shortcode_atts(array(	'float' => 'none',
   									'url' => '',
   									'style' => 'standard',
   									'showfaces' => 'false',
   									'width' => '450',
   									'verb' => 'like',
   									'colorscheme' => 'light',
   									'font' => 'arial', 
   									'locale' => 'en_US' ), $atts));
	global $post;
	if ( ! $post ) {
		$post = new stdClass();
		$post->ID = 0;
	} // End IF Statement
	$allowed_styles = array( 'standard', 'button_count', 'box_count' );
	if ( ! in_array( $style, $allowed_styles ) ) { $style = 'standard'; } // End IF Statement
	if ( !$url )
		$url = get_permalink($post->ID);
	$height = '65';
	if ( 'true' == $showfaces)
		$height = '100';
	if ( ! $width || ! is_numeric( $width ) ) { $width = 450; } // End IF Statement
	// Set the width to "auto" if "showfaces" is off and the default width is still set.
	$widthpx = $width . 'px';
	if ( $width == 450 && 'false' == $showfaces ) { $widthpx = 'auto'; }
	// Set the height to 20 if "showfaces" is disabled and the style is either "standard" or "button_count".
	if ( 'false' == $showfaces && ( $style != 'box_count' ) ) { $height = 25; }
	switch ( $float ) {
		case 'left':
			$float = 'fl';
		break;
		case 'right':
			$float = 'fr';
		break;
		default:
		break;
	} // End SWITCH Statement
	$output = '
<div class="mobileview-fblike '.$float.'">
<iframe src="http://www.facebook.com/plugins/like.php?href=' . $url . '&amp;layout=' . $style . '&amp;show_faces=' . $showfaces . '&amp;width=' . $width . '&amp;action=' . $verb . '&amp;colorscheme=' . $colorscheme . '&amp;font=' . $font . '" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:' . $widthpx . '; height:' . $height . 'px;"></iframe>
</div>
	';
	return $output;
}
function mobileview_shortcode_linkedin_share ( $atts, $content = null ) {
	$defaults = array( 'url' => '', 'style' => 'none', 'float' => 'none' );
	extract( shortcode_atts( $defaults, $atts ) );
	$allowed_floats = array( 'left' => 'fl', 'right' => 'fr', 'none' => '' );
	$allowed_styles = array( 'top' => ' data-counter="top"', 'right' => ' data-counter="right"', 'none' => '' );
	if ( ! in_array( $float, array_keys( $allowed_floats ) ) ) { $float = 'none'; }
	if ( ! in_array( $style, array_keys( $allowed_styles ) ) ) { $style = 'none'; }
	if ( $url ) { $url = ' data-url="' . esc_url( $url ) . '"'; }
	$output = '';
	if ( 'none' == $float ) {} else { $output .= '<div class="shortcode-linkedin_share ' . $allowed_floats[$float] . '">' . "\n"; }
	$output .= '<script type="IN/Share" ' . $url . $allowed_styles[$style] . '></script>' . "\n";
	if ( 'none' == $float ) {} else { $output .= '</div><!--/.shortcode-linkedin_share-->' . "\n"; }
	// Enqueue the LinkedIn button JavaScript from their API.
	add_action( 'wp_footer', 'mobileview_shortcode_linkedin_js' );
	return $output . "\n";
}
function mobileview_shortcode_linkedin_js () {
	echo '<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>' . "\n";
}
function mobileview_shortcode_pinterest ( $atts, $content = null ) {
	global $post;
	$defaults = array(
						'count' => 'horizontal',
						'float' => 'none', 
						'url' => '', 
						'image_url' => '', 
						'description' => '', 
						'use_post' => 'false'
					);
	$atts = shortcode_atts( $defaults, $atts );
	extract( $atts );
	$allowed_floats = array( 'left' => ' fl', 'right' => ' fr', 'none' => '' );
	if ( ! in_array( $float, array_keys( $allowed_floats ) ) ) { $float = 'none'; }
	$allowed_counts = array( 'horizontal', 'vertical', 'none' );
	if ( ! in_array( $count, array_keys( $allowed_counts ) ) ) { $count = 'horizontal'; }
	$output = '';
	// Use the custom URL, if it has been specified.
	if ( $atts['url'] != '' ) {
		$url = esc_url( $atts['url'] );
	} else {
		// Use the URL to the current $post in the loop.
		$url = esc_url( get_permalink( $post ) );
	}
	// Use the custom image URL, if it has been specified.
	if ( $atts['image_url'] != '' ) {
		$image_url = esc_url( $atts['image_url'] );
	}
	// Use the custom description, if it has been specified.
	if ( $atts['description'] != '' ) {
		$description = esc_attr( $atts['description'] );
	} else {
		// Use the excerpt of the current $post in the loop, if no description is set and if instructed to do so.
		if ( 'true' == $atts['use_post'] ) {
			$description = esc_attr( strip_shortcodes( apply_filters( 'get_the_excerpt', $post->post_excerpt ) ) );
		}
	}
	$output = '<div class="shortcode-pinterest' . $allowed_floats[$float] . '"><a href="http://pinterest.com/pin/create/button/?url=' . urlencode( $url ) . '&media=' . urlencode( $image_url ) . '&description=' . urlencode( $description ) . '" class="pin-it-button" count-layout="' . $count . '">' . __( 'Pin It', 'mobileviewlang' ) . '</a></div>';
	// Enqueue the Pinterest button JavaScript from their API.
	add_action( 'wp_footer', 'mobileview_shortcode_pinterest_javascript' );
	return $output . "\n";
}
function mobileview_shortcode_pinterest_javascript () {
    if(is_single()) echo '<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>' . "\n";
}
//twitter
class mobileview_twitter
{
	public $consumer_key = '9oniptvwS1XN16mCar5w';
	public $consumer_secret = 'RqEiNy3RksnYm29T3TCnb1pSbOZUcdIxZrAyS9Fs';
	/**
	* Linkify Twitter Text
	* 
	* @param string s Tweet
	* 
	* @return string a Tweet with the links, mentions and hashtags wrapped in <a> tags 
	*/
	function mobileview_linkify_twitter_text($tweet = ''){
		$url_regex = '/((https?|ftp|gopher|telnet|file|notes|ms-help):((\/\/)|(\\\\))+[\w\d:#@%\/\;$()~_?\+-=\\\.&]*)/';
		$tweet = preg_replace($url_regex, '<a href="$1" target="_blank">'. "$1" .'</a>', $tweet);
		$tweet = preg_replace( array(
		  '/\@([a-zA-Z0-9_]+)/', # Twitter Usernames
		  '/\#([a-zA-Z0-9_]+)/' # Hash Tags
		), array(
		  '<a href="http://twitter.com/$1" target="_blank">@$1</a>',
		  '<a href="http://twitter.com/search?q=%23$1" target="_blank">#$1</a>'
		), $tweet );
		
		return $tweet;
	}

	/**
	* Get User Timeline
	* 
	*/
	function mobileview_get_user_timeline( $username = '', $limit = 5 ) {
		$key = "twitter_user_timeline_{$username}_{$limit}";

		// Check if cache exists
		$timeline = get_transient( $key );
		if ($timeline !== false) {
		  return $timeline;
		} else {
		  $headers = array( 'Authorization' => 'Bearer ' . $this->mobileview_get_access_token() );
		  $response = wp_remote_get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name={$username}&count={$limit}", array( 
			'headers' => $headers, 
			'timeout' => 40,
			'sslverify' => false 
		  ));
		  if ( is_wp_error($response) ) {
			// In case Twitter is down we return error
			dbgx_trace_var($response);
			return array('error' => __('There is problem fetching twitter timeline', 'colabsthemes'));
		  } else {
			// If everything's okay, parse the body and json_decode it
			$json = json_decode(wp_remote_retrieve_body($response));

			// Check for error
			if( !count( $json ) ) {
			  return array('error' => __('There is problem fetching twitter timeline', 'colabsthemes'));
			} elseif( isset( $json->errors ) ) {
			  return array('error' => $json->errors[0]->message);
			} else {
			  set_transient( $key, $json, 60 * 60 );
			  return $json;
			}
		  }
		}
	}

	/**
	* Get Twitter application-only access token
	* @return string Access token
	*/
	function mobileview_get_access_token() {
		$consumer_key = urlencode( $this->consumer_key );
		$consumer_secret = urlencode( $this->consumer_secret );
		$bearer_token = base64_encode( $consumer_key . ':' . $consumer_secret );

		$oauth_url = 'https://api.twitter.com/oauth2/token';

		$headers = array( 'Authorization' => 'Basic ' . $bearer_token );
		$body = array( 'grant_type' => 'client_credentials' );

		$response = wp_remote_post( $oauth_url, array(
		  'headers' => $headers,
		  'body' => $body,
		  'timeout' => 40,
		  'sslverify' => false
		) );

		if( !is_wp_error( $response ) ) {
		  $response_json = json_decode( $response['body'] );
		  return $response_json->access_token;
		} else {
		  return false;
		}
	}

	/**
	* Builder Twitter timeline HTML markup
	*/
	function mobileview_build_twitter_markup( $timelines = array() ) { ?>
		<ul class="tweets">
		<?php foreach( $timelines as $item ) : ?>
		  <?php 
			$screen_name = $item->user->screen_name;
			$profile_link = "http://twitter.com/{$screen_name}";
			$status_url = "http://twitter.com/{$screen_name}/status/{$item->id}";
		  ?>
		  <li>
			<span class="content">
			  <?php echo $this->mobileview_linkify_twitter_text( $item->text ); ?>
			  <a href="<?php echo $status_url; ?>" style="font-size:85%" class="time" target="_blank">
				<?php echo date('M j, Y', strtotime($item->created_at)); ?>
			  </a>
			</span>
		  </li>
		<?php endforeach; ?>
		</ul>
		<?php 
	}
}
?>