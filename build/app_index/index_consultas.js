/**
*
* funciones js para ejecutar consultas desde index
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

//Esta funcion obtiene el QueryString en base a su nombre de la url, es case insensitive
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var results = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)", 'i').exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

//funciones para consultar datos y mostrarlos
var _Tablas={};
var _TablasConf={};
var _SelecTabla='';//define si la consulta de nuevas tablas estará referido al elmento existente de una pabla en particular; 
var _SelecElemCod=null;//define el código invariable entre versiones de un elemento a consultar (alternativa a _SelElemId);
var _SelecElemId=null;//define el id de un elemento a consultar (alternativa a _SelElemCod);

var _SelecGeom=null;//guarda la geometría del último obteto selecconado. Formato WKT(), 'EPSG:3857'.

function actualizarPermisos(){
	//repite consultas y cargas en caso de actualizarse los permisos por acceso de usuario registrado
	
	
	if(_UsuarioAcceso==null||_UsuarioA.id=='-1'){
		document.querySelector('div#cuadrovalores').setAttribute('acceso','no');		
		document.querySelector('div#cuadrovalores #menupropios #lista').innerHTML='';
	}else{
		document.querySelector('div#cuadrovalores').setAttribute('acceso','si');		
		_lista=document.querySelector('div#cuadrovalores #menupropios #lista');
		
		console.log(_UsuarioA);
		
		if(_lista !=null){
			// solo se usa en index.php
			
			if(_UsuarioA.permisos==undefined){
				_lista.innerHTML='- sin proyectos con acceso aun -';
			}else if(Object.keys(_UsuarioA.permisos.marcos).length < 1){
				_lista.innerHTML='- sin proyectos con acceso aun -';
			}else{
				_lista.innerHTML='';
				for(_mcod in _UsuarioA.permisos.marcos){
				
					_dat=_UsuarioA.permisos.marcos[_mcod];
					
					if(_dat.maxacc==0){continue;}
					
					_aaa=document.createElement('a');
					_aaa.setAttribute('cod',_mcod);
					_aaa.setAttribute('onclick',"consultarPropio(this.getAttribute('cod'))");
					_aaa.setAttribute('onmouseover',"cargarElementoPropio(this.getAttribute('cod'))");
					_aaa.setAttribute('onmouseout',"desCargarElementoPropio(this.getAttribute('cod'))");
					_lista.appendChild(_aaa);
					
					_spa=document.createElement('span');
					_spa.setAttribute('class','nom');
					_spa.innerHTML=_dat.nombre;
					_aaa.appendChild(_spa);
					
					_spa2=document.createElement('span');
					_spa2.setAttribute('class','cod');
					_spa2.innerHTML=_mcod;
					_aaa.appendChild(_spa2);
					
					
				}
			}
		
		consultarTablas(); // solo se ejecuta en index.php
			
		}
	}
	
	
	
	
	
}

function consultarTablas(){
	document.querySelector('#menutablas #lista').innerHTML='';
	consultarElemento();//limpia residuos de visualización de elementos;
        
    var _Est = getParameterByName('est');
        
	var _parametros = {
		'selecTabla':_SelecTabla,
		'selecElemCod':_SelecElemCod,
		'selecElemId':_SelecElemId		
	};

	
	if(controlConsultando()){alert('Antes tiene que reslverse una consulta en curso');return;}
	activarConsultando();
		
	$.ajax({
		url:   './comun_consultas/consulta_tablas.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			desactivarConsultando();
			var _res = $.parseJSON(response);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
				//console.log(_res);
				_Tablas=_res.data.tablas;
				_TablasConf=_res.data.tablasConf;
				_cont=document.querySelector('#menutablas #lista');
				for(_nn in _Tablas['est']){			
					
					_aaa=document.createElement('a');
					
					_aaa.innerHTML='';
					if(_TablasConf[_Tablas['est'][_nn]] != undefined){
						_aaa.innerHTML+=_TablasConf[_Tablas['est'][_nn]].nombre_humano;
					}else{
						_aaa.innerHTML+=' <span>('+_Tablas['est'][_nn]+')</span>';
					}
					_aaa.title=_Tablas['est'][_nn];
					_aaa.setAttribute('tabla',_Tablas['est'][_nn]);
					_aaa.setAttribute('class','nombretabla');
					_aaa.setAttribute('onclick','_Est="";_Cod="";mostrartabla(this)');
					_cont.appendChild(_aaa);
					
					
					if(_TablasConf[_Tablas['est'][_nn]].acceso>=3){
						//boton cargar version
						_aaa=document.createElement('a');
						_aaa.innerHTML='<img src="./comun_img/editar.png" alt="editar">';
						_aaa.title='subir una nueva versión';
						_aaa.setAttribute('tabla',_Tablas['est'][_nn]);
						_aaa.setAttribute('onclick','cargarAtabla(this)');
						_cont.appendChild(_aaa);
					}					
					
					if(_TablasConf[_Tablas['est'][_nn]].acceso>=3){
						//boton configurar
						_aaa=document.createElement('a');
						_aaa.innerHTML='<img src="./comun_img/configurar.png" alt="configurar">';
						_aaa.title='confiturar capa';
						_aaa.setAttribute('tabla',_Tablas['est'][_nn]);
						_aaa.setAttribute('onclick','configurartabla(this)');
						_cont.appendChild(_aaa);
					}
					
					_aaa=document.createElement('a');
					
					_standarSHP="ows?service=WFS&version=1.0.0&request=GetFeature&maxFeatures=1000000&outputFormat=SHAPE-ZIP";
					_capaSHP="&typeName=geogec:"+_Tablas['est'][_nn];
					
					_aaa.setAttribute('onclick','descargarSHP(this,event)');
					
					_host="http://<?php echo $IP_gs;?>:8080/geoserver/geoGEC/";
						
					_aaa.setAttribute('link',_host+_standarSHP+_capaSHP);
					_aaa.setAttribute('link',_host+_standarSHP+_capaSHP);//retiramos el recorte para la descarga
					
					
					_aaa.innerHTML='<img src="./comun_img/descargar.png" alt="descargar">';
					_aaa.setAttribute('tabla',_Tablas['est'][_nn]);
					_cont.appendChild(_aaa);
										
					_aaa=document.createElement('br');
					_cont.appendChild(_aaa);
					
				}
				
			if(_Est!=null && _Est!=''){
				console.log(_Est);
				//consultarElemento("0",_Cod,_Est);
				mostrartabla(document.querySelector('#lista > a.nombretabla[tabla="'+_Est+'"]'));
			}
		
				
		}
	});
}


					
function cargarAtabla(_this){
	limpiarfomularioversion();
	document.getElementById('formcargaverest').style.display='block';
	document.getElementById('formcargaverest').setAttribute('tabla',_this.getAttribute('tabla'));
}



function ocultarProyectosPropios(){
	document.querySelector('#menupropios').setAttribute('estado','cerrado');
	
}

function mostrarProyectosPropios(){
	document.querySelector('#menupropios').setAttribute('estado','abierto');
	document.querySelector('#menuelementos #lista').innerHTML='';
	limpiarListascarga();
}

function mostrartabla(_this){
	
	limpiarListascarga();
	ocultarProyectosPropios();
	document.querySelector('div#cuadrovalores').removeAttribute('cargado');
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
	
	if(_Est==''){
		mostrarTablaEnMapa(_tabla);	
	}
	consultarCentroides(_tabla);
}


	
function consultarCentroides(_tabla){
	
	if(controlConsultando()){alert('Antes tiene que reslverse una consulta en curso');return;}
	activarConsultando();
	
	_parametros={
		'tabla': _tabla
	};
	
	$.ajax({
		data: _parametros,
		url:   './comun_consultas/consulta_centroides.php',
		type:  'post',
		success:  function (response){
			desactivarConsultando();
			var _res = $.parseJSON(response);			
			console.log(_res);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='err'){
				
			}else{
				//cargaContrato();	
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
					    	'nom':_dat.nom,
					    	'cod':_dat.cod,
					    	'id':_dat.id,
					    });
				    
						_lyrCentSrc.addFeature(_feat);						
						_lyrCent.setSource(_lyrCentSrc);
						
						_MapaCargado='si';
						
						_aaa.setAttribute('onmouseover','resaltarcentroide(this)');
						_aaa.setAttribute('onmouseout','desaltarcentroide(this)');
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
					consultarElemento("0",_Cod,_Est);					
				}
				
			}
		}
	})			
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


function limpiarListascarga(){
	
	_SelecElemCod=null;
	
	_elems = document.querySelectorAll('#menupropios #lista > a[cargado="si"]');
	if(_elems!=null){
	for(_nn in _elems){
		if(typeof _elems[_nn] != 'object'){continue;}
		_elems[_nn].removeAttribute('cargado');
	}
	}
	
	
	_elems = document.querySelectorAll('#menuelementos #lista a[cargado="si"]');
	if(_elems!=null){
	for(_nn in _elems){
		if(typeof _elems[_nn] != 'object'){continue;}
		_elems[_nn].removeAttribute('cargado');
	}
	}
	
	
}




function consultarPropio(_codElem){

	
	document.querySelector('#menudatos #titulo').innerHTML='';
	document.querySelector('#menudatos #lista').innerHTML='';
	document.querySelector('#menudatos').removeAttribute('style');
	document.querySelector('#menuacciones #titulo').innerHTML='';
	document.querySelector('#menuacciones #lista').innerHTML='';
	document.querySelector('#menuacciones').removeAttribute('style');
	
	limpiarListascarga();
	
	_SelecElemCod=_codElem;
	_SelecTabla="est_02_marcoacademico";
	
	
	if(_codElem==null){return;}
	
	document.querySelector('#menupropios #lista > a[cod="'+_codElem+'"]').setAttribute('cargado','si');
	
	_parametros = {
		'id': '',
		'cod': _codElem,
		'tabla':_SelecTabla
	};
	
	if(controlConsultando()){alert('Antes tiene que reslverse una consulta en curso');return;}
	activarConsultando();
	
	$.ajax({
		data: _parametros,
		url:   './comun_consultas/consulta_elemento.php',
		type:  'post',
		success:  function (response){
			desactivarConsultando();
			var _res = $.parseJSON(response);
			console.log(_res);
			for(_nm in _res.mg){
				alert(_res.mg[_nm]);
			}
			if(_res.res!='exito'){	alert('error al consultar el servidor');return;}
			
			_DataElem=_res.data.elemento;
			_DataConf=_res.data.tablasConf;
			
			_campocod=_res.data.tablasConf.campo_id_geo;
			_camponom=_res.data.tablasConf.campo_id_humano;
			_campodesc=_res.data.tablasConf.campo_desc_humano;
							
			document.querySelector('#menuacciones #titulo').innerHTML=_res.data.elemento.nombre;
			document.querySelector('#menuacciones #titulo').innerHTML="Módulos disponibles";
			_lista=document.querySelector('#menuacciones #lista');	
			
			
			for(_accnom in _res.data.tablasConf.acciones){
				
				_accndata=_res.data.tablasConf.acciones[_accnom];	
				if(_res.data.elemento.accesoAccion[_accnom]>0){
					document.querySelector('#menuacciones').style.display='block';
					_li=document.createElement('a');
					_li.setAttribute('href','./'+_accnom+'.php?cod='+_res.data.elemento[_campocod]);
					_li.setAttribute('activa',_accndata.activo);
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
			
			if(_campodesc==null){
				_desc='';
			}else{
				_desc=_res.data.elemento[_campodesc];
			}
			document.querySelector('#menudatos #descripcion').innerHTML=_desc;
			
			
			_lista=document.querySelector('#menudatos #lista');	
			for(_nd in _res.data.elemento){
				if(_nd == 'geo'){continue;}
				if(_nd == 'accesoAccion'){continue;}
				if(_nd == 'acceso'){continue;}
				if(_nd == 'geotx'){continue;}
				if(_nd == 'zz_obsoleto'){continue;}
				if(_nd == 'zz_accesolibre'){continue;}
				
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
			
			_pe=$('#menupropios #lista').offset().top;
			_sc=document.querySelector('#menupropios #lista').scrollTop;
			console.log($('#menupropios #lista [cod="'+_res.data.elemento.codigo+'"]').offset().top+_sc);
			
			$('#menupropios #lista').animate({
			        scrollTop: ($('#menupropios #lista [cod="'+_res.data.elemento.codigo+'"]').offset().top+_sc-_pe)
			 }, 2000);
			    
			document.querySelector('#menudatos').style.display='block';
			
			_SelecGeom=_res.data.elemento.geotx;
			
			_lyrElemSrc.clear();
			if(_res.data.elemento.geotx!=null){
			
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
				
				_ext= _lyrElemSrc.getExtent();
			
				setTimeout(
					function(){mapa.getView().fit(_ext, { duration: 1000 })},
						200
				);
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
	
	
	limpiarListascarga();
	
	_SelecElemCod=_codElem;
	_SelecTabla=_tabla;
	
	if(_codElem==null){return;}
	
	document.querySelector('div#cuadrovalores').setAttribute('cargado','si');
	
	_parametros = {
		'id': _idElem,
		'cod': _codElem,
		'tabla':_tabla
	};
	
	if(controlConsultando()){alert('Antes tiene que reslverse una consulta en curso');return;}
	activarConsultando();
	
	$.ajax({
		data: _parametros,
		url:   './comun_consultas/consulta_elemento.php',
		type:  'post',
		success:  function (response){
			desactivarConsultando();
			var _res = $.parseJSON(response);
			console.log(_res);
			for(_nm in _res.mg){
				alert(_res.mg[_nm]);
			}
			if(_res.res!='exito'){	alert('error al consultar el servidor');return;}
			
			_DataElem=_res.data.elemento;
			_DataMarco=_res.data;
			_DataConf=_res.data.tablasConf;
			
			_campocod=_res.data.tablasConf.campo_id_geo;
			_camponom=_res.data.tablasConf.campo_id_humano;
			_campodesc=_res.data.tablasConf.campo_desc_humano;
							
			document.querySelector('#menuacciones #titulo').innerHTML=_res.data.elemento.nombre;
			document.querySelector('#menuacciones #titulo').innerHTML="Módulos disponibles";
			_lista=document.querySelector('#menuacciones #lista');	
			
			for(_accnom in _res.data.tablasConf.acciones){
				_accndata=_res.data.tablasConf.acciones[_accnom];	
				if(_res.data.elemento.accesoAccion[_accnom]>0){
					document.querySelector('#menuacciones').style.display='block';
					_li=document.createElement('a');
					_li.setAttribute('href','./'+_accnom+'.php?cod='+_res.data.elemento[_campocod]);
					_li.setAttribute('activa',_accndata.activo);
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
			
			if(_campodesc==null){
				_desc='';
			}else{
				_desc=_res.data.elemento[_campodesc];
			}
			document.querySelector('#menudatos #descripcion').innerHTML=_desc;
			
			_lista=document.querySelector('#menudatos #lista');	
			for(_nd in _res.data.elemento){
				if(_nd == 'geo'){continue;}
				if(_nd == 'accesoAccion'){continue;}
				if(_nd == 'acceso'){continue;}
				if(_nd == 'geotx'){continue;}
				if(_nd == 'zz_obsoleto'){continue;}
				if(_nd == 'zz_accesolibre'){continue;}
				
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
			
			document.querySelector('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').setAttribute('cargado','si');	
			
			_pe=$('#menuelementos #lista').offset().top;
			_sc=document.querySelector('#menuelementos #lista').scrollTop;
			console.log($('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').offset().top+_sc);
			
			$('#menuelementos #lista').animate({
			        scrollTop: ($('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').offset().top+_sc-_pe)
			 }, 2000);
			    
			document.querySelector('#menudatos').style.display='block';
			
			_SelecGeom=_res.data.elemento.geotx;
			
			_lyrElemSrc.clear();
			if(_res.data.elemento.geotx!=null){
			
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
				
				_ext= _lyrElemSrc.getExtent();
			
				setTimeout(
					function(){mapa.getView().fit(_ext, { duration: 1000 })},
					200
				);
			}	
		
			if(_Est!=''){
				_lyrCentSrc.clear();
			}
		}
	})	
}
