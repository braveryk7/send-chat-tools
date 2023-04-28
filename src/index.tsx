import './scss/index.scss';

import { Snackbar, Spinner } from '@wordpress/components';
import { createContext, render, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { ApiError } from 'src/components/atoms/ApiError';
import { Tab } from 'src/components/organisms/Tab';
import { useGetApi } from 'src/hooks/useGetApi';

import { apiType } from 'src/types/apiType';
import { apiContextType, noticeValueType } from 'src/types/useContextType';

export const apiContext = createContext( {} as apiContextType );

const AdminPage = () => {
	const [ apiData, setApiData ] = useState< apiType | undefined >( undefined );
	const [ apiError, setApiError ] = useState( false );
	const [ noticeValue, setNoticeValue ] = useState< noticeValueType | undefined >( undefined );
	const [ noticeMessage, setNoticeMessage ] = useState( '' );
	const [ snackbarTimer, setSnackbarTimer ] = useState( 0 );
	const [ isRinkerExists, setIsRinkerExists ] = useState( false );

	useGetApi( setApiData, setApiError, setIsRinkerExists );

	useEffect( () => {
		if ( noticeValue ) {
			setSnackbarTimer(
				window.setTimeout( () => {
					setNoticeValue( undefined );
				}, 4000 )
			);
		}
	}, [ noticeValue ] );

	return (
		<div id="sct-wrap">
			<h1>{ __( 'Send Chat Tools Settings', 'send-chat-tools' ) }</h1>
			{ noticeValue && (
				<Snackbar className={ noticeValue }>{ noticeMessage }</Snackbar>
			) }
			{ apiData && (
				<apiContext.Provider
					value={ {
						apiData,
						setApiData,
						setApiError,
						setNoticeValue,
						setNoticeMessage,
						snackbarTimer,
						setSnackbarTimer,
						isRinkerExists,
					} }
				>
					<Tab />
				</apiContext.Provider>
			) }
			{ ! apiData && ! apiError && <Spinner /> }
			{ apiError && <ApiError /> }
		</div>
	);
};

render( <AdminPage />, document.getElementById( 'send-chat-tools-settings' ) );
