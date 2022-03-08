import { __ } from '@wordpress/i18n';

import { useChangeValue } from 'src/hooks/useChangeValue';

export const UpdateTime = ( props: { itemKey: 'cron_time', title: string } ) => {
	const { itemKey, title } = props;
	const { apiData, changeValue } = useChangeValue( itemKey );

	return (
		<>
			<h4>{ title }</h4>
			{ apiData && (
				<input
					id="update_time"
					className="update-time"
					type="time"
					value={ apiData.cron_time }
					onChange={ ( newTime ) => changeValue( newTime ) }
				/>
			) }
			{ __( 'この時間以降最初のアクセス時にアップデート確認されます。', 'send-chat-tools' ) }
		</>
	);
};
