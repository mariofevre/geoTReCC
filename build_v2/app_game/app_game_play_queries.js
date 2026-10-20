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


 
function consultarPermisos(){

    _parametros = {
            'codMarco':_CodMarco	
    };
    $.ajax({
        url:   './app_capa/app_capa_consultar_permisos.php',
        type:  'post',
        data: _parametros,
        error:  function (response){alert('error al consultar el servidor');},
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            for(_na in _res.acc){
            	console.log(_res.acc[_na])
	          	procesarAcc(_res.acc[_na]);
            }
            if(_res.res!='exito'){
                alert('error al consultar la base de datos');
            }
        }
    });	
}
//consultarPermisos();




function cargarInfoSesion(){
    
    var parametros = {
        'codMarco': _CodMarco,
        'idMarco': _IdMarco,
        'idSesion': _IdSesion
    };
    
    $.ajax({
            url:   './app_game/app_game_consultar_sesion.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                
                for(var _nm in _res.mg){
	                alert(_res.mg[_nm]);
	            }
	            for(var _na in _res.acc){
	                procesarAcc(_res.acc[_na]);
	            }
                //console.log(_res);
                if(_res.res=='exito'){
                	_DataSesion=_res.data.sesion;
                	_DataIndicadorB=_res.data.indicador;
                	_BufferZeroCoord=Array(
                		_res.data.geomGame.geo_centroide_x,
                		_res.data.geomGame.geo_centroide_y
                	);
                	DibujarBufferZero(_res.data.geomGame.geo_buffer_centroide_tx);
                	accionEditarCrearGeometria();
                	accionSesionPublicadaCargar(_DataSesion.id);
                    cargarInfoIndicador(_DataSesion.id_p_indicadores_indicadores, '', '', '');
                }else{
                    alert('error asf0jg44f8f0gh');
                }
            }
    });
}
cargarInfoSesion();

var _DataIndicador;

function cargarInfoIndicador(idIndicador, seleccionarFechaAno, seleccionarFechaMes, seleccionarDefault){
   
    var parametros = {
        'codMarco': _CodMarco,
        'idMarco': _IdMarco,
        'zz_publicada': '1',
        'id': idIndicador
    };
    
    $.ajax({
            url:   './app_ind/app_ind_consultar_estado.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                
                for(var _nm in _res.mg){
	                alert(_res.mg[_nm]);
	            }
	            for(var _na in _res.acc){
	                procesarAcc(_res.acc[_na]);
	            }
                //console.log(_res);
                if(_res.res=='exito'){
                	_DataIndicador=_res.data.indicador;
                   cargarDatosCapaPublicada(_DataIndicador.calc_superp);
                }else{
                    alert('error asf0jg44f8f0gh');
                }
            }
    });
}

var _DatosCapa={};
function cargarDatosCapaPublicada(_idcapa){
    
    var parametros = {
        'codMarco': _CodMarco,
        'idMarco': _IdMarco,
        'zz_publicada': '1',
        'idcapa': _idcapa
    };
    
    $.ajax({
            url:   './app_game/app_game_consultar_capa.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                if(_res.res=='exito'){
                	_DatosCapa=_res.data;
                    cargarReglasCapa(_res);                   
                }else{
                	_Capa = {};
                    alert('error tf0jg44ff0gh');
                }
            }
    });
}


