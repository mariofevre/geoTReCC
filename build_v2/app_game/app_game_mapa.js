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
		style: styleArea
	    //source: _source
	});

	seleccionLayer = new ol.layer.Vector({
		name: 'seleccionLayer',
		style: _CentStyle,
	    source: _sourceSeleccion
	});
	
	var marcoLayer = new ol.layer.Vector({
		style: new ol.style.Style({
			stroke: new ol.style.Stroke({color : 'rgba(200,50,50,1)', width : 1, lineDash: [2,3]}),
	    	fill: new ol.style.Fill({color : 'rgba(250,255,250,0)'})
		}),
		source: _sMarco
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
	
	_view =	new ol.View({
      projection: 'EPSG:3857',
      center: [-7000000,-4213000],
      zoom: 5,
      minZoom:2,
      maxZoom:19	      
	});
	

	   	
	var tablaRasLayer = new ol.layer.Image();
 /*
	 var tablaRasLayer = new ol.layer.Image({
	    source: new ol.source.ImageWMS({
	      ratio: 1,
	      url: 'http://190.111.246.33:8080/geoserver/geoGEC/wms',
	      params: {
	            'VERSION': '1.1.1',  
	            LAYERS: 'est_01_municipios',
	            STYLES: ''
	      }
	    })
	});
	*/
	//var _sourceBaseOSM=new ol.source.OSM();
	
	
	
	var _sourceIGN= new  ol.source.XYZ({
		url: 'https://wms.ign.gob.ar/geoserver/gwc/service/tms/1.0.0/mapabase_gris@EPSG%3A3857@png/{z}/{x}/{-y}.png',
		crossOrigin: "Anonymous"
	})

	
	_layerBase = new ol.layer.Tile({
		 source: _sourceIGN,	
	});
	
	var _sourceBaseBING=new ol.source.BingMaps({
	 	key: 'CygH7Xqd2Fb2cPwxzhLe~qz3D2bzJlCViv4DxHJd7Iw~Am0HV9t9vbSPjMRR6ywsDPaGshDwwUSCno3tVELuob__1mx49l2QJRPbUBPfS8qN',
	 	imagerySet:  'Aerial'
	});
		
	_sourceBaseBING.setAttributions(
		['base satelital: <a target="blank" href="https://www.microsoft.com/en-us/maps/product"><img src="https://dev.virtualearth.net/Branding/logo_powered_by.png"> Microsoft</a>']
	)

        
	_lyrCent.setStyle(_CentStyle);

	
	var style = new ol.style.Style ({
	  fill: new ol.style.Fill({
	    color: 'black',
	  }),
	});
	
	var layerBing = new ol.layer.Tile({
		 
	});	

        
	_lyrCent.setStyle(_CentStyle);
   	
     
     _lyrElem.setStyle(_lyrElemStyle);
    La_ExtraBaseWms = new ol.layer.Tile({
        visible: true,
        source: _ExtraBaseWmsSource
    });

	mapa = new ol.Map({
	    layers: [
			_layerBase,
			layerBing,
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
	
	
	function reiniciarMapa(){
		_features=_sCargado.getFeatures();	
		for (i = 0; i < _features.length; i++) {		
			_sCargado.removeFeature(_features[i]);
		}
		
		_features=_sCandidato.getFeatures();	
		for (i = 0; i < _features.length; i++) {		
			_sCandidato.removeFeature(_features[i]);
		}
	}


	function consultaPuntoAj(_Pid){
		console.log(_Pid);
		formAI(_Pid);
		
	}
}
cargarMapa();


function cargarCapaMarco(){
	_sMarco.clear();
	_haygeom='no';
	if(_DataMarco.geotx==''){return;}		
	
	if(_DataMarco.geotx!=null){
		console.log('carga marco');
		var _format = new ol.format.WKT();
		var _ft = _format.readFeature(_DataMarco.geotx, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857'
	    });
	   	_sMarco.addFeature(_ft);
   	} 
}

var _src={};
var _lyr={};


//GAME layer de geometría del buffer Zero
_src['bufferzero']= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});
_lyr['bufferzero']= new ol.layer.Vector({
	name: 'bufferzero',
    source: _src['bufferzero'],
    style: new ol.style.Style({
		 stroke: new ol.style.Stroke({color: 'rgb(117, 25, 8)',  width: 1,  lineDash: [4,4]}),
		 zIndex:101
	})       
});



mapa.addLayer(_lyr['bufferzero']);


var	_source_ind_superp_wms = new ol.source.TileWMS();//variable source utilizada por la capa extra base wms para mostar un url asignado dinámicamente.
var _layer_ind_superp_wms = new ol.layer.Tile({
        visible: true,
        source: _source_ind_superp_wms
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
		 zIndex:1
	})    
});
mapa.addLayer(_layer_ind_superp);

