/////////////////////
///funciones para cargar información base
////////////////////

function cargaBase(_accion){
	    var _accion=_accion;
	
	    if(_accion=='cargainicial'){
	        document.querySelector('#contenidoextenso > .hijos').innerHTML="<div id='siblingzero'></div>";
	    }
	
	    _parametros = {
	        'idMarco': _IdMarco,
	        'codMarco': _CodMarco
	    };

		_cn = consultasPHP_nueva('./app_plan/app_plan_consulta.ph');  
	    $.ajax({
	        url:   './app_plan/app_plan_consulta.php',
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
				if(_res.res!='exito'){return;} 

				_Items=_res.data.psdir;
				_Orden=_res.data.orden;
				if(_accion=='cargainicial'){
						generarItemsHTML();
						cargaDocumentos();
				}else if(_accion=='actualizaresponsables'){
						mostrarResponsables();
				}else if(_accion=='actualizarprogreso'){
						mostrarProgreso();
				}
	        }
	    });
	}

cargaBase('cargainicial');
	
function actualizarItem(_res){
	    var _preres = _res;
	    _parametros = {
	            'idMarco': _IdMarco,
	            'codMarco': _CodMarco
	    };

		_cn = consultasPHP_nueva('./app_plan/app_plan_consulta.php');  	
	    $.ajax({
	        url:   './app_plan/app_plan_consulta.php',
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
				if(_res.res!='exito'){return;}          
			
				cargarValoresCapaExist(_res);	
			
				_Items=_res.data.psdir;
				_Orden=_res.data.orden;
				document.querySelector('.item[idit="'+_preres.data.idit+'"] > h3').innerHTML=_res.data.psdir[_preres.data.idit].nombre;
				document.querySelector('.item[idit="'+_preres.data.idit+'"] > p').innerHTML=_res.data.psdir[_preres.data.idit].descripcion;
			}
		});
	}
	
function cargaNuevoItem(_res){
	var _preres = _res;
	_parametros = {
			'idMarco': _IdMarco,
			'codMarco': _CodMarco
	};

	_cn = consultasPHP_nueva('./app_plan/app_plan_consulta.php');  
	$.ajax({
		data: _parametros,
		url:   './app_plan/app_plan_consulta.php',
		type:  'post',

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
			if(_res.res!='exito'){return;}          

			_Items=_res.data.psdir;
			_Orden=_res.data.orden;

			_dat=_Items[_preres.data.nid];
			_clon=document.querySelector('#modelos .item').cloneNode(true);
			_clon.setAttribute('idit',_preres.data.nid);
			_clon.querySelector('h3').innerHTML=_dat.nombre;
			if(_dat.descripcion==null){_dat.descripcion='- planificación sin descripción -';}
			_clon.querySelector('p').innerHTML=_dat.descripcion;

			_dest=document.querySelector('#contenidoextenso > .hijos .item[idit="'+_dat.id_p_sis_planif_plan+'"] > .hijos');
			if(_dest==null){
				_dest=document.querySelector('#contenidoextenso > .hijos');
			}
			_niv=_dest.parentNode.getAttribute('nivel');
			console.log('_dat.nombre:'+_niv);
			_niv++;
			console.log('>>'+_niv);
			_clon.setAttribute('nivel',_niv);
			_dest.appendChild(_clon);

			_esp=document.createElement('div');				
			_esp.setAttribute('class','medio');
			//_esp.innerHTML='<div class="submedio"></div>';
			_esp.setAttribute('ondragover',"allowDrop(event,this);resaltaHijos(event,this)");
			_esp.setAttribute('ondragleave',"desaltaHijos(this)");
			_esp.setAttribute('ondrop',"drop(event,this)");  
			
			_esp=document.createElement('div');
			_esp.setAttribute('class','medioA');
			//_esp.innerHTML='<div class="submedio"></div>';
			_esp.setAttribute('ondragover',"allowDrop(event,this);resaltaHijos(event,this)");
			_esp.setAttribute('ondragleave',"desaltaHijos(this)");
			_esp.setAttribute('ondrop',"drop(event,this)");
			
			_dest.insertBefore(_esp,_clon);


			$('html, body').animate({
				scrollTop: $("div[idit='"+_preres.data.nid+"']").offset().top
			}, 2000);

			nivelar();
			numerar();
		}
	});
}	
/////////////////////
/////////////////////
///funciones para cargar documentos
////////////////////

