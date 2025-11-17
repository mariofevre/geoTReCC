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
	_view.on('change:center', function(){
		ActualizarLink();
		//console.log(_view.getResolution())
	});// funcion en ./comun_mapa/comun_mapa_link.js
	//_view.on('change:center', function(){alert('hola');});// funcion en ./comun_mapa/comun_mapa_link.js

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
    La_ExtraBaseWms.set('crossOrigin', "anonymous");
    
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
	La_ExtraBaseWms.set('crossOrigin', "anonymous");
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

//layer de superposicion a layer buffer
var _source_rele_superp= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});
var _layer_rele_superp= new ol.layer.Vector({
	name: 'buffer',
    source: _source_rele_superp,
    style: ol.style.Style({
		 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 0.5}),
		 fill: new ol.style.Fill({color: 'rgb(0,0,0)'}),
		 zIndex:1
	})    
});

//layer de geometría del relevamiento
var _source_rele= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});


var labelStyle = new ol.style.Style();
function resetLabelStyle(){
		// se resetea al cambiar la simboloíga o el tipo de geometría
	
	
	var _color='rgba(0,200,256,0.2)';
	var _colorb='rgba(0,0,100,1)';
	var _ancho='0.5';

	if(_DataRele!=undefined){
		if(_DataRele.tipogeometria=='LineString'){
			_ancho='5';
		}
	}
	
	labelStyle = new ol.style.Style({
		image: new ol.style.Circle({
			   fill: new ol.style.Fill({color: _color}),
			   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
			   radius: 12
		}),
		fill: new ol.style.Fill({color: _color}),
		stroke: new ol.style.Stroke({color: _colorb,width: _ancho}),
		zIndex:1000,
		text: new ol.style.Text({
			font: '14px Calibri,sans-serif',
			overflow: true,
			offsetX:8,
			offsetY:-4,
			fill: new ol.style.Fill({
			color: '#000'
			}),
			stroke: new ol.style.Stroke({color: '#fff', width: 4})
		})
	});
}
resetLabelStyle();

var _color='rgba(0,200,256,0.2)';
var _colorb='rgba(0,0,100,1)';
var _ancho='0.5';

var _mudoStyle = new ol.style.Style({
	image: new ol.style.Circle({
		   fill: new ol.style.Fill({color: _color}),
		   stroke: new ol.style.Stroke({color: _colorb,width: 2}),
		   radius: 12
	}),
	fill: new ol.style.Fill({color: _color}),
	stroke: new ol.style.Stroke({color: _colorb,width: _ancho}),
	zIndex:1000
});


var _layer_rel= new ol.layer.Vector({
	name: 'relevamiento',
    className: 'multiply',
    source: _source_rele,
    zIndex:5000,
    //style:_mudoStyle,
    
	style: function(feature){
		console.log('o:'+feature.get('name'));
		if(feature.get('name')!=null){
	    	labelStyle.getText().setText(feature.get('name'));	    	
	   	}else{
	   		labelStyle.getText().setText('s/n');
	   	}
	   //console.log(feature.get('estado'));
	   	if(feature.get('estado')==='0'){
	   		labelStyle.getFill().setColor("rgba(255,0,0,0.2)");
	   		//console.log(labelStyle.getImage().getFill());
	   		//labelStyle.getImage().setColor("rgba(0,255,100,0.8)");
	   		
	   		labelStyle.setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: "rgba(255,0,0,0.2)"}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 12,
					   zIndex:1000,
				})
	   		);
	   		console.log('zero');
	   		labelStyle.setStroke(
				new ol.style.Stroke({
					color: "rgba(255,0,0,0.7)",
					width: 4}
				)
			);
	   		
	   		
	   	}else if(feature.get('estado')==='1'){
			console.log('uno');
	   		labelStyle.getFill().setColor("rgba(0,255,100,0.2)");
	   		labelStyle.setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: "rgba(0,255,100,0.2)"}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 12,
					   zIndex:1000,
				})
	   		);
	   		labelStyle.setStroke(
				new ol.style.Stroke({
					color: _colorb,
					width: 0.8}
				)
			);
	   		
	   	}else{
			console.log('nada');
	   		labelStyle.getFill().setColor(_color);
	   		labelStyle.setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: _color}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 12,
					   zIndex:1000,
				})
	   		);
	   		labelStyle.setStroke(
				new ol.style.Stroke({
					color: "rgba(255,0,0,0.7)",
					width: 4}
				)
			);
	   	}
	   
		return labelStyle;
	},
	declutter: true   
});
mapa.addLayer(_layer_rel);

