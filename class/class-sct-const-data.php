<?php
/**
 * Admin settings page.
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
 * Constant data.
 */
class Sct_Const_Data {
	/**
	 * Database Table prefix.
	 */
	const TABLE_NAME = 'sct';

	/**
	 * Database version.
	 */
	const DB_VERSION = '1.0';

	/**
	 * List used in wp_option
	 */
	const OPTION_LIST = [
		// Use database.
		'sct_db_version'           => self::DB_VERSION,

		// Use encrypt & decrypt.
		'sct_iv'                   => '',
		'sct_use_user_id'          => '',

		// Use tools.
		'sct_use_slack'            => '0',
		'sct_use_discord'          => '0',
		'sct_use_chatwork'         => '0',

		// Tools API.
		'sct_slack_webhook_url'    => '',
		'sct_discord_webhook_url'  => '',
		'sct_chatwork_api_token'   => '',
		'sct_chatwork_room_id'     => '',

		// Send author.
		'sct_send_slack_author'    => '0',
		'sct_send_discord_author'  => '0',
		'sct_send_chatwork_author' => '0',

		// Send update notification.
		'sct_send_slack_update'    => '0',
		'sct_send_discord_update'  => '0',
		'sct_send_chatwork_update' => '0',

		// Send log.
		'sct_slack_log'            => '',
		'sct_discord_log'          => '',
		'sct_chatwork_log'         => '',

		// Cron time.
		'sct_cron_time'            => '',
	];
}
