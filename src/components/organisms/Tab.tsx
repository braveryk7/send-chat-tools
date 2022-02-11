import { TabPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { Items } from '../molecules/TabItems';

export const Tab = () => {
	const tabs = [
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
	];
	return (
		<TabPanel
			activeClass="active-tab"
			className="settings-tab"
			tabs={ tabs }
		>
			{ ( tab ) => <Items id={ tab.name } title={ tab.title } /> }
		</TabPanel>
	);
};
