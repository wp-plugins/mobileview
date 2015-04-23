<?php

global $mobileview_tab_iterator;
global $mobileview_tab;
global $mobileview_tab_id;

global $mobileview_tab_section_iterator;
global $mobileview_tab_section;

global $mobileview_tab_section_settings_iterator;
global $mobileview_tab_section_setting;

global $mobileview_tab_options_iterator;
global $mobileview_tab_option;

$mobileview_tab_iterator = false;

function mobileview_has_tabs() {
	global $mobileview_tab_iterator;
	global $mobileview;
	global $mobileview_tab_id;
	
	if ( !$mobileview_tab_iterator ) {
		$mobileview_tab_iterator = new MobileViewArrayIterator( $mobileview->tabs );
		$mobileview_tab_id = 0;
	}
	
	return $mobileview_tab_iterator->have_items();	
}

function mobileview_rewind_tab_settings() {
	global $mobileview_tab_section_iterator;
	$mobileview_tab_section_iterator = false;
}

function mobileview_the_tab() {
	global $mobileview_tab;
	global $mobileview_tab_iterator;
	global $mobileview_tab_id;
	global $mobileview_tab_section_iterator;
	
	$mobileview_tab = apply_filters( 'mobileview_tab', $mobileview_tab_iterator->the_item() );
	$mobileview_tab_section_iterator = false;
	$mobileview_tab_id++;
}

function mobileview_the_tab_id() {
	echo mobileview_get_tab_id();
}

function mobileview_get_tab_id() {
	global $mobileview_tab_id;
	return apply_filters( 'mobileview_tab_id', $mobileview_tab_id );	
}

function mobileview_has_tab_sections() {
	global $mobileview_tab;	
	global $mobileview_tab_section_iterator;
	
	if ( !$mobileview_tab_section_iterator ) {
		$mobileview_tab_section_iterator = new MobileViewArrayIterator( $mobileview_tab['settings'] );
	}
	
	return $mobileview_tab_section_iterator->have_items();
}

function mobileview_the_tab_section() {
	global $mobileview_tab_section;
	global $mobileview_tab_section_iterator;
	global $mobileview_tab_section_settings_iterator;
		
	$mobileview_tab_section = apply_filters( 'mobileview_tab_section', $mobileview_tab_section_iterator->the_item() );
	$mobileview_tab_section_settings_iterator = false;
}

function mobileview_the_tab_name() {
	echo mobileview_get_tab_name();
}

function mobileview_get_tab_name() {
	global $mobileview_tab_section_iterator;
		
	return apply_filters( 'mobileview_tab_name', $mobileview_tab_section_iterator->the_key() );
}

function mobileview_the_tab_class_name() {
	echo mobileview_get_tab_class_name();
}

function mobileview_get_tab_class_name() {
	return mobileview_string_to_class( mobileview_get_tab_name() );	
}


function mobileview_has_tab_section_settings() {
	global $mobileview_tab_section;
	global $mobileview_tab_section_settings_iterator;
	
	if ( !$mobileview_tab_section_settings_iterator ) {
		$mobileview_tab_section_settings_iterator = new MobileViewArrayIterator( $mobileview_tab_section[1] );
	}
	
	return $mobileview_tab_section_settings_iterator->have_items();
}

function mobileview_the_tab_section_setting() {
	global $mobileview_tab_section_setting;
	global $mobileview_tab_section_settings_iterator;
	global $mobileview_tab_options_iterator;
		
	$mobileview_tab_section_setting = apply_filters( 'mobileview_tab_section_setting', $mobileview_tab_section_settings_iterator->the_item() );
	$mobileview_tab_options_iterator = false;
}

function mobileview_the_tab_section_class_name() {
	echo mobileview_get_tab_section_class_name();
}

function mobileview_get_tab_section_class_name() {
	global $mobileview_tab_section;
	
	return $mobileview_tab_section[0];
}

function mobileview_the_tab_setting_type() {
	echo mobileview_get_tab_setting_type();
}

function mobileview_get_tab_setting_type() {
	global $mobileview_tab_section_setting;
	return apply_filters( 'mobileview_tab_setting_type', $mobileview_tab_section_setting[0] );
}

function mobileview_the_tab_setting_name() {
	echo mobileview_get_tab_setting_name();
}

function mobileview_get_tab_setting_name() {
	global $mobileview_tab_section_setting;
	
	return apply_filters( 'mobileview_tab_setting_name', $mobileview_tab_section_setting[1] );		
}

function mobileview_the_tab_setting_class_name() {
	echo mobileview_get_tab_setting_class_name();
}

