/**
*
* funciones js para ejecutar consultas
 * 
 *  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2017 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (GPL-3.0)
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
* 
*
*/



	
function consultarPermisos(){
	
	
	_parametros = {
		'codMarco':_CodMarco,	
		'accion':'app_rele'
	}
	
    _cn = consultasPHP_nueva('./sistema/sis_consulta_permisos.php');
	$.ajax({
        url:   './sistema/sis_consulta_permisos.php',
        type:  'post',
        data: _parametros,
        beforeSend: function(request, settings) { 
			
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
            
            if(_res.res!='exito'){
            	alert('error al consultar la base de datos');
            }
        }
 	});	
}




function cargarListadoCampa(){
    
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    
    _source_rele.clear();
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco
    };
    
    _cn = consultasPHP_nueva('./app_rele/app_rele_consultar_listado.php');
    $.ajax({
            url:   './app_rele/app_rele_consultar_listado.php',
            type:  'post',
            data: parametros,
        beforeSend: function(request, settings) { 
			
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
            
			if(_res.res=='exito'){
				cargarListadoCampas(_res);
				if(_idCampa > 0){
					cargarDatosCampa(_idCampa);
				}
			}
        }
    });
}

function generarNuevaCampa(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
    	'idRele':'0',
        'codMarco': codMarco,
        'idMarco': idMarco
    };
    
    _cn = consultasPHP_nueva('./app_rele/app_rele_ed_guardar_relevamiento.php');
    $.ajax({
        url:   './app_rele/app_rele_ed_guardar_relevamiento.php',
        type:  'post',
        data: parametros,
        beforeSend: function(request, settings) { 
			
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
            
            if(_res.res=='exito'){
            	cargarDatosCampa(_res.data.nid)
            }
        }
    });
}

function guardarCampa(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');

	if(document.querySelector('.formCargaCampaCuerpo #campaZz_carga_publica').checked==true){
		_cp=1;
	}else{
		_cp=0;
	}
	
	
    var parametros = {
    	'idRele':_DataRele.id,
    	'descripcion':document.querySelector('.formCargaCampaCuerpo #campaDescripcion').value,
    	'nombre':document.querySelector('.formCargaCampaCuerpo #campaNombre').value,
    	'zz_carga_publica':_cp,
        'codMarco': codMarco,
        'idMarco': idMarco,
        'tipogeometria':document.querySelector('.formCargaCampaCuerpo [name="tipogeometria"]').value,
        'unidadanalisis':document.querySelector('.formCargaCampaCuerpo [name="unidadanalisis"]').value
    };
    
    _cn = consultasPHP_nueva('./app_rele/app_rele_ed_guardar_relevamiento.php');
    $.ajax({
        url:   './app_rele/app_rele_ed_guardar_relevamiento.php',
        type:  'post',
        data: parametros,
        beforeSend: function(request, settings) { 
			
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
            //console.log(_res);
            
            if(_res.res=='exito'){
            	cargarDatosCampa(_res.data.id)
            }
        }
    });
}



function asignarIdCampa(_idCampa){
    document.getElementById('formEditarCampa').setAttribute('idcampa', _idCampa);
}

function eliminarCampa(){
	if(!confirm('elminamos esta cmpaña de relevamiento?...¿Segure?')){return;}
	_idcamp = document.querySelector('#divCargaCampa').getAttribute('idcampa');
	var parametros = {
    	'idRele':_DataRele.id,
        'codMarco': getParameterByName('cod'),
        'idMarco': getParameterByName('id')
    };
    
    _cn = consultasPHP_nueva( './app_rele/app_rele_ed_borrar_relevamiento.php');
    $.ajax({
        url:   './app_rele/app_rele_ed_borrar_relevamiento.php',
        type:  'post',
        data: parametros,
        beforeSend: function(request, settings) { 
			
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
            document.querySelector('#contenido').setAttribute('relecargado','no');
            document.querySelector('#divReleACapa').setAttribute('idcampa', '');            
            
            inicializarColumnas();
            cargarListadoCampa();
            
        }
    });
}

