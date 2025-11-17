/**

* index_mapa_funciones.php
* funciones de interacción del mapa genéerico
 * 
 *  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrolló sobre una publicación GNU 2017 TReCC SA
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
//funciones para el control del mapa



function cargarMapa(){
	
	document.getElementById('mapa').innerHTML='';
    document.getElementById('mapa').setAttribute('estado','activo');
    
   	_yStroke = new ol.style.Stroke({
		color : 'rgba(0,100,255,0.8)',
		width : 2,
	});
	_yFill = new ol.style.Fill({
	   color: 'rgba(0,100,255,0.6)'
	}); 
	var cRes = new ol.style.Circle({
	    radius: 5,
	    fill: _yFill,
	    stroke: _yStroke
	});
    var styleMapResalt = new ol.style.Style({
	     image:cRes
    });
    
    var styleDef = new ol.style.Style({
	     image:	new ol.style.Circle({
			    radius: 5,
			    fill: _yFill,
			    stroke: _yStroke
			})
    });
    
	_source = new ol.source.Vector({ 
		wrapX: false,   
    	projection: 'EPSG:3857' 
    }); 

	_sourceSeleccion = new ol.source.Vector({ 
		wrapX: false,   
    	projection: 'EPSG:3857' 
    }); 
    
    
    var styleArea = new ol.style.Style({
	    stroke: new ol.style.Stroke({color : 'rgba(255,50,100,1)', width : 2}),
	    fill: new ol.style.Fill({color : 'rgba(255,150,150,0.4)'})
    });
 
    var styleCandidato = new ol.style.Style({	    
	    image: new ol.style.Circle({ radius: 5,
		    stroke: new ol.style.Stroke({color : 'rgba(255,100,50,1)', width : 1}),
	    	fill: new ol.style.Fill({color : 'rgba(200,250,100,0.5)'}) 
		})
    });
    
    _CentStyle = new ol.style.Style({
         image: new ol.style.Circle({
		       fill: new ol.style.Fill({color: 'rgba(255,155,155,1)'}),
		       stroke: new ol.style.Stroke({color: '#ff3333',width: 0.5}),
		       radius: 3
		 }),
		 fill: new ol.style.Fill({color: 'rgba(255,155,155,1)'}),
		 stroke: new ol.style.Stroke({color: '#ff3333',width: 0.5})
     });
     
   	_CentSelStyle = new ol.style.Style({
         image: new ol.style.Circle({
		       fill: new ol.style.Fill({color: 'rgba(255,102,0,1)'}),
		       stroke: new ol.style.Stroke({color: '#ff3333',width: 0.8}),
		       radius: 6
		 }),
		 fill: new ol.style.Fill({color: 'rgba(255,102,0,1)'}),
		 stroke: new ol.style.Stroke({color: '#ff3333',width: 1.8}),
		 zIndex:100
     });
     
    _lyrElemStyle = new ol.style.Style({
        image: new ol.style.Circle({
		      fill: new ol.style.Fill({color: 'rgba(255,102,0,0.5)'}),
		      stroke: new ol.style.Stroke({color: '#ff3333',width: 0.8}),
		      radius: 6
		}),
		fill: new ol.style.Fill({color: 'rgba(228,25,55,0.5)'}),
		stroke: new ol.style.Stroke({color: 'rgba(228,25,55,0.8)',width: 2}),
		zIndex:1
    });


 	var _myStroke = new ol.style.Stroke({
		color : 'rgba(255,0,0,1.0)',
		width : 1,
	});
	var circle = new ol.style.Circle({
	    radius: 5,
	    stroke: _myStroke
	}); 
	var sy = new ol.style.Style ({
	   image:circle
	});
	var _sResalt = new ol.source.Vector({        
      projection: 'EPSG:3857'      
    });
    var _sCargado = new ol.source.Vector({        
      projection: 'EPSG:3857'      
    });
    var _sCandidato = new ol.source.Vector({        
      projection: 'EPSG:3857'      
    });	    	    
	var  _sArea = new ol.source.Vector({        
      projection: 'EPSG:3857'      
    });
    
	var _cargado='no';

	vectorLayer = new ol.layer.Vector({
		name: 'vectorLayer',
		style: styleArea,
		zIndex:100
	    //source: _source
	});

	seleccionLayer = new ol.layer.Vector({
		name: 'seleccionLayer',
		style: _CentStyle,
	    source: _sourceSeleccion,
	    zIndex:108
	});
	
	var marcoLayer = new ol.layer.Vector({
		style: new ol.style.Style({
			stroke: new ol.style.Stroke({color : 'rgba(200,50,50,1)', width : 1, lineDash: [2,3]}),
	    	fill: new ol.style.Fill({color : 'rgba(250,255,250,0)'})
		}),
		source: _sMarco,
		zIndex:10
	});

	
	var resaltadoLayer = new ol.layer.Vector({
		style: styleMapResalt,
		source: _sResalt,
		zIndex:105
	});

	var cargadoLayer = new ol.layer.Vector({
		style: styleMapResalt,
		source: _sCargado,
		zIndex:100
	});

	var candidatoLayer = new ol.layer.Vector({
		style: styleCandidato,
		source: _sCandidato,
		zIndex:110
	});	
		
	var areaLayer = new ol.layer.Vector({
		style: styleArea,
		source: _sArea,
		zIndex:102
	});
	
	_view =	new ol.View({
      projection: 'EPSG:3857',
      center: [-7000000,-4213000],
      zoom: 5,
      minZoom:2,
      maxZoom:19	      
	});

	var tablaRasLayer = new ol.layer.Image();

	_lyrCent.setStyle(_CentStyle);

	var style = new ol.style.Style ({
	  fill: new ol.style.Fill({
	    color: 'black',
	  }),
	});

	     
    _lyrElem.setStyle(_lyrElemStyle);
    La_ExtraBaseWms = new ol.layer.Tile({
        visible: true,
        source: _ExtraBaseWmsSource
    });
    
	mapa = new ol.Map({
	    layers: [
			seleccionLayer,
			vectorLayer,
			resaltadoLayer,
			candidatoLayer,
			cargadoLayer,
			areaLayer,
			tablaRasLayer,
			La_ExtraBaseWms,
			_lyrCent,
			_lyrElem,
			_lyrPropios,
			_layerRecorte,
			_layerLocat,
			marcoLayer
	    ],
	    target: 'mapa',
	    view: _view
	});
	
	
	if(document.querySelector('.ol-zoom.ol-unselectable.ol-control .ol-zoom-out')!=null){
		//corrije el encode del zoom out
    	document.querySelector('.ol-zoom.ol-unselectable.ol-control .ol-zoom-out').innerHTML='-';
    }
    
	vectorLayer.setSource(_source);	

	
	mapa.on('pointermove', function(_ev) {
		if (_ev.dragging) {
		  return;
		}
		var pixel = mapa.getEventPixel(_ev.originalEvent);

		sobrePunto(pixel,_ev);
	});

	mapa.on('click', function(_ev){    	
	  consultaPunto(_ev.pixel,_ev);       
	});
	
	
	function reiniciarMapa(){
		_features=_sCargado.getFeatures();	
		for (i = 0; i < _features.length; i++) {		
			_sCargado.removeFeature(_features[i]);
		}
		
		_features=_sCandidato.getFeatures();	
		for (i = 0; i < _features.length; i++) {		
			_sCandidato.removeFeature(_features[i]);
		}
		//mostrarArea(parent._Adat);	
	}

		
	function consultaPuntoAj(_Pid){
		console.log(_Pid);
		formAI(_Pid);		
	}
}


cargarMapa();
$("#mapa").css("height", $(window).height() - 150); this.mapa.updateSize();



function mostrarTablaEnMapa(_tabla){
	_ExtraBaseWmsSource= new ol.source.TileWMS({
        url: 'http://190.111.246.33:8080/geoserver/geoGEC/wms',
        params: {
	        'VERSION': '1.1.1',
	        tiled: true,
	        LAYERS: _tabla,
	        STYLES: '',
        }
   });
	La_ExtraBaseWms.setSource(_ExtraBaseWmsSource);
}

function cargarCapaMarco(){
	_sMarco.clear();
    //console.log(_source_ind.getFeatures());
	_haygeom='no';
	if(_DataMarco.geotx==''){return;}		
	
	//console.log('+ um geometria: campo'+_campo+'. valor:'+_val);
	//console.log(_DataMarco.geotx);
	if(_DataMarco.geotx!=null){
		console.log('carga marco');
		var _format = new ol.format.WKT();
		var _ft = _format.readFeature(_DataMarco.geotx, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857'
	    });
	    //_ft.setProperties(_geo);	    
	   	_sMarco.addFeature(_ft);
   	} 
}


function cargarElementoPropio(_codSel){
	_lyrPropiosSrc.clear();
    //console.log(_source_ind.getFeatures());
	_haygeom='no';
	if(_UsuarioA.permisos.marcos[_codSel].geotx==''){return;}		
	if(_UsuarioA.permisos.marcos[_codSel].geotx==null){return;}	
	console.log('carga elmento porpio');	
	var _format = new ol.format.WKT();
	var _ft = _format.readFeature(_UsuarioA.permisos.marcos[_codSel].geotx, {
        dataProjection: 'EPSG:3857',
        featureProjection: 'EPSG:3857',
        id:_codSel
    });
    console.log(_codSel);
    //_ft.setProperties(_geo);	    
   	_lyrPropiosSrc.addFeature(_ft);   	
}

function desCargarElementoPropio(_codSel){
	_lyrPropiosSrc.clear();
}



var sobrePunto = function(pixel) {
	
	if(_mapaEstado=='dibujando'){return;}
	
	limpiarCentroidesResaltados();
	limpiarFeaturesPublic();
	
	mapa.forEachFeatureAtPixel(pixel, function(_feat, _layer){
		

		if(_layer!=null){
			if(_layer.get('name')=='centroides'){
				resaltarDeCentroide(_feat);     
		    	
		    }else if(_layer.get('name')=='publicaciones'){
		    	resaltarFeaturePublic(_feat.getId());
		    }
		    
	    }else{
	      	
	    }
    });
     
   
	
}

function limpiarCentroidesResaltados(){
	if(_lyrCent.getSource()!=null){
    	_feats = _lyrCent.getSource().getFeatures();
    	for(_nn in _feats){  
    		_feats[_nn].setStyle(_CentStyle);   	
    		document.querySelector('#tseleccion').innerHTML='';
    		document.querySelector('#tseleccion').style.display='none';	    	
    		document.querySelector('#tseleccion').removeAttribute('cod');
    		document.querySelector('#listadodepartamentos a[centid="'+_feats[_nn].getId()+'"]').removeAttribute('estado');
		}
	}
}

function resaltarDeCentroide(_feat){
	limpiarCentroidesResaltados();
	_feat.setStyle(_CentSelStyle);
    _pp=_feat.getProperties('nom');
    document.querySelector('#titulomapa').style.display='block';
    document.querySelector('#tseleccion').setAttribute('cod',_pp.cod);
	document.querySelector('#tseleccion').innerHTML=_pp.nom;
	document.querySelector('#tseleccion').style.display='inline-block';
	document.querySelector('#listadodepartamentos a[centid="'+_feat.getId()+'"]').setAttribute('estado','selecto');
}

function resaltarCentroide(_this){
	limpiarCentroidesResaltados();
	_src = _lyrCent.getSource();
	_centid=_this.getAttribute('centid');
	_feat=_src.getFeatureById(_centid);
	_feat.setStyle(_CentSelStyle);
    _pp=_feat.getProperties('nom');
    document.querySelector('#tseleccion').setAttribute('cod',_pp.cod);
	document.querySelector('#tseleccion').innerHTML=_pp.nom;
	document.querySelector('#tseleccion').style.display='inline-block';
}

function desaltarCentroide(_this){	
	_src = _lyrCent.getSource();
	_centid=_this.getAttribute('centid');
	_feat=_src.getFeatureById(_centid);
	_feat.setStyle(_CentStyle);
    document.querySelector('#tseleccion').setAttribute('cod','');
	document.querySelector('#tseleccion').innerHTML='';
	document.querySelector('#tseleccion').style.display='none';
}

function consultaPunto(pixel,_ev){	
    if(_MapaCargado=='no'){console.log('el mapa no se cargó aun');return;}    
    var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
        if(layer.get('name')!=undefined){
        	feature['layer']=layer.get('name');	        	
          	return feature;	        
        }else{
        	console.log('sin elementos en ese punto del mapa');
        	return null;	     
        }
    });
    
    if(feature==null){
    	return;
	}else if(feature.layer=='seleccionLayer'||feature.layer=='centroides'){
		
		//console.log(feature);
		_cod=feature.get('cod');
		_tabla=feature.get('tabla');
		
		seleccionMunicipio(_cod);
		//consultarSeleccion('',_cod,_tabla);		
	}else{
		console.log('sin acciones definidas para esa capa');
	}
}

//definicion de fuente de datos de publicacion selecta
var _SrcPub = new ol.source.Vector();
var _StyPub = new ol.style.Style({
	stroke: new ol.style.Stroke({ 	color : 'rgba(50,200,90,1)', width : 1, lineDash: [2,3] }),
	fill: new ol.style.Fill({ 		color : 'rgba(50,200,90,0.5)'							})	
});
var _StyPubSel = new ol.style.Style({
	stroke: new ol.style.Stroke({ 	color : 'rgba(50,200,90,1)', width : 2 					}),
	fill: new ol.style.Fill({ 		color : 'rgba(50,200,90,0.8)'							})	
});
var _lyrPub = new ol.layer.Vector({name:'publicaciones',source: _SrcPub,style:_StyPub});   

mapa.addLayer(_lyrPub);


function cargarAreaPub(_idpubl){
	limpiarAreaPub()
	_areatx=_Data_Publ.publicaciones[_idpubl].areatx;
	if(_areatx==''||_areatx==null){return;}
	
	_format = new ol.format.WKT(_areatx);
	_ft = _format.readFeature(_areatx, {
        dataProjection: 'EPSG:3857',
        featureProjection: 'EPSG:3857'
    });
    _ft.setId(_idpubl);
    //_ft.setProperties(_geo);	    
   	_SrcPub.addFeature(_ft);
}


function resaltarFeaturePublic(_idpublic){
	limpiarFeaturesPublic();
	//console.log(_idpublic);
	document.querySelector('#listapublicaciones a[idpubl="'+_idpublic+'"]').setAttribute('estado','selecto');
	_feat2=null;
	for(_nn in _feats){  
		if(_feats[_nn].getId()==_idpublic){
			_feat2=_feats[_nn];
		}
	}	
	
	_feat=_SrcPub.getFeatureById(_idpublic);
	if(_feat2!=null){
		_feat2.setStyle(_StyPubSel);
	    _pp=_feat2.getProperties('nom');
    }	
	
    /*document.querySelector('#tseleccion').setAttribute('cod',_pp.cod);
	document.querySelector('#tseleccion').innerHTML=_pp.nom;
	document.querySelector('#tseleccion').style.display='inline-block';*/
}

