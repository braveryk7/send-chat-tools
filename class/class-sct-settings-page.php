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
class Sct_Settings_Page {
	/**
	 * WordPress hook.
	 * Add settings page link in admin page.
	 *
	 * @param string $path send-chat-tools.php path.
	 */
	public function __construct( string $path ) {
		$this->path = $path;
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'add_scripts' ] );
		add_action( 'admin_head-settings_page_send-chat-tools-settings', [ $this, 'include_css' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( $path ), [ $this, 'add_settings_links' ] );
	}

	/**
	 * Add Setting menu.
	 */
	public function add_menu() {
		add_options_page(
			__( 'Send Chat Tools', 'send-chat-tools' ),
			__( 'Send Chat Tools', 'send-chat-tools' ),
			'administrator',
			'send-chat-tools-settings',
			[ $this, 'settings_page' ],
		);
	}

	/**
	 * Add configuration link to plugin page.
	 *
	 * @param array|string $links plugin page setting links.
	 */
	public function add_settings_links( array $links ): array {
		$add_link = '<a href="options-general.php?page=send-chat-tools-settings">' . __( 'Settings', 'send-chat-tools' ) . '</a>';
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
		if ( 'settings_page_send-chat-tools-settings' !== $hook_suffix ) {
			return;
		}

		$assets = require_once dirname( $this->path ) . '/build/index.asset.php';

		wp_enqueue_style(
			'sct-style',
			WP_PLUGIN_URL . '/send-chat-tools/build/index.css',
			[ 'wp-components' ],
			$assets['version'],
		);

		wp_enqueue_script(
			'sct-script',
			WP_PLUGIN_URL . '/send-chat-tools/build/index.js',
			$assets['dependencies'],
			$assets['version'],
			true
		);
	}

	/**
	 * Settings page.
	 */
	public function settings_page() {
		echo '<div id="send-chat-tools-settings"></div>';
	}
}
