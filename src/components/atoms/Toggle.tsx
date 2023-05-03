import { useContext } from 'react';

import { ToggleControl } from '@wordpress/components';

import { RinkerNotActive } from 'src/components/atoms/RinkerNotActive';
import { useChangeValue } from 'src/hooks/useChangeValue';
import { apiContext } from 'src/index';
import { isOptionNameRinker } from 'src/utils/constant';

import { TogglePropsType } from 'src/types/ComponentsType';

export const Toggle = ( props: TogglePropsType ) => {
	const { itemKey, optionName, label } = props;
	const { apiData, changeValue } = useChangeValue( itemKey, optionName );
	const { isRinkerActivated } = useContext( apiContext );

	return (
		<>
			{ apiData &&
				<ToggleControl
					label={ label }
					checked={ apiData[ itemKey ][ optionName ] }
					disabled={ isOptionNameRinker( optionName ) && ! isRinkerActivated }
					onChange={ ( value ) => {
						changeValue( value );
					} }
				/>
			}
			{
				isOptionNameRinker( optionName ) && ! isRinkerActivated && <RinkerNotActive />
			}
		</>
	);
};
