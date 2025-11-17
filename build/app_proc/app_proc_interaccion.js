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





function toogle(_this,_att){
	
	_n=parseInt(_this.getAttribute(_att));
	if(isNaN(_n)){_n=-1;}
	_this.setAttribute(_att,(_n*(-1)));
	
}







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
		document.querySelector('#carga #vinculaciones').style.display='block';
	}else{
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
