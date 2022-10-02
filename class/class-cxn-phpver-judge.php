<?php
/**
 * Judgment PHP Version.
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
 * Return true or false.
 */
class Cxn_Phpver_Judge {
	/**
	 * Judgment PHP version.
	 *
	 * @param string $version_received php version.
	 */
	public function judgment( string $version_received ): bool {
		return version_compare( PHP_VERSION, $version_received, '>=' ) ? true : false;
	}
}
