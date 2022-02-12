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
	 * Search tables.
	 */
	public function search_table() {
		global $wpdb;
		$get_table = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $this->return_table_name() ) ); // db call ok; no-cache ok.
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

		$sql = 'DROP TABLE IF EXISTS ' . $wpdb->prefix . self::TABLE_NAME;
		$wpdb->query( "${sql}" ); // db call ok; no-cache ok.

		/* Remove cron hooks */
		wp_clear_scheduled_hook( 'sct_update_check' );
	}
}
