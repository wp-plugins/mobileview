<?php
/*!		\brief Called inside a template file inside the HEAD tag
 *
 *		This method should be called inside the HEAD area of a MobileView theme template file.
 *		This method ultimately intercepts the main WordPress wp_head action.
 *
 *		The WordPress action \em mobileview_pre_head is executed at the start of the header block, and the action \em mobileview_post_head is executed at the end
 *		of the header block.
 *
 *		\par Typical Usage:
 *		\include mobileview-head.php
 *
 *		\ingroup templatetags
 */	 
function mobileview_head() {
	do_action( 'mobileview_pre_head' );	
	wp_head();		
	do_action( 'mobileview_post_head' );
}
/*!		\brief Called inside a template file prior to the closing BODY tag.
 *
 *		This method should be called just prior to the closing BODY tag inside of a MobileView theme template file.
 *		This method ultimately intercepts the main WordPress \em wp_footer action.  
 *
 *		The WordPress action \em mobileview_pre_footer is executed at the start of the footer block, and the action \em mobileview_post_footer is executed at the end
 *		of the footer block.
 *
 *		\par Typical Usage:
 *		The following is the typical usage of this method and proper placement in the HTML structure:
 *
 *		\include mobileview-footer.php
 *
 *		\ingroup templatetags 
 */	 
function mobileview_footer() {
	do_action( 'mobileview_pre_footer' );
	wp_footer();	
	do_action( 'mobileview_post_footer' );
}
/*!		\brief Echos the title for the WordPress site
 *
 *		This method echos the title for the WordPress site, and looks for the edited version in the MobileView settings first.  
 *		It adds the title of the page when viewing a page or a post.  This method is meant to be used
 *		for the HTML TITLE tag.
 *
 *		\par Typical Usage:
 *		The following is how this method is typically used:
 *		\include mobileview-title.php
 *
 *		\ingroup templatetags 
 */
function mobileview_title() {
	if ( is_home() ) {
		echo mobileview_bloginfo( 'site_title' );
	} else {
		echo mobileview_bloginfo( 'site_title' ) . wp_title( ' &raquo; ', 0 );	
	}
}
/*!		\brief Echos the short title for the WordPress site
 *
 *		This method echos the result of the mobileview_get_site_title() method.
 *
 *		\ingroup templatetags 
 */
function mobileview_site_title() {
	echo mobileview_get_site_title();
}
/*!		\brief Returns the short title for the WordPress site
 *
 *		This method returns the short title for the WordPress site.  If a short title hasn't been set, it returns the long title as defined
 *		in the WordPress admin panel.
 *
 *		This method can be filtered using the WordPress filter \em mobileview_site_title.
 *
 *		\ingroup templatetags 
 */
function mobileview_get_site_title() {
	global $mobileview;	
	$settings = $mobileview->get_settings();
	return apply_filters( 'mobileview_site_title', $settings->site_title );
}
/*!		\brief Returns true when there are posts available for the loop.
 *
 *		This method returns true when there are posts available for the loop.  This method is a wrapper for the normal
 *		WordPress have_posts method.
 *
 *		\par Typical Usage:
 *		The following is an example of a MobileView theme loop:
 *		\include theme-loop.php
 *
 *		\ingroup templatetags 
 */
function mobileview_have_posts() {
	return have_posts();
}
/*!		\brief Populates the post data in a MobileView theme loop.
 *
 *		This method will populate the post data structure and is meant to be used in conjunction with mobileview_have_posts().
 *
 *		\par Typical Usage:
 *		The following is an example of a MobileView theme loop:
 *		\include theme-loop.php 
 *
 *		\ingroup templatetags 
 */
function mobileview_the_post() {
	the_post();
}
/*!		\brief Echos the post content returned from mobileview_get_content()
 *
 *		This method echos the post content returned from mobileview_get_content.  This is a wrapper for the WordPress
 *		method \em get_the_content.
 *
 *		\par Typical Usage:
 *		The following is an example of a MobileView theme loop:
 *		\include theme-loop.php  
 *
 *		\ingroup templatetags 
 */
function mobileview_the_content() {
	echo apply_filters( 'the_content', mobileview_get_content() );
}
/*!		\brief Return the post content associated with a post.
 *
 *		This is a wrapper for the WordPress method \em get_the_content.
 *		This method can be filtered by using the WordPress filter \em mobileview_the_content.
 *
 *		\ingroup templatetags 
 */
function mobileview_get_content() {
	return apply_filters( 'mobileview_the_content', get_the_content() );
}
/*!		\brief Echos the post excerpt associated with a post.
 *
 *		This method echos the output from mobileview_get_excerpt().
 *
 *		\ingroup templatetags 
 */
function mobileview_the_excerpt() {
	echo mobileview_get_excerpt();	
}
/*!		\brief Returns the post excerpt associated with a post.
 *
 *		This function is a wrapper for the WordPress method get_the_excerpt().  The output from this function can be filtered using the 
 *		WordPress filter \em mobileview_excerpt.
 *
 *		\ingroup templatetags 
 */
function mobileview_get_excerpt() {
	return apply_filters( 'mobileview_excerpt', get_the_excerpt() );
}
/*!		\brief Echos a space-separated list of classes to be used for the footer.
 *
 *		This method echos a list of the recommended classes to use within the main footer block in each theme.
 *
 *		\par Typical Usage:
 *		The following is the typical usage of this function:
 *		\include mobileview-footer.php
 *
 *		\ingroup templatetags 
 */
function mobileview_footer_classes() {
	echo mobileview_get_footer_classes();
}
/*!		\brief Returns a space-separated of classes to be used for the footer.
 *
 *		This method returns a list of the recommended classes to use within the main footer block in each theme.
 *		This method can be filtered using the WordPress filter \em mobileview_footer_classes.
 *
 *		\ingroup templatetags 
 */
function mobileview_get_footer_classes() {
	$footer_classes = array( 'footer' );
	return implode( ' ', apply_filters( 'mobileview_footer_classes', $footer_classes ) );
}
/*!		\brief Echos a space-separated list of classes to be used for the main body tag.
 *
 *		This method echos a list of the recommended classes to use for the main body tag in each theme.
 *
 *		\par Typical Usage:
 *		\include body-classes.php 
 *
 *		\ingroup templatetags 
 */
function mobileview_body_classes() {
	echo mobileview_get_body_classes();
}
/*!		\brief Returns a space-separated list of classes to be used for the main body tag.
 *
 *		This method echos a list of the recommended classes to use for the main body tag in each theme.
 *		This method can be filtered using the WordPress filter \em mobileview_body_classes.
 *
 *		\par Added Classes
 *		The following classes are added automatically:
 *		\arg \c device-{devicename} - The name of the device, i.e. \em device-blackberry9500
 *		\arg \c device-class-{deviceclass} - The name of the device class, i.e. \em device-class-blackberry
 *		\arg \c skin-{skinname} - The name of the currently active skin, i.e. \em skin-oceanwave
 *		\arg \c theme-{themename} - The name of the currently active theme, i.e. \em theme-skeleton
 *		\arg \c dark-icon - If the current page icon looks best with a dark background
 *		\arg \c post-thumbnails - If post thumbnails are enabled
 *		\arg \c disqus - Added when the Disqus is installed and active
 *		\arg \c int-deb - Added when the Intense Debate plugin is installed and active 
 *
 *		\ingroup templatetags 
 */
