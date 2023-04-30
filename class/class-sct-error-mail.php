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
	 * Property to store mail to.
	 *
	 * @var string $mail_to.
	 */
	private string $mail_to;

	/**
	 * Property to store mail title.
	 *
	 * @var string $mail_title.
	 */
	private string $mail_title;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->mail_to = get_option( 'admin_email' );
	}

	/**
	 * Instantiate and return itself.
	 */
	public static function get_instance(): Sct_Error_Mail {
		return new self();
	}

	/**
	 * Method to set properties necessary for error mail generation.
	 *
	 * @param int          $error_code    Error code.
	 * @param string       $tool_name     Tool name.
	 * @param object|array $original_data Original data.
	 */
	public function set_error_mail_properties( int $error_code, string $tool_name, object | array $original_data ): Sct_Error_Mail {
		$this->error_code    = $error_code;
		$this->tool_name     = $tool_name;
		$this->original_data = $original_data;

		return $this;
	}

	/**
	 * A method to generate a Error Mail header.
	 */
	public function generate_header(): Sct_Error_Mail {
		return $this;
	}

	/**
	 * Generate comment notify for Error Mail.
	 */
	public function generate_comment_content(): Sct_Error_Mail {
		$this->mail_title = esc_html__( 'You have received a new comment', 'send-chat-tools' );

		$article_title  = get_the_title( $this->original_data->comment_post_ID );
		$article_url    = get_permalink( $this->original_data->comment_post_ID );
		$comment_status = $this->generate_comment_approved_message( $this->tool_name, $this->original_data );
		$header_message = $this->site_name . '(' . $this->site_url . ') ' . $this->get_send_text( 'comment', 'title' );

		$this->content =
			$header_message . "\n\n" .
			$this->get_send_text( 'comment', 'article' ) . ': ' . $article_title . ' - ' . $article_url . "\n" .
			$this->get_send_text( 'comment', 'commenter' ) . ': ' . $this->original_data->comment_author . '<' . $this->original_data->comment_author_email . ">\n" .
			$this->get_send_text( 'constant', 'date' ) . ': ' . $this->original_data->comment_date . "\n" .
			$this->get_send_text( 'comment', 'comment' ) . ': ' . "\n" . $this->original_data->comment_content . "\n\n" .
			$this->get_send_text( 'comment', 'url' ) . ': ' . $article_url . '#comment-' . $this->original_data->comment_ID . "\n" .
			$this->get_send_text( 'comment', 'status' ) . ': ' . $comment_status . "\n\n" .
			esc_html__( 'This message was sent by Send Chat Tools.', 'send-chat-tools' ) . "\n" .
			esc_html__( 'Possible that the message was not sent to the chat tool correctly.', 'send-chat-tools' ) . "\n\n" .
			esc_html__( 'Tool name:', 'send-chat-tools' ) . ucfirst( $this->tool_name ) . "\n" .
			esc_html__( 'Error code:', 'send-chat-tools' ) . $this->error_code;

		return $this;
	}

	/**
	 * Generate update notify for Error Mail.
	 */
	public function generate_update_content(): Sct_Error_Mail {
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
	 */
	public function send_mail(): void {
		wp_mail( $this->mail_to, $this->mail_title, $this->content );
	}
}
