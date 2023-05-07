<?php
/**
 * Abstract class for generating content data and processing it in each chat tool
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 1.5.0
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Abstract class
 */
abstract class Sct_Generate_Content_Abstract extends Sct_Base {
	/**
	 * Property with WordPress comment data.
	 *
	 * @var object Comment data.
	 */
	protected $comment;

	/**
	 * Property to store generated header.
	 *
	 * @var array Generated header.
	 */
	protected $header;

	/**
	 * Property to store generated content.
	 *
	 * @var array Generated comment content.
	 */
	protected $content;

	/**
	 * Property that stores the tool name.
	 *
	 * @var string Tool name.
	 */
	protected $tool_name;

	/**
	 * Property that stores the site name.
	 *
	 * @var string Site name.
	 */
	protected $site_name;

	/**
	 * Property that stores the site URL.
	 *
	 * @var string Site URL.
	 */
	protected $site_url;

	/**
	 * Property that stores the type of notification.
	 *
	 * @var string Notification type.
	 */
	protected $notification_type;

	/**
	 * Property that stores the original data before processing.
	 *
	 * @var object|array Original data.
	 */
	protected $original_data;

	/**
	 * Property that stores the flag of whether or not it is an error mail.
	 *
	 * @var bool Error mail flag.
	 */
	protected $is_error_mail = false;

	/**
	 * Constructor to obtain information necessary for content generation.
	 */
	protected function __construct() {
		$this->site_name = get_bloginfo( 'name' );
		$this->site_url  = get_bloginfo( 'url' );
	}

	/**
	 * Abstract method to get an instance.
	 */
	abstract public static function get_instance(): Sct_Slack | Sct_Discord | Sct_Chatwork | Sct_Error_Mail;

	/**
	 * Abstract method to generate a chat tool header.
	 */
	abstract public function generate_header(): Sct_Slack | Sct_Discord | Sct_Chatwork | Sct_Error_Mail;

	/**
	 * Abstract method to generate comment content to be sent to chat tools.
	 */
	abstract public function generate_comment_content(): Sct_Slack | Sct_Discord | Sct_Chatwork | Sct_Error_Mail;

	/**
	 * Abstract method to generate update content to be sent to chat tools.
	 */
	abstract public function generate_update_content(): Sct_Slack | Sct_Discord | Sct_Chatwork | Sct_Error_Mail;

	/**
	 * Abstract method to generate developer message content to be sent to chat tools.
	 */
	abstract public function generate_developer_content(): Sct_Slack | Sct_Discord | Sct_Chatwork | Sct_Error_Mail;

	/**
	 * Abstract method to generate login message content to be sent to chat tools.
	 */
	abstract public function generate_login_content(): Sct_Slack | Sct_Discord | Sct_Chatwork | Sct_Error_Mail;

	/**
	 * Abstract method to generate Rinker content to be sent to chat tools.
	 */
	abstract public function generate_rinker_content(): Sct_Slack | Sct_Discord | Sct_Chatwork | Sct_Error_Mail;

	/**
	 * Method to set notification type and original data.
	 *
	 * @param string       $notification_type Notification type.
	 * @param array|object $original_data     Original data.
	 */
	public function set_notification_type_original_data( string $notification_type, object | array $original_data ): Sct_Slack | Sct_Discord | Sct_Chatwork {
		$this->notification_type = $notification_type;
		$this->original_data     = $original_data;

		return $this;
	}

	/**
	 * Method to generate headers for each chat tool.
	 *
	 * @param string|null $header_emoji Header emoji.
	 * @param string      $header_message Header message.
	 */
	protected function generate_header_message( string $header_emoji = null, string $header_message = '' ): string {
		return match ( $this->tool_name ) {
			'slack'      => "{$header_emoji} {$this->site_name}({$this->site_url}) " . $header_message,
			'discord'    => "{$header_emoji} __***{$this->site_name}({$this->site_url}) " . $header_message . '***__',
			'chatwork'   => '[title]' . $this->site_name . '(' . $this->site_url . ') ' . $header_message . '[/title]',
			'error_mail' => "{$this->site_name}({$this->site_url}) {$$header_message}",
		};
	}

	/**
	 * Generate comment approved message.
	 *
	 * @param string $tool_name Tool name.
	 * @param object $comment   Comment data.
	 */
	protected function generate_comment_approved_message( string $tool_name, object $comment ): string {
		$approved_url  = admin_url() . 'comment.php?action=approve&c=' . $comment->comment_ID;
		$unapproved    = $this->get_send_text( 'comment_notify', 'unapproved' );
		$click_message = $this->get_send_text( 'comment_notify', 'click' );

		return match ( $comment->comment_approved ) {
			'1'    => $this->get_send_text( 'comment_notify', 'approved' ),
			'0'    => match ( $tool_name ) {
				'slack'    => $unapproved . '<<' . $approved_url . '|' . $click_message . '>>',
				'discord'  => $unapproved . ' >> ' . $click_message . '( ' . $approved_url . ' )',
				'chatwork' => $unapproved . "\n" . $click_message . ' ' . $approved_url,
			},
			'spam' => $this->get_send_text( 'comment_notify', 'spam' ),
		};
	}

