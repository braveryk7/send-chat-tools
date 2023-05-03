import { __ } from '@wordpress/i18n';

import { ChatToolsBaseType } from 'src/types/apiType';

export const rinkerMessages = () => {
	const rinkerExistMessage = __( 'Rinker is not activated.', 'send-chat-tools' );
	const rinkerRecommendMessage = __(
		'Rinker is a very popular product management plug-in for Amazon and Rakuten.',
		'send-chat-tools'
	);
	const rinkerUrl = 'https://oyakosodate.com/rinker/';
	const rinkerFanboxUrl = 'https://oyayoi.fanbox.cc/';

	return [
		rinkerExistMessage,
		rinkerRecommendMessage,
		rinkerUrl,
		rinkerFanboxUrl,
	];
};

export const isOptionNameRinker = ( optionName: Exclude<keyof ChatToolsBaseType, 'log'> ) => {
	return 'rinker_notify' === optionName ? true : false;
};