//layer de geometría del indicador
var _source_ind= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});
var _layer_ind= new ol.layer.Vector({
	name: 'indicador',
    source: _source_ind,
    style: ol.style.Style({
		 zIndex:100
	})       
});
mapa.addLayer(_layer_ind);

var _source_ind_sel= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});


//layer de geometría seleccionadad para cargar datos
var _st_ind_sel=new ol.style.Style({
     image: new ol.style.Circle({
	       stroke: new ol.style.Stroke({color:'rgb(8, 175, 217)',width: 8}),
	       radius: 6
	 }),
	 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 20}),
	 zIndex:200
});
var _layer_ind_sel= new ol.layer.Vector({
	name: 'indicador: elemento selecto',
    source: _source_ind_sel,
    style: _st_ind_sel
});
mapa.addLayer(_layer_ind_sel);


//layer de area de influencia de la geometría
var _source_ind_buffer= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});
var _layer_ind_buffer= new ol.layer.Vector({
	name: 'buffer',
    source: _source_ind_buffer,
    style: ol.style.Style({
		 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)', width: 0.5}),
		 fill: new ol.style.Fill({color: 'rgb(0,0,0)'}),
		 zIndex:2
	})    
});
mapa.addLayer(_layer_ind_buffer);


//GAME layer de geometría cubierta por la geometria ejecutada en el juego
_src['game_cubierto']= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});
_lyr['game_cubierto']= new ol.layer.Vector({
	name: 'game_cubierto',
    source: _src['game_cubierto'],
    style: new ol.style.Style({
    	image: new ol.style.Circle({	fill: new ol.style.Fill({color: 'rgba(255,102,0,1)'}),	stroke: new ol.style.Stroke({color: '#ff3333',width: 0.8}), radius: 6 }),
		fill: new ol.style.Fill({color: 'rgba(255,102,0,0.5)'}),
		stroke: new ol.style.Stroke({color: 'rgb(80, 0, 0)',  width: 1}),
		zIndex:101
	})       
});
mapa.addLayer(_lyr['game_cubierto']);


//GAME layer de geometría del buffer Preliminar
_src['bufferpre']= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});
_lyr['bufferpre']= new ol.layer.Vector({
	name: 'bufferzero',
    source: _src['bufferzero'],
    style: new ol.style.Style({
		 stroke: new ol.style.Stroke({color: 'rgb(117, 25, 8)',  width: 1,  lineDash: [4,4]}),
		 zIndex:102
	})       
});
mapa.addLayer(_lyr['bufferpre']);

var _encuadrado='no';
var _mapaEstado ='';

mapa.on('pointermove', function(evt){
	//alert(_mapaEstado);
	if(_mapaEstado=='terminado'){return;}
	if(_mapaEstado=='ejecutando'){
		preliminarGeom(evt,'prelim');
		return;
	}
	
});