function mobileview_get_body_classes() {
	global $mobileview;
	$settings = $mobileview->get_settings();
	$body_classes = array( 'mobileview' );
	$mobile_device = $mobileview->get_active_mobile_device();
	if ( $mobile_device ) {
		$body_classes[] = 'device-' . mobileview_make_css_friendly( $mobile_device );	
	}
	$active_device_class = $mobileview->get_active_device_class();
	if ( $active_device_class ) {
		$body_classes[] = 'device-class-' . mobileview_make_css_friendly( $active_device_class );	
	}
	$current_skin = $mobileview->get_current_theme_skin();
	if ( $current_skin ) {
		$body_classes[] = 'skin-' . mobileview_make_css_friendly( basename( $current_skin, '.css' ) );
	}	
	$body_classes[] = 'theme-' . mobileview_make_css_friendly( $mobileview->get_current_theme() );	
	if ( is_page() || mobileview_is_custom_page_template() ) {
		global $post;
		if ( mobileview_is_custom_page_template() ) {
			$page_id = mobileview_get_custom_page_template_id();
		} else {
			$page_id = $post->ID;
		}
	}
	if ( function_exists( 'dsq_comments_template' ) ) {
		$body_classes[] = 'disqus';	
	}
	if ( function_exists( 'id_comments_template' ) ) {
		$body_classes[] = 'int-deb';	
	}
	// Add a body class for post thumbnails
	if ( $settings->post_thumbnails_enabled ) {
		$body_classes[] = 'post-thumbnails';
	}	
	if (is_archive()){
		$body_classes[] = 'mobileview-archive';
	}	
	if (is_search()){
		$body_classes[] = 'mobileview-search';
	}
	//Is Archive
	return implode( ' ', apply_filters( 'mobileview_body_classes', $body_classes ) );	
}
/*!		\brief Converts a string into a representation suitable for a CSS class.
 *
 *		This method converts a string into a format that's suitable for a CSS class.
 *
 *		\ingroup templatetags 
 */
function mobileview_make_css_friendly( $name ) {
	return strtolower( str_replace( ' ', '-', $name ) );
}
/*!		\brief Echos the title for a post
 *
 *		This method echos the title for a post.  It is a wrapper for the standard WordPress \em the_title() method.
 *
 *		\par Typical Usage:
 *		This function is typically used in the following manner:
 *		\include theme-loop.php 
 *
 *		\ingroup templatetags 
 */
function mobileview_the_title() {
	echo mobileview_get_title();	
}
/*!		\brief Returns the title for a post
 *
 *		This method returns the title for a post.  It is a wrapper for the standard WordPress function \em get_the_title().  
 * 		This method can be filtered using the WordPress filter \em mobileview_the_title.
 *
 *		\ingroup templatetags 
 */
function mobileview_get_title() {
	return apply_filters( 'mobileview_the_title', get_the_title() );	
}
/*!		\brief Echos the permalink for a post
 *
 *		This method echos the permalink for a post.  It is a wrapper for the standard WordPress function \em the_permalink().
 *
 *		\ingroup templatetags 
 */
function mobileview_the_permalink() {
	echo mobileview_get_the_permalink(); 
}
/*!		\brief Returns the permalink for a post
 *
 *		This method returns the permalink for a post.  It is a wrapper for the standard WordPress get_the_permalink() method.
 *		This method can be filtered using the WordPress \em mobileview_the_permalink filter.
 *
 *		\ingroup templatetags 
 */
function mobileview_get_the_permalink() {
	return apply_filters( 'mobileview_the_permalink', get_permalink() );	
}
/*!		\brief Echos a list of space-separated classes to be used for each post
 *
 *		This method returns a list of space-separeated classes to be used for each post.  It echos the results from
 *		mobileview_get_post_classes().
 *
 *		\ingroup templatetags 
 */
function mobileview_post_classes() {
	echo implode( ' ', mobileview_get_post_classes() );	
}
/*!		\brief Returns a list of space-separated classes to be used for each post.
 *
 *		This function returns a list of space-separated classes that can be added to a post.  The output of this function can be filtered
 *		with the WordPress filter \em mobileview_post_classes.
 *
 *		\par Added Classes:
 *		The following classes are added automatically:
 *		\arg \c post-{ID} - The post ID, i.e. \em post-2
 *		\arg \c post-name-{name} - The name of the current post, i.e. \em post-name-some-cool-post
 *		\arg \c post-parent-{paremt} - The ID of the post's parent, i.e. \em post-parent-10
 *		\arg \c post-author-{author} - The post author
 *		\arg \c single - On single post pages
 *		\arg \c not-single - Not on single post pages
 *		\arg \c page - When viewing a page
 *		\arg \c not-page - When not viewing a page
 *		\arg \c ajax - When the post content was generated view Ajax
 *		\arg \c has-thumbnail - When the post has a thumbnail
 *
 *		\par Typical Usage:
 *		The following example shows how this function is typically used:
 *		\include theme-loop.php
 *
 *		\par Adding Custom Classes:
 *		The following examples show how custom post classes can be added dynamically:
 *		\include custom-post-classes.php
 *
 *		\ingroup templatetags
 */
function mobileview_get_post_classes() {
	global $post;
	$post_classes = array( 'post', 'section' );
	// Add the post ID as a class
	if ( isset( $post->ID ) ) {
		$post_classes[] = 'post-' . $post->ID;	
	}
	// Add the post title
	if ( isset( $post->post_name ) ) {
		$post_classes[] = 'post-name-' . $post->post_name;	
	}	
	// Add the post parent
	if ( isset( $post->post_parent ) && $post->post_parent ) {
		$post_classes[] = 'post-parent-' . $post->post_parent;	
	}
	// Add the post parent
	if ( isset( $post->post_author ) && $post->post_author ) {
		$post_classes[] = 'post-author-' . $post->post_author;	
	}	
	if ( is_single() ) {
		$post_classes[] = 'single';
	} else {
		$post_classes[] = 'not-single';
	}
	if ( is_page() ) {
		$post_classes[] = 'page';
	} else {
		$post_classes[] = 'not-page';
	}
	if ( mobileview_is_ajax() ) {
		$post_classes[] = 'ajax';
	}
	if ( mobileview_has_post_thumbnail() ) {
		$post_classes[] = 'has-thumbnail';
	} else {
		$post_classes[] = 'no-thumbnail';
	}
	return apply_filters( 'mobileview_post_classes', $post_classes );
}
/*!		\brief Used to determine if a post has an associated thumbnail.  
 *
 *		Used to determine if a post has an associated thumbnail image. 
 *
 *		\returns True if the post has a thumbnail, otherwise false
 *
 *		\ingroup templatetags
 */
function mobileview_has_post_thumbnail() {
	if ( function_exists( 'has_post_thumbnail' ) ) {
		$has_thumbnail = has_post_thumbnail();
		return apply_filters( 'mobileview_has_post_thumbnail', $has_thumbnail );
	} else {
		return apply_filters( 'mobileview_has_post_thumbnail', false );
	}
}
/**
 * This function echos a post thumbnail.
 *
 * @since 1.0.4
 * 
 * This function echos a post thumbnail image; it should be used in conjunction with mobileview_has_post_thumbnail().
 * 
 * @param float $param
 * @param string $size Optional. Image size. Defaults to 'small-thumbnail'.
 * 
 */
function mobileview_the_post_thumbnail( $param = false, $size = 'small-thumbnail' ) {
	echo mobileview_get_the_post_thumbnail( $param, $size );
}
/**
 * This function returns a post thumbnail.
 *
 * @since 1.0.4
 * 
 * This function returns a post thumbnail image; it should be used in conjunction with mobileview_has_post_thumbnail().
 * This method calls the WordPress function \em get_the_post_thumbnail() internally.
 * 
 * @param float $param
 * @param string $size Optional. Image size. Defaults to 'small-thumbnail'.
 * 
 */
function mobileview_get_the_post_thumbnail( $param = false, $size = 'small-thumbnail' ) {
	global $post;
	$thumbnail = false;
	if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
			$thumbnail = get_the_post_thumbnail( $post->ID, $size );
			if ( preg_match( '#src=\"(.*)\"#iU', $thumbnail, $matches ) ) {
				$thumbnail = $matches[1];
			}
		}
		return apply_filters( 'mobileview_the_post_thumbnail', $thumbnail, $param );
}
/*!		\brief Echos a list of space-separated classes to be used for the content of each post.
 *
 *		This method echos a list of space-separeated classes to be used for the content of each post.  It echos the results from
 *		mobileview_get_content_classes().
 *
 *		\ingroup templatetags
 */
