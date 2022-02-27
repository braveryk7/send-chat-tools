<?php
declare( strict_types = 1 );

/**
 * Test: Sct_Activate
 */
class SctActivateTest extends PHPUnit\Framework\TestCase {
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

		if ( ! class_exists( 'Sct_Base ' ) ) {
			require_once './class/class-sct-base.php';
		}

		require_once './class/class-sct-activate.php';
		require_once './tests/lib/wordpress-functions.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp() :void {
		$this->instance = new Sct_Activate();
	}

	/**
	 * TEST: register_options()
	 */
	public function test_register_options() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: uninstall_options()
	 */
	public function test_uninstall_options() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: migration_options()
	 */
	public function test_migration_options() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: crypto2plain()
	 */
	public function test_crypto2plain() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: developer_message()
	 */
	public function test_developer_message() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}
}