mapa.on('click', function(evt){   
	
	if(_mapaEstado==''){_mapaEstado='ejecutando';}
	
	console.log(_mapaEstado);
	if(_mapaEstado=='terminado'){return;}
	if(_mapaEstado=='ejecutando'){
		preliminarGeom(evt,'click');		
		return;
	}
	
	if(_mapaEstado=='change'){
		console.log('detenido');		
		_mapaEstado='detenido';
		return;
	}
		
  	if(_mapaEstado=='detenido'){
  		//console.log('click estando detenido');
  		preliminarGeom(evt,'click');
  		_mapaEstado='ejecutando';
		return;
  	}
});



function dibujarBufferMapa(_res){
    _source_ind_buffer.clear();
	_haygeom='no';
	for(_gn in _res.data.geom){		
		//console.log('aaa');
		_geo=_res.data.geom[_gn];
		_val=null;
		_haygeom='si';		
		
		var _format = new ol.format.WKT();
		var _ft = _format.readFeature(_geo.geotx, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857'
	    });
		
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
	_source_ind_superp.clear();
    if (_res.data != null){
        var capaQuery = _res.data.capa_superp;
		_features = _res.data.geom_superp;
		_capa = _res.data.capa_superp;
        //Operaciones para leer del xml los valores de simbologia
        var xmlSld = capaQuery["sld"];
		
		//console.log('representando capa superpuesta');
        if (xmlSld && xmlSld != ''){
            var colorRelleno = '';
            var transparenciaRelleno = '';
            var colorTrazo = '';
            var anchoTrazo = '';

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
					//console.log(_algo);
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
			
		    for(var elem in _features){
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
		        
		        
	        	_feat.setStyle(_st);
		
		        _source_ind_superp.addFeature(_feat);
			}
		}
    }
}


function accionSeleccionarGeom(idgeom, _res){
	
	_source_ind_sel.clear();

    //TODO
    
    //Aqui el codigo para seleccionar una feature del mapa, pero por codigo y no con click 
    //(o sea que no voy a saber que pixel se apreta sino la geometria seleccionada)
    
    accionGeomSeleccionada(idgeom, _res);
}

var drawL={};
var _nnelem=0;

var _drawL_coords=Array();
var _drawL_largoacum=0;
var _lprev=0;
var _abortarFeature=0;//valor 1 aborta la creacion del feature.

function accionEditarCrearGeometria(){    
	_mapaEstado='ejecutando';
	_drawL_coords=Array();	
	console.log('vacia el listado');
	//_typeGeom=_DatosCapaRes.tipogeometria;
	_typeGeom=_DataIndicadorB.tipogeometria;
	
	mapa.removeInteraction(drawL);
		    
    drawL = new ol.interaction.Draw({
        source: _source_ind_sel,
        type: _typeGeom
    });
            
    mapa.addInteraction(drawL); 
		
	_source_ind_sel.on('change', function(evt){
		
		if(_mapaEstado=='validando'){return;}
		
		console.log('change');		
		//_drawL_coords=Array();
		//console.log('vaciado el listado');
		_mapaEstado='change';
				
		
		console.log(_GeometriaTurno.largo_definido);
		
		_ld=_GeometriaTurno['largo_antefeatures']+_GeometriaTurno.largo_definido;
		
		_GeometriaTurno={
			'vertices':Array(),
			'distancias':Array(),
			'largo_definido':0,
			'largo_anteturnos':0,
			'largo_antefeatures':_ld,
			'vertice_cursor':Array(),
			'largo_cursor':0
		};	
		
		
	});	
}

function zoomArea(){
	_feats=_sMarco.getFeatures();
	
	for(_f in _feats){
		
		_view.fit(
			_feats[_f].getGeometry(), 
			{
				duration: 3000	
			}	
		);
	}
}



