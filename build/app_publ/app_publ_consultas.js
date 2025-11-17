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
		'codMarco':_CodMarco,	
		'accion':_Acc
	}

    _cn = consultasPHP_nueva('./sistema/sis_consulta_permisos.php'); 
	$.ajax({
        url:   './sistema/sis_consulta_permisos.php',
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

            for(_na in _res.acc){procesarAcc(_res.acc[_na]);}
        }
 	});	
}
consultarPermisos();


function actualizarPermisos(){
    //repite consultas y cargas en caso de actualizarse los permisos por acceso de usuario registrado
    consultarTablas();
}


function consultarPublicaciones(){
    document.querySelector('#listapublicaciones').innerHTML='';
    //consultarElemento();//limpia residuos de visualización de elementos;
    var _parametros = {
		'codMarco':_CodMarco
    };

    _cn = consultasPHP_nueva('./app_publ/app_publ_consultar_publicaciones.php');  
    $.ajax({
        url:   './app_publ/app_publ_consultar_publicaciones.php',
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
            
            _Data_Publ=_res.data;
            
            
            _cont=document.querySelector('#listapublicaciones');
            _cont.innerHTML='';
            
            for(var _np in _res.data.publicacionesOrden){
            	_idp=_res.data.publicacionesOrden[_np];
            	_dat=_res.data.publicaciones[_idp];
                _aaa=document.createElement('a');
                _aaa.setAttribute('idpubl',_idp);
                _aaa.innerHTML=_dat.titulo;
                _aaa.title=_dat.autoria;
                _aaa.setAttribute('onmouseover','resaltarFeaturePublic(this.getAttribute("idpubl"))');
                _aaa.setAttribute('onmouseout','desaltarFeaturePublic(this.getAttribute("idpubl"))');
                _aaa.setAttribute('onclick','mostrarPubl(this.getAttribute("idpubl"))');
                _cont.appendChild(_aaa);
            }
            
            cargarAreaPubTodas() // en ./app_publ/app_publ_mapa_funciones.js
        }
    });
}
consultarPublicaciones();


var _Tipos={};

function consultarTipos(){
    _cn = consultasPHP_nueva('./app_publ/app_publ_consultar_tipos.php'); 
	$.ajax({
        url:   './app_publ/app_publ_consultar_tipos.php',
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
		
            for(_na in _res.acc){procesarAcc(_res.acc[_na]);}

            _Tipos=_res.data.tipos;
            _TiposOrden=_res.data.tiposOrden;
        }
 	});	
}
consultarTipos();

function consultarCentroides(){
	
	_parametros={
		'tabla': 'est_01_municipios'
	};
	
    _cn = consultasPHP_nueva('./consulta_centroides.php');
	$.ajax({
		url:   './consulta_centroides.php',
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

				//cargaContrato();	
            _lyrCentSrc.clear();
            _cont=document.querySelector('#listadodepartamentos');
            _cont.innerHTML='';
                
                
            for(_no in _res.data.centroidesOrden){					
                _nc=_res.data.centroidesOrden[_no];
                _hayaux='no';						
                _dat=_res.data.centroides[_nc];				
                
                _aaa=document.createElement('a');
                _aaa.setAttribute('centid',_dat.id);
                _aaa.setAttribute('cod',_dat.cod);
                _aaa.innerHTML='<span class="nom">'+_dat.nom+"</span>"+'<span class="cod">'+_dat.cod+"</cod>";
                _aaa.setAttribute('onclick','seleccionMunicipio("'+_dat.cod+'")');
                if(_dat.geo!=null){		
                    var format = new ol.format.WKT();				
                    var _feat = format.readFeature(_dat.geo, {
                        dataProjection: 'EPSG:3857',
                        featureProjection: 'EPSG:3857'
                    });
                    _feat.setId(_dat.id);
                    _feat.setProperties({
                        'nom':_dat.nom,
                        'cod':_dat.cod,
                        'id':_dat.id,
                    });
                
                    _lyrCentSrc.addFeature(_feat);						
                    _lyrCent.setSource(_lyrCentSrc);
                    
                    _MapaCargado='si';
                    
                    _aaa.setAttribute('onmouseover','resaltarCentroide(this)');
                    _aaa.setAttribute('onmouseout','desaltarCentroide(this)');
                }else{
                    _aaa.innerHTML+='<span class="alert" title="sin geometría">!</span>';
                }
                
                _cont.appendChild(_aaa);
                                
            }
            
            if(_MapaCargado=='si'){
                
                _ext= _lyrCentSrc.getExtent();
                mapa.getView().fit(_ext, { duration: 1000 });
                
            }
            
            if(_Cod != ''){							
                consultarElemento("0",_Cod,"est_01_municipios");					
            }	
		}
	})			
}	

function consultarElemento(_idElem,_codElem,_tabla){
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

    _cn = consultasPHP_nueva('./consulta_elemento.php'); 
    $.ajax({
        url:   './consulta_elemento.php',
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
        }
    });	
}

function accionCreaGuardar(){
	
	_parametros={};
	_inps=document.querySelectorAll('#formEditarPublicaciones input, #formEditarPublicaciones textarea, #formEditarPublicaciones select');
	for(_in in _inps){
		if(typeof _inps[_in] != 'object'){continue;}
		if(_inps[_in].getAttribute('name')=='municipios[]'){continue;}
		_parametros[_inps[_in].getAttribute('name')]=_inps[_in].value;
	}
	_inps=document.querySelectorAll('#formEditarPublicaciones input[name="municipios[]"]');
	
	_muni={'0':'0'};
	for(_in in _inps){
		if(typeof _inps[_in] != 'object'){continue;}
		_muni[_inps[_in].value]=_inps[_in].value;
	}
	
	
	_parametros['municipios']=_muni;
	_parametros['codMarco']=_CodMarco;	
	_idp=document.querySelector('#formEditarPublicaciones').getAttribute('idPubl');
	_parametros['idPubl']=_idp;
	
	if(_parametros['id_p_ref_01']==''){
		alert('Error reconociendo el Archivo. Sin archivo, no hay publicación.\n Gracias.');
		return;
	}

    _cn = consultasPHP_nueva('./app_publ/app_publ_ed_guardar_publicacion.php'); 
	$.ajax({
        url:   './app_publ/app_publ_ed_guardar_publicacion.php',
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

            accionVolverAlListado();	
            consultarPublicaciones();            	
            //alert('ok');
            //generarItemsHTML();		
            //generarArchivosHTML();
        }
    });			
}

function accionEliminar(){
	
	if(!confirm('¡Eliminamos esta publicación?')){return;}
	_idp=document.querySelector('#formEditarPublicaciones').getAttribute('idPubl');
	_parametros={
		'idPubl':_idp,
		'codMarco':_CodMarco,	
		'accion':'eliminar'
	}

    _cn = consultasPHP_nueva('./app_publ/app_publ_ed_eliminar_publicacion.php'); 		
	$.ajax({
        data: _parametros,
        url:   './app_publ/app_publ_ed_eliminar_publicacion.php',
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

            accionVolverAlListado();	
            consultarPublicaciones();            	
            //alert('ok');
            //generarItemsHTML();		
            //generarArchivosHTML();
        }
    });		
}

