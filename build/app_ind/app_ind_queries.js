/**
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
*/

function consultarPermisos(){
	
	
    var _IdMarco = getParameterByName('id');
    var _CodMarco = getParameterByName('cod');
    
    
    _parametros = {
            'codMarco':_CodMarco	
    };
    
    _cn = consultasPHP_nueva('./app_capa/app_capa_consultar_permisos.php');
    $.ajax({
        url:   './app_capa/app_capa_consultar_permisos.php',
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
                
            for(_na in _res.acc){
	          	procesarAcc(_res.acc[_na]);
            }
        }
    });	
}


function cargarListadoCapasPublicadas(){
	
	limpiarContenidosMapa();
	
	document.querySelector('#cuadrovalores').setAttribute('estado','inicial');
	
	
	document.querySelector('#cuadrovalores').setAttribute('formseleccionind','si');
	document.querySelector('#cuadrovalores').setAttribute('formseleccionmod','no');
	document.querySelector('#cuadrovalores').setAttribute('formeditarindicadores','no');
	document.querySelector('#cuadrovalores').setAttribute('formdivlistaindicadorescarga','no');
	document.querySelector('#cuadrovalores').setAttribute('formCargaInd','no');	
    document.querySelector("#cuadrovalores").setAttribute("divListaIndicadoresCarga","no");
		
    var _this = _this;
    
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };

    _cn = consultasPHP_nueva('./app_ind/app_ind_capa_consultar_listado.php');    
    $.ajax({
		url:   './app_ind/app_ind_capa_consultar_listado.php',
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

			_DataCapas=_res.data;
			//console.log(_DataCapas);
			
			cargarValoresCapasPublicadas(_res);
			mostrarListadoCapasPublicadas();
		}
    });
}


function cargarDatosCapaPublicada(idcapa){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': '1',
        'idcapa': idcapa
    };
    
    _cn = consultasPHP_nueva('./app_ind/app_ind_capa_consultar.php');  
    $.ajax({
		url:   './app_ind/app_ind_capa_consultar.php',
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
		
			cargarValoresCapaExist(_res);	
		}
    });
}

function cargarValoresCapaExistQuery(){
    var idCapa = document.getElementById('capaseleccionada').getAttribute('idcapa'); 
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco,
            'idcapa': idCapa
    };
    
    _cn = consultasPHP_nueva('./app_capa/app_capa_consultar_registros.php');  
    $.ajax({
        url:   './app_capa/app_capa_consultar_registros.php',
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
			
            _Features=_res.data;
            cargarFeatures();
        }
    });
}


function  editarIndPublicado(_this){
    //consultar si ya existe un indicador sin publicar para este autor y sino crearlo
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '0';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };

	_cn = consultasPHP_nueva('./app_ind/app_ind_consultar.php');
    $.ajax({
            url:   './app_ind/app_ind_consultar.php',
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
					_DataIndicador=_res.data;
					cargarValoresIndicadorExist(_res);                        
				} else {
					generarNuevoIndicadorQuery();
                }
            }
    });
}


function generarNuevoIndicador(){
    //consultar si ya existe un indicador sin publicar para este autor y sino crearlo
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '0';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };
	_cn = consultasPHP_nueva('./app_ind/app_ind_consultar.php');
    $.ajax({
            url:   './app_ind/app_ind_consultar.php',
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
					_DataIndicador=_res.data;
					cargarValoresIndicadorExist(_res);
				} else {
					generarNuevoIndicadorQuery();
				}
            }
    });
}


function generarNuevoModelo(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var parametros = {};

	_cn = consultasPHP_nueva('./app_ind/app_ind_ed_crea_modelo.php');
    $.ajax({
		url:   './app_ind/app_ind_ed_crea_modelo.php',
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

			_Id_Modelo_Editando=_res.data.nid;
			cargarListadoModelo();				
		}
    });
}

function generarNuevoIndicadorQuery(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco
    };

	_cn = consultasPHP_nueva('./app_ind/app_ind_generar.php');    
    $.ajax({
		url:   './app_ind/app_ind_generar.php',
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

			_DataIndicador['id']=_res.data.id;
			asignarIdIndicador(_res.data.id);
		}
    });
}

function consultarIndicadorParaModificar(idindicador){
	 var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idindicador,
        'zz_publicada': zz_publicada
    };

	_cn = consultasPHP_nueva('./app_ind/app_ind_consultar.php');    
    $.ajax({
		url:   './app_ind/app_ind_consultar.php',
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

			if (_res.data != null){
				_DataIndicador=_res.data;
				
				//TODO
				cargarValoresIndicadorExist(_res);
			}
		}
    });
}

function accionCrearCapaAux(){
    _idMarco = getParameterByName('id');
    _codMarco = getParameterByName('cod');
    _copiar = 'no';
	
    _func=document.querySelector('#formEditarIndicadores #funcionalidadSelector').value;
    _tipog=document.querySelector('#formEditarIndicadores [name="tipogeometria"]').value;
    if(_tipog=='' && _func!='sinGeometria'){alert('Primero tenés que seleccionar un tipo de geometria');return;}
    if(_func=='sinGeometria'){_tipog='Polygon';_copiar='marco';}
    
    var parametros = {
        'codMarco': _codMarco,
        'idMarco': _idMarco,
        'idindicador': _DataIndicador.id,
        'tipogeometria': _tipog,
        'copiar':_copiar
    };

	_cn = consultasPHP_nueva('./app_capa/app_capa_generar_ind_aux.php');    
    $.ajax({
		url:   './app_capa/app_capa_generar_ind_aux.php',
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

			if (_res.data != null){
				//TODO
				alert('capa auxiliar creada y asociada a este indicador');
				accionIndicadorPublicadoSeleccionado('', _res.data.id_indicador);
				//cargarValoresIndicadorExist(_res);
			}
		}
    });
}

function editarInd(parametros){
	_cn = consultasPHP_nueva('./app_ind/app_ind_editar.php');    
    $.ajax({
		url:   './app_ind/app_ind_editar.php',
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
		}
    });
}

function eliminarCandidatoIndicador(_this){
    var idindicador = document.getElementById('formEditarIndicadores').getAttribute('idindicador');
    var _parametros = {
        'id': idindicador,
        'codMarco':_CodMarco
    };

    _cn = consultasPHP_nueva('./app_ind/app_ind_eliminar.php');    
    $.ajax({
		url:   './app_ind/app_ind_eliminar.php',
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

			//cargarMapa();
			accionCreaCancelar();
		}
    });
}

function publicarIndicador(){
	console.log('p');
    var idindicador = document.getElementById('formEditarIndicadores').getAttribute('idindicador');
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco,
            'id': idindicador
    };

	console.log('q');
	_cn = consultasPHP_nueva('./app_ind/app_ind_consultar_publicable.php');    
    $.ajax({
        url:   './app_ind/app_ind_consultar_publicable.php',
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

            publicarIndicadorQuery();
        }
    });
}

function publicarIndicadorQuery(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');

    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': document.getElementById('formEditarIndicadores').getAttribute('idindicador'),
        'tipogeometria': document.querySelector('#tipodeometriaNuevaGeometria #inputTipoGeom').value
    };

	_cn = consultasPHP_nueva('./app_ind/app_ind_publicar.php');    
    $.ajax({
		url:   './app_ind/app_ind_publicar.php',
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

			accionCreaCancelar(_this);
			alert("Indicador publicado");
		}
    });
}

