<?php

global $wpmobi_icon_pack;
global $wpmobi_icon_packs_iterator;
$wpmobi_icon_packs_iterator = false;

global $wpmobi_icon;
global $wpmobi_icons_iterator;
$wpmobi_icons_iterator = false;

global $wpmobi_admin_menu_items;
global $wpmobi_admin_menu_iterator;
global $wpmobi_admin_menu_item;

$wpmobi_admin_menu_items = $wpmobi_admin_menu_iterator = $wpmobi_admin_menu_item = false;

global $wpmobi_site_icons;
global $wpmobi_site_icon;
global $wpmobi_site_icon_iterator;

$wpmobi_site_icons = $wpmobi_site_icon = $wpmobi_site_icon_iterator = false;


function wpmobi_have_icon_packs() {
	global $wpmobi;
	global $wpmobi_icon_packs_iterator;	
	
	if ( !$wpmobi_icon_packs_iterator ) {
		$wpmobi_icon_packs = $wpmobi->get_available_icon_packs();
		$wpmobi_icon_packs_iterator = new WPMobiArrayIterator( $wpmobi_icon_packs );
	} 
	
	$has_items = $wpmobi_icon_packs_iterator->have_items();
	
	return $has_items;
}

function wpmobi_the_icon_pack() {
	global $wpmobi_icon_pack;	
	global $wpmobi_icon_packs_iterator;	
	
	$wpmobi_icon_pack = $wpmobi_icon_packs_iterator->the_item();
}

function wpmobi_the_icon_pack_name() {
	echo wpmobi_get_icon_pack_name();	
}

function wpmobi_get_icon_pack_name() {
	global $wpmobi_icon_pack;	
	
//	print_r( $wpmobi_icon_pack );
	
	return apply_filters( 'wpmobi_icon_pack_name', $wpmobi_icon_pack->name );		
}

function wpmobi_get_icon_pack_author_url() {
	global $wpmobi_icon_pack;

	if ( isset( $wpmobi_icon_pack->author_url ) ) {
		return $wpmobi_icon_pack->author_url;	
	} else {
		return false;	
	}
}

function wpmobi_the_icon_pack_author_url() {
	$url = wpmobi_get_icon_pack_author_url();
	if ( $url ) {
		echo $url;	
	} 
}

function wpmobi_get_icon_pack_dark_bg() {
	global $wpmobi_icon_pack;	
	return $wpmobi_icon_pack->dark_background;
}


function wpmobi_the_icon_pack_desc() {
	echo wpmobi_get_icon_pack_desc();
}

function wpmobi_get_icon_pack_desc() {
	global $wpmobi_icon_pack;
	return apply_filters( 'wpmobi_icon_pack_desc', $wpmobi_icon_pack->description );		
}

function wpmobi_is_icon_set_enabled() {
	global $wpmobi;
	global $wpmobi_icon_pack;
	
	$settings = $wpmobi->get_settings();
	if ( isset( $settings->enabled_icon_packs[ $wpmobi_icon_pack->name ] ) ) {
		return true;	
	} else {	
		return false;	
	}
}

function wpmobi_the_icon_pack_class_name() {
	echo wpmobi_get_icon_pack_class_name();
}

function wpmobi_get_icon_pack_class_name() {
	global $wpmobi_icon_pack;
	return apply_filters( 'wpmobi_icon_pack_class_name', $wpmobi_icon_pack->class_name );			
}

function wpmobi_have_icons( $set_name ) {
	global $wpmobi_icons_iterator;	
	global $wpmobi;
	
	if ( !$wpmobi_icons_iterator ) {
		$icons = $wpmobi->get_icons_from_packs( $set_name );	
		$wpmobi_icons_iterator = new WPMobiArrayIterator( $icons );
	}
	
	return $wpmobi_icons_iterator->have_items();
}

function wpmobi_the_icon() {
	global $wpmobi_icon;	
	global $wpmobi_icons_iterator;		
	
	$wpmobi_icon = $wpmobi_icons_iterator->the_item();
	return $wpmobi_icon;
}

function wpmobi_the_icon_name() {
	echo wpmobi_get_icon_name();	
}

function wpmobi_get_icon_name() {
	global $wpmobi_icon;
	return apply_filters( 'wpmobi_icon_name', $wpmobi_icon->name );	
}

function wpmobi_the_icon_short_name() {
	echo wpmobi_get_icon_short_name();	
}

function wpmobi_get_icon_short_name() {
	global $wpmobi_icon;
	return apply_filters( 'wpmobi_icon_short_name', $wpmobi_icon->short_name );	
}


function wpmobi_the_icon_url() {
	echo wpmobi_get_icon_url();	
}

function wpmobi_get_icon_url() {
	global $wpmobi_icon;
	return apply_filters( 'wpmobi_icon_url', $wpmobi_icon->url );	
}

function wpmobi_the_icon_set() {
	echo wpmobi_get_icon_set();
}

function wpmobi_get_icon_set() {
	global $wpmobi_icon;
	return apply_filters( 'wpmobi_icon_set', $wpmobi_icon->set );		
}