function bufferAIprevia(){

	 var parser = new jsts.io.OL3Parser();
    parser.inject(Point, LineString, LinearRing, Polygon, MultiPoint, MultiLineString, MultiPolygon);

    for (var i = 0; i < features.length; i++) {
      var feature = features[i];
      // convert the OpenLayers geometry to a JSTS geometry
      var jstsGeom = parser.read(feature.getGeometry());

      // create a buffer of 40 meters around each line
      var buffered = jstsGeom.buffer(40);

      // convert back from JSTS and replace the geometry on the feature
      feature.setGeometry(parser.write(buffered));
    }

    source.addFeatures(features);
}


var _BufferZeroCoord=Array();
var _BufferZeroFeature={};
function SeguirCursorBufferZero(_nuevaCoord){
	//console.log('coord:'+_BufferZeroCoord);
	//console.log('ncoord:'+_nuevaCoord);
	_deltax=_nuevaCoord[0]-_BufferZeroCoord[0];
	_deltay=_nuevaCoord[1]-_BufferZeroCoord[1];
	//console.log('deltas:'+_deltax+','+_deltay)
	if(_bufferzerodisponible=='no'){return;}
	_BufferZeroFeature.getGeometry().translate(_deltax, _deltay);
	_BufferZeroCoord=_nuevaCoord;
	//console.log('ncoord act:'+_BufferZeroCoord);
}

var _BufferPreCoord=Array();
var _BufferPreFeature={};
function SeguirBufferPrevio(){
	
	_feat=_source_ind_sel.getFeatures();
	
	 var parser = new jsts.io.OL3Parser();
     parser.inject(Point, LineString, LinearRing, Polygon, MultiPoint, MultiLineString, MultiPolygon);
    
    _src['bufferpre'].clear();  
	for(_ff in _feat){
		  // convert the OpenLayers geometry to a JSTS geometry
          var jstsGeom = parser.read(_feat[_ff].getGeometry());
          
          // create a buffer of 40 meters around each line
          var buffered = jstsGeom.buffer(200);
          //TODO parametrizar distancia buffer
          
          // convert back from JSTS and replace the geometry on the feature
          _ft.setGeometry(parser.write(buffered));
          
          _src['bufferpre'].addFeature(_ft);
	}
}


var _bufferzerodisponible='no';
function DibujarBufferZero(_geo_buffer_centroide_tx){
	
	_format = new ol.format.WKT();	
	_BufferZeroFeature = _format.readFeature(_geo_buffer_centroide_tx, {
        dataProjection: 'EPSG:3857',
        featureProjection: 'EPSG:3857'
    });
	
	_src['bufferzero'].clear();
	_src['bufferzero'].addFeature(_BufferZeroFeature);
	_bufferzerodisponible='si';
	
	mapa.on('pointermove', function(evt){
		
		if(_mapaEstado=='terminado'){return;}   
        
        //console.log(evt);
        _nuevaCoord = evt.coordinate;
        
        SeguirCursorBufferZero(_nuevaCoord);
  	});
}

/////
//tomado de stackoverflow: https://stackoverflow.com/a/6853926
///////
function pDistance(x, y, x1, y1, x2, y2) {

  var A = x - x1;
  var B = y - y1;
  var C = x2 - x1;
  var D = y2 - y1;

  var dot = A * C + B * D;
  var len_sq = C * C + D * D;
  var param = -1;
  if (len_sq != 0) //in case of 0 length line
      param = dot / len_sq;

  var xx, yy;

  if (param < 0) {
    xx = x1;
    yy = y1;
  }
  else if (param > 1) {
    xx = x2;
    yy = y2;
  }
  else {
    xx = x1 + param * C;
    yy = y1 + param * D;
  }

  var dx = x - xx;
  var dy = y - yy;
  return Math.sqrt(dx * dx + dy * dy);
}
///////////////



