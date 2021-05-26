<?php
/**
 * Admin settings page.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 0.0.1
 */

declare( strict_type = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Return admin settings page.
 */
class Sct_Settings_Page {
	/**
	 * WordPress hook.
	 * Add settings page link in admin page.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
	}

	/**
	 * Add Setting menu.
	 */
	public function add_menu() {
		add_options_page(
			__( 'Send Chat Tools', 'send-chat-tools' ),
			__( 'Send Chat Tools', 'send-chat-tools' ),
			'administrator',
			'send-chat-tools-settings',
			[ $this, 'settings_page' ],
		);
	}

	/**
	 * Add configuration link to plugin page.
	 *
	 * @param array|string $links plugin page setting links.
	 */
	public static function add_settings_links( array $links ): array {
		$add_link = '<a href="options-general.php?page=send-chat-tools-settings">' . __( 'Settings', 'send-chat-tools' ) . '</a>';
		array_unshift( $links, $add_link );
		return $links;
	}

	/**
	 * Settings page.
	 */
	public function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You have no sufficient permissions to access this page.', 'send-chat-tools' ) );
		}
		require_once dirname( __FILE__ ) . '/class-sct-encryption.php';

		$hidden_field_name = 'hiddenStatus';

		if ( isset( $_POST[ $hidden_field_name ] ) && 'Y' === $_POST[ $hidden_field_name ] ) {
			if ( check_admin_referer( 'sct_settings_nonce', 'sct_settings_nonce' ) ) {
				if ( ! empty( $_POST['use_slack'] ) ) {
					$use_slack = sanitize_text_field( wp_unslash( $_POST['use_slack'] ) );
					update_option( 'sct_use_slack', $use_slack );
				} else {
					update_option( 'sct_use_slack', '0' );
				}
				if ( ! empty( $_POST['slack_webhook_url'] ) ) {
					$slack_webhook_url = sanitize_text_field( wp_unslash( $_POST['slack_webhook_url'] ) );
					$crypt_slack       = Sct_Encryption::encrypt( $slack_webhook_url );
					update_option( 'sct_slack_webhook_url', $crypt_slack );
				}
				if ( ! empty( $_POST['send_slack_author'] ) ) {
					$send_slack_author = sanitize_text_field( wp_unslash( $_POST['send_slack_author'] ) );
					update_option( 'sct_send_slack_author', $send_slack_author );
				} else {
					update_option( 'sct_send_slack_author', '0' );
				}
			}
		}

		$get_slack_webhook_url = Sct_Encryption::decrypt( get_option( 'sct_slack_webhook_url' ) );
		$get_use_slack         = '1' === get_option( 'sct_use_slack' ) ? 'checked' : '';
		$get_send_slack_author = '1' === get_option( 'sct_send_slack_author' ) ? 'checked' : '';

		?>
<div class="wrap">
		<?php if ( isset( $_POST[ $hidden_field_name ] ) && 'Y' === $_POST[ $hidden_field_name ] ) : ?>
			<?php if ( check_admin_referer( 'sct_settings_nonce', 'sct_settings_nonce' ) ) : ?>
	<div class="updated">
		<p><?php esc_html_e( 'Your update has been successfully completed!!', 'send-chat-tools' ); ?></p>
	</div>
			<?php else : ?>
	<div class="error">
		<p><?php esc_html_e( 'An error has occurred. Please try again.', 'send-chat-tools' ); ?></p>
	</div>
			<?php endif ?>
		<?php endif ?>
	<form method="POST">
		<h1><?php esc_html_e( 'Send Chat Tools Settings', 'send-chat-tools' ); ?></h1>
		<h2><?php esc_html_e( 'Slack', 'send-chat-tools' ); ?></h2>
		<input type="hidden" name="<?php echo esc_attr( $hidden_field_name ); ?>" value="Y">
		<?php wp_nonce_field( 'sct_settings_nonce', 'sct_settings_nonce' ); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<label for="use_slack"><?php esc_html_e( 'Use Slack', 'send-chat-tools' ); ?></label>
					</th>
					<td>
						<input type="checkbox" id="use_slack" name="use_slack" value="1" <?php echo esc_attr( $get_use_slack ); ?>>
					</td>
				</tr>
				<tr>
					<th>
						<label for="slack_webhook_url"><?php esc_html_e( 'Webhook URL', 'send-chat-tools' ); ?></label>
					</th>
					<td>
						<input type="text" id="slack_webhook_url" name="slack_webhook_url" size="60" value="<?php echo esc_attr( $get_slack_webhook_url ); ?>" placeholder="<?php esc_html_e( 'Input Slack Webhook URL', 'send-chat-tools' ); ?>">
					</td>
				</tr>
				<tr>
					<th>
						<label for="send_slack_author"><?php esc_html_e( 'Don\'t send self comment', 'send-chat-tools' ); ?></label>
					</th>
					<td>
						<input type="checkbox" id="send_slack_author" name="send_slack_author" value="1" <?php echo esc_attr( $get_send_slack_author ); ?>>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
		</p>
	</form>
</div>
		<?php
	}
}

if ( is_admin() ) {
	$settings_page = new Sct_Settings_Page();
}
