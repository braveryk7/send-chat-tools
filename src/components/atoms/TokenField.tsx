import { FormTokenField } from '@wordpress/components';

import { useChangeValue } from 'src/hooks/useChangeValue';

export const TokenField = ( props: { itemKey: 'ignore_key', title: string } ) => {
	const { itemKey, title } = props;
	const { apiData, changeValue } = useChangeValue( itemKey );

	return (
		<>
			<h3>{ title }</h3>
			{ apiData &&
				<FormTokenField
					value={ apiData.ignore_key }
					onChange={ ( tokens: [] ) => changeValue( tokens ) }
				/>
			}
		</>
	);
};
