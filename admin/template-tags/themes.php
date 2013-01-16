<?php

global $wpmobi_themes;
global $wpmobi_cur_theme;

$wpmobi_themes = false;
$wpmobi_cur_theme = false;

global $wpmobi_theme_item;	
global $wpmobi_theme_iterator;

$wpmobi_theme_item = $wpmobi_theme_iterator = false;

function wpmobi_rewind_themes() {
	global $wpmobi_themes;
	$wpmobi_themes = false;
}


function wpmobi_has_themes() {
	global $wpmobi;
	global $wpmobi_theme_iterator;
	
	if ( !$wpmobi_theme_iterator ) {	
		$wpmobi_themes = $wpmobi->get_available_themes();
		$wpmobi_theme_iterator = new WPMobiArrayIterator( $wpmobi_themes ); 
	} 
	
	return $wpmobi_theme_iterator->have_items();
}

function wpmobi_the_theme() {
	global $wpmobi_theme_iterator;
	global $wpmobi_cur_theme;
	
	$wpmobi_cur_theme = $wpmobi_theme_iterator->the_item();
	
	return apply_filters( 'wpmobi_theme', $wpmobi_cur_theme );
}

function wpmobi_the_theme_classes( $extra_classes = array() ) {
	echo implode( ' ', wpmobi_get_theme_classes( $extra_classes ) ) ;	
}

function wpmobi_get_theme_classes( $extra_classes = array() ) {
	$classes = explode( ' ', $extra_classes );
		
	if ( wpmobi_is_theme_active() ) {
		$classes[] = 'active';
	}
	
	if ( wpmobi_is_theme_custom() ) {
		$classes[] = 'custom';	
	}
	
	if ( wpmobi_has_theme_tags() ) {
		$tags = wpmobi_get_theme_tags();
		foreach( $tags as $tag ) {
			$classes[] = $tag;
		}		
	}
	
	return $classes;
}

function wpmobi_has_theme_tags() {
	global $wpmobi_cur_theme;
	
	return ( isset( $wpmobi_cur_theme->tags ) && count( $wpmobi_cur_theme->tags ) );	
}

function wpmobi_get_theme_tags() {
	global $wpmobi_cur_theme;
	
	return apply_filters( 'wpmobi_theme_tags', $wpmobi_cur_theme->tags );	
}

function wpmobi_is_theme_active() {
	global $wpmobi;
	global $wpmobi_cur_theme;
	
	$settings = $wpmobi->get_settings();
		
	$current_theme_location = $settings->current_theme_location . '/' . $settings->current_theme_name;
	
	return ( $wpmobi_cur_theme->location == $current_theme_location );
}

function wpmobi_active_theme_has_settings() {
	$menu = apply_filters( 'wpmobi_theme_menu', array() );
	return count( $menu );	
}

function wpmobi_is_theme_custom() {
	global $wpmobi_cur_theme;
	return ( $wpmobi_cur_theme->custom_theme );	
}

function wpmobi_is_theme_child() {
	global $wpmobi_cur_theme;
	return ( isset( $wpmobi_cur_theme->parent_theme ) && strlen( $wpmobi_cur_theme->parent_theme ) );	
}

function wpmobi_the_theme_version() {
	echo wpmobi_get_theme_version();
}	

function wpmobi_get_theme_version() {
	global $wpmobi_cur_theme;
	if ( $wpmobi_cur_theme ) {
		return apply_filters( 'wpmobi_theme_version', $wpmobi_cur_theme->version );
	}
	
	return false;		
}


function wpmobi_the_theme_title() {
	echo wpmobi_get_theme_title();	
}

function wpmobi_get_theme_title() {
	global $wpmobi_cur_theme;
	if ( $wpmobi_cur_theme ) {
		return apply_filters( 'wpmobi_theme_title', $wpmobi_cur_theme->name );
	}
	
	return false;		
}

function wpmobi_the_theme_location() {
	echo wpmobi_get_theme_location();	
}

function wpmobi_get_theme_location() {
	global $wpmobi_cur_theme;
	if ( $wpmobi_cur_theme ) {
		return apply_filters( 'wpmobi_theme_location', $wpmobi_cur_theme->location );
	}
	
	return false;		
}

function wpmobi_the_theme_features() {
	echo implode( wpmobi_get_theme_features(), ', ' );	
}

function wpmobi_get_theme_features() {
	global $wpmobi_cur_theme;
	return apply_filters( 'wpmobi_theme_features', $wpmobi_cur_theme->features );	
}

function wpmobi_theme_has_features() {
	global $wpmobi_cur_theme;
	return $wpmobi_cur_theme->features;		
}

function wpmobi_the_theme_author() {
	echo wpmobi_get_theme_author();	
}

function wpmobi_get_theme_author() {
	global $wpmobi_cur_theme;
	if ( $wpmobi_cur_theme ) {
		return apply_filters( 'wpmobi_theme_author', $wpmobi_cur_theme->author );
	}
	
	return false;		
}

function wpmobi_the_theme_description() {
	echo wpmobi_get_theme_description();	
}

function wpmobi_get_theme_description() {
	global $wpmobi_cur_theme;
	if ( $wpmobi_cur_theme ) {
		return apply_filters( 'wpmobi_theme_description', $wpmobi_cur_theme->description );
	}
	
	return false;		
}

function wpmobi_the_theme_screenshot() {
	echo wpmobi_get_theme_screenshot();
}

function wpmobi_get_theme_screenshot() {
	global $wpmobi_cur_theme;
	if ( $wpmobi_cur_theme ) {
		return apply_filters( 'wpmobi_theme_screenshot', $wpmobi_cur_theme->screenshot );
	}
	
	return false;	
}
