import { ExternalLink } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export const RinkerNotActive = () => {
	const rinkerExistMessage = __( 'Rinker is not activated.', 'send-chat-tools' );
	const rinkerRecommendMessage = __(
		'Rinker is a very popular product management plug-in for Amazon and Rakuten.',
		'send-chat-tools'
	);
	const rinkerUrl = 'https://oyakosodate.com/rinker/';
	const rinkerFanboxUrl = 'https://oyayoi.fanbox.cc/';

	return (
		<div className="sct-rinker-notice">
			<p>
				{ rinkerExistMessage + ' ' + rinkerRecommendMessage } <br />
				{ __( 'Download', 'send-chat-tools' ) + ' >>> ' }
				{ 'string' === typeof rinkerUrl &&
					<ExternalLink
						href={ rinkerUrl }
					>
						{ __( 'Rinker Official Web Site', 'send-chat-tools' ) }
					</ExternalLink>
				}
				{ ' / ' }
				{ 'string' === typeof rinkerFanboxUrl &&
					<ExternalLink
						href={ rinkerFanboxUrl }
					>
						{ __( 'Rinker Official FANBOX', 'send-chat-tools' ) }
					</ExternalLink>
				}
			</p>
		</div>
	);
};
