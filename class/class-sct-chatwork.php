<?php
/**
 * Use Chatwork.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 0.0.2
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

require_once dirname( __FILE__ ) . '/trait-sct-sending.php';
require_once dirname( __FILE__ ) . '/class-sct-encryption.php';

/**
 * Send Chatwork.
 */
class Sct_Chatwork {
	use Sct_Sending;

	/**
	 * Send Chatwork.
	 */
	public static function create_comment_contents() {
		global $wpdb;
		$comment     = get_comment( $wpdb->insert_id );
		$send_author = get_option( 'sct_send_chatwork_author' );

		if ( ( '0' === $send_author ) || ( '1' === $send_author && '0' === $comment->user_id ) ) {
			$api_token        = get_option( 'sct_chatwork_api_token' );
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

			/* Use trait_sct_sending */
			self::sending( $options, $wpdb->insert_id, 'chatwork' );
		}
	}

	/**
	 * Create update contents.
	 *
	 * @param array $result Update data.
	 */
	public static function create_update_contents( array $result ) {
		$api_token = get_option( 'sct_chatwork_api_token' );
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
			$core = esc_html__( 'WordPress Core:', 'send-chat-tools' ) . "\n" . $add_core;
		}
		if ( isset( $add_themes ) ) {
			$themes = esc_html__( 'Themes:', 'send-chat-tools' ) . "\n" . $add_themes;
		}
		if ( isset( $add_plugins ) ) {
			$plugins = esc_html__( 'Plugins:', 'send-chat-tools' ) . "\n" . $add_plugins;
		}

		$message = [
			'body' =>
				'[info][title]' . $site_name . '( ' . $site_url . ' )' . esc_html__( 'Notification of new updates.', 'send-chat-tools' ) . '[/title]' .
				$core . '[hr]' . $themes . '[hr]' . $plugins . '[hr]' .
				esc_html__( 'Please login to the admin panel to update.', 'send-chat-tools' ) . "\n" .
				esc_html__( 'Update Page:', 'send-chat-tools' ) . $admin_url . "\n\n" .
				esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ) .
				'https://wordpress.org/plugins/send-chat-tools/' .
				'[/info]',
		];

		$options = [
			'headers' => 'X-ChatWorkToken: ' . Sct_Encryption::decrypt( $api_token ),
			'body'    => $message,
		];

		self::sending( $options, $id, 'chatwork' );
	}
}
