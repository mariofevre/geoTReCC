//funciones de envio y salida del formulario
	
	function editarActor(_this){
		alert('en desarrollo');	
	}
	
	function limpiarFormPlan(){
		document.querySelector('#form_pla').style.display='none';
		//console.log('en desarrollo');
				
		document.querySelector('#form_pla input[name="id"]').value='';
		document.querySelector('#form_pla input[name="numero"]').value='';
		document.querySelector('#form_pla input[name="nombre"]').value='';
		document.querySelector('#form_pla input[name="nivel"]').value='';
		document.querySelector('#form_pla div .sugerencia.uno').innerHTML='<a aid="0" onclick="cargarOpcion(this);">-vacio-</a><br>';
		document.querySelector('#form_pla div .sugerencia.dos').innerHTML='';
		
		document.querySelector('#form_pla .paquete #listaestados').innerHTML='';
		
		document.querySelector('#form_pla #adjuntos #listadosubiendo').innerHTML='';
		document.querySelector('#form_pla #adjuntos #adjuntoslista').innerHTML='';

		document.querySelector('#form_pla #menumover').setAttribute('activo','no');
		document.querySelector("#form_pla #menumover #listamover").innerHTML='';
	}
		
	function cancelarPlan(_this){
		_this.parentNode.style.display='none';
		limpiarFormPlan();	
	}
	
	function eliminarPlan(_this){
		
		_id= document.querySelector('#form_pla input[name="id"]').value;
		_nivel= document.querySelector('#form_pla input[name="nivel"]').value;
		_cont=document.querySelectorAll('div[nivel="'+_nivel+'"][iddb="'+_id+'"] > div.contenidos > div');
		//console.log('div[nivel="'+_nivel+'"][iddb="'+_id+'"] > contenidos > div');
		//console.log(_cont);
		_cuenta=0;
		for(_in in _cont){
			if(typeof _cont[_in] != 'object'){continue;}
			_cuenta++;
		}
		if(_cuenta>1){
			alert('este componente tiene subcomponente. Antes debe eliminar los subcomponentes');
			return;
		}
		if(confirm("¿Realmente querés eliminar este componente?")){
			
			_params={
            	'panid': _PanId,
				"id":_id,
				"nivel":_nivel
			}
			$.ajax({
				data:_params,
				url:'./app_plae/app_plae_ed_borra_plan.php',
				type:'post',
				error: function (requestObject, error, errorThrown) {alert('error al contactar al servidor');console.log(requestObject)},
                success:  function (response,status,xhr) {
                	_res = PreprocesarRespuesta(response);
					
					if(_res.res='exito'){
						quitarFila(_res.data.nivel,_res.data.id);
						_this.parentNode.style.display='none';
						limpiarFormPlan();
					}
				}
			});
		}	
	}
	
	function quitarFila(_nivel,_id){
		//en desarrollo;
		_elem=document.querySelector('div[nivel="'+_nivel+'"][iddb="'+_id+'"]');
		_elem.parentNode.removeChild(_elem);
	}
		
		
	function aCero(_sub){
		_sub.style.right='0';
		_sub.style.bottom='0';
	}
			
				
	function guardarPlan(_this){
		_form=document.querySelector('#form_pla');
		
		_subs=_form.querySelectorAll('.archivo[subiendo="si"]');
		for(_sn in _subs){
			if(typeof(_subs[_sn])!='object'){continue;}
			_pos=_subs[_sn].getBoundingClientRect();console.log(_pos);		
			document.querySelector('#coladesubidas').appendChild(_subs[_sn]);
			_subs[_sn].style.position='relative';
			_sr=($(window).width() - _pos.right )+'px'
			_subs[_sn].style.right =_sr;
			_sh=($(window).height() - _pos.bottom)+'px';
			_subs[_sn].style.bottom=_sh;
			//console.log(_sr);console.log(_sh);		
			setTimeout(aCero, 1, _subs[_sn]);
		}
			
						
		_innn=_form.querySelectorAll('input');
		_param={};		
		for(_nin in _innn){
			if(typeof _innn[_nin] != 'object'){continue;}
			if(_innn[_nin].getAttribute('type')=='button'){continue;}
			if(_innn[_nin].getAttribute('type')=='checkbox'){continue;}
			if(_innn[_nin].getAttribute('type')=='submit'){continue;}
			if(_innn[_nin].getAttribute('type')=='radio'){
				if(!_innn[_nin].selected){
					continue;
				}
			}
			if(_innn[_nin].getAttribute('exo')=='si'){continue;}
			if(_innn[_nin].getAttribute('name')==undefined){
				console.log('le falta name al siguiente:');
				console.log(_innn[_nin]);
				continue;
			}
			
			_name=_innn[_nin].getAttribute('name');
			_param[_name]=_innn[_nin].value;
		}
		
		
		//accion para absorber código basura generado por editores de texto al copiar pegar
		var editor = tinymce.get('descripcion'); // use your own editor id here - equals the id of your textarea
		_con=$('#form_pla #descripcion').html(editor.getContent({format: 'html'}));
		//_con=editor.getContent({format: 'html'});
		/*
		//console.log('_con:');
		_contcrudo = _con['0'].textContent;
		
		//console.log('_contcrudo:');
		//console.log(_contcrudo);
					
		_result=Array();			
		_regex=/<!-- \[if([^]+)<!\[endif]-->/g;
		if(new RegExp(_regex).test(_contcrudo)){
			_result = _contcrudo.match(_regex).map(function(val){
		   		return  val;
			});
		}			
		for(_nc in _result){
			//console.log('_nc:'+_nc);
			_contcrudo=_contcrudo.replace(_result[_nc],'');
		}
		_contcrudo=_contcrudo.replace('<p>&nbsp;</p>','');
		*/
		_contcrudo = _con['0'].innerHTML;
		//console.log(_contcrudo);
		$('#form_pla #descripcion').html(editor.setContent(_contcrudo, {format: 'HTML'}));	
		_param.descripcion=_contcrudo;
	
		var _comid=_param.id;
		
		if(_this.value=='guardar'){
			_param['modo']='actualizar';
		}else{
			_param['modo']='insertar';
		}
		
		$.ajax({
			data:_param,
			type:'post',
			url:'./app_plae/app_plae_ed_guarda_plan.php',
			error: function (requestObject, error, errorThrown) {alert('error al contactar al servidor');console.log(requestObject)},
            success:  function (response,status,xhr) {
            	_res = PreprocesarRespuesta(response);
				limpiarFormPlan();
				cargarPlan(_res.data.id,_res.data.nivel,_res.data.modo);
			}
		})
	}
	
	
	function guardarPlanEnca(_this){
		_form=document.querySelector('#form_pla_encabeza');
				
		var editor = tinymce.get('encabezado'); // use your own editor id here - equals the id of your textarea
		_con=$('#' + 'descripcion').html(editor.getContent({format: 'html'}));
		_contcrudo = _con['0'].innerHTML;
		$('#encabezado').html(editor.setContent(_contcrudo, {format: 'HTML'}));
		
					
		_param={
			
			'panid': _PanId,
			'encabezado':_contcrudo
		}
			
		$.ajax({
			data:_param,
			type:'post',
			url:'./app_plae/app_plae_ed_guarda_plan_encabezado.php',
			error: function (requestObject, error, errorThrown) {alert('error al contactar al servidor');console.log(requestObject)},
            success:  function (response,status,xhr) {
            	_res = PreprocesarRespuesta(response);
				limpiarFormPlan();
				cargarPlan(_res.data.id,_res.data.nivel,_res.data.modo);
			}
		})
	}
	
	
	function moverNivelPlan(_id,_nivel,_id_dest){
		_param={
			'panid': _PanId,
			'id':_id,
			'nivel':_nivel,
			'id_dest':_id_dest
		}		
		
		$.ajax({
			data:_param,
			type:'post',
			url:'./app_plae/app_plae_ed_mueve_plan.php',
			error: function (requestObject, error, errorThrown) {alert('error al contactar al servidor');console.log(requestObject)},
            success:  function (response,status,xhr) {
            	_res = PreprocesarRespuesta(response);
				limpiarFormPlan();
				borrarPlan(_res.data.id,_res.data.nivel);
				cargarPlan(_res.data.id,_res.data.nivel,'insertar');
			}
		})
		
	}
	
	
	
	function subirNivel(_this){
		
		_form=document.querySelector('#form_pla');
		_idp=_form.querySelector('[name="id"]').value;
		_nivel=_form.querySelector('[name="nivel"]').value;
		
		console.log(_idp);
		
		_subs=_form.querySelectorAll('.archivo[subiendo="si"]');
		for(_sn in _subs){
			
			if(typeof(_subs[_sn])!='object'){continue;}
			console.log('subiendo en curso:'+_sn);	
			_pos=_subs[_sn].getBoundingClientRect();console.log(_pos);		
			document.querySelector('#coladesubidas').appendChild(_subs[_sn]);
			_subs[_sn].style.position='relative';
			_sr=($(window).width() - _pos.right )+'px'
			_subs[_sn].style.right =_sr;
			_sh=($(window).height() - _pos.bottom)+'px';
			_subs[_sn].style.bottom=_sh;
			//console.log(_sr);console.log(_sh);		
			setTimeout(aCero, 1, _subs[_sn]);
		}
		
		
		_param={
			'id':_idp,
			'nivel':_nivel
		}
		
		$.ajax({
			data:_param,
			type:'post',
			url:'./app_plae/app_plae_ed_nivel_sube_plan.php',
			error: function (requestObject, error, errorThrown) {alert('error al contactar al servidor');console.log(requestObject)},
            success:  function (response,status,xhr) {
            	_res = PreprocesarRespuesta(response);
				limpiarFormPlan();
				
				borrarPlan(_res.data.id,_res.data.nivel);
				cargarPlan(_res.data.nid,_res.data.nnivel,'insertar');
			}
		})
	}


	
	function bajarNivel(_id,_nivel,_id_dest,_nivel_dest){
		
		_form=document.querySelector('#form_pla');
		
		_subs=_form.querySelectorAll('.archivo[subiendo="si"]');
		for(_sn in _subs){
			
			if(typeof(_subs[_sn])!='object'){continue;}
			console.log('subiendo en curso:'+_sn);	
			_pos=_subs[_sn].getBoundingClientRect();console.log(_pos);		
			document.querySelector('#coladesubidas').appendChild(_subs[_sn]);
			_subs[_sn].style.position='relative';
			_sr=($(window).width() - _pos.right )+'px'
			_subs[_sn].style.right =_sr;
			_sh=($(window).height() - _pos.bottom)+'px';
			_subs[_sn].style.bottom=_sh;
			//console.log(_sr);console.log(_sh);		
			setTimeout(aCero, 1, _subs[_sn]);
		}
		
		
		_param={
			'id':_id,
			'nivel':_nivel,
			'id_dest':_id_dest,
			'nivel_dest':_nivel_dest
		}
		
		$.ajax({
			data:_param,
			type:'post',
			url:'./app_plae/app_plae_ed_nivel_baja_plan.php',
			error: function (requestObject, error, errorThrown) {alert('error al contactar al servidor');console.log(requestObject)},
            success:  function (response,status,xhr) {
            	_res = PreprocesarRespuesta(response);
				limpiarFormPlan();
				
				borrarPlan(_res.data.id,_res.data.nivel);
				cargarPlan(_res.data.nid,_res.data.nnivel,'insertar');
			}
		})
	}
