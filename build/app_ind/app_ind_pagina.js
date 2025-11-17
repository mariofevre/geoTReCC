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

function accionCreaCancelar(_this){
    limpiarFormularioIndicadores();
    limpiarFormularioSeleccionCapa();
    limpiarFormularioCapa();
    
    document.getElementById('formEditarIndicadores').style.display='none';
    
    $('#cuadrovalores').attr('estado','inicio');
    $('#cuadrovalores').attr('modo','');
	//document.getElementById('formSeleccionInd').style.display='none';
    //document.getElementById('divListaIndicadoresCarga').style.display='none';
    document.querySelector('#divListaIndicadoresCarga #botonSeleccionarIndCambio').removeAttribute('idind');
    document.getElementById('divMenuAccionesCrea').style.display='none';
    
    
    accionCancelarSeleccionCapa(_this);
}

function accionCreaEliminar(_this){
    //console.log('eliminarCandidatoIndicador()');

    if(confirm("¿Confirma que quiere eliminar este candidato a versión? \n Si lo hace se guardará registro en la papelera de los datos cargados en el formulario.")){
        //console.log('o');
        eliminarCandidatoIndicador(_this);
    }
}

function accionCreaGuardar(){

	_form=document.querySelector('#formEditarIndicadores');
    if (_form.querySelector('#indNombre').value != ''){
        editarCampoInd('indNombre');
    }
    if (_form.querySelector('#indDescripcion').value != ''){
        editarCampoInd('indDescripcion');
    }
    if (_form.querySelector('#inputFechaDesde').value != ''){
        editarCampoInd('inputFechaDesde');
    }
    if (_form.querySelector('#inputFechaHasta').value != ''){
        editarCampoInd('inputFechaHasta');
    }
    
    
    
    if (_form.querySelector('#funcionalidadSelector').value == 'nuevaGeometria'){
        editarCampoInd('calc_buffer');
    }
    
    if (_form.querySelector('#funcionalidadSelector').value == 'nuevaGeometria'){
        editarCampoInd('calc_superp');
    }
    
    if (_form.querySelector('#funcionalidadSelector').value == 'nuevaGeometria'){
        editarCampoInd('calc_zonificacion');
    }
    
    
    editarIndPeriodicidad(null, _form.querySelector('#periodicidadSelector'));
    editarIndFuncionalidad(null, _form.querySelector('#funcionalidadSelector'));
    
    var idcapa = document.getElementById('capaseleccionada').getAttribute('idcapa');
    if (idcapa != '' && idcapa > 0){
        editarCampoIndParametros('idcapa', idcapa);
    }
    
    var divColumnasValores = document.getElementById('columnasValores');
    for (var child in divColumnasValores.children){
         if (divColumnasValores.children[child].nodeType == 1 &&
                divColumnasValores.children[child].getAttribute('id') == 'nuevacolumna'){
           guardarColumna(divColumnasValores.children[child]);
        }
    }

    editarRepresentacionCampo();
    editarRepresentacionValorMaximo();
    editarRepresentacionValorMinimo();
}


function formularioAmpliado(_modo){

	if(_modo=='modelo'){
		_dat=_DataModelo;
	}
	
	if(_modo=='indicador'){
		_dat=_DataIndicador;
	}
	
	console.log(_dat);
	
	
	$('#form_ind_exp').attr('estado','activo');
	$('#form_ind_exp').attr('modo',_modo);
	
	$('#form_ind_exp [name="'+'id'+'"]').val(_dat.id);
	
	$('#form_ind_exp [name="'+'nombre'+'"]').val(_dat.nombre);
	$('#form_ind_exp [name="'+'descripcion'+'"]').val(_dat.descripcion);
	$('#form_ind_exp [name="'+'unidad_medida'+'"]').val(_dat.unidad_medida);
	$('#form_ind_exp [name="'+'relevancia_acc'+'"]').val(_dat.relevancia_acc);
	$('#form_ind_exp [name="'+'limitaciones'+'"]').val(_dat.limitaciones);
	$('#form_ind_exp [name="'+'ejemplo'+'"]').val(_dat.ejemplo);
	$('#form_ind_exp [name="'+'datos_input'+'"]').val(_dat.datos_input);
	$('#form_ind_exp [name="'+'fuentes_input'+'"]').val(_dat.fuentes_input);
	$('#form_ind_exp [name="'+'calculo'+'"]').val(_dat.calculo);
	$('#form_ind_exp [name="'+'escala_espacial'+'"]').val(_dat.escala_espacial);
	$('#form_ind_exp [name="'+'desagrgacion'+'"]').val(_dat.desagrgacion);
	$('#form_ind_exp [name="'+'valoracion'+'"]').val(_dat.valoracion);
	
	
	
	$('#form_ind_exp [name="'+'funcionalidad'+'"]').val(_dat.funcionalidad);
	$('#form_ind_exp [name="'+'periodicidad'+'"]').val(_dat.periodicidad);
	$('#form_ind_exp [name="'+'fechadesde'+'"]').val(_dat.fechadesde);
	$('#form_ind_exp [name="'+'fechahasta'+'"]').val(_dat.fechahasta);
	$('#form_ind_exp [name="'+'representar_campo'+'"]').val(_dat.representar_campo);
	$('#form_ind_exp [name="'+'representar_val_max'+'"]').val(_dat.representar_val_max);
	$('#form_ind_exp [name="'+'representar_val_min'+'"]').val(_dat.representar_val_min);	
	$('#form_ind_exp [name="'+'calc_buffer'+'"]').val(_dat.calc_buffer);
	$('#form_ind_exp [name="'+'calc_superp'+'"]').val(_dat.calc_superp);
	$('#form_ind_exp [name="'+'calc_zonificacion'+'"]').val(_dat.calc_zonificacion);
	$('#form_ind_exp [name="'+'calc_superp_campo'+'"]').val(_dat.calc_superp_campo);



	if(_modo=='modelo'){
		_cont=document.querySelector('#form_ind_exp #matriz_clasif #categorias');
		_cont.innerHTML='';
		for(_tn in _DataListaModelos.tagsTiposOrden){
			
			_idti=_DataListaModelos.tagsTiposOrden[_tn];
			_datti=_DataListaModelos.tagsTipos[_idti];
			_tipo=_datti.tipo;
			
			_d=document.createElement('div');
			_d.setAttribute('class','col');
			_cont.appendChild(_d);
			
			_h=document.createElement('h6');
			_h.innerHTML=_tipo;
			_d.appendChild(_h);
				
			for(_nt in _DataListaModelos.tagsOrden[_tipo]){
				_idt=_DataListaModelos.tagsOrden[_tipo][_nt];
				_d=document.createElement('div');
				_d.setAttribute('class','row tag');
				_d.setAttribute('id_tag',_idt);
				_h.appendChild(_d);
								
				_l=document.createElement('input');
				_d.appendChild(_l);
				_l.setAttribute('name','estado');
				_l.setAttribute('type','checkbox');
				
				if(_DataModelo.tag_link[_idt].activo=='1'){
					_l.checked=true;
				}else{
					_l.checked=false;
				}
				
				_l=document.createElement('label');
				_d.appendChild(_l);
				_l.innerHTML=_DataListaModelos.tags[_idt].nombre;				
				
				_l=document.createElement('input');
				_l.setAttribute('name','comentario');
				_d.appendChild(_l);
				_l.checked=_DataModelo.tag_link[_idt].comentario;

			}
	
			
		}
		
		
		_lista=document.querySelector('#requerimientos #requerimientos_app');
		_lista.innerHTML='';
		
		
		
		_fila=document.createElement('div');
		_lista.appendChild(_fila);
		_fila.setAttribute('class','row');
		
		_c=0;
		
		
		
		for(_accnom in _Acciones.acciones){
			if(_accnom==''){continue;}
			if(_c==4){
				_fila=document.createElement('div');
				_lista.appendChild(_fila);
				_fila.setAttribute('class','row');				
				_c=0;
			}
			_c++;
			
			_accndata=_Acciones.acciones[_accnom];	
			
			
			_di=document.createElement('div');
			_fila.appendChild(_di);
			_di.setAttribute('class','col app');
			_di.setAttribute('cod_app',_accnom);
			
			
			_li=document.createElement('input');
			_di.appendChild(_li);
			_li.setAttribute('type','checkbox');
			_li.setAttribute('name','estado');
			

			
			_sp=document.createElement('span');
			_di.appendChild(_sp);		
			_sp.setAttribute('class','img');
			_la=document.createElement('img');
			_la.setAttribute('src','./comun_img/'+_accnom+'.png');
			_la.setAttribute('alt',_accnom);
			_la.setAttribute('title',_accndata.resumen);
			_sp.appendChild(_la);
			

			_la=document.createElement('span');
			_la.innerHTML=_accndata.resumen;
			_di.appendChild(_la);
			
			_li2=document.createElement('input');
			_di.appendChild(_li2);
			_li2.setAttribute('type','text');
			_li2.setAttribute('name','comentario');

			_acc=_accnom.replace('app_','');
			if(_dat.requerimientos[_acc]!=undefined){
				_li.checked=true;
				_li2.value=_dat.requerimientos[_acc].descripcion;
				//document.querySelector('#configModal #apps').setAttribute('estado','lleno');
			}

		}
		while(_c<4){			
				
			_di=document.createElement('div');
			_fila.appendChild(_di);
			_di.setAttribute('class','col');
				_c++;
		}
	}	
}


function accionCreaPublicar(_this){
    
    guardarInd();
    console.log('o');
    publicarIndicador();
    if(confirm('Esta seguro que desea publicar este indicador? \nUna vez publicado no se pueden hacer cambios')){
        publicarIndicador();
    }
}

function accionCargaCancelar(){
    limpiarFormularioIndicadores();
    limpiarFormularioSeleccionCapa();
    limpiarFormularioCapa();
    limpiarFormularioIndPublicados();
    
    document.querySelector("#cuadrovalores").setAttribute("formeditarindicadores","no");
    document.querySelector("#cuadrovalores").setAttribute("formseleccionind","si");
    document.querySelector("#cuadrovalores").setAttribute("formseleccionmod","no");
    document.querySelector("#cuadrovalores").setAttribute("formdivlistaindicadorescarga","no");
    
    $('#cuadrovalores').attr('estado','inicio');
    $('#cuadrovalores').attr('modo','');   
    //document.getElementById('formSeleccionInd').style.display='none';
    //document.getElementById('divListaIndicadoresCarga').style.display='none';
    
    document.querySelector('#divListaIndicadoresCarga #botonSeleccionarIndCambio').removeAttribute('idind');
    //document.getElementById('divMenuAccionesCarga').style.display='none';
    //document.getElementById('botonCrearIndicador').style.display='block';
    
   _source_ind.clear();
   _source_ind_buffer.clear();
   _source_ind_superp.clear();
    accionCargarIndicador();
    
    
    //accionCancelarSeleccionCapa(_this);
}

function accionCrearIndicador(_this){

    generarNuevoIndicador();
    document.querySelector("#cuadrovalores").setAttribute("formeditarindicadores","si");
    document.querySelector("#cuadrovalores").setAttribute("formseleccionind","no");
    document.querySelector("#cuadrovalores").setAttribute("formseleccionmod","no");
    document.querySelector("#cuadrovalores").setAttribute("formCargaInd","si");
    document.querySelector("#cuadrovalores").setAttribute("formdivlistaindicadorescarga","no");

  
    document.querySelector("#cuadrovalores").setAttribute("estado","elemento"); //deprecar 
    document.getElementById('divSeleccionIndCuerpo').style.display='none'; //deprecar 
        
}


function accionCrearModelo(){
	
    generarNuevoModelo();
    document.getElementById('divMenuAccionesCrea').style.display='none';
    
    $('#cuadrovalores').attr('estado','');
    //document.getElementById('botonCrearIndicador').style.display='none';
    
    document.getElementById('divSeleccionIndCuerpo').style.display='none';   
    document.querySelector('#cuadrovalores').setAttribute('divListaIndicadoresCarga','no');
}


