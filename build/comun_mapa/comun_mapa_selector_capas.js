/*
 * 
 *  definicion de variables y funciones de recorte para mapas en todos los módulos
 * */



var _layerBase={};
var _CapasExtra={};

function generarBotonCapas(){
	
	if(document.querySelector('#page #portamapa #botonera_mapa') == null){return;}
	 
	_div=document.createElement('div');
	_div.setAttribute('id','menucapas');
	_div.setAttribute('abierto','-1');
	_div.setAttribute('modo','IGN');
	document.querySelector('#page #portamapa #botonera_mapa').appendChild(_div);
	
	
	_bc=document.createElement('a');
	_bc.setAttribute('id','botoncapas');	
	_bc.innerHTML='<img src="./img/capas.png"></a>'
	_bc.setAttribute('onclick','cerrarBotones(this.parentNode);this.parentNode.setAttribute("abierto",(parseInt(this.parentNode.getAttribute("abierto"))*-1))');
	_div.appendChild(_bc);
	
	_bc=document.createElement('div');
	_bc.setAttribute('id','opciones');
	_div.appendChild(_bc);
	
	_op=document.createElement('a');
	_op.setAttribute('onclick','baseMapaaIGN()');
	_op.setAttribute('modo','IGN');
	_op.innerHTML="callejero nacional: IGN"; 
	_bc.appendChild(_op);
	
	_op=document.createElement('a');
	_op.setAttribute('onclick','baseMapaaOSM()');
	_op.setAttribute('modo','OSM');
	_op.innerHTML="callejero gobal: OSM."; 
	_bc.appendChild(_op);
	
	_op=document.createElement('a');
	_op.setAttribute('onclick','baseMapaaOSMgris()');
	_op.setAttribute('modo','OSMgris');
	_op.innerHTML="callejero gobal: OSM gris."; 
	_bc.appendChild(_op);
	
	/*		
	_op=document.createElement('a');
	_op.setAttribute('onclick','baseMapaaBing()');
	_op.setAttribute('modo','Bing');
	_op.innerHTML="satelital: Bing"; 
	_bc.appendChild(_op);
	*/
		
	_op=document.createElement('a');
	_op.setAttribute('onclick','baseMapaaESRI()');
	_op.setAttribute('modo','ESRI');
	_op.innerHTML="satelital: ESRI"; 
	_bc.appendChild(_op);
	
	
	_op=document.createElement('a');
	_op.setAttribute('onclick','baseMapaaGoogle()');
	_op.setAttribute('modo','Google');
	_op.innerHTML="satelital: Google"; 
	_bc.appendChild(_op);
	
	
	_div_sub=document.createElement('div');
	_div_sub.setAttribute('id','capasextra');
	_div.appendChild(_div_sub);
	_div_sub.innerHTML='mostrar capas extra';
	
	_in=document.createElement('input');
	_in.setAttribute('id','buscador_mapa_capas');
	_in.setAttribute('placeholder','buscar');
	_div_sub.appendChild(_in);
	
	_div_lista=document.createElement('div');
	_div_lista.setAttribute('id','lista_capasextra');
	_div_sub.appendChild(_div_lista);
}
generarBotonCapas();


