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
            _res = $.parseJSON(response);
            for(_nm in _res.mg){alert(_res.mg[_nm]);}
            for(_na in _res.acc){procesarAcc(_res.acc[_na]);}
            if(_res.res!='exito'){
                alert('error al consultar la base de datos');
            }
        }
    });	
}
consultarPermisos();


function tecleoGeneral(_ev){
	
	console.log(_ev.keyCode);
	
	if(_ev.keyCode==27){
		interrumpirDibujo();
	}
	
}

function cargarTiposForm(){
	_sel=document.querySelector('#formEditarPublicaciones [name="id_p_ref_publ_tipos"]');
	_sel.innerHTML="<option value='0'>-sin datos-</option>";
	for(_nt in _TiposOrden){
		_idt=_TiposOrden[_nt];
		_nom=_Tipos[_idt].nombre;
		_op=document.createElement('option');
		_op.value=_idt;
		_op.innerHTML=_nom;
		_sel.appendChild(_op);
	}
}

function accionCargarNuevaPubl(){
	cargarTiposForm();
    consultarDepartamentos();
    accionIniciarEdicion();
    
    document.querySelector('#cuadrovalores').setAttribute('divSeleccionPubl','no');
    document.querySelector('#cuadrovalores').setAttribute('divSeleccionPublCuerpo','no');
    document.querySelector('#cuadrovalores').setAttribute('formEditarPublicaciones','no');

    document.getElementById('formEditarPublicaciones').setAttribute('idpubl','0');
    document.getElementById('botonAnadirPubl').style.display='none';
    document.getElementById('botonCancelar').style.display='inline-block';
}

function mostrarPubl(_idpubl){
	cargarTiposForm();
    limpiarFormularioSeleccionPubl();
    limpiarFormularioPubl();
    cargarDatosPubl(_idpubl);
    zoomPubl(_idpubl);
    
   		
    document.querySelector('#cuadrovalores').setAttribute('divSeleccionPubl','no');
    document.querySelector('#cuadrovalores').setAttribute('divSeleccionPublCuerpo','no');
    document.querySelector('#cuadrovalores').setAttribute('formEditarPublicaciones','no');  
     
    document.getElementById('botonAnadirPubl').style.display='none';
    document.getElementById('botonCancelar').style.display='inline-block';
    
    document.querySelector('#formEditarPublicaciones #pubTitulo').readOnly = true; 
	document.querySelector('#formEditarPublicaciones #publAutoria').readOnly = true; 
	document.querySelector('#formEditarPublicaciones #publUrl').readOnly = true; 
	document.querySelector('#formEditarPublicaciones #publAnio').readOnly = true; 
	document.querySelector('#formEditarPublicaciones #publMes').readOnly = true;
	document.querySelector('#formEditarPublicaciones #publAnio').type='text';
    document.querySelector('#formEditarPublicaciones #publMes').type='text';
    
    document.querySelector('#formEditarPublicaciones #publTipo').readOnly = true; 
    document.querySelector('#formEditarPublicaciones #publObservaciones').readOnly = true;
}

function accionVolverAlListado(){
	
	
	
	limpiarFormularioPubl();
	cargarAreaPubTodas();// en ./app_publ/app_publ_mapa_funciones.js
	zoomGeneral();
	document.getElementById('listapublicaciones').innerHTML='';
    consultarPublicaciones();
 
 
    document.querySelector('#cuadrovalores').setAttribute('divSeleccionPublCuerpo','si');
    document.querySelector('#cuadrovalores').setAttribute('divSeleccionPubl','si');
    

 
    document.getElementById('botonAnadirPubl').style.display='inline-block';
    document.getElementById('botonCancelar').style.display='none';
    
    document.querySelector('#cuadrovalores').setAttribute('formCargaPubl','no');
    document.querySelector('#cuadrovalores').setAttribute('formEditarPublicaciones','no');    
    
}

function accionIniciarEdicion(){
	
	consultarDepartamentos();
    document.getElementById('formEditarPublicaciones').setAttribute('modo','edicion');
    
    document.querySelector('#cuadrovalores').setAttribute('formEditarPublicaciones','si');  
    
    
    document.querySelector('#formEditarPublicaciones #pubTitulo').readOnly = false; 
	document.querySelector('#formEditarPublicaciones #publAutoria').readOnly = false; 
	document.querySelector('#formEditarPublicaciones #publUrl').readOnly = false; 
	document.querySelector('#formEditarPublicaciones #publAnio').readOnly = false;
	document.querySelector('#formEditarPublicaciones #publAnio').type='number';	 
	document.querySelector('#formEditarPublicaciones #publMes').readOnly = false; 	
    document.querySelector('#formEditarPublicaciones #publMes').type='number';
    
    document.querySelector('#formEditarPublicaciones #publTipo').readOnly = false; 
    document.querySelector('#formEditarPublicaciones #publObservaciones').readOnly = false;
	
	
}


