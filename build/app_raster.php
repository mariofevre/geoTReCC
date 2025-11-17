<?php
/**
* aplicación de gestión de capas raster.
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
    <?php include("./comun_general/meta.php");?>
    <link rel="manifest" href="pantallahorizontal.json">
    
    <link href="./comun_css/general.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./comun_css/general_app.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./app_raster/css/app_raster.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    
    <link href="./terceras_js/ol6.3/ol.css" rel="stylesheet" type="text/css">
    <style>

    </style>
</head>

<body>
	
<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./js/ol6.3/ol.js"></script>
<script type="text/javascript" src="./js/proj4js2.8.1/proj4.js"></script>


<div estado="cerrado" id="cartel_consultando">
	<a id="cerrar" onclick="cerrarCartelConsultando();">cerrar</a>
	<img id="cargando" src="./comun_img/cargando.gif">
	<img id="error" src="./comun_img/error.png">
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
                <img src='./comun_img/app_raster_hd.png' style='float:left;'>
                <h2 id='titulo'>Gestor de capas complementarias de información</h2>
                <div id='descripcion'>Espacio para visualizar, explorar y descargar capas compartidas.</div>
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
        	
            <div class='capaEncabezadoCuadro cajaacciones'>
                <h1>Acciones generales para capas raster</h1>
                
				<a onclick="accionCargarNuevaCapaRaster(this)" id='botonAnadirCapa' title='Genera una capa a partir de un archivo shapefile'><img src='./img/agregar.png'> Generar Raster</a>
				<a onclick="accionMenuImportarCapaRasterPublica(this)" id='botonImportarCapaPublica' title='Genera una capa a partir de una capa pública'><img src='./img/agregar.png'>Importar capa pública</a>
				<a onclick="accionCargarDocsCandidatosRaster()" id='accionCargarDocsCandidatosRaster' title='Carga el listado de codumentos compatibles con raster'><img src='./img/agregar.png'>Buscar Documentos compatibles raster</a>
            </div>
            
            
            <div class="formSeleccionCapa" id="listadoRaster">
                <h2>Capas Raster disponibles</h2>
                
                <div class='elementoCarga accionesCapa'>
                   <!-- <a id="botonCancelarCarga"  onclick='accionCancelarSeleccionCapa(this)'>Cancelar</a></br>-->
                </div>
                
                <div id='barrabusqueda'>
					<input id='barrabusquedaI' autocomplete='off' value='-EN DESARROLLO-' onkeyup="actualizarBusqueda(event);">
					<a onclick='limpiaBarra(event);'>x</a>
				</div>
				<p id='txningunacapa'>- ninguna -</p>
				<div id='listacapaspublicadas'></div>
			
            </div>
            
            
            
            <div class="form" id="divCandidatosDocRaster">
				<div class='encabezado'>
					<h2 title="tipos previstos:\n - Sentinel2 - 2A"> Documentos <span>cargados con nombre compatibles con tipos raster previstos</span></h2>
					<a id='boton_cancela' onclick="gestionarForms('divCandidatosDocRaster','no')" class='boton_gris'>volver</a>
					<div class='botoneralocal'>
					   <!-- <a id="botonCancelarCarga"  onclick='accionCancelarSeleccionCapa(this)'>Cancelar</a></br>-->
					   <a onclick='procesarDocARaster("","","todos")' class='boton_celeste'>Procesar Todos</a>
					</div>
                </div>
                
                <div id='listado_candidatos'>
				</div>
            </div>
            
            
            <div id='formRaster' idraster='' >
                
                <h2>Raster: <span>-nombre raster-</span></h2>
                
                <a id='boton_cancela' onclick="gestionarForms('formRaster','no')" class='boton_gris'>volver</a>
				<a id='boton_elimina_raster' class='boton_rojo' onclick="eliminarContenidoRaster(this.parentNode.getAttribute('idraster'),'completo','');">Eliminar</a>
				
				<p><label>fecha medición:</label><input name="fecha" type='date'></p>
				<p><label>hora utc medición:</label><input name="hora_utc" type='time'></p>
				<p><label>polígono de cobertura:</label>
					<div class='estado'>
						<label>Localización:</label><span>estado</span>
						<span>
							<a id='nolisto' ><img src='./comun_img/procesar_off.png'></a>					
							<a id='procesar' onclick=''><img src='./comun_img/procesar.png'></a>
							<a id='hecho' ><img src='./comun_img/procesar_hecho.png'></a>
							<a id='procesando' ><img src='./comun_img/cargando.gif'></a>
						</span>
					</div>
				</p>
				<p>
					<label>documento fuente:</label><span>definido:</span>
					<br>
					<span id='iddoc'></span>
					<input name="doc_nombre" type='text' readonly='readonly'>					
				</p>
				<h3>Tipo</h3>
				<label>nombre:</label><span id='tipo_nombre'></span>
				<label>descripcion:</label><span id='tipo_descripcion'></span>
				<label>consulta:</label><a id='tipo_url_consulta' target="blank"></a>
				<h3 id='titulobandas'>
					bandas
					<a id='nolisto'><img src="./comun_img/procesar_off.png"></a>
					<a id='procesar'><img src="./comun_img/procesar.png"></a>		
					<a id='cargando'><img src="./comun_img/cargando.gif"></a>			
					<a id='hecho'><img src="./comun_img/procesar_hecho.png"></a>	
				</h3>
				<div id='bandas'>
					<p>Sin datos de bandas</p>
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
	
	var _IdUsu='<?php echo $_SESSION[$CU]["usuario"]['id'];?>';
	var _Acc = "capa";

	<?php if(!isset($_GET["idr"])){$_GET["idr"]='';} ?>	
	var _idCapa = '<?php echo $_GET["idr"];?>';
	var _CodMarco = '<?php echo $_GET["cod"];?>';


	var _DataRaster={};

    //Variable de filtro en búsquedas de datos.
    <?php if(!isset($_SESSION['geogec']['usuario']['recorte'])){$_SESSION['geogec']['usuario']['recorte']='';};?>
    
	_RecorteDeTrabajo=JSON.parse('<?php echo json_encode($_SESSION[$CU]['usuario']['recorte']);?>');
	
	
</script>


<script type="text/javascript" src="./comun_interac/comun_interac.js?t=<?php echo time();?>"></script> <!-- definicion de funcions comunes como la interpretacion de respuestas ajax-->

<script type="text/javascript" src="./sis_gral/sistema_marco.js?t=<?php echo time();?>"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./sis_gral/sis_acciones.js?t=<?php echo time();?>"></script> <!-- carga funcion de consulta de acciones y ejecución, completa _Acciones -->

<script type="text/javascript" src="./comun_mapa/comun_mapa_inicia.js?t=<?php echo time();?>"></script> <!-- definicion de variables comunes para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_link.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones para actualizar el link url directo al mapa en su configuración actual-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_recorte.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de recorte para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_selector_capas.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de selector de capa base y extras para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_localiz.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de localizacion de direcciones para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_tamano.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de agrandar mapa-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_descarga.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de descarga del mapa activo-->

<script charset="UTF-8" type="text/javascript" src="./app_raster/app_raster_js_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones de consultas ajax -->
<script charset="UTF-8" type="text/javascript" src="./app_raster/app_raster_js_mostrar.js?t=<?php echo time();?>"></script> <!-- carga funciones visualizaciones de contenidos -->
<script charset="UTF-8" type="text/javascript" src="./app_raster/app_raster_js_interaccion.js?t=<?php echo time();?>"></script> <!-- carga funciones dei interacción -->
<script charset="UTF-8" type="text/javascript" src="./app_raster/app_raster_js_mapa.js?t=<?php echo time();?>"></script> <!-- carga funciona de gestión de mapa-->

<script type="text/javascript" src="./comun_consultas/comunes_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones de interaccion con el mapa -->


<script type="text/javascript">
	
	baseMapaaIGN();//cargar mapa base IGN
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
	consultarPermisos();
	consultarListadoRaster();
	
	if(_RecorteDeTrabajo!=''){
		console.log(_RecorteDeTrabajo);
		cargaRecorteSession();
	}
	
	function reingresaGeneral(){
		consultarPermisos();
		//consultarUsuaries();		
		consultarListadoRaster();
	}
	
</script>

</body>
