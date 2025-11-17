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


//funciones para consultar datos y mostrarlos
var _Tablas={};
var _TablasConf={};
var _SelecTabla='';//define si la consulta de nuevas tablas estará referido al elmento existente de una pabla en particular; 
var _SelecElemCod=null;//define el código invariable entre versiones de un elemento a consultar (alternativa a _SelElemId);
var _SelecElemId=null;//define el id de un elemento a consultar (alternativa a _SelElemCod);


function consultarPermisos(){
	var _IdMarco = getParameterByName('id');
	var _CodMarco = getParameterByName('cod');
	_parametros = {
		'codMarco':_CodMarco	
	}
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
			
            for(var _na in _res.acc){
                procesarAcc(_res.acc[_na]);
            }					
			if(_res.res!='exito'){return;}
        }
 	});	
}
consultarPermisos();


function actualizarPermisos(){
    //repite consultas y cargas en caso de actualizarse los permisos por acceso de usuario registrado
    consultarTablas();
}

function consultarTablas(){
    document.querySelector('#menutablas #lista').innerHTML='';
    consultarElemento();//limpia residuos de visualización de elementos;
    var _parametros = {
        'selecTabla':_SelecTabla,
        'selecElemCod':_SelecElemCod,
        'selecElemId':_SelecElemId		
    };
	_cn = consultasPHP_nueva('./consulta_tablas.php');
    $.ajax({
        url:   './consulta_tablas.php',
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
			
            //console.log(_res);
            _Tablas=_res.data.tablas;
            _TablasConf=_res.data.tablasConf;
            _cont=document.querySelector('#menutablas #lista');
            
        }
    });
}
consultarTablas();

var _Linkeables={};

function consultarCapasLinkeables(){
    var _parametros = {
        'codMarco':_CodMarco
    };
    
    document.querySelector('#formlinkcapa #lista').innerHTML='';
	document.querySelector('#formlinkcapa').style.display='none';
	_cn = consultasPHP_nueva('./app_capa/app_capa_consultar_listado_linkeable.php');
    $.ajax({
        url:   './app_capa/app_capa_consultar_listado_linkeable.php',
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
			
            _Linkeables=_res.data.linkeables;
            
            for(_n in _Linkeables){
				_d=_Linkeables[_n];
				_op=document.createElement('a');
				_op.innerHTML=_d.nombre;
				_op.setAttribute('onclick','elijeCapaLink("'+_n+'")');
				_op.title=_d.descripcion;				
				document.querySelector('#formlinkcapa #lista').appendChild(_op);
			}
			document.querySelector('#formlinkcapa').style.display='block';
		}
	})
}

function consultarCamposExternosLinkeables(){	

    var _parametros = {
        'codMarco':_CodMarco
    };
    
    _va_li_ca=document.querySelector('#vinculaciones input[name="link_capa"]').value;
    if(_va_li_ca==''){
		alert('para elegir un campo de vinculación en la capa destino antes debe elegir cual seá la capa destino');
		return;
	}
      
    
    document.querySelector('#formlinkcampoexterno #lista').innerHTML='';
	document.querySelector('#formlinkcampoexterno').style.display='none';
	_cn = consultasPHP_nueva('./app_capa/app_capa_consultar_listado_linkeable.php');
    $.ajax({
        url:   './app_capa/app_capa_consultar_listado_linkeable.php',
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
            
            _Linkeables=_res.data.linkeables;
            
            _va_li_ca=document.querySelector('#vinculaciones input[name="link_capa"]').value;
            
            for(_n in _Linkeables[_va_li_ca]){
				_d=_Linkeables[_va_li_ca][_n];
				
				if(_n.substring(0,8)!='nom_col_'){continue;}
				
				_op=document.createElement('a');
				_op.innerHTML=_d;
				_op.setAttribute('onclick','elijeCampoExternoLink("'+_n+'")');
				document.querySelector('#formlinkcampoexterno #lista').appendChild(_op);
				
			}
			
			document.querySelector('#formlinkcampoexterno').style.display='block';
		}
	})			
}

function mostrarCamposLocalesLinkeables(){	

    document.querySelector('#formlinkcampolocal #lista').innerHTML='';
	document.querySelector('#formlinkcampolocal').style.display='none';
	
            
	for(_n in _Capa){
		
		_d=_Capa[_n];
		
		if(_n.substring(0,8)!='nom_col_'){continue;}
		
		_op=document.createElement('a');
		_op.innerHTML=_d;
		_op.setAttribute('onclick','elijeCampoLocalLink("'+_n+'")');
		
		document.querySelector('#formlinkcampolocal #lista').appendChild(_op);	
	}
	document.querySelector('#formlinkcampolocal').style.display='block';
}

					
function cargarAtabla(_this){
    limpiarfomularioversion();
    //document.getElementById('divCargaCapa').style.display='block';
    document.getElementById('divCargaCapa').setAttribute('tabla',_this.getAttribute('tabla'));
}

