//funciones de configuración
function usuarios(){
	if(_UsuarioAcceso.general.general.general<3){return;}
	document.querySelector('#formusuarios').setAttribute('estado','activo');
	actualizarusuarios();
	actualizarTablas();
	actualizarAccesos();
}

function actualizarusuarios(){		
	$.ajax({
		url:   './sis_usuarios/usu_consulta_ajax.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
				console.log(_res);
			if(_res.res=='exito'){
				formCrearAccesoUsus(_res);
			}else{
				alert('error asfffgh');
			}
		}
	});	
}


function formCrearAccesoUsus(_usuarios){
	//abre el formulario para crear permisos de acceso
	
	_sel=document.querySelector('form#crearpermiso select#usu');
	for(_nu in _usuarios.data){
		_opt=document.createElement('option');
		_opt.setAttribute('value',_usuarios.data[_nu].id);
		_opt.innerHTML=_usuarios.data[_nu].apellido+', '+_usuarios.data[_nu].nombre+',  <span id="log">'+_usuarios.data[_nu].log+'</span>';
		_sel.appendChild(_opt);
	}	
}
	

function actualizarTablas(){	
	var _parametros = {
		'selecTabla':'',
		'selecElemCod':'',
		'selecElemId':''		
	};
	
	$.ajax({
		url:   'consulta_tablas.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			var _res = $.parseJSON(response);
				console.log(_res);
			if(_res.res=='exito'){
				formCrearAccesoTablas(_res.data);
			}else{
				alert('error asfffgh');
			}
		}
	});	
}


function formCrearAccesoTablas(_tablas){
	//abre el formulario para crear permisos de acceso
	
	_sel=document.querySelector('form#crearpermiso select#tabla');
	for(_nu in _tablas.tablas.est){
		 _tab=_tablas.tablas.est[_nu];
		_opt=document.createElement('option');
		_opt.setAttribute('value',_tab);
		_opt.innerHTML=_tablas.tablasConf[_tab].nombre_humano;
		_sel.appendChild(_opt);
	}	
}


function actualizarElementos(_tabla){
	
	_parametros={
		'tabla':_tabla
	}

	$.ajax({
		data: _parametros,
		url:   './comun_consultas/consulta_centroides.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);			
			console.log(_res);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='err'){
			}else{
				//cargaContrato();	
				_lyrCentSrc.clear();
				_sel=document.querySelector('form#crearpermiso select#elemento');
				_sel.innerHTML='';
				_aaa=document.createElement('option');
					_aaa.setAttribute('value','general');
					_aaa.setAttribute('conf','Atención, el valor "general" para el elemento de acceso: significa que se le está brindando acceso a todo elemento accesible desde esta tabla');
					_aaa.innerHTML='¡general!';
					_sel.appendChild(_aaa);
				for(_no in _res.data.centroidesOrden){
					_nc=_res.data.centroidesOrden[_no];
					_dat=_res.data.centroides[_nc];
					_aaa=document.createElement('option');
					_aaa.setAttribute('value',_dat.cod);
					_aaa.innerHTML=_dat.nom;
					_sel.appendChild(_aaa);				
				}
			}
		}
	})			
}
	

function revocarPermiso(_event,_this){
	_event.preventDefault();
	_idacc=_this.parentNode.getAttribute('accid');
	_parametros={
		'id_acc':_idacc
	}
	$.ajax({
		data: _parametros,
		url:   './sis_usuarios/acc_revocar_ajax.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);			
			console.log(_res);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='err'){
				
			}else if(_res.res=='exito'){
				actualizarAccesos();
			}
		}
	})	
}

function crearPermiso(_event,_this){
	_event.preventDefault();

	_parametros={
		'id_usu':_this.querySelector('#usu').value,
		'tabla':_this.querySelector('#tabla').value,
		'elemento':_this.querySelector('#elemento').value,
		'accion':_this.querySelector('#accion').value,
		'nivel':_this.querySelector('#nivel').value,
	}

	$.ajax({
		data: _parametros,
		url:   './sis_usuarios/acc_crear_ajax.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);			
			console.log(_res);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='err'){
				
			}else if(_res.res=='exito'){
				actualizarAccesos();
			}
		}
	})
}

