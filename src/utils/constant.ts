import { ChatToolsBaseType } from 'src/types/apiType';

export const PREFIX = 'sct';

export const addPrefix = ( value: string ): string => {
	return PREFIX + '_' + value;
};

export const isOptionNameRinker = ( optionName: Exclude<keyof ChatToolsBaseType, 'log'> ) => {
	return 'rinker_notify' === optionName ? true : false;
};
