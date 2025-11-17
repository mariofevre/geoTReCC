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
function cargarListadoPaneles(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    _source_rele.clear();
    
    var parametros = {
        'codMarco': _CodMarco,
        'idpanel':_idPanel
    };
    
    _cn = consultasPHP_nueva('./app_comu/app_comu_consultar_paneles.php');
    $.ajax({
            url:   './app_comu/app_comu_consultar_paneles.php',
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
			
			if(_idPanel==''){
				for(_np in _res.data.paneles){
					_idPanel=_np;
				}
			}
			
			_DataPaneles=_res.data.paneles;
			if(_res.data.acc>0){
				mostrarMenuAdm();
			}
			 
			_DataPanel=_res.data.paneles[_idPanel];
			_DataConsultas=_res.data.paneles[_idPanel].consultas; 
			_DataCapas=_res.data.paneles[_idPanel].capas; 
			_DataCapasRaster=_res.data.paneles[_idPanel].capasraster; 
			_DataCapasColec=_res.data.paneles[_idPanel].colecciones; 
			
			mostrarPanel(_idPanel);
			
        }
    });
}

	
function cargarCapas(){

	
	for(_nc in _DataCapas){
		_idcapa=_DataCapas[_nc].id_p_ref_capasgeo;
		if(_idcapa==null){continue;} // debe ser una capa raster
		//console.log(_DataCapas[_nc].cargada);
		
		if(_DataCapas[_nc].cargada==undefined){_DataCapas[_nc]['cargada']='no';}
		if(_DataCapas[_nc]=='si'){continue;}
	
		_DataCapas[_nc]['cargada']='si';
		var parametros = {
			'codMarco': _CodMarco,
			'idMarco': _IdMarco,
			'zz_publicada': '1',
			'idcapa': _idcapa
		};
		_url='./app_capa/app_capa_consultar_registros.php';
		_cn = consultasPHP_nueva(_url);
		$.ajax({
			url:  _url ,
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
				if(_res.res!='exito'){return;}
				
				_DataCapas[_res.data.idcapa]['registros']=_res.data.registros;					
				mapearCapa(_res.data.idcapa);
			}
		});	
	}
	cargarColecciones();
	
	for(_nc in _DataCapasRaster){
		console.log(_DataCapasRaster[_nc].fi_raster);
		//console.log(_DataCapas[_nc].cargada);
		_dat=_DataCapasRaster[_nc];
		cargarMapaRaster(_dat);
			
		
	}
}

function cargarColecciones(){
	
	for(_nc in _DataCapasColec){
		
		_idcapa=_DataCapasColec[_nc].id_p_ref_capasgeo;
		if(_idcapa>0){
			if(_DataCapasColec[_nc].cargada==undefined){
				_DataCapasColec[_nc]['cargada']='si';
							
				var parametros = {
					'codMarco': _CodMarco,
					'idMarco': _IdMarco,
					'zz_publicada': '1',
					'idcapa': _idcapa
					
				};
				_url='./app_capa/app_capa_consultar_registros.php';
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
						//console.log(_res);
						
						_DataCapas[_res.data.idcapa]['registros']=_res.data.registros;
						mostrarColeccion(_res.data.idcapa);
					}
				});	
			}
		}else{
			
		}
	}
	cargarConsultas();
}


function consultaDocsPub(){

				
	var parametros = {
		'codMarco': _CodMarco,
		'idMarco': _IdMarco
		
	};
	_url='./app_docs/app_docs_consulta_publica.php';
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
			//console.log(_res);
			
			_DataDocs=_res.data;
			
		}
	});	


}



function cargarConsultas(){
	for(_nc in _DataConsultas){
		_idrele=_DataConsultas[_nc].id_p_ref_rele_campa;
		
		if(_DataConsultas[_nc].cargada==undefined){
			_DataConsultas[_nc]['cargada']='si';
		}
			
		var parametros = {
			'codMarco': _CodMarco,
			'idMarco': _IdMarco,
			'idcampa': _idrele
		};
		
		_url='./app_rele/app_rele_consultar.php';
		_cn = consultasPHP_nueva(_url);
		$.ajax({
			url:  _url ,
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
				zoomMarco();
				//mostrarConsulta();
			}
		});
	}
}