///funciones cargar el formulario
var _PlaCargada={};
function iraPlan(_id,_nivel,_idpadre){

	if(_HabilitadoEdicion!='si'){
		alert('su usuario no tiene permisos de edicion');
		return;
	}
	
	_form=document.querySelector('#form_pla');
	_form.style.display='block';
	
	if(_id==''){
		//creando nuevo
		if(_nivel!='PLAn1' && _idpadre==''){
			alert('error, vuelva a intentar. No hemos identificado la ubicación del elemento a crear.');
			_form.style.display='none';
			return;
		}
		_form.querySelector('#ejec').value='crear';
		
	}else{
		_this=document.querySelector('div[nivel="'+_nivel+'"][iddb="'+_id+'"]');
		_form.querySelector('#ejec').value='guardar';	
	}
	
	
	_params={
        'panid': _PanId,
		"id":_id,
		"nivel":_nivel,
		"id_p_PLA":_idpadre //solo para crear un nuevo elemento, indica donde crearlo.
	};			
	$.ajax({
		data:_params,
		url:'./app_plae/app_plae_consulta_componente.php',
		type:'post',
		error: function (requestObject, error, errorThrown) {alert('error al contactar al servidor');console.log(requestObject)},
        success:  function (response,status,xhr) {
        	_res = PreprocesarRespuesta(response);
			
			_PlaCargada=_res.data.componente;
			console.log(_PlaCargada);
			formularPlanCargado();	
			
		}
	})			
}

