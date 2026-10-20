/**
* funciones js para controlar mapa
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
		image: new ol.style.Circle({
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
     
     var _styleCapa = [
     
		
		new ol.style.Style({
			 image: new ol.style.Circle({
				   fill: new ol.style.Fill({color: 'rgba(255,155,155,1)'}),
				   stroke: new ol.style.Stroke({color: '#ff3333',width: 0.5}),
				   radius: 3
			 }),
			 fill: new ol.style.Fill({color: 'rgba(255,155,155,0.1)'}),
			 stroke: new ol.style.Stroke({color: 'rgba(20,0,0,0.9)',width: 3})
		}),
		new ol.style.Style({
			 stroke: new ol.style.Stroke({lineDash: [4,10], color: 'rgba(255,255,255,1)',width: 1})
		})
	]
     
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
    
    _sCapa = new ol.source.Vector({        
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
		zIndex:1000
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
	
	_lyrCapa = new ol.layer.Vector({
		name:'vector',
		idcapa:'',
		style: _styleCapa,
		source: _sCapa,
		className:'multiply',
		zIndex:101
	});
	
	
	_view =	new ol.View({
      projection: 'EPSG:3857',
      center: [-7000000,-4213000],
      zoom: 5,
      minZoom:2,
      maxZoom:19	      
	});
	
	//_view.on('change:center',  ol.debounce(ActualizarLink(), 2000));// funcion en ./comun_mapa/comun_mapa_link.js
	_view.on('change:center', function(){ActualizarLink();});// funcion en ./comun_mapa/comun_mapa_link.js

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
			_lyrCapa,
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
/*
function mostrarTablaEnMapa(_tabla){
	_ExtraBaseWmsSource= new ol.source.TileWMS({
        url: 'http://190.2.6.204:8080/geoserver/geoGEC/wms',
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
*/
function cargarMapaRaster(_raster){
	
	_mod=document.querySelector('#capas_cuerpo .ref_capa.modelo');
	_clon=_mod.cloneNode(true);
	_mod.parentNode.appendChild(_clon);
	_clon.setAttribute('idcapa',_raster.id);
	_clon.setAttribute('class','ref_capa');
	_clon.querySelector('#sp_nombre').innerHTML='a';
	_clon.querySelector('#sp_desc').innerHTML='b';
	
	simbologiaRasterHTML(_raster.id);
	
	_lDato_id=_raster.id;
	_sDato = new ol.source.GeoTIFF({
		sources:[
			{
				url:_raster.fi_raster,
				overviews: [_raster.fi_raster_ovr]
			},
			
		],
		interpolate: false,
		convertToRGB: false,
		addAlpha: false
	});
	
	_sDato.normalize_ = false;
	_sDato.nodataValues_ = []; // Elimina los valores nodata problemáticos
	
	_st=[
			"interpolate",
			["linear"],
			["band", 1],
			-1000,[255, 255, 0, 1],
			0,   [0, 0, 0, 0],  
			1, [3, 255, 251, 0.5], 
			2, [255, 0, 0, 0.5],   
			3, [61, 255, 129, 0.5],   
			4, [101, 177, 57, 0.5],
			100,[255, 0, 0, 1],
			1000,[255, 255, 0, 1],
			10000,[0, 255, 255, 1]
		];
	
	
	if(_raster.simbologia_raster!=''&&_raster.simbologia_raster!=undefined){
		
		console.log(_raster.simbologia_raster);
		_st=JSON.parse(_raster.simbologia_raster);
		console.log(_st);
	}
	
	
	_lDato = new ol.layer.WebGLTile({
		title: '',
		name:'raster',
		className: 'multiply',
		zIndex:1000,
		source: _sDato,
		style: {
			color: _st
		}
	});
	_lDato.set('idcapa',_raster.id);	
	mapa.addLayer(_lDato);
	
	_sDato.getView().then((viewOptions) => {
		_view.fit(viewOptions.extent, { padding: [30, 30, 30, 30] });
	});
        
}
function cargarCapaMarco(){
	_sMarco.clear();
    //console.log(_source_ind.getFeatures());
	_haygeom='no';
	if(_DataMarco.elemento.geotx==''){return;}		
	if(_DataMarco.elemento.geotx==null){return;}		
	
		console.log('carga marco');
		var _format = new ol.format.WKT();
		var _ft = _format.readFeature(_DataMarco.elemento.geotx, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857'
	    });
	    //_ft.setProperties(_geo);	    
	   	_sMarco.addFeature(_ft);
   	 
}

