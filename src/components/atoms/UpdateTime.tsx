import { useChangeValue } from 'src/hooks/useChangeValue';
type timeItemKey = 'cron_time' | 'check_rinker_exists_items_cron';

export const UpdateTime = (
	props: { itemKey: timeItemKey, title: string, id: timeItemKey, message: string, }
) => {
	const { itemKey, title, id, message } = props;
	const { apiData, changeValue } = useChangeValue( itemKey );

	return (
		<>
			<h4>{ title }</h4>
			{ apiData && (
				<input
					id={ id }
					className="update-time"
					type="time"
					value={ apiData.cron_time }
					onChange={ ( newTime ) => changeValue( newTime ) }
				/>
			) }
			{ message }
		</>
	);
};
