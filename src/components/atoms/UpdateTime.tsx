import { useEffect, useState } from '@wordpress/element';

import { useChangeValue } from 'src/hooks/useChangeValue';
type timeItemKey = 'cron_time' | 'check_rinker_exists_items_cron';

export const UpdateTime = (
	props: { itemKey: timeItemKey, title: string, id: timeItemKey, message: string, }
) => {
	const { itemKey, title, id, message } = props;
	const { apiData, changeValue } = useChangeValue( itemKey );
	const [ inputValue, setInputValue ] = useState( '' );

	useEffect( () => {
		if ( apiData ) {
			if ( 'cron_time' === itemKey ) {
				setInputValue( apiData.cron_time );
			} else if ( 'check_rinker_exists_items_cron' === itemKey ) {
				setInputValue( apiData.check_rinker_exists_items_cron );
			}
		}
	}, [ apiData, itemKey ] );

	return (
		<>
			<h4>{ title }</h4>
			{ apiData && (
				<input
					id={ id }
					className="update-time"
					type="time"
					value={ inputValue }
					onChange={ ( newTime ) => changeValue( newTime ) }
				/>
			) }
			{ message }
		</>
	);
};
