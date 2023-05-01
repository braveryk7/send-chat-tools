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
	 * WordPress hook.
	 */
	public function __construct() {
		add_action( $this->add_prefix( 'rinker_exists_items_check' ), [ $this, 'controller' ] );
		add_action( 'admin_init', [ $this, 'check_cron_time' ] );
	}

	/**
	 * Controller method called by WP-Cron that executes the check_rinker_discontinued_items method and calls each chat class.
	 */
	public function controller() {
		$discontinued_items = $this->check_rinker_discontinued_items();

		if ( ! empty( $discontinued_items ) ) {
			$sct_options = $this->get_sct_options();

			foreach ( $this->get_chat_tools() as $tool ) {
				$api_column = 'chatwork' === $tool ? 'api_token' : 'webhook_url';

				if ( $sct_options[ $tool ]['use'] && $sct_options[ $tool ]['rinker_notify'] ) {
					$this->call_chat_tool_class( $tool, 'generate_rinker_message', 'rinker_notify', $discontinued_items );
				} elseif ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ][ $api_column ] ) ) {
					$this->logger( 1001, $tool, '1' );
				} elseif ( 'chatwork' === $tool && ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ]['room_id'] ) ) ) {
					$this->logger( 1002, 'chatwork', '1' );
				};
			}
		}
	}
	/**
	 * Check Amazon and Rakuten for discontinued items for Rinker.
	 */
	public function check_rinker_discontinued_items(): array {
		$discontinued_items = [];

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

				$discontinued_items[ $item->ID ] = [
					'item_id'   => $item->ID,
					'item_shop' => $shop,
					'item_name' => $item->post_title,
					'item_url'  => admin_url() . 'post.php?post=' . $item->ID . '&action=edit',
				];
			}
			wp_reset_postdata();
		}

		return $discontinued_items;
	}

	/**
	 * WP-cron check.
	 */
	public function check_cron_time(): void {
		$get_next_schedule     = wp_get_scheduled_event( 'sct_rinker_exists_items_check' );
		$sct_options           = $this->get_sct_options();
		$to_datetime_string    = gmdate( 'Y-m-d ' . $sct_options['check_rinker_exists_items_cron'], strtotime( current_datetime()->format( 'Y-m-d H:i:s' ) ) );
		$sct_options_timestamp = strtotime( -1 * (int) current_datetime()->format( 'O' ) / 100 . 'hour', strtotime( $to_datetime_string ) );

		if ( ! $get_next_schedule ) {
			wp_schedule_event( $sct_options_timestamp, 'daily', 'sct_rinker_exists_items_check' );
		} else {
			if ( isset( $sct_options['cron_time'] ) ) {
				if ( $get_next_schedule->timestamp !== $sct_options_timestamp ) {
					$sct_options_timestamp <= time() ? $sct_options_timestamp = strtotime( '+1 day', $sct_options_timestamp ) : $sct_options_timestamp;
					wp_clear_scheduled_hook( 'sct_rinker_exists_items_check' );
					wp_schedule_event( $sct_options_timestamp, 'daily', 'sct_rinker_exists_items_check' );
				}
			} else {
				$sct_options['check_rinker_exists_items_cron'] = '19:00';
				$this->set_sct_options( $sct_options );
			}
		}
	}
}
