<?php
declare( strict_types = 1 );

/**
 * Test: Sct_Admin_Page
 */
class SctCheckUpdateTest extends PHPUnit\Framework\TestCase {
	/**
	 * This test class instance.
	 *
	 * @var object $instance instance.
	 */
	private $instance;

	/**
	 * Settings: ABSPATH, test class file, WordPress functions.
	 */
	public static function setUpBeforeClass(): void {
		if ( ! defined( 'ABSPATH' ) ) {
			define( 'ABSPATH', '' );
		}

		require_once './class/class-sct-check-update.php';
		require_once './tests/lib/wordpress-functions.php';
	}
}
