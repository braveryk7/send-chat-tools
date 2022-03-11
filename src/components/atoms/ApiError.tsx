import { Panel, PanelBody, PanelRow } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export const ApiError = () => {
	const messageList = [
		__( 'Send Chat Toolsは、設定読み込みにWordPress REST APIを使用しています。', 'send-chat-tools' ),
		__( 'もしこのエラーが続けて表示される場合は、WordPress REST APIが無効化されている可能性があります。', 'send-chat-tools' ),
		__( 'WordPress REST APIはプラグインやテーマのfunctions.phpファイルで設定することができます。', 'send-chat-tools' ),
		__( 'WordPress REST APIを有効化して再度アクセスしてください。', 'send-chat-tools' ),
	];
	return (
		<Panel header={ __( 'API接続に失敗しました。', 'send-chat-tools' ) } >
			<PanelBody>
				{ messageList.map( ( message, index ) => (
					<PanelRow key={ index }>{ message }</PanelRow>
				) ) }
			</PanelBody>
		</Panel>
	);
};
