import apiFetch from '@wordpress/api-fetch';
import { useContext, useEffect, useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { apiContext } from 'src/index';

import { useSetApiType } from 'src/types/apiType';

export const useSetApi: useSetApiType = ( itemKey, value ) => {
	const {
		apiData,
		setNoticeValue,
		setNoticeMessage,
		snackbarTimer,
	} = useContext( apiContext );

	const isFirstRender = useRef( true );
	const apiDataRef = useRef( apiData );

	useEffect( () => {
		if ( isFirstRender.current ) {
			isFirstRender.current = false;
		} else if ( value && apiData !== apiDataRef.current ) {
			setNoticeValue( undefined );
			clearTimeout( snackbarTimer );
			apiDataRef.current = apiData;

			apiFetch( {
				path: '/send-chat-tools/v1/update',
				method: 'POST',
				data: { [ itemKey ]: value[ itemKey ] },
			} ).then( ( ) => {
				setNoticeValue( 'sct_success' );
				setNoticeMessage( __( 'Success', 'send-chat-tools' ) );
			} ).catch( ( ) => {
				setNoticeValue( 'sct_error' );
				setNoticeMessage( __( 'Error', 'send-chat-tools' ) );
			} );
		}
	}, [ apiData, itemKey, value, setNoticeMessage, setNoticeValue, snackbarTimer ] );
};
