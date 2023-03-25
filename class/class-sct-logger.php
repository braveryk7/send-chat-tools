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
	 * Constructor.
	 */
	public function __construct() {
		return $this;
	}

	/**
	 * Create log format.
	 *
	 * @param int    $status_code HTTP status code.
	 * @param string $tool_name Use tool name.
	 * @param string $notification_type Comment, Update.
	 */
	public function create_log( int $status_code, string $tool_name, string $notification_type ): array {
		$create_log_data = [];

		$tool = match ( $tool_name ) {
			'slack'    => '1',
			'discord'  => '2',
			'chatwork' => '3',
		};

		$type = ctype_digit( $notification_type ) ? '1' : match ( $notification_type ) {
			'update'     => '2',
			'dev_notify' => '3',
		};

		if ( isset( $tool ) && isset( $type ) ) {
			$create_log_data = [
				'status'    => $status_code,
				'tool'      => $tool,
				'type'      => $type,
				'send_date' => current_time( 'mysql' ),
			];
		}

		return $create_log_data;
	}

	/**
	 * Save Send Chat Tools log data.
	 *
	 * @param array $log_data Current log data.
	 */
	public function save_log( array $log_data ): bool {
		if ( empty( $log_data ) ) {
			return false;
		} else {
			$sct_logs = get_option( $this->add_prefix( 'logs' ) );

			if ( array_key_exists( 999, $sct_logs ) ) {
				unset( $sct_logs[999] );
			}

			return update_option( $this->add_prefix( 'logs' ), array_merge( [ $log_data ], $sct_logs ) );
		}
	}
}
