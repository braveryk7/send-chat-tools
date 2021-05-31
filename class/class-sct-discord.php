<?php
/**
 * Use Discord.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 0.2.0
 */

declare( strict_type = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}
/**
 * Send Discord.
 */
class Sct_Discord {
	/**
	 * Send Discord.
	 */
	public static function send_discord() {
		require_once dirname( __FILE__ ) . '/class-sct-encryption.php';

		global $wpdb;
		$comment     = get_comment( $wpdb->insert_id );
		$send_author = get_option( 'sct_send_discord_author' );

		if ( ( '0' === $send_author ) || ( '1' === $send_author && '0' === $comment->user_id ) ) {
			$url              = get_option( 'sct_discord_webhook_url' );
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

			$message =
				$site_name . '( ' . $site_url . ' )' . esc_html__( 'new comment has been posted.', 'send-chat-tools' ) . "\n\n" .
				esc_html__( 'Commented article:', 'send-chat-tools' ) . $article_title . ' - ' . $article_url . "\n" .
				esc_html__( 'Author:', 'send-chat-tools' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
				esc_html__( 'Date and time:', 'send-chat-tools' ) . $comment->comment_date . "\n" .
				esc_html__( 'Text:', 'send-chat-tools' ) . "\n" . $comment->comment_content . "\n\n" .
				esc_html__( 'Comment URL:', 'send-chat-tools' ) . $article_url . '#comment-' . $comment->comment_ID . "\n\n" .
				esc_html__( 'Comment Status:', 'send-chat-tools' ) . $comment_status;

			$options = [
				'method'  => 'POST',
				'headers' => [
					'Content-Type: application/json;charset=utf-8',
				],
				'body'    => [
					'content' => $message,
				],
			];

			$result = wp_remote_post( Sct_Encryption::decrypt( $url ), $options );
			update_option( 'sct_discord_log', $result );

			if ( ! isset( $result->errors ) ) {
				$states_code = $result['response']['code'];
			} else {
				$states_code = 1000;
			}
			if ( 204 !== $states_code ) {
				require_once dirname( __FILE__ ) . '/class-sct-error-mail.php';
				$send_mail = new Sct_Error_Mail( $states_code, $wpdb->insert_id );
				$send_mail->make_contents();
			}
		}
	}
}
