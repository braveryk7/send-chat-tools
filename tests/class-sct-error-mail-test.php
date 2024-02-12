<?php

declare( strict_types = 1 );

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * Test: Sct_Error_Mail
 */
class Sct_Error_Mail_Test extends TestCase {
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

		require_once './class/class-sct-generate-content-abstract.php';
		require_once './class/class-sct-error-mail.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	public function set_up(): void {
		parent::set_up();
		$this->instance = new Sct_Error_Mail();
	}

	/**
	 * TEST: send_mail()
	 */
	public function test_send_mail() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}
}
