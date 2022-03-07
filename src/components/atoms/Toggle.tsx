import { ToggleControl } from '@wordpress/components';

import { useChangeValue } from 'src/hooks/useChangeValue';

import { TogglePropsType } from 'src/types/ComponentsType';

export const Toggle = ( props: TogglePropsType ) => {
	const { itemKey, optionName, label } = props;
	const { apiData, changeValue } = useChangeValue( itemKey, optionName );

	return (
		<>
			{ apiData &&
				<ToggleControl
					label={ label }
					checked={ apiData[ itemKey ][ optionName ] }
					onChange={ ( value ) => {
						changeValue( value );
					} }
				/>
			}
		</>
	);
};
