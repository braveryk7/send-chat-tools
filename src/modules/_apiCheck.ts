declare const wp: { i18n: { __: ( text: string, domain: string ) => string } };
const __ = wp.i18n.__;

export class ApiCheck {
	private getApiId: string;

	constructor( private getApiInput: HTMLInputElement ) {
		this.getApiId = getApiInput.id;
	}

	createContent(): void {
		let textArea: HTMLElement;
		let tool: string;
		let options: {
			method: string;
			headers?: {};
			body: any;
		};

		if ( 'slack_webhook_url' === this.getApiId ) {
			textArea = <HTMLElement>document.getElementById( 'slack-check' );
			tool = 'slack';
			options = {
				method: 'POST',
				body: JSON.stringify( {
					text: 'Test message from Send Chat Tools.',
				} ),
			};
		} else if ( 'discord_webhook_url' === this.getApiId ) {
			textArea = <HTMLElement>document.getElementById( 'discord-check' );
			tool = 'discord';
			options = {
				method: 'POST',
				headers: {
					'content-Type': 'application/json;charset=utf-8',
				},
				body: JSON.stringify( {
					content: 'Test message from Send Chat Tools.',
				} ),
			};
		} else {
			textArea = <HTMLElement>document.getElementById( '-check' );
			tool = '';
			options = {
				method: 'POST',
				headers: {},
				body: '',
			};
		}
		this.send( options, textArea, tool );
	}

	send( options: object, textArea: HTMLElement, tool: string ) {
		let url: string;
		if ( 'chatwork' === tool ) {
			// url = 'https://api.chatwork.com/v2/rooms/231052493/messages';
			url = 'https://enwluxd1c6a0ipk.m.pipedream.net';
		} else {
			url = this.getApiInput.value;
		}
		fetch( url, options )
			.then( ( res ) => {
				const jsons: Response = res;
				const responsOk: boolean = jsons.ok;
				if ( responsOk ) {
					textArea.classList.remove( 'connect-false' );
					textArea.classList.add( 'connect-true' );
					const msg = __( 'Sent a test message.', 'send-chat-tools' );
					textArea.innerHTML = msg;
				} else {
					textArea.classList.remove( 'connect-true' );
					textArea.classList.add( 'connect-false' );
					textArea.innerHTML = __(
						'Could not connect.',
						'send-chat-tools'
					);
				}
			} )
			.catch( ( error: Error ) => {
				if ( 'Failed to fetch' === error.message ) {
					textArea.classList.remove( 'connect-true' );
					textArea.classList.add( 'connect-false' );
					textArea.innerHTML = __(
						'Could not connect.',
						'send-chat-tools'
					);
				}
			} );
	}
}
