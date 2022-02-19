<?php
/**
 * Developer notify.
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
 * Developer notify class.
 */
class Sct_Developer_Notify extends Sct_Base {
	/**
	 * Check for the presence of keys in developer messages.
	 *
	 * @param array $developer_message Developer message.
	 */
	private function developer_message_arraykeys_check( array $developer_message ): bool {
		$flag = false;

		$flag = array_key_exists( 'key', $developer_message ) ? true : false;
		$flag = array_key_exists( 'type', $developer_message ) ? true : false;
		$flag = array_key_exists( 'title', $developer_message ) ? true : false;
		$flag = array_key_exists( 'message', $developer_message ) ? true : false;
		$flag = array_key_exists( 'url', $developer_message ) ? true : false;

		return $flag;
	}
}
