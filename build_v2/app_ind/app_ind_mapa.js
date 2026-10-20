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

	var _source = new ol.source.Vector({ 
		wrapX: false,   
    	projection: 'EPSG:3857' 
    }); 

	var _sourceSeleccion = new ol.source.Vector({ 
		wrapX: false,   
    	projection: 'EPSG:3857' 
    }); 
    
    

	//layer de geometría del indicador
	var _source_ind= new ol.source.Vector({
		wrapX: false,   
		projection: 'EPSG:3857' 
	});
	var _layer_ind= new ol.layer.Vector({
		name: 'indicador',
		source: _source_ind,
		style: ol.style.Style({}),
		zIndex:100    
	});
	
	//layer de superposicion a layer buffer
	var _source_ind_superp= new ol.source.Vector({
		wrapX: false,   
		projection: 'EPSG:3857'
	});
	var _layer_ind_superp= new ol.layer.Vector({
		name: 'buffer',
		source: _source_ind_superp,
		style: ol.style.Style({
			 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 0.5}),
			 fill: new ol.style.Fill({color: 'rgb(0,0,0)'}),
		}),
		zIndex:95
	});
	
	var _source_ind_sel= new ol.source.Vector({
		wrapX: false,   
		projection: 'EPSG:3857' 
	});

	//layer de geometría seleccionadad para cargar datos
	var _st_ind_sel=new ol.style.Style({
		 image: new ol.style.Circle({
			   stroke: new ol.style.Stroke({color:'rgb(8, 175, 217)',width: 0.8}),
			   radius: 6
		 }),
		 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 1}),
		 zIndex:200
	});
	var _layer_ind_sel= new ol.layer.Vector({
		name: 'indicador: elemento selecto',
		source: _source_ind_sel,
		style: _st_ind_sel,
		zIndex:95
	});
	
	//layer de area de influencia de la geometría
	var _source_ind_buffer= new ol.source.Vector({
		wrapX: false,   
		projection: 'EPSG:3857' 
	});
	var _layer_ind_buffer= new ol.layer.Vector({
		name: 'buffer',
		source: _source_ind_buffer,
		style: ol.style.Style({
			 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 0.5}),
			 fill: new ol.style.Fill({color: 'rgb(0,0,0)'})
		}),
		zIndex:95  
	});
	
	var _rmax = 228;
	var _gmax = 25;
	var _bmax = 55;

	var _rmin = 204;
	var _gmin = 255;
	var _bmin = 204;

	var _encuadrado='no';

	var _mapaEstado ='';
	
function cargarMapa(){

	_encuadrado='no';

	_mapaEstado ='';
		
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
		zIndex: 100
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
		zIndex:900
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

	function consultaPuntoAj(_Pid){
		console.log(_Pid);
		formAI(_Pid);
		
	}	

	mapa.addLayer(_layer_ind_superp);

	mapa.addLayer(_layer_ind);

	mapa.addLayer(_layer_ind_sel);

	mapa.addLayer(_layer_ind_buffer);
}

