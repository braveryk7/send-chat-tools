<?php
/**
 * Admin settings page.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 0.0.1
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Connect Database.
 */
class Sct_Connect_Database extends Sct_Base {
	/**
	 * Constructor.
	 * Gave prefix.
	 */
	public function __construct() {
		$this->table_name = $this->return_table_name();
	}

	/**
	 * Search tables.
	 */
	public function search_table() {
		global $wpdb;
		$get_table = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $this->return_table_name() ) ); // db call ok; no-cache ok.
	}

	/**
	 * Insert log.
	 *
	 * @param int    $status_code Status code.
	 * @param string $tool Use tool number.
	 * @param string $type Type.
	 */
	public function insert_log( int $status_code, string $tool, string $type ) {
		global $wpdb;

		$wpdb->insert(
			$this->return_table_name(),
			[
				'states'    => $status_code,
				'tool'      => $tool,
				'type'      => $type,
				'send_date' => current_time( 'mysql' ),
			]
		); // db call ok.
	}

	/**
	 * Get log data.
	 *
	 * @param int $limit Get column limit.
	 * @var array
	 */
	public function get_log( int $limit = 100 ): array {
		global $wpdb;
		$result = $wpdb->get_results( // phpcs:ignore
			$wpdb->prepare( "SELECT * FROM $this->table_name ORDER BY send_date DESC LIMIT %d", $limit ) // phpcs:ignore
		); // phpcs:ignore

		return $result;
	}

	/**
	 * Delete wp_option.
	 */
	public static function delete_db() {
		global $wpdb;

		/* Remove columns from wp_options table */
		$options = self::OPTIONS_COLUMN;
		foreach ( $options as $key ) {
			delete_option( $key );
		}

		$sql = 'DROP TABLE IF EXISTS ' . $this->return_table_name();
		$wpdb->query( "${sql}" ); // db call ok; no-cache ok.

		/* Remove cron hooks */
		wp_clear_scheduled_hook( 'sct_update_check' );
	}
}
