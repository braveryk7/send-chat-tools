export type apiType = {
	sct_use_slack?: boolean, // eslint-disable-line
	sct_send_slack_author?: boolean, // eslint-disable-line
	sct_send_slack_update?: boolean, // eslint-disable-line
	sct_use_discord?: boolean, // eslint-disable-line
	sct_send_discord_author?: boolean, // eslint-disable-line
	sct_send_discord_update?: boolean, // eslint-disable-line
	sct_use_chatwork?: boolean, // eslint-disable-line
	sct_send_chatwork_author?: boolean, // eslint-disable-line
	sct_send_chatwork_update?: boolean, // eslint-disable-line
};

export type WPApiType< T > = {
	[ key: string ]: { // eslint-disable-line
		[ key: string ]: T;
	};
};
export type useSetApiType = {
	( itemKey: string, value: string | boolean | {} ): void;
};
