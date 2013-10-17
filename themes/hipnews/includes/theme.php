<?php
add_action( 'mobileview_theme_init', 'hipnews_theme_initialization' );

add_action( 'mobileview_post_head', 'hipnews_iphone_meta' );
add_action( 'mobileview_post_head', 'hipnews_compat_css' );

add_filter( 'mobileview_custom_templates', 'hipnews_custom_templates' );

add_filter( 'mobileview_localize_scripts', 'hipnews_localize_scripts' );
add_filter( 'mobileview_setting_filter_hipnews_custom_user_agents', 'hipnews_user_agent_filter' );

add_filter( 'mobileview_has_post_thumbnail', 'hipnews_has_post_thumbnail' );
add_filter( 'mobileview_the_post_thumbnail', 'hipnews_the_post_thumbnail' );
add_filter( 'mobileview_the_content', 'hipnews_show_attached_image_filter' );	

add_filter( 'mobileview_body_classes', 'hipnews_global_body_classes' );


add_filter( 'mobileview_create_thumbnails', 'hipnews_should_create_thumbnails' );

/* Global Functions For HipNews Mobile + iPad */
function hipnews_theme_initialization() {

	if ( !is_admin() ) {
		wp_enqueue_script( 'jquery' );
		mobileview_persisitence_mode();
	/* Un-comment and reload to delete all theme cookies */
	//mobileview_hipnews_delete_cookie();

	}
}

function hipnews_global_body_classes( $global_body_classes ) {
	global $mobileview;
	if ( $mobileview->locale ) {
		$global_body_classes[] = 'locale-' . strtolower( $mobileview->locale );'';

		if ( $mobileview->locale != 'en_US' ) {
			$global_body_classes[] = 'translated';
		}
	}

	return $global_body_classes;
}

function hipnews_should_create_thumbnails( $create_thumbnails ) {
        $settings = mobileview_get_settings();     
        
        return ( $settings->hipnews_icon_type == 'thumbnails' );
}


// Eat all the cookies for lunch
function mobileview_hipnews_delete_cookie() {
	if ( isset( $_SERVER['HTTP_COOKIE'] ) ) {
	    $cookies = explode( ';', $_SERVER['HTTP_COOKIE'] );
		$url_path = str_replace( array( 'http://' . $_SERVER['SERVER_NAME'] . '','https://' . $_SERVER['SERVER_NAME'] . '' ), '', mobileview_get_bloginfo( 'url' ) . '/' );
	    foreach( $cookies as $cookie ) {
	        $parts = explode( '=', $cookie );
	        $name = trim( $parts[0] );
	        setcookie( $name, '', time()-1000 );
	        setcookie( $name, '', time()-1000, $url_path );
	    }
	}
}

function mobileview_persisitence_mode() {
 if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'iPhone' ) || strpos( $_SERVER['HTTP_USER_AGENT'], 'iPod' ) || strpos( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) ) {
	$settings = mobileview_get_settings();
		if ( $settings->hipnews_enable_persistent && defined( 'WP_USE_THEMES' ) && !is_admin() ) {
			if ( isset( $_COOKIE['mobileview-load-last-url'] ) && !isset( $_COOKIE['web-app-mode'] ) && strpos( $_SERVER['HTTP_USER_AGENT'], 'Safari/' ) === false ) {
				$saved_url = $_COOKIE['mobileview-load-last-url'];
				$time = time()+60*60*24*365;
				$url_path = str_replace( array( 'http://' . $_SERVER['SERVER_NAME'] . '','https://' . $_SERVER['SERVER_NAME'] . '' ), '', get_bloginfo( 'url' ) . '/' );
				setcookie( 'web-app-mode', 'on', 0, $url_path );
				if ( $saved_url && ( $saved_url != $_SERVER['REQUEST_URI'] ) ) {
					header( 'Location: ' . $saved_url );
					die;
				}
			}
		}
	}
}

function hipnews_is_web_app_mode(){
	if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Safari/' ) === false && ( strpos( $_SERVER['HTTP_USER_AGENT'], 'iPhone' ) || strpos( $_SERVER['HTTP_USER_AGENT'], 'iPod' ) || strpos( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) ) ) {
		return true;
	} else {
		return false;
	}
}

