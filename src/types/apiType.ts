export type apiType = {
	sct_options: {
		slack: ChatToolsBaseType & ChatToolsType1Type;
		discord: ChatToolsBaseType & ChatToolsType1Type;
		chatwork: ChatToolsBaseType & ChatworkType;
		db_version: string;
		iv: string;
		user_id: string;
		cron_time: string;
	};
	sct_logs: {
		[ key: string ]: SctLogsType;
	};
};

export type ChatToolsBaseType = {
	use: boolean;
	send_author: boolean;
	send_update: boolean;
	log: {};
};

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

export type WPApiType< T > = {
	[ key: string ]: { // eslint-disable-line
		[ key: string ]: T;
	};
};
export type useSetApiType = {
	( itemKey: string, value: string | boolean | {} ): void;
};
