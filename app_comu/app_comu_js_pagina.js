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

function formatNumberTwoDigits(number) {
     return (number < 10 ? '0' : '') + number;
}

function obtenerNombreMes(mesNumero){
    var month = new Array();
    month[0] = "Enero";
    month[1] = "Febrero";
    month[2] = "Marzo";
    month[3] = "Abril";
    month[4] = "Mayo";
    month[5] = "Junio";
    month[6] = "Julio";
    month[7] = "Agosto";
    month[8] = "Septiembre";
    month[9] = "Octubre";
    month[10] = "Noviembre";
    month[11] = "Diciembre";

    return month[mesNumero];
}


function toglevalorSiNo(_this){
	_nom=_this.getAttribute('for');	
	if(_this.checked==true){
		_val=_this.getAttribute('valorsi');
	}else{
		_val=_this.getAttribute('valorno');
	}
	_this.parentNode.querySelector("[name='"+_nom+"']").value=_val;
}

function toglevalorSiNoRev(_this){
	_for=_this.getAttribute('name');
	_val=_this.value;
	_inp=_this.parentNode.querySelector('[for="'+_for+'"]');
	if(_inp.getAttribute('valorsi')==_val){
		_inp.checked=true;
	}else{
		_inp.checked=false;
	}
}

function limpiaBarra(_event){
	document.querySelector("#barrabusqueda input").value='';
	actualizarBusqueda(_event);
}

function toogleCheck(_this){	
	
	if(_this.getAttribute('type')=='hidden'){
		_name=_this.getAttribute('name');
		console.log('input[type="hidden"][para="'+_name+'"]');
		_inputs=document.querySelector('input[type="hidden"][para="'+_name+'"]');
		_inputh=this;		
	}else{
		_name=_this.getAttribute('para');
		_inputs=this;
		console.log('input[type="hidden"][name="'+_name+'"]');
		_inputh=document.querySelector('input[type="hidden"][name="'+_name+'"]');
	}
	if(_inputh.value==0){_inputh.value=-1;}
	_inputh.value=parseInt(_inputh.value)*(-1);
	
	if(_inputh.value=='1'){_inputs.checked=true;}
	if(_inputh.value=='-1'){_inputs.checked=false;}	
}

function actualizarUrl(){
	
	_center=_view.getCenter();
	_z=_view.getZoom();
	_url = new URL(window.location);
	_url.searchParams.set('idr', _idCampa);

	window.history.pushState(null, '', _url.toString());

	
}

function actualizaAccionMapa (){
	_AccMapa=document.querySelector('#selector_acciones input:checked').value;
	console.log(document.querySelector('#selector_acciones input:checked'));
	if(_AccMapa=='undefined'){
		_AccMapa='selecciona';
	}
}

function cambiaModoDato(_radiob){
	if(_radiob.checked==false){return;}
	_radiob.parentNode.parentNode.parentNode.setAttribute('modo',_radiob.value);
	
}
