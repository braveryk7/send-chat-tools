<?php
/**
 * Plugin notify
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 1.3.0
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Plugin notify on WordPress dashboard.
 */
class Sct_Dashboard_Notify {
	/**
	 * WordPress hook.
	 * Use Dashboard API.
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', [ $this, 'add_dashboard' ] );
	}

	/**
	 * Add dashboard.
	 */
	public function add_dashboard() {
		wp_add_dashboard_widget(
			'sct-dashboard',
			__( 'Notice from Send Chat Tools', 'send-chat-tools' ),
			[ $this, 'dashboard_message' ]
		);
	}
}
