<?php
/**
 * Send Chat Tools base class.
 *
 * @author     Ken-chan
 * @package    WordPress
 * @subpackage Send Chat Tools
 * @since      1.3.0
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Send Chat Tools base class.
 */
class Sct_Base {
	protected const PREFIX              = 'sct';
	protected const PLUGIN_SLUG         = 'send-chat-tools';
	protected const PLUGIN_NAME         = 'Send Chat Tools';
	protected const PLUGIN_FILE         = self::PLUGIN_SLUG . '.php';
	protected const DB_VERSION          = '1.0';
	protected const OPTIONS_COLUMN_NAME = 'options';

	protected const TABLE_NAME = self::PREFIX;

	protected const ENCRYPT_METHOD = 'AES-256-CBC';

	public const OPTIONS_COLUMN = [
		// Use database.
		'db_version',
		'iv',
		'use_user_id',
		// Use tools.
		'use_slack',
		'use_discord',
		'use_chatwork',
		// Tools API.
		'slack_webhook_url',
		'discord_webhook_url',
		'chatwork_api_token',
		'chatwork_room_id',
		// Send author.
		'send_slack_author',
		'send_discord_author',
		'send_chatwork_author',
		// Send update notification.
		'send_slack_update',
		'send_discord_update',
		'send_chatwork_update',
		// Send log.
		'slack_log',
		'discord_log',
		'chatwork_log',
		// Cron time.
		'cron_time',
	];

	protected const THEME_OPTION_NAME  = [
		'Cocoon'    => 'external_theme_updates-cocoon-master',
		'SANGO'     => 'puc_external_updates_theme-sango-theme',
		'THE SONIC' => 'puc_external_updates_theme-tsnc-main-theme-updater',
	];
	protected const PLUGIN_OPTION_NAME = [
		'Rinker'                     => 'external_updates-yyi-rinker',
		'THE SONIC SEO Plugin'       => 'external_updates-tsnc-seo-plugin',
		'THE SONIC Gutenberg Blocks' => 'external_updates-tsnc-gutenberg-plugin',
		'THE SONIC COPIA'            => 'external_updates-tsnc-cmp-plugin',
		'SANGO Gutenberg'            => 'external_updates-sango-theme-gutenberg',
	];

	/**
	 * Return add prefix.
	 *
	 * @param string $value After prefix value.
	 */
	public static function add_prefix( string $value ): string {
		return self::PREFIX . '_' . $value;
	}

	/**
	 * Return plugin url.
	 * e.g. https://expamle.com/wp-content/plugins/send-chat-tools
	 *
	 * @param string $plugin_name Plugin name.
	 */
	protected function return_plugin_url( string $plugin_name ): string {
		return WP_PLUGIN_URL . '/' . $plugin_name;
	}

	/**
	 * Return plugin directory.
	 * e.g. /DocumentRoot/wp-content/plugins/send-chat-tools
	 *
	 * @param string $plugin_name Plugin name.
	 */
	protected function return_plugin_dir( string $plugin_name ): string {
		return WP_PLUGIN_DIR . '/' . $plugin_name;
	}

	/**
	 * Return plugin file path.
	 * e.g. /DocumentRoot/wp-content/plugins/send-chat-tools/send-chat-tools.php
	 */
	protected function return_plugin_path(): string {
		return $this->return_plugin_dir( self::PLUGIN_SLUG ) . '/' . self::PLUGIN_FILE;
	}

	/**
	 * Return option group.
	 * Use register_setting.
	 * e.g. send-chat-tools-settings
	 */
	protected function return_option_group(): string {
		return self::PLUGIN_SLUG . '-settings';
	}

	/**
	 * Return Database table name.
	 */
	protected function return_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_NAME;
	}

	/**
	 * Get sct_options.
	 */
	protected static function get_sct_options(): string {
		return get_option( $this->add_prefix( self::OPTIONS_COLUMN_NAME ) );
	}

	/**
	 * Send Slack.
	 *
	 * @param array  $options API options.
	 * @param string $id ID(Comment/Update).
	 * @param string $tools Use chat tools prefix.
	 */
	protected function send_tools( array $options, string $id, string $tools ) {
		require_once dirname( __FILE__ ) . '/class-sct-encryption.php';

		switch ( $tools ) {
			case 'slack':
				$url = Sct_Encryption::decrypt( get_option( $this->add_prefix( 'slack_webhook_url' ) ) );
				$log = $this->add_prefix( 'slack_log' );
				break;
			case 'discord':
				$url = Sct_Encryption::decrypt( get_option( $this->add_prefix( 'discord_webhook_url' ) ) );
				$log = $this->add_prefix( 'discord_log' );
				break;
			case 'chatwork':
				$url = 'https://api.chatwork.com/v2/rooms/' . Sct_Encryption::decrypt( get_option( $this->add_prefix( 'chatwork_room_id' ) ) ) . '/messages';
				$log = $this->add_prefix( 'chatwork_log' );
				break;
		}

		$result = wp_remote_post( $url, $options );
		update_option( $log, $result );

		if ( ! isset( $result->errors ) ) {
			$status_code = $result['response']['code'];
		} else {
			$status_code = 1000;
		}
		if ( 200 !== $status_code && 204 !== $status_code ) {
			require_once dirname( __FILE__ ) . '/class-sct-error-mail.php';
			if ( 'update' === $id ) {
				$send_mail = new Sct_Error_mail( $status_code, $id );
				$send_mail->update_contents( $options );
			} else {
				$send_mail = new Sct_Error_Mail( $status_code, $id );
				$send_mail->make_contents();
			}
		}

		$logger = new Sct_Logger();
		$logger->create_log( $status_code, $tools, $id );
	}

	/**
	 * Output browser console.
	 * WARNING: Use debag only!
	 *
	 * @param string|int|float|boolean|array|object $value Output data.
	 */
	protected function console( $value ): void {
		echo '<script>console.log(' . wp_json_encode( $value ) . ');</script>';
	}
}