function registrar(_this){		
	
	_stop='no';
	_form=_this.parentNode.parentNode;
	if(_form.querySelector('input[name="password"]').value !=_form.querySelector('input[name="password2"]').value){
		_form.querySelector('input[name="password2"]').style.backgroundColor='#fda';
		alert('no coinciden las contraseñas');
		_stop='si';
	}
	
	if(_form.querySelector('input[name="password"]').value.length < 4){
		_form.querySelector('input[name="password"]').style.backgroundColor='#fda';
		alert('la contraseña requiere al menos 4 caracteres');
		_stop='si';
	}
	
	if(_form.querySelector('input[name="nombre"]').value.length < 1){
		_form.querySelector('input[name="nombre"]').style.backgroundColor='#fda';
		alert('falta ingresar su nombre');
		_stop='si';
	}
	
	if(_form.querySelector('input[name="apellido"]').value.length < 1){
		_form.querySelector('input[name="apellido"]').style.backgroundColor='#fda';
		alert('falta ingresar su nombre');
		_stop='si';
	}
	
	if(_form.querySelector('input[name="mail"]').value.length < 6){
		_form.querySelector('input[name="mail"]').style.backgroundColor='#fda';
		alert('falta ingresar su mail');
		_stop='si';
	}
	
	if(_form.querySelector('input[name="numeroid"]').value.length < 6){
		_form.querySelector('input[name="numeroid"]').style.backgroundColor='#fda';
		alert('falta ingresar su numero de identificación');
		_stop='si';
	}
	
	if(_form.querySelector('select[name="isopais"]').value==''){
		_form.querySelector('select[name="isopais"]').style.backgroundColor='#fda';
		alert('falta ingresar su estado de pertenencia');
		_stop='si';
	}
	
	if(_form.querySelector('[name="pronombre"]').value==''){
		_form.querySelector('[name="pronombre"]').style.backgroundColor='#fda';
		alert('falta ingresar su pronombre de preferencia');
		_stop='si';
	}
	
	_pn=_form.querySelector('[name="pronombre"]').value;
	if(_form.querySelector('[name="pronombre"]').value=='__otro__'){
		_pn=_form.querySelector('input[name="pronombre_otro"]').value;
		
		if(_form.querySelector('input[name="pronombre_otro"]').value==''){
			_form.querySelector('input[name="pronombre_otro"]').style.backgroundColor='#fda';
			alert('falta ingresar su pronombre de preferencia');
			_stop='si';
			
		}
	}
	
	
	if(_stop=='si'){
		return;
	}
	
	var parametros = {
		'log': _form.querySelector('input[name="log"]').value,
		'password': _form.querySelector('input[name="password"]').value,
		'password2': _form.querySelector('input[name="password2"]').value,
		'nombre': _form.querySelector('input[name="nombre"]').value,
		'email': _form.querySelector('input[name="mail"]').value,
		'apellido': _form.querySelector('input[name="apellido"]').value,
		'isopais': _form.querySelector('[name="isopais"]').value,
		'numeroid': _form.querySelector('input[name="numeroid"]').value,
		'pronombre': _pn
	};
	
	$.ajax({
		data:  parametros,
		url:   './sis_usuarios/usu_registro_ajax.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);
			if(_res.res=='exito'){
				_UsuarioA= _res.data;
				acceder(_this);	
				cerrar();					
			}else{
				alert('error')
			}
		}
	});
}	


