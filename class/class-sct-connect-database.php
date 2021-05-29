<?php
/**
 * Admin settings page.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 0.0.1
 */

declare( strict_type = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Connect Database.
 */
class Sct_Connect_Database {
	/**
	 * Delete wp_option.
	 */
	public static function delete_db() {
		global $wpdb;

		delete_option( 'sct_iv' );
		delete_option( 'sct_use_user_id' );
		delete_option( 'sct_use_slack' );
		delete_option( 'sct_slack_webhook_url' );
		delete_option( 'sct_send_slack_author' );
		delete_option( 'sct_slack_log' );
		delete_option( 'sct_use_chatwork' );
		delete_option( 'sct_chatwork_api_token' );
		delete_option( 'sct_chatwork_room_id' );
		delete_option( 'sct_send_chatwork_author' );
		delete_option( 'sct_chatwork_log' );
	}
}
