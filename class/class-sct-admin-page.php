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
		add_filter( 'plugin_action_links_' . plugin_basename( $this->get_plugin_path() ), [ $this, 'add_settings_links' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_api' ] );
	}

	/**
	 * Add Setting menu.
	 */
	public function add_menu(): void {
		add_options_page(
			__( 'Send Chat Tools', 'send-chat-tools' ),
			__( 'Send Chat Tools', 'send-chat-tools' ),
			'administrator',
			self::PLUGIN_SLUG,
			[ $this, 'settings_page' ],
		);

		$sct_options = get_option( $this->add_prefix( 'options' ) );
		if ( array_key_exists( 'old_settings', $sct_options ) && $sct_options['old_settings'] ) {
			unset( $sct_options['old_settings'] );
			$this->set_sct_options( $sct_options );

			add_options_page(
				__( 'Old Send Chat Tools', 'send-chat-tools' ),
				__( 'Old Send Chat Tools', 'send-chat-tools' ),
				'administrator',
				'send-chat-tools-settings',
				[ $this, 'old_settings_page' ]
			);
		}
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
	 * Include JS in Send Chat Tools settings page.
	 *
	 * @param string $hook_shuffix WordPress hook_shuffix.
	 */
	public function add_scripts( string $hook_shuffix ): void {
		if ( 'settings_page_' . self::PLUGIN_SLUG !== $hook_shuffix ) {
			return;
		}

		$assets = require_once $this->get_plugin_dir( 'send-chat-tools' ) . '/build/index.asset.php';

		wp_enqueue_style(
			$this->add_prefix( 'style' ),
			$this->get_plugin_url( self::PLUGIN_SLUG ) . '/build/index.css',
			[ 'wp-components' ],
			$assets['version'],
		);

		wp_enqueue_script(
			$this->add_prefix( 'script' ),
			$this->get_plugin_url( self::PLUGIN_SLUG ) . '/build/index.js',
			$assets['dependencies'],
			$assets['version'],
			true
		);

		wp_set_script_translations(
			$this->add_prefix( 'script' ),
			self::PLUGIN_SLUG,
			$this->get_plugin_dir( self::PLUGIN_SLUG ) . '/languages/js',
		);
	}

	/**
	 * Create custom endpoint.
	 */
	public function register_rest_api(): void {
		register_rest_route(
			$this->get_api_namespace(),
			'/options',
			[
				'method'              => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'readable_api' ],
				'permission_callback' => [ $this, 'get_wordpress_permission' ],
			]
		);

		register_rest_route(
			$this->get_api_namespace(),
			'/update',
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'editable_api' ],
				'permission_callback' => [ $this, 'get_wordpress_permission' ],
			]
		);
	}

	/**
	 * Return WordPress  administrator permission.
	 */
	public function get_wordpress_permission(): bool {
		return current_user_can( 'administrator' );
	}

	/**
	 * Custom endpoint for read.
	 */
	public function readable_api(): WP_REST_Response {
		$sct_options         = $this->get_sct_options();
		$sct_options['logs'] = get_option( $this->add_prefix( 'logs' ) );
		return new WP_REST_Response( $sct_options, 200 );
	}

	/**
	 * Custom endpoint for edit.
	 *
	 * @param WP_REST_Request $request WP_REST_Request object.
	 */
	public function editable_api( WP_REST_Request $request ): WP_REST_Response {
		$sct_options = $this->get_sct_options();
		$params      = $request->get_json_params();
		$keys        = [ 'slack', 'discord', 'chatwork', 'ignore_key', 'cron_time', 'check_rinker_exists_items_cron' ];
		$flag        = false;

		foreach ( $keys as $key ) {
			if ( $params && array_key_exists( $key, $params ) ) {
				$sct_options[ $key ] = $params[ $key ];
				$flag                = true;
				continue;
			}
		}

		if ( ! $flag ) {
			return new WP_Error( 'invalid_key', __( 'Required key does not exist', 'admin-bar-tools' ), [ 'status' => 404 ] );
		}

		$this->set_sct_options( $sct_options );

		return new WP_REST_Response( $params, 200 );
	}

	/**
	 * Settings page.
	 */
	public function settings_page(): void {
		echo '<div id="' . esc_attr( $this->get_option_group() ) . '"></div>';
	}

	/**
	 * Update user ONLY!!
	 * Old settings page redirect to settings_page().
	 */
	public function old_settings_page(): void {
		$url = admin_url() . 'options-general.php?page=send-chat-tools';
		echo "<script>location.href='{$url}';</script>"; //phpcs:ignore
	}
}
