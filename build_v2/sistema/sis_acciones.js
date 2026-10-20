/**
* 
*     funciones de consulta general del sistema
*  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author	based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrolló sobre una publicación GNU 2017 TReCC SA
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

//Esta funcion obtiene el QueryString en base a su nombre de la url, es case insensitive

function consultarAcciones(){
	
    _parametros = {
        'id': _IdMarco,
        'cod': _CodMarco,
        'tabla':'est_02_marcoacademico'
       
    };

    if(typeof consultasPHP_nueva === 'function'){
        _cn = consultasPHP_nueva('./sistema/sis_consulta_acciones.php');
    }
    $.ajax({
        url:   './sistema/sis_consulta_acciones.php',
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
            _Acciones=_res.data;
            //let _Acc;
        	if(_Acc === undefined){return;}
			_ref=document.querySelector('#elemento[tipo="Accion"]');
			
			if(_ref!=undefined){
				
				_ref1=_ref.querySelector('#AccLogoHd');
				
				if(_ref1!=undefined){
					_ref1.setAttribute('src','./img/app_'+_Acc+'_hd.png');
					_ref1.setAttribute('alt','accion: '+_res.data.acciones['app_'+_Acc].descripcion);
				}
				
				_ref1=_ref.querySelector('#titulo');
				if(_ref1!=undefined){
					_ref1.innerHTML=_res.data.acciones['app_'+_Acc].resumen;
				}
				
				_ref1=_ref.querySelector('#descripcion');
				if(_ref1!=undefined){
					_ref1.innerHTML=_res.data.acciones['app_'+_Acc].descripcion;
				}		            				
			}
        	    
        }
    });	
}
consultarAcciones();
