<?php

global $wpmobi_plugin_warning_iterator;
global $wpmobi_plugin_warning;

function wpmobi_get_plugin_warning_count() {
	global $wpmobi;
	$settings = wpmobi_get_settings();
	
	$warnings = apply_filters( 'wpmobi_plugin_warnings', $wpmobi->warnings );
	ksort( $warnings );
	
	$new_warnings = array();
	foreach( $warnings as $key => $value ) {
		if ( !in_array( $key, $settings->dismissed_warnings ) ) {
			$new_warnings[ $key ] = $value;
		}
	}
	
	return count( $new_warnings );
}

function wpmobi_has_plugin_warnings() {
	global $wpmobi;
	global $wpmobi_plugin_warning_iterator;	
	$settings = wpmobi_get_settings();
	
	if ( !$wpmobi_plugin_warning_iterator ) {
		$warnings = apply_filters( 'wpmobi_plugin_warnings', $wpmobi->warnings );
		ksort( $warnings );
		
		$new_warnings = array();
		foreach( $warnings as $key => $value ) {
			if ( !in_array( $key, $settings->dismissed_warnings ) ) {
				$new_warnings[ $key ] = $value;
			}
		}
		
		$wpmobi_plugin_warning_iterator = new WPMobiArrayIterator( $new_warnings );	
	}
	
	return $wpmobi_plugin_warning_iterator->have_items();
}

function wpmobi_the_plugin_warning() {
	global $wpmobi_plugin_warning_iterator;
	global $wpmobi_plugin_warning;	
	
	if ( $wpmobi_plugin_warning_iterator ) {
		$wpmobi_plugin_warning = apply_filters( 'wpmobi_plugin_warning', $wpmobi_plugin_warning_iterator->the_item() );	
	}
}

function wpmobi_plugin_warning_the_name() {
	echo wpmobi_plugin_warning_get_name();	
}

function wpmobi_plugin_warning_get_name() {
	global $wpmobi_plugin_warning;	
	return apply_filters( 'wpmobi_plugin_warning_name', $wpmobi_plugin_warning[0] );
}

function wpmobi_plugin_warning_the_desc() {
	echo wpmobi_plugin_warning_get_desc();
}

function wpmobi_plugin_warning_get_desc() {
	global $wpmobi_plugin_warning;	
	return apply_filters( 'wpmobi_plugin_warning_desc', $wpmobi_plugin_warning[1] );
}

function wpmobi_plugin_warning_has_link() {
	global $wpmobi_plugin_warning;
	
	return ( $wpmobi_plugin_warning[2] == true );
}

function wpmobi_plugin_warning_get_link() {
	global $wpmobi_plugin_warning;
	
	return $wpmobi_plugin_warning[2];
}

function wpmobi_plugin_warning_the_link() {
	echo wpmobi_plugin_warning_get_link();
}
