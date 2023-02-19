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
	 *
	 * @testWith [ 1, 1 ]
	 *           [ 100, 100 ]
	 *           [ 2800, 2800 ]
	 *
	 * @param int $comment_id Comment->comment_ID.
	 * @param int $expected Expected value.
	 */
	public function test_get_comment_data( int $comment_id, int $expected ): void {
		$method = new ReflectionMethod( $this->instance, 'get_comment_data' );
		$method->setAccessible( true );

		$this->assertSame( $expected, $method->invoke( $this->instance, $comment_id )->comment_ID, );
	}

	/**
	 * TEST: create_content()
	 */
	public function test_create_content() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: make_comment_message()
	 *
	 * @dataProvider make_comment_message_parameters
	 *
	 * @param string $tool    Chat tool name.
	 * @param object $comment Comment data.
	 */
	public function test_make_comment_message( string $tool, object $comment ): void {
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
	public function test_get_send_text( $type, $param, $expected ): void {
		$method = new ReflectionMethod( $this->instance, 'get_send_text' );
		$method->setAccessible( true );

		$this->assertSame( $expected, $method->invoke( $this->instance, $type, $param ), );
	}

	/**
	 * TEST: make_comment_approved_message()
	 *
	 * @dataProvider make_comment_approved_message_parameters
	 *
	 * @param string $tool_name Chat tool name.
	 * @param object $comment WordPress comment date.
	 * @param string $expected Expected value.
	 */
	public function test_make_comment_approved_message( string $tool_name, object $comment, string $expected ): void {
		$method = new ReflectionMethod( $this->instance, 'make_comment_approved_message' );
		$method->setAccessible( true );

		$this->assertSame( $expected, $method->invoke( $this->instance, $tool_name, $comment ) );
	}

	/**
	 * TEST: make_context()
	 *
	 * @testWith [ "slack" ]
	 *           [ "discord" ]
	 *           [ "chatwork" ]
	 *
	 * @param string $tool_name Chat tool name.
	 */
	public function test_make_context( string $tool_name ): void {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: make_comment_message()
	 */
	public function make_comment_message_parameters(): array {
		$comment                       = new stdClass();
		$comment->comment_post_ID      = '123';
		$comment->comment_ID           = '111';
		$comment->comment_approved     = '1';
		$comment->comment_author       = 'test author';
		$comment->comment_author_email = 'test@example.com';
		$comment->comment_date         = '';
		$comment->comment_content      = '';

		return [
			'With Slack'    => [
				'slack',
				$comment,
			],
			'With Discord'  => [
				'discord',
				$comment,
			],
			'With Chatwork' => [
				'chatwork',
				$comment,
			],
		];
	}

	/**
	 * TEST: make_comment_approved_message
	 */
	public function make_comment_approved_message_parameters(): array {
		require_once './tests/lib/wordpress-functions.php';

		$comment                   = new stdClass();
		$comment->comment_ID       = '123';
		$comment->comment_approved = '1';

		$comment_pending                   = new stdClass();
		$comment_pending->comment_ID       = '123';
		$comment_pending->comment_approved = '0';

		$comment_spam                   = new stdClass();
		$comment_spam->comment_ID       = '123';
		$comment_spam->comment_approved = 'spam';

		$admin_url    = admin_url();
		$approved_url = $admin_url . 'comment.php?action=approve&c=';
		$unapproved   = 'Unapproved';
		$click        = 'Click here to approve';

		return [
			'Comment status is Approved'                 => [
				'slack',
				$comment,
				'Approved',
			],
			'Comment status is unapproved with slack'    => [
				'slack',
				$comment_pending,
				$unapproved . '<<' . $approved_url . $comment_pending->comment_ID . '|' . $click . '>>',
			],
			'Comment status is unapproved with discord'  => [
				'discord',
				$comment_pending,
				$unapproved . ' >> ' . $click . '( ' . $approved_url . $comment_pending->comment_ID . ' )',
			],
			'Comment status is unapproved with chatwork' => [
				'chatwork',
				$comment_pending,
				$unapproved . "\n" . $click . ' ' . $approved_url . $comment_pending->comment_ID,
			],
			'Comment status is spam'                     => [
				'spam',
				$comment_spam,
				'Spam',
			],
		];
	}
}