var xmlDoc;
function cargarReglasCapa(_res){
    if (_res.data != null){
		
        var capaQuery = _res.data;
        //Operaciones para leer del xml los valores de simbologia
        var xmlSld = capaQuery["sld"];
		//console.log(xmlSld);
		
        if (xmlSld && xmlSld != ''){
            var colorRelleno = '';
            var transparenciaRelleno = '';
            var colorTrazo = '';
            var anchoTrazo = '';
            
            if (window.DOMParser)
            {
                parser = new DOMParser();
                xmlDoc = parser.parseFromString(xmlSld, "text/xml");
            }
            else // Internet Explorer
            {
                xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
                xmlDoc.async = false;
                xmlDoc.loadXML(xmlSld);
            }
            
            _rules= xmlDoc.getElementsByTagName("Rule");
			
			
			if(Object.keys(_rules).length==1){
			    var xmlFill = xmlDoc.getElementsByTagName("Fill")[0];
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
	
	            var xmlStroke = xmlDoc.getElementsByTagName("Stroke")[0];
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

           }else{
           		for(_rn in _rules){
           			_larule = _rules[_rn];
           			
           			if(typeof _larule != 'object'){continue;}
           			
           			//console.log(_larule);
           			
           			var xmlFill = _larule.getElementsByTagName("Fill")[0];
           			
           			//console.log(xmlFill);
           			
		            for(var node in xmlFill.childNodes){
		                if (xmlFill.childNodes[node].nodeName == "CssParameter" 
		                        && xmlFill.childNodes[node].getAttribute("name") == "fill"){
		                    colorRelleno = xmlFill.childNodes[node].textContent;
		                    //console.log(colorRelleno);
		                }
		                if (xmlFill.childNodes[node].nodeName == "CssParameter"
		                        && xmlFill.childNodes[node].getAttribute("name") == "fill-opacity"){
		                    transparenciaRelleno = xmlFill.childNodes[node].textContent;
		                }
		            }
		
		            var xmlStroke = _larule.getElementsByTagName("Stroke")[0];
		            for(var node in xmlStroke.childNodes){
		                if (xmlStroke.childNodes[node].nodeName == "CssParameter"
		                        && xmlStroke.childNodes[node].getAttribute("name") == "stroke"){
		                    //colorTrazo = xmlStroke.childNodes[node].textContent;
		                }
		                if (xmlStroke.childNodes[node].nodeName == "CssParameter"
		                        && xmlStroke.childNodes[node].getAttribute("name") == "stroke-width"){
		                    anchoTrazo = xmlStroke.childNodes[node].textContent;
		                }
		            }
		            		            
		           _etiqueta = _larule.getElementsByTagName("Name")[0].textContent;
		           _mayor = _larule.getElementsByTagName("ogc:PropertyIsGreaterThanOrEqualTo")[0]; 
		             for(var node in _mayor.childNodes){
		             	if(_mayor.childNodes[node].nodeName == "ogc:PropertyName"){
		             		_campo = _mayor.childNodes[node].textContent;
		             	}
		             	if(_mayor.childNodes[node].nodeName == "ogc:Literal"){
		             		_valorMayor = _mayor.childNodes[node].textContent;
		             	}
		             }
		            _menor = _larule.getElementsByTagName("ogc:PropertyIsLessThan")[0]; 
		             for(var node in _menor.childNodes){
		             	if(_menor.childNodes[node].nodeName == "ogc:Literal"){
		             		_valorMenor = _menor.childNodes[node].textContent;
		             	}
		             }
		             	 
           		}
           }
        }
    } else {
        alert('error otjsf0jg44ffgh');
    }
    
    cargarValoresCapa();
}


function cargarValoresCapa(){
	
	//alert(_DatosCapa.wms_layer);
	if(_DatosCapa.wms_layer==null){_DatosCapa.wms_layer='';}
		
	if(_DatosCapa.wms_layer!=''){
		cargarWms(_DatosCapa.wms_layer);
		_ext= _sMarco.getExtent();

	    setTimeout(function(){
	        mapa.getView().fit(_ext, { duration: 1000 });
	    }, 200);
	    
	}else{
	    var _CodMarco = getParameterByName('cod');
	    _parametros = {
	            'codMarco':_CodMarco,
	            'idcapa': _DataIndicador.calc_superp,
	            'modo':'forzado'
	    };
	    $.ajax({
	        url:   './app_capa/app_capa_consultar_registros.php',
	        type:  'post',
	        data: _parametros,
	        success:  function (response){
	            var _res = $.parseJSON(response);
	            for(var _nm in _res.mg)
	            {
	                alert(_res.mg[_nm]);
	            }
	            
	            _Features=_res.data.registros;
	            
	            if(_res.res == 'exito'){
	            	cargarFeatures('capa');
	            }
	        }
	    });
   }
}


function cargarWms(_layer_wms){
	_ExtraBaseWmsSource= new ol.source.TileWMS({
        url: 'http://190.111.246.33:8080/geoserver/geoGEC/wms',
        params: {
	        'VERSION': '1.1.1',
	        tiled: true,
	        LAYERS: _layer_wms,
	        STYLES: '',
        }
   });
   
	La_ExtraBaseWms.setSource(_ExtraBaseWmsSource);
}



