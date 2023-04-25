<?php
/**
 * A class that detects user login and calls a class for sending when the condition is met.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 1.5.0
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * A class that detects user login and calls a class for sending when the condition is met.
 */
class Sct_Check_Login extends Sct_Base {
	/**
	 * WordPress hook.
	 */
	public function __construct() {
		add_action( 'wp_login', [ $this, 'controller' ], 10, 2 );
	}

	/**
	 * Method called when a user logs in.
	 *
	 * @param string $user_login User name.
	 * @param object $user       User object.
	 */
	public function controller( $user_login, $user ) {
		$sct_options = $this->get_sct_options();
		$tools       = [ 'slack', 'discord', 'chatwork' ];

		foreach ( $tools as $tool ) {
			$api_column = 'chatwork' === $tool ? 'api_token' : 'webhook_url';

			if ( $sct_options[ $tool ]['use'] && $sct_options[ $tool ]['login_notify'] ) {
				$instance = match ( $tool ) {
					'slack'    => Sct_Slack::get_instance(),
					'discord'  => Sct_Discord::get_instance(),
					'chatwork' => Sct_Chatwork::get_instance(),
				};
				$instance?->generate_login_message( $user )?->generate_header()?->send_tools( 'login_notify', $tool );
			} elseif ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ][ $api_column ] ) ) {
				$this->logger( 1001, $tool, '1' );
			} elseif ( 'chatwork' === $tools && ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ]['room_id'] ) ) ) {
				$this->logger( 1002, 'chatwork', '1' );
			};
		}
	}
}
