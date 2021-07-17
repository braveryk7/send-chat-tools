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
		$my_time   = gmdate( 'Y-m-d 18:00:00', strtotime( current_datetime()->format( 'Y-m-d H:i:s' ) ) );
		$cron_time = strtotime( -1 * (int) current_datetime()->format( 'O' ) / 100 . 'hour', strtotime( $my_time ) );

		add_action( 'sct_update_check', [ $this, 'controller' ] );
		if ( ! wp_next_scheduled( 'sct_update_check' ) ) {
			wp_schedule_event( $cron_time, 'daily', 'sct_update_check' );
			update_option( 'sct_cron_time', '18:00' );
		}
	}

	/**
	 * Call WordPress Core, Themes and Plugin check function.
	 */
	public function controller() {
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
	private function check_core() {
		$get_core_states = get_option( '_site_transient_update_core' );
		$return          = [];
		if ( ! empty( $get_core_states ) && 'upgrade' === $get_core_states->updates[0]->response ) {
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

		$ex_themes = [
			'Cocoon'    => 'external_theme_updates-cocoon-master',
			'SANGO'     => 'puc_external_updates_theme-sango-theme',
			'THE SONIC' => 'puc_external_updates_theme-tsnc-main-theme-updater',
		];

		if ( is_child_theme() ) {
			$theme_name      = wp_get_theme()->parent()->Name;
			$current_version = wp_get_theme()->parent()->Version;
		} else {
			$theme_name      = wp_get_theme()->Name;
			$current_version = wp_get_theme()->Version;
		}

		if ( array_key_exists( $theme_name, $ex_themes ) ) {
			$get_update = get_option( $ex_themes[ $theme_name ] );
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

		$ex_plugins = [
			'Rinker'                     => 'external_updates-yyi-rinker',
			'THE SONIC SEO Plugin'       => 'external_updates-tsnc-seo-plugin',
			'THE SONIC Gutenberg Blocks' => 'external_updates-tsnc-gutenberg-plugin',
			'THE SONIC COPIA'            => 'external_updates-tsnc-cmp-plugin',
			'SANGO Gutenberg'            => 'external_updates-sango-theme-gutenberg',
		];

		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$current_plugins = get_plugins();
		foreach ( $current_plugins as $key => $value ) {
			if ( array_key_exists( $value['Name'], $ex_plugins ) ) {
				$get_update = get_option( $ex_plugins[ $value['Name'] ] );
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
}