function mobileview_content_classes() {
	echo implode( ' ', mobileview_get_content_classes() );
}
/*!		\brief Returns a list of space-separated classes to be used for the content of each post.
 *
 *		This method returns a list of space-separated classes to be used for the content of each post.
 *		This method can be filtered using the WordPress filter \em mobileview_content_classes.
 *
 *		\note Currently only the class 'content' is added, but additional classes will be added in future versions
 *
 *		\ingroup templatetags
 */
function mobileview_get_content_classes() {
	$content_classes = array( 'content' );
	return apply_filters( 'mobileview_content_classes', $content_classes );
}
/*!		\brief Echos a list of space-separated classes to be used for the date field of any post.
 *
 *		This function echos a list of space-separeated classes to be used for the date field of any post.  It echos the results from
 *		mobileview_get_date_classes().
 *
 *		\ingroup templatetags
 */
function mobileview_date_classes() {
	echo implode( ' ', mobileview_get_date_classes() );
}
/*!		\brief Returns an array of space-separated classes to be used for the date field of each post.
 *
 *		This method returns an array of space-separated classes to be used for the date field of each post.
 *		This method can be filtered using the WordPress filter \em mobileview_date_classes.
 *
 *		\par Added Classes:
 *		The following classes are added automatically:
 *		\arg \c date - The word 'date' is added
 *		\arg \c m-{month} - The month number is added, i.e. \em m-2 for February
 *		\arg \c y-{year} - The year is added, i.e. \em y-2010
 *		\arg \c dt-{ampm} - Am or Pm, i.e. \em dt-am or \em dt-pm
 *		\arg \c day-{day} - The day is added, i.e. \em day-12 for the 12th day of the month
 *		\arg \c dow-{dayofweek} - The day of the week is added, i.e. \em dow-0 for Sunday
 *
 *		\par Typical Usage:
 *		The following shows a typical content loop and usage of mobileview_date_classes():
 *		\include theme-loop.php
 *
 *		\ingroup templatetags
 */
function mobileview_get_date_classes() {
	$date_classes = array();
	$date_classes[] = 'date';
	$date_classes[] = 'm-' . get_the_time( 'n' );
	$date_classes[] = 'y-' . get_the_time( 'Y' );
	$date_classes[] = 'dt-' . get_the_time( 'a' );
	$date_classes[] = 'day-' . get_the_time( 'j' );
	$date_classes[] = 'dow-' . get_the_time( 'w' );
	return apply_filters( 'mobileview_date_classes', $date_classes );
}
/*!		\brief Echos the date for a post.
 *
 *		This function echos the result of mobileview_get_the_time().
 *
 *		\param format Indicates how the date field should be formatted.  Please refer to the documentation for PHP's date() function 
 *		for information about the accepted parameters.
 *
 *		\ingroup templatetags 
 */
function mobileview_the_time( $format ) {
	echo mobileview_get_the_time( $format );	
}
/*!		\brief Returns the date for a post
 *
 *		Interally this method wraps the WordPress function the_time().  
 *
 *		\param format Indicates how the date field should be formatted.  Please refer to the documentation for PHP's date() function.
 *		for information about the accepted parameters.
 *
 *		\returns The formatted date
 *
 *		\ingroup templatetags 
 */
function mobileview_get_the_time( $format ) {
	$settings = mobileview_get_settings();
	if ( $settings->respect_wordpress_date_format ) {
		$format = get_option('date_format');	
	}
	return apply_filters( 'mobileview_get_the_time', get_the_time( $format ) );
}
/*!		\brief Can be used to determine if an AJAX request is happening
 *
 *		Can be used to determine if an AJAX request is happening by checking for the HTTP_X_REQUESTED_WITH header.
 *
 *		\returns True if an AJAX request is underway, otherwise false  
 *
 *		\ingroup templatetags
 */
function mobileview_is_ajax() {
	return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) );
}
/*!		\brief Used to determine whether a post has tags
 *
 *		This function can be used to determine whether or not a post has any tags.
 *
 *		\returns True if the post has tags, false otherwise  
 *
 *		\ingroup templatetags
 */
function mobileview_has_tags() {
	if ( is_page() ) {
		return false;	
	}
	$tags = get_the_tags();
	return apply_filters( 'mobileview_has_tags', $tags );
}
/*!		\brief Used to echo a comma-separated list of tags associated with a post. 
 *
 *		This function can be used to echo a comma-separated list of tags associated with the current post.  This method should be used in conjunction with
 *		mobileview_has_tags().
 *
 *		\ingroup templatetags 
 */
function mobileview_the_tags() {
	the_tags( '',', ','' ); 
}
/*!		\brief Used to determine whether a post has categories
 *
 *		This function can be used to determine whether or not a post has any categories associated with it.
 *
 *		\returns True if the post has categories, false otherwise  
 *
 *		\ingroup templatetags
 */
function mobileview_has_categories() {
	if ( is_page() ) {
		return false;
	}
	$cats = get_the_category();
	return true;
}
/*!		\brief Used to echo a comma-separated list of categories associated with a post. 
 *
 *		This function can be used to echo a comma-separated list of categories associated with the current post.  This method should be used in conjunction with
 *		mobileview_has_categories().
 *
 *		\ingroup templatetags 
 */
function mobileview_the_categories() {
	the_category( ', ' );
}
/*!		\brief Used to determine if a page has additional content or not
 *
 *		This function can be used to determine whether or not a page has additional pages of content available. If can be used to selectively show or hide the
 *		link to additional posts. 
 *
 *		\ingroup templatetags
 */
function mobileview_has_next_posts_link() {
	ob_start();
	next_posts_link();
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;	
}
/*!		\brief Executes a non-standard template file in the current MobileView theme directory.
 *
 *		This function executes a non-standard template file in the current MobileView theme directory.  The standard template files 
 *		(index.php, header.php, etc) for WordPress will work with MobileView.  Non-standard template files can be
 *		included by using this function.  For example, mobileview_do_template( 'gliding-menu.php' ) will execute the gliding-menu.php
 *		PHP script that is located in the current MobileView theme directory.  
 *
 *		\param template_name the name of the template_file, i.e. \em my-template.php
 *
 *		\returns True if the template was successfully executed, false otherwise
 *
 *		\ingroup templatetags 
 */
function mobileview_do_template( $template_name ) {
	global $mobileview;
	$template_path = $mobileview->get_current_theme_directory() . '/' . $mobileview->get_active_device_class() . '/' . $template_name;
	$directories = array( TEMPLATEPATH );
	if ( $mobileview->is_child_theme() ) { 
		$diretories[] = STYLESHEETPATH;	
	}
	foreach( $directories as $dir ) {
		if ( file_exists( $dir . '/' . $template_name ) ) {
			include( $dir . '/' . $template_name );
			return true;	
		}
	}
	return false;
}
/*!		\brief Determines whether or not the current page has an icon that should be shown
 *
 *		Determines whether or not the current page has an icon that should be shown.  This is primarily determined by the setting \em enable_menu_icons.
 *
 *		\returns True if there is an icon that should be shown, false otherwise
 *
 *		\ingroup templatetags 
 */
function mobileview_page_has_icon() {
	$settings = mobileview_get_settings();
	return ( $settings->enable_menu_icons );	
}
/*!		\brief Used to determine if the current execution is a custom page template
 *
 *		This function can be used to determine if the current template is a custom page template.
 *
 *		\returns True if a custom page template is currently being executed, false if the template is a standard WordPress template
 *
 *		\ingroup templatetags 
 */