function mostrartabla(_this){	
    _lyrElemSrc.clear();
    //document.querySelector('#titulomapa').style.display='block';
    document.querySelector('#menuelementos').style.display='block';
    _tabla=_this.getAttribute('tabla');
    consultarElemento();//limpia datos ya consultados de elementos puntuales dentro de una tabla;
    document.querySelector('#titulomapa #tnombre').innerHTML=_tabla;
    if(_TablasConf[_tabla]!='undefined'){
        document.querySelector('#titulomapa #tnombre_humano').innerHTML=_TablasConf[_tabla].nombre_humano;
        document.querySelector('#titulomapa #tdescripcion').innerHTML=_TablasConf[_tabla].resumen;
    }

    _ExtraBaseWmsSource= new ol.source.TileWMS({
        url: 'http://190.111.246.33:8080/geoserver/geoGEC/wms',
        params: {
            'VERSION': '1.1.1',
            tiled: true,
            LAYERS: _tabla,
            STYLES: ''
        }
    });
    La_ExtraBaseWms.setSource(_ExtraBaseWmsSource);
    consultarCentroides(_this);
}

	
function consultarCentroides(_this){
    _parametros={
        'tabla': _this.getAttribute('tabla')
    };

	_cn = consultasPHP_nueva('./consulta_centroides.php');
    $.ajax({
        data: _parametros,
        url:   './consulta_centroides.php',
        type:  'post',        
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
		
			_lyrCentSrc.clear();
			_cont=document.querySelector('#menuelementos #lista');
			_cont.innerHTML='';
			for(var _no in _res.data.centroidesOrden){
				_nc=_res.data.centroidesOrden[_no];
				_hayaux='no';						
				_dat=_res.data.centroides[_nc];					
				var format = new ol.format.WKT();				
				var _feat = format.readFeature(_dat.geo, {
					dataProjection: 'EPSG:3857',
					featureProjection: 'EPSG:3857'
				});
				_feat.setId(_dat.id);
				_feat.setProperties({
					'nom':_dat.nom,
					'cod':_dat.cod,
					'id':_dat.id
				});

				_lyrCentSrc.addFeature(_feat);						
				_lyrCent.setSource(_lyrCentSrc);

				_MapaCargado='si';

				_aaa=document.createElement('a');
				_aaa.setAttribute('centid',_dat.id);
				_aaa.setAttribute('onmouseover','resaltarcentroide(this)');
				_aaa.setAttribute('onmouseout','desaltarcentroide(this)');
				_aaa.setAttribute('cod',_dat.cod);
				_aaa.innerHTML='<span class="nom">'+_dat.nom+"</span>"+'<span class="cod">'+_dat.cod+"</cod>";
				_aaa.setAttribute('onclick','consultarElemento("0","'+_dat.cod+'","'+_res.data.tabla+'")');
				_cont.appendChild(_aaa);
			}
			_ext= _lyrCentSrc.getExtent();
			mapa.getView().fit(_ext, { duration: 1000 });

			if(_Cod != ''){		
				consultarElemento("0",_Cod,_Est);
			}
	   
        }
    });		
}


function eliminarRegistrosCapa(){
	if(!confirm('¿Borramos TODOS los registros de esta capa?... ¿Segure?')){return;}
	
	var _parametros = {		
        'codMarco':_CodMarco,
        'idcapa': document.querySelector('#divCargaCapa').getAttribute('idcapa'),
    };	
    	_cn = consultasPHP_nueva('./app_capa/app_capa_eliminar_registro.php');
    $.ajax({
        data: _parametros,
        url:   './app_capa/app_capa_eliminar_registros_todos.php',
        type:  'post',
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
	
			
			limpiarFormularioSeleccionCapa();
			limpiarFormularioCapa();
			document.querySelector('#cuadrovalores').setAttribute('modo','muestracapaexistente');   
			cargarDatosCapaPublicada(_idCapa);
			
		}
	});
}

function guardarCapa(_this,_procesar){

    //console.log(document.querySelector('#divCargaCapa'));
    //console.log(document.querySelector('#divCargaCapa').getAttribute('idcapa'));
    
    
    if(document.querySelector('#divCargaCapa #verproccampo')!=null){
		_instrucciones=document.querySelector('#divCargaCapa #verproccampo').innerHTML;
	}else{
		_instrucciones=''
	}
     _parametros = {
        'instrucciones': document.querySelector('#divCargaCapa #verproccampo').innerHTML, 
        'fi_prj': document.querySelector('#divCargaCapa select#crs').value,
        'id': document.querySelector('#divCargaCapa').getAttribute('idcapa'),
        'codMarco': _CodMarco,
        'procesar':_procesar
    };
    
    var _this=_this;
	_cn = consultasPHP_nueva('./app_capa/app_capa_editar_shapefile.php');
    $.ajax({
        url:  './app_capa/app_capa_editar_shapefile.php',
        type: 'post',
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

			if(_res.data.procesar=='si')
            {
                procesarCapa2(_this.parentNode,0);
                return;
            }
            formversion(_this.parentNode);
        }
    });
    
    editarNombreCapa();
    
    editarDescripcionCapa();
    editarCamposCapa();
    editarCamposNombresCapa();
    editarTipoGeomCapa();
    editarModoWMSCapa();
    editarCaracteristicasCapa();
    guardarSLD();
    
}



function editarCapaNombre(_event,_this){
    //console.log(_event.keyCode);
    if(_event.keyCode==9){return;}//tab
    if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
    if(_event.keyCode==13){
        editarNombreCapa();
    }
}