function zoomMarco(){
	_fts=_sMarco.getFeatures();
	
	for(_fn in _fts){
		
		_feat=_fts[_fn];
		_geom = _feat.getGeometry();
		//_view.fit(_geom, {padding: [30, 30, 30, 30], minResolution: 50});
		return;
	}
}


function guardarOpinion(){
	_op='['+_DataPanel.titulo+'('+_DataPanel.id+')]: ';
	_op+=document.querySelector('#consulta [name="opinion"]').value;
	
	_geotx=document.querySelector('#consulta [name="geotx"]').value;
	if(_geotx==''){
		document.querySelector('#consulta #cartel_marcar').setAttribute('activo','si');
		return;
	}

	var parametros = {
		'codMarco': _CodMarco,
		'idMarco': _IdMarco,
		'idcampa': _idrele,
		'geotx': _geotx,
		'opinion': _op
	};
	
	_cn = consultasPHP_nueva( './app_rele/app_rele_ed_guardar_consulta_comu.php');
	$.ajax({
		url:   './app_rele/app_rele_ed_guardar_consulta_comu.php',
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
			alert('opinión guardada');
			zoomMarco();
			mostrarConsulta();
		}
	});	
}

function crearPanel(){
	var parametros = {
		'codMarco': _CodMarco
	};
	
	_url='./app_comu/app_comu_ed_crear_paneles.php';
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
			cargarListadoPaneles();
		}
	});	
}



function guardarPanel(){

	_form=document.querySelector('#menu_admin_flotante');
	
	
	
	var parametros = {
		'codMarco': _CodMarco,
		'idp':_form.querySelector('[name="idp"]').value,
		'nombre':_form.querySelector('[name="nombre"]').value,
		'descripcion':_form.querySelector('[name="descripcion"]').value,
		'visible':_form.querySelector('[name="visible"]').value,
		'mba':_form.querySelector('[name="mba"]').value,
		'x':_form.querySelector('[name="x"]').value,
		'y':_form.querySelector('[name="y"]').value,
		'z':_form.querySelector('[name="z"]').value,
		'muestra_marco':_form.querySelector('[name="muestra_marco"]').value,
		
		
		'col_fi_img_raster': _form.querySelector('#formulario_coleccion [name="fi_img_raster"]').value,
		'col_fi_img_raster_ovr': _form.querySelector('#formulario_coleccion [name="fi_img_raster_ovr"]').value,
		
		'col_id_p_ref_capasgeo': _form.querySelector('#formulario_coleccion [name="id_p_ref_capasgeo"]').value,
		'col_campo': _form.querySelector('#formulario_coleccion [name="campo"]').value,
		'col_campob': _form.querySelector('#formulario_coleccion [name="campob"]').value,
		
		
		
		'cap_fi_raster': _form.querySelector('#formulario_capa [name="fi_raster"]').value,
		'cap_fi_raster_ovr': _form.querySelector('#formulario_capa [name="fi_raster_ovr"]').value,
		'cap_simbologia_raster': _form.querySelector('#formulario_capa [name="simbologia_raster"]').value,
		'cap_etiquetas_raster': _form.querySelector('#formulario_capa [name="etiquetas_raster"]').value,
	
		
		'cap_id_p_ref_capasgeo': _form.querySelector('#formulario_capa [name="id_p_ref_capasgeo"]').value,
		'cap_simbologia': _form.querySelector('#formulario_capa [name="simbologia"]').value
	};
	
	_url='./app_comu/app_comu_ed_guarda_paneles.php';
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
			cargarListadoPaneles();
		}
	});		
	
	
	
}
