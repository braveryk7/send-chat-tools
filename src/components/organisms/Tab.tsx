import { TabPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { BasicSettingsTabItems } from 'src/components/molecules/BasicSettingsTabItems';
import { TabItems } from 'src/components/molecules/TabItems';

export const Tab = () => {
	const tabs = [
		{
			name: 'basic',
			title: __( 'Basic Settings', 'send-chat-tools' ),
			className: 'tab-basic',
		},
		{ name: 'slack', title: __( 'Slack', 'send-chat-tools' ), className: 'tab-slack' },
		{ name: 'discord', title: __( 'Discord', 'send-chat-tools' ), className: 'tab-discord' },
		{ name: 'chatwork', title: __( 'Chatwork', 'send-chat-tools' ), className: 'tab-chatwork' },
		{
			name: 'update',
			title: __( 'Update notify', 'send-chat-tools' ),
			className: 'tab-update',
		},
		{ name: 'logs', title: __( 'Logs', 'send-chat-tools' ), className: 'tab-logs' },
	];

	return (
		<TabPanel activeClass="active-tab" className="settings-tab" tabs={ tabs }>
			{ ( tab ) => (
				tab.name !== 'basic'
					? <TabItems id={ tab.name } title={ tab.title } />
					: <BasicSettingsTabItems id={ tab.name } title={ tab.title } />
			) }
		</TabPanel>
	);
};
