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
		$this->is_error_mail = true;

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
		$this->mail_title = $this->get_send_text( 'update', 'title' );

		$plain_data = $this->generate_plain_update_message( $this->original_data );

		$header_message = $this->site_name . '(' . $this->site_url . ') ' . $this->get_send_text( 'update', 'title' );

		$this->content =
			$header_message . "\n\n" . $plain_data->core . $plain_data->themes . $plain_data->plugins .
			$this->get_send_text( 'update', 'update' ) . "\n" . $this->get_send_text( 'update', 'page' ) . ': ' . $plain_data->admin_url . "\n\n" .
			$this->generate_context( 'error_mail' ) . "\n" .
			esc_html__( 'Tool name:', 'send-chat-tools' ) . ucfirst( $this->tool_name ) . "\n" .
			esc_html__( 'Error code:', 'send-chat-tools' ) . $this->error_code;

		return $this;
	}

	/**
	 * Generate developer notify for Error Mail.
	 */
	public function generate_developer_message(): Sct_Error_Mail {
		$this->mail_title = sprintf( $this->get_send_text( 'dev_notify', 'title' ), esc_html( $this->original_data['title'] ), );

		$content = '';
		update_option( 'sct_dev_error', 'called' );

		$i = 0;
		foreach ( $this->original_data['message'] as $value ) {
			if ( $i >= 50 ) {
				break;
			}
			$content .= $value . "\n";
			$i++;
		}

		$header_message  = $this->site_name . '(' . $this->site_url . ') ' . sprintf( $this->get_send_text( 'dev_notify', 'title' ), esc_html( $this->original_data['title'] ), );
		$website_url     = $this->original_data['url']['website'];
		$update_page_url = $this->original_data['url']['update_page'];

		$title        = $header_message . "\n\n";
		$main_content = $content . "\n";
		$website      = $website_url ? $this->get_send_text( 'dev_notify', 'website' ) . ': <' . $website_url . ">\n" : null;
		$update_page  = $update_page_url ? $this->get_send_text( 'dev_notify', 'detail' ) . ': <' . $update_page_url . ">\n" : null;
		$ignore       = "\n" . $this->get_send_text( 'dev_notify', 'ignore' ) . ': ' . $this->original_data['key'] . "\n";

		$this->content =
			$title . $main_content . $website . $update_page . $ignore . "\n" .
			$this->generate_context( 'error_mail' ) . "\n" .
			esc_html__( 'Tool name:', 'send-chat-tools' ) . ucfirst( $this->tool_name ) . "\n" .
			esc_html__( 'Error code:', 'send-chat-tools' ) . $this->error_code;

		return $this;
	}

	/**
	 * Generate login notify for Error Mail.
	 */
	public function generate_login_message(): Sct_Error_Mail {
		$this->mail_title = $this->get_send_text( 'login_notify', 'title' );

		$user_name       = $this->original_data->data->user_login;
		$user_email      = $this->original_data->data->user_email;
		$login_user_name = $this->get_send_text( 'login_notify', 'user_name' ) . ": {$user_name}<$user_email>";

		$now_date   = gmdate( 'Y-m-d H:i:s', strtotime( current_datetime()->format( 'Y-m-d H:i:s' ) ) );
		$login_date = $this->get_send_text( 'constant', 'date' ) . ": {$now_date}";

		$os_browser = getenv( 'HTTP_USER_AGENT' );
		$login_env  = $this->get_send_text( 'login_notify', 'login_env' ) . ": {$os_browser}";

		$ip_address       = getenv( 'REMOTE_ADDR' );
		$login_ip_address = $this->get_send_text( 'login_notify', 'ip_address' ) . ": {$ip_address}";

		$this->content =
			$this->site_name . '(' . $this->site_url . ') ' . $this->mail_title . "\n\n" .
			$login_user_name . "\n" . $login_date . "\n" . $login_env . "\n" . $login_ip_address . "\n\n" .
			$this->get_send_text( 'login_notify', 'unauthorized_login' ) . "\n" .
			$this->get_send_text( 'login_notify', 'disconnect' ) . "\n" .
			$this->site_url . '/wp-admin/profile.php' . "\n\n" .
			$this->generate_context( 'error_mail' ) . "\n" .
			esc_html__( 'Tool name:', 'send-chat-tools' ) . ucfirst( $this->tool_name ) . "\n" .
			esc_html__( 'Error code:', 'send-chat-tools' ) . $this->error_code;

		return $this;
	}

	/**
	 * Generate Rinker notify for Error Mail.
	 */
	public function generate_rinker_message(): Sct_Error_Mail {
		$this->mail_title = $this->get_send_text( 'rinker_notify', 'title' );

		$items = $this->generate_rinker_content( $this->original_data );

		$after_message = $this->get_send_text( 'rinker_notify', 'temporary' ) . "\n" . $this->get_send_text( 'rinker_notify', 'resume' );

		$this->content =
			$this->site_name . '(' . $this->site_url . ') ' . $this->mail_title . "\n\n" .
			$items . "\n\n" . $after_message . "\n\n" .
			$this->generate_context( 'error_mail' ) . "\n" .
			esc_html__( 'Tool name:', 'send-chat-tools' ) . ucfirst( $this->tool_name ) . "\n" .
			esc_html__( 'Error code:', 'send-chat-tools' ) . $this->error_code;

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
