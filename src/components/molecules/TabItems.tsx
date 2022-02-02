import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { itemKey } from '../../types/ComponentsType';
import { Toggle } from '../atoms/Toggle';

export const Items = ( props: any ) => {
	const { id, title } = props;
	const [ basicFlag, setBasicFlag ] = useState( false );
	const [ itemKeys, setItemKeys ] = useState< itemKey >( 'sct_use_slack' );
	const [ authorItemKey, setAuthorItemKey ] = useState< itemKey >(
		'sct_send_slack_author'
	);
	const [ updateItemKey, setUpdateItemKey ] = useState< itemKey >(
		'sct_send_slack_update'
	);
	const [ itemLabel, setItemLabel ] = useState( '' );

	useEffect( () => {
		switch ( id ) {
			case 'slack':
				setItemKeys( 'sct_use_slack' );
				setAuthorItemKey( 'sct_send_slack_author' );
				setUpdateItemKey( 'sct_send_slack_update' );
				setItemLabel( __( 'Slack通知を使用する', 'send-chat-tools' ) );
				break;
			case 'discord':
				setItemKeys( 'sct_use_discord' );
				setAuthorItemKey( 'sct_send_discord_author' );
				setUpdateItemKey( 'sct_send_discord_update' );
				setItemLabel(
					__( 'Discord通知を使用する', 'send-chat-tools' )
				);
				break;
			case 'chatwork':
				setItemKeys( 'sct_use_chatwork' );
				setAuthorItemKey( 'sct_send_chatwork_author' );
				setUpdateItemKey( 'sct_send_chatwork_update' );
				setItemLabel(
					__( 'Chatwork通知を使用する', 'send-chat-tools' )
				);
				break;
		}
		setBasicFlag( id !== 'basic' ? true : false );
	}, [ id ] );

	return (
		<div>
			<h2>
				{ title } { __( 'settings', 'send-chat-tools' ) }
			</h2>
			<Toggle itemKey={ itemKeys } label={ itemLabel } />
			{ basicFlag && (
				<Toggle
					itemKey={ authorItemKey }
					label={ __(
						'自分自身のコメントを送信しない',
						'send-chat-tools'
					) }
				/>
			) }
			{ basicFlag && (
				<Toggle
					itemKey={ updateItemKey }
					label={ __(
						'アップデート通知を使用する',
						'send-chat-tools'
					) }
				/>
			) }
		</div>
	);
};
