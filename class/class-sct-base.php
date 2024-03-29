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
	protected const VERSION              = '1.5.4';
	protected const OPTIONS_COLUMN_NAME  = 'options';
	protected const REQUIRED_PHP_VERSION = '8.0.0';
	protected const OFFICIAL_DIRECTORY   = 'https://wordpress.org/plugins/' . self::PLUGIN_SLUG . '/';

	protected const TABLE_NAME = self::PREFIX;

	protected const ENCRYPT_METHOD = 'AES-256-CBC';

	private const WP_CRON_EVENT_NAME               = 'update_check';
	private const WP_CRON_RINKER_NOTIFY_EVENT_NAME = 'rinker_discontinued_items_check';

	public const OPTIONS_COLUMN = [
		'options',
		'logs',
	];

	private const CHAT_TOOLS = [
		'slack',
		'discord',
		'chatwork',
	];

	protected const OPTIONS_KEY = [
		'slack',
		'discord',
		'chatwork',
		'version',
		'cron_time',
		'ignore_key',
		'rinker_cron_time',
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
		'Cocoon'    => 'puc_external_updates_theme-cocoon-master',
		'SANGO'     => 'puc_external_updates_theme-sango-theme',
		'THE SONIC' => 'puc_external_updates_theme-tsnc-main-theme-updater',
		'JIN:R'     => 'external_theme_updates-jinr',
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
	 * Method to return the value of CHAT_TOOLS.
	 * e.g [ 'slack', 'discord', 'chatwork' ]
	 */
	protected function get_chat_tools(): array {
		return self::CHAT_TOOLS;
	}

	/**
	 * Get WP-cron event name.
	 *
	 * @param string $event_type Event type.
	 */
	public static function get_wpcron_event_name( string $event_type ): string {
		return match ( $event_type ) {
			'update_notify' => self::add_prefix( self::WP_CRON_EVENT_NAME ),
			'rinker_notify' => self::add_prefix( self::WP_CRON_RINKER_NOTIFY_EVENT_NAME ),
		};
	}

	/**
	 * A common method that calls the class for each chat tool.
	 *
	 * @param string       $tool_name   Tool name.
	 * @param string       $method_name Method name.
	 * @param string       $type        Comment ID/update/dev_notify/login_notify.
	 * @param object|array $data        An object or array for generating data to be sent.
	 */
	protected function call_chat_tool_class( string $tool_name, string $method_name, string $type, object | array $data ): void {
		$class = match ( $tool_name ) {
			'slack'    => 'Sct_Slack',
			'discord'  => 'Sct_Discord',
			'chatwork' => 'Sct_Chatwork',
		};

		$class::get_instance()
			?->set_notification_type_original_data( $method_name, $data )
			?->$method_name()
			?->generate_header()
			?->send_tools( $type, $tool_name );
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
				__( 'Updated to version 1.5.4!', 'send-chat-tools' ),
				__( 'Here are the main changes...', 'send-chat-tools' ),
				'',
				__( 'Important:', 'send-chat-tools' ),
				__( '- Active support for PHP 8.1 ends November 25, 2023. Therefore, PHP 8.2 will become a requirement around November 2023.', 'send-chat-tools' ),
				'',
				__( 'Improvements:', 'send-chat-tools' ),
				__( '- WordPress 6.3.1 is now supported.', 'send-chat-tools' ),
				'',
				__( 'Fixes:', 'send-chat-tools' ),
				__( '- Fixed deprecated options in new WordPress packages.', 'send-chat-tools' ),
				'',
				__( 'Development:', 'send-chat-tools' ),
				__( '- Existing packages have been modernized.', 'send-chat-tools' ),
			],
			'url'     => [
				'website'     => 'https://www.braveryk7.com/portfolio/send-chat-tools/',
				'update_page' => 'https://www.braveryk7.com/portfolio/send-chat-tools/#update',
			],
		];
	}

	/**
	 * Create and save log data.
	 *
	 * @param int    $status_code HTTP status code.
	 * @param string $tool_name Use tool name.
	 * @param string $notification_type Comment, Update.
	 */
	protected function logger( int $status_code, string $tool_name, string $notification_type ): bool {
		return Sct_Logger::get_instance()
				?->create_log( $status_code, $tool_name, $notification_type )
				?->save_log()
				?->is_saved();
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