function mobileview_is_custom_page_template() {
	global $mobileview;
	return $mobileview->is_custom_page_template;
}
/*!		\brief Used to echo the current custom page template ID
 *
 *		This function will echo the results from mobileview_get_custom_page_template_id(). 
 *
 *		\ingroup templatetags 
 */
function mobileview_the_custom_page_template_id() {
	echo mobileview_get_custom_page_template_id();
}
/*!		\brief Used to determine the current custom page template ID
 *
 *		This function can be used to determine the current custom page template ID.  This method should be used in conjunction with mobileview_is_custom_page_template().
 *
 *		\note Custom page templates technically do not have page IDs in the WordPress sense, but they are given numerical IDs mainly for the purpose of
 *		assigning icons to them
 *
 *		\returns A number representing the custom page template ID
 *
 *		\ingroup templatetags 
 */
function mobileview_get_custom_page_template_id() {
	global $mobileview;	
	return $mobileview->custom_page_template_id;
}
/*!		\brief Used to retrieve the page icon for the current page
 *
 *		This function can be used to retrieve the page icon for the current page.   
 *
 *		\returns The icon for the current page, or false if icons are disabled
 *
 *		\ingroup templatetags 
 */
function mobileview_page_get_icon() {
	global $mobileview;
	if ( mobileview_page_has_icon() ) {
		if ( mobileview_is_custom_page_template() ) {
			$page_id = mobileview_get_custom_page_template_id();
		} else {
			$page_id = get_the_ID();
		}
		// If we're not in the loop yet, let's grab the first post and then rewind
		if ( !$page_id ) {
			if ( have_posts() ) {
				the_post();
				rewind_posts();
				$page_id = get_the_ID();	
			}	
		}
		$settings = mobileview_get_settings();
			return mobileview_get_site_menu_icon( $page_id );
	} else {
		return false;	
	}	
}
/*!		\brief Echos the page icon for the current page
 *
 *		This function can be used to echo the page icon for the current page
 *
 *		\ingroup templatetags 
 */
function mobileview_page_the_icon() {
	$icon = mobileview_page_get_icon();
	if ( $icon ) {
		echo $icon;	
	}
}
/*!		\brief Echos the mobile/desktop switch link URL for mobile theme
 *
 *		This function echos the mobile/desktop switch link URL.  It echos the result from mobileview_get_mobile_switch_link().
 *
 *		\ingroup templatetags 
 */
function mobileview_the_mobile_switch_link() {
	echo mobileview_get_mobile_switch_link();
}
/*!		\brief Retrieves the mobile/desktop switch link URL for mobile theme
 *
 *		This function can be used to retrieve the mobile/desktop switch link URL.  It can be filtered using the WordPress filter \em mobileview_mobile_switch_link.
 *
 *		\returns The URL for the desktop switch link, and respects the admin setting whether the URL re-direct is the request URI, or homepage.
 *
 *		\note Visiting this URL alters the mobile switch COOKIE and redirects back to the current page.
 *
 *		\ingroup templatetags 
 */
function mobileview_get_mobile_switch_link() {
	$settings = mobileview_get_settings();
	if ( $settings->show_switch_link ) {
		if ( $settings->home_page_redirect_address == 'same' ) {
			return apply_filters( 'mobileview_mobile_switch_link', get_bloginfo( 'url' ) . '?mobileview_switch=desktop&amp;redirect=' . urlencode( $_SERVER['REQUEST_URI'] ) );
		} else {
			return apply_filters( 'mobileview_mobile_switch_link', get_bloginfo( 'url' ) . '?mobileview_switch=desktop&amp;redirect=' . get_bloginfo( 'url' ) );
		}
	}
}
/*!		\brief Determines whether or not an unseen welcome message should be displayed.
 *
 *		This function can be used to determine whether or not a welcome message should be shown.  The welcome message can be configured in the WordPress
 *		administration panel for MobileView, and is stored in the setting \em welcome_alert.
 *
 *		\returns True if a welcome message should be shown, false otherwise
 *
 *		\ingroup templatetags 
 */
function mobileview_has_welcome_message() {
	$settings = mobileview_get_settings();
	if ( isset( $_COOKIE['mobileview_welcome'] ) && $_COOKIE['mobileview_welcome'] === '1' ) {
		// user has already seen the message
		return false;	
	} else {
		return ( isset( $settings->welcome_alert ) && strlen( $settings->welcome_alert ) );
	}
}
/*!		\brief Echos the welcome message
 *
 *		This function can be used to echo the welcome message retrieved using mobileview_get_welcome_message().
 *
 *		\note The welcome message is currently disabled work in Web-App Mode
 *
 *		\ingroup templatetags 
 */
function mobileview_the_welcome_message() {
	echo mobileview_get_welcome_message();
}
/*!		\brief Retrieves the welcome message that should be displayed upon first viewing
 *
 *		This function can be used to retrieve the welcome message to be displayed the first time the mobile theme is shown.  The output from this method can
 *		be filtered by the WordPress filter \em mobileview_welcome_message. 
 *
 *		\note The welcome message is currently disabled work in Web-App Mode
 *
 *		\returns A string representing the welcome message
 *
 *		\ingroup templatetags 
 */
function mobileview_get_welcome_message() {
	$settings = mobileview_get_settings();
	return apply_filters( 'mobileview_welcome_message', $settings->welcome_alert );
}
/*!		\brief Echos the 404 message
 *
 *		This function can be used to echo the 404 message retrieved using mobileview_get_404_message().
 *
 *		\note The 404 message is currently only shown in English
 *
 *		\ingroup templatetags 
 */
function mobileview_the_404_message() {
	echo mobileview_get_404_message();
}
/*!		\brief Retrieves the 404 message that is displayed on 404 pages
 *
 *		This function can be used to retrieve the 404 message to be displayed on 404 pages.  The output from this method can
 *		be filtered by the WordPress filter \em mobileview_404_message. 
 *
 *		\returns A string representing the 404 message
 *
 *		\ingroup templatetags 
 */
function mobileview_get_404_message() {
	$settings = mobileview_get_settings();
	return apply_filters( 'mobileview_404_message', $settings->fourohfour_message );
}
/*!		\brief Echos the footer message
 *
 *		This function can be used to echo the footer message retrieved using mobileview_get_the_footer_message(). It also wraps paragraph tags
 *		around the footer message.
 * 
 *		\ingroup templatetags 
 */
function mobileview_the_footer_message() {
	echo '<p>';
	echo mobileview_get_the_footer_message();
	echo '</p>';
}
/*!		\brief Retrieves the footer message
 *
 *		This function can be used to retrieve the footer message.  The message is currently stored in the \em footer_message setting.  The output
 *		of this function can be filtered using the WordPress filter \em mobileview_footer_message.
 * 
 *		\returns A string representing the footer message
 *
 *		\ingroup templatetags 
 */
function mobileview_get_the_footer_message() {
	$settings = mobileview_get_settings();
	return apply_filters( 'mobileview_footer_message', $settings->footer_message );
}
/*!		\brief Echos the URL that will dismiss the Welcome message 
 *
 *		This function can be used to echo the URL that will dismiss the Welcome message.   
 *
 *		\ingroup templatetags 
 */
function mobileview_the_welcome_message_dismiss_url() {
	echo mobileview_get_welcome_message_dismiss_url();
}
/*!		\brief Retrieves the URL that will dismiss the Welcome message 
 *
 *		This function can be used to retrieve the URL that will dismiss the footer message.  Visiting this link via Ajax will set a COOKIE that instructs
 *		the browser not to show the Welcome message again.
 * 
 *		\returns A string representing the Welcome message dismissal URL
 *
 *		\ingroup templatetags 
 */
function mobileview_get_welcome_message_dismiss_url() {
	return apply_filters( 'mobileview_welcome_message_dismiss_url', get_bloginfo( 'url' ) . '?mobileview=dismiss_welcome&amp;redirect=' . $_SERVER['REQUEST_URI'] );
}
/*!		\brief Echos the comment/pingback/trackback count for the current post
 *
 *		This function can be used to echo the comment/pingback/trackback count for the current post.
 *
 *		\ingroup templatetags 
 */