function accionCargarIndicador(){
    limpiarFormularioIndPublicados();
    cargarListadoIndicadoresPublicados();
    
        
    $('#cuadrovalores').attr('estado','listado');
    $('#cuadrovalores').attr('modo','indicadores');   
    //document.getElementById('formSeleccionInd').style.display='inline-block';
    //document.getElementById('divMenuAccionesCarga').style.display='inline-block';
    //document.getElementById('botonCrearIndicador').style.display='block';
}
accionCargarIndicador();

function accionModificarIndicador(_this){
    if(confirm("Editar Indicadores ya publicados puede causar errores en el sistema. \n Esta seguro que desea continuar con esta operacion?")){
        limpiarFormularioIndPublicados();
        cargarListadoIndicadoresPublicadosAModificar();

		$('#cuadrovalores').attr('estado','listado');
		$('#cuadrovalores').attr('modo','indicadores');   
        //document.getElementById('formSeleccionInd').style.display='inline-block';
        document.getElementById('divMenuAccionesCrea').style.display='inline-block';
        document.getElementById('botonCrearIndicador').style.display='none';
        
    }
}


function cancelarEditarInd(){
		document.querySelector('#cuadrovalores').setAttribute('formeditarindicadores','no');
}

function accionSeleccionarCapa(_this){
    cargarListadoCapasPublicadas();
    
    document.getElementById('formSeleccionCapa').style.display='block';
    //document.getElementById('AccionesSeleccionCapa').style.display='none';
    document.getElementById('botonSeleccionarCapa').style.display='none';
    document.getElementById('botonCancelarSeleccionarCapa').style.display='block';
}



function accionCancelarSeleccionCapa(_this){
    limpiarFormularioSeleccionCapa();
    
    document.getElementById('formSeleccionCapa').style.display='none';
    //document.getElementById('AccionesSeleccionCapa').style.display='block';
    document.getElementById('botonSeleccionarCapa').style.display='block';
    document.getElementById('botonCancelarSeleccionarCapa').style.display='none';
    
    //limpiarFormularioCapa();
    //document.getElementById('capaseleccionada').style.display='none';
}

function accionSeleccionarCapaCambio(_this){
    cargarListadoCapasPublicadas();
    
    document.getElementById('formSeleccionCapa').style.display='block';
    //document.getElementById('AccionesSeleccionCapaCambio').style.display='none';
    document.getElementById('botonSeleccionarCapaCambio').style.display='none';
    document.getElementById('botonCancelarSeleccionarCapaCambio').style.display='block';
}

function accionCancelarSeleccionCapaCambio(_this){
    limpiarFormularioSeleccionCapa();
    
    document.getElementById('formSeleccionCapa').style.display='none';
    //document.getElementById('AccionesSeleccionCapaCambio').style.display='block';
    document.getElementById('botonSeleccionarCapaCambio').style.display='block';
    document.getElementById('botonCancelarSeleccionarCapaCambio').style.display='none';
    
    //limpiarFormularioCapa();
    //document.getElementById('capaseleccionada').style.display='none';
}

function cargarValoresCapasPublicadas(_res){
    if (_res.data != null){
        for (var elemCapa in _res.data){
            var divRoot = document.getElementById('listacapaspublicadas');
            var filaCapa = document.createElement('a');
            filaCapa.setAttribute('idcapa', _res.data[elemCapa]["id"]);
            filaCapa.setAttribute('class', 'filaCapaLista');
            filaCapa.setAttribute('onclick', "accionCapaPublicadaSeleccionada(this,"+_res.data[elemCapa]["id"]+")" );
            var capaId = document.createElement('div');
            capaId.setAttribute('id','capaIdLista');
            capaId.innerHTML = "ID <span class='idn'>" + _res.data[elemCapa]["id"]+"</span>";
            var capaNombre = document.createElement('div');
            capaNombre.setAttribute('id','capaNombreLista');
            capaNombre.innerHTML = _res.data[elemCapa]["nombre"];
            var capaDescripcion = document.createElement('div');
            capaDescripcion.setAttribute('id','capaDescripcionLista');
            capaDescripcion.innerHTML = _res.data[elemCapa]["descripcion"];
            filaCapa.appendChild(capaId);
            filaCapa.appendChild(capaNombre);
            filaCapa.appendChild(capaDescripcion);
            divRoot.appendChild(filaCapa);
        }
    } else {
        console.log('no hay capas publicadas para este usuario');
    }   
}

function mostrarListadoCapasPublicadas(){
    document.querySelector('#divSeleccionCapaCuerpo #txningunacapa').style.display='none';
    document.getElementById('divSeleccionCapaCuerpo').style.display='block';
}

function accionCapaPublicadaSeleccionada(_this, idcapa){
    editarCampoIndParametros('idcapa', idcapa);
    accionCapaPublicadaCargar(_this, idcapa);
}

function accionCapaPublicadaCargar(_this, idcapa){
    limpiarFormularioSeleccionCapa();
    limpiarFormularioCapa();
    
    cargarDatosCapaPublicada(idcapa);
    
    document.getElementById('capaseleccionada').style.display='block';
    document.getElementById('AccionesSeleccionCapa').style.display='none';
    document.getElementById('botonSeleccionarCapa').style.display='block';
    document.getElementById('botonCancelarSeleccionarCapa').style.display='none';
    document.getElementById('botonSeleccionarCapaCambio').style.display='block';
    document.getElementById('botonCancelarSeleccionarCapaCambio').style.display='none';
}

function limpiarFormularioIndicadores(){
    document.getElementById('formEditarIndicadores').setAttribute('idindicador', 0);
    document.getElementById('indNombre').value = '';
    document.getElementById('indDescripcion').value = '';
    document.getElementById('periodicidadSelector').value = 'elegir';
    document.getElementById('funcionalidadSelector').value = 'elegir';
    document.getElementById('inputFechaDesde').value = '';
    document.getElementById('inputFechaHasta').value = '';
    
    //Limpiar Columna Valores
    document.getElementById('columnasValores').innerHTML = '';
    inicializarColumnas();
    document.querySelector('#representacionescalacolor[tipo="numero"] input#maximo').value = null;
    document.querySelector('#representacionescalacolor[tipo="numero"] input#minimo').value = null;
}

function limpiarFormularioSeleccionCapa(){
    document.querySelector('#formSeleccionCapa #txningunacapa').style.display='block';
    document.querySelector('#formSeleccionCapa #listacapaspublicadas').innerHTML='';
    document.getElementById('formSeleccionCapa').style.display='none';
}

function limpiarFormularioCapa(){
    document.getElementById('capaseleccionada').setAttribute('idcapa', 0);
    document.getElementById('AccionesSeleccionCapa').style.display='block';
    document.getElementById('capaNombre').innerHTML = '';
    document.getElementById('capaDescripcion').innerHTML = '';
}

function limpiarFormularioIndPublicados(){
    document.getElementById('indicadorActivo').setAttribute('idindicador', 0);
    document.querySelector('#txningunind').style.display='block';
    document.getElementById('listaindpublicadas').innerHTML = '';
    document.getElementById('indCargaDescripcion').innerHTML = '';
}

function asignarIdIndicador(idIndicador){
    document.getElementById('formEditarIndicadores').setAttribute('idindicador', idIndicador);
}

function cambiarModoSoloLectura(readonly){
    document.getElementById('indNombre').setAttribute('disabled', readonly);
    document.getElementById('indDescripcion').setAttribute('disabled', readonly);
    document.getElementById('periodicidadSelector').setAttribute('disabled', readonly);
    document.getElementById('funcionalidadSelector').setAttribute('disabled', readonly);
    document.getElementById('inputFechaDesde').setAttribute('disabled', readonly);
    document.getElementById('inputFechaHasta').setAttribute('disabled', readonly);
}

var _Features={};
function cargarFeatures(){
    _lyrElemSrc.clear();
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

    _ext= _lyrElemSrc.getExtent();

    setTimeout(function(){
        mapa.getView().fit(_ext, { duration: 1000 });
    }, 50);
}

function cargarValoresCapaExist(_res){

	
    if(_res.data == null){
    	alert('error, no se encontrò la capa');
    	return;
    }
    
    var capaQuery = _res.data;

    document.getElementById('capaseleccionada').setAttribute('idcapa', capaQuery["id"]);
    document.getElementById('capaNombre').innerHTML = capaQuery["nombre"];
    document.getElementById('capaDescripcion').innerHTML = capaQuery["descripcion"];

    //Operaciones para leer del xml los valores de simbologia
    var xmlSld = capaQuery["sld"];

    if (xmlSld && xmlSld != ''){
        var colorRelleno = '';
        var transparenciaRelleno = '';
        var colorTrazo = '';
        var anchoTrazo = '';

        var xmlDoc;
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

        document.getElementById('inputcolorrelleno').value = colorRelleno;
        document.getElementById('inputtransparenciarellenoNumber').value = transparenciaRelleno * 100;
        document.getElementById('inputtransparenciarellenoRange').value = transparenciaRelleno * 100;
        document.getElementById('inputcolortrazo').value = colorTrazo;
        document.getElementById('inputanchotrazoNumber').value = anchoTrazo;
        document.getElementById('inputanchotrazoRange').value = anchoTrazo;
    }
    
    cargarValoresCapaExistQuery();
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

function cargarValoresIndicadorExist(_res){
    if (_res.data != null){
        var indicadorQuery = _res.data;

		_form=document.querySelector('#formEditarIndicadores');
        _form.setAttribute('idindicador', indicadorQuery["id"]);
        
        if(indicadorQuery["zz_publicada"]=='1'){
        	document.querySelector('#divMenuAccionesCrea #botonPublicar').style.display='none';
        }else{
        	document.querySelector('#divMenuAccionesCrea #botonPublicar').style.display='inline-block';
        }
        
        _form.querySelector('#indNombre').value = indicadorQuery["nombre"];
        _form.querySelector('#indDescripcion').value = indicadorQuery["descripcion"];
        _form.querySelector('#inputFechaDesde').value = indicadorQuery["fechadesde"];
        _form.querySelector('#inputFechaHasta').value = indicadorQuery["fechahasta"];
        
        _form.querySelector('#calc_buffer').value = indicadorQuery["calc_buffer"];
        _form.querySelector('#calc_superp').value = indicadorQuery["calc_superp"];
        _form.querySelector('#calc_zonificacion').value = indicadorQuery["calc_zonificacion"];
        
        
        if (indicadorQuery["funcionalidad"] != null && indicadorQuery["funcionalidad"] != ''){
            document.getElementById('funcionalidadSelector').value = indicadorQuery["funcionalidad"];
            if(indicadorQuery["funcionalidad"]=='nuevaGeometria'){
            	document.querySelector('#formcalculo').style.display='block';
            }else{
            	document.querySelector('#formcalculo').style.display='none';
            }
        } else {
            document.getElementById('funcionalidadSelector').value = 'elegir';
        }
        
        if (indicadorQuery["periodicidad"] != null && indicadorQuery["periodicidad"] != ''){
            document.getElementById('periodicidadSelector').value = indicadorQuery["periodicidad"];
        } else {
            document.getElementById('periodicidadSelector').value = 'elegir';
        }
        
        if (indicadorQuery["id_p_ref_capasgeo"] != null &&  indicadorQuery["id_p_ref_capasgeo"] > 0){
            accionCapaPublicadaCargar(this, indicadorQuery["id_p_ref_capasgeo"]);
        }
        
        document.querySelector('#representacionescalacolor[tipo="numero"] input#maximo').value = indicadorQuery['representar_val_max'];
        document.querySelector('#representacionescalacolor[tipo="numero"] input#minimo').value = indicadorQuery['representar_val_min'];
        
        for (var i = 1; i <= 5; i++) { 
            if (indicadorQuery['col_texto'+i+'_nom'] != null || indicadorQuery['col_texto'+i+'_unidad'] != null){
                var columnaValorNueva = accionAnadirNuevaColumnaValorInit(null, 0, i);
                columnaValorNueva.querySelector('.indValoresTipo').value = 'texto';
                //accionSelectTipoColumna(null, )
                columnaValorNueva.querySelector('.indValoresNombre').value = indicadorQuery['col_texto'+i+'_nom'];
                columnaValorNueva.querySelector('.indValoresUnidad').value = indicadorQuery['col_texto'+i+'_unidad'];
                columnaValorNueva.querySelector('.indValoresUnidad').placeholder = 'Categorias separadas por coma';
                
                if (indicadorQuery['representar_campo'] == 'col_texto'+i){
                    columnaValorNueva.querySelector('.indValoresRep').checked = true;
                    actualizarEscala();
                }
            }
        }
        for (var i = 1; i <= 5; i++) { 
            if (indicadorQuery['col_numero'+i+'_nom'] != null || indicadorQuery['col_numero'+i+'_unidad'] != null){
                var columnaValorNueva = accionAnadirNuevaColumnaValorInit(null, i, 0);
                columnaValorNueva.querySelector('.indValoresTipo').value = 'numerico';
                //accionSelectTipoColumna(null, )
                columnaValorNueva.querySelector('.indValoresNombre').value = indicadorQuery['col_numero'+i+'_nom'];
                columnaValorNueva.querySelector('.indValoresUnidad').value = indicadorQuery['col_numero'+i+'_unidad'];
                columnaValorNueva.querySelector('.indValoresUnidad').placeholder = 'Unidad de medida';
                
                if (indicadorQuery['representar_campo'] == 'col_numero'+i){
                    columnaValorNueva.querySelector('.indValoresRep').checked = true;
                    actualizarEscala();
                }
            }
        }
    } else {
        alert('error otjsf0jg44ffgh');
    }
}

function accionEditarIndCampo(_event,_this){
    //console.log(_event.keyCode);
    if(_event.keyCode==9){return;}//tab
    if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
    if(_event.keyCode==13){
        editarCampoInd(_this.id);
    }
}

function editarCampoInd(idcampo){
    var nuevoValor = document.getElementById(idcampo).value;
    
    var campo = '';
    switch (idcampo){
        case 'indNombre':
            campo = 'nombre';
            break;
        case 'indDescripcion':
            campo = 'descripcion';
            break;
        case 'idcapa':
            campo = 'idcapa';
            break;
        case 'inputFechaDesde':
            campo = 'fechadesde';
            break;
        case 'inputFechaHasta':
            campo = 'fechahasta';
            break;
        case 'calc_buffer':
            campo = 'calc_buffer';
            break;
        case 'calc_superp':
            campo = 'calc_superp';
            break;
        case 'calc_zonificacion':
            campo = 'calc_zonificacion';
            break;                
                
            
        default:
            alert('error mvr20fh');
    }
    
    editarCampoIndParametros(campo, nuevoValor);
}

function editarCampoIndParametros(nombreCampo, nuevoValor){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idindicador = document.getElementById('formEditarIndicadores').getAttribute('idindicador');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idindicador
    };
    parametros[nombreCampo] = nuevoValor;
    
    editarInd(parametros);
}

