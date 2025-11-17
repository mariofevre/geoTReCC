/**
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


function cargaBase(){
    _parametros = {
            'idMarco': _IdMarco,
            'codMarco': _CodMarco
    };

    _cn = consultasPHP_nueva('./app_cart/app_cart_consulta.php');
    $.ajax({
            url:   './app_cart/app_cart_consulta.php',
            type:  'post',
            data: _parametros,
            beforeSend: function(request, settings) { 
                request._data = {'cn':_cn};
            },
            error:  function (request, status, errorThrown){	
                _cn = request._data.cn;
                consultasPHP_respuesta("err",_cn);	
            },
            success:  function (response, status, request){			
                var _res = $.parseJSON(response);            
                _cn = request._data.cn;			
                consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
                if(_res.res!='exito'){return;}
                for(_nd in _res.data.documentosOrden){
                _iddoc=_res.data.documentosOrden[_nd];
                _docdata=_res.data.documentos[_iddoc];
                if(_docdata==undefined){continue;}
                
                _sp=_docdata.archivo.split('.');
                _ext = _sp[(_sp.length - 1)].toLowerCase();
                _cont=document.querySelector('#portamapa');
                    if(
                            _ext=='jpg'
                            ||
                            _ext=='jpeg'
                            ||
                            _ext=='gif'
                            ||												
                            _ext=='png'
                            ||
                            _ext=='tif'
                            ||
                            _ext=='bmp'
                    ){
                        _img=document.createElement('img');
                        _img.setAttribute('src',_docdata.archivo);
                        _cont.appendChild(_img);
                        
                    }else{											
                        _descarga=document.createElement('a');
                        _descarga.setAttribute('href',_docdata.archivo);
                        
                        if(_docdata.descripcion!=null){
                            _descarga.innerHTML=_docdata.descripcion;
                        }else{
                            _descarga.innerHTML=_docdata.nombre;
                        }
                        _cont.appendChild(_descarga);
                    }   
                }      
            }
    });
}

cargaBase('cargainicial');