/*
var div = document.getElementById(_layer_rel.id);
var canvas = document.querySelector('#portamapa .multiply canvas');
var context = canvas.getContext("2d");
context.globalCompositeOperation = "multiply";*/


//layer de geometría seleccionada para cargar datos
var _source_rel_sel= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});

var _st_rel_sel=new ol.style.Style({
     image: new ol.style.Circle({
	       stroke: new ol.style.Stroke({color:'rgb(8, 175, 217)',width: 0.8}),
	       radius: 6
	 }),
	 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 1}),
	 zIndex:200000
});
var _layer_rel_sel= new ol.layer.Vector({
	name: 'indicador: elemento selecto',
    source: _source_rel_sel,
    style: _st_rel_sel,
	 zIndex:200000
});
mapa.addLayer(_layer_rel_sel);

//layer de vercites handler para cargar datos
var _source_rel_sel_vert= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});

var _st_rel_sel_vert=new ol.style.Style({
     image: new ol.style.Circle({
	       stroke: new ol.style.Stroke({color:'rgb(8, 175, 217)',width: 0.8}),
	       radius: 6
	 })
});
var _layer_rel_sel_vert= new ol.layer.Vector({
	name: 'vertices del elemento selecto',
    source: _source_rel_sel_vert,
    style: _st_rel_sel_vert,
	 zIndex:190000
});
mapa.addLayer(_layer_rel_sel_vert);


mapa.on('click', function(evt){   
	if(_mapaEstado=='dibujando'){return;} 		
  	consultaPuntoRel(evt.pixel,evt);       
});

function consultaPuntoRel(pixel,_event) { 
	if(_mapaEstado=='dibujando'){return;}
	if(_mapaEstado=='nuevaGeom'){return;}  
	if(_mapaEstado=='terminado'){return;}
	//if(_Dibujando=='si'){return;}	
	_source_rel_sel.clear();
	
	document.querySelector('#portamapa #listadoUA').setAttribute('activo','no');
	_listado = document.querySelector('#portamapa #listadoUA #listadito');
	_listado.innerHTML='';
	
	_c=0;
	_feature={};
	_prop={};
    mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
		
        if(layer.get('name')=='relevamiento'){	
			_c++;	
			_feature=feature;
			_prop=feature.getProperties();
			_prop['id']=feature.getId();
			//console.log(_prop);
			
			_ua=document.createElement('a');
			_listado.appendChild(_ua);
			_ua.setAttribute('onclick','accionGeomSeleccionada('+feature.getId()+')');

			_ua.innerHTML=_prop.name;
			        	
          //return feature;
        }else{
        	console.log('no');
        }
    });
    
    if(_c>1){
		 document.querySelector('#portamapa #listadoUA').setAttribute('activo','si');
	}
	
	if(_c==0){return;}
    
    
    
    //_ext= _ft.getGeometry().getExtent();	
    //_view.fit(_ext, { duration: 1000 });
    //console.log(feature.getProperties());
    accionGeomSeleccionada(_prop.id);
   //alert('hizo click en registro id:')
}