function borraEstado(_this){
	if(!confirm("¿Comfirmás que querés eliminar el registro de que ha existido este estado?")){
		return;	
	}
	_this.parentNode.getAttribute('ide');
	alert('funcion en dearrollo');
	
}


function guardarPlanGral(_this){
	console.log('u');
	_form=document.querySelector('#form_pla_gral');						
	_form.style.display='none';
	_param={
		"nombre":_form.querySelector('input[name="nombre"]').value,
		"panid":_PanId
	};
	//accion para absorber código basura generado por editores de texto al copiar pegar
	var editor = tinymce.get('descripciongral'); // use your own editor id here - equals the id of your textarea
	_con=$('#' + 'descripciongral').html( editor.getContent({format: 'html'}));
	console.log(_con['0']);
	_contcrudo = _con['0'].textContent;		
	/*
	_result=Array();			
	_regex=/<!-- \[if([^]+)<!\[endif]-->/g;

	if(new RegExp(_regex).test(_contcrudo)){
		_result = _contcrudo.match(_regex).map(function(val){
	   		return  val;
		});
	}			
	for(_nc in _result){
		//console.log('_nc:'+_nc);
		_contcrudo=_contcrudo.replace(_result[_nc],'');
	}
	_contcrudo=_contcrudo.replace('<p>&nbsp;</p>','');*/
	_param.descripcion=_contcrudo;
	
	$.ajax({
		data:_param,
		type:'post',
		url:'./app_plae/app_plae_ed_guarda_plan_general.php',
		error: function (requestObject, error, errorThrown) {alert('error al contactar al servidor');console.log(requestObject)},
        success:  function (response,status,xhr) {
        	_res = PreprocesarRespuesta(response);
			limpiarFormPlan();
			cargarPlan(_res.data.id,_res.data.nivel,_res.data.modo);
		}
	})
}


function crearCategoria(_id_estandar,_nombre){
	
	_nivel=document.querySelector('#form_pla input[name="nivel"]').value;
	
	_params={
        'panid': _PanId,
		"nivel":_nivel,
		"id_p_PLAcategoria_estandar":_id_estandar,
		'nombre':_nombre
	};	
			
	$.ajax({
		data:_params,
		url:'./app_plae/app_plae_ed_crea_categoria.php',
		type:'post',
		error: function (requestObject, error, errorThrown) {alert('error al contactar al servidor');console.log(requestObject)},
        success:  function (response,status,xhr) {
        	
        	_res = PreprocesarRespuesta(response);
        	
        	consultarCategorias();
        	
		}
	})
}