function editarCapaDescripcion(_event,_this){
    //console.log(_event.keyCode);
    if(_event.keyCode==9){return;}//tab
    if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
    if(_event.keyCode==13){
        editarDescripcionCapa();
    }
}

function editarNombreCapa(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
    var nuevoNombre = document.getElementById('capaNombre').value;
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idCapa,
        'nombre': nuevoNombre
    };
    
    editarCapa(parametros);
}


function editarTipoGeomCapa(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
    
    _val='';
    _ops=document.querySelectorAll('#carga [name="tipogeometria"] option');
    for(_opn in _ops){
    	if(typeof _ops[_opn] != 'object'){continue;}
    	if(_ops[_opn].selected==true){
    		_val=_ops[_opn].value;
    		break;		
    	}
    }
        
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idCapa,
        'tipogeometria': _val
    };
    
    editarCapa(parametros);
}


function editarModoWMSCapa(){
	
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');

    _val=document.querySelector('#carga [name="modo_defecto"]').value;
    
    if(_val=='wms'){
    	_wms_layer='geoGEC:v_capas_registros_capa_'+idCapa;
    }else{
    	_wms_layer='';
    }
    //var _wms_layer = 'v_capas_registros_capa_'+idCapa;
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idCapa,
        'wms_layer': _wms_layer
    };
	_cn = consultasPHP_nueva('./app_capa/app_capa_publicar_wms.php');
    $.ajax({
	   url:   './app_capa/app_capa_publicar_wms.php',
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

			//Hacer algo luego de editar?
			//alert('wmseditado');            
        }
    })
}



function editarCaracteristicasCapa(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');

    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idCapa,
        'modo_defecto': document.querySelector('#carga [name="modo_defecto"]').value,
        'modo_publica': document.querySelector('#carga [name="modo_publica"]').value,
        'tipo_fuente': document.querySelector('#carga [name="tipo_fuente"]').value,
        'link_capa': document.querySelector('#carga [name="link_capa"]').value,
        'link_capa_campo_local': document.querySelector('#carga [name="link_capa_campo_local"]').value,
        'link_capa_campo_externo': document.querySelector('#carga [name="link_capa_campo_externo"]').value,
        'fecha_ano': document.querySelector('#carga [name="fecha_ano"]').value,
        'fecha_mes': document.querySelector('#carga [name="fecha_mes"]').value,
        'fecha_dia': document.querySelector('#carga [name="fecha_dia"]').value
        
    };
    	_cn = consultasPHP_nueva('./app_capa/app_capa_editar.php');
    $.ajax({
		url:   './app_capa/app_capa_editar.php',
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
			
                //Hacer algo luego de editar?
                //accionCargarCapaPublicada('',_res.data.id);
    			//alert('wmseditado');            
        }
    })
}


function editarDescripcionCapa(idCapa, descripcion){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
    var nuevaDescripcion = document.getElementById('capaDescripcion').value;
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idCapa,
        'descripcion': nuevaDescripcion
    };

    editarCapa(parametros);
}

function editarCamposCapa(idCapa, descripcion){
    var idMarco = getParameterByName('id');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
    var nuevaDescripcion = document.getElementById('capaDescripcion').value;
    
    _inps=document.querySelectorAll('#camposident #renombrar');
    
    var parametros = {
        'codMarco': _CodMarco,
        'idMarco': idMarco,
        'id': idCapa,
    };
    
    _cant=0;
    for(_ni in _inps){
    	if(typeof _inps[_ni] != 'object'){continue;}
		_nom=_inps[_ni].getAttribute('nom');
		_nom=_nom.replace('texto','text');
		_nom=_nom.replace('numero','num');
		parametros['nom_col_'+_nom]=_inps[_ni].value;
		_cant++;
    }
	if(_cant>0){
    	editarCapa(parametros);
    }
}


function editarCamposNombresCapa(){
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
    var parametros = {
        'codMarco': _CodMarco,
        'idMarco':  getParameterByName('id'),
        'id': idCapa,
    };
    _inps=document.querySelectorAll('#configurarCampos input[editado="si"]');
    
    _cant=0;
    for(_ni in _inps){
    	if(typeof _inps[_ni] != 'object'){continue;}
		_nom=_inps[_ni].getAttribute('name');
		parametros[_nom]=_inps[_ni].value;
		_cant++;
    }
    if(_cant>0){
		editarCapa(parametros);
    }
}

function editarCapa(parametros){
	_cn = consultasPHP_nueva('./app_capa/app_capa_editar.php');
    $.ajax({
            url:   './app_capa/app_capa_editar.php',
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
                //Hacer algo luego de editar?
            }
    });
}

function publicarCapaQuery(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');

    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': document.getElementById('divCargaCapa').getAttribute('idcapa')
    };
	_cn = consultasPHP_nueva('./app_capa/app_capa_publicar.php');
    $.ajax({
		url:   './app_capa/app_capa_publicar.php',
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
			
        }
    });
}

function publicarCapa(_this){
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
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
			
		
			publicarCapaQuery();

			accionCancelarCargarNuevaCapa(_this);
			alert("Capa publicada");
        
        }
    });
}

