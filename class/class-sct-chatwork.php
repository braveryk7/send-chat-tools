<?php
/**
 * Class for Chatwork processing.
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
 * Class for Chatwork processing.
 */
class Sct_Chatwork extends Sct_Generate_Content_Abstract {

	/**
	 * Instantiate and return itself.
	 */
	public static function get_instance(): Sct_Chatwork {
		return new self();
	}

	/**
	 * A method to generate a Chatwork header.
	 */
	public function generate_header(): Sct_Chatwork {
		$this->header = [
			'method'  => 'POST',
			'headers' => 'X-ChatWorkToken: ' . $this->get_sct_options()['chatwork']['api_token'],
			'body'    => $this->content,
		];

		return $this;
	}

	/**
	 * Abstract method to create comment data to be sent to chat tools.
	 *
	 * @param object $comment Comment data.
	 */
	public function generate_comment_content( object $comment, ): Sct_Chatwork {
		$this->comment = $comment;

		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$article_title  = get_the_title( $comment->comment_post_ID );
		$article_url    = get_permalink( $comment->comment_post_ID );
		$comment_status = $this->generate_comment_approved_message( 'chatwork', $comment );

		$this->content = [
			'body' =>
				'[info][title]' . $site_name . '(' . $site_url . ')' . $this->get_send_text( 'comment', 'title' ) . '[/title]' .
				$this->get_send_text( 'comment', 'article' ) . $article_title . ' - ' . $article_url . "\n" .
				$this->get_send_text( 'comment', 'author' ) . $comment->comment_author . '<' . $comment->comment_author_email . ">\n" .
				$this->get_send_text( 'comment', 'date' ) . $comment->comment_date . "\n" .
				$this->get_send_text( 'comment', 'content' ) . "\n" . $comment->comment_content . "\n\n" .
				$this->get_send_text( 'comment', 'url' ) . $article_url . '#comment-' . $comment->comment_ID . "\n" .
				'[hr]' .
				$this->get_send_text( 'comment', 'status' ) . $comment_status .
				$this->generate_context( 'chatwork' ) . '[/info]',
		];

		return $this;
	}

	/**
	 * Generate update notifications for Chatwork.
	 *
	 * @param array $update_content Update data.
	 */
	public function generate_update_content( array $update_content ): Sct_Chatwork {
		$plain_data = $this->generate_plain_update_message( $update_content );

		$core    = isset( $plain_data->core ) ? rtrim( $plain_data->core ) . '[hr]' : $plain_data->core;
		$themes  = isset( $plain_data->themes ) ? rtrim( $plain_data->themes ) . '[hr]' : $plain_data->themes;
		$plugins = isset( $plain_data->plugins ) ? rtrim( $plain_data->plugins ) . '[hr]' : $plain_data->plugins;

		$this->content = [
			'body' =>
				'[info][title]' . $plain_data->site_name . '( ' . $plain_data->site_url . ' )' . $plain_data->update_title . '[/title]' .
				$core . $themes . $plugins . $plain_data->update_text . "\n" . $plain_data->update_page . $plain_data->admin_url . "\n" .
				$this->generate_context( 'chatwork' ) . '[/info]',
		];
		return $this;
	}
}
