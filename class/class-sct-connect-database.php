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
		$this->table_name = $this->create_table_name();
	}

	/**
	 * Search tables.
	 */
	public function search_table() {
		global $wpdb;
		$get_table = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $this->create_table_name() ) ); // db call ok; no-cache ok.
		if ( null === $get_table ) {
			$this->create_table();
		}
	}

	/**
	 * Create table.
	 */
	private function create_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $this->table_name (
			id int unique NOT NULL AUTO_INCREMENT,
			states smallint(4) UNSIGNED NOT NULL,
			tool smallint(1) UNSIGNED NOT NULL,
			type varchar(255) NOT NULL,
			send_date datetime
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		/* Create columns from wp_options table */
		$options = Sct_Const_Data::OPTION_LIST;
		foreach ( $options as $key => $value ) {
			if ( $this->add_prefx( 'iv' ) === $key ) {
				add_option( $key, Sct_Encryption::make_vector() );
			} else {
				add_option( $key, $value );
			}
		}
	}

	/**
	 * Insert log.
	 *
	 * @param int    $states_code States code.
	 * @param string $tool Use tool number.
	 * @param string $type Type.
	 */
	public function insert_log( int $states_code, string $tool, string $type ) {
		global $wpdb;

		$wpdb->insert(
			$this->create_table_name(),
			[
				'states'    => $states_code,
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
		$options = Sct_Const_Data::OPTION_LIST;
		foreach ( $options as $key => $value ) {
			delete_option( $key );
		}

		/* Remove wp_sct table */
		$table_name = $wpdb->prefix . Sct_Const_Data::TABLE_NAME;

		$sql = 'DROP TABLE IF EXISTS ' . $this->create_table_name();
		$wpdb->query( "${sql}" ); // db call ok; no-cache ok.

		/* Remove cron hooks */
		wp_clear_scheduled_hook( 'sct_update_check' );
	}
}
