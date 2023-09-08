<?php
declare( strict_types = 1 );

/**
 * Test: Sct_Dashboard_Notify
 */
class SctDashboardNotifyTest extends PHPUnit\Framework\TestCase {
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

		require_once './class/class-sct-dashboard-notify.php';
		require_once './tests/lib/wordpress-functions.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp(): void {
		$this->instance = new Sct_Dashboard_Notify();
	}

	/**
	 * TEST: add_dashboard()
	 */
	public function test_add_dashboard() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: dashboard_message()
	 */
	public function test_dashboard_message() {
		$sct_base = new Sct_Base();
		$method   = new ReflectionMethod( $sct_base, 'get_developer_messages' );
		$method->setAccessible( true );

		ob_start();
		$this->instance->dashboard_message();
		$actual             = ob_get_clean();
		$value              = '';
		$base               = new Sct_Base();
		$developer_messages = $method->invoke( $sct_base )['message'];
		foreach ( $developer_messages as $message ) {
			$value .= strpos( $message, '：' ) || strpos( $message, ':' ) ? '<h3>' . $message . '</h3>' : $message . '<br>';
		}
		$this->assertSame(
			$value,
			$actual,
		);
	}
}
