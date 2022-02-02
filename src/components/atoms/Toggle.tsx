import { ToggleControl } from '@wordpress/components';
import { useContext, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { apiContext } from '../..';
import { useSetApi } from '../../hooks/useSetApi';
import { TogglePropsType } from '../../types/ComponentsType';
import { apiType } from '../../types/apiType';
import { addPrefix } from '../../utils/constant';

export const Toggle = ( props: TogglePropsType ) => {
	const { itemKey, label } = props;
	const { apiData, setApiData } = useContext( apiContext );
	const [ itemData, setItemData ] = useState(
		{} as { checked: boolean; setApiValue: boolean }
	);

	useEffect( () => {
		switch ( itemKey ) {
			case addPrefix( 'use_slack' ):
				setItemData( {
					checked: apiData.sct_use_slack!,
					setApiValue: apiData.sct_use_slack!,
				} );
				break;
			case addPrefix( 'use_discord' ):
				setItemData( {
					checked: apiData.sct_use_discord!,
					setApiValue: apiData.sct_use_discord!,
				} );
				break;
			case addPrefix( 'use_chatwork' ):
				setItemData( {
					checked: apiData.sct_use_chatwork!,
					setApiValue: apiData.sct_use_chatwork!,
				} );
				break;
			case addPrefix( 'send_slack_author' ):
				setItemData( {
					checked: apiData.sct_send_slack_author!,
					setApiValue: apiData.sct_send_slack_author!,
				} );
				break;
			case addPrefix( 'send_discord_author' ):
				setItemData( {
					checked: apiData.sct_send_discord_author!,
					setApiValue: apiData.sct_send_discord_author!,
				} );
				break;
			case addPrefix( 'send_chatwork_author' ):
				setItemData( {
					checked: apiData.sct_send_chatwork_author!,
					setApiValue: apiData.sct_send_chatwork_author!,
				} );
				break;
			case addPrefix( 'send_slack_update' ):
				setItemData( {
					checked: apiData.sct_send_slack_update!,
					setApiValue: apiData.sct_send_slack_update!,
				} );
				break;
			case addPrefix( 'send_discord_update' ):
				setItemData( {
					checked: apiData.sct_send_discord_update!,
					setApiValue: apiData.sct_send_discord_update!,
				} );
				break;
			case addPrefix( 'send_chatwork_update' ):
				setItemData( {
					checked: apiData.sct_send_chatwork_update!,
					setApiValue: apiData.sct_send_chatwork_update!,
				} );
				break;
		}
	}, [ itemKey, apiData ] );

	const changeStatus = ( status: boolean ) => {
		const newItem: apiType = JSON.parse( JSON.stringify( { ...apiData } ) );

		newItem[ itemKey ] = status;
		setApiData( newItem );
	};

	useSetApi( itemKey, apiData[ itemKey ]! );

	return (
		<ToggleControl
			label={ label }
			checked={ itemData.checked }
			onChange={ ( status ) => {
				changeStatus( status );
			} }
		/>
	);
};
