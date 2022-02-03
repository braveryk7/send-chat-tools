<?php
/**
 * Admin settings page.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 1.0.0
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Settings page row.
 */
class Sct_Settings_Page_Row {
	/**
	 * Judgement tools and call row.
	 *
	 * @param string $tool_name Tool name.
	 */
	public function settings( string $tool_name ) {
		if ( 'default' === $tool_name ) {
			$tool = 'default';
		} elseif ( 'slack' === $tool_name ) {
			$tool = 'slack';
		} elseif ( 'discord' === $tool_name ) {
			$tool = 'discord';
		} elseif ( 'chatwork' === $tool_name ) {
			$tool = 'chatwork';
		}
		$this->row( $tool );
	}
	/**
	 * Create settings page row.
	 *
	 * @param string $tool Tool name.
	 */
	private function row( string $tool ) {
		$get_cron = get_option( 'sct_cron_time' );

		if ( 'default' !== $tool ) {
			$use_flag = get_option( 'sct_use_' . $tool );
			if ( '1' === $use_flag ) {
				[ $use, $is_active, $window_status ] = [ 'checked', 'is-active', 'is-open' ];
			} else {
				[ $use, $is_active, $window_status ] = [ '', '', '' ];
			}

			$get_send_author = '1' === get_option( 'sct_send_' . $tool . '_author' ) ? 'checked' : '';
			$get_update      = '1' === get_option( 'sct_send_' . $tool . '_update' ) ? 'checked' : '';
		}

		if ( 'default' === $tool ) {
			$get_comments_notify   = '1' === get_option( 'comments_notify' ) ? 'checked' : '';
			$get_moderation_notify = '1' === get_option( 'moderation_notify' ) ? 'checked' : '';
		} elseif ( 'slack' === $tool ) {
			$api_type                     = 1;
			$tool_name_attr               = esc_html__( 'Slack', 'send-chat-tools' );
			$tool_use_attr                = esc_html__( 'Use Slack', 'send-chat-tools' );
			$tool_webhook_attr            = esc_html__( 'Input Slack Webhook URL', 'send-chat-tools' );
			$tool_webhook_html            = esc_html__( 'Get the webhook URL from the Slack API.', 'send-chat-tools' );
			$webhook_description_html     = esc_html__( 'The URL is usually in https://hooks.slack.com/services/XXXXX/XXXXX format.', 'send-chat-tools' );
			$webhook_description_url_html = esc_html__( 'Explanation of getting the Slack Webhook URL:', 'send-chat-tools' );
			$webhook_description_url_text = esc_html__( 'Steps to add a Slack Webhook URL to Send Chat Tools | L\'7 Records(Japanese Only)', 'send-chat-tools' );
			$webhook_description_url      = 'https://www.braveryk7.com/portfolio/send-chat-tools/slack-webhook-url-settings/';
			$tool_status                  = get_option( 'sct_' . $tool . '_webhook_url' );
			if ( $tool_status ) {
				$get_webhook_url = Sct_Encryption::decrypt( $tool_status );
			}
		} elseif ( 'discord' === $tool ) {
			$api_type                     = 1;
			$tool_name_attr               = esc_html__( 'Discord', 'send-chat-tools' );
			$tool_use_attr                = esc_html__( 'Use Discord', 'send-chat-tools' );
			$tool_webhook_attr            = esc_html__( 'Input Discord Webhook URL', 'send-chat-tools' );
			$tool_webhook_html            = esc_html__( 'Get the webhook URL from the Discord API.', 'send-chat-tools' );
			$webhook_description_html     = esc_html__( 'The URL is usually in https://discord.com/api/webhooks/XXXXX/XXXXX format.', 'send-chat-tools' );
			$webhook_description_url_html = esc_html__( 'Explanation of getting the Discord Webhook URL:', 'send-chat-tools' );
			$webhook_description_url_text = esc_html__( 'Steps to add a Discord Webhook URL to Send Chat Tools | L\'7 Records(Japanese Only)', 'send-chat-tools' );
			$webhook_description_url      = 'https://www.braveryk7.com/portfolio/send-chat-tools/discord-webhook-url-settings/';
			$tool_status                  = get_option( 'sct_' . $tool . '_webhook_url' );
			if ( $tool_status ) {
				$get_webhook_url = Sct_Encryption::decrypt( $tool_status );
			}
		} elseif ( 'chatwork' === $tool ) {
			$api_type                 = 2;
			$tool_name_attr           = esc_html__( 'Chatwork', 'send-chat-tools' );
			$tool_use_attr            = esc_html__( 'Use Chatwork', 'send-chat-tools' );
			$tool_api_attr            = esc_html__( 'Input Chatwork API Token', 'send-chat-tools' );
			$tool_api_html            = esc_html__( 'Get the API Toke from the Chatwork.', 'send-chat-tools' );
			$api_description_html     = esc_html__( 'It is usually random alphanumeric.', 'send-chat-tools' );
			$api_description_url_html = esc_html__( 'Explanation of getting the Chatwork API Token & Room ID:', 'send-chat-tools' );
			$api_description_url_text = esc_html__( 'Steps to add a Chatwork API token and Room ID to Send Chat Tools | L\'7 Records(Japanese Only)', 'send-chat-tools' );
			$api_description_url      = 'https://www.braveryk7.com/portfolio/send-chat-tools/send-chat-tools-chatwork-api-token-and-room-id/';
			$tool_room_attr           = esc_html__( 'Input Chatwork room ID', 'send-chat-tools' );
			$tool_room_html           = esc_html__( 'Get the Room ID from the Chatwork chat URL.', 'send-chat-tools' );
			$room_description_html    = esc_html__( 'The number after "rid" in the URL of the chat page you want to receive.', 'send-chat-tools' );
			$api_status               = get_option( 'sct_chatwork_api_token' );
			$room_status              = get_option( 'sct_chatwork_room_id' );
			if ( $api_status ) {
				$get_api_token = Sct_Encryption::decrypt( $api_status );
			}
			if ( $room_status ) {
				$get_room_id = Sct_Encryption::decrypt( $room_status );
			}
		}

		if ( 'default' === $tool ) :
			?>
		<section>
			<h2 class="accordion-title"><?php esc_html_e( 'Standard WordPress settings', 'send-chat-tools' ); ?></h2>
			<div class="postbox accordion-content">
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
		</section>
		<?php else : ?>
		<section>
			<h2 class="accordion-title <?php echo esc_attr( $is_active ); ?>"><?php echo esc_html( $tool_name_attr ); ?></h2>
			<div class="postbox accordion-content <?php echo esc_attr( $window_status ); ?>">
				<table class="form-table">
					<tbody>
						<tr>
							<th>
								<label for="use_<?php echo esc_attr( $tool ); ?>"><?php echo esc_html( $tool_use_attr ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="use_<?php echo esc_attr( $tool ); ?>" name="use_<?php echo esc_attr( $tool ); ?>" value="1" <?php echo esc_attr( $use ); ?>>
							</td>
						</tr>
						<?php if ( 1 === $api_type ) : ?>
						<tr>
							<th>
								<label for="<?php echo esc_attr( $tool ); ?>_webhook_url"><?php esc_html_e( 'Webhook URL', 'send-chat-tools' ); ?></label>
							</th>
							<td>
								<div id="<?php echo esc_attr( $tool ); ?>_input">
									<input type="text" id="<?php echo esc_attr( $tool ); ?>_webhook_url" name="<?php echo esc_attr( $tool ); ?>_webhook_url" size="60" value="<?php echo esc_attr( $get_webhook_url ); ?>" placeholder="<?php echo esc_html( $tool_webhook_attr ); ?>">
									<span class="api-check" id="<?php echo esc_attr( $tool ); ?>-check"></span>
								</div>
								<p><?php echo esc_html( $tool_webhook_html ); ?></p>
								<p><?php echo esc_html( $webhook_description_html ); ?></p>
								<p>
									<a href="<?php echo esc_attr( $webhook_description_url ); ?>" target="_blank"><?php echo esc_html( $webhook_description_url_text ); ?></a>
								</p>
							</td>
						</tr>
						<?php else : ?>
						<tr>
							<th>
								<label for="<?php echo esc_attr( $tool ); ?>_api_token"><?php esc_html_e( 'API Token', 'send-chat-tools' ); ?></label>
							</th>
							<td>
								<input type="text" id="<?php echo esc_attr( $tool ); ?>_api_token" name="<?php echo esc_attr( $tool ); ?>_api_token" size="60" value="<?php echo esc_attr( $get_api_token ); ?>" placeholder="<?php echo esc_attr( $tool_api_attr ); ?>">
								<span class="api-check" id="<?php echo esc_attr( $tool ); ?>-check"></span>
								<p><?php echo esc_html( $tool_api_html ); ?></p>
								<p><?php echo esc_html( $api_description_html ); ?></p>
								<p>
									<?php echo esc_html( $api_description_url_html ); ?>
									<a href="<?php echo esc_attr( $api_description_url ); ?>" target="_blank"><?php echo esc_html( $api_description_url_text ); ?></a>
								</p>
							</td>
						</tr>
						<tr>
							<th>
								<label for="<?php echo esc_attr( $tool ); ?>_room_id"><?php esc_html_e( 'Room ID', 'send-chat-tools' ); ?></label>
							</th>
							<td>
								<input type="text" id="<?php echo esc_attr( $tool ); ?>_room_id" name="<?php echo esc_attr( $tool ); ?>_room_id" value="<?php echo esc_attr( $get_room_id ); ?>" placeholder="<?php echo esc_attr( $tool_room_attr ); ?>">
								<p><?php echo esc_html( $tool_room_html ); ?></p>
								<p><?php echo esc_html( $room_description_html ); ?></p>
							</td>
						</tr>
						<?php endif; ?>
						<tr>
							<th>
								<label for="send_<?php echo esc_attr( $tool ); ?>_author"><?php esc_html_e( 'Don\'t send self comment', 'send-chat-tools' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="send_<?php echo esc_attr( $tool ); ?>_author" name="send_<?php echo esc_attr( $tool ); ?>_author" value="1" <?php echo esc_attr( $get_send_author ); ?>>
							</td>
						</tr>
						<tr>
							<th>
								<label for="send_<?php echo esc_attr( $tool ); ?>_update"><?php esc_html_e( 'Use update notifications', 'send-chat-tools' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="send_<?php echo esc_attr( $tool ); ?>_update" name="send_<?php echo esc_attr( $tool ); ?>_update" value="1" <?php echo esc_attr( $get_update ); ?>>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</section>
			<?php
		endif;
	}

	/**
	 * Update check settings.
	 */
	public function update_check_view() {
		?>
		<section>
			<h2 class="accordion-title"><?php esc_html_e( 'Update check settings', 'send-chat-tools' ); ?></h2>
			<div class="postbox accordion-content">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php esc_html_e( 'Update notification time', 'send-chat-tools' ); ?></th>
							<td>
								<fieldset>
									<label for="notification_time">
										<input name="notification_time" type="time" id="notification_time" value="<?php echo esc_attr( get_option( 'sct_cron_time' ) ); ?>" >
									</label>
								</fieldset>
								<p><?php esc_html_e( 'Select the time at which notifications of updates will be sent.', 'send-chat-tools' ); ?></p>
								<p><?php esc_html_e( 'However, due to the nature of WordPress Cron, it will be sent the first time your site is accessed after this time.', 'send-chat-tools' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</section>
		<?php
	}

	/**
	 * Log view.
	 */
	public function log_view() {
		$get_date = new Sct_Connect_Database();
		$limit    = 100;
		$result   = $get_date->get_log( $limit );
		?>
		<section>
			<h2 class="accordion-title">Log</h2>
			<div class="postbox accordion-content accordion-content--log">
		<?php
		foreach ( $result as $key => $value ) {
			if ( '1' === $value->tool ) {
				$tool = 'Slack';
			} elseif ( '2' === $value->tool ) {
				$tool = 'Discord';
			} elseif ( '3' === $value->tool ) {
				$tool = 'Chatwork';
			} else {
				$tool = 'Microsoft Teams';
			}

			if ( '1' === $value->type ) {
				$type = 'Comment';
			} elseif ( '2' === $value->type ) {
				$type = 'Update';
			}

			if ( '1000' === $value->status ) {
				$status_class   = '--alert';
				$status_message = 'Could not communicate.';
			} elseif ( '1001' === $value->status ) {
				$status_class   = '--alert';
				$status_message = 'Webhook URL/API Token not entered.';
			} elseif ( '1002' === $value->status ) {
				$status_class   = '--alert';
				$status_message = 'RoomID not entered.';
			} elseif ( '200' !== $value->status && '204' !== $value->status ) {
				$status_class   = '--alert';
				$status_message = 'Could not communicate.';
			} else {
				$status_class   = '--ok';
				$status_message = 'Respons OK!';
			}
			?>
				<p class="log-row<?php echo esc_attr( $status_class ); ?>" >
					[ <?php echo esc_html( $value->send_date ); ?> ]
					[ <?php echo esc_html( $tool ); ?> ]
					[ <?php echo esc_html( $type ); ?> ]
					[ <?php echo esc_html( $value->status ); ?> ] 
					<?php echo esc_html( $status_message ); ?>
				</p>
			<?php
		}
		?>
			</div>
		</section>
		<?php
	}
}