function wpmobi_icon_has_image_size_info() {
	global $wpmobi_icon;
	return isset( $wpmobi_icon->image_size );	
}

function wpmobi_icon_the_width() {
	echo wpmobi_icon_get_width();	
}

function wpmobi_icon_get_width() {
	global $wpmobi_icon;
	return $wpmobi_icon->image_size[0];	
}


function wpmobi_icon_the_height() {
	echo wpmobi_icon_get_height();	
}

function wpmobi_icon_get_height() {
	global $wpmobi_icon;
	return $wpmobi_icon->image_size[1];
}

function wpmobi_the_icon_class_name() {
	echo wpmobi_get_icon_class_name();
}

function wpmobi_get_icon_class_name() {
	global $wpmobi_icon;
	return apply_filters( 'wpmobi_icon_class_name', $wpmobi_icon->class_name );			
}

function wpmobi_admin_has_menu_items() {
	global $wpmobi_admin_menu_items;
	global $wpmobi_admin_menu_iterator;
	
	wpmobi_build_menu_tree( 0, 1, $wpmobi_admin_menu_items );	
	
	$wpmobi_admin_menu_iterator = new WPMobiArrayIterator( $wpmobi_menu_items );
	
	return $wpmobi_admin_menu_iterator->have_items();
}

function wpmobi_admin_the_menu_item() {
	global $wpmobi_admin_menu_item;
	global $wpmobi_admin_menu_iterator;
	
	if ( $wpmobi_admin_menu_iterator ) {
		$wpmobi_admin_menu_item = $wpmobi_admin_menu_iterator->the_item();
	}
}

function wpmobi_has_site_icons() {
	global $wpmobi;
	global $wpmobi_site_icons;
	global $wpmobi_site_icon_iterator;
	
	if ( !$wpmobi_site_icons ) {
		$wpmobi_site_icons = $wpmobi->get_site_icons();
		$wpmobi_site_icon_iterator = new WPMobiArrayIterator( $wpmobi_site_icons );
	}
	
	return $wpmobi_site_icon_iterator->have_items();
}

function wpmobi_the_site_icon() {
	global $wpmobi_site_icon_iterator;	
	global $wpmobi_site_icon;
	
	$wpmobi_site_icon = apply_filters( 'wpmobi_site_icon', $wpmobi_site_icon_iterator->the_item() );	
	return $wpmobi_site_icon;
}

function wpmobi_the_site_icon_name() {
	echo wpmobi_get_site_icon_name();	
}

function wpmobi_get_site_icon_name() {
	global $wpmobi_site_icon;
	return apply_filters( 'wpmobi_site_icon_name', $wpmobi_site_icon->name );	
}

function wpmobi_the_site_icon_id() {
	echo wpmobi_get_site_icon_id();
}

function wpmobi_get_site_icon_id() {
	global $wpmobi_site_icon;
	return $wpmobi_site_icon->id;
}

function wpmobi_the_site_icon_icon() {
	echo wpmobi_get_site_icon_icon();
}

function wpmobi_get_site_icon_icon() {
	global $wpmobi_site_icon;
	global $wpmobi;
	
	$settings = $wpmobi->get_settings();
	if ( isset( $settings->menu_icons[ $wpmobi_site_icon->id ] ) ) {
		$icon = clc_wpmobi_sslize( WP_CONTENT_URL . $settings->menu_icons[ $wpmobi_site_icon->id ] );
	} else {
		$icon = clc_wpmobi_sslize( WP_CONTENT_URL . $wpmobi_site_icon->icon );
	}	
	
	return apply_filters( 'wpmobi_site_icon_icon', $icon );
}

function wpmobi_the_site_icon_location() {
	echo wpmobi_get_site_icon_location();
}

function wpmobi_get_site_icon_location() {
	global $wpmobi_site_icon;
	global $wpmobi;
	
	$settings = $wpmobi->get_settings();
	if ( isset( $settings->menu_icons[ $wpmobi_site_icon->id ] ) ) {
		$icon = WP_CONTENT_DIR . $settings->menu_icons[ $wpmobi_site_icon->id ];
	} else {
		$icon = WP_CONTENT_DIR . $wpmobi_site_icon->icon;
	}	
	
	return apply_filters( 'wpmobi_site_icon_location', $icon );
}

function wpmobi_the_site_icon_classes() {
	echo implode( ' ', wpmobi_get_site_icon_classes() );	
}

function wpmobi_get_site_icon_classes() {
	global $wpmobi_site_icon;	
	
	$classes = array( $wpmobi_site_icon->class_name );
	
	return apply_filters( 'wpmobi_site_icon_classes', $classes );	
}

function wpmobi_site_icon_has_dark_bg() {
	global $wpmobi;
	
	$set_info = $wpmobi->get_set_with_icon( wpmobi_get_site_icon_location() );
	if ( $set_info ) {
		return $set_info->dark_background;	
	} else {
		return false;	
	}
}
