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


function consultarPermisos(){
    var _IdMarco = getParameterByName('id');
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco	
    };
    $.ajax({
        url:   './app_capa/app_capa_consultar_permisos.php',
        type:  'post',
        data: _parametros,
        error:  function (response){alert('error al consultar el servidor');},
        success:  function (response){
            var _res = $.parseJSON(response);
            for(var _nm in _res.mg)
            {
                alert(_res.mg[_nm]);
            }
            if(_res.res!='exito'){
                alert('error al consultar la base de datos');
            }
        }
    });	
}

function actualizarPermisos(){
    //repite consultas y cargas en caso de actualizarse los permisos por acceso de usuario registrado
    consultarTablas();
}



					
function cargarAtabla(_this){
    limpiarfomularioversion();
    document.getElementById('divCargaCapa').style.display='block';
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

	
function consultarCentroidesSeleccion(_tabla){
    _parametros={
        'tabla': _tabla
    };


    $.ajax({
        data: _parametros,
        url:   './consulta_centroides.php',
        type:  'post',
        success:  function (response){
            var _res = $.parseJSON(response);			
            console.log(_res);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='err'){
                
            } else {
            	
            	_lyrCentSrc.clear();
				_cont=document.querySelector('#menuelementos #lista');
				_cont.innerHTML='';
								
				for(_no in _res.data.centroidesOrden){					
					_nc=_res.data.centroidesOrden[_no];
					_hayaux='no';						
					_dat=_res.data.centroides[_nc];				
					
					_aaa=document.createElement('a');
					_aaa.setAttribute('centid',_dat.id);
					_aaa.setAttribute('cod',_dat.cod);
					_aaa.innerHTML='<span class="nom">'+_dat.nom+"</span>"+'<span class="cod">'+_dat.cod+"</cod>";
					_aaa.setAttribute('onclick','consultarElemento("0","'+_dat.cod+'","'+_res.data.tabla+'")');
					if(_dat.geo!=null){		
						var format = new ol.format.WKT();				
					    var _feat = format.readFeature(_dat.geo, {
					        dataProjection: 'EPSG:3857',
					        featureProjection: 'EPSG:3857'
					    });
					    _feat.setId(_dat.id);
					    _feat.setProperties({
					    	'tabla':_res.data.tabla,
					    	'nom':_dat.nom,
					    	'cod':_dat.cod,
					    	'id':_dat.id,
					    });
				    
						_sourceSeleccion.addFeature(_feat);		
						
						_MapaCargado='si';
						
						_aaa.setAttribute('onmouseover','resaltarcentroide(this)');
						_aaa.setAttribute('onmouseout','desaltarcentroide(this)');
					}else{
						_aaa.innerHTML+='<span class="alert" title="sin geometría">!</span>';
					}					
					_cont.appendChild(_aaa);									
				}
				
				if(_MapaCargado=='si'){					
					_ext= _sourceSeleccion.getExtent();
					mapa.getView().fit(_ext, { duration: 1000 });					
				}
				
				if(_Cod != ''){							
					consultarElemento("0",_Cod,_Est);					
				}				
            }
        }
    });		
}

function resaltarcentroide(_this){
    var _src = _lyrCent.getSource();
    _centid=_this.getAttribute('centid');
    _feat=_src.getFeatureById(_centid);

    _feat.setStyle(_CentSelStyle);
    _pp=_feat.getProperties('nom');
    document.querySelector('#tseleccion').setAttribute('cod',_pp.cod);
    document.querySelector('#tseleccion').innerHTML=_pp.nom;
    document.querySelector('#tseleccion').style.display='inline-block';
}

function desaltarcentroide(_this){	
    var _src = _lyrCent.getSource();
    _centid=_this.getAttribute('centid');
    _feat=_src.getFeatureById(_centid);

    _feat.setStyle(_CentStyle);
    document.querySelector('#tseleccion').setAttribute('cod','');
    document.querySelector('#tseleccion').innerHTML='';
    document.querySelector('#tseleccion').style.display='none';
}

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
                _sourceSeleccion.clear();
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

                _sourceSeleccion.addFeature(_feat);

                _MapaCargado='si';


                document.querySelector('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').setAttribute('cargado','si');	

                _pe=$('#menuelementos #lista').offset().top;
                _sc=document.querySelector('#menuelementos #lista').scrollTop;
                console.log($('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').offset().top+_sc);

                $('#menuelementos #lista').animate({
                        scrollTop: ($('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').offset().top+_sc-_pe)
                 }, 2000);

                document.querySelector('#menudatos').style.display='block';

                _ext= _sourceSeleccion.getExtent();


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
    });	
}

function consultarGeometria(_tabla,_codElem){

    _parametros = {
            'id': '',
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
                
				document.querySelector('#formEditarCandidato #marco_geotx').value=_res.data.elemento.geotx;
            }else{
                alert('error dsfasdg ');
            }
        }
    });	
}

function consultarSeleccion(_idElem,_codElem,_tabla){

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
            	
            	console.log(_res);
               for(_campo in _res.data.elemento){
               		_inp= document.querySelector('[campoSeleccion="'+_campo+'"]');
               		if(_inp!=undefined){
	               		if(confirm('¿Asignar el campo '+_campo+ ' al formulario en curso?')){
	               			_inp.value=_res.data.elemento[_campo];
	               		}               	
               		}
               }
            
            }else{
                alert('error dsfg');
            }
        }
    });	
}


function consultarDesarrollo(_codMarco){
	
    _parametros = {
            'codMarco': _codMarco
    };	
   $.ajax({
        data: _parametros,
        url:   './app_est/app_est_consultar.php',
        type:  'post',
        success:  function (response){
            var _res = $.parseJSON(response);
            console.log(_res);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='exito'){		
				
				document.querySelector('#cuadrovalores [name="nombre"]').value=_res.data.nombre;
				document.querySelector('#cuadrovalores [name="nombre_oficial"]').value=_res.data.nombre_oficial	;
				document.querySelector('#cuadrovalores [name="descripcion"]').value=_res.data.descripcion;
				document.querySelector('#cuadrovalores [name="codigo"]').value=_res.data.codigo;
				document.querySelector('#cuadrovalores [name="geotx"]').value=_res.data.geotx;
			
            }else{
                alert('error dsfg');
            }
        }
    });		
}

