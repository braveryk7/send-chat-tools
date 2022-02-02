export type apiType = {
	sct_use_slack?: boolean, // eslint-disable-line
	sct_use_discord?: boolean, // eslint-disable-line
	sct_use_chatwork?: boolean, // eslint-disable-line
};

export type WPApiType< T > = {
	[ key: string ]: { // eslint-disable-line
		[ key: string ]: T;
	};
};
export type useSetApiType = {
	( itemKey: string, value: string | boolean | {} ): void;
};
