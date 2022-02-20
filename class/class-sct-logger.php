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
class Sct_Logger extends Sct_Base {
	/**
	 * Create log format.
	 *
	 * @param int    $status_code HTTP status code.
	 * @param string $tool_name Use tool name.
	 * @param string $notification_type Comment, Update.
	 */
	public function create_log( int $status_code, string $tool_name, string $notification_type ): void {

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
			case 'dev_notify':
				$type = '3';
				break;
		}

		if ( isset( $tool ) && isset( $type ) ) {
			$sct_logs = get_option( $this->add_prefix( 'logs' ) );

			$create_log_data = [
				'status'    => $status_code,
				'tool'      => $tool,
				'type'      => $type,
				'send_date' => current_time( 'mysql' ),
			];

			if ( array_key_exists( 999, $sct_logs ) ) {
				unset( $sct_logs[999] );
			}
			update_option( $this->add_prefix( 'logs' ), array_merge( [ $create_log_data ], $sct_logs, ) );
		}
	}
}
