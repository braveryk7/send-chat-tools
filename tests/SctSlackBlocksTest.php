<?php
declare( strict_types = 1 );

/**
 * Test: Sct_Slack_Blocks
 */
class SctSlackBlocksTest extends PHPUnit\Framework\TestCase {
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

		require_once './class/class-sct-slack-blocks.php';
		require_once './tests/lib/wordpress-functions.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp() :void {
		$this->instance = new Sct_Slack_Blocks();
	}

	/**
	 * TEST: header()
	 */
	public function test_header() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: single_column()
	 */
	public function test_single_column() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: two_column()
	 */
	public function test_two_column() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: context()
	 */
	public function test_context() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}
}