function hipnews_compat_css() {
	$settings = mobileview_get_settings();
	if ( $settings->hipnews_use_compat_css ) {
		echo "<link rel='stylesheet' type='text/css' href='" .MOBILEVIEW_URL . "/includes/css/compat.css?ver=" . mobileview_refreshed_files() . "' /> \n";		
	}
}

// This spits out all the meta tags for iPhone
// (web-app, startup img, device width, status bar style)
function hipnews_iphone_meta() {
	global $mobileview;
	$ipad = ( $mobileview->active_device_class == 'ipad' );
	$settings = mobileview_get_settings();

	if ( $ipad ) {	
		$status_type = 'default';
	} else {
		$status_type = $settings->hipnews_webapp_status_bar_color;	
	}
	
	// lock the viewport as 1:1, no zooming, unless enabled for mobile
	if ( $ipad || !hipnews_mobile_enable_zoom() ) {	
		echo "<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no' /> \n";
	} else {
		echo "<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes' /> \n";
	}
	
	if ( $settings->hipnews_webapp_enabled ) {
		echo "<meta name='apple-mobile-web-app-status-bar-style' content='" . $status_type . "' /> \n";	
		echo "<meta name='apple-mobile-web-app-capable' content='yes' /> \n";
	}

	if ( $settings->hipnews_webapp_use_loading_img ) {
	// iPhone
		if ( !$ipad ) {	
			if ( $settings->hipnews_webapp_loading_img_location ) {
				echo "<link rel='apple-hipnews-startup-image' href='" . $settings->hipnews_webapp_loading_img_location . "' /> \n";
			} else {
				echo "<link rel='apple-hipnews-startup-image' href='http://mobileviewlang.s3.amazonaws.com/resources/startup/iphone/startup.png' /> \n";	
			}
			if ( is_iOS_5() ) {
				if ( $settings->hipnews_webapp_retina_loading_img_location ) {
					echo "<link rel='apple-hipnews-startup-image' media='(device-width: 320px) and (-webkit-min-device-pixel-ratio: 2)' href='" . $settings->hipnews_webapp_retina_loading_img_location . "' /> \n";
				} else {
					echo "<link rel='apple-hipnews-startup-image' media='(device-width: 320px) and (-webkit-min-device-pixel-ratio: 2)' href='http://mobileviewlang.s3.amazonaws.com/resources/startup/iphone/retina-startup.png' /> \n";		
				}
			}
		}
	// iPad
		if ( $ipad ) {
			if ( $settings->hipnews_ipad_webapp_loading_img_location ) {
				echo "<link rel='apple-hipnews-startup-image' media='(device-width: 768px) and (orientation: portrait)' href='" . $settings->hipnews_ipad_webapp_loading_img_location . "' /> \n";
			} else {
				echo "<link rel='apple-hipnews-startup-image' media='(device-width: 768px) and (orientation: portrait)' href='http://mobileviewlang.s3.amazonaws.com/resources/startup/ipad/startup.png' /> \n";
			}
			if ( is_iOS_5() ) {
				if ( $settings->hipnews_ipad_webapp_landscape_loading_img_location ) {
					echo "<link rel='apple-hipnews-startup-image' media='(device-width: 768px) and (orientation: landscape)' href='" . $settings->hipnews_ipad_webapp_landscape_loading_img_location . "' /> \n";
				} else {
					echo "<link rel='apple-hipnews-startup-image' media='(device-width: 768px) and (orientation: landscape)' href='http://mobileviewlang.s3.amazonaws.com/resources/startup/ipad/startup-landscape.png' /> \n";
				}
				if ( $settings->hipnews_ipad_webapp_retina_loading_img_location ) {
					echo "<link rel='apple-hipnews-startup-image' media='(device-width: 768px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)' href='" . $settings->hipnews_ipad_webapp_retina_loading_img_location . "' /> \n";
				} else {
					echo "<link rel='apple-hipnews-startup-image' media='(device-width: 768px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)' href='http://mobileviewlang.s3.amazonaws.com/resources/startup/ipad/startup@2x.png' /> \n";
				}
				if ( $settings->hipnews_ipad_webapp_retina_landscape_loading_img_location ) {
					echo "<link rel='apple-hipnews-startup-image' media='(device-width: 768px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)' href='" . $settings->hipnews_ipad_webapp_retina_landscape_loading_img_location . "' /> \n";
				} else {
					echo "<link rel='apple-hipnews-startup-image' media='(device-width: 768px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)' href='http://mobileviewlang.s3.amazonaws.com/resources/startup/ipad/startup-landscape@2x.png' /> \n";
				}
			}
		}
	}
}

