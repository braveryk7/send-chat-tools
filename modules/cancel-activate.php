<?php
/**
 * Cancel activate.
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
 * Return error message.
 */
function cancel_activate() {
	?>
<div class="error">
	<p><?php esc_html_e( '[Plugin error] Send Chat Tools has been stopped because the PHP version is old.', 'send-chat-tools' ); ?></p>
	<p>
		<?php esc_html_e( 'Send Chat Tools requires at least PHP 7.3.0 or later.', 'send-chat-tools' ); ?>
		<?php esc_html_e( 'Please upgrade PHP.', 'send-chat-tools' ); ?>
	</p>
	<p>
		<?php esc_html_e( 'Current PHP version:', 'send-chat-tools' ); ?>
		<?php echo PHP_VERSION; ?>
	</p>
</div>
	<?php
}