function cargarListadoIndicadoresPublicados(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };

	_cn = consultasPHP_nueva('./app_ind/app_ind_consultar_listado.php');    
    $.ajax({
		url:   './app_ind/app_ind_consultar_listado.php',
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
		
			$('#cuadrovalores').attr('modo','indicadores');
			_DataListaIndicadores=_res.data;
			cargarValoresIndicadoresPublicados(null, "accionIndicadorPublicadoSeleccionado");
			mostrarListadoIndicadoresPublicados();
		}
    });
}


function cargarListadoModelo(){
	document.querySelector("#cuadrovalores").setAttribute("formeditarindicadores","no");
    document.querySelector("#cuadrovalores").setAttribute("formseleccionind","no");
    document.querySelector("#cuadrovalores").setAttribute("formseleccionmod","si");
    document.querySelector("#cuadrovalores").setAttribute("formdivlistaindicadorescarga","no");
	var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };
	
	_cn = consultasPHP_nueva('./app_ind/app_ind_consultar_listado_modelos.php');    
    $.ajax({
		url:   './app_ind/app_ind_consultar_listado_modelos.php',
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
			
			$('#cuadrovalores').attr('modo','modelos');
			_DataListaModelos=_res.data;
			mostrarListadoModelos();
			
			if(_Id_Modelo_Editando!=''){
				cargarModelo(_Id_Modelo_Editando);
				_Id_Modelo_Editando='';
			}
		}
    });
}

function cargarModelo(_idmod){
	_DataModelo=_DataListaModelos.modelos[_idmod];
	formularioAmpliado('modelo');	
}




	

/*
function cargarListadoIndicadoresPublicadosAModificar(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada,
        'nivelPermiso': '3'
    };
    
    $.ajax({
            url:   './app_ind/app_ind_consultar_listado.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                console.log(_res);
                if(_res.res=='exito'){
                    cargarValoresIndicadoresPublicados(_res, "accionIndicadorPublicadoSeleccionadoModificar");
                    mostrarListadoIndicadoresPublicados();
                }else{
                    alert('error asf0jg44ff0gh');
                }
            }
    });
}
*/
function refrescarIndicadorActivo(){	
    var idindicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    cargarInfoIndicador(idindicador, ano, mes, 'false');
}

function refrescarDatosIndicadorActivo(_res){
	if(ano!=_res.data.ano){return;}
	if(mes!=_res.data.mes){return;}
	if(idIndicador!=_res.data.idIndicador){return;}
	
	_inps = document.querySelectorAll('#listaUnidadesInd unidad[idgeom="'+_res.data.id_p_ref_capas_registros+'"] input');
	for(_ni in _inps){
		if(typeof _inps[_ni] != 'obejct'){continue;}
		_inps[_ni].removeAttribute('cambiado');
	}
}

function cargarIndicadorPublicado(idIndicador){
    cargarInfoIndicador(idIndicador,'true');
}

function cargarInfoIndicador(_idIndicador, _seleccionarDefault){
    var _idMarco = getParameterByName('id');
    var _codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': _codMarco,
        'idMarco': _idMarco,
        'zz_publicada': '1',
        'id': _idIndicador
    };

	_cn = consultasPHP_nueva('./app_ind/app_ind_consultar_estado.php');  
    $.ajax({
		url:   './app_ind/app_ind_consultar_estado.php',
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

			for(var _na in _res.acc){
				procesarAcc(_res.acc[_na]);
			}
			//console.log(_res);

			_DataIndicador=_res.data.indicador;
				
			if(_DataIndicador.periodicidad=='anual'){
				if(_DataIndicador.fechadesde!=null){
					_Select_Fecha['mes']=parseInt(_DataIndicador.fechadesde.split('-')[1]);
					_Select_Fecha['dia']=parseInt(_DataIndicador.fechadesde.split('-')[2]);
				}
			}else if(_DataIndicador.periodicidad=='mensual'){
				if(_DataIndicador.fechadesde!=null){
					_Select_Fecha['dia']=parseInt(_DataIndicador.fechadesde.split('-')[2]);
				}
			}			
					
			accionIndicadorPublicadoCargar(_idIndicador, _res, _seleccionarDefault);
		}
    });
}



function cargarPoligonosIndicadorPublicado(idIndicador, seleccionarDefault){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var _paramMes = 1;
    if (_Select_Fecha.mes != null && _Select_Fecha.mes > 0){
        _paramMes = _Select_Fecha['mes'];
    }
    
    _paramDia=1;
    if(_Select_Fecha['dia'] != null && _Select_Fecha['dia'] >0){
		_paramDia=_Select_Fecha['dia']
	}
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada,
        'id': idIndicador,
        'ano': _Select_Fecha['ano'],
        'mes':_paramMes,
        'dia':_paramDia
    };
    
	_cn = consultasPHP_nueva('./app_ind/app_ind_consultar_indicador_geom.php');  
    $.ajax({
            url:   './app_ind/app_ind_consultar_indicador_geom.php',
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
   
                _DataCapa=_res.data.capa;
	            
	            for(var _na in _res.acc){
	                procesarAcc(_res.acc[_na]);
	            }_res.data.periodo.me
	            
	            if(_DataIndicador.calc_buffer>0){
	            	consultarBuffer(_res.data.indicador.id,_res.data.periodo.ano,_res.data.periodo.mes);
	            }
					
				dibujarPoligonosMapa(_res);
				//console.log('o');
				if(_res.data.indicador.funcionalidad=='nuevaGeometria'){
					cargarFormularioNuevasGeometrias(_res);
				}else{
					cargarFormularioValoresMultiple(_res);
				}
				
				if(_res.data.indicador.funcionalidad=='geometriaExistente'){
					
					if(obtenerNombreMes(_res.data.periodo.mes-1)==undefined){_m='';
						}else{_m=obtenerNombreMes(_res.data.periodo.mes-1);
					}
					_p=_res.data.periodo.ano+'_'+parseInt(_res.data.periodo.mes)+'_'+parseInt(_res.data.periodo.dia);
					_selector='#periodo > #selectorPeriodo [periodo="'+_p+'"] #valor';
					//console.log(_selector);
					_divValor=document.querySelector(_selector); 
							
					_val=_res.data.resumen.sum_numero1;
					if(_val>100){
						_v=formatearNumero(_val,0);	
					}else{
						_v=formatearNumero(_val,2);	
					}
					_divValor.innerHTML='s: '+_v;
					
					_selector='#periodo > #selectorPeriodo [periodo="'+_p+'"] #porc';
					console.log(_selector);
					_divPorc=document.querySelector(_selector);
					console.log(_divPorc);
					
					_val=_res.data.resumen.prom_numero1;
					if(_val>100){
						_v=formatearNumero(_val,0);	
					}else{
						_v=formatearNumero(_val,2);	
					}
					_divPorc.innerHTML='m: '+_v;
					
				/*
				if (seleccionarDefault == 'true'){                    	
					seleccionarGeomDefault(_res);
				} else {
					var idgeom = document.getElementById('divPeriodoSeleccionado').getAttribute('idgeom');
					accionSeleccionarGeom(idgeom, _res);
				}*/
				}
		}
    });
}


