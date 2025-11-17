<?php
/**
* aplicación de visualización y gestion de documentos de trabajo. consulta carga y genera la interfaz de configuración de lo0s mismos.
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

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); 
//error_reporting(-1);
ini_set('display_errors', 1);
// verificación de seguridad 
//include('./includes/conexion.php');
if(!isset($_SESSION)) {
	session_start(); 

	if(!isset($_SESSION["geogec"]["usuario"]['id'])){
		$_SESSION["geogec"]["usuario"]['id']='-1';
	}
}

$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";

// funciones frecuentes
include($GeoGecPath."/includes/fechas.php");
include($GeoGecPath."/includes/cadenas.php");

// función de consulta de proyectoes a la base de datos 
// include("./consulta_mediciones.php");
$COD = isset($_GET['cod'])?$_GET['cod'] : '';
$IDSESION = isset($_GET['idsesion'])?$_GET['idsesion'] : '';
$PART = isset($_GET['partida'])?$_GET['partida'] : '';
$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	
// medicion de rendimiento lamp 
$starttime = microtime(true);
?>
<head>
    <title>GEC - Plataforma Geomática</title>
    <?php include("./includes/meta.php");?>
    <link href="./css/mapauba.css" rel="stylesheet" type="text/css">
    <link rel="manifest" href="pantallahorizontal.json">
    
    <link href="./css/geogecgeneral.css" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app.css" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app_game.css" rel="stylesheet" type="text/css">
    
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
		
		#presentacion{
			position:fixed;
			top:20vh;
			left:20vw;
			width:0vw;
			height:0vh;
			max-height:40vh;
			opacity:0;
			transition: width 2s, height 2s, opacity 2s;
			z-index:1000;
			border:1px solid #08afd9;
			background-color: #fff;
			box-shadow:10px 10px 20px #000;
			overflow:hidden;
		}
		
		
		#presentacion p{
			width: 60vw;
			font-size: 14px;
			margin-bottom: 4px;
			margin-top: 4px;
			line-height: 1.5;
			width: calc(60vw - 20px);
			text-align:justify;
		}
		#presentacion p span.variable{
			display:inline-block;
			width:260px;
			text-align:right;
			margin:2px;
		}
		#presentacion p span.valor{
			display:inline-block;
			font-weight:bold;
		}	
		
		#presentacion #botonpresentok{
			display:block;
			width:40px;
			text-align:middle;
			height:20px;
			border:1px solid #08afd9;
			position:absolute;
			right:5px;
			bottom:5px;
			font-size:15px;
			border-radius:5px;
		}
		
		.senales{
			border:1px solid #000;
			box-shadow:1px 1px 1px #08afd9;
			background-color:#fff;
			display:block;
			position:absolute;
			left:20px;
			top:5px;
			width:100px;
			font-size:11px;
			z-index:200;
			height: 40px;
		}	
		
		#limite.senales{
			top:5px;
		}
		
		#limite.senales #barra{
			width:calc(100% - 10px);
			position:relative;
			display:block;
			margin:5px;
			
		}
		#limite.senales #barra #avan{
			width:1%;
			background-color:#08afd9;
			height:5px;
		}
		.senales[estado='alerta']{
			background-color:rgba(255,100,100,0.8);
		}
		#limite.senales[estado='alerta'] #barra #avan{
			background-color:red;
		}
		
		
		#pasar.senales{
			top:60px;
		}
		#pasar.senales[estado='inactivo']{
			opacity:0.5;
			cursor:auto;
		}
		
		#pasar.senales[estado='inactivo'] a:{
			cursor:auto;
		}
		
		#pasar.senales[estado='inactivo'] a:hover{
			background-color:#fff;
			color:#08afd9;
			cursor:auto;
		}
		#pasar.senales #flecha{
			width:calc(100% - 10px);
			font-size:24px;
			margin:5px;
			display:inline-block;
			line-height: 16px;
			 text-align:center;
		}
		
		#pasar.senales #flecha span{
			font-size:11px;
			display:block;
			text-align:center;
		}		
		
		#press{
			font-family:'game';
			font-size:30px;
			width:100%;
			margin-top:50px;
			text-align:center;
		}	
		
		.portamapa h1{
			font-family:'game';
			font-size:30px;
			text-align:center;
			width:600px;
			margin:0;
			
		}
		
		
		#turnos h1{	
			margin-bottom:10px;
		}
		#turnos h2{
			margin-top:20px;	
			margin-bottom:4px;
		}
		#turnos p{
			font-size:14px;
			text-align:justify;
			margin-top:5px;
			margin-bottom:5px;
		}
		#turnos li{
			margin-top:5px;
			margin-bottom:5px;			
			font-size:13px;
		}
		div.fila{
			width:100%
		}
		.fila div{
			display: inline-block;
			width: 120px;
			font-size: 20px;
			text-align: justify;
			font-family: 'game';
		}
		
		div.fila[selecto='selecto']{
			background-color: #08afd9;
		}
		
		.fila div.iniciales{
			text-align:center;
			width:200px;
		}
		
		.fila div.iniciales .anonimo{
			font-size:12px;
		}
		
		
		.fila div.orden{
			text-align:right;
			width:60px;
		}
		
				
		.fila div.puntaje{
			text-align:right;
		}
		
		.fila div.puntajePorc{
			text-align:center;
			font-size:16px;
		}
		
    </style>
</head>

<body>   
<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./js/ol5.3/ol.js"></script>
<!---<script type="text/javascript" src="./js/ol4.2/ol-debug.js"></script>--->

<div id="pageborde">
    <div id="page">
        <div id='encabezado'>
			<a target='_blank' href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
                <h2>geoGEC</h2>
                <p>Plataforma Geomática del centro de Gestión de Espacios Costeros</p>
            </a>
			<div id='elemento'></div>
        </div>
        <div id='menutablas'>
            <h1 id='titulo'>- nombre de la sesion -</h1>
            <p id='descripcion'>- presentacion de la sesion -</p>
            <div id='ayuda'>ayuda extendida</div>
        </div>	
        <div class="portamapa">
        	<h1>HIGHSCORES</h1>
        </div>
        <div id="cuadrovalores">
            <div id='sesionActiva' idindicador='0' class="elementoOculto"></div>
            <div id='turnos'>
                <h1>Ciclovias!</h1>
            	<p>En busca de la adaptación al cambío climático, deberás construir ciclovías en el Partido de Vicente López.</p>

            	<h2>Movilidad sostenible</h2>
            	<p>La movilidad sostenible es uno de los temas urgentes en la adaptación al cambio climático.</p>
            	<p>Hoy en día los gobiernos Municipales deben brindar recursos que garanticen a la población la capacidad de desplazarse ante: <ul><li>suministro de combustible deficiente</li><li>amenazas inminentes a la salud en su lugar de residencia</li><li> falta de suministro de serviciós básicos.</li></ul></p>

            	<a id='press' href='./app_game_play.php?idsesion=<?php echo $IDSESION;?>&cod=<?php echo $COD; ?>'> - PRESS START - </a>	
            </div>
        </div>
    </div>
</div>
<div id='presentacion'></div>

 <script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema --> 
 <script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->
 
 <script type='text/javascript'>
 
 	var _Partida='<?php echo $PART;?>';	
 </script>
 <script type="text/javascript" src="./app_game/app_game_highscores.js"></script> <!-- carga funciones de interaccion con el mapa -->
 
 
</body>
