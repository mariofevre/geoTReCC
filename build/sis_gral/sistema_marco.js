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
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var results = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)", 'i').exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

var _IdMarco = getParameterByName('id');
var _CodMarco = getParameterByName('cod');
var _DataMarco = Array();

if(_Tabla == undefined){
	var _Tabla=getParameterByName('est');
}

function consultarMarco(){
	
	if(_Tabla == undefined){_Tabla='est_02_marcoacademico';}
	if(_Tabla==''){_Tabla='est_02_marcoacademico';}
    _parametros = {
        'id': _IdMarco,
        'cod': _CodMarco,
        'tabla':_Tabla
    };

    $.ajax({
        data: _parametros,
        url:   './consulta_elemento.php',
        type:  'post',
        success:  function (response) {
            var _res = $.parseJSON(response);
           
            for(var _nm in _res.mg) {
               alert(_res.mg[_nm]);
            }

            if(_res.res=='exito') {	
            	_DataMarco=	_res.data;
            	
            	//console.log('PPPP');
            	if(typeof cargarListaSitios === "function"){
				   	cargarListaSitios();//en ./comun_mapa_localiz.js
            	}
            	_s=document.querySelector('style#pers_marco');
            	if(
					_s != null
					&&
					_DataMarco.personalizacion!=undefined
				){							
							
					_s.innerHTML='a, select, input[type="button"], input{';
				
					console.log(_DataMarco.personalizacion.css_color_botones);
					if(_DataMarco.personalizacion.css_color_botones != ''){
						_s.innerHTML+='color:'+_DataMarco.personalizacion.css_color_botones+';';			
					}
					if(_DataMarco.personalizacion.css_border_botones != ''){
						_s.innerHTML+='borde:'+_DataMarco.personalizacion.css_border_botones+';';			
					}
				
					_s.innerHTML+='}';			
				}
		    	
				document.querySelector('body').setAttribute('display_autonomo',_res.data.elemento.zz_display_autonomo);
				
            	
            	if(document.querySelector('#mapa')!=undefined){
            		cargarCapaMarco();
            	}
                document.querySelector('#menutablas #titulo').innerHTML=_res.data.elemento.nombre_oficial;
                document.querySelector('#menutablas #descripcion').innerHTML=_res.data.elemento.nombre;
                //generarItemsHTML();		
                //generarArchivosHTML();
                if (typeof cargarFormMarco !== "undefined") { 
				    cargarFormMarco();
				}
				
                
            } else {
                alert('error dsfg');
            }
        }
    });	
}
consultarMarco();

function procesarAcc(_acc){
	
	if(_acc=='login'){
		if(_DataMarco.elemento.zz_accesolibre=='1'){return;}
		alert('su sesión ha caducado. Por favor vuela a loguearse.');
		window.location.assign('./index.php?est=est_02_marcoacademico&cod='+_CodMarco);
	}else{
		alert('acción desconocida');
	}	
}


function formatearNumero(_numero,_dec){
    if (!_numero || _numero == 'NaN') return '-';
    if (_numero == 'Infinity') return '&#x221e;';
    _numero = _numero.toString().replace(/\$|\,/g, '');
    if (isNaN(_numero))
    	_numero = "0";
    sign = (_numero == (_numero = Math.abs(_numero)));
    _numero = Math.floor(_numero * 100 + 0.50000000001);
    cents = _numero % 100;
    _numero = Math.floor(_numero / 100).toString();
    
    if(_dec==0){
    	for (var i = 0; i < Math.floor((_numero.length - (1 + i)) / 3) ; i++)
	        _numero = _numero.substring(0, _numero.length - (4 * i + 3)) + '.' + _numero.substring(_numero.length - (4 * i + 3));
	   return (((sign) ? '' : '-') + _numero);	   	
    }else if(_dec==1){
	    for (var i = 0; i < Math.floor((_numero.length - (1 + i)) / 3) ; i++)
	        _numero = _numero.substring(0, _numero.length - (4 * i + 3)) + '.' + _numero.substring(_numero.length - (4 * i + 3));
	    return (((sign) ? '' : '-') + _numero + ',' + cents);
    }else{
    	 if (cents < 10)
	        cents = "0" + cents;
	    for (var i = 0; i < Math.floor((_numero.length - (1 + i)) / 3) ; i++)
	        _numero = _numero.substring(0, _numero.length - (4 * i + 3)) + '.' + _numero.substring(_numero.length - (4 * i + 3));
	    return (((sign) ? '' : '-') + _numero + ',' + cents);	
    }
    

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
