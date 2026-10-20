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
            if(_res.res!='exito'){return;}
            
            
        }
 	});	
}

function consultarUsuaries(){
	_parametros = {
		'codMarco':_CodMarco,	
		'accion':'app_rele'
	}
    _cn = consultasPHP_nueva('./usuarios/acc_consulta_compas.php');
	$.ajax({
        url:   './usuarios/acc_consulta_compas.php',
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
            	return;
            }
            _DataUsuaries=_res.data;
        }
 	});	
}





function consultarListadoRaster(){
		
	_parametros = {
		'codMarco':_CodMarco
	}
	_cn = consultasPHP_nueva('./app_raster/app_raster_consulta_listado.php');
	
	$.ajax({
		url:   './app_raster/app_raster_consulta_listado.php',
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
            
            _DataRaster['listado']=_res.data;
            
            mostrarListadoRaster();
		}
	});	
	
}

function accionCargarDocsCandidatosRaster(){
        
    var parametros = {
        'codMarco': _CodMarco
    };
	_cn = consultasPHP_nueva('./app_docs/app_docs_consulta_externa.php');
    
    $.ajax({
		url:   './app_docs/app_docs_consulta_externa.php',
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
			
			_DataRaster['candidatos']=_res.data;
							
			document.querySelector('#divCandidatosDocRaster #listado_candidatos').innerHTML='';
				
			mostrarListadoCandidatosDocs(_res.data);
			
		}
    });
}


function procesarDocARaster(_iddoc,_idraster,_modo,_id_tipo_banda){
	
	// _modo = 'todos' genera rasters y os procesa a partir de todo el listado de documentos candidatos cargado en HTML
	// _modo = 'raster' procesa todas las instancias de un raster
	// _modo = 'bandas' procesa todas las bandas correspondientes al raster activo
	// _modo = 'uno' (defecto): realiza un solo paso (por lo general una banda)
	
	
	if((_modo=='todos'||_modo=='raster'||_modo=='bandas')&&_iddoc==''){
		_a=document.querySelector('#divCandidatosDocRaster #listado_candidatos [proceso="no"]');
		_a.setAttribute('proceso','procesando');
		_iddoc=_a.getAttribute('iddoc');
	}
	
	if(_idraster>0){
		document.querySelector('#cuadrovalores #formRaster .estado').setAttribute('estado','procesando');	
	}else{
		_a=document.querySelector('#divCandidatosDocRaster #listado_candidatos a[iddoc="'+_iddoc+'"]');
		if(_a==null){alert('error');return;}
		_a.setAttribute('proceso','procesando');
	}
	
	if(_id_tipo_banda==null||_id_tipo_banda==""){
		_id_tipo_banda=0;
	}else{
		_sel='#cuadrovalores #formRaster #bandas .banda[id_tipo_banda="'+_id_tipo_banda+'"]';
		console.log(_sel);
		_a=document.querySelector(_sel);
		_a.setAttribute('estado','procesando');
	}
	
	_parametros = {
		'codMarco':_CodMarco,
		'idraster':_idraster,
		'iddoc':_iddoc,
		'modo':_modo,
		'id_tipo_banda':_id_tipo_banda
	}	

	_cn = consultasPHP_nueva('./app_raster/app_raster_proc_procesar_archivo.php');
	
	$.ajax({
		url:   './app_raster/app_raster_proc_procesar_archivo.php',
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
			
		
			_iddoc=_res.data.iddoc;
			
			
			_DataRaster.listado.coberturas[_res.data.cobertura.id]=_res.data.cobertura;
			_idr=_res.data.cobertura.id;
			
			console.log('mostramos listado:');
			mostrarListadoRaster();
			
			console.log('mostramos raster:');
			mostrarRaster(_idr);
			
			
			_a=document.querySelector('#divCandidatosDocRaster #listado_candidatos [iddoc="'+_iddoc+'"]');
			if(_a!=null){
				_a.setAttribute('proceso','localizada');			
			}
			
			
			document.querySelector('#cuadrovalores').setAttribute('divCandidatosDocRaster','no');
			
			
			_iddoc_sig='';
			
			
			
			if(_res.data.modo=='todos'){	
							
				if(_res.avance=='final'){
					_a=document.querySelector('#divCandidatosDocRaster #listado_candidatos [proceso="no"]');				
					
					if(_a==null){
						//TODOS LOS DOCUMENTOS PROCESADOS
						return;
					}else{
						_a.setAttribute('proceso','procesando');
						_iddoc_sig=_a.getAttribute('iddoc');				
						procesarDocARaster(_iddoc_sig,"",_res.data.modo,"");
					}
				}else{					
					_iddoc_sig=_iddoc;
					procesarDocARaster(_iddoc_sig,_res.data.cobertura.id,_res.data.modo,"");
				}
			}
						
			if(_res.data.modo=='raster'){
				
				if(_res.avance=='final'){
					return;					
				}else{
					_iddoc_sig=_iddoc;
					procesarDocARaster(_iddoc_sig,_res.data.cobertura.id,_res.data.modo,"");
				}
			}
			
			if(_res.data.modo=='bandas'){
				
				if(_res.avance=='final'){
					return;					
				}else{
					_iddoc_sig=_iddoc;
					procesarDocARaster(_iddoc_sig,_res.data.cobertura.id,_res.data.modo,"");
				}
			}
			
			
			if(_res.data.modo=='uno'){				
					return;		
			}
			
			
		}
	});	
}


