/**
*
* funciones js para ejecutar consultas desde index
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


var _ConsultaActiva='no';

function controlConsultando(){
	if(_ConsultaActiva=='si'){
		return true;
	}else{
		return false;
	}	
}

function activarConsultando(){
	_ConsultaActiva='si';	
}

function desactivarConsultando(){
	_ConsultaActiva='no';	
}


function consultarElementoAcciones(_idElem,_codElem,_tabla){

	_parametros = {
		'id': _idElem,
		'cod': _codElem,
		'tabla':_tabla
	};
		
	if(controlConsultando()){alert('Antes tiene que resolverse una consulta en curso');return;}
	activarConsultando();
	
	if(typeof consultasPHP_nueva === 'function'){
	_cn = consultasPHP_nueva('./consulta_elemento.php');
	}
	$.ajax({
		url:   './consulta_elemento.php',
		type:  'post',
		data: _parametros,
		beforeSend: function(request, settings) { 
			request._data = {'cn':_cn};
		},
        error:  function (request, status, errorThrown){	
			_cn = request._data.cn;
			if(typeof consultasPHP_respuesta === 'function'){ 
				consultasPHP_respuesta("err",_cn);}	
		},
		success:  function (response, status, request){			
			var _res = $.parseJSON(response);            
            _cn = request._data.cn;	
			if(typeof consultasPHP_respuesta === 'function'){ 
	            consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);}
            if(_res.res!='exito'){return;}
            	
			_campocod=_res.data.tablasConf.campo_id_geo;
			_camponom=_res.data.tablasConf.campo_id_humano;
			_campodesc=_res.data.tablasConf.campo_desc_humano;	
							
			_lista=document.querySelector('#menuacciones #lista');
				
			for(_accnom in _res.data.tablasConf.acciones){
				
				_accndata=_res.data.tablasConf.acciones[_accnom];
				
				if(_res.data.elemento.accesoAccion[_accnom]>0){
				
					document.querySelector('#menuacciones').style.display='block';
					_li=document.createElement('a');
					_li.setAttribute('href','./'+_accnom+'.php?cod='+_res.data.elemento[_campocod]);
					_li.setAttribute('activa',_accndata.activo);
					_la=document.createElement('img');
					_la.setAttribute('src','./img/'+_accnom+'.png');
					_la.setAttribute('alt',_accnom);
					_la.setAttribute('title',_accndata.resumen);
					_li.appendChild(_la);
					_lista.appendChild(_li);
					
				}
			}				
		}
	})	
}
