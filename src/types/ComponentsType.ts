export type itemKeyType = 'slack' | 'discord' | 'chatwork' | 'basic';

export type optionNameType = 'use' | 'send_author' | 'send_update';

export type TogglePropsType = {
	itemKey: itemKeyType;
	optionName: optionNameType;
	label: string;
};
