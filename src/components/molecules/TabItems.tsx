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
	const { id, title } = props;
	const [ updateFlag, setUpdateFlag ] = useState( false );
	const [ logsFlag, setLogsFlag ] = useState( false );
	const [ tabItems, setTabItems ] = useState< TogglePropsType[] >( [] );
	const [ itemKey, setItemKey ] = useState< itemKeyType | null >( null );
	const [ itemLabel, setItemLabel ] = useState( '' );
	const [
		apiOptionName,
		setApiOptionName,
	] = useState< optionNameType | null >( null );
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
				setItemLabel( __( 'Slack通知を使用する', 'send-chat-tools' ) );
				setApiOptionName( 'webhook_url' );
				setTextLabel( __( 'Slack Webhook URL', 'send-chat-tools' ) );
				break;
			case 'discord':
				setItemKey( 'discord' );
				setItemLabel(
					__( 'Discord通知を使用する', 'send-chat-tools' )
				);
				setApiOptionName( 'webhook_url' );
				setTextLabel( __( 'Discord Webhook URL', 'send-chat-tools' ) );
				break;
			case 'chatwork':
				setItemKey( 'chatwork' );
				setItemLabel(
					__( 'Chatwork通知を使用する', 'send-chat-tools' )
				);
				setApiOptionName( 'api_token' );
				setChatworkRoomId( 'room_id' );
				setTextLabel( __( 'Chatwork APIキー', 'send-chat-tools' ) );
				setChatworkText( __( 'Chatwork RoomID', 'send-chat-tools' ) );
				break;
			case 'update':
				setUpdateFlag( true );
				break;
			case 'logs':
				setLogsFlag( true );
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
						'自分自身のコメントを送信しない',
						'send-chat-tools'
					),
				},
				{
					itemKey,
					optionName: 'send_update',
					label: __(
						'アップデート通知を使用する',
						'send-chat-tools'
					),
				},
			];
			setTabItems( items );
		}
	}, [ itemKey ] );

	return (
		<div className="sct-wrapper">
			<h2>
				{ title } { __( 'settings', 'send-chat-tools' ) }
			</h2>
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
