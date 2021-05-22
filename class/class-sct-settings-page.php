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

		$hidden_field_name = 'hiddenStatus';

		if ( isset( $_POST[ $hidden_field_name ] ) && 'Y' === $_POST[ $hidden_field_name ] ) {
			if ( check_admin_referer( 'sct_settings_nonce', 'sct_settings_nonce' ) ) {
				if ( isset( $_POST['slack_api_key'] ) ) {
					$slack_api_key = sanitize_text_field( wp_unslash( $_POST['slack_api_key'] ) );
					update_option( 'sct_slack_api_key', $slack_api_key );
				}
				if ( isset( $_POST['slack_channel_name'] ) ) {
					$slack_channel_name = sanitize_text_field( wp_unslash( $_POST['slack_channel_name'] ) );
					update_option( 'sct_slack_channel_name', $slack_channel_name );
				}
			}
		}

		$get_slack_api_key      = get_option( 'sct_slack_api_key' );
		$get_slack_channel_name = get_option( 'sct_slack_channel_name' );

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
	<h1><?php esc_html_e( 'Send Chat Tools Settings', 'send-chat-tools' ); ?></h1>
	<h2><?php esc_html_e( 'Slack', 'send-chat-tools' ); ?></h2>
	<form method="POST">
		<input type="hidden" name="<?php echo esc_attr( $hidden_field_name ); ?>" value="Y">
		<?php wp_nonce_field( 'sct_settings_nonce', 'sct_settings_nonce' ); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<label><?php esc_html_e( 'API Key', 'send-chat-tools' ); ?></label>
					</th>
					<td>
						<input type="text" name="slack_api_key" size="60" value="<?php echo esc_attr( $get_slack_api_key ); ?>" placeholder="<?php esc_html_e( 'Input Slack API Key', 'send-chat-tools' ); ?>">
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e( 'Slack channel name', 'send-chat-tools' ); ?></label>
					</th>
					<td>
						<input type="text" name="slack_channel_name" size="" value="<?php echo esc_attr( $get_slack_channel_name ); ?>" placeholder="<?php esc_html_e( '@channnel', 'send-chat-tools' ); ?>">
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
