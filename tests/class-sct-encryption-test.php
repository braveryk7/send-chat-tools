<?php

declare( strict_types = 1 );

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * Test: Sct_Encryption
 */
class Sct_Encryption_Test extends TestCase {
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

		require_once './class/class-sct-encryption.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	public function set_up(): void {
		parent::set_up();
		$this->instance = new Sct_Encryption();
	}

	/**
	 * TEST: make_vector()
	 */
	public function test_make_vector() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}
	/**
	 * TEST: decrypt()
	 */
	public function test_decrypt() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}
}
