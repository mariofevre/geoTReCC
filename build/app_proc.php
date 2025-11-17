<?php
/**
* módulo de automaticación de procesamiento de datos.
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
    <link href="./css/geogec_app_docs.css" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app_proc.css" rel="stylesheet" type="text/css">
    
    <link href="./js/ol6.3/ol.css" rel="stylesheet" type="text/css">
    <style>
    	
    </style>
</head>

<body>
	
<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./js/ol6.3/ol.js"></script>


<div estado="cerrado" id="cartel_consultando">
	<a id="cerrar" onclick="cerrarCartelConsultando();">cerrar</a>
	<img id="cargando" src="./img/cargando.gif">
	<img id="error" src="./img/error.png">
	<div id="mensajes"> </div>	
	<div id="consultas"> </div>
</div>

<div id="pageborde">
    <div id="page">
		<?php include('./sis_usuarios/usu_acceso.php');?>
		
        <div id='encabezado'>
		<a href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
                <h2><?php echo $CONF['plataforma']['nombre'];?></h2>
                <p><?php echo $CONF['plataforma']['descrip'];?></p>
            </a>

            <div id='elemento'>
                <img src='./img/cargando.gif' style='float:left;'>
                <h2 id='titulo'>--</h2>
                <div id='descripcion'>--</div>
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
        	
            <div class='capaEncabezadoCuadro'>
                <h1>Capas Complementarias de Información</h1>
            </div>
            
            <a onclick="procIniciarInstancias()" id='botonProcIniciarInstancia' class='boton_celeste'>[+] Iniciar un proceso</a>
            
            <div id="divMenuSeleccionProcActivos">
                <div class='elementoCarga accionesCapa'>
                    <a id="botonCancelarCarga"  onclick='procCancelarSeleccionProcActivo(this)' class='boton_rojo'>Cancelar</a></br>
                </div>
                <div class='formSeleccionCapaCuerpo' id='divSeleccionProcActivo'  contenido='vacio'>
                    <h1>Procesos activos</h1>
                    <div id='barrabusqueda'><input id='barrabusquedaI' autocomplete='off' value='' onkeyup="actualizarBusqueda(event);"><a onclick='limpiaBarra(event);'>x</a></div>
                    <div id='listaprocesosactivos'></div>
                </div>
            </div>
            
            
            
            <div id="formProcActivo" contenido='vacio'>
				<img id='procesando' src='./img/cargando.gif'>
				<p id='avance'></p>
				<h2 id='tituloProcActivo'></h2>
				<div>
					<label>Título:</label>
					<input name='titulo'>				
				</div>
				
				<input name='idproc' type='hidden'>
				<input name='idinst' type='hidden'>
				
				<div>
					<label>tipo:</label>
					<span id='tipoproceso'></span>
				</div>
				
				<div id='componentes'>
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
	
	
	var _IdUsu='<?php echo $_SESSION["geogec"]["usuario"]['id'];?>';
	var _Acc = "proc";
	

		
	//funciones para consultar datos y mostrarlos
	var _Proc={};  //COntiende procesos disponibles de iniciar.
	var _Instancias={}; //Contiene instancias de procesos.
	var _CapasDisponibles={}; //Contiene capas disponibles.



	//funciones para consultar datos y mostrarlos
	var _Tablas={};
	var _TablasConf={};
	var _SelecTabla='';//define si la consulta de nuevas tablas estará referido al elmento existente de una pabla en particular; 
	var _SelecElemCod=null;//define el código invariable entre versiones de un elemento a consultar (alternativa a _SelElemId);
	var _SelecElemId=null;//define el id de un elemento a consultar (alternativa a _SelElemCod);



</script>

<script type="text/javascript" src="./comun_interac/comun_interac.js?t=<?php echo time();?>"></script> <!-- definicion de funcions comunes como la interpretacion de respuestas ajax-->


<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./app_proc/app_proc_consultas.js"></script> <!-- carga funciones de consultas ajax -->
<script type="text/javascript" src="./app_proc/app_proc_muestra.js"></script> <!-- carga funciones de representacion en el DOM -->


<script type="text/javascript" src="./app_proc/app_proc_interaccion.js"></script> <!-- carga funciones dei interacción -->
<script type="text/javascript" src="./app_proc/app_proc_mapa.js"></script> <!-- carga funciona de gestión de mapa-->
<script type="text/javascript" src="./app_proc/app_proc_pagina.js"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_proc/app_proc_Shapefile.js"></script> <!-- carga funciones de operacion del formulario central para la carga de SHP -->
<script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript">
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
</script>

</body>
