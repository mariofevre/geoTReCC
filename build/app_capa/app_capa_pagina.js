/**
*
* funciones de operacion de la pagina 
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
    var _IdMarco = getParameterByName('id');
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco,
            'accion':_Acc
    };
    $.ajax({
        url:   './sistema/sis_consulta_permisos.php',
        type:  'post',
        data: _parametros,
        error:  function (response){alert('error al consultar el servidor');},
        success:  function (response){
            var _res = $.parseJSON(response);
            for(var _nm in _res.mg)
            {
                alert(_res.mg[_nm]);
            }
            if(_res.res!='exito'){
                alert('error al consultar la base de datos');
            }
        }
    });	
}



function cagarDefaults(){
    //RGBA (250, 200, 100, 0.5)
    document.getElementById('inputcolorrelleno').value = '#FAC864';
    document.getElementById('inputtransparenciarellenoNumber').value = 50;
    document.getElementById('inputtransparenciarellenoRange').value = 50;
    //RGBA (255, 100, 50, 1)
    document.getElementById('inputcolortrazo').value = '#FF6432';
    //Trazo ancho = 1
    document.getElementById('inputanchotrazoNumber').value = 1;
    document.getElementById('inputanchotrazoRange').value = 1;
}
cagarDefaults();

function accionCargarNuevaCapa(){
    generarNuevaCapa();
    document.querySelector('#cuadrovalores').setAttribute('modo','subiendonuevacapa');
    limpiarFormCapa();
    
}

function accionCargarCapaExist(){
    cargarListadoCapasPublicadas();
    document.querySelector('#cuadrovalores').setAttribute('modo','muestralistado');    
}


function accionCancelarCargarNuevaCapa(_this){
	
	document.getElementById('listacapaspublicadas').innerHTML='';
    cargarListadoCapasPublicadas();
    
    document.querySelector('#cuadrovalores').setAttribute('modo','');   
    limpiarFormularioCapa();
}

function accionCancelarSeleccionCapa(_this){
    
    document.querySelector('#cuadrovalores').setAttribute('modo','');  
    
    limpiarFormularioSeleccionCapa();
    limpiarFormularioCapa();
}

function accionCargarCapaPublicada(_deprecado, idcapa){
    limpiarFormularioSeleccionCapa();
    limpiarFormularioCapa();
    cargarDatosCapaPublicada(idcapa);
    
    document.querySelector('#cuadrovalores').setAttribute('modo','muestracapaexistente');   
}

function limpiarFormularioSeleccionCapa(){
	
    document.querySelector('#divSeleccionCapa #txningunacapa').style.display='block';
    document.querySelector('#divSeleccionCapa #listacapaspublicadas').innerHTML='';
    document.getElementById('divSeleccionCapa').style.display='none';
}

function limpiarFormularioCapa(){
    document.getElementById('divCargaCapa').setAttribute('idcapa', 0);
    document.getElementById('capaNombre').value = '';
    document.getElementById('capaDescripcion').value = '';
    
    document.getElementById('crs').value = '';
    document.querySelector('#divCargaCapa #txningunarchivo').style.display='block';
    document.querySelector('#divCargaCapa #archivoscargados').innerHTML='';
    document.querySelector('#divCargaCapa #cargando').innerHTML='';
    document.querySelector('#divCargaCapa #camposident').innerHTML='';
    
    
    document.querySelector('#divCargaCapa #reglas_adicionales').innerHTML='';
    
    //document.getElementById('divCargaCapa').style.display='none';
    cagarDefaults();
    
    _divrules=document.querySelectorAll('#simbologia > div[name="rule"]');
    for(_nr in _divrules){
    	if(typeof _divrules[_nr] == 'object'){
    		//console.log(_divrules[_nr]);
    		_divrules[_nr].parentNode.removeChild(_divrules[_nr]);
    	}
    }
    
    _inp=document.querySelectorAll('#configurarCampos input');
    
    for(_in in _inp){
    	if(typeof _inp[_in] == 'object'){
			
    		//console.log(_divrules[_nr]);    		
    		_inp[_in].setAttribute('editado','no');
    		_inp[_in].value='';
    		_inp[_in].parentNode.setAttribute('activo','no');
    		if(_inp[_in].parentNode.getAttribute('class')=='campo'){
				_inp[_in].parentNode.setAttribute('activo','no');
			}
    		
    	}
    }
}



function mostrarListadoCapasPublicadas(){
    document.querySelector('#divSeleccionCapa #txningunacapa').style.display='none';
    document.getElementById('divSeleccionCapa').style.display='block';
}

function limpiaBarra(_event){
	document.querySelector("#barrabusqueda input").value='';
	actualizarBusqueda(_event);
}

function actualizarBusqueda(_event){
	
	_input=document.querySelector("#barrabusqueda input");
	_str=_input.value;
	if(_str.length>=3){
		_input.parentNode.setAttribute('estado','activo');
	}else{
		_str='';
		_input.parentNode.setAttribute('estado','inactivo');
	}
	_str=_str.toLowerCase();
	//console.log('buscando: '+_str);
	
	_lis=document.querySelectorAll('#listacapaspublicadas > a.filaCapaLista');
	
	for(_ln in _lis){
		if(typeof _lis[_ln] != 'object'){continue;}
		
		_contId=_lis[_ln].querySelector('#capaIdLista');
		_contNom=_lis[_ln].querySelector('#capaNombreLista');
		_contDes=_lis[_ln].querySelector('#capaDescripcionLista');
		
		_cont=_contId.innerHTML+' '+_contNom.innerHTML+' '+_contNom.innerHTML;
		
		_cont=_cont.toLowerCase();
		
		_cont=_cont.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
		_str=_str.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
		
		if(_cont.toLowerCase().indexOf(_str)==-1){
			_lis[_ln].setAttribute('filtrado','si');
		}else{
			_lis[_ln].setAttribute('filtrado','no');
		}
	}
	

}



function cargarValoresCapaExist(_res){
	//console.log('o');
    if (_res.data != null){
		//console.log('p');
        var capaQuery = _res.data;

        document.getElementById('divCargaCapa').setAttribute('idcapa', capaQuery["id"]);
        document.getElementById('capaNombre').value = capaQuery["nombre"];
        document.getElementById('capaDescripcion').value = capaQuery["descripcion"];
        
        document.querySelector('#carga [name="modo_defecto"]').value = capaQuery["modo_defecto"];
        document.querySelector('#carga [name="modo_defecto"]').onchange();
        document.querySelector('#carga [name="modo_publica"]').value = capaQuery["modo_publica"];
        document.querySelector('#carga [name="tipo_fuente"]').value = capaQuery["tipo_fuente"];
        document.querySelector('#carga [name="link_capa"]').value = capaQuery["link_capa"];
        document.querySelector('#carga #muestra_link_capa').innerHTML = capaQuery["muestra_link_capa"];
        
        document.querySelector('#carga [name="link_capa_campo_local"]').value = capaQuery["link_capa_campo_local"];
        document.querySelector('#carga #muestra_link_capa_campo_local').innerHTML = capaQuery[capaQuery["link_capa_campo_local"]];
        
        document.querySelector('#carga [name="link_capa_campo_externo"]').value = capaQuery["link_capa_campo_externo"];
        document.querySelector('#carga #muestra_link_capa_campo_externo').innerHTML = capaQuery["muestra_link_capa_campo_externo"];
        
        document.querySelector('#carga [name="fecha_ano"]').value = capaQuery["fecha_ano"];
        document.querySelector('#carga [name="fecha_mes"]').value = capaQuery["fecha_mes"];
        document.querySelector('#carga [name="fecha_dia"]').value = capaQuery["fecha_dia"];
        
        
        for(_k in _res.data){
			//console.log(_res.data[_k]);
			if(_k.substring(0,8)=='nom_col_' && _res.data[_k]!='' && _res.data[_k]!=null){
				document.querySelector('#configurarCampos [name="'+_k+'"]').parentNode.setAttribute('activo','si');
				document.querySelector('#configurarCampos [name="'+_k+'"]').value=_res.data[_k];
			}		
			if(_k.substring(0,8)=='cod_col_' && _res.data[_k]!=''){
				document.querySelector('#configurarCampos [name="'+_k+'"]').value=_res.data[_k];
			}		
		}
        
        
        if(capaQuery["tipogeometria"]!=null){
			if(document.querySelector('.formCargaCapa [name="tipogeometria"] option[value="'+capaQuery["tipogeometria"]+'"')!=null){
				document.querySelector('.formCargaCapa [name="tipogeometria"] option[value="'+capaQuery["tipogeometria"]+'"').selected=true;
			}else{
				//console.log('error, no se encontró la opción '+capaQuery["tipogeometria"]);
			}
		}
		
		$(".formCargaCapa [name='tipogeometria']" ).change();
		
		document.querySelector('#cuadrovalores').setAttribute('publicada',_res.data.zz_publicada);
	
		
					
		if(_res.data.zz_publicada=='1'){
			document.querySelector('.formCargaCapa #cargarGeometrias').setAttribute('abiertaedicion','no');
		}else{
			document.querySelector('.formCargaCapa #cargarGeometrias').setAttribute('abiertaedicion','si');
			formversion(document.querySelector('#botonformversion'));	
		}

		document.querySelector('#simbologia #simbologia_carga optgroup#alternativas').innerHTML='';

		_sel=document.querySelector('#simbologia #simbologia_carga');
		
		
		for(_sid in _res.data.simbologias){
			_sda=_res.data.simbologias[_sid];
			_op=document.createElement('option');
			_op.innerHTML=_sda.nombre;
			_op.setAttribute('class','adicional');
			_op.value=_sid;
			_sel.appendChild(_op);
		}
		
		sldHtml(capaQuery["sld"]);
        //Operaciones para leer del xml los valores de simbologia
        
        
    } else {
        alert('error otjsf0jg44ffgh');
    }
    
    if(_res.data.modo_defecto=='wms'){	
		if(_res.data.wms_layer!=''){
			cargarWmsCapaExist(_res);
		}
	}else{
		cargarValoresCapaExistQuery();//carga consulta y carga registros en capa vectorial
	}
	
    
    
}


function sldHtml(xmlSld){
	
	if (xmlSld == ''){return;}
	
	colorRelleno = '';
	transparenciaRelleno = '';
	colorTrazo = '';
	anchoTrazo = '';

	//console.log(xmlSld);
	
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
	
	_eti= xmlDoc.getElementsByTagName("Label")[0];
	
	console.log(_eti);
	console.log(typeof _eti);
	if(_eti!=undefined){
		_campo_eti = _eti.children[0].textContent;
		
	}else{
		_campo_eti ='';
	}
	
	console.log(_campo_eti);
	
	document.querySelector('#simbologia [name="campo_et"]').innerHTML='<option value="">- etiquetar -</option>';
	
	for(_k in _Capa){
		if(_k.slice(0,7)=='nom_col'){
			if(_Capa[_k]!=null){
				_op=document.createElement('option');
				_op.innerHTML=_Capa[_k];
				_op.value=_k;
				document.querySelector('#simbologia [name="campo_et"]').appendChild(_op);
			}
		}
	}
	
	

	document.querySelector('#simbologia [name="campo_et"]').value = _campo_eti;
	
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
				_opacidad=xmlFill.childNodes[node].textContent;
				transparenciaRelleno = (1 - _opacidad);
				console.log(transparenciaRelleno);
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
		

		document.getElementById('inputcolorrelleno').value = colorRelleno;
		document.getElementById('inputtransparenciarellenoNumber').value = transparenciaRelleno * 100;
		document.getElementById('inputtransparenciarellenoRange').value = transparenciaRelleno * 100;
		document.getElementById('inputcolortrazo').value = colorTrazo;
		document.getElementById('inputanchotrazoNumber').value = anchoTrazo;
		document.getElementById('inputanchotrazoRange').value = anchoTrazo;
   }else{
		for(_rn in _rules){
			_larule = _rules[_rn];
			if(typeof _larule != 'object'){continue;}
			_algo=_larule.getElementsByTagName("Fill")[0];
			//console.log(_algo);
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
		  //  console.log( capaQuery["sld"]);
			
			if(_larule.getElementsByTagName("Name")[0]!=null){
				_etiqueta = _larule.getElementsByTagName("Name")[0].textContent;
			}else{
				_etiqueta='';
			}

			_campo=null;
			_valorMenor=null;
			_valorMayor=null;
			_valorIgual=null;
		   
			_iguala = _larule.getElementsByTagName("ogc:PropertyIsEqualTo")[0]; 
			
			_mayor = _larule.getElementsByTagName("ogc:PropertyIsGreaterThanOrEqualTo")[0]; 
			_menor = _larule.getElementsByTagName("ogc:PropertyIsLessThan")[0]; 
			
			if(_iguala != null){
			   
				for(var node in _iguala.childNodes){
					//console.log(_mayor.childNodes[node].nodeName);
					if(_iguala.childNodes[node].nodeName == "ogc:PropertyName"){
						_campo = _iguala.childNodes[node].textContent;
					}
					if(_iguala.childNodes[node].nodeName == "ogc:Literal"){
						_valorIgual = _iguala.childNodes[node].textContent;
					}
				 }
				_rr=crearReglaDiv(_rn,_etiqueta,_valorMenor,_valorMayor,_valorIgual,colorRelleno,transparenciaRelleno,colorTrazo,anchoTrazo,_campo);
				document.querySelector('#simbologia #reglas_adicionales').appendChild(_rr);
				
			   
			}else if(_menor != null && _mayor != null){
				
				
				_mayor = _larule.getElementsByTagName("ogc:PropertyIsGreaterThanOrEqualTo")[0]; 
				_menor = _larule.getElementsByTagName("ogc:PropertyIsLessThan")[0]; 
				
				if(_mayor != null && _menor != null){
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
				_rr=crearReglaDiv(_rn,_etiqueta,_valorMenor,_valorMayor,_valorIgual,colorRelleno,transparenciaRelleno,colorTrazo,anchoTrazo,_campo);
				document.querySelector('#simbologia #reglas_adicionales').appendChild(_rr);
				
			}else{
				
				
				var xmlFill = _larule.getElementsByTagName("Fill")[0];
				for(var node in xmlFill.childNodes){
					if (xmlFill.childNodes[node].nodeName == "CssParameter" 
							&& xmlFill.childNodes[node].getAttribute("name") == "fill"){
						colorRelleno = xmlFill.childNodes[node].textContent;
					}
					if (xmlFill.childNodes[node].nodeName == "CssParameter"
							&& xmlFill.childNodes[node].getAttribute("name") == "fill-opacity"){
						_opacidad=xmlFill.childNodes[node].textContent;
						transparenciaRelleno = (1 - _opacidad);
						console.log(transparenciaRelleno);
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
				
				document.getElementById('inputcolorrelleno').value = colorRelleno;
				document.getElementById('inputtransparenciarellenoNumber').value = transparenciaRelleno * 100;
				document.getElementById('inputtransparenciarellenoRange').value = transparenciaRelleno * 100;
				document.getElementById('inputcolortrazo').value = colorTrazo;
				document.getElementById('inputanchotrazoNumber').value = anchoTrazo;
				document.getElementById('inputanchotrazoRange').value = anchoTrazo;
				
				
			}	

		}
		simbolizarCapa();           	
   }
}


function anadirReglaSLD(_padre){
	_rn='1';
	_etiqueta='nueva regla';
	_valorMenor='0';
	_valorMayor='0';
	_valorIgual='';
	colorRelleno='#fbb';
	transparenciaRelleno='0.5';
	colorTrazo='#000';
	anchoTrazo='1';
	_campo=_Capa['nom_col_num1'];
	_div=crearReglaDiv(_rn,_etiqueta,_valorMenor,_valorMayor,_valorIgual,colorRelleno,transparenciaRelleno,colorTrazo,anchoTrazo,_campo);
	_padre.parentNode.appendChild(_div); 
}	
	
function crearReglaDiv(_rn,_etiqueta,_valorMenor,_valorMayor,_valorIgual,colorRelleno,transparenciaRelleno,colorTrazo,anchoTrazo,_campo){
	_rr=document.createElement('div');
	_rr.setAttribute('name','rule');
	_rr.setAttribute('tipocampo','indefinido');
	
	_ident=document.createElement('div');
	_ident.setAttribute('class','identificacion');
	_rr.appendChild(_ident);		
	
	_tit=document.createElement('h3');
	_tit.innerHTML='Regla '+(Number(_rn)+1);
	_ident.appendChild(_tit);
	
	_aaa=document.createElement('a');
	_aaa.setAttribute('onclick','eliminarReglaSLD(this)');
	_aaa.setAttribute('id','eliminarRegla');
	_aaa.innerHTML="x";
	_aaa.titl="eliminar regla";
	_ident.appendChild(_aaa);
	
	_imp=document.createElement('input');
	_imp.setAttribute('id','etiqueta');
	_imp.setAttribute('type','text');
	_imp.value=_etiqueta;
	_ident.appendChild(_imp);

	
	_cond=document.createElement('div');
	_cond.setAttribute('class','condicion');
	_rr.appendChild(_cond);
	//console.log(_Capa);
	_camposNum=Array();
	for(_nc in _Capa){
		if(_nc.substring(0, 11)!='nom_col_num'){continue;}
		if(_Capa[_nc]==''){continue;}						
		_c=_nc.replace('nom_col_num', 'numero');
		_ob={
			campo:_c,
			nombre:_Capa[_nc]
		}
		_camposNum.push(_ob);		
	}
							
	_imp=document.createElement('select');
	_imp.setAttribute('onchange',' actualizarSimbolo(this)');
	_imp.setAttribute('id','campo');					
	_cond.appendChild(_imp);					
	
	_og=document.createElement('optgroup');
	_imp.appendChild(_og);
	_og.setAttribute('label',"Numéricos");
	
	for(_nc in _camposNum){
		_op=document.createElement('option');
		_op.value=_camposNum[_nc].campo;
		_op.setAttribute('tipo','numero');
		_op.innerHTML=_camposNum[_nc].nombre;
		_og.appendChild(_op);
		console.log(_camposNum[_nc].campo+'vs'+_campo);
		if(_camposNum[_nc].campo==_campo){_op.selected=true;}
	}

	
	
	_camposTex=Array();
	for(_nc in _Capa){
		//console.log('oo');
		//console.log(_nc+': '+_Capa[_nc]);
		//console.log(_nc);
		if(_nc.substring(0, 11)!='nom_col_tex'){continue;}
		//console.log('oo');
		if(_Capa[_nc]==''){continue;}						
		_c=_nc.replace('nom_col_text', 'texto');
		//console.log('oo');
		_ob={
			campo:_c,
			nombre:_Capa[_nc]
		}
		_camposTex.push(_ob);		
	}
	
	_og=document.createElement('optgroup');
	_imp.appendChild(_og);
	_og.setAttribute('label',"Texto");
	
	for(_nc in _camposTex){
		_op=document.createElement('option');
		_op.value=_camposTex[_nc].campo;
		_op.setAttribute('tipo','texto');
		_op.innerHTML=_camposTex[_nc].nombre;
		_og.appendChild(_op);
		//console.log(_camposTex[_nc].campo+'vs'+_campo);
		if(_camposTex[_nc].campo==_campo){_op.selected=true;}
	}
	
	_br=document.createElement('br');
	_cond.appendChild(_br);
	
	
	_div=document.createElement('div');
	_div.setAttribute('id','valores_rango');
	_cond.appendChild(_div);
		
	_imp=document.createElement('label');
	_imp.innerHTML='de:';
	_div.appendChild(_imp);
						
	_imp=document.createElement('input');
	_imp.setAttribute('id','desde');
	_imp.setAttribute('oninput',' actualizarSimbolo(this);simbolizarCapa();');
	_imp.setAttribute('type','number');
	_imp.value=_valorMayor;
	_div.appendChild(_imp);
	
	_br=document.createElement('br');
	_div.appendChild(_br);		
	
	_imp=document.createElement('label');
	_imp.innerHTML='a:';
	_div.appendChild(_imp);
	
	_imp=document.createElement('input');
	_imp.setAttribute('id','hasta');
	_imp.setAttribute('oninput',' actualizarSimbolo(this);simbolizarCapa();');
	_imp.setAttribute('type','number');
	_imp.value=_valorMenor;
	_div.appendChild(_imp);
	

	
	_div=document.createElement('div');
	_div.setAttribute('id','valores_texto');
	_cond.appendChild(_div);	
	
	_imp=document.createElement('label');
	_imp.innerHTML='es:';
	_div.appendChild(_imp);
						
	_imp=document.createElement('input');
	_imp.setAttribute('id','iguala');
	_imp.setAttribute('oninput',' actualizarSimbolo(this);simbolizarCapa();');
	_imp.setAttribute('type','text');
	_imp.value=_valorIgual;
	_div.appendChild(_imp);
	
	
	_sim=document.createElement('div');
	_sim.setAttribute('class','simbolo');
	_rr.appendChild(_sim);
		
		_con=document.createElement('div');
		_con.setAttribute('class','contienecolor');
		_con.style.border=anchoTrazo+'px solid '+colorTrazo;
			_imp=document.createElement('input');
			_imp.setAttribute('id','inputcolorrelleno');
			_imp.setAttribute('oninput',' actualizarSimbolo(this);simbolizarCapa();');
			_imp.style.opacity=transparenciaRelleno;
			_imp.setAttribute('type','color');
			_imp.setAttribute('name','colorRelleno');
			_imp.value=colorRelleno;
		_con.appendChild(_imp);
		_sim.appendChild(_con);
		
	
		_con=document.createElement('div');
		_con.setAttribute('class','grupoborde');
		_sim.appendChild(_con);
		
			_imp=document.createElement('input');
			_imp.setAttribute('id','inputcolortrazo');
			_imp.setAttribute('oninput',' actualizarSimbolo(this);simbolizarCapa();')
			_imp.setAttribute('type','color');
			_imp.setAttribute('name','colorBorde');
			_imp.value= colorTrazo;
			_con.appendChild(_imp);		
			
			_imp=document.createElement('input');
			_imp.setAttribute('id','inputanchotrazoRange');
			_imp.setAttribute('oninput',' actualizarSimbolo(this);simbolizarCapa();');
			_imp.setAttribute('type','range');
			_imp.setAttribute('name','anchoBorde');
			_imp.setAttribute('min','0');
			_imp.setAttribute('max','10');
			_imp.setAttribute('step','0.5');
			_imp.value=anchoTrazo;
			_con.appendChild(_imp);
			
			_imp=document.createElement('input');
			_imp.setAttribute('id','inputanchotrazoNumber');
			_imp.setAttribute('oninput',' actualizarSimbolo(this);simbolizarCapa();');
			_imp.setAttribute('name','anchoBorde');
			_imp.setAttribute('min','0');
			_imp.setAttribute('max','10');
			_imp.setAttribute('step','0.5');
			_imp.value=anchoTrazo;
			_con.appendChild(_imp);
								
			_imp=document.createElement('span');
			_imp.setAttribute('id','uniancho');
			_imp.innerHTML='px';
			_con.appendChild(_imp);
		
		
		_con=document.createElement('div');
		_con.setAttribute('class','gruporelleno');
		_sim.appendChild(_con);
		
			
		
		_imp=document.createElement('input');
		_imp.setAttribute('id','inputtransparenciarellenoRange');
		_imp.setAttribute('oninput',' actualizarSimbolo(this);simbolizarCapa();');
		_imp.setAttribute('type','range');
		_imp.setAttribute('name','transparenciaRelleno');
		_imp.setAttribute('min','0');
		_imp.setAttribute('max','100');
		_imp.value= transparenciaRelleno * 100;
		_con.appendChild(_imp);
		
		_imp=document.createElement('input');
		_imp.setAttribute('id','inputtransparenciarellenoNumber');
		_imp.setAttribute('oninput',' actualizarSimbolo(this);simbolizarCapa();');
		_imp.setAttribute('name','transparenciaRelleno');
		_imp.value=transparenciaRelleno * 100;
		_con.appendChild(_imp);
		
		_imp=document.createElement('span');
		_imp.setAttribute('id','unitransp');
		_imp.innerHTML='%';
		_con.appendChild(_imp);
		
		actualizarSimbolo(_rr);
		
	return(_rr);
}




function actualizarSimbolo(_this){
	
	_padre=_this;
	if(_padre.getAttribute('name')!='rule'){
		_padre=_padre.parentNode;
		if(_padre.getAttribute('name')!='rule'){
			_padre=_padre.parentNode;
			if(_padre.getAttribute('name')!='rule'){
				_padre=_padre.parentNode;		
				if(_padre.getAttribute('name')!='rule'){
					_padre=_padre.parentNode;
				}
			}		
		}
	}
	
	
	_tipocampo=_padre.querySelector('#campo option:checked').getAttribute('tipo');	
	_padre.setAttribute('tipocampo',_tipocampo);

	
	_variable=_this.getAttribute('name');
	if(_variable=='anchoBorde'){
		_color=_padre.querySelector('#inputcolortrazo').value;
		
		_padre.querySelector('.contienecolor').style.border=_this.value+"px solid "+_color;	
		_padre.querySelector('.contienecolor').style.margin=(10-_this.value)+"px";
		
		if(_this.value===''){_this.value=0;}
		_padre.querySelector("#inputanchotrazoNumber").value=_this.value;
		_padre.querySelector("#inputanchotrazoRange").value=_this.value;
	}else 	
	if(_variable=='colorBorde'){
		_ancho=_padre.querySelector('#inputanchotrazoNumber').value;		
		_padre.querySelector('.contienecolor').style.border=_ancho+"px solid "+_this.value;	
		
	}else 	
	if(_variable=='transparenciaRelleno'){
		
		
		_padre.querySelector('#inputcolorrelleno').style.opacity=_this.value/100;
		
		_padre.querySelector("#inputtransparenciarellenoRange").value=_this.value;
		_padre.querySelector("#inputtransparenciarellenoNumber").value=_this.value;
		
	}
	
	//simbolizarCapa();
		
}


function simbolizarCapa(){	
	_condiciones= condicionesSimbologiaInput();	
	//console.log(_condiciones);	
	_features=_lyrElemSrc.getFeatures();
		
		

	for(_fn in _features){
		_idreg=_features[_fn].getId();
		_feat=_features[_fn];
        _datasec=_Features[_idreg];
	
		_campo_et= document.querySelector('#simbologia [name="campo_et"]').value;
		
		//console.log(_Features[elem][_campoMM] +' >= '+_valorMM+'&&'+_Features[elem][_campomm]+' < '+ _valormm);

     	_c= hexToRgb(document.getElementById('inputcolorrelleno').value);
     	//console.log(document.getElementById('inputtransparenciarellenoNumber').value);
     	
			
        _n=(100 - (document.getElementById('inputtransparenciarellenoNumber').value) )/100;
        //console.log(_n);
        
        _rgba='rgba('+_c.r+', '+_c.g+', '+_c.b+', '+_n+')';

		_st= new ol.style.Style({
			fill: new ol.style.Fill({
				color: _rgba
		}),
		stroke: new ol.style.Stroke({
			color: document.getElementById('inputcolortrazo').value,
			width: document.getElementById('inputanchotrazoNumber').value
		}),
		image:new ol.style.Circle({
			fill: new ol.style.Fill({color: _rgba}),
			stroke: new ol.style.Stroke({
				color: document.getElementById('inputcolortrazo').value,
				width: document.getElementById('inputanchotrazoNumber').value
			}),
			   radius: 5
			})
		});
		
		_regla_asignada='no';
		
		for(_nc in _condiciones){
			
			if(_condiciones[_nc].campoig!=''&&_condiciones[_nc].campoig!=undefined){
				for(_k in _datasec){
					_kref=_k.replace('texto','nom_col_text');
					_kref=_kref.replace('numero','nom_col_num');
					
					//console.log(_k +' - '+_kref+' - '+_Capa[_kref] +' vs ' +_condiciones[_nc].campoig);
					if(_k == _condiciones[_nc].campoig){
						_campoig =_k; 
						//console.log('eureka. ahora: '+_campoMM);
						break;
					}		
				}
			}else{
				_campoig='';
			}
				
			if(_condiciones[_nc].campoMM!=''&&_condiciones[_nc].campoMM!=undefined){
				for(_k in _datasec){
					_kref=_k.replace('texto','nom_col_text');
					_kref=_kref.replace('numero','nom_col_num');
					
					//console.log(_k +' - '+_kref+' - '+_Capa[_kref] +' vs ' +_condiciones[_nc].campoMM);
					
					if(_k == _condiciones[_nc].campoMM){
						_campoMM =_k; 
						//console.log('eureka. ahora: '+_campoMM);
						break;
					}		
				}
			}else{
				_campoMM='';
			}
			
			if(_condiciones[_nc].campomm!=''&&_condiciones[_nc].campomm!=undefined){
				for(_k in _datasec){
					_kref=_k.replace('texto','nom_col_text');
					_kref=_kref.replace('numero','nom_col_num');
					//console.log(_kref+' - '+_Capa[_kref] +' vs ' +_campoMM);
					if(_k == _condiciones[_nc].campomm){
						_campomm =_k; 
						//console.log('eureka. ahora: '+_campomm);
						break;
					}	
				}
			}else{
				_campomm='';
			}
						
			
			if((_campoMM!=''&& _campomm!='')|| _campoig!=''){
				
				console.log('aaa');
				//console.log(_campoig);
				//console.log(Number(_Features[_idreg][_campoMM]) +'>= '+Number(_condiciones[_nc].valorMM));        	
				//console.log(Number(_Features[_idreg][_campomm]) +'< '+Number(_condiciones[_nc].valormm));
				//console.log(_Features[_idreg][_campoig] +' = '+_condiciones[_nc]._campoig);
				
				if(
					(
					Number(_Features[_idreg][_campoMM]) >= Number(_condiciones[_nc].valorMM)
					&&
					Number(_Features[_idreg][_campomm]) <  Number(_condiciones[_nc].valormm)
					||
					_Features[_idreg][_campoig] == _condiciones[_nc].valorig
					)
					&&
					_regla_asignada=='no'
					
				){
					_regla_asignada='si';
					_c= hexToRgb(_condiciones[_nc].colorRelleno);
					//console.log(_condiciones[_nc].transparenciaRelleno);
					_n=(1 - (_condiciones[_nc].transparenciaRelleno));
					_rgba='rgba('+_c.r+', '+_c.g+', '+_c.b+', '+_n+')';
			
					//console.log(_rgba);
					_st= new ol.style.Style({
						
						fill: new ol.style.Fill({
							color: _rgba		
						}),
						stroke: new ol.style.Stroke({
							color: _condiciones[_nc].colorTrazo,
							width: _condiciones[_nc].anchoTrazo
						}),
						image:new ol.style.Circle({
						   fill: new ol.style.Fill({color: _rgba}),
						   stroke: new ol.style.Stroke({
								color: _condiciones[_nc].colorTrazo,
								width: _condiciones[_nc].anchoTrazo
						}),
						   radius: 5
						}),
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
					
					_st.getText().setText(_Features[_idreg][_campo_et]);	    	
				}
			}else if(_regla_asignada=='no'){
				
				_regla_asignada='si';
				_c= hexToRgb(_condiciones[_nc].colorRelleno);
				//console.log(_condiciones[_nc].transparenciaRelleno);
				_n=(1 - (_condiciones[_nc].transparenciaRelleno));
				
				_rgba='rgba('+_c.r+', '+_c.g+', '+_c.b+', '+_n+')';
		
				//console.log(_rgba);
				_st= new ol.style.Style({
					
				  fill: new ol.style.Fill({
					color: _rgba		
				  }),
				  stroke: new ol.style.Stroke({
					color: _condiciones[_nc].colorTrazo,
					width: _condiciones[_nc].anchoTrazo
				  }),
				  image:new ol.style.Circle({
					   fill: new ol.style.Fill({color: _rgba}),
					   stroke: new ol.style.Stroke({
							color: _condiciones[_nc].colorTrazo,
							width: _condiciones[_nc].anchoTrazo
					}),
					   radius: 5
					})
				});
				
			}
			
			
		}
		
    	_feat.setStyle (_st);

   
    }
}

function condicionesSimbologiaInput(){
	_condiciones=Array();
    
    _rules=document.querySelectorAll("div[name='rule']");
    //console.log(_rules);
    for(_nr in _rules){
		
    	if(typeof _rules[_nr] != 'object'){continue;}
    	
    	
    	_con={
    		'tipo':'rango',    	
        	'campoMM':_rules[_nr].querySelector('#campo').value,
        	'valorMM':_rules[_nr].querySelector('#desde').value,
        	'campomm':_rules[_nr].querySelector('#campo').value,
        	'valormm':_rules[_nr].querySelector('#hasta').value,
        	'campoig':_rules[_nr].querySelector('#campo').value,
        	'valorig':_rules[_nr].querySelector('#iguala').value,
        	'colorRelleno':_rules[_nr].querySelector('#inputcolorrelleno').value,
        	'transparenciaRelleno':1-(_rules[_nr].querySelector('#inputtransparenciarellenoRange').value/100),
        	'colorTrazo':_rules[_nr].querySelector('#inputcolortrazo').value,
        	'anchoTrazo':_rules[_nr].querySelector('#inputanchotrazoRange').value
        }
        _condiciones.push(_con);
    }
    

	_con={
		'tipo':'default',    	
    	'colorRelleno':document.querySelector('#simbologia > #inputcolorrelleno').value,
    	'transparenciaRelleno':1-(document.querySelector('#simbologia > #inputtransparenciarellenoNumber').value/100),
       // 'transparenciaRelleno':1-(_rules[_nr].querySelector('#inputtransparenciarellenoRange').value/100),
    	'colorTrazo':document.querySelector('#simbologia > #inputcolortrazo').value,
    	'anchoTrazo':document.querySelector('#simbologia > #inputanchotrazoRange').value
    }
    _condiciones.push(_con);        
	
	return _condiciones;	        
}
    
var _Features={};

function cargarValoresCapaExistQuery(){
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa'); 
    var _CodMarco = getParameterByName('cod');
    _parametros = {
		'codMarco':_CodMarco,
		'idcapa': idCapa,
		'RecorteDeTrabajo' : _RecorteDeTrabajo
    };
    $.ajax({
        url:   './app_capa/app_capa_consultar_registros.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res != 'exito'){reutrn;}
            
            console.log('regs:'+Object.keys(_res.data.registros).length);
            if(Object.keys(_res.data.registros).length>0){
				_Features=_res.data.registros;
				console.log(_Features);
				document.querySelector('#cantreg #contador').innerHTML=_res.data.cant_reg;
            	cargarFeatures();
            	simbolizarCapa();
			}
        }
    });
}

function hexToRgb(hex) {
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
        return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

var xmlDoc;
function cargarFeatures(){
    _lyrElemSrc.clear();
    
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
			
			_campoMM='';
	        _valorMM='';
	        _campomm='';
	        _valormm='';
	        _campoig='';
	        _valorig='';
	        
			var _mayorIgualQue = _larule.getElementsByTagName("ogc:PropertyIsGreaterThanOrEqualTo")[0];   			
			if(_mayorIgualQue!= null){
				for(var node in _mayorIgualQue.childNodes){		            	
					if (_mayorIgualQue.childNodes[node].nodeName == "ogc:PropertyName"){
						_campoMM = _mayorIgualQue.childNodes[node].textContent;
					}
					if (_mayorIgualQue.childNodes[node].nodeName == "ogc:Literal"){
						_valorMM = _mayorIgualQue.childNodes[node].textContent;
					}
				}
			}
			
			
			
	        var _menorQue = _larule.getElementsByTagName("ogc:PropertyIsLessThan")[0];   	
	        if(_menorQue!= null){		
				for(var node in _menorQue.childNodes){		            	
					if (_menorQue.childNodes[node].nodeName == "ogc:PropertyName"){
						_campomm = _menorQue.childNodes[node].textContent;
					}
					if (_menorQue.childNodes[node].nodeName == "ogc:Literal"){
						_valormm = _menorQue.childNodes[node].textContent;
					}
				}
			}
			
	        var _igualQue = _larule.getElementsByTagName("ogc:PropertyIsEqualTo")[0];   	
	        if(_igualQue!= null){		
				for(var node in _igualQue.childNodes){		            	
					if (_igualQue.childNodes[node].nodeName == "ogc:PropertyName"){
						_campoig = _igualQue.childNodes[node].textContent;
					}
					if (_igualQue.childNodes[node].nodeName == "ogc:Literal"){
						_valorig = _igualQue.childNodes[node].textContent;
					}
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
				'tipo':'intervalo',
	        	'campoMM':_campoMM,
	        	'valorMM':_valorMM,
	        	'campomm':_campomm,
	        	'valormm':_valormm,
	        	'campoig':_campoig,
	        	'valorig':_valorig,
	        	'colorRelleno':colorRelleno,
	        	'transparenciaRelleno':transparenciaRelleno,
	        	'colorTrazo':colorTrazo,
	        	'anchoTrazo':anchoTrazo
	        }
	        _condiciones.push(_con);
		}
	}
	
	console.log('regs:'+Object.keys(_Features).length);


	//_cc=0;
    for(var elem in _Features){
		//_cc++;
		//if(_cc<500){continue;}
		if(_Features[elem].geotx==''){continue;}//este registro debería ser eliminado
		if(_Features[elem].geotx==null){continue;}//este registro debería ser eliminado
        var format = new ol.format.WKT();	
        var _feat = format.readFeature(_Features[elem].geotx, {
            dataProjection: 'EPSG:3857',
            featureProjection: 'EPSG:3857'
        });
        
        
		//console.log(_Features[elem].id+': '+_Features[elem].geotx.substring(0,50));		
		if(_Features[elem].id!=null){
			_feat.setId(_Features[elem].id);
		}
        //console.log('ok1');

        _feat.setProperties({
            'id':_Features[elem].id
        });
        
        
        if(_condiciones.length>0){
			_datasec=_Features[elem];
			
			for(_k in _datasec){
				_kref=_k.replace('texto','nom_col_text');
				_kref=_kref.replace('numero','nom_col_num');
				//console.log(_kref+' - '+_Capa[_kref] +' vs ' +_campoMM);
				if(_Capa[_kref] == _campoig){
					_campoig =_k; 
					console.log('eureka. ahora: '+_campoig);
					break;
				}		
			}
			
			
			for(_k in _datasec){
				_kref=_k.replace('texto','nom_col_text');
				_kref=_kref.replace('numero','nom_col_num');
				//console.log(_kref+' - '+_Capa[_kref] +' vs ' +_campoMM);
				if(_Capa[_kref] == _campoMM){
					_campoMM =_k; 
					console.log('eureka. ahora: '+_campoMM);
					break;
				}		
			}
			
			for(_k in _datasec){
				
				_kref=_k.replace('texto','nom_col_text');
				_kref=_kref.replace('numero','nom_col_num');
				//console.log(_kref+' - '+_Capa[_kref] +' vs ' +_campoMM);
				if(_Capa[_kref] == _campomm){
					_campomm =_k; 
					//console.log('eureka. ahora: '+_campomm);
					break;
				}	
						
			}
		}
		//console.log('ok2');
		//console.log('condiciones: ');
		//console.log(_condiciones);
		//console.log(_Features[elem][_campoMM] +' >= '+_valorMM+'&&'+_Features[elem][_campomm]+' < '+ _valormm);
		//console.log('campo igs: '+_campoig);
		//console.log(_Features[elem][_campoig] +' = '+_valorig);
		
			
         _c= hexToRgb(document.getElementById('inputcolorrelleno').value);
         //console.log('transp: '+document.getElementById('inputtransparenciarellenoNumber').value);
	        _n=(1 - (document.getElementById('inputtransparenciarellenoNumber').value) * 1.0 / 100);
	        _rgba='rgba('+_c.r+', '+_c.g+', '+_c.b+', '+_n+')';
	
	        _st= new ol.style.Style({
	          fill: new ol.style.Fill({
	            color: _rgba
	
	          }),
	          stroke: new ol.style.Stroke({
	            color: document.getElementById('inputcolortrazo').value,
	            width: document.getElementById('inputanchotrazoNumber').value
	          }),
	          image: new ol.style.Circle({
				   fill: new ol.style.Fill({
							color: _rgba
				
						  }),
				   stroke:  new ol.style.Stroke({
							color: document.getElementById('inputcolortrazo').value,
							width: document.getElementById('inputanchotrazoNumber').value
						  }),
				   radius: 5
				 }),
	        });
	        
	        for(_nc in _condiciones){
				if(
					(
						Number(_Features[elem][_campoMM]) >= Number(_condiciones[_nc].valorMM)
						&&
						Number(_Features[elem][_campomm]) <  Number(_condiciones[_nc].valormm)
					)
					||
					(
						_Features[elem][_campoig] ==  _condiciones[_nc].valorig
					)
				){
					//console.log(_condiciones[_nc].valorig);
					
					_c= hexToRgb(_condiciones[_nc].colorRelleno);
					
					
			        _n=(1 - (_condiciones[_nc].transparenciaRelleno));
			        //console.log(_condiciones[_nc].transparenciaRelleno);
			        _n=_condiciones[_nc].transparenciaRelleno;
			        //console.log(_n);
			       
			        _rgba='rgba('+_c.r+', '+_c.g+', '+_c.b+', '+_n+')';
			        //console.log(_rgba);
			
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
			//console.log('ok3');
			//console.log(_cc);
        _lyrElemSrc.addFeature(_feat);
		//console.log('ok4');
        _MapaCargado='si';
    }
	
    _ext= _lyrElemSrc.getExtent();

    setTimeout(function(){
        mapa.getView().fit(_ext, { duration: 1000 });
    }, 200);
}

function generarNuevaCapa(){
    //consultar si ya existe una capa sin publicar para este autor y sino crearla
    //var _this = _this;
    
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '0';
    
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada,
        'crear_capa':'si'
    };
    
    $.ajax({
            url:   './app_capa/app_capa_consultar.php',
            type:  'post',
            data: parametros,
            success:  function (response){   
				
                var _res = $.parseJSON(response);
                //console.log(_res);
                
                if(_res.res!='exito'){return;}
                
				_Capa=_res.data;
				if (_res.data != null){
					cargarValoresCapaExist(_res);
				} else {
					generarNuevaCapaQuery();
				}
			
            }
    });
}

function generarNuevaCapaQuery(_this){
    //Genera una capa vacia en la base de datos
    var _this = _this;
    
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco
    };
    
    $.ajax({
            url:   './app_capa/app_capa_generar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
					
					generarNuevaCapa();
                    //asignarIdCapa(_res.data.id);
                }else{
                    alert('error asf0jg44ffgh');
                }
            }
    });
}

function asignarIdCapa(idCapa){
    document.getElementById('divCargaCapa').setAttribute('idcapa', idCapa);
}


function cargarValoresCapasPublicadas(_res){
    
    for (var elemCapa in _res.data){
        var divRoot = document.getElementById('listacapaspublicadas');
        var filaCapa = document.createElement('a');
        filaCapa.setAttribute('idcapa', _res.data[elemCapa]["id"]);
        filaCapa.setAttribute('class', 'filaCapaLista');
        filaCapa.setAttribute('onclick', "accionCargarCapaPublicada(this,"+_res.data[elemCapa]["id"]+")" );
        var capaId = document.createElement('div');
        capaId.setAttribute('id','capaIdLista');
        capaId.innerHTML = "ID <span class='idn'>" + _res.data[elemCapa]["id"]+'</span>';
        var capaNombre = document.createElement('div');
        capaNombre.setAttribute('id','capaNombreLista');
        capaNombre.innerHTML = _res.data[elemCapa]["nombre"];
        var capaAu = document.createElement('div');
        capaAu.setAttribute('id','capaAutoriaLista');
        capaAu.innerHTML ='('+ _res.data[elemCapa].registros+')por: '+ _res.data[elemCapa]["autornom"]+' '+_res.data[elemCapa]["autorape"];
        var capaDescripcion = document.createElement('div');
        capaDescripcion.setAttribute('id','capaDescripcionLista');
        capaDescripcion.innerHTML = _res.data[elemCapa]["descripcion"];
        
        var capaDescarga = document.createElement('a');
        capaDescarga.innerHTML='<img class="imgdescarga" src="./comun_img/descargar.png"><img class="imgcargando" src="./comun_img/cargando.gif">'
        capaDescarga.setAttribute('onclick','event.stopPropagation();descargarSHP(this.parentNode.getAttribute("idcapa"))');
        capaDescarga.setAttribute('class','botondescarga');
        
        filaCapa.appendChild(capaId);
        filaCapa.appendChild(capaDescarga);
        filaCapa.appendChild(capaNombre);
        filaCapa.appendChild(capaDescripcion);
        filaCapa.appendChild(capaAu);
        divRoot.appendChild(filaCapa);
    }
   
}


var _Capa = {};
function cargarDatosCapaPublicada(idcapa){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');

    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': '1',
        'idcapa': idcapa
    };
    
    $.ajax({
            url:   './app_capa/app_capa_consultar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                	
                	if(_res.data.autor==_IdUsu){
                		
                		document.querySelector('#cuadrovalores').setAttribute('autoria','propia');                		
                		if(_res.data.zz_publicada=='1'){
                			document.querySelector('#cuadrovalores').setAttribute('modo','muestracapaexistente');
                		}else{
                			document.querySelector('#cuadrovalores').setAttribute('modo','subiendonuevacapa');
                		}
                		                		
                	}else{                		
                		document.querySelector('#cuadrovalores').setAttribute('autoria','ajena');                		
                	}
                	document.querySelector('#cuadrovalores').setAttribute('publicada',_res.data.zz_publicada);                	
                	_Capa=_res.data;                	
                	cargarValoresCapaExist(_res);                    
                	
                }else{
                	_Capa = {};
                    alert('error tf0jg44ff0gh');
                }
            }
    });
}

function toglevalorSiNo(_this){
	_nom=_this.getAttribute('for');	
	if(_this.checked==true){
		_val=_this.getAttribute('valorsi');
	}else{
		_val=_this.getAttribute('valorno');
	}
	_this.parentNode.querySelector("[name='"+_nom+"']").value=_val;
}

function toglevalorSiNoRev(_this){
	_for=_this.getAttribute('name');
	_val=_this.value;
	_inp=_this.parentNode.querySelector('[for="'+_for+'"]');
	if(_inp.getAttribute('valorsi')==_val){
		_inp.checked=true;
	}else{
		_inp.checked=false;
	}
}



function exportarCapaMenu(){
	
	document.querySelector('#exportarCapaMenu #lista').innerHTML='';
	document.querySelector('#exportarCapaMenu').style.display='block';
	
	var parametros = {
        'accion': 'app_capa'
    };
    
    $.ajax({
            url:   './sistema/sis_consulta_marcos.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                	
					for(_cod in _res.data){
						_a=document.createElement('a');
						_a.setAttribute('codigo',_cod);
						_a.setAttribute('onclick','exportarCapa(this.getAttribute("codigo"))');
						_a.innerHTML=_res.data[_cod].nombre+' - '+_res.data[_cod].nombre_oficial;
						document.querySelector('#exportarCapaMenu #lista').appendChild(_a);								
					}
                }
               
            }
        });
}



function exportarCapa(_codMarcDest){
	
	document.querySelector('#exportarCapaMenu').style.display='none';
	
	var parametros = {
        'idCapa': document.querySelector('#divCargaCapa.formCargaCapa').getAttribute('idcapa'),
        'codMarcoDestino': _codMarcDest,
        'codMarco': getParameterByName('cod'),
    };
	
	 $.ajax({
        url:   './app_capa/app_capa_exportar.php',
        type:  'post',
        data: parametros,
        success:  function (response)
        {   
            var _res = $.parseJSON(response);
            //console.log(_res);
            if(_res.res=='exito'){
            	
				for(_cod in _res.data){
					_a=document.createElement('a');
					_a.setAttribute('codigo',_cod);
					_a.setAttribute('onclick','exportarCapa(this.getAttribute("codigo"))');
					_a.innerHTML=_res.data[_cod].nombre+' - '+_res.data[_cod].nombre_oficial;
					document.querySelector('#exportarCapaMenu #lista').appendChild(_a);
										
				}
            }
           
        }
    });
	
}



function generarTarjetaRegistro(){
	
		//al hacer clck sobre un polígono genera una copia de los datos del registro en una tarjeta
		
		_orig=document.querySelector('#portamapa #auxiliar');
		_clon=_orig.cloneNode(true);
		_clon.removeAttribute('id');
		_clon.setAttribute('class','auxiliar');
		_clon.setAttribute('nuevo','si');
		
		_idreg=_clon.getAttribute('idreg');
		_a=document.createElement('a');
		_a.innerHTML="<img src='./comun_img/icon-delete-16.jpg'>";
		_a.setAttribute('onclick','borrarRegistro('+_idreg+')');
		_a.setAttribute('class','eliminar');
		_clon.appendChild(_a);
		
		_a=document.createElement('a');
		_a.innerHTML='cerrar';
		_a.setAttribute('onclick','borrarTarjeta(this.parentNode)');
		_a.setAttribute('id','b_cerrar');
		_a.setAttribute('class','boton_gris');
		_clon.appendChild(_a);
		
		_a=document.createElement('a');
		_a.innerHTML="<img src='./comun_img/dibujar.png'>";
		_a.setAttribute('onclick','formularRegistro('+_idreg+')');
		_a.setAttribute('class','formular');
		_clon.appendChild(_a);
		
		
		document.querySelector('#portamapa #tarjetas').insertBefore(_clon, document.querySelector('#portamapa #tarjetas').childNodes[0]);
		
		setTimeout(() => {
		  _a=document.querySelector('#portamapa .auxiliar[nuevo="si"]');
		  _a.removeAttribute('nuevo');
		}, "50");
}

var _trans={
	'nom_col_num1':'numero1',
	'nom_col_num2':'numero2',
	'nom_col_num3':'numero3',
	'nom_col_num4':'numero4',
	'nom_col_num5':'numero5',
	'nom_col_text1':'texto1',
	'nom_col_text2':'texto2',
	'nom_col_text3':'texto3',
	'nom_col_text4':'texto4',
	'nom_col_text5':'texto5',
	'numero1':'nom_col_num1',
	'numero2':'nom_col_num2',
	'numero3':'nom_col_num3',
	'numero4':'nom_col_num4',
	'numero5':'nom_col_num5',
	'texto1':'nom_col_text1',
	'texto2':'nom_col_text2',
	'texto3':'nom_col_text3',
	'texto4':'nom_col_text4',
	'texto5':'nom_col_text5'	
	};



function cerrarformEditaReg(){
	
	_form=document.querySelector('#formulario_registro');
	_form.setAttribute('activo','no');	
}


function formularRegistro(_idreg){
	
	_form=document.querySelector('#formulario_registro');
	_form.setAttribute('activo','si');
	_divcampos=_form.querySelector('#campos');
	_divcampos.innerHTML='';
	_df=_Features[_idreg];
	
	_form.querySelector('[name="idr"]').value=_df.id;
	_form.querySelector('[name="idcapa"]').value=_Capa.id;
	
	for(_campo in _df){
		if(_campo=='id'){continue;}
		if(_campo=='geotx'){continue;}
		
		_f=document.createElement('div');
		_f.setAttribute('class','fila');
		_divcampos.appendChild(_f);
		
		_l=document.createElement('label');
		_l.innerHTML=_Capa[_trans[_campo]];
		_f.appendChild(_l);
		
		_i=document.createElement('input');
		_i.value=_df[_campo];
		_i.setAttribute('name',_campo);
		_f.appendChild(_i);
		
	}
	
}



function borrarTarjeta(_tarjeta){
	_tarjeta.parentNode.removeChild(_tarjeta);
}


function formularNuevoCampo(){
	
	document.querySelector('#configurarCampos #form_NuevoCampo').setAttribute('estado','activo');
	document.querySelector('#configurarCampos #form_NuevoCampo option[value="num"]').setAttribute('disabled','disabled');
	document.querySelector('#configurarCampos #form_NuevoCampo option[value="num"]').setAttribute('libre','');
	
	
	document.querySelector('#configurarCampos #form_NuevoCampo option[value="tex"]').setAttribute('disabled','disabled');
	document.querySelector('#configurarCampos #form_NuevoCampo option[value="tex"]').setAttribute('libre','');
	
	
	for(_p in _Capa){
		
		if(_p.slice(0,11)=='nom_col_num'){
			//console.log(_p);
			//console.log(_Capa[_p]);
			if(_Capa[_p]==''||_Capa[_p]==null){
				console.log(_p);
				console.log(_p.slice(11,12));
				document.querySelector('#configurarCampos #form_NuevoCampo option[value="num"]').removeAttribute('disabled');
				
				if(document.querySelector('#configurarCampos #form_NuevoCampo option[value="num"]').getAttribute('libre')==''){
					document.querySelector('#configurarCampos #form_NuevoCampo option[value="num"]').setAttribute('libre',_p.slice(11,12));
				}
				//campo numerico disponible
			}
		}
		
		if(_p.slice(0,11)=='nom_col_tex'){
			if(_Capa[_p]==''||_Capa[_p]==null){
				
				document.querySelector('#configurarCampos #form_NuevoCampo option[value="tex"]').removeAttribute('disabled');
				//console.log(_p.slice(12,1));
				if(document.querySelector('#configurarCampos #form_NuevoCampo option[value="tex"]').getAttribute('libre')==''){
					document.querySelector('#configurarCampos #form_NuevoCampo option[value="tex"]').setAttribute('libre',_p.slice(12,13));
				}
				//campo numerico disponible
			}
		}		
	}	
}


function formularCalculo(_boton){
	//alert('función en desarrollo   ¨\\_:L_/¨');
	
	_campo=_boton.parentNode.parentNode.getAttribute('id');
	
	
	document.querySelector('#form_calculoCampo').setAttribute('activo','si');
	
	document.querySelector('#form_calculoCampo input[name="campo"]').value=_campo;
}


function cargaSimbologia(_idsim){

	console.log(_idsim);
	
	if(_idsim=='-1'){
		//crear simbologia
		crearSimbologia();
	}else if(_idsim==0){
		//cargar simbologia por defecto
		document.querySelector('#simbologia #reglas_adicionales').innerHTML='';
		
		sldHtml(_Capa.sld);
		
	}else{
		document.querySelector('#simbologia #reglas_adicionales').innerHTML='';
		
		sldHtml(_Capa.simbologias[_idsim].sld);
		//cargar simbologia adicional
	}
}
