import { useContext, useEffect, useState } from '@wordpress/element';

import { RinkerNotActive } from 'src/components/atoms/RinkerNotActive';
import { useChangeValue } from 'src/hooks/useChangeValue';
import { apiContext } from 'src/index';

type timeItemKey = 'cron_time' | 'rinker_cron_time';

export const UpdateTime = (
	props: { itemKey: timeItemKey, title: string, id: timeItemKey, message: string, }
) => {
	const { itemKey, title, id, message } = props;
	const { apiData, changeValue } = useChangeValue( itemKey );
	const [ inputValue, setInputValue ] = useState( '' );
	const { isRinkerActivated } = useContext( apiContext );

	useEffect( () => {
		if ( apiData ) {
			if ( 'cron_time' === itemKey ) {
				setInputValue( apiData.cron_time );
			} else if ( 'rinker_cron_time' === itemKey ) {
				setInputValue( apiData.rinker_cron_time );
			}
		}
	}, [ apiData, itemKey ] );

	const isItemKeyNameRinker = ( itemKeyName: timeItemKey ): boolean => {
		return 'rinker_cron_time' === itemKeyName ? true : false;
	};

	return (
		<>
			<h4>{ title }</h4>
			{ apiData && (
				<input
					id={ id }
					className="update-time"
					type="time"
					disabled={ isItemKeyNameRinker( itemKey ) && ! isRinkerActivated }
					value={ inputValue }
					onChange={ ( newTime ) => changeValue( newTime ) }
				/>
			) }
			{ message }
			{
				isItemKeyNameRinker( itemKey ) && ! isRinkerActivated && <RinkerNotActive />
			}
		</>
	);
};
