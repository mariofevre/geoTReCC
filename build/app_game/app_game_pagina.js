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
    limpiarFormularioSesiones();
    limpiarFormularioSeleccionCapa();
    limpiarFormularioCapa();
    
    document.getElementById('formEditarSesiones').style.display='none';
    document.getElementById('formSeleccionSesion').style.display='none';
    document.getElementById('divListaSesionesCarga').style.display='none';
    document.querySelector('#divListaSesionesCarga #botonSeleccionarIndCambio').removeAttribute('idind');
    document.getElementById('divMenuAccionesCrea').style.display='none';
    document.getElementById('botonCrearSesion').style.display='block';
    
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


	_form=document.querySelector('#formEditarSesiones');
	
	_parametros={
		'codMarco':getParameterByName('cod'),
		'idSesion':_DataSesion.id,
		'nombre':_form.querySelector('input#sesionNombre').value,
		'presentacion':_form.querySelector('textarea#sesionPresentacion').value,
		'id_p_indicadores_indicadores':_form.querySelector('select#sesionIndicadorAsociado').value,
		'costounitario':_form.querySelector('input#sesionCostounitario').value,
		'limiteunitarioporturno':_form.querySelector('input#sesionLimiteunitarioporturno').value,
		'modored':_form.querySelector('input#sesionModored').value,
		'turnos':_form.querySelector('input#sesionTurnos').value
	}
	
	editarSesion(_parametros);
}

function accionCreaPublicar(_this){
    accionCreaGuardar();
    
    if (confirm('Esta seguro que desea publicar este indicador? \nUna vez publicado no se pueden hacer cambios')) {
        publicarIndicador();
    }
}

function accionCargaCancelar(_this){
    limpiarFormularioSesiones();
    limpiarFormularioSeleccionCapa();
    limpiarFormularioCapa();
    limpiarFormularioSesionesPublicadas();
    
    document.getElementById('formEditarSesiones').style.display='none';
    document.getElementById('formSeleccionSesion').style.display='none';
    document.getElementById('divListaSesionesCarga').style.display='none';
    document.querySelector('#divListaSesionesCarga #botonSeleccionarIndCambio').removeAttribute('idind');
    //document.getElementById('divMenuAccionesCarga').style.display='none';
    document.getElementById('botonCrearSesion').style.display='block';
    
   _source_ind.clear();
   _source_ind_buffer.clear();
   _source_ind_superp.clear();
    accionCargarIndicador();
    
    
    //accionCancelarSeleccionCapa(_this);
}

function accionCrearSesion(_this){
	
    generarNuevaSesion();
    document.getElementById('formEditarSesiones').style.display='inline-block';
    document.getElementById('divMenuAccionesCrea').style.display='inline-block';
    document.getElementById('botonCrearSesion').style.display='none';
 //   document.getElementById('divSeleccionIndCuerpo').style.display='none';   
    //document.getElementById('divListaSesionesCarga').style.display='none';   
}

function accionCargarSesion(){
    limpiarFormularioSesionesPublicadas();
    cargarListadoSesionesPublicadas();
    
    document.getElementById('formSeleccionSesion').style.display='inline-block';
    //document.getElementById('divMenuAccionesCarga').style.display='inline-block';
    document.getElementById('botonCrearSesion').style.display='block';
}
accionCargarSesion();

