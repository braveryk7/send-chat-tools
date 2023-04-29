import { useContext } from 'react';

import { ExternalLink, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { useChangeValue } from 'src/hooks/useChangeValue';
import { apiContext } from 'src/index';

import { TogglePropsType } from 'src/types/ComponentsType';

export const Toggle = ( props: TogglePropsType ) => {
	const { itemKey, optionName, label } = props;
	const { apiData, changeValue } = useChangeValue( itemKey, optionName );
	const { isRinkerExists } = useContext( apiContext );

	const isOptionNameRinker = () => {
		return 'rinker_notify' === optionName ? true : false;
	};

	const rinkerExistMessage = __( 'Rinker is not activated.', 'send-chat-tools' );
	const rinkerRecommendMessage = __(
		'Rinker is a very popular product management plug-in for Amazon and Rakuten.',
		'send-chat-tools'
	);
	const rinkerUrl = 'https://oyakosodate.com/rinker/';
	const rinkerFanboxUrl = 'https://oyayoi.fanbox.cc/';

	return (
		<>
			{ apiData &&
				<ToggleControl
					label={ label }
					checked={ apiData[ itemKey ][ optionName ] }
					disabled={ isOptionNameRinker() && ! isRinkerExists }
					onChange={ ( value ) => {
						changeValue( value );
					} }
				/>
			}
			{
				isOptionNameRinker() && ! isRinkerExists &&
					<div className="sct-rinker-notice">
						<p>
							{ rinkerExistMessage + ' ' + rinkerRecommendMessage } <br />
							{ __( 'Download', 'send-chat-tools' ) + ' >>> ' }
							<ExternalLink
								href={ rinkerUrl }
							>
								{ __( 'Rinker Official Web Site', 'send-chat-tools' ) }
							</ExternalLink>
							{ ' / ' }
							<ExternalLink
								href={ rinkerFanboxUrl }
							>
								{ __( 'Rinker Official FANBOX', 'send-chat-tools' ) }
							</ExternalLink>
						</p>
					</div>
			}
		</>
	);
};
