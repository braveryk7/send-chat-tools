import { FormTokenField } from '@wordpress/components';

import { useChangeValue } from 'src/hooks/useChangeValue';

export const TokenField = ( props: { itemKey: 'ignore_key', title: string } ) => {
	const { itemKey, title } = props;
	const { apiData, changeValue } = useChangeValue( itemKey );

	return (
		<>
			<h4>{ title }</h4>
			{ apiData &&
				<FormTokenField
					value={ apiData.ignore_key }
					onChange={ ( tokens: any ) => changeValue( tokens ) }
				/>
			}
		</>
	);
};