	/**
	 * Generate update raw data.
	 *
	 * @param array $update_content Update data.
	 */
	protected function generate_update_raw_data( array $update_content ): stdClass {
		$add_core    = null;
		$add_themes  = null;
		$add_plugins = null;

		foreach ( $update_content as $value ) {
			$is_core                              = 'core' === $value['attribute'] ? null : 's';
			${ "add_$value[attribute]$is_core" } .= "   $value[name] ( $value[current_version] -> $value[new_version] )\n";
		};

		$update_raw_data            = new stdClass();
		$update_raw_data->core      = isset( $add_core ) ? esc_html__( 'WordPress Core:', 'send-chat-tools' ) . "\n" . $add_core . "\n" : null;
		$update_raw_data->themes    = isset( $add_themes ) ? esc_html__( 'Themes:', 'send-chat-tools' ) . "\n" . $add_themes . "\n" : null;
		$update_raw_data->plugins   = isset( $add_plugins ) ? esc_html__( 'Plugins:', 'send-chat-tools' ) . "\n" . $add_plugins . "\n" : null;
		$update_raw_data->admin_url = admin_url() . 'update-core.php';

		return $update_raw_data;
	}

	/**
	 * A method to format the list of items that are no longer handled by Rinker into a string type.
	 *
	 * @param array $rinker_discontinued_items Rinker discontinued items.
	 */
	protected function format_rinker_items( array $rinker_discontinued_items ): string {
		$format = $this->is_error_mail ? "    ・ %2\$s\n         %1\$s\n" : match ( $this->tool_name ) {
			'slack'                => "    ・ <%s|%s>\n",
			'discord', 'chatwork'  => "    ・ %2\$s - %1\$s\n",
		};

		$amazon  = '';
		$rakuten = '';

		foreach ( $rinker_discontinued_items as $item ) {
			if ( 'amazon' === $item['item_shop'] ) {
				$amazon = $amazon . sprintf( $format, $item['item_url'], $item['item_name'] );
			} elseif ( 'rakuten' === $item['item_shop'] ) {
				$rakuten = $rakuten . sprintf( $format, $item['item_url'], $item['item_name'] );
			}
		}

		$amazon            = $amazon ? $this->get_send_text( 'rinker_notify', 'amazon' ) . ": \n" . $amazon : $amazon;
		$rakuten           = $rakuten ? $this->get_send_text( 'rinker_notify', 'rakuten' ) . ": \n" . $rakuten : $rakuten;
		$is_amazon_rakuten = $amazon && $rakuten ? "\n" : '';

		return $amazon . $is_amazon_rakuten . $rakuten;
	}

	/**
	 * Method to retrieve text to be used during content generation.
	 *
	 * @param string $type  Message type.
	 * @param string $param Item parameter.
	 */
	protected function get_send_text( string $type, string $param ): string {
		$message = [
			'constant'       => [
				'date' => __( 'Date and time', 'send-chat-tools' ),
			],
			'comment_notify' => [
				'title'      => __( 'New comment has been posted', 'send-chat-tools' ),
				'article'    => __( 'Commented article', 'send-chat-tools' ),
				'commenter'  => __( 'Commenter', 'send-chat-tools' ),
				'comment'    => __( 'Comment', 'send-chat-tools' ),
				'url'        => __( 'Comment URL', 'send-chat-tools' ),
				'status'     => __( 'Comment Status', 'send-chat-tools' ),
				'approved'   => __( 'Approved', 'send-chat-tools' ),
				'unapproved' => __( 'Unapproved', 'send-chat-tools' ),
				'click'      => __( 'Click here to approve', 'send-chat-tools' ),
				'spam'       => __( 'Spam', 'send-chat-tools' ),
			],
			'update_notify'  => [
				'title'  => __( 'Notification of new updates', 'send-chat-tools' ),
				'update' => __( 'Please login to the admin panel to update.', 'send-chat-tools' ),
				'page'   => __( 'Update Page', 'send-chat-tools' ),
			],
			'dev_notify'     => [
				/* translators: 1: Theme or Plugin name */
				'title'   => __( 'Update notifications from %s', 'send-chat-tools' ),
				'website' => __( 'Official Web Site', 'send-chat-tools' ),
				'detail'  => __( 'Update details', 'send-chat-tools' ),
				'ignore'  => __(
					'If this message is sent by a malicious developer, it can be rejected with the following key',
					'send-chat-tools',
				),
			],
			'login_notify'   => [
				'title'              => __( 'Login Notification', 'send-chat-tools' ),
				'user_name'          => __( 'User name', 'send-chat-tools' ),
				'login_env'          => __( 'Login environment', 'send-chat-tools' ),
				'ip_address'         => __( 'IP Address', 'send-chat-tools' ),
				'unauthorized_login' => __( 'If you do not recognize this message, you may have an unauthorized login.', 'send-chat-tools' ),
				'disconnect'         => __( 'Disconnect all location sessions and change passwords.', 'send-chat-tools' ),
			],
			'rinker_notify'  => [
				'title'     => __( 'Rinker End Of Sales Notification', 'send-chat-tools' ),
				'amazon'    => __( 'Amazon', 'send-chat-tools' ),
				'rakuten'   => __( 'Rakuten', 'send-chat-tools' ),
				'temporary' => __( 'Amazon and Rakuten may have temporarily withdrawn sales.', 'send-chat-tools' ),
				'resume'    => __( 'Note that sales may resume after this notice is received, and there is no promise of an end of sales.', 'send-chat-tools' ),
			],
			'error_mail'     => [
				'tool_name'  => __( 'Tool name', 'send-chat-tools' ),
				'error_code' => __( 'Error code', 'send-chat-tools' ),
			],
		];

		return $message[ $type ][ $param ];
	}

