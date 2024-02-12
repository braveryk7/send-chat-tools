<?php

declare( strict_types = 1 );

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * Test: Sct_Admin_Page
 */
class Sct_Check_Update_Test extends TestCase {
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

		require_once './class/class-sct-check-update.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	public function set_up(): void {
		parent::set_up();
		$this->instance = new Sct_Check_Update();
	}

	/**
	 * TEST: controller()
	 */
	public function test_controller() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: check_core()
	 */
	public function test_check_core() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: check_themes()
	 */
	public function test_check_themes() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: check_plugins()
	 */
	public function test_check_plugins() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: check_cron_time()
	 */
	public function test_check_cron_time() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}
}