var xmlDoc;
function cargarFeatures(_tipo){
    if(_tipo=='capa'){
        _lyrElemSrc.clear();
	}else if(_tipo=='cubierto'){
		_src['game_cubierto'].clear();
	}else{
		alert('error');
	}
	
    _rules={};
    if(typeof xmlDoc != 'undefined'){    
    	_rules= xmlDoc.getElementsByTagName("Rule");
	}
	    	
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
	                
	                console.log(transparenciaRelleno);
	                if(_tipo=='capa'){
	                	transparenciaRelleno=Math.max(0.6,(Number(transparenciaRelleno)+0.3));
	                	transparenciaRelleno=Math.min(0.9,transparenciaRelleno);
	                }
	                console.log(transparenciaRelleno);
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
	
    for(var elem in _Features){

        var format = new ol.format.WKT();	
        var _feat = format.readFeature(_Features[elem].geotx, {
            dataProjection: 'EPSG:3857',
            featureProjection: 'EPSG:3857'
        });

        _feat.setId(_Features[elem].id);

        _feat.setProperties({
            'id':_Features[elem].id
        });
        
        
        if(_condiciones.length>0){
			_datasec=_Features[elem];
			for(_k in _datasec){
				_kref=_k.replace('texto','nom_col_text');
				_kref=_kref.replace('numero','nom_col_num');
				//console.log(_kref+' - '+_Capa[_kref] +' vs ' +_campoMM);
				if(_DatosCapa[_kref] == _campoMM){
					_campoMM =_k; 
					console.log('eureka. ahora: '+_campoMM);
					break;
				}		
			}
			
			for(_k in _datasec){
				
				_kref=_k.replace('texto','nom_col_text');
				_kref=_kref.replace('numero','nom_col_num');
				if(_DatosCapa[_kref] == _campomm){
					_campomm =_k; 
					//console.log('eureka. ahora: '+_campomm);
					break;
				}	
						
			}
		}
		//console.log(_Features[elem][_campoMM] +' >= '+_valorMM+'&&'+_Features[elem][_campomm]+' < '+ _valormm);
		
			
     
	        
	          _st= new ol.style.Style({
		          fill: new ol.style.Fill({
		            color: 'rgba(255,0,0,0.2)'
		
		          }),
		          stroke: new ol.style.Stroke({
		            color: 'rgba(0,0,0,0.2)',
		            width: '2'
		          })
		        });
        
	        for(_nc in _condiciones){
				if(
					Number(_Features[elem][_campoMM]) >= Number(_condiciones[_nc].valorMM)
					&&
					Number(_Features[elem][_campomm]) <  Number(_condiciones[_nc].valormm)
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
			          })
			        });
				}
			}
        	_feat.setStyle (_st);

		if(_tipo=='capa'){
	        _lyrElemSrc.addFeature(_feat);
		}else if(_tipo=='cubierto'){
			_src['game_cubierto'].addFeature(_feat);
		}
        _MapaCargado='si';
    }

    _ext= _lyrElemSrc.getExtent();

    setTimeout(function(){
        mapa.getView().fit(_ext, { duration: 1000 });
    }, 200);
}


function cerrarPartida(){
		
    var parametros = {
        'codMarco': _CodMarco,
        'idMarco': _IdMarco,
        'idSesion': _IdSesion,
        'partida': _Partida,
        'iniciales': document.querySelector('#gameover input[name="iniciales"]').value
    };
    
    $.ajax({
        url:   './app_game/app_game_cerrar_partida.php',
        type:  'post',
        data: parametros,
        success:  function (response){   
	    	var _res = $.parseJSON(response);
	            
	        for(var _nm in _res.mg){
	            alert(_res.mg[_nm]);
	        }
	        for(var _na in _res.acc){
	            procesarAcc(_res.acc[_na]);
	        }
	        
	        //console.log(_res);
	        if(_res.res=='exito'){
				window.location.assign('./app_game_highscores.php?partida='+_Partida+'&idsesion='+_IdSesion+'&cod='+_CodMarco);
			}			
		}
	});
	
}

function completar(_this,_event){
	
	if(_event.keyCode=='13'){
		cerrarPartida();
	}
}
function mayusculizar(_this){
	_str=_this.value.toLowerCase();
   _str =_str.replace(new RegExp(/\s/g),"");
   _str =_str.replace(new RegExp(/[àáâãäå]/g),"a");
   _str =_str.replace(new RegExp(/æ/g),"ae");
   _str =_str.replace(new RegExp(/ç/g),"c");
   _str =_str.replace(new RegExp(/[èéêë]/g),"e");
   _str =_str.replace(new RegExp(/[ìíîï]/g),"i");
   _str =_str.replace(new RegExp(/ñ/g),"n");                
   _str =_str.replace(new RegExp(/[òóôõö]/g),"o");
   _str =_str.replace(new RegExp(/œ/g),"oe");
   _str =_str.replace(new RegExp(/[ùúûü]/g),"u");
   _str =_str.replace(new RegExp(/[ýÿ]/g),"y");
   _str =_str.replace(new RegExp(/\W/g),"");
   _str=_this.value.toUpperCase(); 
	_this.value=_str;
}
