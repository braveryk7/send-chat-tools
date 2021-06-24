<?php
/**
 * Use Slack.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 1.0.0
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

require_once dirname( __FILE__ ) . '/trait-sct-sending.php';

/**
 * Assemble the content before sending.
 */
class Sct_Create_Content {
	use Sct_Sending;

	/**
	 * Comment ID.
	 *
	 * @var $id
	 */
	private $id;

	/**
	 * WordPress hook.
	 */
	public function __construct() {
		add_action( 'comment_post', [ $this, 'controller' ] );
	}

	/**
	 * Check tools use status and call.
	 */
	public function controller() {
		global $wpdb;
		$comment_id = $wpdb->insert_id;
		$comment    = $this->get_comment_data( $comment_id );
		if ( '1' === get_option( 'sct_use_slack' ) ) {
			$author = get_option( 'sct_send_slack_author' );
			if ( ( '0' === $author ) || ( '1' === $author && '0' === $comment->user_id ) ) {
				$options = $this->create_comment_message( 'slack', $comment );
				self::sending( $options, (string) $wpdb->insert_id, 'slack' );
			}
		};
		if ( '1' === get_option( 'sct_use_discord' ) ) {
			$author = get_option( 'sct_send_discord_author' );
			if ( ( '0' === $author ) || ( '1' === $author && '0' === $comment->user_id ) ) {
				$options = $this->create_comment_message( 'discord', $comment );
				self::sending( $options, (string) $wpdb->insert_id, 'discord' );
			}
		}
		if ( '1' === get_option( 'sct_use_chatwork' ) ) {
			$author = get_option( 'sct_send_chatwork_author' );
			if ( ( '0' === $author ) || ( '1' === $author && '0' === $comment->user_id ) ) {
				$options = $this->create_comment_message( 'chatwork', $comment );
				self::sending( $options, (string) $wpdb->insert_id, 'chatwork' );
			}
		}
	}

	/**
	 * Get comment data.
	 *
	 * @param int $comment_id Comment ID.
	 */
	private function get_comment_data( int $comment_id ): object {
		return get_comment( $comment_id );
	}

	/**
	 * Create comments message.
	 *
	 * @param string $tool Tool name.
	 * @param object $comment Comment data.
	 */
	private function create_comment_message( string $tool, object $comment ): array {
		$site_name     = get_bloginfo( 'name' );
		$site_url      = get_bloginfo( 'url' );
		$article_title = get_the_title( $comment->comment_post_ID );
		$article_url   = get_permalink( $comment->comment_post_ID );
		$approved_url  = admin_url() . 'comment.php?action=approve&c=' . $comment->comment_ID;

		if ( 'slack' === $tool ) {
			$content_type = [ 'Content-Type: application/json;charset=utf-8' ];

			if ( '1' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Approved', 'send-chat-tools' );
			} elseif ( '0' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Unapproved', 'send-chat-tools' ) . '<<' . $approved_url . '|' . esc_html__( 'Click here to approve', 'send-chat-tools' ) . '>>';
			} elseif ( 'spam' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Spam', 'send-chat-tools' );
			}

			$message = [
				'text' =>
					$site_name . '(' . $site_url . ')' . esc_html__( 'new comment has been posted.', 'send-chat-tools' ) . "\n\n" .
					esc_html__( 'Commented article:', 'send-chat-tools' ) . '<' . $article_url . '|' . $article_title . ">\n" .
					esc_html__( 'Author:', 'send-chat-tools' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
					esc_html__( 'Date and time:', 'send-chat-tools' ) . $comment->comment_date . "\n" .
					esc_html__( 'Text:', 'send-chat-tools' ) . "\n" . $comment->comment_content . "\n\n" .
					esc_html__( 'Comment URL:', 'send-chat-tools' ) . $article_url . '#comment-' . $comment->comment_ID . "\n\n" .
					esc_html__( 'Comment Status:', 'send-chat-tools' ) . $comment_status,
			];

			$body = wp_json_encode( $message );

		} elseif ( 'discord' === $tool ) {
			$content_type = [ 'Content-Type: application/json;charset=utf-8' ];

			if ( '1' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Approved', 'send-chat-tools' );
			} elseif ( '0' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Unapproved', 'send-chat-tools' ) . ' >> ' . esc_html__( 'Click here to approve', 'send-chat-tools' ) . '( ' . $approved_url . ' )';
			} elseif ( 'spam' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Spam', 'send-chat-tools' );
			}

			$message =
				$site_name . '( ' . $site_url . ' )' . esc_html__( 'new comment has been posted.', 'send-chat-tools' ) . "\n\n" .
				esc_html__( 'Commented article:', 'send-chat-tools' ) . $article_title . ' - ' . $article_url . "\n" .
				esc_html__( 'Author:', 'send-chat-tools' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
				esc_html__( 'Date and time:', 'send-chat-tools' ) . $comment->comment_date . "\n" .
				esc_html__( 'Text:', 'send-chat-tools' ) . "\n" . $comment->comment_content . "\n\n" .
				esc_html__( 'Comment URL:', 'send-chat-tools' ) . $article_url . '#comment-' . $comment->comment_ID . "\n\n" .
				esc_html__( 'Comment Status:', 'send-chat-tools' ) . $comment_status;

			$body = [ 'content' => $message ];

		} elseif ( 'chatwork' === $tool ) {
			$content_type = 'X-ChatWorkToken: ' . Sct_Encryption::decrypt( get_option( 'sct_chatwork_api_token' ) );

			if ( '1' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Approved', 'send-chat-tools' );
			} elseif ( '0' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Unapproved', 'send-chat-tools' ) . "\n" . esc_html__( 'Click here to approve', 'send-chat-tools' ) . ' ' . $approved_url;
			} elseif ( 'spam' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Spam', 'send-chat-tools' );
			}

			$message = [
				'body' =>
					'[info][title]' . $site_name . '(' . $site_url . ')' . esc_html__( 'new comment has been posted.', 'send-chat-tools' ) . '[/title]' .
					esc_html__( 'Commented article:', 'send-chat-tools' ) . $article_title . ' - ' . $article_url . "\n" .
					esc_html__( 'Author:', 'send-chat-tools' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
					esc_html__( 'Date and time:', 'send-chat-tools' ) . $comment->comment_date . "\n" .
					esc_html__( 'Text:', 'send-chat-tools' ) . "\n" . $comment->comment_content . "\n\n" .
					esc_html__( 'Comment URL:', 'send-chat-tools' ) . $article_url . '#comment-' . $comment->comment_ID . "\n\n" .
					'[hr]' .
					esc_html__( 'Comment Status:', 'send-chat-tools' ) . $comment_status .
					'[/info]',
			];

			$body = $message;
		}

		$options = [
			'method'  => 'POST',
			'headers' => $content_type,
			'body'    => $body,
		];

		return $options;
	}
}
