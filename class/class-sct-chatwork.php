<?php
/**
 * Use Chatwork.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 0.0.2
 */

declare( strict_type = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}
/**
 * Send Chatwork.
 */
class Sct_Chatwork {
	/**
	 * Send Chatwork.
	 */
	public static function send_chatwork() {
		require_once dirname( __FILE__ ) . '/class-sct-encryption.php';

		global $wpdb;
		$comment     = get_comment( $wpdb->insert_id );
		$send_author = get_option( 'sct_send_chatwork_author' );

		if ( ( '0' === $send_author ) || ( '1' === $send_author && '0' === $comment->user_id ) ) {
			$api_token        = get_option( 'sct_chatwork_api_token' );
			$room_id          = get_option( 'sct_chatwork_room_id' );
			$chatwork_api_url = 'https://api.chatwork.com/v2/rooms/' . Sct_Encryption::decrypt( $room_id ) . '/messages';
			$site_name        = get_bloginfo( 'name' );
			$site_url         = get_bloginfo( 'url' );
			$comment_approved = $comment->comment_approved;
			$article_title    = get_the_title( $comment->comment_post_ID );
			$article_url      = get_permalink( $comment->comment_post_ID );
			$approved_url     = admin_url() . 'comment.php?action=approve&c=' . $comment->comment_ID;

			if ( '1' === $comment_approved ) {
				$comment_status = esc_html__( 'Approved', 'send-chat-tools' );
			} elseif ( '0' === $comment_approved ) {
				$comment_status = esc_html__( 'Unapproved', 'send-chat-tools' ) . "\n" . esc_html__( 'Click here to approve', 'send-chat-tools' ) . ' ' . $approved_url;
			} elseif ( 'spam' === $comment_approved ) {
				$comment_status = esc_html__( 'Spam', 'send-chat-tools' );
			}

			$contents = [
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

			$options = [
				'headers' => 'X-ChatWorkToken: ' . Sct_Encryption::decrypt( $api_token ),
				'body'    => $contents,
			];

			$result = wp_remote_post( $chatwork_api_url, $options );
			update_option( 'sct_chatwork_log', $result );

			if ( ! isset( $result->errors ) ) {
				$states_code = $result['response']['code'];
				if ( 200 !== $states_code ) {
					require_once dirname( __FILE__ ) . '/class-sct-error-mail.php';
					$send_mail = new Sct_Error_Mail( $states_code, $wpdb->insert_id );
					$send_mail->make_contents();
				}
			}
		}
	}
}
