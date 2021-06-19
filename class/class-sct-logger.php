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
	 * @param string $states_code HTTP states code.
	 * @param string $tool_name Use tool name.
	 * @param string $notification_type Comment, Update.
	 */
	public function create_log( string $states_code, string $tool_name, string $notification_type ) {
		if ( 'slack' === $tool_name ) {
			$tool = '1';
		} elseif ( 'discord' === $tool_name ) {
			$tool = '2';
		} elseif ( 'chatwork' === $tool_name ) {
			$tool = '3';
		}

		if ( ctype_digit( $notification_type ) ) {
			$type = '1';
		} elseif ( 'update' === $notification_type ) {
			$type = '2';
		}

		if ( isset( $tool ) && isset( $type ) ) {
			$db_class = new Sct_Connect_Database();
			$db_class->insert_log( $states_code, $tool, $type );
		}
	}
}
