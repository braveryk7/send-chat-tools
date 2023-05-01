<?php
/**
 * Class for Discord processing.
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
 * Class for Discord processing.
 */
class Sct_Discord extends Sct_Generate_Content_Abstract {
	/**
	 * Constructors that inherit from the abstract class constructor.
	 */
	private function __construct() {
		parent::__construct();
		$this->tool_name = 'discord';
	}

	/**
	 * Instantiate and return itself.
	 */
	public static function get_instance(): Sct_Discord {
		return new self();
	}

	/**
	 * A method to generate a Discord header.
	 */
	public function generate_header(): Sct_Discord {
		$this->header = [
			'method'  => 'POST',
			'headers' => [ 'Content-Type: application/json;charset=utf-8' ],
			'body'    => [ 'content' => $this->content ],
		];

		return $this;
	}

	/**
	 * Generate comment content for Discord.
	 */
	public function generate_comment_content(): Sct_Discord {
		$this->comment = $this->original_data;

		$article_title  = get_the_title( $this->original_data->comment_post_ID );
		$article_url    = get_permalink( $this->original_data->comment_post_ID );
		$comment_status = $this->generate_comment_approved_message( $this->tool_name, $this->original_data );

		$header_emoji   = ':mailbox_with_mail:';
		$header_message = $this->generate_header_message( $header_emoji, $this->get_send_text( 'comment_notify', 'title' ) );

		$this->content =
			$header_message . "\n\n" .
			'**' . $this->get_send_text( 'comment_notify', 'article' ) . '**: ' . $article_title . ' - <' . $article_url . '>' . "\n" .
			'**' . $this->get_send_text( 'comment_notify', 'commenter' ) . '**: ' . $this->original_data->comment_author . '<' . $this->original_data->comment_author_email . ">\n" .
			'**' . $this->get_send_text( 'constant', 'date' ) . '**: ' . $this->original_data->comment_date . "\n" .
			'**' . $this->get_send_text( 'comment_notify', 'comment' ) . '**: ' . "\n" . $this->original_data->comment_content . "\n\n" .
			'**' . $this->get_send_text( 'comment_notify', 'url' ) . '**: <' . $article_url . '#comment-' . $this->original_data->comment_ID . '>' . "\n\n" .
			'**' . $this->get_send_text( 'comment_notify', 'status' ) . '**: ' . $comment_status . "\n\n" .
			$this->generate_context( $this->tool_name );

		return $this;
	}

	/**
	 * Generate update content for Discord.
	 */
	public function generate_update_content(): Sct_Discord {
		$plain_data = $this->generate_plain_update_message( $this->original_data );

		$header_emoji    = ':zap:';
		$header_message  = $this->generate_header_message( $header_emoji, $this->get_send_text( 'update_notify', 'title' ) );
		$core_content    = null;
		$themes_content  = null;
		$plugins_content = null;

		if ( $plain_data->core ) {
			$core_content = ':star: ' . $plain_data->core;
		}

		if ( $plain_data->themes ) {
			$themes_content = ':art: ' . $plain_data->themes;
		}

		if ( $plain_data->plugins ) {
			$plugins_content = ':wrench: ' . $plain_data->plugins;
		}

		$this->content =
			$header_message . "\n\n" .
			$core_content . $themes_content . $plugins_content .
			$this->get_send_text( 'update_notify', 'update' ) . "\n" . $this->get_send_text( 'update_notify', 'page' ) . ': <' . $plain_data->admin_url . '>' . "\n\n" .
			$this->generate_context( $this->tool_name );

		return $this;
	}

	/**
	 * Generate developer content for Discord.
	 */
	public function generate_developer_content(): Sct_Discord {
		if ( isset( $this->original_data['title'] ) && isset( $this->original_data['message'] ) && array_key_exists( 'url', $this->original_data ) ) {
			$message_title = sprintf( $this->get_send_text( 'dev_notify', 'title' ), esc_html( $this->original_data['title'] ), );
			$content       = '';

			$i = 0;
			foreach ( $this->original_data['message'] as $value ) {
				if ( $i >= 50 ) {
					break;
				}
				$content .= $value . "\n";
				$i++;
			}

			$header_emoji    = ':tada:';
			$header_message  = $this->generate_header_message( $header_emoji, $message_title );
			$website_url     = $this->original_data['url']['website'];
			$update_page_url = $this->original_data['url']['update_page'];

			$title         = $header_message . "\n\n";
			$main_content  = $content . "\n";
			$website       = $website_url ? $this->get_send_text( 'dev_notify', 'website' ) . ': <' . $website_url . ">\n" : null;
			$update_page   = $update_page_url ? $this->get_send_text( 'dev_notify', 'detail' ) . ': <' . $update_page_url . ">\n" : null;
			$ignore        = "\n" . $this->get_send_text( 'dev_notify', 'ignore' ) . ': ' . $this->original_data['key'] . "\n";
			$this->content = $title . $main_content . $website . $update_page . $ignore . "\n" . $this->generate_context( $this->tool_name );
		}

		return $this;
	}

	/**
	 * Generate login content for Discord.
	 */
	public function generate_login_content(): Sct_Discord {
		$header_emoji   = ':unlock:';
		$header_message = $this->generate_header_message( $header_emoji, $this->get_send_text( 'login_notify', 'title' ) );

		$user_name       = $this->original_data->data->user_login;
		$user_email      = $this->original_data->data->user_email;
		$login_user_name = '***' . $this->get_send_text( 'login_notify', 'user_name' ) . "***: {$user_name}<$user_email>";

		$now_date   = gmdate( 'Y-m-d H:i:s', strtotime( current_datetime()->format( 'Y-m-d H:i:s' ) ) );
		$login_date = '***' . $this->get_send_text( 'constant', 'date' ) . "***: {$now_date}";

		$os_browser = getenv( 'HTTP_USER_AGENT' );
		$login_env  = '***' . $this->get_send_text( 'login_notify', 'login_env' ) . "***: {$os_browser}";

		$ip_address       = getenv( 'REMOTE_ADDR' );
		$login_ip_address = '***' . $this->get_send_text( 'login_notify', 'ip_address' ) . "***: {$ip_address}";

		$this->content =
			$header_message . "\n\n" . $login_user_name . "\n" . $login_date . "\n" . $login_env . "\n" . $login_ip_address . "\n\n" .
			$this->get_send_text( 'login_notify', 'unauthorized_login' ) . "\n" .
			$this->get_send_text( 'login_notify', 'disconnect' ) . "\n" .
			$this->site_url . '/wp-admin/profile.php' . "\n\n" .
			$this->generate_context( $this->tool_name );

		return $this;
	}

	/**
	 * Generate Rinker content for Discord.
	 */
	public function generate_rinker_content(): Sct_Discord {
		$header_emoji   = ':package:';
		$header_message = $this->generate_header_message( $header_emoji, $this->get_send_text( 'rinker_notify', 'title' ) );

		$items = $this->format_rinker_items( $this->original_data );

		$after_message = $this->get_send_text( 'rinker_notify', 'temporary' ) . "\n" . $this->get_send_text( 'rinker_notify', 'resume' );

		$this->content = $header_message . "\n\n" . $items . "\n\n" . $after_message . "\n" . $this->generate_context( $this->tool_name );

		return $this;
	}
}