function limpiarContenidosMapa(){
	_source.clear();
	_sourceSeleccion.clear();
	_source_ind.clear();
	_source_ind_superp.clear();	
	_source_ind_sel.clear();
	_source_ind_buffer.clear();
	
	
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



function dibujarPoligonosMapa(_res){
    console.log('inicia funcion'); 
	console.log(_res.data.geom);
    _source_ind.clear();
    console.log(_source_ind.getFeatures());
    
	_campo=_res.data.indicador.representar_campo;
	
	_haygeom='no';
	for(_gn in _res.data.geom){		
		_geo=_res.data.geom[_gn];
		_val=null;
		
		
		if(_DataIndicador.funcionalidad=='nuevaGeometria'&&_geo.estadocarga!='listo'){
			//esta geometría no es para esteperíodo
			continue;	
		}
		_haygeom='si';
		
		for(_vn in _geo.valores){
			if(_geo.valores[_vn].zz_superado=='0'){
				_val=_geo.valores[_vn][_campo+'_dato'];				
			}
		}		
		var _format = new ol.format.WKT();
		
		if(_geo.geotx==null||_geo.geotx==''){
				//no tiene geometría válida, la salteamos.
				continue;
		}
		
		var _ft = _format.readFeature(_geo.geotx, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857'
	    });
	   
		_color='rgba(255,100,100, 0.9)';
		_colors='rgba(255,100,100, 1)';
	   if(
			_geo.estadocarga=='sin carga'){
	   		_color='rgba(98, 98, 98, 0.9';
	   		_colors='rgba(98, 98, 98, 1)';
	   }else if(
			_geo.estadocarga=='incompleto'){
	   		_color='rgba(128, 128, 128,0.9)';
	   		_colors='rgba(128, 128, 128,1)';
	   }else if(_val!=undefined){
		    _amplitud=(_res.data.indicador.representar_val_max-_res.data.indicador.representar_val_min);
		    //console.log(_amplitud);
		    _porc=(_val-_res.data.indicador.representar_val_min)/_amplitud;
		    //console.log(_porc);
		    if(_porc>1){
		    	_red= _rmax;
		    	_gre = _gmax;
		   		_blu = _bmax;
		    }else if(_porc<0){
		    	_red=_rmin;
		    	_gre = _gmin;
		   		_blu = _bmin;	
		    		
		    }else{
			    _red = _rmin+(_rmax-_rmin)*_porc;
			    _gre = _gmin+(_gmax-_gmin)*_porc;
			    _blu = _bmin+(_bmax-_bmin)*_porc;
		    }
		    _color='rgba('+_red+','+_gre+','+_blu+', 0.8)';
		    _colors='rgba('+_red+','+_gre+','+_blu+', 1)';
	    }
		_ancho=1.8;
		if(_res.data.capa.tipogeometria=='LineString'){
			_ancho=10;
		}
	    
	    _ft.setStyle(new ol.style.Style({
	         image: new ol.style.Circle({
			       fill: new ol.style.Fill({color: _color}),
			       stroke: new ol.style.Stroke({color: _colors,width: 0.8}),
			       radius: 6
			 }),
			 fill: new ol.style.Fill({color: _color}),
			 stroke: new ol.style.Stroke({color: _colors,width: _ancho}),
			 zIndex:100
		}));	    
	    _ft.setProperties(_geo);	    
	   	_source_ind.addFeature(_ft); 
	}
	if(_haygeom=='si'){
		_ext= _source_ind.getExtent();	
		//console.log(_ext);
		if(_encuadrado=='no'){
			mapa.getView().fit(_ext, { duration: 1000 });
			_encuadrado='si';
		}
	}
	//geometryOrExtent
	
	
	mapa.on('pointermove', function(evt){
		if(_mapaEstado=='dibujando'){return;}   
        if (evt.dragging) {	
        	//console.log(evt);
        	//deltaX = evt.coordinate[0] - evt.coordinate_[0];
  			//deltaY = evt.coordinate[1] - evt.coordinate_[1];
			//console.log(deltaX);			
          return;
        }
        var pixel = mapa.getEventPixel(evt.originalEvent);
        sobreIndicador(pixel);
    });

	var sobreIndicador = function(pixel) {     
	 
		if(_mapaEstado=='dibujando'){return;}   
    	
        var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
	        if(layer.get('name')=='indicador'){	        	
	          return feature;
	        }else{
	        	//console.log('no');
	        }
        });
        //console.log(feature.getProperties);
       //alert('señaló en registro id:')
    }
    
    
    mapa.on('click', function(evt){   
		if(_mapaEstado=='dibujando'){return;}   	
      	consultaPuntoInd(evt.pixel,evt);       
    });
	
	function consultaPuntoInd(pixel,_event) { 
		if(_mapaEstado=='dibujando'){return;}       
		//if(_Dibujando=='si'){return;}	
    	_source_ind_sel.clear();
    	
        var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
	        if(layer.get('name')=='indicador'){	        	
	          return feature;
	        }else{
	        	console.log('no');
	        }
        });
        
        _prop=feature.getProperties();
        
        
        var _format = new ol.format.WKT();
		var _ft = _format.readFeature(_prop.geotx, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857'
	    });
	    
	    _ft.setStyle(new ol.style.Style({
	         image: new ol.style.Circle({
			       fill: new ol.style.Fill({color: _color}),
			       stroke: new ol.style.Stroke({color: _colors,width: 0.5}),
			       radius: 6
			 }),
			 fill: new ol.style.Fill({color: _color}),
			 stroke: new ol.style.Stroke({color: _colors,width: 0.5}),
			 zIndex:100
		}));
		
	    _source_ind_sel.addFeature(_ft);
	    
	    
	    
		
		
        //console.log(feature.getProperties());
        
        accionGeomSeleccionada(_prop.id, _res);
            
       //alert('hizo click en registro id:')
    }
}

