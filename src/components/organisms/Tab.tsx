import { TabPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { Items } from '../molecules/TabItems';

export const Tab = () => {
	return (
		<TabPanel
			activeClass="active-tab"
			className="settings-tab"
			tabs={ [
				{
					name: 'basic',
					title: __( 'Basic', 'send-chat-tools' ),
					className: 'tab-basic',
				},
				{
					name: 'slack',
					title: 'Slack',
					className: 'tab-slack',
				},
				{
					name: 'discord',
					title: 'Discord',
					className: 'tab-discord',
				},
				{
					name: 'chatwork',
					title: 'Chatwork',
					className: 'tab-chatwork',
				},
				{
					name: 'logs',
					title: 'Logs',
					className: 'tab-logs',
				},
			] }
		>
			{ ( tab ) => <Items id={ tab.name } title={ tab.title } /> }
		</TabPanel>
	);
};
