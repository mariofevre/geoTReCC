<?php
/**
* aplicación de visualización y gestion publicaciónes oficiales compartidas
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
    
    <link href="./css/geogecgeneral.css?v=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app.css?v=<?php echo time();?>" rel="stylesheet" type="text/css">    
    <link href="./css/geogec_app_publ.css?v=<?php echo time();?>" rel="stylesheet" type="text/css">
    
    <style>
    </style>
</head>

<body onkeydown="tecleoGeneral(event)">   
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
		<?php include('./sis_usuarios/usu_acceso.php');?>
		
        <div id='encabezado'>
		<a href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
                <h2><?php echo $CONF['plataforma']['nombre'];?></h2>
                <p><?php echo $CONF['plataforma']['descrip'];?></p>
            </a>


            <div id='elemento' tipo='Accion'>
                <img src='./img/app_publ_hd.png' style='float:left;'>
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
            <div id="wrapper">
                <div id="location"></div>
                <div id="scale"></div>
            </div>
            <div id='botonera_mapa'></div>
            
        </div>
        <div id="cuadrovalores" 
        
			formSeleccionPublicacion = "si"
			divSeleccionPubl = "si"			
			formEditarPublicaciones = "no"
			formCargaPubl = "no"
		>
		
			<div class='cajaacciones'>
				 <a onclick="accionCargarNuevaPubl(this)" 
				 id='botonAnadirPubl' 
				 class= "boton_celeste"
				 >
					<img src='./img/agregar.png'>Compartir publicación
				</a>
			</div>    
			           
            <div class="formSeleccionPublicacion" id="divSeleccionPubl">
                <h1>Publicaciones compartidas</h1>
                <div id='PublActiva' idindicador='0' class="elementoOculto"></div>
                <div class='formSeleccionPublicacionCuerpo' id='divSeleccionPublCuerpo'>
                    <div id='barrabusqueda'>
                    	<input id='barrabusquedaI' autocomplete='off' value='' onkeyup="actualizarBusqueda(event);">
					</div>                    	
                    <div id='listapublicaciones'></div>
                </div>
            </div>
            
            
            
                       
            
            <div id='formCargaPubl'>
				 
				<h1 id='tituloPubl'></h1>
	            	
				<a id='botonCancelar' onclick='accionVolverAlListado();' class="boton_gris">Volver</a>
				<a id='botonEditar' onclick='accionIniciarEdicion();' class="boton_celeste" title="editar este indicador"><img src='./img/editar.png'></a>	
				
				<p id='autoriaPubl'></p>
				<p id='tipoPubl'></p>
				<p id='obsPubl'></p>
				<p id='webPubl'></p>
				<p id='fechaPubl'></p>
				<p id='deparPubl'></p>
				<a id='archivoPubl' onclick="descargarArchivo(this)">
					
				</a>
				
			</div>
			
			
			
            <div id='formEditarPublicaciones' idPubl='0' class="elementoOculto" modo='off'>
            	
	            <div class="menuAcciones elementoCarga" id="divMenuAccionesCrea">	
					<a id='botonCancelarEdita' onclick='cancelarEditaPubl();' class="boton_gris">Cancelar</a>				
	                <a onclick='accionEliminar(this)' id="botonEliminar" class='boton_rojo'>Eliminar</a>
	                <a onclick='accionCreaGuardar(this)' id="botonGuardar" class='boton_celeste'>Guardar</a>
	            </div>
            	
            	<div id="gestorarchivos">
            		<input id='publ_DOC' type='hidden' value='' name='id_p_ref_01'>
            		<div id="documento" onclick='descargarArchivo(this)' ruta=''>
            			<img src='./img/app_publ.png'>
            			<span id='txdoc'></span>
		            </div>
		            
		            <div id='edicion'>
	            		<a  id='botonDeactivaGestorarchivos' style="display:none;" onclick="deactivarGestorarchivos();" >cancelar</a>
	            		<a onclick='activarUploader()' id='botonActivaUploader' class='boton_celeste'>Subir Archivo</a>
	            		
		            	<form action='' enctype='multipart/form-data' method='post' id='uploader' ondragover='resDrFile(event)' ondragleave='desDrFile(event)'>
			                <div id='contenedorlienzo'>									
			                    <div id='upload'>
			                        <label>Arrastre archivo aquí.</label>
			                        <input id='uploadinput' type='file' name='upload' value='' onchange='cargarCmp(this);'>
			                    </div>
			                </div>
			            </form>
			            
			            <a onclick='activarSelectorDocs()' id='botonActivaSelectorDocs' class='boton_celeste'>Elegir archivo cargado</a>
			            <div id="listadoDocumentos">
			            </div>
		            </div>
		            		            		
            	</div>
            	
                <div id='titulo' class='elementoCarga'>
                    <h1>Título de la Publicación</h1>
                    <input type="text" id="pubTitulo" name='titulo'></input>
                </div>
                <div id='Autoria' class='elementoCarga'>
                    <h1>Autoría:</h1>
                    <input type="text" id="publAutoria" name='autoria'></input>
                </div>
                <div id='Tipo' class='elementoCarga'>
                    <h1>Tipo de documento:</h1>
                    <select id="publTipo"  name='id_p_ref_publ_tipos'></select>
                </div>

                <div id='observaciones' class='elementoCarga'>
                    <h1>Observaciones:</h1>
                    <textarea id='publObservaciones' name='observaciones'></textarea>
                </div>
                              
                <div id='Url' class='elementoCarga'>
                    <h1>Web oficial (o donde la encontraste):</h1>
                    <input type="text" id="publUrl" name='url'  onclick='consultarUrl(this)' ></input>
                </div>
                <div id='fecha' class='elementoCarga'>
                    <h1>Fecha de publicacion:</h1>
                    Año: <input id="publAnio" type='number' name='ano'></input>
                    Mes: <input id="publMes" type='number' name='mes'></input>
                </div>
                
                <div id='departamentos'  class='elementoCarga'>
                    <h1>Departamientos/partidos:</h1>
                    <div id='listadodepartamentosSelectos'></div>
                    
                    <div id='selectorDepartamentos'>
	                    <label>buscar: </label><input type="text" id="publBuscarDepto" onkeyup="accionBuscardpto(event, this)"></input>                    
	                    <div id='listadodepartamentos'></div>
	                </div>
	                
                </div>
                
                <div class='elementoCarga'>
                    <h1>Area</h1>
                    <a id='dibujarArea' onclick='dibujarAreaEnMapa()' class='boton_celeste'>Dibujar Area de Estudio</a>
                     <input type="hidden" id="publArea" name='areatx'></input>
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
	var _Acc = "publ";
	
	<?php 
	if(isset($_GET['id'])){$ID=$_GET['id'];}else{$ID='';}
	if(isset($_GET['cod'])){$COD=$_GET['cod'];}else{$COD='';}	
	?>
    var _IdMarco='<?php echo $ID;?>';
    var _CodMarco='<?php echo $COD;?>';	
    
	var _DataUsuaries={};
		
	var _Data_Publ = {};
</script>

<script type="text/javascript" src="./comun_interac/comun_interac.js?t=<?php echo time();?>"></script> <!-- definicion de funcions comunes como la interpretacion de respuestas ajax-->

<script type="text/javascript" src="./sistema/sistema_marco.js?v=<?php echo time();?>"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./sistema/sis_acciones.js?v=<?php echo time();?>"></script> <!-- funciones de consulta general del sistema: acciones -->

<script type="text/javascript" src="./comun_mapa/comun_mapa_inicia.js?t=<?php echo time();?>"></script> <!-- definicion de variables comunes para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_recorte.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de recorte para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_selector_capas.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de selector de capa base y extras para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_localiz.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de localizacion de direcciones para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_tamano.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de agrandar mapa-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_descarga.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de descarga del mapa activo-->

<script type="text/javascript" src="./app_publ/app_publ_consultas.js?v=<?php echo time();?>"></script> <!-- carga funciones de consulta de base de datos -->
<script type="text/javascript" src="./app_publ/app_publ_pagina.js?v=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_publ/app_publ_mapa_funciones.js?v=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./comunes_consultas.js?v=<?php echo time();?>"></script> <!-- carga funciones de interaccion con el mapa -->


<script type="text/javascript">
	
	
	baseMapaaIGN();//cargar mapa base IGN
	
	
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
</script>

</body>