function dibujarBufferMapa(_res){
    //console.log('inicia funcion'); 
	//console.log(_res.data.geom);
	//console.log(_source_ind.getFeatures());
    _source_ind_buffer.clear();
    //console.log(_source_ind.getFeatures());
	_haygeom='no';
	for(_gn in _res.data.geom){		
		_geo=_res.data.geom[_gn];
		_val=null;
		_haygeom='si';		
		
		//console.log('+ um geometria: campo'+_campo+'. valor:'+_val);
		var _format = new ol.format.WKT();
		var _ft = _format.readFeature(_geo.geotx, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857'
	    });

		_color='rgba(100,100,100,0.2)';
		_colors='rgba(80,80,80,0.8)';
		
		_ft.setStyle(new ol.style.Style({
	         image: new ol.style.Circle({
			       fill: new ol.style.Fill({color: _color}),
			       stroke: new ol.style.Stroke({color: _colors,width: 0.5}),
			       radius: 6
			 }),
			 fill: new ol.style.Fill({color: 'rgba(0,0,0,0)'}),
			 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 0.5}),
			 zIndex:1
		}));
		
	    //_ft.setProperties(_geo);	    
	   	_source_ind_buffer.addFeature(_ft); 
	   		
	}
	
	if(_haygeom=='si'){
		_ext= _source_ind_buffer.getExtent();	
		//console.log(_ext);
		if(_encuadrado=='no'){
			mapa.getView().fit(_ext, { duration: 1000 });
			_encuadrado='si';
		}
	}
	//geometryOrExtent

}


