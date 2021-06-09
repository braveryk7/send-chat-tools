export function toggle( eleme: HTMLElement ) {
	if ( eleme !== null ) {
		const content: HTMLElement = <HTMLElement>eleme.nextElementSibling;
		eleme.classList.toggle( 'is-active' );
		content.classList.toggle( 'is-open' );
	}
}