function hipnews_show_attached_image_filter( $content ) {
	global $post;
	$settings = mobileview_get_settings();

	if ( post_password_required( $post ) ) {
		return $content;
	}
	
	$should_show_image = false;
	if ( $settings->hipnews_show_attached_image && is_single() && !is_page() ) {
		$should_show_image = true;
	} else if ( $settings->hipnews_show_attached_image_on_page && is_page() ) {
		$should_show_image = true;
	}

	if ( $should_show_image ) {
		$photos = get_children( 
			array( 
				'post_parent' => $post->ID, 
				'post_status' => 'inherit', 
				'post_type' => 'attachment', 
				'post_mime_type' => 'image', 
				'order' => 'ASC', 
				'orderby' => 'menu_order ID'
			)
		);
	
		$attachment_html = false;	
		$gallery = '';
		if ( $photos ) {
			// Grab the first photo, may show more than one eventually			
			foreach( $photos as $photo ) {
				if(get_post_thumbnail_id($post->ID)!=$photo->ID)
				$gallery .=  '<li>'.wp_get_attachment_image( $photo->ID, 'large' ).'</li>';	
			}	
			$attachment_html = apply_filters( 'mobileview_image_attachment', '<div class="mobileview-image-attachment"><div class="flexslider"><ul class="slides">' . $gallery . '</ul></div></div>' );
		}
		
		if ( $attachment_html ) {
			$can_show_attachment = true;
			
			// Make sure the image isn't already in the post content
			if ( preg_match( '#src=\"(.*)\"#iU', $attachment_html, $matches ) ) {
				$image_url = str_replace( mobileview_get_bloginfo( 'url' ), '', $matches[1] );
				
				if ( strpos( $content, $image_url ) !== false ) {
					$can_show_attachment = false;	
				}	
			}
			
			if ( $can_show_attachment ) {			
				$settings = mobileview_get_settings();
				switch( $settings->hipnews_show_attached_image_location ) {
					case 'above':
						$content = $attachment_html . $content;
						break;
					case 'below':
						$content = $content . $attachment_html;
						break;	
				}
			}
		}
	}
	
	return $content;
}

// Remove whitespace from beginning and end of user agents
function hipnews_user_agent_filter( $agents ) {
	return trim( $agents );	
}

function hipnews_localize_scripts( $localize_info ) {
	$settings = mobileview_get_settings();
	$localize_info['loading_text'] = __( 'Loading...', 'mobileviewlang' );
	$localize_info['external_link_text'] = __( 'This is an external link.', 'mobileviewlang' );
	$localize_info['mobileview_ignored_text'] = __( 'This page is not mobile formatted.', 'mobileviewlang' );
	$localize_info['open_browser_text'] = __( 'Do you want to open it in Safari?', 'mobileviewlang' );
	$localize_info['hipnews_post_desc'] = __( 'Enter Description for Post', 'mobileviewlang' );
	$localize_info['leave_a_comment'] = __( 'Leave a comment', 'mobileviewlang' );
	$localize_info['leave_a_reply'] = __( 'Leave a reply to', 'mobileviewlang' );
	$localize_info['comment_failure'] = __( 'Comment publication failed. Please check your comment details and try again.', 'mobileviewlang' );
	$localize_info['comment_success'] = __( 'Your comment was published.', 'mobileviewlang' );
	$localize_info['validation_message'] = __( 'One or more fields were not completed.', 'mobileviewlang' );
	$localize_info['leave_webapp'] = __( 'Visiting this link will cause you to leave Web-App Mode.  Are you sure?', 'mobileviewlang' );
	$localize_info['add2home_message'] = $settings->hipnews_add2home_msg;
	$localize_info['toggle_on'] = __( 'ON', 'mobileviewlang' );
	$localize_info['toggle_off'] = __( 'OFF', 'mobileviewlang' );

	return $localize_info;	
}

