<?php
/**
* aplicación de visualización y gestion de indicadores
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
    
    <link href="./terceras_js/ol6.3/ol.css" rel="stylesheet" type="text/css">
    
    <link href="./comun_css/general.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./comun_css/general_app.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./app_ind/css/app_ind.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    
    <style>
	 
			
    </style>
</head>

<body>   
<script type="text/javascript" charset="UTF-8" src="./terceras_js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" charset="UTF-8" src="./terceras_js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" charset="UTF-8" src="./terceras_js/ol6.3/ol.js"></script>

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
                <img src='./img/app_ind_hd.png' style='float:left;'>
                <h2 id='titulo'>Gestor de carga de indicadores</h2>
                <div id='descripcion'>
            		Espacio definir, y visualizar indicadores.<br>
            		Esta aplicación permite cargar datos asociados a recortes territoriales, y visualizarlos en el mapa.                	
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
            <div id="wrapper">
                <div id="location"></div>
                <div id="scale"></div>
            </div>
            <div id='botonera_mapa'></div>
        </div>

        <div id="cuadrovalores" 
			formseleccionind = "si"
			formseleccionmod = "no"
			formeditarindicadores = "no"
			formdivlistaindicadorescarga = "no"
			formCargaInd  = "no"
			divListaIndicadoresCarga="no"
			
			estado = "inicial"
		>
		
			<div id="contenido">
	        

				<div class='cajaacciones'>
					<a onclick="accionCrearIndicador(this)" id='botonCrearIndicador' Class="boton_celeste" ><img src="./img/agregar.png">Crear indicador</a>
					<a onclick="accionCrearModelo()" id='botonCrearModelo' Class="boton_celeste" ><img src="./img/agregar.png">Crear Modelo</a>
					<a onclick='accionCargaCancelar(this)'  id="botonCancelarCarga" Class="boton_celeste" >Volver al listado de indicadores</a>
					<a onclick='cargarListadoModelo()'  id="botonIndicadoresModelo" Class="boton_celeste" >Mostrar indicadores modelo</a>
					<a onclick='formularCruce()'  id="botonFormularCruce" Class="boton_celeste" >Cruzar indicadores</a>
				</div>
				
				<div id="formSeleccionInd" class="elementoOculto">   
					<h1>Indicadores disponibles</h1>
					<div id='barrabusqueda'><input id='barrabusquedaI' autocomplete='off' value='' placeholder="buscar" onkeyup="actualizarBusqueda(event);"><a onclick='limpiaBarra(event);'>x</a></div>
					<p id='txningunind'>- ninguno -</p>
					<div id='listaindpublicadas'></div>
					<div id='indicadorActivo' idindicador='0' class="elementoOculto"></div>
					<div class='formSeleccionIndCuerpo' id='divSeleccionIndCuerpo'></div>
				</div>
				
				<div id="formSeleccionMod" class="elementoOculto">   
					<div class='formSeleccionModCuerpo' id='divSeleccionModCuerpo'>
						<h1>Modelos disponibles</h1>
						<div id='barrabusqueda'><input id='barrabusquedaM' autocomplete='off' value='' onkeyup="actualizarBusqueda(event);"><a onclick='limpiaBarra(event);'>x</a></div>
						<p id='txningunmod'>- ninguno -</p>
						<div id='listamodpublicadas'></div>
					</div>
				</div>
				
				
				<div class='formCargaInd' id='formCargaInd'>	
								
					<div id='avanceproceso'></div>
					<div class='elementoCarga accionesCampa cajaacciones'>
						<h1 id='tituloInd'></h1>
						<div id="indCargaDescripcion" class="divValor"></div>
						<div id="indCargaPeriodicidad" class="divValor"></div>
						<div id="indCapaGeom" class="divValor"></div>
						
						
						
						<a id='botoncancelaedita' onclick='cargarListadoCapasPublicadas();' class="boton_gris">Volver</a>
						<a id='botonedita' onclick='editarIndicador();' title="editar este indicador"><img src='./img/editar.png'></a>
						<div id='accionesInd'>
							<a onclick='cancelarEditarInd()' class="boton_gris">Cancelar</a>
							<a class='boton_rojo' id='botonelim' onclick='eliminarInd();' title="Eliminar Campa">Eliminar</a>
							<a class="boton_celeste" id='botonguarada' onclick='guardarInd();guardarSLD();' title="guardar este indicador">Guardar</a>
							<a class="boton_celeste" id='botonduplica' onclick='duplicarIndicador()'>Duplicar Indicador</a>
							<a onclick='accionCreaPublicar(this)' id="botonPublicar" class="boton_celeste">Publicar</a>
						</div>					
					</div>
				
					
					
					<div class="menuAcciones elementoOculto" id="divMenuAccionesCrea" display='none' DEPRECADO='DEPRECADO'>
						<h1>Crear indicador</h1>
						<a onclick='accionCreaEliminar(this)' id="botonEliminar" class="boton_rojo">Eliminar</a>
						<a onclick='accionCreaGuardar()' id="botonGuardar" class="boton_celeste">Guardar</a>
						<a onclick='accionCreaPublicar(this)' id="botonPublicar" class="boton_celeste">Publicar</a>
						<a onclick='formularioAmpliado("indicador")' id="botonAmpliar" class="boton_celeste">Formulario Completo</a>
						<a onclick='accionCargaCancelar()' id="botonVolver" class="boton_gris">Volver</a>
					</div>
					
					<div id='formEditarIndicadores' idindicador='0' class="elementoOculto">
						<div class='elementoCarga accionesCampa cajaacciones'>
							<h1>Nombre del indicador</h1>
							<input type="text" id="indNombre" onkeypress="accionEditarIndCampo(event, this)"></input>
						</div>
						<div class='elementoCarga'>
							<h1>Descripción</h1>
							<input type="text" id="indDescripcion" onkeypress="accionEditarIndCampo(event, this)"></input>
						</div>
						<div class='elementoCarga'>
							<h1>Funcionalidad</h1>
							<select onchange="editarIndFuncionalidad(event, this)" id="funcionalidadSelector">
								
								<option value="elegir">-elegir-</option>
								<option value="sinGeometria">Asignar un único valor para toda el área</option>
								<option value="geometriaExistente">Asignar valores a geometrías fijas</option>
								<option value="nuevaGeometria">Asignar valores a nuevas cambiantes</option>
								<option value="otrosIndicadores">Calcular valores a partir de otros indicadores (no disponible)</option>
								<option value="archivosGeograficos">Cargar valores subiendo archivos geográficos (no disponible)</option>
								<option value="vinculosExternos">Obtener valores de vínculos externos (no disponible)</option>
							</select>
							
							
							<p id="tipodeometriaNuevaGeometria">
							<select onchange="" id='inputTipoGeom'>
								<option value="Point">Puntos</option>
								<option value="LineString">Lineas</option>
								<option value="Polygon">Polígonos</option>
							</select>
							</p>
						</div>
						<div class='elementoCarga'>
							<h1>Periodicidad</h1>
							<select onchange="editarIndPeriodicidad(event, this)" id="periodicidadSelector">
								<option value="elegir" selected>-elegir-</option>
								<option value="diario">Diario</option>
								<option value="mensual">Mensual</option>
								<option value="anual">Anual</option>
							</select>
						</div>
						<div class='elementoCarga'>
							<h1>Fecha inicial</h1>
							<input name="fechaDesde" id="inputFechaDesde" type="date" >
						</div>
						<div class='elementoCarga'>
							<h1>Fecha final</h1>
							<input name="fechaHasta" id="inputFechaHasta" type="date" >
						</div>
						<div class='elementoCargaLargo'>
							<h1>Capa</h1>
							<div id="AccionesSeleccionCapa">
								tipo de geometría: 
								<select name='tipogeometria'>
									<option value=''>-elegir-</option>	
									<option value='Point'>Point</option>
									<option value='LineString'>LineString</option>
									<option value='Polygon'>Polygon</option>
								</select>
								<a 
									title='la capa auxiliar aloja las UA del indicador pero no es visible en el menu de capas' 
									onclick="accionCrearCapaAux(this)" id='botonCrearCapaAux' class="boton_celeste"
									>Crear Capa Auxiliar</a>
								</br>
								<a 
									title='En lugar de usar una capa auxiliar (no visible) se puede utilizar una capa ya cargada' 
									onclick="accionSeleccionarCapa(this)" id='botonSeleccionarCapa'  class="boton_celeste"
									>Seleccionar Capa de referencia </a>
								</br>
								<a onclick='accionCancelarSeleccionCapa(this)' id="botonCancelarSeleccionarCapa" class="elementoOculto">Cancelar seleccion de capa</a></br>
												
								
							</div>
							<div id="capaseleccionada" idcapa="0" class="elementoOculto">
								<div>
									<label>Nombre de la capa</label>
									<div id="capaNombre" class="divValor"></div>
								</div>
								<div>
									<label>Descripción</label>
									<div id="capaDescripcion" class="divValor"></div>
								</div>
								
								<div id="AccionesSeleccionCapaCambio">
									<a onclick="accionSeleccionarCapaCambio(this)" id='botonSeleccionarCapaCambio'  class="boton_celeste">Cambiar Capa Seleccionada</a></br>
									<a onclick='accionCancelarSeleccionCapaCambio(this)' id="botonCancelarSeleccionarCapaCambio " class="elementoOculto boton_rojo">Cancelar cambio de capa</a></br>
								</div>
							</div>
							<div id="formSeleccionCapa" class="elementoOculto">
								
								<div class='formSeleccionCapaCuerpo' id='divSeleccionCapaCuerpo'>
									<h1>Capas publicadas</h1>
									<p id='txningunacapa'>- ninguna -</p>
									<div id='listacapaspublicadas'></div>
								</div>
							</div>
							<div class="elementoOculto">
								<h1>Selector criterio de simbología</h1>
								<label>color de relleno</label>
								<input onchange='cargarFeatures()' type="color" id="inputcolorrelleno"/>
								<label>transparencia de relleno</label>
								<input onchange='cargarFeatures()' id="inputtransparenciarellenoNumber" min="0" max="100" oninput="inputtransparenciarellenoRange.value=inputtransparenciarellenoNumber.value" type="number" style="width: 10%">
								<input onchange='cargarFeatures()' id="inputtransparenciarellenoRange" min="0" max="100" oninput="inputtransparenciarellenoNumber.value=inputtransparenciarellenoRange.value" type="range" style="width: 25%">
								<label>color de trazo</label>
								<input onchange='cargarFeatures()' type="color" id="inputcolortrazo"/>
								<label>ancho de trazo (en pixeles)</label>
								<input onchange='cargarFeatures()' id="inputanchotrazoNumber" min="0" max="10" oninput="inputanchotrazoRange.value=inputanchotrazoNumber.value" type="number" style="width: 10%">
								<input onchange='cargarFeatures()' id="inputanchotrazoRange" min="0" max="10" oninput="inputanchotrazoNumber.value=inputanchotrazoRange.value" type="range" style="width: 25%">
							</div>
						</div>
						
						<div class='elementoCargaLargo'>
							<h1>Valores a cargar</h1>
							<div class="indValoresRep"></div>
							<div class="indValoresNombre">Nombre</div>
							<div class="indValoresTipo">Tipo de dato</div>
							<div class="indValoresUnidad">Unidad de medida</div>
							<div id="columnasValores"></div>
							<a onclick="accionAnadirNuevaColumnaValor(this)" id='botonAnadirNuevaColumnaValor'  class="boton_celeste">+ añadir valor</a>
						</div>
						
						<div class='elementoCargaLargo'>
							<h1>Representación</h1>
							<div id='representacionescalacolor' tipo='numero'>
								<div class="fila" id='sobre'>
									<div id='colorejemplo'></div>
									<div id='valorminimo'><p>sobre</p></div>                    		
								</div>
								<div class="fila" id='maximo'>
									<div id='colorejemplo'></div>
									<div id='valorminimo'><input id='maximo' placeholder='max' onkeyup='accionRepresentacionValorMaximo();'></div>                    		
								</div>
								<div class="fila" id='alto'>
									<div id='colorejemplo'></div>
									<div id='valorminimo'><p>medio</p></div>                    		
								</div>
								<div class="fila" id='bajo'>
									<div id='colorejemplo'></div>
									<div id='valorminimo'><input id='minimo' placeholder='min' onkeyup='accionRepresentacionValorMinimo();'></div>                    		
								</div>
								<div class="fila" id='minimo'>
									<div id='colorejemplo'></div>
									<div id='valorminimo'><p>sub</p></div>                    		
								</div>
								<div class="fila" id='sub'>
									<div id='colorejemplo'></div>                  		
								</div>
							</div>
							
							<div id='representacionescalacolor' tipo='texto'></div>
						</div>
						
						 
						<div class='elementoCargaLargo' id='formcalculo'>
							<h1>Cálculos automáticos</h1>
							<div>Área de Influencia (buffer): <input type="text" id="calc_buffer"></input></div>
							<div>Superposición con otras variables: <input type="text" id="calc_superp"></input></div>
							<div>Segmentación territorial del resultado: <input type="text" id="calc_zonificacion"></input></div>
						</div>
					</div>
				</div>
			
				
				<div class='formCargaInd' id="divListaIndicadoresCarga" idindicadorcarga="" class="elementoOculto">
					<div id='avanceproceso'></div>
					
					 <div id="AccionesSeleccionIndCambio">
						<h1>Acciones</h1>
						<a id='botonelim' onclick='eliminarCandidatoCapa(this.parentNode);' title="Eliminar Capa">Eliminar</a>
						<a id='botonguarada' onclick='guardarCapa(this.parentNode);' title="guardar esta capa preliminarmente">Guardar</a>
						<a id='botonpublica' onclick='publicarCapa(this.parentNode);' >Publicar</a>
					</div>
					
					         
						
					<div class='elementoCargaLargo' id='periodo'>
						<h2>Datos Cargados</h2>
						<div class="scrolling-wrapper" id="selectorPeriodo">
						</div>
						
						
						<div id="divPeriodoSeleccionado" ano="" mes="" class="elementoOculto" idgeom="">
							<h3><span id="tituloPariodoSeleccionado"></h3>
				   
				   
							<div style="display: inline-block;">
								<label>Periodo Seleccionado</label>
								<div id="indCargaPeriodoLabel" class="divValor"></div>
							</div>
							
							<div class="menuAcciones cajaacciones" id="divMenuAccionesEditarValor" style="display: inline-block;">
								<a onclick='accionEditarValorGuardar(this)' id="botonGuardar">Guardar</a>
								<a onclick='accionEditarCrearGeometria(this)' id="botonCrearGeom">Añadir geometría</a>
								<a onclick='accionCopiarGeometriaAnterior(this)' id="botonDuplicarGeom">Copiar geometría del período anterior</a>
								<a onclick='formularCopiarGeometriaCapa(this)' id="botonCopiarGeomCapa">Copiar geometrías de una capa</a>
								<a 
									title='Puede generar una capa auxiliar a partir de los datos de un relevamiento' 
									onclick="formularCopiarGeometriaRele(this)" id='botonImporatrRele'
									>Importar datos de un relevamiento</a>
							</br>     
							</div>
							<form id='formImportarRele'>
								<div id='listarelesfuente'></div>
								<div id='selectorCamposRele'>
								<p>campo 1:<select name='campo_importar_1'></select></p>
								<p>campo 2:<select name='campo_importar_2'></select></p>
								<p>campo 3:<select name='campo_importar_3'></select></p>
								<p>campo 4:<select name='campo_importar_4'></select></p>
								<a id='accionImportarRele' id_rele='' onclick='importarRele()'>Importar</a>
								</div>
							</form>
							<form>
								<div id='listacapasfuente'></div>
							</form>
							<div id='listaUnidadesInd'></div>
								
							
						</div>
					</div>
					
				</div>
			</div>
		</div>
    </div>
</div>


<div class='formcent' id='form_ind_exp'>
	<div class='borde-contenido'>
	<div class='contenido'>
		<div class="cajaacciones">
			<a class='' onclick='$("#form_ind_exp").attr("estado","inactivo")'>Cerrar</a>
			<a class='eliminar' onclick='accionEliminarFormCent()'>Eliminar</a>
		</div>
		<div id='esmodelo' class='cajaacciones'>
			<a class='administra' onclick='accionGuardaMod()'>Guardar Cambios</a>
			<a>Generar Indicador desde este modelo</a>
		</div>
		
		<div id='esindicador' class='cajaacciones'>
			<a>Guardar Cambios</a>	
			<a class='administra'>Generar un modelo desde este indicador</a>
		</div>
		
		<div id='características_generales'>
			
			<input name="id" type='hidden'>
			
			<h4>Definición teórica</h4>
			
			
			<div class="row">
				<div class="col">
					<label>nombre</label><input name="nombre" type='text'>
				</div>	
					
				<div class="col">	
					<label>unidad_medida</label><input name="unidad_medida" type='text'>
				</div>
				
				<div class="col">
					<label>escala_espacial</label><input name="escala_espacial" type='text'>
				</div>
				
				<div class="col">
					<label>desagrgacion</label><input name="desagrgacion" type='text'>
				</div>
			</div>
			
			<div class="row">
				<div class="col">
					<label>descripcion</label><textarea name="descripcion"></textarea>
				</div>	
					
				<div class="col">	
					<label>relevancia_acc</label><textarea name="relevancia_acc"></textarea>
				</div>
			</div>
			
			<div class="row">
				<div class="col">
					<label>limitaciones</label><textarea name="limitaciones"></textarea>
				</div>	
					
				<div class="col">	
				<label>ejemplo</label><textarea name="ejemplo"></textarea>
				</div>
			</div>
			
			<div class="row">
				<div class="col">
					<label>Datos necesarios para su generación</label><textarea name="datos_input"></textarea>
				</div>
				
				<div class="col">
					<label>Fuentes posibles para la obteción de datos</label><textarea name="fuentes_input"></textarea>
				</div>
			</div>
			
			<div class="row">
				
				<div class="col">
				<label>calculo</label><textarea name="calculo"></textarea>
				</div>

				
				<div class="col">
				<label>valoracion</label><textarea name="valoracion"></textarea>
				</div>
			</div>
			
			<h4>Configuración general en la plataforma</h4>

			<div class="row">
				<div class="col">
				<label>funcionalidad</label><select name="funcionalidad">
					<option value="elegir" selected>-elegir-</option>
					<option value="geometriaExistente">Asignar valores a las geometrías de una capa existente</option>
					<option value="nuevaGeometria">Asignar valores a nuevas geometrías en una capa de trabajo</option>
					<option value="otrosIndicadores">Calcular valores a partir de otros indicadores (no disponible)</option>
					<option value="archivosGeograficos">Cargar valores subiendo archivos geográficos (no disponible)</option>
					<option value="vinculosExternos">Obtener valores de vínculos externos (no disponible)</option>                    
				</select>
				</div>
				
				<div class="col">
				<label>periodicidad</label><select name="periodicidad">
					<option value="elegir" selected>-elegir-</option>
					<option value="mensual">Mensual</option>
					<option value="anual">Anual</option>
				</select>		
				</div>
			</div>
			
			<div class="row">
				
				<div class="col">		
					<label>representar_campo</label><select name="representar_campo">
						<option value="elegir" selected>-elegir-</option>			
					</select>
				</div>
				
				<div class="col">	
					<label>representar_val_max</label><input name="representar_val_max" type='text'>
				</div>
								
				<div class="col">	
					<label>representar_val_min</label><input name="representar_val_min" type='text'>
				</div>
			</div>
		
			<h4>Configuración particular en la plataforma</h4>
			
			<div class="row">
				
				<div class="col">	
					<label>Capa a la cual se superpondrá</label><select name="calc_superp">
						<option value="elegir" selected>-elegir-</option>
						<option value="mensual">Mensual</option>
						<option value="anual">Anual</option>
					</select>	
				</div>
			
				<div class="col">		
					<label>Distancia de cobertura</label><input name="calc_buffer" type='calc_buffer'>
				</div>
				
				<div class="col">	
					<label>Campo de valoración de la cobertura</label><select name="calc_superp_campo">
						<option value="elegir" selected>-elegir-</option>
						<option value="mensual">Mensual</option>
						<option value="anual">Anual</option>
					</select>			
				</div>
				
				<div class="col">		
					<label>Capa de zonas de agregación</label><select name="calc_zonificacion">
						<option value="elegir" selected>-elegir-</option>
						<option value="mensual">Mensual</option>
						<option value="anual">Anual</option>
					</select>	
				</div>
						
				<div class="col">
					<label>fechadesde</label><input name="fechadesde" type='date'>
				</div>
				
				<div class="col">
					<label>fechahasta</label><input name="fechahasta" type='date'>
				</div>				
			</div>			
		</div>
		
		<div id='matriz_clasif'>
			<h4>Categorización del modelo</h4>
			<div id='categorias' class="row">
			</div>
		</div>
		
		<div id='requerimientos'>
			<h4>Requerimientos</h4>
			<div id='requerimientos_app' class="row">
			</div>
		</div>		
	</div>
	</div>
</div>



<div class='formcent' id='form_cruce'>
	<div class='borde-contenido'>
	<div class='contenido'>
		<div class="cajaacciones">
			<a class='' onclick='$("#form_cruce").attr("estado","inactivo")'>Cerrar</a>
		</div>
		<div id='esmodelo' class='cajaacciones'>
			<a class='administra' onclick='generarGrafico()'>Guardar Cambios</a>
			<a onclick='generarCruce("grafico");'>Generar gráfico de análisis cruzado</a>
		</div>
		
		<div>			
			
			<input name="id" type='hidden'>
			
			<h4>Definición teórica</h4>
			
			
			<h2>Variable principal</h2>
			<i>* obligatorio</i>
			<p>El indicador elegido definirá las unidades de salida (geográficas y temporales)</p>
			<p>Se aceptan geometrías de tipo punto y polígono</p>
			<select name='inicador_1' onchange='actualizarFormCruceSi1()'></select>
			<select name='campo_i_1'></select>
			
			
			<h2>Variable secundaria</h2>
			<i>* opcional</i>
			<p>El indicador elegido se aplicará a la variable principal deacuedoa su superposición</p>
			<p>se aceptan puntos y polígonos (deber superponerse a la variable principal y ser de tipo de geometría diferente)</p>
			<p>si la temporalidad es menor a la principal, se promediarán los valores</p>
			
			<select name='inicador_2' onchange='actualizarFormCruceSi1Campos2()'></select>
			<select name='campo_i_2'></select>
			
			
			<h2>Caficiación por factor</h2>
			<i>* opcional</i>
			<p>Se puede incoroprar una capa con simbología, en la medida que las unidades principales estén contenidas en un polígono de esta capa se el aplicará es código de color correspondiente</p>
			<p>Solo acepta polígonos</p>
			<p>Solo se acepta para indicadore principales de tipo punto</p>
			
			<select name='capa_3'></select>
			
						
		</div>		
	</div>
	</div>
</div>


<div id='ventanagrafico'>
<a id='botoncerrar' class='boton' onclick="this.parentNode.setAttribute('estado','inactivo')">cerrar</a>
<a id='botondescargar' class='boton' onclick="descargarImagen()">descargar imagen</a>
<canvas height="800" width="1000">

</canvas>
	

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
	var _idInd = '<?php echo $_GET["idr"];?>';

    //Variable de filtro en búsquedas de datos.
    <?php if(!isset($_SESSION[$CU]['usuario']['recorte'])){$_SESSION[$CU]['usuario']['recorte']='';};?>
    
	_RecorteDeTrabajo=JSON.parse('<?php echo json_encode($_SESSION[$CU]['usuario']['recorte']);?>');

  	
  	<?php if(!isset($_GET['id'])){$_GET['id']='';} ?>	
  	<?php if(!isset($_GET['cod'])){$_GET['cod']='';} ?>	
  	var _IdMarco = '<?php echo $_GET['id'];?>';
	var _CodMarco = '<?php echo $_GET['cod'];?>';
	var _DataUsuaries={};
	
  	var _DataIndicador={};
	var _DataPeriodo=Array();
	var _DataCapa=Array();
	
	var _DataListaIndicadores={};
	var _DataListaModelos={};
	
	
	var _Data_reles={}; //variable para almacenar información de relevamientos (otro módulo) en caso de quere importar datos de un relevamiento.
	var _DataCapas={}; //variable para almacenar información de capas (otro módulo) en caso de quere cruzar indicadores con capas
	
	var _Acciones={}; //completada por "./sistema/sis_acciones.js"
	
	
	var _Id_Modelo_Editando=''; // si está definido lo muestra tras una consulta general.

	
	_fechaHoy = new Date();
	var _Select_Fecha={
		'ano':_fechaHoy.getFullYear(),
		'mes':_fechaHoy.getMonth()+1,
		'dia':_fechaHoy.getDate()
	}
	//console.log(_Select_Fecha);
	

</script>


<script type="text/javascript" src="./comun_interac/comun_interac.js?t=<?php echo time();?>"></script> <!-- definicion de funcions comunes como la interpretacion de respuestas ajax-->

<script type="text/javascript" src="./sis_gral/sistema_marco.js?t=<?php echo time();?>"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./sis_gral/sis_acciones.js?t=<?php echo time();?>"></script> <!-- carga funcion de consulta de acciones y ejecución, completa _Acciones -->

<script type="text/javascript" src="./comun_mapa/comun_mapa_inicia.js?t=<?php echo time();?>"></script> <!-- definicion de variables comunes para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_recorte.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de recorte para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_selector_capas.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de selector de capa base y extras para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_localiz.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de localizacion de direcciones para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_tamano.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de agrandar mapa-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_descarga.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de descarga del mapa activo-->

<script type="text/javascript" src="./app_ind/app_ind_mapa.js?t=<?php echo time();?>"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript" src="./app_ind/app_ind_queries.js?t=<?php echo time();?>"></script> <!-- carga funciones de consulta de base de datos -->
<script type="text/javascript" src="./app_ind/app_ind_pagina.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./comun_consutlas/comunes_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones de interaccion con el mapa -->


<script type="text/javascript">

	cargarMapa();	
	baseMapaaIGN();//cargar mapa base IGN
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
	cargarListadoCapasPublicadas();
	consultarPermisos();
	
	if(_RecorteDeTrabajo!=''){
		cargaRecorteSession();
	}

	if(_idInd!=''){
		accionIndicadorPublicadoSeleccionado(this,_idInd);
	}
</script>

</body>