function preliminarGeom(_evt,_tipo){
	
	//_tipo "click" - define un nuevo punto en la geomatría.
	//_tipo "prelim" - estima la distancia definida por el puntero
	
	_l=0;
	_porc=0;
	
	if(_GeometriaTurno.vertices.length==0){
		//console.log('sin geom previa');
		if(_tipo=='click'){
			
			_abortarFeature=0;
			
			_px=_evt.coordinate[0];
			_py=_evt.coordinate[1];
			
			
			//TODO retomar esto
			//VERIFICACION DE CUMPLIMIENTO DE RED
				/*
				_feats=_source_ind_sel.getFeatures();
				
				_mindist=100000;//valor de referencia para iniciar el min para identificar refecto red;
				_n=0;
				
				featloop:
				for(_fn in _feats){
					_n++;
					_geom=_feats[_fn].getGeometry();
					
					_geom.forEachSegment(function(start, end){					
						_dist=pDistance(_px,_py,start[0],start[1],end[0],end[1])/1.23; //TODO ajustar este factor al valr correspondeinte por la proyección en esta zona.
						_mindist=Math.min(_mindist,_dist);
					});
				}
				
				//console.log('mindist:'+_mindist);
				if(_n>0){
					//esta no es la primer feature.
					if(_DataSesion.modored=='1'){
						//el modo red debe impedir nuevos elementos lejos de los viejos.
						_maxdist=_DataIndicador.calc_buffer*0.3;//limite admisible en modo red (distancia de un nuevo elemento a los preexistentes);
						if(_mindist>_maxdist){
							_abortarFeature=1;						
							alert('Este punto está muy lejos de la red existente!');
						}	
					}
				}
				
				//console.log('se incorpora coordenada a listado vacio');
				//_drawL_coords.push(_evt.coordinate);
				* */
			////////////	
			NuevoVerticeTurno(_evt);
		}
		DistanciaInstantanea(_evt);
		return;
	}
	
	if(_mapaEstado=='detenido'){console.log('estado detenido');return;}
	
	DistanciaInstantanea(_evt);
  	
  	if(_abortarFeature=='1'){
  		document.querySelector('#limite').setAttribute('estado','alerta');
	  	document.querySelector('#limite #tx').innerHTML='¡Empieza muy lejos!';
  	}else{
		
		if(_tipo=='click'){
			//_drawL_coords.push(_evt.coordinate);
			NuevoVerticeTurno(_evt);
			_porc=100*(_GeometriaTurno.largo_definido+_GeometriaTurno.largo_antefeatures)/_DataSesion.limiteunitarioporturno;
			
			if(_porc>100){
				drawL.finishDrawing();
				//console.log('limite');
				recortarFeature();
				pasarTurno();
				//ValidarGeom();
			}else{
				NuevoVerticeTurno(_evt);
			}
		}
  	}
}	
	
var _GeometriaTurno={
	'vertices':Array(),
	'distancias':Array(),
	'largo_definido':0,
	'largo_anteturnos':0,
	'largo_antefeatures':0,
	'vertice_cursor':Array(),
	'largo_cursor':0
};	

function NuevoVerticeTurno(_evt){
	
	_GeometriaTurno.vertices.push(_evt.coordinate);
	_GeometriaTurno.largo_definido=0;
	_GeometriaTurno.distancias=Array();
	//Distancias de cada vértice
	for(_n in _GeometriaTurno.vertices){		
		if(_n == 0){//el primer vertice no define distancia
			_GeometriaTurno.distancias.push(0);
			continue;
		}
			
		_coN= _GeometriaTurno.vertices[_n];
		_coA= _GeometriaTurno.vertices[_n-1];
		
		_deltax=_coN[0]-_coA[0];
		_deltay=_coN[1]-_coA[1];
	  	
	  	_dist=Math.sqrt(Math.pow(_deltax,2)+ Math.pow(_deltay,2));
  		_dist=Math.round(_dist/1.23); //TODO ajustar este factor al valr correspondeinte por la proyección en esta zona.
		
		_GeometriaTurno.distancias.push(_dist);
		_GeometriaTurno.largo_definido+=_dist;
	}
	DistanciaInstantanea(_evt);
	actualizarCartel();	
}