function cancelarEditaPubl(){
	
    document.querySelector('#cuadrovalores').setAttribute('formEditarPublicaciones','no');  
	
}


/*
function accionCargarPublExist(){
    consultarPublicaciones();   
    document.getElementById('divSeleccionPubl').style.display='none';
    document.getElementById('botonCancelar').style.display='inline-block';
    //document.getElementById('botonElegirCapa').style.display='none';
    document.getElementById('botonAnadirPubl').style.display='block';
}
accionCargarPublExist();
*/

/*
function accionCancelarCargarNuevaPubl(_this){
	document.getElementById('listapublicacionespublicadas').innerHTML='';
    consultarPublicaciones();
    document.getElementById('divSeleccionPubl').style.display='none';
    document.getElementById('botonCancelarPubl').style.display='none';
    //document.getElementById('botonElegirCapa').style.display='none';
    document.getElementById('botonAnadirPubl').style.display='block';
    limpiarFormularioPubl();
}

function accionCancelarSeleccionPubl(_this){
    document.getElementById('divSeleccionPubl').style.display='none';
    document.getElementById('botonElegirPubl').style.display='block';
    document.getElementById('botonAnadirPubl').style.display='block';
    
    limpiarFormularioSeleccionPubl();
    limpiarFormularioPubl();
}
*/


function limpiarFormularioSeleccionPubl(){
    document.querySelector('#divSeleccionPublCuerpo #listapublicaciones').innerHTML='';
}


function limpiarFormularioPubl(){
	document.getElementById('formEditarPublicaciones').setAttribute('idpubl','0');
	document.querySelector('#gestorarchivos #publ_DOC').setAttribute('publ_DOC','0');
	document.getElementById('publ_DOC').value = '0';
    document.getElementById('pubTitulo').value = '';
    document.getElementById('publAutoria').value = '';
    document.getElementById('publUrl').value = '';
    document.getElementById('publAnio').value = '';
    document.getElementById('publMes').value = '';
    
	document.querySelector('#gestorarchivos #documento').setAttribute('ruta','');
	document.querySelector('#gestorarchivos #documento #txdoc').innerHTML='';
	
    document.getElementById('listadodepartamentos').innerHTML='';
    document.getElementById('publArea').value = '';    
    document.querySelector('#listadodepartamentosSelectos').innerHTML='';
}

function mostrarListadoPublicaciones(){
    document.querySelector('#divSeleccionPublicacionCuerpo #txningunapublicacion').style.display='none';
    document.getElementById('divSeleccionPublicacionCuerpo').style.display='block';
}

function limpiaBarra(_event){
	document.querySelector("#barrabusqueda input").value='';
	actualizarBusqueda(_event);
}


function descargarArchivo(_this){
	
	if(_this.getAttribute('ruta')==''){return;}
	
	window.open(_this.getAttribute('ruta'), '_blank');
	
}

function consultarUrl(_this){
	
	console.log(_this);
	console.log(document.querySelector('#formEditarPublicaciones').getAttribute('modo'));
	if(_this.value==''){return;}
	if(document.querySelector('#formEditarPublicaciones').getAttribute('modo')!='vista'){return;}
	window.open('http://'+_this.value, '_blank');
	
} 


