<?php
/**
 * Plugin Name: Send Chat Tools
 * Plugin URI:  https://www.braveryk7.com/
 * Description: A plugin that allows you to send WordPress announcements to chat tools.
 * Version:     1.3.0
 * Author:      Ken-chan
 * Author URI:  https://twitter.com/braveryk7
 * Text Domain: send-chat-tools
 * Domain Path: /languages
 * License:     GPL2
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

load_plugin_textdomain( 'send-chat-tools', false, basename( dirname( __FILE__ ) ) . '/languages' );

require_once dirname( __FILE__ ) . '/class/class-sct-base.php';
require_once dirname( __FILE__ ) . '/class/class-sct-judgment-php-version.php';

$get_php_version_bool = new Sct_Judgment_Php_Version();
if ( false === $get_php_version_bool->judgment( Sct_Base::REQUIRED_PHP_VERSION ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
		if ( is_admin() ) {
			require_once dirname( __FILE__ ) . '/modules/cancel-activate.php';
			cancel_activate();
		}
		deactivate_plugins( plugin_basename( __FILE__ ) );
	} else {
		echo '<p>' . esc_html_e( 'Send Chat Tools requires at least PHP 7.3.0 or later.', 'send-chat-tools' ) . esc_html_e( 'Please upgrade PHP.', 'send-chat-tools' ) . '</p>';
		exit;
	}
} elseif ( true === $get_php_version_bool->judgment( Sct_Base::REQUIRED_PHP_VERSION ) ) {
	require_once dirname( __FILE__ ) . '/class/class-sct-encryption.php';
	require_once dirname( __FILE__ ) . '/class/class-sct-admin-page.php';
	require_once dirname( __FILE__ ) . '/class/class-sct-create-content.php';
	require_once dirname( __FILE__ ) . '/class/class-sct-check-update.php';
	require_once dirname( __FILE__ ) . '/class/class-sct-logger.php';
	require_once dirname( __FILE__ ) . '/class/class-sct-activate.php';
	require_once dirname( __FILE__ ) . '/class/class-sct-dashboard-notify.php';
	require_once dirname( __FILE__ ) . '/class/class-sct-developer-notify.php';

	/**
	 * Start comment process.
	 */
	new Sct_Create_Content();

	/**
	 * Start update process.
	 */
	new Sct_Check_Update();

	/**
	 * Admin page.
	 */
	new Sct_Admin_Page();

	/**
	 * Plugin activate.
	 */
	new Sct_Activate();

	/**
	 * Dashboard notify.
	 */
	new Sct_Dashboard_Notify();

	/**
	 * Developer notify.
	 */
	new Sct_Developer_Notify();

	/**
	 * Plugin uninstall hook.
	 * Delete wp_options column.
	 */
	register_uninstall_hook( __FILE__, 'Sct_Activate::uninstall_options' );

	/**
	 * Delete wp_options column.
	 * THIS ITEM WILL BE DELETED IN APRIL 2022!!
	 */
	delete_option( 'sct_plugins' );
	delete_option( 'sct_plugin111' );
	delete_option( 'sct_uhehe' );
}
