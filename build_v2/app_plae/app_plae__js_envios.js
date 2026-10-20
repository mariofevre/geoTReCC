//funciones de envio y salida del formulario
	
	function editarActor(_this){
		alert('en desarrollo');	
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
            	'codMarco': _CodMarco,
				"id":_id,
				"nivel":_nivel
			}
			
					
			_url='./app_plae/app_plae_ed_borra_plan.php',
			_cn = consultasPHP_nueva(_url);  
			$.ajax({
				url:   _url,
				type:  'post',
				data: _parametros,

				beforeSend: function(request, settings){
						request._data = {'cn':_cn};
				},
				error:  function (request, status, errorThrown){	
						_cn = request._data.cn;
						consultasPHP_respuesta("err",_cn);	
				},
				success:  function (response, status, request){		
					 var _res = $.parseJSON(response);		
					_cn = request._data.cn;			
					consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
			
					quitarFila(_res.data.nivel,_res.data.id);
					_this.parentNode.style.display='none';
					limpiarFormPlan();
				}
			})					
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
		
				
		if(_form.querySelector('#subform_indicador').getAttribute('activo')=='si'){
			alert('debe cerrar el formulario de indicador antes de avanzar con esta acción');
			return;
		}
		
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
		
		
		_param["id_p_rele_campa"]=_form.querySelector('[name="id_p_rele_campa"]').value;
		_param["id_p_rele_campo"]=_form.querySelector('[name="id_p_rele_campo"]').value;
		if(_param["id_p_rele_campo"]==''){_param["id_p_rele_campo"]='0';}
		
		
		//accion para absorber código basura generado por editores de texto al copiar pegar
				
		_param.descripcion=_EditorP.value;
		_param.codMarco=_CodMarco;
	
		var _comid=_param.id;
		
		if(_this.value=='guardar'){
			_param['modo']='actualizar';
		}else{
			_param['modo']='insertar';
		}
		
		
		
		
		_url='./app_plae/app_plae_ed_guarda_plan.php',
		_cn = consultasPHP_nueva(_url);  
		$.ajax({
			url:   _url,
			type:  'post',
			data: _param,

			beforeSend: function(request, settings){
					request._data = {'cn':_cn};
			},
			error:  function (request, status, errorThrown){	
					_cn = request._data.cn;
					consultasPHP_respuesta("err",_cn);	
			},
			success:  function (response, status, request){		
				 var _res = $.parseJSON(response);		
				_cn = request._data.cn;			
				consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
				
				limpiarFormPlan();
				cargarPlan(_res.data.id,_res.data.nivel,_res.data.modo);
			}
		})		
	}
	
		
				
	function guardaLinkInd(){
		_form=document.querySelector('#form_pla');
		_subform=document.querySelector('#form_pla #subform_indicador');
		
		_pv=_subform.querySelector('[name="periodos_cargados"]').value;
		_p=_pv.split("/");
		if(_pv==''){_p[0]=0;_p[1]=0;}
		if(_subform.querySelector('[name="conobjetivo"]').checked){
			_o=_subform.querySelector('[name="objetivo"]').value;
		}else{
			_o=null;
		}
		_id_lk=_subform.getAttribute('id_link');
		_param={
			'id_comp': _form.querySelector('[name="id"]').value,
			'nivel':   _form.querySelector('[name="nivel"]').value,
			'codMarco':_CodMarco,
			'id_lk':_id_lk,
			'id_p_ref_indicadores_indicadores':_subform.querySelector('[name="id_p_ref_indicadores_indicadores"]').value,
			'caracter':_subform.querySelector('[name="caracter"]').value,
			'ldb_ano':_p[0],
			'ldb_mes':_p[1],
			'ldb_valor':_subform.querySelector('[name="ldb_valor"]').value,
			'objetivo':_o
		}		
		
		_c=_subform.querySelector('[name="caracter"]').value;
		_idi=_subform.querySelector('[name="id_p_ref_indicadores_indicadores"]').value;
		_n=_DataInd[_idi].nombre;
		_form.querySelector('#lista_indicadores [id_lk="'+_id_lk+'"]').innerHTML='<b>'+_c+'</b> - <b>'+_idi+'</b>'+' - '+_n;
		limpiarSubFormInd();
		
		_url='./app_plae/app_plae_ed_guarda_link_indicador.php',
		_cn = consultasPHP_nueva(_url);  
		$.ajax({
			url:   _url,
			type:  'post',
			data: _param,

			beforeSend: function(request, settings){
					request._data = {'cn':_cn};
			},
			error:  function (request, status, errorThrown){	
					_cn = request._data.cn;
					consultasPHP_respuesta("err",_cn);	
			},
			success:  function (response, status, request){		
				 var _res = $.parseJSON(response);		
				_cn = request._data.cn;			
				consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
				limpiarSubFormInd();
			}
		})			
	}		
			
	function borraLinkInd(){
		_form=document.querySelector('#form_pla');
		_subform=document.querySelector('#form_pla #subform_indicador');		
		_id_lk=_subform.getAttribute('id_link');
		_param={
			'id_comp': _form.querySelector('[name="id"]').value,
			'nivel':   _form.querySelector('[name="nivel"]').value,
			'codMarco':_CodMarco,
			'id_lk':_id_lk,
		}		
		
		_a=_form.querySelector('#lista_indicadores [id_lk="'+_id_lk+'"]');
		_a.parentNode.removeChild(_a);
		limpiarSubFormInd();
		
		_url='./app_plae/app_plae_ed_borra_link_indicador.php',
		_cn = consultasPHP_nueva(_url);  
		$.ajax({
			url:   _url,
			type:  'post',
			data: _param,

			beforeSend: function(request, settings){
					request._data = {'cn':_cn};
			},
			error:  function (request, status, errorThrown){	
					_cn = request._data.cn;
					consultasPHP_respuesta("err",_cn);	
			},
			success:  function (response, status, request){		
				 var _res = $.parseJSON(response);		
				_cn = request._data.cn;			
				consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
				limpiarSubFormInd();
			}
		})	
		
	}	
				
	function agregarSubIndicador(){
		
		_form=document.querySelector('#form_pla');
			
		_param={
			'id_comp': _form.querySelector('[name="id"]').value,
			'nivel':   _form.querySelector('[name="nivel"]').value,
			'codMarco':_CodMarco
		}
		_url='./app_plae/app_plae_ed_crea_link_indicador.php',
		_cn = consultasPHP_nueva(_url);  
		$.ajax({
			url:   _url,
			type:  'post',
			data: _param,

			beforeSend: function(request, settings){
					request._data = {'cn':_cn};
			},
			error:  function (request, status, errorThrown){	
					_cn = request._data.cn;
					consultasPHP_respuesta("err",_cn);	
			},
			success:  function (response, status, request){		
				 var _res = $.parseJSON(response);		
				_cn = request._data.cn;			
				consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
				
				_form=document.querySelector('#form_pla');
				_data={
					'caracter':	null,
					'descripcion':	null,
					'fechadesde': null,
					'fechahasta': null,
					'funcionalidad': null,
					'id_ind': null,
					'id_lk':	_res.data.nid,
					'id_p_ref_capasgeo': null,
					'idcomp':	_form.querySelector('[name="id"]').value,
					'ldb_ano': null,
					'ldb_mes': null,
					'ldb_valor': null,
					'nombre': null,
					'objetivo': null,
					'periodicidad': null
				}
				
				_PlaCargada.indicadores[_res.data.nid]=_data;
				
				
				_c='';
				_idi='N';
				_n='- indefinido -';
				
				
				_a = document.createElement('a');
				_a.setAttribute('idind','null');
				_a.setAttribute('id_lk',_res.data.nid);
				_a.setAttribute('onclick','formularSubInd(this.getAttribute("id_lk"))');
				_a.innerHTML='<b>'+_c+'</b> - <b>'+_idi+'</b>'+' - '+_n;
				_form.querySelector('#lista_indicadores').appendChild(_a);
				limpiarSubFormInd();
				
				formularSubInd(_res.data.nid);
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
			
			'codMarco': _CodMarco,
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
			'codMarco': _CodMarco,
			'id':_id,
			'nivel':_nivel,
			'id_dest':_id_dest
		}		
		
		_url='./app_plae/app_plae_ed_mueve_plan.php';
		_cn = consultasPHP_nueva(_url);  
		$.ajax({
			url:   _url,
			type:  'post',
			data: _param,

			beforeSend: function(request, settings){
					request._data = {'cn':_cn};
			},
			error:  function (request, status, errorThrown){	
					_cn = request._data.cn;
					consultasPHP_respuesta("err",_cn);	
			},
			success:  function (response, status, request){		
				 var _res = $.parseJSON(response);		
				_cn = request._data.cn;			
				consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
				
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
			'codMarco': _CodMarco,	
			'id':_idp,
			'nivel':_nivel
		}
		
		
		_url='./app_plae/app_plae_ed_nivel_sube_plan.php';
		_cn = consultasPHP_nueva(_url);  
		$.ajax({
			url:   _url,
			type:  'post',
			data: _param,

			beforeSend: function(request, settings){
					request._data = {'cn':_cn};
			},
			error:  function (request, status, errorThrown){	
					_cn = request._data.cn;
					consultasPHP_respuesta("err",_cn);	
			},
			success:  function (response, status, request){		
				 var _res = $.parseJSON(response);		
				_cn = request._data.cn;			
				consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
				
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
			'codMarco': _CodMarco,	
			'nivel':_nivel,
			'id_dest':_id_dest,
			'nivel_dest':_nivel_dest
		}
		
		_url='./app_plae/app_plae_ed_nivel_baja_plan.php';
		_cn = consultasPHP_nueva(_url);  
		$.ajax({
			url:   _url,
			type:  'post',
			data: _param,

			beforeSend: function(request, settings){
					request._data = {'cn':_cn};
			},
			error:  function (request, status, errorThrown){	
					_cn = request._data.cn;
					consultasPHP_respuesta("err",_cn);	
			},
			success:  function (response, status, request){		
				 var _res = $.parseJSON(response);		
				_cn = request._data.cn;			
				consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
				
				
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
	
	
	_parametros={
        'codMarco': _CodMarco,
		"id":_id,
		"nivel":_nivel,
		"id_p_PLA":_idpadre //solo para crear un nuevo elemento, indica donde crearlo.
	};	

 
	_url='./app_plae/app_plae_consulta_componente.php',
	_cn = consultasPHP_nueva(_url);  
	$.ajax({
		url:   _url,
		type:  'post',
		data: _parametros,

		beforeSend: function(request, settings){
				request._data = {'cn':_cn};
		},
		error:  function (request, status, errorThrown){	
				_cn = request._data.cn;
				consultasPHP_respuesta("err",_cn);	
		},
		success:  function (response, status, request){		
			 var _res = $.parseJSON(response);		
			_cn = request._data.cn;			
			consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
			
			_PlaCargada=_res.data.componente;
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

	_form=document.querySelector('#form_pla_gral');						
	_form.style.display='none';
	_parametros={
		"nombre":_form.querySelector('input[name="nombre"]').value,
		"codMarco":_CodMarco,
		"descripcion":_EditorGral.value
	};
	//accion para absorber código basura generado por editores de texto al copiar pegar
	

	_url='./app_plae/app_plae_ed_guarda_plan_general.php',
	_cn = consultasPHP_nueva(_url);  
	$.ajax({
		url:   _url,
		type:  'post',
		data: _parametros,

		beforeSend: function(request, settings){
				request._data = {'cn':_cn};
		},
		error:  function (request, status, errorThrown){	
				_cn = request._data.cn;
				consultasPHP_respuesta("err",_cn);	
		},
		success:  function (response, status, request){		
			 var _res = $.parseJSON(response);		
			_cn = request._data.cn;			
			consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
	
			limpiarFormPlan();
			cargarPlan(_res.data.id,_res.data.nivel,_res.data.modo);
		}
	})
}


function crearCategoria(_id_estandar,_nombre){
	
	_nivel=document.querySelector('#form_pla input[name="nivel"]').value;

	
	_parametros={
        'codMarco': _CodMarco,
		"nivel":_nivel,
		"id_p_PLAcategoria_estandar":_id_estandar,
		'nombre':_nombre
	};	

	_url='./app_plae/app_plae_ed_crea_categoria.php',
	_cn = consultasPHP_nueva(_url);  
	$.ajax({
		url:   _url,
		type:  'post',
		data: _parametros,

		beforeSend: function(request, settings){
				request._data = {'cn':_cn};
		},
		error:  function (request, status, errorThrown){	
				_cn = request._data.cn;
				consultasPHP_respuesta("err",_cn);	
		},
		success:  function (response, status, request){		
			 var _res = $.parseJSON(response);		
			_cn = request._data.cn;			
			consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);        	
        	consultarCategorias();
        	
		}
	})
}



function consultarReleListado(){
	
	_parametros={
        'codMarco': _CodMarco
	};	

	_url='./app_plae/app_plae_consulta_rele_listado.php',
	_cn = consultasPHP_nueva(_url);  
	$.ajax({
		url:   _url,
		type:  'post',
		data: _parametros,

		beforeSend: function(request, settings){
				request._data = {'cn':_cn};
		},
		error:  function (request, status, errorThrown){	
				_cn = request._data.cn;
				consultasPHP_respuesta("err",_cn);	
		},
		success:  function (response, status, request){		
			 var _res = $.parseJSON(response);		
			_cn = request._data.cn;			
			consultasPHP_respuesta("exito",_cn,_res.mg,_res.res); 
			
        	_DataRele=_res.data;
		}
	})
}



function consultarIndicadoresListado(){
	
	_parametros={
        'codMarco': _CodMarco
	};	

	_url='./app_plae/app_plae_consulta_indicadores_listado.php',
	_cn = consultasPHP_nueva(_url);  
	$.ajax({
		url:   _url,
		type:  'post',
		data: _parametros,

		beforeSend: function(request, settings){
				request._data = {'cn':_cn};
		},
		error:  function (request, status, errorThrown){	
				_cn = request._data.cn;
				consultasPHP_respuesta("err",_cn);	
		},
		success:  function (response, status, request){		
			 var _res = $.parseJSON(response);		
			_cn = request._data.cn;			
			consultasPHP_respuesta("exito",_cn,_res.mg,_res.res); 
			
        	_DataInd=_res.data;
		}
	})
}
