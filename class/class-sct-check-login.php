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
}
