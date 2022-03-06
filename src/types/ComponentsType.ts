import { itemKeyType } from 'src/types/apiType';

export type TabItemsType = {
	id: string;
	title: string;
};

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

export type LogsType = {
	[ key: string ]: {
		id: string;
		author: string;
		email: string;
		url: string;
		comment: string;
		status: number;
	};
};
