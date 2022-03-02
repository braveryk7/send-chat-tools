import { useTabItems } from '../../hooks/useTabItems';
import { TabItemsType } from '../../types/ComponentsType';
import { LogDetail } from '../atoms/LogDetail';
import { LogView } from '../atoms/LogView';
import { TextControlForm } from '../atoms/TextControlForm';
import { Toggle } from '../atoms/Toggle';
import { UpdateTime } from '../atoms/UpdateTime';

export const Items = ( props: TabItemsType ) => {
	const {
		updateFlag,
		itemKey,
		logsFlag,
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
			{ updateFlag && (
				<div className="sct-items">
					<UpdateTime />
				</div>
			) }
			{ logsFlag && (
				<div className="sct-items">
					<LogView />
				</div>
			) }
		</div>
	);
};