_Listado_Capas={};
function cargarListaCapas(){
	//console.log(_IdUsu);
	if(_IdUsu<"1"){ return;}// sin usuario no se realiza esta consulta.

    var _parametros = {
		'codMarco':_CodMarco,
		'zz_publicada':'1'
	};
    
	if(typeof consultasPHP_nueva === 'function'){
		_cn = consultasPHP_nueva('./app_capa/app_capa_consultar_listado.php');
	}
    $.ajax({
        url:   './app_capa/app_capa_consultar_listado.php',
        type:  'post',
        data: _parametros,
		beforeSend: function(request, settings) { 
			request._data = {'cn':_cn};
		},
		error:  function (request, status, errorThrown){	
			_cn = request._data.cn;
			if(typeof consultasPHP_respuesta === 'function'){ 
				consultasPHP_respuesta("err",_cn);}	
		},
		success:  function (response, status, request){			
			var _res = $.parseJSON(response);            
			_cn = request._data.cn;	
			if(typeof consultasPHP_respuesta === 'function'){ 
				consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);}
			if(_res.res!='exito'){return;}
		}
	}) 
	.done(function (_data, _textStatus, _jqXHR){
		
		
		_res = preprocesarRespuestaAjax(_data, _textStatus, _jqXHR);
		if(_res===false){return;}
		
		document.querySelector('#page #portamapa #lista_capasextra').innerHTML='';
		
		for(_nc in _res.data){	
			_Listado_Capas[_res.data[_nc].id]=_res.data[_nc];
			_p=document.createElement('p');
			document.querySelector('#page #portamapa #lista_capasextra').appendChild(_p);
			_p.setAttribute('class','capadisponible');
			_p.setAttribute('id_capa',_res.data[_nc].id);
			_p.title=_res.data[_nc].descripcion;
			
			_ch=document.createElement('input');
			_p.appendChild(_ch);
			_ch.setAttribute('type','checkbox');
			_ch.setAttribute('onchange','actualizaCapasExtra()');
			
			_sp=document.createElement('span');
			_p.appendChild(_sp);
			_sp.setAttribute('id','nombrecapa');
			_sp.innerHTML=_res.data[_nc].nombre;
							
		}
		
		cargarCapasUrl();
	});
	
	
}
cargarListaCapas();

function cargarCapasUrl(){
	//console.log('hola');
	_queryString = window.location.search;
	_urlParams = new URLSearchParams(_queryString);
	_layers_prendidos=_urlParams.getAll('mlyr');
	//console.log(_urlParams);
	//console.log(_layers_prendidos);
	for(_ln in _layers_prendidos){
		
		console.log('activa '+ _layers_prendidos[_ln]);
		
		_in=document.querySelector('.capadisponible[id_capa="'+_layers_prendidos[_ln]+'"] input');
		if(_in!=null){
			_in.checked=true;
			_in.onchange();
		}else{
			alert('no pudimos encontrar la capa adicional id: '+_layers_prendidos[_ln]);
		}
	}
}

var _sourceESRI= new  ol.source.XYZ({
    attributions: ['Powered by Esri',
                   'Source: Esri, DigitalGlobe, GeoEye, Earthstar Geographics, CNES/Airbus DS, USDA, USGS, AeroGRID, IGN, and the GIS User Community'],
    attributionsCollapsible: false,
    url: 'https://services.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
    maxZoom: 23
  })
  
var _sourceIGN= new  ol.source.XYZ({
    url: 'https://wms.ign.gob.ar/geoserver/gwc/service/tms/1.0.0/mapabase_gris@EPSG%3A3857@png/{z}/{x}/{-y}.png',
	crossOrigin: "Anonymous"
})


var _sourceBaseOSM=new ol.source.OSM({crossOrigin: 'anonymous'});
/*
var _sourceBaseBING=new ol.source.BingMaps({
	key: 'CygH7Xqd2Fb2cPwxzhLe~qz3D2bzJlCViv4DxHJd7Iw~Am0HV9t9vbSPjMRR6ywsDPaGshDwwUSCno3tVELuob__1mx49l2QJRPbUBPfS8qN',
	imagerySet:  'Aerial',
	crossOrigin: 'anonymous',
	Attributions:['base satelital: <a target="blank" href="https://www.microsoft.com/en-us/maps/product"><img src="https://dev.virtualearth.net/Branding/logo_powered_by.png"> Microsoft</a>']
});
*/

var _sourceBaseGoogle=new ol.source.XYZ({
	url:  'http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}',
});


function baseMapaaESRI(){
	mapa.removeLayer(_layerBase);
	//delete _layerBase;
	_layerBase = new ol.layer.Tile({
		zIndex:1,
		crossOrigin: "Anonymous"
	});
	mapa.addLayer(_layerBase);
	//_sourceIGN.refresh();
	_layerBase.setSource(_sourceESRI);
	document.querySelector('#page #portamapa #menucapas').setAttribute('modo','IGN');
	ActualizarLink();
	//mapa.removeLayer(_layerBase);
}


function baseMapaaIGN(){
	mapa.removeLayer(_layerBase);
	//delete _layerBase;
	_layerBase = new ol.layer.Tile({
		zIndex:1,
		crossOrigin: "Anonymous"
	});
	mapa.addLayer(_layerBase);
	//_sourceIGN.refresh();
	_layerBase.setSource(_sourceIGN);
	document.querySelector('#page #portamapa #menucapas').setAttribute('modo','IGN');
	ActualizarLink();
	//mapa.removeLayer(_layerBase);
}