function guardarSLD(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    _idsimbologia = document.querySelector('#simbologia #simbologia_carga').value;
    
    var colorRelleno = document.querySelector('#simbologia > #inputcolorrelleno').value;
    var transparenciaRelleno = document.querySelector('#simbologia > #inputtransparenciarellenoNumber').value;
    var colorTrazo = document.querySelector('#simbologia > #inputcolortrazo').value;
    var anchoTrazo = document.querySelector('#simbologia > #inputanchotrazoNumber').value;
    var capaNombre = document.getElementById('capaNombre').value;
    var layerName = capaNombre;
    var styleTitle = '';
    var ruleTitle = '';
    var _campo_et = document.querySelector('#simbologia [name="campo_et"]').value;
    
    //Convertir la transparencia de porcentaje a numero decimal 
    transparenciaRelleno = transparenciaRelleno * 1.0 /100;
    var opacidadRelleno = Math.round((1 - transparenciaRelleno)*100)/100;
    
    
    
    _rules=document.querySelectorAll('#simbologia div[name="rule"]');
    
    if(Object.keys(_rules).length==0){

    var sld = `<?xml version="1.0" encoding="ISO-8859-1"?>
<StyledLayerDescriptor version="1.0.0"
  xsi:schemaLocation="http://www.opengis.net/sld http://schemas.opengis.net/sld/1.0.0/StyledLayerDescriptor.xsd"
  xmlns="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc"
  xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

  <NamedLayer>
	<Name>`+layerName+`</Name>
	<UserStyle>
  	<Title>`+styleTitle+`</Title>
  	<FeatureTypeStyle>
    	<Rule>
      	<Title></Title>
      	<PolygonSymbolizer>
        	<Fill>
          	<CssParameter name="fill">`+colorRelleno+`</CssParameter>
          	<CssParameter name="fill-opacity">`+opacidadRelleno+`</CssParameter>
        	</Fill>
        	<Stroke>
          	<CssParameter name="stroke">`+colorTrazo+`</CssParameter>
          	<CssParameter name="stroke-width">`+anchoTrazo+`</CssParameter>
        	</Stroke>
      	</PolygonSymbolizer>
    	</Rule>
  	</FeatureTypeStyle>
	</UserStyle>
  </NamedLayer>
</StyledLayerDescriptor>`;
    }else{
    	
     var sld = `<?xml version="1.0" encoding="ISO-8859-1"?>
<StyledLayerDescriptor version="1.0.0"
  xsi:schemaLocation="http://www.opengis.net/sld http://schemas.opengis.net/sld/1.0.0/StyledLayerDescriptor.xsd"
  xmlns="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc"
  xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

  <NamedLayer>
	<Name>`+layerName+`</Name>
	<UserStyle>
  	<Title>`+styleTitle+`</Title>
  	<FeatureTypeStyle>  	
		<TextSymbolizer>
		 <Label>
			<ogc:PropertyName>`+_campo_et+`</ogc:PropertyName>
		 </Label>
		</TextSymbolizer>
  	`;
  	
  	
  	for(_nr in _rules){
		if(typeof _rules[_nr] != 'object'){continue;}
		
		
		sld += `
				<Rule>
				 <Name>`+_rules[_nr].querySelector('#etiqueta').value+`</Name>
				 <Title></Title>`;
		
		
		console.log(_rules[_nr].getAttribute('tipocampo'));
		console.log(_rules[_nr]);
		if(_rules[_nr].getAttribute('tipocampo')=='numero'){
			console.log('aca');
			sld += `
				 <ogc:Filter>
				   <ogc:And>
					 <ogc:PropertyIsGreaterThanOrEqualTo>
					   <ogc:PropertyName>`+_rules[_nr].querySelector('#campo').value+`</ogc:PropertyName>
					   <ogc:Literal>`+_rules[_nr].querySelector('#desde').value+`</ogc:Literal>
					 </ogc:PropertyIsGreaterThanOrEqualTo>
					 <ogc:PropertyIsLessThan>
					   <ogc:PropertyName>`+_rules[_nr].querySelector('#campo').value+`</ogc:PropertyName>
					   <ogc:Literal>`+_rules[_nr].querySelector('#hasta').value+`</ogc:Literal>
					 </ogc:PropertyIsLessThan>
				   </ogc:And>
				 </ogc:Filter>
			 `;
		 }else if(_rules[_nr].getAttribute('tipocampo')=='texto'){
			 sld += `
				 <ogc:Filter>
				   <ogc:Or>
						<ogc:PropertyIsEqualTo>
							<ogc:PropertyName>`+_rules[_nr].querySelector('#campo').value+`</ogc:PropertyName>
							<ogc:Literal>`+_rules[_nr].querySelector('#iguala').value+`</ogc:Literal>
						</ogc:PropertyIsEqualTo>
				   </ogc:Or>
				 </ogc:Filter>`;
				
		 }
		 
		 sld += `
				<PolygonSymbolizer>
				   <Fill>
					 <CssParameter name="fill">`+_rules[_nr].querySelector('#inputcolorrelleno').value+`</CssParameter>
					 <CssParameter name="fill-opacity">`+(Math.round(_rules[_nr].querySelector('#inputtransparenciarellenoNumber').value*100)/100)/100+`</CssParameter>
				   </Fill>
				   <Stroke>
					<CssParameter name="stroke">` +_rules[_nr].querySelector('#inputcolortrazo').value+`</CssParameter>
					<CssParameter name="stroke-width">`+_rules[_nr].querySelector('#inputanchotrazoRange').value+`</CssParameter>
					</Stroke>
				 </PolygonSymbolizer>
			 </Rule>`;
			 
			 
		
  	}



		sld += ` 
	
		<Rule>
		   <Title>Otros valores</Title>
			<PolygonSymbolizer>
				<Fill>
				<CssParameter name="fill">`+colorRelleno+`</CssParameter>
				<CssParameter name="fill-opacity">`+opacidadRelleno+`</CssParameter>
				</Fill>
				<Stroke>
				<CssParameter name="stroke">`+colorTrazo+`</CssParameter>
				<CssParameter name="stroke-width">`+anchoTrazo+`</CssParameter>
				</Stroke>
			</PolygonSymbolizer>
		</Rule>`;  	
  	


		sld += ` 
		</FeatureTypeStyle>
		</UserStyle>
	  </NamedLayer>
	</StyledLayerDescriptor>`;

   	console.log(sld);
    }
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id_simbologia':_idsimbologia,
        'id_capa': document.getElementById('divCargaCapa').getAttribute('idcapa'),
        'sld': sld
    };

	_url='./app_capa/app_capa_editar_simbologia.php';
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
			
		}  
    });    
}