function generarFeatureSelecta(_idgeom){	
	
	_source_rel_sel.clear();
	
	_ft_orig=_source_rele.getFeatureById(_idgeom);
	
    if(_ft_orig==null){
		console.log('¡No se encontró el feature con id:'+_idgeom);
		return;
	}



	_color="rgba(0,200,256,0.0)";
    _colors='rgba(0,0,256,0.5)';
	_ft_orig.setStyle(new ol.style.Style({
         image: new ol.style.Circle({
		       fill: new ol.style.Fill({color: _color}),
		       stroke: new ol.style.Stroke({color: _colors,width: 0.5}),
		       radius: 6,
			   zIndex:100
		 }),
		 fill: new ol.style.Fill({color: _color}),
		 stroke: new ol.style.Stroke({color: _colors,width: 2,lineDash:[4,8]}),
		 
		 zIndex:100
	}));
	
	
	_ft=_ft_orig.clone();
	_ft.setId(_idgeom);
	_source_rel_sel.addFeature(_ft);		
	
	
	//_view.fit(_ft.getGeometry());
	
    
    /*
	//console.log('carga punto rel');
    var _format = new ol.format.WKT();
	var _ft = _format.readFeature(_prop.geotx, {
        dataProjection: 'EPSG:3857',
        featureProjection: 'EPSG:3857',
        id:_prop.id
    });
    _ft.setId(_prop.id);
    
   
    
    console.log(_prop.geotx);
   */ 
   //_color='rgba(255,255,255,0.0)';
	_color="rgba(0,200,256,0.0)";
    _colors='rgba(0,0,256,0.5)';
    _ft.setStyle(new ol.style.Style({
         image: new ol.style.Circle({
		       fill: new ol.style.Fill({color: _color}),
		       stroke: new ol.style.Stroke({color: _colors,width: 0.5}),
		       radius: 6,
			   zIndex:100
		 }),
		 fill: new ol.style.Fill({color: _color}),
		 stroke: new ol.style.Stroke({color: _colors,width: 2}),
		 zIndex:100
	}));
    //_source_rel_sel.addFeature(_ft);
    
    _geom=_ft.getGeometry();
    if(	//es un punto
		_geom.extent_[2]-_geom.extent_[0]<0.5
		&&
		_geom.extent_[3]-_geom.extent_[1]<0.5
    ){
		_view.setCenter(_geom.extent_);
		_view.setZoom(17);
	}else{
		// es más grande que un punto
		_view.fit(_ft.getGeometry(),{padding:[20,20,20,20],duration:1000});	
	}
    
}


function mostrarVerticesSelecta(){	
	ocultarVerticesSelecta();
	_feats=_source_rel_sel.getFeatures();
	
	for(_f in _feats){
		
		_cl=_feats[_f].clone();
		_source_rel_sel_vert.addFeature(_cl);
		_cl.setStyle(new ol.style.Style({
			image: new ol.style.Circle({
				   fill: new ol.style.Fill({color: 'rgba(255,255,255,1)'}),
				   stroke: new ol.style.Stroke({color: 'rgba(0,0,256,1)',width: 2}),
				   radius: 4,
				   zIndex:1
			}),
			geometry: function (feature) {
			  // return the coordinates of the first ring of the polygon
			  const coordinates = feature.getGeometry().getCoordinates()[0];
			  return new ol.geom.MultiPoint(coordinates);
			}
		}));
	}
}

function ocultarVerticesSelecta(){	
	
	_source_rel_sel_vert.clear();
	
}


var _rmax = 228;
var _gmax = 25;
var _bmax = 55;

var _rmin = 204;
var _gmin = 255;
var _bmin = 204;

var _encuadrado='no';

var _mapaEstado ='';



function asignarRepresentacion(_data){
	
	_modo='anance'; // TODO incorporar la posibilidad de ver contenidos en lugar de avances.
	
	if(_modo=='anance'){
	
		_color='rgba(0,200,256,0.2)';
		_colorb='rgba(0,0,100,1)';
		_ancho='0.5';
		_estilo =new ol.style.Style({
	         image: new ol.style.Circle({
			       fill: new ol.style.Fill({color: _color}),
			       stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
			       radius: 6
			 }),
			 fill: new ol.style.Fill({color: _color}),
			 stroke: new ol.style.Stroke({color: _colorb,width: _ancho}),
			 zIndex:100
		});
		
		
	}else if(_modo=='contenido'){
		
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
	    
	    _estilo =new ol.style.Style({
	         image: new ol.style.Circle({
			       fill: new ol.style.Fill({color: _color}),
			       stroke: new ol.style.Stroke({color: _colors,width: 0.8}),
			       radius: 6
			 }),
			 fill: new ol.style.Fill({color: _color}),
			 stroke: new ol.style.Stroke({color: _colors,width: _ancho}),
			 zIndex:100
		});
	}
	
	return _estilo;
}


