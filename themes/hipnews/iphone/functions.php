<?php

do_action( 'wpmobi_functions_start' );

add_action( 'wpmobi_theme_init', 'hipnews_init' );
add_action( 'wpmobi_theme_language', 'hipnews_language' );
add_action( 'wpmobi_post_head', 'wpmobi_header_style' );
add_filter( 'wpmobi_body_classes', 'hipnews_body_classes' );

// Functions from root-functions.php, don't want them changing the admin queries
add_filter( 'pre_get_posts', 'hipnews_exclude_categories' );
add_filter( 'pre_get_posts', 'hipnews_exclude_tags' );
add_filter( 'pre_get_posts', 'hipnews_search_filter' );

//--Device Theme Functions for HipNews --//

function hipnews_init() {

	$output_hipnews_scripts = apply_filters( 'hipnews_output_scripts', true );

	if ( $output_hipnews_scripts && !is_admin() ) {
	
		wp_enqueue_script( 'fitvids', WPMOBI_URL . '/include/js/fitvids.js', array( 'hipnews-js' ), wpmobi_refreshed_files() );

		$minfile = WPMOBI_DIR . '/themes/hipnews/iphone/js/theme.min.js';
		if ( file_exists( $minfile ) ) {
			wp_enqueue_script( 'hipnews-js', wpmobi_get_bloginfo('template_directory') . '/js/hipnews.min.js', array( 'jquery-form' ), wpmobi_refreshed_files() );
		} else {
			wp_enqueue_script( 'hipnews-js', wpmobi_get_bloginfo('template_directory') . '/js/hipnews.js', array( 'jquery-form' ), wpmobi_refreshed_files() );
		}

	if ( show_webapp_notice() ) {
		$minfile = WPMOBI_DIR . '/include/js/add2home.min.js';
		if ( file_exists( $minfile ) ) {
			wp_enqueue_script( 'add2home', WPMOBI_URL . '/include/js/add2home.min.js', array( 'hipnews-js' ), wpmobi_refreshed_files() );
		} else {
			wp_enqueue_script( 'add2home', WPMOBI_URL . '/include/js/add2home.js', array( 'hipnews-js' ), wpmobi_refreshed_files() );
		}
	}
    
    wp_enqueue_script( 'plugins-js', wpmobi_get_bloginfo('template_directory') . '/js/plugins.js', array( 'jquery-form' ), wpmobi_refreshed_files() );
    wp_enqueue_script( 'scripts-js', wpmobi_get_bloginfo('template_directory') . '/js/scripts.js', array( 'plugins-js' ), wpmobi_refreshed_files() );
    
}
		
function wpmobi_header_style() {
	$settings = wpmobi_get_settings();
	//$header_style = $settings->hipnews_header_color_style;
    if(!empty($header_style)){
	   //echo "<link rel='stylesheet' type='text/css' href='" . wpmobi_get_bloginfo('template_directory') . "/css/". $header_style .".css?ver=" . wpmobi_refreshed_files() . "' /> \n";
    }		
}

} //init

function hipnews_language( $locale ) {
	// In a normal theme a language file would be loaded here for text translation
}

function hipnews_body_classes( $body_classes ) {
	$settings = wpmobi_get_settings();
	
	$is_idevice = strpos( $_SERVER['HTTP_USER_AGENT'],'iPad' ) || strpos($_SERVER['HTTP_USER_AGENT'],'iPhone' ) || strpos($_SERVER['HTTP_USER_AGENT'],'iPod' );

	$body_classes[] = $settings->hipnews_icon_type;
	
	//$body_classes[] = $settings->hipnews_header_color_style;

	$body_classes[] = $settings->hipnews_calendar_icon_bg;

	$body_classes[] = $settings->hipnews_text_justification;

	if ( $settings->hipnews_webapp_use_ajax ) {
		$body_classes[] = 'ajax-on';
	}

	if ( !$settings->enable_menu_icons ) {
		$body_classes[] = 'no-icons';
	}

	if ( $settings->hipnews_hide_addressbar ) {
		$body_classes[] = 'hide-addressbar';
	}

//	if ( $settings->make_menu_relative ) {
//		$body_classes[] = 'relative-menu';
//	}
	
	if ( $settings->hipnews_webapp_status_bar_color == 'black-translucent' ) {
		$body_classes[] = $settings->hipnews_webapp_status_bar_color;
	}

	if ( $is_idevice ) {
		$body_classes[] = 'idevice';
	} else {
		$body_classes[] = 'generic';
	}

	if ( $is_idevice && is_iOS_5() ) {
		$body_classes[] = 'ios5';
	}

	if ( $settings->hipnews_enable_persistent ) {
		$body_classes[] = 'loadsaved';
	}

	return $body_classes;
}

// New logo code
function hipnews_mobile_has_logo() {
	$settings = wpmobi_get_settings();
		if ( $settings->hipnews_header_img_location ) {
			return true;
		} else {
			return false;
		}
}

function hipnews_has_header_retina_image() {
	$settings = wpmobi_get_settings();
	
	return apply_filters( 'hipnews_has_header_retina_image', ( $settings->hipnews_retina_header_img_location && strlen( $settings->hipnews_retina_header_img_location ) ) );
}

