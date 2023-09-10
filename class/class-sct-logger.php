<?php
/**
 * A class that generates logs and stores them in a database.
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
 * A class that generates logs and stores them in a database.
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
			'update'        => '2',
			'dev_notify'    => '3',
			'login_notify'  => '4',
			'rinker_notify' => '5',
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

	/**
	 * Method for storing individual logs.
	 *
	 * @param object $original_data Comment data.
	 * @param int    $status_code   HTTP status code.
	 * @param string $tool_name     Use tool name.
	 * @param array  $sct_options   Send Chat Tools options.
	 */
	public function individual_log( object $original_data, int $status_code, string $tool_name, array $sct_options ) {
		$logs = [
			$original_data->comment_date => [
				'id'      => $original_data->comment_ID,
				'author'  => $original_data->comment_author,
				'email'   => $original_data->comment_author_email,
				'url'     => $original_data->comment_author_url,
				'comment' => $original_data->comment_content,
				'status'  => $status_code,
			],
		];

		if ( '3' <= count( $sct_options[ $tool_name ]['log'] ) ) {
			array_pop( $sct_options[ $tool_name ]['log'] );
		}

		$sct_options[ $tool_name ]['log'] = $logs + $sct_options[ $tool_name ]['log'];

		$this->result = $this->set_sct_options( $sct_options );

		return $this;
	}
}
