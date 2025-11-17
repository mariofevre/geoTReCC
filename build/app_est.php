<?php
/**
* gestion de elementos estructurantes (marcos de trabajo y municipios.
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
	if(!isset($_SESSION["geogec"]["usuario"]['id'])){
		$_SESSION["geogec"]["usuario"]['id']='-1';
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

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

?><!DOCTYPE html>
<head>
    <title><?php echo $CONF['plataforma']['nombre'];?></title>
    <?php include("./comun_general/meta.php");?>
	<link rel="manifest" href="pantallahorizontal.json">
	
    <link href="./comun_css/tablarelev.css" rel="stylesheet" type="text/css">
    <link href="./comun_css/general.css" rel="stylesheet" type="text/css">
    <link href="./comun_css/general_app.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./app_docs/css/app_docs.css" rel="stylesheet" type="text/css">
    <link href="./app_capa/css/app_capa.css" rel="stylesheet" type="text/css">
    <link href="./app_est/css/app_est.css" rel="stylesheet" type="text/css">
    <style>
    	#mapa{width:600px;}
    	#page > div#cuadrovalores{
			display: inline-block;
			width: 400px;
		}
    </style>
</head>

<body>
	
<script type="text/javascript" src="./terceras_js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./terceras_js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./terceras_js/ol4.2/ol-debug.js"></script>

<div estado="cerrado" id="cartel_consultando">
	<a id="cerrar" onclick="cerrarCartelConsultando();">cerrar</a>
	<img id="cargando" src="./comun_img/cargando.gif">
	<img id="error" src="./comun_img/error.png">
	<div id="mensajes"> </div>	
	<div id="consultas"> </div>
</div>

<div id="pageborde">
    <div id="page">
       
        	
		<a href='./index.php?est=est_03_candidatos&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
                <h2><?php echo $CONF['plataforma']['nombre'];?></h2>
                <p><?php echo $CONF['plataforma']['descrip'];?></p>
        </a>

		<div id='elemento'>
			<img src='' style='float:left;'>
			<h2 id='titulo'>titulo</h2>
			<div id='descripcion'>descrioción.</div>
		</div>	
        
        <div id='menutablas'>
            <h1 id='titulo'>- nombre de proyecto -</h1>
            <p id='descripcion'>- descripcion de proyecto -</p>
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
        </div>
        <div id="cuadrovalores">
        	<div id='menuacciones'>
				<div id='lista'></div>	
			</div>
            <div id='menuelementos'>
				<div id='lista'></div>	
			</div>
            
            
             <div id='formEditarCandidato' idcandidato='0' class="elementoOculto">
            
				<div class="menuAcciones" id="divMenuAccionesCrea">
	            	<h1>Acciones</h1>
	                <a onclick='accionCreaEliminar(this)' id="botonEliminar">Eliminar</a>
	                <a onclick='accionGuardarCandidato(this)' id="botonGuardar">Guardar</a>
	                <a onclick='accionPublicarCandidato(this)' id="botonPublicar">Publicar</a>
	            </div>
            
                <div class='elementoCarga'>
                    <h1>Nombre del Marco</h1>
                    <input type="text" id="marco_nombre" name='nombre'>
                    <input type="hidden" name='codigo'>
                </div>
                <div class='elementoCarga'>
                    <h1>Nombre oficial</h1>
                    <input type="text" id="marco_nombre_oficial" name='nombre_oficial'>
                </div>
                <div class='elementoCarga'>
                    <h1>Descripción</h1>
                    <textarea name='descripcion'></textarea>
                </div>
                <div class='elementoCarga'>
                    <h1>Tabla</h1>
                    <input type="text" id="marco_tabla" name='tabla' value='est_02_marcoacademico'>
                </div>
                <div class='elementoCarga'>
                    <h1>Geometría (WKT)</h1>
                    <textarea name='geotx' campoSeleccion='geotx'></textarea>
                </div>
                
                
                
            </div>
             
            <h1>Geometría</h1>
            <a onclick="alert('función en desarrollo');accionCargarNuevaCapa(this)" id='botonAnadirCapa'>Subir un shapefile a la plataforma</a><br>
            <a onclick="accionCargarmunicipios(this)" id='botonAnadirCapa'>Seleccionar de un municipio costero</a>
            
            <div id='muestra'></div>
            
            <div class="formSeleccionCapa" id="divSeleccionCapa">
                <div class='elementoCarga accionesCapa'>
                    <a id="botonCancelarCarga"  onclick='accionCancelarSeleccionCapa(this)'>Cancelar</a></br>
                </div>
            </div>
            
            <div class='formCargaCapa' id='divCargaCapa' idcapa=''>
                <div id='avanceproceso'></div>
                <div class='elementoCarga accionesCapa'>
                    <h1>Acciones</h1>
                    <a id='botonelim' onclick='eliminarCandidatoCapa(this.parentNode);' title="Eliminar Capa">Eliminar</a></br>
                </div>
				
                <div class='formCargaCapaCuerpo' id='carga'>
                    <div id='nombrecapa' class='elementoCarga'>
                        <h1>Nombre de la capa</h1>
                        <input type="text" id="capaNombre" onkeypress="editarCapaNombre(event, this)"></input>
                    </div>
                    <div id='desccapa' class='elementoCarga'>
                        <h1>Descripción</h1>
                        <textarea type="text" id="capaDescripcion" onkeypress="editarCapaDescripcion(event, this)"></textarea>
                    </div>
                    
                    <div  id='cargarGeometrias'  class='elementoCargaLargo'>                 
                    	<h1>Gargar geometrías</h1>   	
	                    <div id='earchivoscargando' class='elementoCarga'>
	                        <h2>archivos cargando</h2>
	                        <div id='archivosacargar'>
	                            <form id='shp' enctype='multipart/form-data' method='post' action='./ed_ai_adjunto.php'>			
	                                <label style='position:relative;' class='upload'>							
	                                <span id='upload' style='position:absolute;top:0px;left:0px;'>arrastre o busque aquí un archivo</span>
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
	                        <h2>archivos cargados</h2>
	                        <p id='txningunarchivo'>- ninguno -</p>
	                        <div id='archivoscargados'></div>
	                    </div>
	
	                    <div  id='ecamposdelosarchivos'  class='elementoCargaLargo'>
	                        <h2>campos identificados</h2>
	                        <p id='verproccampo'></p>
	                        <div id='camposident'></div>
	                        <a id='procesarBoton' onclick='procesarCapa(this.parentNode)' estado='inviable'>Procesar Shapefile</a>
	                    </div>
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
	
	var _IdUsu='<?php echo $_SESSION["geogec"]["usuario"]['id'];?>';
	var _Cod = '<?php echo $COD;?>';
	var _Est = '';
	var _Tabla='est_03_candidatos';
	
	var _Tablas={};
	var _TablasConf={};
	var _SelecTabla='';//define si la consulta de nuevas tablas estará referido al elmento existente de una pabla en particular; 
	var _SelecElemCod=null;//define el código invariable entre versiones de un elemento a consultar (alternativa a _SelElemId);
	var _SelecElemId=null;//define el id de un elemento a consultar (alternativa a _SelElemCod);
	
	var _Features={};
	
</script>


<script type="text/javascript" src="./comun_interac/comun_interac.js?t=<?php echo time();?>"></script> <!-- definicion de funcions comunes como la interpretacion de respuestas ajax-->

<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./app_est/app_est_consultas.js"></script> <!-- carga funciona de gestión de mapa-->



<script type="text/javascript" src="./comun_mapa/comun_mapa_inicia.js"></script> <!-- definicion de variables comunes para mapas en todos los módulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_recorte.js"></script> <!-- definicion de variables y funciones de recorte para mapas en todos los módulos-->

<script type="text/javascript" src="./index_mapa.js"></script> <!-- carga funciona de gestión de mapa-->

<script type="text/javascript" src="./app_est/app_est_mapa.js"></script> <!-- carga funciona de gestión de mapa-->
<script type="text/javascript" src="./app_est/app_est_pagina.js"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_est/app_est_Shapefile.js"></script> <!-- carga funciones de operacion del formulario central para la carga de SHP -->

<script type="text/javascript">


	
	consultarPermisos();// en app_est_consultas.js
	
	
	consultarDesarrollo(_Cod);

	//consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_03_candidatos');
	function accionCargarmunicipios(_this){
		mostrarTablaEnMapa('est_01_municipios');
		consultarCentroidesSeleccion('est_01_municipios');
	}
	
</script>

</body>
