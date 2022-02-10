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
		require_once 'class-sct-slack-blocks.php';
	}

	/**
	 * Check tools use status and call.
	 *
	 * @param int    $comment_id Comment ID (0: default).
	 * @param string $type Content Type (comment: default, update).
	 * @param array  $check_date Update cehck date ([]: default).
	 */
	public function controller( int $comment_id = 0, string $type = 'comment', array $check_date = [] ) {
		global $wpdb;

		$sct_options = $this->get_sct_options();
		$tools       = [ 'slack', 'discord', 'chatwork' ];

		if ( 'comment' === $type ) {
			$comment = $this->get_comment_data( $comment_id );

			foreach ( $tools as $tool ) {
				'chatwork' === $tool ? $api_column = 'api_token' : $api_column = 'webhook_url';

				if ( $this->get_send_status( $tool, $sct_options[ $tool ], $comment->user_id ) ) {
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
					$options = $this->create_content( $type, $tool, null, $check_date );
					$this->send_tools( $options, 'update', $tool );
				}
			}
		}
	}

	/**
	 * Check send status.
	 *
	 * @param string $tool_name Tool name.
	 * @param array  $tools Tool options -> sct_options[TOOLNAME].
	 * @param string $comment_user_id Comment user id.
	 */
	private function get_send_status( string $tool_name, array $tools, string $comment_user_id ): bool {
		$status     = [
			'use'    => false,
			'api'    => false,
			'author' => false,
		];
		$api_exists = false;

		if ( $tools['use'] ) {
			$status['use'] = true;
		}

		if ( ! $tools['send_author'] || $tools['send_author'] && '0' === $comment_user_id ) {
			$status['author'] = true;
		}

		switch ( $tool_name ) {
			case 'slack':
			case 'discord':
				$api = $tools['webhook_url'];

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
	 * @param string $type Create type.
	 * @param string $tool Tool name.
	 * @param object $comment Comment data.
	 * @param array  $check_data Update check data.
	 */
	private function create_content( string $type, string $tool, object $comment = null, array $check_data = [] ): array {
		if ( 'slack' === $tool ) {
			$content_type = [ 'Content-Type: application/json;charset=utf-8' ];

			if ( 'comment' === $type ) {
				$message = $this->create_comment_message( $comment, $tool );
			} elseif ( 'update' === $type ) {
				$message = $this->create_update_message( $check_data, $tool );
			}

			$body = wp_json_encode( $message );

		} elseif ( 'discord' === $tool ) {
			$content_type = [ 'Content-Type: application/json;charset=utf-8' ];

			if ( 'comment' === $type ) {
				$message = $this->create_comment_message( $comment, $tool );
			} elseif ( 'update' === $type ) {
				$message = $this->create_update_message( $check_data, $tool );
			}

			$body = [ 'content' => $message ];

		} elseif ( 'chatwork' === $tool ) {
			$sct_options  = $this->get_sct_options();
			$content_type = 'X-ChatWorkToken: ' . $sct_options['chatwork']['api_token'];

			if ( 'comment' === $type ) {
				$message = $this->create_comment_message( $comment, $tool );
			} elseif ( 'update' === $type ) {
				$message = $this->create_update_message( $check_data, $tool );
			}

			$body = $message;
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
	 * @param object $comment Comment data.
	 * @param string $tool Tool name.
	 */
	private function create_comment_message( object $comment, string $tool ) {
		$site_name     = get_bloginfo( 'name' );
		$site_url      = get_bloginfo( 'url' );
		$article_title = get_the_title( $comment->comment_post_ID );
		$article_url   = get_permalink( $comment->comment_post_ID );
		$approved_url  = admin_url() . 'comment.php?action=approve&c=' . $comment->comment_ID;

		if ( 'slack' === $tool ) {
			if ( '1' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Approved', 'send-chat-tools' );
			} elseif ( '0' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Unapproved', 'send-chat-tools' ) . '<<' . $approved_url . '|' . esc_html__( 'Click here to approve', 'send-chat-tools' ) . '>>';
			} elseif ( 'spam' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Spam', 'send-chat-tools' );
			}

			$header_emoji     = ':mailbox_with_mail:';
			$header_message   = "{$header_emoji} {$site_name}({$site_url})" . esc_html__( 'new comment has been posted.', 'send-chat-tools' );
			$comment_article  = '*' . esc_html__( 'Commented article:', 'send-chat-tools' ) . "*<{$article_url}|{$article_title}>";
			$author           = '*' . esc_html__( 'Author:', 'send-chat-tools' ) . "*\n{$comment->comment_author}<{$comment->comment_author_email}>";
			$date             = '*' . esc_html__( 'Date and time:', 'send-chat-tools' ) . "*\n{$comment->comment_date}";
			$comment_content  = '*' . esc_html__( 'Text:', 'send-chat-tools' ) . "*\n{$comment->comment_content}";
			$comment_url      = '*' . esc_html__( 'Comment URL:', 'send-chat-tools' ) . "*\n{$article_url}#comment-{$comment->comment_ID}";
			$comment_statuses = '*' . esc_html__( 'Comment Status:', 'send-chat-tools' ) . "*\n{$comment_status}";
			$context          =
			esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ) . "\n" .
			'<https://wordpress.org/plugins/send-chat-tools/|' . esc_html__( 'WordPress Plugin Directory', 'send-chat-tools' ) . '> / ' .
			'<https://www.braveryk7.com/portfolio/send-chat-tools/|' . esc_html__( 'Send Chat Tools Official Page', 'send-chat-tools' ) . '>';

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
			if ( '1' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Approved', 'send-chat-tools' );
			} elseif ( '0' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Unapproved', 'send-chat-tools' ) . ' >> ' . esc_html__( 'Click here to approve', 'send-chat-tools' ) . '( ' . $approved_url . ' )';
			} elseif ( 'spam' === $comment->comment_approved ) {
				$comment_status = esc_html__( 'Spam', 'send-chat-tools' );
			}

			$message =
			$site_name . '( <' . $site_url . '> )' . esc_html__( 'new comment has been posted.', 'send-chat-tools' ) . "\n\n" .
			esc_html__( 'Commented article:', 'send-chat-tools' ) . $article_title . ' - <' . $article_url . '>' . "\n" .
			esc_html__( 'Author:', 'send-chat-tools' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
			esc_html__( 'Date and time:', 'send-chat-tools' ) . $comment->comment_date . "\n" .
			esc_html__( 'Text:', 'send-chat-tools' ) . "\n" . $comment->comment_content . "\n\n" .
			esc_html__( 'Comment URL:', 'send-chat-tools' ) . '<' . $article_url . '#comment-' . $comment->comment_ID . '>' . "\n\n" .
			esc_html__( 'Comment Status:', 'send-chat-tools' ) . $comment_status;
		} elseif ( 'chatwork' === $tool ) {
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
		}

		return $message;
	}

	/**
	 * Create update message.
	 *
	 * @param array  $check_data Update data.
	 * @param string $tool Tool name.
	 */
	private function create_update_message( array $check_data, string $tool ) {
		$site_name = get_bloginfo( 'name' );
		$site_url  = get_bloginfo( 'url' );
		$admin_url = admin_url() . 'update-core.php';
		$add_plugins;
		$add_themes;
		$add_core;

		foreach ( $check_data as $key => $value ) {
			if ( 'plugin' === $value['attribute'] ) {
				$add_plugins .= '   ' . $value['name'] . ' ( ' . $value['current_version'] . ' -> ' . $value['new_version'] . ' )' . "\n";
			} elseif ( 'theme' === $value['attribute'] ) {
				$add_themes .= '   ' . $value['name'] . ' ( ' . $value['current_version'] . ' -> ' . $value['new_version'] . ' )' . "\n";
			} elseif ( 'core' === $value['attribute'] ) {
				$add_core .= '   ' . $value['name'] . ' ( ' . $value['current_version'] . ' -> ' . $value['new_version'] . ' )' . "\n";
			};
		};

		if ( isset( $add_core ) ) {
			$core = esc_html__( 'WordPress Core:', 'send-chat-tools' ) . "\n" . $add_core . "\n";
		}
		if ( isset( $add_themes ) ) {
			$themes = esc_html__( 'Themes:', 'send-chat-tools' ) . "\n" . $add_themes . "\n";
		}
		if ( isset( $add_plugins ) ) {
			$plugins = esc_html__( 'Plugins:', 'send-chat-tools' ) . "\n" . $add_plugins . "\n";
		}

		if ( 'slack' === $tool ) {
			$header_emoji   = ':zap:';
			$header_message = "{$header_emoji} {$site_name}({$site_url})" . esc_html__( 'Notification of new updates.', 'send-chat-tools' );
			$update_message =
			esc_html__( 'Please login to the admin panel to update.', 'send-chat-tools' ) . "\n" .
			esc_html__( 'Update Page:', 'send-chat-tools' ) . "<{$admin_url}>";
			$context        =
			esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ) . "\n" .
			'<https://wordpress.org/plugins/send-chat-tools/|' . esc_html__( 'WordPress Plugin Directory', 'send-chat-tools' ) . '> / ' .
			'<https://www.braveryk7.com/portfolio/send-chat-tools/|' . esc_html__( 'Send Chat Tools Official Page', 'send-chat-tools' ) . '>';

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
			$site_name . '( <' . $site_url . '> )' . esc_html__( 'Notification of new updates.', 'send-chat-tools' ) . "\n\n" .
			$core . $themes . $plugins .
			esc_html__( 'Please login to the admin panel to update.', 'send-chat-tools' ) . "\n" .
			esc_html__( 'Update Page:', 'send-chat-tools' ) . '<' . $admin_url . '>' . "\n\n" .
			esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ) .
			'<https://wordpress.org/plugins/send-chat-tools/>';
		} elseif ( 'chatwork' === $tool ) {
			if ( isset( $core ) ) {
				$core = $core . '[hr]';
			}
			if ( isset( $themes ) ) {
				$themes = $themes . '[hr]';
			}
			if ( isset( $plugins ) ) {
				$plugins = $plugins . '[hr]';
			}
			$message = [
				'body' =>
					'[info][title]' . $site_name . '( ' . $site_url . ' )' . esc_html__( 'Notification of new updates.', 'send-chat-tools' ) . '[/title]' .
					$core . $themes . $plugins .
					esc_html__( 'Please login to the admin panel to update.', 'send-chat-tools' ) . "\n" .
					esc_html__( 'Update Page:', 'send-chat-tools' ) . $admin_url . "\n\n" .
					esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ) .
					'https://wordpress.org/plugins/send-chat-tools/' . "\n" .
					'[/info]',
			];
		}

		return $message;
	}
}
