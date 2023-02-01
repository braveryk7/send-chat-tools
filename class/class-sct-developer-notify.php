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
	 * Constructer.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'developer_notify' ] );
	}

	/**
	 * Developer notify hook, process.
	 */
	public function developer_notify(): void {
		$developer_messages = apply_filters( 'sct_developer_notify', [] );

		if ( $developer_messages && is_array( $developer_messages ) ) {
			foreach ( $developer_messages as $developer_message ) {
				if ( $developer_message && $this->developer_message_controller( $developer_message ) ) {
					$create_content = new Sct_Create_Content();
					$create_content->controller( 0, 'dev_notify', $developer_message );
				}
			}
		}
	}

	/**
	 * Controller to check developer messages.
	 *
	 * @param array $developer_message Developer message.
	 */
	private function developer_message_controller( array $developer_message ) {
		$kesy_check   = $this->developer_message_arraykeys_check( $developer_message );
		$exist_check  = $this->developer_message_key_exists( $developer_message );
		$url_check    = $this->developer_message_urls_regex( $developer_message );
		$ignore_check = $this->developer_message_ignore_check( $developer_message );

		return $kesy_check && $exist_check && $url_check && $ignore_check ? true : false;
	}

	/**
	 * Check for the presence of keys in developer messages.
	 *
	 * @param array $developer_message Developer message.
	 */
	private function developer_message_arraykeys_check( array $developer_message ): bool {
		$keys = [ 'key', 'type', 'title', 'message', 'url', 'website', 'update_page' ];

		foreach ( $keys as $key ) {
			if ( 'website' === $key || 'update_page' === $key ) {
				if ( ! array_key_exists( $key, $developer_message['url'] ) ) {
					return false;
				}
			} elseif ( ! array_key_exists( $key, $developer_message ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Encode website and update_page values.
	 *
	 * @param array $developer_message Developer message.
	 */
	private function developer_message_url_encode( array $developer_message ): array {
		if ( ! is_null( $developer_message['url']['website'] ) ) {
			$developer_message['url']['website'] = rawurlencode( $developer_message['url']['website'] );
		}

		if ( ! is_null( $developer_message['url']['update_page'] ) ) {
			$developer_message['url']['update_page'] = rawurlencode( $developer_message['url']['update_page'] );
		}

		return $developer_message;
	}

	/**
	 * Check for the presence of themes or plugins in developer messages.
	 *
	 * @param array $developer_message Developer message.
	 */
	private function developer_message_key_exists( array $developer_message ): bool {
		if ( 'theme' === $developer_message['type'] ) {
			$themes = get_option( '_site_transient_theme_roots' );
			return array_key_exists( $developer_message['key'], $themes ) ? true : false;
		} elseif ( 'plugin' === $developer_message['type'] ) {
			$plugins = get_option( 'active_plugins' );
			return in_array( $developer_message['key'], $plugins, true ) ? true : false;
		}

		return false;
	}

	/**
	 * Check if the array value of the url key is in URL format.
	 *
	 * @param array $developer_message Developer message.
	 */
	private function developer_message_urls_regex( array $developer_message ): bool {
		$url     = $developer_message['url'];
		$pattern = '/\Ahttps?:\/\/.*/';

		if ( ( is_null( $url['website'] ) || preg_match( $pattern, $url['website'] ) ) &&
			( is_null( $url['update_page'] ) || preg_match( $pattern, $url['update_page'] ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if the key is on the exclusion list.
	 *
	 * @param array $developer_message Developer message.
	 */
	private function developer_message_ignore_check( array $developer_message ): bool {
		$sct_options = $this->get_sct_options();
		$ignore_list = $sct_options['ignore_key'];

		foreach ( $ignore_list as $ignore ) {
			if ( $developer_message['key'] === $ignore ) {
				return false;
			}
		}

		return true;
	}
}
