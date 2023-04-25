import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import {
	apiOptionNameType,
	chatworkRoomIdType,
	setUseStateType,
	TabItemsType,
	TogglePropsType,
} from 'src/types/ComponentsType';
import { ChatToolsItemKeyType } from 'src/types/apiType';

export const useTabItems = ( props: TabItemsType ) => {
	const { id } = props;
	const [ itemKey, setItemKey ] = useState< ChatToolsItemKeyType | undefined >( undefined );
	const [ itemLabel, setItemLabel ] = useState( '' );
	const [ componentName, setComponentName ] = useState( '' );
	const [ titleText, setTitleText ] = useState( '' );
	const [ textLabel, setTextLabel ] = useState( '' );
	const [
		apiOptionName,
		setApiOptionName,
	] = useState< apiOptionNameType | undefined >( undefined );
	const [
		chatworkRoomId, setChatworkRoomId,
	] = useState< chatworkRoomIdType | undefined >( undefined );
	const [ chatworkText, setChatworkText ] = useState( '' );
	const [ tabItems, setTabItems ] = useState< TogglePropsType[] >( [] );

	useEffect( () => {
		const setUseState: setUseStateType = ( itemName, label, optionName, title, text ) => {
			setItemKey( itemName );
			setItemLabel( label );
			setApiOptionName( optionName );
			setTitleText( title );
			setTextLabel( text );
		};

		if ( 'basic' === id ) {
			setComponentName( 'basic' );
			setTitleText( __( 'Basic settings', 'send-chat-tools' ), );
		} else if ( 'slack' === id ) {
			setUseState(
				'slack',
				__( 'Use Slack notify', 'send-chat-tools' ),
				'webhook_url',
				__( 'Slack settings', 'send-chat-tools' ),
				__( 'Slack Webhook URL', 'send-chat-tools' ),
			);
		} else if ( 'discord' === id ) {
			setUseState(
				'discord',
				__( 'Use Discord notify', 'send-chat-tools' ),
				'webhook_url',
				__( 'Discord settings', 'send-chat-tools' ),
				__( 'Discord Webhook URL', 'send-chat-tools' ),
			);
		} else if ( 'chatwork' === id ) {
			setUseState(
				'chatwork',
				__( 'Use Chatwork notify', 'send-chat-tools' ),
				'api_token',
				__( 'Chatwork settings', 'send-chat-tools' ),
				__( 'Chatwork API token', 'send-chat-tools' ),
			);
			setChatworkRoomId( 'room_id' );
			setChatworkText( __( 'Chatwork Room ID', 'send-chat-tools' ) );
		} else if ( 'logs' === id ) {
			setComponentName( 'logs' );
			setTitleText( __( 'Logs', 'send-chat-tools' ) );
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
				{
					itemKey,
					optionName: 'login_notify',
					label: __( 'Use login notify', 'send-chat-tools' ),
				},
			];
			setTabItems( items );
		}
	}, [ itemKey, itemLabel ] );

	return {
		itemKey,
		componentName,
		titleText,
		textLabel,
		tabItems,
		chatworkRoomId,
		chatworkText,
		apiOptionName,
	};
};
