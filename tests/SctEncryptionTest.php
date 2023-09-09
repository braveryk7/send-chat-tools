<?php

declare( strict_types = 1 );

/**
 * Test: Sct_Encryption
 */
class SctEncryptionTest extends PHPUnit\Framework\TestCase {
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

		require_once './class/class-sct-encryption.php';
		require_once './tests/lib/wordpress-functions.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp(): void {
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