function consultarBuffer(idIndicador, ano, mes){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var _paramMes = 1;
    if (_Select_Fecha.mes != null && _Select_Fecha.mes > 0){
        _paramMes = _Select_Fecha['mes'];
    }
    
    _paramDia=1;
    if(_Select_Fecha['dia'] != null && _Select_Fecha['dia'] >0){
		_paramDia=_Select_Fecha['dia']
	}
       
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada,
        'id': idIndicador,
        'ano': ano,
        'mes': _paramMes,
        'dia': _paramDia
    };
    
	_cn = consultasPHP_nueva('./app_ind/app_ind_consultar_indicador_geom_buffer.php');  
    $.ajax({
		url:   './app_ind/app_ind_consultar_indicador_geom_buffer.php',
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

			for(var _na in _res.acc){
				procesarAcc(_res.acc[_na]);
			}
				
			dibujarBufferMapa(_res);
			
			console.log(_res.data.capa.sld);
			if(_res.data.capa_superp!=undefined){
				//console.log(_res.data.capa.sld);
				dibujarCapaSuperp(_res);	
				if(obtenerNombreMes(_res.data.periodo.mes-1)==undefined){_m='';}else{_m=obtenerNombreMes(_res.data.periodo.mes-1);}
				
				
				_p=_res.data.periodo.ano+'_'+parseInt(_res.data.periodo.mes)+'_'+parseInt(_res.data.periodo.dia);
				
				document.querySelector('#periodo > #selectorPeriodo [periodo="'+_p+'"]').setAttribute('estado_resumen','cargado');            
				
				
				_selector='#periodo > #selectorPeriodo [periodo="'+_p+'"] #valor';
				
				console.log(_selector);
				_divValor=document.querySelector(_selector);          
				_val=_res.data.intersec_sum;
				
				if(_val>100){
					_v=formatearNumero(_val,0);	
				}else{
					_v=formatearNumero(_val,2);	
				}
				_divValor.innerHTML=_v;
				
				_p=_res.data.periodo.ano+'_'+parseInt(_res.data.periodo.mes)+'_'+parseInt(_res.data.periodo.dia);
				_selector='#periodo > #selectorPeriodo [periodo="'+_p+'"] #porc';
				//console.log(_selector);
				_divPorc=document.querySelector(_selector);
				//console.log(_divPorc);	
				
				if(Number(_res.data.geom_superp_max.superp_max_numero1)>0){
					_val=Number(_res.data.intersec_sum*100/_res.data.geom_superp_max.superp_max_numero1);
					if(_val>10){
						_v=formatearNumero(_val,0);	
					}else{
						_v=formatearNumero(_val,2);
					}
					_divPorc.innerHTML=_v+'%';
				}
			}
			completarResumenPeriodos();
		}
    });
}

function completarResumenPeriodos(){
	
	if(document.querySelector('#selectorPeriodo [estado_resumen="precarga"].completo')==null){
		//ningún período psin carga de vlores resumen.
		return;
	}

	_div=document.querySelector('#selectorPeriodo [estado_resumen="precarga"].completo');
	
    var zz_publicada = '1';
    
    _per=_div.getAttribute('periodo');
    _s=_per.split('_');
    
    var _paramMes = 1;
    if (_s[1] != null && _s[1] > 0){
        _paramMes = _s[1];
    }
    
    _paramDia=1;
    if(_s[2] != null && _s[2] >0){
		_paramDia=_s[2]
	}
       
    var parametros = {
        'codMarco': _CodMarco,
        'idMarco': _IdMarco,
        'zz_publicada': zz_publicada,
        'id': _DataIndicador.id,
        'ano': _s[0],
        'mes': _paramMes,
        'dia': _paramDia
    };
    
	_cn = consultasPHP_nueva('./app_ind/app_ind_consultar_indicador_geom_buffer.php');  
    $.ajax({
		url:   './app_ind/app_ind_consultar_indicador_geom_buffer.php',
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

			for(var _na in _res.acc){
				procesarAcc(_res.acc[_na]);
			}
			
			if(_res.data.capa_superp!=undefined){
				//console.log(_res.data.capa.sld);
				//dibujarCapaSuperp(_res);	
				if(obtenerNombreMes(_res.data.periodo.mes-1)==undefined){_m='';}else{_m=obtenerNombreMes(_res.data.periodo.mes-1);}
				
				_p=_res.data.periodo.ano+'_'+parseInt(_res.data.periodo.mes)+'_'+parseInt(_res.data.periodo.dia);
				
				console.log(_p);
				console.log('#periodo > #selectorPeriodo [periodo="'+_p+'"]');
				document.querySelector('#periodo > #selectorPeriodo [periodo="'+_p+'"]').setAttribute('estado_resumen','cargado');            
				_selector='#periodo > #selectorPeriodo [periodo="'+_p+'"] #valor';
				console.log(_selector);
				_divValor=document.querySelector(_selector);             
				_val=_res.data.intersec_sum;
				
				if(_val>100){
					_v=formatearNumero(_val,0);	
				}else{
					_v=formatearNumero(_val,2);	
				}
				_divValor.innerHTML=_v;
				
				_p=_res.data.periodo.ano+'_'+parseInt(_res.data.periodo.mes)+'_'+parseInt(_res.data.periodo.dia);
				_selector='#periodo > #selectorPeriodo [periodo="'+_p+'"] #porc';
				//console.log(_selector);
				_divPorc=document.querySelector(_selector);
				//console.log(_divPorc);	
				
				if(Number(_res.data.geom_superp_max.superp_max_numero1)>0){
					_val=Number(_res.data.intersec_sum*100/_res.data.geom_superp_max.superp_max_numero1);
					if(_val>10){
						_v=formatearNumero(_val,0);	
					}else{
						_v=formatearNumero(_val,2);
					}
					_divPorc.innerHTML=_v+'%';
				}
			}else{
				console.log('sin capa superp');
				_p=_res.data.periodo.ano+'_'+parseInt(_res.data.periodo.mes)+'_'+parseInt(_res.data.periodo.dia);
				
				console.log(_p);
				console.log('#periodo > #selectorPeriodo [periodo="'+_p+'"]');
				document.querySelector('#periodo > #selectorPeriodo [periodo="'+_p+'"]').setAttribute('estado_resumen','cargado');            
				_selector='#periodo > #selectorPeriodo [periodo="'+_p+'"] #valor';
				console.log(_selector);
				_divValor=document.querySelector(_selector);             
				_val=_res.data.intersec_sum;
				
				if(_val>100){
					_v=formatearNumero(_val,0);	
				}else{
					_v=formatearNumero(_val,2);	
				}
				_divValor.innerHTML=_v;
				
				_p=_res.data.periodo.ano+'_'+parseInt(_res.data.periodo.mes)+'_'+parseInt(_res.data.periodo.dia);
				_selector='#periodo > #selectorPeriodo [periodo="'+_p+'"] #porc';
				//console.log(_selector);
				_divPorc=document.querySelector(_selector);
			}
			completarResumenPeriodos();
		}
    });

}

