<?php

global $wpmobi_tab_iterator;
global $wpmobi_tab;
global $wpmobi_tab_id;

global $wpmobi_tab_section_iterator;
global $wpmobi_tab_section;

global $wpmobi_tab_section_settings_iterator;
global $wpmobi_tab_section_setting;

global $wpmobi_tab_options_iterator;
global $wpmobi_tab_option;

$wpmobi_tab_iterator = false;

function wpmobi_has_tabs() {
	global $wpmobi_tab_iterator;
	global $wpmobi;
	global $wpmobi_tab_id;
	
	if ( !$wpmobi_tab_iterator ) {
		$wpmobi_tab_iterator = new WPMobiArrayIterator( $wpmobi->tabs );
		$wpmobi_tab_id = 0;
	}
	
	return $wpmobi_tab_iterator->have_items();	
}

function wpmobi_rewind_tab_settings() {
	global $wpmobi_tab_section_iterator;
	$wpmobi_tab_section_iterator = false;
}

function wpmobi_the_tab() {
	global $wpmobi_tab;
	global $wpmobi_tab_iterator;
	global $wpmobi_tab_id;
	global $wpmobi_tab_section_iterator;
	
	$wpmobi_tab = apply_filters( 'wpmobi_tab', $wpmobi_tab_iterator->the_item() );
	$wpmobi_tab_section_iterator = false;
	$wpmobi_tab_id++;
}

function wpmobi_the_tab_id() {
	echo wpmobi_get_tab_id();
}

function wpmobi_get_tab_id() {
	global $wpmobi_tab_id;
	return apply_filters( 'wpmobi_tab_id', $wpmobi_tab_id );	
}

function wpmobi_has_tab_sections() {
	global $wpmobi_tab;	
	global $wpmobi_tab_section_iterator;
	
	if ( !$wpmobi_tab_section_iterator ) {
		$wpmobi_tab_section_iterator = new WPMobiArrayIterator( $wpmobi_tab['settings'] );
	}
	
	return $wpmobi_tab_section_iterator->have_items();
}

function wpmobi_the_tab_section() {
	global $wpmobi_tab_section;
	global $wpmobi_tab_section_iterator;
	global $wpmobi_tab_section_settings_iterator;
		
	$wpmobi_tab_section = apply_filters( 'wpmobi_tab_section', $wpmobi_tab_section_iterator->the_item() );
	$wpmobi_tab_section_settings_iterator = false;
}

function wpmobi_the_tab_name() {
	echo wpmobi_get_tab_name();
}

function wpmobi_get_tab_name() {
	global $wpmobi_tab_section_iterator;
		
	return apply_filters( 'wpmobi_tab_name', $wpmobi_tab_section_iterator->the_key() );
}

function wpmobi_the_tab_class_name() {
	echo wpmobi_get_tab_class_name();
}

function wpmobi_get_tab_class_name() {
	return wpmobi_string_to_class( wpmobi_get_tab_name() );	
}


function wpmobi_has_tab_section_settings() {
	global $wpmobi_tab_section;
	global $wpmobi_tab_section_settings_iterator;
	
	if ( !$wpmobi_tab_section_settings_iterator ) {
		$wpmobi_tab_section_settings_iterator = new WPMobiArrayIterator( $wpmobi_tab_section[1] );
	}
	
	return $wpmobi_tab_section_settings_iterator->have_items();
}

function wpmobi_the_tab_section_setting() {
	global $wpmobi_tab_section_setting;
	global $wpmobi_tab_section_settings_iterator;
	global $wpmobi_tab_options_iterator;
		
	$wpmobi_tab_section_setting = apply_filters( 'wpmobi_tab_section_setting', $wpmobi_tab_section_settings_iterator->the_item() );
	$wpmobi_tab_options_iterator = false;
}

function wpmobi_the_tab_section_class_name() {
	echo wpmobi_get_tab_section_class_name();
}

function wpmobi_get_tab_section_class_name() {
	global $wpmobi_tab_section;
	
	return $wpmobi_tab_section[0];
}

function wpmobi_the_tab_setting_type() {
	echo wpmobi_get_tab_setting_type();
}

function wpmobi_get_tab_setting_type() {
	global $wpmobi_tab_section_setting;
	return apply_filters( 'wpmobi_tab_setting_type', $wpmobi_tab_section_setting[0] );
}

function wpmobi_the_tab_setting_name() {
	echo wpmobi_get_tab_setting_name();
}

function wpmobi_get_tab_setting_name() {
	global $wpmobi_tab_section_setting;
	
	return apply_filters( 'wpmobi_tab_setting_name', $wpmobi_tab_section_setting[1] );		
}

function wpmobi_the_tab_setting_class_name() {
	echo wpmobi_get_tab_setting_class_name();
}

