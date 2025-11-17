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

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

?><!DOCTYPE html>
<head>
    <title><?php echo $CONF['plataforma']['nombre'];?></title>
    
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	
    <?php include("./includes/meta.php");?>
    <link rel="manifest" href="pantallahorizontal.json">
	<link href="./css/general.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./css/general_app.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    
    
    <link href="./js/ol6.3/ol.css" rel="stylesheet" type="text/css">
    
    
	<link href="./sis_usuarios/usuarios.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">	
    
    <link href="./app_comu/css/app_comu.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    
    <style>
		
    </style>
</head>

<body>
	
<script type="text/javascript" charset='utf-8' src="./terceras_js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" charset='utf-8' src="./terceras_js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" charset='utf-8' src="./terceras_js/ol10.1/ol.js"></script>
<script type="text/javascript" charset='utf-8' src="./terceras_js/geotiff/geotiff.js"></script>

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
				
				
		<div id='encabezado' style='display:none;'>
			<a href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
                <h2><?php echo $CONF['plataforma']['nombre'];?></h2>
                <p><?php echo $CONF['plataforma']['descrip'];?></p>
			</a>
			<div id='elemento' tipo="Accion">
				<img src='./comun_img/app_rele_hd.png' style='float:left;'>
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
        <div id="menu_admin_flotante" style='display:none'>
			<h2>Paneles de comunidad <a onclick='crearPanel()'><img src='./img/agregar.png'></a></h2>
			<div id='listadito_paneles'></div>
			
			<div id='formulario_panel'>

				
				<h2>Panel de comunidad</h2>
				<input type='hidden' name='idp'>
				<a class='boton_celeste' onclick='guardarPanel()'>guardar</a>
				
				<div>
					<label>Nombre:</label>
					<input  type='text' name='nombre'>
				</div>
				<div>
					<label>Visible:</label>
					<input type='checkbox' name='visible' value='1'>
					<label>Mostrar Marco:</label>
					<input type='checkbox' name='muestra_marco' value='1'>
				</div>
				
				
				<div>
					<label>Descripción:</label>
					<textarea name='descripcion'></textarea>
				</div>
				<div>
					<label>x:</label>
					<input type='text' name='x'>
					<label>y:</label>
					<input type='text' name='y'>
					<label>z:</label>
					<input type='text' name='z'>
					<a onclick='coordMapaAinput()'>tomar de encuadre actual</a>
				</div>
				<div>
					<label>Mapa base:</label>
					<select name='mba'>
						<option value='IGN'>IGN</option>
						<option value='OSM'>OSM</option>
						<option value='OSMgris'>OSM gris</option>
						<option value='Google'>Google satélite</option>
						<option value='ESRI'>Esri satélite</option>
					</select>
				</div>
		
			
				<h2>Capa navegable <a onclick='crearCapaNav()'><img src='./img/agregar.png'></a></h2>
				<div id='formulario_coleccion' class='bloque'>
				
					<input type='hidden' name='idcol'>
									
					<div class='vectorial'>
						<h3><label for='br2'>Vectorial:</label><input onchange='cambiaModoDato(this)' name='modelocolec' value='vectorial' type='radio' id='br2'></h3>
						<label>Capa Vectorial asociada base:</label>
						<select name='id_p_ref_capasgeo'>
							<option value=''>-elegir-</option>
						</select>
						<label>Campo 1:</label>
						<select name='campo'>
							<option value=''>-ninguno-</option>
						</select>
						<label>Campo 2:</label>
						<select name='campob'>
							<option value=''>-ninguno-</option>
						</select>
					</div>
					
					<div class='raster'>
						<h3><label for='br1'>Raster:</label><input onchange='cambiaModoDato(this)' name='modelocolec' value='raster' type='radio' id='br1'></h3>
						<label>Capa Raster en módulo documentos:</label>
						<select name='fi_img_raster'>
							<option value=''>-elegir-</option>
						</select>
						<label>Capa Piramidal overview:</label>
						<select name='fi_img_raster_ovr'>
							<option value=''>-elegir-</option>
						</select>
					</div>
					
				</div>
				
				<h2>Capas visibles <a onclick='crearCapaVis()'><img src='./img/agregar.png'></a></h2>
				<div id='listadito_capas' class='bloque'></div>
				<div id='formulario_capa' class='bloque'>

					<div class='vectorial'>
						<h3><label for='br2'>Vectorial:</label><input onchange='cambiaModoDato(this)' name='modelocapa' value='vectorial' type='radio' id='br2'></h3>
						<label>Capa Vectorial asociada base:</label>
						<select name='id_p_ref_capasgeo'>
							<option value=''>-elegir-</option>
						</select>
						<label>Simbología (por defeto toma la de la capa):</label>
						<textarea name='simbologia'></textarea>
					
					
					
					</div>
					
					<div class='raster'>
						<h3><label for='br1'>Raster:</label><input onchange='cambiaModoDato(this)' name='modelocapa' value='raster' type='radio' id='br1'></h3>
						<label>Capa Raster en módulo documentos:</label>
						<select name='fi_raster'>
							<option value=''>-elegir-</option>
						</select>
						<label>Capa Piramidal overview:</label>
						<select name='fi_raster_ovr'>
							<option value=''>-elegir-</option>
						</select>
			
							<label title='[
								"interpolate",
								["linear"],
								["band", 1],
								0,   [0, 0, 0, 0],  
								2, [14, 154, 159, 0.5], 
								5, [47, 170, 9, 0.5],   
								10, [161, 136, 8, 0.5],   
								25, [155, 57, 8, 0.5] ,   
								50, [8, 26, 134, 0.5]   ,   
								100, [70, 8, 92, 0.5]     
							]'>Tabla de color raster:</label>
							<textarea name='simbologia_raster'></textarea>
							
							
							
							<label title='{
							"1":{"tx":"Cuerpos de Agua (-0.01)", "color":"3, 255, 251, 0.5"},
							"2":{"tx":"Suelo desnudo (-0.01 - 0.25)", "color":"255, 0, 0, 0.5"},
							"3":{"tx":"Vegetación Dispersa (0.126 - 0.428)", "color":"161, 255, 129, 0.5"},
							"4":{"tx":"Vegetación densa (0.126 - 0.428)", "color":"101, 177, 57, 0.5"}
							}'>Etiquetas raster:</label>
							<textarea name='etiquetas_raster'></textarea>
					
					</div>
			
				</div>
			</div>
		
			
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
	var _IdUsu='<?php echo $_SESSION[$CU]["usuario"]['id'];?>';
	
	if(_IdUsu>0){document.querySelector('#menu_admin_flotante').style.display='block';}
	
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

	var _DataPaneles={}; // configuración de los paneles del marco

	var _DataPanel={}; // configuración del panel cargado
	var _DataConsultas={}; // relevamiento de opinion
	var _DataCapas={}; // capas base complemento
	var _DataCapasColec={}; // capas listado de objetos
	var _DataDocs={}; // Listado de documentos públicos, (se utilizan para capas raster)

	var _EstilosCapa={};
	
	var _sCapa = {};//source para layer de capa referencia
	var _lyrCapa = {};//layer de capa referencia
	var _feat={};
	var _AccMapa='selecciona';//accion en mapa: marca punto (marca) o selecciona objeto (selecciona)
	
	var _sDato={};// source de tipo geotiff.
	var _lDato={};// layer de tipo geotiff.
	var _lDato_id='';// id capa raster.
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

<script type="text/javascript" src="./app_comu/app_comu_js_mapa.js?t=<?php echo time();?>"></script> <!-- carga funciona de gestión de mapa-->
<script type="text/javascript" src="./app_comu/app_comu_js_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_comu/app_comu_js_pagina.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_comu/app_comu_js_mostrar.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./comun_consultas/comunes_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript">
	
	
	
	cargaBaseLink(); //en comun_mapa_link.js
	
	//inicializarColumnas(); //en app_rele_pagina.js define variables en estado false.


	cargarListadoPaneles(); // en app_comu_js_consultas.js
	consultaDocsPub();//  en app_comu_js_consultas.js
	
	function resetVarGlobales(){
/*
		_Reles={};
		_Colecciones={};
		_Complemento={};
*/
		//_DataPaneles={}; // configuración de los paneles del marco

		_DataPanel={}; // configuración del panel cargado
		_DataConsultas={}; // relevamiento de opinion
		_DataCapas={}; // capas base complemento
		_DataCapasColec={}; // capas listado de objetos

		//_EstilosCapa={};
		
		//_sDato={};// source de tipo geotiff.
		//_lDato={};// layer de tipo geotiff.
		_lDato_id='';// id capa raster.		
		
	}
	
</script>

</body>
