<?php
/**
 * Abstract class for generating content data and processing it in each chat tool
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
 * Abstract class
 */
abstract class Sct_Generate_Content_Abstract extends Sct_Base {
	/**
	 * Property with WordPress comment data.
	 *
	 * @var object Comment data.
	 */
	protected $comment;

	/**
	 * Property to store generated header.
	 *
	 * @var array Generated header.
	 */
	protected $header;

	/**
	 * Property to store generated comment content.
	 *
	 * @var array Generated comment content.
	 */
	protected $comment_content;

	/**
	 * Abstract method to get an instance.
	 */
	abstract public static function get_instance(): Sct_Slack | Sct_Discord | Sct_Chatwork;

	/**
	 * Abstract method to generate a chat tool header.
	 */
	abstract public function generate_header(): Sct_Slack | Sct_Discord | Sct_Chatwork;

	/**
	 * Abstract method to create comment data to be sent to chat tools.
	 *
	 * @param object $comment Comment data.
	 */
	abstract public function generate_comment_content( object $comment, ): Sct_Slack | Sct_Discord | Sct_Chatwork;

	/**
	 * Get comment notify content.
	 *
	 * @param string $type  Message type.
	 * @param string $param Item parameter.
	 */
	protected function get_send_text( string $type, string $param ): string {
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
	 * Methods to send to chat tools.
	 *
	 * @param string $id      ID(Comment/Update).
	 * @param string $tool    Use chat tools prefix.
	 */
	public function send_tools( string $id, string $tool ): bool {

		$sct_options = $this->get_sct_options();

		switch ( $tool ) {
			case 'slack':
			case 'discord':
				$url   = $sct_options[ $tool ]['webhook_url'];
				$regex = $this->api_regex( $tool, $url );
				break;
			case 'chatwork':
				$url   = 'https://api.chatwork.com/v2/rooms/' . $sct_options[ $tool ]['room_id'] . '/messages';
				$regex = $this->api_regex( 'chatworkid', $sct_options[ $tool ]['room_id'] );
				break;
		}

		if ( $regex ) {
			$result = wp_remote_post( $url, $this->header );

			if ( ! is_null( $this->comment ) ) {
				$logs = [
					$this->comment->comment_date => [
						'id'      => $this->comment->comment_ID,
						'author'  => $this->comment->comment_author,
						'email'   => $this->comment->comment_author_email,
						'url'     => $this->comment->comment_author_url,
						'comment' => $this->comment->comment_content,
						'status'  => $result['response']['code'],
					],
				];

				if ( '3' <= count( $sct_options[ $tool ]['log'] ) ) {
					array_pop( $sct_options[ $tool ]['log'] );
				}
				$sct_options[ $tool ]['log'] = $logs + $sct_options[ $tool ]['log'];
				$this->set_sct_options( $sct_options );
			}
		}

		$status_code = match ( true ) {
			! isset( $result->errors ) => $result['response']['code'],
			! $regex                   => 1003,
			default                    => 1000,
		};

		if ( 200 !== $status_code && 204 !== $status_code ) {
			require_once dirname( __FILE__ ) . '/class-sct-error-mail.php';
			if ( 'update' === $id ) {
				$send_mail = new Sct_Error_Mail( $status_code, $id, $tool );
				$send_mail->send_mail( ...$send_mail->update_contents( $options['plain_data'] ) );
			} else {
				$send_mail = new Sct_Error_Mail( $status_code, $id, $tool );
				$send_mail->send_mail( ...$send_mail->generate_contents() );
			}
		}

		return $this->logger( $status_code, $tool, $id );
	}
}