	/**
	 * Generate context message.
	 *
	 * @param string $tool_name Tool name.
	 */
	protected function generate_context( string $tool_name ): string {
		$message = [
			0 => esc_html__( 'This message was sent by Send Chat Tools', 'send-chat-tools' ),
			1 => esc_html__( 'WordPress Plugin Directory', 'send-chat-tools' ),
			2 => esc_html__( 'Send Chat Tools Official Page', 'send-chat-tools' ),
			3 => esc_html__( 'Possible that the message was not sent to the chat tool correctly.', 'send-chat-tools' ),
		];

		$wordpress_directory = $this->get_official_directory();
		$official_web_site   = 'https://www.braveryk7.com/portfolio/send-chat-tools/';

		return match ( $tool_name ) {
			'slack'      => $message[0] . "\n" . '<' . $wordpress_directory . '|' . $message[1] . '> / <' . $official_web_site . '|' . $message[2] . '>',
			'discord'    => '>>> ' . $message[0] . "\n\n" . $message[1] . ': <' . $wordpress_directory . '>' . "\n" . $message[2] . ': <' . $official_web_site . '>',
			'chatwork'   => '[hr]' . $message[0] . "\n" . $message[1] . ': ' . $wordpress_directory . "\n" . $message[2] . ': ' . $official_web_site,
			'error_mail' => $message[0] . "\n\n" . $message[1] . ': ' . $wordpress_directory . "\n" . $message[2] . ': ' . $official_web_site . "\n\n" . $message[3],
		};
	}

	/**
	 * Methods to send to chat tools.
	 *
	 * @param string $notification_type Notification type.
	 * @param string $tool_name         Use chat tools prefix.
	 */
	public function send_tools( string $notification_type, string $tool_name ): bool {

		$sct_options = $this->get_sct_options();

		switch ( $tool_name ) {
			case 'slack':
			case 'discord':
				$url   = $sct_options[ $tool_name ]['webhook_url'];
				$regex = $this->api_regex( $tool_name, $url );
				break;
			case 'chatwork':
				$url   = 'https://api.chatwork.com/v2/rooms/' . $sct_options[ $tool_name ]['room_id'] . '/messages';
				$regex = $this->api_regex( 'chatworkid', $sct_options[ $tool_name ]['room_id'] );
				break;
		}

		if ( $regex ) {
			$result = wp_remote_post( $url, $this->header );

			if ( ! is_null( $this->comment ) ) {
				$logs = [
					$this->comment->comment_date => [
						'id'      => $this->comment->comment_ID,
						'author'  => $this->comment->comment_author,
						'email'   => $this->comment->comment_author_email,
						'url'     => $this->comment->comment_author_url,
						'comment' => $this->comment->comment_content,
						'status'  => $result['response']['code'],
					],
				];

				if ( '3' <= count( $sct_options[ $tool_name ]['log'] ) ) {
					array_pop( $sct_options[ $tool_name ]['log'] );
				}
				$sct_options[ $tool_name ]['log'] = $logs + $sct_options[ $tool_name ]['log'];
				$this->set_sct_options( $sct_options );
			}
		}

		$status_code = match ( true ) {
			! isset( $result->errors ) => $result['response']['code'],
			! $regex                   => 1003,
			default                    => 1000,
		};

		if ( 200 !== $status_code && 204 !== $status_code ) {
			require_once dirname( __FILE__ ) . '/class-sct-error-mail.php';
			$this->call_error_mail_class( $status_code, $tool_name );
		}

		return $this->logger( $status_code, $tool_name, $notification_type );
	}

	/**
	 * Method to call the error mail class.
	 *
	 * @param int    $error_code Error code.
	 * @param string $tool_name  Chat tool name.
	 */
	private function call_error_mail_class( int $error_code, string $tool_name ): void {
		$method = $this->notification_type;

		Sct_Error_Mail::get_instance()
			?->set_error_mail_properties( $error_code, $tool_name, $this->original_data )
			?->$method()
			?->send_mail();
	}
}
