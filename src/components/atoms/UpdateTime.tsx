import { __ } from '@wordpress/i18n';

import { useChangeValue } from 'src/hooks/useChangeValue';

export const UpdateTime = ( props: {itemKey: 'cron_time'} ) => {
	const { itemKey } = props;
	const { apiData, changeValue } = useChangeValue( itemKey );

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
						onChange={ ( newTime ) => changeValue( newTime ) }
					/>
					{ __( 'Set time to check for updates', 'send-chat-tools' ) }
				</label>
			) }
		</>
	);
};
