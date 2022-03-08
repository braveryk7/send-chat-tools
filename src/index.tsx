import './scss/index.scss';

import { Snackbar, Spinner } from '@wordpress/components';
import { createContext, render, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { Tab } from 'src/components/organisms/Tab';
import { useGetApi } from 'src/hooks/useGetApi';

import { apiType } from 'src/types/apiType';
import { apiContextType, noticeValueType } from 'src/types/useContextType';

export const apiContext = createContext( {} as apiContextType );

const AdminPage = () => {
	const [ apiData, setApiData ] = useState< apiType | null >( null );
	const [ noticeValue, setNoticeValue ] = useState< noticeValueType >( null );
	const [ noticeMessage, setNoticeMessage ] = useState( '' );
	const [ snackbarTimer, setSnackbarTimer ] = useState( 0 );

	useGetApi( setApiData );

	useEffect( () => {
		if ( noticeValue ) {
			setSnackbarTimer(
				window.setTimeout( () => {
					setNoticeValue( null );
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
			{ apiData ? (
				<apiContext.Provider
					value={ {
						apiData,
						setApiData,
						setNoticeValue,
						setNoticeMessage,
						snackbarTimer,
					} }
				>
					<Tab />
				</apiContext.Provider>
			) : (
				<Spinner />
			) }
		</div>
	);
};

render( <AdminPage />, document.getElementById( 'send-chat-tools-settings' ) );