function wpmobi_get_tab_setting_class_name() {
	global $wpmobi_tab_section_setting;
	
	if ( isset( $wpmobi_tab_section_setting[1] ) ) {
		return apply_filters( 'wpmobi_tab_setting_class_name', wpmobi_string_to_class( $wpmobi_tab_section_setting[1] ) );	
	} else {
		return false;	
	}	
}

function wpmobi_the_tab_setting_has_tooltip() {
	return ( strlen( wpmobi_get_tab_setting_tooltip() ) > 0 );
}

function wpmobi_the_tab_setting_tooltip() {
	echo wpmobi_get_tab_setting_tooltip();
}

function wpmobi_get_tab_setting_tooltip() {
	global $wpmobi_tab_section_setting;
	
	if ( isset( $wpmobi_tab_section_setting[3] ) ) {
		return htmlspecialchars( apply_filters( 'wpmobi_tab_setting_tooltip', $wpmobi_tab_section_setting[3] ), ENT_COMPAT, 'UTF-8' );	
	} else {
		return false;	
	}	
}


function wpmobi_the_tab_setting_desc() {
	echo wpmobi_get_tab_setting_desc();
}

function wpmobi_get_tab_setting_desc() {
	global $wpmobi_tab_section_setting;
	return apply_filters( 'wpmobi_tab_setting_desc', $wpmobi_tab_section_setting[2] );		
}

function wpmobi_the_tab_setting_value() {
	echo wpmobi_get_tab_setting_value();
}

function wpmobi_get_tab_setting_value() {
	$settings = wpmobi_get_settings();
	$name = wpmobi_get_tab_setting_name();
	if ( isset( $settings->$name ) ) {
		return $settings->$name;	
	} else {
		return false;	
	}
}

function wpmobi_the_tab_setting_is_checked() {
	return wpmobi_get_tab_setting_value();
}

function wpmobi_tab_setting_has_options() {
	global $wpmobi_tab_options_iterator;
	global $wpmobi_tab_section_setting;
	
	if ( isset( $wpmobi_tab_section_setting[4] ) ) {			
		if ( !$wpmobi_tab_options_iterator ) {
			$wpmobi_tab_options_iterator = new WPMobiArrayIterator( $wpmobi_tab_section_setting[4] );	
		}
		
		return $wpmobi_tab_options_iterator->have_items();
	} else {
		return false;	
	}
}

function wpmobi_tab_setting_the_option() {
	global $wpmobi_tab_options_iterator;
	global $wpmobi_tab_option;	
	
	$wpmobi_tab_option = apply_filters( 'wpmobi_tab_setting_option', $wpmobi_tab_options_iterator->the_item() );
}

function wpmobi_tab_setting_has_tags() {
	global $wpmobi_tab_section_setting;
	
	$has_tag = false;
	
	switch( wpmobi_get_tab_setting_type() ) {
		case 'checkbox':
		case 'text':
		case 'textarea':
			$has_tag =  isset( $wpmobi_tab_section_setting[4] );
			break;
		case 'list':
			$has_tag = isset( $wpmobi_tab_section_setting[5] );
			break;
		case 'custom-latest':
			$has_tag = isset( $wpmobi_tab_section_setting[4] );
			break;
	}
	
	return apply_filters( 'wpmobi_tab_setting_tags', $has_tag );
}

function wpmobi_tab_setting_get_tags() {
	global $wpmobi_tab_section_setting;
	
	$tags = array();
	
	switch( wpmobi_get_tab_setting_type() ) {
		case 'checkbox':
		case 'text':
		case 'textarea':
			$tags = $wpmobi_tab_section_setting[4];
			break;		
		case 'list':
			$tags = $wpmobi_tab_section_setting[5];
			break;
		case 'custom-latest':
			$tags = $wpmobi_tab_section_setting[4];
			break;			
	}	
	
	return apply_filters( 'wpmobi_tab_setting_tags', $tags );
}

function wpmobi_tab_setting_the_tags() {
	return @implode( '', wpmobi_tab_setting_get_tags() );	
}

function wpmobi_tab_setting_the_option_desc() {
	echo wpmobi_tab_setting_get_option_desc();
}	

function wpmobi_tab_setting_get_option_desc() {
	global $wpmobi_tab_option;		
	return apply_filters( 'wpmobi_tab_setting_option_desc', $wpmobi_tab_option );
}	

function wpmobi_tab_setting_the_option_key() {
	echo wpmobi_tab_setting_get_option_key();
}

function wpmobi_tab_setting_get_option_key() {
	global $wpmobi_tab_options_iterator;
	return apply_filters( 'wpmobi_tab_setting_option_key', $wpmobi_tab_options_iterator->the_key() );	
}

function wpmobi_tab_setting_is_selected() {
	return ( wpmobi_tab_setting_get_option_key() == wpmobi_get_tab_setting_value() );
}
