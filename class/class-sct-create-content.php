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
	public function controller( int $comment_id = 0, string $type = 'comment', array $check_date = [] ): void {
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
		} elseif ( 'plugin_update' === $type ) {
			foreach ( $tools as $tool ) {
				if ( $sct_options[ $tool ]['use'] ) {
					$options = $this->create_content( $type, $tool, null, $check_date );
					$this->send_tools( $options, 'plugin_update', $tool );
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
	 * @param string $type Create type.
	 * @param string $tool Tool name.
	 * @param object $comment Comment data.
	 * @param array  $check_data Update check data.
	 */
	private function create_content( string $type, string $tool, object $comment = null, array $check_data = [] ): array {
		$message = [];
		switch ( $type ) {
			case 'comment':
				$message = $this->create_comment_message( $comment, $tool );
				break;
			case 'update':
				$message = $this->create_update_message( $check_data, $tool );
				break;
			case 'plugin_update':
				$message = $this->create_plugin_update_message( $tool );
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
	 * @param object $comment Comment data.
	 * @param string $tool Tool name.
	 */
	private function create_comment_message( object $comment, string $tool ) {
		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$article_title  = get_the_title( $comment->comment_post_ID );
		$article_url    = get_permalink( $comment->comment_post_ID );
		$comment_status = $this->get_comment_approved_message( $tool, $comment );

		if ( 'slack' === $tool ) {
			$header_emoji     = ':mailbox_with_mail:';
			$header_message   = "{$header_emoji} {$site_name}({$site_url})" . $this->get_comment_text( 'comment', 'title' );
			$comment_article  = '*' . $this->get_comment_text( 'comment', 'article' ) . "*<{$article_url}|{$article_title}>";
			$author           = '*' . $this->get_comment_text( 'comment', 'author' ) . "*\n{$comment->comment_author}<{$comment->comment_author_email}>";
			$date             = '*' . $this->get_comment_text( 'comment', 'date' ) . "*\n{$comment->comment_date}";
			$comment_content  = '*' . $this->get_comment_text( 'comment', 'content' ) . "*\n{$comment->comment_content}";
			$comment_url      = '*' . $this->get_comment_text( 'comment', 'url' ) . "*\n{$article_url}#comment-{$comment->comment_ID}";
			$comment_statuses = '*' . $this->get_comment_text( 'comment', 'status' ) . "*\n{$comment_status}";
			$context          = $this->create_context( $tool );

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
				$site_name . '( <' . $site_url . '> )' . $this->get_comment_text( 'comment', 'title' ) . "\n\n" .
				$this->get_comment_text( 'comment', 'article' ) . $article_title . ' - <' . $article_url . '>' . "\n" .
				$this->get_comment_text( 'comment', 'author' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
				$this->get_comment_text( 'comment', 'date' ) . $comment->comment_date . "\n" .
				$this->get_comment_text( 'comment', 'content' ) . "\n" . $comment->comment_content . "\n\n" .
				$this->get_comment_text( 'comment', 'url' ) . '<' . $article_url . '#comment-' . $comment->comment_ID . '>' . "\n\n" .
				$this->get_comment_text( 'comment', 'status' ) . $comment_status . "\n\n" .
				$this->create_context( $tool );
		} elseif ( 'chatwork' === $tool ) {
			$message = [
				'body' =>
					'[info][title]' . $site_name . '(' . $site_url . ')' . $this->get_comment_text( 'comment', 'title' ) . '[/title]' .
					$this->get_comment_text( 'comment', 'article' ) . $article_title . ' - ' . $article_url . "\n" .
					$this->get_comment_text( 'comment', 'author' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
					$this->get_comment_text( 'comment', 'date' ) . $comment->comment_date . "\n" .
					$this->get_comment_text( 'comment', 'content' ) . "\n" . $comment->comment_content . "\n\n" .
					$this->get_comment_text( 'comment', 'url' ) . $article_url . '#comment-' . $comment->comment_ID . "\n\n" .
					'[hr]' .
					$this->get_comment_text( 'comment', 'status' ) . $comment_status .
					'[hr]' . $this->create_context( $tool ) .
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
		$site_name   = get_bloginfo( 'name' );
		$site_url    = get_bloginfo( 'url' );
		$admin_url   = admin_url() . 'update-core.php';
		$add_plugins = '';
		$add_themes  = '';
		$add_core    = '';

		foreach ( $check_data as $key => $value ) {
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
			$context        = $this->create_context( $tool );

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
				$this->create_context( $tool );
		} elseif ( 'chatwork' === $tool ) {
			isset( $core ) ? $core       = $core . '[hr]' : $core;
			isset( $themes ) ? $themes   = $themes . '[hr]' : $themes;
			isset( $plugins ) ? $plugins = $plugins . '[hr]' : $themes;

			$message = [
				'body' =>
					'[info][title]' . $site_name . '( ' . $site_url . ' )' . esc_html__( 'Notification of new updates.', 'send-chat-tools' ) . '[/title]' .
					$core . $themes . $plugins .
					esc_html__( 'Please login to the admin panel to update.', 'send-chat-tools' ) . "\n" .
					esc_html__( 'Update Page:', 'send-chat-tools' ) . $admin_url . "\n\n" .
					$this->create_context( $tool ) .
					'[/info]',
			];
		}

		return $message;
	}

	/**
	 * Create plugin update message.
	 *
	 * @param string $tool Tool name.
	 */
	private function create_plugin_update_message( string $tool ) {
		$site_name         = get_bloginfo( 'name' );
		$site_url          = get_bloginfo( 'url' );
		$admin_url         = admin_url() . 'update-core.php';
		$developer_message = '';

		foreach ( $this->get_developer_messages() as $value ) {
			$developer_message .= $value . "\n";
		}

		if ( 'slack' === $tool ) {
			$header_emoji   = ':tada:';
			$header_message = "{$header_emoji} {$site_name}({$site_url}) " . esc_html__( 'Plugin update', 'send-chat-tools' );

			$context = $this->create_context( $tool );

			$blocks  = new Sct_Slack_Blocks();
			$message = [
				'text'   => $header_message,
				'blocks' => [
					$blocks->header( 'plain_text', $header_message, true ),
				],
			];

			$fixed_phrase = [
				'blocks' => [
					$blocks->single_column( 'mrkdwn', $developer_message ),
					$blocks->divider(),
					$blocks->context( 'mrkdwn', $context ),
				],
			];

			$message = array_merge_recursive( $message, $fixed_phrase );
		} elseif ( 'discord' === $tool ) {
			$message =
				$site_name . '( <' . $site_url . '> ) ' . esc_html__( 'Plugin update', 'send-chat-tools' ) . "\n\n" .
				$developer_message . "\n" . $this->create_context( $tool );
		} elseif ( 'chatwork' === $tool ) {
			$message = [
				'body' =>
					'[info][title]' . $site_name . '( ' . $site_url . ' ) ' . esc_html__( 'Plugin update', 'send-chat-tools' ) . '[/title]' .
					$developer_message . "\n" . $this->create_context( $tool ) .
					'[/info]',
			];
		}

		return $message;
	}

	/**
	 * Create comment notify content.
	 *
	 * @param string $type  Message type.
	 * @param string $param Item parameter.
	 */
	private function get_comment_text( string $type, string $param ) {
		$message = [
			'comment' => [
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
			'update'  => [
				'update' => esc_html__( 'Please login to the admin panel to update.', 'send-chat-tools' ),
				'page'   => esc_html__( 'Update Page:', 'send-chat-tools' ),
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
	private function get_comment_approved_message( string $tool_name, object $comment ) {
		$comment_status = '';
		$approved_url   = admin_url() . 'comment.php?action=approve&c=' . $comment->comment_ID;
		$unapproved     = $this->get_comment_text( 'comment', 'unapproved' );
		$click_message  = $this->get_comment_text( 'comment', 'click' );

		switch ( $comment->comment_approved ) {
			case '1':
				$comment_status = $this->get_comment_text( 'comment', 'approved' );
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
				$comment_status = $this->get_comment_text( 'comment', 'spam' );
				break;
		}

		return $comment_status;
	}

	/**
	 * Create context message.
	 *
	 * @param string $tool_name Tool name.
	 */
	private function create_context( string $tool_name ) {
		$context = '';
		$message = [
			0 => esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ),
			1 => esc_html__( 'WordPress Plugin Directory', 'send-chat-tools' ),
			2 => esc_html__( 'Send Chat Tools Official Page', 'send-chat-tools' ),
		];

		switch ( $tool_name ) {
			case 'slack':
				$context =
					$message[0] . "\n" .
					'<https://wordpress.org/plugins/send-chat-tools/|' . $message[1] . '> / ' .
					'<https://www.braveryk7.com/portfolio/send-chat-tools/|' . $message[2] . '>';
				break;
			case 'discord':
				$context =
					$message[0] . "\n" .
					$message[1] . ' <https://wordpress.org/plugins/send-chat-tools/>' . "\n" .
					$message[2] . ' <https://www.braveryk7.com/portfolio/send-chat-tools/>';
				break;
			case 'chatwork':
				$context =
					$message[0] . "\n" .
					$message[1] . ' https://wordpress.org/plugins/send-chat-tools/' . "\n" .
					$message[2] . ' https://www.braveryk7.com/portfolio/send-chat-tools/';
				break;
		}

		return $context;
	}
}
