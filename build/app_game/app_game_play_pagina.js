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
var _Partida = 'nueva';
var _Turno = '1';
var _Puntaje = '0';
var _PuntajeP = '0';
var _DataSesion={
	'turnos':1
};

function refrescarSesionActiva(){
    var idindicador = document.getElementById('sesionActivo').getAttribute('idsesion');
    cargarInfoIndicador(idindicador);
}

function refrescarDatosSesionActiva(_res){
	if(idSesion!=_res.data.idSesion){return;}
}

function cargarSesionPublicada(idSesion){
    cargarInfoSesion(idSesion);
}

/*
var _DataSesion;
function cargarInfoSesion(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'idSesion': _IdSesion
    };
    
    $.ajax({
            url:   './app_game/app_game_consultar_sesion.php',
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
                	_DataSesion=_res.data.sesion;
                	accionSesionPublicadaCargar(_DataSesion.id);
                    zoomArea();
                }else{
                    alert('error asf0jg44f8f0gh');
                }
            }
    });
}
cargarInfoSesion();
*/

function accionSesionPublicadaCargar(_idsesion){	
	presentar();	
}

function presentar(){
	
	_pres=document.querySelector('#presentacion');
	_pres.style.width='0vw';
	_pres.style.height='0vh';
	_pres.style.opacity='0';
	
	_pres.style.display='block'
	_pres.innerHTML='';
	_pres.innerHTML+='<p>Bienvenides a:</p>';
	_pres.innerHTML+='<h1>'+_DataSesion.nombre+'</h1>';
	_pres.innerHTML+='<p>'+_DataSesion.presentacionBR+'</p>';
	_pres.innerHTML+='<p><span class="variable">Turnos disponibles	:</span><span class="valor">'+_DataSesion.turnos+'</p>';
	_pres.innerHTML+='<p><span class="variable">Obra a ejecutar por turno:</span><span class="valor">'+_DataSesion.limiteunitarioporturno+' <span id="unidad">m</span></p>';
	_pres.innerHTML+='<p><span class="variable">Costo de ejecución por <span id="unidad">metro</span>:</span><span class="valor">$ '+_DataSesion.costounitario+'</p>';
		
	_pres.innerHTML+='<a id="botonpresentok" onclick="cerrarPres()">start</a>';
	
	_pres.style.width='60vw';
	_pres.style.height='auto';
	_pres.style.opacity='100';
	
}

function cerrarPres(){
	document.querySelector('#presentacion').style.display='none';
	document.querySelector('#pasar').setAttribute('estado','activo');
	
}

function avanzarTurno(){
	
	_Turno++;
	if(_Turno<=_DataSesion.turnos){
		document.querySelector('#nturn').innerHTML=_Turno;
		document.querySelector('#nturndif').innerHTML=parseInt(_DataSesion.turnos)-parseInt(_Turno);
		document.querySelector('#puntajeactual').innerHTML=_Puntaje;
		document.querySelector('#puntajePactual').innerHTML=_PuntajeP;		
		accionEditarCrearGeometria();
	}else{
		gameOver();
	}
	
}

function gameOver(){
	
	document.querySelector('#pasar').setAttribute('estado','inactivo');
	
	
	_pres=document.querySelector('#gameover');
	_pres.style.width='0vw';
	_pres.style.height='0vh';
	_pres.style.opacity='0';
	
	_pres.style.display='block'
	_pres.innerHTML='';
	_pres.innerHTML+='<h1>GAME OVER</h1>';
	_pres.innerHTML+='<p><span class="variable">a logrado :</span><span class="valor">'+_Puntaje+' puntos</p>';
	_pres.innerHTML+='<p><span class="variable">Representa :</span><span class="valor">'+_PuntajeP+' % de cobertura</p>';
	
	_pres.innerHTML+='<p><span class="variable">Inscriba sus iniciales: </span><input onkeyup="mayusculizar(this);completar(this,event)" name="iniciales" maxlength="3"></p>';
			
	_pres.innerHTML+='<a onclick="cerrarPartida()">guardar record</a>';
	
	_pres.style.width='60vw';
	_pres.style.height='auto';
	_pres.style.opacity='100';
		
	_pres.querySelector('input').focus();
}

