import { LogDetail } from 'src/components/atoms/LogDetail';
import { LogView } from 'src/components/atoms/LogView';
import { TextControlForm } from 'src/components/atoms/TextControlForm';
import { Toggle } from 'src/components/atoms/Toggle';
import { UpdateTime } from 'src/components/atoms/UpdateTime';
import { useTabItems } from 'src/hooks/useTabItems';

import { TabItemsType } from 'src/types/ComponentsType';

export const TabItems = ( props: TabItemsType ) => {
	const {
		itemKey,
		componentName,
		titleText,
		textLabel,
		tabItems,
		chatworkRoomId,
		chatworkText,
		apiOptionName,
	} = useTabItems( props );

	return (
		<div className="sct-wrapper">
			<h2>{ titleText }</h2>
			{ itemKey && (
				<div className="sct-items">
					{ Object.values( tabItems ).map( ( item, i ) => (
						<Toggle
							key={ i }
							itemKey={ item.itemKey }
							optionName={ item.optionName }
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
			{ componentName === 'update' && (
				<div className="sct-items">
					<UpdateTime itemKey="cron_time" />
				</div>
			) }
			{ componentName === 'logs' && (
				<div className="sct-items">
					<LogView />
				</div>
			) }
		</div>
	);
};
