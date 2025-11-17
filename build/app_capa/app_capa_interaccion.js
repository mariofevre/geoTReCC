/**
*
* funciones js para incorporar interactividad a app_capa
 * 
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
* 
*
*/



function elijeCapaLink(_idcapal){
	document.querySelector('#vinculaciones input[name="link_capa"]').value=_idcapal;
	document.querySelector('#vinculaciones #muestra_link_capa').innerHTML=_Linkeables[_idcapal].nombre;
	document.querySelector('#vinculaciones #muestra_link_capa').title=_Linkeables[_idcapal].descripcion;
	
	document.querySelector('#formlinkcapa #lista').innerHTML='';
	document.querySelector('#formlinkcapa').style.display='none';	
}


function elijeCampoExternoLink(_campo){
	
	_idcapal=document.querySelector('#vinculaciones input[name="link_capa"]').value;
	
	
	
	
	if(_idcapal=='-1'){ // tratamiento diferente si se trta de la capa estructural de municipios.
		if(_campo=='nom_col_text1'){
			document.querySelector('#vinculaciones input[name="link_capa_campo_externo"]').value=_Linkeables[_idcapal][_campo];
			document.querySelector('#vinculaciones #muestra_link_capa_campo_externo').innerHTML=_Linkeables[_idcapal][_campo];
		}else{
				alert('error en los datos cargados');
		}
	}else{
	
		document.querySelector('#vinculaciones input[name="link_capa_campo_externo"]').value=_campo;
		document.querySelector('#vinculaciones #muestra_link_capa_campo_externo').innerHTML=_Linkeables[_idcapal][_campo];
		
	}
	document.querySelector('#formlinkcampoexterno #lista').innerHTML='';
	document.querySelector('#formlinkcampoexterno').style.display='none';	
}



function elijeCampoLocalLink(_campo){		
	document.querySelector('#vinculaciones input[name="link_capa_campo_local"]').value=_campo;
	document.querySelector('#vinculaciones #muestra_link_capa_campo_local').innerHTML=_Capa[_campo];

	document.querySelector('#formlinkcampolocal #lista').innerHTML='';
	document.querySelector('#formlinkcampolocal').style.display='none';	
}



function cerrarFormLink(_this){
_this.parentNode.querySelector('#lista').innerHTML='';	
_this.parentNode.style.display='none';
}

function actualizarOpcionesGeometria(){
	_tg=document.querySelector('#carga select[name="tipogeometria"]').value;
	
	if(_tg=='Tabla'){
		document.querySelector('#archivosacargar').setAttribute('tipo','tabla');
		document.querySelector('#carga #vinculaciones').style.display='block';
	}else{		
		document.querySelector('#archivosacargar').setAttribute('tipo','geometria');
		document.querySelector('#carga #vinculaciones').style.display='none';
	}
	
	if(
		_tg=='Polygon'
		||
		_tg=='LineString'
		||
		_tg=='Point'
		||
		_tg=='Tabla'
		
		){
		
		document.querySelector('#cargarGeometrias').setAttribute('definiogeom','si');
		document.querySelector('#cargarGeometrias').setAttribute('pedirproj','si');
		if(_tg=='Tabla'){
			document.querySelector('#cargarGeometrias').setAttribute('pedirproj','no');
		}
	}else{
		document.querySelector('#cargarGeometrias').setAttribute('definiogeom','no');
	}
}

function limpiarFormCapa(){
	document.querySelector('#capaNombre').value='';
	document.querySelector('#cantreg #contador').innerHTML='0';
	
	document.querySelector('#capaDescripcion').value='';
	
	document.querySelector('#tipo_fuente [name="modo_publica"]').value='';
	document.querySelector('#tipo_fuente [name="tipo_fuente"]').value='';
	document.querySelector('#tipo_fuente [name="tipogeometria"]').value='';
	document.querySelector('#tipo_fuente [name="link_capa"]').value='';
	document.querySelector('#tipo_fuente #muestra_link_capa').innerHTML='';
	
	document.querySelector('#tipo_fuente [name="link_capa_campo_externo"]').value='';
	document.querySelector('#tipo_fuente #muestra_link_capa_campo_externo').innerHTML='';
	
	document.querySelector('#tipo_fuente [name="link_capa_campo_local"]').value='';
	document.querySelector('#tipo_fuente #muestra_link_capa_campo_local').innerHTML='';
	
	document.querySelector('#fechas [name="fecha_ano"]').value='';
	document.querySelector('#fechas [name="fecha_mes"]').value='';
	document.querySelector('#fechas [name="fecha_dia"]').value='';
	
	
	document.querySelector('#cargarGeometrias').setAttribute('definiogeom','no');
	document.querySelector('#cargarGeometrias').setAttribute('abiertaedicion','si');
	
	//document.querySelector('#cargarGeometrias #archivosacargar').innerHTML='';
	document.querySelector('#cargarGeometrias #archivoscargados').innerHTML='';
	
	document.querySelector('#cargarGeometrias #ecamposdelosarchivos').setAttribute('archivocargado','no');
}


