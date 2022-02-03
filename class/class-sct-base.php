<?php
/**
 * Send Chat Tools base class.
 *
 * @author     Ken-chan
 * @package    WordPress
 * @subpackage Send Chat Tools
 * @since      1.3.0
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Send Chat Tools base class.
 */
class Sct_Base {
	protected const PREFIX      = 'sct';
	protected const PLUGIN_SLUG = 'send-chat-tools';
	protected const PLUGIN_NAME = 'Send Chat Tools';
	protected const PLUGIN_FILE = self::PLUGIN_SLUG . '.php';

	protected const TABLE_NAME = self::PREFIX;

	/**
	 * Return add prefix.
	 *
	 * @param string $value After prefix value.
	 */
	public static function add_prefix( string $value ): string {
		return self::PREFIX . '_' . $value;
	}

	/**
	 * Return plugin url.
	 * e.g. https://expamle.com/wp-content/plugins/send-chat-tools
	 *
	 * @param string $plugin_name Plugin name.
	 */
	protected function return_plugin_url( string $plugin_name ): string {
		return WP_PLUGIN_URL . '/' . $plugin_name;
	}

	/**
	 * Return plugin directory.
	 * e.g. /DocumentRoot/wp-content/plugins/send-chat-tools
	 *
	 * @param string $plugin_name Plugin name.
	 */
	protected function return_plugin_dir( string $plugin_name ): string {
		return WP_PLUGIN_DIR . '/' . $plugin_name;
	}

	/**
	 * Return plugin file path.
	 * e.g. /DocumentRoot/wp-content/plugins/send-chat-tools/send-chat-tools.php
	 */
	protected function return_plugin_path(): string {
		return $this->return_plugin_dir( self::PLUGIN_SLUG ) . '/' . self::PLUGIN_FILE;
	}

	/**
	 * Return option group.
	 * Use register_setting.
	 * e.g. send-chat-tools-settings
	 */
	protected function return_option_group(): string {
		return self::PLUGIN_SLUG . '-settings';
	}

	/**
	 * Return Database table name.
	 */
	protected function create_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_NAME;
	}

	/**
	 * Output browser console.
	 * WARNING: Use debag only!
	 *
	 * @param string|int|float|boolean|array|object $value Output data.
	 */
	protected function console( $value ): void {
		echo '<script>console.log(' . wp_json_encode( $value ) . ');</script>';
	}
}
