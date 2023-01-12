<?php
declare( strict_types = 1 );

/**
 * Test: Sct_Phpver_Judge
 */
class SctPhpverJudgeTest extends PHPUnit\Framework\TestCase {
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

		require_once './class/class-sct-phpver-judge.php';
		require_once './tests/lib/wordpress-functions.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp() :void {
		$this->instance = new Sct_Phpver_Judge();
	}

	/**
	 * TEST: judgment()
	 */
	public function test_judgment() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: deactivate()
	 */
	public function test_deactivate() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}
}
