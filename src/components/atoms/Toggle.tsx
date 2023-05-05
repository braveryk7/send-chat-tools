import { useContext } from 'react';

import { ToggleControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

import { RinkerNotActive } from 'src/components/atoms/RinkerNotActive';
import { useChangeValue } from 'src/hooks/useChangeValue';
import { apiContext } from 'src/index';

import { TogglePropsType } from 'src/types/ComponentsType';

export const Toggle = ( props: TogglePropsType ) => {
	const { itemKey, optionName, label } = props;
	const { apiData, changeValue } = useChangeValue( itemKey, optionName );
	const { isRinkerActivated } = useContext( apiContext );

	const h4Titles = {
		use: sprintf(
			/* translators: Chat tool name. */
			__( 'Use %s', 'send-chat-tools' ),
			itemKey.charAt( 0 ).toUpperCase() + itemKey.slice( 1 )
		),
		send_author: __( 'Comment notification', 'send-chat-tools' ),
		update_notify: __( 'Update notification', 'send-chat-tools' ),
		login_notify: __( 'Login notification', 'send-chat-tools' ),
		rinker_notify: __( 'Rinker notification', 'send-chat-tools' ),
	};

	const isOptionNameRinker = () => {
		return 'rinker_notify' === optionName ? true : false;
	};

	return (
		<>
			{ apiData &&
				<section>
					<h4>{ h4Titles[ optionName ] }</h4>
					<ToggleControl
						label={ label }
						checked={ apiData[ itemKey ][ optionName ] }
						disabled={ isOptionNameRinker() && ! isRinkerActivated }
						onChange={ ( value ) => {
							changeValue( value );
						} }
					/>
				</section>
			}
			{
				isOptionNameRinker() && ! isRinkerActivated && <RinkerNotActive />
			}
		</>
	);
};
