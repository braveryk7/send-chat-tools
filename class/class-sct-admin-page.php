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
 * Return admin settings page.
 */
class Sct_Admin_Page extends Sct_Base {
	/**
	 * WordPress hook.
	 * Add settings page link in admin page.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'add_scripts' ] );
		add_action( 'rest_api_init', [ $this, 'register' ] );
		add_action( 'admin_head-settings_page_send-chat-tools-settings', [ $this, 'include_css' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( $this->return_plugin_path() ), [ $this, 'add_settings_links' ] );
	}

	/**
	 * Add Setting menu.
	 */
	public function add_menu() {
		add_options_page(
			__( 'Send Chat Tools', 'send-chat-tools' ),
			__( 'Send Chat Tools', 'send-chat-tools' ),
			'administrator',
			self::PLUGIN_SLUG,
			[ $this, 'settings_page' ],
		);
	}

	/**
	 * Add configuration link to plugin page.
	 *
	 * @param array|string $links plugin page setting links.
	 */
	public function add_settings_links( array $links ): array {
		$add_link = '<a href="options-general.php?page=' . self::PLUGIN_SLUG . '">' . __( 'Settings', 'send-chat-tools' ) . '</a>';
		array_unshift( $links, $add_link );
		return $links;
	}

	/**
	 * Include CSS in Send Chat Tools settings page.
	 */
	public function include_css() {
	}

	/**
	 * Include JS in Send Chat Tools settings page.
	 *
	 * @param string $hook_shuffix WordPress hook_shuffix.
	 */
	public function add_scripts( string $hook_shuffix ) {
		if ( 'settings_page_' . self::PLUGIN_SLUG !== $hook_shuffix ) {
			return;
		}

		$assets = require_once $this->return_plugin_dir( 'send-chat-tools' ) . '/build/index.asset.php';

		wp_enqueue_style(
			$this->add_prefix( 'style' ),
			$this->return_plugin_url( self::PLUGIN_SLUG ) . '/build/index.css',
			[ 'wp-components' ],
			$assets['version'],
		);

		wp_enqueue_script(
			$this->add_prefix( 'script' ),
			$this->return_plugin_url( self::PLUGIN_SLUG ) . '/build/index.js',
			$assets['dependencies'],
			$assets['version'],
			true
		);
	}

	/**
	 * Set register.
	 */
	public function register() {
		register_setting(
			$this->return_option_group(),
			$this->add_prefix( 'options' ),
			[
				'show_in_rest' => [
					'schema' => [
						'type'       => 'object',
						'properties' => [
							'slack'      => [],
							'discord'    => [],
							'chatwork'   => [],
							'db_version' => [],
							'iv'         => [],
							'user_id'    => [],
							'cron_time'  => [],
						],
					],
				],
			],
		);

		register_setting(
			$this->return_option_group(),
			$this->add_prefix( 'use_slack' ),
			[
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => get_option( $this->add_prefix( 'use_slack' ) ),
			]
		);

		register_setting(
			$this->return_option_group(),
			$this->add_prefix( 'send_slack_author' ),
			[
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => get_option( $this->add_prefix( 'send_slack_author' ) ),
			]
		);

		register_setting(
			$this->return_option_group(),
			$this->add_prefix( 'send_slack_update' ),
			[
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => get_option( $this->add_prefix( 'send_slack_update' ) ),
			]
		);

		register_setting(
			$this->return_option_group(),
			$this->add_prefix( 'use_discord' ),
			[
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => get_option( $this->add_prefix( 'use_discord' ) ),
			]
		);

		register_setting(
			$this->return_option_group(),
			$this->add_prefix( 'send_discord_author' ),
			[
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => get_option( $this->add_prefix( 'send_discord_author' ) ),
			]
		);

		register_setting(
			$this->return_option_group(),
			$this->add_prefix( 'send_discord_update' ),
			[
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => get_option( $this->add_prefix( 'send_discord_update' ) ),
			]
		);

		register_setting(
			$this->return_option_group(),
			$this->add_prefix( 'use_chatwork' ),
			[
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => get_option( $this->add_prefix( 'use_chatwork' ) ),
			]
		);

		register_setting(
			$this->return_option_group(),
			$this->add_prefix( 'send_chatwork_author' ),
			[
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => get_option( $this->add_prefix( 'send_chatwork_author' ) ),
			]
		);

		register_setting(
			$this->return_option_group(),
			$this->add_prefix( 'send_chatwork_update' ),
			[
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => get_option( $this->add_prefix( 'send_chatwork_update' ) ),
			]
		);
	}

	/**
	 * Settings page.
	 */
	public function settings_page() {
		echo '<div id="' . esc_attr( $this->return_option_group() ) . '"></div>';
	}
}
