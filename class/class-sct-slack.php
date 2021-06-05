<?php
/**
 * Use Slack.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 0.0.1
 */

declare( strict_type = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}
/**
 * Send Slack.
 */
class Sct_Slack {
	/**
	 * Send Slack.
	 */
	public static function create_comment_contents() {
		global $wpdb;
		$comment     = get_comment( $wpdb->insert_id );
		$send_author = get_option( 'sct_send_slack_author' );

		if ( ( '0' === $send_author ) || ( '1' === $send_author && '0' === $comment->user_id ) ) {
			$site_name        = get_bloginfo( 'name' );
			$site_url         = get_bloginfo( 'url' );
			$comment_approved = $comment->comment_approved;
			$article_title    = get_the_title( $comment->comment_post_ID );
			$article_url      = get_permalink( $comment->comment_post_ID );
			$approved_url     = admin_url() . 'comment.php?action=approve&c=' . $comment->comment_ID;

			if ( '1' === $comment_approved ) {
				$comment_status = esc_html__( 'Approved', 'send-chat-tools' );
			} elseif ( '0' === $comment_approved ) {
				$comment_status = esc_html__( 'Unapproved', 'send-chat-tools' ) . '<<' . $approved_url . '|' . esc_html__( 'Click here to approve', 'send-chat-tools' ) . '>>';
			} elseif ( 'spam' === $comment_approved ) {
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

			$options = [
				'headers' => [
					'Content-Type: application/json;charset=utf-8',
				],
				'body'    => wp_json_encode( $message ),
			];

			self::send_slack( $options, $wpdb->insert_id );
		}
	}

	/**
	 * Create update contents.
	 *
	 * @param array $result Update data.
	 */
	public static function create_update_contents( array $result ) {
		$site_name = get_bloginfo( 'name' );
		$site_url  = get_bloginfo( 'url' );
		$admin_url = admin_url() . 'update-core.php';
		$id        = 'update';
		$add_plugins;
		$add_themes;
		$add_core;

		foreach ( $result as $key => $value ) {
			if ( 'plugin' === $value['attribute'] ) {
				$add_plugins .= '   ' . $value['name'] . ' ( ' . $value['current_version'] . ' -> ' . $value['new_version'] . ' )' . "\n";
			} elseif ( 'theme' === $value['attribute'] ) {
				$add_themes .= '   ' . $value['name'] . ' ( ' . $value['current_version'] . ' -> ' . $value['new_version'] . ' )' . "\n";
			} elseif ( 'core' === $value['attribute'] ) {
				$add_core .= '   ' . $value['name'] . ' ( ' . $value['current_version'] . ' -> ' . $value['new_version'] . ' )' . "\n";
			};
		};

		if ( isset( $add_core ) ) {
			$core = esc_html__( 'WordPress Core:', 'send-chat-tools' ) . "\n" . $add_core . "\n\n";
		}
		if ( isset( $add_themes ) ) {
			$themes = esc_html__( 'Themes:', 'send-chat-tools' ) . "\n" . $add_themes . "\n\n";
		}
		if ( isset( $add_plugins ) ) {
			$plugins = esc_html__( 'Plugins:', 'send-chat-tools' ) . "\n" . $add_plugins . "\n\n";
		}

		$message = [
			'text' =>
				$site_name . '( ' . $site_url . ' )' . esc_html__( 'Notification of new updates.', 'send-chat-tools' ) . "\n\n" .
				$core . $themes . $plugins .
				esc_html__( 'Please login to the admin panel to update.', 'send-chat-tools' ) . "\n" .
				esc_html__( 'Update Page:', 'send-chat-tools' ) . $admin_url . "\n\n" .
				esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ) .
				'https://wordpress.org/plugins/send-chat-tools/',
		];

		$options = [
			'headers' => [
				'Content-Type: application/json;charset=utf-8',
			],
			'body'    => wp_json_encode( $message ),
		];

		self::send_slack( $options, $id );
	}

	/**
	 * Send Slack.
	 *
	 * @param array  $options Slack API options.
	 * @param string $id Comment ID.
	 */
	private static function send_slack( array $options, string $id ) {
		require_once dirname( __FILE__ ) . '/class-sct-encryption.php';

		$url    = get_option( 'sct_slack_webhook_url' );
		$result = wp_remote_post( Sct_Encryption::decrypt( $url ), $options );
		update_option( 'sct_slack_log', $result );

		if ( ! isset( $result->errors ) ) {
			$states_code = $result['response']['code'];
		} else {
			$states_code = 1000;
		}
		if ( 200 !== $states_code ) {
			require_once dirname( __FILE__ ) . '/class-sct-error-mail.php';
			if ( 'update' === $id ) {
				$send_mail = new Sct_Error_mail( $states_code, $id );
				$send_mail->update_contents( $options );
			} else {
				$send_mail = new Sct_Error_Mail( $states_code, $id );
				$send_mail->make_contents();
			}
		}
	}
}