function accionModificarIndicador(_this){
    if(confirm("Editar Sesiones ya publicados puede causar errores en el sistema. \n Esta seguro que desea continuar con esta operacion?")){
        limpiarFormularioSesionesPublicadas();
        cargarListadoSesionesPublicadosAModificar();

        document.getElementById('formSeleccionSesion').style.display='inline-block';
        document.getElementById('divMenuAccionesCrea').style.display='inline-block';
        document.getElementById('botonCrearSesion').style.display='none';
        
    }
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

function cargarValoresSesionesPublicadas(_res){
    if (_res.data != null){
        for (var elemCapa in _res.data){
            var divRoot = document.getElementById('listaSesionesPublicadas');
            var filaSesion = document.createElement('a');
            filaSesion.setAttribute('idcapa', _res.data[elemCapa]["id"]);
            filaSesion.setAttribute('class', 'filaSesionLista');
            filaSesion.setAttribute('onclick', "accionSesionPublicadaSeleccionada(this,"+_res.data[elemCapa]["id"]+")" );
            var sesionId = document.createElement('div');
            sesionId.setAttribute('id','sesionIdLista');
            sesionId.innerHTML = "ID <span class='idn'>" + _res.data[elemCapa]["id"]+"</span>";
            var sesionNombre = document.createElement('div');
            sesionNombre.setAttribute('id','sesionNombreLista');
            sesionNombre.innerHTML = _res.data[elemCapa]["nombre"];
            var sesionPresentacion = document.createElement('div');
            sesionPresentacion.setAttribute('id','capaDescripcionLista');
            sesionPresentacion.innerHTML = _res.data[elemCapa]["presentacion"];
            filaSesion.appendChild(sesionId);
            filaSesion.appendChild(sesionNombre);
            filaSesion.appendChild(sesionPresentacion);
            divRoot.appendChild(filaSesion);
            
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

function limpiarFormularioSesiones(){
    document.getElementById('formEditarSesiones').setAttribute('idindicador', 0);
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


function limpiarFormularioSesionesPublicadas(){
    document.getElementById('sesionActiva').setAttribute('idsesion', 0);
    document.querySelector('#divSeleccionSesionCuerpo #txningunasesion').style.display='block';
    document.getElementById('sesionNombre').innerHTML = '';
    document.getElementById('sesionPresentacion').innerHTML = '';
}

function asignarIdIndicador(idIndicador){
    document.getElementById('formEditarSesiones').setAttribute('idindicador', idIndicador);
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

		_form=document.querySelector('#formEditarSesiones');
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
    var idindicador = document.getElementById('formEditarSesiones').getAttribute('idindicador');
    
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
    var idindicador = document.getElementById('formEditarSesiones').getAttribute('idindicador');
    
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
    
    if (nuevaPeriodicidad == 'mensual' || nuevaPeriodicidad == 'anual'){
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

function mostrarListadoSesionesPublicadas(){
    document.querySelector('#divSeleccionSesionCuerpo #txningunasesion').style.display='none';
    document.getElementById('divSeleccionSesionCuerpo').style.display='block';
}

function accionSesionPublicadaSeleccionada(_this, idsesion){
	_encuadrado='no';
    document.getElementById('sesionActiva').setAttribute('idsesion', idsesion);  
    document.querySelector('#formSeleccionSesion').style.display='none';
    document.querySelector('#botonCancelarCarga').style.display='block';
    document.querySelector('#formEditarSesiones').style.display='block';
    document.querySelector('#divMenuAccionesCrea').style.display='block';
    var fechaHoy = new Date();
    cargarSesionPublicada(idsesion);
}


function accionSesionPublicadaSeleccionadoModificar(_this, idsesion){
    consultarIndicadorParaModificar(idindicador);
    document.getElementById('formSeleccionSesion').style.display='none';
    document.getElementById('formEditarSesiones').style.display='inline-block';
    document.getElementById('divMenuAccionesCrea').style.display='inline-block';
}

function accionSesionPublicadaCargar(_idsesion){
	if(_DataSesion.id!=_idsesion){alert('error');return;}
    
    _form=document.querySelector('#formEditarSesiones');
    _form.querySelector('input#idsesion').value=_idsesion;
    _form.querySelector('input#sesionNombre').value=_DataSesion.nombre;
    _form.querySelector('textarea#sesionPresentacion').value=_DataSesion.presentacion;
    
    _form.querySelector('select#sesionIndicadorAsociado').value=_DataSesion.id_p_indicadores_indicadores;
    
    
    _form.querySelector('input#sesionCostounitario').value=_DataSesion.costounitario;
    _form.querySelector('input#sesionLimiteunitarioporturno').value=_DataSesion.limiteunitarioporturno;    
    
    
    _ch=_form.querySelector('input#checksesionModored');
    			
    if(_DataSesion.modored=='1'){
    	_ch.checked=true;
    }else{
    	_ch.checked=false;
    }
    chekearinput(_ch);
        
    _form.querySelector('input#sesionTurnos').value=_DataSesion.turnos;   
}

function formatNumberTwoDigits(number) {
     return (number < 10 ? '0' : '') + number;
}



function accionSeleccionarIndCambio(_this){
	
	//pone en edición un indicador definido. Como hacer esto es peligroso, una vez ingresados datos, solo se puede con el máximo lnivel de acceso y tras verificarlo.
	
	if(confirm("¿Confirma que quiere cancelar la carga de valores para este indicador? \n Si lo hace se perderan los cambios no guardados.")){
        _idind=_this.parentNode.parentNode.getAttribute('idindicadorcarga');
        accionCargaCancelar(_this);
                
        if(confirm("¿Confirma que quiere modificar un indicador que ya está publicado? \n Esto puede ser peligros, sobretodo y ya cuenta con valores cargados.")){
        	
	        document.getElementById('formEditarSesiones').style.display='inline-block';
		    document.getElementById('divMenuAccionesCrea').style.display='inline-block';
		    document.getElementById('botonCrearSesion').style.display='none';
		    document.getElementById('formSeleccionSesion').style.display='none';
	        consultarIndicadorParaModificar(_idind);
	        
        }
    }
}

function accionPeriodoElegido(ano, mes, seleccionarDefault){
	
	_source_ind_sel.clear();//limpia la source de la capa de seleccion cargada en mapa. 
	
	
    var idindicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    //var periodicidad = document.getElementById('indCargaPeriodicidad').innerHTML;
    var periodicidad = document.getElementById('indicadorActivo').getAttribute('periodicidad');
    
    if (periodicidad == 'mensual') {
        document.getElementById('indCargaPeriodoLabel').innerHTML = obtenerNombreMes(mes-1) + " " + ano;
        _DataPeriodo['mes']=mes;
        _DataPeriodo['ano']=ano;
    } else {
        document.getElementById('indCargaPeriodoLabel').innerHTML = ano;
        _DataPeriodo['mes']='';
        _DataPeriodo['ano']=ano;
    }
    
    document.getElementById('divPeriodoSeleccionado').setAttribute('ano', ano);
    if (periodicidad == 'mensual') {
        document.getElementById('divPeriodoSeleccionado').setAttribute('mes', mes);
    }
    
    var selectorPeriodo = document.getElementById('selectorPeriodo');
    for (var child in selectorPeriodo.children) {
        if (selectorPeriodo.children[child].nodeType == 1
                && selectorPeriodo.children[child].getAttribute('selected') == 'true'){
            selectorPeriodo.children[child].setAttribute('style', 'border: 2px solid white;');
            selectorPeriodo.children[child].setAttribute('selected', 'false');
        }
    }
    
    var idPeriodoFecha = 'periodoFecha'+ano;
    if (periodicidad == 'mensual'){
        idPeriodoFecha = 'periodoFecha'+obtenerNombreMes(mes-1)+ano;
    }
    console.log(idPeriodoFecha);
    var divPeriodoFecha = document.getElementById(idPeriodoFecha);
    divPeriodoFecha.setAttribute('style', 'border: 2px solid rgb(8,175,217);');
    divPeriodoFecha.setAttribute('selected', 'true');
    
    cargarPoligonosIndicadorPublicado(idindicador, ano, mes, seleccionarDefault);
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
    
    
	//console.log( _res.data.geom);
    for(_ng in _res.data.geom){
    	_datg=_res.data.geom[_ng];
    	
    	_divg=document.createElement('div');
    	_divg.setAttribute('class','unidad');
    	_divg.setAttribute('idgeom',_datg.id);
    	_form.appendChild(_divg);
    	
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
}

function chekearinput(_this){
	_c={
		't':_this.getAttribute('t'),
		'f':_this.getAttribute('f')
	};
	_for=_this.getAttribute('for');
	_inp=_this.parentNode.querySelector('#'+_for)
	if(_this.checked==true){
		_inp.value=_c['t'];
	}else{
		_inp.value=_c['f'];
	}
}

function accionJugar(){
	_idsesion=_DataSesion.id;
	_url='./app_game_play.php?idsesion='+_idsesion+'&cod='+_CodMarco;
	window.location.assign(_url);
} 

function activaborrar(_id){
	
	_act=document.querySelector('#'+_id).getAttribute('activo');
	_act=parseInt(_act);
	_act*(-1);
	document.querySelector('#'+_id).setAttribute('activo',_act);
	
}

