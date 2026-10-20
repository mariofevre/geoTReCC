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

var _IdMarco = getParameterByName('id');
var _CodMarco = getParameterByName('cod');
var _IdSesion = getParameterByName('idsesion');  


function cargarScores(){
    
    var parametros = {
        'codMarco': _CodMarco,
        'idMarco': _IdMarco,
        'idSesion': _IdSesion
    };
    
    $.ajax({
            url:   './app_game/app_game_consultar_scores.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                
                for(var _nm in _res.mg){
	                alert(_res.mg[_nm]);
	            }
	            for(var _na in _res.acc){
	                procesarAcc(_res.acc[_na]);
	            }
                //console.log(_res);
                if(_res.res=='exito'){
                	
                	for(_nord in _res.data.scores){
                		_dat=_res.data.scores[_nord];
                		
                		_fila=document.createElement('div');
                		_fila.setAttribute('class','fila');
                		_fila.setAttribute('orden',_nord);
                		
                		if(_Partida == _dat.id){
                			_fila.setAttribute('selecto','selecto');
                			_fila.setAttribute('id','selecto');
                		}
                		
                		_val=document.createElement('div');
                		_val.setAttribute('class','orden');
                		_val.innerHTML=_nord;
                		if(_nord=='1'){_val.innerHTML+='st';}
                		if(_nord=='2'){_val.innerHTML+='nd';}
                		if(_nord=='3'){_val.innerHTML+='rd';}
                		_fila.appendChild(_val);
                		
                		_val=document.createElement('div');
                		_val.setAttribute('class','iniciales');
                		_val.innerHTML=_dat.iniciales;
                		
                		if(_dat.iniciales==''){
                			_val.innerHTML='<span class="anonimo">-desconocide-</span>';
                		}
                		
                		_fila.appendChild(_val);
                		
                		_val=document.createElement('div');
                		_val.setAttribute('class','puntaje');
                		_val.innerHTML=Math.round(_dat.puntaje);
                		_fila.appendChild(_val);
                		
                		_val=document.createElement('div');
                		_val.setAttribute('class','puntajePorc');
                		_val.innerHTML=(Math.round(_dat.puntaje_porc*100)/100)+'%';
                		_fila.appendChild(_val);
                		
                		document.querySelector('.portamapa').appendChild(_fila);
                	}
                	
                	 if(_Partida > 0){
				    	_altoventana=window.innerHeight;
				    	console.log($("#selecto"));
				    	$([document.documentElement, document.body]).animate({
					        scrollTop: $("#selecto").offset().top - (_altoventana/2)
					    }, 2000);
				    }
                }else{
                    alert('error asf0jg44f8f0gh');
                }
            }
    });
    
   
}
cargarScores();
