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

/**
 * Assemble the content before sending.
 */
class Sct_Create_Content extends Sct_Base {
	/**
	 * WordPress hook.
	 */
	public function __construct() {
		add_action( 'comment_post', [ $this, 'controller' ] );
		require_once 'class-sct-slack-blocks.php';
	}

	/**
	 * Check tools use status and call.
	 *
	 * @param int    $comment_id     Comment ID             (0: default).
	 * @param string $type           Content Type           (comment: default, update).
	 * @param array  $update_content Update date or message ([]: default).
	 */
	public function controller( int $comment_id = 0, string $type = 'comment', array $update_content = [] ): void {
		$sct_options = $this->get_sct_options();
		$tools       = [ 'slack', 'discord', 'chatwork' ];

		if ( 'comment' === $type ) {
			$comment = $this->get_comment_data( $comment_id );

			foreach ( $tools as $tool ) {
				'chatwork' === $tool ? $api_column = 'api_token' : $api_column = 'webhook_url';

				if ( $this->get_send_status( $tool, $sct_options[ $tool ], $comment->user_id ) ) {
					global $wpdb;
					$options = $this->create_content( $type, $tool, $comment );
					$this->send_tools( $options, (string) $wpdb->insert_id, $tool, $comment );
				} elseif ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ][ $api_column ] ) ) {
					$logger = new Sct_Logger();
					$logger->create_log( 1001, $tool, '1' );
				} elseif ( 'chatwork' === $tools && ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ]['room_id'] ) ) ) {
					$logger = new Sct_Logger();
					$logger->create_log( 1002, 'chatwork', '1' );
				};
			}
		} elseif ( 'update' === $type ) {
			foreach ( $tools as $tool ) {
				if ( $sct_options[ $tool ]['use'] && $sct_options[ $tool ]['send_update'] ) {
					$options = $this->create_content( $type, $tool, null, $update_content );
					$this->send_tools( $options, 'update', $tool );
				}
			}
		} elseif ( 'dev_notify' === $type ) {
			foreach ( $tools as $tool ) {
				if ( $sct_options[ $tool ]['use'] ) {
					$options = $this->create_content( $type, $tool, null, $update_content );
					$this->send_tools( $options, 'dev_notify', $tool );
				}
			}
		}
	}

	/**
	 * Check send status.
	 *
	 * @param string $tool_name       Tool name.
	 * @param array  $tools           Tool options -> sct_options[TOOLNAME].
	 * @param string $comment_user_id Comment user id.
	 */
	private function get_send_status( string $tool_name, array $tools, string $comment_user_id ): bool {
		$status     = [
			'use'    => false,
			'api'    => false,
			'author' => false,
		];
		$api_exists = false;

		$tools['use'] ? $status['use'] = true : $status['use'] = false;

		! $tools['send_author'] || $tools['send_author'] && '0' === $comment_user_id ? $status['author'] = true : $status['author'] = false;

		switch ( $tool_name ) {
			case 'slack':
			case 'discord':
				$api                             = $tools['webhook_url'];
				! empty( $api ) ? $status['api'] = true : $status['api'] = false;
				break;
			case 'chatwork':
				$api = [
					'api_token' => $tools['api_token'],
					'room_id'   => $tools['room_id'],
				];
				! empty( $api['api_token'] ) && ! empty( $api['room_id'] ) ? $status['api'] = true : $status['api'] = false;
				break;
			default:
				$status['api'] = false;
		}

		return in_array( false, $status, true ) ? false : true;
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
	 * Create send options.
	 *
	 * @param string $type           Create type.
	 * @param string $tool           Tool name.
	 * @param object $comment        Comment data.
	 * @param array  $update_content Update data or message.
	 */
	private function create_content( string $type, string $tool, object $comment = null, array $update_content = [] ): array {
		$message = [];
		switch ( $type ) {
			case 'comment':
				$message = $this->create_comment_message( $tool, $comment );
				break;
			case 'update':
				$message = $this->create_update_message( $tool, $update_content );
				break;
			case 'dev_notify':
				$message = $this->create_developer_message( $tool, $update_content );
				break;
		}

		switch ( $tool ) {
			case 'slack':
				$content_type = [ 'Content-Type: application/json;charset=utf-8' ];
				$body         = wp_json_encode( $message );
				break;
			case 'discord':
				$content_type = [ 'Content-Type: application/json;charset=utf-8' ];
				$body         = [ 'content' => $message ];
				break;
			case 'chatwork':
				$sct_options  = $this->get_sct_options();
				$content_type = 'X-ChatWorkToken: ' . $sct_options['chatwork']['api_token'];
				$body         = $message;
				break;
		}

		$options = [
			'method'  => 'POST',
			'headers' => $content_type,
			'body'    => $body,
		];

		return $options;
	}

	/**
	 * Create comment message.
	 *
	 * @param string $tool    Tool name.
	 * @param object $comment Comment data.
	 */
	private function create_comment_message( string $tool, object $comment ) {
		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$article_title  = get_the_title( $comment->comment_post_ID );
		$article_url    = get_permalink( $comment->comment_post_ID );
		$comment_status = $this->make_comment_approved_message( $tool, $comment );

		if ( 'slack' === $tool ) {
			$header_emoji     = ':mailbox_with_mail:';
			$header_message   = "{$header_emoji} {$site_name}({$site_url})" . $this->get_send_text( 'comment', 'title' );
			$comment_article  = '*' . $this->get_send_text( 'comment', 'article' ) . "*<{$article_url}|{$article_title}>";
			$author           = '*' . $this->get_send_text( 'comment', 'author' ) . "*\n{$comment->comment_author}<{$comment->comment_author_email}>";
			$date             = '*' . $this->get_send_text( 'comment', 'date' ) . "*\n{$comment->comment_date}";
			$comment_content  = '*' . $this->get_send_text( 'comment', 'content' ) . "*\n{$comment->comment_content}";
			$comment_url      = '*' . $this->get_send_text( 'comment', 'url' ) . "*\n{$article_url}#comment-{$comment->comment_ID}";
			$comment_statuses = '*' . $this->get_send_text( 'comment', 'status' ) . "*\n{$comment_status}";
			$context          = $this->make_context( $tool );

			$blocks  = new Sct_Slack_Blocks();
			$message = [
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
		} elseif ( 'discord' === $tool ) {
			$message =
				$site_name . '( <' . $site_url . '> )' . $this->get_send_text( 'comment', 'title' ) . "\n\n" .
				$this->get_send_text( 'comment', 'article' ) . $article_title . ' - <' . $article_url . '>' . "\n" .
				$this->get_send_text( 'comment', 'author' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
				$this->get_send_text( 'comment', 'date' ) . $comment->comment_date . "\n" .
				$this->get_send_text( 'comment', 'content' ) . "\n" . $comment->comment_content . "\n\n" .
				$this->get_send_text( 'comment', 'url' ) . '<' . $article_url . '#comment-' . $comment->comment_ID . '>' . "\n\n" .
				$this->get_send_text( 'comment', 'status' ) . $comment_status . "\n\n" .
				$this->make_context( $tool );
		} elseif ( 'chatwork' === $tool ) {
			$message = [
				'body' =>
					'[info][title]' . $site_name . '(' . $site_url . ')' . $this->get_send_text( 'comment', 'title' ) . '[/title]' .
					$this->get_send_text( 'comment', 'article' ) . $article_title . ' - ' . $article_url . "\n" .
					$this->get_send_text( 'comment', 'author' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
					$this->get_send_text( 'comment', 'date' ) . $comment->comment_date . "\n" .
					$this->get_send_text( 'comment', 'content' ) . "\n" . $comment->comment_content . "\n\n" .
					$this->get_send_text( 'comment', 'url' ) . $article_url . '#comment-' . $comment->comment_ID . "\n\n" .
					'[hr]' .
					$this->get_send_text( 'comment', 'status' ) . $comment_status .
					'[hr]' . $this->make_context( $tool ) .
					'[/info]',
			];
		}

		return $message;
	}

	/**
	 * Create update message.
	 *
	 * @param string $tool           Tool name.
	 * @param array  $update_content Update data.
	 */
	private function create_update_message( string $tool, array $update_content ) {
		$site_name    = get_bloginfo( 'name' );
		$site_url     = get_bloginfo( 'url' );
		$admin_url    = admin_url() . 'update-core.php';
		$update_title = $this->get_send_text( 'update', 'title' );
		$update_text  = $this->get_send_text( 'update', 'update' );
		$update_page  = $this->get_send_text( 'update', 'page' );
		$add_core;
		$add_themes;
		$add_plugins;

		foreach ( $update_content as $key => $value ) {
			switch ( $value['attribute'] ) {
				case 'core':
					$add_core .= '   ' . $value['name'] . ' ( ' . $value['current_version'] . ' -> ' . $value['new_version'] . ' )' . "\n";
					break;
				case 'theme':
					$add_themes .= '   ' . $value['name'] . ' ( ' . $value['current_version'] . ' -> ' . $value['new_version'] . ' )' . "\n";
					break;
				case 'plugin':
					$add_plugins .= '   ' . $value['name'] . ' ( ' . $value['current_version'] . ' -> ' . $value['new_version'] . ' )' . "\n";
					break;
			}
		};

		$core    = isset( $add_core ) ? esc_html__( 'WordPress Core:', 'send-chat-tools' ) . "\n" . $add_core . "\n" : null;
		$themes  = isset( $add_themes ) ? esc_html__( 'Themes:', 'send-chat-tools' ) . "\n" . $add_themes . "\n" : null;
		$plugins = isset( $add_plugins ) ? esc_html__( 'Plugins:', 'send-chat-tools' ) . "\n" . $add_plugins . "\n" : null;

		if ( 'slack' === $tool ) {
			$header_emoji   = ':zap:';
			$header_message = "{$header_emoji} {$site_name}({$site_url})" . $update_title;
			$update_message = $update_text . "\n" . $update_page . "<{$admin_url}>";
			$context        = $this->make_context( $tool );

			$blocks  = new Sct_Slack_Blocks();
			$message = [
				'text'   => $header_message,
				'blocks' => [
					$blocks->header( 'plain_text', $header_message, true ),
				],
			];

			if ( isset( $core ) ) {
				$core_message = [
					'blocks' => [
						$blocks->single_column( 'mrkdwn', ':star: ' . $core ),
					],
				];

				$message = array_merge_recursive( $message, $core_message );
			}

			if ( isset( $themes ) ) {
				$themes_message = [
					'blocks' => [
						$blocks->single_column( 'mrkdwn', ':art: ' . $themes ),
					],
				];

				$message = array_merge_recursive( $message, $themes_message );
			}

			if ( isset( $plugins ) ) {
				$plugins_message = [
					'blocks' => [
						$blocks->single_column( 'mrkdwn', ':wrench: ' . $plugins ),
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
		} elseif ( 'discord' === $tool ) {
			$message =
				$site_name . '( <' . $site_url . '> )' . $update_title . "\n\n" .
				$core . $themes . $plugins . $update_text . "\n" . $update_page . '<' . $admin_url . '>' . "\n\n" .
				$this->make_context( $tool );
		} elseif ( 'chatwork' === $tool ) {
			isset( $core ) ? $core       = $core . '[hr]' : $core;
			isset( $themes ) ? $themes   = $themes . '[hr]' : $themes;
			isset( $plugins ) ? $plugins = $plugins . '[hr]' : $themes;

			$message = [
				'body' =>
					'[info][title]' . $site_name . '( ' . $site_url . ' )' . $update_title . '[/title]' .
					$core . $themes . $plugins . $update_text . "\n" . $update_page . $admin_url . "\n\n" .
					$this->make_context( $tool ) .
					'[/info]',
			];
		}

		return $message;
	}

	/**
	 * Create plugin update message.
	 *
	 * @param string $tool           Tool name.
	 * @param array  $update_message Update message.
	 */
	private function create_developer_message( string $tool, array $update_message ) {
		$message = __( 'No announcement.', 'send- chat-tools' );

		if ( isset( $update_message['title'] ) && isset( $update_message['message'] ) && array_key_exists( 'url', $update_message ) ) {
			$site_name     = get_bloginfo( 'name' );
			$site_url      = get_bloginfo( 'url' );
			$message_title = sprintf(
				/* translators: 1: Theme or Plugin name */
				esc_html__( 'Update notifications from %s', 'send-chat-tools' ),
				esc_html( $update_message['title'] ),
			);
			$developer_message = '';

			$i = 0;
			foreach ( $update_message['message'] as $value ) {
				if ( $i >= 50 ) {
					break;
				}
				$developer_message .= $value . "\n";
				$i++;
			}

			if ( ! is_null( $update_message['url'] ) ) {
				$website_url     = array_key_exists( 'website', $update_message['url'] ) ? $update_message['url']['website'] : null;
				$update_page_url = array_key_exists( 'update_page', $update_message['url'] ) ? $update_message['url']['update_page'] : null;
			} else {
				$website_url     = null;
				$update_page_url = null;
			}

			if ( 'slack' === $tool ) {
				$header_emoji   = ':tada:';
				$header_message = "{$header_emoji} {$site_name}({$site_url}) " . $message_title;

				$context = $this->make_context( $tool );

				$blocks  = new Sct_Slack_Blocks();
				$message = [
					'text'   => $header_message,
					'blocks' => [
						$blocks->header( 'plain_text', $header_message, true ),
					],
				];

				$main_content = [
					'blocks' => [
						$blocks->single_column( 'mrkdwn', $developer_message ),
					],
				];

				$message = array_merge_recursive( $message, $main_content );

				if ( $website_url ) {
					$website = [
						'blocks' => [
							$blocks->single_column(
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
							$blocks->single_column(
								'mrkdwn',
								$this->get_send_text( 'dev_notify', 'detail' ) . ': ' . $update_page_url,
							),
						],
					];

					$message = array_merge_recursive( $message, $update_page );
				}

				$fixed_phrase = [
					'blocks' => [
						$blocks->divider(),
						$blocks->context(
							'mrkdwn',
							$this->get_send_text( 'dev_notify', 'ignore' ) . ': ' . $update_message['key'],
						),
						$blocks->context( 'mrkdwn', $context ),
					],
				];

				$message = array_merge_recursive( $message, $fixed_phrase );
			} elseif ( 'discord' === $tool ) {
				$title        = $site_name . '( <' . $site_url . '> ) ' . $message_title . "\n\n";
				$main_content = $developer_message . "\n";
				$website      = $website_url ? $this->get_send_text( 'dev_notify', 'website' ) . ': <' . $website_url . ">\n" : null;
				$update_page  = $update_page_url ? $this->get_send_text( 'dev_notify', 'detail' ) . ': <' . $update_page_url . ">\n" : null;
				$ignore       = "\n" . $this->get_send_text( 'dev_notify', 'ignore' ) . ': ' . $update_message['key'] . "\n";
				$message      = $title . $main_content . $website . $update_page . $ignore . "\n" . $this->make_context( $tool );
			} elseif ( 'chatwork' === $tool ) {
				$website     = $website_url ? $this->get_send_text( 'dev_notify', 'website' ) . ': ' . $website_url . "\n" : null;
				$update_page = $update_page_url ? $this->get_send_text( 'dev_notify', 'detail' ) . ': ' . $update_page_url . "\n" : null;
				$message     = [
					'body' =>
						'[info][title]' . $site_name . '( ' . $site_url . ' ) ' . $message_title . '[/title]' .
						$developer_message . "\n" .
						$website . $update_page .
						'[hr]' . $this->get_send_text( 'dev_notify', 'ignore' ) . ': ' . $update_message['key'] .
						$this->make_context( $tool ) .
						'[/info]',
				];
			}
		}
		return $message;
	}

	/**
	 * Create comment notify content.
	 *
	 * @param string $type  Message type.
	 * @param string $param Item parameter.
	 */
	private function get_send_text( string $type, string $param ): string {
		$message = [
			'comment'    => [
				'title'      => esc_html__( 'new comment has been posted.', 'send-chat-tools' ),
				'article'    => esc_html__( 'Commented article:', 'send-chat-tools' ),
				'author'     => esc_html__( 'Author:', 'send-chat-tools' ),
				'date'       => esc_html__( 'Date and time:', 'send-chat-tools' ),
				'content'    => esc_html__( 'Text:', 'send-chat-tools' ),
				'url'        => esc_html__( 'Comment URL:', 'send-chat-tools' ),
				'status'     => esc_html__( 'Comment Status:', 'send-chat-tools' ),
				'approved'   => esc_html__( 'Approved', 'send-chat-tools' ),
				'unapproved' => esc_html__( 'Unapproved', 'send-chat-tools' ),
				'click'      => esc_html__( 'Click here to approve', 'send-chat-tools' ),
				'spam'       => esc_html__( 'Spam', 'send-chat-tools' ),
			],
			'update'     => [
				'title'  => esc_html__( 'Notification of new updates.', 'send-chat-tools' ),
				'update' => esc_html__( 'Please login to the admin panel to update.', 'send-chat-tools' ),
				'page'   => esc_html__( 'Update Page:', 'send-chat-tools' ),
			],
			'dev_notify' => [
				'title'   => esc_html__( 'Notification of plugin updates from', 'send-chat-tools' ),
				'website' => esc_html__( 'Official Web Site', 'send-chat-tools' ),
				'detail'  => esc_html__( 'Update details', 'send-chat-tools' ),
				'ignore'  => esc_html__(
					'If this message is sent by a malicious developer, it can be rejected with the following key'
				),
			],
		];

		return $message[ $type ][ $param ];
	}

	/**
	 * Create comment approved message.
	 *
	 * @param string $tool_name Tool name.
	 * @param object $comment   Comment data.
	 */
	private function make_comment_approved_message( string $tool_name, object $comment ): string {
		$comment_status = '';
		$approved_url   = admin_url() . 'comment.php?action=approve&c=' . $comment->comment_ID;
		$unapproved     = $this->get_send_text( 'comment', 'unapproved' );
		$click_message  = $this->get_send_text( 'comment', 'click' );

		switch ( $comment->comment_approved ) {
			case '1':
				$comment_status = $this->get_send_text( 'comment', 'approved' );
				break;
			case '0':
				switch ( $tool_name ) {
					case 'slack':
						$comment_status = $unapproved . '<<' . $approved_url . '|' . $click_message . '>>';
						break;
					case 'discord':
						$comment_status = $unapproved . ' >> ' . $click_message . '( ' . $approved_url . ' )';
						break;
					case 'chatwork':
						$comment_status = $unapproved . "\n" . $click_message . ' ' . $approved_url;
						break;
				}
				break;
			case 'spam':
				$comment_status = $this->get_send_text( 'comment', 'spam' );
				break;
		}

		return $comment_status;
	}

	/**
	 * Create context message.
	 *
	 * @param string $tool_name Tool name.
	 */
	private function make_context( string $tool_name ): string {
		$message = [
			0 => esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ),
			1 => esc_html__( 'WordPress Plugin Directory', 'send-chat-tools' ),
			2 => esc_html__( 'Send Chat Tools Official Page', 'send-chat-tools' ),
		];

		$wordpress_directory = 'https://wordpress.org/plugins/send-chat-tools/';
		$official_web_site   = 'https://www.braveryk7.com/portfolio/send-chat-tools/';

		switch ( $tool_name ) {
			case 'slack':
				$context = $message[0] . "\n" . '<' . $wordpress_directory . '|' . $message[1] . '> / <' . $official_web_site . '|' . $message[2] . '>';
				break;
			case 'discord':
				$context = $message[0] . "\n" . $message[1] . ' <' . $wordpress_directory . '>' . "\n" . $message[2] . ' <' . $official_web_site . '>';
				break;
			case 'chatwork':
				$context = '[hr]' . $message[0] . "\n" . $message[1] . ' ' . $wordpress_directory . "\n" . $message[2] . ' ' . $official_web_site;
				break;
		}

		return $context;
	}
}