function hipnews_custom_templates( $templates ) {
	$settings = mobileview_get_settings();

	return $templates;
}

function hipnews_was_redirect_target() {
	return ( isset( $_GET['mobileview_custom_redirect'] ) );
}

// Previous + Next Post Functions For Single Post Pages
function hipnews_get_previous_post_link() {	
	$settings = mobileview_get_settings();

	$prev_post = get_adjacent_post( false, $settings->hipnews_excluded_categories ); 
	if ( $prev_post ) {
		$prev_url = get_permalink( $prev_post->ID ); 
		echo '<a href="' . $prev_url . '" class="nav-back ajax-link">' . __( "Prev", "mobileviewlang" ) . '</a>';
	}
}

function hipnews_get_next_post_link() {
	$settings = mobileview_get_settings();

	$next_post = get_adjacent_post( false, $settings->hipnews_excluded_categories, 0 ); 
	if ( $next_post ) {
		$next_url = get_permalink( $next_post->ID ); 
		echo '<a href="' . $next_url . '" class="nav-fwd ajax-link">'. __( "Next", "mobileviewlang" ) . '</a>';
	}
}

// Dynamic archives heading text for archive result pages, and search
function hipnews_archive_text() {
	global $wp_query;
	$total_results = $wp_query->found_posts;

	if ( !is_home() ) {
		echo '<div class="archive-text">';
	}
	if ( is_search() ) {
		echo sprintf( __( "Search results &rsaquo; %s", "mobileviewlang" ), get_search_query() );
		echo '&nbsp;(' . $total_results . ')';
	} if ( is_category() ) {
		echo sprintf( __( "Categories &rsaquo; %s", "mobileviewlang" ), single_cat_title( "", false ) );
	} elseif ( is_tag() ) {
		echo sprintf( __( "Tags &rsaquo; %s", "mobileviewlang" ), single_tag_title(" ", false ) );
	} elseif ( is_day() ) {
		echo sprintf( __( "Archives &rsaquo; %s", "mobileviewlang" ),  get_the_time( 'F jS, Y' ) );
	} elseif ( is_month() ) {
		echo sprintf( __( "Archives &rsaquo; %s", "mobileviewlang" ),  get_the_time( 'F, Y' ) );
	} elseif ( is_year() ) {
		echo sprintf( __( "Archives &rsaquo; %s", "mobileviewlang" ),  get_the_time( 'Y' ) );
	} elseif ( is_404() ) {
		echo( __( "404 Not Found", "mobileviewlang" ) );
	}
	if ( !is_home() ) {
		echo '</div>';
	}
}

// If AJAX load more is turned off, this shows
function hipnews_archive_navigation_back() {
	if ( is_search() ) {
		previous_posts_link( __( 'Back in Search', "mobileviewlang" ) );
	} elseif ( is_category() ) {
		previous_posts_link( __( 'Back in Category', "mobileviewlang" ) );
	} elseif ( is_tag() ) {
		previous_posts_link( __( 'Back in Tag', "mobileviewlang" ) );
	} elseif ( is_day() ) {
		previous_posts_link( __( 'Back One Day', "mobileviewlang" ) );
	} elseif ( is_month() ) {
		previous_posts_link( __( 'Back One Month', "mobileviewlang" ) );
	} elseif ( is_year() ) {
		previous_posts_link( __( 'Back One Year', "mobileviewlang" ) );
	}
}

