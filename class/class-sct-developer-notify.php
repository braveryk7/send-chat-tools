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
	 * Constructor.
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
					$sct_options = $this->get_sct_options();
					$tools       = [ 'slack', 'discord', 'chatwork' ];

					foreach ( $tools as $tool ) {
						$api_column = 'chatwork' === $tool ? 'api_token' : 'webhook_url';

						if ( $sct_options[ $tool ]['use'] && $sct_options[ $tool ]['update_notify'] ) {
							$this->call_chat_tool_class( $tool, 'generate_developer_content', 'dev_notify', $developer_message );
						} elseif ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ][ $api_column ] ) ) {
							$this->logger( 1001, $tool, '1' );
						} elseif ( 'chatwork' === $tools && ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ]['room_id'] ) ) ) {
							$this->logger( 1002, 'chatwork', '1' );
						};
					}
				}
			}
		}
	}

	/**
	 * Controller to check developer messages.
	 *
	 * @param array $developer_message Developer message.
	 */
	private function developer_message_controller( array $developer_message ): bool {
		$keys_check   = $this->developer_message_arraykeys_check( $developer_message );
		$exist_check  = $this->developer_message_key_exists( $developer_message );
		$url_check    = $this->developer_message_urls_regex( $developer_message );
		$ignore_check = $this->developer_message_ignore_check( $developer_message );

		return $keys_check && $exist_check && $url_check && $ignore_check ? true : false;
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
		return match ( $developer_message['type'] ) {
			'theme'  => array_key_exists( $developer_message['key'], get_option( '_site_transient_theme_roots' ) ) ? true : false,
			'plugin' => in_array( $developer_message['key'], get_option( 'active_plugins' ), true ) ? true : false,
			default  => false,
		};
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
