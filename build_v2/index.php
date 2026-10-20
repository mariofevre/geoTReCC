<?php 
/**index.php
*
* aplicacioón de presentación terriotrial de trabajos del GEC
* 
*  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
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

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificación de seguridad 
//include('./includes/conexion.php');
if(!isset($_SESSION)) { session_start(); }

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");

// función de consulta de proyectoes a la base de datos 
// include("./consulta_mediciones.php");

$ID = isset($_GET['id'])?$_GET['id'] : '';

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	
// medicion de rendimiento lamp 
$starttime = microtime(true);
?><!DOCTYPE html>

<head>
	<title>GEC - Plataforma Geomática</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<?php include("./includes/meta.php");?>
	<link href="./css/mapauba.css" rel="stylesheet" type="text/css">
	<!---<link href="./css/BaseSonido.css" rel="stylesheet" type="text/css">-->
	
	
	<!---<link href="./css/ad_navega.css" rel="stylesheet" type="text/css">	-->
	<link rel="manifest" href="pantallahorizontal.json">
	<!---<link href="./css/BA_salidarelevamiento.css" rel="stylesheet" type="text/css">-->
	
	
	<link href="./css/geogecgeneral.css" rel="stylesheet" type="text/css">
	<link href="./css/geogecindex.css" rel="stylesheet" type="text/css">

	<link href="./usuarios/usuarios.css" rel="stylesheet" type="text/css">	
	<link href="./js/ol6.3/ol.css" rel="stylesheet" type="text/css">
		
	<style type='text/css'>
		#menudatos #descripcion{
			font-size:14px;
			text-align:justify;
		}
		
		div#cuadrovalores div#encabezado > h1 a{
			display:none;			
		}	
		
		
		div#cuadrovalores #menupropios{
			display:none;
		}		
		
		div#cuadrovalores[acceso='si'] #menupropios{
			display:block;
		}		
		
		div#cuadrovalores[acceso='si'] #encabezado{
			display:none;
		}
		
		div#cuadrovalores[acceso='si'] #menupropios{
			display:block;
		}
		
		
		div#cuadrovalores[acceso='si'] #elemento{
			display:none;
		}
		
		#menupropios[estado='cerrado']{
			overflow:hidden;
			font-size:14px;
			height:24px;
			transition: height 2s;	
		}
		#menupropios[estado='cerrado'] #btnMuestraPropios{
			display:inline-block;
		}
		#menupropios #btnMuestraPropios{
			display:none;
			height: 14px;
			margin: 4px;
			padding:2px;
			color:#000;	
			border: 1px solid #ff6600;		
		}
		#menupropios #btnMuestraPropios:hover{
			background-color: rgba(255, 102, 0, 0.2);
		}
		
		#menupropios #lista a[cargado='si']{
			border: 1px solid rgba(255,25,55,1);
    		background-color: rgba(228,25,55,0.8);
		}	
		
	</style>	
	
	<style id='elementoSeleccionado' type='text/css'>
	
		div#cuadrovalores[cargado='si'] div#encabezado > h1 a{
			display:inline-block;
		}	
		
		div#cuadrovalores[cargado='si'] div#encabezado > p, div#encabezado > ul{
			display:none;
		}
		div#cuadrovalores[cargado='si'] div#elemento{
			display:none;
		}
		
		div#cuadrovalores[cargado='si'] div#menutablas{
			
		}		

		
		div#cuadrovalores[cargado='si'] div#menuelementos #titulo{
			display:none;
		}		
		
		div#cuadrovalores[cargado='si'] div#menuelementos #lista a[cargado='si']{
			display:block;
		}
		div#cuadrovalores[cargado='si'] div#menuelementos #lista a{
			display:none;
		}		
		
	</style>	
</head>

<body>
	

<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./js/ol6.3/ol.js"></script>


<div id="pageborde">
	<div id="page">
		
		<?php include('./usuarios/usu_acceso.php');?>
		
		<div id='portamapa'>
			<div id='titulomapa'><p id='tnombre'></p><h1 id='tnombre_humano'></h1><p id='tdescripcion'></p><b><p id='tseleccion'></p></b></div>
			<div id='mapa'></div>
			<div id='botonera_mapa'></div>
			<div id="wrapper">
		        <div id="location"></div>
		        <div id="scale"></div>
		    </div>
		</div><!---
		
		--><div id='cuadrovalores'>
			<div class='fila' id='encabezado'>
				<h1>geoGEC<a href='./index.php'>volver al inicio</a></h1>
				<p>Plataforma Geomática del centro <a target='blank' href="http://www.municipioscosteros.org/nuestros-principios.aspx">Gestión de Espacios Costeros</a></p>
				<p>En este espacio se incorporan datos estructurados para la visualización de las perspectivas de abordaje del GEC</p>
				<p>Aquí proponemos tres niveles de información geográfica</p>
				<ul>
					<li>Datos estructurales</li>
					<li>Datos regionales o intermunicipales</li>
					<li>Datos intramunicipales</li>				
				</ul>
			</div>
			
			<div id='elemento'>
				<h1 id='titulo'>base de datos geoGEC</h1>
				<div id='descripcion'>base de datos geográfica del GEC</div>
			</div>	
			
			<div id='menupropios'>
				<a id='btnMuestraPropios' onclick='mostrarProyectosPropios()'>Ver Tus Proyectos</a>
				<h2 id='titulo'>Tus proyectos</h2>
				<div id='lista'></div>	
			</div>	
			
			<div id='menutablas'>
				<h2 id='titulo'>Índices disponibles <span>capas estructurales</spna></h2>
				<div id='lista'></div>	
			</div>	
			<div id='menuelementos'>
				<h2 id='titulo'>menu de elementos disponbiles</h2>
				<div id='lista'></div>	
			</div>	
			<div id='menuacciones'>
				<h3 id='titulo'>menu de módulos disponibles</h3>
				<div id='lista'></div>	
			</div>					
			<div id='menudatos'>
				<h3 id='titulo'>menu de datos cargados</h2>
				<div id='descripcion'></div>	
				<div id='lista'></div>	
			</div>
			
		</div>

		<?php include('./autorias.php');?>
		v2025.1
	</div>	
</div>	


	
<div class='formcentral' id='formcargaverest' idver=''>
	<div id='avanceproceso'></div>
	<a class='cerrar' onclick='this.parentNode.style.display="none";'>x- cerrar</a>
	<h1>formulario para la carga de una nueva versión para una capa estructural</h1>
	<p>las capas estructurales regulan la operación de la plataforma.</p>
	<p>Es muy recomendable que sepa lo que está haciendo antes de seguir.</p>
	<a id='botonformversion' onclick='formversion(this)'>cargar una nueva versión</a>
	<div id='carga'>
		<h2> usted está cargando una nueva versión con el id <span id='idnv'></span></h2>
		<p id='nomver'></p>

		<div class='componentecarga'>
			<h1>archivos cargando</h1>
			<div id='archivosacargar'>
				<form id='shp' enctype='multipart/form-data' method='post' action='./ed_ai_adjunto.php'>			
					<label style='position:relative;' class='upload'>							
					<span id='upload' style='position:absolute;top:0px;left:0px;'>arrastre o busque aquí un archivo</span>
					<input id='uploadinput' style='opacity:0;' type='file' multiple name='upload' value='' onchange='enviarSHP(event,this);'></label>
					<select id='crs' onchange='ValidarProcesarBoton()'>
						<option value=''>- elegir -</option>
						<option value='4326'>4326</option>
						<option value='3857'>3857</option>
						<option value='22171'>22171</option>
						<option value='22172'>22172</option>
						<option value='22173'>22173</option>
						<option value='22174'>22174</option>
						<option value='22175'>22175</option>
						<option value='22176'>22176</option>
						<option value='22177'>22177</option>
					</select>
					
					<div id='cargando'></div>
				</form>
			</div>
		</div>
		
		<div class='componentecarga'>
			<h1>archivos cargados</h1>
			<p id='txningunarchivo'>- ninguno -</p>
			<div id='archivoscargados'></div>
		</div>
		
		<div class='componentecargalargo'>
			<h1>campos identificados</h1>
			<p id='verproccampo'></p>
			<div id='camposident'></div>			
		</div>
		
		<div class='componentecarga'>
			<h1>Módulos</h1>
			<a onclick='eliminarCandidatoVersion(this.parentNode);'>eliminar esta versión candidata</a>
			<a onclick='guardarVer(this.parentNode);'>guardar esta versión preliminarmente</a>
			<a id='procesarBoton' onclick='procesarVersion(this.parentNode)'>procesar la carga de esta versión</a>
		</div>
	</div>
	
</div>

<script type="text/javascript" src="./index_formSHP.js"></script> <!-- carga funciones de operacion del formulario central para la carga de SHP-->
<script type="text/javascript" src="./index_consultas.js"></script> <!-- carga funciones consulta de datos-->
<script>
	var _Est = getParameterByName('est');
    var _Cod = getParameterByName('cod');
    
    var _DataElem={};
    
    //Variable de filtro en búsquedas de datos.
    <?php if(!isset($_SESSION['geogec']['usuario']['recorte'])){$_SESSION['geogec']['usuario']['recorte']='';};?>
	_RecorteDeTrabajo=JSON.parse('<?php echo json_encode($_SESSION['geogec']['usuario']['recorte']);?>');
    
    
    actualizarPermisos();
    
</script>

<script type="text/javascript" src="./index_upload.js"></script> <!-- carga funciones de upload de SHP-->

<script type="text/javascript" src="./comun_mapa/comun_mapa_inicia.js"></script> <!-- definicion de variables comunes para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_recorte.js"></script> <!-- definicion de variables y funciones de recorte para mapas en todos los módulos-->


<script type="text/javascript" src="./index_mapa.js"></script> <!-- carga funciones de carga de mapa general-->
<script type="text/javascript" src="./index_mapa_funciones.js"></script> <!-- carga funciones de interacción con el mapa-->

<script>



	if(_RecorteDeTrabajo!=''){
		cargaRecorteSession();
	}

</script>
</body>
