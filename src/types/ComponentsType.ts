import {
	ChatToolsBaseType,
	ChatToolsType1Type,
	ChatworkType,
	itemKeyType,
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
