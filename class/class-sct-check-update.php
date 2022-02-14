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
class Sct_Check_Update extends Sct_Base {

	/**
	 * WordPress hook.
	 * Add WP-Cron.
	 */
	public function __construct() {
		add_action( 'wp', [ $this, 'check_cron_time' ] );
		add_action( $this->add_prefix( 'update_check' ), [ $this, 'controller' ] );
	}

	/**
	 * Call WordPress Core, Themes and Plugin check function.
	 */
	public function controller(): void {
		update_option( 'sct_cron_test', 1234 );
		$check_data = [];
		$core       = $this->check_core();
		$themes     = $this->check_themes();
		$plugins    = $this->check_plugins();

		if ( isset( $core ) ) {
			$check_data = array_merge( $check_data, $core );
		}
		if ( isset( $themes ) ) {
			$check_data = array_merge( $check_data, $themes );
		}
		if ( isset( $plugins ) ) {
			$check_data = array_merge( $check_data, $plugins );
		}

		if ( ! empty( $check_data ) ) {
			$next = new Sct_Create_Content();
			$next->controller( 0, 'update', $check_data );
		}
	}

	/**
	 * WordPress Core.
	 */
	private function check_core(): array {
		$get_core_status = get_option( '_site_transient_update_core' );
		$return          = [];
		if ( ! empty( $get_core_status ) && 'upgrade' === $get_core_status->updates[0]->response ) {
			$update_core    = $get_core_status->updates[0];
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
	private function check_themes(): array {
		$get_theme_status = get_option( '_site_transient_update_themes' );
		$return           = [];
		if ( ! empty( $get_theme_status->response ) ) {
			$update_themes = $get_theme_status->response;
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

		if ( is_child_theme() ) {
			$theme_name      = wp_get_theme()->parent()->Name;
			$current_version = wp_get_theme()->parent()->Version;
		} else {
			$theme_name      = wp_get_theme()->Name;
			$current_version = wp_get_theme()->Version;
		}

		if ( array_key_exists( $theme_name, self::THEME_OPTION_NAME ) ) {
			$get_update = get_option( self::THEME_OPTION_NAME[ $theme_name ] );
			if ( version_compare( $current_version, $get_update->update->version, '<' ) ) {
				$return[ $theme_name ] = [
					'name'            => $theme_name,
					'attribute'       => 'theme',
					'current_version' => $current_version,
					'new_version'     => $get_update->update->version,
				];
			}
		}

		return $return;
	}

	/**
	 * Plugins.
	 */
	private function check_plugins(): array {
		$get_plugin_status = get_option( '_site_transient_update_plugins' );
		$return            = [];
		if ( ! empty( $get_plugin_status->response ) ) {
			$update_plugins = $get_plugin_status->response;
			foreach ( $update_plugins as $key ) {
				$path                 = $this->return_plugin_dir( $key->plugin );
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

		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$current_plugins = get_plugins();
		foreach ( $current_plugins as $key => $value ) {
			if ( array_key_exists( $value['Name'], self::PLUGIN_OPTION_NAME ) ) {
				$get_update = get_option( self::PLUGIN_OPTION_NAME[ $value['Name'] ] );
				if ( ! empty( $get_update ) && version_compare( $value['Version'], $get_update->update->version, '<' ) ) {
					$return[ $value['Name'] ] = [
						'name'            => $value['Name'],
						'attribute'       => 'plugin',
						'current_version' => $value['Version'],
						'new_version'     => $get_update->update->version,
					];
				}
			}
		}

		return $return;
	}

	/**
	 * WP-cron check.
	 */
	public function check_cron_time(): void {
		$get_next_schedule     = wp_get_scheduled_event( 'sct_update_check' );
		$sct_options           = $this->get_sct_options();
		$to_datetime_string    = gmdate( 'Y-m-d ' . $sct_options['cron_time'], strtotime( current_datetime()->format( 'Y-m-d H:i:s' ) ) );
		$sct_options_timestamp = strtotime( -1 * (int) current_datetime()->format( 'O' ) / 100 . 'hour', strtotime( $to_datetime_string ) );

		if ( ! $get_next_schedule ) {
			wp_schedule_event( $sct_options_timestamp, 'daily', 'sct_update_check' );
		} else {
			if ( isset( $sct_options['cron_time'] ) ) {
				if ( $get_next_schedule->timestamp !== $sct_options_timestamp ) {
					if ( $sct_options_timestamp <= time() ) {
						$sct_options_timestamp = strtotime( '+1 day', $sct_options_timestamp );
					}
					wp_clear_scheduled_hook( 'sct_update_check' );
					wp_schedule_event( $sct_options_timestamp, 'daily', 'sct_update_check' );
				}
			} else {
				$sct_options['cron_time'] = '18:00';
				$this->set_sct_options( $sct_options );
			}
		}
	}
}
