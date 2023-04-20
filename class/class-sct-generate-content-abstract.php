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
	 * Constructor.
	 *
	 * @param object $comment Comment data.
	 */
	protected function __construct( object $comment, ) {
		$this->comment = $comment;
	}

	/**
	 * Abstract method to generate a chat tool header.
	 */
	abstract public function generate_header(): Sct_Slack;

	/**
	 * Abstract method to create comment data to be sent to chat tools.
	 *
	 * @param object $comment Comment data.
	 */
	abstract public function generate_comment_content( object $comment, ): Sct_Slack;

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
}