var _featurescargadas=0;

function dibujarReleMapa(){
	//REPRESENTA GEOMETRÍAS del relevamiento
	
    _source_rele.clear();
	_campo=_DataRele.representar_campo;
	_haygeom='no';
	
	for(_gn in _DataGeom){
		_featurescargadas++;
		_geo=_DataGeom[_gn];
		_val=null;
		_haygeom='si';
		
		
		for(_vn in _geo.valores){
			//console.log(_vn);
			if(_geo.valores[_vn].zz_superado=='0'){
				_val=_geo.valores[_vn][_campo+'_dato'];				
			}
		}		
		
		//console.log('+ um geometria: campo'+_campo+'. valor:'+_val);
		 //console.log('carga regeom rele');
		 //console.log(_geo.id);
		 //console.log(_geo.geotx);
		if(_geo.geotx.indexOf('inf')!=-1){continue}
		var _format = new ol.format.WKT();
		var _ft = _format.readFeature(_geo.geotx, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857',
	        id: _gn
	    });
	   // console.log('ii: '+_gn);
	 	_ft.setId(_gn);
	 	
	 	
		/*
        _feat.setProperties({
            'id':_features[elem].id
        });*/
		
		//console.log(_color);
		_ancho=1.8;
		//console.log(_res.data.capa);
		if(_DataCapa.tipogeometria=='LineString'){
			_ancho=10;
		}
	    
	    _estilo=asignarRepresentacion('_data');
	    //_ft.setStyle(_estilo);//por ahora usamos el estilo del mapa
	    
	    _ft.setProperties(_geo);
	    console.log('k'+_geo.t1);
	    if(_geo.t1!=''){
			_ft.set('name',_geo.t1);
		}else{
			_ft.set('name','s/n');
		}
		
		_ft.set('estado',_geo.n1);
		_ft.set('name',_geo.t1);
	   	_source_rele.addFeature(_ft);
	   	
	   
	   	_c='rgba(250,250,250,0.8)';
	   	if(_geo.n1=='0'){ 
		   	_c='rgba(250,0,0,0.8)';
	   	}else if(_geo.n1=='1'){
		   	_c='rgba(0,250,0,0.8)';
	   	}
	   	
	   	_st= new ol.layer.Vector({
			/*
			image: new ol.style.Circle({
		       fill: new ol.style.Fill({color: _c}),
		       stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
		       radius: 6
			}),
			fill: new ol.style.Fill({color: _color}),
			stroke: new ol.style.Stroke({color: _colorb,width: _ancho}),
			zIndex:1000,
			text: new ol.style.Text({
		    	font: '12px Calibri,sans-serif',
		    	offsetX:5,
		    	overflow: true,
		    	fill: new ol.style.Fill({
		      	color: '#000'
			    }),
			    stroke: new ol.style.Stroke({color: '#fff', width: 2})
			})
			* */
		});
		//_ft.setStyle(_st);

	}
	
	if(
		_Mapa_z!=''
		&&
		_Mapa_x!=''
		&&
		_Mapa_y!=''
	
	){
		
		
		//alert('hola');
		_coords=[_Mapa_x,_Mapa_y];
		//console.log(_coords);
		_view.setZoom(_Mapa_z);
		_view.centerOn(_coords,mapa.getSize(),[300,400]);
		
	}else if(_haygeom=='si'){
		
		_ext= _source_rele.getExtent();	
		//console.log(_ext);
		if(_encuadrado=='no'){
			_view.fit(_ext, { duration: 1000 , padding: [50, 50, 50, 50]});
			_encuadrado='si';
		}
	}
	//geometryOrExtent
	
	if(_DataRele.resol_fija>0){
		_view.setResolution(_DataRele.resol_fija);
		_z=_view.getZoom();
		_view.setMaxZoom(_z);
		_view.setMinZoom(_z);
		document.querySelector('#resol_fija [name="val"]').innerHTML=_DataRele.resol_fija;
		document.querySelector('#resol_fija').setAttribute('activo','si');
	}else{		
		_view.setMaxZoom(1000);
		_view.setMinZoom(0); 
		document.querySelector('#resol_fija [name="val"]').innerHTML='';
		document.querySelector('#resol_fija').setAttribute('activo','no');
	}
	
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
    
    
}



