import '../src/scss/style.scss';
import { ApiCheck } from './modules/_apiCheck';
import { FormValidation } from './modules/_formValidation';
import { toggle } from './modules/accordion';

declare const wp: { i18n: { __: ( text: string, domain: string ) => string } };
const __ = wp.i18n.__;

const title: NodeListOf< HTMLElement > = document.querySelectorAll< HTMLElement >(
	'.accordion-title'
);

for ( let i = 0; i < title.length; i = i + 1 ) {
	title[ i ]!.addEventListener( 'click', () => {
		toggle( title[ i ]! );
	} );
}

const getApiInputSlack: HTMLInputElement = <HTMLInputElement>(
	document.getElementById( 'slack_webhook_url' )
);

const getApiInputDiscord: HTMLInputElement = <HTMLInputElement>(
	document.getElementById( 'discord_webhook_url' )
);

function callValidation( eventValue: HTMLInputElement ): void {
	let tool: string;
	let notifyHtmlClass: HTMLElement;
	if ( 'slack_webhook_url' === eventValue.id ) {
		tool = 'slack';
		notifyHtmlClass = <HTMLElement>document.getElementById( 'slack-check' );
	} else if ( 'discord_webhook_url' === eventValue.id ) {
		tool = 'discord';
		notifyHtmlClass = <HTMLElement>(
			document.getElementById( 'discord-check' )
		);
	} else {
		tool = 'teams';
		notifyHtmlClass = <HTMLElement>document.getElementById( 'teams-check' );
	}
	const validation = new FormValidation( eventValue.value );
	if ( validation.urlCheck( tool ) ) {
		const startCheck = new ApiCheck( eventValue );
		startCheck.createContent();
	} else {
		notifyHtmlClass.classList.remove( 'connect-true' );
		notifyHtmlClass.classList.add( 'connect-false' );
		const msg = __( 'Not an appropriate value.', 'send-chat-tools' );
		notifyHtmlClass.innerHTML = msg;
	}
}

getApiInputSlack.addEventListener(
	'change',
	( e ) => {
		const eventValue: HTMLInputElement = <HTMLInputElement>e.target;
		callValidation( eventValue );
	},
	false
);

getApiInputDiscord.addEventListener(
	'change',
	( e ) => {
		const eventValue: HTMLInputElement = <HTMLInputElement>e.target;
		callValidation( eventValue );
	},
	false
);
