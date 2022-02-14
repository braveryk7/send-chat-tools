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
				switch ( itemKey ) {
					case 'slack':
						setErrorMessage(
							__(
								'Please enter the correct value',
								'send-chat-tools'
							)
						);
						return /^https:\/\/hooks.slack.com\/services\/.*\/.*/;
					case 'discord':
						setErrorMessage(
							__(
								'Please enter the correct value',
								'send-chat-tools'
							)
						);
						return /^https:\/\/discord.com\/api\/webhooks\/\d{4,}\/.*/;
					default:
						setErrorMessage(
							__(
								'Please enter the correct value',
								'send-chat-tools'
							)
						);
						return /.*/;
				}
			};
			const validate = pattern().test( value );
			if ( validate ) {
				setValidateFlag( true );
			} else {
				setValidateFlag( false );
			}
		}
	}, [ value, itemKey ] );

	return { validateFlag, errorMessage };
};
