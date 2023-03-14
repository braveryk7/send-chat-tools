<?php
declare( strict_types = 1 );

/**
 * Test: Sct_Generate_Content
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

		require_once './class/class-sct-generate-content.php';
		require_once './tests/lib/wordpress-functions.php';
	}

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp() :void {
		$this->instance = new Sct_Generate_Content();
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
	 * TEST: generate_content()
	 */
	public function test_generate_content() {
		$this->markTestIncomplete( 'This test is incomplete.' );
	}

	/**
	 * TEST: generate_comment_message()
	 *
	 * @dataProvider generate_comment_message_parameters
	 *
	 * @param string $tool    Chat tool name.
	 * @param object $comment Comment data.
	 */
	public function test_generate_comment_message( string $tool, object $comment ): void {
		$method = new ReflectionMethod( $this->instance, 'generate_comment_message' );
		$method->setAccessible( true );

		$get_send_text = new ReflectionMethod( $this->instance, 'get_send_text' );
		$get_send_text->setAccessible( true );

		$approved_message = new ReflectionMethod( $this->instance, 'generate_comment_approved_message' );
		$approved_message->setAccessible( true );

		$generate_context = new ReflectionMethod( $this->instance, 'generate_context' );
		$generate_context->setAccessible( true );

		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$article_title  = get_the_title( $comment->comment_post_ID );
		$article_url    = get_permalink( $comment->comment_post_ID );
		$comment_status = $approved_message->invoke( $this->instance, $tool, $comment );

		$expected = match ( $tool ) {
			'slack' => ( function ( $tool, $comment ) use ( $site_name, $site_url, $article_title, $article_url, $comment_status, $get_send_text, $generate_context ) {
				$header_emoji     = ':mailbox_with_mail:';
				$header_message   = "{$header_emoji} {$site_name}({$site_url})" . $get_send_text->invoke( $this->instance, 'comment', 'title' );
				$comment_article  = '*' . $get_send_text->invoke( $this->instance, 'comment', 'article' ) . "*<{$article_url}|{$article_title}>";
				$author           = '*' . $get_send_text->invoke( $this->instance, 'comment', 'author' ) . "*\n{$comment->comment_author}<{$comment->comment_author_email}>";
				$date             = '*' . $get_send_text->invoke( $this->instance, 'comment', 'date' ) . "*\n{$comment->comment_date}";
				$comment_content  = '*' . $get_send_text->invoke( $this->instance, 'comment', 'content' ) . "*\n{$comment->comment_content}";
				$comment_url      = '*' . $get_send_text->invoke( $this->instance, 'comment', 'url' ) . "*\n{$article_url}#comment-{$comment->comment_ID}";
				$comment_statuses = '*' . $get_send_text->invoke( $this->instance, 'comment', 'status' ) . "*\n{$comment_status}";
				$context          = $generate_context->invoke( $this->instance, $tool );

				$blocks = new Sct_Slack_Blocks();

				return [
					'text'   => $header_message,
					'blocks' => [
						$blocks->header( 'plain_text', $header_message, true ),
						$blocks->single_column( 'mrkdwn', $comment_article ),
						$blocks->divider(),
						$blocks->two_column( [ 'mrkdwn', $author ], [ 'mrkdwn', $date ] ),
						$blocks->single_column( 'mrkdwn', $comment_content ),
						$blocks->two_column( [ 'mrkdwn', $comment_url ], [ 'mrkdwn', $comment_statuses ] ),
						$blocks->divider(),
						$blocks->context( 'mrkdwn', $context ),
					],
				];
			} ),
			'discord' => ( function ( $tool, $comment ) use ( $site_name, $site_url, $article_title, $article_url, $comment_status, $get_send_text, $generate_context ) {
				return $site_name . '( <' . $site_url . '> )' . $get_send_text->invoke( $this->instance, 'comment', 'title' ) . "\n\n" .
					$get_send_text->invoke( $this->instance, 'comment', 'article' ) . $article_title . ' - <' . $article_url . '>' . "\n" .
					$get_send_text->invoke( $this->instance, 'comment', 'author' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
					$get_send_text->invoke( $this->instance, 'comment', 'date' ) . $comment->comment_date . "\n" .
					$get_send_text->invoke( $this->instance, 'comment', 'content' ) . "\n" . $comment->comment_content . "\n\n" .
					$get_send_text->invoke( $this->instance, 'comment', 'url' ) . '<' . $article_url . '#comment-' . $comment->comment_ID . '>' . "\n\n" .
					$get_send_text->invoke( $this->instance, 'comment', 'status' ) . $comment_status . "\n\n" .
					$generate_context->invoke( $this->instance, $tool );
			} ),
			'chatwork' =>  ( function ( $tool, $comment ) use ( $site_name, $site_url, $article_title, $article_url, $comment_status, $get_send_text, $generate_context ) {
				return [
					'body' =>
						'[info][title]' . $site_name . '(' . $site_url . ')' . $get_send_text->invoke( $this->instance, 'comment', 'title' ) . '[/title]' .
						$get_send_text->invoke( $this->instance, 'comment', 'article' ) . $article_title . ' - ' . $article_url . "\n" .
						$get_send_text->invoke( $this->instance, 'comment', 'author' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
						$get_send_text->invoke( $this->instance, 'comment', 'date' ) . $comment->comment_date . "\n" .
						$get_send_text->invoke( $this->instance, 'comment', 'content' ) . "\n" . $comment->comment_content . "\n\n" .
						$get_send_text->invoke( $this->instance, 'comment', 'url' ) . $article_url . '#comment-' . $comment->comment_ID . "\n" .
						'[hr]' .
						$get_send_text->invoke( $this->instance, 'comment', 'status' ) . $comment_status .
						$generate_context->invoke( $this->instance, $tool ) .
						'[/info]',
				];
			} ),
		};

		$this->assertSame( $expected( $tool, $comment ), $method->invoke( $this->instance, $tool, $comment ), );
	}

	/**
	 * TEST: generate_update_message()
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
	 * TEST: generate_comment_approved_message()
	 *
	 * @dataProvider generate_comment_approved_message_parameters
	 *
	 * @param string $tool_name Chat tool name.
	 * @param object $comment WordPress comment date.
	 * @param string $expected Expected value.
	 */
	public function test_generate_comment_approved_message( string $tool_name, object $comment, string $expected ): void {
		$method = new ReflectionMethod( $this->instance, 'generate_comment_approved_message' );
		$method->setAccessible( true );

		$this->assertSame( $expected, $method->invoke( $this->instance, $tool_name, $comment ) );
	}

	/**
	 * TEST: generate_context()
	 *
	 * @testWith [ "slack" ]
	 *           [ "discord" ]
	 *           [ "chatwork" ]
	 *
	 * @param string $tool_name Chat tool name.
	 */
	public function test_generate_context( string $tool_name ): void {
		$method = new ReflectionMethod( $this->instance, 'generate_context' );
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
	 * TEST: generate_comment_message()
	 */
	public function generate_comment_message_parameters(): array {
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
	 * TEST: generate_comment_approved_message
	 */
	public function generate_comment_approved_message_parameters(): array {
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
