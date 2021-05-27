<?php
/**
 * Encryption API Key, Webhook URL.
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
/**
 * Encrypt value
 */
class Sct_Encryption {
	const METHOD = 'AES-256-CBC';

	/**
	 * Get current user registered date and time.
	 */
	private static function get_user_registered(): string {
		$user_reg_date = get_userdata( wp_get_current_user()->ID );
		return $user_reg_date->user_registered;
	}

	/**
	 * Make IV for OpenSSL.
	 */
	private static function make_vector(): string {
		$vector_length = openssl_cipher_iv_length( self::METHOD );
		return bin2hex( openssl_random_pseudo_bytes( $vector_length ) );
	}

	/**
	 * Encrypt value.
	 *
	 * @param string $value to encrypt.
	 */
	public static function encrypt( string $value ): string {
		$wpdb;
		if ( false === get_option( 'sct_iv' ) ) {
			$new_iv = self::make_vector();
			update_option( 'sct_iv', $new_iv );
		} else {
			$new_iv = get_option( 'sct_iv' );
		}
		update_option( 'sct_use_user_id', wp_get_current_user()->ID );
		return openssl_encrypt( $value, self::METHOD, self::get_user_registered(), 0, $new_iv );
	}

	/**
	 * Decrypt value.
	 *
	 * @param string $value to decrypt.
	 */
	public static function decrypt( string $value ): string {
		$wpdb;
		$get_user_id = get_option( 'sct_use_user_id' );
		$key         = get_userdata( $get_user_id )->user_registered;
		$iv          = get_option( 'sct_iv' );
		return openssl_decrypt( $value, self::METHOD, $key, 0, $iv );
	}
}
