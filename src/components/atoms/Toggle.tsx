import { ToggleControl } from '@wordpress/components';
import { useContext, useEffect, useState } from '@wordpress/element';

import { useSetApi } from 'src/hooks/useSetApi';
import { apiContext } from 'src/index';

import { TogglePropsType } from 'src/types/ComponentsType';
import { apiType } from 'src/types/apiType';

export const Toggle = ( props: TogglePropsType ) => {
	const { itemKey, optionName, label } = props;
	const { apiData, setApiData } = useContext( apiContext );
	const [ itemData, setItemData ] = useState( false );

	useEffect( () => {
		if ( apiData ) {
			setItemData( apiData[ itemKey ][ optionName ] );
		}
	}, [ itemKey, optionName, apiData ] );

	const changeStatus = ( status: boolean ) => {
		const newItem: apiType = JSON.parse(
			JSON.stringify( { ...apiData } )
		);

		newItem[ itemKey ][ optionName ] = status;
		setApiData( newItem );
	};

	useSetApi( itemKey, apiData );

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
