import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import {
	itemKeyType,
	optionNameType,
	TabItemsType,
	TogglePropsType,
} from 'src/types/ComponentsType';

export const useTabItems = ( props: TabItemsType ) => {
	const { id } = props;
	const [ itemKey, setItemKey ] = useState< itemKeyType | null >( null );
	const [ itemLabel, setItemLabel ] = useState( '' );
	const [ updateFlag, setUpdateFlag ] = useState( false );
	const [ logsFlag, setLogsFlag ] = useState( false );
	const [ tabItems, setTabItems ] = useState< TogglePropsType[] >( [] );
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

	return {
		updateFlag,
		itemKey,
		logsFlag,
		titleText,
		textLabel,
		tabItems,
		chatworkRoomId,
		chatworkText,
		apiOptionName,
	};
};