/*
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
*/

/*
function desCargarElementoPropio(_codSel){
	_lyrPropiosSrc.clear();
}
*/


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
			font: '13px Calibri,sans-serif',
			overflow: true,
			offsetX:8,
			offsetY:-4,
			fill: new ol.style.Fill({
			color: '#000'
			}),
			stroke: new ol.style.Stroke({color: '#fff', width: 3})
		})
	});
}
resetLabelStyle();

var _color='rgba(0,200,256,0.2)';
var _colorb='rgba(0,0,100,1)';
var _ancho='0.5';

/*
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
*/



var _layer_rel= new ol.layer.Vector({
	name: 'relevamiento',
    className: 'multiply',
    source: _source_rele,
    zIndex:5000,
    //style:_mudoStyle,
    
	style: function(feature){
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
					   radius: 15,
					   zIndex:1000,
				})
	   		);
	   		
	   	}else if(feature.get('estado')==='1'){
	   		labelStyle.getFill().setColor("rgba(0,255,100,0.2)");
	   		labelStyle.setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: "rgba(0,255,100,0.2)"}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 15,
					   zIndex:1000,
				})
	   		);
	   		
	   		
	   	}else{
	   		labelStyle.getFill().setColor(_color);
	   		labelStyle.setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: _color}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 15,
					   zIndex:1000,
				})
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



var _source_rel_sel= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});

//layer de geometría seleccionadad para cargar datos
var _st_rel_sel=[

	new ol.style.Style({
		image: new ol.style.RegularShape({
			fill: new ol.style.Fill({color:'rgb(255, 255, 255)'}),
			
			stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 3}),
			points: 4,
			radius: 10,
			radius2: 0,
			angle: Math.PI / 4,
			zIndex: 1
		}),
	  }),
	new ol.style.Style({
		image: new ol.style.Circle({
			fill: new ol.style.Fill({color:'rgb(255, 255, 255)'}),
			stroke: new ol.style.Stroke({color:'rgb(8, 175, 217)',width: 0.8}),
			radius: 6,
			zIndex: 2
		})
	}),
	new ol.style.Style({
		image: new ol.style.RegularShape({
			fill: new ol.style.Fill({color:'rgb(8, 175, 217)'}),
			stroke: new ol.style.Stroke({color: 'rgb(255, 255, 255)',width: 0.8}),
			points: 4,
			radius: 10,
			radius2: 0,
			angle: Math.PI / 4,
			zIndex: 3
		}),
	  }),
	new ol.style.Style({
		image: new ol.style.RegularShape({
			fill: new ol.style.Fill({color:'rgb(255, 255, 255)'}),
			stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 1}),
			points: 4,
			radius: 4,
			radius2: 0,
			angle:0
		}),
	 })
];

var _layer_rel_sel= new ol.layer.Vector({
    source: _source_rel_sel,
    style: _st_rel_sel,
	zIndex:2000000
});
//console.log('1');
mapa.addLayer(_layer_rel_sel);
mapa.on('click', (evt) => {consultaPuntoRel(evt,evt.pixel);});
	
function consultaPuntoRel(_ev,_pixel) { 
	if(_AccMapa=='marca'){
		
		_source_rel_sel.clear();
		document.querySelector('#consulta #cartel_marcar').removeAttribute('activo');
		_geotx='POINT('+_ev.coordinate[0]+' '+_ev.coordinate[1]+')';
		document.querySelector('#consulta [name="geotx"]').value=_geotx;
		_format = new ol.format.WKT();
		
		_ft = _format.readFeature(_geotx, {
			dataProjection: 'EPSG:3857',
			featureProjection: 'EPSG:3857'
		});
		
		_source_rel_sel.addFeature(_ft);
		
		
	}else if(_AccMapa=='selecciona'){
	
		_sel = mapa.forEachFeatureAtPixel(_ev.pixel, function(feature, layer){
			
			console.log(_ev.pixel);
			if(layer.get('name')=='vector'){
				_sel={
					'name':layer.get('name'),
					feat:feature,
					idreg:feature.getId(),
					idcapa:layer.get('idcapa')
				};
				return _sel;
			}
	    });

	    
	    if(_lDato_id!=''){
			_sel={
				'name':'raster',
				coord:_ev.coordinates,
				valor:_lDato.getData(_pixel)[0],
				'idcapar':_lDato_id
			};			
	    }
	    console.log(_sel);
	    if(_sel==undefined){
			return;
		}else if(_sel.name=='vector'){
			fijarObjeto(_sel.idcapa,_sel.idreg);
			return;
		}else if(_sel.name=='raster'){
			caracterizarPixel(_sel.idcapar,_sel.valor,_sel.coord);
			return;
		}
	}
}

