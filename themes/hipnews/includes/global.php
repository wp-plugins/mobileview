<?php

add_filter( 'wpmobi_default_settings', 'hipnews_default_settings' );
add_filter( 'wpmobi_theme_menu', 'hipnews_admin_menu' );

/* Default Settings */

// All default settings must be added to the $settings object here
// All settings should be properly namespaced in a copied theme, i.e. theme_name_my_setting instead of just my_setting or hipnews_
function hipnews_default_settings( $settings ) {
	// General Settings
	$settings->hipnews_latest_posts_page = 'none';
	$settings->hipnews_ajax_mode_enabled = true;
	$settings->hipnews_mobile_enable_zoom = false;
	$settings->hipnews_hide_addressbar = true;
	$settings->hipnews_use_compat_css = true;
	$settings->hipnews_excluded_categories = '';
	$settings->hipnews_excluded_tags = '';
	
	// Style and Appearance
	$settings->hipnews_header_img_location = '';
	$settings->hipnews_retina_header_img_location = '';
	$settings->hipnews_header_shading_style = 'none';
	$settings->hipnews_header_font = 'Helvetica-Bold';
	$settings->hipnews_header_title_font_size = '19px';
	$settings->hipnews_header_color_style = '';
	$settings->hipnews_show_header_icon = false;

	$settings->hipnews_general_font = 'Helvetica';
	$settings->hipnews_general_font_size = '13px';
	$settings->hipnews_general_font_color = '333333';

	$settings->hipnews_post_title_font = 'Helvetica-Bold';
	$settings->hipnews_post_title_font_size = '15px';
	$settings->hipnews_post_title_font_color = '333333';

	$settings->hipnews_post_body_font = 'Helvetica';
	$settings->hipnews_post_body_font_size = '14px';
	
	$settings->hipnews_text_justification = 'left-justify';

	$settings->hipnews_link_color = '006bb3';
	$settings->hipnews_context_headers_color = '475d79';
	$settings->hipnews_footer_text_color = '666666';
	$settings->hipnews_text_shade_color = 'none';

	$settings->hipnews_background_image = 'ipad-thatch-light';
	$settings->hipnews_background_repeat	 = 'repeat';
	$settings->hipnews_background_color = 'CCCCCC';
	$settings->hipnews_custom_background_image = '';

	// Post Icon Settings
	$settings->hipnews_icon_type = 'calendar';
	$settings->hipnews_calendar_icon_bg = 'cal-colors';
	$settings->hipnews_custom_cal_icon_color = '';
	$settings->hipnews_custom_field_thumbnail_name = 'thumbnail';
	$settings->hipnews_thumbs_on_single = false;
	$settings->hipnews_thumbs_on_pages = false;

	// Menu Settings
	$settings->hipnews_use_menu_icon = true;
	$settings->make_menu_relative = true;
	$settings->hipnews_show_categories = true;
	$settings->hipnews_show_tags = true;
	$settings->hipnews_show_account = false;
	$settings->hipnews_show_admin_menu_link = true;
	$settings->hipnews_show_profile_menu_link = true;
	$settings->hipnews_show_archives = false;
	$settings->hipnews_show_links = false;
	$settings->hipnews_show_flickr_rss = false;
	$settings->hipnews_show_search = true;

	// Post Settings
	$settings->hipnews_show_post_author = true;
	$settings->hipnews_show_post_categories = false;
	$settings->hipnews_show_post_tags = false;
	$settings->hipnews_show_post_date = true;
	$settings->hipnews_show_excerpts = 'excerpts-hidden';
	
	// Single Post Settings
	$settings->hipnews_show_post_author_single = true;
	$settings->hipnews_show_post_date_single = true;
	$settings->hipnews_show_post_cats_single = false;
	$settings->hipnews_show_post_tags_single = false;
	$settings->hipnews_show_share_save = false;
	$settings->hipnews_hide_responses = false;
	$settings->hipnews_show_attached_image = true;
	$settings->hipnews_show_attached_image_location = 'above';

	// Page Options
	$settings->hipnews_show_attached_image_on_page = false;
	$settings->hipnews_show_comments_on_pages = false;

	// UA Settings
	$settings->hipnews_custom_user_agents = '';

	// Web App Settings
	$settings->hipnews_webapp_enabled = true;
	$settings->hipnews_webapp_use_ajax = true;
	$settings->hipnews_webapp_use_loading_img = false;
	$settings->hipnews_webapp_status_bar_color = 'default';
	$settings->hipnews_enable_persistent = true;
	$settings->hipnews_show_webapp_notice = false;
	$settings->hipnews_webapp_notice_expiry_days = '30';
	$settings->hipnews_webapp_loading_img_location = '';
	$settings->hipnews_webapp_retina_loading_img_location = '';
	$settings->hipnews_add2home_msg = __( 'Install this Web-App on your [device]: tap [icon] then "Add to Home Screen"', 'wpmobi-me' );

    // Slider Settings
    $settings->hipnews_slider_disable = false;
    $settings->hipnews_slider_cat = '';
    $settings->hipnews_slider_count = '3';
    $settings->hipnews_slider_exclude = true;
    
	// Extensions //

	// FlickrRSS
	$settings->hipnews_show_flickr_rss = false;
	$settings->hipnews_ipad_show_flickr_button = false;
	
	return $settings;
}