function guardarInd(){
	
	_form=document.querySelector('#formEditarIndicadores');
	
	_parametros={
		'codMarco': _CodMarco,
        'idMarco': _IdMarco,
        'id': _form.getAttribute('idindicador'),
		'nombre':_form.querySelector('#indNombre').value,
		'descripcion':_form.querySelector('#indDescripcion').value,
		'fechadesde':_form.querySelector('#inputFechaDesde').value,
		'fechahasta':_form.querySelector('#inputFechaHasta').value,
		'funcionalidad':_form.querySelector('#funcionalidadSelector').value,
		'tipogeom':_form.querySelector('#inputTipoGeom').value,
		'periodicidad':_form.querySelector('#periodicidadSelector').value,
	}
	
	
	$.ajax({
		url:   './app_ind/app_ind_editar.php',
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
		}
    });
    


    if (_form.querySelector('#funcionalidadSelector').value == 'nuevaGeometria'){
        editarCampoInd('calc_buffer');
    }
    
    if (_form.querySelector('#funcionalidadSelector').value == 'nuevaGeometria'){
        editarCampoInd('calc_superp');
    }
    
    if (_form.querySelector('#funcionalidadSelector').value == 'nuevaGeometria'){
        editarCampoInd('calc_zonificacion');
    }
    
    
    var idcapa = document.getElementById('capaseleccionada').getAttribute('idcapa');
    if (idcapa != '' && idcapa > 0){
        editarCampoIndParametros('idcapa', idcapa);
    }
    
    var divColumnasValores = document.getElementById('columnasValores');
    for (var child in divColumnasValores.children){
         if (divColumnasValores.children[child].nodeType == 1 &&
           divColumnasValores.children[child].getAttribute('id') == 'nuevacolumna'){
				guardarColumna(divColumnasValores.children[child]);
        }
    }
    

    editarRepresentacionCampo();
    editarRepresentacionValorMaximo();
    editarRepresentacionValorMinimo();	
	
	
}


function guardarSLD(){
	
	//TODO desarrollar
	
}

/*
function consultarFormularioValores(idIndicador, id_p_ref_capas_registros){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_superado': '0',
        'idIndicador': idIndicador,
        'id_p_ref_capas_registros': id_p_ref_capas_registros,
        'ano': ano,
        'mes': mes
    };
    
    $.ajax({
            url:  './app_ind/app_ind_val_consultar.php',
            type: 'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                console.log(_res);
                if(_res.res=='exito'){
                    cargarFormularioValores(_res);
                }else{
                    alert('error asf89d0jg44f8f0d7gh');
                }
            }
    });
}

*/

function eliminarValorIndicador(idindval){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idindval
    };

	_cn = consultasPHP_nueva('./app_ind/app_ind_val_eliminar.php');  
    $.ajax({
        url:   './app_ind/app_ind_val_eliminar.php',
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

			limpiarValorIndicador();
			
			if (_res.data != null){
				document.getElementById('divPeriodoSeleccionado').setAttribute('idindval', _res.data['id']);
			}
			
			var idgeom = document.getElementById('divPeriodoSeleccionado').getAttribute('idgeom');
			var idindicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
			
			consultarFormularioValores(idindicador, idgeom);
		}
    });
}

function accionEditarValorGuardar(_this){
    var fechaAhora = new Date();
    var _paramGral = {
        'codMarco': _CodMarco,
        'idMarco': '',
        'id': document.getElementById('divPeriodoSeleccionado').getAttribute('idindval'),
        'idIndicador': _DataIndicador.id,
        'ano': document.getElementById('divPeriodoSeleccionado').getAttribute('ano'),
        'mes': document.getElementById('divPeriodoSeleccionado').getAttribute('mes'),
        'dia': document.getElementById('divPeriodoSeleccionado').getAttribute('dia'),
        'fechadecreacion': fechaAhora.toISOString().slice(0,10)        
    };
    
    _registros=document.querySelectorAll('#listaUnidadesInd input[cambiado="si"]');
    _envios=Array();
    for(_rn in _registros){
    	if(typeof _registros[_rn] != 'object'){continue;}
    	
    	_uni=_registros[_rn].parentNode.parentNode;
    	_idg=_uni.getAttribute('idgeom');
    	
    	_imps=_uni.querySelectorAll('input');
    	
    	_envios[_idg]=Array();
    	_envios[_idg]['id_p_ref_capas_registros']=_idg;
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

	_cn = consultasPHP_nueva('./app_ind/app_ind_val_editar.php');  
	$.ajax({
		url:   './app_ind/app_ind_val_editar.php',
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
		}
	})
	.done(function (_data, _textStatus, _jqXHR){
		_res = preprocesarRespuestaAjax(_data, _textStatus, _jqXHR);
		if(_res===false){return;}
		
		cargarInfoIndicador(_res.data.indid, true);
		
		accionPeriodoElegido(_Select_Fecha.ano+'_'+_Select_Fecha.mes+'_'+_Select_Fecha.dia, 'false');	
		
	})	    
    }
}

function guardarNuevaGeometria(_geometria,_idm,_elem){
	var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var idIndicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    if(_DataIndicador.id!=idIndicador){alert('error al formular envío');}
    var idCapa = _DataIndicador.id_p_ref_capasgeo;
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    var dia = document.getElementById('divPeriodoSeleccionado').getAttribute('dia');
    var fechaAhora = new Date();
    
	var _paramGral = {
        'codMarco': codMarco,
        'geometria':_geometria,
        'idMarco': idMarco,
        'idIndicador': idIndicador,
        'idCapa': idCapa,        
        'renombre':'',
        'idCapa_registro_duplica': '',
        'idCapa_registro_elimina': '',
        'ano': ano,
        'mes': mes,
        'dia': dia,
        'fechadecreacion': fechaAhora.toISOString().slice(0,10)        
    };

	_cn = consultasPHP_nueva('./app_ind/app_ind_geom_editar.php');  
	$.ajax({
		url:   './app_ind/app_ind_geom_editar.php',
		type:  'post',
		data:_paramGral,
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

			mapa.removeInteraction(drawL);
			accionPeriodoElegido(_DataPeriodo.ano, _DataPeriodo.mes, 'false');
			//accionIndicadorPublicadoSeleccionado('',_res.data.idInd);
		}
	});
}


function eliminarGeom(_this){
	//elimina goemetría vinculada a un períono para un indicador.
	_idgeom=_this.parentNode.getAttribute('idgeom');
	
	if(!confirm('¿Confirma Eliminar esta geometría para este período?')){return;}

	var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var idIndicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    if(_DataIndicador.id!=idIndicador){alert('error al formular envío');}
    var idCapa = _DataIndicador.id_p_ref_capasgeo;
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    var fechaAhora = new Date();
    
	var _paramGral = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'idIndicador': idIndicador,
        'idCapa_registro_elimina':_idgeom,
        'geometria':'',
        'renombre':'',
        'idCapa_registro_duplica':'',
        'idCapa': idCapa,
        'ano': ano,
        'mes': mes 
    };

	_cn = consultasPHP_nueva('./app_ind/app_ind_geom_editar.php');  
	$.ajax({
		data:_paramGral,
		url:   './app_ind/app_ind_geom_editar.php',
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
			if(_res.res=='exito'){
				accionPeriodoElegido(_DataPeriodo.ano, _DataPeriodo.mes, 'false');
				//accionIndicadorPublicadoSeleccionado('',_res.data.idInd);					
			}
		}
	});	
}    


function guardarNombreGeometria(_idgeom,_nombre){
	//elimina goemetría vinculada a un períono para un indicador.
	
	var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var idIndicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    if(_DataIndicador.id!=idIndicador){alert('error al formular envío');}
    var idCapa = _DataIndicador.id_p_ref_capasgeo;
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    var fechaAhora = new Date();
    
	var _paramGral = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'idIndicador': idIndicador,
        'idCapa_registro_elimina':'',
        'geometria':'',
        'renombre':_nombre,
        'idCapa_registro_renombre':_idgeom,
        'idCapa_registro_duplica':'',
        'idCapa': idCapa,
        'ano': ano,
        'mes': mes 
    };

    _cn = consultasPHP_nueva('./app_ind/app_ind_geom_editar.php');  
	$.ajax({
		data:_paramGral,
		url:   './app_ind/app_ind_geom_editar.php',
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

			document.querySelector('#listaUnidadesInd .unidad[idgeom="'+_res.data.id_geom_edit+'"] input.renombra').removeAttribute('editando');
			accionIndicadorPublicadoSeleccionado('',_res.data.idInd);					
		}
	});	
}    

