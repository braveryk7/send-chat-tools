import { Dispatch, SetStateAction } from 'react';

import apiFetch from '@wordpress/api-fetch';
import { useEffect } from '@wordpress/element';

import { apiType } from 'src/types/apiType';

export const useGetApi = (
	stateFunc: Dispatch< SetStateAction< apiType | undefined > >,
	setApiError: Dispatch< SetStateAction< boolean > >,
	setIsRinkerExists: Dispatch< SetStateAction< boolean > >,
) => {
	useEffect( () => {
		apiFetch< apiType >(
			{ path: '/send-chat-tools/v1/options' }
		).then( ( response ) => {
			setApiError( false );
			stateFunc( response );
		} ).catch( () => {
			setApiError( true );
		} );
	}, [ stateFunc, setApiError ] );

	useEffect( () => {
		apiFetch< boolean >(
			{ path: '/send-chat-tools/v1/get-rinker-activated' }
		).then( ( response ) => {
			setApiError( false );
			setIsRinkerExists( response );
		} ).catch( () => {
			setApiError( true );
		} );
	}, [ setIsRinkerExists, setApiError ] );
};
