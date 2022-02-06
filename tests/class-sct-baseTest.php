<?php
declare( strict_types = 1 );

define( 'ABSPATH', '' );

require_once './class/class-sct-base.php';

/**
 * Test: Sct_Base
 */
class Sct_BaseTest extends PHPUnit\Framework\TestCase {
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
		$this->instance = new Sct_Base();
	}

	/**
	 * TEST: add_prefix()
	 */
	public function test_add_prefix() {
		$this->assertSame( 'sct_options', $this->instance->add_prefix( 'options' ) );
	}

	/**
	 * TEST: return_plugin_url()
	 */
	public function test_return_plugin_url() {
		$method = new ReflectionMethod( $this->instance, 'return_plugin_url' );
		$method->setAccessible( true );

		$this->assertSame(
			'https://example.com/wp-content/plugins/send-chat-tools',
			$method->invoke( $this->instance, 'send-chat-tools' ),
		);
	}

	/**
	 * TEST: return_plugin_dir()
	 */
	public function test_return_plugin_dir() {
		$method = new ReflectionMethod( $this->instance, 'return_plugin_dir' );
		$method->setAccessible( true );

		$this->assertSame(
			'/DocumentRoot/wp-content/plugins/send-chat-tools',
			$method->invoke( $this->instance, 'send-chat-tools' ),
		);
	}

	/**
	 * TEST: return_plugin_path()
	 */
	public function test_return_plugin_path() {
		$method = new ReflectionMethod( $this->instance, 'return_plugin_path' );
		$method->setAccessible( true );

		$this->assertSame(
			'/DocumentRoot/wp-content/plugins/send-chat-tools/send-chat-tools.php',
			$method->invoke( $this->instance )
		);
	}

	/**
	 * TEST: return_option_group()
	 */
	public function test_return_option_group() {
		$method = new ReflectionMethod( $this->instance, 'return_option_group' );
		$method->setAccessible( true );

		$this->assertSame(
			'send-chat-tools-settings',
			$method->invoke( $this->instance )
		);
	}
}