function generarFeatureSelecta(_idgeom){	
	
	_source_rel_sel.clear();
	
	_ft_orig=_source_rele.getFeatureById(_idgeom);
	
    if(_ft_orig==null){
		console.log('¡No se encontró el feature con id:'+_idgeom);
		return;
	}

	
	_ft=_ft_orig.clone();
	_ft.setId(_idgeom);
	_source_rel_sel.addFeature(_ft);		
	 
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
}

var _rmax = 228;
var _gmax = 25;
var _bmax = 55;

var _rmin = 204;
var _gmin = 255;
var _bmin = 204;

var _encuadrado='no';

var _mapaEstado ='';


function simbologiaHTML(_idcapa){
	if(_EstilosCapa[_idcapa]==undefined){return;}
	if(_EstilosCapa[_idcapa]['condiciones']==undefined){return;}
	
	_con=_EstilosCapa[_idcapa]['condiciones'];
	_le=document.querySelector('.ref_capa[idcapa="'+_idcapa+'"] #leyenda');
	_le.innerHTML='';
	for(_cn in _con){
		//console.log('.ref_capa[idcapa="'+_idcapa+'"] #leyenda');
		_r=document.createElement('div');
		_le.appendChild(_r);
		_r.setAttribute('class','regla');
		_s=document.createElement('div');
		_s.setAttribute('class','simbolo');
		_r.appendChild(_s);
		_s.style.border=_con[_cn].anchoTrazo+'px '+_con[_cn].colorTrazo+' solid';
		_s.style.backgroundColor=_con[_cn].colorRelleno;						
		_e=document.createElement('label');
		_r.appendChild(_e);
		//console.log(_con[_cn]);
		_e.innerHTML=' : '+_con[_cn].nombre;

	}
}


function simbologiaRasterHTML(_idcapa){
	//console.log(_idcapa);
	//console.log(_DataCapasRaster[_idcapa]);
	//console.log(_DataCapasRaster[_idcapa].etiquetas_raster);
	_st=JSON.parse(_DataCapasRaster[_idcapa].etiquetas_raster);
	//console.log(_st);
	_le=document.querySelector('.ref_capa[idcapa="'+_idcapa+'"] #leyenda');
	_le.innerHTML='';
	for(_n in _st){
		_r=document.createElement('div');
		_le.appendChild(_r);
		_r.setAttribute('class','regla');
		_s=document.createElement('div');
		_s.setAttribute('class','simbolo');
		_r.appendChild(_s);
		_s.style.border='1px #000 solid';
		_s.style.backgroundColor='rgba('+_st[_n].color+')';
		_e=document.createElement('label');
		_r.appendChild(_e);
		_e.innerHTML=' : '+_st[_n].tx;

	}
		
		
	
	if(_EstilosCapa[_idcapa]==undefined
		||
		_EstilosCapa[_idcapa]==''){return;}
	if(_EstilosCapa[_idcapa]['condiciones']==undefined
		||
		_EstilosCapa[_idcapa]['condiciones']==''
		){return;}
	
	
	_con=_EstilosCapa[_idcapa]['condiciones'];
	_le=document.querySelector('.ref_capa[idcapa="'+_idcapa+'"] #leyenda');
	_le.innerHTML='';
	for(_cn in _con){
		//console.log('.ref_capa[idcapa="'+_idcapa+'"] #leyenda');
		
	}
}


function borrarCapas(){
	_divs=document.querySelectorAll('#capas #capas_cuerpo .ref_capa');
	for(_n in _divs){
		if(typeof _divs[_n] != 'object'){continue;}
		if(_divs[_n].className=='modelo ref_capa'){continue;}
		_divs[_n].parentNode.removeChild(_divs[_n]);
	}
	
	for(_cn in _DataCapas){
		_DataCapas[_cn]['cargada']='no';		
	}
	
	_div=document.querySelector('#colecciones #colecciones_listado');
	_div.innerHTML='';
	
	console.log('borrando '+typeof _sCapa.clear);
	if(typeof _sCapa.clear != "undefined"){
		_sCapa.clear();
	}
	
	
	console.log('borrando '+typeof _sDato.clear);
	if(typeof _sDato.clear != "undefined"){
		_sDato.clear();
		mapa.removeLayer(_lDato);
		console.log('?ok?');
	}
}

