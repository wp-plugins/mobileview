<?php

global $mobileview_themes;
global $mobileview_cur_theme;

$mobileview_themes = false;
$mobileview_cur_theme = false;

global $mobileview_theme_item;	
global $mobileview_theme_iterator;

$mobileview_theme_item = $mobileview_theme_iterator = false;

function mobileview_rewind_themes() {
	global $mobileview_themes;
	$mobileview_themes = false;
}

function mobileview_has_themes() {
	global $mobileview;
	global $mobileview_theme_iterator;
	
	if ( !$mobileview_theme_iterator ) {	
		$mobileview_themes = $mobileview->get_available_themes();
		$mobileview_theme_iterator = new MobileViewArrayIterator( $mobileview_themes ); 
	} 
	
	return $mobileview_theme_iterator->have_items();
}

function mobileview_the_theme() {
	global $mobileview_theme_iterator;
	global $mobileview_cur_theme;
	
	$mobileview_cur_theme = $mobileview_theme_iterator->the_item();
	
	return apply_filters( 'mobileview_theme', $mobileview_cur_theme );
}

function mobileview_the_theme_classes( $extra_classes = array() ) {
	echo implode( ' ', mobileview_get_theme_classes( $extra_classes ) ) ;	
}

function mobileview_get_theme_classes( $extra_classes = array() ) {
	$classes = explode( ' ', $extra_classes );
		
	if ( mobileview_is_theme_active() ) {
		$classes[] = 'active';
	}
	
	if ( mobileview_is_theme_custom() ) {
		$classes[] = 'custom';	
	}
	
	if ( mobileview_has_theme_tags() ) {
		$tags = mobileview_get_theme_tags();
		foreach( $tags as $tag ) {
			$classes[] = $tag;
		}		
	}
	
	return $classes;
}

function mobileview_has_theme_tags() {
	global $mobileview_cur_theme;
	
	return ( isset( $mobileview_cur_theme->tags ) && count( $mobileview_cur_theme->tags ) );	
}

function mobileview_get_theme_tags() {
	global $mobileview_cur_theme;
	
	return apply_filters( 'mobileview_theme_tags', $mobileview_cur_theme->tags );	
}

function mobileview_is_theme_active() {
	global $mobileview;
	global $mobileview_cur_theme;
	
	$settings = $mobileview->get_settings();
		
	$current_theme_location = $settings->current_theme_location . '/' . $settings->current_theme_name;
	
	return ( $mobileview_cur_theme->location == $current_theme_location );
}

function mobileview_active_theme_has_settings() {
	$menu = apply_filters( 'mobileview_theme_menu', array() );
	return count( $menu );	
}

function mobileview_is_theme_custom() {
	global $mobileview_cur_theme;
	return ( $mobileview_cur_theme->custom_theme );	
}

function mobileview_is_theme_child() {
	global $mobileview_cur_theme;
	return ( isset( $mobileview_cur_theme->parent_theme ) && strlen( $mobileview_cur_theme->parent_theme ) );	
}

function mobileview_the_theme_version() {
	echo mobileview_get_theme_version();
}	

function mobileview_get_theme_version() {
	global $mobileview_cur_theme;
	if ( $mobileview_cur_theme ) {
		return apply_filters( 'mobileview_theme_version', $mobileview_cur_theme->version );
	}
	
	return false;		
}


function mobileview_the_theme_title() {
	echo mobileview_get_theme_title();	
}

function mobileview_get_theme_title() {
	global $mobileview_cur_theme;
	if ( $mobileview_cur_theme ) {
		return apply_filters( 'mobileview_theme_title', $mobileview_cur_theme->name );
	}
	
	return false;		
}

function mobileview_the_theme_location() {
	echo mobileview_get_theme_location();	
}

function mobileview_get_theme_location() {
	global $mobileview_cur_theme;
	if ( $mobileview_cur_theme ) {
		return apply_filters( 'mobileview_theme_location', $mobileview_cur_theme->location );
	}
	
	return false;		
}

function mobileview_the_theme_features() {
	echo implode( mobileview_get_theme_features(), ', ' );	
}

function mobileview_get_theme_features() {
	global $mobileview_cur_theme;
	return apply_filters( 'mobileview_theme_features', $mobileview_cur_theme->features );	
}

function mobileview_theme_has_features() {
	global $mobileview_cur_theme;
	return $mobileview_cur_theme->features;		
}

function mobileview_the_theme_author() {
	echo mobileview_get_theme_author();	
}

function mobileview_get_theme_author() {
	global $mobileview_cur_theme;
	if ( $mobileview_cur_theme ) {
		return apply_filters( 'mobileview_theme_author', $mobileview_cur_theme->author );
	}
	
	return false;		
}

function mobileview_the_theme_description() {
	echo mobileview_get_theme_description();	
}

function mobileview_get_theme_description() {
	global $mobileview_cur_theme;
	if ( $mobileview_cur_theme ) {
		return apply_filters( 'mobileview_theme_description', $mobileview_cur_theme->description );
	}
	
	return false;		
}

function mobileview_the_theme_screenshot() {
	echo mobileview_get_theme_screenshot();
}

function mobileview_get_theme_screenshot() {
	global $mobileview_cur_theme;
	if ( $mobileview_cur_theme ) {
		return apply_filters( 'mobileview_theme_screenshot', $mobileview_cur_theme->screenshot );
	}
	
	return false;	
}
/*-----------------------------------------------------------------------------------*/
/* Check Skin Update */
/*-----------------------------------------------------------------------------------*/
function mobileview_is_theme_update(){
	$storefront_skin_version = '';
	$skin_version = '';
	$url_storefront_skin_version = wp_remote_get('http://colorlabsproject.com/updates/mobileview-skins/'.trim(mobileview_get_theme_title()).'/readme.txt');

	if(!is_wp_error($url_storefront_skin_version)){
		if ( preg_match( '#Version: (.*)#i', $url_storefront_skin_version['body'], $matches ) ) {
			if($matches){
			$storefront_skin_version = $matches[1];
			}
		}
		$skin_version = mobileview_get_theme_version();
		if ( version_compare( $storefront_skin_version, $skin_version, '>' ) ) {
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}	