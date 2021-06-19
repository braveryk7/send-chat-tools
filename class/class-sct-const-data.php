<?php
/**
 * Admin settings page.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 1.0.0
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Constant data.
 */
class Sct_Const_Data {
	/**
	 * Database Table prefix.
	 */
	const TABLE_NAME = 'sct';

	/**
	 * Database version.
	 */
	const DB_VERSION = '1.0';
}
