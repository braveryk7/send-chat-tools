<?php

declare( strict_types = 1 );

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * Test: Sct_Logger
 */
class Sct_Logger_Test extends TestCase {
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

		require_once './class/class-sct-logger.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	public function set_up(): void {
		parent::set_up();
		$this->instance = Sct_Logger::get_instance();
	}

	/**
	 * TEST: create_log()
	 */
	public function test_create_log() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: save_log()
	 */
	public function test_save_log() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}
}