function accionCopiarGeometriaAnterior(_this){
	//para indicadores de funcionalidad nuevaGeometria, busca geometrías del período anterior al dado y las replica para este período.	
	var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var idIndicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    if(_DataIndicador.id!=idIndicador){alert('error al formular envío');}
    var idCapa = _DataIndicador.id_p_ref_capasgeo;
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    var dia = document.getElementById('divPeriodoSeleccionado').getAttribute('dia');
    var fechaAhora = new Date();
    
    _prev = document.querySelector('#selectorPeriodo .card[selected="true"]').previousSibling.getAttribute('periodo');
    _p=_prev.split('_');
	_prev_ano = _p[0];
    _prev_mes = _p[1].padStart(2, "0");
    _prev_dia = _p[2].padStart(2, "0");
    
	var _paramGral = {
        'codMarco': codMarco,
        'geometria':'',
        'idMarco': idMarco,
        'idIndicador': idIndicador,
        'idCapa': idCapa,
        'ano': ano,
        'mes': mes,
        'dia': dia,
        'prev_ano': _prev_ano,
        'prev_mes': _prev_mes,
        'prev_dia': _prev_dia,
        'fechadecreacion': fechaAhora.toISOString().slice(0,10)        
    };

	_cn = consultasPHP_nueva('./app_ind/app_ind_geom_duplicar_periodo.php');  
	$.ajax({
		data:_paramGral,
		url:   './app_ind/app_ind_geom_duplicar_periodo.php',
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

			accionPeriodoElegido(_DataPeriodo.ano, _DataPeriodo.mes, 'false');
			//accionIndicadorPublicadoSeleccionado('',_res.data.idInd);					
		}
	});
}

function formularCopiarGeometriaCapa(){
    var codMarco = getParameterByName('cod');
	var _paramGral = {
        'codMarco': codMarco,
        'zz_publicada':'1'
    };

    _cn = consultasPHP_nueva('./app_capa/app_capa_consultar_listado.php');  
	$.ajax({
		data:_paramGral,
		url:   './app_capa/app_capa_consultar_listado.php',
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
			
			_DataCapas=_res.data;
			//console.log(_DataCapas);
			
			_lista=document.querySelector('#listacapasfuente');
			_lista.innerHTML='';
			for(_idc in _res.data){
				_datc=_res.data[_idc];
				_a=document.createElement('a');
				_lista.appendChild(_a);
				_a.innerHTML=_datc.id+' - '+_datc.nombre;
				_a.setAttribute('onclick','accionCopiarGeometriaCapa("'+_datc.id+'")');
			}
		}
	});
}

function formularCopiarGeometriaRele(){
	var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco
    };

	_cn = consultasPHP_nueva('./app_rele/app_rele_consultar_listado.php');  
    $.ajax({
		url:   './app_rele/app_rele_consultar_listado.php',
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
		}     
    })
	.done(function (_data, _textStatus, _jqXHR){
			_res = preprocesarRespuestaAjax(_data, _textStatus, _jqXHR);
			if(_res===false){return;}
			_Data_reles=_res.data;
			_lista=document.querySelector('#listarelesfuente');
			_lista.innerHTML='';
			for(_idr in _res.data){
				_a=document.createElement('a');
				_lista.appendChild(_a);
				_a.innerHTML=_idr+' - '+_res.data[_idr].nombre;
				_a.setAttribute('onclick','formularSeleccionCamposRele("'+_idr+'")');
			}
	})	 
}

function formularSeleccionCamposRele(_idrele){
	document.querySelector('#formImportarRele #accionImportarRele').setAttribute('id_rele',_idrele);
	document.querySelector('#formImportarRele #selectorCamposRele').setAttribute('estado','activo');
	_sel=document.querySelector('#formImportarRele [name="campo_importar_1"]');
	_op=document.createElement('option');
	_op.value='0';
	_op.innerHTML='- dejar vacío -';
	_sel.appendChild(_op);
		
	_reldat=_Data_reles[_idrele];
	
	for(_idc in _reldat.campos){
		if(_reldat.campos[_idc].tipo!='numero'){
			continue;//por ahora solo se pueden importar datos numericos
		}
		_op=document.createElement('option');
		_op.value=_idc;
		_op.innerHTML=_reldat.campos[_idc].nombre;
		_op.title=_reldat.campos[_idc].ayuda;
		_sel.appendChild(_op);
	}
	
	document.querySelector('#formImportarRele [name="campo_importar_1"]').innerHTML=_sel.innerHTML;
	document.querySelector('#formImportarRele [name="campo_importar_2"]').innerHTML=_sel.innerHTML;
	document.querySelector('#formImportarRele [name="campo_importar_3"]').innerHTML=_sel.innerHTML;
	document.querySelector('#formImportarRele [name="campo_importar_4"]').innerHTML=_sel.innerHTML;
}

function importarRele(){		
	var parametros = {
        'codMarco': _CodMarco,
        'idMarco': _IdMarco,
        'idindicador': _DataIndicador.id,
        'id_rele_campa':document.querySelector('#formImportarRele #accionImportarRele').getAttribute('id_rele'),
        'campo_importar_1':document.querySelector('#formImportarRele [name="campo_importar_1"]').value,
        'campo_importar_2':document.querySelector('#formImportarRele [name="campo_importar_2"]').value,
        'campo_importar_3':document.querySelector('#formImportarRele [name="campo_importar_3"]').value,
        'campo_importar_4':document.querySelector('#formImportarRele [name="campo_importar_4"]').value
    };	

	_cn = consultasPHP_nueva('./app_ind/app_ind_importar_rele.php');  
    $.ajax({
		url:   './app_ind/app_ind_importar_rele.php',
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

			alert('Los datos y geometrías fueron importados con éxito. Recargar la página para actualizar la información visible');    
            }
    });
}

function formularCruce(){	
	document.querySelector('#form_cruce').setAttribute('estado','activo');
	
	_si1=document.querySelector('#form_cruce select[name="inicador_1"]');
	_si1.innerHTML='';
	
	_sc1=document.querySelector('#form_cruce select[name="campo_i_1"]');
	_sc1.innerHTML='';
	
	
	_si2=document.querySelector('#form_cruce select[name="inicador_2"]');
	_si2.innerHTML='';
	
	_sc2=document.querySelector('#form_cruce select[name="campo_i_2"]');
	_sc2.innerHTML='';
	
	_op=document.createElement('option');
	_op.innerHTML='- elegir indicador principal -';
	_op.value='';
	_si1.appendChild(_op);
	
	for(_idi in _DataListaIndicadores){
		_op=document.createElement('option');
		_op.innerHTML=_idi+' - '+_DataListaIndicadores[_idi].nombre+' : '+ _DataListaIndicadores[_idi].periodicidad +' : '+_DataListaIndicadores[_idi].tipogeometria;
		_op.value=_idi;
		_si1.appendChild(_op);
	}
			
}