function eliminarCampo(_this){
	
	
	var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
    	'idRele':_DataRele.id,
        'codMarco': codMarco,
        'idMarco': idMarco,
        'idcampo':_this.parentNode.parentNode.getAttribute('idcampo')
    };
    
    _cn = consultasPHP_nueva('./app_rele/app_rele_ed_borrar_campo.php');
    $.ajax({
        url:   './app_rele/app_rele_ed_borrar_campo.php',
        type:  'post',
        data: parametros,
        beforeSend: function(request, settings) { 
			
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
			_form=document.querySelector('#listadecampos #editacampo[idcampo="'+_res.data.idcampo+'"]'),
			_form.parentNode.removeChild(_form);
            
        }
    });
	
	
}

function cargarDatosCampa(_idcampa){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    _idCampa = _idcampa;
    actualizarUrl();
  
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'idcampa': _idcampa
    };
    
    _cn = consultasPHP_nueva('./app_rele/app_rele_consultar.php');
    $.ajax({
        url:   './app_rele/app_rele_consultar.php',
        type:  'post',
        data: parametros,
        beforeSend: function(request, settings) { 
			
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
            
            _DataRele=_res.data;
            resetLabelStyle();

            cargarCamposFormulario();
            cargarRegistrosCampa(_res.data.id);
            cargarUnidadesdeAnalisis(_res.data.id);   

			document.querySelector('#divCargaCampa').setAttribute('idcampa',_res.data.id);

			document.querySelector('#cuadrovalores #contenido #titulorarlev').innerHTML=_DataRele.id+'<span>'+_DataRele.nombre+'<span>';
            document.querySelector('#cuadrovalores #contenido #titulorarlev').title=_DataRele.descripcion;
            document.querySelector('#cuadrovalores #contenido').setAttribute('relecargado','si');
            
            
            
            if(_DataRele.id_p_ref_capasgeo==null){
				editarCampa();
			}else{
				document.querySelector('#cuadrovalores #contenido').setAttribute('releeditando','no');
				document.querySelector('#cuadrovalores #contenido').setAttribute('geometriaeditando','no');
				//document.querySelector('#cuadrovalores #contenido').setAttribute('camposeditando','no');
				document.querySelector('#cuadrovalores #contenido').setAttribute('FormularioRegistro','no');					
				
				if(_DataRele.tipogeometria!=null){
					document.querySelector('#cuadrovalores #contenido').setAttribute('nuevaUAhabilitado','si');					
				}else{
					document.querySelector('#cuadrovalores #contenido').setAttribute('nuevaUAhabilitado','no');					
				}
			}
          
        }
    });
}



function consultarCampaDefinicion(idcampa){
    //consultar si ya existe un indicador sin publicar para este autor y sino crearlo
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idcampa
    };
    
    _cn = consultasPHP_nueva( './app_rele/app_rele_consultar.php');
    $.ajax({
        url:   './app_rele/app_rele_consultar.php',
        type:  'post',
        data: parametros,
        beforeSend: function(request, settings) { 
			
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
            
            if (_res.data != null){
            	_DataRele=_res.data;
            	resetLabelStyle();// en app_rele_mapa.js
                //TODO
                cargarUnidadesdeAnalisis(_res.data.id);   
            }   
        }
    });
}


function cargarRegistrosCampa(_idcampa,_idgeom){
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco,
            'idcampa': _idcampa,
            'idgeom': _idgeom,
            'zz_superado':'0'
    };
    _url='./app_rele/app_rele_consultar_registros.php';
    _cn = consultasPHP_nueva(_url);
    $.ajax({
        url:   _url,
        type:  'post',
        data: _parametros,
        beforeSend: function(request, settings) { 
			request._data = {'cn':_cn};
		},
        error:  function (request, status, errorThrown){	
			_cn = request._data.cn;
			consultasPHP_respuesta("err",_cn);		
		},
		success:  function (response, status, request){
			
			var _res = $.parseJSON(response);            
            _cn = request._data.cn;			
            //console.log(_res);
            consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
			
			//document.querySelector('#divCargaCampa').style.display='block';
			//document.querySelector('#divCargaCampa .accionesCampa').style.display='none';
			if(_res.res != 'exito'){return;}
			//console.log(_res.data.idreg);
			
			if(_res.data.idreg==undefined){
				_Features=_res.data.registros;
				_FeaturesOrden=_res.data.registrosOrden;
				mostrarListadoUA_total();
            	cargarFeatures();
			}else{			
				_Features[_res.data.idreg]=_res.data.registros[_res.data.idreg];
				_FeaturesOrden.push(_res.data.idreg);
				mostrarListadoUA_reg(_res.data.idreg);
				console.log('consulta uno solo');
            	cargarFeatures(_res.data.idreg);
			}
		
        }
    });
}



