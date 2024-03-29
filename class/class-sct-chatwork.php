<?php
/**
 * Class for Chatwork processing.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 1.5.0
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Class for Chatwork processing.
 */
class Sct_Chatwork extends Sct_Generate_Content_Abstract {
	/**
	 * Constructors that inherit from the abstract class constructor.
	 */
	private function __construct() {
		parent::__construct();
		$this->tool_name = 'chatwork';
	}

	/**
	 * Instantiate and return itself.
	 */
	public static function get_instance(): Sct_Chatwork {
		return new self();
	}

	/**
	 * A method to generate a Chatwork header.
	 */
	public function generate_header(): Sct_Chatwork {
		$this->header = [
			'method'  => 'POST',
			'headers' => 'X-ChatWorkToken: ' . $this->get_sct_options()[ $this->tool_name ]['api_token'],
			'body'    => $this->content,
		];

		return $this;
	}

	/**
	 * Generate comment content for Chatwork.
	 */
	public function generate_comment_content(): Sct_Chatwork {
		$article_title  = get_the_title( $this->original_data->comment_post_ID );
		$article_url    = get_permalink( $this->original_data->comment_post_ID );
		$comment_status = $this->generate_comment_approved_message( $this->tool_name, $this->original_data );
		$header_message = $this->generate_header_message( header_message: $this->get_send_text( 'comment_notify', 'title' ) );

		$this->content = [
			'body' =>
				'[info]' . $header_message .
				$this->get_send_text( 'comment_notify', 'article' ) . ': ' . $article_title . ' - ' . $article_url . "\n" .
				$this->get_send_text( 'comment_notify', 'commenter' ) . ': ' . $this->original_data->comment_author . '<' . $this->original_data->comment_author_email . ">\n" .
				$this->get_send_text( 'constant', 'date' ) . ': ' . $this->original_data->comment_date . "\n" .
				$this->get_send_text( 'comment_notify', 'comment' ) . ': ' . "\n" . $this->original_data->comment_content . "\n\n" .
				$this->get_send_text( 'comment_notify', 'url' ) . ': ' . $article_url . '#comment-' . $this->original_data->comment_ID . "\n" .
				'[hr]' .
				$this->get_send_text( 'comment_notify', 'status' ) . ': ' . $comment_status .
				$this->generate_context( $this->tool_name ) . '[/info]',
		];

		return $this;
	}

	/**
	 * Generate update content for Chatwork.
	 */
	public function generate_update_content(): Sct_Chatwork {
		$raw_data = $this->generate_update_raw_data( $this->original_data );

		$core    = isset( $raw_data->core ) ? rtrim( $raw_data->core ) . '[hr]' : $raw_data->core;
		$themes  = isset( $raw_data->themes ) ? rtrim( $raw_data->themes ) . '[hr]' : $raw_data->themes;
		$plugins = isset( $raw_data->plugins ) ? rtrim( $raw_data->plugins ) . '[hr]' : $raw_data->plugins;

		$header_message = $this->generate_header_message( header_message: $this->get_send_text( 'update_notify', 'title' ) );

		$this->content = [
			'body' =>
				'[info]' . $header_message . $core . $themes . $plugins .
				$this->get_send_text( 'update_notify', 'update' ) . "\n" . $this->get_send_text( 'update_notify', 'page' ) . ': ' . $raw_data->admin_url . "\n" .
				$this->generate_context( $this->tool_name ) . '[/info]',
		];
		return $this;
	}

	/**
	 * Generate developer content for Chatwork.
	 */
	public function generate_developer_content(): Sct_Chatwork {
		if ( isset( $this->original_data['title'] ) && isset( $this->original_data['message'] ) && array_key_exists( 'url', $this->original_data ) ) {
			$message_title = sprintf( $this->get_send_text( 'dev_notify', 'title' ), $this->original_data['title'] );
			$content       = '';

			$i = 0;
			foreach ( $this->original_data['message'] as $value ) {
				if ( $i >= 50 ) {
					break;
				}
				$content .= $value . "\n";
				++$i;
			}

			$header_message  = $this->generate_header_message( header_message: $message_title );
			$website_url     = $this->original_data['url']['website'];
			$update_page_url = $this->original_data['url']['update_page'];

			$website     = $website_url ? $this->get_send_text( 'dev_notify', 'website' ) . ': ' . $website_url . "\n" : null;
			$update_page = $update_page_url ? $this->get_send_text( 'dev_notify', 'detail' ) . ': ' . $update_page_url . "\n" : null;

			$this->content = [
				'body' =>
					'[info]' . $header_message . $content . "\n" . $website . $update_page .
					'[hr]' . $this->get_send_text( 'dev_notify', 'ignore' ) . ': ' . $this->original_data['key'] .
					$this->generate_context( $this->tool_name ) . '[/info]',
			];
		}
		return $this;
	}

	/**
	 * Generate login content for Chatwork.
	 */
	public function generate_login_content(): Sct_Chatwork {
		$header_message = $this->generate_header_message( header_message: $this->get_send_text( 'login_notify', 'title' ) );

		$user_name       = $this->original_data->data->user_login;
		$user_email      = $this->original_data->data->user_email;
		$login_user_name = $this->get_send_text( 'login_notify', 'user_name' ) . ": {$user_name}<$user_email>";

		$now_date   = gmdate( 'Y-m-d H:i:s', strtotime( current_datetime()->format( 'Y-m-d H:i:s' ) ) );
		$login_date = $this->get_send_text( 'constant', 'date' ) . ": {$now_date}";

		$os_browser = getenv( 'HTTP_USER_AGENT' );
		$login_env  = $this->get_send_text( 'login_notify', 'login_env' ) . ": {$os_browser}";

		$ip_address       = getenv( 'REMOTE_ADDR' );
		$login_ip_address = $this->get_send_text( 'login_notify', 'ip_address' ) . ": {$ip_address}";

		$this->content = [
			'body' =>
				'[info]' . $header_message .
				$login_user_name . "\n" . $login_date . "\n" . $login_env . "\n" . $login_ip_address . "\n\n" .
				$this->get_send_text( 'login_notify', 'unauthorized_login' ) . "\n" .
				$this->get_send_text( 'login_notify', 'disconnect' ) . "\n" .
				$this->site_url . '/wp-admin/profile.php' . "\n\n" .
				$this->generate_context( $this->tool_name ) . '[/info]',
		];
		return $this;
	}

	/**
	 * Generate Rinker content for Chatwork.
	 */
	public function generate_rinker_content(): Sct_Chatwork {
		$header_message     = $this->generate_header_message( header_message: $this->get_send_text( 'rinker_notify', 'title' ) );
		$discontinued_items = $this->format_rinker_items( $this->original_data );
		$after_message      = $this->get_send_text( 'rinker_notify', 'temporary' ) . "\n" . $this->get_send_text( 'rinker_notify', 'resume' );

		$this->content = [
			'body' =>
				'[info]' . $header_message . $discontinued_items . "\n" . $after_message . "\n" . $this->generate_context( $this->tool_name ) . '[/info]',
		];

		return $this;
	}
}