function zoomPubl(_idpublic){
	for(_nn in _feats){  
		if(_feats[_nn].getId()==_idpublic){
			_feat2=_feats[_nn];
		}
	}	
	if(_feat2!=null){
		_view.fit(_feat2.getGeometry(), { duration: 1000 });
	}else{
		_format = new ol.format.WKT(_Data_Publ.extension);
		_ft = _format.readFeature(_Data_Publ.extension, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857'	     
	    });
	    _ft.getGeometry();
		_view.fit(_ft.getGeometry(), { duration: 1000 });
	}
}
    
function zoomGeneral(){   
	_format = new ol.format.WKT(_Data_Publ.extension);
	_ft = _format.readFeature(_Data_Publ.extension, {
	    dataProjection: 'EPSG:3857',
	    featureProjection: 'EPSG:3857'	     
	});
	_ft.getGeometry();
	_view.fit(_ft.getGeometry(), { duration: 1000 });
}

function desaltarFeaturePublic(_idpublic){	
	
	//console.log(_idpublic);
	
	for(_nn in _feats){  
		if(_feats[_nn].getId()==_idpublic){
			_feat2=_feats[_nn];
		}
	}	
	
	_feat=_SrcPub.getFeatureById(_idpublic);
	if(_feat2!=null){
		_feat2.setStyle(_StyPub);
	}
	document.querySelector('#listapublicaciones a[idpubl="'+_idpublic+'"]').removeAttribute('estado');
    /*document.querySelector('#tseleccion').setAttribute('cod','');
	document.querySelector('#tseleccion').innerHTML='';
	document.querySelector('#tseleccion').style.display='none';*/
}

