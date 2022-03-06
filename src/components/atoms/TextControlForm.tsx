import { TextControl } from '@wordpress/components';
import { useContext, useEffect, useState } from '@wordpress/element';

import { apiContext } from 'src/index';

import { TextControlPropsType } from 'src/types/ComponentsType';
import { apiType } from 'src/types/apiType';

export const TextControlForm = ( props: TextControlPropsType ) => {
	const { itemKey, optionName, label } = props;
	const { apiData, setApiData } = useContext( apiContext );
	const [ itemData, setItemData ] = useState( '' );

	useEffect( () => {
		if ( apiData ) {
			if ( itemKey === 'slack' || itemKey === 'discord' ) {
				setItemData( apiData[ itemKey ].webhook_url );
			} else {
				switch ( optionName ) {
					case 'api_token':
						setItemData( apiData.chatwork.api_token );
						break;
					case 'room_id':
						setItemData( apiData.chatwork.room_id );
						break;
				}
			}
		}
	}, [ optionName, apiData ] );

	const changeValue = ( value: string ) => {
		const newItem: apiType = JSON.parse( JSON.stringify( { ...apiData } ) );

		if (
			( itemKey === 'slack' || itemKey === 'discord' ) &&
			optionName === 'webhook_url'
		) {
			newItem[ itemKey ][ optionName ] = value;
		} else if (
			itemKey === 'chatwork' &&
			( optionName === 'api_token' || optionName === 'room_id' )
		) {
			newItem[ itemKey ][ optionName ] = value;
		}
		setApiData( newItem );
	};

	return (
		<TextControl
			label={ label }
			value={ itemData }
			onChange={ ( value: string ) => changeValue( value ) }
		/>
	);
};
