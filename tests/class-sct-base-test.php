<?php

declare( strict_types = 1 );

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * Test: Sct_Base
 */
class Sct_Base_Test extends TestCase {
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

		require_once './class/class-sct-base.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	public function set_up(): void {
		parent::set_up();
		$this->instance = new Sct_Base();
	}

	/**
	 * TEST: add_prefix()
	 */
	public function test_add_prefix() {
		$this->assertSame( 'sct_options', $this->instance->add_prefix( 'options' ) );
	}

	/**
	 * TEST: get_plugin_name()
	 */
	public function test_get_plugin_name() {
		$this->assertSame( 'Send Chat Tools', $this->instance->get_plugin_name() );
	}

	/**
	 * TEST: return_plugin_url()
	 */
	public function test_get_plugin_url() {
		$method = new ReflectionMethod( $this->instance, 'get_plugin_url' );
		$method->setAccessible( true );

		$this->assertSame(
			home_url( '/wp-content/plugins/send-chat-tools' ),
			$method->invoke( $this->instance, 'send-chat-tools' ),
		);
	}

	/**
	 * TEST: get_plugin_dir()
	 */
	public function test_get_plugin_dir() {
		$method = new ReflectionMethod( $this->instance, 'get_plugin_dir' );
		$method->setAccessible( true );

		$this->assertSame(
			get_home_path() . 'wp-content/plugins/send-chat-tools',
			$method->invoke( $this->instance, 'send-chat-tools' ),
		);
	}

	/**
	 * TEST: get_plugin_path()
	 */
	public function test_get_plugin_path() {
		$method = new ReflectionMethod( $this->instance, 'get_plugin_path' );
		$method->setAccessible( true );

		$this->assertSame(
			'/DocumentRoot/wp-content/plugins/send-chat-tools/send-chat-tools.php',
			$method->invoke( $this->instance )
		);
	}

	/**
	 * TEST: get_api_namespace()
	 */
	public function test_get_api_namespace() {
		$method = new ReflectionMethod( $this->instance, 'get_api_namespace' );
		$method->setAccessible( true );

		$this->assertSame(
			'send-chat-tools/v1',
			$method->invoke( $this->instance ),
		);
	}

	/**
	 * TEST: get_option_group()
	 */
	public function test_get_option_group() {
		$method = new ReflectionMethod( $this->instance, 'get_option_group' );
		$method->setAccessible( true );

		$this->assertSame(
			'send-chat-tools-settings',
			$method->invoke( $this->instance )
		);
	}

	/**
	 * TEST: get_table_name()
	 */
	public function test_get_table_name() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: get_sct_options()
	 */
	public function test_get_sct_options() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: set_sct_options()
	 */
	public function test_set_sct_options() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: get_required_php_version()
	 */
	public function test_get_required_php_version() {
		$this->assertMatchesRegularExpression( '/^[0-9]+\.?[0-9]?+\.?[0-9]?+\.?/', '7.4.0' );
		$this->assertMatchesRegularExpression( '/^[0-9]+\.?[0-9]?+\.?[0-9]?+\.?/', '8' );
		$this->assertMatchesRegularExpression( '/^[0-9]+\.?[0-9]?+\.?[0-9]?+\.?/', '8.1' );
		$this->assertMatchesRegularExpression( '/^[0-9]+\.?[0-9]?+\.?[0-9]?+\.?/', '8.1.5' );
	}

	/**
	 * TEST: get_official_directory()
	 */
	public function test_get_official_directory() {
		$method = new ReflectionMethod( $this->instance, 'get_official_directory' );
		$method->setAccessible( true );

		$this->assertSame(
			'https://wordpress.org/plugins/send-chat-tools/',
			$method->invoke( $this->instance ),
		);
	}

	/**
	 * TEST: get_wpcron_event_name()
	 */
	public function test_get_wpcron_event_name() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: get_developer_messages()
	 */
	public function test_get_developer_messages() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: send_tools()
	 */
	public function test_send_tools() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: logger()
	 */
	public function test_logger() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: api_regex()
	 */
	public function test_api_regex() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: console()
	 */
	public function test_console() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}
}
