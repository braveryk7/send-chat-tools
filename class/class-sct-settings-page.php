<?php
/**
 * Admin settings page.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 0.0.1
 */

declare( strict_type = 1 );

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
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
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
	public static function add_settings_links( array $links ): array {
		$add_link = '<a href="options-general.php?page=send-chat-tools-settings">' . __( 'Settings', 'send-chat-tools' ) . '</a>';
		array_unshift( $links, $add_link );
		return $links;
	}
}

if ( is_admin() ) {
	$settings_page = new Sct_Settings_Page();
}