var xmlDoc;
function dibujarCapaSuperp(_res){
	// console.log(_res.data.capa.sld);
	_source_ind_superp.clear();
    if (_res.data != null){
        var capaQuery = _res.data.capa_superp;
		_features = _res.data.geom_superp;
		_capa = _res.data.capa_superp;
        //Operaciones para leer del xml los valores de simbologia
        var xmlSld = capaQuery["sld"];
		
		console.log('representando capa superpuesta');
        if (xmlSld && xmlSld != ''){
            var colorRelleno = '';
            var transparenciaRelleno = '';
            var colorTrazo = '';
            var anchoTrazo = '';
			//console.log(xmlSld);
            
            if (window.DOMParser){
                parser = new DOMParser();
                xmlDoc = parser.parseFromString(xmlSld, "text/xml");
            }else{ // Internet Explorer
                xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
                xmlDoc.async = false;
                xmlDoc.loadXML(xmlSld);
            }
           
            _rules= xmlDoc.getElementsByTagName("Rule");	
		    _condiciones=Array();
		    
		    if(Object.keys(_rules).length>1){
			    for(_rn in _rules){	
					_larule = _rules[_rn];
					if(typeof _larule != 'object'){continue;}
					_algo=_larule.getElementsByTagName("Fill")[0];
					//console.log(_larule);
					var _mayorIgualQue = _larule.getElementsByTagName("ogc:PropertyIsGreaterThanOrEqualTo")[0];   			
			        for(var node in _mayorIgualQue.childNodes){		            	
			            if (_mayorIgualQue.childNodes[node].nodeName == "ogc:PropertyName"){
			                _campoMM = _mayorIgualQue.childNodes[node].textContent;
			            }
			            if (_mayorIgualQue.childNodes[node].nodeName == "ogc:Literal"){
			                _valorMM = _mayorIgualQue.childNodes[node].textContent;
			            }
			        }
			        var _menorQue = _larule.getElementsByTagName("ogc:PropertyIsLessThan")[0];   			
			        for(var node in _menorQue.childNodes){		            	
			            if (_menorQue.childNodes[node].nodeName == "ogc:PropertyName"){
			                _campomm = _menorQue.childNodes[node].textContent;
			            }
			            if (_menorQue.childNodes[node].nodeName == "ogc:Literal"){
			                _valormm = _menorQue.childNodes[node].textContent;
			            }
			        }
			        var xmlFill = _larule.getElementsByTagName("Fill")[0];						
					for(var node in xmlFill.childNodes){
			            if (xmlFill.childNodes[node].nodeName == "CssParameter" 
			                    && xmlFill.childNodes[node].getAttribute("name") == "fill"){
			                colorRelleno = xmlFill.childNodes[node].textContent;
			                //console.log(colorRelleno);
			            }
			            if (xmlFill.childNodes[node].nodeName == "CssParameter"
			                    && xmlFill.childNodes[node].getAttribute("name") == "fill-opacity"){
			                transparenciaRelleno = xmlFill.childNodes[node].textContent;
			                 //console.log(transparenciaRelleno);
			            }
			        }
			        var xmlStroke = _larule.getElementsByTagName("Stroke")[0];
			        for(var node in xmlStroke.childNodes){
			            if (xmlStroke.childNodes[node].nodeName == "CssParameter"
			                    && xmlStroke.childNodes[node].getAttribute("name") == "stroke"){
			                colorTrazo = xmlStroke.childNodes[node].textContent;
			            }
			            if (xmlStroke.childNodes[node].nodeName == "CssParameter"
			                    && xmlStroke.childNodes[node].getAttribute("name") == "stroke-width"){
			                anchoTrazo = xmlStroke.childNodes[node].textContent;
			            }
			        }
			        
			        _con={
			        	'campoMM':_campoMM,
			        	'valorMM':_valorMM,
			        	'campomm':_campomm,
			        	'valormm':_valormm,
			        	'colorRelleno':colorRelleno,
			        	'transparenciaRelleno':transparenciaRelleno,
			        	'colorTrazo':colorTrazo,
			        	'anchoTrazo':anchoTrazo
			        }
			        _condiciones.push(_con);
				}
			}
			
			console.log(_features);
			
		    for(var elem in _features){
				//console.log('feat:'+elem);
		        var format = new ol.format.WKT();	
		        var _feat = format.readFeature(_features[elem].geom_intersec, {
		            dataProjection: 'EPSG:3857',
		            featureProjection: 'EPSG:3857'
		        });
		
		        _feat.setId(_features[elem].id);
		
		        _feat.setProperties({
		            'id':_features[elem].id
		        });
		        
		        
		        if(_condiciones.length>0){
					_datasec=_features[elem];
					for(_k in _datasec){
						_kref=_k.replace('texto','nom_col_text');
						_kref=_kref.replace('numero','nom_col_num');
						//console.log(_kref+' - '+_capa[_kref] +' vs ' +_campoMM);
						if(_capa[_kref] == _campoMM){
							_campoMM =_k; 
							//console.log('eureka. ahora: '+_campoMM);
							break;
						}		
					}
					
					for(_k in _datasec){
						
						_kref=_k.replace('texto','nom_col_text');
						_kref=_kref.replace('numero','nom_col_num');
						//console.log(_kref+' - '+_capa[_kref] +' vs ' +_campoMM);
						if(_capa[_kref] == _campomm){
							_campomm =_k; 
							//console.log('eureka. ahora: '+_campomm);
							break;
						}	
								
					}
				
				
					//console.log(_features[elem][_campoMM] +' >= '+_valorMM+'&&'+_features[elem][_campomm]+' < '+ _valormm);
				
					
		         	_c= hexToRgb(document.getElementById('inputcolorrelleno').value);
			        _n=(1 - (document.getElementById('inputtransparenciarellenoNumber').value) * 1.0 / 100);
			        _rgba='rgba('+_c.r+', '+_c.g+', '+_c.b+', '+_n+')';
			
			        _st= new ol.style.Style({
			          fill: new ol.style.Fill({
			            color: _rgba
			
			          }),
			          stroke: new ol.style.Stroke({
			            color: document.getElementById('inputcolortrazo').value,
			            width: document.getElementById('inputanchotrazoNumber').value
			          })
			        });
			        //console.log('lll:'+_st);
			        for(_nc in _condiciones){
						if(
							Number(_features[elem][_campoMM]) >= Number(_condiciones[_nc].valorMM)
							&&
							Number(_features[elem][_campomm]) <  Number(_condiciones[_nc].valormm)
						){
							_c= hexToRgb(_condiciones[_nc].colorRelleno);
							//console.log(_condiciones[_nc].transparenciaRelleno);
					        _n=(1 - (_condiciones[_nc].transparenciaRelleno));
					        _rgba='rgba('+_c.r+', '+_c.g+', '+_c.b+', '+_n+')';
					
					        _st= new ol.style.Style({
					          fill: new ol.style.Fill({
					            color: _rgba
					
					          }),
					          stroke: new ol.style.Stroke({
					            color: _condiciones[_nc].colorTrazo,
					            width: _condiciones[_nc].anchoTrazo
					          }),
					          zIndex:1
					        });
						}
					}
		        	_feat.setStyle(_st);
				}
		        _source_ind_superp.addFeature(_feat);
		    }
		}else{
			_st= new ol.style.Style({
		          fill: new ol.style.Fill({
		            color: 'rgba(250, 200, 100, 0.5)'
		
		          }),
		          stroke: new ol.style.Stroke({
		            color: 'rgba(255, 100, 50, 1)',
		            width: '1'
		          })
	        });
			
			for(var elem in _features){
				console.log('feat:'+elem);
		        var format = new ol.format.WKT();	
		        var _feat = format.readFeature(_features[elem].geom_intersec, {
		            dataProjection: 'EPSG:3857',
		            featureProjection: 'EPSG:3857'
		        });
		
		        _feat.setId(_features[elem].id);
		
		        _feat.setProperties({
		            'id':_features[elem].id
		        });
		        
		        
	        	_feat.setStyle(_st);
		
		        _source_ind_superp.addFeature(_feat);
			}
			
			
		}
    }
}





