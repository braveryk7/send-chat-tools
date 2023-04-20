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
		return $this;
	}

	/**
	 * Abstract method to create comment data to be sent to chat tools.
	 *
	 * @param object $comment Comment data.
	 */
	public function generate_comment_content( object $comment, ): Sct_Discord {
		return $this;
	}
}
