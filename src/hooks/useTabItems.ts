import { useEffect, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

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

		const labelContent = ( toolName: string ) => {
			return sprintf(
				/* translators: Chat tool name. */
				__( 'Enable %s notifications.', 'send-chat-tools' ),
				toolName.charAt( 0 ).toUpperCase() + toolName.slice( 1 ),
			);
		};

		if ( 'basic' === id ) {
			setComponentName( 'basic' );
			setTitleText( __( 'Basic settings', 'send-chat-tools' ), );
		} else if ( 'slack' === id ) {
			setUseState(
				'slack',
				labelContent( id ),
				'webhook_url',
				__( 'Slack settings', 'send-chat-tools' ),
				__( 'Slack Webhook URL', 'send-chat-tools' ),
			);
		} else if ( 'discord' === id ) {
			setUseState(
				'discord',
				labelContent( id ),
				'webhook_url',
				__( 'Discord settings', 'send-chat-tools' ),
				__( 'Discord Webhook URL', 'send-chat-tools' ),
			);
		} else if ( 'chatwork' === id ) {
			setUseState(
				'chatwork',
				labelContent( id ),
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
					optionName: 'comment_notify',
					label: __(
						'Notify when new comments are received.',
						'send-chat-tools'
					),
				},
				{
					itemKey,
					optionName: 'send_author',
					label: __(
						'If you comment while logged into WordPress,' +
						'you will not be notified of your own comment.',
						'send-chat-tools'
					),
				},
				{
					itemKey,
					optionName: 'update_notify',
					label: __(
						'Notify you when there are updates to WordPress Core, themes, and plugins.',
						'send-chat-tools' ),
				},
				{
					itemKey,
					optionName: 'login_notify',
					label: __( 'Notify you when a WordPress user logs in.', 'send-chat-tools' ),
				},
				{
					itemKey,
					optionName: 'rinker_notify',
					label: __(
						'Notify you when an item managed by Rinker' +
						'becomes end-of-life on Amazon or Rakuten.',
						'send-chat-tools' ),
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
