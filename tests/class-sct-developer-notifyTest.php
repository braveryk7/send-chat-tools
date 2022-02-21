<?php
declare( strict_types = 1 );

define( 'ABSPATH', '' );

require_once './class/class-sct-developer-notify.php';
require_once './tests/lib/wordpress-functions.php';

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
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp() :void {
		$this->instance =
			$this->getMockBuilder( 'Sct_Developer_Notify' )
			->setMethods( null )
			->disableOriginalConstructor()
			->getMock();
	}

	/**
	 * TEST: developer_notify()
	 */
	public function test_developer_notify() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: developer_message_controller()
	 */
	public function test_developer_message_controller() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: developer_message_arraykeys_check()
	 */
	public function test_developer_message_arraykeys_check() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: developer_message_key_exists()
	 *
	 * @dataProvider developer_message_key_exists_parameters
	 * @param array $developer_message Developer message.
	 */
	public function test_developer_message_key_exists( $developer_message ) {
		$method = new ReflectionMethod( $this->instance, 'developer_message_key_exists' );
		$method->setAccessible( true );

		$this->assertSame(
			true,
			$method->invoke( $this->instance, $developer_message ),
		);
	}

	/**
	 * TEST: developer_message_urls_regex()
	 *
	 * @dataProvider url_parameters
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

	/**
	 * TEST: developer_message_key_exists()
	 */
	public function developer_message_key_exists_parameters() {
		return [
			'theme'  => [
				[
					'key'  => 'my-theme',
					'type' => 'theme',
				],
			],
			'plugin' => [
				[
					'key'  => 'my-plugin/my-plugin.php',
					'type' => 'plugin',
				],
			],
		];
	}

	/**
	 * Use TEST: developer_message_urls_regex()
	 */
	public function url_parameters() {
		return [
			'All set'             => [
				[
					'url' => [
						'website'     => 'https://www.braveryk7.com/',
						'update_page' => 'https://www.braveryk7.com/',
					],
				],
			],
			'website is null'     => [
				[
					'url' => [
						'website'     => 'https://www.braveryk7.com/',
						'update_page' => 'https://www.braveryk7.com/',
					],
				],
			],
			'update_page is null' => [
				[
					'url' => [
						'website'     => 'https://www.braveryk7.com/',
						'update_page' => 'https://www.braveryk7.com/',
					],
				],
			],
			'All null'            => [
				[
					'url' => [
						'website'     => 'https://www.braveryk7.com/',
						'update_page' => 'https://www.braveryk7.com/',
					],
				],
			],
		];
	}
}
