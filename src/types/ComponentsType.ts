export type itemKeyType = 'slack' | 'discord' | 'chatwork' | 'basic';

export type optionNameType =
	| 'use'
	| 'send_author'
	| 'send_update'
	| 'webhook_url'
	| 'api_token'
	| 'room_id';

export type TogglePropsType = {
	itemKey: itemKeyType;
	optionName: optionNameType;
	label: string;
};

export type TextControlPropsType = TogglePropsType;
