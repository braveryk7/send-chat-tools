import { Card, CardHeader, CardBody, CardFooter } from '@wordpress/components';
import { useContext, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { apiContext } from '../..';
import { itemKeyType, LogsType } from '../../types/ComponentsType';

export const LogDetail = ( props: { itemKey: itemKeyType } ) => {
	const { itemKey } = props;
	const { apiData } = useContext( apiContext );
	const [ logs, setLogs ] = useState< LogsType | undefined >( undefined );

	useEffect( () => {
		setLogs( apiData.sct_options[ itemKey ].log );
	}, [ itemKey ] );

	return (
		<div className="sct-tools-log-wrapper">
			<h2>
				{ `${ __( 'Logs', 'send-chat-tools' ) } (
				${ __( 'Last three cases', 'send-chat-tools' ) } )` }
			</h2>
			{ logs &&
				Object.entries( logs! ).map( ( [ key, value ], i ) => (
					<Card className="sct-tools-log" key={ i }>
						<CardHeader>
							{ `${ __(
								'Comment Author',
								'send-chat-tools'
							) }: ${ value.author }<${ value.email }>` }
							{ `${ __( 'Status', 'send-chat-tools' ) }: ${
								value.status
							}` }
						</CardHeader>
						<CardBody>{ value.comment }</CardBody>
						<CardFooter>
							{ `${ __( 'Comment ID', 'send-chat-tools' ) }: ${
								value.id
							}` }
							{ `${ __( 'Date', 'send-chat-tools' ) }: ${ key }` }
						</CardFooter>
					</Card>
				) ) }
		</div>
	);
};