function archivarRegistro(_val){
	
	if(_val='1'){
		if(document.querySelector('#campospersonalizados .campo .fecha[esfechaarchivo="si"]')==null){
			alert('Falta configurar un campo de fecha para que sea la fecha de referencia del dato.');
			return;
		}
		if(document.querySelector('#campospersonalizados .campo .fecha[esfechaarchivo="si"]').value<'1000-01-01'){
			alert('Falta cargar la fecha de referencia para el dato.');
			return;
		}
		_val_fecha=document.querySelector('#campospersonalizados .campo .fecha[esfechaarchivo="si"]').value;
	}else{
		_val_fecha='';
	}
		
    _parametros = {
		'codMarco':_CodMarco,
		'id_registro': _idgeom=document.querySelector('#FormularioRegistro [name="id_registro"]').value,
		'id_p_ref_capas_registros':document.querySelector('#FormularioRegistro [name="idgeom"]').value,
		'fecha_archivo':_val_fecha,
		'zz_archivada':_val
    };
    _cn = consultasPHP_nueva('./app_rele/app_rele_archivar_registro.php');
    $.ajax({
        url:   './app_rele/app_rele_archivar_registro.php',
        type:  'post',
        data: _parametros,
        beforeSend: function(request, settings) { 
			
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
			
			consultarRegistroGeom(_res.data.id_p_ref_capas_registros);
			
        }
    });
}
	
	
	
function consultarRegistroGeom(_idregcapa){
	
	document.querySelector('#cuadrovalores #contenido').setAttribute('FormularioRegistro','no'); 
	
    _parametros = {
            'codMarco':_CodMarco,
            'idcampa': _DataRele.id,
            'idregistrocapa':_idregcapa
    };
    _url ='./app_rele/app_rele_consultar_registros_campos.php';
    _cn = consultasPHP_nueva(_url);
    $.ajax({
        url:   _url,
        type:  'post',
        data: _parametros,
        beforeSend: function(request, settings) { 
			request._data = {'cn':_cn};
		},
        error:  function (request, status, errorThrown){	
			_cn = request._data.cn;
			consultasPHP_respuesta("err",_cn);		
		},
		success:  function (response, status, request){
			_res = $.parseJSON(response);            
            _cn = request._data.cn;			
            consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
			if(_res.res!='exito'){return;}
			
			document.querySelector("#FormularioRegistro").setAttribute('modo','actual');
			
			document.querySelector('#selectorarchivo [id_reg_hist="actual"]').setAttribute('selecto','si');
			
			_DataRegistro=_res.data;
			document.querySelector('#cuadrovalores #contenido').setAttribute('FormularioRegistro','si');
			
			if(_res.data==null){
				cargarCamposFormulario();//limpia el formulario
				return;	
			}else{
				cargarCamposFormulario();//limpia el formulario
			}
			document.querySelector("#selectorarchivo").setAttribute('modo','inactivo');
			document.querySelector('#FormularioNuevaUA #historicos').innerHTML='';		
			for(_idh in _res.data.historicosOrden){
				document.querySelector("#selectorarchivo").setAttribute('modo','activo');
				_id_reg_h=_res.data.historicosOrden[_idh];
				_dat_h=_res.data.historicos[_id_reg_h];
				
				_a=document.createElement('a');
				document.querySelector('#FormularioNuevaUA #historicos').appendChild(_a);
				_a.setAttribute('class','historico');
				_a.setAttribute('id_reg_hist',_id_reg_h);
				_a.setAttribute('onclick','cargarRegistroHistorico(this.getAttribute("id_reg_hist"))');
				_f=_dat_h.registro.zz_archivada_fecha.split('-');
				_a.innerHTML='<span class="dia">'+_f[2]+'</span><span class="mes">'+_f[1]+'</span><span class="ano">'+_f[0];
				
				_elim=document.createElement('a');
				_a.appendChild(_elim);
				_elim.setAttribute('id','boton_borra_reg');
				_elim.innerHTML='<img src="./comun_img/icon-delete-16.jpg"">';
				_elim.setAttribute('onclick','event.stopPropagation();eliminarArchivado(this.parentNode.getAttribute("id_reg_hist"))');

			}
			//console.log(_res.data.registro.id);
			cargarDefinicionRegistro(_res.data.registro);
			
			cargarValoresRegistro(_res.data.campos);
				
				
			
        }
    });	
}



