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
	 * Abstract method to create comment data to be sent to chat tools.
	 *
	 * @param object $comment Comment data.
	 */
	abstract public function generate_comment_content( object $comment, ): array;
}
