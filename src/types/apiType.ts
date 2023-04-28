import { TokenItem } from '@wordpress/components/build-types/form-token-field/types';

export type apiType = {
	slack: ChatToolsBaseType & ChatToolsType1Type;
	discord: ChatToolsBaseType & ChatToolsType1Type;
	chatwork: ChatToolsBaseType & ChatworkType;
	version: string;
	cron_time: string;
	ignore_key: ( string|TokenItem )[];
	login_notify: boolean;
	check_rinker_exists_items_cron: string;
	logs: {
		[ key: string ]: SctLogsType;
	};
};

export type ChatToolsBaseType = {
	use: boolean;
	send_author: boolean;
	send_update: boolean;
	login_notify: boolean;
	rinker_notify: boolean;
	log: ChatLogType;
};

export type ChatLogType = {
	[ key: string ]: {
		author: string,
		comment: string,
		email: string,
		status: number,
		url: string,
	}
}

// Slack, Discord
export type ChatToolsType1Type = {
	webhook_url: string;
};

export type ChatworkType = {
	api_token: string;
	room_id: string;
};

export type SctLogsType = {
	status: number;
	tool: string;
	type: string;
	send_date: string;
};

export type itemKeyType = keyof Omit<apiType, 'logs'>;

export type ChatToolsItemKeyType = Extract< itemKeyType, 'slack' | 'discord' | 'chatwork' >;

export type useSetApiType = {
	( itemKey: itemKeyType, value: apiType | undefined ): void
};