function baseMapaaOSM(){
	mapa.removeLayer(_layerBase);
	//delete _layerBase;
	_layerBase = new ol.layer.Tile({
		zIndex:1,
		crossOrigin: 'anonymous'
	});	
	mapa.addLayer(_layerBase);
	//_sourceIGN.refresh();
	_layerBase.setSource( _sourceBaseOSM );	
	document.querySelector('#page #portamapa #menucapas').setAttribute('modo','OSM');
	ActualizarLink();
	//mapa.removeLayer(_layerBase);
}

function baseMapaaOSMgris(){
	mapa.removeLayer(_layerBase);
	//delete _layerBase;
	_layerBase = new ol.layer.Tile({
		zIndex:1,
		crossOrigin: 'anonymous',
		className: 'capagris',
	});	
	mapa.addLayer(_layerBase);
	//_sourceIGN.refresh();
	_layerBase.setSource( _sourceBaseOSM );	
	document.querySelector('#page #portamapa #menucapas').setAttribute('modo','OSMgris');
	ActualizarLink();
	//mapa.removeLayer(_layerBase);
}


function baseMapaaGoogle(){
	mapa.removeLayer(_layerBase);
	//delete _layerBase;
	_layerBase = new ol.layer.Tile({
		zIndex:1,
		crossOrigin: "Anonymous",
	});
	mapa.addLayer(_layerBase);
	
	
	_layerBase.setSource(_sourceBaseGoogle);
	document.querySelector('#page #portamapa #menucapas').setAttribute('modo','Google');
	ActualizarLink();
}

/*
function baseMapaaBing(){
	mapa.removeLayer(_layerBase);
	//delete _layerBase;
	_layerBase = new ol.layer.Tile({
		zIndex:1
	});
	mapa.addLayer(_layerBase);
	//_sourceIGN.refresh();
	_layerBase.setSource(new ol.source.BingMaps({
	 	key: 'CygH7Xqd2Fb2cPwxzhLe~qz3D2bzJlCViv4DxHJd7Iw~Am0HV9t9vbSPjMRR6ywsDPaGshDwwUSCno3tVELuob__1mx49l2QJRPbUBPfS8qN',
	 	imagerySet:  'Aerial',
	 	crossOrigin: 'anonymous'
	}));	
	document.querySelector('#page #portamapa #menucapas').setAttribute('modo','Bing');
	ActualizarLink();
}
*/


function actualizaCapasExtra(){
	//lee la lista de capas extra seleccionadas en el mapa y las muestra u oculta en el mapa deacuerdo a la selección
	
	_capas=document.querySelectorAll('#lista_capasextra .capadisponible');
	
	for(_nc in _capas){
		if(typeof _capas[_nc] != 'object'){continue;}
		
		_id_capa=_capas[_nc].getAttribute('id_capa');
		
		if(_id_capa==''){
			console.log('falta id:'+_capas[_nc]);
			continue;
		}
		
		if(_capas[_nc].querySelector('input[type="checkbox"]').checked){
			
			cargarDatosCapaExtra(_id_capa);
			
		}else{
			//borrar del mapa si esta cargada
			if(_CapasExtra[_id_capa]!=undefined){
				mapa.removeLayer(_CapasExtra[_id_capa].layer);
			}
		}	
	}
}

