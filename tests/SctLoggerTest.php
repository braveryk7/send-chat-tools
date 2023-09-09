<?php

declare( strict_types = 1 );

/**
 * Test: Sct_Logger
 */
class SctLoggerTest extends PHPUnit\Framework\TestCase {
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

		require_once './class/class-sct-logger.php';
		require_once './tests/lib/wordpress-functions.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp(): void {
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