function mobileview_the_comment_count() {
	echo mobileview_get_comment_count();	
}
/*!		\brief Retrieves the comment/pingback/trackback count for the current post
 *
 *		This function can be used to determine the comment/pingback/trackback count for the current post.
 * 
 *		\returns The comment/pingback/trackback count, or 0 if not comments/pingbacks/trackbacks.
 *
 *		\ingroup templatetags 
 */
function mobileview_get_comment_count() {
	global $wpdb;
	global $post;
	$sql = $wpdb->prepare( "SELECT count(*) AS c FROM {$wpdb->comments} WHERE comment_approved = 1 AND comment_post_ID = %d", $post->ID );
	$result = $wpdb->get_row( $sql );
	if ( $result ) {
		return $result->c;
	} else {
		return 0;	
	}	
}
/*!		\brief Echos an ordered category list
 *
 *		This function can be used to echo an ordered category list.  This function is used internally in the category listings in the theme header/popover sections. $num passed to the function determines the minimum number of posts a category must have to be shown.
 *
 *		\ingroup templatetags 
 */
function mobileview_ordered_cat_list( $num ) {
	global $wpdb;
	$settings = mobileview_get_settings();
	if (  $settings->mobileview_excluded_categories != 0 ) {
		$excluded_cats =  $settings->mobileview_excluded_categories;
	} else {
		$excluded_cats = 0;	
	}
	echo '<ul>';
	$sql = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}term_taxonomy INNER JOIN {$wpdb->prefix}terms ON {$wpdb->prefix}term_taxonomy.term_id = {$wpdb->prefix}terms.term_id WHERE taxonomy = 'category' AND {$wpdb->prefix}term_taxonomy.term_id NOT IN ($excluded_cats) AND count >= 1 ORDER BY count DESC LIMIT 0, $num");
	if ( $sql ) {
		foreach ( $sql as $result ) {
			if ( $result ) {
				echo "<li><a href=\"" . get_category_link( $result->term_id ) . "\">" . $result->name . " <span>(" . $result->count . ")</span></a></li>";			
			}
		}
	}
	echo '</ul>';
}
/*!		\brief Echos an ordered tag list
 *
 *		This function can be used to echo an ordered tag list.  This function is used internally in the tag listings in the theme header/popover sections. $num passed to the function determines the minimum number of posts a tag must have to be shown.
 *
 *		\ingroup templatetags 
 */
function mobileview_ordered_tag_list( $num ) {
	global $wpdb;
	$settings = mobileview_get_settings();
	if (  $settings->mobileview_excluded_tags != 0 ) {
		$excluded_tags =  $settings->mobileview_excluded_tags;
	} else {
		$excluded_tags = 0;	
	}
	echo '<ul>';
	$sql = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}term_taxonomy INNER JOIN {$wpdb->prefix}terms ON {$wpdb->prefix}term_taxonomy.term_id = {$wpdb->prefix}terms.term_id WHERE taxonomy = 'post_tag' AND {$wpdb->prefix}term_taxonomy.term_id NOT IN ($excluded_tags) AND count >= 1 ORDER BY count DESC LIMIT 0, $num");	
	if ( $sql ) {
		foreach ( $sql as $result ) {
			if ( $result ) {
				echo "<li><a href=\"" . get_tag_link( $result->term_id ) . "\">" . $result->name . " <span>(" . $result->count . ")</span></a></li>";			
			}
		}
	}
	echo '</ul>';
}
/*!		\brief Echos the URL for the currently displayed page
 *
 *		This function can be used to echo the URL for the currently displayed page.
 *
 *		\ingroup templatetags 
 */
function mobileview_the_current_page_url() {
	echo mobileview_get_current_page_url();
}
/*!		\brief Retrieves the URL for the currently displayed page
 *
 *		This function can be used to retrieve the URL for the currently displayed page.
 *
 *		\returns The current page URL
 *
 *		\ingroup templatetags 
 */
function mobileview_get_current_page_url() {
	return apply_filters( 'mobileview_current_page_url', $_SERVER['REQUEST_URI'] );	
}
function mobileview_get_site_menu_icon( $icon_type ) {
		global $mobileview;
		$settings = $mobileview->get_settings();
		if ( isset( $settings->menu_icons[ $icon_type ] ) ) {
			$icon = colabsplugin_mobileview_sslize( WP_CONTENT_URL . $settings->menu_icons[ $icon_type ] );
		} else {
			$site_icons = $mobileview->get_site_icons();
			if ( $site_icons && isset( $site_icons[ $icon_type ] ) ) {	
				$icon = $site_icons[ $icon_type ]->url;
			} else {		
				if ( isset( $settings->menu_icons[ MOBILEVIEW_ICON_DEFAULT ] ) ) {
					$icon = colabsplugin_mobileview_sslize( WP_CONTENT_URL . $settings->menu_icons[ MOBILEVIEW_ICON_DEFAULT ] );
				} else {
					$icon = $site_icons[ MOBILEVIEW_ICON_DEFAULT ]->url;				
				}
			}
		}	
		return $icon;
	}
	function show_mobileview_message_in_footer() {
		$settings = mobileview_get_settings();
    if ( $settings->show_mobileview_in_footer ) {		
        if(!function_exists('mobileview_the_theme_title')) require_once( MOBILEVIEW_DIR . '/admin/themes.php' );
				echo '<p>'.sprintf(
        __( "Copyright &copy; 2013 %1\$s by %2\$s.", "mobileviewlang" ),
        mobileview_the_theme_title(),
        '<a href="http://colorlabsproject.com/" target="_blank">ColorLabs & Company</a>' )
        .' '. sprintf( __("Powered by %1\$s ", "mobileviewlang"), '<a href="http://wordpress.org/extend/plugins/mobileview/" target="_blank">MobileView</a>' ) . __( "All Rights Reserved.", "mobileviewlang" ) . '</p>';
    }    
	}


/*-----------------------------------------------------------------------------------*/
/* mobileview_breadcrumbs() - Custom breadcrumb generator function  */
/*
/* Params:
/*
/* Arguments Array:
/*
/* 'separator' 			- The character to display between the breadcrumbs.
/* 'before' 			- HTML to display before the breadcrumbs.
/* 'after' 				- HTML to display after the breadcrumbs.
/* 'front_page' 		- Include the front page at the beginning of the breadcrumbs.
/* 'show_home' 			- If $show_home is set and we're not on the front page of the site, link to the home page.
/* 'echo' 				- Specify whether or not to echo the breadcrumbs. Alternative is "return".
/*
/*-----------------------------------------------------------------------------------*/
/**
 * The code below is inspired by Justin Tadlock's Hybrid Core.
 *
 * mobileview_breadcrumbs() shows a breadcrumb for all types of pages.  Themes and plugins can filter $args or input directly.  
 * Allow filtering of only the $args using get_the_breadcrumb_args.
 *
 * @since 3.7.0
 * @param array $args Mixed arguments for the menu.
 * @return string Output of the breadcrumb menu.
 */
