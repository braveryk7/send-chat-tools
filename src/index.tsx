import './scss/index.scss';

import { Placeholder, Snackbar, Spinner } from '@wordpress/components';
import { createContext, render, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { Tab } from './components/organisms/Tab';
import { useGetApi } from './hooks/useGetApi';
import { apiType } from './types/apiType';
import { apiContextType, noticeValueType } from './types/useContextType';

export const apiContext = createContext( {} as apiContextType );

const AdminPage = () => {
	const [ apiData, setApiData ] = useState< apiType >( {} );
	const [ apiStatus, setApiStatus ] = useState( false );
	const [ noticeStatus, setNoticeStatus ] = useState( false );
	const [ noticeValue, setNoticeValue ] = useState(
		undefined as noticeValueType
	);
	const [ noticeMessage, setNoticeMessage ] = useState( '' );
	const [ snackbarTimer, setSnackbarTimer ] = useState(
		setTimeout( () => {} )
	);
	useGetApi( setApiData, setApiStatus );

	useEffect( () => {
		if ( noticeStatus ) {
			setSnackbarTimer(
				setTimeout( () => {
					setNoticeStatus( false );
				}, 4000 )
			);
		}
	}, [ noticeStatus ] );

	return (
		<div id="sct-wrap">
			<h1>{ __( 'Send Chat Tools Settings', 'send-chat-tools' ) }</h1>
			{ noticeStatus && (
				<Snackbar className={ noticeValue }>{ noticeMessage }</Snackbar>
			) }
			{ apiStatus ? (
				<apiContext.Provider
					value={ {
						apiData,
						setApiData,
						setNoticeStatus,
						setNoticeValue,
						setNoticeMessage,
						snackbarTimer,
					} }
				>
					<Tab />
				</apiContext.Provider>
			) : (
				<Placeholder label={ __( 'Data loading', 'admin-bar-tools' ) }>
					<Spinner />
				</Placeholder>
			) }
		</div>
	);
};

render( <AdminPage />, document.getElementById( 'send-chat-tools-settings' ) );