function actualizarFormCruceSi1(){
	_idi_1=document.querySelector('#form_cruce select[name="inicador_1"').value;
	if(_idi_1==''){formularCruce();return;}
	
	_capa1 = _DataListaIndicadores[_idi_1].id_p_ref_capasgeo;
	if(_capa1==''){formularCruce();return;}
	
	_tipogeom=_DataListaIndicadores[_idi_1].tipogeometria;
			
	_sc1=document.querySelector('#form_cruce select[name="campo_i_1"');
	_sc1.innerHTML='';
	
	_op=document.createElement('option');
	_op.innerHTML='- elegir la variable de cruce primaria -';
	_op.value='';
	_sc1.appendChild(_op);	
	
	if(_DataListaIndicadores[_idi_1].col_numero1_nom!=''){
		_op=document.createElement('option');
		_op.innerHTML=_DataListaIndicadores[_idi_1].col_numero1_nom;
		_op.value='col_numero1';
		_sc1.appendChild(_op);		
	}
	
	if(_DataListaIndicadores[_idi_1].col_numero1_nom!=''){
		_op=document.createElement('option');
		_op.innerHTML=_DataListaIndicadores[_idi_1].col_numero2_nom;
		_op.value='col_numero2';
		_sc1.appendChild(_op);
	}
	
	if(_DataListaIndicadores[_idi_1].col_numero1_nom!=''){			
		_op=document.createElement('option');
		_op.innerHTML=_DataListaIndicadores[_idi_1].col_numero3_nom;
		_op.value='col_numero3';
		_sc1.appendChild(_op);	
	}
	
	if(_DataListaIndicadores[_idi_1].col_numero1_nom!=''){			
		_op=document.createElement('option');
		_op.innerHTML=_DataListaIndicadores[_idi_1].col_numero4_nom;
		_op.value='col_numero4';
		_sc1.appendChild(_op);	
	}
	
	if(_DataListaIndicadores[_idi_1].col_numero1_nom!=''){			
		_op=document.createElement('option');
		_op.innerHTML=_DataListaIndicadores[_idi_1].col_numero5_nom;
		_op.value='col_numero5';
		_sc1.appendChild(_op);	
	}
			
	_si2=document.querySelector('#form_cruce select[name="inicador_2"');	
	
	_op=document.createElement('option');
	_op.innerHTML='- elegir la variable de cruce secundaria -';
	_op.value='';
	_si2.appendChild(_op);	
	
	for(_idi in _DataListaIndicadores){		
		if(_tipogeom==_DataListaIndicadores[_idi].tipogeometria){continue;}
		if( _DataListaIndicadores[_idi].tipogeometria=='LineString'){continue;}		
		_op=document.createElement('option');
		_op.innerHTML=_idi+' - '+_DataListaIndicadores[_idi].nombre+' : '+ _DataListaIndicadores[_idi].periodicidad+' : '+_DataListaIndicadores[_idi].tipogeometria;
		_op.value=_idi;
		_si2.appendChild(_op);
	}
		
	_sc2=document.querySelector('#form_cruce select[name="campo_i_2"');
	_sc2.innerHTML='';
	
	_scapa=document.querySelector('#form_cruce select[name="capa_3"]');
	_scapa.innerHTML='';	
	
	if(_tipogeom=='Polygon'){
		//no acepta capa de superposicion
		return;
	}
	_op=document.createElement('option');
	_op.innerHTML='- elegir una capa de superposición -';
	_op.value='';
	_scapa.appendChild(_op);
	
	for(_id_capa in _DataCapas){
		if( _DataCapas[_id_capa].zz_aux_ind!=null || _DataCapas[_id_capa].zz_aux_rele!=null){continue;}
		_op=document.createElement('option');
		_op.innerHTML=_DataCapas[_id_capa].nombre;
		_op.value=_id_capa;
		_scapa.appendChild(_op);
	}
}

function actualizarFormCruceSi1Campos2(){	
	_idi_2=document.querySelector('#form_cruce select[name="inicador_2"').value;
	if(_idi_2==''){formularCruce();return;}
	
	_capa2 = _DataListaIndicadores[_idi_2].id_p_ref_capasgeo;
	if(_capa2==''){return;}
	
	_tipogeom=_DataListaIndicadores[_idi_2].tipogeometria;
			
	_sc2=document.querySelector('#form_cruce select[name="campo_i_2"');
	_sc2.innerHTML='';
		
	_op=document.createElement('option');
	_op.innerHTML='- elegir la variable de cruce secundaria -';
	_op.value='';
	_sc2.appendChild(_op);	
	
	if(_DataListaIndicadores[_idi_2].col_numero1_nom!=''){
		_op=document.createElement('option');
		_op.innerHTML=_DataListaIndicadores[_idi_2].col_numero1_nom;
		_op.value='col_numero1';
		_sc2.appendChild(_op);		
	}
	
	if(_DataListaIndicadores[_idi_2].col_numero2_nom!=''){
		_op=document.createElement('option');
		_op.innerHTML=_DataListaIndicadores[_idi_2].col_numero2_nom;
		_op.value='col_numero2';
		_sc2.appendChild(_op);
	}
	
	if(_DataListaIndicadores[_idi_2].col_numero1_nom!=''){			
		_op=document.createElement('option');
		_op.innerHTML=_DataListaIndicadores[_idi_2].col_numero3_nom;
		_op.value='col_numero3';
		_sc2.appendChild(_op);	
	}
	
	if(_DataListaIndicadores[_idi_2].col_numero1_nom!=''){			
		_op=document.createElement('option');
		_op.innerHTML=_DataListaIndicadores[_idi_2].col_numero4_nom;
		_op.value='col_numero4';
		_sc2.appendChild(_op);	
	}
	
	if(_DataListaIndicadores[_idi_2].col_numero1_nom!=''){			
		_op=document.createElement('option');
		_op.innerHTML=_DataListaIndicadores[_idi_2].col_numero5_nom;
		_op.value='col_numero5';
		_sc2.appendChild(_op);	
	}
}

function generarCruce(_modo_representa){
	var parametros = {
        'codMarco': _CodMarco,
        'idMarco': _IdMarco,
        'modo_representa':_modo_representa,
        'id_ind_1': document.querySelector('#form_cruce select[name="inicador_1"').value,
        'campo_ind_1': document.querySelector('#form_cruce select[name="campo_i_1"').value,
        'id_ind_2': document.querySelector('#form_cruce select[name="inicador_2"').value,
        'campo_ind_2': document.querySelector('#form_cruce select[name="campo_i_2"').value,
        'id_capa_3': document.querySelector('#form_cruce select[name="capa_3"').value
    };	

	_cn = consultasPHP_nueva('./app_ind/app_ind_calcular_cruce.php');  
    $.ajax({
		url:   './app_ind/app_ind_calcular_cruce.php',
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
			
			dibujarGrafico(_res); 
		}
    });
}