/*
var xmlDoc;
function dibujarCapaSuperp(_res){
	_source_rele_superp.clear();
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
			
			console.log('o'+_features);
			
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
					          zIndex:50
					        });
						}
					}
		        	_feat.setStyle(_st);
		
		        _source_rele_superp.addFeature(_feat);
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
		        _source_rele_superp.addFeature(_feat);
			}
		}
    }
}
*/

var drawL={};
var _nnelem=0;
function accionEditarCrearGeometria(_idgeom){    
	console.log('dibujando geometría id:'+_idgeom);
	_typeGeom=_DataCapa.tipogeometria;
	//_typeGeom='Polygon';//TODO resolver este hardcoded
	mapa.removeInteraction(drawL);
    drawL = new ol.interaction.Draw({
        source: _source_rel_sel,
        type: _typeGeom
    });
    
    _mapaEstado='dibujando';
    mapa.addInteraction(drawL);  
	
	
	_source_rel_sel.on('change', function(evt){
		mapa.removeInteraction(drawL);
		
		console.log('mapaestado'+_mapaEstado);
		if(_mapaEstado!='dibujando'){;return;}
		if(_mapaEstado=='terminado'){_mapaEstado.estado='nuevaGeom';return;}
		
		if(_mapaEstado.estado=='error'){
			_mapaEstado.estado='terminado';
			_source_rel_sel.clear();
			return;
		}	
		
		_features=_source_rel_sel.getFeatures();
		var _format = new ol.format.WKT();
		_geometriatx=_format.writeGeometry(_features[0].getGeometry());
		document.querySelector('#FormularioRegistro #nuevageometria').value=_geometriatx;
		
		
		_idgeom=document.querySelector('#FormularioRegistro [name="idgeom"]').value;
		console.log('actualizando atributo de nuevageom.... con _idgeom: '+_idgeom);
		document.querySelector('#FormularioRegistro #nuevageometria').setAttribute('idgeom',_idgeom);
		
		console.log('removiendo dibujante');
		_nnelem++;		
		
		_mapaEstado='terminado';	
		_clon=_features[0].clone();
		
		_source_rele.addFeature(_clon);
		_clon.setId(_idgeom);
		
		_source_rel_sel.clear();
		
		_mapaEstado='';	
	});	
	
	alert('dibuje en el mapa la nueva geometría');
}




var selecP={};
var _nnelem=0;
function accionSeleccionarGeometria(){    
	
	reiniciarSimbologia();
	//_DataCapa.tipogeometria='Polygon';
	_typeGeom=_DataCapa.tipogeometria;
	mapa.removeInteraction(drawL);
    drawL = new ol.interaction.Draw({
        source: _source_rel_sel,
        type: _typeGeom
    });
    
    _mapaEstado='dibujando';        
    mapa.addInteraction(drawL);  
	
	
	_source_rel_sel.on('change', function(evt){	
	
		if(_mapaEstado!='dibujando'){return;}
	
		if(_mapaEstado=='terminado'){_mapaEstado.estado='nuevaGeom';return;}	
		if(_mapaEstado.estado=='error'){
			_mapaEstado.estado='terminado';
			_source_rel_sel.clear();
			return;
		}	
		
		_features=_source_rel_sel.getFeatures();
		var _format = new ol.format.WKT();
		_geometria=_format.writeGeometry(_features[0].getGeometry());
		
		_nnelem++;		
		guardarNuevaGeometria(_geometria,_nnelem);
		
		_mapaEstado='terminado';	
		_clon=_features[0].clone();
		_source_rele.addFeature(_clon);
		_clon.setId('nn'+_nnelem);
		_source_rel_sel.clear();
		_mapaEstado='';
	});	
	
	alert('dibuje en el mapa la nueva geometría');
}


