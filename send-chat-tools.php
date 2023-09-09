<?php
/**
 * Plugin Name: Send Chat Tools
 * Plugin URI:  https://www.braveryk7.com/
 * Description: A plugin that allows you to send WordPress announcements to chat tools.
 * Version:     1.5.3
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

load_plugin_textdomain( 'send-chat-tools', false, basename( __DIR__ ) . '/languages' );

require_once __DIR__ . '/class/class-sct-phpver-judge.php';

$sct_phpver_judge        = new Sct_Phpver_Judge();
$sct_require_php_version = '8.0.0';

if ( ! $sct_phpver_judge->judgment( $sct_require_php_version ) ) {
	$sct_phpver_judge->deactivate(
		__FILE__,
		'Send Chat Tools',
		$sct_require_php_version,
	);
} else {
	require_once __DIR__ . '/class/class-sct-base.php';
	require_once __DIR__ . '/class/class-sct-encryption.php';
	require_once __DIR__ . '/class/class-sct-admin-page.php';
	require_once __DIR__ . '/class/class-sct-check-comment.php';
	require_once __DIR__ . '/class/class-sct-check-login.php';
	require_once __DIR__ . '/class/class-sct-check-rinker.php';
	require_once __DIR__ . '/class/class-sct-check-update.php';
	require_once __DIR__ . '/class/class-sct-logger.php';
	require_once __DIR__ . '/class/class-sct-activate.php';
	require_once __DIR__ . '/class/class-sct-dashboard-notify.php';
	require_once __DIR__ . '/class/class-sct-developer-notify.php';
	require_once __DIR__ . '/class/class-sct-generate-content-abstract.php';
	require_once __DIR__ . '/class/class-sct-slack.php';
	require_once __DIR__ . '/class/class-sct-discord.php';
	require_once __DIR__ . '/class/class-sct-chatwork.php';

	/**
	 * Start comment process.
	 */
	new Sct_Check_Comment();

	/**
	 * Start login process.
	 */
	new Sct_Check_Login();

	/**
	 * Start Rinker process.
	 */
	new Sct_Check_Rinker();

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
