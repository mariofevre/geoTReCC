/*
 * 
 *  definicion de variables y funciones de links para mapas
 * */

let _Link_act;

function ActualizarLink(){
	//console.log(Date.now()-_Link_act);
	if((Date.now()-_Link_act) < 200 ){return};
	_Link_act=Date.now();
	
	_center=_view.getCenter();
	_z=_view.getZoom();
	
	_mba=document.querySelector('#page #portamapa #menucapas').getAttribute('modo');
	
	_url = new URL(window.location);
	_url.searchParams.set('x', Math.round(_center[0]));
	_url.searchParams.set('y', Math.round(_center[1]));
	_url.searchParams.set('z', Math.round(_z*100)/100);

	_url.searchParams.set('mba', _mba);

	window.history.pushState(null, '', _url.toString());
	
}



function centrarVista(){
	
	//view.centerOn();
		
	
}


function cargaBaseLink(){
	_queryString = window.location.search;
	_urlParams = new URLSearchParams(_queryString);
	var _layers_prendidos=_urlParams.getAll('mlyr');
	var _base_prendida=_urlParams.get('mba');
	
	if(_base_prendida=='OSM'){baseMapaaOSM();
	}else if(_base_prendida=='IGN'){baseMapaaIGN();
	}else if(_base_prendida=='Google'){baseMapaaGoogle();
	}else if(_base_prendida=='Bing'){baseMapaaBing();
	}else{
		baseMapaaIGN();
	}
}			
