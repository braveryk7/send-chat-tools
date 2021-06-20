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
class Sct_Connect_Database {
	/**
	 * Constructor.
	 * Gave prefix.
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . Sct_Const_Data::TABLE_NAME;
	}

	/**
	 * Search tables.
	 */
	public function search_table() {
		global $wpdb;
		$get_table = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $this->table_name ) ); // db call ok; no-cache ok.
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

		update_option( 'sct_db_version', Sct_Const_Data::DB_VERSION );
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
			$this->table_name,
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
	 * @var array
	 */
	public function get_log(): array {
		global $wpdb;
		$result = $wpdb->get_results( "SELECT * FROM $this->table_name ORDER BY send_date DESC" ); // phpcs:ignore

		return $result;
	}

	/**
	 * Delete wp_option.
	 */
	public static function delete_db() {
		global $wpdb;

		/* Remove columns from wp_options table */
		delete_option( 'sct_db_version' );
		delete_option( 'sct_iv' );
		delete_option( 'sct_use_user_id' );
		delete_option( 'sct_use_slack' );
		delete_option( 'sct_slack_webhook_url' );
		delete_option( 'sct_send_slack_author' );
		delete_option( 'sct_send_slack_update' );
		delete_option( 'sct_slack_log' );
		delete_option( 'sct_use_chatwork' );
		delete_option( 'sct_chatwork_api_token' );
		delete_option( 'sct_chatwork_room_id' );
		delete_option( 'sct_send_chatwork_author' );
		delete_option( 'sct_send_chatwork_update' );
		delete_option( 'sct_chatwork_log' );
		delete_option( 'sct_use_discord' );
		delete_option( 'sct_discord_webhook_url' );
		delete_option( 'sct_send_discord_author' );
		delete_option( 'sct_send_discord_update' );
		delete_option( 'sct_cron_time' );

		/* Remove wp_sct table */
		$table_name = $wpdb->prefix . Sct_Const_Data::TABLE_NAME;

		$sql = 'DROP TABLE IF EXISTS ' . $table_name;
		$wpdb->query( "${sql}" ); // db call ok; no-cache ok.

		/* Remove cron hooks */
		wp_clear_scheduled_hook( 'sct_update_check' );
	}
}