function mapearCapa(_idcapa){


	_mod=document.querySelector('#capas_cuerpo .ref_capa.modelo');
	_clon=_mod.cloneNode(true);
	_mod.parentNode.appendChild(_clon);
	_clon.setAttribute('idcapa',_idcapa);
	_clon.setAttribute('class','ref_capa');
	_clon.querySelector('#sp_nombre').innerHTML=_DataCapas[_idcapa].nombre;
	_clon.querySelector('#sp_desc').innerHTML=_DataCapas[_idcapa].descripcion;
	
	if(_DataCapas[_idcapa].modo_fusion!=''){
		_class=_DataCapas[_idcapa].modo_fusion;
		_lyrCapa.className=_class;//esto no fucnoina, no se puede modificar el layer creado, hay que volver a generarlo
		console.log(_class);
	}
	
	
	_sldtx=_DataCapas[_idcapa].simbologia;
	if(_sldtx==null){
		_sldtx=_DataCapas[_idcapa].sld;
		_parser = new DOMParser();
		_EstilosCapa[_idcapa]={
			'sld':_parser.parseFromString(_sldtx, "text/xml")
		};
	}
	//console.log(_DataCapas[_idcapa].sld);
	
	if(_DataCapas[_idcapa].coleccion!=undefined){
		if(_DataCapas[_idcapa].coleccion.simbologia!=undefined){
			if(_DataCapas[_idcapa].coleccion.simbologia!=''){
				_sldtx=_DataCapas[_idcapa].coleccion.simbologia;	
				_parser = new DOMParser();
				if (_sldtx != ''){
					_EstilosCapa[_idcapa]={
						'sld':_parser.parseFromString(_sldtx, "text/xml")
					};
				}
			}
		}
	}
	_lyrCapa.set('idcapa',_idcapa);
	

	for(_idr in _DataCapas[_idcapa].registros){
		_geotx = _DataCapas[_idcapa].registros[_idr].geotx;
		var format = new ol.format.WKT();	
		var _feat = format.readFeature(_geotx, {
			dataProjection: 'EPSG:3857',
			featureProjection: 'EPSG:3857',
			
		});
		_feat.setId(_idr);
		_sCapa.addFeature(_feat);
	}
	
	
	
	_view.fit(_feat.getGeometry(),{padding: [30 , 30, 30, 30]});
	
	if(_DataPanel.z!=null){
		if(_DataPanel.z!=''){
			_view.setZoom(_DataPanel.z);
		}
	}
	//console.log('ooo');
	simbolizarCapa(_idcapa);
}

	
function simbolizarCapa(_idcapa){	
	
	condicionesSimbologiaInput(_idcapa);
	
	simbologiaHTML(_idcapa);
	
	
	_features=_sCapa.getFeatures();
	for(_fn in _features){
		_idreg=_features[_fn].getId();
		_feat=_features[_fn];
		//console.log(_idreg);
        _datasec=_DataCapas[_idcapa].registros[_idreg];

		
     	_c= hexToRgb('#555555');
        _n=(100 - (50) )/100;
        
        _rgba='rgba('+_c.r+', '+_c.g+', '+_c.b+', '+_n+')';

        _st= new ol.style.Style({
          fill: new ol.style.Fill({
            color: _rgba

          }),
          stroke: new ol.style.Stroke({
            color: '#000000',
            width: '1'
          }),
          image:new ol.style.Circle({
					   fill: new ol.style.Fill({color: _rgba}),
					   stroke: new ol.style.Stroke({
							color: '#000000',
							width: '1'
					}),
					   radius: 5
					})
        });
        
        
        _campoMM='';
        _campomm='';
        _campoig='';
        
        _estiloAsignado='no';
        
        
        for(_nc in _EstilosCapa[_idcapa].condiciones){
		
			for(_k in _datasec){
				_kref=_k.replace('texto','nom_col_text');
				_kref=_kref.replace('numero','nom_col_num');
				
				//console.log(_EstilosCapa[_idcapa].condiciones[_nc]);
				
				//console.log(_k +' - '+_kref+' - '+_Capa[_kref] +' vs ' +_condiciones[_nc].campoMM);
				
				if(_k == _EstilosCapa[_idcapa].condiciones[_nc].campoIg){
					_campoig =_k; 
					//console.log('eureka. ahora: '+_campoig);
					break;
				}		
			}
			
						
			for(_k in _datasec){
				_kref=_k.replace('texto','nom_col_text');
				_kref=_kref.replace('numero','nom_col_num');
				
				//console.log(_k +' - '+_kref+' - '+_Capa[_kref] +' vs ' +_condiciones[_nc].campoMM);
				
				if(_k == _EstilosCapa[_idcapa].condiciones[_nc].campoMM){
					_campoMM =_k; 
					//console.log('eureka. ahora: '+_campoMM);
					break;
				}		
			}
			
			for(_k in _datasec){
				
				_kref=_k.replace('texto','nom_col_text');
				_kref=_kref.replace('numero','nom_col_num');
				//console.log(_kref+' - '+_Capa[_kref] +' vs ' +_campoMM);
				if(_k == _EstilosCapa[_idcapa].condiciones[_nc].campomm){
					_campomm =_k; 
					//console.log('eureka. ahora: '+_campomm);
					break;
				}	
						
			}
			
			_fts=_DataCapas[_idcapa].registros;
			if((_campoMM!=''&& _campomm!='')|| _campoig!=''||_EstilosCapa[_idcapa].condiciones[_nc].tipo=='default'){
		
				//console.log(Number(_fts[_idreg][_campoMM]) +'>= '+Number(_condiciones[_nc].valorMM));        	
				//console.log(Number(_fts[_idreg][_campomm]) +'< '+Number(_condiciones[_nc].valormm));
				//console.log(_campoig);
				//console.log(_fts[_idreg]);
				//console.log(_EstilosCapa[_idcapa].condiciones[_nc].tipo);
				//console.log(_fts[_idreg][_campoig] +' = '+_EstilosCapa[_idcapa].condiciones[_nc].valorIg);
				if(
					(
						Number(_fts[_idreg][_campoMM]) >= Number(_EstilosCapa[_idcapa].condiciones[_nc].valorMM)
						&&
						Number(_fts[_idreg][_campomm]) <  Number(_EstilosCapa[_idcapa].condiciones[_nc].valormm)
					)
					||
						_fts[_idreg][_campoig] == _EstilosCapa[_idcapa].condiciones[_nc].valorIg
					||
					(
						_EstilosCapa[_idcapa].condiciones[_nc].tipo=='default'
						&&
						_estiloAsignado=='no'
					)
				){
					
					if(_EstilosCapa[_idcapa].condiciones[_nc].tipo!='default'){
						_estiloAsignado='si';
					}
					_c= hexToRgb(_EstilosCapa[_idcapa].condiciones[_nc].colorRelleno);
					
					_n=(1 - (_EstilosCapa[_idcapa].condiciones[_nc].transparenciaRelleno));
					_rgba='rgba('+_c.r+', '+_c.g+', '+_c.b+', '+_n+')';
			
					//console.log(_rgba);
					_st= new ol.style.Style({
						
					  fill: new ol.style.Fill({
						color: _rgba		
					  }),
					  stroke: new ol.style.Stroke({
						color: _EstilosCapa[_idcapa].condiciones[_nc].colorTrazo,
						width: _EstilosCapa[_idcapa].condiciones[_nc].anchoTrazo
					  }),
					  image:new ol.style.Circle({
						   fill: new ol.style.Fill({color: _rgba}),
						   stroke: new ol.style.Stroke({
								color: _EstilosCapa[_idcapa].condiciones[_nc].colorTrazo,
								width: _EstilosCapa[_idcapa].condiciones[_nc].anchoTrazo
						}),
						   radius: 5
						})
					});
					
				}
			}
		}
		
    	_feat.setStyle (_st);
	}
}