function editarCampoIndParametrosColumna(nombreColumna, nuevoValor){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idindicador = document.getElementById('formEditarIndicadores').getAttribute('idindicador');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idindicador,
        'columna': nombreColumna,
        'columnavalor': nuevoValor
    };
    
    editarInd(parametros);
}

function editarIndPeriodicidad(_event, _this){
    var nuevaPeriodicidad = _this.options[_this.selectedIndex].value;
    
    if (nuevaPeriodicidad == 'mensual' || nuevaPeriodicidad == 'anual' || nuevaPeriodicidad == 'diario'){
        editarCampoIndParametros('periodicidad', nuevaPeriodicidad);
    } else {
        editarCampoIndParametros('periodicidad', 'NULL');
    }
}

function editarIndFuncionalidad(_event, _this){
    var nuevoValor = _this.options[_this.selectedIndex].value;
    
    document.querySelector('#formcalculo').style.display='none';
    
    
    
    if (nuevoValor == '' || nuevoValor == 'elegir'){
        editarCampoIndParametros('funcionalidad', 'NULL');
    } else {
        editarCampoIndParametros('funcionalidad', nuevoValor);
    }
    if(nuevoValor=='nuevaGeometria'){
    	document.querySelector('#formcalculo').style.display='block';
    	document.querySelector('#tipodeometriaNuevaGeometria').style.display='block';
    }else{
    	document.querySelector('#formcalculo').style.display='none';
    	document.querySelector('#tipodeometriaNuevaGeometria').style.display='none';
    }
}

var _ColumnasNumericasUsadas = [];
var _ColumnasTextoUsadas = [];

function inicializarColumnas(){
    _ColumnasNumericasUsadas[1] = false;
    _ColumnasNumericasUsadas[2] = false;
    _ColumnasNumericasUsadas[3] = false;
    _ColumnasNumericasUsadas[4] = false;
    _ColumnasNumericasUsadas[5] = false;
    
    _ColumnasTextoUsadas[1] = false;
    _ColumnasTextoUsadas[2] = false;
    _ColumnasTextoUsadas[3] = false;
    _ColumnasTextoUsadas[4] = false;
    _ColumnasTextoUsadas[5] = false;
}
inicializarColumnas();

function accionAnadirNuevaColumnaValor(_this){
    accionAnadirNuevaColumnaValorInit(_this, 0, 0);
}

function accionAnadirNuevaColumnaValorInit(_this, camponumerico, campotexto){
    var divColumnasValores = document.getElementById('columnasValores');
    var columnaValorNueva = document.createElement('div');
    columnaValorNueva.setAttribute('id', 'nuevacolumna');
    columnaValorNueva.setAttribute('camponumerico', camponumerico);
    columnaValorNueva.setAttribute('campotexto', campotexto);
    
    var inputRepColumna = document.createElement('input');
    inputRepColumna.setAttribute('class', 'indValoresRep');
    inputRepColumna.setAttribute('type', 'radio');
    inputRepColumna.setAttribute('name', 'representar');
    inputRepColumna.setAttribute('onchange', 'accionSeleccionarRepColumna()');
    columnaValorNueva.appendChild(inputRepColumna);    
    
    var _sel = document.querySelector('#columnasValores input[type="radio"]:checked');
    if(_sel == undefined){
    	inputRepColumna.checked=true;
    }
    
    var inputNombreColumna = document.createElement('input');
    inputNombreColumna.setAttribute('class', 'indValoresNombre');
    inputNombreColumna.setAttribute('type', 'text');
    inputNombreColumna.setAttribute('onchange', 'accionInputNombreColumna(event, this)');
    columnaValorNueva.appendChild(inputNombreColumna);
    
    var selectTipoColumna = document.createElement('select');
    selectTipoColumna.setAttribute('class', 'indValoresTipo');
    selectTipoColumna.setAttribute('onchange', 'accionSelectTipoColumna(event, this);actualizarEscala();');
    var optionTexto = document.createElement('option');
    optionTexto.setAttribute('value', 'texto');
    optionTexto.innerHTML = 'Texto';
    var optionNumerico = document.createElement('option');
    optionNumerico.setAttribute('value', 'numerico');
    optionNumerico.innerHTML = 'Numérico';
    var optionElegir = document.createElement('option');
    optionElegir.setAttribute('value', 'elegir');
    optionElegir.innerHTML = '-elegir-';
    
    selectTipoColumna.appendChild(optionElegir);
    selectTipoColumna.appendChild(optionTexto);
    selectTipoColumna.appendChild(optionNumerico);
    optionElegir.selected = true;
    columnaValorNueva.appendChild(selectTipoColumna);
    
    var inputUnidadColumna = document.createElement('input');
    inputUnidadColumna.setAttribute('class', 'indValoresUnidad');
    inputUnidadColumna.setAttribute('type', 'text');
    inputUnidadColumna.setAttribute('onkeyup', 'actualizarEscala()');
    inputUnidadColumna.setAttribute('onchange', 'accionInputUnidadColumna(event, this)');
    columnaValorNueva.appendChild(inputUnidadColumna);
    
    var botonEliminarColumna = document.createElement('a');
    botonEliminarColumna.setAttribute('class', 'indValoresEliminar');
    botonEliminarColumna.setAttribute('onclick', 'accionEliminarNuevaColumnaValor(event, this)');
    botonEliminarColumna.innerHTML = 'elim';
    columnaValorNueva.appendChild(botonEliminarColumna);
    
    divColumnasValores.appendChild(columnaValorNueva);
    return columnaValorNueva;
}

function accionEliminarNuevaColumnaValor(_event, _this){
    var camponumerico = 0;
    var campotexto = 0;
    if (_this.parentNode.hasAttribute('camponumerico')){
        camponumerico = _this.parentNode.getAttribute('camponumerico');
    }
    if (_this.parentNode.hasAttribute('campotexto')){
        camponumerico = _this.parentNode.getAttribute('campotexto');
    }
    
    eliminarColumna(_this.parentNode);
    
    _this.parentNode.parentNode.removeChild(_this.parentNode);
    
    desasignarColumna(_this.parentNode);
    
    var _sel = document.querySelector('#columnasValores input[type="radio"]:checked');
    if(_sel == undefined){
        var sel = document.querySelector('#columnasValores input[type="radio"]');
        if (sel != undefined){
            sel.checked = true;
        }
    }
}

function accionSelectTipoColumna(_event, _this){
    switch(_this.value){
        case 'elegir':
            desasignarColumna(_this.parentNode);
            break;
        case 'numerico':
            var columnaAsignada = asignarColumnaNumerica();
            if (columnaAsignada == 0){
                alert('Ya ha seleccionado el maximo numero de campos numericos');
                _this.value = 'elegir';
                desasignarColumna(_this.parentNode);
            } else{
                for (var child in _this.parentNode.children){
                    if (_this.parentNode.children[child].nodeType == 1 &&
                            _this.parentNode.children[child].getAttribute('class') == 'indValoresUnidad'){
                       _this.parentNode.children[child].placeholder = 'Unidad de medida';
                    }
                }
                
                guardarColumna(_this.parentNode);
                desasignarColumna(_this.parentNode);
                _this.parentNode.setAttribute('camponumerico', columnaAsignada);
            }
            break;
        case 'texto':
            var columnaAsignada = asignarColumnaTexto();
            if (columnaAsignada == 0){
                alert('Ya ha seleccionado el maximo numero de campos de texto');
                _this.value = 'elegir';
                desasignarColumna(_this.parentNode);
            } else{
                for (var child in _this.parentNode.children){
                    if (_this.parentNode.children[child].nodeType == 1 &&
                            _this.parentNode.children[child].getAttribute('class') == 'indValoresUnidad'){
                       _this.parentNode.children[child].placeholder = 'Categorias separadas por coma';
                    }
                }
                
                guardarColumna(_this.parentNode);
                desasignarColumna(_this.parentNode);
                _this.parentNode.setAttribute('campotexto', columnaAsignada);
            }
            break;
        default:
            alert('error 0vn20vjk3');
    }
}

function asignarColumnaNumerica(){
    for (var col in _ColumnasNumericasUsadas){
        if (_ColumnasNumericasUsadas[col] == false){
            _ColumnasNumericasUsadas[col] = true;
            return col;
        }
    }
    return 0;
}

function asignarColumnaTexto(){
    for (var col in _ColumnasTextoUsadas){
        if (_ColumnasTextoUsadas[col] == false){
            _ColumnasTextoUsadas[col] = true;
            return col;
        }
    }
    return 0;
}

function accionInputNombreColumna(_event, _this){
    var nuevoValor = _this.value;
    var camponumerico = _this.parentNode.getAttribute('camponumerico');
    var campotexto = _this.parentNode.getAttribute('campotexto');
    var campo = '';
    
    if (camponumerico > 0 && campotexto > 0){
        alert('error vi303js0cd');
        return;
    }
    
    if (camponumerico > 0){
        campo = 'col_numero'+camponumerico+'_nom';
    }
    
    if (campotexto > 0){
        campo = 'col_texto'+campotexto+'_nom';
    }
    
    if (campo != ''){
        editarCampoIndParametrosColumna(campo, nuevoValor);
    }
}

