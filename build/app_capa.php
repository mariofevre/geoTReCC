<?php
/**
* aplicación de gestión de capas vectoriales tipo shapefile.
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
    <?php include("./comun_general/meta.php");?>
    <link rel="manifest" href="pantallahorizontal.json">
    
    <link href="./comun_css/general.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./comun_css/general_app.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./comun_css/app_capa.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    
    <link href="./terceras_js/ol6.3/ol.css" rel="stylesheet" type="text/css">
    <style>
    	
    </style>
</head>

<body>
	
<script type="text/javascript" src="./terceras_js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./terceras_js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./terceras_js/ol10.1/ol.js"></script>

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
                <img src='./comun_img/app_capa_hd.png' style='float:left;'>
                <h2 id='titulo'>Gestor de capas vectoriales de información</h2>
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
            <div class='cajaacciones'>
				<a onclick="accionCargarNuevaCapa(this)" class="boton_celeste" id='botonAnadirCapa' title='Genera una capa a partir de un archivo shapefile'><img src='./img/agregar.png'> Subir shapefile</a>
				<a onclick="accionReleaCapa(this)" class="boton_celeste" id='botonReleaCapa' title='Genera una capa a partir del m{odulo de relevamientos'><img src='./img/agregar.png'> Importar Relevamiento</a>
				<a onclick="accionMenuImportarCapaPublica(this)" class="boton_celeste" id='botonImportarCapaPublica' title='Genera una capa a partir de una capa pública'><img src='./img/agregar.png'>Importar capa pública</a>
            </div>
            
            <div class="formSeleccionCapa" id="divSeleccionCapa">
                <div class='elementoCarga accionesCapa'>
                   <!-- <a id="botonCancelarCarga"  onclick='accionCancelarSeleccionCapa(this)'>Cancelar</a></br>-->
                </div>
                <div class='formSeleccionCapaCuerpo' id='divSeleccionCapaCuerpo'>
                    <h1>Capas publicadas</h1>
                    <div id='barrabusqueda'><input id='barrabusquedaI' autocomplete='off' value='' placeholder="buscar" onkeyup="actualizarBusqueda(event);"><a onclick='limpiaBarra(event);'>x</a></div>
                    <p id='txningunacapa'>- ninguna -</p>
                    <div id='listacapaspublicadas'></div>
                </div>
            </div>
            
            <div style='display:none;' class='formReleACapa' id='divReleACapa' idcapa='' >
                				
                <div class='formReleACapaCuerpo' id='ReleACapa'>
                    <div id='idcampa' class='elementoCarga'>
                        <h1>Campaña de relevamiento</h1>
                        <select name='idcampa'>
                        	<option value=''>-elegir-</option>	
                        </select>
                    </div>
                	<div id='campos'>                		
                		<p>campo1 nombre:<input id='nombre'> fuente:<select name='fuentec1'></select></p>
                		<p>campo2 nombre:<input id='nombre'> fuente:<select name='fuentec1'></select></p>
                		<p>campo3 nombre:<input id='nombre'> fuente:<select name='fuentec1'></select></p>
                		<p>campo4 nombre:<input id='nombre'> fuente:<select name='fuentec1'></select></p>
                		<p>campo5 nombre:<input id='nombre'> fuente:<select name='fuentec1'></select></p>               		
                	</div>    
                </div>                
            </div>
            
            
			<div class='formTitulo' id='formTitulo' idcapa=''>    
					<h1 id='titulo'></h1>			              
            </div>
                    
            <div class='formCargaCapa' id='divCargaCapa' idcapa='' edicion_gral_activa='no'  edicion_shp_activa='no'>
                <div id='avanceproceso'></div>
                <div class='accionesCapa'>
					<h1>Acciones para subir shapefile</h1>
					<a id='botoncancelaedita'onclick='accionCancelarCargarNuevaCapa(this);' class="boton_gris">Volver</a>
                    <div class='cajaacciones'>
						<a onclick='accionCancelarCargarNuevaCapa(this)' class="boton_celeste"><img src='./img/boton_listado.png'>Volver al listado de capas</a>
						<a id='botonexporta' onclick='exportarCapaMenu();' class="boton_celeste"><img src='./img/dirigir_link.png'>Exportar a otro proyecto</a>
						<a id='botonestadisticas' onclick='consultarEstadisticas();' class="boton_celeste">Ver estadísticas</a>
						<a id='botonelim' onclick='eliminarCandidatoCapa(this.parentNode);' title="Eliminar Capa" class="boton_rojo"><img src='./img/icon-delete-16.jpg'> Eliminar Capa</a>
						<a id='botonelimreg' onclick='eliminarRegistrosCapa();' title="Eliminar Registros de la Capa" class="boton_rojo"><img src='./img/icon-delete-16.jpg'> Eliminar registros</a>
						<a id='botonguarada' onclick='guardarCapa(this.parentNode);' title="guardar esta capa preliminarmente" class="boton_celeste"><img src='./img/disquito.png'> Guardar cambios</a>
						<a id='botonpublica' onclick='publicarCapa(this.parentNode);' class="boton_celeste"><img src='./img/procesar.png'>Publicar</a>
                    </div>
                </div>
				
				<div id='exportarCapaMenu'>
					<a onclick='this.parentNode.style.display="none"'>Cancelar</a>
					<h2>Seleccione un proyecto al cual copiar esta capa</h2>
					<div id='lista'>							
					</div>	
				</div>
				 
                <div class='formCargaCapaCuerpo' id='carga'>
					<a id='b_editar_gral' onclick='this.parentNode.parentNode.setAttribute("edicion_gral_activa","si");' class="boton_celeste">Editar Aspectos generales</a>
					<a id='b_editar_gral_off' onclick='this.parentNode.parentNode.setAttribute("edicion_gral_activa","no");' class="boton_gris">Cerrar Edición de aspectos generales</a>
							
                    <div id='nombrecapa' class='elementoCarga'>
                        <h2>Nombre de la capa <div id='cantreg'>registros:<span id='contador'></span></div></h2>
                        <input type="text" id="capaNombre" onkeypress="editarCapaNombre(event, this)"></input>
                    </div>
                    
                    <div id='desccapa' class='elementoCarga'>
                        <h2>Descripción</h2>
                        <textarea type="text" id="capaDescripcion" onkeypress="editarCapaDescripcion(event, this)"></textarea>
                    </div>
                    
					<div id='tipo_fuente' class="elementoCarga">						
						<p><span style='cursor:help' title='El modo WMS resta interacción a la capa pero permite cargar más registros (capas más pesadas) sin comprometer los recursos del cliente.'>modo WMS </span> 
						<label class="switch">
						  <input type="checkbox" for='modo_defecto' valorsi="wms" valorno="vectorial" onclick="toglevalorSiNo(this)">
						  <span class="slider round"></span>
						  <input type="hidden" name='modo_defecto' onchange='toglevalorSiNoRev(this)'>
						</label>
						</p>	
												
						<p>Información pública	                    
						  <select name='modo_publica'>
						  	<option value=''>-elegir-</option>	
						  	<option value='personal'>Información preliminar</option>
						  	<option value='gec'>Publicada para otros equipos propios</option>
						  	<option value='publica'>Pública</option>
						  </select>						
						</p>	
												
	                    <p> Origen del dato
						  <select name='tipo_fuente'>
						  	<option value=''>-elegir-</option>	
						  	<option value='propia'>Datos propio</option>
						  	<option value='derivada'>Datos derivados de otra fuente</option>
						  	<option value='externa'>Datos tomados de otra fuente</option>
						  </select>						
						</p>
												
						<p>Geometría	                    
						  <select onchange='actualizarOpcionesGeometria()' name='tipogeometria'>
						  	<option value=''>-elegir-</option>	
						  	<option value='Point'>Point</option>
						  	<option value='LineString'>LineString</option>
						  	<option value='Polygon'>Polygon</option>
						  	<option value='Tabla'>Tabla sin geometrías</option>
						  </select>						
						</p>								
								
						<p id='vinculaciones'>Vincular a geometrías de otra capa<br>						
							<a onclick='consultarCapasLinkeables()'>Elegir capa externa</a><input type='hidden' name='link_capa'><span id='muestra_link_capa'></span><br>
							<a onclick='consultarCamposExternosLinkeables()'>Elegir campo externo de viculación</a><input type='hidden' name='link_capa_campo_externo'><span id='muestra_link_capa_campo_externo'></span><br>
							<a onclick='mostrarCamposLocalesLinkeables()'>Elegir campo local de viculación</a><input type='hidden' name='link_capa_campo_local'><span id='muestra_link_capa_campo_local'></span><br>
							
							<div id='formlinkcapa' class='formlink'>
								<a onclick='cerrarFormLink(this)'>cerrar</a>
								<div id='lista'></div>
							</div>
							
							<div id='formlinkcampoexterno' class='formlink'>
							<a onclick='cerrarFormLink(this)'>cerrar</a>
								<div id='lista'></div>
							</div>
							
							<div id='formlinkcampolocal' class='formlink'>
							<a onclick='cerrarFormLink(this)'>cerrar</a>
							<div id='lista'></div>
							</div>
						</p>						
					</div>
					
					<div id='fechas' class="elementoCarga">						
						<p>Año del dato
                        <input type="number" name="fecha_ano"></input></p>
					
						<p>Mes del dato
                        <input type="number" name="fecha_mes"></input></p>
                   
						<p>Dia del dato
                        <input type="number" name="fecha_dia"></input></p>                        
                    </div>   
                    
					<div  id='configurarCampos'  class='elementoCargaLargo'>                 
                    	<h1>Campos <a onclick='formularNuevoCampo()'><img src='./img/agregar.png'></a></h1>
                    	
                    	<div id='form_NuevoCampo'>
							<a id='cerrar' onclick="document.querySelector('#form_NuevoCampo').setAttribute('estado','inactivo');" class='boton_gris'>cerrar</a>
							<a id='crear'  onclick="enviarCrearCampo();" class='boton_celeste'>crear</a>
						
							<h2>Crear Campo de tipo:</h2>							
							<select name='tipo_campo'>
								<option value=''>- elegir -</option>
								<option value='num'>Numérico</option>
								<option value='tex'>Texto</option>
							</select>							
						</div> 
                    	
                    	<div class='campo' id='campo_texto_1'>
							<h3>campo de texto <span class='n'>1</span></h3>
							<label>Nombre:</label><input name='nom_col_text1' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_text1' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_texto_2'>
							<h3>campo de texto <span class='n'>2</span></h3>
							<label>Nombre:</label><input name='nom_col_text2' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_text2' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_texto_3'>
							<h3>campo de texto <span class='n'>3</span></h3>
							<label>Nombre:</label><input name='nom_col_text3' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_text3' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_texto_4'>
							<h3>campo de texto <span class='n'>4</span></h3>
							<label>Nombre:</label><input name='nom_col_text4' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_text4' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_texto_5'>
							<h3>campo de texto <span class='n'>5</span></h3>
							<label>Nombre:</label><input name='nom_col_text5' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_text5' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_numero_1'>
							<h3>campo numérico <span class='n'>1</span><a id='b_calc'  onclick='formularCalculo(this);'><img src="./img/calculadora.png"></a></h3>
							<label>Nombre:</label><input name='nom_col_num1' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_num1' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_numero_2'>
							<h3>campo numérico <span class='n'>2</span><a id='b_calc'  onclick='formularCalculo(this);'><img src="./img/calculadora.png"></a></h3>
							<label>Nombre:</label><input name='nom_col_num2' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_num2' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_numero_3'>
							<h3>campo numérico <span class='n'>3</span><a id='b_calc'  onclick='formularCalculo(this);'><img src="./img/calculadora.png"></a></h3>
							<label>Nombre:</label><input name='nom_col_num3' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_num3' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_numero_4'>
							<h3>campo numérico <span class='n'>4</span><a id='b_calc'  onclick='formularCalculo(this);'><img src="./img/calculadora.png"></a></h3>
							<label>Nombre:</label><input name='nom_col_num4' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_num4' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_numero_5'>
							<h3>campo numérico <span class='n'>5</span><a id='b_calc' onclick='formularCalculo(this);'><img src="./img/calculadora.png"></a></h3>
							<label>Nombre:</label><input name='nom_col_num5' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_num5' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	
                    	<div id='form_calculoCampo' activo='no'>
							<input type='hidden' name='campo'>
							<a class='boton_gris' onclick='this.parentNode.setAttribute("estado","inactivo")'>cerrar</a>
							<h3>Cálculo automatizado de campos</h3>
							
							<select onchange='this.parentNode.setAttribute("modo",this.value);'>
								<option value=''>- elegir Funciones -</option>
								<option value='norm'>Normalización</option>
								<option value='geom'>Geometría</option>
								<option value='cobv'>Cobertura de capa vectorial</option>
								<option value='cobr'>Cobertura de capa raster</option>
							</select>
                    	
							<div class='subform' tipo='norm'>
								<h4>criterio:</h4>
								<label>dividendo:</label><select name='dividendo'></select>
								<label>divisor:</label><select name='divisor'></select>
								<label>porcentual:</label><input type='checkbox'>
								<a class='boton_celeste' onclick='ejecutarCalculoCampoNOrmalizacion()'>Ejecutar</a>
							</div>	
							
							<div class='subform' tipo='geom'>
								<select name='modo'>
									<optgroup label='Superficie'>
										<option value='s_km2'>Km²</option>
										<option value='s_Ha'>Ha</option>
										<option value='s_m²'>m²</option>
									</optgroup>
									<optgroup label='Longitud'>
										<option value='l_Km'>Km²</option>
										<option value='l_m'>m</option>
									</optgroup>
								</select>
								<a class='boton_celeste' onclick='ejecutarCalculoCampoGeometria()'>Ejecutar</a>
							</div>	
							
							<div class='subform' tipo='cobv'>
								<pre>función en desarrollo   ¨\_:L_/¨</pre>
								
								<a class='boton_celeste' onclick='ejecutarCalculoCampoCoberturaVectorial()'>Ejecutar</a>
							</div>
							<div class='subform' tipo='cobr'>
								<pre>función en desarrollo   ¨\_:L_/¨</pre>
								<a class='boton_celeste' onclick='ejecutarCalculoCampoCoberturaRaster()'>Ejecutar</a>
							</div>
                    	</div>
                    </div>
				
					<a id='b_editar_shp' onclick='this.parentNode.parentNode.setAttribute("edicion_shp_activa","si");' class="boton_celeste">Cargar Geometrías</a>
					<a id='b_editar_shp_off' onclick='this.parentNode.parentNode.setAttribute("edicion_shp_activa","no");' class="boton_gris">Cerrar Geometrías</a>
										
                    <div  id='cargarGeometrias'  class='elementoCargaLargo'>                 
                    	<h1>Cargar geometrías</h1>   	
	                    <div id='earchivoscargando' class='elementoCarga'>
	                        <h2>archivos cargando</h2>
	                        <div id='archivosacargar'>
	                            <form id='shp' enctype='multipart/form-data' method='post' action='./ed_ai_adjunto.php'>			
	                                <label style='position:relative;' class='upload'>							
	                                <span id='upload' style='position:absolute;top:0px;left:0px;'>
										arrastre o busque aquí un archivo 
										<span id='tipogeom'>(shp, shx, dbf, prj)</span>
										<span id='tipotabla'>(xlsx)</span>
									</span>
	                                <input id='uploadinput' style='opacity:0;' type='file' multiple name='upload' value='' onchange='enviarArchivosSHP(event,this);'></label>
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
	
	                    <div id='earchivoscargados' class='elementoCarga'>
	                        <h2>archivos cargados <a class='boton eliminar' onclick='eliminarArchivosSHP()'>eliminar</a></h2>
	                        <p id='txningunarchivo'>- ninguno -</p>
	                        <div id='archivoscargados'></div>
	                    </div>
	
	                    <div  id='ecamposdelosarchivos'  class='elementoCargaLargo'>
	                        <h2>campos identificados</h2>
	                        <div id='tituloscampos'>
	                        <p id='titulonombres'>nombre definido</p>
	                        <p id='titulocampo'>campos disponibles</p>
	                        <p id='tituloasignado'>contenido asignado</p></p>
	                        </div>
	                        
	                        <p id='verproccampo'></p>
	                        <div id='camposident'></div>
	                        <p id='titulodesechar'>Contenidos a desechar:</p>
	                        <div id='camposdesecha'></div>
	                        
	                        <a id='procesarBoton' onclick='procesarCapa(this.parentNode)' estado='inviable'>Procesar Shapefile</a>
	                    </div>
                    </div>
				 
					<div id='simbologia' class='elementoCargaLargo'>
						<h1>Simbología<a class= "boton_celeste" onclick='guardarSLD(this.parentNode);' > Guardar Simbología</a></h1>
						<h2>
							<select onchange='cargaSimbologia(this.value)' id='simbologia_carga'>
								<option value='0'>Símbolo por defecto</option>
								<option value='-1'>+Nueva simbología</option>
								<optgroup label='Simbologías alternativas' id="alternativas">
								 </optgroup>
							</select>
							<a class='boton_celeste' onclick='formularSimbologia()'>Cambiar nombre</a>
						</h2>
						<label class='l1'>color de relleno</label>
						<input onchange='cargarFeatures()' type="color" id="inputcolorrelleno"/>
						<label class='l2'>transparencia de relleno</label>
						<input onchange='cargarFeatures()' id="inputtransparenciarellenoNumber" min="0" max="100" oninput="inputtransparenciarellenoRange.value=inputtransparenciarellenoNumber.value" type="number" style="width: 10%">
						<input onchange='cargarFeatures()' id="inputtransparenciarellenoRange" min="0" max="100" oninput="inputtransparenciarellenoNumber.value=inputtransparenciarellenoRange.value" type="range" style="width: 20%">
						<label class='l1'>color de trazo</label>
						<input onchange='cargarFeatures()' type="color" id="inputcolortrazo"/>
						<label class='l2'>ancho de trazo (pixeles)</label>
						<input onchange='cargarFeatures()' id="inputanchotrazoNumber" min="0" max="10" oninput="inputanchotrazoRange.value=inputanchotrazoNumber.value" type="number" style="width: 10%">
						<input onchange='cargarFeatures()' id="inputanchotrazoRange" min="0" max="10" oninput="inputanchotrazoNumber.value=inputanchotrazoRange.value" type="range" style="width: 20%">
						<h3>
							Etiquetado
							<select onchange="actualizarSimbolo(this)" name="campo_et"></select>
						</h3>
						<h2>Reglas adicionales<a onclick='anadirReglaSLD(this.parentNode);' > añadir regla</a></h1></h2>
						<div id='reglas_adicionales'></div>
					</div>
				</div>  
			</div>
        </div>
        


    </div>
</div>
<div class='formcent' id='simbologia_carga' estado='inactivo'>
	<a class='boton_gris' onclick='cerrarFormSimbologia()'>cerrar</a>
	<a class='boton_celeste' onclick='guardarSimbologia()'>guardar</a>
	<a class='boton_rojor' onclick='borrarSimbologia()'>eliminar</a>
	
	<input type='hidden' name='idsim'>
	<label>Nombre:</label><input name='nombre'>
</div>
		


<div class='formcent' id='formulario_registro'>
	<a id='b_cerrar' onclick="cerrarformEditaReg();" class="boton_gris">cerrar</a>
	<a id='b_guardar' onclick="guaradarEditaReg();" class="boton_celeste">guardar</a>
	<label>id:</label><input readonly='readonly' name='idr'><br>
	<label>capa:</label><input readonly='readonly' name='idcapa'><br>
	<div id='campos'></div>
</div>


<div class='formcent' id='form_estadisticas' estado='inactivo'>
	
	<div class='borde-contenido'>
	<div class='contenido'>
		<img id='imgcarga' src='./comun_img/cargando.gif'>
		<div class="cajaacciones">
			<a class='' onclick='$("#form_estadisticas").attr("estado","inactivo")'>Cerrar</a>
		</div>
		
		<table>
			<tr estado='activo'>
				<th>Campo</th>
				<th>Suma</th>
				<th>Promedio</th>
				<th>Densidad <span id='uni_dens'></span></th>
			</tr>
			
			<tr id='numero1'>
				<td id='nom'></td>
				<td id='sum'></td>
				<td id='avg'></td>
				<td id='den'></td>
			</tr>
			
			<tr id='numero2'>
				<td id='nom'></td>
				<td id='sum'></td>
				<td id='avg'></td>
				<td id='den'></td>
			</tr>
			
			<tr id='numero3'>
				<td id='nom'></td>
				<td id='sum'></td>
				<td id='avg'></td>
				<td id='den'></td>
			</tr>
			
			<tr id='numero4'>
				<td id='nom'></td>
				<td id='sum'></td>
				<td id='avg'></td>
				<td id='den'></td>
			</tr>
			
			<tr id='numero5'>
				<td id='nom'></td>
				<td id='sum'></td>
				<td id='avg'></td>
				<td id='den'></td>
			</tr>
			<tr id='superficie'>
				<td id='nom'>superficie</td>
				<td id='sum'></td>
				<td id='avg'></td>
				<td id='den'></td>
			</tr>
		</table>
	</div>
	</div>
</div>
		

<div class='formcent' id='form_importar_capa_pub' estado='inactivo'>
	
	<div class='borde-contenido'>
	<div class='contenido'>
		<img id='imgcarga' src='./comun_img/cargando.gif'>
		<div class="cajaacciones">
			<a class='' onclick='$("#form_importar_capa_pub").attr("estado","inactivo")'>Cerrar</a>
			<a class='administra' id='botonimporta' onclick='accionImportarCapaPublica()'>Importar</a>
		</div>
		
		<div id='seleccion'>
			
			<input name="id" type='hidden'>
			
			<h4>capas disponibles</h4>
			buscar:<input>
			<a id='botondeselecciona' onclick='limpiarSeleccionCapaImporta();'>Deseleccionar capa</a>
			<div id='lista_capas'></div>
						
			<h4>Configuración de la importación</h4>

			<div id='config_importar' class="row tag" id_tag="1" modo_recorte=''>
				<input name="recorte" value="no" type="radio"  onchange='actualizaTiporecorte()'><label>No recortar la capa</label>
				<input name="recorte" value="proyecto" type="radio" checked='checked'  onchange='actualizaTiporecorte()'><label>Recortar a la extensión de tu proyecto</label>
				<input name="recorte" value="departamento" type="radio"  onchange='actualizaTiporecorte()'><label>Recortar con un departamento (partido)</label>
				<input name="recorte" value="capa" type="radio"  onchange='actualizaTiporecorte()'><label>Recortar con una capa propia</label>
				
				<div id='opciones_rec_dto'>					
						<select onchange="actualizaFormPerfil();formularDepartamentos();" class="form-select" aria-label="Estado de pertenencia" aria-describedby="paisHelp" name='provincia'>
						  <option value='' selected>Provincia</option>					  
						</select>
					
						<select onchange="actualizaFormPerfil()" class="form-select" aria-label="Estado de pertenencia" aria-describedby="paisHelp" name='id_depto_recorte'>
						  <option value="__falta_provincia__">Departamento: Elegir primero una provincia</option>					  
						</select>						
				</div>
				
				<div id='opciones_rec_capa'>
					<p>¡Función en desarrollo! Probá otro criterio de recore hasta que tengamos este fucnionando.</p>
					<input name="id_capa_recorte" type='hidden'>
					capa de recorte:<input name="nombre_capa_recorte" type='text' value='indefinida'>
					
					<div id='menu selector capa'>
						
					<input name="recorte_capa" type="checkbox"><label>Recortar a la extensión de tu objeto específico</label>
					<input name="id_objeto_recorte" type='hidden'>
					objeto de recorte:<input name="nombre_objeto_recorte" type='text' value='- indefinida -'>
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
	
	var _Acc = "capa";	
	var _IdUsu='<?php echo $_SESSION[$CU]["usuario"]['id'];?>';
	

	<?php if(!isset($_GET["idr"])){$_GET["idr"]='';} ?>	
	var _idCapa = '<?php echo $_GET["idr"];?>';

    //Variable de filtro en búsquedas de datos.
    <?php if(!isset($_SESSION[$CU]['usuario']['recorte'])){$_SESSION[$CU]['usuario']['recorte']='';};?>
	var _RecorteDeTrabajo=JSON.parse('<?php echo json_encode($_SESSION[$CU]['usuario']['recorte']);?>');
	
	
	var _IdMarco = getParameterByName('id');
	var _CodMarco = getParameterByName('cod');	
	var _DataUsuaries={};	
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


<script type="text/javascript" src="./app_capa/app_capa_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones de consultas ajax -->
<script type="text/javascript" src="./app_capa/app_capa_interaccion.js?t=<?php echo time();?>"></script> <!-- carga funciones dei interacción -->
<script type="text/javascript" src="./app_capa/app_capa_mapa.js?t=<?php echo time();?>"></script> <!-- carga funciona de gestión de mapa-->

<script type="text/javascript" src="./app_capa/app_capa_pagina.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_capa/app_capa_Shapefile.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion del formulario central para la carga de SHP -->
<script type="text/javascript" src="./comun_consultas/comunes_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones de interaccion con el mapa -->

<script type="text/javascript">
		
	cargaBaseLink(); //en comun_mapa_link.js
	
	if(_IdUsu<"1"){
		
		formUsuario("accede");	
		
	}else{ 
		
		console.log(_IdUsu);
		consultarPermisos();
		consultarUsuaries();
		
		//cargarListadoCapas(); // funcion a desarrollar.
		
		consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');

		if(_idCapa!=''){
			limpiarFormularioSeleccionCapa();
			limpiarFormularioCapa();
			document.querySelector('#cuadrovalores').setAttribute('modo','muestracapaexistente');   
			cargarDatosCapaPublicada(_idCapa);
		}else{
			accionCargarCapaExist();
		}


		if(_RecorteDeTrabajo!=''){
			console.log(_RecorteDeTrabajo);
			cargaRecorteSession();
		}
				
		
	}
	
	
	function reingresaGeneral(){
		console.log(_UsuarioA);
		_IdUsu=_UsuarioA.id;
		
		console.log('ui');
		cargarListaCapas();// en comun_mapa_selector_capas.js
		
		consultarPermisos();
		consultarUsuaries();
		
		cargarDatosCapaPublicada(_idCapa);
				
		consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
		
		if(_idCapa!=''){
			limpiarFormularioSeleccionCapa();
			limpiarFormularioCapa();
			document.querySelector('#cuadrovalores').setAttribute('modo','muestracapaexistente');   
			cargarDatosCapaPublicada(_idCapa);
		}else{
			accionCargarCapaExist();
		}


		if(_RecorteDeTrabajo!=''){
			console.log(_RecorteDeTrabajo);
			cargaRecorteSession();
		}
	}

	
</script>

</body>
