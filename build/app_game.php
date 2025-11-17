<?php
/**
* generación de juegos. gamificación de indicadores
 * 
 * 
 ***  
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

ini_set('display_errors', 1);
include_once("./_config_acc/claveunica.php");
include_once("./_config_inst/config.php");

// verificación de seguridad 
if(!isset($_SESSION)) {
	session_start(); 
	if(!isset($_SESSION["geogec"]["usuario"]['id'])){
		$_SESSION["geogec"]["usuario"]['id']='-1';
	}
}

// funciones frecuentes
include("./comun_general/fechas.php");
include("./comun_general/cadenas.php");

$COD = isset($_GET['cod'])?$_GET['cod'] : '';
$ID = isset($_GET['id'])?$_GET['id'] : '';
if($ID==''&&$COD==''){
	header('location: ./index.php');
}

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

?><!DOCTYPE html>
<head>
    <title><?php echo $CONF['plataforma']['nombre'];?></title>
    <?php include("./comun_general/meta.php");?>
    <link href="./css/mapauba.css" rel="stylesheet" type="text/css">
    <link rel="manifest" href="pantallahorizontal.json">
    
    <link href="./comun_css/general.css" rel="stylesheet" type="text/css">
    <link href="./comun_css/general_app.css" rel="stylesheet" type="text/css">
    <link href="./app_game/app_game.css" rel="stylesheet" type="text/css">
    
    <style>
	    #botonCrearGeom{
	    	display:none;
	    }
	    #formcalculo{
	    	display:none;
	    }
	     #botonDuplicarGeom{
	    	display:none;
	    }
	    #periodo > #selectorPeriodo div #valor{
	    	height:15px;
	    }
	     #periodo > #selectorPeriodo{
	    	vertical-align:top;
	    }
	    #periodo > #selectorPeriodo div{
	    	vertical-align: top;
	    	text-align:center;
	    }
	    #tipodeometriaNuevaGeometria{
	    	display:none;
	    }
	    
	    .unidad input.renombra {
			border:none;
			width:200px;
			font-size:11px;
			font-weight:bold;
			color:#000;
			cursor:pointer;
		}
		
		.unidad input.renombra[editando='si'] {
			color:red;
			background-color:pink;
		}
		
		
    </style>
</head>

<body>   
<script type="text/javascript" src="./terceras_js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./terceras_js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./terceras_js/ol4.2/ol-debug.js"></script>

<div estado="cerrado" id="cartel_consultando">
	<a id="cerrar" onclick="cerrarCartelConsultando();">cerrar</a>
	<img id="cargando" src="./comun_img/cargando.gif">
	<img id="error" src="./comun_img/error.png">
	<div id="mensajes"> </div>	
	<div id="consultas"> </div>
</div>

<div id="pageborde">
    <div id="page">
        <div id='encabezado'>
		<a href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
                <h2><?php echo $CONF['plataforma']['nombre'];?></h2>
                <p><?php echo $CONF['plataforma']['descrip'];?></p>
            </a>

            <div id='elemento' tipo='Accion'>
                <img src='./img/app_game_hd.png' style='float:left;'>
                <h2 id='titulo'></h2>
                <div id='descripcion'>
                	           	
            	</div>
            </div>	
        </div>
        <div id='menutablas'>
            <h1 id='titulo'>- nombre de proyecto -</h1>
            <p id='descripcion'>- descripcion de proyecto -</p>
        	<div id='menuacciones'>
				<div id='lista'></div>	
			</div>
        </div>	

        <div id="portamapa">
            <div id='titulomapa'>
                <p id='tnombre'></p>
                <h1 id='tnombre_humano'></h1>
                <p id='tdescripcion'></p>
                <b><p id='tseleccion'></p></b>
            </div>
            <div id='mapa' class="mapa"></div>
            <div id='auxiliar' mostrando=''><div id='cont'></div></div>
            <div id='tarjetas' mostrando=''></div>
            <div id="wrapper">
                <div id="location"></div>
                <div id="scale"></div>
            </div>
            
            <div id='botonera_mapa'></div>
        </div>
        
        <div id="cuadrovalores">
            <div id='sesionActiva' idindicador='0' class="elementoOculto"></div>
            <div class='capaEncabezadoCuadro tituloCuerpo'>
                <h1>Sesiones</h1>
					<a onclick="accionCrearSesion(this)" id='botonCrearSesion' class="boton_celeste">Crear nueva sesion</a>
					<a onclick='accionCargaCancelar(this)'  id="botonCancelarCarga"  class="boton_celeste">Volver al listado de sesiones</a>
            </div>
            
            <div id="formSeleccionSesion" class="elementoOculto">   
                <div class='formSeleccionSesionCuerpo' id='divSeleccionSesionCuerpo'>
                    <h1>Sesiones publicadas</h1>
                    <div id='barrabusqueda'><input id='barrabusquedaI' autocomplete='off' value='' onkeyup="actualizarBusqueda(event);"><a onclick='limpiaBarra(event);'>x</a></div>
                    <p id='txningunasesion'>- ninguna -</p>
                    <div id='listaSesionesPublicadas'></div>
                </div>
            </div>
            
            <div id='formEditarSesiones' idindicador='0' class="elementoOculto">
            
	            <div class="menuAcciones elementoOculto" id="divMenuAccionesCrea">
	            	<h1>Acciones</h1>
	                <a onclick='accionCreaEliminar(this)' id="botonEliminar" class="boton_rojo" >Eliminar</a>
	                <a onclick='accionCreaGuardar(this)' id="botonGuardar" class="boton_celeste" >Guardar</a>
	                <a onclick='accionCreaPublicar(this)' id="botonPublicar"  class="boton_celeste">Publicar</a>
	                <a onclick='accionJugar()'  class="boton_celeste">¡Jugar!</a>
            		<a href='./app_game_highscores.php' class="boton_celeste">high scores</a>
	            </div>
            
            
                <div class='elementoCarga largo'>
                    <h1>Nombre</h1>
					
					<input type="hidden" name='idsesion' id="idsesion">
                    <input type="text" name='nombre' id="sesionNombre" onkeypress="accionEditarIndCampo(event, this)">
                </div>
                <div class='elementoCarga largo'>
                    <h1>Presentacion</h1>
                    <textarea type="text" name='presentacion' id="sesionPresentacion" onkeypress="accionEditarIndCampo(event, this)"></textarea>
                </div>
                
                <div class='elementoCarga'>
                    <h1>Indicador asociado</h1>
                    <select name='id_p_indicadores_indicadores' onchange="editarIndFuncionalidad(event, this)" id="sesionIndicadorAsociado">
                        <option value="0" selected>-elegir-</option>                          
                    </select>
                </div>
                <div class='elementoCarga'>
                    <h1>Costo unitario de ejecución</h1>
                    <input name="costounitario" id="sesionCostounitario" type="number" value='0'>
                </div>
                <div class='elementoCarga'>
                    <h1>Límite unitario de ejecución por turno</h1>
                    <input name="limiteunitarioporturno" id="sesionLimiteunitarioporturno" type="number" value='1'>
                </div>
                <div class='elementoCarga'>
                    <h1>Modo red</h1>
                    <input type='hidden' name="modored" id="sesionModored">                    
                    <input onclick='chekearinput(this)' t='1' f='0' type='checkbox' name="checklimiteunitarioporturno" for="sesionModored"  id="checksesionModored">
                    
                </div>
                <div class='elementoCarga'>
                    <h1>Turnos</h1>
                    <input name="turnos" id="sesionTurnos" type="number" value='4'>
                </div>
            </div>
        
        </div>
    </div>
</div>

<script type="text/javascript">
	var _ConsultasPHP = {
		"cons_real" : 0,  //cantidad de consultas realizadas hasta el momento   
		"cons_resp" : 0,  //cantidad de consultas respondidas hasta el momento
		"consultas" : {},
		"mensajes" : []
	};
</script>

<script type="text/javascript" src="./comun_interac/comun_interac.js?t=<?php echo time();?>"></script> <!-- definicion de funcions comunes como la interpretacion de respuestas ajax-->

<script type="text/javascript" src="./sis_gral/sistema_marco.js?t=<?php echo time();?>"></script> <!-- funciones de consulta general del sistema -->
<!---<script type="text/javascript" src="./sistema/sis_acciones.js"></script> <!-- funciones de consulta general del sistema: acciones -->

<script type="text/javascript" src="./comun_mapa/comun_mapa_inicia.js?t=<?php echo time();?>"></script> <!-- definicion de variables comunes para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_recorte.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de recorte para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_selector_capas.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de selector de capa base y extras para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_localiz.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de localizacion de direcciones para mapas en todos los módulos-->


<script type="text/javascript" src="./app_game/app_game_queries.js?t=<?php echo time();?>"></script> <!-- carga funciones de consulta de base de datos -->
<script type="text/javascript" src="./app_game/app_game_pagina.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_game/app_game_mapa.js?t=<?php echo time();?>"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript" src="./comun_consultas/comunes_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript">
	
	baseMapaaIGN();//cargar mapa base IGN
			
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
</script>

</body>
