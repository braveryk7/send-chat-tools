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
	 * Store the log data generate.
	 *
	 * @var array $log Log data.
	 */
	private $log;

	/**
	 * Store the bool value of update_option.
	 *
	 * @var bool $result
	 */
	public $result;

	/**
	 * Constructor.
	 */
	private function __construct() {
		return $this;
	}

	/**
	 * Instantiate and return itself.
	 */
	public static function get_instance(): Sct_Logger {
		return new self();
	}

	/**
	 * Create log format.
	 *
	 * @param int    $status_code HTTP status code.
	 * @param string $tool_name Use tool name.
	 * @param string $notification_type Comment, Update.
	 */
	public function create_log( int $status_code, string $tool_name, string $notification_type ): Sct_Logger {
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
			$this->log = [
				'status'    => $status_code,
				'tool'      => $tool,
				'type'      => $type,
				'send_date' => current_time( 'mysql' ),
			];
		}

		return $this;
	}

	/**
	 * Save Send Chat Tools log data.
	 */
	public function save_log(): Sct_Logger {
		if ( empty( $this->log ) ) {
			$this->result = false;
		} else {
			$sct_logs = get_option( $this->add_prefix( 'logs' ) );

			if ( array_key_exists( 299, $sct_logs ) ) {
				foreach ( array_keys( $sct_logs ) as $key ) {
					if ( $key >= 299 ) {
						unset( $sct_logs[ $key ] );
					}
				}
			}

			$this->result = update_option( $this->add_prefix( 'logs' ), array_merge( [ $this->log ], $sct_logs ) );
		}

		return $this;
	}

	/**
	 * Returns whether update_option was successful or failure.
	 */
	public function is_saved() {
		return $this->result;
	}
}