function accionInputUnidadColumna(_event, _this){
    var nuevoValor = _this.value;
    var camponumerico = _this.parentNode.getAttribute('camponumerico');
    var campotexto = _this.parentNode.getAttribute('campotexto');
    var campo = '';
    
    if (camponumerico > 0 && campotexto > 0){
        alert('error vi303js0cd');
        return;
    }
    
    if (camponumerico > 0){
        campo = 'col_numero'+camponumerico+'_unidad';
    }
    
    if (campotexto > 0){
        campo = 'col_texto'+campotexto+'_unidad';
    }
    
    if (campo != ''){
        editarCampoIndParametrosColumna(campo, nuevoValor);
    }
}

function guardarColumna(divColumnaNueva){
    //guardar todos los datos de esta columna, leyendo los componentes hijos y disparando una edicion en cada uno
    for (var child in divColumnaNueva.children){
        if (divColumnaNueva.children[child].nodeType == 1 &&
                divColumnaNueva.children[child].getAttribute('class') == 'indValoresNombre'){
            accionInputNombreColumna(null, divColumnaNueva.children[child]);
        }
        
        if (divColumnaNueva.children[child].nodeType == 1 &&
                divColumnaNueva.children[child].getAttribute('class') == 'indValoresUnidad'){
            accionInputUnidadColumna(null, divColumnaNueva.children[child]);
        }
    }
}

function eliminarColumna(divColumnaNueva){
    var camponumerico = 0;
    var campotexto = 0;
    if (divColumnaNueva.hasAttribute('camponumerico')){
        camponumerico = divColumnaNueva.getAttribute('camponumerico');
    }
    if (divColumnaNueva.hasAttribute('campotexto')){
        campotexto = divColumnaNueva.getAttribute('campotexto');
    }
    
    if (camponumerico > 0 && campotexto > 0){
        alert('error vi303js0cd');
        return;
    }
    
    if (camponumerico > 0){
        var campo = '';
        campo = 'col_numero'+camponumerico+'_nom';
        editarCampoIndParametrosColumna(campo, 'NULL');
        
        campo = '';
        campo = 'col_numero'+camponumerico+'_unidad';
        editarCampoIndParametrosColumna(campo, 'NULL');
    }
    
    if (campotexto > 0){
        var campo = '';
        campo = 'col_texto'+campotexto+'_nom';
        editarCampoIndParametrosColumna(campo, 'NULL');
        
        campo = '';
        campo = 'col_texto'+campotexto+'_unidad';
        editarCampoIndParametrosColumna(campo, 'NULL');
    }
    
}

function desasignarColumna(divColumnaNueva){
    var camponumerico = divColumnaNueva.getAttribute('camponumerico');
    if (camponumerico > 0){
        _ColumnasNumericasUsadas[camponumerico] = false;
        divColumnaNueva.setAttribute('camponumerico', 0);
    }
    
    var campotexto = divColumnaNueva.getAttribute('campotexto');
    if (campotexto > 0){
        _ColumnasTextoUsadas[campotexto] = false;
        divColumnaNueva.setAttribute('campotexto', 0);
    }
    
    for (var child in divColumnaNueva.children){
        if (divColumnaNueva.children[child].nodeType == 1 &&
                divColumnaNueva.children[child].getAttribute('class') == 'indValoresUnidad'){
           divColumnaNueva.children[child].placeholder = '';
        }
    }
}

function accionSeleccionarRepColumna(){
    actualizarEscala();
    
    editarRepresentacionCampo();
}

function editarRepresentacionCampo(){
    var opcionSeleccionada = document.querySelector('#columnasValores input[type="radio"]:checked');
    if (opcionSeleccionada != undefined){
        var _tipo = document.querySelector('#columnasValores input[type="radio"]:checked').parentNode.querySelector('select').value;
        if(_tipo == 'texto' || _tipo == 'numerico'){
            var col_seleccionada = 'NULL';
            var camponumerico = opcionSeleccionada.parentNode.getAttribute('camponumerico');
            var campotexto = opcionSeleccionada.parentNode.getAttribute('campotexto');

            if (camponumerico > 0 && campotexto > 0){
                alert('error vi303js0cd');
                return;
            }

            if (camponumerico > 0){
                col_seleccionada = 'col_numero'+camponumerico;
            }

            if (campotexto > 0){
                col_seleccionada = 'col_texto'+campotexto;
            }

            editarCampoIndParametrosColumna('representar_campo', col_seleccionada);
        } else {
            editarCampoIndParametrosColumna('representar_campo', 'NULL');
        }
    }
}

function actualizarEscala(){
    _tipo = document.querySelector('#columnasValores input[type="radio"]:checked').parentNode.querySelector('select').value;

    document.querySelector('#representacionescalacolor[tipo="numero"]').style.display='none';
    document.querySelector('#representacionescalacolor[tipo="texto"]').style.display='none';

    if(_tipo=='texto'){
        document.querySelector('#representacionescalacolor[tipo="texto"]').style.display='block';
        actualizarEscalaT();
    }
    if(_tipo=='numerico'){		
        document.querySelector('#representacionescalacolor[tipo="numero"]').style.display='block';			
        actualizarEscalaN();
    }
}

function accionRepresentacionValorMaximo(){
    actualizarEscalaN();
    
    editarRepresentacionValorMaximo();
}

function editarRepresentacionValorMaximo(){
    var _max = parseFloat(document.querySelector('#representacionescalacolor[tipo="numero"] input#maximo').value);
    if (_max > 0){
        editarCampoIndParametrosColumna('representar_val_max', _max);
    } else {
        editarCampoIndParametrosColumna('representar_val_max', 'NULL');
    }
}

function accionRepresentacionValorMinimo(){
    actualizarEscalaN();
    
    editarRepresentacionValorMinimo();
}

function editarRepresentacionValorMinimo(){
    var _min = parseFloat(document.querySelector('#representacionescalacolor[tipo="numero"] input#minimo').value);    
    if (_min > 0){
        editarCampoIndParametrosColumna('representar_val_min', _min);
    } else {
        editarCampoIndParametrosColumna('representar_val_min', 'NULL');
    }
}

function actualizarEscalaN(){
    var _max = parseFloat(document.querySelector('#representacionescalacolor[tipo="numero"] input#maximo').value);
    var _min = parseFloat(document.querySelector('#representacionescalacolor[tipo="numero"] input#minimo').value);
    //console.log(_max);
    //console.log(_min);
    var _med = (_max+_min)/2;
    var _mm = (_max+_min);
    //console.log(_mm);
    _mm = _mm/2;
    //console.log(_mm);

    var _paso = _max-_med;
    var _sobre = Math.round(100*(_max+_paso))/100;
    var _sub  = Math.round(100*(_min-_paso))/100;		

    //console.log(_med);
    _mm = 100*(_med);
    //console.log(_mm);
    _mm = Math.round(_mm);
    //console.log(_mm);
    _mm = _mm/100;
    //console.log(_mm);

    _med =_mm;

    if(!isNaN(_med)){
        document.querySelector('#representacionescalacolor[tipo="numero"]  #sobre #valorminimo p').innerHTML=_sobre;
    }
    if(!isNaN(_med)){
        document.querySelector('#representacionescalacolor[tipo="numero"]  #alto #valorminimo p').innerHTML=_med;
    }
    if(!isNaN(_sub)){
        document.querySelector('#representacionescalacolor[tipo="numero"]  #minimo #valorminimo p').innerHTML=_sub;
    }
}

function actualizarEscalaT(){
    var _input = document.querySelector('#columnasValores input[type="radio"]:checked').parentNode.querySelector('#columnasValores input.indValoresUnidad').value;
    //console.log(_input);
    var _array = _input.split(',');

    var _rmax = 228;
    var _gmax = 25;
    var _bmax = 55;

    var _rmin = 204;
    var _gmin = 255;
    var _bmin = 204;

    var _cant = _array.length;
    //console.log(_cant);
    var _rpaso = (_rmax-_rmin)/(_cant-1);
    var _gpaso = (_gmax-_gmin)/(_cant-1);
    var _bpaso = (_bmax-_bmin)/(_cant-1);

    var _cont = document.querySelector('#representacionescalacolor[tipo="texto"]');
    _cont.innerHTML='';

    if(_cant<2){return;}
    for(var _np in _array){
        var _red = _rmin+(_np*_rpaso);
        var _gre = _gmin+(_np*_gpaso);
        var _blu = _bmin+(_np*_bpaso);

        //console.log(_red);

        var _fil = document.createElement('div');
        _fil.setAttribute('class','fila');
        _cont.appendChild(_fil);

        var _col = document.createElement('div');
        _col.setAttribute('class','color');
        _col.setAttribute('style','background-color: rgb('+ _red +' '+ _gre +' '+ _blu +');');

        _fil.appendChild(_col);

        var _val = document.createElement('div');
        _val.setAttribute('class','valor');
        _val.innerHTML=_array[_np];
        _fil.appendChild(_val);
    }
}

function cargarValoresIndicadoresPublicados(_res, accionOnClick){
    // DEPRECADA variable _res;
	for (var elemInd in _DataListaIndicadores){
		
		_dati=_DataListaIndicadores[elemInd];
		
		var divRoot = document.getElementById('listaindpublicadas');
		var filaInd = document.createElement('a');
		divRoot.appendChild(filaInd);
		filaInd.setAttribute('idind', _dati["id"]);
		filaInd.setAttribute('class', 'filaIndLista');
		filaInd.setAttribute('onclick', accionOnClick+"(this,"+_dati["id"]+")" );
		
		var indId = document.createElement('div');
		filaInd.appendChild(indId);
		indId.setAttribute('id','indIdLista');
		indId.innerHTML = "ID <span class='idn'>" + _dati["id"]+"</span>";
		
		var indNombre = document.createElement('div');
		filaInd.appendChild(indNombre);
		indNombre.setAttribute('id','indNombreLista');
		indNombre.innerHTML = _dati["nombre"];
		
		var indAu = document.createElement('div');
		filaInd.appendChild(indDescripcion);		
		indAu.setAttribute('id','indAutoriaLista');
		indAu.innerHTML ='por: '+ _dati["autornom"]+' '+_dati["autorape"];
		
		var indDescripcion = document.createElement('div');
		filaInd.appendChild(indAu);
		indDescripcion.setAttribute('id','indDescripcionLista');
		indDescripcion.innerHTML = _dati["descripcion"];
		
	}
}

function mostrarListadoModelos(){
	var divRoot = document.getElementById('listamodpublicadas');
	divRoot.innerHTML='';
	for (_nm in _DataListaModelos['modelosOrden']){
		
		_idm=_DataListaModelos.modelosOrden[_nm];
		console.log(_idm);
		_dati=_DataListaModelos.modelos[_idm];
		console.log(_dati);
		
		var filaInd = document.createElement('a');
		divRoot.appendChild(filaInd);
		filaInd.setAttribute('idind', _dati["id"]);
		filaInd.setAttribute('class', 'filaIndLista');
		filaInd.setAttribute('onclick', 'cargarModelo('+_idm+')');
		
		var indId = document.createElement('div');
		filaInd.appendChild(indId);
		indId.setAttribute('id','indIdLista');
		indId.innerHTML = "ID <span class='idn'>" + _dati["id"]+"</span>";
		
		var indNombre = document.createElement('div');
		filaInd.appendChild(indNombre);
		indNombre.setAttribute('id','indNombreLista');
		indNombre.innerHTML = _dati["nombre"];
		
		var indAu = document.createElement('div');
		filaInd.appendChild(indAu);
		indAu.setAttribute('id','indAutoriaLista');
		indAu.innerHTML ='por: '+ _dati["autornom"]+' '+_dati["autorape"];
		
		var indDescripcion = document.createElement('div');
		filaInd.appendChild(indDescripcion);		
		indDescripcion.setAttribute('id','indDescripcionLista');
		indDescripcion.innerHTML = _dati["descripcion"];
		
	}	
}


