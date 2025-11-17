/*
 * 
 *  definicion de variables y funciones de recorte para mapas en todos los módulos
 * */

var _drawRecorte={};    
var _recortando='no';   

var _styleRecorte = new ol.style.Style({
	stroke: new ol.style.Stroke({color : '#08afd9', width : 1, lineDash: [3,2]}),
	fill: new ol.style.Fill({color : 'rgba(255,150,150,0.0)'})
});

var _sourceRecorte = new ol.source.Vector({        
	projection: 'EPSG:3857',
	wrapX: false // sin este set no funciona el draw ¨\:i/¨
}); 



var _layerRecorte = new ol.layer.Vector({
	style: _styleRecorte,
	source: _sourceRecorte,
	zIndex:9999
});


function generarBotonRecorte(){
	if(document.querySelector('#page #portamapa #botonera_mapa')==null){return;}
	_br=document.createElement('a');
	_br.setAttribute('id','botonrecorte');
	_br.innerHTML='<img src="./comun_img/recorte.png"></a>'
	_br.setAttribute('onclick','iniciaRecorte();');	
	document.querySelector('#page #portamapa #botonera_mapa').appendChild(_br);	
}
generarBotonRecorte();

 
function iniciaRecorte(){
	
	_estado=document.querySelector('#botonrecorte').getAttribute('estado');
	console.log(_estado);
	if(_estado=='recortado'){	
		_estado=document.querySelector('#botonrecorte').removeAttribute('estado');	
		_RecorteDeTrabajo=null;
		registraRecorteSession();
		
		_sourceRecorte.clear();
		mapa.removeInteraction(_drawRecorte);	
		_inputtext=document.querySelector('#buscadatos #inputbuscadatos');
		buscadatos(_inputtext,event); // en ./sis_usuarios/usu_acceso_js_buscadatos.js
		return;
	}
	
	_RecorteDeTrabajo=null;
	_sourceRecorte.clear();
	mapa.removeInteraction(_drawRecorte);

	_recortando='si';	
	_drawRecorte = new ol.interaction.Draw({
		source: _sourceRecorte,
		type: 'Circle',
		geometryFunction:  ol.interaction.Draw.createBox(),
	});
	mapa.addInteraction(_drawRecorte);	
}

_sourceRecorte.on('change', function(evt){		
	if(_recortando=='no'){return;}
	_recortando='no';
	_ext= _layerRecorte.getExtent();	
    
	mapa.removeInteraction(_drawRecorte);
	
	_features=_sourceRecorte.getFeatures();
	
	_cu=_features[0].getGeometry();
	_RecorteDeTrabajo=_cu.getExtent();	
	
    _inputtext=document.querySelector('#buscadatos #inputbuscadatos');
	buscadatos(_inputtext,event); // en ./sis_usuarios/usu_acceso_js_buscadatos.js
	mapa.getView().fit(_RecorteDeTrabajo, { duration: 1000 });	
	//console.log(_RecorteDeTrabajo);
	
	document.querySelector('#botonrecorte').setAttribute('estado','recortado');
	
    registraRecorteSession();
});

function registraRecorteSession(){
    var _parametros = {
        'RecorteDeTrabajo':_RecorteDeTrabajo
    };
    $.ajax({
        url:   './sis_usuarios/usu_definerecorte_ajax.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg){alert(_res.mg[_nm]);}
        }
	});
}

function cargaRecorteSession(){
	_recortando='si';
	_c=_RecorteDeTrabajo;
    _wkt='POLYGON(('+_c[0]+' '+_c[1]+', '+_c[2]+' '+_c[1]+', '+_c[2]+' '+_c[3]+', '+_c[0]+' '+_c[3]+', '+_c[0]+' '+_c[1]+'))';
	var _format = new ol.format.WKT();
	var _ft = _format.readFeature(_wkt, {
        dataProjection: 'EPSG:3857',
        featureProjection: 'EPSG:3857'
    });	    
   	_sourceRecorte.addFeature(_ft);
}
