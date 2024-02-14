<?php

declare( strict_types = 1 );

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * Test: Sct_Activate
 */
class Sct_Activate_Test extends TestCase {
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
		if ( ! class_exists( 'Sct_Base ' ) ) {
			require_once './class/class-sct-base.php';
		}

		require_once './class/class-sct-activate.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	public function set_up(): void {
		parent::set_up();
		$this->instance = new Sct_Activate();
	}

	/**
	 * TEST: register_options()
	 */
	public function test_register_options() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: option_check()
	 */
	public function test_option_check() {
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