// If AJAX load more is turned off, this shows
function hipnews_archive_navigation_next() {
	if ( is_search() ) {
		next_posts_link( __( 'Next in Search', "mobileviewlang" ) );
	} elseif ( is_category() ) {		  
		next_posts_link( __( 'Next in Category', "mobileviewlang" ) );
	} elseif ( is_tag() ) {
		next_posts_link( __( 'Next in Tag', "mobileviewlang" ) );
	} elseif ( is_day() ) {
		next_posts_link( __( 'Next One Day', "mobileviewlang" ) );
	} elseif ( is_month() ) {
		next_posts_link( __( 'Next One Month', "mobileviewlang" ) );
	} elseif ( is_year() ) {
		next_posts_link( __( 'Next One Year', "mobileviewlang" ) );
	}
}

function hipnews_wp_comments_nav_on() {
	if ( get_option( 'page_comments' ) ) {
		return true;
	} else {
		return false;
	}
}

function hipnews_show_comments_on_pages() {
	$settings = mobileview_get_settings();
	if ( comments_open() && $settings->hipnews_show_comments_on_pages && !post_password_required() ) {
		return true;
	} else {
		return false;
	}
}

//2.2
function mobileview_comment_bubble_size() {
	if ( mobileview_get_comment_count() > 9 && mobileview_get_comment_count() < 99 ) {
		echo 'double'; 
	} else if ( mobileview_get_comment_count() > 99 ) {
		echo 'triple';
	}
}

function mobile_view_show_webapp_notice() {
	$settings = mobileview_get_settings();
	if ( $settings->hipnews_webapp_enabled && $settings->hipnews_show_webapp_notice ) {
		return true;
	} else {
		return false;
	}
}

function hipnews_is_ajax_enabled() {
	$settings = mobileview_get_settings();
	return $settings->hipnews_ajax_mode_enabled;
}

function hipnews_use_thumbnail_icons() {
	$settings = mobileview_get_settings();
	return ( $settings->hipnews_icon_type != 'calendar' && $settings->hipnews_icon_type != 'none' );
}

function hipnews_show_admin_menu_link() {
	$settings = mobileview_get_settings();
	if ( hipnews_show_account_tab() ) {
		if ( $settings->hipnews_show_admin_menu_link ) {
			return true;
		} else {
			return false;
		}
	}
}

function hipnews_show_account_tab() {
	$settings = mobileview_get_settings();
	if ( get_option( 'comment_registration' ) || get_option( 'users_can_register' ) || $settings->hipnews_show_account ) {
		return true;
	} else {
		return false;
	}
}

function hipnews_show_profile_menu_link() {
	$settings = mobileview_get_settings();
	if ( hipnews_show_account_tab() ) {
		if ( $settings->hipnews_show_profile_menu_link ) {
			return true;
		} else {
			return false;
		}
	}
}

function hipnews_show_author_in_posts() {
	$settings = mobileview_get_settings();
	return $settings->hipnews_show_post_author;
}

function hipnews_show_date_in_posts() {
	$settings = mobileview_get_settings();
	return $settings->hipnews_show_post_date;
}

// 2.2
// filter added in functions.php for mobile + ipad
function hipnews_exclude_categories( $query ) {
	$settings = mobileview_get_settings();
	$excluded = $settings->hipnews_excluded_categories;
	
	if ( $excluded ) {
		$cats = explode( ',', $excluded );
		$new_cats = array();
		
		foreach( $cats as $cat ) {
			$new_cats[] = trim( $cat );
		}
	
		if ( !$query->is_single() ) {
			$query->set( 'category__not_in', $new_cats );
		}	
	}
		
	return $query;
}

// 2.2
// filter added in functions.php for mobile + ipad
function hipnews_exclude_tags( $query ) {
	$settings = mobileview_get_settings();
	$excluded = $settings->mobileview_excluded_tags;
	
	if ( $excluded ) {
		$tags = explode( ',', $excluded );
		$new_tags = array();
		
		foreach( $tags as $tag ) {
			$new_tags[] = trim( $tag );
		}
	
		if ( !$query->is_single() ) {
			$query->set( 'tag__not_in', $new_tags );
		}	
	}
	
	return $query;
}

// 2.2
// Search results only have posts for now
// filter added in functions.php for mobile + ipad
function hipnews_search_filter( $query ) {
	if ( $query->is_search ) {
		$query->set( 'post_type', 'post' );
	}
	return $query;
}

