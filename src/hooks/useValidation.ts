import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

export const useValidation = (
	value: string,
	itemKey: string
): { validateFlag: boolean; errorMessage: string } => {
	const [ validateFlag, setValidateFlag ] = useState( false );
	const [ errorMessage, setErrorMessage ] = useState( '' );

	useEffect( () => {
		if ( '' !== value ) {
			const pattern = () => {
				setErrorMessage( __( 'Please enter the correct value', 'send-chat-tools' ) );
				if ( 'slack' === itemKey ) {
					return /^https:\/\/hooks.slack.com\/services\/.*\/.*/;
				} else if ( 'discord' === itemKey ) {
					return /^https:\/\/discord.com\/api\/webhooks\/\d{4,}\/.*/;
				}
				return /.*/;
			};

			const validate = pattern().test( value );
			setValidateFlag( validate ? true : false );
		}
	}, [ value, itemKey ] );

	return { validateFlag, errorMessage };
};