function hipnews_get_header_image_location() {
	$settings = wpmobi_get_settings();
	
	if ( hipnews_has_header_retina_image() ) {
		return apply_filters( 'hipnews_header_image_location', $settings->hipnews_retina_header_img_location );
	} else {
		return apply_filters( 'hipnews_header_image_location', $settings->hipnews_header_img_location );
	}
}

function hipnews_mobile_logo_img() {
	if ( hipnews_has_header_retina_image() ) {
		echo "<img id='retina-custom-logo' src='" . hipnews_get_header_image_location() . "' alt='retina-logo-image' /> \n";
	} else {
		echo "<img id='custom-logo' src='" . hipnews_get_header_image_location() . "' alt='logo-image' /> \n";
	}
}

function hipnews_background() {
	$settings = wpmobi_get_settings();
	return $settings->hipnews_background_image;
}
/*
function hipnews_mobile_show_site_icon() {
	$settings = wpmobi_get_settings();
		if ( $settings->hipnews_show_header_icon ) {
			return true;
		} else {
			return false;		
		}
}
*/
function hipnews_mobile_has_menu_icon() {
	$settings = wpmobi_get_settings();
	
	if ( $settings->hipnews_use_menu_icon ) {
		return true;
	} else {
		return false;
	}
}

function hipnews_mobile_hide_responses() {
	$settings = wpmobi_get_settings();
	return $settings->hipnews_hide_responses;
}

function hipnews_mobile_show_search_button() {
	$settings = wpmobi_get_settings();
	return $settings->hipnews_show_search;
}

function hipnews_mobile_show_categories_tab() {
	$settings = wpmobi_get_settings();
	return $settings->hipnews_show_categories;
}

function hipnews_mobile_show_tags_tab() {
	$settings = wpmobi_get_settings();
	return $settings->hipnews_show_tags;
}

function hipnews_mobile_com_toggle() {
	if ( !function_exists( 'id_activate_hooks' ) || !function_exists( 'dsq_is_installed' ) ) {
		$comment_string1 = __( 'No Comments Yet', "wpmobi-me" );
		$comment_string2 = __( '1 Comment', "wpmobi-me" );
		$comment_string3 = __( '% Comments', "wpmobi-me" );

		echo '<a id="comments-' . get_the_ID() . '" class="post no-ajax  com-toggle">';

		if ( hipnews_mobile_hide_responses() ) {
			echo '<img id="com-arrow" class="com-arrow" src="' . wpmobi_get_bloginfo('template_directory') . '/images/com_arrow.png" alt="arrow" />';
		} else {
			echo '<img id="com-arrow" class="com-arrow-down" src="' . wpmobi_get_bloginfo('template_directory') . '/images/com_arrow.png" alt="arrow" />';	
		}
		comments_number( $comment_string1, $comment_string2, $comment_string3 );
		echo '</a>';
	}
}

// Custom Comments
// Custom callback to list comments in the your-theme style
function hipnews_custom_comments( $comment, $args, $depth ) {
	$GLOBALS[ 'comment' ] = $comment;
	$GLOBALS[ 'comment_depth' ] = $depth;
  ?>
   <li id="comment-<?php comment_ID() ?>" <?php comment_class() ?>>
   	<div class="comment-entry">
	    <div class="comment-top">
	    	<?php hipnews_commenter_link() ?>
	    	<div class="comment-meta">
		    	<?php printf( __( '%1$s - %2$s <span class="meta-sep"></span>', "wpmobi-me" ),
					get_comment_date( 'M d' ), 
					get_comment_time() ); 
				?>
	    	</div>
	    	<div class="comment-buttons">
		    	<?php edit_comment_link( __( 'Edit', "wpmobi-me" ), ' <span class="edit-link">', '</span>' ); ?>
					<?php if ( !class_exists( 'wp_thread_comment' ) ) // echo the comment reply link
					if( $args[ 'type' ] == 'all' || get_comment_type() == 'comment' ) : comment_reply_link( 
						array_merge( 
							$args, array(
								'reply_text' => __( 'Reply',"wpmobi-me" ),
								'login_text' => __( 'Reply.',"wpmobi-me" ),
								'depth' => $depth
							)
						) 
					);
					endif; ?>
				</div>
				<?php if ( $comment->comment_approved == '0' ) __( "<span class='unapproved'>Your comment is awaiting moderation.</span>", "wpmobi-me" ) ?>
			</div>

			<div class="comment-content">
				<?php comment_text() ?>
			</div>
		</div>

<?php } // end custom_comments

// Produces an avatar image with the hCard-compliant photo class
function hipnews_commenter_link() {
	$commenter = get_comment_author_link();
	if ( preg_match( '/<a[^>]* class=[^>]+>/', $commenter ) ) {
		$commenter = preg_replace( '/(<a[^>]* class=[\'"]?)/', '\\1url ' , $commenter );
	} else {
		$commenter = preg_replace( '/(<a )/', '\\1class="url "/' , $commenter );
	}

	$avatar_email = get_comment_author_email();
	$avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( $avatar_email, 68 ) );
	echo $avatar . ' <span class="fn n">' . $commenter . '</span>';
} // end commenter_link
