import { ChangeEvent } from 'react';

import { useContext, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { apiContext } from '../..';
import { useSetApi } from '../../hooks/useSetApi';
import { apiType } from '../../types/apiType';

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
			<p>
				{ __(
					'アップデートをチェックする時間を設定する',
					'send-chat-tools'
				) }
			</p>
			<input
				type="time"
				value={ time }
				onChange={ ( newTime ) => changeTime( newTime ) }
			></input>
		</>
	);
};
