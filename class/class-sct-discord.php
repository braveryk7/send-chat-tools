<?php
/**
 * Class for Discord processing.
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
 * Class for Discord processing.
 */
class Sct_Discord extends Sct_Generate_Content_Abstract {

	/**
	 * Instantiate and return itself.
	 */
	public static function get_instance(): Sct_Discord {
		return new self();
	}

	/**
	 * A method to generate a Discord header.
	 */
	public function generate_header(): Sct_Discord {
		$this->header = [
			'method'  => 'POST',
			'headers' => [ 'Content-Type: application/json;charset=utf-8' ],
			'body'    => [ 'content' => $this->content ],
		];

		return $this;
	}

	/**
	 * Abstract method to create comment data to be sent to chat tools.
	 *
	 * @param object $comment Comment data.
	 */
	public function generate_comment_content( object $comment, ): Sct_Discord {
		$this->comment = $comment;

		$site_name     = get_bloginfo( 'name' );
		$site_url      = get_bloginfo( 'url' );
		$article_title = get_the_title( $comment->comment_post_ID );
		$article_url   = get_permalink( $comment->comment_post_ID );

		$approved_url   = admin_url() . 'comment.php?action=approve&c=' . $comment->comment_ID;
		$unapproved     = $this->get_send_text( 'comment', 'unapproved' );
		$click_message  = $this->get_send_text( 'comment', 'click' );
		$comment_status = $unapproved . ' >> ' . $click_message . '( ' . $approved_url . ' )';

		$message = [
			0 => esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ),
			1 => esc_html__( 'WordPress Plugin Directory', 'send-chat-tools' ),
			2 => esc_html__( 'Send Chat Tools Official Page', 'send-chat-tools' ),
		];

		$wordpress_directory = $this->get_official_directory();
		$official_web_site   = 'https://www.braveryk7.com/portfolio/send-chat-tools/';

		$this->content =
			$site_name . '( <' . $site_url . '> )' . $this->get_send_text( 'comment', 'title' ) . "\n\n" .
			$this->get_send_text( 'comment', 'article' ) . $article_title . ' - <' . $article_url . '>' . "\n" .
			$this->get_send_text( 'comment', 'author' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
			$this->get_send_text( 'comment', 'date' ) . $comment->comment_date . "\n" .
			$this->get_send_text( 'comment', 'content' ) . "\n" . $comment->comment_content . "\n\n" .
			$this->get_send_text( 'comment', 'url' ) . '<' . $article_url . '#comment-' . $comment->comment_ID . '>' . "\n\n" .
			$this->get_send_text( 'comment', 'status' ) . $comment_status . "\n\n" .
			$this->$message[0] . "\n" . $message[1] . ' <' . $wordpress_directory . '>' . "\n" . $message[2] . ' <' . $official_web_site . '>';

		return $this;
	}

	/**
	 * Generate update notifications for Discord.
	 *
	 * @param array $update_content Update data.
	 */
	public function generate_update_content( array $update_content ): Sct_Discord {
		$plain_data = $this->generate_plain_update_message( $update_content );

		$message = [
			0 => esc_html__( 'This message was sent by Send Chat Tools: ', 'send-chat-tools' ),
			1 => esc_html__( 'WordPress Plugin Directory', 'send-chat-tools' ),
			2 => esc_html__( 'Send Chat Tools Official Page', 'send-chat-tools' ),
		];

		$wordpress_directory = $this->get_official_directory();
		$official_web_site   = 'https://www.braveryk7.com/portfolio/send-chat-tools/';

		$this->content =
			$plain_data->site_name . '( <' . $plain_data->site_url . '> )' . $plain_data->update_title . "\n\n" .
			$plain_data->core . $plain_data->themes . $plain_data->plugins .
			$plain_data->update_text . "\n" . $plain_data->update_page . '<' . $plain_data->admin_url . '>' . "\n\n" .
			$message[0] . "\n" . $message[1] . ' <' . $wordpress_directory . '>' . "\n" . $message[2] . ' <' . $official_web_site . '>';

		return $this;
	}
}
