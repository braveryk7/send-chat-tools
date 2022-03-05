import { ChangeEvent } from 'react';

import { useContext, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { useSetApi } from 'src/hooks/useSetApi';
import { apiContext } from 'src/index';

import { apiType } from 'src/types/apiType';

export const UpdateTime = () => {
	const { apiData, setApiData } = useContext( apiContext );
	const [ time, setTime ] = useState( apiData.sct_options.cron_time );

	useEffect( () => {
		setTime( apiData.sct_options.cron_time );
	}, [ apiData ] );

	const changeTime = ( newTime: ChangeEvent< HTMLInputElement > ) => {
		const newItem: apiType = JSON.parse( JSON.stringify( { ...apiData } ) );

		newItem.sct_options.cron_time = newTime.target.value;
		setApiData( newItem );
	};

	useSetApi( 'sct_options', apiData.sct_options );

	return (
		<>
			<p>{ __( 'Set time to check for updates', 'send-chat-tools' ) }</p>
			<input
				type="time"
				value={ time }
				onChange={ ( newTime ) => changeTime( newTime ) }
			></input>
		</>
	);
};
