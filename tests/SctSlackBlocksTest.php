<?php
declare( strict_types = 1 );

/**
 * Test: Sct_Slack_Blocks
 */
class SctSlackBlocksTest extends PHPUnit\Framework\TestCase {
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

		require_once './class/class-sct-slack-blocks.php';
		require_once './tests/lib/wordpress-functions.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp() :void {
		$this->instance = new Sct_Slack_Blocks();
	}

	/**
	 * TEST: header()
	 */
	public function test_header() {
		$type  = 'plain_text';
		$text  = 'test message';
		$emoji = true;

		$header = [
			'type' => 'header',
			'text' => [
				'type'  => $type,
				'text'  => $text,
				'emoji' => $emoji,
			],
		];

		$this->assertSame( $header, $this->instance->header( $type, $text, $emoji ) );
	}

	/**
	 * TEST: single_column()
	 */
	public function test_single_column() {
		$type = 'section';
		$text = 'test message';

		$column = [
			'type' => 'section',
			'text' => [
				'type' => $type,
				'text' => $text,
			],
		];

		$this->assertSame( $column, $this->instance->single_column( $type, $text ) );
	}

	/**
	 * TEST: two_column()
	 */
	public function test_two_column() {
		$content1st = [
			'mrkdwn',
			'test_message',
		];
		$content2nd = $content1st;

		$column = [
			'type'   => 'section',
			'fields' => [
				[
					'type' => $content1st[0],
					'text' => $content1st[1],
				],
				[
					'type' => $content2nd[0],
					'text' => $content2nd[1],
				],
			],
		];

		$this->assertSame( $column, $this->instance->two_column( $content1st, $content2nd ) );
	}

	/**
	 * TEST: context()
	 */
	public function test_context() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: divider()
	 */
	public function test_divider() {
		$this->assertSame(
			[ 'type' => 'divider' ],
			$this->instance->divider()
		);
	}
}
