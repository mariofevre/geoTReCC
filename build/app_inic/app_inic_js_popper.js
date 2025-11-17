

	
function show(_a) {
	console.log(_a);
	_c=_a.getAttribute('class');
	_idt=_a.getAttribute('aria-describedby');
	console.log(_idt)
	_t=document.querySelector('#'+_idt);
	_t.setAttribute('data-show', '');
	_InstanciasPopper[_c].instancia.update();
}	
function hide(_a) {
	_c=_a.getAttribute('class');
	_idt=_a.getAttribute('aria-describedby');
	_t=document.querySelector('#'+_idt);
	_t.removeAttribute('data-show');
	_InstanciasPopper[_c].instancia.update();
}		
