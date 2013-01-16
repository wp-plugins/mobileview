<?php

global $wpmobi_debug;

define( 'WPMOBI_ERROR', 1 );
define( 'WPMOBI_SECURITY', 2 );
define( 'WPMOBI_WARNING', 3 );
define( 'WPMOBI_INFO', 4 );
define( 'WPMOBI_VERBOSE', 5 );
define( 'WPMOBI_ALL', 6 );

class WPMobiDebug {
	var $debug_file;		//! The file descriptor for the debug file
	var $enabled;			//! Indicates whether or not the debug log is enabled
	var $log_level;			//! The current log level for the debug log
	
	function WPMobiDebug() {
		$this->debug_file = false;
		$this->enabled = false;
		$this->log_level = WPMOBI_WARNING;
	}	
	
	/*! 	\brief Enables the debug log		 
	 *
	 *		This method enables the debug log.  Since the default state of the debug log is disabled, this method must be used to enable the log
	 *		prior to the debug log outputting any data to a file.	 
	 *
	 *		\ingroup debug	 
	 */		
	function enable() {
		$this->enabled = true;
		
		// Create the debug file
		if ( !$this->debug_file ) {
			$this->debug_file = fopen( WPMOBI_DEBUG_DIRECTORY . '/debug.txt', 'a+t' );
		}
	}
	
	/*! 	\brief Disables the debug log		 
	 *
	 *		This method disables the debug log. 
	 *
	 *		\ingroup debug	 	 
	 */			
	function disable() {
		$this->enabled = false;
		
		// Close the debug file
		if ( $this->debug_file ) {
			fclose( $this->debug_file );
			$this->debug_file = false;
		}
	}
	
	/*! 	\brief Sets the level for the debug log		 
	 *
	 *		This method sets the level for the debug log.  It can be one of WPMOBI_ERROR, WPMOBI_SECURITY, etc.  
	 *
	 *		\note The default log level is WPMOBI_WARNING.
	 *
	 *		\ingroup debug	 
	 */			
	function set_log_level( $level ) {
		$this->log_level = $level;	
	}
	
	/*! 	\brief Attempts to add a message to the debug log		 
	 *
	 *		This method attempts to add a message to the debug log.  
	 *
	 *		\param level The log level for the debug message
	 *		\param msg The debug message
	 *
	 *		\ingroup debug	 
	 */				
	function add_to_log( $level, $msg ) {
		if ( $this->enabled && $level <= $this->log_level ) {
			$message = sprintf( "%28s", date( 'M jS, Y g:i:s a' ) ) . ' - ';
			
			switch( $level ) {
				case WPMOBI_ERROR:
					$message .= '[error]';
					break;
				case WPMOBI_SECURITY:
					$message .= '[security]';
					break;
				case WPMOBI_WARNING:
					$message .= '[warning]';
					break;
				case WPMOBI_INFO:
					$message .= '[info]';
					break;
				case WPMOBI_VERBOSE:
					$message .= '[verbose]';
					break;
			}
			
			// Lock the debug file for writing so multiple PHP processes don't mangle it
			if ( flock( $this->debug_file, LOCK_EX ) ) {
				fwrite( $this->debug_file, $message . ' ' . $msg . "\n" );
				flock( $this->debug_file, LOCK_UN );	
			}
		}	
	}
}

$wpmobi_debug = new WPMobiDebug;

/*! 	\brief Attempts to output a debug message to the debug log	 
 *
 *		This method attempts to output a debug message to the debug log.  The message will only be written if the log has been enabled using wpmobi_debug_enable()
 *		and the log message is at a level at or below the current debug log level.  This message calls WPMobiDebug::add_to_log().
 *
 *		\param level The level for the debug message
 *		\param msg The message to write to the debug log 
 *
 *		\ingroup debug 
 */	
function WPMOBI_DEBUG( $level, $msg ) {
	global $wpmobi_debug;
	
	$wpmobi_debug->add_to_log( $level, $msg );	
}

/*! 	\brief Enables or disables the debug log.	 
 *
 *		This method enables or disables the debug log.  Ultimately it calls WPMobiDebug::enable() or WPMobiDebug::disable() on the global debug object.
 *
 *		\param enable_or_disable True to enable the log, false to disable it
 *
 *		\ingroup debug
 */	
function wpmobi_debug_enable( $enable_or_disable ) {
	global $wpmobi_debug;
	
	if ( $enable_or_disable ) {
		$wpmobi_debug->enable();	
	} else {
		$wpmobi_debug->disable();
	}	
}

/*! 	\brief Sets the debug log level	 
 *
 *		This method sets the debug log level by calling WPMobiDebug::set_log_level().
 *
 *		\param level The level to set the debug log to
 *
 *		\ingroup debug
 */	
function wpmobi_debug_set_log_level( $level ) {
	global $wpmobi_debug;	
	
	$wpmobi_debug->set_log_level( $level );
}
