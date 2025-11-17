/**

*
* aplicación para generar un mapa on line incorporando variable de la base de datos
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

var _MapaCargado='no';
var mapa={};
var vectorLayer={};
var _source={};
var _sCargado={};
var _AIsel='';
var _view={};
var _Dibujando='no';


var	_ExtraBaseWmsSource = new ol.source.TileWMS();//variable source utilizada por la capa extra base wms para mostar un url asignado dinámicamente.
var La_ExtraBaseWms = new ol.layer.Tile();

//definicion de variables para el layer de centroides
var _lyrCentSrc = new ol.source.Vector({});
var _lyrCent = new ol.layer.Vector({
	name:'centroides'
});   
var _CentSelStyle = new ol.style.Style();
var _CentStyle = new ol.style.Style();
//definicion de variables para el layer de elemento consultado
var _lyrElemSrc = new ol.source.Vector({});

var _lyrElemStyle = new ol.style.Style();
var _lyrElem = new ol.layer.Vector({
	name:'elemento consultado',
	source: _lyrElemSrc

});   
_lyrElem.setStyle(_CentSelStyle);


var _sMarco = new ol.source.Vector({        
  projection: 'EPSG:3857'      
}); 


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
    
    _sCargado = new ol.source.Vector({        
      projection: 'EPSG:3857'      
    }); 

    var _sCandidato = new ol.source.Vector({        
      projection: 'EPSG:3857'      
    }); 
    		    	    
	var  _sArea = new ol.source.Vector({        
      projection: 'EPSG:3857'      
    }); 
	
    var sobrePunto = function(pixel) {    
    	
		//if(_Dibujando=='si'){return;}	
    	
        var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
	        if(layer.get('name')=='centroides'){	        	
	          return feature;
	        }else{
	        	console.log('no');
	        }
        });
       
       if(_lyrCent.getSource()!=null){
       	
	        if(feature==undefined){
	        	
	        	_features = _lyrCent.getSource().getFeatures();
	        	for(_nn in _features){        		
	        		_features[_nn].setStyle(_CentStyle);        		
		    		document.querySelector('#tseleccion').innerHTML='';
		    		document.querySelector('#tseleccion').style.display='none';	    	
		    		document.querySelector('#tseleccion').removeAttribute('cod');
		    		
		    		document.querySelector('#menuelementos #lista a[centid="'+_features[_nn].getId()+'"]').removeAttribute('estado');
	    		}
	    		return;
	        }
	        
	        feature.setStyle(_CentSelStyle);
	        _pp=feature.getProperties('nom');
	        document.querySelector('#tseleccion').setAttribute('cod',_pp.cod);
			document.querySelector('#tseleccion').innerHTML=_pp.nom;
			document.querySelector('#tseleccion').style.display='inline-block';
			document.querySelector('#menuelementos #lista a[centid="'+feature.getId()+'"]').setAttribute('estado','selecto');
		}   
    }
    
 
	var _cargado='no';

		
	vectorLayer = new ol.layer.Vector({
		name: 'vectorLayer',
		style: styleArea
	    //source: _source
	});
	
	
	var resaltadoLayer = new ol.layer.Vector({
		style: styleMapResalt,
		source: _sResalt
	});

	var cargadoLayer = new ol.layer.Vector({
		style: styleMapResalt,
		source: _sCargado
	});

	var candidatoLayer = new ol.layer.Vector({
		style: styleCandidato,
		source: _sCandidato
	});	
	
		
	var areaLayer = new ol.layer.Vector({
		style: styleArea,
		source: _sArea
	});
	
	var marcoLayer = new ol.layer.Vector({
		style: new ol.style.Style({
			stroke: new ol.style.Stroke({color : 'rgba(200,50,50,1)', width : 1, lineDash: [2,3]}),
	    	fill: new ol.style.Fill({color : 'rgba(250,255,250,0)'})
		}),
		source: _sMarco
	});

	
	
	
	
	
	_view =	new ol.View({
      projection: 'EPSG:3857',
      center: [-7000000,-4213000],
      zoom: 5,
      minZoom:2,
      maxZoom:19	      
	});

	var tablaRasLayer = new ol.layer.Image();
	
	var _sourceBaseBING=new ol.source.BingMaps({
	 	key: 'CygH7Xqd2Fb2cPwxzhLe~qz3D2bzJlCViv4DxHJd7Iw~Am0HV9t9vbSPjMRR6ywsDPaGshDwwUSCno3tVELuob__1mx49l2QJRPbUBPfS8qN',
	 	imagerySet:  'Aerial'
	});
		
	
	
	var layerOSM = new ol.layer.Tile({
		  source: new ol.source.Stamen({layer: 'toner'})
	});

	
	var _sMascaraMalvinas = new ol.source.Vector({});	  
    _wkt='POLYGON((-7140000 -6400000 , -5600000 -6400000 , -5600000 -7000000 , -7140000 -7000000 , -7140000 -6400000 ))';
	var _format = new ol.format.WKT();
	var _ft = _format.readFeature(_wkt, {
        dataProjection: 'EPSG:3857',
        featureProjection: 'EPSG:3857'
    });	    
   	_sMascaraMalvinas.addFeature(_ft);
   	
	var _layerMascaraMalvinas= new ol.layer.Vector({
		style: null,		
		source: _sMascaraMalvinas
	});			
	
	_layerMascaraMalvinas.getSource().on('addfeature', function () {
	  layerOSM.setExtent(_layerMascaraMalvinas.getSource().getExtent());
	});  
	
	var style = new ol.style.Style ({
	  fill: new ol.style.Fill({color: 'black',}),
	});
	
	layerOSM.on('postrender', function (e) {
	  var vectorContext = ol.render.getVectorContext(e);
	  e.context.globalCompositeOperation = 'destination-out';
	  _layerMascaraMalvinas.getSource().forEachFeature(function (feature) {
	    vectorContext.drawFeature(feature, style);
	  });
	  e.context.globalCompositeOperation = 'source-over';
	});	
	
	var layerBing = new ol.layer.Tile({});	

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
        
	_lyrCent.setStyle(_CentStyle);
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
     
     _lyrElem.setStyle(_lyrElemStyle);
    La_ExtraBaseWms = new ol.layer.Tile({
        visible: true,
        source: _ExtraBaseWmsSource
    });
    
   	
    
	mapa = new ol.Map({
	    layers: [
			layerOSM,
			_layerMascaraMalvinas,
			layerBing,
			vectorLayer,
			resaltadoLayer,
			candidatoLayer,
			cargadoLayer,
			areaLayer,
			tablaRasLayer,
			La_ExtraBaseWms,
			_lyrCent,
			_lyrElem,
			marcoLayer
	    ],
	    target: 'mapa',
	    view: _view
	});
	 
	 //_xy=new ol.Coordinate(-6500000,-4100000);
	
	vectorLayer.setSource(_source);
	
	//layerOSM.setSource(_sourceBaseOSM);
	  
	_view.on('change:resolution', function(evt){       
        if(_view.getZoom()>=19){
       		layerBing.setSource(_sourceBaseBING);
       		layerBing.setOpacity(0.8);
       }else if(_view.getZoom()>=17){
       		layerBing.setSource(_sourceBaseBING);
       		layerBing.setOpacity(0.5);
       }else{
       		layerBing.setSource();
       }
    });	  
	  
	mapa.on('pointermove', function(evt) {
      consultaPunto(evt.pixel,evt);       
    });

	if(document.querySelector('.ol-zoom.ol-unselectable.ol-control .ol-zoom-out')!=null){
		//corrige el encode del zoom out
    	document.querySelector('.ol-zoom.ol-unselectable.ol-control .ol-zoom-out').innerHTML='-';
    }

	function consultaPunto(pixel,_ev){
		
	    if(_MapaCargado=='no'){return;}
		var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
	       if(layer.get('name')=='elemento consultado'){
	          return feature;
	       }
	    });
	     if(feature==undefined){return;}
	     	 
		_idreg=feature.getProperties().id;
		
		_datasec=_Features[_idreg];
		
		_aux=document.querySelector('#auxiliar');	
		if(_aux.getAttribute('mostrando')==_idreg){return;}
		
		_cont=_aux.querySelector('#cont');
		_cont.innerHTML='';
		for(_k in _datasec){
			
			if(_k=='geotx'){continue;}
			
			_kref=_k.replace('texto','nom_col_text');
			_kref=_kref.replace('numero','nom_col_num');
			if(_Capa[_kref] == null){continue;}
			_l=document.createElement('label');
			_l.innerHTML=_Capa[_kref];
			_cont.appendChild(_l);
			_s=document.createElement('span');
			_s.innerHTML=_datasec[_k];
			_cont.appendChild(_s);
			_b=document.createElement('br');
			_cont.appendChild(_b);		
		}
		_aux.setAttribute('idreg',_datasec.id);
		
	}
	
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


function limpiarMapa(){	
	_lyrElemSrc.clear();
	La_ExtraBaseWms.setSource(null);
	//mostrarArea(parent._Adat);	
}



function cargarWmsCapaExist(_res){
	_ExtraBaseWmsSource= new ol.source.TileWMS({
        url: 'http://190.111.246.33:8080/geoserver/geoGEC/wms',
        params: {
	        'VERSION': '1.1.1',
	        tiled: true,
	        LAYERS: _res.data.wms_layer,
	        STYLES: '',
        }
   });
   
	La_ExtraBaseWms.setSource(_ExtraBaseWmsSource);
	
	mapa.on('singleclick', function(evt) {
        document.getElementById('auxiliar').innerHTML = '';
        var viewResolution = /** @type {number} */ (_view.getResolution());
        var url = _ExtraBaseWmsSource.getGetFeatureInfoUrl(
          evt.coordinate, viewResolution, 'EPSG:3857',
          {'INFO_FORMAT': 'text/html'});
        if (url) {
          document.getElementById('auxiliar').innerHTML =
              '<iframe seamless src="' + url + '"></iframe>';
        }
      });
      
	//consultarCentroides(_this);
}


function cargarCapaMarco(){
	
	_sMarco.clear();
    //console.log(_source_ind.getFeatures());
	_haygeom='no';
	if(_DataMarco.geotx==''){return;}		
	
	//console.log('+ um geometria: campo'+_campo+'. valor:'+_val);
	var _format = new ol.format.WKT();
	var _ft = _format.readFeature(_DataMarco.geotx, {
        dataProjection: 'EPSG:3857',
        featureProjection: 'EPSG:3857'
    });
    //_ft.setProperties(_geo);	    
   	_sMarco.addFeature(_ft); 
}