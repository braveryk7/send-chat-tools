import {
	apiType,
	ChatToolsBaseType,
	ChatToolsType1Type,
	ChatworkType,
} from '../types/apiType';

export const PREFIX = 'sct';

export const addPrefix = ( value: string ): string => {
	return PREFIX + '_' + value;
};

export const getApiInitValue = () => {
	const ChatoToolsBase: ChatToolsBaseType = {
		use: false,
		send_author: false,
		send_update: false,
		log: {},
	};

	const ChatToolsType1: ChatToolsType1Type = {
		webhook_url: '',
	};

	const Chatwork: ChatworkType = {
		api_token: '',
		room_id: '',
	};

	const sctOptions: apiType = {
		sct_options: {
			slack: { ...ChatoToolsBase, ...ChatToolsType1 },
			discord: { ...ChatoToolsBase, ...ChatToolsType1 },
			chatwork: { ...ChatoToolsBase, ...Chatwork },
			version: 0,
			cron_time: '',
		},
		sct_logs: {},
	};

	return sctOptions;
};