function eliminarArchivado(_id_reg_hist){
	
	document.querySelector('#cuadrovalores #contenido').setAttribute('FormularioRegistro','no'); 
	
    _parametros = {
		'codMarco':_CodMarco,
		'idcampa': _DataRele.id,
		'id_registro_rele':_id_reg_hist
    };
    _cn = consultasPHP_nueva('./app_rele/app_rele_borrar_registro_historico.php');
    $.ajax({
        url:   './app_rele/app_rele_borrar_registro_historico.php',
        type:  'post',
        data: _parametros,
        beforeSend: function(request, settings) { 
			
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
			
			_div=document.querySelector('#selectorarchivo .historico[id_reg_hist="'+_res.data.id_registro_rele+'"]');
			_div.parentNode.removeChild(_div);
			
			cargarRegistroHistorico('actual');
		}
	});	    

}



function cargarUnidadesdeAnalisis(_id_campa){
	
	_idCampa=_id_campa;
	
	
    var parametros = {
        'codMarco': _CodMarco,
        'idMarco': '',
        'idcampa': _idCampa,        
		'RecorteDeTrabajo' : _RecorteDeTrabajo
    };
    
    _url='./app_rele/app_rele_consultar_geom.php';
    _cn = consultasPHP_nueva(_url);
    $.ajax({
            url:   _url,
            type:  'post',
            data: parametros,
			beforeSend: function(request, settings) { 
				
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

                _DataCapa=_res.data.capa;
                _DataGeom=_res.data.geom;
                _DataGeomOrden=_res.data.geomOrden;

	            
	            for(var _na in _res.acc){
	                procesarAcc(_res.acc[_na]);
	            }
	            
				dibujarReleMapa();			
				
				cargarFormularioNuevasGeometrias(_res);
				
            }
    });
}



           
function borrarGeometrias(_modo,_idgeom){
	
	if(_modo=='todos'){
		if(!confirm('esto va a borrar TODAS las geometrías de este relevamiento, cargada por cualquier usuarie.')){
			return;
		}
	}else if(_modo=='propios'){
		if(!confirm('esto va a borrar las geometrías que vos cargagaste en este relevamiento.')){
			return;
		}
	}else if(_modo=='registro'){
		if(_idgeom==null){alert('error');return;}
		_source_rel_sel.clear();
		if(!confirm('esto va a borrar la geometría seleccionada. (iddb:'+_idgeom+')')){
			return;
		}
	}else{
		alert('error');
		return;
	}
	
	 var parametros = {
        'codMarco': _CodMarco,
        'idMarco': '',
        'modo': _modo,
        'idcampa': _DataRele.id
    };
    
    if(_modo=='registro'){
		 parametros['idgeom'] = _idgeom
	}

	_url='./app_rele/app_rele_borrar_geom.php';
    _cn = consultasPHP_nueva(_url);
    $.ajax({
        url:   _url,
        type:  'post',
        data: parametros,
		beforeSend: function(request, settings) { 
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
			
					 
			for(var _na in _res.acc){procesarAcc(_res.acc[_na]);}
	   
			_a=document.querySelector('#listadoUA_total #listadito [idgeom="'+_res.data.idgeom+'"]');
			_a.parentNode.removeChild(_a);
			
			cargarUnidadesdeAnalisis(_DataRele.id);                
		
		}
	});
}