function eliminarReglaSLD(_this){
	_divrule=_this.parentNode.parentNode;
	_divrule.parentNode.removeChild(_divrule);
}


function actualizaTiporecorte(){
	_val=document.querySelector('#form_importar_capa_pub #config_importar [name="recorte"]:checked').value;
	document.querySelector('#form_importar_capa_pub #config_importar').setAttribute('modo_recorte',_val);
	actualizaFormPerfil();
}


function formularDepartamentos(){
	_seld=document.querySelector('#opciones_rec_dto [name="id_depto_recorte"]');
	_selp=document.querySelector('#opciones_rec_dto [name="provincia"]');
	if(_selp.value==''){
		_seld.innerHTML='<option value="" selected="">Elegir primero una provincia</option>';
		return;
	}
	_seld.innerHTML='<option value="" selected="">Departamento</option>';
	_idp=_selp.value;
	for(_nd in _DataProvincias.provincias[_idp].departamentosOrden){
		_idd=_DataProvincias.provincias[_idp].departamentosOrden[_nd];
		_datd=_DataProvincias.provincias[_idp].departamentos[_idd];
		_op=document.createElement('option');
		_op.value=_datd.link;
		_op.innerHTML=_datd.nombre;
		_seld.appendChild(_op);
	}
}


function actualizaFormPerfil(){

	_form=document.querySelector('#form_importar_capa_pub');
	_form.setAttribute('completo','si');
	
	
	
	_capa=document.querySelector('#form_importar_capa_pub #seleccion').getAttribute('capa_elegida');
	
	if(_capa!='si'){
		document.querySelector('#form_importar_capa_pub').setAttribute('completo','no');		
		console.log('sin capa definida');
	}
	
	
	if(document.querySelector('#config_importar').getAttribute('modo_recorte')=='departamento'){
		if(_form.querySelector('[name="provincia"]').value==''){
			document.querySelector('#form_importar_capa_pub').setAttribute('completo','no');
				console.log('sin provincia definido');
		}
		
		if(_form.querySelector('[name="id_depto_recorte"]').value==''||_form.querySelector('[name="id_depto_recorte"]').value=='__falta_provincia__'){
	
			document.querySelector('#form_importar_capa_pub').setAttribute('completo','no');
			console.log('sin departamento definido');
		}
	}else if(document.querySelector('#config_importar').getAttribute('modo_recorte')=='capa'){
				document.querySelector('#form_importar_capa_pub').setAttribute('completo','no');
			console.log('fucion de recorte por capa indefinida');
	}	
}


function elegirImportarCapaPublica(_idcapa){	
	document.querySelector('#form_importar_capa_pub #seleccion [name="id"]').value=_idcapa;
	_sels=document.querySelectorAll('#form_importar_capa_pub #lista_capas a[elegida="si"]');
	for(_ns in _sels){
		if(typeof _sels[_ns] != 'object'){continue;}
		_sels[_ns].removeAttribute('elegida');
	}
	_asel=document.querySelector('#form_importar_capa_pub #lista_capas a[idcapa="'+_idcapa+'"]');
	_asel.setAttribute('elegida','si');
	document.querySelector('#form_importar_capa_pub #seleccion').setAttribute('capa_elegida','si');
	actualizaFormPerfil()
}


function limpiarSeleccionCapaImporta(){
	document.querySelector('#form_importar_capa_pub #seleccion').removeAttribute('capa_elegida');	
	
	
	
}

function limpiarSeleccionCapaImporta(){
	_sels=document.querySelectorAll('#form_importar_capa_pub #lista_capas a[elegida="si"]');
	for(_ns in _sels){
		if(typeof _sels[_ns] != 'object'){continue;}
		_sels[_ns].removeAttribute('elegida');
	}
	document.querySelector('#form_importar_capa_pub #seleccion').removeAttribute('capa_elegida');	
	document.querySelector('#form_importar_capa_pub #seleccion [name="id"]').value='';
	document.querySelector('#form_importar_capa_pub').setAttribute('completo','no');
}


function formularSimbologia(){
	
	_idsimbologia=document.querySelector('#simbologia_carga').value;
	if(_idsimbologia<0){alert('error, no tiene cargada una simbología habilitada para renombrar');return;}
	
	document.querySelector('#form_simbologia').setAttribute('activo','si');
	document.querySelector('#form_simbologia [name="id"]').value=_idsimbologia;
	document.querySelector('#form_simbologia [name="nombre"]').value='';
	
}
