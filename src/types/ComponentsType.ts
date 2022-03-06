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
