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
		register_activation_hook( $this->get_plugin_path(), [ $this, 'register_options' ] );
		add_action( 'init', [ $this, 'option_check' ] );
		add_action( 'init', [ $this, 'migration_options' ], 5 );
		add_filter( 'sct_developer_notify', [ $this, 'developer_message' ] );
	}

	/**
	 * Register wp_options column.
	 */
	public function register_options(): void {
		$chat_tools_value = [
			'use'         => false,
			'webhook_url' => '',
			'send_author' => false,
			'send_update' => false,
			'log'         => [],
		];
		$chatwork_value   = [
			'use'         => false,
			'api_token'   => '',
			'room_id'     => '',
			'send_author' => false,
			'send_update' => false,
			'log'         => [],
		];

		$options = [
			'slack'      => $chat_tools_value,
			'discord'    => $chat_tools_value,
			'chatwork'   => $chatwork_value,
			'version'    => self::VERSION,
			'cron_time'  => '18:00',
			'ignore_key' => [],
		];

		add_option( $this->add_prefix( 'options' ), $options );
		add_option( $this->add_prefix( 'logs' ), [] );
	}

	/**
	 * Send Chat Tools option key check.
	 */
	public function option_check() {
		$sct_options = $this->get_sct_options();

		if ( self::VERSION !== $sct_options['version'] ) {
			foreach ( self::OPTIONS_KEY as $key_name ) {
				if ( ! array_key_exists( $key_name, $sct_options ) ) {
					if ( 'ignore_key' === $key_name ) {
						$sct_options[ $key_name ] = [];
					}
				}
			}
			$this->set_sct_options( $sct_options );
		}
	}

	/**
	 * Uninstall wp_options column.
	 */
	public static function uninstall_options(): void {
		foreach ( self::OPTIONS_COLUMN as $option_name ) {
			delete_option( self::add_prefix( $option_name ) );
		}
		if ( wp_get_scheduled_event( self::get_wpcron_event_name() ) ) {
			wp_clear_scheduled_hook( self::get_wpcron_event_name() );
		}
	}

	/**
	 * Fix use old wp_options -> create new options and migration.
	 */
	public function migration_options(): void {
		$sct_options = get_option( $this->add_prefix( 'options' ) );
		if ( ! $sct_options ) {
			$this->register_options();

			$old_options = [];
			foreach ( self::OLD_OPTIONS_COLUMN as $key ) {
				$old_options[ $key ] = get_option( $this->add_prefix( $key ) );
				delete_option( $this->add_prefix( $key ) );
			}

			foreach ( $old_options as $old_key => $old_value ) {
				switch ( $old_key ) {
					case 'use_slack':
						$sct_options['slack']['use'] = (bool) $old_value;
						break;
					case 'use_discord':
						$sct_options['discord']['use'] = (bool) $old_value;
						break;
					case 'use_chatwork':
						$sct_options['chatwork']['use'] = (bool) $old_value;
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
						$sct_options['slack']['send_author'] = (bool) $old_value;
						break;
					case 'send_discord_author':
						$sct_options['discord']['send_author'] = (bool) $old_value;
						break;
					case 'send_chatwork_author':
						$sct_options['chatwork']['send_author'] = (bool) $old_value;
						break;
					case 'send_slack_update':
						$sct_options['slack']['send_update'] = (bool) $old_value;
						break;
					case 'send_discord_update':
						$sct_options['discord']['send_update'] = (bool) $old_value;
						break;
					case 'send_chatwork_update':
						$sct_options['chatwork']['send_update'] = (bool) $old_value;
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
						$sct_options['version'] = $old_value;
						break;
					case 'iv':
						$sct_options['iv'] = $old_value;
						break;
					case 'use_user_id':
						$sct_options['user_id'] = (int) $old_value;
						break;
					case 'cron_time':
						$sct_options['cron_time'] = $old_value;
						break;
				}
			}
			$sct_options['old_settings'] = true;
			update_option( $this->add_prefix( 'options' ), $sct_options );

			$this->crypto2plain( $sct_options );
		}

		/**
		 * If sct table exists.
		 */
		if ( ! get_option( $this->add_prefix( 'logs' ) ) ) {
			global $wpdb;
			$get_table = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $this->get_table_name() ) ); // db call ok; no-cache ok.

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

				$sql = 'DROP TABLE IF EXISTS ' . $wpdb->prefix . self::TABLE_NAME;
				$wpdb->query( "${sql}" ); // db call ok; no-cache ok.
			}
		}
	}

	/**
	 * Encrypt -> Plain.
	 *
	 * @param array $sct_options Send Chat Tools options.
	 */
	private function crypto2plain( array $sct_options ): void {
		if ( $sct_options['user_id'] ) {
			$tools      = [ 'slack', 'discord', 'chatwork' ];
			$encryption = new Sct_Encryption();

			foreach ( $tools as $tool ) {
				if ( 'slack' === $tool || 'discord' === $tool ) {
					$sct_options[ $tool ]['webhook_url'] = $encryption->decrypt( $sct_options[ $tool ]['webhook_url'] );
				} elseif ( 'chatwork' === $tool ) {
					$sct_options['chatwork']['api_token'] = $encryption->decrypt( $sct_options['chatwork']['api_token'] );
					$sct_options['chatwork']['room_id']   = $encryption->decrypt( $sct_options['chatwork']['room_id'] );
				}
			}
		}

		unset( $sct_options['iv'] );
		unset( $sct_options['user_id'] );

		$this->set_sct_options( $sct_options );
	}

	/**
	 * Developer notify check.
	 *
	 * @param array $developer_message Use developer notify.
	 */
	public function developer_message( $developer_message ) {
		$sct_options = $this->get_sct_options();
		if ( array_key_exists( 'version', $sct_options ) && self::VERSION !== $sct_options['version'] ) {
			$developer_message[] = $this->get_developer_messages();

			$sct_options['version'] = self::VERSION;
			$this->set_sct_options( $sct_options );

			return $developer_message;
		}
	}
}
