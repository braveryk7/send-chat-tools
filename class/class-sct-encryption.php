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
	 * Make IV for OpenSSL.
	 */
	public static function make_vector(): string {
		$vector_length = openssl_cipher_iv_length( self::ENCRYPT_METHOD );
		return bin2hex( openssl_random_pseudo_bytes( $vector_length ) );
	}

	/**
	 * Decrypt value.
	 *
	 * @param string $value to decrypt.
	 */
	public function decrypt( string $value ) {
		$sct_options = $this->get_sct_options();
		$key         = get_userdata( $sct_options['user_id'] )->user_registered;
		return openssl_decrypt( $value, self::ENCRYPT_METHOD, $key, 0, hex2bin( $sct_options['iv'] ) );
	}
}
