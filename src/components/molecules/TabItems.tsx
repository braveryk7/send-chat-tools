import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { itemKeyType } from '../../types/ComponentsType';
import { Toggle } from '../atoms/Toggle';

export const Items = ( props: any ) => {
	const { id, title } = props;
	const [ basicFlag, setBasicFlag ] = useState( false );
	const [ itemKey, setItemKey ] = useState< itemKeyType | null >( null );
	const [ itemLabel, setItemLabel ] = useState( '' );

	useEffect( () => {
		switch ( id ) {
			case 'slack':
				setItemKey( 'slack' );
				setItemLabel( __( 'Slack通知を使用する', 'send-chat-tools' ) );
				break;
			case 'discord':
				setItemKey( 'discord' );
				setItemLabel(
					__( 'Discord通知を使用する', 'send-chat-tools' )
				);
				break;
			case 'chatwork':
				setItemKey( 'chatwork' );
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
			{ itemKey && (
				<Toggle
					itemKey={ itemKey }
					optionName="use"
					label={ itemLabel }
				/>
			) }
			{ basicFlag && itemKey && (
				<Toggle
					itemKey={ itemKey }
					optionName="send_author"
					label={ __(
						'自分自身のコメントを送信しない',
						'send-chat-tools'
					) }
				/>
			) }
			{ basicFlag && itemKey && (
				<Toggle
					itemKey={ itemKey }
					optionName="send_update"
					label={ __(
						'アップデート通知を使用する',
						'send-chat-tools'
					) }
				/>
			) }
		</div>
	);
};
