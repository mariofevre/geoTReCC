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



function cargarFormMarco(){
	/*
	document.querySelector('#formEditarCandidato').setAttribute('idcandidato',_DataMarco.id);
	document.querySelector('.elementoCarga #marco_descripcion').value=_DataMarco.descripcion;
	document.querySelector('.elementoCarga #marco_geotx').value=_DataMarco.geotx;
	document.querySelector('.elementoCarga #marco_nombre').value=_DataMarco.nombre;
	document.querySelector('.elementoCarga #marco_nombre_oficial').value=_DataMarco.nombre_oficial;	
	* */
}


function accionCargarNuevaCapa(_this){
    generarNuevaCapa(_this);
    document.getElementById('divSeleccionCapa').style.display='none';
    document.getElementById('divCargaCapa').style.display='block';
    //document.getElementById('botonElegirCapa').style.display='none';
    document.getElementById('botonAnadirCapa').style.display='none';
}

function accionCargarCapaExist(){
    cargarListadoCapasPublicadas();    
    document.getElementById('divSeleccionCapa').style.display='none';
    document.getElementById('botonCancelarCarga').style.display='none';
    //document.getElementById('botonElegirCapa').style.display='none';
    document.getElementById('botonAnadirCapa').style.display='block';
}

function accionCancelarCargarNuevaCapa(_this){
	document.getElementById('listacapaspublicadas').innerHTML='';
    cargarListadoCapasPublicadas();    
    document.getElementById('divSeleccionCapa').style.display='none';
    document.getElementById('botonCancelarCarga').style.display='none';
    //document.getElementById('botonElegirCapa').style.display='none';
    document.getElementById('botonAnadirCapa').style.display='block';
    
    limpiarFormularioCapa();
}

function accionCancelarSeleccionCapa(_this){
    document.getElementById('divSeleccionCapa').style.display='none';
    document.getElementById('botonElegirCapa').style.display='block';
    document.getElementById('botonAnadirCapa').style.display='block';
    
    limpiarFormularioSeleccionCapa();
    limpiarFormularioCapa();
}

function accionCargarCapaPublicada(_this, idcapa){
    limpiarFormularioSeleccionCapa();
    limpiarFormularioCapa();
    
    cargarDatosCapaPublicada(idcapa);
    
    
    document.getElementById('divSeleccionCapa').style.display='none';
    document.getElementById('divCargaCapa').style.display='block';
    //document.getElementById('botonElegirCapa').style.display='none';
    document.getElementById('botonAnadirCapa').style.display='none';
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
    document.getElementById('divCargaCapa').style.display='none';
    cagarDefaults();
    
    _divrules=document.querySelectorAll('#simbologia > div[name="rule"]');
    for(_nr in _divrules){
    	if(typeof _divrules[_nr] == 'object'){
    		console.log(_divrules[_nr]);
    		_divrules[_nr].parentNode.removeChild(_divrules[_nr]);
    	}
    }
}



function cargarValoresCapaExistQuery(){
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa'); 
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco,
            'idcapa': idCapa
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
            
            _Features=_res.data;
            
            if(_res.res == 'exito'){
            	cargarFeatures();
            }
        }
    });
}

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

        _lyrElemSrc.addFeature(_feat);

        _MapaCargado='si';
    }

    _ext= _lyrElemSrc.getExtent();

    setTimeout(function(){
        mapa.getView().fit(_ext, { duration: 1000 });
    }, 200);
}

function generarNuevaCapa(_this){
    //consultar si ya existe una capa sin publicar para este autor y sino crearla
    var _this = _this;
    
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '0';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };
    
    $.ajax({
            url:   './app_capa/app_capa_consultar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                console.log(_res);
                if(_res.res=='exito'){
                    if (_res.data != null){
                        cargarValoresCapaExist(_res);
                    } else {
                        generarNuevaCapaQuery(_this);
                    }
                }else{
                    alert('error asf0jg44f9fgh');
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
                console.log(_res);
                if(_res.res=='exito'){
                    asignarIdCapa(_res.data.id);
                }else{
                    alert('error asf0jg44ffgh');
                }
            }
    });
}

