import { Dispatch, SetStateAction } from 'react';

import { apiType } from './apiType';

export type noticeValueType = 'sct_success' | 'sct_error' | undefined;

export type apiContextType = {
	apiData: apiType;
	setApiData: Dispatch< SetStateAction< apiType > >;
	setNoticeStatus: Dispatch< SetStateAction< boolean > >;
	setNoticeValue: Dispatch< SetStateAction< noticeValueType > >;
	setNoticeMessage: Dispatch< SetStateAction< string > >;
	snackbarTimer: number;
};