function mostrarModelo(){
	
	
	
}

function cargarValoresIndicadoresPublicados(_res, accionOnClick){
    
	for (var elemInd in _DataListaIndicadores){
		
		_dati=_DataListaIndicadores[elemInd];
		
		var divRoot = document.getElementById('listaindpublicadas');
		var filaInd = document.createElement('a');
		divRoot.appendChild(filaInd);
		filaInd.setAttribute('idind', _dati["id"]);
		filaInd.setAttribute('class', 'filaIndLista');
		filaInd.setAttribute('onclick', accionOnClick+"(this,"+_dati["id"]+")" );
		
		var indId = document.createElement('div');
		filaInd.appendChild(indId);
		indId.setAttribute('id','indIdLista');
		indId.innerHTML = "ID <span class='idn'>" + _dati["id"]+"</span>";
		
		var indNombre = document.createElement('div');
		filaInd.appendChild(indNombre);
		indNombre.setAttribute('id','indNombreLista');
		indNombre.innerHTML = _dati["nombre"];
		
		var indAu = document.createElement('div');
		filaInd.appendChild(indAu);
		indAu.setAttribute('id','indAutoriaLista');
		indAu.innerHTML ='por: '+ _dati["autornom"]+' '+_dati["autorape"];
		
		var indDescripcion = document.createElement('div');
		filaInd.appendChild(indDescripcion);		
		indDescripcion.setAttribute('id','indDescripcionLista');
		indDescripcion.innerHTML = _dati["descripcion"];
		
	}
}

function mostrarListadoIndicadoresPublicados(){
    document.querySelector('#txningunind').style.display='none';
    document.getElementById('divSeleccionIndCuerpo').style.display='block';
}

function accionIndicadorPublicadoSeleccionado(_this, _idindicador){
    document.querySelector("#cuadrovalores").setAttribute("botonIndicadoresModelo", "no");
	_encuadrado='no';
    document.getElementById('indicadorActivo').setAttribute('idindicador', _idindicador);    
    document.querySelector('#divListaIndicadoresCarga').setAttribute('idindicadorcarga',_idindicador);
    //document.querySelector('#botonCancelarCarga').style.display='block';
        
    cargarIndicadorPublicado(_idindicador);
}

function accionIndicadorPublicadoSeleccionadoModificar(_this, idindicador){
	$('#cuadrovalores').attr('estado','elemento');
    consultarIndicadorParaModificar(idindicador);
    //document.getElementById('formSeleccionInd').style.display='none';
    document.getElementById('formEditarIndicadores').style.display='inline-block';
}

