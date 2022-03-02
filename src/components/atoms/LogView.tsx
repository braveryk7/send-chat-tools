import { Card, CardHeader, CardBody, CardFooter } from '@wordpress/components';
import { useContext } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { apiContext } from '../..';
import { SctLogsType } from '../../types/apiType';

export const LogView = () => {
	const { apiData } = useContext( apiContext );

	const getTools = ( toolId: string ) => {
		switch ( toolId ) {
			case '1':
				return 'Slack';
			case '2':
				return 'Discord';
			case '3':
				return 'Chatwork';
			default:
				return null;
		}
	};

	const getTypes = ( typeId: string ) => {
		switch ( typeId ) {
			case '1':
				return 'Comment';
			case '2':
				return 'Update';
			case '3':
				return 'Developer';
			default:
				return null;
		}
	};

	const getStatusMessage = ( status: number ) => {
		const addSpanTag = ( value: string, sendStatus: boolean ) => {
			const classNameAttr = sendStatus ? 'success' : 'error';

			const createClass = (
				<span className={ `res-${ classNameAttr }` }>{ value }</span>
			);

			return createClass;
		};

		switch ( status ) {
			case 200:
			case 204:
				return addSpanTag(
					__( 'Response OK!', 'send-chat-tools' ),
					true
				);
			case 1000:
				return addSpanTag(
					__( 'Could not communicate.', 'send-chat-tools' ),
					false
				);
			case 1001:
				return addSpanTag(
					__(
						'Webhook URL/API Token not entered.',
						'send-chat-tools'
					),
					false
				);
			case 1002:
				return addSpanTag(
					__( 'RoomID not entered.', 'send-chat-tools' ),
					false
				);
			case 1003:
				return addSpanTag(
					__( 'API system value is incorrect.', 'send-chat-tools' ),
					false
				);
			default:
				return addSpanTag(
					__( 'Could not communicate.', 'send-chat-tools' ),
					false
				);
		}
	};

	return (
		<Card className="sct-logs">
			<CardHeader>
				{ __( 'Communication logs.', 'send-chat-tools' ) }
			</CardHeader>
			<CardBody>
				<table className="sct-logs-table">
					<tr>
						<th>{ __( 'Date', 'send-chat-tools' ) }</th>
						<th>{ __( 'Tool', 'send-chat-tools' ) }</th>
						<th>{ __( 'Type', 'send-chat-tools' ) }</th>
						<th>{ __( 'Status', 'send-chat-tools' ) }</th>
						<th>{ __( 'Detail', 'send-chat-tools' ) }</th>
					</tr>
					{ Object.values( apiData.sct_logs ).map(
						( log: SctLogsType, i ) => (
							<tr key={ i }>
								<td>{ log.send_date }</td>
								<td>{ getTools( log.tool ) }</td>
								<td>{ getTypes( log.type ) }</td>
								<td>{ log.status }</td>
								<td>{ getStatusMessage( log.status ) }</td>
							</tr>
						)
					) }
				</table>
			</CardBody>
			<CardFooter>
				<p>Total { Object.keys( apiData.sct_logs ).length } items.</p>
			</CardFooter>
		</Card>
	);
};
