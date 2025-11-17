<?php
/**
* aplicación de visualización y gestion de documentos de trabajo. consulta carga y genera la interfaz de configuración de lo0s mismos.
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
	if(!isset($_SESSION[$CU]["usuario"]['id'])){
		$_SESSION[$CU]["usuario"]['id']='-1';
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

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d"); $HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

?><!DOCTYPE html>
<head>
    <title><?php echo $CONF['plataforma']['nombre'];?></title>
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
			font-family:'game';
			display:block;
			width:80px;
			text-align:center;
			line-height:20px;
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
			left:5px;
			top:5px;
			width:100px;
			font-size:11px;
			z-index:200;
			height: 40px;
		}	
		#ayuda.senales{
			top:5px;
		}
		#ayuda.senales > div{
			display: inline-block;
			width: 47px;
			top: 5px;
			text-align: right;
		}
		
		
		#limite.senales{
			top:60px;
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
			top:115px;
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
		
		
		
		#gameover{
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
		#gameover h1{
			font-family:'game';
		}
		#gameover p{
			font-size:14px;
			margin-top:8px;
			margin-top:8px;
		}
			
		#gameover a{
			display:block;
			text-align:center;
			line-height:20px;
			height:20px;
			border:1px solid #08afd9;
			position:absolute;
			right:5px;
			bottom:5px;
			font-size:15px;
			border-radius:5px;
			font-family:'game';
			width:auto;
		}	
		
		#gameover input{
			text-align:center;
			height:30px;
			border:1px solid #08afd9;
			font-size:20px;
			border-radius:5px;
			font-family:'game';
			width:80px;
		}		
		
		#menucapas{
			display:none;
		}
		#botonrecorte{
			display:none;
		}
    </style>
</head>

<body>   
<script type="text/javascript" src="./terceras_js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./terceras_js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./terceras_js/ol5.3/ol.js"></script>

<div id="pageborde">
    <div id="page">
		<div id='encabezado'>
			<a href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
                <h2><?php echo $CONF['plataforma']['nombre'];?></h2>
                <p><?php echo $CONF['plataforma']['descrip'];?></p>
            </a>

        </div>
        <div id='menutablas'>
            <h1 id='titulo'>- nombre de la sesion -</h1>
            <p id='descripcion'>- presentacion de la sesion -</p>
            <div id='ayuda'>ayuda extendida</div>
        </div>	
        <div id="portamapa">
        	
        	<div id='ayuda' class='senales'>
				<div id='func'>click:</div> inicia traza<br>
				<div id='func'>dobleclick:</div> termina<br>
				<div id='func'>rueda:</div> zoom
        	</div>
        	
        	<div id='limite' class='senales'><div id='barra'><div id='avan'></div></div><span id='porc'>0% 0m</span><div id='tx'>propuesta válida...</div></div>
        	<div id='pasar' class='senales' estado='inactivo'><a id='flecha' onclick='pasarTurno()'>pasar -><span>al siguiente turno</span></a></div>
            <div id='titulomapa'>
                <p id='tnombre'></p>
                <h1 id='tnombre_humano'></h1>
                <p id='tdescripcion'></p>
                <b><p id='tseleccion'></p></b>
            </div>
            <div id='mapa' class="mapa"></div>
            <div id="wrapper">
                <div id="location"></div>
                <div id="scale"></div>
            </div>
        </div>
        <div id="cuadrovalores">
        	
            <div id='sesionActiva' idindicador='0' class="elementoOculto"></div>
            <div id='turnos'>
                <h1>Turnos</h1>
                <p id='actual'><span id='titulo'>turno actual:</span><span id='nturn'></span>. Tenés otros <span id='nturndif'> turnos.</p>
                <div id='historial'></div>
            </div>
            
            <div id='graficos'>
            	<h1>Puntaje</h1>
            	<p><span id='puntajeactual'></span> (<span id='puntajePactual'></span>%)</p>
            	<br>

            	
            </div>

        </div>
    </div>
</div>
<div id='presentacion'></div>
<div id='gameover'></div>
<div id='gifanimado'>
	<img src='./_img/cargando.gif'>               
</div>
 <script type="text/javascript">
 var _IdUsu=-1;
</script>



<script type="text/javascript" src="./comun_interac/comun_interac.js"></script> <!-- definicion de funcions comunes como la interpretacion de respuestas ajax-->

<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<!---<script type="text/javascript" src="./sistema/sis_acciones.js"></script> <!-- funciones de consulta general del sistema: acciones -->

<script type="text/javascript" src="./comun_mapa/comun_mapa_inicia.js"></script> <!-- definicion de variables comunes para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_recorte.js"></script> <!-- definicion de variables y funciones de recorte para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_selector_capas.js"></script> <!-- definicion de variables y funciones de selector de capa base y extras para mapas en todos los módulos-->
<!---<script type="text/javascript" src="./comun_mapa/comun_mapa_localiz.js"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de localizacion de direcciones para mapas en todos los módulos-->




 <script type="text/javascript" src="./app_game/app_game_play_pagina.js"></script> <!-- carga funciones de operacion de la pagina -->
 <script type="text/javascript" src="./app_game/app_game_play_queries.js"></script> <!-- carga funciones de consulta de base de datos -->
 
 <script type="text/javascript" src="./app_game/app_game_mapa.js"></script> <!-- carga funciones de interaccion con el mapa -->
 
<script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->


 <script type="text/javascript" src="./js/jsts/jsts.js"></script> <!-- geoprocesos del lado del cliente -->
 
 
<script type="text/javascript">
	
	
	baseMapaaIGN();//cargar mapa base IGN

 
	avanzarTurno();

</body>
