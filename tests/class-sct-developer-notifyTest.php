<?php
declare( strict_types = 1 );

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
	 * Settings: ABSPATH, test class file, WordPress functions.
	 */
	public static function setUpBeforeClass(): void {
		define( 'ABSPATH', '' );

		require_once './class/class-sct-developer-notify.php';
		require_once './tests/lib/wordpress-functions.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp() :void {
		$this->instance = new Sct_Developer_Notify();
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
	 *
	 * @dataProvider developer_message_arraykeys_check_parameters
	 * @param array $developer_message Developer message.
	 * @param bool  $expected Expected value.
	 */
	public function test_developer_message_arraykeys_check( $developer_message, $expected ) {
		$method = new ReflectionMethod( $this->instance, 'developer_message_arraykeys_check' );
		$method->setAccessible( true );

		$this->assertSame(
			$expected,
			$method->invoke( $this->instance, $developer_message ),
		);
	}

	/**
	 * TEST: developer_message_key_exists()
	 *
	 * @dataProvider developer_message_key_exists_parameters
	 * @param array $developer_message Developer message.
	 * @param bool  $expected Expected value.
	 */
	public function test_developer_message_key_exists( $developer_message, $expected ) {
		$method = new ReflectionMethod( $this->instance, 'developer_message_key_exists' );
		$method->setAccessible( true );

		$this->assertSame(
			$expected,
			$method->invoke( $this->instance, $developer_message ),
		);
	}

	/**
	 * TEST: developer_message_urls_regex()
	 *
	 * @dataProvider developer_message_urls_regex_parameters
	 * @param array $developer_message Developer message.
	 * @param bool  $expected Expected value.
	 */
	public function test_developer_message_urls_regex( $developer_message, $expected ) {
		$method = new ReflectionMethod( $this->instance, 'developer_message_urls_regex' );
		$method->setAccessible( true );

		$this->assertSame(
			$expected,
			$method->invoke( $this->instance, $developer_message ),
		);
	}

	/**
	 * TEST: developer_message_key_exists()
	 */
	public function developer_message_key_exists_parameters() {
		return [
			'theme'     => [
				[
					'key'  => 'my-theme',
					'type' => 'theme',
				],
				true,
			],
			'plugin'    => [
				[
					'key'  => 'my-plugin/my-plugin.php',
					'type' => 'plugin',
				],
				true,
			],
			'type less' => [
				[
					'key'  => 'my-theme',
					'type' => '',
				],
				false,
			],
			'key less'  => [
				[
					'key'  => '',
					'type' => 'theme',
				],
				false,
			],
			'null'      => [
				[
					'key'  => null,
					'type' => null,
				],
				false,
			],
		];
	}

	/**
	 * Use TEST: developer_message_urls_regex()
	 */
	public function developer_message_urls_regex_parameters() {
		return [
			'All set'             => [
				[
					'url' => [
						'website'     => 'https://www.braveryk7.com/',
						'update_page' => 'https://www.braveryk7.com/',
					],
				],
				true,
			],
			'website is null'     => [
				[
					'url' => [
						'website'     => 'https://www.braveryk7.com/',
						'update_page' => 'https://www.braveryk7.com/',
					],
				],
				true,
			],
			'update_page is null' => [
				[
					'url' => [
						'website'     => 'https://www.braveryk7.com/',
						'update_page' => 'https://www.braveryk7.com/',
					],
				],
				true,
			],
			'All null'            => [
				[
					'url' => [
						'website'     => 'https://www.braveryk7.com/',
						'update_page' => 'https://www.braveryk7.com/',
					],
				],
				true,
			],
		];
	}
}
