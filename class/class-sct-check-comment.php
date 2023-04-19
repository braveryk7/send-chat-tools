<?php
/**
 * If comment data exists, a class that calls the class that performs various processes.
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
 * If comment data exists, a class that calls the class that performs various processes.
 */
class Sct_Check_Comment extends Sct_Base {

	/**
	 * Controller called by add_action.
	 *
	 * @param int $comment_id     Comment ID.
	 */
	public function controller( int $comment_id ) {
		$sct_options = $this->get_sct_options();
		$tools       = [ 'slack', 'discord', 'chatwork' ];
		$comment     = get_comment( $comment_id );
	}
}
