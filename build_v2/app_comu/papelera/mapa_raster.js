

//var _mapa={};
//var _sBase={};
//var _lBase={};
//var _view={};
//var _lDato={};
//var _sDato={};

	
	
function cargarMapaBase2(){
		
	_sBase=new ol.source.OSM({crossOrigin: 'anonymous'});
	
	_lBase = new ol.layer.Tile({
		zIndex:0,
		className: 'gris',
		crossOrigin: 'anonymous',
		source:_sBase
	});	

	_view =	new ol.View({
          projection: 'EPSG:3857',
		  center: [-7000000,-4213000],
		  zoom: 2,
		  minZoom:2,
		  maxZoom:19
    });
    
	_mapa = new ol.Map({
		layers: [
			_lBase
		],
		target: 'mapa',
		view: _view
	});

	_mapa.on('click', (evt) => {
	   consultaPunto(evt.pixel,evt);     
	});
	


	_mod=document.querySelector('#capas_cuerpo .ref_capa.modelo');
	_clon=_mod.cloneNode(true);
	_mod.parentNode.appendChild(_clon);
	_clon.setAttribute('idcapa','');
	_clon.setAttribute('class','ref_capa');
	_clon.querySelector('#sp_nombre').innerHTML='a';
	_clon.querySelector('#sp_desc').innerHTML='b';
	
	

		
	//document.getElementById('mapa').innerHTML='';
    //document.getElementById('mapa').setAttribute('estado','activo');

    
	
		
			
	
}
	
		 
function cargarMapaBase(){
		
	_sBase=new ol.source.OSM({crossOrigin: 'anonymous'});
	
	_lBase = new ol.layer.Tile({
		zIndex:0,
		className: 'gris',
		crossOrigin: 'anonymous',
		source:_sBase
	});	

	_view =	new ol.View({
          projection: 'EPSG:3857',
		  center: [-7000000,-4213000],
		  zoom: 5,
		  minZoom:2,
		  maxZoom:19
    });
    
	_mapa = new ol.Map({
		layers: [
			_lBase
		],
		target: 'mapa',
		view: _view
	});

	_mapa.on('click', (evt) => {
	   consultaPunto(evt.pixel,evt);     
	});
};

  
function consultaPunto(pixel,_ev){

	const data = _lDato.getData(pixel);
	if (!data) {
		return;
	}
	
	alert('valor: '+(Math.round(data[0]*100))+' %');
}


function cargarIndicador(_in){
	console.log("cargando "+_in);
	console.log(_Base.I[_in]);
	_sDato = new ol.source.GeoTIFF({
		sources:[
			{
				url:_Base.I[_in].url,
				overviews: [_Base.I[_in].url_ovr],
				normalize: false, // esto parece que no fucniona y hay que definirlo después.
			},
			
		],
		interpolate: false,
		convertToRGB: false,
		addAlpha: false
	});
	console.log(_sDato);

	_sDato.normalize_ = false;
	_sDato.nodataValues_ = []; // Elimina los valores nodata problemáticos

	
	_lDato = new ol.layer.WebGLTile({
		title: _Base.I[_in].nombre,
		name:_Base.I[_in].nombre,
		className: 'multiply',
		source: _sDato,
		style: {
			color: [
				'interpolate',
				['linear'],
				['band', 1],
				0,   [0, 0, 0, 0],  
				0.1, [0, 50, 255, 0.3], 
				0.5, [0, 50, 255, 0.6],   
				1,   [0, 50, 255, 1]   
			]
		}
	});
	
	_mapa.addLayer(_lDato);
	
}
	
	
function cargarIndicador2(_in){
	
	
	_mod=document.querySelector('#capas_cuerpo .ref_capa.modelo');
	_clon=_mod.cloneNode(true);
	_mod.parentNode.appendChild(_clon);
	_clon.setAttribute('idcapa','');
	_clon.setAttribute('class','ref_capa');
	_clon.querySelector('#sp_nombre').innerHTML='a';
	_clon.querySelector('#sp_desc').innerHTML='b';
	
	
	
	
	_sDato = new ol.source.GeoTIFF({
		sources:[
			{
				//url:_raster.fi_raster,
				url:'./app_comu/papelera/prueba.tif',
				overviews:['./app_comu/papelera/04_cobertura.tif.ovr'],
				//overviews: _raster.fi_raster_ovr,
				//normalize: false, // esto parece que no fucniona y hay que definirlo después.
			},
			
		],
		//interpolate: false,
		//convertToRGB: false,
		//addAlpha: false
	});

	_sDato.normalize_ = false;
	_sDato.nodataValues_ = []; // Elimina los valores nodata problemáticos

	
	_lDato = new ol.layer.WebGLTile({
		//title: '',
		//name:'',
		//className: 'multiply',
		zIndex:10000,
		source: _sDato,
		style: {
			color: [
				'interpolate',
				['linear'],
				['band', 1],
				0,   [0, 0, 0, 0],  
				0.1, [0, 50, 255, 0.3], 
				0.5, [0, 50, 255, 0.6],   
				1,   [0, 50, 255, 1]   
			]
		}
	});
	
	
	
	
	_sDato = new ol.source.GeoTIFF({
		sources:[
			{
				url:_Base.I[_in].url,
				overviews: [_Base.I[_in].url_ovr],
				normalize: false, // esto parece que no fucniona y hay que definirlo después.
			},
			
		],
		interpolate: false,
		convertToRGB: false,
		addAlpha: false
	});
	console.log(_sDato);

	_sDato.normalize_ = false;
	_sDato.nodataValues_ = []; // Elimina los valores nodata problemáticos

	
	_lDato = new ol.layer.WebGLTile({
		title: _Base.I[_in].nombre,
		name:_Base.I[_in].nombre,
		className: 'multiply',
		source: _sDato,
		style: {
			color: [
				'interpolate',
				['linear'],
				['band', 1],
				0,   [0, 0, 0, 0],  
				0.1, [0, 50, 255, 0.3], 
				0.5, [0, 50, 255, 0.6],   
				1,   [0, 50, 255, 1]   
			]
		}
	});
	
	_mapa.addLayer(_lDato);	
	
	
	
	
	
	
	//_sldtx=_DataCapas[_idcapa].simbologia;

	

	
	/*
	if(_DataPanel.z!=null){
		if(_DataPanel.z!=''){
			_view.setZoom(_DataPanel.z);
		}
	}*/
	//console.log('ooo');
	//simbolizarCapa(_idcapa);
	
		
	//document.getElementById('mapa').innerHTML='';
    //document.getElementById('mapa').setAttribute('estado','activo');

    
	
}		
	
    
	
	
function cargarVariable(_var){	
}
