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
	 * Generate comment content for Slack.
	 */
	public function generate_comment_content(): Sct_Slack {
		$article_title  = get_the_title( $this->original_data->comment_post_ID );
		$article_url    = get_permalink( $this->original_data->comment_post_ID );
		$comment_status = $this->generate_comment_approved_message( $this->tool_name, $this->original_data );

		$header_emoji     = ':mailbox_with_mail:';
		$header_message   = $this->generate_header_message( $header_emoji, $this->get_send_text( 'comment_notify', 'title' ) );
		$comment_article  = '*' . $this->get_send_text( 'comment_notify', 'article' ) . ": *<{$article_url}|{$article_title}>";
		$commenter        = '*' . $this->get_send_text( 'comment_notify', 'commenter' ) . "*\n{$this->original_data->comment_author}<{$this->original_data->comment_author_email}>";
		$date             = '*' . $this->get_send_text( 'constant', 'date' ) . "*\n{$this->original_data->comment_date}";
		$comment_content  = '*' . $this->get_send_text( 'comment_notify', 'comment' ) . "*\n{$this->original_data->comment_content}";
		$comment_url      = '*' . $this->get_send_text( 'comment_notify', 'url' ) . "*\n{$article_url}#comment-{$this->original_data->comment_ID}";
		$comment_statuses = '*' . $this->get_send_text( 'comment_notify', 'status' ) . "*\n{$comment_status}";
		$context          = $this->generate_context( $this->tool_name );

		$this->content = [
			'text'   => $header_message,
			'blocks' => [
				$this->header( 'plain_text', $header_message, true ),
				$this->single_column( 'mrkdwn', $comment_article ),
				$this->divider(),
				$this->two_column( [ 'mrkdwn', $commenter ], [ 'mrkdwn', $date ] ),
				$this->single_column( 'mrkdwn', $comment_content ),
				$this->two_column( [ 'mrkdwn', $comment_url ], [ 'mrkdwn', $comment_statuses ] ),
				$this->divider(),
				$this->context( 'mrkdwn', $context ),
			],
		];

		return $this;
	}

	/**
	 * Generate update content for Slack.
	 */
	public function generate_update_content(): Sct_Slack {
		$raw_data = $this->generate_update_raw_data( $this->original_data );

		$header_emoji   = ':zap:';
		$header_message = $this->generate_header_message( $header_emoji, $this->get_send_text( 'update_notify', 'title' ) );
		$update_message = $this->get_send_text( 'update_notify', 'update' ) . "\n" . $this->get_send_text( 'update_notify', 'page' ) . ": <{$raw_data->admin_url}>";
		$context        = $this->generate_context( $this->tool_name );

		$message = [
			'text'   => $header_message,
			'blocks' => [
				$this->header( 'plain_text', $header_message, true ),
			],
		];

		if ( isset( $raw_data->core ) ) {
			$core_message = [
				'blocks' => [
					$this->single_column( 'mrkdwn', ':star: ' . $raw_data->core ),
				],
			];

			$message = array_merge_recursive( $message, $core_message );
		}

		if ( isset( $raw_data->themes ) ) {
			$themes_message = [
				'blocks' => [
					$this->single_column( 'mrkdwn', ':art: ' . $raw_data->themes ),
				],
			];

			$message = array_merge_recursive( $message, $themes_message );
		}

		if ( isset( $raw_data->plugins ) ) {
			$plugins_message = [
				'blocks' => [
					$this->single_column( 'mrkdwn', ':wrench: ' . $raw_data->plugins ),
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
	 * Generate developer content for Slack.
	 */
	public function generate_developer_content(): Sct_Slack {
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

			$header_emoji    = ':tada:';
			$header_message  = $this->generate_header_message( $header_emoji, $message_title );
			$website_url     = $this->original_data['url']['website'];
			$update_page_url = $this->original_data['url']['update_page'];

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
						$this->get_send_text( 'dev_notify', 'ignore' ) . ': ' . $this->original_data['key'],
					),
					$this->context( 'mrkdwn', $context ),
				],
			];

			$this->content = array_merge_recursive( $message, $fixed_phrase );
		}

		return $this;
	}

	/**
	 * Generate login content for Slack.
	 */
	public function generate_login_content(): Sct_Slack {
		$header_emoji   = ':door:';
		$header_message = $this->generate_header_message( $header_emoji, $this->get_send_text( 'login_notify', 'title' ) );

		$user_name       = $this->original_data->data->user_login;
		$user_email      = $this->original_data->data->user_email;
		$login_user_name = '*' . $this->get_send_text( 'login_notify', 'user_name' ) . "*\n{$user_name}<$user_email>";

		$now_date   = gmdate( 'Y-m-d H:i:s', strtotime( current_datetime()->format( 'Y-m-d H:i:s' ) ) );
		$login_date = '*' . $this->get_send_text( 'constant', 'date' ) . "*\n{$now_date}";

		$os_browser = getenv( 'HTTP_USER_AGENT' );
		$login_env  = '*' . $this->get_send_text( 'login_notify', 'login_env' ) . "*\n{$os_browser}";

		$ip_address       = getenv( 'REMOTE_ADDR' );
		$login_ip_address = '*' . $this->get_send_text( 'login_notify', 'ip_address' ) . "*\n{$ip_address}";

		$message =
			$this->get_send_text( 'login_notify', 'unauthorized_login' ) . "\n" .
			$this->get_send_text( 'login_notify', 'disconnect' ) . "\n" .
			$this->site_url . '/wp-admin/profile.php';

		$this->content = [
			'text'   => $header_message,
			'blocks' => [
				$this->header( 'plain_text', $header_message, true ),
				$this->two_column( [ 'mrkdwn', $login_user_name ], [ 'mrkdwn', $login_date ] ),
				$this->two_column( [ 'mrkdwn', $login_env ], [ 'mrkdwn', $login_ip_address ] ),
				$this->divider(),
				$this->single_column( 'mrkdwn', $message, ),
				$this->divider(),
				$this->context( 'mrkdwn', $this->generate_context( $this->tool_name ) ),
			],
		];

		return $this;
	}

	/**
	 * Generate Rinker content for Slack.
	 */
	public function generate_rinker_content(): Sct_Slack {
		$header_emoji   = ':package:';
		$header_message = $this->generate_header_message( $header_emoji, $this->get_send_text( 'rinker_notify', 'title' ) );

		$discontinued_items = $this->format_rinker_items( $this->original_data );

		$after_message = $this->get_send_text( 'rinker_notify', 'temporary' ) . "\n" . $this->get_send_text( 'rinker_notify', 'resume' );

		$this->content = [
			'text'   => $header_message,
			'blocks' => [
				$this->header( 'plain_text', $header_message, true ),
				$this->single_column( 'mrkdwn', $discontinued_items ),
				$this->divider(),
				$this->single_column( 'mrkdwn', $after_message ),
				$this->divider(),
				$this->context( 'mrkdwn', $this->generate_context( $this->tool_name ) ),
			],
		];

		return $this;
	}

	/**
	 * Method to generate a header for Slack Blocks.
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
	 * Method to generate a single column for Slack Blocks.
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
	 * Method to generate TWO columns of Slack Blocks.
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
	 * Method to generate Slack Blocks context.
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
	 * Method to generate a divider for Slack Blocks.
	 */
	private function divider(): array {
		$divider = [
			'type' => 'divider',
		];

		return $divider;
	}
}