function accionIndicadorPublicadoCargar(idindicador, _res, seleccionarDefault){
    if (_res.data != null && _res.data['indicador'] != null){
		
		
		$('#cuadrovalores').attr('estado','elemento');
		//document.getElementById('formSeleccionInd').style.display='none';
		
		document.querySelector('#cuadrovalores').setAttribute('divListaIndicadoresCarga','si');
		
		document.querySelector('#cuadrovalores').setAttribute('formCargaInd','si');
		
		
		document.querySelector('#formCargaInd').setAttribute('idcampa',_res.data.id);

		document.querySelector('#cuadrovalores #contenido #tituloInd').innerHTML=_DataIndicador.id+'<span>'+_DataIndicador.nombre+'<span>';
		document.querySelector('#cuadrovalores #contenido #tituloInd').title=_DataIndicador.descripcion;
		document.querySelector('#cuadrovalores #contenido').setAttribute('indcargado','si');
		
	
        document.getElementById('indCargaDescripcion').innerHTML = _res.data['indicador']['descripcion'];
        document.getElementById('indCargaPeriodicidad').innerHTML = _res.data['indicador']['periodicidad'];
        
        if( _res.data['indicador']['capa']!=undefined){
			document.getElementById('indCapaGeom').innerHTML = _res.data['indicador']['id_p_ref_capasgeo'] + ' - '+ _res.data['indicador']['capa']['nombre'];
		}
        
        document.getElementById('indCapaGeom').innerHTML += '<a href="./app_capa.php?cod='+_CodMarco+'&idr='+_res.data['indicador']['id_p_ref_capasgeo']+'">ir</a>'
        
        document.getElementById('indicadorActivo').setAttribute('periodicidad', _res.data['indicador']['periodicidad']);
        
        document.querySelector('#cuadrovalores').setAttribute('divListaIndicadoresCarga','si');
        
        
        document.querySelector('#divListaIndicadoresCarga #AccionesSeleccionIndCambio').style.display='none';
		//document.querySelector('#divListaIndicadoresCarga #botonSeleccionarIndCambio').setAttribute('idind',_res.data.indicador.id);
		
		
        if (_res.data['indicador']['fechadesde'] == null || _res.data['indicador']['fechahasta'] == null){
            //alert('error asf0jg434f2f0gh');
            //ADVERTIR DE FALTA CARGAR FECHA DE INICIO Y DE FIN
        }

        var fechadesde = new Date(_res.data['indicador']['fechadesde']);
        var fechahasta = new Date(_res.data['indicador']['fechahasta']);

        if (fechahasta < fechadesde){
            alert('error 04nf82kd02j5');
        }

        //Listado de todos los años
        var years = new Array();
        var i = 0;
        for (var nYear = fechadesde.getFullYear(); nYear <= fechahasta.getFullYear(); nYear++) {
            years[i] = nYear;
            i++;
        }
		
        var periodicidad = _res.data['indicador']['periodicidad'];

        var periodos = new Array();
        if (periodicidad == 'anual'){
			
			
			_divRoot = document.getElementById('selectorPeriodo');
			_divRoot.innerHTML='';
			for(_ano in _res.data.periodos){
				for(_mes in _res.data.periodos[_ano]){					
					for(_dia in _res.data.periodos[_ano][_mes]){						
						_divPeriodo = document.createElement('div');						
						_divRoot.appendChild(_divPeriodo);
						
						_divPeriodo.setAttribute('class', 'periodoFecha');
						_divPeriodo.setAttribute('periodo', _ano+'_'+_mes+'_'+_dia);
						_divPeriodo.setAttribute('estado_resumen','precarga');
						_divPeriodo.setAttribute('class', 'card '+ periodicidad+' ' +_res.data.periodos[_ano][_mes][_dia].estado.replace(' ',''));
						_divPeriodo.setAttribute('selected', 'false');
						_divPeriodo.setAttribute('onclick', "accionPeriodoElegido(this.getAttribute('periodo'), 'true')" );
						if(_res.data.periodos[_ano][_mes][_dia].representa!=undefined && _res.data.indicador.representar_campo !=''){
							
							if(_res.data.indicador.representar_campo!=null){
								_rc=_res.data.indicador.representar_campo;
								_porc=_res.data.periodos[_ano][_mes][_dia].representa[_rc+'_dato'].valora;//TODO hacer configurable
								if(_porc>1){
									_red= _rmax;
									_gre = _gmax;
									_blu = _bmax;
								}else if(_porc<0){
									_red=_rmin;
									_gre = _gmin;
									_blu = _bmin;	
										
								}else{
									_red = _rmin+(_rmax-_rmin)*_porc;
									_gre = _gmin+(_gmax-_gmin)*_porc;
									_blu = _bmin+(_bmax-_bmin)*_porc;
								}
								_color='rgba('+_red+','+_gre+','+_blu+', 1)';
								_divPeriodo.style.backgroundColor=_color;
							}
						}
						
						_img=document.createElement('img');
						_img.setAttribute('src','./comun_img/check-sinborde.png');
						_img.setAttribute('class','completo');
						_divPeriodo.appendChild(_img);
												
						_h2dia = document.createElement('h2');
						_divPeriodo.appendChild(_h2dia);
						_h2dia.innerHTML = _dia;
						_h2dia.setAttribute('class','dia');
						
						_h2mes = document.createElement('h2');
						_divPeriodo.appendChild(_h2mes);
						_h2mes.innerHTML = _mes;
						_h2mes.setAttribute('class','mes');
						
						_h2ano = document.createElement('h2');
						_divPeriodo.appendChild(_h2ano);
						_h2ano.innerHTML = _ano;
						
						
						document.getElementById('periodo').setAttribute('campo','n1');
						
						if(_res.data.periodos[_ano][_mes][_dia].resumen!=undefined){
							_divN1=document.createElement('div');
							_divPeriodo.appendChild(_divN1);
							_divN1.setAttribute('class','resultado');
							_divN1.setAttribute('id','n1');
							
							_divValor=document.createElement('div');
							_divN1.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero1;
							
							_divPorc=document.createElement('div');
							_divN1.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero1;
							
							_divN2=document.createElement('div');
							_divPeriodo.appendChild(_divN2);
							_divN2.setAttribute('class','resultado');
							_divN2.setAttribute('id','n2');
							
							_divValor=document.createElement('div');
							_divN2.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero2;
							
							_divPorc=document.createElement('div');
							_divN2.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero2;						
							
									
							_divN3=document.createElement('div');
							_divPeriodo.appendChild(_divN3);
							_divN3.setAttribute('class','resultado');
							_divN3.setAttribute('id','n3');
							
							_divValor=document.createElement('div');
							_divN3.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero3;
							
							_divPorc=document.createElement('div');
							_divN3.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero3;						
													
													
							_divN4=document.createElement('div');
							_divPeriodo.appendChild(_divN4);
							_divN4.setAttribute('class','resultado');
							_divN4.setAttribute('id','n4');
							
							_divValor=document.createElement('div');
							_divN4.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero4;
							
							_divPorc=document.createElement('div');
							_divN4.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero4;		
							
													
							_divN5=document.createElement('div');
							_divPeriodo.appendChild(_divN5);
							_divN5.setAttribute('class','resultado');
							_divN5.setAttribute('id','n5');
							
							_divValor=document.createElement('div');
							_divN5.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero5;
							
							_divPorc=document.createElement('div');
							_divN5.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero5;		
						}	
																	
					}		
				}
			}	
        }else if (periodicidad == 'mensual'){
        	console.log(fechadesde);
        	_divRoot = document.getElementById('selectorPeriodo');
			_divRoot.innerHTML='';
			for(_ano in _res.data.periodos){
				for(_mes in _res.data.periodos[_ano]){
					
					for(_dia in _res.data.periodos[_ano][_mes]){
						
						
						_divPeriodo = document.createElement('div');						
						_divRoot.appendChild(_divPeriodo);
						
						_divPeriodo.setAttribute('class', 'periodoFecha');
						_divPeriodo.setAttribute('periodo', _ano+'_'+_mes+'_'+_dia);
						_divPeriodo.setAttribute('estado_resumen','precarga');
						_divPeriodo.setAttribute('class', 'card '+ periodicidad+' ' +_res.data.periodos[_ano][_mes][_dia].estado.replace(' ',''));
						_divPeriodo.setAttribute('selected', 'false');
						_divPeriodo.setAttribute('onclick', "accionPeriodoElegido(this.getAttribute('periodo'), 'true')" );
						if(_res.data.periodos[_ano][_mes][_dia].representa!=undefined && _res.data.indicador.representar_campo !=''){
							
							_rc=_res.data.indicador.representar_campo;
							
							if(_res.data.periodos[_ano][_mes][_dia].representa[_rc+'_dato']!=undefined){
								
								_porc=_res.data.periodos[_ano][_mes][_dia].representa[_rc+'_dato'].valora;//TODO hacer configurable
								if(_porc>1){
									_red= _rmax;
									_gre = _gmax;
									_blu = _bmax;
								}else if(_porc<0){
									_red=_rmin;
									_gre = _gmin;
									_blu = _bmin;	
										
								}else{
									_red = _rmin+(_rmax-_rmin)*_porc;
									_gre = _gmin+(_gmax-_gmin)*_porc;
									_blu = _bmin+(_bmax-_bmin)*_porc;
								}
								_color='rgba('+_red+','+_gre+','+_blu+', 1)';
								_divPeriodo.style.backgroundColor=_color;
								
							}
						}
						
						_img=document.createElement('img');
						_img.setAttribute('src','./comun_img/check-sinborde.png');
						_img.setAttribute('class','completo');
						_divPeriodo.appendChild(_img);
												
						_h2dia = document.createElement('h2');
						_divPeriodo.appendChild(_h2dia);
						_h2dia.innerHTML = _dia;
						_h2dia.setAttribute('class','dia');
						
						_h2mes = document.createElement('h2');
						_divPeriodo.appendChild(_h2mes);
						_h2mes.innerHTML = _mes;
						_h2mes.setAttribute('class','mes');
						
						_h2ano = document.createElement('h2');
						_divPeriodo.appendChild(_h2ano);
						_h2ano.innerHTML = _ano;
						
						
						document.getElementById('periodo').setAttribute('campo','n1');
						
						if(_res.data.periodos[_ano][_mes][_dia].resumen!=undefined){
							_divN1=document.createElement('div');
							_divPeriodo.appendChild(_divN1);
							_divN1.setAttribute('class','resultado');
							_divN1.setAttribute('id','n1');
							
							_divValor=document.createElement('div');
							_divN1.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero1;
							
							_divPorc=document.createElement('div');
							_divN1.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero1;
							
							_divN2=document.createElement('div');
							_divPeriodo.appendChild(_divN2);
							_divN2.setAttribute('class','resultado');
							_divN2.setAttribute('id','n2');
							
							_divValor=document.createElement('div');
							_divN2.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero2;
							
							_divPorc=document.createElement('div');
							_divN2.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero2;						
							
									
							_divN3=document.createElement('div');
							_divPeriodo.appendChild(_divN3);
							_divN3.setAttribute('class','resultado');
							_divN3.setAttribute('id','n3');
							
							_divValor=document.createElement('div');
							_divN3.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero3;
							
							_divPorc=document.createElement('div');
							_divN3.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero3;						
													
													
							_divN4=document.createElement('div');
							_divPeriodo.appendChild(_divN4);
							_divN4.setAttribute('class','resultado');
							_divN4.setAttribute('id','n4');
							
							_divValor=document.createElement('div');
							_divN4.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero4;
							
							_divPorc=document.createElement('div');
							_divN4.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero4;		
							
													
							_divN5=document.createElement('div');
							_divPeriodo.appendChild(_divN5);
							_divN5.setAttribute('class','resultado');
							_divN5.setAttribute('id','n5');
							
							_divValor=document.createElement('div');
							_divN5.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero5;
							
							_divPorc=document.createElement('div');
							_divN5.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero5;		
						}												
							
                    }
                }
            }
        }else if (periodicidad == 'diario'){
			
			_divRoot = document.getElementById('selectorPeriodo');
			_divRoot.innerHTML='';
			for(_ano in _res.data.periodos){
				for(_mes in _res.data.periodos[_ano]){
					
					for(_dia in _res.data.periodos[_ano][_mes]){
						
						_divPeriodo = document.createElement('div');						
						_divRoot.appendChild(_divPeriodo);
						
						_divPeriodo.setAttribute('class', 'periodoFecha');
						_divPeriodo.setAttribute('periodo', _ano+'_'+_mes+'_'+_dia);
						_divPeriodo.setAttribute('estado_resumen','precarga');
						_divPeriodo.setAttribute('class', 'card '+ periodicidad+' ' +_res.data.periodos[_ano][_mes][_dia].estado.replace(' ',''));
						_divPeriodo.setAttribute('selected', 'false');
						_divPeriodo.setAttribute('onclick', "accionPeriodoElegido(this.getAttribute('periodo'), 'true')" );
						if(_res.data.periodos[_ano][_mes][_dia].representa!=undefined && _res.data.indicador.representar_campo !=''){
							
							_rc=_res.data.indicador.representar_campo;
							_porc=_res.data.periodos[_ano][_mes][_dia].representa[_rc+'_dato'].valora;//TODO hacer configurable
							if(_porc>1){
								_red= _rmax;
								_gre = _gmax;
								_blu = _bmax;
							}else if(_porc<0){
								_red=_rmin;
								_gre = _gmin;
								_blu = _bmin;	
									
							}else{
								_red = _rmin+(_rmax-_rmin)*_porc;
								_gre = _gmin+(_gmax-_gmin)*_porc;
								_blu = _bmin+(_bmax-_bmin)*_porc;
							}
							_color='rgba('+_red+','+_gre+','+_blu+', 1)';
							_divPeriodo.style.backgroundColor=_color;
						}
						
						_img=document.createElement('img');
						_img.setAttribute('src','./comun_img/check-sinborde.png');
						_img.setAttribute('class','completo');
						_divPeriodo.appendChild(_img);
												
						_h2dia = document.createElement('h2');
						_divPeriodo.appendChild(_h2dia);
						_h2dia.innerHTML = _dia;
						_h2dia.setAttribute('class','dia');
						
						_h2mes = document.createElement('h2');
						_divPeriodo.appendChild(_h2mes);
						_h2mes.innerHTML = _mes;
						_h2mes.setAttribute('class','mes');
						
						_h2ano = document.createElement('h2');
						_divPeriodo.appendChild(_h2ano);
						_h2ano.innerHTML = _ano;
						
						
						document.getElementById('periodo').setAttribute('campo','n1');
						
						if(_res.data.periodos[_ano][_mes][_dia].resumen!=undefined){
							_divN1=document.createElement('div');
							_divPeriodo.appendChild(_divN1);
							_divN1.setAttribute('class','resultado');
							_divN1.setAttribute('id','n1');
							
							_divValor=document.createElement('div');
							_divN1.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero1;
							
							_divPorc=document.createElement('div');
							_divN1.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero1;
							
							_divN2=document.createElement('div');
							_divPeriodo.appendChild(_divN2);
							_divN2.setAttribute('class','resultado');
							_divN2.setAttribute('id','n2');
							
							_divValor=document.createElement('div');
							_divN2.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero2;
							
							_divPorc=document.createElement('div');
							_divN2.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero2;						
							
									
							_divN3=document.createElement('div');
							_divPeriodo.appendChild(_divN3);
							_divN3.setAttribute('class','resultado');
							_divN3.setAttribute('id','n3');
							
							_divValor=document.createElement('div');
							_divN3.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero3;
							
							_divPorc=document.createElement('div');
							_divN3.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero3;						
													
													
							_divN4=document.createElement('div');
							_divPeriodo.appendChild(_divN4);
							_divN4.setAttribute('class','resultado');
							_divN4.setAttribute('id','n4');
							
							_divValor=document.createElement('div');
							_divN4.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero4;
							
							_divPorc=document.createElement('div');
							_divN4.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero4;		
							
													
							_divN5=document.createElement('div');
							_divPeriodo.appendChild(_divN5);
							_divN5.setAttribute('class','resultado');
							_divN5.setAttribute('id','n5');
							
							_divValor=document.createElement('div');
							_divN5.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero5;
							
							_divPorc=document.createElement('div');
							_divN5.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero5;		
						}												
																		
																		
						/* RESTITUIR ESTO QUE INDICA EL ESTADO GLOBAL PARA UN PERIODO
						if(periodos[indice][4]!=undefined){
							//console.log(periodos[indice][4]);	
							if(_res.data.indicador.funcionalidad=='nuevaGeometria'){
								_val=periodos[indice][4].superp_sum
								if(_val>100){
									_v=formatearNumero(_val,0);	
								}else{
									_v=formatearNumero(_val,2);	
								}
								_divValor.innerHTML=_v;
								
								
								if(Number(periodos[indice][4].superp_max_numero1)>0){
									_val=Number(periodos[indice][4].superp_sum*100/Number(periodos[indice][4].superp_max_numero1));
									if(_val>10){
										_v=formatearNumero(_val,0);	
									}else{
										_v=formatearNumero(_val,2);
									}
									_divPorc.innerHTML=_v+'%';
								}
							}else if(_res.data.indicador.funcionalidad=='geometriaExistente'){
								_val=periodos[indice][4].sum_numero1
								if(_val>100){
									_v=formatearNumero(_val,0);	
								}else{
									_v=formatearNumero(_val,2);	
								}
								_divValor.innerHTML='s: '+_v;
								
					
								_val=periodos[indice][4].prom_numero1;
								if(_val>100){
									_v=formatearNumero(_val,0);	
								}else{
									_v=formatearNumero(_val,2);	
								}
								_divPorc.innerHTML='m: '+_v;
								
							}
						}*/
						
						
					}
				}
			}
		}
		
		/*else{
			_divRoot = document.getElementById('selectorPeriodo');
			_divRoot.innerHTML='';
			for(_ano in _res.data.periodos){
				for(_mes in _res.data.periodos[_ano]){					
					for(_dia in _res.data.periodos[_ano][_mes]){
												
						_divPeriodo = document.createElement('div');						
						_divRoot.appendChild(_divPeriodo);
						
						_divPeriodo.setAttribute('class', 'periodoFecha');
						_divPeriodo.setAttribute('periodo', _ano+'_'+_mes+'_'+_dia);
						_divPeriodo.setAttribute('class', 'card '+ periodicidad+' ' +_res.data.periodos[_ano][_mes][_dia].estado.replace(' ',''));
						_divPeriodo.setAttribute('selected', 'false');
						_divPeriodo.setAttribute('onclick', "accionPeriodoElegido(this.getAttribute('periodo'), 'true')" );
						if(_res.data.periodos[_ano][_mes][_dia].representa!=undefined && _res.data.indicador.representar_campo !=''){
							
							_rc=_res.data.indicador.representar_campo;
							_porc=_res.data.periodos[_ano][_mes][_dia].representa[_rc+'_dato'].valora;//TODO hacer configurable
							if(_porc>1){
								_red= _rmax;
								_gre = _gmax;
								_blu = _bmax;
							}else if(_porc<0){
								_red=_rmin;
								_gre = _gmin;
								_blu = _bmin;	
									
							}else{
								_red = _rmin+(_rmax-_rmin)*_porc;
								_gre = _gmin+(_gmax-_gmin)*_porc;
								_blu = _bmin+(_bmax-_bmin)*_porc;
							}
							_color='rgba('+_red+','+_gre+','+_blu+', 1)';
							_divPeriodo.style.backgroundColor=_color;
						}
						
						_img=document.createElement('img');
						_img.setAttribute('src','./comun_img/check-sinborde.png');
						_img.setAttribute('class','completo');
						_divPeriodo.appendChild(_img);
												
						_h2dia = document.createElement('h2');
						_divPeriodo.appendChild(_h2dia);
						_h2dia.innerHTML = _dia;
						_h2dia.setAttribute('class','dia');
						
						_h2mes = document.createElement('h2');
						_divPeriodo.appendChild(_h2mes);
						_h2mes.innerHTML = _mes;
						_h2mes.setAttribute('class','mes');
						
						_h2ano = document.createElement('h2');
						_divPeriodo.appendChild(_h2ano);
						_h2ano.innerHTML = _ano;
						
						
						document.getElementById('periodo').setAttribute('campo','n1');
						
						if(_res.data.periodos[_ano][_mes][_dia].resumen!=undefined){
							_divN1=document.createElement('div');
							_divPeriodo.appendChild(_divN1);
							_divN1.setAttribute('class','resultado');
							_divN1.setAttribute('id','n1');
							
							_divValor=document.createElement('div');
							_divN1.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero1;
							
							_divPorc=document.createElement('div');
							_divN1.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero1;
							
							_divN2=document.createElement('div');
							_divPeriodo.appendChild(_divN2);
							_divN2.setAttribute('class','resultado');
							_divN2.setAttribute('id','n2');
							
							_divValor=document.createElement('div');
							_divN2.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero2;
							
							_divPorc=document.createElement('div');
							_divN2.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero2;						
							
									
							_divN3=document.createElement('div');
							_divPeriodo.appendChild(_divN3);
							_divN3.setAttribute('class','resultado');
							_divN3.setAttribute('id','n3');
							
							_divValor=document.createElement('div');
							_divN3.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero3;
							
							_divPorc=document.createElement('div');
							_divN3.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero3;						
													
													
							_divN4=document.createElement('div');
							_divPeriodo.appendChild(_divN4);
							_divN4.setAttribute('class','resultado');
							_divN4.setAttribute('id','n4');
							
							_divValor=document.createElement('div');
							_divN4.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero4;
							
							_divPorc=document.createElement('div');
							_divN4.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero4;		
							
													
							_divN5=document.createElement('div');
							_divPeriodo.appendChild(_divN5);
							_divN5.setAttribute('class','resultado');
							_divN5.setAttribute('id','n5');
							
							_divValor=document.createElement('div');
							_divN5.appendChild(_divValor);
							_divValor.setAttribute('id','valor');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.sum_numero5;
							
							_divPorc=document.createElement('div');
							_divN5.appendChild(_divPorc);
							_divPorc.setAttribute('id','porc');
							_divValor.innerHTML= _res.data.periodos[_ano][_mes][_dia].resumen.prom_numero5;		
						}							
					}
				}			
			}
		}*/
		

        //Mostrar los campos correctos para cargar valores
        /*
        if (_res.data['indicador']['col_texto1_nom'] != null && _res.data['indicador']['col_texto1_nom'] != ''){
            document.getElementById('cargaTexto1').style.display='block';
            document.getElementById('indCargaTexto1Nombre').innerHTML = _res.data['indicador']['col_texto1_nom'];
        } else {
            document.getElementById('cargaTexto1').style.display='none';
        }

        if (_res.data['indicador']['col_texto2_nom'] != null && _res.data['indicador']['col_texto2_nom'] != ''){
            document.getElementById('cargaTexto2').style.display='block';
            document.getElementById('indCargaTexto2Nombre').innerHTML = _res.data['indicador']['col_texto2_nom'];
        } else {
            document.getElementById('cargaTexto2').style.display='none';
        }

        if (_res.data['indicador']['col_texto3_nom'] != null && _res.data['indicador']['col_texto3_nom'] != ''){
            document.getElementById('cargaTexto3').style.display='block';
            document.getElementById('indCargaTexto3Nombre').innerHTML = _res.data['indicador']['col_texto3_nom'];
        } else {
            document.getElementById('cargaTexto3').style.display='none';
        }

        if (_res.data['indicador']['col_texto4_nom'] != null && _res.data['indicador']['col_texto4_nom'] != ''){
            document.getElementById('cargaTexto4').style.display='block';
            document.getElementById('indCargaTexto4Nombre').innerHTML = _res.data['indicador']['col_texto4_nom'];
        } else {
            document.getElementById('cargaTexto4').style.display='none';
        }

        if (_res.data['indicador']['col_texto5_nom'] != null && _res.data['indicador']['col_texto5_nom'] != ''){
            document.getElementById('cargaTexto5').style.display='block';
            document.getElementById('indCargaTexto5Nombre').innerHTML = _res.data['indicador']['col_texto5_nom'];
        } else {
            document.getElementById('cargaTexto5').style.display='none';
        }

        if (_res.data['indicador']['col_numero1_nom'] != null && _res.data['indicador']['col_numero1_nom'] != ''){
            document.getElementById('cargaNumero1').style.display='block';
            document.getElementById('indCargaNumero1Nombre').innerHTML = _res.data['indicador']['col_numero1_nom'];
            if (_res.data['indicador']['col_numero1_unidad'] != null)
                document.getElementById('indCargaNumero1Unidad').innerHTML = _res.data['indicador']['col_numero1_unidad'];
        } else {
            document.getElementById('cargaNumero1').style.display='none';
        }

        if (_res.data['indicador']['col_numero2_nom'] != null && _res.data['indicador']['col_numero2_nom'] != ''){
            document.getElementById('cargaNumero2').style.display='block';
            document.getElementById('indCargaNumero2Nombre').innerHTML = _res.data['indicador']['col_numero2_nom'];
            if (_res.data['indicador']['col_numero2_unidad'] != null)
                document.getElementById('indCargaNumero2Unidad').innerHTML = _res.data['indicador']['col_numero2_unidad'];
        } else {
            document.getElementById('cargaNumero2').style.display='none';
        }

        if (_res.data['indicador']['col_numero3_nom'] != null && _res.data['indicador']['col_numero3_nom'] != ''){
            document.getElementById('cargaNumero3').style.display='block';
            document.getElementById('indCargaNumero3Nombre').innerHTML = _res.data['indicador']['col_numero3_nom'];
            if (_res.data['indicador']['col_numero3_unidad'] != null)
                document.getElementById('indCargaNumero3Unidad').innerHTML = _res.data['indicador']['col_numero3_unidad'];
        } else {
            document.getElementById('cargaNumero3').style.display='none';
        }

        if (_res.data['indicador']['col_numero4_nom'] != null && _res.data['indicador']['col_numero4_nom'] != ''){
            document.getElementById('cargaNumero4').style.display='block';
            document.getElementById('indCargaNumero4Nombre').innerHTML = _res.data['indicador']['col_numero4_nom'];
            if (_res.data['indicador']['col_numero4_unidad'] != null)
                document.getElementById('indCargaNumero4Unidad').innerHTML = _res.data['indicador']['col_numero4_unidad'];
        } else {
            document.getElementById('cargaNumero4').style.display='none';
        }

        if (_res.data['indicador']['col_numero5_nom'] != null && _res.data['indicador']['col_numero5_nom'] != ''){
            document.getElementById('cargaNumero5').style.display='block';
            document.getElementById('indCargaNumero5Nombre').innerHTML = _res.data['indicador']['col_numero5_nom'];
            if (_res.data['indicador']['col_numero5_unidad'] != null)
                document.getElementById('indCargaNumero5Unidad').innerHTML = _res.data['indicador']['col_numero5_unidad'];
        } else {
            document.getElementById('cargaNumero5').style.display='none';
        }
        */
        
        accionPeriodoElegido(_Select_Fecha.ano+'_'+_Select_Fecha.mes+'_'+_Select_Fecha.dia, seleccionarDefault);
    } else {
        console.log('Error al cargar indicador');
    }
}

