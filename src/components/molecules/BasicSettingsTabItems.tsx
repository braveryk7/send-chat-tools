import { __ } from '@wordpress/i18n';

import { TokenField } from 'src/components/atoms/TokenField';
import { UpdateTime } from 'src/components/atoms/UpdateTime';
import { useTabItems } from 'src/hooks/useTabItems';

import { TabItemsType } from 'src/types/ComponentsType';

export const BasicSettingsTabItems = ( props: TabItemsType ) => {
	const { titleText } = useTabItems( props );

	return (
		<div className="sct-wrapper">
			<h3>{ titleText }</h3>
			<div className="sct-items">
				<UpdateTime
					itemKey="cron_time"
					title={ __( 'Set time to check for updates', 'send-chat-tools' ) }
				/>
			</div>
			<div className="sct-items">
				<TokenField
					itemKey="ignore_key"
					title={
						__(
							'Theme/plugin key to remove update notifications',
							'send-chat-tools'
						)
					}
				/>
			</div>
		</div>
	);
};
