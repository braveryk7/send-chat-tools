<?php
/**
 * Send Chat logic.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 1.0.0
 */

declare( strict_types = 1 );

/**
 * Send Chat logic.
 */
trait Sct_Sending {
	/**
	 * Send Slack.
	 *
	 * @param array  $options API options.
	 * @param string $id ID(Comment/Update).
	 * @param string $tools Use chat tools prefix.
	 */
	private static function sending( array $options, string $id, string $tools ) {
		require_once dirname( __FILE__ ) . '/class-sct-encryption.php';

		if ( 'slack' === $tools ) {
			$url = Sct_Encryption::decrypt( get_option( 'sct_slack_webhook_url' ) );
			$log = 'sct_slack_log';
		} elseif ( 'discord' === $tools ) {
			$url = Sct_Encryption::decrypt( get_option( 'sct_discord_webhook_url' ) );
			$log = 'sct_discord_log';
		} elseif ( 'chatwork' === $tools ) {
			$url = 'https://api.chatwork.com/v2/rooms/' . Sct_Encryption::decrypt( get_option( 'sct_chatwork_room_id' ) ) . '/messages';
			$log = 'sct_chatwork_log';
		}
		$result = wp_remote_post( $url, $options );
		update_option( $log, $result );

		if ( ! isset( $result->errors ) ) {
			$status_code = $result['response']['code'];
		} else {
			$status_code = 1000;
		}
		if ( 200 !== $status_code && 204 !== $status_code ) {
			require_once dirname( __FILE__ ) . '/class-sct-error-mail.php';
			if ( 'update' === $id ) {
				$send_mail = new Sct_Error_mail( $status_code, $id );
				$send_mail->update_contents( $options );
			} else {
				$send_mail = new Sct_Error_Mail( $status_code, $id );
				$send_mail->make_contents();
			}
		}

		$logger = new Sct_Logger();
		$logger->create_log( $status_code, $tools, $id );
	}
}
