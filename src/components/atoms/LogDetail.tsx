import { Card, CardHeader, CardBody, CardFooter } from '@wordpress/components';
import { useContext, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { apiContext } from 'src/index';

import { ChatLogType, ChatToolsItemKeyType } from 'src/types/apiType';

export const LogDetail = ( props: { itemKey: ChatToolsItemKeyType } ) => {
	const { itemKey } = props;
	const { apiData } = useContext( apiContext );
	const [ logs, setLogs ] = useState< ChatLogType | undefined >( undefined );

	useEffect( () => {
		if ( apiData ) {
			setLogs( apiData[ itemKey ].log );
		}
	}, [ itemKey, apiData ] );

	return (
		<div className="sct-tools-log-wrapper">
			<h4>
				{ `${ __( 'Logs', 'send-chat-tools' ) } (
				${ __( 'Last three cases', 'send-chat-tools' ) } )` }
			</h4>
			{ logs &&
				Object.entries( logs ).map( ( [ key, value ], i ) => (
					<Card className="sct-tools-log" key={ i }>
						<CardHeader>
							{ `${ __(
								'Commenter',
								'send-chat-tools'
							) }: ${ value.commenter } <${ value.email }>` }
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