function enviarDatosRegistro(_archivar){
	
	if(document.querySelector('#FormularioRegistro').getAttribute('modo')=='archivado'){
		//alert('este registro ya fue archivado. Para modificarlo hay que desarchivarlo');
		//return;
	}
	_idgeom=document.querySelector('#FormularioRegistro [name="idgeom"]').value;	
	_t1=document.querySelector('#FormularioRegistro [name="t1"]').value; //nombre UA
	_n1=document.querySelector('#FormularioRegistro [name="n1"]').value; //estado de carga completo
	
	_id_registro=document.querySelector('#FormularioRegistro [name="id_registro"]').value;
	
	_personalizados={};
	
	_inputs=document.querySelectorAll('#FormularioRegistro #campospersonalizados input');
	for(_in in _inputs){
		if(typeof _inputs[_in] != 'object'){continue;}
		_name=_inputs[_in].getAttribute('name');
		if(_name==null){continue;}
		_personalizados[_name]=_inputs[_in].value;
	}
	
	_ta=document.querySelectorAll('#FormularioRegistro #campospersonalizados textarea');
	for(_tan in _ta){
		if(typeof _ta[_tan] != 'object'){continue;}
		_name=_ta[_tan].getAttribute('name');
		if(_name==null){continue;}
		_personalizados[_name]=_ta[_tan].value;
	}

	_ta=document.querySelectorAll('#FormularioRegistro #campospersonalizados select');
	for(_tan in _ta){
		//console.log(_ta[_tan]);
		if(typeof _ta[_tan] != 'object'){continue;}
		//console.log(_ta[_tan].value);
		_name=_ta[_tan].getAttribute('name');
		if(_name==null){continue;}
		_personalizados[_name]=_ta[_tan].value;
	}
	
	//_Features[_id_registro]['geotx']='';
	
	_DataGeom[_idgeom]['t1']=_t1;
	_DataGeom[_idgeom]['estado']=_n1;
	
	
	document.querySelector('#FormularioRegistro .switch input').checked=false;
	document.querySelector('#FormularioRegistro .switch input').onchange();
	
	
		//cada vez que se gurada un registro de la tabla ref_rele_registros, reemplaza un registro anterior sin sobreescribirlo. nueva id
	var parametros = {
        'codMarco': _CodMarco,
        'idMarco': '',
        'idcampa': _DataRele.id,
        'idgeom':_idgeom,
        'id_registro':_id_registro,
        't1':_t1,
        'n1':_n1,
        'personalizados':_personalizados,
		'archivar':_archivar
    };

    _url='./app_rele/app_rele_cargar_registro.php';
    _cn = consultasPHP_nueva(_url);
    $.ajax({
        url:   _url,
        type:  'post',
        data: parametros,
		beforeSend: function(request, settings) { 
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
			
            for(var _na in _res.acc){
                procesarAcc(_res.acc[_na]);
            }					
			if(_res.res!='exito'){return;}
			

			document.querySelector('#FormularioRegistro [name="id_registro"]').value=_res.data.nid;				
			

						
			if(_res.data.registro_nuevo	=='si'){				
				_Features[_res.data.nid]={
					'id_p_ref_capas_registros':_res.data.idgeom,
					'col_texto1_dato':'',
					'id':_res.data.nid		
				};
			}else{
				_Features[_res.data.nid]=_Features[_res.data.id_registro];	
			}
			
			_Features[_res.data.nid]['col_texto1_dato']='nuevo';
			cargarFeatures(_res.data.nid);
			
            if(_res.data.archivar=='si'){
				archivarRegistro(1);	
			}
			
			
            //TODO remplazar la carga total por la incorporación del dato editado.    
			//cargarRegistrosCampa(_DataRele.id,_res.data.idgeom);
			
            //cargarDatosCampa(_DataRele.id);
            //document.querySelector('#FormularioRegistro').style.display='none';
			_source_rel_sel.clear();
        }
    });

	_idnuevageom=document.querySelector('#FormularioRegistro #nuevageometria').getAttribute('idgeom');


	

	if(_idnuevageom==_idgeom){
		
		_DataGeom[_idgeom]['geotx']=document.querySelector('#FormularioRegistro #nuevageometria').value;
			
		
		var parametros = {
	        'codMarco': _CodMarco,
	        'idMarco': '',
	        'idcapa': _DataCapa.id,
	        'idgeom':_idgeom,
	        'tipogeom':_DataCapa.tipogeometria,
	        'geomtx': document.querySelector('#FormularioRegistro #nuevageometria').value
	    };
	
		_url='./app_capa/app_capa_editar_registro.php';
		_cn = consultasPHP_nueva(_url);
		$.ajax({
	        url:   _url,
	        type:  'post',
	        data: parametros,
			beforeSend: function(request, settings) { 
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
				
				_feat=_source_rele.getFeatureById(_res.data.idgeom);
				
				for(var _na in _res.acc){
					procesarAcc(_res.acc[_na]);
				}					
				if(_res.res!='exito'){return;}
	            
	        }
		});
	}	
}