function cargarListadoCapasPublicadas(){
	
	limpiarMapa();
    var _this = _this;
    
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };
	_cn = consultasPHP_nueva('./app_capa/app_capa_consultar_listado.php');
    $.ajax({
		url:   './app_capa/app_capa_consultar_listado.php',
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
			
			cargarValoresCapasPublicadas(_res);
			mostrarListadoCapasPublicadas();
	   
		}
    });
}

/* DEPRECADO
function descargarSHP(_this,_ev){	
    _ev.stopPropagation();
    _if=document.createElement('iframe');
    _this.appendChild(_if);

    _if.style.display='none';
    _if.onload = function() { alert('myframe is loaded'); }; 

    _im=document.createElement('img');
    //_this.appendChild(_im);
    _im.src='./comun_img/cargando.gif';

    _if.src=_this.getAttribute('link');
}
*/





function consultarElemento(_idElem,_codElem,_tabla){
	/*
	return;
    document.querySelector('#menudatos #titulo').innerHTML='';
    document.querySelector('#menudatos #lista').innerHTML='';
    document.querySelector('#menudatos').removeAttribute('style');
    document.querySelector('#menuacciones #titulo').innerHTML='';
    document.querySelector('#menuacciones #lista').innerHTML='';
    document.querySelector('#menuacciones').removeAttribute('style');

    _elems = document.querySelectorAll('#menuelementos #lista a[cargado="si"]');
    if(_elems!=null){
		for(_nn in _elems){
				if(typeof _elems[_nn] != 'object'){continue;}
				_elems[_nn].removeAttribute('cargado');
		}
    }

    if(_codElem==null){return;}


    _parametros = {
            'id': _idElem,
            'cod': _codElem,
            'tabla':_tabla
    };

    $.ajax({
        data: _parametros,
        url:   './consulta_elemento.php',
        type:  'post',
        success:  function (response){
            var _res = $.parseJSON(response);
            console.log(_res);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='exito'){		
                _campocod=_res.data.tablasConf.campo_id_geo;
                _camponom=_res.data.tablasConf.campo_id_humano;

                document.querySelector('#menuacciones #titulo').innerHTML=_res.data.elemento.nombre;
                document.querySelector('#menuacciones #titulo').innerHTML="acciones disponibles";
                _lista=document.querySelector('#menuacciones #lista');

                for(_accnom in _res.data.tablasConf.acciones){
                    _accndata=_res.data.tablasConf.acciones[_accnom];

                    if(_res.data.elemento.accesoAccion[_accnom]>0){
                        document.querySelector('#menuacciones').style.display='block';
                        _li=document.createElement('a');
                        _li.setAttribute('href','./'+_accnom+'.php?cod='+_res.data.elemento[_campocod]);
                        _la=document.createElement('img');
                        _la.setAttribute('src','./comun_img/'+_accnom+'.png');
                        _la.setAttribute('alt',_accnom);
                        _la.setAttribute('title',_accndata.resumen);
                        _li.appendChild(_la);
                        _lista.appendChild(_li);
                    }
                }
                document.querySelector('#menudatos').style.display='block';

                document.querySelector('#menudatos #titulo').innerHTML=_res.data.elemento[_camponom];
                _lista=document.querySelector('#menudatos #lista');	
                for(var _nd in _res.data.elemento){
                    if(_nd == 'geo'){continue;}
                    if(_nd == 'accesoAccion'){continue;}
                    if(_nd == 'acceso'){continue;}
                    if(_nd == 'geotx'){continue;}
                    if(_nd == 'zz_obsoleto'){continue;}

                    _li=document.createElement('div');
                    _li.setAttribute('class','fila');
                    _la=document.createElement('label');
                    _la.setAttribute('class','variable');
                    _la.innerHTML=_nd+":";
                    _li.appendChild(_la);
                    _sp=document.createElement('div');
                    _sp.setAttribute('class','dato');
                    _sp.innerHTML=_res.data.elemento[_nd];
                    _li.appendChild(_sp);
                    _lista.appendChild(_li);
                }

                _lyrElemSrc.clear();
                var format = new ol.format.WKT();	
                var _feat = format.readFeature(_res.data.elemento.geotx, {
                    dataProjection: 'EPSG:3857',
                    featureProjection: 'EPSG:3857'
                });

                _feat.setId(_res.data.elemento.id);

                _feat.setProperties({
                    'nom':_res.data.elemento[_camponom],
                    'cod':_res.data.elemento[_campocod],
                    'id':_res.data.elemento.id
                });

                _lyrElemSrc.addFeature(_feat);

                _MapaCargado='si';


                document.querySelector('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').setAttribute('cargado','si');	

                _pe=$('#menuelementos #lista').offset().top;
                _sc=document.querySelector('#menuelementos #lista').scrollTop;
                console.log($('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').offset().top+_sc);

                $('#menuelementos #lista').animate({
                        scrollTop: ($('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').offset().top+_sc-_pe)
                 }, 2000);

                document.querySelector('#menudatos').style.display='block';

                _ext= _lyrElemSrc.getExtent();


                setTimeout(
                    function(){mapa.getView().fit(_ext, { duration: 1000 })},
                            200
                    );

                //generarItemsHTML();		
                //generarArchivosHTML();
            }else{
                alert('error dsfg');
            }
        }
    });	*/
}