function asignarIdCapa(idCapa){
    document.getElementById('divCargaCapa').setAttribute('idcapa', idCapa);
}

function editarCapaNombre(_event,_this){
    console.log(_event.keyCode);
    if(_event.keyCode==9){return;}//tab
    if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
    if(_event.keyCode==13){
        editarNombreCapa();
    }
}

function editarCapaDescripcion(_event,_this){
    console.log(_event.keyCode);
    if(_event.keyCode==9){return;}//tab
    if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
    if(_event.keyCode==13){
        editarDescripcionCapa();
    }
}

function editarNombreCapa(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
    var nuevoNombre = document.getElementById('capaNombre').value;
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idCapa,
        'nombre': nuevoNombre
    };
    
    editarCapa(parametros);
}

function editarDescripcionCapa(idCapa, descripcion){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
    var nuevaDescripcion = document.getElementById('capaDescripcion').value;
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idCapa,
        'descripcion': nuevaDescripcion
    };

    editarCapa(parametros);
}



function accionGuardarCandidato(){
    var idCand = document.getElementById('formEditarCandidato').getAttribute('idcandidato');
    var nuevaDescripcion = document.getElementById('capaDescripcion').value;
    var parametros = {
        'codMarco': _CodMarco,
        'idcand': idCand,
        
    };
    _inps=document.querySelectorAll('#formEditarCandidato .elementoCarga input, #formEditarCandidato .elementoCarga textarea');
    for(_ni in _inps){
    	if(typeof _inps[_ni] != 'object'){continue;}
		_nom=_inps[_ni].getAttribute('name');
		parametros[_nom]=_inps[_ni].value;
    }	
	
    $.ajax({
            url:   './app_est/app_est_editarCandidato.php',
            type:  'post',
            data: parametros,
            error:  function (response){alert('error al contactar al servidor');},
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                console.log(_res);
                for(_nm in _res.mg){alert(_res.mg[_nm]);}
                if(_res.res=='exito'){
                	alert('ok');
                    //Hacer algo luego de editar?
                }else{
                    alert('error asf0jg4fcn02h');
                }
            }
    });
}


function accionPublicarCandidato(){
    var idCand = document.getElementById('formEditarCandidato').getAttribute('idcandidato');
    var nuevaDescripcion = document.getElementById('capaDescripcion').value;
    var parametros = {
        'idcand': idCand,
    };
    _inps=document.querySelectorAll('#formEditarCandidato .elementoCarga input, #formEditarCandidato .elementoCarga textarea');
    for(_ni in _inps){
    	if(typeof _inps[_ni] != 'object'){continue;}
		_nom=_inps[_ni].getAttribute('name');
		parametros[_nom]=_inps[_ni].value;
    }	
	
    $.ajax({
            url:   './app_est/app_est_publicar_candidato.php',
            type:  'post',
            data: parametros,
            error:  function (response){alert('error al contactar al servidor');},
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                console.log(_res);
                for(_nm in _res.mg){alert(_res.mg[_nm]);}
                if(_res.res=='exito'){
                	
                	//_url= './index.php?est=est_02_marcoacademico&cod='+_CodMarco;
                    //location.assign(_url);
                    
                }else{
                    alert('error asf0jg4fcn02h');
                }
            }
    });
}

/*

function publicarCapaQuery(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');

    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': document.getElementById('divCargaCapa').getAttribute('idcapa')
    };
    
    $.ajax({
            url:   './app_capa/app_capa_publicar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                console.log(_res);
                if(_res.res!='exito'){
                    alert('error asf0jofvg4fcn02h');
                }
            }
    });
}

function publicarCapa(_this){
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco,
            'idcapa': idCapa
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
            
            if(_res.res == 'exito'){
                publicarCapaQuery();

                accionCancelarCargarNuevaCapa(_this);
                alert("Capa publicada");
            } else {
                alert ("La capa no tiene shapefile cargado");
            }
        }
    });
}
*/