function enviarCreaRegistroCapa(){

	document.querySelector('#FormularioRegistro [name="id_registro"]').value='';

	var parametros = {
	        'codMarco': _CodMarco,
	        'idMarco': '',
	        'idcapa': _DataCapa.id,
	        'tipogeom':_DataCapa.tipogeometria //TODO corregir este hardcoded
    };
    _url='./app_capa/app_capa_crear_registro.php';
    _cn = consultasPHP_nueva(_url);
     $.ajax({
        url:   _url,
        type:  'post',
        data: parametros,
		beforeSend: function(request, settings) { 
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
            for(var _na in _res.acc){
                procesarAcc(_res.acc[_na]);
            }					
			if(_res.res!='exito'){return;}
            
            _DataGeom[_res.data.idgeom]={
            		'id': _res.data.idgeom, 
            		'geotx': "", 
            		'estadocarga': "sin dibujar",
            		't1':'',
            		'n1':''
            };
            
            
			
            
			gestionarForms('FormularioRegistro','si');
            //console.log( document.querySelector('#cuadrovalores #contenido'));
            
            accionGeomSeleccionada(_res.data.idgeom);
            
            cambiarGeometria(_res.data.idgeom);            
        }
    });
}
     
function accionEditarValorGuardar(_this){
	
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    
    var idindval = document.getElementById('divPeriodoSeleccionado').getAttribute('idindval');
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    var fechaAhora = new Date();
    
    
    var _paramGral = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idindval,
        'idrele':_IdRele        
    };
    
    
    _registros=document.querySelectorAll('#listaVaraiblesRele input[cambiado="si"]');
    _envios=Array();
    for(_rn in _registros){
    	if(typeof _registros[_rn] != 'object'){continue;}
    	
    	_uni=_registros[_rn].parentNode.parentNode;
    	_idg=_uni.getAttribute('idgeom');
    	
    	_imps=_uni.querySelectorAll('input');
    	
    	_envios[_idg]=Array();
    	_envios[_idg]['id_p_ref_campa_registros']=_idg;
    	for(_in in _imps){
    		if(typeof _imps[_in] != 'object'){continue;}
    		
    		_campo = _imps[_in].getAttribute('id');
    		_campo = _campo.replace('indCarga','col_');
    		_campo = _campo.replace('Texto','texto');
    		_campo = _campo.replace('Numero','numero');
    		_campo = _campo.replace('Dato','_dato');
    		
    		_envios[_idg][_campo]=_imps[_in].value;
    	}
    }
    
    
    for(_idg in _envios){    
    	
    	parametros = _paramGral;
    	for(_campo in  _envios[_idg]){
    		parametros[_campo]=_envios[_idg][_campo];    	
    	}

    _cn = consultasPHP_nueva('./app_rele/app_rele_val_editar.php');
	    $.ajax({
            url:   './app_rele/app_rele_val_editar.php',
            type:  'post',
            data: parametros,
			beforeSend: function(request, settings) { 
				
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
				
				for(var _na in _res.acc){
					procesarAcc(_res.acc[_na]);
				}					
				if(_res.res!='exito'){return;}
			
                	
            	accionPeriodoElegido(_DataPeriodo.ano, _DataPeriodo.mes, 'false');
                
            }
	    });	    
    }
}


function guardarDXFfondo(_imgs){
	
	alert("Generaremos un dxf con un raster georreferenciado con el mapa visible en este momento.\n Al estar georeferenciado, se pueden dibujar sobre este polígonos como UA para cargarlos directamente.");
	
	var parametros = _imgs
    parametros['idcampa']=_DataRele.id;
    _cn = consultasPHP_nueva( './app_rele/app_rele_generar_dxf_captura.php');
     $.ajax({
        url:   './app_rele/app_rele_generar_dxf_captura.php',
        type:  'post',
        data: parametros,
		beforeSend: function(request, settings) { 
			
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
			
            for(var _na in _res.acc){
                procesarAcc(_res.acc[_na]);
            }					
			if(_res.res!='exito'){return;}
            
            _if=document.createElement('iframe');
			_if.style.display='none';
			_if.src=_res.data.descarga;
			document.querySelector('body').appendChild(_if);
            
        }
    });
}