function descargarSHP(_idcapa){
	_boton=document.querySelector('#listacapaspublicadas .filaCapaLista[idcapa="'+_idcapa+'"] .botondescarga');
	
	if(_boton.getAttribute('estado')=='generandoshp'){alert('ya estamos generando la descarga de esta capa... paciencia.');return;}
	_boton.setAttribute('estado','generandoshp');
	
    var _parametros = {
        'codMarco':_CodMarco,
        'idcapa': _idcapa
    };		
    _cn = consultasPHP_nueva('./app_capa/app_capa_generar_SHP_descarga.php');
	$.ajax({
        data: _parametros,
        url:   './app_capa/app_capa_generar_SHP_descarga.php',
        type:  'post',
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
			
			descargarSHPzip(_res.data.idcapa);
		}
	})	
}

function descargarSHPzip(_idcapa){
	
    var _parametros = {		
        'codMarco':_CodMarco,
        'idcapa': _idcapa
    };		
    _cn = consultasPHP_nueva('./app_capa/app_capa_generar_SHPzip_descarga.php');
	$.ajax({
        data: _parametros,
        url:   './app_capa/app_capa_generar_SHPzip_descarga.php',
        type:  'post',
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
						
			_boton=document.querySelector('#listacapaspublicadas .filaCapaLista[idcapa="'+_res.data.idcapa+'"] .botondescarga');
			_boton.setAttribute('estado','generandoshp');


			console.log('descarga:'+_res.data.descarga);
			var file_path = _res.data.descarga;
			var a = document.createElement('A');
			a.href = file_path;
			a.download = file_path.substr(file_path.lastIndexOf('/') + 1);
			a.download =_res.data.capa.nombre+'.zip';
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
		}
	})	
}

function accionMenuImportarCapaPublica(){
	limpiarSeleccionCapaImporta();
	document.querySelector('#form_importar_capa_pub').setAttribute('estado','cargando');
	consultarProvincias();
	
    var _parametros = {	
		'solopublicas':'si',
		'sinfiltro':'si'
    };		
	_cn = consultasPHP_nueva('./app_capa/app_capa_busca_datos.php');
	$.ajax({
        data: _parametros,
        url:   './app_capa/app_capa_busca_datos.php',
        type:  'post',
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
				
			_form=document.querySelector('#form_importar_capa_pub');
			_form.setAttribute('estado','activo');
			
			_form.querySelector('#lista_capas').innerHTML='';				
			for(_id_capa_p in _res.data.resultados){
				_datcapa_p=_res.data.resultados[_id_capa_p];
				
				_a=document.createElement('a');	
				_a.setAttribute('idcapa',_id_capa_p);
				_a.setAttribute('onclick','elegirImportarCapaPublica('+_id_capa_p+')');					
				_a.innerHTML='<span id="idcapa">'+_id_capa_p + '</span><span id="nombre">'+_datcapa_p.nombre + '</span><span id="descripcion">'+_datcapa_p.descripcion+'</span>';
				_form.querySelector('#lista_capas').appendChild(_a);
			}		
		}
	})		
}

