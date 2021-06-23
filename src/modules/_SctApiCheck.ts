import { SctFormValidation } from './_SctFormValidation';

declare const wp: { i18n: { __: ( text: string, domain: string ) => string } };
const __ = wp.i18n.__;

export class SctApiCheck {
	constructor() {
		const getApiInputSlack: HTMLInputElement = <HTMLInputElement>(
			document.getElementById( 'slack_webhook_url' )
		);

		const getApiInputDiscord: HTMLInputElement = <HTMLInputElement>(
			document.getElementById( 'discord_webhook_url' )
		);

		getApiInputSlack.addEventListener(
			'change',
			( e ) => {
				const eventValue: HTMLInputElement = <HTMLInputElement>e.target;
				this.callValidation( eventValue );
			},
			false
		);

		getApiInputDiscord.addEventListener(
			'change',
			( e ) => {
				const eventValue: HTMLInputElement = <HTMLInputElement>e.target;
				this.callValidation( eventValue );
			},
			false
		);
	}

	callValidation( eventValue: HTMLInputElement ): void {
		let tool: string;
		let notifyHtmlClass: HTMLElement;
		if ( 'slack_webhook_url' === eventValue.id ) {
			tool = 'slack';
			notifyHtmlClass = <HTMLElement>(
				document.getElementById( 'slack-check' )
			);
		} else if ( 'discord_webhook_url' === eventValue.id ) {
			tool = 'discord';
			notifyHtmlClass = <HTMLElement>(
				document.getElementById( 'discord-check' )
			);
		} else {
			tool = 'teams';
			notifyHtmlClass = <HTMLElement>(
				document.getElementById( 'teams-check' )
			);
		}
		const validation = new SctFormValidation( eventValue.value );
		if ( validation.urlCheck( tool ) ) {
			this.createContent( eventValue );
		} else {
			notifyHtmlClass.classList.remove( 'connect-true' );
			notifyHtmlClass.classList.add( 'connect-false' );
			const msg = __( 'Not an appropriate value.', 'send-chat-tools' );
			notifyHtmlClass.innerHTML = msg;
		}
	}

	createContent( eventValue: HTMLInputElement ): void {
		let textArea: HTMLElement;
		let url: string;
		let options: {
			method: string;
			headers?: {};
			body: any;
		};

		if ( 'slack_webhook_url' === eventValue.id ) {
			textArea = <HTMLElement>document.getElementById( 'slack-check' );
			url = eventValue.value;
			options = {
				method: 'POST',
				body: JSON.stringify( {
					text: 'Test message from Send Chat Tools.',
				} ),
			};
		} else if ( 'discord_webhook_url' === eventValue.id ) {
			textArea = <HTMLElement>document.getElementById( 'discord-check' );
			url = eventValue.value;
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
			url = eventValue.value;
			options = {
				method: 'POST',
				headers: {},
				body: '',
			};
		}
		this.send( options, textArea, url );
	}

	send( options: object, textArea: HTMLElement, url: string ) {
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