function dibujarGrafico(_res){
	document.querySelector('#ventanagrafico').setAttribute('estado','activo');
	
	_canvas=document.querySelector('#ventanagrafico canvas');
	_alto=800;
	_ancho=1000;
	_margen=50;
	_alto_leyenda_x=200;
	_ancho_leyenda_y=200;
	_ancho_cuadro_neto=_ancho-(_margen*2)-_ancho_leyenda_y;
	_alto_cuadro_neto=_alto-(_margen*2)-_alto_leyenda_x;
	
	_amplitud_x=_res.data.resumen.x.max-_res.data.resumen.x.min;
	_amplitud_y=_res.data.resumen.y.max-_res.data.resumen.y.min;
	
	//_canvas.setAttribute('height',(Object.keys(_gra.tareas).length * _ConfGra.fila_alto) + _ConfGra.encabez_alto);
	//_canvas.setAttribute('width',_ConfGra.canvas_ancho);
	_ctx = _canvas.getContext('2d');

	//dibuja fondo blanco
	_ctx.fillStyle = 'rgba(256,256,256,1)';
	_ctx.fillRect( //fillect(x, y, width, height)
		0, 
		0,
		_ancho,
		_alto
	);

	//Dibujar eje x
		_ctx.fillStyle = 'rgba(0,0,0,0)';
		_ctx.strokeStyle = 'rgba(0,0,0,1)';
		_ctx.lineWidth = 3;		
		_ctx.beginPath();
		_ctx.moveTo( (_margen+_ancho_leyenda_y) , (_margen+_alto_cuadro_neto));
		_ctx.lineTo( (_ancho-_margen) , (_margen+_alto_cuadro_neto));
		_ctx.stroke();
		
		// dibujar ticks X
		
		_a=[parseFloat(_res.data.resumen.x.min), 0];
		_ticks=[_a];
		_av=parseFloat(_res.data.resumen.x.min);
		
		if(_amplitud_x<=3){
			_paso=0.1;	
			_av=Math.round(_av*10)/10;
		}
		if(_amplitud_x>3){
			_paso=1;	
			_av=Math.round(_av);
		}
		if(_amplitud_x>30){
			_paso=10;	
			_av=Math.round(_av/10)*10;
		}
		if(_amplitud_x>300){
			_paso=100;	
			_av=Math.round(_av/10)*10;
		}		
		_av+=parseFloat(_paso);
		
		_c=0;				
		while(_av<parseFloat(_res.data.resumen.x.max) && _c<10){
			_c++;
			
			_u_desde_izq=_av-_res.data.resumen.x.min;
			_p_cuadro_x=_u_desde_izq/_amplitud_x;
			_pos_x_neta= _p_cuadro_x * _ancho_cuadro_neto;
			_a=[_av, _pos_x_neta];
			
			_ticks.push(_a);
			
			_av+=parseFloat(_paso);
		}
		
		_a=[parseFloat(_res.data.resumen.x.max), _ancho_cuadro_neto];
		_ticks.push(_a);
		
		for(_nt in _ticks){			
		
			_ctx.fillStyle = 'rgba(0,0,0,0)';
			_ctx.strokeStyle = 'rgba(0,0,0,1)';
			_ctx.lineWidth = 1;		
			_ctx.beginPath();
			_ctx.moveTo( _margen+_ancho_leyenda_y+_ticks[_nt][1], (_margen+_alto_cuadro_neto));
			_ctx.lineTo( _margen+_ancho_leyenda_y+_ticks[_nt][1], (_margen+_alto_cuadro_neto) + 5);
			_ctx.stroke();
		
		
			_ctx.font = '10px serif';
			_ctx.fillStyle = 'rgba(0,0,0,1)';
			_ctx.textAlign = 'center';
			_ctx.fillText(//strokeText(text, x, y [, maxWidth])
				_ticks[_nt][0], 
				( _margen+_ancho_leyenda_y+_ticks[_nt][1]), 
				(_margen+_alto_cuadro_neto) + 25
			);	
		}
		
		_ctx.font = '16px serif';
		_ctx.fillStyle = 'rgba(0,0,0,1)';
		_ctx.textAlign = 'center';
		_ctx.fillText(//strokeText(text, x, y [, maxWidth])
			'Variable Principal', 
			(_margen+_ancho_leyenda_y+_ancho_cuadro_neto/2), 
			(_margen+_alto_cuadro_neto + 60) 
		);	
		_ctx.font = '16px serif';
		_ctx.fillStyle = 'rgba(0,0,0,1)';
		_ctx.textAlign = 'center';
		_ctx.fillText(//strokeText(text, x, y [, maxWidth])
			_res.data.variable_principal.nombre, 
			(_margen+_ancho_leyenda_y+_ancho_cuadro_neto/2), 
			(_margen+_alto_cuadro_neto + 90) 
		);			
		
		
	//Dibujar eje y
		_ctx.fillStyle = 'rgba(0,0,0,0)';
		_ctx.strokeStyle = 'rgba(0,0,0,1)';
		_ctx.lineWidth = 3;		
		_ctx.beginPath();
		_ctx.moveTo( (_margen+_ancho_leyenda_y) , (_margen+_alto_cuadro_neto));
		_ctx.lineTo( (_margen+_ancho_leyenda_y) , _margen);
		_ctx.stroke();
		
		//  dibujar ticks Y

		_a=[parseFloat(_res.data.resumen.y.min), 0];
		_ticks=[_a];
		
		_av=parseFloat(_res.data.resumen.y.min);
		
		//console.log(_res.data.resumen.y.max);
		
		if(_amplitud_y<=3){
			_paso=0.1;	
			_av=Math.round(_av*10)/10;
		}
		if(_amplitud_y>3){
			_paso=1;	
			_av=Math.round(_av);
		}
		if(_amplitud_y>30){
			_paso=10;	
			_av=Math.round(_av/10)*10;
		}
		if(_amplitud_y>300){
			_paso=100;	
			_av=Math.round(_av/10)*10;
		}		
		_av+=parseFloat(_paso);
		
		_c=0;				
		while(_av<parseFloat(_res.data.resumen.y.max) && _c<10){
			_c++;
			
			_u_desde_base=_av-_res.data.resumen.y.min;
			_p_cuadro_y=_u_desde_base/_amplitud_y;
			_pos_y_neta= _p_cuadro_y * _alto_cuadro_neto;
			_a=[_av, _pos_y_neta];
			
			_ticks.push(_a);
			
			_av+=parseFloat(_paso);
		}
		console.log(_ticks);
		_a=[parseFloat(_res.data.resumen.y.max), _alto_cuadro_neto];
		_ticks.push(_a);
		
		for(_nt in _ticks){			
		
			_ctx.fillStyle = 'rgba(0,0,0,0)';
			_ctx.strokeStyle = 'rgba(0,0,0,1)';
			_ctx.lineWidth = 1;		
			_ctx.beginPath();
			_ctx.moveTo( (_margen+_ancho_leyenda_y) , _margen+_alto_cuadro_neto-_ticks[_nt][1]);
			_ctx.lineTo( (_margen+_ancho_leyenda_y-5) , _margen+_alto_cuadro_neto-_ticks[_nt][1]);
			_ctx.stroke();
		
		
			_ctx.font = '10px serif';
			_ctx.fillStyle = 'rgba(0,0,0,1)';
			_ctx.textAlign = 'right';
			_ctx.fillText(//strokeText(text, x, y [, maxWidth])
				_ticks[_nt][0], 
				(_margen+_ancho_leyenda_y-8), 
				_margen+_alto_cuadro_neto-_ticks[_nt][1], 
			);	
		}
				
			_ctx.rotate(-1*Math.PI/2);
			
			_ctx.font = '16px serif';
			_ctx.fillStyle = 'rgba(0,0,0,1)';
			_ctx.textAlign = 'center';
			_ctx.fillText(//strokeText(text, x, y [, maxWidth])
				'Variable Secundaria', 
				-1 * _alto_cuadro_neto/2, 
				_margen+_ancho_leyenda_y - 100
			);	

			_ctx.font = '16px serif';
			_ctx.fillStyle = 'rgba(0,0,0,1)';
			_ctx.textAlign = 'center';
			_ctx.fillText(//strokeText(text, x, y [, maxWidth])
				_res.data.variable_secundaria.nombre, 
				-1 * _alto_cuadro_neto/2, 
				_margen+_ancho_leyenda_y - 75
			);	
						
			_ctx.rotate(Math.PI/2);
		
	//puntos
	for(_np in _res.data.cruce){

		_u_desde_izq=_res.data.cruce[_np].campo_i_1-_res.data.resumen.x.min;
		_p_cuadro_x=_u_desde_izq/_amplitud_x;
		_pos_x_neta= _p_cuadro_x*_ancho_cuadro_neto;
		_pos_x_fin=_pos_x_neta+_ancho_leyenda_y+_margen;
		//console.log('x:'+_u_desde_izq+' '+_p_cuadro_x+' ' +_pos_x_neta+' ' +_pos_x_fin);

		_u_desde_base=_res.data.cruce[_np].campo_i_2_promedio-_res.data.resumen.y.min;
		_p_cuadro_y=_u_desde_base/_amplitud_y;
		_pos_y_neta= _p_cuadro_y*_alto_cuadro_neto;
		_pos_y_fin=_margen+_alto_cuadro_neto-_pos_y_neta;
		//console.log('y:'+_u_desde_base+' '+_p_cuadro_y+' ' +_pos_y_neta+' ' +_pos_y_fin);
				
		
		_ctx.beginPath();
		_ctx.arc(_pos_x_fin, _pos_y_fin, 5, 0, 2 * Math.PI, false);
		_ctx.fillStyle = _res.data.cruce[_np].color;
		_ctx.fill();
		_ctx.lineWidth = 5;
		_ctx.strokeStyle = _res.data.cruce[_np].color;
		_ctx.stroke();
				
	}
	
	//referencias color capas
	
	_pos=60;	
	
	_ctx.font = '16px serif';
	_ctx.fillStyle = 'rgba(0,0,0,1)';
	_ctx.textAlign = 'left';
	_ctx.fillText(//strokeText(text, x, y [, maxWidth])
		_res.data.capa.nombre, 
		(_margen), 
		(_margen+_alto_cuadro_neto + _pos) 
	);		
	
	for(_nr in _res.data.capa.reglas){
		_pos+=20;
		_dataregla=_res.data.capa.reglas[_nr];
		
		_ctx.beginPath();
		_ctx.arc((_margen), (_margen+_alto_cuadro_neto + _pos) , 5, 0, 2 * Math.PI, false);
		_ctx.fillStyle = _dataregla.color;
		_ctx.fill();
		_ctx.lineWidth = 5;
		_ctx.strokeStyle = _dataregla.color;
		_ctx.stroke();
		
		_ctx.font = '16px serif';
		_ctx.fillStyle = 'rgba(0,0,0,1)';
		_ctx.textAlign = 'left';
		_ctx.fillText(//strokeText(text, x, y [, maxWidth])
			': '+_dataregla.nombre[0], 
			(_margen+20), 
			(_margen+_alto_cuadro_neto + _pos) 
		);			
		
		_pos+=30;	
	}
}


