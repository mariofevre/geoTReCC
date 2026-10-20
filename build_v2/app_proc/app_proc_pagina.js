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
consultarPermisos();



function accionCargarNuevaCapa(){
    generarNuevaCapa();
    document.getElementById('divSeleccionCapa').style.display='none';
    document.getElementById('divCargaCapa').style.display='block';
    limpiarFormCapa();
    //document.getElementById('botonElegirCapa').style.display='none';
    document.getElementById('botonAnadirCapa').style.display='none';
}

function accionCargarCapaExist(){
    cargarListadoCapasPublicadas();    
    document.getElementById('divSeleccionCapa').style.display='none';
    document.getElementById('botonCancelarCarga').style.display='none';
    
    //document.getElementById('botonAnadirCapa').style.display='block';
}
//accionCargarCapaExist();

function accionCancelarCargarNuevaCapa(_this){
	document.getElementById('listacapaspublicadas').innerHTML='';
    cargarListadoCapasPublicadas();    
    document.getElementById('divSeleccionCapa').style.display='none';
    document.getElementById('botonCancelarCarga').style.display='none';
    //document.getElementById('botonAnadirCapa').style.display='block';
    
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
    		//console.log(_divrules[_nr]);
    		_divrules[_nr].parentNode.removeChild(_divrules[_nr]);
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

