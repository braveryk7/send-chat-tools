import { Dispatch, SetStateAction } from 'react';

import { apiType } from 'src/types/apiType';

export type noticeValueType = 'sct_success' | 'sct_error';

export type apiContextType = {
	apiData: apiType | undefined;
	setApiData: Dispatch< SetStateAction< apiType | undefined > >;
	setApiError: Dispatch< SetStateAction< boolean > >;
	setNoticeValue: Dispatch< SetStateAction< noticeValueType | undefined > >;
	setNoticeMessage: Dispatch< SetStateAction< string > >;
	snackbarTimer: number;
	setSnackbarTimer: Dispatch< SetStateAction< number > >;
	isRinkerExists: boolean;
};