function formatNumberTwoDigits(number) {
     return (number < 10 ? '0' : '') + number;
}

function obtenerNombreMes(mesNumero){
    var month = new Array();
    month[0] = "Enero";
    month[1] = "Febrero";
    month[2] = "Marzo";
    month[3] = "Abril";
    month[4] = "Mayo";
    month[5] = "Junio";
    month[6] = "Julio";
    month[7] = "Agosto";
    month[8] = "Septiembre";
    month[9] = "Octubre";
    month[10] = "Noviembre";
    month[11] = "Diciembre";

    return month[mesNumero];
}

function editarIndicador(){
	
	//pone en edición un indicador definido. Como hacer esto es peligroso, una vez ingresados datos, solo se puede con el máximo nivel de acceso y tras verificarlo.
	
	if(confirm("¿Confirma que quiere cancelar la carga de valores para este indicador? \n Si lo hace se perderan los cambios no guardados.")){
        _idind=_DataIndicador.id;
        
        //accionCargaCancelar(_this);
                
        if(confirm("¿Confirma que quiere modificar un indicador que ya está publicado? \n Esto puede ser peligros, sobretodo y ya cuenta con valores cargados.")){
        	
        	document.querySelector('#cuadrovalores').setAttribute('formEditarIndicadores','si');
        	document.querySelector('#cuadrovalores').setAttribute('divMenuAccionesCrea','si');
        	document.querySelector('#cuadrovalores').setAttribute('botonCrearIndicador','no');
        	
        	
        	/*
	        document.getElementById('formEditarIndicadores').style.display='inline-block';
		    document.getElementById('divMenuAccionesCrea').style.display='inline-block';
		    document.getElementById('botonCrearIndicador').style.display='none';
		    
		    $('#cuadrovalores').attr('estado','elemento');
		    //document.getElementById('formSeleccionInd').style.display='none';*/
	        consultarIndicadorParaModificar(_idind);
	        
        }
    }
}

function accionPeriodoElegido(_periodo, seleccionarDefault){
	
	
	_p=_periodo.split('_');
	_Select_Fecha['ano']=_p[0];
	_Select_Fecha['mes']=_p[1];
	_Select_Fecha['dia']=_p[2];
	//console.log(_Select_Fecha);
	
	_source_ind_sel.clear();//limpia la source de la capa de seleccion cargada en mapa. 
	
    var idindicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    //var periodicidad = document.getElementById('indCargaPeriodicidad').innerHTML;
    var periodicidad = document.getElementById('indicadorActivo').getAttribute('periodicidad');
    
    
    if (periodicidad == 'diario') {
        document.querySelector('#indCargaPeriodoLabel').innerHTML = _Select_Fecha.dia+" de "+obtenerNombreMes(_Select_Fecha.mes-1) + " " + _Select_Fecha.ano;
        _DataPeriodo['dia']=_Select_Fecha.dia;
        _DataPeriodo['mes']=_Select_Fecha.mes;
        _DataPeriodo['ano']=_Select_Fecha.ano;
    } else if (periodicidad == 'mensual') {
        document.querySelector('#indCargaPeriodoLabel').innerHTML = obtenerNombreMes(_Select_Fecha.mes-1) + " " + _Select_Fecha.ano;
        _DataPeriodo['dia']='';
        _DataPeriodo['mes']=_Select_Fecha.mes;
        _DataPeriodo['ano']=_Select_Fecha.ano;
    } else {
        document.querySelector('#indCargaPeriodoLabel').innerHTML = _Select_Fecha.ano;
        _DataPeriodo['dia']='';
        _DataPeriodo['mes']='';
        _DataPeriodo['ano']=_Select_Fecha.ano;
    }
    
    document.getElementById('divPeriodoSeleccionado').setAttribute('ano', _Select_Fecha.ano);
    /*
    if (periodicidad == 'mensual') {
        document.getElementById('divPeriodoSeleccionado').setAttribute('mes', _Select_Fecha.mes);
    }
    if (periodicidad == 'diario') {*/
        document.getElementById('divPeriodoSeleccionado').setAttribute('mes', _Select_Fecha.mes);		
        document.getElementById('divPeriodoSeleccionado').setAttribute('dia', _Select_Fecha.dia);
    //}
    
    var selectorPeriodo = document.getElementById('selectorPeriodo');
    for (var child in selectorPeriodo.children) {
        if (selectorPeriodo.children[child].nodeType == 1
                && selectorPeriodo.children[child].getAttribute('selected') == 'true'){
            selectorPeriodo.children[child].setAttribute('selected', 'false');
        }
    }
    
    _divPeriodoFecha = document.querySelector('#selectorPeriodo [periodo="'+_periodo+'"]');
    if(_divPeriodoFecha!=null){
		_divPeriodoFecha.setAttribute('selected', 'true');			
		_divPeriodoFecha.scrollIntoView();
	}    
	
    cargarPoligonosIndicadorPublicado(
		_DataIndicador.id, 
		_Select_Fecha.ano, 
		_Select_Fecha.mes, 
		_Select_Fecha.dia, 
		seleccionarDefault
	);
}


function renombrarGeometria(_this,_event){
	_event.preventDefault();
	if(_event.keyCode=='13'){
		_id=_this.parentNode.parentNode.getAttribute('idgeom');
		_nombre=_this.value;
		guardarNombreGeometria(_id,_nombre);
	}else{
		_this.setAttribute('editando','si');
	}
}