function accionSeleccionarGeom(idgeom, _res){
	
	_source_ind_sel.clear();
	/*
	var _format = new ol.format.WKT();
	var _ft = _format.readFeature(_prop.geotx, {
        dataProjection: 'EPSG:3857',
        featureProjection: 'EPSG:3857'
    });
    _source_ind_sel.addFeature(_ft);
    
    console.log(feature.getProperties());
	*/
    //TODO
    
    //Aqui el codigo para seleccionar una feature del mapa, pero por codigo y no con click 
    //(o sea que no voy a saber que pixel se apreta sino la geometria seleccionada)
    
    accionGeomSeleccionada(idgeom, _res);
}

var drawL={};
var _nnelem=0;
function accionEditarCrearGeometria(){    
	
	_typeGeom=_DataCapa.tipogeometria;
	mapa.removeInteraction(drawL);
    drawL = new ol.interaction.Draw({
        source: _source_ind_sel,
        type: _typeGeom
    });
    
    _mapaEstado='dibujando';        
    mapa.addInteraction(drawL);  
	
	
	_source_ind_sel.on('change', function(evt){	
	
		if(_mapaEstado!='dibujando'){return;}
	
		if(_mapaEstado=='terminado'){_mapaEstado.estado='nuevaGeom';return;}	
		if(_mapaEstado.estado=='error'){
			_mapaEstado.estado='terminado';
			_source_ind_sel.clear();
			return;
		}	
		
		_features=_source_ind_sel.getFeatures();
		var format = new ol.format.WKT();
		_geometria=format.writeGeometry(_features[0].getGeometry());
		
		_nnelem++;		
		guardarNuevaGeometria(_geometria,_nnelem);
		
		_mapaEstado='terminado';	
		_clon=_features[0].clone();
		_source_ind.addFeature(_clon);
		_clon.setId('nn'+_nnelem);
		
		_source_ind_sel.clear();
	
	});	
}

