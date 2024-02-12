<?php

declare( strict_types = 1 );

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * Test: Sct_Phpver_Judge
 */
class Sct_Phpver_Judge_Test extends TestCase {
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

		require_once './class/class-sct-phpver-judge.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	public function set_up(): void {
		parent::set_up();
		$this->instance = new Sct_Phpver_Judge();
	}

	/**
	 * TEST: judgment()
	 */
	public function test_judgment() {
		$this->assertGreaterThanOrEqual( Sct_Base::get_required_php_version(), PHP_VERSION );
	}

	/**
	 * TEST: deactivate()
	 */
	public function test_deactivate() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: deactivate_message()
	 *
	 * @testWith [ "Send Chat Tools", "7.3.0" ]
	 *           [ "Admin Bar Tools", "8.0" ]
	 *
	 * @param string $project Project name.
	 * @param string $version PHP version.
	 */
	public function test_deactivate_message( $project, $version ) {
		$messages = [
			'header'  => sprintf(
				'[Plugin error] %s has been stopped because the PHP version is old.',
				$project,
			),
			'require' => sprintf(
				'%1$s requires at least PHP %2$s or later.',
				$project,
				$version,
			),
			'upgrade' => 'Please upgrade PHP.',
			'current' => 'Current PHP version:',
		];

		$this->assertSame(
			$messages,
			$this->instance->deactivate_message( $project, $version ),
		);
	}
}
