import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import {
	itemKeyType,
	optionNameType,
	TogglePropsType,
} from '../../types/ComponentsType';
import { LogDetail } from '../atoms/LogDetail';
import { LogView } from '../atoms/LogView';
import { TextControlForm } from '../atoms/TextControlForm';
import { Toggle } from '../atoms/Toggle';
import { UpdateTime } from '../atoms/UpdateTime';

export const Items = ( props: any ) => {
	const { id } = props;
	const [ updateFlag, setUpdateFlag ] = useState( false );
	const [ logsFlag, setLogsFlag ] = useState( false );
	const [ tabItems, setTabItems ] = useState< TogglePropsType[] >( [] );
	const [ itemKey, setItemKey ] = useState< itemKeyType | null >( null );
	const [ itemLabel, setItemLabel ] = useState( '' );
	const [
		apiOptionName,
		setApiOptionName,
	] = useState< optionNameType | null >( null );
	const [ titleText, setTitleText ] = useState( '' );
	const [ textLabel, setTextLabel ] = useState( '' );
	const [
		chatworkRoomId,
		setChatworkRoomId,
	] = useState< optionNameType | null >( null );
	const [ chatworkText, setChatworkText ] = useState( '' );

	useEffect( () => {
		switch ( id ) {
			case 'slack':
				setItemKey( 'slack' );
				setItemLabel( __( 'Use Slack notify', 'send-chat-tools' ) );
				setApiOptionName( 'webhook_url' );
				setTitleText( __( 'Slack settings', 'send-chat-tools' ) );
				setTextLabel( __( 'Slack Webhook URL', 'send-chat-tools' ) );
				break;
			case 'discord':
				setItemKey( 'discord' );
				setItemLabel( __( 'Use Discord notify', 'send-chat-tools' ) );
				setApiOptionName( 'webhook_url' );
				setTitleText( __( 'Discord settings', 'send-chat-tools' ) );
				setTextLabel( __( 'Discord Webhook URL', 'send-chat-tools' ) );
				break;
			case 'chatwork':
				setItemKey( 'chatwork' );
				setItemLabel( __( 'Use Chatwork notify', 'send-chat-tools' ) );
				setApiOptionName( 'api_token' );
				setChatworkRoomId( 'room_id' );
				setTitleText( __( 'Chatwork settings', 'send-chat-tools' ) );
				setTextLabel( __( 'Chatwork API token', 'send-chat-tools' ) );
				setChatworkText( __( 'Chatwork Room ID', 'send-chat-tools' ) );
				break;
			case 'update':
				setUpdateFlag( true );
				setTitleText(
					__( 'Update notify settings', 'send-chat-tools' )
				);
				break;
			case 'logs':
				setLogsFlag( true );
				setTitleText( __( 'Logs', 'send-chat-tools' ) );
				break;
		}
	}, [ id ] );

	useEffect( () => {
		if ( itemKey ) {
			const items: TogglePropsType[] = [
				{ itemKey, optionName: 'use', label: itemLabel },
				{
					itemKey,
					optionName: 'send_author',
					label: __(
						"Don't send your own comments",
						'send-chat-tools'
					),
				},
				{
					itemKey,
					optionName: 'send_update',
					label: __( 'Use Update notify', 'send-chat-tools' ),
				},
			];
			setTabItems( items );
		}
	}, [ itemKey ] );

	return (
		<div className="sct-wrapper">
			<h2>{ titleText }</h2>
			{ itemKey && (
				<div className="sct-items">
					{ Object.values( tabItems ).map( ( item, i ) => (
						<Toggle
							key={ i }
							itemKey={ item.itemKey! }
							optionName={ item.optionName! }
							label={ item.label }
						/>
					) ) }
				</div>
			) }
			{ itemKey && apiOptionName && (
				<div className="sct-items">
					<TextControlForm
						itemKey={ itemKey }
						optionName={ apiOptionName }
						label={ textLabel }
					/>
				</div>
			) }
			{ itemKey && chatworkRoomId && (
				<div className="sct-items">
					<TextControlForm
						itemKey={ itemKey }
						optionName={ chatworkRoomId }
						label={ chatworkText }
					/>
				</div>
			) }
			{ itemKey && (
				<div className="sct-items">
					<LogDetail itemKey={ itemKey } />
				</div>
			) }
			{ updateFlag && (
				<div className="sct-items">
					<UpdateTime />
				</div>
			) }
			{ logsFlag && (
				<div className="sct-items">
					<LogView />
				</div>
			) }
		</div>
	);
};
