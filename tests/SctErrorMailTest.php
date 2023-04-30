<?php
declare( strict_types = 1 );

/**
 * Test: Sct_Error_Mail
 */
class SctErrorMailTest extends PHPUnit\Framework\TestCase {
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

		require_once './class/class-sct-error-mail.php';
		require_once './tests/lib/wordpress-functions.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp() :void {
		$error_code     = 1;
		$comment_id     = '1';
		$tool_name      = 'slack';
		$this->instance = new Sct_Error_Mail( $error_code, $comment_id, $tool_name );
	}

	/**
	 * TEST: send_mail()
	 */
	public function test_send_mail() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}
}
