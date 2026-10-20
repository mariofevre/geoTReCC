
function cerrar(){
	$("#configform").remove();
	_inps=document.querySelectorAll('#formconfig input');
	for(_nn in _inps){
		if(typeof _inps[_nn] != 'object'){continue;}
		if(_inps[_nn].getAttribute('type','submit')){continue;}
		_inps[_nn].value='';
	}
	_sel=document.querySelectorAll('#formconfig select');
	for(_nn in _sel){
		if(typeof _sel[_nn] != 'object'){continue;}
		if(_sel[_nn].getAttribute('id')=='nivel'){continue;}
		if(_sel[_nn].getAttribute('id')=='accion'){continue;}
		_opt=_sel[_nn].querySelectorAll('option');
		_c=0;
		for(_no in _opt){
			_c++;
			if(_c==1){continue;}
			if(typeof _opt[_no] != 'object'){continue;}
			_opt[_no].parentNode.removeChild(_opt[_no]);
		}
	}
	
	$("#usucssform").remove();
	_inps=document.querySelectorAll('#formacceso input');
	for(_nn in _inps){
		_inps[_nn].value='';
	}
	
	_sel=document.querySelectorAll('#formacceso select');
	for(_nn in _sel){
		if(typeof _sel[_nn] != 'object'){continue;}
		if(_sel[_nn].getAttribute('id')=='nivel'){continue;}
		if(_sel[_nn].getAttribute('id')=='accion'){continue;}
		_opt=_sel[_nn].querySelectorAll('option');
		_c=0;
		for(_no in _opt){
			_c++;
			if(_c==1){continue;}
			if(typeof _opt[_no] != 'object'){continue;}
			_opt[_no].parentNode.removeChild(_opt[_no]);
		}
	}
}


function activaOtroPronombre(){
	if(document.querySelector('#formacceso [name="pronombre"]').value=='__otro__'){	
		document.querySelector('#formacceso #parrafo_pronombre').setAttribute('estado','activo');
	}else{	
		document.querySelector('#formacceso #parrafo_pronombre').setAttribute('estado','inactivo');
	}		
}


function activarCambioContrasena(){
	 document.querySelector('#formacceso').setAttribute('editacontrasena','si');	
	 document.querySelector('#formacceso [name="cambiocontrasena"]').value='si';	
}
