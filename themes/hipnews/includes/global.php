<?php

add_filter( 'mobileview_default_settings', 'hipnews_default_settings' );
add_filter( 'mobileview_theme_menu', 'hipnews_admin_menu' );

/* Default Settings */

// All default settings must be added to the $settings object here
// All settings should be properly namespaced in a copied theme, i.e. theme_name_my_setting instead of just my_setting or hipnews_
function hipnews_default_settings( $settings ) {
	// General Settings
	$settings->mobileview_latest_posts_page = 'none';
	$settings->hipnews_ajax_mode_enabled = true;
	$settings->hipnews_mobile_enable_zoom = false;
	$settings->hipnews_use_compat_css = true;
	$settings->mobileview_excluded_categories = '';
	$settings->mobileview_excluded_tags = '';
	
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
	$settings->hipnews_icon_type = 'thumbnails';
	$settings->hipnews_calendar_icon_bg = 'cal-colors';
	$settings->hipnews_custom_cal_icon_color = '';
	$settings->hipnews_custom_field_thumbnail_name = 'thumbnail';
	$settings->hipnews_thumbs_on_single = false;
	$settings->hipnews_thumbs_on_pages = false;

	// Menu Settings
	$settings->make_menu_relative = true;

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
	$settings->hipnews_add2home_msg = __( 'Install this Web-App on your [device]: tap [icon] then "Add to Home Screen"', 'mobileviewlang' );

    // Slider Settings
    $settings->hipnews_slider_disable = false;
    $settings->hipnews_slider_cat = '';
    $settings->hipnews_slider_count = '3';
    $settings->hipnews_slider_exclude = true;
    
	// Extensions //

	// FlickrRSS
	$settings->hipnews_show_flickr_rss = false;
	$settings->hipnews_ipad_show_flickr_button = false;
	
	$settings->switch_colour = '#4FD065';
	
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
		__( "General Settings", "mobileviewlang" ) => array ( 'general', 
			array(
				array( 'section-start', 'misc-options', __( 'Miscellaneous Options', "mobileviewlang" ) ),
				array( 'custom-latest', '', '', '', array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_ajax_mode_enabled', __( 'Enable AJAX "Load More" link for posts and comments', "mobileviewlang" ), __( 'Posts and comments will be appended to existing content with an AJAX "Load More..." link. If unchecked regular post/comment pagination will be used.', "mobileviewlang" ), array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_mobile_enable_zoom', __( 'Allow zooming', "mobileviewlang" ), __( 'Will allow visitors to zoom in/out on content.', "mobileviewlang" ) ),
				array( 'checkbox', 'hipnews_use_compat_css', __( 'Use compatibility CSS', "mobileviewlang" ), __( 'Add the compat.css file from the theme folder. Contains various CSS declarations for a variety of plugins.', "mobileviewlang" ), array( 'ipad' ) ),
				array( 'text', 'mobileview_excluded_categories', __( 'Excluded Categories (Comma list of category IDs)', "mobileviewlang" ), __( 'Posts in these categories will not be shown in MobileView. (e.g. 3,4,5)', "mobileviewlang" ), array( 'ipad' ) ),
				array( 'text', 'mobileview_excluded_tags', __( 'Excluded Tags (Comma list of tag IDs)', "mobileviewlang" ), __( 'Posts with these tags will not be shown in MobileView. (e.g. 3,4,5)', "mobileviewlang" ), array( 'ipad' ) ),
				array( 'section-end' ),

                array( 'section-start', 'misc-options', __( 'Slider Options', "mobileviewlang" ) ),
                array( 'checkbox', 'hipnews_slider_disable', __( 'Disable Slider on Home Page', "mobileviewlang" ), __( 'Will disable the slider on homepage.', "mobileviewlang" ) ),
                array( 'list', 'hipnews_slider_cat', __( 'Select Slider Category', 'mobileviewlang' ), __( 'Select which category to show on slider. ', "mobileviewlang" ), 
					$colabs_categories, array( 'ipad' )
				),
                array( 'list', 'hipnews_slider_count', __( 'Number of Slides', 'mobileviewlang' ), __( 'The number of slide that will shown on homepage slider. ', "mobileviewlang" ), 
					$array_oneten, array( 'ipad' )
				),
                array( 'checkbox', 'hipnews_slider_exclude', __( 'Slider post exclusion', "mobileviewlang" ), __( 'Prevent slider posts from being displayed more than once on the Front Page. Notice: this settings will be only implemented on the Front Page.', "mobileviewlang" ) ),
				array( 'section-end' )
                
				)
			),
		__( "Menu, Posts and Pages", "mobileviewlang" ) => array ( 'post-theme', 
			array(
              
				array( 'section-start', 'template-options', __( 'Theme Templates', "mobileviewlang" ) ),
				array( 'copytext', 'copytext-info-templates', __( 'These templates are custom to MobileView. They trigger a new menu item which can be configured in the menu settings once activated here.', "mobileviewlang" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_show_archives', __( 'Use MobileView Archives template', "mobileviewlang" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_show_links', __( 'Use MobileView Links template', "mobileviewlang" ), '', array( 'ipad' ) ),
				array( 'section-end' )	,
				array( 'spacer' ),
				array( 'section-start', 'post-options', __( 'Blog Listings', "mobileviewlang" ) ),
				array( 'copytext', 'copytext-info-post-opts', __( 'These settings affect the display of posts on the MobileView blog, blog archive & search pages.', "mobileviewlang" ) ),
				array( 'checkbox', 'hipnews_show_post_author', __( 'Show author name', "mobileviewlang" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_show_post_date', __( 'Show date', "mobileviewlang" ), __( 'Will show the date in post listings where thumbnails or none are selected in the post icon settings. Does not affect calendar icons.', "mobileviewlang" ), array( 'ipad' ) ),
				array( 'section-end' )	,
				array( 'spacer' ),
				array( 'section-start', 'single-post-options', __( 'Single Posts', "mobileviewlang" ) ),
				array( 'checkbox', 'hipnews_show_post_author_single', __( 'Show author in post header', "mobileviewlang" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_show_post_date_single', __( 'Show date in post header', "mobileviewlang" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_hide_responses', __( 'Hide Responses', "mobileviewlang" ), __('Hides comments, trackbacks and pingbacks by default, until a visitor clicks to show them. Speeds up load times if hidden.', "mobileviewlang" ) ),
				array( 'checkbox', 'hipnews_thumbs_on_single', __( 'Show thumbnails on single post pages next to the post title', 'mobileviewlang' ), '', array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_show_attached_image', __( 'Show attached image in post content', 'mobileviewlang' ), __( 'This option can be used to include an attached image in the post content.  The image is only included if it doesn\'t already exist in the post content.', 'mobileviewlang' ), array( 'ipad' ) ),
				array( 'list', 'hipnews_show_attached_image_location', __( 'Attached image location in post content', 'mobileviewlang' ), '', 
					array(
						'above' => __( 'Above content', 'mobileviewlang' ),
						'below' => __( 'Below content', 'mobileviewlang' )
					), array( 'ipad' )
				),				
				array( 'section-end' )	,
				array( 'spacer' ),
				array( 'section-start', 'page-options', __( 'Pages', "mobileviewlang" ) ),
				array( 'checkbox', 'hipnews_show_attached_image_on_page', __( 'Show attached image in page content', 'mobileviewlang' ), __( 'This option can be used to include an attached image in the page content.  The image is only included if it doesn\'t already exist in the page content.', 'mobileviewlang' ), array( 'ipad' ) ),
				array( 'checkbox', 'hipnews_show_comments_on_pages', __( 'Show comments on pages', "mobileviewlang" ), __( 'Enabling this setting will cause comments to be shown on pages, if they are enabled in the WordPress settings.', "mobileviewlang" ), array( 'ipad' ) ),
				array( 'section-end' )	
			)
		),
		__( 'Mobile User Agents', "mobileviewlang" ) => array( 'user-agents',
			array(
				array( 'section-start', 'smartphone-devices', __( 'Default Mobile User Agents', "mobileviewlang" ) ),
				array( 'user-agents'),
				array( 'section-end' ),
				array( 'spacer' ),
				array( 'section-start', 'custom-user-agents', __( 'Custom Mobile User Agents', "mobileviewlang" ) ),
				array( 'textarea', 'hipnews_custom_user_agents', __( 'Enter additional user agents on separate lines, not device names or other information.', 'mobileviewlang' ) . '<br />' . sprintf( str_replace( 'Wikipedia', 'here', __( 'Visit %sWikipedia%s for a list of device user agents', 'mobileviewlang' ) ), '<a href="http://www.zytrax.com/tech/web/mobile_ids.html" target="_blank">', '</a>' ) ),	
				array( 'section-end' )
			)
		),
	);	
	
	return $menu;
}

function hipnews_theme_thumbnail_options() {
	$thumbnail_options = array();

	// WordPress 2.9+ thumbs
	if ( function_exists( 'add_theme_support' ) ) {
		$thumbnail_options['thumbnails'] = __( 'WordPress Thumbnails/Featured Images', 'mobileviewlang' );
	}	

	// 'thumbnail' Custom field thumbnails
	$thumbnail_options['custom_thumbs'] = __( 'Custom Field Thumbnails', 'mobileviewlang' );

	// Simple Post Thumbnails Plugin
	if (function_exists('p75GetThumbnail')) { 
		$thumbnail_options['simple_thumbs'] = __( 'Simple Post Thumbnails Plugin', 'mobileviewlang' );
	}
	
	// Show nothing!
	$thumbnail_options['none'] = __( 'None', 'mobileviewlang' );	
	
	return $thumbnail_options;
}

function hipnews_theme_font_options() {
	$font_options = array( 
			'sans-serif' => __( '***Some fonts may not be available on all devices***', 'mobileviewlang' ), 		
			'AmericanTypewriter' => __( 'AmericanTypewriter', 'mobileviewlang' ),
			'AmericanTypewriter-Condensed' => __( 'AmericanTypewriter-Condensed', 'mobileviewlang' ),
			'AmericanTypewriter-Bold' => __( 'AmericanTypewriter-Bold', 'mobileviewlang' ),
			'ArialMT' => __( 'ArialMT', 'mobileviewlang' ),
			'Arial-BoldMT' => __( 'Arial-BoldMT', 'mobileviewlang' ),
			'Baskerville' => __( 'Baskerville', 'mobileviewlang' ),
			'Baskerville-Bold' => __( 'Baskerville-Bold', 'mobileviewlang' ),
			'Cochin' => __( 'Cochin', 'mobileviewlang' ),
			'Cochin-Bold' => __( 'Cochin-Bold', 'mobileviewlang' ),
			'Courier' => __( 'Courier', 'mobileviewlang' ),
			'Courier-Bold' => __( 'Courier-Bold', 'mobileviewlang' ),
			'Copperplate' => __( 'Copperplate', 'mobileviewlang' ),
			'Copperplate-Bold' => __( 'Copperplate-Bold', 'mobileviewlang' ),
			'Futura-Medium' => __( 'Futura', 'mobileviewlang' ),
			'GeezaPro' => __( 'GeezaPro', 'mobileviewlang' ),
			'GeezaPro-Bold' => __( 'GeezaPro-Bold', 'mobileviewlang' ),
			'Georgia' => __( 'Georgia', 'mobileviewlang' ),
			'Georgia-Bold' => __( 'Georgia-Bold', 'mobileviewlang' ),
			'GillSans' => __( 'GillSans', 'mobileviewlang' ),
			'GillSans-Light' => __( 'GillSans-Light', 'mobileviewlang' ),
			'GillSans-Bold' => __( 'GillSans-Bold', 'mobileviewlang' ),
			'Helvetica' => __( 'Helvetica', 'mobileviewlang' ), 
			'Helvetica-Bold' => __( 'Helvetica-Bold', 'mobileviewlang' ), 
			'HelveticaNeue' => __( 'HelveticaNeue', 'mobileviewlang' ),
			'HelveticaNeue-Bold' => __( 'HelveticaNeue-Bold', 'mobileviewlang' ),
			'Optima-Regular' => __( 'Optima-Regular', 'mobileviewlang' ),
			'Optima-Bold' => __( 'Optima-Bold', 'mobileviewlang' ),
			'Palatino-Roman' => __( 'Palatino-Roman', 'mobileviewlang' ),
			'Thonburi' => __( 'Thonburi', 'mobileviewlang' ),
			'Thonburi-Bold' => __( 'Thonburi-Bold', 'mobileviewlang' ),
			'TimesNewRomanPSMT' => __( 'TimesNewRomanPSMT', 'mobileviewlang' ),
			'TrebuchetMS' => __( 'TrebuchetMS', 'mobileviewlang' ),
			'TrebuchetMS-Bold' => __( 'TrebuchetMS-Bold', 'mobileviewlang' ),
			'Verdana' => __( 'Verdana', 'mobileviewlang' ),
			'Verdana-Bold' => __( 'Verdana-Bold', 'mobileviewlang' )
		);

	return $font_options;
}