function descargarImagen(){
	_nombre='grafico';
	_f=new Date();    
	_filename=_nombre+'_'+_f.getFullYear()+'_'+_f.getMonth()+'_'+_f.getDate()+'.png';
	 var link = document.createElement('a');
	  link.download = _filename;
	  link.href=document.querySelector('#ventanagrafico canvas').toDataURL()
	  link.click();
}

function accionCopiarGeometriaCapa(_idcapa){
	//para indicadores de funcionalidad nuevaGeometria, busca geometrías del período anterior al dado y las replica para este período.	
	var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var idIndicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    if(_DataIndicador.id!=idIndicador){alert('error al formular envío');}
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    var dia = document.getElementById('divPeriodoSeleccionado').getAttribute('dia');
    var fechaAhora = new Date();
    
    _lista=document.querySelector('#listacapasfuente');
	_lista.innerHTML='';	
				
	var _paramGral = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'idIndicador': idIndicador,
        'idCapa': _idcapa,
        'ano': ano,
        'mes': mes,
        'dia': dia,
        'fechadecreacion': fechaAhora.toISOString().slice(0,10)        
    };

	_cn = consultasPHP_nueva('./app_ind/app_ind_geom_copiar_a_periodo_capa.php');  
	$.ajax({
		url:   './app_ind/app_ind_geom_copiar_a_periodo_capa.php',
		type:  'post',
		data:_paramGral,

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

			accionPeriodoElegido(_DataPeriodo.ano, _DataPeriodo.mes, 'false');
			//accionIndicadorPublicadoSeleccionado('',_res.data.idInd);		
		}
	});
}

function accionGuardaMod(){	
	_parametros={};
	
	_form=document.querySelector('#form_ind_exp');	
	
	_inps=_form.querySelectorAll('#características_generales input, #características_generales textarea, #características_generales select');	
	for(_ni in _inps){
			if(typeof _inps[_ni] != 'object'){continue;}
		_parametros[_inps[_ni].getAttribute('name')]=_inps[_ni].value;
	}
	
	_tags=_form.querySelectorAll('#matriz_clasif .tag');	
	_parametros['tags']={};
	for(_tn in _tags){
		if(typeof _tags[_tn] != 'object'){continue;}
		
		_idt=_tags[_tn].getAttribute('id_tag');
		_stat=_tags[_tn].querySelector('[name="estado"]').checked;
		_comentario=_tags[_tn].querySelector('[name="comentario"]').value;
		_parametros.tags[_idt]={
			'stat':_stat,
			'idtag':_idt,
			'comentario':_comentario
		}
	}
	
	_apps=_form.querySelectorAll('#requerimientos .app');	
	_parametros['apps']={};
	for(_na in _apps){
		if(typeof _apps[_na] != 'object'){continue;}
		
		_coda=_apps[_na].getAttribute('cod_app');
		_stat=_apps[_na].querySelector('[name="estado"]').checked;
		_comentario=_apps[_na].querySelector('[name="comentario"]').value;
		_parametros.apps[_coda]={
			'stat':_stat,
			'cod':_coda,
			'comentario':_comentario,
			'modelos':''
		}
	}
	
    _cn = consultasPHP_nueva('./app_ind/app_ind_ed_guarda_modelo.php');  
	$.ajax({
		url:   './app_ind/app_ind_ed_guarda_modelo.php',
		type:  'post',
		data:_parametros,
		
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
		
			$("#form_ind_exp").attr("estado","inactivo");
			cargarListadoModelo();
		}
	});
}

function accionEliminarFormCent(){
	_modo=document.querySelector('#form_ind_exp').getAttribute('modo');
	if(!confirm("¿Eliminamos este "+_modo+"?.. ¿Segure?")){return;}
	
	var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
        
	if(_modo=='modelo'){
		_url=	'./app_ind/app_ind_ed_borra_modelo.php'	
	}else if(_modo=='indicador'){
		_url=	'./app_ind/app_ind_eliminar.php'
	}else{
		alert('error');return;
	}
	
	_parametros={
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': document.querySelector('#form_ind_exp [name="id"]').value		
	}
	
	_cn = consultasPHP_nueva(_url);  
	$.ajax({
		url:   _url,
		type:  'post',		
		data:_parametros,
    
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
			
			$("#form_ind_exp").attr("estado","inactivo");
			cargarListadoModelo();
		}
	});		
}
