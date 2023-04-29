<?php
/**
 * Send error mail.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 0.1.2
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Send error mail.
 */
class Sct_Error_Mail extends Sct_Generate_Content_Abstract {
	/**
	 * Property to store error code.
	 *
	 * @var int $error_code.
	 */
	private int $error_code;

	/**
	 * Property to store comment ID.
	 *
	 * @var string $comment_id.
	 */
	private string $comment_id;

	/**
	 * Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Instantiate and return itself.
	 */
	public static function get_instance(): Sct_Error_Mail {
		return new self();
	}

	/**
	 * A method to generate a Error Mail header.
	 */
	public function generate_header(): Sct_Error_Mail {
		return $this;

	}

	/**
	 * Generate comment notify for Error Mail.
	 *
	 * @param object $comment Comment data.
	 */
	public function generate_comment_content( object $comment, ): Sct_Error_Mail {
		$comment          = get_comment( $this->comment_id );
		$comment_approved = $comment->comment_approved;
		$article_title    = get_the_title( $comment->comment_post_ID );
		$article_url      = get_permalink( $comment->comment_post_ID );
		$approved_url     = admin_url() . 'comment.php?action=approve&c=' . $comment->comment_ID;

		$comment_status = match ( $comment_approved ) {
			'1'    => $comment_status = esc_html__( 'Approved', 'send-chat-tools' ),
			'0'    => esc_html__( 'Unapproved', 'send-chat-tools' ) . '<<' . $approved_url . '|' . esc_html__( 'Click here to approve', 'send-chat-tools' ) . '>>',
			'spam' => esc_html__( 'Spam', 'send-chat-tools' ),
		};

		$mail_to      = get_option( 'admin_email' );
		$mail_title   = esc_html__( 'You have received a new comment', 'send-chat-tools' );
		$mail_message =
			$this->site_name . '( ' . $this->site_url . ' )' . esc_html__( 'new comment has been posted.', 'send-chat-tools' ) . "\n\n" .
			esc_html__( 'Commented article:', 'send-chat-tools' ) . $article_title . ' - ' . $article_url . "\n" .
			esc_html__( 'Author:', 'send-chat-tools' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
			esc_html__( 'Date and time:', 'send-chat-tools' ) . $comment->comment_date . "\n" .
			esc_html__( 'Text:', 'send-chat-tools' ) . "\n" . $comment->comment_content . "\n\n" .
			esc_html__( 'Comment URL:', 'send-chat-tools' ) . $article_url . '#comment-' . $comment->comment_ID . "\n\n" .
			esc_html__( 'Comment Status:', 'send-chat-tools' ) . $comment_status . "\n\n" .
			esc_html__( 'This message was sent by Send Chat Tools.', 'send-chat-tools' ) . "\n" .
			esc_html__( 'Possible that the message was not sent to the chat tool correctly.', 'send-chat-tools' ) . "\n\n" .
			esc_html__( 'Tool name:', 'send-chat-tools' ) . ucfirst( $this->tool_name ) . "\n" .
			esc_html__( 'Error code:', 'send-chat-tools' ) . $this->error_code;

		$this->content = [ $mail_to, $mail_title, $mail_message ];

		return $this;
	}

	/**
	 * Generate update notify for Error Mail.
	 *
	 * @param array $update_content Update data.
	 */
	public function generate_update_content( array $update_content ): Sct_Error_Mail {
		return $this;
	}

	/**
	 * Generate developer notify for Error Mail.
	 *
	 * @param array $developer_message Developer message.
	 */
	public function generate_developer_message( array $developer_message ): Sct_Error_Mail {
		return $this;
	}

	/**
	 * Generate login notify for Error Mail.
	 *
	 * @param object $user User object.
	 */
	public function generate_login_message( object $user ): Sct_Error_Mail {
		return $this;
	}

	/**
	 * Generate Rinker notify for Error Mail.
	 *
	 * @param array $rinker_exists_items Rinker exists items.
	 */
	public function generate_rinker_message( array $rinker_exists_items ): Sct_Error_Mail {
		return $this;
	}

	/**
	 * Generate WordPress Core, Theme, Plugin update content.
	 *
	 * @param object $plain_data The outgoing message is stored.
	 */
	public function update_contents( object $plain_data ): array {
		$mail_to      = get_option( 'admin_email' );
		$mail_title   = esc_html__( 'WordPress update notification.', 'send-chat-tools' );
		$mail_message =
			$plain_data->site_name . '(' . $plain_data->site_url . ')' . $plain_data->update_title . "\n\n" .
			$plain_data->core . $plain_data->themes . $plain_data->plugins . $plain_data->update_text . "\n" .
			$plain_data->update_page . $plain_data->admin_url . "\n\n" .
			esc_html__( 'This message was sent by Send Chat Tools.', 'send-chat-tools' ) . "\n" .
			esc_html__( 'Possible that the message was not sent to the chat tool correctly.', 'send-chat-tools' ) . "\n\n" .
			esc_html__( 'Tool name:', 'send-chat-tools' ) . ucfirst( $this->tool_name ) . "\n" .
			esc_html__( 'Error code:', 'send-chat-tools' ) . $this->error_code;

		return [ $mail_to, $mail_title, $mail_message ];
	}

	/**
	 * Send mail.
	 *
	 * @param string $mail_to mail to.
	 * @param string $mail_title mail title.
	 * @param string $mail_message mail message.
	 */
	public function send_mail( string $mail_to, string $mail_title, string $mail_message ): void {
		wp_mail( $mail_to, $mail_title, $mail_message );
	}
}