function mobileview_get_tab_setting_class_name() {
	global $mobileview_tab_section_setting;
	
	if ( isset( $mobileview_tab_section_setting[1] ) ) {
		return apply_filters( 'mobileview_tab_setting_class_name', mobileview_string_to_class( $mobileview_tab_section_setting[1] ) );	
	} else {
		return false;	
	}	
}

function mobileview_the_tab_setting_has_tooltip() {
	return ( strlen( mobileview_get_tab_setting_tooltip() ) > 0 );
}

function mobileview_the_tab_setting_tooltip() {
	echo mobileview_get_tab_setting_tooltip();
}

function mobileview_get_tab_setting_tooltip() {
	global $mobileview_tab_section_setting;
	
	if ( isset( $mobileview_tab_section_setting[3] ) ) {
		return htmlspecialchars( apply_filters( 'mobileview_tab_setting_tooltip', $mobileview_tab_section_setting[3] ), ENT_COMPAT, 'UTF-8' );	
	} else {
		return false;	
	}	
}


function mobileview_the_tab_setting_desc() {
	echo mobileview_get_tab_setting_desc();
}

function mobileview_get_tab_setting_desc() {
	global $mobileview_tab_section_setting;
	return apply_filters( 'mobileview_tab_setting_desc', $mobileview_tab_section_setting[2] );		
}

function mobileview_the_tab_setting_value() {
	echo mobileview_get_tab_setting_value();
}

function mobileview_get_tab_setting_value() {
	$settings = mobileview_get_settings();
	$name = mobileview_get_tab_setting_name();
	if ( isset( $settings->$name ) ) {
		return $settings->$name;	
	} else {
		return false;	
	}
}

function mobileview_the_tab_setting_is_checked() {
	return mobileview_get_tab_setting_value();
}

function mobileview_tab_setting_has_options() {
	global $mobileview_tab_options_iterator;
	global $mobileview_tab_section_setting;
	
	if ( isset( $mobileview_tab_section_setting[4] ) ) {			
		if ( !$mobileview_tab_options_iterator ) {
			$mobileview_tab_options_iterator = new MobileViewArrayIterator( $mobileview_tab_section_setting[4] );	
		}
		
		return $mobileview_tab_options_iterator->have_items();
	} else {
		return false;	
	}
}

function mobileview_tab_setting_the_option() {
	global $mobileview_tab_options_iterator;
	global $mobileview_tab_option;	
	
	$mobileview_tab_option = apply_filters( 'mobileview_tab_setting_option', $mobileview_tab_options_iterator->the_item() );
}

function mobileview_tab_setting_has_tags() {
	global $mobileview_tab_section_setting;
	
	$has_tag = false;
	
	switch( mobileview_get_tab_setting_type() ) {
		case 'checkbox':
		case 'text':
		case 'textarea':
			$has_tag =  isset( $mobileview_tab_section_setting[4] );
			break;
		case 'list':
			$has_tag = isset( $mobileview_tab_section_setting[5] );
			break;
		case 'custom-latest':
			$has_tag = isset( $mobileview_tab_section_setting[4] );
			break;
	}
	
	return apply_filters( 'mobileview_tab_setting_tags', $has_tag );
}

function mobileview_tab_setting_get_tags() {
	global $mobileview_tab_section_setting;
	
	$tags = array();
	
	switch( mobileview_get_tab_setting_type() ) {
		case 'checkbox':
		case 'text':
		case 'textarea':
			$tags = $mobileview_tab_section_setting[4];
			break;		
		case 'list':
			$tags = $mobileview_tab_section_setting[5];
			break;
		case 'custom-latest':
			$tags = $mobileview_tab_section_setting[4];
			break;			
	}	
	
	return apply_filters( 'mobileview_tab_setting_tags', $tags );
}

function mobileview_tab_setting_the_tags() {
	return @implode( '', mobileview_tab_setting_get_tags() );	
}

function mobileview_tab_setting_the_option_desc() {
	echo mobileview_tab_setting_get_option_desc();
}	

function mobileview_tab_setting_get_option_desc() {
	global $mobileview_tab_option;		
	return apply_filters( 'mobileview_tab_setting_option_desc', $mobileview_tab_option );
}	

function mobileview_tab_setting_the_option_key() {
	echo mobileview_tab_setting_get_option_key();
}

function mobileview_tab_setting_get_option_key() {
	global $mobileview_tab_options_iterator;
	return apply_filters( 'mobileview_tab_setting_option_key', $mobileview_tab_options_iterator->the_key() );	
}

function mobileview_tab_setting_is_selected() {
	return ( mobileview_tab_setting_get_option_key() == mobileview_get_tab_setting_value() );
}
