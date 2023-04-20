<?php
/**
 * Class for Slack processing.
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
 * Class for Slack processing.
 */
class Sct_Slack extends Sct_Generate_Content_Abstract {
	/**
	 * Constructor.
	 */
	public function __construct() {
		require_once 'class-sct-slack-blocks.php';
	}

	/**
	 * Instantiate and return itself.
	 */
	public static function get_instance(): Sct_Slack {
		return new self();
	}

	/**
	 * A method to generate a Slack header.
	 */
	public function generate_header(): Sct_Slack {
		$this->header = [
			'method'  => 'POST',
			'headers' => [ 'Content-Type: application/json;charset=utf-8' ],
			'body'    => wp_json_encode( $this->content ),
		];

		return $this;
	}

	/**
	 * Abstract method to create comment data to be sent to chat tools.
	 *
	 * @param object $comment Comment data.
	 */
	public function generate_comment_content( object $comment, ): Sct_Slack {
		$this->comment = $comment;

		$site_name     = get_bloginfo( 'name' );
		$site_url      = get_bloginfo( 'url' );
		$article_title = get_the_title( $comment->comment_post_ID );
		$article_url   = get_permalink( $comment->comment_post_ID );

		$approved_url   = admin_url() . 'comment.php?action=approve&c=' . $comment->comment_ID;
		$unapproved     = $this->get_send_text( 'comment', 'unapproved' );
		$click_message  = $this->get_send_text( 'comment', 'click' );
		$comment_status = $unapproved . '<<' . $approved_url . '|' . $click_message . '>>';

		$message = [
			0 => esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ),
			1 => esc_html__( 'WordPress Plugin Directory', 'send-chat-tools' ),
			2 => esc_html__( 'Send Chat Tools Official Page', 'send-chat-tools' ),
		];

		$wordpress_directory = $this->get_official_directory();
		$official_web_site   = 'https://www.braveryk7.com/portfolio/send-chat-tools/';

		$header_emoji     = ':mailbox_with_mail:';
		$header_message   = "{$header_emoji} {$site_name}({$site_url})" . $this->get_send_text( 'comment', 'title' );
		$comment_article  = '*' . $this->get_send_text( 'comment', 'article' ) . "*<{$article_url}|{$article_title}>";
		$author           = '*' . $this->get_send_text( 'comment', 'author' ) . "*\n{$comment->comment_author}<{$comment->comment_author_email}>";
		$date             = '*' . $this->get_send_text( 'comment', 'date' ) . "*\n{$comment->comment_date}";
		$comment_content  = '*' . $this->get_send_text( 'comment', 'content' ) . "*\n{$comment->comment_content}";
		$comment_url      = '*' . $this->get_send_text( 'comment', 'url' ) . "*\n{$article_url}#comment-{$comment->comment_ID}";
		$comment_statuses = '*' . $this->get_send_text( 'comment', 'status' ) . "*\n{$comment_status}";
		$context          = $message[0] . "\n" . '<' . $wordpress_directory . '|' . $message[1] . '> / <' . $official_web_site . '|' . $message[2] . '>';

		$blocks        = new Sct_Slack_Blocks();
		$this->content = [
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

		return $this;
	}

	/**
	 * Generate update notifications for Slack.
	 *
	 * @param array $update_content Update data.
	 */
	public function generate_update_content( array $update_content ): Sct_Slack {
		$plain_data     = $this->generate_plain_update_message( $update_content );
		$header_emoji   = ':zap:';
		$header_message = "{$header_emoji} {$plain_data->site_name}({$plain_data->site_url})" . $plain_data->update_title;
		$update_message = $plain_data->update_text . "\n" . $plain_data->update_page . "<{$plain_data->admin_url}>";

		$message = [
			0 => esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ),
			1 => esc_html__( 'WordPress Plugin Directory', 'send-chat-tools' ),
			2 => esc_html__( 'Send Chat Tools Official Page', 'send-chat-tools' ),
		];

		$wordpress_directory = $this->get_official_directory();
		$official_web_site   = 'https://www.braveryk7.com/portfolio/send-chat-tools/';

		$context = $message[0] . "\n" . '<' . $wordpress_directory . '|' . $message[1] . '> / <' . $official_web_site . '|' . $message[2] . '>';

		$blocks  = new Sct_Slack_Blocks();
		$message = [
			'text'   => $header_message,
			'blocks' => [
				$blocks->header( 'plain_text', $header_message, true ),
			],
		];

		if ( isset( $plain_data->core ) ) {
			$core_message = [
				'blocks' => [
					$blocks->single_column( 'mrkdwn', ':star: ' . $plain_data->core ),
				],
			];

			$message = array_merge_recursive( $message, $core_message );
		}

		if ( isset( $plain_data->themes ) ) {
			$themes_message = [
				'blocks' => [
					$blocks->single_column( 'mrkdwn', ':art: ' . $plain_data->themes ),
				],
			];

			$message = array_merge_recursive( $message, $themes_message );
		}

		if ( isset( $plain_data->plugins ) ) {
			$plugins_message = [
				'blocks' => [
					$blocks->single_column( 'mrkdwn', ':wrench: ' . $plain_data->plugins ),
				],
			];

			$message = array_merge_recursive( $message, $plugins_message );
		}

		$fixed_phrase = [
			'blocks' => [
				$blocks->single_column( 'mrkdwn', $update_message ),
				$blocks->divider(),
				$blocks->context( 'mrkdwn', $context ),
			],
		];

		$message = array_merge_recursive( $message, $fixed_phrase );

		$this->content = $message;

		return $this;
	}
}
