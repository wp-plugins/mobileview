<?php

global $mobileview_plugin_warning_iterator;
global $mobileview_plugin_warning;

function mobileview_get_plugin_warning_count() {
	global $mobileview;
	$settings = mobileview_get_settings();
	
	$warnings = apply_filters( 'mobileview_plugin_warnings', $mobileview->warnings );
	ksort( $warnings );
	
	$new_warnings = array();
	foreach( $warnings as $key => $value ) {
		if ( !in_array( $key, $settings->dismissed_warnings ) ) {
			$new_warnings[ $key ] = $value;
		}
	}
	
	return count( $new_warnings );
}

function mobileview_has_plugin_warnings() {
	global $mobileview;
	global $mobileview_plugin_warning_iterator;	
	$settings = mobileview_get_settings();
	
	if ( !$mobileview_plugin_warning_iterator ) {
		$warnings = apply_filters( 'mobileview_plugin_warnings', $mobileview->warnings );

		if( !$warnings )
			return;

		ksort( $warnings );
		
		$new_warnings = array();
		foreach( $warnings as $key => $value ) {
			if ( !in_array( $key, $settings->dismissed_warnings ) ) {
				$new_warnings[ $key ] = $value;
			}
		}
		
		$mobileview_plugin_warning_iterator = new MobileViewArrayIterator( $new_warnings );	
	}
	
	return $mobileview_plugin_warning_iterator->have_items();
}

function mobileview_the_plugin_warning() {
	global $mobileview_plugin_warning_iterator;
	global $mobileview_plugin_warning;	
	
	if ( $mobileview_plugin_warning_iterator ) {
		$mobileview_plugin_warning = apply_filters( 'mobileview_plugin_warning', $mobileview_plugin_warning_iterator->the_item() );	
	}
}

function mobileview_plugin_warning_the_name() {
	echo mobileview_plugin_warning_get_name();	
}

function mobileview_plugin_warning_get_name() {
	global $mobileview_plugin_warning;	
	return apply_filters( 'mobileview_plugin_warning_name', $mobileview_plugin_warning[0] );
}

function mobileview_plugin_warning_the_desc() {
	echo mobileview_plugin_warning_get_desc();
}

function mobileview_plugin_warning_get_desc() {
	global $mobileview_plugin_warning;	
	return apply_filters( 'mobileview_plugin_warning_desc', $mobileview_plugin_warning[1] );
}

function mobileview_plugin_warning_has_link() {
	global $mobileview_plugin_warning;
	
	return ( $mobileview_plugin_warning[2] == true );
}

function mobileview_plugin_warning_get_link() {
	global $mobileview_plugin_warning;
	
	return $mobileview_plugin_warning[2];
}

function mobileview_plugin_warning_the_link() {
	echo mobileview_plugin_warning_get_link();
}
