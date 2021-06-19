<?php
/**
 * Check Update WordPress core, theme, and plugin.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 0.1.2
 */

declare( strict_types = 1 );

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
		global $wpdb;
		$my_time   = gmdate( 'Y-m-d 03:00:00', strtotime( current_datetime()->format( 'Y-m-d H:i:s' ) ) );
		$cron_time = strtotime( -1 * (int) current_datetime()->format( 'O' ) / 100 . 'hour', strtotime( $my_time ) );

		add_action( 'sct_update_check', [ $this, 'controller' ] );
		if ( ! wp_next_scheduled( 'sct_update_check' ) ) {
			wp_schedule_event( $cron_time, 'daily', 'sct_update_check' );
			update_option( 'sct_cron_time', '03:00' );
		}
	}

	/**
	 * Call WordPress Core, Themes and Plugin check function.
	 */
	public function controller() {
		$check_all = [];
		$core      = $this->check_core();
		$themes    = $this->check_themes();
		$plugins   = $this->check_plugins();
		if ( isset( $core ) ) {
			$check_all = array_merge( $check_all, $core );
		}
		if ( isset( $themes ) ) {
			$check_all = array_merge( $check_all, $themes );
		}
		if ( isset( $plugins ) ) {
			$check_all = array_merge( $check_all, $plugins );
		}
		$this->check_tools( $check_all );
	}

	/**
	 * WordPress Core.
	 */
	private function check_core() {
		$get_core_states = get_option( '_site_transient_update_core' );
		$return          = [];
		if ( ! empty( $get_core_states ) ) {
			$update_core    = $get_core_states->updates[0];
			$return['core'] = [
				'name'            => 'WordPress Core',
				'attribute'       => 'core',
				'current_version' => get_bloginfo( 'version' ),
				'new_version'     => $update_core->version,
			];
		}
		return $return;
	}

	/**
	 * Themes.
	 */
	private function check_themes() {
		$get_theme_states = get_option( '_site_transient_update_themes' );
		$return           = [];
		if ( ! empty( $get_theme_states->response ) ) {
			$update_themes = $get_theme_states->response;
			foreach ( $update_themes as $key => $value ) {
				$theme_date                  = wp_get_theme( $key );
				$return[ $theme_date->name ] = [
					'name'            => $theme_date->name,
					'attribute'       => 'theme',
					'path'            => $theme_date->theme_root . '/' . $key,
					'current_version' => $theme_date->version,
					'new_version'     => $value['new_version'],
				];
			}
		}
		return $return;
	}

	/**
	 * Plugins.
	 */
	private function check_plugins() {
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
		return $return;
	}

	/**
	 * Check tools use status and call.
	 *
	 * @param array $return Update info.
	 */
	private function check_tools( array $return ) {
		if ( '1' === get_option( 'sct_use_slack' ) && '1' === get_option( 'sct_send_slack_update' ) ) {
			$slack = Sct_Slack::create_update_contents( $return );
		}

		if ( '1' === get_option( 'sct_use_discord' ) && '1' === get_option( 'sct_send_discord_update' ) ) {
			$discord = Sct_Discord::create_update_contents( $return );
		}

		if ( '1' === get_option( 'sct_use_chatwork' ) && '1' === get_option( 'sct_send_chatwork_update' ) ) {
			$chatwork = Sct_Chatwork::create_update_contents( $return );
		}
	}
}
