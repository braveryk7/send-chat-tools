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

if ( empty( $get_php_version_bool ) ) {
	require_once dirname( __FILE__ ) . '/class/class-Sct-phpver-judge.php';
	$get_php_version_bool = new Sct_Phpver_Judge();
}

if ( ! $get_php_version_bool->judgment( Sct_Base::get_required_php_version() ) ) {
	$get_php_version_bool->deactivate(
		__FILE__,
		Sct_Base::get_plugin_name(),
		Sct_Base::get_required_php_version()
	);
} else {
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
}