function condicionesSimbologiaInput(_idcapa){
	
	_condiciones=Array();
    
    if(_EstilosCapa[_idcapa]==undefined||_EstilosCapa[_idcapa]==''){
		_EstilosCapa[_idcapa]={'condiciones':{}}
		return;	
	}
    

	_rules= _EstilosCapa[_idcapa].sld.getElementsByTagName("Rule");
	
	_EstilosCapa[_idcapa]['condiciones']=Array();
	
   // console.log(_rules);
	for(_nr in _rules){
		_larule=_rules[_nr];
		if(typeof _larule != 'object'){continue;}
		
		// POR DEFECTO
		_nombre='Otros';
		colorRelleno='#fff';
		transparenciaRelleno='0';
		colorTrazo='#000';
		anchoTrazo='3';
		tipo='default';
		_campo='';
		_valorIgual=''
		_valorMayor='';
		_valorMenor='';
		// -----------
		
		//console.log(_larule);
		//console.log(_larule.getElementsByTagName("Fill")[0]);
		xmlFill = _larule.getElementsByTagName("Fill")[0];		
		for(var node in xmlFill.childNodes){
			//console.log(xmlFill.childNodes[node]);
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

		xmlStroke = _larule.getElementsByTagName("Stroke")[0];
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
		
		_iguala = _larule.getElementsByTagName("ogc:PropertyIsEqualTo")[0]; 
		
		_mayor = _larule.getElementsByTagName("ogc:PropertyIsGreaterThanOrEqualTo")[0]; 
		_menor = _larule.getElementsByTagName("ogc:PropertyIsLessThan")[0]; 
			
		if(_iguala != null){		   
			tipo='igual';
			for(var node in _iguala.childNodes){
				//console.log(_iguala.childNodes[node].nodeName);
				if(_iguala.childNodes[node].nodeName == "ogc:PropertyName"){
					_campo = _iguala.childNodes[node].textContent;
				}
				if(_iguala.childNodes[node].nodeName == "ogc:Literal"){
					_valorIgual = _iguala.childNodes[node].textContent;
				}
			 }			 
		}else if(_mayor != null && _menor != null){
			tipo='rango';
			 for(var node in _mayor.childNodes){
				//console.log(_mayor.childNodes[node].nodeName);
				if(_mayor.childNodes[node].nodeName == "ogc:PropertyName"){
					_campo = _mayor.childNodes[node].textContent;
				}
				if(_mayor.childNodes[node].nodeName == "ogc:Literal"){
					_valorMayor = _mayor.childNodes[node].textContent;
				}
			 }
			
			 for(var node in _menor.childNodes){
			 
				if(_menor.childNodes[node].nodeName == "ogc:Literal"){
					_valorMenor = _menor.childNodes[node].textContent;
				}
			 }
		}
		
		
		
		xmlNom = _larule.getElementsByTagName("Name")[0];		
		if(xmlNom!=null){
			_nombre= xmlNom.innerHTML;
		}
		
		_con={
			'nombre':_nombre,  
			'tipo':tipo,  
			'campoIg':_campo,
			'valorIg':_valorIgual,
			'campoMM':_campo,
			'valorMM':_valorMayor,
			'campomm':_campo,
			'valormm':_valorMenor,
			'colorRelleno':colorRelleno,
			'transparenciaRelleno':1-Number(transparenciaRelleno),
			'colorTrazo':colorTrazo,
			'anchoTrazo':anchoTrazo
		}
					 
		_EstilosCapa[_idcapa]['condiciones'].push(_con);
	}

}



function cargarFeatures(_idreg){
	if(_idreg==''){
		_lyrElemSrc.clear();	
	}
    _haygeom='no';
    for(var elem in _Features){
		
		if(_idreg!='' && elem!=_idreg){continue;}
		if(_Features[elem].geotx!=''){continue;}
		
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
		
        _feat.setId(_Features[elem].id);

        _feat.setProperties({
            'id':_Features[elem].id
        });
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
        _feat.setStyle (_st);
		
		
        _lyrElemSrc.addFeature(_feat);

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
			//mapa.getView().fit(_ext, { duration: 1000 });
		}, 50);
		
	}else{
		_ext= _sMarco.getExtent();
		setTimeout(function(){
			//mapa.getView().fit(_ext, { duration: 1000 });
		}, 50);
	}
}

