import '../src/scss/style.scss';
import { toggle } from './modules/accordion';

const title = document.querySelectorAll<HTMLElement>('.accordion-title');

for(let i: number = 0; i < title.length; i = ( i + 1 )) {
	title[i].addEventListener('click', () => {
		toggle(title[i])
	});
}