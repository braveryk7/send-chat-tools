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
		add_action( 'admin_head-settings_page_send-chat-tools-settings', [ $this, 'include_css' ] );
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
	 * Include CSS in Send Chat Tools settings page.
	 */
	public function include_css() {
			wp_enqueue_style( 'sct-admin-page.css', plugins_url( 'css/style.css', dirname( __FILE__ ) ), false, gmdate( 'Ymd', filemtime( __FILE__ ) ) );
	}

	/**
	 * Settings page.
	 */
	public function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You have no sufficient permissions to access this page.', 'send-chat-tools' ) );
		}
		require_once dirname( __FILE__ ) . '/class-sct-encryption.php';
		require_once dirname( __FILE__ ) . '/class-sct-check-update.php';

		$hidden_field_name = 'hiddenStatus';

		if ( isset( $_POST[ $hidden_field_name ] ) && 'Y' === $_POST[ $hidden_field_name ] ) {
			if ( check_admin_referer( 'sct_settings_nonce', 'sct_settings_nonce' ) ) {
				/** WordPress */
				if ( isset( $_POST['comments_notify'] ) ) {
					$use_slack = sanitize_text_field( wp_unslash( $_POST['comments_notify'] ) );
					update_option( 'comments_notify', '1' );
				} else {
					update_option( 'comments_notify', '' );
				}
				if ( isset( $_POST['moderation_notify'] ) ) {
					$use_slack = sanitize_text_field( wp_unslash( $_POST['moderation_notify'] ) );
					update_option( 'moderation_notify', '1' );
				} else {
					update_option( 'moderation_notify', '' );
				}
				/** Slack */
				if ( ! empty( $_POST['use_slack'] ) ) {
					$use_slack = sanitize_text_field( wp_unslash( $_POST['use_slack'] ) );
					update_option( 'sct_use_slack', $use_slack );
				} else {
					update_option( 'sct_use_slack', '0' );
				}
				if ( isset( $_POST['slack_webhook_url'] ) ) {
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
				/** Chatwork */
				if ( ! empty( $_POST['use_chatwork'] ) ) {
					$use_chatwork = sanitize_text_field( wp_unslash( $_POST['use_chatwork'] ) );
					update_option( 'sct_use_chatwork', $use_chatwork );
				} else {
					update_option( 'sct_use_chatwork', '0' );
				}
				if ( isset( $_POST['chatwork_api_token'] ) ) {
					$chatwork_api_token = sanitize_text_field( wp_unslash( $_POST['chatwork_api_token'] ) );
					$crypt_chatwork     = Sct_Encryption::encrypt( $chatwork_api_token );
					update_option( 'sct_chatwork_api_token', $crypt_chatwork );
				}
				if ( isset( $_POST['chatwork_room_id'] ) ) {
					$chatwork_room_id = sanitize_text_field( wp_unslash( $_POST['chatwork_room_id'] ) );
					$crypt_room_id    = Sct_Encryption::encrypt( $chatwork_room_id );
					update_option( 'sct_chatwork_room_id', $crypt_room_id );
				}
				if ( ! empty( $_POST['send_chatwork_author'] ) ) {
					$send_chatwork_author = sanitize_text_field( wp_unslash( $_POST['send_chatwork_author'] ) );
					update_option( 'sct_send_chatwork_author', $send_chatwork_author );
				} else {
					update_option( 'sct_send_chatwork_author', '0' );
				}
				/** Discord */
				if ( ! empty( $_POST['use_discord'] ) ) {
					$use_discord = sanitize_text_field( wp_unslash( $_POST['use_discord'] ) );
					update_option( 'sct_use_discord', $use_discord );
				} else {
					update_option( 'sct_use_discord', '0' );
				}
				if ( isset( $_POST['discord_webhook_url'] ) ) {
					$discord_webhook_url = sanitize_text_field( wp_unslash( $_POST['discord_webhook_url'] ) );
					$crypt_discord       = Sct_Encryption::encrypt( $discord_webhook_url );
					update_option( 'sct_discord_webhook_url', $crypt_discord );
				}
				if ( ! empty( $_POST['send_discord_author'] ) ) {
					$send_discord_author = sanitize_text_field( wp_unslash( $_POST['send_discord_author'] ) );
					update_option( 'sct_send_discord_author', $send_discord_author );
				} else {
					update_option( 'sct_send_discord_author', '0' );
				}
			}
		}
		$get_comments_notify   = '1' === get_option( 'comments_notify' ) ? 'checked' : '';
		$get_moderation_notify = '1' === get_option( 'moderation_notify' ) ? 'checked' : '';

		$get_slack_webhook_url = Sct_Encryption::decrypt( get_option( 'sct_slack_webhook_url' ) );
		$get_use_slack         = '1' === get_option( 'sct_use_slack' ) ? 'checked' : '';
		$get_send_slack_author = '1' === get_option( 'sct_send_slack_author' ) ? 'checked' : '';

		$get_chatwork_api_token   = Sct_Encryption::decrypt( get_option( 'sct_chatwork_api_token' ) );
		$get_chatwork_room_id     = Sct_Encryption::decrypt( get_option( 'sct_chatwork_room_id' ) );
		$get_use_chatwork         = '1' === get_option( 'sct_use_chatwork' ) ? 'checked' : '';
		$get_send_chatwork_author = '1' === get_option( 'sct_send_chatwork_author' ) ? 'checked' : '';

		$get_discord_webhook_url = Sct_Encryption::decrypt( get_option( 'sct_discord_webhook_url' ) );
		$get_use_discord         = '1' === get_option( 'sct_use_discord' ) ? 'checked' : '';
		$get_send_discord_author = '1' === get_option( 'sct_send_discord_author' ) ? 'checked' : '';

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
	<form method="POST">
		<input type="hidden" name="<?php echo esc_attr( $hidden_field_name ); ?>" value="Y">
		<?php wp_nonce_field( 'sct_settings_nonce', 'sct_settings_nonce' ); ?>
		<div class="postbox">
			<h2><?php esc_html_e( 'Standard WordPress settings', 'send-chat-tools' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Email me whenever' ); ?></th>
						<td>
							<fieldset>
								<label for="comments_notify">
									<input name="comments_notify" type="checkbox" id="comments_notify" value="1" <?php echo esc_attr( $get_comments_notify ); ?>>
									<?php esc_html_e( 'Anyone posts a comment' ); ?>
								</label>
								<br>
								<label for="moderation_notify">
								<input name="moderation_notify" type="checkbox" id="moderation_notify" value="1" <?php echo esc_attr( $get_moderation_notify ); ?>>
									<?php esc_html_e( 'A comment is held for moderation' ); ?>
								</label>
							</fieldset>
							<p><?php esc_html_e( 'Uncheck this box if you don\'t need the standard WordPress email notifications.', 'send-chat-tools' ); ?></p>
							<p><?php esc_html_e( 'Even if this checkbox is unchecked, you will still be notified by email if the message was not successfully sent to the chat tool.', 'send-chat-tools' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="postbox">
			<h2><?php esc_html_e( 'Slack', 'send-chat-tools' ); ?></h2>
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
							<p><?php esc_html_e( 'Get the webhook URL from the Slack API.', 'send-chat-tools' ); ?></p>
							<p><?php esc_html_e( 'The URL is usually in https://hooks.slack.com/services/XXXXX/XXXXX format.', 'send-chat-tools' ); ?></p>
							<p>
								<?php esc_html_e( 'Explanation of getting the Slack Webhook URL:', 'send-chat-tools' ); ?>
								<a href="https://www.braveryk7.com/portfolio/send-chat-tools/slack-webhook-url-settings/" target="_blank"><?php esc_html_e( 'Steps to add a Slack Webhook URL to Send Chat Tools | L\'7 Records(Japanese Only)', 'send-chat-tools' ); ?></a>
							</p>
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
		</div>
		<div class="postbox">
			<h2><?php esc_html_e( 'Chatwork', 'send-chat-tools' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="use_chatwork"><?php esc_html_e( 'Use Chatwork', 'send-chat-tools' ); ?></label>
						</th>
						<td>
							<input type="checkbox" id="use_chatwork" name="use_chatwork" value="1" <?php echo esc_attr( $get_use_chatwork ); ?>>
						</td>
					</tr>
					<tr>
						<th>
							<label for="chatwork_api_token"><?php esc_html_e( 'API Token', 'send-chat-tools' ); ?></label>
						</th>
						<td>
							<input type="text" id="chatwork_api_token" name="chatwork_api_token" size="60" value="<?php echo esc_attr( $get_chatwork_api_token ); ?>" placeholder="<?php esc_html_e( 'Input Chatwork API Token', 'send-chat-tools' ); ?>">
							<p><?php esc_html_e( 'Get the API Toke from the Chatwork.', 'send-chat-tools' ); ?></p>
							<p><?php esc_html_e( 'It is usually random alphanumeric.', 'send-chat-tools' ); ?></p>
							<p>
								<?php esc_html_e( 'Explanation of getting the Chatwork API Token & Room ID:', 'send-chat-tools' ); ?>
								<a href="https://www.braveryk7.com/portfolio/send-chat-tools/send-chat-tools-chatwork-api-token-and-room-id/" target="_blank"><?php esc_html_e( 'Steps to add a Chatwork API token and Room ID to Send Chat Tools | L\'7 Records(Japanese Only)', 'send-chat-tools' ); ?></a>
							</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="chatwork_room_id"><?php esc_html_e( 'Room ID', 'send-chat-tools' ); ?></label>
						</th>
						<td>
							<input type="text" id="chatwork_room_id" name="chatwork_room_id" value="<?php echo esc_attr( $get_chatwork_room_id ); ?>" placeholder="<?php esc_html_e( 'Input Chatwork room ID', 'send-chat-tools' ); ?>">
							<p><?php esc_html_e( 'Get the Room ID from the Chatwork chat URL.', 'send-chat-tools' ); ?></p>
							<p><?php esc_html_e( 'The number after "rid" in the URL of the chat page you want to receive.', 'send-chat-tools' ); ?></p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="send_chatwork_author"><?php esc_html_e( 'Don\'t send self comment', 'send-chat-tools' ); ?></label>
						</th>
						<td>
							<input type="checkbox" id="send_chatwork_author" name="send_chatwork_author" value="1" <?php echo esc_attr( $get_send_chatwork_author ); ?>>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="postbox">
			<h2><?php esc_html_e( 'Discord', 'send-chat-tools' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="use_discord"><?php esc_html_e( 'Use Discord', 'send-chat-tools' ); ?></label>
						</th>
						<td>
							<input type="checkbox" id="use_discord" name="use_discord" value="1" <?php echo esc_attr( $get_use_discord ); ?>>
						</td>
					</tr>
					<tr>
						<th>
							<label for="discord_webhook_url"><?php esc_html_e( 'Webhook URL', 'send-chat-tools' ); ?></label>
						</th>
						<td>
							<input type="text" id="discord_webhook_url" name="discord_webhook_url" size="60" value="<?php echo esc_attr( $get_discord_webhook_url ); ?>" placeholder="<?php esc_html_e( 'Input Discord Webhook URL', 'send-chat-tools' ); ?>">
							<p><?php esc_html_e( 'Get the webhook URL from the Discord API.', 'send-chat-tools' ); ?></p>
							<p><?php esc_html_e( 'The URL is usually in https://discord.com/api/webhooks/XXXXX/XXXXX format.', 'send-chat-tools' ); ?></p>
							<p>
								<?php esc_html_e( 'Explanation of getting the Discord Webhook URL:', 'send-chat-tools' ); ?>
								<a href="https://www.braveryk7.com/portfolio/send-chat-tools/slack-webhook-url-settings/" target="_blank"><?php esc_html_e( 'Steps to add a Discord Webhook URL to Send Chat Tools | L\'7 Records(Japanese Only)', 'send-chat-tools' ); ?></a>
							</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="send_discord_author"><?php esc_html_e( 'Don\'t send self comment', 'send-chat-tools' ); ?></label>
						</th>
						<td>
							<input type="checkbox" id="send_discord_author" name="send_discord_author" value="1" <?php echo esc_attr( $get_send_discord_author ); ?>>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
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
