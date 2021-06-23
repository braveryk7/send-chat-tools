export class SctFormValidation {
	constructor( private url: string ) {
		this.url = url;
	}

	urlCheck( tool: string ): boolean {
		if ( 'slack' === tool ) {
			const regPattern: RegExp = /^(?:https|http):\/\/(?:[a-z]{1,}\.|)(?:hooks\.slack\.com)(?:\/(?:.*)|\?(?:.*)|$)$/;
			return regPattern.test( this.url );
		} else if ( 'discord' === tool ) {
			const regPattern: RegExp = /^(?:https|http):\/\/(?:[a-z]{1,}\.|)(?:discord\.com)(?:\/(?:.*)|\?(?:.*)|$)$/;
			return regPattern.test( this.url );
		} else if ( 'chatwork' === tool ) {
			const regPattern: RegExp = /^[0-9a-zA-Z]+$/;
			return regPattern.test( this.url );
		}
		return false;
	}
}
