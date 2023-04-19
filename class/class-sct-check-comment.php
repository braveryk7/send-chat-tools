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

	/**
	 * Check send status.
	 *
	 * @param string $tool_name       Tool name.
	 * @param array  $tools           Tool options -> sct_options[TOOLNAME].
	 * @param string $comment_user_id Comment user id.
	 */
	private function get_send_status( string $tool_name, array $tools, string $comment_user_id ): bool {
		$status = [
			'use'    => false,
			'api'    => false,
			'author' => false,
		];

		$status['use'] = $tools['use'] ? true : false;

		$status['author'] = ! $tools['send_author'] || $tools['send_author'] && '0' === $comment_user_id ? true : false;

		switch ( $tool_name ) {
			case 'slack':
			case 'discord':
				$api           = $tools['webhook_url'];
				$status['api'] = ! empty( $api ) ? true : false;
				break;
			case 'chatwork':
				$api           = [
					'api_token' => $tools['api_token'],
					'room_id'   => $tools['room_id'],
				];
				$status['api'] = ! empty( $api['api_token'] ) && ! empty( $api['room_id'] ) ? true : false;
				break;
			default:
				$status['api'] = false;
		}

		return in_array( false, $status, true ) ? false : true;
	}
}