function cargaDocumentos(){
	var parametros = {
		'idMarco': _IdMarco,
		'codMarco': _CodMarco
	};

	_cn = consultasPHP_nueva('./app_docs/app_docs_consulta.php');  
	$.ajax({
		url:   './app_docs/app_docs_consulta.php',
		type:  'post',
		data: parametros,

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
			if(_res.res!='exito'){return;} 
			_DocItems=respuesta.data.psdir;
			_DocOrden=respuesta.data.orden;
		}
	});
}

function consultarDocumentosAsociados(accion, _this){
	var parametros = {
		'idMarco': _IdMarco,
		'codMarco': _CodMarco,
		'idactividad': _idit
	};

	_cn = consultasPHP_nueva('./app_plan/app_plan_doc_asociados_consulta.php');  
	$.ajax({
		url:  './app_plan/app_plan_doc_asociados_consulta.php',
		type: 'post',
		data: parametros,

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
			if(_res.res!='exito'){return;}
				
			_DocAsoc = respuesta.data.psdir;
			resolverDocumentosAsociados(accion, _this);
		}
	});
}
	
function guardarCambiosCarpeta(tipoDoc,idcarp,idit,nuevoestado,comentario){
	_parametros = {
		"codMarco":_CodMarco,
		"idcarp":idcarp,
		"idit":idit,
		"nuevoestado":nuevoestado,
		"comentario":comentario
	};
	
	var postUrl = '';
	if(tipoDoc != ''){
		if(tipoDoc == 'carpeta'){
			postUrl = './app_plan/app_plan_docitem_asociados_guardar.php';
		} else {
			alert('error');
			return;
		}
	}

	_cn = consultasPHP_nueva(postUrl);  
	$.ajax({
		url:   postUrl,
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
			if(_res.res!='exito'){return;} 
								
			document.querySelector('#incluidos div[idcarp="'+_res.data.idcarp+'"] input').setAttribute('estado','ok');
			consultarDocumentosAsociados('actualizarDocumentosAsociados', this);
		}
	});			
}

function guardarCambiosDocs(tipoDoc,iddoc,idit,nuevoestado,comentario){
	_parametros = {
		"codMarco":_CodMarco,
		"iddoc":iddoc,
		"idit":idit,
		"nuevoestado":nuevoestado,
		"comentario":comentario
	};
	
	var postUrl = '';
	if(tipoDoc != ''){
		if(tipoDoc == 'documento'){
			postUrl = './app_plan/app_plan_doc_asociados_guardar.php';
		} else if(tipoDoc == 'url'){
			postUrl = './app_plan/app_plan_doc_asociados_url_guardar.php';
		}
	}

    _cn = consultasPHP_nueva(postUrl);  
	$.ajax({
		url:   postUrl,
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
			if(_res.res!='exito'){return;}			
				
			document.querySelector('#incluidos div[iddoc="'+_res.data.iddoc+'"] input').setAttribute('estado','ok');
			consultarDocumentosAsociados('actualizarDocumentosAsociados', this);
		}
	});			
}
/////////////////////
/////////////////////
///funciones para consultar responsables
////////////////////	
function sumarResp(_this){
	var _this=_this;
	
	_parametros = {
		"idMarco": _IdMarco,
		'codMarco': _CodMarco
	};

	_cn = consultasPHP_nueva('./usuarios/usu_consulta_ajax.php');  
	$.ajax({
		url:   './usuarios/usu_consulta_ajax.php',
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
			if(_res.res!='exito'){return;}

			formResponsables(_this,_res);
		}
	});	
}

function cargarResponsablesenForm(_idit){
	_parametros={
			"codMarco":_CodMarco,
			"idit":_idit
	};
	console.log(_parametros);

	_cn = consultasPHP_nueva('./app_plan/app_plan_consulta_resp.php');  
	$.ajax({
		url:   './app_plan/app_plan_consulta_resp.php',
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
			if(_res.res!='exito'){return;}    

			for(_idusu in _res.data.resp){
				_dat=_res.data.resp[_idusu];

				if(_dat.zz_borrada=='0'){
					document.querySelector('#incluidos div[idusu="'+_idusu+'"]').setAttribute('estado','incluido');
					document.querySelector('#excluidos div[idusu="'+_idusu+'"]').setAttribute('estado','incluido');
				} else {
					document.querySelector('#incluidos div[idusu="'+_idusu+'"]').setAttribute('estado','excluido');
					document.querySelector('#excluidos div[idusu="'+_idusu+'"]').setAttribute('estado','excluido');
				}

				document.querySelector('#incluidos div[idusu="'+_idusu+'"] input').value=_dat.responsabilidad;
				document.querySelector('#excluidos div[idusu="'+_idusu+'"] input').value=_dat.responsabilidad;
			}
		}					
	});		
}	
			
