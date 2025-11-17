/*
 * 
 * definicion de variables comunes para mapas en todos los módulos
 * */



//funciones para el control del mapa
var _MapaCargado='no';
var _mapaEstado='';
var mapa={};
var vectorLayer={};
var seleccionLayer={};
var _source={};
var _souceSeleccion={};
var _AIsel='';
var _view={};
var _Dibujando='no';


_queryString = window.location.search;
_urlParams = new URLSearchParams(_queryString);
var _layers_prendidos=_urlParams.getAll('mlyr');
var _base_prendida=_urlParams.get('mba');

var _Mapa_z=_urlParams.getAll('z');
var _Mapa_x=_urlParams.getAll('x');
var _Mapa_y=_urlParams.getAll('y');





//definicion de variables para el layer de centroides
var _lyrCentSrc = new ol.source.Vector();
var _lyrCent = new ol.layer.Vector({name:'centroides'});   
var _CentSelStyle = new ol.style.Style();
var _CentStyle = new ol.style.Style();
//definicion de variables para el layer de elemento consultado
var _lyrElemSrc = new ol.source.Vector();
var _lyrElemStyle = new ol.style.Style();
var _lyrElem = new ol.layer.Vector({
	name:'elemento consultado',
	source: _lyrElemSrc
});   
_lyrElem.setStyle(_CentSelStyle);
_lyrPropiosStyle = new ol.style.Style({
	fill: new ol.style.Fill({color: 'rgba(255,102,0,0.5)'}),
	stroke: new ol.style.Stroke({color: 'rgba(255,102,0,0.8)',width: 2}),
	zIndex:1
});
var _lyrPropiosSrc = new ol.source.Vector();
var _lyrPropios = new ol.layer.Vector({
	name:'proyectos propios',
	source: _lyrPropiosSrc,
	style: _lyrPropiosStyle,
	zIndex:8
});   

var	_ExtraBaseWmsSource = new ol.source.TileWMS();//variable source utilizada por la capa extra base wms para mostar un url asignado dinámicamente.
var La_ExtraBaseWms = new ol.layer.Tile({zIndex:1000});


var _sMarco = new ol.source.Vector({projection: 'EPSG:3857'}); 


function cerrarBotones(_n){	
	_botones=document.querySelectorAll('#botonera_mapa > div');	
	for(_nb in _botones){
		if(typeof _botones[_nb]!='object'){continue;}
		if(_n==_botones[_nb]){continue;}
		_botones[_nb].setAttribute('abierto','-1');
	}
}