function limpiarFeaturesPublic(){
	_feats = _SrcPub.getFeatures();
	for(_nn in _feats){  
		_feats[_nn].setStyle(_StyPub);
		//console.log(_feats[_nn].getId());   			
		/*document.querySelector('#tseleccion').innerHTML='';
		document.querySelector('#tseleccion').style.display='none';	    	
		document.querySelector('#tseleccion').removeAttribute('cod');*/
		document.querySelector('#listapublicaciones a[idpubl="'+_feats[_nn].getId()+'"]').removeAttribute('estado');
	}	
}

function cargarAreaPubTodas(){
	limpiarAreaPub();
	for(_idpubl in _Data_Publ.publicaciones){
		//console.log(_idpubl);
		
		_areatx=_Data_Publ.publicaciones[_idpubl].areatx;
		
		if(_areatx==''||_areatx==null){continue;}
		
		_format = new ol.format.WKT(_areatx);
		_ft = _format.readFeature(_areatx, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857'	     
	    });
	    _ft.setId(_idpubl);
	    //_ft.setProperties(_geo);	    
	    console.log(_areatx);
	    
   		_SrcPub.addFeature(_ft);
   		
   		_ls=mapa.getLayers();
   		console.log(_ls.getArray());
   		
   		_ls.forEach(function(layer) {
			//If this is actually a group, we need to create an inner loop to go through its individual layers
			console.log(layer.get('name'));
			//console.log(layer.getSource().getExtent());
			
		});
   		
	}
}

