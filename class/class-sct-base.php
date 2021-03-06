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
	protected const API_NAME            = self::PLUGIN_SLUG;
	protected const API_VERSION         = 'v1';
	protected const VERSION             = '1.3.0';
	protected const OPTIONS_COLUMN_NAME = 'options';

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
	 * Developer message.
	 * Use dashboard, notify.
	 *
	 * @var array
	 */
	protected $developer_messages;

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
	protected function get_api_namespace( string $api_name = self::API_NAME, string $api_version = self::API_VERSION ) {
		return "${api_name}/${api_version}";
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
	 * Get WP-cron event name.
	 */
	public function get_wpcron_event_name(): string {
		return self::add_prefix( self::WP_CRON_EVENT_NAME );
	}

	/**
	 * Set & Get $this->developer_messages.
	 */
	protected function get_developer_messages(): array {
		$this->developer_messages = [
			'key'     => plugin_basename( $this->get_plugin_path() ),
			'type'    => 'plugin',
			'title'   => esc_html__( 'Send Chat Tools', 'send-chat-tools' ),
			'message' => [
				__( 'Updated to version 1.3.0!', 'send-chat-tools' ),
				__( 'Here are the main changes...', 'send-chat-tools' ),
				'',
				__( 'Important:', 'send-chat-tools' ),
				__( '- PHP 8.0 and earlier will no longer be supported after next minor update (1.4.0). The update is now scheduled for April.', 'send-chat-tools' ),
				'',
				__( 'Improvements:', 'send-chat-tools' ),
				__( '- The admin panel has been replaced with React for a more modern design using WordPress components that are familiar to everyone.', 'send-chat-tools' ),
				__( '- The chat tool now saves up to 3 individual logs.', 'send-chat-tools' ),
				__( '- The Send Chat Tools widget is now available on the WordPress dashboard.', 'send-chat-tools' ),
				__( '- A warning will be displayed when the next update changes the supported PHP version.', 'send-chat-tools' ),
				__( '- We will send you the updated content vwhen the plugin is updated.', 'send-chat-tools' ),
				__( '- The internal design of PHP has been substantially revised.', 'send-chat-tools' ),
				__( '- Most options starting with "sct" have been removed and replaced by the sct_options column.', 'send-chat-tools' ),
				__( '- The sct table is no longer available and has been unified with the sct_logs column.', 'send-chat-tools' ),
				__( '- The sct_update_notify hook is now publicly available for developers.', 'send-chat-tools' ),
				'',
				__( 'Fixes:', 'send-chat-tools' ),
				__( '- The encryption of API values has been removed.', 'send-chat-tools' ),
				__( '- You will not receive a response if the API value is invalid.', 'send-chat-tools' ),
				__( '- You won\'t be able to change the WordPress settings from the Send Chat Tools settings screen.', 'send-chat-tools' ),
				'',
				__( 'Development:', 'send-chat-tools' ),
				__( '- The development environment has been largely upgraded for future updates.', 'send-chat-tools' ),
				__( '- PHPUnit has been installed to enable a more precise testing environment.', 'send-chat-tools' ),
				__( '- We installed PHPStan to build a static analysis environment.', 'send-chat-tools' ),
				__( '- The development environment has been built by combining WordPress components and TypeScript.', 'send-chat-tools' ),
				__( '- Updated settings for ESLint, Stylelint, and PHP_Codesniffer.', 'send-chat-tools' ),
				__( '- Adopted small tools (zip.sh, git hooks).', 'send-chat-tools' ),
			],
			'url'     => [
				'website'     => 'https://www.braveryk7.com/portfolio/send-chat-tools/',
				'update_page' => 'https://www.braveryk7.com/portfolio/send-chat-tools/#update',
			],
		];

		return $this->developer_messages;
	}

	/**
	 * Send Slack.
	 *
	 * @param array  $options API options.
	 * @param string $id ID(Comment/Update).
	 * @param string $tools Use chat tools prefix.
	 * @param object $comment Comment object.
	 */
	protected function send_tools( array $options, string $id, string $tools, object $comment = null ): void {

		$sct_options = $this->get_sct_options();

		switch ( $tools ) {
			case 'slack':
			case 'discord':
				$url   = $sct_options[ $tools ]['webhook_url'];
				$regex = $this->api_regex( $tools, $url );
				break;
			case 'chatwork':
				$url   = 'https://api.chatwork.com/v2/rooms/' . $sct_options[ $tools ]['room_id'] . '/messages';
				$regex = $this->api_regex( 'chatworkid', $sct_options[ $tools ]['room_id'] );
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

				if ( '3' <= count( $sct_options[ $tools ]['log'] ) ) {
					array_pop( $sct_options[ $tools ]['log'] );
				}
				$sct_options[ $tools ]['log'] = $logs + $sct_options[ $tools ]['log'];
				$this->set_sct_options( $sct_options );
			}
		}

		if ( ! $regex ) {
			$status_code = 1003;
		} elseif ( ! isset( $result->errors ) ) {
			$status_code = $result['response']['code'];
		} else {
			$status_code = 1000;
		}

		if ( 200 !== $status_code && 204 !== $status_code ) {
			require_once dirname( __FILE__ ) . '/class-sct-error-mail.php';
			if ( 'update' === $id ) {
				$send_mail = new Sct_Error_Mail( $status_code, $id );
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
	 * Regex Webhook_url, Api token, room ID.
	 *
	 * @param string $tool tool name.
	 * @param string $value Webhook_url, Api token, room ID.
	 */
	protected function api_regex( string $tool, string $value ) {
		$pattern = '';

		switch ( $tool ) {
			case 'slack':
				$pattern = '/\Ahttps:\/\/hooks.slack.com\/services\/[a-zA-Z0-9]*\/[a-zA-Z0-9]*\/[a-zA-Z0-9]*/';
				break;
			case 'discord':
				$pattern = '/\Ahttps:\/\/discord.com\/api\/webhooks\/[0-9]*\/[a-zA-Z0-9_-]*/';
				break;
			case 'chatworkapi':
				$pattern = '/\A[0-9a-zA-Z]+\z/';
				break;
			case 'chatworkid':
				$pattern = '/\A[0-9]+\z/';
				break;
		}

		return preg_match( $pattern, $value );
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
