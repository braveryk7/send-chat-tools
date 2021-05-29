<?php
/**
 * Plugin Name: Send Chat Tools
 * Plugin URI:  https://www.braveryk7.com/
 * Description: A plugin that allows you to send WordPress announcements to chat tools.
 * Version:     0.1.2
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

declare( strict_type = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

load_plugin_textdomain( 'send-chat-tools', false, basename( dirname( __FILE__ ) ) . '/languages' );

require_once dirname( __FILE__ ) . '/class/class-sct-judgment-php-version.php';

$require_php_version  = '7.3.0';
$get_php_version_bool = new Sct_Judgment_Php_Version();
if ( false === $get_php_version_bool->judgment( $require_php_version ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
		if ( is_admin() ) {
			require_once dirname( __FILE__ ) . '/modules/cancel-activate.php';
			cancel_activate();
		}
		deactivate_plugins( plugin_basename( __FILE__ ) );
	} else {
		echo '<p>' . esc_html_e( 'Send Chat Tools requires at least PHP 7.3.0 or later.', 'send-chat-tools' ) . esc_html_e( 'Please upgrade PHP.', 'admin-bar-tools' ) . '</p>';
		exit;
	}
} elseif ( true === $get_php_version_bool->judgment( $require_php_version ) ) {
	require_once dirname( __FILE__ ) . '/class/class-sct-connect-database.php';
	require_once dirname( __FILE__ ) . '/class/class-sct-settings-page.php';
	require_once dirname( __FILE__ ) . '/class/class-sct-slack.php';
	require_once dirname( __FILE__ ) . '/class/class-sct-chatwork.php';

	global $wpdb;

	if ( '1' === get_option( 'sct_use_slack' ) ) {
		add_action( 'comment_post', 'Sct_Slack::send_slack' );
	}

	if ( '1' === get_option( 'sct_use_chatwork' ) ) {
		add_action( 'comment_post', 'Sct_Chatwork::send_chatwork' );
	}

	/**
	 * Uninstall Hook.
	 */
	function sct_uninstall() {
		register_uninstall_hook( __FILE__, 'Sct_Connect_Database::delete_db' );
	}
	sct_uninstall();

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'Sct_Settings_Page::add_settings_links' );
}

