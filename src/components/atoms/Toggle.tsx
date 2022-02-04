import { ToggleControl } from '@wordpress/components';
import { useContext, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { apiContext } from '../..';
import { useSetApi } from '../../hooks/useSetApi';
import { TogglePropsType } from '../../types/ComponentsType';
import { apiType } from '../../types/apiType';

export const Toggle = ( props: TogglePropsType ) => {
	const { itemKey, optionName, label } = props;
	const { apiData, setApiData } = useContext( apiContext );
	const [ itemData, setItemData ] = useState( false );

	useEffect( () => {
		if (
			itemKey === 'slack' ||
			itemKey === 'discord' ||
			itemKey === 'chatwork'
		) {
			switch ( optionName ) {
				case 'use':
					setItemData( apiData.sct_options[ itemKey ].use );
					break;
				case 'send_author':
					setItemData( apiData.sct_options[ itemKey ].send_author );
					break;
				case 'send_update':
					setItemData( apiData.sct_options[ itemKey ].send_update );
					break;
			}
		}
	}, [ itemKey, optionName, apiData ] );

	const changeStatus = ( status: boolean ) => {
		if (
			itemKey === 'slack' ||
			itemKey === 'discord' ||
			itemKey === 'chatwork'
		) {
			const newItem: apiType = JSON.parse(
				JSON.stringify( { ...apiData } )
			);

			newItem.sct_options[ itemKey ][ optionName ] = status;
			setApiData( newItem );
		}
	};

	useSetApi( 'sct_options', apiData.sct_options );

	return (
		<ToggleControl
			label={ label }
			checked={ itemData }
			onChange={ ( status ) => {
				changeStatus( status );
			} }
		/>
	);
};
