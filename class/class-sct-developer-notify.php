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
	 * Developer notify hook, process.
	 */
	public function developer_notify() {
		$developer_message = null;

		$developer_message = apply_filters( 'sct_developer_notify', $developer_message );

		if ( $developer_message && $this->developer_message_controller( $developer_message ) ) {
			$create_content = new Sct_Create_Content();
			$create_content->controller( 0, 'dev_notify', $developer_message );
		}
	}

	/**
	 * Controller to check developer messages.
	 *
	 * @param array $developer_message Developer message.
	 */
	private function developer_message_controller( array $developer_message ) {
		$kesy_check  = $this->developer_message_arraykeys_check( $developer_message );
		$exist_check = $this->developer_message_key_exists( $developer_message );

		$flag = $kesy_check && $exist_check ? true : false;

		return $flag;
	}

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

	/**
	 * Check for the presence of themes or plugins in developer messages.
	 *
	 * @param array $developer_message Developer message.
	 */
	private function developer_message_key_exists( array $developer_message ): bool {
		$flag = false;

		if ( 'theme' === $developer_message['type'] ) {
			$themes = get_option( '_site_transient_theme_roots' );
			$flag   = array_key_exists( $developer_message['key'], $themes ) ? true : false;
		} elseif ( 'plugin' === $developer_message['type'] ) {
			$plugins = get_option( 'active_plugins' );
			$flag    = in_array( $developer_message['key'], $plugins, true ) ? true : false;
		}

		return $flag;
	}
}
