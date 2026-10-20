<?php
/**
* aplicación de visualización y gestion de relevamientos de campo.
 * 
 *  
* @package    	geoGEC
* @subpackage 	app_rele. Aplicacion para la gestión de relevamientos
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author	based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
* @copyright	2018-2023 Universidad de Buenos Aires
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


$COD = isset($_GET['cod'])?$_GET['cod'] : '';
$ID = isset($_GET['id'])?$_GET['id'] : '';
if($ID==''&&$COD==''){
	header('location: ./index.php');
}

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	
// medicion de rendimiento lamp 
$starttime = microtime(true);
?>
<!DOCTYPE html>
<head>
    <title>GEC - Plataforma Geomática</title>
    
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	
    <?php include("./includes/meta.php");?>
    <link href="./css/mapauba.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link rel="manifest" href="pantallahorizontal.json">
    
	<link href="./css/geogecgeneral.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    
    
    <link href="./js/ol6.3/ol.css" rel="stylesheet" type="text/css">
    
    
	<link href="./usuarios/usuarios.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">	
    
    <link href="./app_comu/app_comu_css_gral.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    
    <style>

    </style>
</head>

<body>
	
<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<!---<script type="text/javascript" src="./js/ol6.3/ol.js"></script>-->
<script type="text/javascript" src="./js/ol10.1/ol.js"></script>
<script charset='utf-8' src="./js/geotiff/geotiff.js"></script>

<div estado="cerrado" id="cartel_consultando">
	<a id="cerrar" onclick="cerrarCartelConsultando();">cerrar</a>
	<img id="cargando" src="./img/cargando.gif">
	<img id="error" src="./img/error.png">
	<div id="mensajes"> </div>	
	<div id="consultas"> </div>
</div>


<div id="pageborde">
    <div id="page">
		
		<?php include('./usuarios/usu_acceso.php');?>
				
				
		<div id='encabezado' style='display:none;'>
			<a href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
				<h2>geoGEC</h2>
				<p>Plataforma Geomática del centro de Gestión de Espacios Costeros</p>
			</a>
			<div id='elemento' tipo="Accion">
				<img src='./img/app_rele_hd.png' style='float:left;'>
				<h2 id='titulo'>cargando...</h2>
				<div id='descripcion'>cargndo...</div>
			</div>	
		</div>

		<div id='menutablas'>
			<h1 id='titulo'>- nombre de proyecto -</h1>
			<p id='descripcion'>- descripcion de proyecto -</p>
			<div id='menuacciones'>
				<div id='lista'></div>	
			</div>
		</div>
  		<div id='encabezado_panel'>
			<h1 id='titulo'>- nombre de proyecto -</h1>
			<p id='descripcion'>- descripcion de proyecto -</p>
		</div>      
        <div id="portamapa">
			<div id="leyenda_flotante_seleccion">
			</div>	
            <div id='titulomapa'>
                <p id='tnombre'></p>
                <h1 id='tnombre_humano'></h1>
                <p id='tdescripcion'></p>
                <b><p id='tseleccion'></p></b>
            </div>
            <div id='mapa' class="mapa"></div>
            <div id='auxiliar' mostrando=''><div id='cont'></div></div>
            <div id="wrapper">
                <div id="location"></div>
                <div id="scale"></div>
            </div>
            <div id='botonera_mapa'></div>

        </div>
		<div id="cuadrovalores">
			<div id="colecciones">
			
			
				<div id="colecciones_seleccion">	
				</div>	
				<div id="colecciones_listado">	
				</div>				
			</div>	
			
			<div id="combo_capas_consulta">
				
				<div id="capas">
					<h3>Referecias</h3>
					<div id="capas_cuerpo">	
						
						<div class='modelo ref_capa' idcapa=''>
							<div class='campo'>
								<label>Nombre: </label>
								<span id='sp_nombre'></span>
							</div>
							<div class='campo'>
								<label>Descripción: </label>
								<span id='sp_desc'></span>
							</div>
							<div class='campo'>
								<label>Leyenda: </label>
								<div id='leyenda'>
									<div class='regla'>
										<div class='simbolo'></div>	
										<label> : </label>
									</div>
								</div>
							</div>
						</div>
					</div>	
				</div>	
				
				<div id='selector_acciones'>
					<h3>Acción activa en el mapa</h3>
					<input onchange='actualizaAccionMapa()' value='selecciona' name='accion' checked='true' type='radio'><label for='selecciona'>Consultar un objeto</label>
					<br>
					<input onchange='actualizaAccionMapa()' value='marca' name='accion' type='radio'><label for='marca'>Marcar un punto</label>
				</div>
				
				<div id="consulta">
					<h3>Envíe su consulta</h3>
					<p>Primero marque un punto.</p>
					<div id="capas_cuerpo">	
						
						<input type='hidden' name='geotx'>
						<textarea name='opinion' placeholder='opinión...'></textarea>
						<p id='cartel_marcar'>Primero marque un punto en el mapa al cual su opinión hace referencia</p>
						<a class='boton_celeste' onclick='guardarOpinion()'>enviar</a>
					</div>	
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
	
	var _Acc = "comu";
	var _IdUsu='<?php echo $_SESSION["geogec"]["usuario"]['id'];?>';
	
	<?php if(!isset($_GET["id"])){$_GET["id"]='';} ?>
	var _idPanel = '<?php echo $_GET["id"];?>';
	
	var _RecorteDeTrabajo='';
	
	//funciones para consultar datos y mostrarlos
	var _Reles={};
	var _Colecciones={};
	var _Complemento={};

	var _IdMarco = getParameterByName('id');
	var _CodMarco = getParameterByName('cod');	
	var _DataUsuaries={};

	var _DataPanel={}; // configuración del panel cargado
	var _DataConsultas={}; // relevamiento de opinion
	var _DataCapas={}; // capas base complemento
	var _DataCapasColec={}; // capas listado de objetos

	var _EstilosCapa={};
	
	
</script>

<script type="text/javascript" charset="UTF-8" src="./comun_interac/comun_interac.js?t=<?php echo time();?>"></script> <!-- definicion de funcions comunes como la interpretacion de respuestas ajax-->


<script type="text/javascript" src="./sistema/sistema_marco.js?t=<?php echo time();?>"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./sistema/sis_acciones.js?t=<?php echo time();?>"></script> <!-- carga funcion de consulta de acciones y ejecución, completa _Acciones -->

<script type="text/javascript" src="./comun_mapa/comun_mapa_inicia.js?t=<?php echo time();?>"></script> <!-- definicion de variables comunes para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_link.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones para actualizar el link url directo al mapa en su configuración actual-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_recorte.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de recorte para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_selector_capas.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de selector de capa base y extras para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_localiz.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de localizacion de direcciones para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_tamano.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de agrandar mapa-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_descarga.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de descarga del mapa activo-->

<script type="text/javascript" src="./app_comu/app_comu_js_mapa.js?t=<?php echo time();?>"></script> <!-- carga funciona de gestión de mapa-->
<script type="text/javascript" src="./app_comu/app_comu_js_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_comu/app_comu_js_pagina.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_comu/app_comu_js_mostrar.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./comunes_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones de interaccion con el mapa -->

<script charset='utf-8' type="text/javascript" src="./app_comu/papelera/indice_bases.js"></script>	

<script charset='utf-8' type="text/javascript" src="./app_comu/papelera/mapa_raster.js"></script>	

<script type="text/javascript">
	console.log(_view);
	//cargarMapaBase();
	//cargarIndicador('I1');
	
	
	cargaBaseLink(); //en comun_mapa_link.js
	
	//inicializarColumnas(); //en app_rele_pagina.js define variables en estado false.

	cargarListadoPaneles(); // en app_comu_js_consultas.js
	

		
</script>

</body>