function guardarCambiosResp(_idusu,_idit,_nuevoestado,_responsabilidad){
	var _idusu=_idusu;
	_parametros={
		"codMarco":_CodMarco,
		"idusu":_idusu,
		"idit":_idit,
		"nuevoestado":_nuevoestado,
		"responsabilidad":_responsabilidad
	};

	_cn = consultasPHP_nueva('./app_plan/app_plan_guardarresp.ph');  
	$.ajax({
		data: _parametros,
		url:   './app_plan/app_plan_guardarresp.php',
		type:  'post',

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
			if(_res.res!='exito'){return;}  
					
			document.querySelector('#incluidos div[idusu="'+_res.data.idusu+'"] input').setAttribute('estado','ok');
			//_Items[_res.data.idit]['responsables'][_res.data.idusu]=_res.data;
			cargaBase('actualizaresponsables');
		}
	});			
}
	

/////////////////////
///funciones para editar items
////////////////////	
	
function eliminarI(_event,_this){
	if (confirm("¿Eliminar item y sus archivos asociados? \n (los ítems anidados quedarán en la raiz)")==true){
		
		_event.preventDefault();
		
		var _this=_this;
		
		_parametros = {
			"id": _this.parentNode.querySelector('input[name="id"]').value,
			"accion": "borrar",
			"tipo": "item",
			"idMarco": _IdMarco,
			'codMarco': _CodMarco
		};

	    _cn = consultasPHP_nueva('./app_plan/app_plan_borraritem.php');  
		$.ajax({
			url:   './app_plan/app_plan_borraritem.php',
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
				if(_res.res!='exito'){return;}  
				cerrar(_this);
				//cargaBase();
				cargaElim(_res);
			}
		});
	//envía los datos para editar el ítem		
	}
}
	
function guardarI(_event,_this){// ajustado geogec
	_event.preventDefault();
	console.log(_this);
	var _this=_this;
		
	//Guardar datos del item generales
	_parametros = {
			"idMarco":_IdMarco,
			'codMarco': _CodMarco,
			"id": _this.querySelector('input[name="id"]').value,
			"nombre": _this.querySelector('input[name="nombre"]').value,
			"descripcion": _this.querySelector('[name="descripcion"]').value
	};
	
	_cn = consultasPHP_nueva('./app_plan/app_plan_cambiaritem.php');  
	$.ajax({
			url:   './app_plan/app_plan_cambiaritem.php',
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
				if(_res.res!='exito'){return;}  

				cerrar(_this.querySelector('#botoncierra'));
				actualizarItem(_res);
			}
	});
	//envía los datos para editar el ítem
		
	var estadoMasNuevo = obtenerEstadoProgreso(_idit);
	//Solo guardar estado si fue cambiado
	if (estadoMasNuevo !== null){
		porcentajeProgreso = estadoMasNuevo.porcentaje_progreso;
		nuevoProgreso = _this.querySelector('input[name="progresoNumber"]').value;
		
		fecha_propuesta = estadoMasNuevo.fecha_propuesta;
		nuevaFechaPropuesta = _this.querySelector('input[name="fechaPropuesta"]').value;
		
		if ((porcentajeProgreso == nuevoProgreso) && (fecha_propuesta == nuevaFechaPropuesta)){
			return;
		}
	}
	
	//Guardar Estado de la actividad
	_parametros = {
			"idMarco":_IdMarco,
			'codMarco': _CodMarco,
			"id": _this.querySelector('input[name="id"]').value,
			"progresoNumber": _this.querySelector('input[name="progresoNumber"]').value,
			"fechaPropuesta": _this.querySelector('input[name="fechaPropuesta"]').value
	};

	_cn = consultasPHP_nueva('./app_plan/app_plan_cambiaritemestado.php');  
	$.ajax({
			url:   './app_plan/app_plan_cambiaritemestado.php',
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
				if(_res.res!='exito'){return;} 
					
				cerrar(_this.querySelector('#botoncierra'));
				actualizarItem(_res);
				cargaBase('actualizarprogreso');
			}
	});
}

function anadirItem(_ev,_idit){//ajustado a geogec
	_ev.stopPropagation();
	_parametros = {
	"idMarco":_IdMarco,
	'codMarco': _CodMarco,
	'idit': _idit
	};

	_cn = consultasPHP_nueva('./app_plan/app_plan_crearitem.ph');  
	$.ajax({
		url:   './app_plan/app_plan_crearitem.php',
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
			if(_res.res!='exito'){return;} 

			//cargaBase();
			cargaNuevoItem(_res);

		}
	});	
}
/////////////////////	