function DistanciaInstantanea(_evt){
	
	if(_GeometriaTurno.vertices.length<1){return;}

	_GeometriaTurno.vertice_cursor=_evt.coordinate;
	
	//Distancias de cada vértice
	_f  = _GeometriaTurno.vertices.length;
	_ult=_GeometriaTurno.vertices[_f-1];
	
	_coN= _GeometriaTurno.vertice_cursor;
	_coA= _ult;
	
	_deltax=_coN[0]-_coA[0];
	_deltay=_coN[1]-_coA[1];
	
	_dist=Math.sqrt(Math.pow(_deltax,2)+ Math.pow(_deltay,2));
	_dist=Math.round(_dist/1.23); //TODO ajustar este factor al valr correspondeinte por la proyección en esta zona.
	
	_GeometriaTurno.largo_cursor=_dist;

	actualizarCartel();
}

function actualizarCartel(){

	_tx='..propuesta válida...';
	_estado='normal';
	_l=_GeometriaTurno.largo_cursor+_GeometriaTurno.largo_definido+_GeometriaTurno.largo_antefeatures;
	_porc=100*_l/_DataSesion.limiteunitarioporturno;

	if(_porc>100){
		_estado='alerta';
		_tx='¡Límite Sobrepasado!';
	}

	document.querySelector('#limite #barra #avan').style.width=Math.min(100,_porc)+'%';
	document.querySelector('#limite #porc').innerHTML=Math.round(_porc)+'%'+' '+_l+'m';  
	document.querySelector('#limite').setAttribute('estado',_estado);
	document.querySelector('#limite #tx').innerHTML=_tx;
}