function cargarDatosPubl(_idpubl){
	//console.log(_idpubl);
	_dat=_Data_Publ.publicaciones[_idpubl];
	
	cargarAreaPub(_idpubl); //en ./app_publ/app_publ_mapa_funciones.js
	
	document.querySelector('#cuadrovalores').setAttribute('formCargaPubl','si');
	
	document.querySelector('#formCargaPubl #tituloPubl').innerHTML=_dat.titulo;
	document.querySelector('#formCargaPubl #autoriaPubl').innerHTML=_dat.autoria;
	document.querySelector('#formCargaPubl #tipoPubl').innerHTML=_dat.titulo;
	document.querySelector('#formCargaPubl #obsPubl').innerHTML=_dat.observaciones;
	document.querySelector('#formCargaPubl #webPubl').innerHTML=_dat.url;
	document.querySelector('#formCargaPubl #fechaPubl').innerHTML=_dat.ano+' / '+_dat.mes;
	document.querySelector('#formCargaPubl #archivoPubl').innerHTML=_dat.doc_nombre;
	document.querySelector('#formCargaPubl #archivoPubl').title=_dat.doc_descripcion;
	document.querySelector('#formCargaPubl #archivoPubl').setAttribute('ruta',_dat.doc_archivo);
	
			
				
	 
	document.querySelector('#formEditarPublicaciones').setAttribute('idpubl',_idpubl);
	
	document.querySelector('#formEditarPublicaciones #pubTitulo').value=_dat.titulo;
	document.querySelector('#formEditarPublicaciones #publAutoria').value=_dat.autoria;
	document.querySelector('#formEditarPublicaciones #publUrl').value=_dat.url;
	document.querySelector('#formEditarPublicaciones #publObservaciones').value=_dat.observaciones;
	document.querySelector('#formEditarPublicaciones #publAnio').value=_dat.ano;
	document.querySelector('#formEditarPublicaciones #publMes').value=_dat.mes;
	document.querySelector('#formEditarPublicaciones #publArea').value=_dat.areatx;
	document.querySelector('#formEditarPublicaciones #publ_DOC').value=_dat.id_p_ref_01;
	document.querySelector('#gestorarchivos #documento').setAttribute('ruta',_dat.doc_archivo);
	document.querySelector('#gestorarchivos #documento #txdoc').innerHTML=_dat.doc_nombre;
	document.querySelector('#gestorarchivos #documento').title=_dat.doc_descripcion;
	
	document.querySelector('#listadodepartamentosSelectos').innerHTML='';
	for(_nm in _dat.municipios){
		
		_cod=_dat.municipios[_nm].ic_p_est_01_municipios;
		_nom=_dat.municipios[_nm].nombre;
		
		_div=document.createElement('div');
		document.querySelector('#listadodepartamentosSelectos').appendChild(_div);
		
		_aa=document.createElement('a');
		_aa.setAttribute('onclick','borrarMuni(this)');
		_aa.innerHTML='x';
		_div.appendChild(_aa);
		
		_div.innerHTML+=_nom;
		
		_inp=document.createElement('input');
		_inp.setAttribute('name','municipios[]');
		_inp.setAttribute('type','hidden');
		_inp.setAttribute('value',_cod);
		_div.appendChild(_inp);
	}
	
	
}







function accionBuscardpto(_event){
	
	console.log('tecla: '+_event.keyCode);
	_input=document.querySelector("input#publBuscarDepto");
	_str=_input.value;
	console.log('largo: '+_str.length);
	if(_str.length>=3){
		_input.setAttribute('estado','activo');
	}else{
		_str='';
		_input.setAttribute('estado','inactivo');
	}
	_str=_str.toLowerCase();
	//console.log('buscando: '+_str);
	
	_lis=document.querySelectorAll("#listadodepartamentos a");
	for(_ln in _lis){
		if(typeof _lis[_ln] != 'object'){continue;}
		
		_cont=_lis[_ln].innerHTML;
				
		_cont=_cont.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
		_str=_str.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
		
		if(_cont.toLowerCase().indexOf(_str)==-1){
			_lis[_ln].setAttribute('filtrado','si');
		}else{
			_lis[_ln].setAttribute('filtrado','no');
		}
	}
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
	
	_lis=document.querySelectorAll('#listapublicaciones > a');
	
	for(_ln in _lis){
		if(typeof _lis[_ln] != 'object'){continue;}
		
		_contId=_lis[_ln].getAttribute('idpubl');
		
		_dat=_Data_Publ.publicaciones[_contId];
		
		_cont =_dat.doc_nombre;
		_cont+=' '+_dat.titulo;
		_cont+=' '+_dat.doc_descricion;
				
		_cont=_cont.toLowerCase();
		
		_cont=_cont.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
		_str=_str.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
		
		if(_cont.toLowerCase().indexOf(_str)==-1){
			_lis[_ln].setAttribute('filtrob','nover');
		}else{
			_lis[_ln].setAttribute('filtrob','vera');
		}
	}
	
}




/////////////////////
///Funciones para controlar la selección de documentos del modulo DOCS
//////////////
var _Items=Array();
var _Docs=Array();
var _Orden=Array();


