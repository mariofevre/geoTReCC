/**

*
* aplicación de gestion de carga de archivos al servidor para index.php
 * 
 *  
* @package    	geoTReCC
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar>
* @author    	http://www.trecc.com.ar
* @author		based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
* @copyright	2024 TReCC SA
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2018 - Universidad de Buenos Aires
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2017 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (GPL-3.0)
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
* 
*
*/
//funciones para la gestión de archivos uploads
var _contUp=0;
var _Cargas={};

function enviarSHP(_event,_this){	
	ValidarProcesarBoton();
	var files = _this.files;		
	for (i = 0; i < files.length; i++) {
		
    	_contUp++;
    	_Cargas[_contUp]='subiendo';
		var parametros = new FormData();
		parametros.append("upload",files[i]);
		parametros.append("idver",document.querySelector('#formcargaverest').getAttribute('idver'));
		parametros.append("crs",_this.parentNode.parentNode.querySelector('#crs').value);
		parametros.append("cont",_contUp);
		
		cargando(files[i].name,_contUp);
		
		//Llamamos a los puntos de la actividad
		$.ajax({
				data:  parametros,
				url:   './app_index/index_shp_upload.php',
				type:  'post',
				processData: false, 
				contentType: false,
				success:  function (response) {
					var _res = $.parseJSON(response);
					for(_nm in _res.mg){alert(_res.mg[_nm]);}
					if(_res.res=='exito'){
						archivoSubido(_res);
					}else{
						archivoFallido(_res)
					}
					
					_Cargas[_res.data.ncont]='terminado';
					
					_pendientes=0;
					for(_nn in _Cargas){
						if(_Cargas[_nn]=='subiendo'){_pendientes++;}
					}
					if(_pendientes==0){
						alert(document.querySelector('#botonformversion').innerHTML);
						formversion(document.querySelector('#botonformversion'));
						alert('ok');
					}
				}
		});
	}
}

function cargando(_nombre,_con){
	
	_ppp=document.createElement('p');
	_ppp.innerHTML=_nombre;
	_ppp.setAttribute('ncont',_con);
	_ppp.setAttribute('class','carga');
	
	document.querySelector('#shp #cargando').appendChild(_ppp);
	
}	
	
function archivoSubido(_res){
	
	document.querySelector('#shp #cargando p[ncont="'+_res.data.ncont+'"]').innerHTML+=' ...subido';
	document.querySelector('#shp #cargando p[ncont="'+_res.data.ncont+'"]').setAttribute('estado','subido');
	
}	

function archivoFallido(_res){
		
	document.querySelector('#shp #cargando p[ncont="'+_res.data.ncont+'"]').innerHTML+=' ...fallido';
	document.querySelector('#shp #cargando p[ncont="'+_res.data.ncont+'"]').setAttribute('estado','fallido');
	
}	
		