function recortarFeature(){
	
	_l=0; //largo acumulado para los features de este turno
	_feats=_source_ind_sel.getFeatures();
	for(_fn in _feats){
		_geom=_feats[_fn].getGeometry();
		
		var _format = new ol.format.WKT();
		_geometriatx=_format.writeGeometry(_geom);
		_geometriatx=_geometriatx.replace('LINESTRING(','');
		_geometriatx=_geometriatx.replace(')','');
		_coords=_geometriatx.split(',');
		
		for (_i in _coords){
			if(_i==0){continue;}//el punto 0 no define distancia
			_coi=_coords[_i].split(' ');
			_coi_1=_coords[_i-1].split(' ');//acá refiere al punto anterior, el inicio del segmento.
			_deltax=_coi[0]-_coi_1[0];
			_deltay=_coi[1]-_coi_1[1];
			_dist=Math.sqrt(Math.pow(_deltax,2)+ Math.pow(_deltay,2));
			_dist=_dist/1.23; //TODO ajustar este factor al valr correspondeinte por la proyección en esta zona.
			_lanterior=_l;
			_l+=Math.round(_dist);
			
			_lfalta=_DataSesion.limiteunitarioporturno-_lanterior;
			//console.log('largo anterior: '+_lanterior+' largofaltaba: '+_lfalta+' largo segmento a cortar:'+Math.round(_dist));
			
			if(_l>_DataSesion.limiteunitarioporturno){
				
				//calcula el último segmento que complete justo el largo del turno
				_rel=_lfalta/Math.round(_dist);
				_deltax_recorte = _deltax * _rel;
				_xrecorte=Number(_coi_1[0]) + _deltax_recorte;
				_deltay_recorte = _deltay * _rel;	
				_yrecorte=Number(_coi_1[1]) + _deltay_recorte;
				
				_wkt='LINESTRING (';
				_coordsCortas=Array();					
				for (ib = 0; ib < _i; ib++){
					_coib=_coords[ib].split(' ');
					_wkt+=_coords[ib]+', ';
					_coordsCortas.push(_coib);
				}
				
				//incorpora el ultimo segmento de recorte coalculado al largo total
				_cofalta=Array(_xrecorte,_yrecorte);
				_coordsCortas.push(_cofalta);
				_wkt+=_xrecorte+' '+_yrecorte+', ';
				
				_geomCorta=new ol.geom.LineString(_coordsCortas);
				
				_feats[_fn].setGeometry(_geomCorta);
				_l-=Math.round(_dist);
				_drawL_largoacum=_l
				
				_wkt=_wkt.substring(0, _wkt.length - 2);
				_wkt+=')';
								
				_format = new ol.format.WKT();
				_ft = _format.readFeature(_wkt, {
					dataProjection: 'EPSG:3857',
					featureProjection: 'EPSG:3857'
				});
				
				_source_ind_sel.removeFeature(_feats[_fn]);
				_source_ind_sel.addFeature(_ft);
				 
				_l=_DataSesion.limiteunitarioporturno;
				
				break;
			}
		}
		_lprev=_l;
		_drawL_largoacum=_lprev;
		_porc=100*_l/_DataSesion.limiteunitarioporturno;
	}
}

	
function pasarTurno(){
	if(document.querySelector('#pasar').getAttribute('estado')=='inactivo'){return;}
	
	_feats=_source_ind_sel.getFeatures();

	_arr_wkt=Array();
	for(_fn in _feats){
		_geom=_feats[_fn].getGeometry();
		var _format = new ol.format.WKT();
		_geometriatx=_format.writeGeometry(_geom);
		_arr_wkt.push(_geometriatx);
	}
	if(_fn == undefined){
		
	}
	
	_GeometriaTurno={
		'vertices':Array(),
		'distancias':Array(),
		'largo_definido':0,
		'largo_anteturnos':0,
		'largo_antefeatures':0,
		'vertice_cursor':Array(),
		'largo_cursor':0
	};	
	actualizarCartel();

	guardarGeometriaTurno(_arr_wkt,_nnelem);
	
	_mapaEstado='terminado';
	_source_ind_sel.clear();
}

	
function guardarGeometriaTurno(_arr_wkt,_nnelem){ 
	
	document.querySelector('#gifanimado').style.display='block';
	
	var _param = {
		'codMarco': _CodMarco,
        'idMarco': _IdMarco,
        'arr_wkt': _arr_wkt,
        'idSesion': _IdSesion,
        'partida':_Partida,
        'tipogeometria':_DataIndicadorB.tipogeometria
    };
	
	$.ajax({
		data:_param,
		url:   './app_game/app_game_guardar_turno.php',
		type:  'post',
		success: function (response){alert('error al consulta el servidor');},
		success:  function (response){
			var _res = $.parseJSON(response);
			
			document.querySelector('#gifanimado').style.display='none';
			
			if(_res.res=='exito'){
				mapa.removeInteraction(drawL);

				_Partida=_res.data.nid.partida;
				_drawL_largoacum=0;
			
				
				_Features=_res.data.geom_superp;
				if(_res.res == 'exito'){
	            	cargarFeatures('cubierto');
	            }
	            
	            if(Number(_res.data.geom_superp_max.superp_max_numero1)>0){
					_val=Number(_res.data.intersec_sum*100/_res.data.geom_superp_max.superp_max_numero1);
					if(_val>10){
	        			_vp=formatearNumero(_val,0);	
	        		}else{
	        			_vp=formatearNumero(_val,2);
	        		}
	        		
	        		if(_res.data.intersec_sum>10){
	        			_v=formatearNumero(_res.data.intersec_sum,0);	
	        		}else{
	        			_v=formatearNumero(_res.data.intersec_sum,2);
	        		}
	        		
					_Puntaje=_v;
					_PuntajeP=_vp;
				}
				
	            avanzarTurno();			
			}else{
				alert('la solicitud no fue ejecutada');
			}
		}
	});
}
