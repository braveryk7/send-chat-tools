<?php
/**
 * Check Update WordPress core, theme, and plugin.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 1.0.0
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Check Update WordPress core, theme, and plugin.
 */
class Sct_Logger {
	/**
	 * Create log format.
	 *
	 * @param int    $status_code HTTP status code.
	 * @param string $tool_name Use tool name.
	 * @param string $notification_type Comment, Update.
	 */
	public function create_log( int $status_code, string $tool_name, string $notification_type ) {

		switch ( $tool_name ) {
			case 'slack':
				$tool = '1';
				break;
			case 'discord':
				$tool = '2';
				break;
			case 'chatwork':
				$tool = '3';
				break;
		}

		switch ( $notification_type ) {
			case ctype_digit( $notification_type ):
				$type = '1';
				break;
			case 'update':
				$type = '2';
				break;
		}

		if ( isset( $tool ) && isset( $type ) ) {
			$db_class = new Sct_Connect_Database();
			$db_class->insert_log( $status_code, $tool, $type );
		}
	}
}
