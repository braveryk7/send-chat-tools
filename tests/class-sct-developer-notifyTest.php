<?php
declare( strict_types = 1 );

define( 'ABSPATH', '' );

require_once './class/class-sct-developer-notify.php';

/**
 * Test: Sct_Developer_Notify
 */
class Sct_Developer_NotifyTest extends PHPUnit\Framework\TestCase {
	/**
	 * This test class instance.
	 *
	 * @var object $instance instance.
	 */
	private $instance;

	/**
	 * Use arg.
	 *
	 * @var array $developer_message arg.
	 */
	private $developer_message;

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp() :void {
		$this->instance          = new Sct_Developer_Notify();
		$this->developer_message = [
			'url' => [
				'website'     => null,
				'update_page' => null,
			],
		];
	}

	/**
	 * TEST: add_prefix()
	 *
	 * @param array $developer_message Developer message.
	 */
	public function test_developer_message_urls_regex( $developer_message ) {
		$method = new ReflectionMethod( $this->instance, 'developer_message_urls_regex' );
		$method->setAccessible( true );

		$this->assertSame(
			true,
			$method->invoke( $this->instance, $developer_message ),
		);
	}
}
