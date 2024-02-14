<?php

declare( strict_types = 1 );

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * Test: Sct_Developer_Notify
 */
class Sct_Developer_Notify_Test extends TestCase {
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

		require_once './class/class-sct-developer-notify.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	public function set_up(): void {
		parent::set_up();
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
	 * TEST: developer_message_url_encode()
	 *
	 * @dataProvider developer_message_url_encode_parameters
	 * @param array $developer_message Developer message.
	 * @param array $expected Expected value.
	 */
	public function test_developer_message_url_encode( $developer_message, $expected ) {
		$method = new ReflectionMethod( $this->instance, 'developer_message_url_encode' );
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

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		activate_plugin( plugin_dir_path( ROOT_DIR ) . 'send-chat-tools/send-chat-tools.php' );

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
	 * TEST: developer_message_arraykeys_check()
	 */
	public function developer_message_arraykeys_check_parameters() {
		return [
			'All exists'     => [
				[
					'key'     => '',
					'type'    => '',
					'title'   => '',
					'message' => '',
					'url'     => [
						'website'     => '',
						'update_page' => '',
					],
				],
				true,
			],
			'url child less' => [
				[
					'key'     => '',
					'type'    => '',
					'title'   => '',
					'message' => '',
					'url'     => [],
				],
				false,
			],
		];
	}

	/**
	 * TEST: developer_message_url_encode()
	 */
	public function developer_message_url_encode_parameters() {
		return [
			'All set'             => [
				[
					'url' => [
						'website'     => 'https://www.braveryk7.com/',
						'update_page' => 'https://www.braveryk7.com/',
					],
				],
				[
					'url' => [
						'website'     => 'https%3A%2F%2Fwww.braveryk7.com%2F',
						'update_page' => 'https%3A%2F%2Fwww.braveryk7.com%2F',
					],
				],
			],
			'website is null'     => [
				[
					'url' => [
						'website'     => null,
						'update_page' => 'https://www.braveryk7.com/',
					],
				],
				[
					'url' => [
						'website'     => null,
						'update_page' => 'https%3A%2F%2Fwww.braveryk7.com%2F',
					],
				],
			],
			'update_page is null' => [
				[
					'url' => [
						'website'     => 'https://www.braveryk7.com/',
						'update_page' => null,
					],
				],
				[
					'url' => [
						'website'     => 'https%3A%2F%2Fwww.braveryk7.com%2F',
						'update_page' => null,
					],
				],
			],
			'All null'            => [
				[
					'url' => [
						'website'     => null,
						'update_page' => null,
					],
				],
				[
					'url' => [
						'website'     => null,
						'update_page' => null,
					],
				],
			],
		];
	}

	/**
	 * TEST: developer_message_key_exists()
	 */
	public function developer_message_key_exists_parameters() {
		return [
			'theme'     => [
				[
					'key'  => 'twentytwentyfour',
					'type' => 'theme',
				],
				true,
			],
			'plugin'    => [
				[
					'key'  => 'send-chat-tools/send-chat-tools.php',
					'type' => 'plugin',
				],
				true,
			],
			'type less' => [
				[
					'key'  => 'twentytwentyfour',
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