function mobileview_breadcrumbs( $args = array() ) {
	global $wp_query, $wp_rewrite;

	/* Get the textdomain. */
	$textdomain = 'mobileviewlang';

	/* Create an empty variable for the breadcrumb. */
	$breadcrumb = '';

	/* Create an empty array for the trail. */
	$trail = array();
	$path = '';

	/* Set up the default arguments for the breadcrumb. */
	$defaults = array(
		'separator' => '&gt;',
		'before' => '<span class="breadcrumb-title">' . __( 'You are here:', $textdomain ) . '</span>',
		'after' => false,
		'front_page' => true,
		'show_home' => __( 'Home', $textdomain ),
		'echo' => true, 
		'show_posts_page' => true
	);

	/* Allow singular post views to have a taxonomy's terms prefixing the trail. */
	if ( is_singular() )
		$defaults["singular_{$wp_query->post->post_type}_taxonomy"] = false;

	/* Apply filters to the arguments. */
	$args = apply_filters( 'mobileview_breadcrumbs_args', $args );

	/* Parse the arguments and extract them for easy variable naming. */
	extract( wp_parse_args( $args, $defaults ) );

	/* If $show_home is set and we're not on the front page of the site, link to the home page. */
	if ( !is_front_page() && $show_home )
		$trail[] = '<a href="' . home_url() . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . $show_home . '</a>';

	/* If viewing the front page of the site. */
	if ( is_front_page() ) {
		if ( !$front_page )
			$trail = false;
		elseif ( $show_home )
			$trail['trail_end'] = "{$show_home}";
	}

	/* If viewing the "home"/posts page. */
	elseif ( is_home() ) {
		$home_page = get_page( $wp_query->get_queried_object_id() );
		$trail = array_merge( $trail, mobileview_breadcrumbs_get_parents( $home_page->post_parent, '' ) );
		$trail['trail_end'] = get_the_title( $home_page->ID );
	}

	/* If viewing a singular post (page, attachment, etc.). */
	elseif ( is_singular() ) {

		/* Get singular post variables needed. */
		$post = $wp_query->get_queried_object();
		$post_id = absint( $wp_query->get_queried_object_id() );
		$post_type = $post->post_type;
		$parent = $post->post_parent;

		/* If a custom post type, check if there are any pages in its hierarchy based on the slug. */
		if ( 'page' !== $post_type && 'post' !== $post_type ) {

			$post_type_object = get_post_type_object( $post_type );

			/* If $front has been set, add it to the $path. */
			if ( 'post' == $post_type || 'attachment' == $post_type || ( $post_type_object->rewrite['with_front'] && $wp_rewrite->front ) )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If there's a slug, add it to the $path. */
			if ( !empty( $post_type_object->rewrite['slug'] ) )
				$path .= $post_type_object->rewrite['slug'];

			/* If there's a path, check for parents. */
			if ( !empty( $path ) && '/' != $path )
				$trail = array_merge( $trail, mobileview_breadcrumbs_get_parents( '', $path ) );

			/* If there's an archive page, add it to the trail. */
			if ( !empty( $post_type_object->has_archive ) && function_exists( 'get_post_type_archive_link' ) )
				$trail[] = '<a href="' . get_post_type_archive_link( $post_type ) . '" title="' . esc_attr( $post_type_object->labels->name ) . '">' . $post_type_object->labels->name . '</a>';
		}

		/* If the post type path returns nothing and there is a parent, get its parents. */
		if ( empty( $path ) && 0 !== $parent || 'attachment' == $post_type )
			$trail = array_merge( $trail, mobileview_breadcrumbs_get_parents( $parent, '' ) );

		/* Toggle the display of the posts page on single blog posts. */		
		if ( 'post' == $post_type && $show_posts_page == true && 'page' == get_option( 'show_on_front' ) ) {
			$posts_page = get_option( 'page_for_posts' );
			if ( $posts_page != '' && is_numeric( $posts_page ) ) {
				$trail = array_merge( $trail, mobileview_breadcrumbs_get_parents( $posts_page, '' ) );
			}
		}

		/* Display terms for specific post type taxonomy if requested. */
		if ( isset( $args["singular_{$post_type}_taxonomy"] ) && $terms = get_the_term_list( $post_id, $args["singular_{$post_type}_taxonomy"], '', ', ', '' ) )
			$trail[] = $terms;

		/* End with the post title. */
		$post_title = get_the_title( $post_id ); // Force the post_id to make sure we get the correct page title.
		if ( !empty( $post_title ) )
			$trail['trail_end'] = $post_title;
	}

	/* If we're viewing any type of archive. */
	elseif ( is_archive() ) {

		/* If viewing a taxonomy term archive. */
		if ( is_tax() || is_category() || is_tag() ) {

			/* Get some taxonomy and term variables. */
			$term = $wp_query->get_queried_object();
			$taxonomy = get_taxonomy( $term->taxonomy );

			/* Get the path to the term archive. Use this to determine if a page is present with it. */
			if ( is_category() )
				$path = get_option( 'category_base' );
			elseif ( is_tag() )
				$path = get_option( 'tag_base' );
			else {
				if ( $taxonomy->rewrite['with_front'] && $wp_rewrite->front )
					$path = trailingslashit( $wp_rewrite->front );
				$path .= $taxonomy->rewrite['slug'];
			}

			/* Get parent pages by path if they exist. */
			if ( $path )
				$trail = array_merge( $trail, mobileview_breadcrumbs_get_parents( '', $path ) );

			/* If the taxonomy is hierarchical, list its parent terms. */
			if ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent )
				$trail = array_merge( $trail, mobileview_breadcrumbs_get_term_parents( $term->parent, $term->taxonomy ) );

			/* Add the term name to the trail end. */
			$trail['trail_end'] = $term->name;
		}

		/* If viewing a post type archive. */
		elseif ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() ) {

			/* Get the post type object. */
			$post_type_object = get_post_type_object( get_query_var( 'post_type' ) );

			/* If $front has been set, add it to the $path. */
			if ( $post_type_object->rewrite['with_front'] && $wp_rewrite->front )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If there's a slug, add it to the $path. */
			if ( !empty( $post_type_object->rewrite['archive'] ) )
				$path .= $post_type_object->rewrite['archive'];

			/* If there's a path, check for parents. */
			if ( !empty( $path ) )
				$trail = array_merge( $trail, mobileview_breadcrumbs_get_parents( '', $path ) );

			/* Add the post type [plural] name to the trail end. */
			$trail['trail_end'] = $post_type_object->labels->name;
		}

		/* If viewing an author archive. */
		elseif ( is_author() ) {

			/* If $front has been set, add it to $path. */
			if ( !empty( $wp_rewrite->front ) )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If an $author_base exists, add it to $path. */
			if ( !empty( $wp_rewrite->author_base ) )
				$path .= $wp_rewrite->author_base;

			/* If $path exists, check for parent pages. */
			if ( !empty( $path ) )
				$trail = array_merge( $trail, mobileview_breadcrumbs_get_parents( '', $path ) );

			/* Add the author's display name to the trail end. */
			$trail['trail_end'] = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
		}

		/* If viewing a time-based archive. */
		elseif ( is_time() ) {

			if ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
				$trail['trail_end'] = get_the_time( __( 'g:i a', $textdomain ) );

			elseif ( get_query_var( 'minute' ) )
				$trail['trail_end'] = sprintf( __( 'Minute %1$s', $textdomain ), get_the_time( __( 'i', $textdomain ) ) );

			elseif ( get_query_var( 'hour' ) )
				$trail['trail_end'] = get_the_time( __( 'g a', $textdomain ) );
		}

		/* If viewing a date-based archive. */
		elseif ( is_date() ) {

			/* If $front has been set, check for parent pages. */
			if ( $wp_rewrite->front )
				$trail = array_merge( $trail, mobileview_breadcrumbs_get_parents( '', $wp_rewrite->front ) );

			if ( is_day() ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', $textdomain ) ) . '">' . get_the_time( __( 'Y', $textdomain ) ) . '</a>';
				$trail[] = '<a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '" title="' . get_the_time( esc_attr__( 'F', $textdomain ) ) . '">' . get_the_time( __( 'F', $textdomain ) ) . '</a>';
				$trail['trail_end'] = get_the_time( __( 'j', $textdomain ) );
			}

			elseif ( get_query_var( 'w' ) ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', $textdomain ) ) . '">' . get_the_time( __( 'Y', $textdomain ) ) . '</a>';
				$trail['trail_end'] = sprintf( __( 'Week %1$s', $textdomain ), get_the_time( esc_attr__( 'W', $textdomain ) ) );
			}

			elseif ( is_month() ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', $textdomain ) ) . '">' . get_the_time( __( 'Y', $textdomain ) ) . '</a>';
				$trail['trail_end'] = get_the_time( __( 'F', $textdomain ) );
			}

			elseif ( is_year() ) {
				$trail['trail_end'] = get_the_time( __( 'Y', $textdomain ) );
			}
		}
	}

	/* If viewing search results. */
	elseif ( is_search() )
		$trail['trail_end'] = sprintf( __( 'Search results for &quot;%1$s&quot;', $textdomain ), esc_attr( get_search_query() ) );

	/* If viewing a 404 error page. */
	elseif ( is_404() )
		$trail['trail_end'] = __( '404 Not Found', $textdomain );

	/* Allow child themes/plugins to filter the trail array. */
	$trail = apply_filters( 'mobileview_breadcrumbs_trail', $trail, $args );

	/* Connect the breadcrumb trail if there are items in the trail. */
	if ( is_array( $trail ) ) {

		/* Open the breadcrumb trail containers. */
		$breadcrumb = '<div class="breadcrumb breadcrumbs colabs-breadcrumbs"><div class="breadcrumb-trail">';

		/* If $before was set, wrap it in a container. */
		if ( !empty( $before ) )
			$breadcrumb .= '<span class="trail-before">' . $before . '</span> ';

		/* Wrap the $trail['trail_end'] value in a container. */
		if ( !empty( $trail['trail_end'] ) )
			$trail['trail_end'] = '<span class="trail-end">' . $trail['trail_end'] . '</span>';

		/* Format the separator. */
		if ( !empty( $separator ) )
			$separator = '<span class="sep">' . $separator . '</span>';

		/* Join the individual trail items into a single string. */
		$breadcrumb .= join( " {$separator} ", $trail );

		/* If $after was set, wrap it in a container. */
		if ( !empty( $after ) )
			$breadcrumb .= ' <span class="trail-after">' . $after . '</span>';

		/* Close the breadcrumb trail containers. */
		$breadcrumb .= '</div></div>';
	}

	/* Allow developers to filter the breadcrumb trail HTML. */
	$breadcrumb = apply_filters( 'mobileview_breadcrumbs', $breadcrumb );

	/* Output the breadcrumb. */
	if ( $echo )
		echo $breadcrumb;
	else
		return $breadcrumb;


} // End mobileview_breadcrumbs()

