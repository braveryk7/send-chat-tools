export type itemKey =
	| 'sct_use_slack'
	| 'sct_send_slack_author'
	| 'sct_send_slack_update'
	| 'sct_use_discord'
	| 'sct_send_discord_author'
	| 'sct_send_discord_update'
	| 'sct_use_chatwork'
	| 'sct_send_chatwork_author'
	| 'sct_send_chatwork_update';

export type TogglePropsType = {
	itemKey: itemKey;
	label: string;
};
