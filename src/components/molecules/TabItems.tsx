import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { itemKeyType, optionNameType } from '../../types/ComponentsType';
import { LogDetail } from '../atoms/LogDetail';
import { LogView } from '../atoms/LogView';
import { TextControlForm } from '../atoms/TextControlForm';
import { Toggle } from '../atoms/Toggle';

export const Items = ( props: any ) => {
	const { id, title } = props;
	const [ logsFlag, setLogsFlag ] = useState( false );
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
			case 'logs':
				setLogsFlag( true );
				break;
		}
	}, [ id ] );

	return (
		<div>
			<h2>
				{ title } { __( 'settings', 'send-chat-tools' ) }
			</h2>
			{ itemKey && (
				<Toggle
					itemKey={ itemKey }
					optionName="use"
					label={ itemLabel }
				/>
			) }
			{ itemKey && (
				<Toggle
					itemKey={ itemKey }
					optionName="send_author"
					label={ __(
						'自分自身のコメントを送信しない',
						'send-chat-tools'
					) }
				/>
			) }
			{ itemKey && (
				<Toggle
					itemKey={ itemKey }
					optionName="send_update"
					label={ __(
						'アップデート通知を使用する',
						'send-chat-tools'
					) }
				/>
			) }
			{ itemKey && apiOptionName && (
				<TextControlForm
					itemKey={ itemKey }
					optionName={ apiOptionName }
					label={ textLabel }
				/>
			) }
			{ itemKey && chatworkRoomId && (
				<TextControlForm
					itemKey={ itemKey }
					optionName={ chatworkRoomId }
					label={ chatworkText }
				/>
			) }
			{ itemKey && <LogDetail itemKey={ itemKey } /> }
			{ logsFlag && <LogView /> }
		</div>
	);
};
