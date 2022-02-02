import { Dispatch, SetStateAction } from 'react';

// @ts-ignore
import api from '@wordpress/api'; // eslint-disable-line
import { useEffect } from '@wordpress/element';

import { apiType, WPApiType } from '../types/apiType';

export const useGetApi = (
	stateFunc: Dispatch< SetStateAction< apiType > >,
	setApiStatus: Dispatch< SetStateAction< boolean > >
) => {
	useEffect( () => {
		api.loadPromise.then( () => {
			const model = new api.models.Settings();

			model.fetch().then( ( res: WPApiType< apiType > ) => {
				stateFunc( res );
				setApiStatus( true );
			} );
		} );
	}, [ stateFunc ] );
};
