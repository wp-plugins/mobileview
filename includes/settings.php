<?php

class MobileViewSettings extends stdClass {	};

// Settings are added as setting_name => default_value
class MobileViewDefaultSettings extends MobileViewSettings {
	function MobileViewDefaultSettings() {
		
		// default settings go here
		$this->site_title = get_bloginfo( 'name' );	
		$this->desktop_is_first_view = false;
		$this->welcome_alert = '';
		$this->fourohfour_message = sprintf( __('%sSorry, the content you were looking for cannot be found. Please use the menu in the header to access the site search, or browse other site content.%s', 'mobileviewlang'), '<p>', '</p>' );
		$this->footer_message = '';

		$this->custom_css_file = '';
		$this->current_theme_friendly_name = __( 'HipNews', 'mobileviewlang' );
		$this->current_theme_location = '/plugins/' . MOBILEVIEW_ROOT_DIR . '/themes';
		$this->current_theme_name = 'hipnews';
		$this->current_theme_skin = 'none';
		$this->custom_menu_name = '';
		
		$this->show_mobileview_in_footer = true;
		
        $this->mobileview_enable_custom_post_types = false;
        
        $this->glossy_bookmark_icon = true;
        //Icon
        $this->menu_show_email = false;
        $this->menu_show_rss = false;
        
		// Thumbnail images
		$this->post_thumbnails_enabled = true;
		$this->post_thumbnails_new_image_size = 92;
		$this->post_thumbnails_width = 100;
        $this->post_thumbnails_height = 100;
        
		// Excerpt information
		$this->excerpt_length = 26;
		
		// Global core related options
		$this->disable_menu = false;
		
		// Icons
		$this->site_icon = 'hipnews/Home.png';

		// Stats
		$this->custom_stats_code = '';
		
		// Tools
		$this->show_footer_load_times = false;
		$this->always_refresh_css_js_files = false;
		$this->developer_mode = 'off';	
		$this->admin_client_mode_hide_browser = false;
		$this->admin_client_mode_hide_tools = false;
		$this->debug_log = false;	
		$this->debug_log_level = MOBILEVIEW_SECURITY;
		
		// Devices
		$this->custom_user_agents = '';
		
		
		// Real menu settings
		$this->menu_icons = array();
		$this->default_menu_icon = '/plugins/' . MOBILEVIEW_ROOT_DIR . '/resources/icons/hipnews/Default.png';
		$this->disabled_menu_items = array();
		$this->temp_disabled_menu_items = array();
		$this->enable_menu_icons = false;
		$this->menu_disable_parent_as_child = false;
		$this->cache_menu_tree = false;
		$this->cache_time = 300;
		
		// Temporary menu settings
		$this->temp_menu_icons = array();
		
		$this->temp_icon_file_to_unzip = '';
		$this->temp_icon_set_for_readme = '';

		// to be hooked up for each theme
		$this->theme_settings = array();
		
		// Custom menu links
		$this->custom_menu_text_1 = '';
		$this->custom_menu_link_1 = '';
		$this->custom_menu_position_1 = 'top';
		$this->custom_menu_force_external_1 = false;
		$this->custom_menu_text_2 = '';
		$this->custom_menu_link_2 = '';
		$this->custom_menu_position_2 = 'top';
		$this->custom_menu_force_external_2 = false;
		$this->custom_menu_text_3 = '';
		$this->custom_menu_link_3 = '';
		$this->custom_menu_position_3 = 'top';	
		$this->custom_menu_force_external_3 = false;	
		
		$this->enable_home_page_redirect = false;
		$this->home_page_redirect_custom = '';
		$this->home_page_redirect_target = 0;
		
		// Switch link Settings
		$this->show_switch_link = true;
		$this->home_page_redirect_address = 'same';

		// Plugin compatibility
		$this->disable_shortcodes = true;
		$this->disable_google_libraries = true;
		$this->include_functions_from_desktop_theme = false;
		$this->functions_php_inclusion_method = 'translate';
		$this->dismissed_warnings = array();
		$this->convert_menu_links_to_internal = true;
		
		$this->plugin_hooks = false;
		
		// Set up dynamic plugin disabling code
		global $mobileview;
		if ( $mobileview->plugin_hooks && count( $mobileview->plugin_hooks ) ) {
			foreach( $mobileview->plugin_hooks as $name => $hook ) {
				$proper_name = "plugin_disable_" . str_replace( '-', '_', $name );				
				$this->$proper_name = false;
			}
		}
		
//		$this->put_mobileview_in_appearance_menu = false;
		$this->respect_wordpress_date_format = false;
		$this->force_locale = 'auto';
		$this->has_migrated_icons = false;
		$this->make_links_clickable = false;
		$this->restore_string = '';
		$this->backup_or_restore = 'backup';
		
		$this->ignore_urls = '';
		
		// Multisite
		$this->multisite_force_enable = false;
		$this->multisite_disable_overview_pane = true;
		$this->multisite_disable_manage_icons_pane = true;
		$this->multisite_disable_compat_pane = true;
		$this->multisite_disable_debug_pane = true;
		$this->multisite_disable_backup_pane = true;
		$this->multisite_disable_theme_browser_tab = true;
		$this->multisite_disable_statistics_pane = true;
		
		$this->multisite_inherit_statistics = false;
		$this->multisite_inherit_advertising = false;
		$this->multisite_inherit_theme = false;
		$this->multisite_inherit_compat = false;
		
		// iPad
		$this->ipad_support = 'none';
		
		$this->desktop_switch_css = 
		'#mobileview-desktop-switch {
		  padding: 15px 15px 30px;
		  font-size: 80%;
		  background-color: #222;
		  color: #ccd8da;
		  text-shadow: #000 0 -1px 1px;
		  font-weight: bold;
		  text-align: center;
		  border-top: 5px solid #000;
		  width: auto;
		  clear: both;
		  display: block;
		  margin: 0 auto -20px;
		}';		
		
		
		$this->remove_shortcodes = '';
		$this->developer_mode_device_class = 'iphone';
		$this->enable_buddypress_mobile_support = false;
	}
};
