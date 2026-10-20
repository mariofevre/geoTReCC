/*
 * 
 *  definicion de variables y funciones de recorte para mapas en todos los módulos
 * */



var _layerBase={};
var _CapasExtra={};

function generarBotonTamano(){
	
	_div=document.createElement('div');
	_div.setAttribute('id','menutamano');
	_div.setAttribute('abierto','-1');
	document.querySelector('#page #portamapa #botonera_mapa').appendChild(_div);
	
	_bc=document.createElement('a');
	_bc.setAttribute('id','botontamano');	
	_bc.innerHTML='<img src="./img/fullscreen.png"></a>'
	
	_bc.setAttribute('onclick','tamanoMapaCambia()');
	_div.appendChild(_bc);
	
	
	
	
}
generarBotonTamano();


var _Mapa_ext=[];

function tamanoMapaCambia(){
	_div_portam=document.querySelector('#portamapa');

		
	if(_div_portam.getAttribute('tamano')=='grandes'){
		
		_div_portam.setAttribute('tamano','normal');		
		document.querySelector('#page').insertBefore(_div_portam, document.querySelector('#page #cuadrovalores'));
		
		
		_div_m=document.querySelector('#portamapa #mapa');
		_div_m.removeAttribute('style');
	}else{
		_Mapa_ext=mapa.getView().calculateExtent();
		
		_div_portam.setAttribute('tamano','grandes');
		
		_div_m=document.querySelector('#portamapa #mapa');
		_div_m.style.width='auto';
		_div_m.style.height='4150px';
		
		document.querySelector('body').prepend(_div_portam);
	}
	
	mapa.updateSize();
	//_div_portam.style.width=1600;
	actualizarMapa();
}


function actualizarMapa(){
	setTimeout(() => { 
		mapa.updateSize();
		mapa.getView().fit(_Mapa_ext,{padding:[-200,-200,-200,-200]});
	}, 500);
}