// The administrational page for the hipnews theme is constructed here:
function hipnews_admin_menu( $menu ) {
    
    //Access the WordPress Categories via an Array
    $colabs_categories = array();  
    $colabs_categories_obj = get_categories('hide_empty=0');
    foreach ($colabs_categories_obj as $colabs_cat) {
        $colabs_categories[$colabs_cat->cat_ID] = $colabs_cat->cat_name;
        //$colabs_categories[] = $colabs_cat->cat_name;
    }
    
    $array_oneten = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10' );
    
	$menu = array(
		__( "General Settings", "wpmobi-me" ) => array ( 'general', 
			array(
				array( 'section-start', 'misc-options', __( 'Miscellaneous Options', "wpmobi-me" ) ),
				array( 'custom-latest', '', '', '', array( 'ipad' ) ),
				//array( 'checkbox', 'hipnews_ajax_mode_enabled', __( 'Enable AJAX "Load More" link for posts and comments', "wpmobi-me" ), __( 'Posts and comments will be appended to existing content with an AJAX "Load More..." link. If unchecked regular post/comment pagination will be used.', "wpmobi-me" ), array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_mobile_enable_zoom', __( 'Allow zooming', "wpmobi-me" ), __( 'Will allow visitors to zoom in/out on content.', "wpmobi-me" ) ),
				array( 'checkbox', 'hipnews_hide_addressbar', __( 'Hide address bar on page load', "wpmobi-me" ), __( 'Will hide the address bar in compatible browsers on a page load, showing the WPMobi header as the top of the page.', "wpmobi-me" ) ),
				array( 'checkbox', 'hipnews_use_compat_css', __( 'Use compatibility CSS', "wpmobi-me" ), __( 'Add the compat.css file from the theme folder. Contains various CSS declarations for a variety of plugins.', "wpmobi-me" ), array( 'ipad' ) ),
				array( 'text', 'hipnews_excluded_categories', __( 'Excluded Categories (Comma list of category IDs)', "wpmobi-me" ), __( 'Posts in these categories will not be shown in MobileView. (e.g. 3,4,5)', "wpmobi-me" ), array( 'ipad' ) ),
				array( 'text', 'hipnews_excluded_tags', __( 'Excluded Tags (Comma list of tag IDs)', "wpmobi-me" ), __( 'Posts with these tags will not be shown in MobileView. (e.g. 3,4,5)', "wpmobi-me" ), array( 'ipad' ) ),
				array( 'section-end' ),

                array( 'section-start', 'misc-options', __( 'Slider Options', "wpmobi-me" ) ),
                array( 'checkbox', 'hipnews_slider_disable', __( 'Disable Slider on Home Page', "wpmobi-me" ), __( 'Will disable the slider on homepage.', "wpmobi-me" ) ),
                array( 'list', 'hipnews_slider_cat', __( 'Select Slider Category', 'wpmobi-me' ), __( 'Select which category to show on slider. ', "wpmobi-me" ), 
					$colabs_categories, array( 'ipad' )
				),
                array( 'list', 'hipnews_slider_count', __( 'Number of Slides', 'wpmobi-me' ), __( 'The number of slide that will shown on homepage slider. ', "wpmobi-me" ), 
					$array_oneten, array( 'ipad' )
				),
                array( 'checkbox', 'hipnews_slider_exclude', __( 'Slider post exclusion', "wpmobi-me" ), __( 'Prevent slider posts from being displayed more than once on the Front Page. Notice: this settings will be only implemented on the Front Page.', "wpmobi-me" ) ),
				array( 'section-end' )
                
				)
			),
		__( "Menu, Posts and Pages", "wpmobi-me" ) => array ( 'post-theme', 
			array(
				array( 'section-start', 'menu-options', __( 'Theme Menu', "wpmobi-me" ) ),
				array( 'checkbox', 'hipnews_use_menu_icon', __( 'Use menu icon for menu button', "wpmobi-me" ), __( 'If unchecked the word "Menu" will be shown instead of an icon.', "wpmobi-me"  ) ),
				array( 'checkbox', 'hipnews_show_categories', __( 'Show Categories in tab-bar', "wpmobi-me" ) ),
				array( 'checkbox', 'hipnews_show_tags', __( 'Show Tags in tab-bar', "wpmobi-me" ) ),
				array( 'checkbox', 'hipnews_show_account', __( 'Show Account in tab-bar', "wpmobi-me" ), __( 'Will always show account login/links in tab bar, even if registration for your website is not allowed.', "wpmobi-me"  ) ),
				array( 'checkbox', 'hipnews_show_search', __( 'Show Search in tab-bar', "wpmobi-me" ) ),
				array( 'checkbox', 'hipnews_show_admin_menu_link', __( 'Show "Admin" in Account tab links', "wpmobi-me" ), __( 'Shows an "Admin" menu link for logged in users that have edit posts capability.', "wpmobi-me"  ), array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_show_profile_menu_link', __( 'Show "Profile" in Account tab links', "wpmobi-me" ), __( 'Show a "Profile" link for all logged in users.', "wpmobi-me"  ), array( 'ipad' ) ),
				array( 'spacer' ),
				array( 'copytext', 'copytext-info-push', __( 'The push message and account tabs are shown/hidden automatically.', "wpmobi-me" ), '', array( 'ipad' ) ),
				array( 'section-end' )	,
				array( 'spacer' ),
				array( 'section-start', 'template-options', __( 'Theme Templates', "wpmobi-me" ) ),
				array( 'copytext', 'copytext-info-templates', __( 'These templates are custom to MobileView. They trigger a new menu item which can be configured in the menu settings once activated here.', "wpmobi-me" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_show_archives', __( 'Use MobileView Archives template', "wpmobi-me" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_show_links', __( 'Use MobileView Links template', "wpmobi-me" ), '', array( 'ipad' ) ),
				array( 'section-end' )	,
				array( 'spacer' ),
				array( 'section-start', 'post-options', __( 'Blog Listings', "wpmobi-me" ) ),
				array( 'copytext', 'copytext-info-post-opts', __( 'These settings affect the display of posts on the MobileView blog, blog archive & search pages.', "wpmobi-me" ) ),
				array( 'checkbox', 'hipnews_show_post_author', __( 'Show author name', "wpmobi-me" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_show_post_date', __( 'Show date', "wpmobi-me" ), __( 'Will show the date in post listings where thumbnails or none are selected in the post icon settings. Does not affect calendar icons.', "wpmobi-me" ), array( 'ipad' ) ),
				array( 'section-end' )	,
				array( 'spacer' ),
				array( 'section-start', 'single-post-options', __( 'Single Posts', "wpmobi-me" ) ),
				array( 'checkbox', 'hipnews_show_post_author_single', __( 'Show author in post header', "wpmobi-me" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_show_post_date_single', __( 'Show date in post header', "wpmobi-me" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_hide_responses', __( 'Hide Responses', "wpmobi-me" ), __('Hides comments, trackbacks and pingbacks by default, until a visitor clicks to show them. Speeds up load times if hidden.', "wpmobi-me" ) ),
				array( 'checkbox', 'hipnews_show_attached_image', __( 'Show attached image in post content', 'wpmobi-me' ), __( 'This option can be used to include an attached image in the post content.  The image is only included if it doesn\'t already exist in the post content.', 'wpmobi-me' ), array( 'ipad' ) ),
				array( 'list', 'hipnews_show_attached_image_location', __( 'Attached image location in post content', 'wpmobi-me' ), '', 
					array(
						'above' => __( 'Above content', 'wpmobi-me' ),
						'below' => __( 'Below content', 'wpmobi-me' )
					), array( 'ipad' )
				),				
				array( 'section-end' )	,
				array( 'spacer' ),
				array( 'section-start', 'page-options', __( 'Pages', "wpmobi-me" ) ),
				array( 'checkbox', 'hipnews_show_attached_image_on_page', __( 'Show attached image in page content', 'wpmobi-me' ), __( 'This option can be used to include an attached image in the page content.  The image is only included if it doesn\'t already exist in the page content.', 'wpmobi-me' ), array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_show_comments_on_pages', __( 'Show comments on pages', "wpmobi-me" ), __( 'Enabling this setting will cause comments to be shown on pages, if they are enabled in the WordPress settings.', "wpmobi-me" ), array( 'ipad' ) ),
				array( 'section-end' )	
			)
		),
		__( 'Mobile User Agents', "wpmobi-me" ) => array( 'user-agents',
			array(
				array( 'section-start', 'smartphone-devices', __( 'Default Mobile User Agents', "wpmobi-me" ) ),
				array( 'user-agents'),
				array( 'section-end' ),
				array( 'spacer' ),
				array( 'section-start', 'custom-user-agents', __( 'Custom Mobile User Agents', "wpmobi-me" ) ),
				array( 'textarea', 'hipnews_custom_user_agents', __( 'Enter additional user agents on separate lines, not device names or other information.', 'wpmobi-me' ) . '<br />' . sprintf( str_replace( 'Wikipedia', 'here', __( 'Visit %sWikipedia%s for a list of device user agents', 'wpmobi-me' ) ), '<a href="http://www.zytrax.com/tech/web/mobile_ids.html" target="_blank">', '</a>' ) ),	
				array( 'section-end' )
			)
		),
		//__( "Extensions", "wpmobi-me" ) => array( 'extensions', hipnews_get_extensions_menu() )
	);	
	
	return $menu;
}

function hipnews_get_extensions_menu() {
	if ( function_exists( 'get_flickrRSS' ) ) {
		$flickr_mobile_rss_option = array( 'checkbox', 'hipnews_show_flickr_rss', __( 'Use mobile MobileView FlickrRSS Photos template', "wpmobi-me" ), __( "Shows the latest 20 photos from your Flickr RSS feed on its own page, and adds a link to for it to your drop-down menu.", "wpmobi-me"  ) );
		$flickr_ipad_rss_option = array( 'checkbox', 'hipnews_ipad_show_flickr_button', __( 'Enable iPad FlickrRSS popover', "wpmobi-me" ), __( "Shows the latest 20 photos from your Flickr RSS feed in its own popover, and adds a button to the menubar.", "wpmobi-me"  ) );
	} else {
		$flickr_mobile_rss_option = array( 'checkbox-disabled', 'hipnews_show_flickr_rss', __( 'Use mobile MobileView Photos template', "wpmobi-me" ), __( "Shows the latest 20 photos from your Flickr RSS feed on its own page, and adds a link to for it to your drop-down menu.", "wpmobi-me"  ) );
		$flickr_ipad_rss_option = array( 'checkbox-disabled', 'hipnews_ipad_show_flickr_button', __( 'Enable iPad FlickrRSS popover', "wpmobi-me" ), __( "Shows the latest 20 photos from your Flickr RSS feed in its own popover, and adds a button to the menubar.", "wpmobi-me"  ) );
	}

	$top_section = 	array();	
	
	$middle_section = array();	

	$bottom_section = array(
		array( 'section-start', 'flickr-options', __( 'FlickrRSS', "wpmobi-me" ) ),
		array( 'copytext', 'copytext-flickr', sprintf( __( 'These settings require the %sFlickrRSS%s plugin to be installed:', "wpmobi-me" ), '<a href="http://eightface.com/wordpress/flickrrss/" target="_blank">', '</a>' ) ),
		$flickr_mobile_rss_option,
		$flickr_ipad_rss_option,
		array( 'section-end' )	
	);
		
	return apply_filters( 'hipnews_extensions_admin', array_merge( $top_section, $middle_section, $bottom_section ) );
}

function hipnews_theme_thumbnail_options() {
	$thumbnail_options = array();

	// WordPress 2.9+ thumbs
	if ( function_exists( 'add_theme_support' ) ) {
		$thumbnail_options['thumbnails'] = __( 'WordPress Thumbnails/Featured Images', 'wpmobi-me' );
	}	

	// 'thumbnail' Custom field thumbnails
	$thumbnail_options['custom_thumbs'] = __( 'Custom Field Thumbnails', 'wpmobi-me' );

	// Simple Post Thumbnails Plugin
	if (function_exists('p75GetThumbnail')) { 
		$thumbnail_options['simple_thumbs'] = __( 'Simple Post Thumbnails Plugin', 'wpmobi-me' );
	}
	
	// Show nothing!
	$thumbnail_options['none'] = __( 'None', 'wpmobi-me' );	
	
	return $thumbnail_options;
}

function hipnews_theme_font_options() {
	$font_options = array( 
			'sans-serif' => __( '***Some fonts may not be available on all devices***', 'wpmobi-me' ), 		
			'AmericanTypewriter' => __( 'AmericanTypewriter', 'wpmobi-me' ),
			'AmericanTypewriter-Condensed' => __( 'AmericanTypewriter-Condensed', 'wpmobi-me' ),
			'AmericanTypewriter-Bold' => __( 'AmericanTypewriter-Bold', 'wpmobi-me' ),
			'ArialMT' => __( 'ArialMT', 'wpmobi-me' ),
			'Arial-BoldMT' => __( 'Arial-BoldMT', 'wpmobi-me' ),
			'Baskerville' => __( 'Baskerville', 'wpmobi-me' ),
			'Baskerville-Bold' => __( 'Baskerville-Bold', 'wpmobi-me' ),
			'Cochin' => __( 'Cochin', 'wpmobi-me' ),
			'Cochin-Bold' => __( 'Cochin-Bold', 'wpmobi-me' ),
			'Courier' => __( 'Courier', 'wpmobi-me' ),
			'Courier-Bold' => __( 'Courier-Bold', 'wpmobi-me' ),
			'Copperplate' => __( 'Copperplate', 'wpmobi-me' ),
			'Copperplate-Bold' => __( 'Copperplate-Bold', 'wpmobi-me' ),
			'Futura-Medium' => __( 'Futura', 'wpmobi-me' ),
			'GeezaPro' => __( 'GeezaPro', 'wpmobi-me' ),
			'GeezaPro-Bold' => __( 'GeezaPro-Bold', 'wpmobi-me' ),
			'Georgia' => __( 'Georgia', 'wpmobi-me' ),
			'Georgia-Bold' => __( 'Georgia-Bold', 'wpmobi-me' ),
			'GillSans' => __( 'GillSans', 'wpmobi-me' ),
			'GillSans-Light' => __( 'GillSans-Light', 'wpmobi-me' ),
			'GillSans-Bold' => __( 'GillSans-Bold', 'wpmobi-me' ),
			'Helvetica' => __( 'Helvetica', 'wpmobi-me' ), 
			'Helvetica-Bold' => __( 'Helvetica-Bold', 'wpmobi-me' ), 
			'HelveticaNeue' => __( 'HelveticaNeue', 'wpmobi-me' ),
			'HelveticaNeue-Bold' => __( 'HelveticaNeue-Bold', 'wpmobi-me' ),
			'Optima-Regular' => __( 'Optima-Regular', 'wpmobi-me' ),
			'Optima-Bold' => __( 'Optima-Bold', 'wpmobi-me' ),
			'Palatino-Roman' => __( 'Palatino-Roman', 'wpmobi-me' ),
			'Thonburi' => __( 'Thonburi', 'wpmobi-me' ),
			'Thonburi-Bold' => __( 'Thonburi-Bold', 'wpmobi-me' ),
			'TimesNewRomanPSMT' => __( 'TimesNewRomanPSMT', 'wpmobi-me' ),
			'TrebuchetMS' => __( 'TrebuchetMS', 'wpmobi-me' ),
			'TrebuchetMS-Bold' => __( 'TrebuchetMS-Bold', 'wpmobi-me' ),
			'Verdana' => __( 'Verdana', 'wpmobi-me' ),
			'Verdana-Bold' => __( 'Verdana-Bold', 'wpmobi-me' )
		);

	return $font_options;
}
