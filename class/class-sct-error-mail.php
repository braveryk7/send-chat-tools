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
	 * Generate comment content for Error Mail.
	 */
	public function generate_comment_content(): Sct_Error_Mail {
		$this->mail_title = $this->generate_header_message( header_message: $this->get_send_text( 'comment_notify', 'title' ) );

		$article_title  = get_the_title( $this->original_data->comment_post_ID );
		$article_url    = get_permalink( $this->original_data->comment_post_ID );
		$comment_status = $this->generate_comment_approved_message( $this->tool_name, $this->original_data );

		$this->content =
			$this->get_send_text( 'comment_notify', 'article' ) . ': ' . $article_title . ' - ' . $article_url . "\n" .
			$this->get_send_text( 'comment_notify', 'commenter' ) . ': ' . $this->original_data->comment_author . '<' . $this->original_data->comment_author_email . ">\n" .
			$this->get_send_text( 'constant', 'date' ) . ': ' . $this->original_data->comment_date . "\n" .
			$this->get_send_text( 'comment_notify', 'comment' ) . ': ' . "\n" . $this->original_data->comment_content . "\n\n" .
			$this->get_send_text( 'comment_notify', 'url' ) . ': ' . $article_url . '#comment-' . $this->original_data->comment_ID . "\n" .
			$this->get_send_text( 'comment_notify', 'status' ) . ': ' . $comment_status . "\n\n" .
			$this->generate_context( 'error_mail' ) . "\n\n" .
			$this->generate_error_message();

		return $this;
	}

	/**
	 * Generate update content for Error Mail.
	 */
	public function generate_update_content(): Sct_Error_Mail {
		$this->mail_title = $this->generate_header_message( header_message: $this->get_send_text( 'update_notify', 'title' ) );
		$raw_data         = $this->generate_update_raw_data( $this->original_data );

		$this->content =
			$raw_data->core . $raw_data->themes . $raw_data->plugins .
			$this->get_send_text( 'update_notify', 'update' ) . "\n" . $this->get_send_text( 'update_notify', 'page' ) . ': ' . $raw_data->admin_url . "\n\n" .
			$this->generate_context( 'error_mail' ) . "\n" .
			$this->generate_error_message();

		return $this;
	}

	/**
	 * Generate developer content for Error Mail.
	 */
	public function generate_developer_content(): Sct_Error_Mail {
		$this->mail_title = $this->generate_header_message(
			header_message: sprintf( $this->get_send_text( 'dev_notify', 'title' ), $this->original_data['title'] )
		);

		$content = '';

		$i = 0;
		foreach ( $this->original_data['message'] as $value ) {
			if ( $i >= 50 ) {
				break;
			}
			$content .= $value . "\n";
			++$i;
		}

		$website_url     = $this->original_data['url']['website'];
		$update_page_url = $this->original_data['url']['update_page'];

		$main_content = $content . "\n";
		$website      = $website_url ? $this->get_send_text( 'dev_notify', 'website' ) . ': <' . $website_url . ">\n" : null;
		$update_page  = $update_page_url ? $this->get_send_text( 'dev_notify', 'detail' ) . ': <' . $update_page_url . ">\n" : null;
		$ignore       = "\n" . $this->get_send_text( 'dev_notify', 'ignore' ) . ': ' . $this->original_data['key'] . "\n";

		$this->content =
			$main_content . $website . $update_page . $ignore . "\n" .
			$this->generate_context( 'error_mail' ) . "\n" .
			$this->generate_error_message();

		return $this;
	}

	/**
	 * Generate login content for Error Mail.
	 */
	public function generate_login_content(): Sct_Error_Mail {
		$this->mail_title = $this->generate_header_message( header_message: $this->get_send_text( 'login_notify', 'title' ) );

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
			$login_user_name . "\n" . $login_date . "\n" . $login_env . "\n" . $login_ip_address . "\n\n" .
			$this->get_send_text( 'login_notify', 'unauthorized_login' ) . "\n" .
			$this->get_send_text( 'login_notify', 'disconnect' ) . "\n" .
			$this->site_url . '/wp-admin/profile.php' . "\n\n" .
			$this->generate_context( 'error_mail' ) . "\n" .
			$this->generate_error_message();

		return $this;
	}

	/**
	 * Generate Rinker content for Error Mail.
	 */
	public function generate_rinker_content(): Sct_Error_Mail {
		$this->mail_title = $this->generate_header_message( header_message: $this->get_send_text( 'rinker_notify', 'title' ) );

		$items = $this->format_rinker_items( $this->original_data );

		$after_message = $this->get_send_text( 'rinker_notify', 'temporary' ) . "\n" . $this->get_send_text( 'rinker_notify', 'resume' );

		$this->content =
			$items . "\n\n" . $after_message . "\n\n" .
			$this->generate_context( 'error_mail' ) . "\n" .
			$this->generate_error_message();

		return $this;
	}

	/**
	 * Method to generate tool name and error code for error mail.
	 */
	private function generate_error_message(): string {
		return __( 'Tool name:', 'send-chat-tools' ) . ucfirst( $this->tool_name ) . "\n" . __( 'Error code:', 'send-chat-tools' ) . $this->error_code;
	}

	/**
	 * Send mail.
	 */
	public function send_mail(): void {
		wp_mail( $this->mail_to, $this->mail_title, $this->content );
	}
}
