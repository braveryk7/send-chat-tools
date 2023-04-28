import { ChangeEvent } from 'react';

import { TokenItem } from '@wordpress/components/build-types/form-token-field/types';
import { useContext } from '@wordpress/element';

import { useSetApi } from 'src/hooks/useSetApi';
import { apiContext } from 'src/index';

import { optionNameType } from 'src/types/ComponentsType';
import { apiType, ChatToolsBaseType, ChatToolsItemKeyType, itemKeyType } from 'src/types/apiType';

export const useChangeValue = ( itemKey: itemKeyType, optionName?: optionNameType ) => {
	const { apiData, setApiData } = useContext( apiContext );

	const changeValue = (
		value: string | boolean | ( string|TokenItem )[] | ChangeEvent< HTMLInputElement >
	) => {
		const newItem: apiType = JSON.parse( JSON.stringify( { ...apiData } ) );

		const chatTools = ( toolName: ChatToolsItemKeyType ) => {
			const isString = ( option: unknown ): option is string => {
				return typeof option === 'string' ? true : false;
			};
			const isBaseOption = (
				option: optionNameType
			): option is keyof Omit< ChatToolsBaseType, 'log' > => {
				return [
					'use', 'send_author', 'send_update', 'login_notify', 'rinker_notify',
				].includes( option );
			};

			const isWebhook = ( option: optionNameType ): option is 'webhook_url' => {
				return option === 'webhook_url';
			};

			if ( optionName && isBaseOption( optionName ) && typeof value === 'boolean' ) {
				newItem[ toolName ][ optionName ] = value;
			}

			if ( optionName && isWebhook( optionName ) && isString( value ) ) {
				newItem[ toolName as 'slack' | 'discord' ][ optionName ] = value;
			}

			if ( toolName === 'chatwork' && isString( value ) ) {
				if ( optionName === 'api_token' ) {
					newItem.chatwork.api_token = value;
				}
				if ( optionName === 'room_id' ) {
					newItem.chatwork.room_id = value;
				}
			}
		};

		if ( itemKey === 'slack' ) {
			chatTools( 'slack' );
		} else if ( itemKey === 'discord' ) {
			chatTools( 'discord' );
		} else if ( itemKey === 'chatwork' ) {
			chatTools( 'chatwork' );
		} else if ( itemKey === 'cron_time' || 'check_rinker_exists_items_cron' ) {
			if ( typeof value === 'object' && 'target' in value ) {
				if ( value.target.id === 'cron_time' ) {
					newItem.cron_time = value.target.value;
				} else if ( value.target.id === 'check_rinker_exists_items_cron' ) {
					newItem.check_rinker_exists_items_cron = value.target.value;
				}
			}
		} else if ( itemKey === 'ignore_key' ) {
			if ( Array.isArray( value ) ) {
				newItem.ignore_key = value;
			}
		}

		setApiData( newItem );
	};

	useSetApi( itemKey, apiData );

	return { apiData, changeValue };
};