function cargarDatosCapaExtra(_id_capa){
    
    var parametros = {
        'codMarco': _CodMarco,
        'idMarco': _IdMarco,
        'zz_publicada': '1',
        'idcapa': _id_capa
    };
    
	if(typeof consultasPHP_nueva === 'function'){
		_cn = consultasPHP_nueva('./app_capa/app_capa_consultar.php');
		}
    $.ajax({
		url:   './app_capa/app_capa_consultar.php',
		type:  'post',
		data: parametros,
		beforeSend: function(request, settings) { 
			request._data = {'cn':_cn};
		},
        error:  function (request, status, errorThrown){	
			_cn = request._data.cn;
			if(typeof consultasPHP_respuesta === 'function'){ 
				consultasPHP_respuesta("err",_cn);}	
		},
		success:  function (response, status, request){			
			var _res = $.parseJSON(response);            
            _cn = request._data.cn;	
			if(typeof consultasPHP_respuesta === 'function'){ 
	            consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);}
            if(_res.res!='exito'){return;}
		}
	})
   .done(function (_data, _textStatus, _jqXHR){		   
		_res = preprocesarRespuestaAjax(_data, _textStatus, _jqXHR);
		if(_res===false){return;}

		_datc = _res.data;
		_id_capa=_datc.id;
		_CapasExtra[_id_capa]={};
		_CapasExtra[_id_capa]['data']=_datc;
		_CapasExtra[_id_capa]['source']= new ol.source.Vector({projection: 'EPSG:3857',crossOrigin: 'anonymous'}); 
		_CapasExtra[_id_capa]['layer']= new ol.layer.Vector({source: _CapasExtra[_id_capa].source, zIndex:9});
		mapa.addLayer(_CapasExtra[_id_capa].layer);
		if(_res.data.modo_defecto=='wms'){	
			if(_res.data.wms_layer!=''){
				cargarWmsCapaExist(_res);
			}
		}else{
		
			_CapasExtra[_id_capa]['reglas']=Array();
			if (_datc.sld && _datc.sld != ''){	
				if (window.DOMParser){
					parser = new DOMParser();
					xmlDoc = parser.parseFromString(_datc.sld, "text/xml");
				}else{ // Internet Explorer
					xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
					xmlDoc.async = false;
					xmlDoc.loadXML(xmlSld);
				}
				_rules= xmlDoc.getElementsByTagName("Rule");
			}	
			
			if(Object.keys(_rules).length>0){
				for(_rn in _rules){	
					_larule = _rules[_rn];
					if(typeof _larule != 'object'){continue;}
					
					var _mayorIgualQue = _larule.getElementsByTagName("ogc:PropertyIsGreaterThanOrEqualTo")[0];
					var _menorQue = _larule.getElementsByTagName("ogc:PropertyIsLessThan")[0];   	
					var xmlFill = _larule.getElementsByTagName("Fill")[0];		
					var xmlStroke = _larule.getElementsByTagName("Stroke")[0];
					
					_campoMM=0;
					_valorMM=0;
					_campomm=0;
					_valormm=0;
						
					if(_mayorIgualQue!=undefined){					
						for(var node in _mayorIgualQue.childNodes){		            	
							if (_mayorIgualQue.childNodes[node].nodeName == "ogc:PropertyName"){
								_campoMM = _mayorIgualQue.childNodes[node].textContent;
							}
							if (_mayorIgualQue.childNodes[node].nodeName == "ogc:Literal"){
								_valorMM = _mayorIgualQue.childNodes[node].textContent;
							}
						}
					}
					
					if(_menorQue!=undefined){				
						for(var node in _menorQue.childNodes){		            	
							if (_menorQue.childNodes[node].nodeName == "ogc:PropertyName"){
								_campomm = _menorQue.childNodes[node].textContent;
							}
							if (_menorQue.childNodes[node].nodeName == "ogc:Literal"){
								_valormm = _menorQue.childNodes[node].textContent;
							}
						}
					}				
					
					
					if(xmlFill!=undefined){	
						for(var node in xmlFill.childNodes){
							if (xmlFill.childNodes[node].nodeName == "CssParameter" 
									&& xmlFill.childNodes[node].getAttribute("name") == "fill"){
								colorRelleno = xmlFill.childNodes[node].textContent;
							}
							if (xmlFill.childNodes[node].nodeName == "CssParameter"
									&& xmlFill.childNodes[node].getAttribute("name") == "fill-opacity"){
								transparenciaRelleno = xmlFill.childNodes[node].textContent;
							}
						}
					}
					
					if(xmlStroke!=undefined){	
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
					}	
					
					_con={
						'tipo':'intervalo',
						'campoMM':_campoMM,
						'valorMM':_valorMM,
						'campomm':_campomm,
						'valormm':_valormm,
						'colorRelleno':colorRelleno,
						'transparenciaRelleno':transparenciaRelleno,
						'colorTrazo':colorTrazo,
						'anchoTrazo':anchoTrazo
					}
					_CapasExtra[_id_capa]['reglas'].push(_con);
				}
			}		
			consultarFeaturesCapaExtra(_id_capa);//carga consulta y carga registros en capa vectorial
		}
	});
}