function ReleACapa(){
	
	_sels=document.querySelectorAll('#ReleACapa #campos select');
	
	_fuentes=Array();
	
	
	for(_sn in _sels){
		if(typeof _sels[_sn] !='object'){continue;}
		if(_sels[_sn].value==''){continue}
		_fue=_sels[_sn].value;
		_fuentec=_sels[_sn].getAttribute('name');
		_s=_fuentec.split('_');
		_nombrec='nombrec_'+_s[1];
		_nom=document.querySelector('#ReleACapa #campos input[name="'+_nombrec+'"]').value;
		
		_fuentes.push({'nom':_nom,'fue':_fue});
	}
	if(_fuentes.length==0){alert("Primero tenés que definir con cuales datos relevados querés generar la capa.");}
	
	_parametros={
		'idcampa':_DataRele.id,
		'codMarco':_CodMarco,
		'fuentes':_fuentes
	}
    _cn = consultasPHP_nueva('./app_rele/app_rele_generar_capa.php');
 	$.ajax({
        url:   './app_rele/app_rele_generar_capa.php',
        type:  'post',
        data: _parametros,
		beforeSend: function(request, settings) { 
			
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
			
            for(var _na in _res.acc){
                procesarAcc(_res.acc[_na]);
            }					
			if(_res.res!='exito'){return;}
			
            if(confirm('Nueva capa disponible en el módulo de capas. ¿Vamos ahí?')){
					window.location.assign('./app_capa.php?cod='+_CodMarco+'&ird='+_res.data.nidcapa);
			}
            
        }
    });	
}



function guardarCampo(_this){
	
	_form=_this.parentNode.parentNode;
	
	if(_form.getAttribute('id')=='nuevocampo'){_form.setAttribute('estado','inactivo');}
	
	if(_form.querySelector('[name="es_fecha_archivo"]').checked){
		_es_fecha='si';
	}else{
		_es_fecha='no';
	}
	
	_parametros={
		'idRele':_DataRele.id,
		'codMarco':_CodMarco,		
		'idCampo':_form.querySelector('[name="idcampo"]').value,
		'nombre':_form.querySelector('[name="nombre"]').value,
		'ayuda':_form.querySelector('[name="ayuda"]').value,
		'tipo':_form.querySelector('[name="tipo"]').value,
		'opciones_select':_form.querySelector('[name="opciones_select"]').value,
		'unidademedida':_form.querySelector('[name="unidademedida"]').value,
		'es_fecha_archivo':_es_fecha,
		'matriz':_form.querySelector('[name="matriz"]').value,
		'nombre_matriz':_form.querySelector('[name="nombre_matriz"]').value,
		'nombre_columna':_form.querySelector('[name="nombre_columna"]').value,
		'nombre_fila':_form.querySelector('[name="nombre_fila"]').value
		
	}
    _cn = consultasPHP_nueva('./app_rele/app_rele_ed_guardar_campo.php');
 	$.ajax({
        url:   './app_rele/app_rele_ed_guardar_campo.php',
        type:  'post',
        data: _parametros,
		beforeSend: function(request, settings) { 
			
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
			
            for(var _na in _res.acc){
                procesarAcc(_res.acc[_na]);
            }					
			if(_res.res!='exito'){return;}
            
            cargarDatosCampa(_DataRele.id);            
            
        }
    });	
	
	
}


