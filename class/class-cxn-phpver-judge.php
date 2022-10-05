<?php
/**
 * Judgment PHP Version.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 0.0.1
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Return true or false.
 */
class Cxn_Phpver_Judge {
	/**
	 * Judgment PHP version.
	 *
	 * @param string $version_received php version.
	 */
	public function judgment( string $version_received ): bool {
		return version_compare( PHP_VERSION, $version_received, '>=' ) ? true : false;
	}

	/**
	 * Deactivate plugin & show deactivate massege.
	 *
	 * @param string $path Plugin path.
	 */
	public function deactivate( string $path ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( plugin_basename( $path ) ) ) {
			if ( is_admin() ) {
				$this->deactivate_message();
			}
			deactivate_plugins( plugin_basename( $path ) );
		} else {
			echo '<p>' . esc_html_e( 'Send Chat Tools requires at least PHP 7.3.0 or later.', 'send-chat-tools' ) . esc_html_e( 'Please upgrade PHP.', 'send-chat-tools' ) . '</p>';
			exit;
		}
	}

	/**
	 * Show deactivate error message.
	 */
	public function deactivate_message() {
		$project = 'Send Chat Tools';
		$version = '7.3.0';

		?>
		<div class="error">
			<p>
				<?php
					echo sprintf(
						/* translators: 1: Plugin name */
						esc_html__( '[Plugin error] %s has been stopped because the PHP version is old.', 'send-chat-tools' ),
						esc_html( $project ),
					);
				?>
			</p>
			<p>
				<?php
					echo sprintf(
						/* translators: 1: Plugin name 2: PHP version */
						esc_html__( '%1$s requires at least PHP %2$s or later.', 'send-chat-tools' ),
						esc_html( $project ),
						esc_html( $version ),
					);
				?>
				<?php esc_html_e( 'Please upgrade PHP.', 'send-chat-tools' ); ?>
			</p>
			<p>
				<?php esc_html_e( 'Current PHP version:', 'send-chat-tools' ); ?>
				<?php echo PHP_VERSION; ?>
			</p>
		</div>
		<?php
	}
}
