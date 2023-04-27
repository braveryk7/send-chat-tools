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
	 * Check Amazon and Rakuten for discontinued products for Rinker.
	 */
	public function check_rinker_exists_items(): array {
		$exists_items = [
			'amazon'  => [],
			'rakuten' => [],
		];

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
				$exists_items[ $shop ][ get_the_ID() ] = get_post();
			}
			wp_reset_postdata();
		}

		return $exists_items;
	}
}
