function cargarusuario(){
	if(_UsuarioA==null){
		_UsuarioA={'nombre':'Anónimo','apellido':''}
	}
	
	document.querySelector('#hola').innerHTML=_UsuarioA.nombre+" "+_UsuarioA.apellido;
	
	if(_UsuarioA.log!=undefined){
		document.querySelector('#navegador').setAttribute('registrado','si');
	}else{
		document.querySelector('#navegador').setAttribute('registrado','no');
	}
}
	


//funciones del formulario de usuarios
function formUsuario(_modo){		
	
	document.querySelector('#formacceso').setAttribute('modo',_modo);
	document.querySelector('#formacceso').setAttribute('estado','activo');
	document.querySelector('#formacceso').setAttribute('editacontrasena','no');
	document.querySelector('#formacceso [name="cambiocontrasena"]').value='no';
	
	if(_modo=='accede'){
		document.getElementById('acceder').value = "acceder";
		var delayInMilliseconds = 500;
		setTimeout(function() {
			document.getElementById('inputUsuarioLogNombre').focus();
		}, delayInMilliseconds);        
		
		document.querySelector('#formacceso [name="id"]').value='';
		document.querySelector('#formacceso [name="apellido"]').value='';
		document.querySelector('#formacceso [name="nombre"]').value='';
		document.querySelector('#formacceso [name="mail"]').value='';
		document.querySelector('#formacceso [name="log"]').value='';
		document.querySelector('#formacceso [name="isopais"]').value='AR';
		document.querySelector('#formacceso [name="numeroid"]').value='';
		document.querySelector('#formacceso [name="pronombre"]').value='';
		document.querySelector('#formacceso [name="pronombre"]').onchange();
		document.querySelector('#formacceso [name="pronombre_otro"]').value='';
	}
	
	if(_modo=='edita'){
		document.querySelector('#formacceso [name="id"]').value=_UsuarioA.id;
		document.querySelector('#formacceso [name="apellido"]').value=_UsuarioA.apellido;
		document.querySelector('#formacceso [name="nombre"]').value=_UsuarioA.nombre;
		document.querySelector('#formacceso [name="mail"]').value=_UsuarioA.email;
		document.querySelector('#formacceso [name="log"]').value=_UsuarioA.log;
		if(_UsuarioA.isopais!=''){
			document.querySelector('#formacceso [name="isopais"]').value=_UsuarioA.isopais;
		}else{
			document.querySelector('#formacceso [name="isopais"]').value='AR';
		}
		document.querySelector('#formacceso [name="numeroid"]').value=_UsuarioA.numeroid;
		
		document.querySelector('#formacceso [name="pronombre"]').value=_UsuarioA.pronombre;
		
		if(
			_UsuarioA.pronombre!='' 
			&&
			document.querySelector('#formacceso [name="pronombre"] option[value="'+_UsuarioA.pronombre+'"]')==null
		){
			document.querySelector('#formacceso [name="pronombre"]').value='__otro__';
			document.querySelector('#formacceso [name="pronombre"]').onchange();
			document.querySelector('#formacceso [name="pronombre_otro"]').value=_UsuarioA.pronombre;
		}
		
	}
}
    
function handleKeyPressUsuario(e){
    var key=e.keyCode || e.which;
    if (key==13){
        document.getElementById('inputUsuarioLogPass').focus();
    }
}

function handleKeyPressPass(e){
    var key=e.keyCode || e.which;
    if (key==13){
        document.getElementById('acceder').focus();
    }
}


function ampliarUsu(_this){
	document.querySelector('#formacceso').setAttribute('registra',_modo);
	
}

function verayuda(_this){
	_this.parentNode.querySelector('#ayuda').style.display='block';
}

function acceder(_this){		
	
	var parametros = {
		'log': _this.parentNode.parentNode.querySelector('input[name="log"]').value,
		'password': _this.parentNode.parentNode.querySelector('input[name="password"]').value
	};
	
	$.ajax({
		data:  parametros,
		url:   './sis_usuarios/usu_acceso_ajax.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);
			if(_res.res=='exito'){	
				_UsuarioA= _res.data;
				cargarusuario();
				_UsuarioAcceso= _res.data.permisos.acc;
				_UsuarioProyectos= _res.data.permisos.marcos;
				actualizarPermisos();//en index_consultas.php
				cerrarVentana(document.querySelector('#formacceso'));
				
				verificarPerfilCompleto();
				console.log('o');
				if (typeof reingresaGeneral === "function") { 
					console.log('u');
					reingresaGeneral();
				}
			}else{
				alert('error')
			}
		}
	});	
}

function verificarPerfilCompleto(){
	if(
		_UsuarioA.apellido==''
		||
		_UsuarioA.email==''
		||
		_UsuarioA.isopais==''
		||
		_UsuarioA.nombre==''
		||
		_UsuarioA.numeroid==''
		||
		_UsuarioA.pronombre==''		
	){
		if(confirm('Nos falta alguno de tus dato de perfil. ¿Podrías completarlos ahora?')){
			formUsuario("edita");
		}
	}
	
}

function salir(){		
	
	var parametros = {
	};
	
	$.ajax({
		data:  parametros,
		url:   './sis_usuarios/usu_salir_ajax.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);	
			_UsuarioA= Array();
			_UsuarioA.nombre= "Anónimo";
			_UsuarioA.apellido= "";
			_UsuarioAcceso=null;
			cargarusuario();
			actualizarPermisos();//en index_consultas.php
			cerrarVentana(document.querySelector('#formacceso'));
			

			
		}
	});

}