function dibujarNuevaUa(_idgeom){
	alert('Función en desarrollo. Paciencia. \n Si necesitás esta función comunicate con el equipo de desarrollo.');
}

function cambiarGeometria(_idgeom){
	_source_rel_sel.clear();
	
	_feat=_source_rele.getFeatureById(_idgeom);
	if(_feat!=null){
		_feat.setStyle(
			new ol.style.Style({
				image: new ol.style.Circle({
				       fill: new ol.style.Fill({color: _color}),
				       stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
				       radius: 6
				}),
				fill: new ol.style.Fill({color: 'rgba(200,200,200,0.2)'}),
				stroke: new ol.style.Stroke({lineDash: [2,6], color: 'rgba(256, 75, 8, 0.8)',width: 2}),
				zIndex:1000
			})
		);
		_ext= _feat.getGeometry().getExtent();	
		//console.log(_ext);
		_view.fit(_ext, { duration: 1000 });
	}	
	console.log('inicionado accionEditar.... con _idgeom: '+_idgeom);
	accionEditarCrearGeometria(_idgeom);
}


function activarDibujarGeomatria(){
	
	
}

function descargarMapaDXF(){
	/* esto no funciona ya en openlayers6
	mapa.once('postcompose', function(event) {
	console.log(event);
		
      var canvas = event.context.canvas;
      //exportPNGElement.href = canvas.toDataURL('image/png');
      _ext=mapa.getView().calculateExtent();
      _img={
      	'png':canvas.toDataURL('image/png'),//para que esta acción no sea frenada por insegura, los wms tienen que tener cors. las capas OSM y vectoriales no tiebnen problema.
      	'minx':_ext[0],
      	'miny':_ext[1],
      	'maxx':_ext[2],
      	'maxy':_ext[3],
      }
      
      _imgs=Array(_img);
      guardarDXFfondo(_img);
    });
    mapa.renderSync();
    */
   
	   
	  mapa.once('rendercomplete', function () {
	    const mapCanvas = document.createElement('canvas');
	    const size = mapa.getSize();
	    mapCanvas.width = size[0];
	    mapCanvas.height = size[1];
	    const mapContext = mapCanvas.getContext('2d');
	    Array.prototype.forEach.call(
	      document.querySelectorAll('.ol-layer canvas'),
	      function (canvas) {
	        if (canvas.width > 0) {
	          const opacity = canvas.parentNode.style.opacity;
	          mapContext.globalAlpha = opacity === '' ? 1 : Number(opacity);
	          const transform = canvas.style.transform;
	          // Get the transform parameters from the style's transform matrix
	          const matrix = transform
	            .match(/^matrix\(([^\(]*)\)$/)[1]
	            .split(',')
	            .map(Number);
	          // Apply the transform to the export map context
	          CanvasRenderingContext2D.prototype.setTransform.apply(
	            mapContext,
	            matrix
	          );
	          mapContext.drawImage(canvas, 0, 0);
	        }
	      }
	    );
	    if (navigator.msSaveBlob) {
	      // link download attribute does not work on MS browsers
	      navigator.msSaveBlob(mapCanvas.msToBlob(), 'map.png');
	    } else {
	  
	  		_ext=mapa.getView().calculateExtent();

	    	_img={
	      	'png':mapCanvas.toDataURL(),//para que esta acción no sea frenada por insegura, los wms tienen que tener cors. las capas OSM y vectoriales no tiebnen problema.
	      	'minx':_ext[0],
	      	'miny':_ext[1],
	      	'maxx':_ext[2],
	      	'maxy':_ext[3],
	      }
	      guardarDXFfondo(_img);
	    }
	  });
	  mapa.renderSync();


}
/*
function exportPNGElement() {
  mapa.once('postcompose', function(event) {
      var canvas = event.context.canvas;
      if (navigator.msSaveBlob) {
        navigator.msSaveBlob(canvas.msToBlob(), 'map.png');
      } else {
        canvas.toBlob(function(blob) {
          saveAs(blob, 'map.png');
        });
      }
    });
    map.renderSync();
}*/


function cargarFeatures(_idreg){
	if(_idreg==''){
		_source_rele.clear();	
	}
    _haygeom='no';
    for(var elem in _Features){
		//console.log('elem:'+elem);
		if(_idreg!='' && elem!=_idreg){console.log('no es este');continue;}
		_id_geom=_Features[elem].id_p_ref_capas_registros;
		if(_DataGeom[_id_geom] == undefined){console.log('sin geom');continue;}
		//console.log('idgeom:'+_id_geom);
		_datgeom=_DataGeom[_id_geom];
		_Features[elem]['geotx']=_datgeom.geotx;
		if(_Features[elem].geotx==''){console.log('sin geotx');continue;}
		
		_feat_vieja=_source_rele.getFeatureById(_id_geom);
		//console.log(_feat_vieja);
		_source_rele.removeFeature(_feat_vieja);
		//console.log('o');
		console.log(_Features[elem].geotx);
        var format = new ol.format.WKT();	
        _haygeom='si';        
        var _feat = format.readFeature(_Features[elem].geotx, {
            dataProjection: 'EPSG:3857',
            featureProjection: 'EPSG:3857'
        });
        
        if(_idreg!=''){
			_geom=_feat.getGeometry();
			_gex=_geom.getExtent();
			console.log(_gex);
		}
		
        _feat.setId(_Features[elem].id_p_ref_capas_registros);

        _feat.setProperties({
            'id':_Features[elem].id
        });
        
        
        
	    if(_datgeom.t1!=''){
			_feat.set('name',_datgeom.t1);
		}else{
			_feat.set('name','s/n');
		}
		
		_feat.set('estado',_datgeom.estado);
	   	
	   	
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
        
        //_feat.setStyle (_st);
        //labelStyle.getText().setText('hola');	 
        //console.log('o');
        //labelStyle.getText().setText(feature.get('name'));	 
        _feat.setStyle (labelStyle);
        
        
		_feat.getStyle().getText().setText(_feat.get('name'));	 
		//_feat.getStyle().getText().setText('H');	 
		console.log(_feat.get('estado'));
		if(_feat.get('estado')==='0'){
	   		_feat.getStyle().getFill().setColor("rgba(255,0,0,0.2)");	   		
	   		_feat.getStyle().setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: "rgba(255,0,0,0.2)"}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 12,
					   zIndex:1000,
				})
	   		);
	   		
	   	}else if(_feat.get('estado')==='1'){
	   		_feat.getStyle().getFill().setColor("rgba(0,255,100,0.2)");
	   		_feat.getStyle().setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: "rgba(0,255,100,0.2)"}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 12,
					   zIndex:1000,
				})
	   		);
	   		
	   		
	   	}else{
	   		_feat.getStyle().getFill().setColor(_color);
	   		_feat.getStyle().setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: _color}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 12,
					   zIndex:1000,
				})
	   		);
	   	}
	   	
	   	
		_source_rele.addFeature(_feat);
		//resetLabelStyle();
       // _lyrElemSrc.addFeature(_feat);

        _MapaCargado='si';
    }
	console.log(_haygeom);
	if(_haygeom=='si'){
		
		
		if(_idreg!=''){
			_ext=_gex;
		}else{
			_ext= _lyrElemSrc.getExtent();	
		}
		setTimeout(function(){
			mapa.getView().fit(_ext, { duration: 500 });
		}, 50);
		
	}else{
		_ext= _sMarco.getExtent();
		setTimeout(function(){
			mapa.getView().fit(_ext, { duration: 500 });
		}, 50);
	}
}