function accionImportarCapaPublica(){
	document.querySelector('#form_importar_capa_pub').setAttribute('estado','cargando');
	_comp=document.querySelector('#form_importar_capa_pub').getAttribute('completo');
	if(_comp=='no'){
		alert('Aún no has seleccionado las especificaciones mínimas para que podemos copiar una capa de datos a tu proyecto. Por favor completá elpfrmulario');
		return;
	}
	var parametros = {
        'codMarco':_CodMarco,
        'idcapa': document.querySelector('#form_importar_capa_pub [name="id"]').value,
        'modo_recorte': document.querySelector('#form_importar_capa_pub #config_importar [name="recorte"]:checked').value,
        'link_dto': document.querySelector('#form_importar_capa_pub #config_importar [name="id_depto_recorte"]').value,
        'idcapa_recorte': document.querySelector('#form_importar_capa_pub #config_importar [name="nombre_capa_recorte"]').value,
        'id_reg_recorte': document.querySelector('#form_importar_capa_pub #config_importar [name="nombre_objeto_recorte"]').value
	};
		_cn = consultasPHP_nueva('./app_capa/app_capa_generar_duplicado.php');
	$.ajax({
		data:  parametros,
		url:   './app_capa/app_capa_generar_duplicado.php',
		type:  'post',
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


			document.querySelector('#form_importar_capa_pub').setAttribute('estado','inactivo');
			_idCapa=_res.data.nid;
			
			limpiarFormularioSeleccionCapa();
			limpiarFormularioCapa();
			document.querySelector('#cuadrovalores').setAttribute('modo','muestracapaexistente');   
			cargarDatosCapaPublicada(_idCapa);
		}
	});
}


	
function consultarProvincias(){

	var parametros = {
		
	};
		_cn = consultasPHP_nueva('./app_capa/app_capa_consultar_partidos_provincias.php');
	$.ajax({
		data:  parametros,
		url:   './app_capa/app_capa_consultar_partidos_provincias.php',
		type:  'post',
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
		
			_DataProvincias=_res.data;
			
			_sel=document.querySelector('#opciones_rec_dto [name="provincia"]');
			_sel.innerHTML='<option value="" selected="">Provincia</option>';
			for(_np in _DataProvincias.provinciasOrden){
				_idp=_DataProvincias.provinciasOrden[_np];
				_datp=_DataProvincias.provincias[_idp];
				
				_op=document.createElement('option');
				_op.value=_datp.link;
				_op.innerHTML=_datp.nombre;
				_sel.appendChild(_op);
			}
		}
	});		
}

function consultarEstadisticas(){
    var _parametros = {		
        'codMarco':_CodMarco,
        'idcapa': document.querySelector('#divCargaCapa').getAttribute('idcapa')
    };		
    _form=document.querySelector('#form_estadisticas');
	_form.setAttribute('estado','cargando');
		_cn = consultasPHP_nueva('./app_capa/app_capa_consultar_estadistica.php');
	$.ajax({
        data: _parametros,
        url:   './app_capa/app_capa_consultar_estadistica.php',
        type:  'post',
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
			_form=document.querySelector('#form_estadisticas');
			_form.setAttribute('estado','activo');
			
			
			_fila=_form.querySelector('#form_estadisticas #superficie');
			if(_res.data.estadisticas['sum_sup']!=null){
				_fila.setAttribute('estado','activo');
				_fila.querySelector('#nom').innerHTML='superficie (km<sup>2</sup>)';
				_form.querySelector('#form_estadisticas #uni_dens').innerHTML='(x / km<sup>2</sup>)';
				
				_val= new Intl.NumberFormat('de-DE').format(parseFloat(_res.data.estadisticas['sum_sup']));	
				_fila.querySelector('#sum').innerHTML=_val;
				
				_val= new Intl.NumberFormat('de-DE').format(parseFloat(_res.data.estadisticas['avg_sup']));	
				_fila.querySelector('#avg').innerHTML=_val
				_t='sum_sup';
			}else if(_res.data.estadisticas['sum_largo']!=null){
				_fila.setAttribute('estado','activo');
				_fila.querySelector('#nom').innerHTML='largo (km)';
				_form.querySelector('#form_estadisticas #uni_dens').innerHTML='(x / km)';
				_val= new Intl.NumberFormat('de-DE').format(parseFloat(_res.data.estadisticas['sum_larg']));	
				_fila.querySelector('#sum').innerHTML=_val;
									
				_val= new Intl.NumberFormat('de-DE').format(parseFloat(_res.data.estadisticas['avg_larg']));
				_fila.querySelector('#avg').innerHTML=_val;
				_t='sum_larg';
			}else{
				_fila.setAttribute('estado','inactivo');
				_form.querySelector('#form_estadisticas #uni_dens').innerHTML='';
				_t='pto';
			}
			
			for (let i = 1; i < 6; i++) {
				
				_fila=_form.querySelector('#form_estadisticas #numero'+i);
				if(_res.data['nom_col_num'+i]==''){
					_fila.setAttribute('estado','inactivo');
				}else{
					_fila.setAttribute('estado','activo');
				}
				_fila.querySelector('#nom').innerHTML=_res.data['nom_col_num'+i];
				
				_val= new Intl.NumberFormat('de-DE').format(parseFloat(_res.data.estadisticas['sum_numero'+i]));					
				_fila.querySelector('#sum').innerHTML=_val;
				
				_val= new Intl.NumberFormat('de-DE').format(parseFloat(_res.data.estadisticas['avg_numero'+i]).toFixed(2));	
				_fila.querySelector('#avg').innerHTML=_val;
				
				if(_t=='pto'){continue;}
				_fila.querySelector('#den').innerHTML=new Intl.NumberFormat('de-DE').format(parseFloat(_res.data.estadisticas['sum_numero'+i])/parseFloat(_res.data.estadisticas[_t]));
			}
		}
	})		
}