/*-----------------------------------------------------------------------------------*/
/* mobileview_breadcrumbs_get_parents() - Retrieve the parents of the current page/post */
/*-----------------------------------------------------------------------------------*/
/**
 * Gets parent pages of any post type or taxonomy by the ID or Path.  The goal of this function is to create 
 * a clear path back to home given what would normally be a "ghost" directory.  If any page matches the given 
 * path, it'll be added.  But, it's also just a way to check for a hierarchy with hierarchical post types.
 *
 * @since 3.7.0
 * @param int $post_id ID of the post whose parents we want.
 * @param string $path Path of a potential parent page.
 * @return array $trail Array of parent page links.
 */
function mobileview_breadcrumbs_get_parents( $post_id = '', $path = '' ) {

	/* Set up an empty trail array. */
	$trail = array();

	/* If neither a post ID nor path set, return an empty array. */
	if ( empty( $post_id ) && empty( $path ) )
		return $trail;

	/* If the post ID is empty, use the path to get the ID. */
	if ( empty( $post_id ) ) {

		/* Get parent post by the path. */
		$parent_page = get_page_by_path( $path );

		/* ********************************************************************
		Modification: The above line won't get the parent page if
		the post type slug or parent page path is not the full path as required
		by get_page_by_path. By using get_page_with_title, the full parent
		trail can be obtained. This may still be buggy for page names that use
		characters or long concatenated names.
		******************************************************************* */

		if( empty( $parent_page ) )
		        // search on page name (single word)
			$parent_page = get_page_by_title ( $path );

		if( empty( $parent_page ) )
			// search on page title (multiple words)
			$parent_page = get_page_by_title ( str_replace( array('-', '_'), ' ', $path ) );

		/* End Modification */

		/* If a parent post is found, set the $post_id variable to it. */
		if ( !empty( $parent_page ) )
			$post_id = $parent_page->ID;
	}

	/* If a post ID and path is set, search for a post by the given path. */
	if ( $post_id == 0 && !empty( $path ) ) {

		/* Separate post names into separate paths by '/'. */
		$path = trim( $path, '/' );
		preg_match_all( "/\/.*?\z/", $path, $matches );

		/* If matches are found for the path. */
		if ( isset( $matches ) ) {

			/* Reverse the array of matches to search for posts in the proper order. */
			$matches = array_reverse( $matches );

			/* Loop through each of the path matches. */
			foreach ( $matches as $match ) {

				/* If a match is found. */
				if ( isset( $match[0] ) ) {

					/* Get the parent post by the given path. */
					$path = str_replace( $match[0], '', $path );
					$parent_page = get_page_by_path( trim( $path, '/' ) );

					/* If a parent post is found, set the $post_id and break out of the loop. */
					if ( !empty( $parent_page ) && $parent_page->ID > 0 ) {
						$post_id = $parent_page->ID;
						break;
					}
				}
			}
		}
	}

	/* While there's a post ID, add the post link to the $parents array. */
	while ( $post_id ) {

		/* Get the post by ID. */
		$page = get_page( $post_id );

		/* Add the formatted post link to the array of parents. */
		$parents[]  = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( get_the_title( $post_id ) ) . '">' . get_the_title( $post_id ) . '</a>';

		/* Set the parent post's parent to the post ID. */
		$post_id = $page->post_parent;
	}

	/* If we have parent posts, reverse the array to put them in the proper order for the trail. */
	if ( isset( $parents ) )
		$trail = array_reverse( $parents );

	/* Return the trail of parent posts. */
	return $trail;

} // End mobileview_breadcrumbs_get_parents()

/*-----------------------------------------------------------------------------------*/
/* mobileview_breadcrumbs_get_term_parents() - Retrieve the parents of the current term */
/*-----------------------------------------------------------------------------------*/
/**
 * Searches for term parents of hierarchical taxonomies.  This function is similar to the WordPress 
 * function get_category_parents() but handles any type of taxonomy.
 *
 * @since 3.7.0
 * @param int $parent_id The ID of the first parent.
 * @param object|string $taxonomy The taxonomy of the term whose parents we want.
 * @return array $trail Array of links to parent terms.
 */
function mobileview_breadcrumbs_get_term_parents( $parent_id = '', $taxonomy = '' ) {

	/* Set up some default arrays. */
	$trail = array();
	$parents = array();

	/* If no term parent ID or taxonomy is given, return an empty array. */
	if ( empty( $parent_id ) || empty( $taxonomy ) )
		return $trail;

	/* While there is a parent ID, add the parent term link to the $parents array. */
	while ( $parent_id ) {

		/* Get the parent term. */
		$parent = get_term( $parent_id, $taxonomy );

		/* Add the formatted term link to the array of parent terms. */
		$parents[] = '<a href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( $parent->name ) . '">' . $parent->name . '</a>';

		/* Set the parent term's parent as the parent ID. */
		$parent_id = $parent->parent;
	}

	/* If we have parent terms, reverse the array to put them in the proper order for the trail. */
	if ( !empty( $parents ) )
		$trail = array_reverse( $parents );

	/* Return the trail of parent terms. */
	return $trail;
	
} // End mobileview_breadcrumbs_get_term_parents()