function actualizarUsu(_this){		
	
	_stop='no';
	_form=_this.parentNode.parentNode;
	
	if(_form.querySelector('input[name="log"]').value.length < 1){
		_form.querySelector('input[name="log"]').style.backgroundColor='#fda';
		alert('falta ingresar un nombre de usuario válido');
		_stop='si';
	}
	
		
	if(_form.querySelector('input[name="nombre"]').value.length < 1){
		_form.querySelector('input[name="nombre"]').style.backgroundColor='#fda';
		alert('falta ingresar su nombre');
		_stop='si';
	}
	
	if(_form.querySelector('input[name="apellido"]').value.length < 1){
		_form.querySelector('input[name="apellido"]').style.backgroundColor='#fda';
		alert('falta ingresar su apellido');
		_stop='si';
	}
	
	if(_form.querySelector('input[name="mail"]').value.length < 6){
		_form.querySelector('input[name="mail"]').style.backgroundColor='#fda';
		alert('falta ingresar su mail');
		_stop='si';
	}
	
	if(_form.querySelector('input[name="numeroid"]').value.length < 6){
		_form.querySelector('input[name="numeroid"]').style.backgroundColor='#fda';
		alert('falta ingresar su numero de identificación');
		_stop='si';
	}
	
	if(_form.querySelector('select[name="isopais"]').value==''){
		_form.querySelector('select[name="isopais"]').style.backgroundColor='#fda';
		alert('falta ingresar su estado de pertenencia');
		_stop='si';
	}
	
	if(_form.querySelector('[name="pronombre"]').value==''){
		_form.querySelector('[name="pronombre"]').style.backgroundColor='#fda';
		alert('falta ingresar su pronombre de preferencia');
		_stop='si';
	}
	
	_pn=_form.querySelector('[name="pronombre"]').value;
	if(_form.querySelector('[name="pronombre"]').value=='__otro__'){
		_pn=_form.querySelector('input[name="pronombre_otro"]').value;
		
		if(_form.querySelector('input[name="pronombre_otro"]').value==''){
			_form.querySelector('input[name="pronombre_otro"]').style.backgroundColor='#fda';
			alert('falta ingresar su pronombre de preferencia');
			_stop='si';
			
		}
	}
	
	
	if(_form.querySelector('input[name="cambiocontrasena"]').value =='si'){
			
		if(_form.querySelector('input[name="password"]').value !=_form.querySelector('input[name="password2"]').value){
			_form.querySelector('input[name="password2"]').style.backgroundColor='#fda';
			alert('no coinciden las contraseñas');
			_stop='si';
		}
		
		if(_form.querySelector('input[name="password"]').value.length < 4){
			_form.querySelector('input[name="password"]').style.backgroundColor='#fda';
			alert('la contraseña requiere al menos 4 caracteres');
			_stop='si';
		}
	}
		
	
	
	if(_stop=='si'){
		return;
	}
	
	var parametros = {
		'id': _form.querySelector('input[name="id"]').value,
		'log': _form.querySelector('input[name="log"]').value,
		'nombre': _form.querySelector('input[name="nombre"]').value,
		'email': _form.querySelector('input[name="mail"]').value,
		'apellido': _form.querySelector('input[name="apellido"]').value,
		'isopais': _form.querySelector('[name="isopais"]').value,
		'numeroid': _form.querySelector('input[name="numeroid"]').value,
		'pronombre': _pn,
		'password': _form.querySelector('input[name="password"]').value,
		'password2': _form.querySelector('input[name="password2"]').value,
		'cambiocontrasena': _form.querySelector('input[name="cambiocontrasena"]').value
	};
	
	$.ajax({
		data:  parametros,
		url:   './sis_usuarios/usu_ed_ajax.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);
			if(_res.res=='exito'){
				_UsuarioA= _res.data;
				acceder(_this);	
				cerrar();					
			}else{
				alert('error')
			}
		}
	});
}	

function actualizarBusquedaUsuario(){

	_busqueda=document.querySelector('#formusuarios input[name="busqueda"]').value;
	_busqueda=_busqueda.toLowerCase();
	_inps = document.querySelectorAll('#formusuarios #eliminarpermiso > div');
	
	
	for(_in in _inps){
		if(typeof _inps[_in] != 'object'){continue;}
		_nom=_inps[_in].querySelector('#nom').innerHTML;
		_nom=_nom.toLowerCase();
		if(_nom.indexOf(_busqueda)>-1){
			_inps[_in].setAttribute('selecto','si');
		}else{
			_inps[_in].setAttribute('selecto','no');
		}
	}
	
	_ops = document.querySelectorAll('#formusuarios select#usu > option');
	
	for(_on in _ops){
		if(typeof _ops[_on] != 'object'){continue;}
		_nom=_ops[_on].innerHTML;
		_nom=_nom.toLowerCase();
		if(_nom.indexOf(_busqueda)>-1){
			_ops[_on].setAttribute('selecto','si');
		}else{
			_ops[_on].setAttribute('selecto','no');
		}
	}
}