function consultarDocumentos(){
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco
    };
    $.ajax({
        url:   './app_docs/app_docs_consulta_externa.php',
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
            _Items=_res.data.psdir;
            _Orden=_res.data.orden;

			document.querySelector('#gestorarchivos #listadoDocumentos').innerHTML='';
            generarItemsHTML();		
            generarArchivosHTML();
            generarArchivoLinksHTML();
           
           	//finalizada la carga inicial de datos
           	
           	_idpseudocarpeta=getParameterByName('idpseudocarpeta');
           	if(_idpseudocarpeta != ''){
           		if(_idpseudocarpeta>0){
           			_caja=document.querySelector('#contenidoextenso .item[idit="'+_idpseudocarpeta+'"] > h3');
               		if(_caja==null){
               			alert('No hemos podido localizar la carpeta que está buscando. por favor verifique el link utilizado');
               			return;
               		}
               		editarI(_caja);
           		}
           	}
        }
    });	
}

function generarItemsHTML(){
    //genera un elemento html por cada instancia en el array _Items
    for(_nO in _Orden.psdir){
        _ni=_Orden.psdir[_nO];
        _dat=_Items[_ni];
        
        _clon=document.createElement('div');
        _clon.setAttribute('class','item');
        _clon.setAttribute('idit',_dat.id);
        _clon.setAttribute('nivel',"1");
        _clon.setAttribute('zz_sis',_dat.zz_sis);
        
        document.querySelector('#gestorarchivos #listadoDocumentos').appendChild(_clon);
        
        if(_dat.nombre==null){_dat.nombre='- caja sin nombre -';}
        _nom=document.createElement('h3');
        _clon.appendChild(_nom);
        _nom.innerHTML=_dat.nombre;
        _cont=document.createElement('div');
        _clon.appendChild(_cont);
        _contdir=document.createElement('div');
        _contdir.setAttribute('class','hijos');
        _clon.appendChild(_contdir);
        
        for(_na in _dat['archivos']){
                _dar=_dat['archivos'][_na];
                _Docs[_dar.id]=_dar;
                _aa=document.createElement('a');

                _aa.innerHTML=_dar.nombre;
                _aa.setAttribute('onclick','ElegirDoc(this)');
                _aa.setAttribute('idfi',_dar.id);
                _aa.setAttribute('class','archivo');
                _cont.appendChild(_aa);
        }       
    }

    //anida los itmes genereados unos dentro de otros
    for(_nO in _Orden.psdir){
        _ni=_Orden.psdir[_nO];
        _el=document.querySelector('#gestorarchivos #listadoDocumentos .item[idit="'+_Items[_ni].id+'"]');
		
        if(_Items[_ni].id_p_ref_02_pseudocarpetas!='0'){
            //alert(_Items[_ni].id_p_ESPitems_anidado);
            _dest=document.querySelector('#gestorarchivos #listadoDocumentos .item[idit="'+_Items[_ni].id_p_ref_02_pseudocarpetas+'"] > .hijos');
            _niv=_dest.parentNode.getAttribute('nivel');
            _niv++;
            _el.setAttribute('nivel',_niv);
            _dest.appendChild(_el);
        }
    }
}
  
function ElegirDoc(_this){
	document.querySelector('#gestorarchivos #documento #txdoc').innerHTML=_this.innerHTML;
	document.querySelector('#gestorarchivos #publ_DOC').innerHTML=_this.getAttribute('idfi');
	document.querySelector('#gestorarchivos #publ_DOC').value=_this.getAttribute('idfi');
	deactivarGestorarchivos()
} 
//////////////FIN



/////////////////////
///Funciones para controlar la selección de Municipios
//////////////

_Cod='';
function consultarDepartamentos(){	
	_lyrElemSrc.clear();
	//document.querySelector('#titulomapa').style.display='block';
	//document.querySelector('#menuelementos #lista').style.display='block';
	//console.log('hola');
	//document.querySelector('#menuelementos').style.display='block';
	//consultarElemento();//limpia datos ya consultados de elementos puntuales dentro de una tabla;
	
	
	mostrarTablaEnMapa('est_01_municipios');	
	consultarCentroides();
}



function seleccionMunicipio(_cod){
	console.log(_cod);
	_ref=document.querySelector('#listadodepartamentos > a[cod="'+_cod+'"] .nom');
	console.log(_ref);
	if(_ref==null){return;}
	
	_div=document.createElement('div');
	document.querySelector('#listadodepartamentosSelectos').appendChild(_div);
	
	_aa=document.createElement('a');
	_aa.setAttribute('onclick','borrarMuni(this)');
	_aa.innerHTML='x';
	_div.appendChild(_aa);
	
	
	_div.innerHTML+=_ref.innerHTML;
	
	_inp=document.createElement('input');
	_inp.setAttribute('name','municipios[]');
	_inp.setAttribute('type','hidden');
	_inp.setAttribute('value',_cod);
	_div.appendChild(_inp);
	
}