function consultarSampleoRasterTCI(_ext,_size,_id_tipo_banda,_id_raster_cobertura){
	
	_datr=_DataRaster.listado.coberturas[_id_raster_cobertura];
	
	_id_tipo_raster=_datr.id_p_ref_raster_tipos_diccionario;
	
	_monobanda=_DataRaster.listado.tipos[_id_tipo_raster].bandas[_id_tipo_banda].monobanda;
	
	_mapearcolor='no';
	if(_monobanda=='1'){
		_mapearcolor='si';
	}
	
	
	_nombretabla="r"+_datr.id.padStart(8, "0")+"_"+_datr.bandas[_id_tipo_banda].id;
	
	_parametros = {
		'codMarco':_CodMarco,
		'id_raster':32,
		'tabla_raster_banda':_nombretabla,
		'px_alto':_size[1],
		'mapearcolor':_mapearcolor,
		'modo_salida':'url_imagen',
		'px_ancho':_size[0],
		'max_x':_ext[2],
		'max_y':_ext[3],
		'min_x':_ext[0],
		'min_y':_ext[1],
		't':Math.floor(Date.now() / 1000)
	};
	
	_str='';
	for(_k in _parametros){
		_v=_parametros[_k];
		_str+=_k+'='+_v+'&';
	}
	_str=_str.slice(0,-1);
	_url='./app_raster/app_raster_consulta_vista.php?'+_str;
	
	_src_muestra_raster.set('url',_url);
	
	_geo=_datr.geom_tx;
	if(_geo==''||_geo=='undefined'||_geo==undefined){return;}//esta cobertura no tiene geometría asociada			
	var _format = new ol.format.WKT();
	var _ft = _format.readFeature(_geo, {
		dataProjection: 'EPSG:3857',
		featureProjection: 'EPSG:3857'
	});
	
	_DataRaster.listado.coberturas[_gn]['ft_dibujada']=true;
	
	_f_ext=_ft.getGeometry().getExtent();
	
	_xmax=Math.min(_f_ext[2],_ext[2]);
	_ymax=Math.min(_f_ext[3],_ext[3]);
	_xmin=Math.max(_f_ext[0],_ext[0]);
	_ymin=Math.max(_f_ext[1],_ext[1]);
	_ext_int=[_xmin, _ymin, _xmax, _ymax];
	
	console.log(_ext);
	console.log(_f_ext);	
	console.log(_ext_int);
	_src_muestra_raster = new ol.source.ImageStatic({
		opacity: 1,
		url: _url,
		imageExtent: _ext_int,
		projection: 'EPSG:3857'
	});
	_lyr_muestra_raster.setSource(_src_muestra_raster);


	_parametros['modo_salida']='url_json';
		
	$.ajax({
		url:   './app_raster/app_raster_consulta_vista.php',
		type:  'get',
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
			
			
			_Def={
					"EPSG:32717":"+proj=utm +zone=17 +south +datum=WGS84 +units=m +no_defs +type=crs",
					"EPSG:32718":"+proj=utm +zone=18 +south +datum=WGS84 +units=m +no_defs +type=crs",
					"EPSG:32719":"+proj=utm +zone=19 +south +datum=WGS84 +units=m +no_defs +type=crs",
					"EPSG:32720":"+proj=utm +zone=20 +south +datum=WGS84 +units=m +no_defs +type=crs",
					"EPSG:32721":"+proj=utm +zone=21 +south +datum=WGS84 +units=m +no_defs +type=crs",
					"EPSG:32722":"+proj=utm +zone=22 +south +datum=WGS84 +units=m +no_defs +type=crs"
				};
			
			proj4.defs("EPSG:"+_res.data.srid,_Def["EPSG:"+_res.data.srid]);
			ol.proj.proj4.register(proj4);
			

		
			_src_muestra_raster = new ol.source.ImageStatic({
				opacity: 1,
				url: "data:image/png;base64," + _res.data.img_data,
				imageExtent: _res.data.extend,
				projection: 'EPSG:'+_res.data.srid,
				interpolate: false
			});
			_lyr_muestra_raster.setSource(_src_muestra_raster);
			
			_i=document.createElement('img');
			_i.style.display='none';
			document.querySelector('body').appendChild(_i);
			_i.setAttribute('src', "data:image/png;base64," + _res.data.img_data);
		}
	});	
	
	
	//console.log(_src_muestra_raster.getUrl());
	/*
	_cn = consultasPHP_nueva('./app_raster/app_raster_consulta_vista.php');
	
	$.ajax({
		url:   './app_raster/app_raster_consulta_vista.php',
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
			
			//var _res = $.parseJSON(response);            
			_cn = request._data.cn;			
			
			consultasPHP_respuesta("exito",_cn,Array(),"exito");
			
			_img=document.createElement('img');
			_img.setAttribute('src','data:image/png;base64, '+btoa(response));
			document.querySelector('body').appendChild(_img);
			
		}
	});	
	*/

	
	
}


