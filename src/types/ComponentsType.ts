import Dashicon from '@wordpress/components/dashicon';

import {
	ChatToolsBaseType,
	ChatToolsItemKeyType,
	ChatToolsType1Type,
	ChatworkType,
} from 'src/types/apiType';

export type TabItemsType = {
	id: string;
	title: string;
};

export type optionNameType =
	keyof Omit< ChatToolsBaseType, 'log' > |
	keyof ChatToolsType1Type |
	keyof ChatworkType;

export type TogglePropsType = {
	itemKey: ChatToolsItemKeyType;
	optionName: Exclude< keyof ChatToolsBaseType, 'log' >;
	label: string;
};

export type TextControlPropsType = {
	itemKey: ChatToolsItemKeyType;
	optionName: keyof ChatToolsType1Type | keyof ChatworkType;
	label: string;
};

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

export type apiOptionNameType = Extract< optionNameType, 'webhook_url' | 'api_token' >;

export type chatworkRoomIdType = Extract< optionNameType, 'room_id' >;

export type setUseStateType = (
	itemName: ChatToolsItemKeyType,
	label: string,
	optionName: apiOptionNameType,
	title: string,
	text: string,
) => void;

export type itemType = {
	icon: Dashicon.Icon,
	key: string,
	text: string,
}
