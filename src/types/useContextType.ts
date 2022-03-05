import { Dispatch, SetStateAction } from 'react';

import { apiType } from 'src/types/apiType';

export type noticeValueType = 'sct_success' | 'sct_error' | null;

export type apiContextType = {
	apiData: apiType | null;
	setApiData: Dispatch< SetStateAction< apiType | null > >;
	setNoticeStatus: Dispatch< SetStateAction< boolean > >;
	setNoticeValue: Dispatch< SetStateAction< noticeValueType > >;
	setNoticeMessage: Dispatch< SetStateAction< string > >;
	snackbarTimer: number;
};