// Check what order comments are displayed, governs whether 'load more comments' link uses previous_ or next_ function
function hipnews_comments_newer() {
	if ( get_option( 'default_comments_page' ) == 'newest' ) {
			return true;
		} else {
			return false;
		}
}

// Thumbnail stuff added in 2.0.4
function hipnews_has_post_thumbnail() {
	global $post;
	
	$settings = mobileview_get_settings();
	
	$has_post_thumbnail = false;
	
	switch( $settings->hipnews_icon_type ) {
		case 'thumbnails':
			$has_post_thumbnail = function_exists( 'has_post_thumbnail' ) && has_post_thumbnail();
			break;
		case 'simple_thumbs':
			$has_post_thumbnail = function_exists( 'p75GetThumbnail' ) && p75HasThumbnail( $post->ID );
			break;
		case 'custom_thumbs':
			$has_post_thumbnail = get_post_meta( $post->ID, $settings->hipnews_custom_field_thumbnail_name, true ) || get_post_meta( $post->ID, 'Thumbnail', true ) || get_post_meta( $post->ID, 'thumbnail', true );
			break;
	}

	return $has_post_thumbnail;
}

function hipnews_the_post_thumbnail( $thumbnail ) {
	global $post;
	
	$settings = mobileview_get_settings();	
	$custom_field_name = $settings->hipnews_custom_field_thumbnail_name;
	
	switch( $settings->hipnews_icon_type ) {
		case 'thumbnails':
			if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
				return $thumbnail;	
			}
			break;
		case 'simple_thumbs':
			if ( function_exists( 'p75GetThumbnail' ) && p75HasThumbnail( $post->ID ) ) {
				return p75GetThumbnail( $post->ID );	
			}
			break;
		case 'custom_thumbs':
			if ( get_post_meta( $post->ID, $custom_field_name, true ) ) {
				return get_post_meta( $post->ID, $custom_field_name, true );
			} else if ( get_post_meta( $post->ID, 'Thumbnail', true ) ) {
				return get_post_meta( $post->ID, 'Thumbnail', true );
			} else if ( get_post_meta( $post->ID, 'thumbnail', true ) ) {
				return get_post_meta( $post->ID, 'thumbnail', true );
			}
			
			break;
	}		
	// return default if none of those exist
	return mobileview_get_bloginfo( 'template_directory' ) . '/images/default-thumbnail.png';
}

function hipnews_thumbs_on_single() {
	$settings = mobileview_get_settings();	
	if ( $settings->hipnews_thumbs_on_single ) {
		return true;
	} else {
		return false;
	}
}

function hipnews_thumbs_on_pages() {
	$settings = mobileview_get_settings();	
	if ( $settings->hipnews_thumbs_on_pages && hipnews_has_post_thumbnail() ) {
		return true;
	} else {
		return false;
	}
}

//Single Post Page
function hipnews_show_date_single() {
	$settings = mobileview_get_settings();
	return $settings->hipnews_show_post_date_single;
}

function hipnews_show_author_single() {
	$settings = mobileview_get_settings();
	return $settings->hipnews_show_post_author_single;
}


// Custom Post Types
function hipnews_should_show_taxonomy() {
	global $post;
	
	$should_show_taxonomy = ( $post->post_type == 'post' );
	
	return apply_filters( 'mobileview_should_show_taxonomy', $should_show_taxonomy );
}

function hipnews_has_custom_taxonomy() {
	global $post;
	
	$custom_taxonomy = ( $post->post_type != 'post' );
	
	return apply_filters( 'mobileview_has_custom_taxonomy', $custom_taxonomy );
}

function hipnews_get_custom_taxonomy() {
	$custom_tax = array();
	return apply_filters( 'mobileview_get_custom_taxonomy', $custom_tax );
}

function hipnews_url_encode( $string ) {
    $entities = array( '%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D', '%C2' );
    $replacements = array( '!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]", "-" );
    return str_replace( $entities, $replacements, urlencode( $string ) );
}
