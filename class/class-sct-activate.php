<?php
/**
 * Plugin activate.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 1.3.0
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Activate process.
 */
class Sct_Activate extends Sct_Base {
	/**
	 * Register wp_options column.
	 */
	public function register_options() {
		$chat_tools_value = [
			'use'         => false,
			'webhook_url' => '',
			'send_author' => false,
			'send_update' => false,
			'log'         => '',
		];
		$chatwork_value   = [
			'use'         => false,
			'api_token'   => '',
			'room_id'     => '',
			'send_author' => false,
			'send_update' => false,
			'log'         => '',
		];

		$iv = Sct_Encryption::make_vector();

		$options = [
			'slack'      => [ $chat_tools_value ],
			'discord'    => [ $chat_tools_value ],
			'chatwork'   => [ $chatwork_value ],
			'db_version' => self::DB_VERSION,
			'iv'         => $iv,
			'user_id'    => '',
			'cron_time'  => false,
		];

		add_option( $this->add_prefix( 'options' ), $options );
	}
}
