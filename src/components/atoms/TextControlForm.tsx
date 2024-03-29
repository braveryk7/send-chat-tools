import { TextControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';

import { useChangeValue } from 'src/hooks/useChangeValue';

import { TextControlPropsType } from 'src/types/ComponentsType';

export const TextControlForm = ( props: TextControlPropsType ) => {
	const { itemKey, optionName, label } = props;
	const { apiData, changeValue } = useChangeValue( itemKey, optionName );
	const [ itemData, setItemData ] = useState( '' );

	useEffect( () => {
		if ( apiData ) {
			if ( itemKey === 'slack' || itemKey === 'discord' ) {
				setItemData( apiData[ itemKey ].webhook_url );
			} else if ( optionName === 'api_token' ) {
				setItemData( apiData.chatwork.api_token );
			} else if ( optionName === 'room_id' ) {
				setItemData( apiData.chatwork.room_id );
			}
		}
	}, [ itemKey, optionName, apiData ] );

	return (
		<>
			{ apiData &&
				<TextControl
					label={ label }
					value={ itemData }
					onChange={ ( value: string ) => changeValue( value ) }
				/>
			}
		</>
	);
};
