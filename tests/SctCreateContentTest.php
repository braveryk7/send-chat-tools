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
	 * TEST: make_comment_approved_message()
	 *
	 * @dataProvider make_comment_approved_message_parameters
	 *
	 * @param string $tool_name Chat tool name.
	 * @param object $comment WordPress comment date.
	 * @param string $expected Expected value.
	 */
	public function test_make_comment_approved_message( string $tool_name, object $comment, string $expected ) {
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
	public function test_make_context( string $tool_name ) {
		$method = new ReflectionMethod( $this->instance, 'make_context' );
		$method->setAccessible( true );

		$message = [
			0 => esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ),
			1 => esc_html__( 'WordPress Plugin Directory', 'send-chat-tools' ),
			2 => esc_html__( 'Send Chat Tools Official Page', 'send-chat-tools' ),
		];

		$wordpress_directory = 'https://wordpress.org/plugins/send-chat-tools/';
		$official_web_site   = 'https://www.braveryk7.com/portfolio/send-chat-tools/';

		$context = match ( $tool_name ) {
			'slack'    => $message[0] . "\n<" . $wordpress_directory . '|' . $message[1] . '> / <' . $official_web_site . '|' . $message[2] . '>',
			'discord'  => $message[0] . "\n" . $message[1] . ' <' . $wordpress_directory . '>' . "\n" . $message[2] . ' <' . $official_web_site . '>',
			'chatwork' => '[hr]' . $message[0] . "\n" . $message[1] . ' ' . $wordpress_directory . "\n" . $message[2] . ' ' . $official_web_site,
		};

		$this->assertSame( $context, $method->invoke( $this->instance, $tool_name, ), );
	}

	/**
	 * TEST: make_comment_approved_message
	 */
	public function make_comment_approved_message_parameters() {
		$comment                   = new stdClass();
		$comment->comment_ID       = '123';
		$comment->comment_approved = '1';

		$comment_pending                   = new stdClass();
		$comment_pending->comment_ID       = '123';
		$comment_pending->comment_approved = '0';

		$comment_spam                   = new stdClass();
		$comment_spam->comment_ID       = '123';
		$comment_spam->comment_approved = 'spam';

		$admin_url = 'https://expamle.com/';

		return [
			'Comment status is Approved' => [
				'slack',
				$comment,
				'Approved',
			],
			[
				'slack',
				$comment_pending,
				'Unapproved<<' . $admin_url . 'comment.php?action=approve&c=' . $comment_pending->comment_ID . '|Click here to approve>>',
			],
			[
				'discord',
				$comment_pending,
				'Unapproved >> Click here to approve( ' . $admin_url . 'comment.php?action=approve&c=' . $comment_pending->comment_ID . ' )',
			],
			[
				'chatwork',
				$comment_pending,
				'Unapproved' . "\n" . 'Click here to approve ' . $admin_url . 'comment.php?action=approve&c=' . $comment_pending->comment_ID,
			],
			[
				'spam',
				$comment_spam,
				'Spam',
			],
		];
	}
}
