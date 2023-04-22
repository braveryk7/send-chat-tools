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
	 * Constructors that inherit from the abstract class constructor.
	 */
	private function __construct() {
		parent::__construct();
		$this->tool_name = 'slack';
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

		$article_title  = get_the_title( $comment->comment_post_ID );
		$article_url    = get_permalink( $comment->comment_post_ID );
		$comment_status = $this->generate_comment_approved_message( $this->tool_name, $comment );

		$header_emoji     = ':mailbox_with_mail:';
		$header_message   = "{$header_emoji} {$this->site_name}({$this->site_url})" . $this->get_send_text( 'comment', 'title' );
		$comment_article  = '*' . $this->get_send_text( 'comment', 'article' ) . "*<{$article_url}|{$article_title}>";
		$author           = '*' . $this->get_send_text( 'comment', 'author' ) . "*\n{$comment->comment_author}<{$comment->comment_author_email}>";
		$date             = '*' . $this->get_send_text( 'comment', 'date' ) . "*\n{$comment->comment_date}";
		$comment_content  = '*' . $this->get_send_text( 'comment', 'content' ) . "*\n{$comment->comment_content}";
		$comment_url      = '*' . $this->get_send_text( 'comment', 'url' ) . "*\n{$article_url}#comment-{$comment->comment_ID}";
		$comment_statuses = '*' . $this->get_send_text( 'comment', 'status' ) . "*\n{$comment_status}";
		$context          = $this->generate_context( $this->tool_name );

		$this->content = [
			'text'   => $header_message,
			'blocks' => [
				$this->header( 'plain_text', $header_message, true ),
				$this->single_column( 'mrkdwn', $comment_article ),
				$this->divider(),
				$this->two_column( [ 'mrkdwn', $author ], [ 'mrkdwn', $date ] ),
				$this->single_column( 'mrkdwn', $comment_content ),
				$this->two_column( [ 'mrkdwn', $comment_url ], [ 'mrkdwn', $comment_statuses ] ),
				$this->divider(),
				$this->context( 'mrkdwn', $context ),
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
		$context        = $this->generate_context( $this->tool_name );

		$message = [
			'text'   => $header_message,
			'blocks' => [
				$this->header( 'plain_text', $header_message, true ),
			],
		];

		if ( isset( $plain_data->core ) ) {
			$core_message = [
				'blocks' => [
					$this->single_column( 'mrkdwn', ':star: ' . $plain_data->core ),
				],
			];

			$message = array_merge_recursive( $message, $core_message );
		}

		if ( isset( $plain_data->themes ) ) {
			$themes_message = [
				'blocks' => [
					$this->single_column( 'mrkdwn', ':art: ' . $plain_data->themes ),
				],
			];

			$message = array_merge_recursive( $message, $themes_message );
		}

		if ( isset( $plain_data->plugins ) ) {
			$plugins_message = [
				'blocks' => [
					$this->single_column( 'mrkdwn', ':wrench: ' . $plain_data->plugins ),
				],
			];

			$message = array_merge_recursive( $message, $plugins_message );
		}

		$fixed_phrase = [
			'blocks' => [
				$this->single_column( 'mrkdwn', $update_message ),
				$this->divider(),
				$this->context( 'mrkdwn', $context ),
			],
		];

		$message = array_merge_recursive( $message, $fixed_phrase );

		$this->content = $message;

		return $this;
	}

	/**
	 * Generate developer message for Slack.
	 *
	 * @param array $developer_message Developer message.
	 */
	public function generate_developer_message( array $developer_message ): Sct_Slack {
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

			if ( ! is_null( $developer_message['url'] ) ) {
				$website_url     = array_key_exists( 'website', $developer_message['url'] ) ? $developer_message['url']['website'] : null;
				$update_page_url = array_key_exists( 'update_page', $developer_message['url'] ) ? $developer_message['url']['update_page'] : null;
			} else {
				$website_url     = null;
				$update_page_url = null;
			}

			$header_emoji   = ':tada:';
			$header_message = "{$header_emoji} {$this->site_name}({$this->site_url}) " . $message_title;

			$context = $this->generate_context( $this->tool_name );

			$message = [
				'text'   => $header_message,
				'blocks' => [
					$this->header( 'plain_text', $header_message, true ),
				],
			];

			$main_content = [
				'blocks' => [
					$this->single_column( 'mrkdwn', $content ),
				],
			];

			$message = array_merge_recursive( $message, $main_content );

			if ( $website_url ) {
				$website = [
					'blocks' => [
						$this->single_column(
							'mrkdwn',
							$this->get_send_text( 'dev_notify', 'website' ) . ': ' . $website_url,
						),
					],
				];

				$message = array_merge_recursive( $message, $website );
			}

			if ( $update_page_url ) {
				$update_page = [
					'blocks' => [
						$this->single_column(
							'mrkdwn',
							$this->get_send_text( 'dev_notify', 'detail' ) . ': ' . $update_page_url,
						),
					],
				];

				$message = array_merge_recursive( $message, $update_page );
			}

			$fixed_phrase = [
				'blocks' => [
					$this->divider(),
					$this->context(
						'mrkdwn',
						$this->get_send_text( 'dev_notify', 'ignore' ) . ': ' . $developer_message['key'],
					),
					$this->context( 'mrkdwn', $context ),
				],
			];

			$this->content = array_merge_recursive( $message, $fixed_phrase );
		}

		return $this;
	}

	/**
	 * Create header.
	 *
	 * @param string $type content type.
	 * @param string $text content text.
	 * @param bool   $emoji Use emoji.
	 */
	private function header( string $type, string $text, bool $emoji = true ): array {
		$header = [
			'type' => 'header',
			'text' => [
				'type'  => $type,
				'text'  => $text,
				'emoji' => $emoji,
			],
		];

		return $header;
	}
	/**
	 * Create single column.
	 *
	 * @param string $type content type.
	 * @param string $text content text.
	 */
	private function single_column( string $type, string $text ): array {
		$column = [
			'type' => 'section',
			'text' => [
				'type' => $type,
				'text' => $text,
			],
		];

		return $column;
	}

	/**
	 * Create two column.
	 *
	 * @param array $content1st [ $type, $text ].
	 * @param array $content2nd [ $type, $text ].
	 */
	private function two_column( array $content1st, array $content2nd ): array {
		$columns = [
			'type'   => 'section',
			'fields' => [
				[
					'type' => $content1st[0],
					'text' => $content1st[1],
				],
				[
					'type' => $content2nd[0],
					'text' => $content2nd[1],
				],
			],
		];

		return $columns;
	}

	/**
	 * Create context.
	 *
	 * @param string $type content type.
	 * @param string $text content text.
	 */
	private function context( string $type, string $text ): array {
		$context = [
			'type'     => 'context',
			'elements' => [
				[
					'type' => $type,
					'text' => $text,
				],
			],
		];

		return $context;
	}

	/**
	 * Create divider.
	 */
	private function divider(): array {
		$divider = [
			'type' => 'divider',
		];

		return $divider;
	}
}
