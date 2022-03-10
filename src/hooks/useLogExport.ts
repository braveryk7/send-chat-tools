import { useContext } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { apiContext } from 'src/index';

import { itemType } from 'src/types/ComponentsType';

export const useLogExport = () => {
	const { apiData } = useContext( apiContext );

	const items: itemType[] = [
		{ icon: 'clipboard', key: 'clipboard', text: __( 'Copy to clipboard', 'send-chat-tools' ) },
		{ icon: 'media-text', key: 'text', text: __( 'Export text', 'send-chat-tools' ) },
		{ icon: 'media-spreadsheet', key: 'csv', text: __( 'Export CSV', 'send-chat-tools' ) },
	];

	const copyLogs = ( itemKey: string ) => {
		const toolNumberToToolName = ( toolNumber: string ) => {
			switch ( toolNumber ) {
				case '1':
					return 'Slack';
				case '2':
					return 'Discord';
				case '3':
					return 'Chatwork';
				default:
					return null;
			}
		};

		const typeNumberToTypeName = ( typeNumber: string ) => {
			switch ( typeNumber ) {
				case '1':
					return 'Comment';
				case '2':
					return 'Update';
				case '3':
					return 'Developer';
				default:
					return null;
			}
		};

		const exportFile = ( data: string[], type: string ) => {
			if ( type === 'csv' ) {
				data.unshift( 'Status,Type,Tool,Date\n' );
			}
			const blob = new Blob( data, { type: `text/${ type }` } );
			const anchor = document.createElement( 'a' );
			anchor.href = URL.createObjectURL( blob );
			anchor.download = `log.${ type }`;
			anchor.click();
		};

		if ( apiData ) {
			const separet = itemKey === 'csv' ? ',' : ' ';
			const header = {
				status: itemKey === 'csv' ? '' : 'Status:',
				type: itemKey === 'csv' ? '' : 'Type:',
				tool: itemKey === 'csv' ? '' : 'Tool:',
				date: itemKey === 'csv' ? '' : 'Date:',
			};

			const logData = Object.values( apiData.logs ).map( ( log ) => {
				const row =
					`${ header.status }${ log.status }${ separet }` +
					`${ header.tool }${ toolNumberToToolName( log.tool ) }${ separet }` +
					`${ header.type }${ typeNumberToTypeName( log.type ) }${ separet }` +
					`${ header.date }${ log.send_date }\n`;
				return row;
			} );

			switch ( itemKey ) {
				case 'clipboard':
					navigator.clipboard.writeText( logData.join( '' ) );
					break;
				case 'text':
				case 'csv':
					exportFile( logData, itemKey );
					break;
			}
		}
	};

	return { apiData, items, copyLogs };
};
