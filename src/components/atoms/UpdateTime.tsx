import { ChangeEvent } from 'react';

import { useContext } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { useSetApi } from 'src/hooks/useSetApi';
import { apiContext } from 'src/index';

import { apiType } from 'src/types/apiType';

export const UpdateTime = () => {
	const { apiData, setApiData } = useContext( apiContext );

	const changeTime = ( newTime: ChangeEvent< HTMLInputElement > ) => {
		const newItem: apiType = JSON.parse( JSON.stringify( { ...apiData } ) );

		newItem.cron_time = newTime.target.value;
		setApiData( newItem );
	};

	useSetApi( 'cron_time', apiData );

	return (
		<>
			{ apiData && (
				<label
					id="update_time_label"
					htmlFor="update_time"
				>
					<input
						id="update_time"
						className="update-time"
						type="time"
						value={ apiData.cron_time }
						onChange={ ( newTime ) => changeTime( newTime ) }
					/>
					{ __( 'Set time to check for updates', 'send-chat-tools' ) }
				</label>
			) }
		</>
	);
};
