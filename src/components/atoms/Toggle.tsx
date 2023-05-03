import { useContext } from 'react';

import { ExternalLink, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { useChangeValue } from 'src/hooks/useChangeValue';
import { apiContext } from 'src/index';
import { isOptionNameRinker, rinkerMessages } from 'src/utils/rinkerUtils';

import { TogglePropsType } from 'src/types/ComponentsType';

export const Toggle = ( props: TogglePropsType ) => {
	const { itemKey, optionName, label } = props;
	const { apiData, changeValue } = useChangeValue( itemKey, optionName );
	const { isRinkerActivated } = useContext( apiContext );
	const [
		rinkerExistMessage,
		rinkerRecommendMessage,
		rinkerUrl,
		rinkerFanboxUrl,
	] = rinkerMessages();

	return (
		<>
			{ apiData &&
				<ToggleControl
					label={ label }
					checked={ apiData[ itemKey ][ optionName ] }
					disabled={ isOptionNameRinker( optionName ) && ! isRinkerActivated }
					onChange={ ( value ) => {
						changeValue( value );
					} }
				/>
			}
			{
				isOptionNameRinker( optionName ) && ! isRinkerActivated &&
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
			}
		</>
	);
};
