export class SctAccordion {
	private title: NodeListOf< HTMLElement >;

	constructor() {
		this.title = document.querySelectorAll< HTMLElement >(
			'.accordion-title'
		);

		for ( let i = 0; i < this.title.length; i = i + 1 ) {
			this.title[ i ]!.addEventListener( 'click', () => {
				this.toggle( this.title[ i ]! );
			} );
		}
	}

	private toggle( eleme: HTMLElement ) {
		if ( eleme !== null ) {
			const content: HTMLElement = <HTMLElement>eleme.nextElementSibling;
			eleme.classList.toggle( 'is-active' );
			content.classList.toggle( 'is-open' );
		}
	}
}