function caracterizarPixel(_idCapaR,_valor,_coord){
	
	_div=document.querySelector('#portamapa #leyenda_flotante_seleccion');
	_div.parentNode.removeChild(_div);
		
	_div2=document.createElement('div');
	_div2.setAttribute('id','leyenda_flotante_seleccion');
	document.querySelector('#portamapa').appendChild(_div2);	
	
	_a=document.createElement('a');
	_div2.appendChild(_a);
	_a.innerHTML='hacer zoom';
	
	_a.setAttribute('onclick','_view.fit(_source_rele.getFeatures()[0].getGeometry(),{duration:2000})');
	//_a.setAttribute('onclick','_fs_source_rel_sel.getFeatures();_view.fit(_fs[0].getGeometry())');
	
	console.log(_idCapaR);
	console.log(_DataCapasRaster[_idCapaR]);
	
	if(_DataCapasRaster[_idCapaR].etiquetas_raster!=null&&_DataCapasRaster[_idCapaR].etiquetas_raster!=''){
		_etis=JSON.parse(_DataCapasRaster[_idCapaR].etiquetas_raster);
	}
	
	_e='sin etiqueta';
	if(_etis[_valor]!=undefined){
		_e=_etis[_valor].tx;
	}
	
	_div3=document.createElement('div');
	_l=document.createElement('label');
	_div3.appendChild(_l);
	_l.innerHTML=_valor+': ';
	
	_s=document.createElement('span');
	_div3.appendChild(_s);
	_s.innerHTML=_e;
	
	
	_div2.appendChild(_div3);
	console.log(_div2);
	
	setTimeout(function(){document.querySelector('#portamapa #leyenda_flotante_seleccion').setAttribute('activo','si')}, 50);
}
	