function reiniciarSimbologia(){
	
	
   for(var elem in _Features){
		console.log('elem:'+elem);
		
		_id_geom=_Features[elem].id_p_ref_capas_registros;
		if(_DataGeom[_id_geom] == undefined){console.log('sin geom');continue;}
		
		_datgeom=_DataGeom[_id_geom];
		_Features[elem]['geotx']=_datgeom.geotx;
		if(_Features[elem].geotx==''){console.log('sin geotx');continue;}
		
		_feat=_source_rele.getFeatureById(_id_geom);
		_feat.set('estado',_datgeom.n1);
	   	//_feat.set('name','O');
	   	
	   	
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
        _feat.setStyle (labelStyle);
        
        
		_feat.getStyle().getText().setText(_feat.get('name'));
		//_feat.getStyle().getText().setText('H');	 	 
		console.log(_feat.get('estado'));
		if(_feat.get('estado')==='0'){
	   		_feat.getStyle().getFill().setColor("rgba(255,0,0,0.2)");	   		
	   		_feat.getStyle().setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: "rgba(255,0,0,0.2)"}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 12,
					   zIndex:1000,
				})
	   		);
	   		
	   	}else if(_feat.get('estado')==='1'){
	   		_feat.getStyle().getFill().setColor("rgba(0,255,100,0.2)");
	   		_feat.getStyle().setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: "rgba(0,255,100,0.2)"}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 12,
					   zIndex:1000,
				})
	   		);
	   		
	   		
	   	}else{
	   		_feat.getStyle().getFill().setColor(_color);
	   		_feat.getStyle().setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: _color}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 12,
					   zIndex:1000,
				})
	   		);
	   	}
    }	
}

