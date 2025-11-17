<?php
/**
* aplicación de visualización y gestion de relevamientos de campo.
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
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <?php include("./comunes_general/meta.php");?>
    <link rel="manifest" href="pantallahorizontal.json">
    
	<link href="./comunes_css/general.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./comunes_css/general_app.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./app_rele/css/app_rele.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    
    <link href="./terceras_js/ol6.3/ol.css" rel="stylesheet" type="text/css">
        
	<link href="./sis_usuarios/usuarios.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">	
    <style>
		
		#divCargaCampa #resol_fija{
			display:none;
		}
		#divCargaCampa #resol_fija[activo='si']{
			display:block;
		}
		

    </style>
    <style id='pers_marco'>
		
		a, select, input[type="button"], input{
			<?php 
			if(isset($_SESSION[$CU]["pers"])){
				echo"
					color:".$_SESSION[$CU]["pers"]['css_color_botones'].";			
					border:".$_SESSION[$CU]["pers"]['css_border_botones'].";			
				";
			}
			?>
		}
		
		
	</style>
</head>

<body>
	
<script type="text/javascript" src="./terceras_js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./terceras_js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./terceras_js/ol6.3/ol.js"></script>

<div estado="cerrado" id="cartel_consultando">
	<a id="cerrar" onclick="cerrarCartelConsultando();">cerrar</a>
	<img id="cargando" src="./comun_img/cargando.gif">
	<img id="error" src="./comun_img/error.png">
	<div id="mensajes"> </div>	
	<div id="consultas"> </div>
</div>

<div id="pageborde">
    <div id="page">
		
		<?php include('./usuarios/usu_acceso.php');?>
				
        <div id='encabezado'>
        	
        
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
            <div id='listadoUA'>
				<h2>UAs <span>en el punto indicado</span></h2>
				<a onclick='this.parentNode.setAttribute("activo","no")' class='boton_celeste'>- cerrar -</a>
				<div id='listadito'>
				
				</div>
            </div>
            <div id='listadoUA_total' abierto='1'>
				<a onclick='this.parentNode.setAttribute("abierto",(parseInt(this.parentNode.getAttribute("abierto"))*-1))' class='boton_celeste'>ver / ocultar</a>
				<div id='listadito'>
				
				</div>
            </div>
        </div>
        <div id="cuadrovalores">
			<div id="contenido">
				<div class="formSeleccionCampa" id="divSeleccionCampa">
					<a onclick="accionCrearCampa(this)" id='botonAnadirCampa' class='boton_celeste'><img src="./img/agregar.png"> Crear Campaña </a>
					<div class='formSeleccionCampaCuerpo' id='divSeleccionCampaCuerpo'>
	                    <h1>Campañas publicadas </h1>
	                    <div id='barrabusqueda'><input id='barrabusquedaI' autocomplete='off' value='' placeholder="buscar" onkeyup="actualizarBusqueda(event);"><a onclick='limpiaBarra(event);'>x</a></div>
	                    <p id='txningunacampa'>- ninguna -</p>
	                    <div id='listacampaspublicadas'></div>
	                </div>
	            </div>
	            
	            
	            <div class='formCargaCampa' id='divCargaCampa' idcampa=''>
	                <div id='avanceproceso'></div>
	                <div class='elementoCarga accionesCampa cajaacciones'>
	                    <h1 id='titulorarlev'></h1>
	                    <a id='botoncancelaedita' onclick='cancelarEditarCampa();' class="boton_gris" >Volver</a>
	                    <a id='botonedita' onclick='editarCampa();' title="editar esta campaña"><img src='./img/editar.png'></a>
	                    <div id='accionesCampa'>
							<a class="boton_gris" onclick='cancelarEditarCampa()' class="boton_celeste">Cancelar</a>
							<a class='boton_rojo' id='botonelim' onclick='eliminarCampa();' title="Eliminar Campa">Eliminar</a>
							<a class="boton_celeste" id='botonguarada' onclick='guardarCampa(this.parentNode); guardarSLD(this.parentNode);' title="guardar esta campaña preliminarmente">Guardar</a>
							<a class="boton_celeste" id='botonguarada' onclick='document.querySelector("#divReleACapa").style.display="block";' title="generar una capa a partir de este relevamiento">Generar Capa a partir de este relevamiento</a>
							<a class="boton_celeste" id='botonduplica' onclick='duplicarCampa()'>Duplicar Campaña</a>
							<a class="boton_celeste" id='botoncampo' onclick='editarCampos()'>Editar campos</a>
	                    </div>
	                    <p id='resol_fija'>resolución fija:<span name='val'></span> m/px</p>
	                </div>
					
					<div style='display:none;' class='formReleACapa' id='divReleACapa' idcapa='' >
                				
		                <div class='formReleACapaCuerpo' id='ReleACapa' estado='inactivo'>
		                	<div id='campos'>
		                		<p>campo1 nombre:<input name='nombrec_1'> fuente:<select name='fuentec_1'></select></p>
		                		<p>campo2 nombre:<input name='nombrec_2'> fuente:<select name='fuentec_2'></select></p>
		                		<p>campo3 nombre:<input name='nombrec_3'> fuente:<select name='fuentec_3'></select></p>
		                		<p>campo4 nombre:<input name='nombrec_4'> fuente:<select name='fuentec_4'></select></p>
		                		<p>campo5 nombre:<input name='nombrec_5'> fuente:<select name='fuentec_5'></select></p>                		
		                	</div>    
		                	<input type='submit' value='generar capa' onclick='event.preventDefault();ReleACapa()'>
		                </div>
		            </div>
		            
	                <div class='formCargaCampaCuerpo' id='edicionCampa'>
	                    <div id='nombrecampa' class='elementoCarga'>
	                        <h2>Nombre de la campaña</h2>
	                        <input type="text" id="campaNombre" class="largo"></input>
	                    </div>
	                    <div id='desccampa' class='elementoCarga'>
	                        <h2>Descripción</h2>
	                        <textarea type="text" id="campaDescripcion" class="largo"></textarea>
	                    </div>
	                    <div id='desccampa' class='elementoCarga'>
	                        <h2>Carga pública</h2>
	                        <input type="checkbox" id="campaZz_carga_publica">
	                    </div>
	                    
	                    
	                    <div id='desccampa' class='elementoCarga'>
	                        <h2>Unidad de Análisis</h2>
	                        <label>Nombre</label><input name='unidadanalisis' value='' class="medio"><br>
	                        <label>Tipo</label><select name='tipogeometria' class="medio">
                        		<option value=''>- elegir -</option>
					  			<option value="Point">Puntos</option>
					  			<option value="LineString">Lineas</option>
					  			<option value="Polygon">Polígonos</option>
					  		</select>
	                    </div>
		                
		                
	                    <div id='simbologia' class='elementoCarga'>
	                        <h2>Símbolo por defecto</h2>
	                        <label class='l1'>color de relleno</label>
	                        <input onchange='cargarFeatures()' type="color" id="inputcolorrelleno"/>
	                        <label class='l2'>transparencia de relleno</label>
	                        <input onchange='cargarFeatures()' id="inputtransparenciarellenoNumber" min="0" max="100" oninput="inputtransparenciarellenoRange.value=inputtransparenciarellenoNumber.value" type="number" style="width: 10%">
	                        <input onchange='cargarFeatures()' id="inputtransparenciarellenoRange" min="0" max="100" oninput="inputtransparenciarellenoNumber.value=inputtransparenciarellenoRange.value" type="range" style="width: 25%">
	                        <label class='l1'>color de trazo</label>
	                        <input onchange='cargarFeatures()' type="color" id="inputcolortrazo"/>
	                        <label class='l2'>ancho de trazo (pixeles)</label>
	                        <input onchange='cargarFeatures()' id="inputanchotrazoNumber" min="0" max="10" oninput="inputanchotrazoRange.value=inputanchotrazoNumber.value" type="number" style="width: 10%">
	                        <input onchange='cargarFeatures()' id="inputanchotrazoRange" min="0" max="10" oninput="inputanchotrazoNumber.value=inputanchotrazoRange.value" type="range" style="width: 25%">
	                        <h2>Reglas adicionales</h2>
							<a onclick='document.querySelector("#vacio").innerHTML=""; anadirReglaSLD(this.parentNode);'><img src="./img/agregar.png">regla</a>
							<div id="listadoreglas"><p id="vacio">Sin reglas adicionales</p></div>
	                    </div>
	                </div>
	            </div>
	            
	            
	            
	            <form class='cajaacciones' id='FormularioNuevaUA' onsubmit='event.preventDefault();enviarDatosRegistro()'()>
					<h2>Gestionar UAs <span>Unidades de Análisis</span></h2>
					<div id='botones'> 
						<a onclick="enviarCreaRegistroCapa()">
							<img src='./img/agregar.png'>
							 UA, dibujar
						</a>
						
						<div id='inout_botones' activo='-1'>	
							
							<a id='activar_borrar' abridor='si' onclick='activabotones("inout_botones");' title="opciones de importación y exportación">
								<img src='./img/subir_archivo.png'>
								importar y exportar UAs
							</a>
							<a  class='boton_gris' id='cerrar_borrar' onclick='activabotones("inout_botones");' title="opciones de borrar">
								cerrar
							</a>
							
							<a title="Subir archivo xlsx conteniendo datos para las UA definidas" onclick="gestionarForms('FormularioSubirPlanilla','si')">
								<img src='./img/subir_archivo.png'>
								 UAs + datos desde .xlsx
							</a>
							<a id='botoncargar' onclick="gestionarForms('cargarGeometrias','si');" title="UA desde shp o dxf">
								<img src='./img/subir_archivo.png'>
								UAs desde .dxf o .shp
							</a>
							<a onclick="descargarMapaDXF()">
								<img src='./img/descargar_archivo.png'>
								 descargar captura .dxf
							</a>	
						</div>
						<div id='borrar_botones' activo='-1'>	
							
							<a id='activar_borrar' abridor='si' onclick='activabotones("borrar_botones");' title="opciones de borrar">
								<img src='./img/eliminar_16.png'>
								borrar UAs...
							</a>
							<a class='boton_gris' id='cerrar_borrar' onclick='activabotones("borrar_botones");' title="opciones de borrar">
								cerrar
							</a>
							<a id='botoncargar' onclick='borrarGeometrias("propios");' title="borrar mis geometrías en esta campaña de relevamiento">
								<img src='./img/eliminar_16.png'>
								borrar mis UAs
							</a>
							<a id='botoncargar' onclick='borrarGeometrias("todos");' title="borrar todas las geometrías de esta campaña de relevamiento">
								<img src='./img/eliminar_16.png'>
								borrar todas las UAs
							</a>
							<a id='botoncargar' onclick='borrarDatosGeometrias();' title="borrar todos los registros vinculados a esta cmapala de relevamiento dejando las UA">
								<img src='./img/eliminar_16.png'>
								borrar datos de todas UA
							</a>
						</div>				
	            	</div>
	            	
	            	<div id='selectorarchivo' modo='inactivo'>
						<span id='titulo'>Registros archivados</span>
						<div id='historicos'></div>
						<a class="historico" id_reg_hist='actual' onclick="cargarRegistroHistorico('actual')" selecto='si'>registro<br>actual</a>
	            	</div>
	            </form>
	            
	            <div id='cargarGeometrias'  class='elementoCargaLargo'>                 
					<h2>Cargar geometrías</h2>   	
					<a id="cerrar" class='boton_gris' onclick="gestionarForms('cargarGeometrias','no');">cerrar</a>
					<div id='earchivoscargando' class='elementoCarga'>						
						<div id='archivosacargar'>
							<form id='shp' enctype='multipart/form-data' method='post' action='./ed_ai_adjunto.php'>			
								<label style='position:relative;' class='upload'>							
								<span id='upload' style='position:absolute;top:0px;left:0px;'>arrastre o busque aquí un archivo (shp o dxf)</span>
								<input id='uploadinput' style='opacity:0;' type='file' multiple name='upload' value='' onchange='enviarArchivosSHPDXF(event,this);'></label>
								<div id='earchivoscargados' class='elementoCarga'>
									<p>archivos cargados</p>
									<p id='txningunarchivo'>- ninguno -</p>
									<div id='cargando'></div>
								</div>
								
								<select id='crs' onchange='ValidarProcesarBotonSHP()' title="el scr es el sistema de coordenadas con el cual se dibujó. &#10; Si está subiendo un dxf descargado de este módulo, elija 3857.">
									<option value=''>- elegir src-</option>
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
								
								<a class='boton_celeste' id='procesarBotonSHP' onclick='alert("en desarrollo");return;procesarCampaSHP(this.parentNode)' estado='inviable'>
									Procesar Shapefile
								</a>
								<a class='boton_celeste' id='procesarBotonDXF' onclick='procesarCampaDXF(0);' estado='inviable'>
									Procesar DXF
								</a>
							</form>
							
						</div>
					</div>
				</div>
	            
	            
	            <form class='elementoCargaLargo' id='FormularioSubirPlanilla' onsubmit='event.preventDefault()'>
					<h2>Subir UAs y datos desde archivo .xlsx</h2>
					<a id="cerrar" class='boton_cancela' onclick="gestionarForms('FormularioSubirPlanilla','no');">cerrar</a>
					
					<div>
						<label style='position:relative;' class='upload'>							
						<span id='upload' style='position:absolute;top:0px;left:0px;'>arrastre o busque aquí un archivo (xlsx)</span>
						<input id='uploadxlsxinput' style='opacity:0;' type='file' multiple name='uploadxlsx' value='' onchange='enviarArchivoXLSX(event,this);'></label>
						<div id='archivosXLSXcargados' class='elementoCarga'>
							<p>archivo cargado:</p>
							<p id='txarchivocargado'>- ninguno -</p>		
							<div id='cargando'></div>
						</div>
					</div>
					
					
					<input type='hidden' name='filename'>
					<div id='asignanombre'>
						<label>nombre UA</label>
						<select name='nombreua'></select>
					</div>
					
					<div id='modo' modo_selecto=''>
						
						<select name='modo_importacion' onchange="cambioModo()">
							<option value='asignar'>Asignar datos a geometrías ya disponibles</option>
							<option value='geo_capa'>Generar geometrías a partir de elementos en una capa</option>
							<option value='geo_coord'>Generar geometrías a partir coordenadas en planilla</option>
							<option value='geo_wkt'>Generar geometrías a partir WKT en planilla</option>						
						</select>
						
						
						<div id='elegircapa'>	
							<div>
								<label>capa con UAs</label>
								<select onchange="cambioImportarCapaGeom()" name='capa_fuente'>
									<option>capa 1</option>						
								</select>
							</div>	
							
							<div>
								<label>campo link en capa</label>
								<select name='campo_link_en_capa'>
								</select>
							</div>	
							<div>
								<label>columna con link en .xlsx</label>
								<select name='columna_link_en_planillla'>
								</select>
							</div>	
						</div>
							
							
						<div id='elegircamposcoords'>	
							<div>
								<label>columna con latitud</label>
								<select name='campo_latitud'>
									<option>columna 1</option>						
								</select>
							</div>	
							<div>	
								<label>columna con longitud</label>
								<select name='campo_longitud'>
									<option>columna 1</option>						
								</select>
							</div>
						</div>
						
						
						<div id='elegircamposgeom'>	
							<div>	
								<label>columna con geometría WKT</label>									
								<select name='columna_wkt_en_planillla'>
									<option>columna 1</option>						
								</select>
							</div>
							<div>	
								<label>Src WKT</label>									
								<select name='srid_wkt'>
									<option value='4326'>4326</option>						
									<option value='3857'>3857</option>						
								</select>
							</div>
						</div>
						
							
							
						<div id='modo_asignar'>
							<div id='asignacampos'>							
							</div>		
						</div>
							
												
						<label>generar nuevos campos con fila 1</label><input type='checkbox' onchange="cambiaModoCampos()" name='check_crear_campos'>
						
						<div id='crear_campos'>
							<div id='listado_campos'>							
							</div>		
						</div>
						
						<br>
						<a onclick='procesarPlanillaXLSX()' class='boton_celeste'>
							<img src='./img/procesar.png'>
							procesar
						</a>
											
					</div>
					
					
	            </form>
				
	            <form class='elementoCargaLargo' id='FormularioRegistro' onsubmit='event.preventDefault()'>
	            	
	            	<h2>
						UA <span>consultada</span>
						<input type='hidden' name='id_registro'>
						<span class='aux'>#:<input name='idgeom' readonly='readonly'></span>
						<div id='superficie' title='superficie en esferoide WGS84'><span></span> ha</div>
						<a id='b_elim' onclick='borrarGeometrias("registro",this.parentNode.querySelector("[name=\"idgeom\"]").value)' title='borrar UA'><img src='./img/eliminar_16.png'></a>
						<input type='hidden' idgeom='' id='nuevageometria'>
						<a id='b_redib' onclick='cambiarGeometria(this.parentNode.querySelector("[name=\"idgeom\"]").value)' title="dibujar / redibujar geometría"><img src='./img/dibujar.png'></a>
						
						<label class="switch">
							  <input type="checkbox" onchange="if(this.checked){activarModificarGeometria();}else{desactivarModificarGeometria()}">
							  <span class="slider round"></span>
							  <div class='boton_compuesto' title="activar edición por vértice"><img src='./img/editar_vertices.png'></div>
						</label>
						
					</h2>
					<a id="cerrar" class='boton_cancela' onclick="gestionarForms('FormularioRegistro','no');">cerrar</a>
					<div id='autoria'>por: <span id='usu'></span><br><span id='fecha'></span></div>
	                <div class='campo' id='sect1'><label>nombre</label>:<input name='t1'></div>
	                
	                
	                <div id='campospersonalizados'></div>
	                <div>
	                	Datos completos UA: 
	                	<input type='checkbox' for='n1' valorsi='1' valorno='0' onchange='toglevalorSiNo(this)'>
	                	<input type='hidden' name='n1' value='0' onchange='toglevalorSiNoRev(this)'>
	                </div>
	                
	                <input 
						type='button' 
						value='guardar registro'
						onclick='enviarDatosRegistro();'
					>
	                
	                <input 
						type='button' 
						title='al archivar un registro, este queda referido a una fecha específica y permite cargar otros registros para una nueva UA.'  
						id='botonarchivar' 
						onclick='enviarDatosRegistro("si")' 
						value='guardar registro y archivar'
					>
	                <input type='button'  id='botondesarchivar' onclick='enviarDatosRegistro();archivarRegistro(0)' value='Desarchivar'>
	                	
	            </form>
	            
	            <form id='campos'>
					<h2>Campos Existentes</h2>
					<a onclick='cancelarCamposExistentes()'>X</a>
					<a id='botoncampo' onclick='nuevoCampo()'>+ Añadir campo</a>
					<div id='listadecampos'>
						Sin Campos Disponibles
					</div>
					
	            </form>
					
	            <form id='nuevocampo'>
	            	<h2>Nuevo Campo</h2>
	            	<div id='accionescampo'>
						<a onclick='guardarCampo(this)'><img src='img/disquito.png'></a>
						<a onclick='eliminarCampo(this)' class='elimina'>Eliminar</a>
						<a id='botoncancelacampo' onclick='cancelarCampo(this)'>X</a>
	            	</div>
	            	<br>
	            	<input name='idcampo' type='hidden' value=''>
	            	<label>Nombre:</label><input name='nombre'>
	            	<label>Ayuda:</label><textarea name='ayuda'></textarea>
	            	<label>Tipo:</label><select name='tipo' onchange='cambiaTipoCampo(this)'>
	            		<option>- elegir -</option>
	            		<option value='texto'>texto</option>
	            		<option value='checkbox'>checkbox</option>
	            		<option value='select'>menu desplegable</option>
	            		<option value='numero'>numero</option>
	            		<option value='fecha'>fecha</option>
	            		<option value='coleccion_imagenes'>imagenes</option>
	            	</select>
	            	
	            	<div para='select'>
						<label>opciones (separar con salto de línea):</label><textarea class='chico' name='opciones_select'></textarea>
	            	</div>
	            	
	            	<div para='numero'>
						<label>Unidad de Medida:</label><input name='unidademedida'>	            	
	            	</div>
	            	<div para='fecha'>
						<label>fecha de archivado:</label><input type='checkbox' name='es_fecha_archivo'>	            	
	            	</div>
	            	<label >en tabla</label>
					<input para='matriz' type='checkbox' onchange='toogleCheck(this);cambiaMatrizCampo(this)'><input name='matriz' type='hidden' value='-1'>
	            	<div para='matriz'>
	            		<label class=''>Nombre tabla: <br><span class='aux'> (igual en todos sus campos)</span></label><input name='nombre_matriz'><br>
	            		<label>Nombre columna:</label><input name='nombre_columna'>
	            		<label>Nombre fila:</label><input name='nombre_fila'>	            		
	            	</div>	
	            </form>
	       </div>     
	      
        </div>
    </div>
</div>

<div id='muestradocumento' estado='inactivo'>
	<a class='boton_celeste' onclick='cerrarMostrarFoto()'>cerrar</a>
	<a id='b_descarga' class='boton_celeste'>descargar <img src='./img/descargar_archivo.png'></a>
	<div id='contenido'>
		<img>
	</div>
</div>

<script type="text/javascript">

	var _ConsultasPHP = {
		"cons_real" : 0,  //cantidad de consultas realizadas hasta el momento   
		"cons_resp" : 0,  //cantidad de consultas respondidas hasta el momento
		"consultas" : {},
		"mensajes" : []
	};
	
	var _Acc = "rele";
	var _IdUsu='<?php echo $_SESSION["geogec"]["usuario"]['id'];?>';
	
	<?php if(!isset($_GET["idr"])){$_GET["idr"]='';} ?>
	var _idCampa = '<?php echo $_GET["idr"];?>';
	var _idRele = '';//DEPRECADO		
	//Variable de filtro en búsquedas de datos.
    <?php if(!isset($_SESSION[$CU]['usuario']['recorte'])){$_SESSION[$CU]['usuario']['recorte']='';};?>
	var _RecorteDeTrabajo=JSON.parse('<?php echo json_encode($_SESSION[$CU]['usuario']['recorte']);?>');
	
	
	//funciones para consultar datos y mostrarlos
	var _Tablas={};
	var _TablasConf={};
	var _SelecTabla='';//define si la consulta de nuevas tablas estará referido al elmento existente de una pabla en particular; 
	var _SelecElemCod=null;//define el código invariable entre versiones de un elemento a consultar (alternativa a _SelElemId);
	var _SelecElemId=null;//define el id de un elemento a consultar (alternativa a _SelElemCod);


	var _IdMarco = getParameterByName('id');
	var _CodMarco = getParameterByName('cod');	
	var _DataUsuaries={};

	var _Features={};
	var _DataRele={};
	var _DataCapa=Array();
	var _DataRegistro={}; //archivo los datos vigentes de registro para una geometría (actuales e históricos);
	
	var _DataFormAgurp = {};
	var _ColumnasNumericasUsadas = [];
	var _ColumnasTextoUsadas = [];
	
	var _DataGeomOrden = Array();

	
</script>

<script type="text/javascript" charset="UTF-8" src="./comun_interac/comun_interac.js?t=<?php echo time();?>"></script> <!-- definicion de funcions comunes como la interpretacion de respuestas ajax-->

<script type="text/javascript" src="./sis_gral/sistema_marco.js?t=<?php echo time();?>"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./sis_gral/sis_acciones.js?t=<?php echo time();?>"></script> <!-- carga funcion de consulta de acciones y ejecución, completa _Acciones -->

<script type="text/javascript" src="./comun_mapa/comun_mapa_inicia.js?t=<?php echo time();?>"></script> <!-- definicion de variables comunes para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_link.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones para actualizar el link url directo al mapa en su configuración actual-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_recorte.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de recorte para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_selector_capas.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de selector de capa base y extras para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_localiz.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de localizacion de direcciones para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_tamano.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de agrandar mapa-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_descarga.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de descarga del mapa activo-->

<script type="text/javascript" src="./app_rele/app_rele_mapa.js?t=<?php echo time();?>"></script> <!-- carga funciona de gestión de mapa-->
<script type="text/javascript" src="./app_rele/app_rele_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_rele/app_rele_pagina.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_rele/app_rele_uploads.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion del formulario central para la carga de SHP -->
<script type="text/javascript" src="./comun_consultas/comunes_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones de interaccion con el mapa -->

<script type="text/javascript">
	
	cargaBaseLink(); //en comun_mapa_link.js
	inicializarColumnas(); //en app_rele_pagina.js define variables en estado false.

	if(_IdUsu<"1"){
		formUsuario("accede");	
	}else{ 
		consultarPermisos();
		consultarUsuaries();
		cargarListadoCampa();
		consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
		if(_RecorteDeTrabajo!=''){cargaRecorteSession();}
		if(_idRele!=''){cargarDatosCampa(_idCampa);}
	}
	
	function reingresaGeneral(){
		console.log(_UsuarioA);
		_IdUsu=_UsuarioA.id;
		
		console.log('ui');
		cargarListaCapas();// en comun_mapa_selector_capas.js
		cargarCapaMarco();// en app_rele_mapa.js
		
		consultarPermisos();
		consultarUsuaries();
		
		cargarListadoCampa();
		if(_idCampa > 0){
			cargarDatosCampa(_idCampa);
		}
				
		consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
		if(_RecorteDeTrabajo!=''){cargaRecorteSession();}
		if(_idRele!=''){cargarDatosCampa(_idCampa);}
	}
</script>

</body>s
