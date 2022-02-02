export const PREFIX = 'sct';

export const addPrefix = ( value: string ): string => {
	return PREFIX + '_' + value;
};
