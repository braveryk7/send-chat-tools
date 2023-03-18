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
class Sct_Generate_Content extends Sct_Base {
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
			$comment = get_comment( $comment_id );

			foreach ( $tools as $tool ) {
				$api_column = 'chatwork' === $tool ? 'api_token' : 'webhook_url';

				if ( $this->get_send_status( $tool, $sct_options[ $tool ], $comment->user_id ) ) {
					global $wpdb;
					$options = $this->generate_content( type: $type, tool: $tool, comment: $comment );
					$this->send_tools( $options, (string) $wpdb->insert_id, $tool, $comment );
				} elseif ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ][ $api_column ] ) ) {
					$this->logger( 1001, $tool, '1' );
				} elseif ( 'chatwork' === $tools && ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ]['room_id'] ) ) ) {
					$this->logger( 1002, 'chatwork', '1' );
				};
			}
		} elseif ( 'update' === $type ) {
			foreach ( $tools as $tool ) {
				if ( $sct_options[ $tool ]['use'] && $sct_options[ $tool ]['send_update'] ) {
					$options = $this->generate_content( type: $type, tool: $tool, update_content: $update_content );
					$this->send_tools( $options, 'update', $tool );
				}
			}
		} elseif ( 'dev_notify' === $type ) {
			foreach ( $tools as $tool ) {
				if ( $sct_options[ $tool ]['use'] ) {
					$options = $this->generate_content( type: $type, tool: $tool, update_content: $update_content );
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
		$status = [
			'use'    => false,
			'api'    => false,
			'author' => false,
		];

		$status['use'] = $tools['use'] ? true : false;

		$status['author'] = ! $tools['send_author'] || $tools['send_author'] && '0' === $comment_user_id ? true : false;

		switch ( $tool_name ) {
			case 'slack':
			case 'discord':
				$api           = $tools['webhook_url'];
				$status['api'] = ! empty( $api ) ? true : false;
				break;
			case 'chatwork':
				$api           = [
					'api_token' => $tools['api_token'],
					'room_id'   => $tools['room_id'],
				];
				$status['api'] = ! empty( $api['api_token'] ) && ! empty( $api['room_id'] ) ? true : false;
				break;
			default:
				$status['api'] = false;
		}

		return in_array( false, $status, true ) ? false : true;
	}

	/**
	 * Generate send options.
	 *
	 * @param string $type           Generate type.
	 * @param string $tool           Tool name.
	 * @param object $comment        Comment data.
	 * @param array  $update_content Update data or message.
	 */
	private function generate_content( string $type, string $tool, object $comment = null, array $update_content = [] ): array {
		$message = [];
		switch ( $type ) {
			case 'comment':
				$message = $this->generate_comment_message( $tool, $comment );
				break;
			case 'update':
				$plain_data = $this->generate_update_message( $tool, $update_content );
				$message    = $this->generate_processed_chat_tools( $plain_data );
				break;
			case 'dev_notify':
				$message = $this->generate_developer_message( $tool, $update_content );
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

		if ( 'update' === $type && isset( $plain_data ) ) {
			$options['plain_data'] = $plain_data;
		}

		return $options;
	}

	/**
	 * Generate comment message.
	 *
	 * @param string $tool    Tool name.
	 * @param object $comment Comment data.
	 */
	private function generate_comment_message( string $tool, object $comment ): string | array {
		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$article_title  = get_the_title( $comment->comment_post_ID );
		$article_url    = get_permalink( $comment->comment_post_ID );
		$comment_status = $this->generate_comment_approved_message( $tool, $comment );

		if ( 'slack' === $tool ) {
			$header_emoji     = ':mailbox_with_mail:';
			$header_message   = "{$header_emoji} {$site_name}({$site_url})" . $this->get_send_text( 'comment', 'title' );
			$comment_article  = '*' . $this->get_send_text( 'comment', 'article' ) . "*<{$article_url}|{$article_title}>";
			$author           = '*' . $this->get_send_text( 'comment', 'author' ) . "*\n{$comment->comment_author}<{$comment->comment_author_email}>";
			$date             = '*' . $this->get_send_text( 'comment', 'date' ) . "*\n{$comment->comment_date}";
			$comment_content  = '*' . $this->get_send_text( 'comment', 'content' ) . "*\n{$comment->comment_content}";
			$comment_url      = '*' . $this->get_send_text( 'comment', 'url' ) . "*\n{$article_url}#comment-{$comment->comment_ID}";
			$comment_statuses = '*' . $this->get_send_text( 'comment', 'status' ) . "*\n{$comment_status}";
			$context          = $this->generate_context( $tool );

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
				$this->generate_context( $tool );
		} elseif ( 'chatwork' === $tool ) {
			$message = [
				'body' =>
					'[info][title]' . $site_name . '(' . $site_url . ')' . $this->get_send_text( 'comment', 'title' ) . '[/title]' .
					$this->get_send_text( 'comment', 'article' ) . $article_title . ' - ' . $article_url . "\n" .
					$this->get_send_text( 'comment', 'author' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
					$this->get_send_text( 'comment', 'date' ) . $comment->comment_date . "\n" .
					$this->get_send_text( 'comment', 'content' ) . "\n" . $comment->comment_content . "\n\n" .
					$this->get_send_text( 'comment', 'url' ) . $article_url . '#comment-' . $comment->comment_ID . "\n" .
					'[hr]' .
					$this->get_send_text( 'comment', 'status' ) . $comment_status .
					$this->generate_context( $tool ) .
					'[/info]',
			];
		}

		return $message;
	}

	/**
	 * Generate update message.
	 *
	 * @param string $tool           Tool name.
	 * @param array  $update_content Update data.
	 */
	private function generate_update_message( string $tool, array $update_content ): stdClass {
		$add_core    = null;
		$add_themes  = null;
		$add_plugins = null;

		foreach ( $update_content as $value ) {
			$is_core                              = 'core' === $value['attribute'] ? null : 's';
			${ "add_$value[attribute]$is_core" } .= "   $value[name] ( $value[current_version] -> $value[new_version] )\n";
		};

		$plain               = new stdClass();
		$plain->tools        = $tool;
		$plain->core         = isset( $add_core ) ? esc_html__( 'WordPress Core:', 'send-chat-tools' ) . "\n" . $add_core . "\n" : null;
		$plain->themes       = isset( $add_themes ) ? esc_html__( 'Themes:', 'send-chat-tools' ) . "\n" . $add_themes . "\n" : null;
		$plain->plugins      = isset( $add_plugins ) ? esc_html__( 'Plugins:', 'send-chat-tools' ) . "\n" . $add_plugins . "\n" : null;
		$plain->site_name    = get_bloginfo( 'name' );
		$plain->site_url     = get_bloginfo( 'url' );
		$plain->update_page  = $this->get_send_text( 'update', 'page' );
		$plain->admin_url    = admin_url() . 'update-core.php';
		$plain->update_title = $this->get_send_text( 'update', 'title' );
		$plain->update_text  = $this->get_send_text( 'update', 'update' );
		return $plain;
	}

	/**
	 * Generate plugin update message.
	 *
	 * @param string $tool           Tool name.
	 * @param array  $update_message Update message.
	 */
	private function generate_developer_message( string $tool, array $update_message ): string | array {
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

				$context = $this->generate_context( $tool );

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
				$message      = $title . $main_content . $website . $update_page . $ignore . "\n" . $this->generate_context( $tool );
			} elseif ( 'chatwork' === $tool ) {
				$website     = $website_url ? $this->get_send_text( 'dev_notify', 'website' ) . ': ' . $website_url . "\n" : null;
				$update_page = $update_page_url ? $this->get_send_text( 'dev_notify', 'detail' ) . ': ' . $update_page_url . "\n" : null;
				$message     = [
					'body' =>
						'[info][title]' . $site_name . '( ' . $site_url . ' ) ' . $message_title . '[/title]' .
						$developer_message . "\n" .
						$website . $update_page .
						'[hr]' . $this->get_send_text( 'dev_notify', 'ignore' ) . ': ' . $update_message['key'] .
						$this->generate_context( $tool ) .
						'[/info]',
				];
			}
		}
		return $message;
	}

	/**
	 * Processed for chat tools.
	 *
	 * @param stdClass $plain_data Plain data.
	 */
	public function generate_processed_chat_tools( stdClass $plain_data ) {
		if ( 'slack' === $plain_data->tools ) {
			$header_emoji   = ':zap:';
			$header_message = "{$header_emoji} {$plain_data->site_name}({$plain_data->site_url})" . $plain_data->update_title;
			$update_message = $plain_data->update_text . "\n" . $plain_data->update_page . "<{$plain_data->admin_url}>";
			$context        = $this->generate_context( $plain_data->tools );

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
		} elseif ( 'discord' === $plain_data->tools ) {
			$message =
				$plain_data->site_name . '( <' . $plain_data->site_url . '> )' . $plain_data->update_title . "\n\n" .
				$plain_data->core . $plain_data->themes . $plain_data->plugins .
				$plain_data->update_text . "\n" . $plain_data->update_page . '<' . $plain_data->admin_url . '>' . "\n\n" .
				$this->generate_context( $plain_data->tools );
		} elseif ( 'chatwork' === $plain_data->tools ) {
			$core    = isset( $plain_data->core ) ? rtrim( $plain_data->core ) . '[hr]' : $plain_data->core;
			$themes  = isset( $plain_data->themes ) ? rtrim( $plain_data->themes ) . '[hr]' : $plain_data->themes;
			$plugins = isset( $plain_data->plugins ) ? rtrim( $plain_data->plugins ) . '[hr]' : $plain_data->plugins;

			$message = [
				'body' =>
					'[info][title]' . $plain_data->site_name . '( ' . $plain_data->site_url . ' )' . $plain_data->update_title . '[/title]' .
					$core . $themes . $plugins . $plain_data->update_text . "\n" . $plain_data->update_page . $plain_data->admin_url . "\n" .
					$this->generate_context( $plain_data->tools ) .
					'[/info]',
			];
		}

		return $message;
	}

	/**
	 * Get comment notify content.
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
					'If this message is sent by a malicious developer, it can be rejected with the following key',
					'send-chat-tools',
				),
			],
		];

		return $message[ $type ][ $param ];
	}

	/**
	 * Generate comment approved message.
	 *
	 * @param string $tool_name Tool name.
	 * @param object $comment   Comment data.
	 */
	private function generate_comment_approved_message( string $tool_name, object $comment ): string {
		$comment_status = '';
		$approved_url   = admin_url() . 'comment.php?action=approve&c=' . $comment->comment_ID;
		$unapproved     = $this->get_send_text( 'comment', 'unapproved' );
		$click_message  = $this->get_send_text( 'comment', 'click' );

		$comment_status = match ( $comment->comment_approved ) {
			'1'    => $this->get_send_text( 'comment', 'approved' ),
			'2'    => match ( $tool_name ) {
				'slack'    => $unapproved . '<<' . $approved_url . '|' . $click_message . '>>',
				'discord'  => $unapproved . ' >> ' . $click_message . '( ' . $approved_url . ' )',
				'chatwork' => $unapproved . "\n" . $click_message . ' ' . $approved_url,
			},
			'spam' => $this->get_send_text( 'comment', 'spam' ),
		};

		return $comment_status;
	}

	/**
	 * Generate context message.
	 *
	 * @param string $tool_name Tool name.
	 */
	private function generate_context( string $tool_name ): string {
		$message = [
			0 => esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ),
			1 => esc_html__( 'WordPress Plugin Directory', 'send-chat-tools' ),
			2 => esc_html__( 'Send Chat Tools Official Page', 'send-chat-tools' ),
		];

		$wordpress_directory = $this->get_official_directory();
		$official_web_site   = 'https://www.braveryk7.com/portfolio/send-chat-tools/';

		return match ( $tool_name ) {
			'slack'    => $message[0] . "\n" . '<' . $wordpress_directory . '|' . $message[1] . '> / <' . $official_web_site . '|' . $message[2] . '>',
			'discord'  => $message[0] . "\n" . $message[1] . ' <' . $wordpress_directory . '>' . "\n" . $message[2] . ' <' . $official_web_site . '>',
			'chatwork' => '[hr]' . $message[0] . "\n" . $message[1] . ' ' . $wordpress_directory . "\n" . $message[2] . ' ' . $official_web_site,
		};
	}
}
