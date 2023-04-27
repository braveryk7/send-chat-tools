<?php
/**
 * A class that checks Rinker's out-of-stock information and calls the class for transmission when the condition is met.
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
 * A class that checks Rinker's out-of-stock information and calls the class for transmission when the condition is met.
 */
class Sct_Check_Rinker extends Sct_Base {
	/**
	 * Controller method called by WP-Cron that executes the check_rinker_exists_items method and calls each chat class.
	 */
	public function controller() {
		$exists_items = $this->check_rinker_exists_items();

		if ( ! empty( $exists_items ) ) {
			$sct_options = $this->get_sct_options();

			foreach ( $this->get_chat_tools() as $tool ) {
				$api_column = 'chatwork' === $tool ? 'api_token' : 'webhook_url';

				if ( $sct_options[ $tool ]['use'] && $sct_options[ $tool ]['rinker_notify'] ) {
					$this->call_chat_tool_class( $tool, 'generate_rinker_content', 'rinker', $exists_items );
				} elseif ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ][ $api_column ] ) ) {
					$this->logger( 1001, $tool, '1' );
				} elseif ( 'chatwork' === $tool && ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ]['room_id'] ) ) ) {
					$this->logger( 1002, 'chatwork', '1' );
				};
			}
		}
	}
	/**
	 * Check Amazon and Rakuten for discontinued products for Rinker.
	 */
	public function check_rinker_exists_items(): array {
		$exists_items = [];

		foreach ( [ 'amazon', 'rakuten' ] as $shop ) {
			$query = new WP_Query(
				[
					'post_type'      => 'yyi_rinker',
					'meta_key'       => 'yyi_rinker_is_' . $shop . '_no_exist', //phpcs:ignore
					'meta_value_num' => 1,
					'posts_per_page' => -1,
				],
			);

			while ( $query->have_posts() ) {
				$query->the_post();
				$item = get_post();

				$exists_items[ $item->ID ] = [
					'item_id'   => $item->ID,
					'item_shop' => $shop,
					'item_name' => $item->post_title,
					'item_url'  => admin_url() . 'post.php?post=' . $item->ID . '&action=edit',
				];
			}
			wp_reset_postdata();
		}

		return $exists_items;
	}
}
