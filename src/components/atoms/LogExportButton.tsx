import { Button, Dashicon, Dropdown, MenuGroup, MenuItem } from '@wordpress/components';

import { useLogExport } from 'src/hooks/useLogExport';

import { itemType } from 'src/types/ComponentsType';

export const LogExportButton = () => {
	const { items, copyLogs } = useLogExport();

	return (
		<Dropdown
			className="sct-logs__export-button"
			position="bottom right"
			renderToggle={ ( { isOpen, onToggle } ) => (
				<Button
					onClick={ onToggle }
					aria-expanded={ isOpen }
				>
					<span className="dashicons dashicons-ellipsis"></span>
				</Button>
			) }
			renderContent={ () => (
				<MenuGroup>
					{ items.map( ( item: itemType, index ) => (
						<MenuItem
							key={ index }
							icon={ <Dashicon icon={ item.icon } /> }
							onClick={ () => copyLogs( item.key ) }
						>
							{ item.text }
						</MenuItem>
					) ) }
				</MenuGroup>
			) }
		/>
	);
};