function cargarFormularioNuevasGeometrias(_res){


    _form=document.querySelector('#divPeriodoSeleccionado #listaUnidadesInd');
    _form.innerHTML='';
	document.getElementById('divPeriodoSeleccionado').setAttribute('idindval', _res.data.indicador["id"]);
    document.getElementById('divPeriodoSeleccionado').setAttribute('idgeom', '');
    document.querySelector('#divPeriodoSeleccionado #divMenuAccionesEditarValor #botonCrearGeom').style.display='inline-block';
    document.querySelector('#divPeriodoSeleccionado #divMenuAccionesEditarValor #botonDuplicarGeom').style.display='inline-block';
    
    
	console.log( _res.data.geom);
    for(_ng in _res.data.geom){
    	_datg=_res.data.geom[_ng];
    	
    	
		if(_DataIndicador.funcionalidad=='nuevaGeometria'&&_datg.estadocarga!='listo'){
			//esta geometría no es para este período
			continue;	
		}
		
    	_divg=document.createElement('div');
    	_divg.setAttribute('class','unidad');
    	_divg.setAttribute('idgeom',_datg.id);
    	_form.appendChild(_divg);
    	console.log(_divg);
    	_aelim=document.createElement('a');
    	_aelim.setAttribute('class','eliminar');
    	_aelim.setAttribute('onclick','eliminarGeom(this)');
    	_aelim.innerHTML='x';
    	_aelim.title='Elminar esta geometría de este período';
    	_divg.appendChild(_aelim);
    	
    	
    	_nombre=document.createElement('h4');
    	_nombre.innerHTML='<input title="renombrar esta geometria" class="renombra" onkeyup="renombrarGeometria(this,event)" value="'+_datg.texto1+'">';
    	_divg.appendChild(_nombre);
    	for(_var in _res.data.indicador){
    		
    		if(
    			_var.substring(0,4)=='col_'
    			&&
    			_var.substring((_var.length-4),_var.length)=='_nom'
    			&&
    			_res.data.indicador[_var]!=null
    			){
    			
    			_campo=document.createElement('div');
		    	//_campo.setAttribute('class','elementoOculto');
		    	_idv=_var.replace('col_','');
		    	_idv=_idv.replace('_nom','');
		    	_idv=_idv.replace('texto','Texto');
		    	_idv=_idv.replace('numero','Numero');
		    	_idTit='indCarga'+_idv+'Nombre';
		    	_idInp='indCarga'+_idv+'Dato';
		    	_idv='carga'+_idv;
		    	_campo.setAttribute('id',_idv);
		    	_divg.appendChild(_campo);
		    	
		    	_titulo=document.createElement('div');
		    	_titulo.setAttribute('id',_idTit);
		    	_titulo.innerHTML=_res.data.indicador[_var];
		    	_campo.appendChild(_titulo);
		    	
		    	_input=document.createElement('input');
		    	_input.setAttribute('id',_idInp);
		    	_input.setAttribute('onkeyup','controlarCambiosinput(this)');
		    	_campo.appendChild(_input);
		    	if(_idInp.substring(8,5)=='Texto'){
		    		_input.setAttribute('type','text');
		    	}else{
		    		_input.setAttribute('class','number');
		    		_input.setAttribute('type','text');
		    		_input.setAttribute('step','any');
		    		
		    		_unimed=document.createElement('div');
		    		_idUnimed=_idInp.replace('Dato','Unidad');
			    	_unimed.setAttribute('id',_idUnimed);
			    	_campo.appendChild(_unimed);
		    	}
		    	
		    	_input.setAttribute('valororiginal','');
		    	if(_datg.valores[0]!=undefined){
			    	if(_datg.valores[0][_var.replace('_nom','_dato')]!=null){
			    		_input.value=_datg.valores[0][_var.replace('_nom','_dato')];
			    		_input.setAttribute('valororiginal',_datg.valores[0][_var.replace('_nom','_dato')]);
			    	}
		    	}
		    			    	
    		}
    	}
    }
    document.getElementById('divPeriodoSeleccionado').style.display='block';
 }
     



function cargarFormularioValoresMultiple(_res){
    _form=document.querySelector('#divPeriodoSeleccionado #listaUnidadesInd');
    _form.innerHTML='';
    
    document.getElementById('divPeriodoSeleccionado').setAttribute('idindval', _res.data.indicador["id"]);
    document.getElementById('divPeriodoSeleccionado').setAttribute('idgeom', '');
	//console.log( _res.data.geom);
    for(_ng in _res.data.geom){
    	_datg=_res.data.geom[_ng];
    	
    	_divg=document.createElement('div');
    	_divg.setAttribute('class','unidad');
    	_divg.setAttribute('idgeom',_datg.id);
    	_form.appendChild(_divg);
    	
    	
    	_nombre=document.createElement('h4');
    	_nombre.innerHTML=_datg.texto1;
    	_divg.appendChild(_nombre);
    	for(_var in _res.data.indicador){
    		
    		if(
    			_var.substring(0,4)=='col_'
    			&&
    			_var.substring((_var.length-4),_var.length)=='_nom'
    			&&
    			_res.data.indicador[_var]!=null
    			){
    			
    			_campo=document.createElement('div');
		    	//_campo.setAttribute('class','elementoOculto');
		    	_idv=_var.replace('col_','');
		    	_idv=_idv.replace('_nom','');
		    	_idv=_idv.replace('texto','Texto');
		    	_idv=_idv.replace('numero','Numero');
		    	_idTit='indCarga'+_idv+'Nombre';
		    	_idInp='indCarga'+_idv+'Dato';
		    	_idv='carga'+_idv;
		    	_campo.setAttribute('id',_idv);
		    	_divg.appendChild(_campo);
		    	
		    	_titulo=document.createElement('div');
		    	_titulo.setAttribute('id',_idTit);
		    	_titulo.innerHTML=_res.data.indicador[_var];
		    	_campo.appendChild(_titulo);
		    	
		    	_input=document.createElement('input');
		    	_input.setAttribute('id',_idInp);
		    	_input.setAttribute('onkeyup','controlarCambiosinput(this)');
		    	_campo.appendChild(_input);
		    	if(_idInp.substring(8,5)=='Texto'){
		    		_input.setAttribute('type','text');
		    	}else{
		    		_input.setAttribute('class','number');
		    		_input.setAttribute('type','text');
		    		_input.setAttribute('step','any');
		    		
		    		_unimed=document.createElement('div');
		    		_idUnimed=_idInp.replace('Dato','Unidad');
			    	_unimed.setAttribute('id',_idUnimed);
			    	_campo.appendChild(_unimed);
		    	}
		    	
		    	_input.setAttribute('valororiginal','');
		    	if(_datg.valores[0]!=undefined){
			    	if(_datg.valores[0][_var.replace('_nom','_dato')]!=null){
			    		_input.value=_datg.valores[0][_var.replace('_nom','_dato')];
			    		_input.setAttribute('valororiginal',_datg.valores[0][_var.replace('_nom','_dato')]);
			    	}
		    	}
		    			    	
    		}
    	}
    }
    document.getElementById('divPeriodoSeleccionado').style.display='block';
 }
 
 function controlarCambiosinput(_this){
 	if(_this.value!=_this.getAttribute('valororiginal')){
 		_this.setAttribute('cambiado','si');
 	}else{
 		_this.removeAttribute('cambiado');
 	}
 }    	



/*
function cargarFormularioValores(_res){ 
    limpiarValorIndicador();
            
    if (_res.data != null){
        for (var elem in _res.data){
            document.getElementById('divPeriodoSeleccionado').setAttribute('idindval', _res.data[elem]["id"]);
            document.getElementById('divPeriodoSeleccionado').setAttribute('idgeom', _res.data[elem]["id_p_ref_capas_registros"]);
            
            if (_res.data[elem]["col_texto1_dato"] != null){
                document.getElementById('indCargaTexto1Dato').value = _res.data[elem]["col_texto1_dato"];
            }
            if (_res.data[elem]["col_texto2_dato"] != null){
                document.getElementById('indCargaTexto2Dato').value = _res.data[elem]["col_texto2_dato"];
            }
            if (_res.data[elem]["col_texto3_dato"] != null){
                document.getElementById('indCargaTexto3Dato').value = _res.data[elem]["col_texto3_dato"];
            }
            if (_res.data[elem]["col_texto4_dato"] != null){
                document.getElementById('indCargaTexto4Dato').value = _res.data[elem]["col_texto4_dato"];
            }
            if (_res.data[elem]["col_texto5_dato"] != null){
                document.getElementById('indCargaTexto5Dato').value = _res.data[elem]["col_texto5_dato"];
            }
            
            if (_res.data[elem]["col_numero1_dato"] != null){
                document.getElementById('indCargaNumero1Dato').value = _res.data[elem]["col_numero1_dato"];
            }
            if (_res.data[elem]["col_numero2_dato"] != null){
                document.getElementById('indCargaNumero2Dato').value = _res.data[elem]["col_numero2_dato"];
            }
            if (_res.data[elem]["col_numero3_dato"] != null){
                document.getElementById('indCargaNumero3Dato').value = _res.data[elem]["col_numero3_dato"];
            }
            if (_res.data[elem]["col_numero4_dato"] != null){
                document.getElementById('indCargaNumero4Dato').value = _res.data[elem]["col_numero4_dato"];
            }
            if (_res.data[elem]["col_numero5_dato"] != null){
                document.getElementById('indCargaNumero5Dato').value = _res.data[elem]["col_numero5_dato"];
            }
            
            break; //Agarro el primero porque se supone que solo viene un resultado
        }
    }
    
    document.getElementById('divPeriodoSeleccionado').style.display='block';
}
*/


function limpiarValorIndicador(){
    document.getElementById('indCargaTexto1Dato').value = null;
    document.getElementById('indCargaTexto2Dato').value = null;
    document.getElementById('indCargaTexto3Dato').value = null;
    document.getElementById('indCargaTexto4Dato').value = null;
    document.getElementById('indCargaTexto5Dato').value = null;

    document.getElementById('indCargaNumero1Dato').value = null;
    document.getElementById('indCargaNumero2Dato').value = null;
    document.getElementById('indCargaNumero3Dato').value = null;
    document.getElementById('indCargaNumero4Dato').value = null;
    document.getElementById('indCargaNumero5Dato').value = null;
}

function accionEditarValorEliminar(_this){
    var idindval = document.getElementById('divPeriodoSeleccionado').getAttribute('idindval');
    if (idindval != null && idindval != '' && idindval != 'undefined'){
        if(confirm("¿Confirma que quiere eliminar este valor para el indicador? \n Si lo hace se guardará registro en la papelera de los datos cargados en el formulario.")){
            //console.log('ve');

            eliminarValorIndicador(idindval);
            refrescarIndicadorActivo();
        }
    } else {
       console.log('no hay registro para borrar'); 
    }
}

/*
function seleccionarGeomDefault(_res){
    if (_res.data != null){
        for (var elem in _res.data['geom']){
            accionSeleccionarGeom(elem, _res);
            break; //Agarro el primero que encuentre
        }
    }
}
*/

function accionGeomSeleccionada(idgeom, _res){
    //document.getElementById('divPeriodoSeleccionado').setAttribute('idgeom', idgeom);
    _sels=document.querySelectorAll('#listaUnidadesInd .unidad[selecta="si"]');
    for(_sn in _sels){
    	if(typeof _sels[_sn] != 'object'){continue;}
    	_sels[_sn].removeAttribute('selecta');
    }
    document.querySelector('#listaUnidadesInd .unidad[idgeom="'+idgeom+'"]').setAttribute('selecta','si');
    //consultarFormularioValores(_res.data['indicador'].id, _res.data['geom'][idgeom].id);
    
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
	console.log('buscando: '+_str);
	
	_lis=document.querySelectorAll('#listaindpublicadas > a.filaIndLista');
	
	for(_ln in _lis){
		if(typeof _lis[_ln] != 'object'){continue;}
		
		_contId=_lis[_ln].querySelector('#indIdLista');
		_contNom=_lis[_ln].querySelector('#indNombreLista');
		_contDes=_lis[_ln].querySelector('#indDescripcionLista');
		
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
	
	/*
	buscarContratos();
	buscarWiki();
	buscarAntec();
	buscarTelef();*/
}