var _Mod ={};
function activarModificarGeometria(){
	
	document.querySelector('#FormularioRegistro').setAttribute('editango_geom','si');
	
	_mapaEstado='dibujando';
	_Mod = new ol.interaction.Modify({source: _source_rel_sel});
	mapa.addInteraction(_Mod);	
	mostrarVerticesSelecta();
	
	_source_rel_sel.on('change', function(evt){
		//mapa.removeInteraction(_mod);
		
		
		console.log('mapaestado'+_mapaEstado);
		mostrarVerticesSelecta();
		if(_mapaEstado!='dibujando'){return;}
		if(_mapaEstado=='terminado'){_mapaEstado.estado='nuevaGeom';return;}
		
		
		
		if(_mapaEstado.estado=='error'){
			_mapaEstado.estado='terminado';
			_source_rel_sel.clear();
			return;
		}	
		
		_features=_source_rel_sel.getFeatures();
		var _format = new ol.format.WKT();
		_geometriatx=_format.writeGeometry(_features[0].getGeometry());
		document.querySelector('#FormularioRegistro #nuevageometria').value=_geometriatx;
		
		_idgeom=document.querySelector('#FormularioRegistro [name="idgeom"]').value;
		console.log('actualizando atributo de nuevageom.... con _idgeom: '+_idgeom);
		document.querySelector('#FormularioRegistro #nuevageometria').setAttribute('idgeom',_idgeom);
		/*
		console.log('removiendo dibujante');
		_nnelem++;		
		
		_mapaEstado='terminado';	
		_clon=_features[0].clone();
		
		_source_rele.addFeature(_clon);
		_clon.setId(_idgeom);
		
		_source_rel_sel.clear();
		*/
		//_mapaEstado='';	
	});	
		
}



function desactivarModificarGeometria(){
	document.querySelector('#FormularioRegistro').setAttribute('editango_geom','no');
	mapa.removeInteraction(_Mod);	
	ocultarVerticesSelecta();
	_mapaEstado='';
	
}