function fijarObjeto(_idcapa,_idr){
	
	_sels=document.querySelectorAll('#colecciones #colecciones_listado [selecto="si"]');
	for(_sn in _sels){
		if(typeof _sels[_sn] != 'object'){continue;}
		 _sels[_sn].removeAttribute('selecto');
	}
	
	_sel=document.querySelector('#colecciones #colecciones_listado [idreg="'+_idr+'"]');
	_sel.setAttribute('selecto','si');
	_sel.scrollIntoView( {behavior: "smooth",block:"center"});
	//console.log(_sel);
		
	_div=document.querySelector('#portamapa #leyenda_flotante_seleccion');
	_div.parentNode.removeChild(_div);
		
	_div2=document.createElement('div');
	_div2.setAttribute('id','leyenda_flotante_seleccion');
	document.querySelector('#portamapa').appendChild(_div2);	
	
	_a=document.createElement('a');
	_div2.appendChild(_a);
	_a.innerHTML='hacer zoom';
	
	_a.setAttribute('onclick','_view.fit(_source_rele.getFeatures()[0].getGeometry(),{duration:2000})');
	//_a.setAttribute('onclick','_fs_source_rel_sel.getFeatures();_view.fit(_fs[0].getGeometry())');
	
	_tns=['1','2','3','4','5'];
	for(_tn in _tns){
		_camponombre='nom_col_text'+_tns[_tn];
		_campo='texto'+_tns[_tn];
		if(_DataCapas[_idcapa][_camponombre]!=''){
			_div3=document.createElement('div');
			_l=document.createElement('label');
			_div3.appendChild(_l);
			_div3.setAttribute('campo',_campo);
			_l.innerHTML=_DataCapas[_idcapa][_camponombre]+':';
			
			_s=document.createElement('span');
			_div3.appendChild(_s);
			_s.innerHTML=_DataCapas[_idcapa].registros[_idr][_campo];
			
			_div2.appendChild(_div3);
		}
	}
	
	_nns=['1','2','3','4','5'];
	for(_nn in _nns){
		_camponombre='nom_col_num'+_tns[_tn];
		_campo='numero'+_tns[_tn];
		if(_DataCapas[_idcapa][_camponombre]!=''){
			_div3=document.createElement('div');
			_l=document.createElement('label');
			_div3.appendChild(_l);
			_l.innerHTML=_DataCapas[_idcapa][_camponombre]+':';
			
			_s=document.createElement('span');
			_div3.appendChild(_s);
			_s.innerHTML=_DataCapas[_idcapa].registros[_idr][_campo];
			
			_div2.appendChild(_div3);
		}
	}
		
	setTimeout(function(){document.querySelector('#portamapa #leyenda_flotante_seleccion').setAttribute('activo','si')}, 50);
	
	_source_rele.clear();
	_wkt=_DataCapas[_idcapa].registros[_idr].geotx;

	
	var _format = new ol.format.WKT();
	var _ft = _format.readFeature(_wkt, {
		dataProjection: 'EPSG:3857',
		featureProjection: 'EPSG:3857'
	});
	_ft.set('name',_DataCapas[_idcapa].registros[_idr]['texto1']);
	_source_rele.addFeature(_ft);
}
