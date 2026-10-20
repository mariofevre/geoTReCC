/*
 * 
 *  definicion de variables y funciones de localizacion de direcciones
 * */




function generarBotonLocat(){

	_div=document.createElement('div');
	_div.setAttribute('id','menulocat');
	_div.setAttribute('abierto','-1');
	_div.setAttribute('modo','IGN');
	document.querySelector('#page #portamapa #botonera_mapa').appendChild(_div);
		
	_br=document.createElement('a');
	_br.setAttribute('id','botonlocat');
	_br.innerHTML='<img src="./img/locat.png"></a>';
	_br.setAttribute('onclick','cerrarBotones(this.parentNode);this.parentNode.setAttribute("abierto",(parseInt(this.parentNode.getAttribute("abierto"))*-1))');
	_div.appendChild(_br);
	
	_bc=document.createElement('div');
	_bc.setAttribute('id','opciones');
	_div.appendChild(_bc);
	
	_op=document.createElement('input');
	_op.setAttribute('id','inputlocat');
	_op.setAttribute('placeholder','introducir sitio');
	_op.setAttribute('onkeyup','intentarLocat()');
	_bc.appendChild(_op);	
	
	_op=document.createElement('a');
	_op.setAttribute('id','limpiainputlocat');
	
	_op.setAttribute('onclick','this.previousSibling.value="";intentarLocat();this.parentNode.setAttribute("abierto",(parseInt(this.parentNode.getAttribute("abierto"))*-1))');
	_op.innerHTML='X';
	_bc.appendChild(_op);		
	
	
	_bd=document.createElement('div');
	_bd.setAttribute('id','sitios');
	_bc.appendChild(_bd);
	_bd.innerHTML='Sitios destacados';
	
	_l=document.createElement('div');
	_l.setAttribute('id','sitios_listado');
	_bd.appendChild(_l);
	
}
generarBotonLocat();


function cargarListaSitios(){
	console.log('lista sitios');
	//console.log(_IdUsu);
	if(_IdUsu<"1"){ return;}// sin usuario no se realiza esta consulta.
	document.querySelector('#page #portamapa #sitios_listado').innerHTML='';
	
	if(_DataMarco.personalizacion == undefined){return;}
	
	for(_ns in _DataMarco.personalizacion.sitios){	
		_ds=_DataMarco.personalizacion.sitios[_ns];
		_a= document.createElement('a');
		_a.innerHTML=_ds.nombre;
		_a.setAttribute('geotx',_ds.centro);
		_a.setAttribute('onclick','irAlPunto(this.getAttribute("geotx"))');
		document.querySelector('#page #portamapa #sitios_listado').appendChild(_a);
	}
}


function irAlPunto(_geotx){	
	
	_sLocat.clear();

	_ar=Array(_res.data.geo_x, _res.data.geo_y);
			  
	console.log('carga marco');
	console.log(_geotx);
	var _format = new ol.format.WKT();
	var _ft = _format.readFeature(_geotx, {
		dataProjection: 'EPSG:3857',
		featureProjection: 'EPSG:3857'
	});
	//_ft.setProperties(_geo);	    
	_sLocat.addFeature(_ft);
	console.log(_ft);				
	
	_view.fit(_ft.getGeometry());
	_view.setZoom(18);

}






var _sLocat = new ol.source.Vector({ 
	wrapX: false,   
	projection: 'EPSG:3857' 
}); 

var _styleLocat = new ol.style.Style({
		
		image: new ol.style.Icon({
			src: './img/locat.png',
		})
});

var _layerLocat = new ol.layer.Vector({
	style: _styleLocat,
	source: _sLocat,
	zIndex:100000
});

function intentarLocat(){	
	_str=document.querySelector('#inputlocat').value;
	_sLocat.clear();
	//-34.614502292985726, -58.501934390410554
	_sp=_str.split(', ');
	
	if(_sp.length==2){
		//asume coordendasa tipo google maps (lat, lon);
		_ar=[parseFloat(_sp[0]), parseFloat(_sp[1])];
		
		var parametros = {
			'codMarco': _CodMarco,
			'lat': _sp[0],
			'lon': _sp[1]
		};
		
		$.ajax({
			url:   './app_rele/app_rele_consultar_reproy.php',
			type:  'post',
			data: parametros,
			success:  function (response){   
				
				_res = preprocesarRespuestaAjax(response);
				if(_res===false){return;}
				
				
				//console.log(_res);
				_ar=Array(_res.data.geo_x, _res.data.geo_y);
						  
				console.log('carga marco');
				console.log(_res.data.geotx);
				var _format = new ol.format.WKT();
				var _ft = _format.readFeature(_res.data.geotx, {
					dataProjection: 'EPSG:3857',
					featureProjection: 'EPSG:3857'
				});
				//_ft.setProperties(_geo);	    
				_sLocat.addFeature(_ft);
				console.log(_ft);				
				
				_view.setCenter(_ar);
				_view.setZoom(18);
			}				
		});
	}	
}
