// @ts-ignore
import api from '@wordpress/api';
import { useContext, useEffect, useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { apiContext } from 'src/index';
import { addPrefix } from 'src/utils/constant';

import { useSetApiType } from 'src/types/apiType';

export const useSetApi: useSetApiType = ( itemKey, value ) => {
	const {
		apiData,
		setNoticeStatus,
		setNoticeValue,
		setNoticeMessage,
		snackbarTimer,
	} = useContext( apiContext );

	const isFirstRender = useRef( true );

	useEffect( () => {
		if ( isFirstRender.current ) {
			isFirstRender.current = false;
		} else {
			api.loadPromise.then( () => {
				const model = new api.models.Settings( {
					[ itemKey ]: value,
				} );
				const save = model.save();

				setNoticeStatus( false );
				clearTimeout( snackbarTimer );

				save.success( () => {
					setNoticeStatus( true );
					setNoticeValue( addPrefix( 'success' ) as 'sct_success' );
					setNoticeMessage( __( 'Success.', 'send-chat-tools' ) );
				} );
				save.error( () => {
					setNoticeStatus( true );
					setNoticeValue( addPrefix( 'error' ) as 'sct_error' );
					setNoticeMessage( __( 'Error.', 'send-chat-tools' ) );
				} );
			} );
		}
	}, [ apiData ] );
};
