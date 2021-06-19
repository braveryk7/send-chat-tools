<?php
/**
 * Admin settings page.
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
		add_action( 'admin_enqueue_scripts', [ $this, 'include_js' ] );
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
		wp_enqueue_style(
			'sct-admin-page.css',
			plugins_url( 'dist/css/style.css', dirname( __FILE__ ) ),
			false,
			gmdate( 'Ymd', filemtime( __FILE__ ) )
		);
	}

	/**
	 * Include JS in Send Chat Tools settings page.
	 */
	public function include_js() {
		wp_enqueue_script(
			'sct-admin-page',
			plugins_url( '/dist/main.js?', dirname( __FILE__ ) ),
			[ 'wp-i18n' ],
			time(),
			true,
		);

		wp_set_script_translations(
			'sct-admin-page',
			'send-chat-tools',
			dirname( __DIR__ ) . '/languages',
		);
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
		require_once dirname( __FILE__ ) . '/class-sct-settings-page-row.php';

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
				if ( ! empty( $_POST['send_slack_update'] ) ) {
					$send_slack_author = sanitize_text_field( wp_unslash( $_POST['send_slack_update'] ) );
					update_option( 'sct_send_slack_update', $send_slack_author );
				} else {
					update_option( 'sct_send_slack_update', '0' );
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
				if ( ! empty( $_POST['send_chatwork_update'] ) ) {
					$send_chatwork_author = sanitize_text_field( wp_unslash( $_POST['send_chatwork_update'] ) );
					update_option( 'sct_send_chatwork_update', $send_chatwork_author );
				} else {
					update_option( 'sct_send_chatwork_update', '0' );
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
				if ( ! empty( $_POST['send_discord_update'] ) ) {
					$send_discord_author = sanitize_text_field( wp_unslash( $_POST['send_discord_update'] ) );
					update_option( 'sct_send_discord_update', $send_discord_author );
				} else {
					update_option( 'sct_send_discord_update', '0' );
				}
			}
		}

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
		<?php
			wp_nonce_field( 'sct_settings_nonce', 'sct_settings_nonce' );

			$row = new Sct_Settings_Page_Row();
			$row->settings( 'default' );
			$row->settings( 'slack' );
			$row->settings( 'discord' );
			$row->settings( 'chatwork' );
		?>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
		</p>
	</form>
		<?php $row->log_view(); ?>
</div>
		<?php
	}
}

if ( is_admin() ) {
	$settings_page = new Sct_Settings_Page();
}