function eliminarContenidoRaster(_idraster,_modo,_id_tipo_banda){

	//_modo: completo: elimina los datos de todas las bandas de un raster
	//_modo: unabanda: elimina los datos de todas una banda de un raster
	
	if(!confirm('¿Eliminamos este contenido raster (todas las bandas)? (será eliminada la información de forma irrecuperable)')){return;}
		
	_parametros = {
		'codMarco':_CodMarco,
		'idraster':_idraster,
		'modo':_modo
	}
	
	_cn = consultasPHP_nueva('./app_raster/app_raster_borrar_tablas_geogec_raster.php');
	
	$.ajax({
		url:   './app_raster/app_raster_borrar_tablas_geogec_raster.php',
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
			
			
			delete _DataRaster.listado.coberturas[_res.data.cobertura.id];
			
			mostrarListadoRaster();
			
			document.querySelector('#cuadrovalores').setAttribute('formRaster','no');
			_src_muestra_raster.unset('url');
		}
	});		
}
	
/*
function procesarTodosCandidatosDocRaster(){
	
		_a=document.querySelector('#divCandidatosDocRaster #listado_candidatos [proceso="no"]');
		_a.setAttribute('proceso','procesando');
	
		_parametros = {
			'codMarco':_CodMarco,
			'idraster':'0',
			'iddoc':_a.getAttribute('iddoc')
		}
		
		_cn = consultasPHP_nueva('./app_raster/app_raster_proc_procesar_archivo.php');
		
		$.ajax({
			url:   './app_raster/app_raster_proc_procesar_archivo.php',
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
				
				_iddoc=_res.data.iddoc;
		
		
				_DataRaster.listado.coberturas[_res.data.cobertura.id]=_res.data.cobertura;
				
				mostrarListadoRaster();
				
				mostrarRaster(_idr);
				
				
				if(_res.avance!='final'){
					procesarDocARaster(_iddoc,_res.data.cobertura.id);
				}
				
				_a=document.querySelector('#divCandidatosDocRaster #listado_candidatos [iddoc="'+_iddoc+'"]');
				_a.setAttribute('proceso','localizada');
				
				
				document.querySelector('#cuadrovalores').setAttribute('divCandidatosDocRaster','no');
				
				
				_a=document.querySelector('#divCandidatosDocRaster #listado_candidatos [iddoc="'+_iddoc+'"]');
				_a.setAttribute('proceso','localizada');
				 procesarTodosCandidatosDocRaster();
			
			}
		});	

	
}
     */  
       
function cargarRaster(_idr){
	
	
	//DESARROLLAR CONSULTA
	
	mostrarRaster(_idr);
}
        
        
/*
function accionCargarNuevaCapaRaster(){
		
	_parametros = {
		'codMarco':_CodMarco,
		'idraster':'1'
	}
	* _cn = consultasPHP_nueva('./app_raster/app_raster_proc_procesar_archivo.php');
	$.ajax({
		url:   './app_raster/app_raster_proc_procesar_archivo.php',
		type:  'post',
		data: _parametros,
		error:  function (response){alert('error al consultar el servidor');},
		success:  function (response){
			var _res = $.parseJSON(response);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res!='exito'){
				alert('error al consultar la base de datos');
			}
		}
	});	
	
}
*/
