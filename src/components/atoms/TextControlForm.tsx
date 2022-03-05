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
		if ( itemKey === 'slack' || itemKey === 'discord' ) {
			setItemData( apiData.sct_options[ itemKey ].webhook_url );
		} else if ( itemKey === 'chatwork' ) {
			switch ( optionName ) {
				case 'api_token':
					setItemData( apiData.sct_options.chatwork.api_token );
					break;
				case 'room_id':
					setItemData( apiData.sct_options.chatwork.room_id );
					break;
			}
		}
	}, [ optionName, apiData ] );

	const changeValue = ( value: string ) => {
		const newItem: apiType = JSON.parse( JSON.stringify( { ...apiData } ) );

		if (
			( itemKey === 'slack' || itemKey === 'discord' ) &&
			optionName === 'webhook_url'
		) {
			newItem.sct_options[ itemKey ][ optionName ] = value;
		} else if (
			itemKey === 'chatwork' &&
			( optionName === 'api_token' || optionName === 'room_id' )
		) {
			newItem.sct_options[ itemKey ][ optionName ] = value;
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
