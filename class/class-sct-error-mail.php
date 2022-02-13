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
class Sct_Error_Mail extends Sct_Base {

	/**
	 * Return error code.
	 *
	 * @var int
	 */
	private $error_code;

	/**
	 * Comment ID.
	 *
	 * @var string
	 */
	private $comment_id;

	/**
	 * Construc.
	 *
	 * @param int    $error_code error code.
	 * @param string $comment_id comment ID.
	 */
	public function __construct( int $error_code, string $comment_id ) {
		$this->error_code = $error_code;
		$this->comment_id = $comment_id;
	}

	/**
	 * Make mail to, title, and message.
	 */
	public function make_contents() {
		global $wpdb;
		$comment          = get_comment( $this->comment_id );
		$site_name        = get_bloginfo( 'name' );
		$site_url         = get_bloginfo( 'url' );
		$comment_approved = $comment->comment_approved;
		$article_title    = get_the_title( $comment->comment_post_ID );
		$article_url      = get_permalink( $comment->comment_post_ID );
		$approved_url     = admin_url() . 'comment.php?action=approve&c=' . $comment->comment_ID;

		switch ( $comment_approved ) {
			case '1':
				$comment_status = esc_html__( 'Approved', 'send-chat-tools' );
				break;
			case '0':
				$comment_status = esc_html__( 'Unapproved', 'send-chat-tools' ) . '<<' . $approved_url . '|' . esc_html__( 'Click here to approve', 'send-chat-tools' ) . '>>';
				break;
			case 'spam':
				$comment_status = esc_html__( 'Spam', 'send-chat-tools' );
				break;
		}

		$mail_to      = get_option( 'admin_email' );
		$mail_title   = esc_html__( 'You have received a new comment', 'send-chat-tools' );
		$mail_message =
			$site_name . '( ' . $site_url . ' )' . esc_html__( 'new comment has been posted.', 'send-chat-tools' ) . "\n\n" .
			esc_html__( 'Commented article:', 'send-chat-tools' ) . $article_title . ' - ' . $article_url . "\n" .
			esc_html__( 'Author:', 'send-chat-tools' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
			esc_html__( 'Date and time:', 'send-chat-tools' ) . $comment->comment_date . "\n" .
			esc_html__( 'Text:', 'send-chat-tools' ) . "\n" . $comment->comment_content . "\n\n" .
			esc_html__( 'Comment URL:', 'send-chat-tools' ) . $article_url . '#comment-' . $comment->comment_ID . "\n\n" .
			esc_html__( 'Comment Status:', 'send-chat-tools' ) . $comment_status . "\n\n" .
			esc_html__( 'This message was sent by Send Chat Tools.', 'send-chat-tools' ) . "\n" .
			esc_html__( 'Possible that the message was not sent to the chat tool correctly.', 'send-chat-tools' ) . "\n" .
			esc_html__( 'Error code:', 'send-chat-tools' ) . $this->error_code;

		$this->send_mail( $mail_to, $mail_title, $mail_message );
	}

	/**
	 * Create WordPress Core, Theme, Plugin update content.
	 *
	 * @param array $options The outgoing message is stored.
	 */
	public function update_contents( array $options ) {
		$mail_to      = get_option( 'admin_email' );
		$mail_title   = esc_html__( 'WordPress update notification.', 'send-chat-tools' );
		$mail_message = json_decode( $options['body'], false )->text;

		$this->send_mail( $mail_to, $mail_title, $mail_message );
	}

	/**
	 * Send mail.
	 *
	 * @param string $mail_to mail to.
	 * @param string $mail_title mail title.
	 * @param string $mail_message mail message.
	 */
	private function send_mail( string $mail_to, string $mail_title, string $mail_message ) {
		wp_mail( $mail_to, $mail_title, $mail_message );
	}
}
