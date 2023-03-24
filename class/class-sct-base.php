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
	protected const PREFIX               = 'sct';
	protected const PLUGIN_SLUG          = 'send-chat-tools';
	protected const PLUGIN_NAME          = 'Send Chat Tools';
	protected const PLUGIN_FILE          = self::PLUGIN_SLUG . '.php';
	protected const API_NAME             = self::PLUGIN_SLUG;
	protected const API_VERSION          = 'v1';
	protected const VERSION              = '1.3.1';
	protected const OPTIONS_COLUMN_NAME  = 'options';
	protected const REQUIRED_PHP_VERSION = '8.0.0';
	protected const OFFICIAL_DIRECTORY   = 'https://wordpress.org/plugins/' . self::PLUGIN_SLUG . '/';

	protected const TABLE_NAME = self::PREFIX;

	protected const ENCRYPT_METHOD = 'AES-256-CBC';

	public const WP_CRON_EVENT_NAME = 'update_check';

	public const OPTIONS_COLUMN = [
		'options',
		'logs',
	];

	protected const OPTIONS_KEY = [
		'slack',
		'discord',
		'chatwork',
		'version',
		'cron_time',
		'ignore_key',
	];

	public const OLD_OPTIONS_COLUMN = [
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
	 * Return plugin name.
	 * e.g. Send Chat Tools
	 */
	public static function get_plugin_name(): string {
		return self::PLUGIN_NAME;
	}

	/**
	 * Return plugin url.
	 * e.g. https://expamle.com/wp-content/plugins/send-chat-tools
	 *
	 * @param string $plugin_name Plugin name.
	 */
	protected function get_plugin_url( string $plugin_name ): string {
		return WP_PLUGIN_URL . '/' . $plugin_name;
	}

	/**
	 * Return plugin directory.
	 * e.g. /DocumentRoot/wp-content/plugins/send-chat-tools
	 *
	 * @param string $plugin_name Plugin name.
	 */
	protected function get_plugin_dir( string $plugin_name ): string {
		return WP_PLUGIN_DIR . '/' . $plugin_name;
	}

	/**
	 * Return plugin file path.
	 * e.g. /DocumentRoot/wp-content/plugins/send-chat-tools/send-chat-tools.php
	 */
	protected function get_plugin_path(): string {
		return $this->get_plugin_dir( self::PLUGIN_SLUG ) . '/' . self::PLUGIN_FILE;
	}

	/**
	 * Return WP-API parameter.
	 * e.g. send-chat-tools/v1
	 *
	 * @param string $api_name    Plugin unique name.
	 * @param string $api_version Plugin API version.
	 */
	protected function get_api_namespace( string $api_name = self::API_NAME, string $api_version = self::API_VERSION ): string {
		return "{$api_name}/{$api_version}";
	}

	/**
	 * Return option group.
	 * Use register_setting.
	 * e.g. send-chat-tools-settings
	 */
	protected function get_option_group(): string {
		return self::PLUGIN_SLUG . '-settings';
	}

	/**
	 * Return Database table name.
	 */
	protected function get_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_NAME;
	}

	/**
	 * Get sct_options.
	 */
	protected function get_sct_options(): array {
		return get_option( $this->add_prefix( self::OPTIONS_COLUMN_NAME ) );
	}

	/**
	 * Set sct_options.
	 *
	 * @param array $sct_options sct_options column data.
	 */
	protected function set_sct_options( array $sct_options ): void {
		update_option( $this->add_prefix( self::OPTIONS_COLUMN_NAME ), $sct_options );
	}

	/**
	 * Get required PHP version.
	 */
	public static function get_required_php_version(): string {
		return self::REQUIRED_PHP_VERSION;
	}

	/**
	 * Get WordPress official directory url
	 */
	protected function get_official_directory(): string {
		return self::OFFICIAL_DIRECTORY;
	}

	/**
	 * Get WP-cron event name.
	 */
	public static function get_wpcron_event_name(): string {
		return self::add_prefix( self::WP_CRON_EVENT_NAME );
	}

	/**
	 * Set & Get $this->developer_messages.
	 */
	protected function get_developer_messages(): array {
		return [
			'key'     => plugin_basename( $this->get_plugin_path() ),
			'type'    => 'plugin',
			'title'   => esc_html__( 'Send Chat Tools', 'send-chat-tools' ),
			'message' => [
				__( 'Updated to version 1.3.1!', 'send-chat-tools' ),
				__( 'Here are the main changes...', 'send-chat-tools' ),
				'',
				__( 'Important:', 'send-chat-tools' ),
				__( '- Please update your PHP version to 8.0 or higher. The next update will not allow use with PHP 8.0 or lower and will automatically disable the plugin. This change is scheduled around April 15th.', 'send-chat-tools' ),
				__( '- The limit for logs will be reduced from 1,000 to 300 in the next update. Please make a backup if needed.', 'send-chat-tools' ),
				'',
				__( 'Improvements:', 'send-chat-tools' ),
				__( '- It is now ready for WordPress 6.2.', 'send-chat-tools' ),
				__( '- The new functionality to reject certain developer messages has been implemented.', 'send-chat-tools' ),
				__( '- The @wordpress/api-fetch package has been adopted for quicker and safer use of the setting page.', 'send-chat-tools' ),
				__( '- The design of the screen during data loading has been simplified.', 'send-chat-tools' ),
				__( '- We have moved the time settings for update notification to the Basic Settings tab. With this change, the Update Notification tab has been eliminated.', 'send-chat-tools' ),
				__( '- An output function for logs has been installed. You can choose to copy to the clipboard and output to text/CSV.', 'send-chat-tools' ),
				__( '- We now show an error message when data retrieval failures occur in the WordPress REST API.', 'send-chat-tools' ),
				'',
				__( 'Fixes:', 'send-chat-tools' ),
				__( '- We have fixed a problem that was causing API errors being output to PHP.', 'send-chat-tools' ),
				__( '- Refactoring of code has been implemented.', 'send-chat-tools' ),
				__( '- Some legacy code has been removed.', 'send-chat-tools' ),
				__( '- We have resolved an issue that caused a critical error in some environments during updates.', 'send-chat-tools' ),
				'',
				__( 'Development:', 'send-chat-tools' ),
				__( '- The development environment has been reorganized.', 'send-chat-tools' ),
			],
			'url'     => [
				'website'     => 'https://www.braveryk7.com/portfolio/send-chat-tools/',
				'update_page' => 'https://www.braveryk7.com/portfolio/send-chat-tools/#update',
			],
		];
	}

	/**
	 * Send Slack.
	 *
	 * @param array  $options API options.
	 * @param string $id      ID(Comment/Update).
	 * @param string $tool    Use chat tools prefix.
	 * @param object $comment Comment object.
	 */
	protected function send_tools( array $options, string $id, string $tool, object $comment = null ): bool {

		$sct_options = $this->get_sct_options();

		switch ( $tool ) {
			case 'slack':
			case 'discord':
				$url   = $sct_options[ $tool ]['webhook_url'];
				$regex = $this->api_regex( $tool, $url );
				break;
			case 'chatwork':
				$url   = 'https://api.chatwork.com/v2/rooms/' . $sct_options[ $tool ]['room_id'] . '/messages';
				$regex = $this->api_regex( 'chatworkid', $sct_options[ $tool ]['room_id'] );
				break;
		}

		if ( $regex ) {
			$result = wp_remote_post( $url, $options );

			if ( ! is_null( $comment ) ) {
				$logs = [
					$comment->comment_date => [
						'id'      => $comment->comment_ID,
						'author'  => $comment->comment_author,
						'email'   => $comment->comment_author_email,
						'url'     => $comment->comment_author_url,
						'comment' => $comment->comment_content,
						'status'  => $result['response']['code'],
					],
				];

				if ( '3' <= count( $sct_options[ $tool ]['log'] ) ) {
					array_pop( $sct_options[ $tool ]['log'] );
				}
				$sct_options[ $tool ]['log'] = $logs + $sct_options[ $tool ]['log'];
				$this->set_sct_options( $sct_options );
			}
		}

		$status_code = match ( true ) {
			! isset( $result->errors ) => $result['response']['code'],
			! $regex                   => 1003,
			default                    => 1000,
		};

		if ( 200 !== $status_code && 204 !== $status_code ) {
			require_once dirname( __FILE__ ) . '/class-sct-error-mail.php';
			if ( 'update' === $id ) {
				$send_mail = new Sct_Error_Mail( $status_code, $id, $tool );
				$send_mail->send_mail( ...$send_mail->update_contents( $options['plain_data'] ) );
			} else {
				$send_mail = new Sct_Error_Mail( $status_code, $id, $tool );
				$send_mail->send_mail( ...$send_mail->generate_contents() );
			}
		}

		return $this->logger( $status_code, $tool, $id );
	}

	/**
	 * Create and save log data.
	 *
	 * @param int    $status_code HTTP status code.
	 * @param string $tool_name Use tool name.
	 * @param string $notification_type Comment, Update.
	 */
	protected function logger( int $status_code, string $tool_name, string $notification_type ): bool {
		$logger   = new Sct_Logger();
		$log_data = $logger->create_log( $status_code, $tool_name, $notification_type );
		$result   = $logger->save_log( $log_data );

		return $result;
	}

	/**
	 * Regex Webhook_url, Api token, room ID.
	 *
	 * @param string $tool tool name.
	 * @param string $value Webhook_url, Api token, room ID.
	 */
	protected function api_regex( string $tool, string $value ): ?bool {
		$pattern = match ( $tool ) {
			'slack'       => '/\Ahttps:\/\/hooks.slack.com\/services\/[a-zA-Z0-9]*\/[a-zA-Z0-9]*\/[a-zA-Z0-9]*/',
			'discord'     => '/\Ahttps:\/\/discord.com\/api\/webhooks\/[0-9]*\/[a-zA-Z0-9_-]*/',
			'chatworkapi' => '/\A[0-9a-zA-Z]+\z/',
			'chatworkid'  => '/\A[0-9]+\z/',
			default       => null,
		};

		return is_null( $pattern ) ? null : ( preg_match( $pattern, $value ) ? true : false );
	}

	/**
	 * Output browser console.
	 * WARNING: Use debag only!
	 *
	 * @param mixed $value Output data.
	 */
	protected function console( mixed $value ): void {
		echo '<script>console.log(' . wp_json_encode( $value ) . ');</script>';
	}
}