function procesarPlanillaXLSX(){
	
	_parametros={
		'idcampa':_DataRele.id,
		'codMarco':_CodMarco,		
		'filename':document.querySelector('#FormularioSubirPlanilla input[name="filename"]').value,
		'nombreua':document.querySelector('#FormularioSubirPlanilla select[name="nombreua"]').value,
		'campos_rellenos':{},		
		'modo_importacion':document.querySelector('#FormularioSubirPlanilla select[name="modo_importacion"]').value,
		'capa_fuente':document.querySelector('#FormularioSubirPlanilla select[name="capa_fuente"]').value,
		'campo_link_en_capa':document.querySelector('#FormularioSubirPlanilla select[name="campo_link_en_capa"]').value,
		'columna_link_en_planillla':document.querySelector('#FormularioSubirPlanilla select[name="columna_link_en_planillla"]').value,
		'check_crear_campos':document.querySelector('#FormularioSubirPlanilla input[name="check_crear_campos"]').value,		
		'campos_creados':{},
		'columna_wkt_en_planillla':document.querySelector('#FormularioSubirPlanilla #elegircamposgeom select[name="columna_wkt_en_planillla"]').value,
		'srid_wkt':document.querySelector('#FormularioSubirPlanilla #elegircamposgeom select[name="srid_wkt"]').value
				
	}

	
	_sels=document.querySelectorAll('#FormularioSubirPlanilla #asignacampos select');		
	for(_sn in _sels){
		if(typeof(_sels[_sn])!='object'){continue;}
		_name=_sels[_sn].getAttribute('name');
		_val=_sels[_sn].value;
		_parametros['campos_rellenos'][_sels[_sn].getAttribute('name')]=_val;
	}
	
	
	_filas=document.querySelectorAll('#FormularioSubirPlanilla #crear_campos .mini_form_campo');		
	for(_fn in _filas){
		if(typeof(_filas[_fn])!='object'){continue;}
		if(_filas[_fn].querySelector('[name="crear"]').checked==false){continue;}
		
		_col=_filas[_fn].getAttribute('num_col_crea');
		_arr={
			nombre:_filas[_fn].querySelector('[name="nombre"]').value,
			col:_col,
			tipo:_filas[_fn].querySelector('[name="tipo"]').value
		}
		_parametros['campos_creados'][_col]=_arr;
	}
		
	
    _cn = consultasPHP_nueva('./app_rele/app_rele_procesa_xlsx.php');
	$.ajax({
        url:   './app_rele/app_rele_procesa_xlsx.php',
        type:  'post',
        data: _parametros,
		beforeSend: function(request, settings) { 
			
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
			
            for(var _na in _res.acc){
                procesarAcc(_res.acc[_na]);
            }					
			if(_res.res!='exito'){return;}
			
		
			alert('planilla procesada exitosamente');
			
			document.querySelector('#FormularioSubirPlanilla').setAttribute('archivocargado','no');
			document.querySelector('#FormularioSubirPlanilla').setAttribute('estado','inactivo');
			
			
			document.querySelector('#FormularioSubirPlanilla [name="nombreua"]').innerHTML='';
			document.querySelector('#FormularioSubirPlanilla #asignacampos').innerHTML='';
			document.querySelector('#FormularioSubirPlanilla input[name="filename"]').value='';
		}	
	});
	
}

function borrarDatosGeometrias(){
	
	_parametros={
		'idcampa':_DataRele.id,
		'codMarco':_CodMarco
	}
	
	
    _cn = consultasPHP_nueva('./app_rele/app_rele_ed_borrar_registros.php');
	$.ajax({
        url:   './app_rele/app_rele_ed_borrar_registros.php',
        type:  'post',
        data: _parametros,
		beforeSend: function(request, settings) { 			
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
            for(var _na in _res.acc){
                procesarAcc(_res.acc[_na]);
            }					
			if(_res.res!='exito'){return;}
	
		
			cargarDatosCampa(_res.data.idcampa);
		}
	});
}



function duplicarCampa(){
	
	_parametros={
		'idcampa':_DataRele.id,
		'codMarco':_CodMarco
	}	
	
    _cn = consultasPHP_nueva('./app_rele/app_rele_ed_duplicar_relevamiento.php');
	$.ajax({
        url:   './app_rele/app_rele_ed_duplicar_relevamiento.php',
        type:  'post',
        data: _parametros,
		beforeSend: function(request, settings) { 			
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
            for(var _na in _res.acc){
                procesarAcc(_res.acc[_na]);
            }					
			if(_res.res!='exito'){return;}
		
			_res = preprocesarRespuestaAjax(_data, _textStatus, _jqXHR);
			if(_res===false){return;}
			
			cargarDatosCampa(_res.data.nidrel);
		}
	});
}
/* MODELO A RETOMAR done en lugar de succes
$.ajax({
        url:   './app_rele/app_rele_ed_duplicar_relevamiento.php',
        type:  'post',
        data: _parametros
	})
	.done(function (_data, _textStatus, _jqXHR){
		
		_res = preprocesarRespuestaAjax(_data, _textStatus, _jqXHR);
		if(_res===false){return;}
		
		cargarDatosCampa(_res.data.nidrel);
		
	})*/
