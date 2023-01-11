<?php
declare( strict_types = 1 );

/**
 * Test: Sct_Admin_Page
 */
class SctAdminPageTest extends PHPUnit\Framework\TestCase {
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

		require_once './class/class-sct-admin-page.php';
		require_once './tests/lib/wordpress-functions.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp() :void {
		$this->instance = new Sct_Admin_Page();
	}

	/**
	 * TEST: add_menu()
	 */
	public function test_add_menu() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: add_settings_links()
	 */
	public function test_add_settings_links() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: add_scripts()
	 */
	public function test_add_scripts() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: register()
	 */
	public function test_register() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: register_rest_api()
	 */
	public function test_register_rest_api() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: get_wordpress_permission()
	 */
	public function test_get_wordpress_permission() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: readable_api()
	 */
	public function test_readable_api() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: settings_page()
	 */
	public function test_settings_page() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: old_settings_page()
	 */
	public function test_old_settings_page() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}
}
