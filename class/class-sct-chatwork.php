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
	 * Abstract method to create comment data to be sent to chat tools.
	 *
	 * @param object $comment Comment data.
	 */
	public function generate_comment_content( object $comment, ): Sct_Chatwork {
		$this->comment = $comment;

		$article_title  = get_the_title( $comment->comment_post_ID );
		$article_url    = get_permalink( $comment->comment_post_ID );
		$comment_status = $this->generate_comment_approved_message( $this->tool_name, $comment );

		$this->content = [
			'body' =>
				'[info][title]' . $this->site_name . '(' . $this->site_url . ')' . $this->get_send_text( 'comment', 'title' ) . '[/title]' .
				$this->get_send_text( 'comment', 'article' ) . $article_title . ' - ' . $article_url . "\n" .
				$this->get_send_text( 'comment', 'author' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
				$this->get_send_text( 'constant', 'date' ) . $comment->comment_date . "\n" .
				$this->get_send_text( 'comment', 'content' ) . "\n" . $comment->comment_content . "\n\n" .
				$this->get_send_text( 'comment', 'url' ) . $article_url . '#comment-' . $comment->comment_ID . "\n" .
				'[hr]' .
				$this->get_send_text( 'comment', 'status' ) . $comment_status .
				$this->generate_context( $this->tool_name ) . '[/info]',
		];

		return $this;
	}

	/**
	 * Generate update notifications for Chatwork.
	 *
	 * @param array $update_content Update data.
	 */
	public function generate_update_content( array $update_content ): Sct_Chatwork {
		$plain_data = $this->generate_plain_update_message( $update_content );

		$core    = isset( $plain_data->core ) ? rtrim( $plain_data->core ) . '[hr]' : $plain_data->core;
		$themes  = isset( $plain_data->themes ) ? rtrim( $plain_data->themes ) . '[hr]' : $plain_data->themes;
		$plugins = isset( $plain_data->plugins ) ? rtrim( $plain_data->plugins ) . '[hr]' : $plain_data->plugins;

		$this->content = [
			'body' =>
				'[info][title]' . $plain_data->site_name . '( ' . $plain_data->site_url . ' )' . $plain_data->update_title . '[/title]' .
				$core . $themes . $plugins . $plain_data->update_text . "\n" . $plain_data->update_page . $plain_data->admin_url . "\n" .
				$this->generate_context( $this->tool_name ) . '[/info]',
		];
		return $this;
	}

	/**
	 * Generate developer message for Chatwork.
	 *
	 * @param array $developer_message Developer message.
	 */
	public function generate_developer_message( array $developer_message ): Sct_Chatwork {
		if ( isset( $developer_message['title'] ) && isset( $developer_message['message'] ) && array_key_exists( 'url', $developer_message ) ) {
			$message_title = sprintf( $this->get_send_text( 'dev_notify', 'title' ), esc_html( $developer_message['title'] ), );
			$content       = '';

			$i = 0;
			foreach ( $developer_message['message'] as $value ) {
				if ( $i >= 50 ) {
					break;
				}
				$content .= $value . "\n";
				$i++;
			}

			$website_url     = $developer_message['url']['website'];
			$update_page_url = $developer_message['url']['update_page'];

			$website       = $website_url ? $this->get_send_text( 'dev_notify', 'website' ) . ': ' . $website_url . "\n" : null;
			$update_page   = $update_page_url ? $this->get_send_text( 'dev_notify', 'detail' ) . ': ' . $update_page_url . "\n" : null;
			$this->content = [
				'body' =>
					'[info][title]' . $this->site_name . '( ' . $this->site_url . ' ) ' . $message_title . '[/title]' .
					$content . "\n" .
					$website . $update_page .
					'[hr]' . $this->get_send_text( 'dev_notify', 'ignore' ) . ': ' . $developer_message['key'] .
					$this->generate_context( $this->tool_name ) .
					'[/info]',
			];
		}
		return $this;
	}

	/**
	 * Generate login message for Slack.
	 *
	 * @param object $user User data.
	 */
	public function generate_login_message( object $user ): Sct_Chatwork {
		$header_message = '[title]' . $this->site_name . '(' . $this->site_url . ') ' . $this->get_send_text( 'login_notify', 'title' ) . '[/title]';

		$user_name       = $user->data->user_login;
		$user_email      = $user->data->user_email;
		$login_user_name = $this->get_send_text( 'login_notify', 'user_name' ) . ": {$user_name}<$user_email>";

		$now_date   = gmdate( 'Y-m-d H:i:s', strtotime( current_datetime()->format( 'Y-m-d H:i:s' ) ) );
		$login_date = $this->get_send_text( 'login_notify', 'date' ) . ": {$now_date}";

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
				$this->site_url . "\n\n" .
				$this->generate_context( $this->tool_name ) . '[/info]',
		];
		return $this;
	}
}
