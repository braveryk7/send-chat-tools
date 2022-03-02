import { Card, CardHeader, CardBody, CardFooter } from '@wordpress/components';
import { useContext, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { ChatLogType } from 'types/apiType';

import { apiContext } from '../..';
import { itemKeyType } from '../../types/ComponentsType';

export const LogDetail = ( props: { itemKey: itemKeyType } ) => {
	const { itemKey } = props;
	const { apiData } = useContext( apiContext );
	const [ logs, setLogs ] = useState< ChatLogType | undefined >( undefined );

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
				Object.entries( logs ).map( ( [ key, value ], i ) => (
					<Card className="sct-tools-log" key={ i }>
						<CardHeader>
							{ `${ __(
								'Comment Author',
								'send-chat-tools'
							) }: ${ value.author } <${ value.email }>` }
						</CardHeader>
						<CardBody className="sct-tools-log-body">
							{ value.comment }
						</CardBody>
						<CardFooter className="sct-tools-log-footer">
							<span className="sct-tools-log-footer-comment_id">
								{ `${ __(
									'Comment ID',
									'send-chat-tools'
								) }: ${ value.comment }` }
							</span>
							<span className="sct-tools-log-footer-status">
								{ `${ __( 'Status', 'send-chat-tools' ) }: ${
									value.status
								}` }
							</span>
							<span className="sct-tools-log-footer-date">
								{ `${ __(
									'Date',
									'send-chat-tools'
								) }: ${ key }` }
							</span>
						</CardFooter>
					</Card>
				) ) }
		</div>
	);
};
