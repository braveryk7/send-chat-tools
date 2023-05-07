import { Tip } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { TokenField } from 'src/components/atoms/TokenField';
import { UpdateTime } from 'src/components/atoms/UpdateTime';
import { useTabItems } from 'src/hooks/useTabItems';

import { TabItemsType } from 'src/types/ComponentsType';

export const BasicSettingsTabItems = ( props: TabItemsType ) => {
	const { titleText } = useTabItems( props );

	const useWpCron = __(
		'Update and Rinker notifications are made using wp-cron.', 'send-chat-tools'
	);
	const wpCronNotifyTime = __(
		'wp-cron will be executed on the first access after this time.', 'send-chat-tools'
	);

	return (
		<div className="sct-wrapper">
			<h3>{ titleText }</h3>
			<div className="sct-items">
				<UpdateTime
					itemKey="cron_time"
					title={ __( 'Set time to check for updates', 'send-chat-tools' ) }
					id="cron_time"
					message={
						__(
							'Time to send Updates notifications',
							'send-chat-tools'
						)
					}

				/>
			</div>
			<div className="sct-items">
				<UpdateTime
					itemKey="rinker_cron_time"
					title={ __( 'Set time to check for Rinker exists items', 'send-chat-tools' ) }
					id="rinker_cron_time"
					message={
						__(
							'Time to send Rinker end-of-sale notifications',
							'send-chat-tools'
						)
					}

				/>
			</div>
			<div className="sct-items">
				<Tip>{ useWpCron + ' ' + wpCronNotifyTime }</Tip>
			</div>
			<div className="sct-items">
				<TokenField
					itemKey="ignore_key"
					title={
						__(
							'Theme/plugin key to remove update notifications',
							'send-chat-tools'
						)
					}
				/>
			</div>
		</div>
	);
};
