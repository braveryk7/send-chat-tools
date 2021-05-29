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
	 * Plugins.
	 */
	public static function check_plugins(): array {
		global $wpdb;

		$get_plugin_states = get_option( '_site_transient_update_plugins' );
		$return            = [];
		if ( ! empty( $get_plugin_states->response ) ) {
			$update_plugins = $get_plugin_states->response;
			foreach ( $update_plugins as $key ) {
				$new_version          = get_file_data( WP_PLUGIN_DIR . '/' . $key->plugin, [ 'version' => 'Version' ] );
				$return[ $key->slug ] = [
					'name'            => $key->slug,
					'path'            => WP_PLUGIN_DIR . '/' . $key->plugin,
					'current_version' => $new_version['version'],
					'new_version'     => $key->new_version,
				];
			}
		}
		return $return;
	}
}