function limpiarAreaPub(){	
	_SrcPub.clear();
}


var _Interac={
	'dibujarPubl':{}
}
function dibujarAreaEnMapa(){
	console.log('inicia funcion'); 
	//console.log(_source_ind.getFeatures());
    _SrcPub.clear();	
		
	mapa.estado='nuevaPubl';
	
 	function addInteractionPolPubl() {
    	mapa.removeInteraction(_Interac.dibujarPubl);
    	//_mapas[_idm].mapa.removeInteraction(_mapas[_idm].draw);        
        _Interac.dibujarPubl = new ol.interaction.Draw({
            source: _SrcPub,
            type: 'Polygon'
        });        
        mapa.addInteraction(_Interac.dibujarPubl);   
	}
	
	_SrcPub.on('change', function(evt){		
		if(mapa.estado=='terminado'){mapa.estado='nuevodisp';return;}	
		if(mapa.estado=='error'){
			mapas.estado='terminado';
			_SrcPub.clear();
			return;
		}		
		
		_features=_SrcPub.getFeatures();
		_form = new ol.format.WKT();
		if(_features[0]!=undefined){
			_geometria=_form.writeGeometry(_features[0].getGeometry());
		}else{
			_geometria='';
		}
		document.querySelector('#publArea').value=_geometria;
		mapa.estado='terminado';
		
    });
    
	addInteractionPolPubl();	
	
}



function interrumpirDibujo(){
	mapa.removeInteraction(_Interac.dibujarPubl);
}

