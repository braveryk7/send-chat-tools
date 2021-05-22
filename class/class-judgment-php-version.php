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

/**
 * Return true or false.
 */
class Judgment_Php_Version {
	/**
	 * Argument.
	 *
	 * @var int
	 */
	private $version_received;

	/**
	 * Judgment PHP version.
	 *
	 * @param int $version_received php version.
	 * @return bool
	 */
	public function judgment( $version_received ) {
		if ( version_compare( PHP_VERSION, $version_received, '>=' ) ) {
			return true;
		} else {
			return false;
		}
	}
}
