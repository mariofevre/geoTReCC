<?php 
/**
* aplicación de visualización y gestion de la planificación de marcos academicos; procedos de producción de datos.
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

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);
ini_set('display_errors', '1');
// verificación de seguridad 
//include('./includes/conexion.php');
if(!isset($_SESSION)) {
	 session_start(); 

	if(!isset($_SESSION["geogec"]["usuario"]['id'])){
		$_SESSION["geogec"]["usuario"]['id']='-1';
	}
}

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");
// función de consulta de proyectoes a la base de datos 
// include("./consulta_mediciones.php");

$COD = isset($_GET['cod'])?$_GET['cod'] : '';
$ID = isset($_GET['id'])?$_GET['id'] : '';
if($ID==''&&$COD==''){
	header('location: ./index.php');
}

if(!isset($_POST['modo'])){
	$_POST['modo']='enumeracion';
}

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
    <link href="./css/geogec_app_plan.css" rel="stylesheet" type="text/css">
    
	<style>
			
	</style>
	
</head>

<body>
	
<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./js/ol4.2/ol-debug.js"></script>

<div estado="cerrado" id="cartel_consultando">
	<a id="cerrar" onclick="cerrarCartelConsultando();">cerrar</a>
	<img id="cargando" src="./img/cargando.gif">
	<img id="error" src="./img/error.png">
	<div id="mensajes"> </div>	
	<div id="consultas"> </div>
</div>

<div id="pageborde">
	<div id="page">
		<div id='encabezado'>
			<a href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
				<h2>geoGEC</h2>
				<p>Plataforma Geomática del centro de Gestión de Espacios Costeros</p>
			</a>
			
			<div id='elemento'>
				<img src='./img/app_plan_hd.png' style='float:left;'>
				<h2 id='titulo'>Gestor de la Planificación del Proyecto</h2>
				<div id='descripcion'>
					Espacio para cargar y gestionar planificaciónes de proyecto.
					Cada Proyecto puede ser desagreado en actividades y estas ser definidas según su descripción, su fecha prevista de finalización, su estado de avance y sus responsables.
					
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
		
		<div id='portamapa'>
			<div id='titulomapa'><p id='tnombre'></p><h1 id='tnombre_humano'></h1><p id='tdescripcion'></p><b><p id='tseleccion'></p></b></div>
		</div>
		
		<div id="contenidoextenso" idit='0' nivel='0'>
			<div style= "border: #bbb solid 1px; margin: 0px 0px 6px 10px; padding: 3px;">
			<h1>Acciones generales para planificación interna</h1>
			<a onclick='anadirItem(event,0)' style= "font-size: 16px">+ nueva actividad</a>
			</div>
			<div 
				class='hijos'
				nivel="0"
				ondrop="drop(event,this)" 
				ondragover="allowDrop(event,this);resaltaHijos(event,this)" 
				ondragleave="desaltaHijos(this)" 
				ondblclick="anadirItem(event,this.parentNode.getAttribute('idit'));"
			></div>
		</div>
		
		<div id='modelos'>
			
			<div
				class='item'
				idit='nn'
				draggable="true"
				ondragstart="dragcaja(event);bloquearhijos(event,this);"
				ondragleave="limpiarAllowFile()"
				ondragover="allowDropFile(event,this)"
				ondrop='dropFile(event,this)'
			>
				<div id='max'>
					<h1 id='num'>N</h1>
					<div id='resp'><div class='pack'><a title='añadir un nuevo responsable' onclick='sumarResp(this)'>+</a></div></div>
                                        <div id='estadoActividad' class="estadoActividad">
                                            <div class="barraProgresoPropio">
                                                <div class="progressbar-container barraProgresoPropioBar" style="width:0%">0%</div>   
                                            </div>
                                        </div>
				</div>
				<h3 onmouseout='desaltar(this)' onmouseover='resaltar(this)' onclick='editarI(this)'>nombre de esta unidad de planificación</h3>
				<p onmouseout='desaltar(this)' onmouseover='resaltar(this)' onclick='editarI(this)'>descripción de la unidad de planificación</p>
				<div class='documentos'>
				</div>
				<div 
					class='hijos'
					ondrop="drop(event,this)"
					ondragover="allowDrop(event,this)"
					ondragleave="limpiarAllow()" 
					ondblclick="anadirItem(event,this.parentNode.getAttribute('idit'));"
				><div id='siblingzero'></div></div>
			</div>
		</div>

	</div>	
</div>
	

<form id="editoritem" onsubmit="guardarI(event,this)">
        <label>Nombre de la Unidad de Planificación</label>
        <input name='nombre'>
        <input name='id' type='hidden'>
        <label>Descripcion de la Unidad de Planificación</label>
        <textarea name='descripcion'></textarea>
        <label>Progreso de la Unidad de Planificación</label>
        <input type="number" name="progresoNumber" id="progresoNumber" min="0" max="100" oninput="progresoRange.value=progresoNumber.value">
        <input type="range" name="progresoRange" id="progresoRange" min="0" max="100" oninput="progresoNumber.value=progresoRange.value">
        <label class="fechaPropuesta">Finalización: </label>
        <input type="date" name="fechaPropuesta" id="fechaPropuesta">
        <label class="progresoCambiadoPor">Cambiado por: </label>
        <div class="autorCambioItem" name="autorCambioItem">Autor</div>
        <label>Documentos Asociados a la Unidad de Planificación</label>
        <div class="listaDocumentosAsociados"></div>
        <a class="editarListaDocs" onclick="consultarDocumentosAsociados('abrirEditorDocs',this)">Editar Listado</a>

        <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
        <input type='submit' value='guardar'>
        <a id='botonelimina' onclick='eliminarI(event,this)'>eliminar</a>
</form>

<form id="editorresp">
        <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
        <h1>Nombre de la Unidad de Planificación</h1>
        <div id="tituloexcluidos">usuarios sin <br>responsabilidades asignadas.</div>
        <div id="tituloincluidos">usuarios con <br>responsabilidades asignadas.</div>
        <div id="tituloresponsabilidad">responsabilidad <br> asignada</div>
        <div id="excluidos"></div>
        <div id="incluidos"></div>
        <input name='idit' type='hidden'>
</form>

<form id="editorlistadocs">
        <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
        <h1>Vínculos a esta Unidad de Planificación</h1>
        <div id="tituloexcluidos">Documentos disponibles</div>
        <div id="tituloincluidos">Documentos asociados</div>
        <div id="documentocomentario">Comentario</div>
        <div id="excluidos"></div>
        <div id="incluidos"></div>
        <input name='idit' type='hidden'>
</form>

<script type='text/javascript'>
	
	var _ConsultasPHP = {
		"cons_real" : 0,  //cantidad de consultas realizadas hasta el momento   
		"cons_resp" : 0,  //cantidad de consultas respondidas hasta el momento
		"consultas" : {},
		"mensajes" : []
	};
	
	
	///variables globales para cargar información base
    var _IdMarco='<?php echo $ID;?>';
    var _CodMarco='<?php echo $COD;?>';	
    var _Items=Array();
    var _Orden=Array();
    var _UsuId ='<?php echo $_SESSION["geogec"]["usuario"]['id'];?>';
    
    ///variables globales para asignar documentos
	var _DocItems = new Array();
	var _DocOrden = new Array();
	var _DocAsoc = new Array();
</script>

<script type="text/javascript" src="./comun_interac/comun_interac.js?t=<?php echo time();?>"></script> <!-- definicion de funcions comunes como la interpretacion de respuestas ajax-->

<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->

<script type="text/javascript" src="./app_plan/app_plan_pagina.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./app_plan/app_plan_queries.js"></script> <!-- carga funciones de interaccion con el mapa -->

<script type="text/javascript">
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
</script>

</body>