function borrarMuni(_this){
	
	_ref=_this.parentNode;
	_ref.parentNode.removeChild(_ref);
	
}

//////////////FIN








/////////////////////
//FUNciones para subir archivos
//////////////////



function activarUploader(){
	document.querySelector('#gestorarchivos #botonActivaUploader').style.display='none';
	document.querySelector('#gestorarchivos #uploader').style.display='block';
	document.querySelector('#gestorarchivos #botonDeactivaGestorarchivos').style.display='block';
	document.querySelector('#gestorarchivos #listadoDocumentos').style.display='none';
}

function activarSelectorDocs(){
	consultarDocumentos();
	document.querySelector('#gestorarchivos #botonDeactivaGestorarchivos').style.display='block';
	document.querySelector('#gestorarchivos #botonActivaUploader').style.display='none';
	document.querySelector('#gestorarchivos #botonActivaSelectorDocs').style.display='none';
	document.querySelector('#gestorarchivos #listadoDocumentos').style.display='block';
}

function deactivarGestorarchivos(){
	
	document.querySelector('#gestorarchivos #botonDeactivaGestorarchivos').style.display='none';
	document.querySelector('#gestorarchivos #uploader').style.display='none';
	document.querySelector('#gestorarchivos #botonActivaSelectorDocs').style.display='block';	
	document.querySelector('#gestorarchivos #botonActivaUploader').style.display='block';
	document.querySelector('#gestorarchivos #listadoDocumentos').style.display='none';
}

function resDrFile(){
       
        document.querySelector('#contenedorlienzo #upload').style.backgroundColor='rgba(0,155,255,0.8)';
        document.querySelector('#contenedorlienzo #upload label').style.color='#000';
}	

function desDrFile(){
		//console.log(_event);
		document.querySelector('#contenedorlienzo #upload').removeAttribute('style');
		document.querySelector('#contenedorlienzo #upload label').removeAttribute('style');    
}

var xhr=Array();
var inter=Array();
var _nFile=0;
function cargarCmp(_this){
	
	desDrFile();
        
    var _file = _this.files[0];
    _nFile++;
    
    var parametros = new FormData();
    parametros.append('upload',_file);
    parametros.append('nfile',_nFile);
    parametros.append('idMarco',_IdMarco);
    parametros.append('modo','pre_Publ');
    parametros.append('codMarco',_CodMarco);

	_spantxdoc=document.querySelector('#gestorarchivos #documento #txdoc');
	_spantxdoc.parentNode.removeChild(_spantxdoc);
	
    var _nombre=_file.name;
    _upF=document.createElement('a');
    _upF.setAttribute('nf',_nFile);
    _upF.setAttribute('id',"txdoc");
    _upF.setAttribute('size',Math.round(_file.size/1000));
    _upF.innerHTML=_file.name;
    document.querySelector('#gestorarchivos #documento').appendChild(_upF);

    _nn=_nFile;
    xhr[_nn] = new XMLHttpRequest();
    xhr[_nn].open('POST', './app_docs/app_docs_guardaarchivo.php', true);
    xhr[_nn].upload.li=_upF;
    xhr[_nn].upload.addEventListener("progress", updateProgress, false);

    xhr[_nn].onreadystatechange = function(evt){
        //console.log(evt);
        if(evt.explicitOriginalTarget.readyState==4){
            var _res = $.parseJSON(evt.explicitOriginalTarget.response);
            //alert('terminó '+_res.data.nf);
            if(_res.res=='exito'){							
                _file=document.querySelector('#gestoracrchivos >  #documento > a[nf="'+_res.data.nf+'"]');								
               	document.querySelector('#gestorarchivos > #publ_DOC').value=_res.data.nid;
               	deactivarGestorarchivos();
            } else {
                _file=document.querySelector('#gestorarchivos >  #documento > a[nf="'+_res.data.nf+'"]');
                _file.innerHTML+=' ERROR';
                _file.style.color='red';
            }
        }
    }
    xhr[_nn].send(parametros);		
}

 function updateProgress(evt) {
    if (evt.lengthComputable) {
        var percentComplete = 100 * evt.loaded / evt.total;		   
        this.li.style.width="calc("+Math.round(percentComplete)+"% - ("+Math.round(percentComplete)/100+" * 6px))";
    } else {
        // Unable to compute progress information since the total size is unknown
    } 
}


////////FIN///////////////////////////


