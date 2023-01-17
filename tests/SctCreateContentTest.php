<?php
declare( strict_types = 1 );

/**
 * Test: Sct_Create_Content
 */
class SctCreateContentTest extends PHPUnit\Framework\TestCase {
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

		require_once './class/class-sct-create-content.php';
		require_once './tests/lib/wordpress-functions.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp() :void {
		$this->instance = new Sct_Create_Content();
	}

	/**
	 * TEST: controller()
	 */
	public function test_controller() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: get_send_status()
	 */
	public function test_get_send_status() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: get_comment_data()
	 */
	public function test_get_comment_data() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: create_content()
	 */
	public function test_create_content() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: create_comment_message()
	 */
	public function test_create_comment_message() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: create_update_message()
	 */
	public function test_create_update_message() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: create_developer_message()
	 */
	public function test_create_developer_message() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: get_send_text()
	 *
	 * @testWith [ "comment", "title", "new comment has been posted." ]
	 *           [ "update", "page", "Update Page:" ]
	 *           [ "dev_notify", "detail", "Update details" ]
	 *
	 * @param string $type Message type.
	 * @param string $param Message content.
	 * @param string $expected Expected value.
	 */
	public function test_get_send_text( $type, $param, $expected ) {
		$method = new ReflectionMethod( $this->instance, 'get_send_text' );
		$method->setAccessible( true );

		$this->assertSame( $expected, $method->invoke( $this->instance, $type, $param ), );
	}

	/**
	 * TEST: get_comment_approved_message()
	 */
	public function test_get_comment_approved_message() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: create_context()
	 */
	public function test_create_context() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}
}