function consultarFeaturesCapaExtra(_id_capa){
	_parametros = {
		'codMarco':_CodMarco,
		'idcapa': _id_capa,
		'RecorteDeTrabajo' : _RecorteDeTrabajo
	};
	if(typeof consultasPHP_nueva === 'function'){
		_cn = consultasPHP_nueva('./app_capa/app_capa_consultar_registros.php');
	}
	$.ajax({
		url:   './app_capa/app_capa_consultar_registros.php',
		type:  'post',
		data: _parametros,
		beforeSend: function(request, settings) { 
			request._data = {'cn':_cn};
		},
        error:  function (request, status, errorThrown){	
			_cn = request._data.cn;
			if(typeof consultasPHP_respuesta === 'function'){ 
				consultasPHP_respuesta("err",_cn)}	
		},
		success:  function (response, status, request){			
			var _res = $.parseJSON(response);            
            _cn = request._data.cn;	
			if(typeof consultasPHP_respuesta === 'function'){ 
	            consultasPHP_respuesta("exito",_cn,_res.mg,_res.res)};
            if(_res.res!='exito'){return;}
		}
	})
	.done(function (_data, _textStatus, _jqXHR){		   
		_res = preprocesarRespuestaAjax(_data, _textStatus, _jqXHR);
		if(_res===false){return;}

		_id_capa=_res.data.idcapa;
		_features=_res.data.registros;
		
		for(var _nf in _features){
			if(_features[_nf].geotx==''){continue;}//este registro debería ser eliminado
			if(_features[_nf].geotx==null){continue;}//este registro debería ser eliminado
			var format = new ol.format.WKT();	
			var _feat = format.readFeature(_features[_nf].geotx, {
				dataProjection: 'EPSG:3857',
				featureProjection: 'EPSG:3857'
			});				
				
			if(_features[_nf].id!=null){
				_feat.setId(_features[_nf].id);
			}

			_feat.setProperties({
				'id':_features[_nf].id
			});				
			
			for(_nr in _CapasExtra[_id_capa].reglas){				
				_datasec=_features[_nf];
				for(_k in _datasec){
					_kref=_k.replace('texto','nom_col_text');
					_kref=_kref.replace('numero','nom_col_num');
					//console.log(_kref+' - '+_Capa[_kref] +' vs ' +_campoMM);
					if(_CapasExtra[_id_capa].data[_kref] == _campoMM){
						_campoMM =_k; 				//console.log('eureka. ahora: '+_campoMM);
						break;
					}		
				}
				
				for(_k in _datasec){
					_kref=_k.replace('texto','nom_col_text');
					_kref=_kref.replace('numero','nom_col_num');
					if(_CapasExtra[_id_capa].data[_kref] == _campomm){
						_campomm =_k; 			//console.log('eureka. ahora: '+_campomm);
						break;
					}								
				}
			}
			
			_st= new ol.style.Style({
			  fill: new ol.style.Fill({
				color: 'rgba(0,0,0,0)'
	
			  }),
			  stroke: new ol.style.Stroke({
				color: 'rgba(0,0,0,1)',
				width: '1'
			  })
			});
			for(_nr in _CapasExtra[_id_capa].reglas){		
				if(
					Number(_features[_nf][_campoMM]) >= Number(_CapasExtra[_id_capa].reglas[_nr].valorMM)
					&&
					Number(_features[_nf][_campomm]) <  Number(_CapasExtra[_id_capa].reglas[_nr].valormm)
				){
					_c= hexToRgb(_CapasExtra[_id_capa].reglas[_nr].colorRelleno);
					_n=_CapasExtra[_id_capa].reglas[_nr].transparenciaRelleno;					   
					_rgba='rgba('+_c.r+', '+_c.g+', '+_c.b+', '+_n+')';
			
					_st= new ol.style.Style({
					  fill: new ol.style.Fill({
						color: _rgba
			
					  }),
					  stroke: new ol.style.Stroke({
						color: _CapasExtra[_id_capa].reglas[_nr].colorTrazo,
						width: _CapasExtra[_id_capa].reglas[_nr].anchoTrazo
					  })
					});
				}
			}
			console.log(_st);
			_feat.setStyle(_st);
			_CapasExtra[_id_capa].source.addFeature(_feat);
			console.log(_feat);
		}			

	});
}
