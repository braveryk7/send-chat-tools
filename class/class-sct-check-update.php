<?php
/**
 * Check Update WordPress core, theme, and plugin.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 0.1.2
 */

declare( strict_type = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Check Update WordPress core, theme, and plugin.
 */
class Sct_Check_Update {

	/**
	 * WordPress hook.
	 * Add WP-Cron.
	 */
	public function __construct() {
		add_action( 'sct_plugin_check', [ $this, 'check_plugins' ] );
		if ( ! wp_next_scheduled( 'sct_plugin_check' ) ) {
			$my_time   = gmdate( 'Y-m-d 03:00:00', strtotime( current_datetime()->format( 'Y-m-d H:i:s' ) ) );
			$cron_time = strtotime( -1 * (int) current_datetime()->format( 'O' ) / 100 . 'hour', strtotime( $my_time ) );

			wp_schedule_event( $cron_time, 'daily', 'sct_plugin_check' );
		}
	}

	/**
	 * Plugins.
	 */
	public function check_plugins() {
		global $wpdb;

		$get_plugin_states = get_option( '_site_transient_update_plugins' );
		$return            = [];
		if ( ! empty( $get_plugin_states->response ) ) {
			$update_plugins = $get_plugin_states->response;
			foreach ( $update_plugins as $key ) {
				$path                 = WP_PLUGIN_DIR . '/' . $key->plugin;
				$plugin_date          = get_file_data(
					$path,
					[
						'name'    => 'Plugin Name',
						'version' => 'Version',
					]
				);
				$return[ $key->slug ] = [
					'name'            => $plugin_date['name'],
					'attribute'       => 'plugin',
					'path'            => $path,
					'current_version' => $plugin_date['version'],
					'new_version'     => $key->new_version,
				];
			}
		}
		$this->check_tools( $return, $id );
	}

	/**
	 * Check tools use status and call.
	 *
	 * @param array $return Update info.
	 */
	private function check_tools( array $return ) {
		if ( '1' === get_option( 'sct_use_slack' ) ) {
			Sct_Slack::create_update_contents( $return, $id );
		}
		if ( '1' === get_option( 'sct_use_chatwork' ) ) {
			add_action( 'comment_post', 'Sct_Chatwork::send_chatwork' );
		}
		if ( '1' === get_option( 'sct_use_discord' ) ) {
			add_action( 'comment_post', 'Sct_Discord::send_discord' );
		}
	}
}
