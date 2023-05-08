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
	 * Property that holds the name of the updates cron event.
	 *
	 * @var string $cron_event_name
	 */
	private $cron_event_name;

	/**
	 * WordPress hook.
	 * Add WP-Cron.
	 */
	public function __construct() {
		$this->cron_event_name = $this->add_prefix( 'update_check' );
		add_action( $this->add_prefix( 'update_check' ), [ $this, 'controller' ] );
		add_action( 'admin_init', [ $this, 'check_cron_time' ] );
	}

	/**
	 * Call WordPress Core, Themes and Plugin check function.
	 */
	public function controller(): void {
		$updates = [];
		$core    = $this->check_core();
		$themes  = $this->check_themes();
		$plugins = $this->check_plugins();

		foreach ( [ $core, $themes, $plugins ] as $value ) {
			$updates = array_merge( $updates, $value ?? [] );
		}

		if ( ! empty( $updates ) ) {
			$sct_options = $this->get_sct_options();

			foreach ( $this->get_chat_tools() as $tool ) {
				$api_column = 'chatwork' === $tool ? 'api_token' : 'webhook_url';

				if ( $sct_options[ $tool ]['use'] && $sct_options[ $tool ]['update_notify'] ) {
					$this->call_chat_tool_class( $tool, 'generate_update_content', 'update', $updates );
				} elseif ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ][ $api_column ] ) ) {
					$this->logger( 1001, $tool, '1' );
				} elseif ( 'chatwork' === $tool && ( $sct_options[ $tool ]['use'] && empty( $sct_options[ $tool ]['room_id'] ) ) ) {
					$this->logger( 1002, 'chatwork', '1' );
				};
			}
		}
	}

	/**
	 * WordPress Core.
	 */
	private function check_core(): ?array {
		$get_core_status = get_option( '_site_transient_update_core' );
		$core_data       = null;

		if ( ! empty( $get_core_status ) && 'upgrade' === $get_core_status->updates[0]->response ) {
			$update_information = $get_core_status->updates[0];
			$core_data['core']  = [
				'name'            => 'WordPress Core',
				'attribute'       => 'core',
				'current_version' => get_bloginfo( 'version' ),
				'new_version'     => $update_information->version,
			];
		}

		return $core_data;
	}

	/**
	 * Themes.
	 */
	private function check_themes(): ?array {
		$current_theme   = is_child_theme() ? wp_get_theme()->parent()->name : wp_get_theme()->Name;
		$current_version = is_child_theme() ? wp_get_theme()->parent()->Version : wp_get_theme()->Version;
		$theme_data      = null;

		if ( array_key_exists( $current_theme, self::THEME_OPTION_NAME ) ) {
			$update_themes = get_option( self::THEME_OPTION_NAME[ $current_theme ] );
			if ( version_compare( $current_version, $update_themes->update->version, '<' ) ) {
				$theme_data[ $current_theme ] = [
					'name'            => $current_theme,
					'attribute'       => 'theme',
					'current_version' => $current_version,
					'new_version'     => $update_themes->update->version,
				];
			}
		}

		$get_theme_status = get_option( '_site_transient_update_themes' );

		if ( ! empty( $get_theme_status->response ) ) {
			foreach ( $get_theme_status->response as $key => $value ) {
				$theme_date                      = wp_get_theme( $key );
				$theme_data[ $theme_date->name ] = [
					'name'            => $theme_date->name,
					'attribute'       => 'theme',
					'path'            => $theme_date->theme_root . '/' . $key,
					'current_version' => $theme_date->version,
					'new_version'     => $value['new_version'],
				];
			}
		}

		return $theme_data;
	}

	/**
	 * Plugins.
	 */
	private function check_plugins(): ?array {
		$update_plugins = get_option( '_site_transient_update_plugins' );
		$plugin_data    = null;

		if ( ! empty( $update_plugins->response ) ) {
			foreach ( $update_plugins->response as $key ) {
				$path                      = $this->get_plugin_dir( $key->plugin );
				$plugin_date               = get_file_data(
					$path,
					[
						'name'    => 'Plugin Name',
						'version' => 'Version',
					]
				);
				$plugin_data[ $key->slug ] = [
					'name'            => $plugin_date['name'],
					'attribute'       => 'plugin',
					'path'            => $path,
					'current_version' => $plugin_date['version'],
					'new_version'     => $key->new_version,
				];
			}
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		foreach ( get_plugins() as $plugin ) {
			if ( array_key_exists( $plugin['Name'], self::PLUGIN_OPTION_NAME ) ) {
				$get_update = get_option( self::PLUGIN_OPTION_NAME[ $plugin['Name'] ] );
				if ( ! empty( $get_update ) && version_compare( $plugin['Version'], $get_update->update->version, '<' ) ) {
					$plugin_data[ $plugin['Name'] ] = [
						'name'            => $plugin['Name'],
						'attribute'       => 'plugin',
						'current_version' => $plugin['Version'],
						'new_version'     => $get_update->update->version,
					];
				}
			}
		}

		ksort( $plugin_data );

		return $plugin_data;
	}

	/**
	 * Check the execution time of the WP-Cron event and set it if not already set.
	 * If the time is different from the event already registered, set a new time.
	 */
	public function check_cron_time(): void {
		$next_schedule      = wp_get_scheduled_event( $this->cron_event_name );
		$sct_options        = $this->get_sct_options();
		$datetime_string    = gmdate( 'Y-m-d ' . $sct_options['cron_time'], strtotime( current_datetime()->format( 'Y-m-d H:i:s' ) ) );
		$datetime_timestamp = strtotime( -1 * (int) current_datetime()->format( 'O' ) / 100 . 'hour', strtotime( $datetime_string ) );

		if ( ! $next_schedule ) {
			wp_schedule_event( $datetime_timestamp, 'daily', $this->cron_event_name );
		} else {
			if ( isset( $sct_options['cron_time'] ) ) {
				if ( $next_schedule->timestamp !== $datetime_timestamp ) {
					$datetime_timestamp <= time() ? $datetime_timestamp = strtotime( '+1 day', $datetime_timestamp ) : $datetime_timestamp;
					wp_clear_scheduled_hook( $this->cron_event_name );
					wp_schedule_event( $datetime_timestamp, 'daily', $this->cron_event_name );
				}
			} else {
				$sct_options['cron_time'] = '18:00';
				$this->set_sct_options( $sct_options );
			}
		}
	}
}
