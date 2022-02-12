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
	 * Constructor.
	 */
	public function __construct() {
		register_activation_hook( $this->return_plugin_path(), [ $this, 'register_options' ] );
		add_action( 'wp_loaded', [ $this, 'migration_options' ] );
	}

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
			'log'         => [],
		];

		$encryption = new Sct_Encryption();
		$iv         = $encryption->make_vector();

		$options = [
			'slack'      => $chat_tools_value,
			'discord'    => $chat_tools_value,
			'chatwork'   => $chatwork_value,
			'db_version' => self::DB_VERSION,
			'iv'         => $iv,
			'user_id'    => '',
			'cron_time'  => false,
		];

		add_option( $this->add_prefix( 'options' ), $options );
	}

	/**
	 * Fix use old wp_options -> create new options and migration.
	 */
	public function migration_options() {
		$sct_options = get_option( $this->add_prefix( 'options' ) );
		if ( ! $sct_options ) {
			$this->register_options();

			$old_options = [];
			foreach ( self::OPTIONS_COLUMN as $key ) {
				$old_options[ $key ] = get_option( $this->add_prefix( $key ) );
			}

			foreach ( $old_options as $old_key => $old_value ) {
				switch ( $old_key ) {
					case 'use_slack':
						$sct_options['slack']['use'] = $old_value;
						break;
					case 'use_discord':
						$sct_options['discord']['use'] = $old_value;
						break;
					case 'use_chatwork':
						$sct_options['chatwork']['use'] = $old_value;
						break;
					case 'slack_webhook_url':
						$sct_options['slack']['webhook_url'] = $old_value;
						break;
					case 'discord_webhook_url':
						$sct_options['discord']['webhook_url'] = $old_value;
						break;
					case 'chatwork_api_token':
						$sct_options['chatwork']['api_token'] = $old_value;
						break;
					case 'chatwork_room_id':
						$sct_options['chatwork']['room_id'] = $old_value;
						break;
					case 'send_slack_author':
						$sct_options['slack']['send_author'] = $old_value;
						break;
					case 'send_discord_author':
						$sct_options['discord']['send_author'] = $old_value;
						break;
					case 'send_chatwork_author':
						$sct_options['chatwork']['send_author'] = $old_value;
						break;
					case 'send_slack_update':
						$sct_options['slack']['send_update'] = $old_value;
						break;
					case 'send_discord_update':
						$sct_options['discord']['send_update'] = $old_value;
						break;
					case 'send_chatwork_update':
						$sct_options['chatwork']['send_update'] = $old_value;
						break;
					case 'slack_log':
						$sct_options['slack']['log'] = [];
						break;
					case 'discord_log':
						$sct_options['discord']['log'] = [];
						break;
					case 'chatwork_log':
						$sct_options['chatwork']['log'] = [];
						break;
					case 'db_version':
						$sct_options['db_version'] = $old_value;
						break;
					case 'iv':
						$sct_options['iv'] = $old_value;
						break;
					case 'use_user_id':
						$sct_options['user_id'] = $old_value;
						break;
					case 'cron_time':
						$sct_options['cron_time'] = $old_value;
						break;
				}
			}
			update_option( $this->add_prefix( 'options' ), $sct_options );

			$this->crypto2plain( $sct_options );
		}

		/**
		 * If sct table exists.
		 */
		if ( ! get_option( $this->add_prefix( 'logs' ) ) ) {
			global $wpdb;
			$get_table = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $this->return_table_name() ) ); // db call ok; no-cache ok.

			if ( $get_table ) {
				$result = $wpdb->get_results( 'SELECT * FROM wp_sct' ); // phpcs:ignore

				$sct_logs = [];
				foreach ( $result as $key => $value ) {
					$sct_logs[ $value->id ] = [
						'status'    => (int) $value->states,
						'type'      => $value->type,
						'tool'      => $value->tool,
						'send_date' => $value->send_date,
					];
					unset( $result[ $key ]->id );

				}
				foreach ( $sct_logs as $key => $value ) {
					$abc[ $key ] = $value['send_date'];
				}
				array_multisort( $abc, SORT_DESC, SORT_REGULAR, $sct_logs );

				update_option( $this->add_prefix( 'logs' ), $sct_logs );
			}
		}
	}

	/**
	 * Encrypt -> Plain.
	 *
	 * @param array $sct_options Send Chat Tools options.
	 */
	private function crypto2plain( array $sct_options ) {
		$tools = [ 'slack', 'discord', 'chatwork' ];

		$encryption = new Sct_Encryption();

		foreach ( $tools as $tool ) {
			'chatwork' !== $tool ? $api_key = 'webhook_url' : $api_key = 'api_token';
			$api_value                      = $encryption->decrypt( $sct_options[ $tool ][ $api_key ] );

			if ( $api_value ) {
				$sct_options[ $tool ][ $api_key ] = $api_value;
			}

			if ( 'chatwork' === $tool ) {
				$this->console( $sct_options[ $tool ]['room_id'] );
				$room_id = $encryption->decrypt( $sct_options[ $tool ]['room_id'] );
				if ( $room_id ) {
					$sct_options[ $tool ]['room_id'] = $room_id;
				}
			}
		}
		$this->set_sct_options( $sct_options );
	}
}