function borrarRegistro(_idreg){
	if(!confirm('¿Borramos este registro?... ¿Segure?')){return;}
	
	var _parametros = {		
        'codMarco':_CodMarco,
        'idcapa': document.querySelector('#divCargaCapa').getAttribute('idcapa'),
        'idreg':_idreg
    };	
    	_cn = consultasPHP_nueva('./app_capa/app_capa_eliminar_registro.php');
    $.ajax({
        data: _parametros,
        url:   './app_capa/app_capa_eliminar_registro.php',
        type:  'post',
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
	
			_tarjeta=document.querySelector('#portamapa .auxiliar[idreg="'+_res.data.idreg+'"]');
			_tarjeta.parentNode.removeChild(_tarjeta);
			_idCapa=document.querySelector('#divCargaCapa').getAttribute('idcapa');
			limpiarFormularioSeleccionCapa();
			limpiarFormularioCapa();
			document.querySelector('#cuadrovalores').setAttribute('modo','muestracapaexistente');   
			cargarDatosCapaPublicada(_idCapa);
			
		}
	});
}


function enviarCrearCampo(){
	
	_val=document.querySelector('#configurarCampos #form_NuevoCampo [name="tipo_campo"]').value;
	if(_val!='tex'&&_val!='num'){
		alert('error al seleccinar el tipo de campo a crear. Seleccione un tipo válido');
		return;
	}
	
	_num_campo=document.querySelector('#configurarCampos #form_NuevoCampo [name="tipo_campo"] option:checked').getAttribute('libre');
	
	var _parametros = {		
        'codMarco':_CodMarco,
        'idcapa': document.querySelector('#divCargaCapa').getAttribute('idcapa'),
        'tipo_campo':_val,
        'num_campo':_num_campo
    };
    	_cn = consultasPHP_nueva('./app_capa/app_capa_ed_habilita_campo.php');	
    $.ajax({
        data: _parametros,
        url:   './app_capa/app_capa_ed_habilita_campo.php',
        type:  'post',
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
			
			if(_res.data.tipo_campo=='tex'){
				_iddiv='campo_texto_';
			}else if(_res.data.tipo_campo=='num'){
				_iddiv='campo_numero_';
			}
			_iddiv+=_res.data.num_campo;
			
			_div=document.querySelector('#configurarCampos #'+_iddiv);
			_div.setAttribute('activo','si');
			_div.querySelector('input').value='- nuevo campo vacío -';
			
		}
	});
}


function crearSimbologia(){

	var _parametros = {		
        'codMarco':_CodMarco,
        'idcapa': _Capa.id
    };
    
    _url='./app_capa/app_capa_ed_crea_simbologia.php';
    _cn = consultasPHP_nueva(_url);	
    $.ajax({
        data: _parametros,
        url:   _url,
        type:  'post',
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
						
			_op=document.createElement('option');
			_op.value=_res.data.nid_capa;
			_op.innerHTML=_res.data.nombre;			
			_op.selected=true;
			
			document.querySelector('#simbologia_carga').appendChild(_op);
			document.querySelector('#reglas_adicionales').innerHTML='';
			simbolizarCapa();
		}
	});
}



function guaradarEditaReg(){
		
	_form=document.querySelector('#formulario_registro');
	_parametros={
        'codMarco':_CodMarco,
		'idr':_form.querySelector('[name="idr"]').value,
		'idcapa':_form.querySelector('[name="idcapa"]').value
	};
	_campos=_form.querySelectorAll('#campos input');
	for(_nc in _campos ){
		if(typeof _campos[_nc] != 'object'){continue};
		_parametros[_campos[_nc].getAttribute('name')]=_campos[_nc].value
	}
	  
	 _form.setAttribute('activo','no');
	  console.log(_parametros);
    _url='./app_capa/app_capa_editar_registro_alfa.php';
    _cn = consultasPHP_nueva(_url);	
    $.ajax({
        data: _parametros,
        url:   _url,
        type:  'post',
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
			
			simbolizarCapa();
		}
	});
}


function ejecutarCalculoCampoGeometria(){
	
	_form=document.querySelector('#form_calculoCampo');
	_parametros={
        'codMarco':_CodMarco,
		'campo':_form.querySelector('[name="campo"]').value,
		'modo':_form.querySelector('[name="modo"]').value,		
		'idgeom':'',
		'idcapa':_Capa.id
	};
	_campos=_form.querySelectorAll('#campos input');
	for(_nc in _campos ){
		if(typeof _campos[_nc] != 'object'){continue};
		_parametros[_campos[_nc].getAttribute('name')]=_campos[_nc].value
	}

	_form.setAttribute('activo','no');
	
    _url='./app_capa/app_capa_editar_calcular_superficie_registros.php';
    _cn = consultasPHP_nueva(_url);	
    $.ajax({
        data: _parametros,
        url:   _url,
        type:  'post',
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
			
			simbolizarCapa();
		}
	});
	
	
	
}
