<?php
/**
 * Encryption API Key, Webhook URL.
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
/**
 * Encrypt value
 */
class Sct_Encryption extends Sct_Base {
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
	public static function make_vector(): string {
		$vector_length = openssl_cipher_iv_length( self::ENCRYPT_METHOD );
		return bin2hex( openssl_random_pseudo_bytes( $vector_length ) );
	}

	/**
	 * Encrypt value.
	 *
	 * @param string $value to encrypt.
	 */
	public function encrypt( string $value ): string {
		$wpdb;
		$sct_options = $this->get_sct_options();

		if ( ! $sct_options['iv'] ) {
			$sct_options['iv'] = $this->make_vector();
			$this->set_sct_options( $sct_options );
		}

		$sct_options['user_id'] = wp_get_current_user()->ID;
		$this->set_sct_options( $sct_options );
		return openssl_encrypt( $value, self::METHOD, $this->get_user_registered(), 0, hex2bin( $sct_options['iv'] ) );
	}

	/**
	 * Decrypt value.
	 *
	 * @param string $value to decrypt.
	 */
	public function decrypt( string $value ): string {
		$wpdb;
		$sct_options = $this->get_sct_options();
		$key         = get_userdata( $sct_options['user_id'] )->user_registered;
		return openssl_decrypt( $value, self::ENCRYPT_METHOD, $key, 0, hex2bin( $sct_options['iv'] ) );
	}
}