/*-----------------------------------------------------------------------------------*/
/*  mobileview_share - Twitter, FB & Google +1    */
/*-----------------------------------------------------------------------------------*/
if ( !function_exists( 'mobileview_share' ) ) {
function mobileview_share() {
    
$return = '';
$image = '';

$settings = mobileview_get_settings();

$mobileview_share_twitter = $settings->mobileview_share_twitter;
$mobileview_share_fblike = $settings->mobileview_share_fblike;
$mobileview_share_google_plusone = $settings->mobileview_share_google_plusone;

$mobileview_twitter_username= $settings->mobileview_twitter_username;

$mobileview_share_pinterest = $settings->mobileview_share_pinterest;
$mobileview_share_linkedin = $settings->mobileview_share_linkedin;

    //Share Button Functions 
    $url = get_permalink();
    $share = '';
    
    //Twitter Share Button
    if(function_exists('mobileview_shortcode_twitter') && $mobileview_share_twitter == "on"){
        $tweet_args = array(  'url' => $url,
   							'style' => 'horizontal',
   							'source' => ( $mobileview_twitter_username )? $mobileview_twitter_username : '',
   							'text' => '',
   							'related' => '',
   							'lang' => '',
   							'float' => 'left'
                        );

        $share .= mobileview_shortcode_twitter($tweet_args);
    }
    
   
        
    //Google +1 Share Button
    if( function_exists('mobileview_shortcode_google_plusone') && $mobileview_share_google_plusone == "on"){
        $google_args = array(
						'size' => 'medium',
						'language' => '',
						'count' => '',
						'href' => $url,
						'callback' => '',
						'float' => 'left'
					);        

        $share .= mobileview_shortcode_google_plusone($google_args);       
    }
	
	 //Facebook Like Button
    if(function_exists('mobileview_shortcode_fblike') && $mobileview_share_fblike == "on"){
    $fblike_args = 
    array(	
        'float' => 'left',
        'url' => '',
        'style' => 'button_count',
        'showfaces' => 'false',
        'width' => '85',
        'height' => '',
        'verb' => 'like',
        'colorscheme' => 'light',
        'font' => 'arial'
        );
        $share .= mobileview_shortcode_fblike($fblike_args);    
    }
    
    if (is_attachment()){
        $att_image = wp_get_attachment_image_src( $post->id, "thumbnail");
        $image = $att_image[0];
    }
    
    //Pinterest Share Button
    if( function_exists('mobileview_shortcode_pinterest') && $mobileview_share_pinterest == "on" ){
    $pinterest_args = array(
    		'count' => 'horizontal',
    		'float' => 'left',  
    		'use_post' => 'true',
    		'image_url' => $image,
    		'url' => $url
    	);        
    $share .= mobileview_shortcode_pinterest($pinterest_args);       
    }
    
    //Linked Share Button
    if( function_exists('mobileview_shortcode_linkedin_share') && $mobileview_share_linkedin == "on" ){
    $linkedin_args = array(
    		'url' 	=> $url,
    		'style' => 'right', 
    		'float' => 'left'
    	);        
    $share .= mobileview_shortcode_linkedin_share($linkedin_args);       
    }
    
    $return .= '<div class="social_share clearfix">'.$share.'</div><div class="clear"></div>';
    
    return $return;
}
}

/**
 * mobileview_pagination() is used for paginating the various archive pages created by WordPress. This is not
 * to be used on single.php or other single view pages.
 *
 * @since 3.7.0
 * @uses paginate_links() Creates a string of paginated links based on the arguments given.
 * @param array $args Arguments to customize how the page links are output.
 * @param object $query An optional custom query to paginate.
 */

if ( ! function_exists( 'mobileview_pagination' ) ) {

	function mobileview_pagination( $args = array(), $query = '' ) {
		global $wp_rewrite, $wp_query;
		
		do_action( 'mobileview_pagination_start' );
		
		if ( $query ) {
		
			$wp_query = $query;
		
		} // End IF Statement
	
		/* If there's not more than one page, return nothing. */
		if ( 1 >= $wp_query->max_num_pages )
			return;
	
		/* Get the current page. */
		$current = ( get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1 );
	
		/* Get the max number of pages. */
		$max_num_pages = intval( $wp_query->max_num_pages );
	
		/* Set up some default arguments for the paginate_links() function. */
		$defaults = array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'total' => $max_num_pages,
			'current' => $current,
			'prev_next' => true,
			'prev_text' => __( '&laquo; Previous', 'mobileviewlang' ), // Translate in WordPress. This is the default.
			'next_text' => __( 'Next &raquo;', 'mobileviewlang' ), // Translate in WordPress. This is the default.
			'show_all' => false,
			'end_size' => 1,
			'mid_size' => 1,
			'add_fragment' => '',
			'type' => 'plain',
			'before' => '<div class="pagination colabs-pagination">', // Begin mobileview_pagination() arguments.
			'after' => '</div>',
			'echo' => true,
		);
	
		/* Add the $base argument to the array if the user is using permalinks. */
		if( $wp_rewrite->using_permalinks() )
			$defaults['base'] = user_trailingslashit( trailingslashit( get_pagenum_link() ) . 'page/%#%' );
	
		/* If we're on a search results page, we need to change this up a bit. */
		if ( is_search() ) {
		/* If we're in BuddyPress, use the default "unpretty" URL structure. */
			if ( class_exists( 'BP_Core_User' ) ) {
				
				$search_query = get_query_var( 's' );
				$paged = get_query_var( 'paged' );
				
				$base = user_trailingslashit( home_url() ) . '?s=' . $search_query . '&paged=%#%';
				
				$defaults['base'] = $base;
			} else {
				$search_permastruct = $wp_rewrite->get_search_permastruct();
				if ( !empty( $search_permastruct ) )
					$defaults['base'] = user_trailingslashit( trailingslashit( get_search_link() ) . 'page/%#%' );
			}
		}
	
		/* Merge the arguments input with the defaults. */
		$args = wp_parse_args( $args, $defaults );
	
		/* Allow developers to overwrite the arguments with a filter. */
		$args = apply_filters( 'mobileview_pagination_args', $args );
	
		/* Don't allow the user to set this to an array. */
		if ( 'array' == $args['type'] )
			$args['type'] = 'plain';
		
		/* Make sure raw querystrings are displayed at the end of the URL, if using pretty permalinks. */
		$pattern = '/\?(.*?)\//i';
		
		preg_match( $pattern, $args['base'], $raw_querystring );
		
		if( $wp_rewrite->using_permalinks() && $raw_querystring ){
			if(isset($raw_querystring[0])){
			$rawquerystring = str_replace( '', '', $raw_querystring[0] );
			$args['base'] = str_replace( $rawquerystring, '', $args['base'] );
			$args['base'] .= substr( $rawquerystring, 0, -1 );
			}
		}
		/* Get the paginated links. */
		$page_links = paginate_links( $args );
	
		/* Remove 'page/1' from the entire output since it's not needed. */
		$page_links = str_replace( array( '&#038;paged=1\'', '/page/1\'' ), '\'', $page_links );
	
		/* Wrap the paginated links with the $before and $after elements. */
		$page_links = $args['before'] . $page_links . $args['after'];
	
		/* Allow devs to completely overwrite the output. */
		$page_links = apply_filters( 'mobileview_pagination', $page_links );
	
		do_action( 'mobileview_pagination_end' );
		
		/* Return the paginated links for use in themes. */
		if ( $args['echo'] )
			echo $page_links;
		else
			return $page_links;
			
	} // End mobileview_pagination()

} // End IF Statement


function mobileview_is_custom_latest_posts_page() {
	global $post;
	
	$settings = mobileview_get_settings();	
	
	if ( $settings->mobileview_latest_posts_page == 'none' ) {
		return false;	
	} else {		
		rewind_posts();
		the_post();
		rewind_posts();
		
		return apply_filters( 'mobileview_is_custom_latest_posts_page', ( $settings->mobileview_latest_posts_page == $post->ID ) );
	}
}


function mobileview_custom_latest_posts_query() {
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args = array(
		'paged' => $paged,
		'posts_per_page' => intval( get_option( 'posts_per_page') )
	);
	
	query_posts( $args ); 	
}