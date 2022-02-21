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
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp() :void {
		$this->instance = new Sct_Developer_Notify();
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
