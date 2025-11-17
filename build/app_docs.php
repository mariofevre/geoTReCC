<?php 
/**
* aplicación de visualización y gestion de documentos de trabajo. consulta carga y genera la interfaz de configuración de los mismos.
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


ini_set('display_errors', '1');
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

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

?><!DOCTYPE html>
<head>
    <title><?php echo $CONF['plataforma']['nombre'];?></title>
    <?php include("./includes/meta.php");?>
    <link href="./css/mapauba.css" rel="stylesheet" type="text/css">
    <link rel="manifest" href="pantallahorizontal.json">
    
    <link href="./css/geogecgeneral.css" rel="stylesheet" type="text/css">    
    <link href="./css/geogec_app.css" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app_docs.css" rel="stylesheet" type="text/css">
    
    <style type='text/css'>
    	
    	#editoritem > #botonanadir {
    		top: 12vh;
    	}
    	
    	
    	.documentos > a > a {
    		border: none;
    		width:auto;
    		height:auto;
    	}	
    	
    
    	#botondescarga{
    		position: absolute;
			width: auto;
			text-align: right;
			right: 1vw;
			top: 14vh;
			background: transparent;
			border: none;
			margin: 0;
			padding: 0;
    	}
    	
    	#publicacion{
    		position: absolute;
			width: auto;
			text-align: right;
			right: 1vw;
			top: calc(14vh + 44px);
			font-size: 12px;
    	}
    	
    	body[modopublico='si'] input[value='guardar']{
    		display:none;    		
    	}
    	
    	body[modopublico='si'] .hijos{
    		min-height: 0px;
    	}
    	
    	body[modopublico='si'] textarea{
    		background-color:transparent;
    	}
    	
    	body[modopublico='si'] #archivos{
    		display:none;
    	}
    	
    	body[modopublico='si'] #menuacciones{
    		display:none;
    	}
    	
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
                <h2><?php echo $CONF['plataforma']['nombre'];?></h2>
                <p><?php echo $CONF['plataforma']['descrip'];?></p>
            </a>

            <div id='elemento' tipo="Accion">
                <img id='AccLogoHd' src='' style='float:left;'>
                <h2 id='titulo'></h2>
                <div id='descripcion'></div>
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
            <div id='titulomapa'>
                <p id='tnombre'></p>
                <h1 id='tnombre_humano'></h1>
                <p id='tdescripcion'></p>
                <b><p id='tseleccion'></p></b>
            </div>
        </div>
        <div id='modelos'>
            <div class='item'
                 idit='nn'
                 draggable="true"
                 ondragstart="dragcaja(event);bloquearhijos(event,this);"
                 ondragleave="limpiarAllowFile()"
                 ondragover="allowDropFile(event,this)"
                 ondrop='dropFile(event,this)'>
                <h3 onmouseout='desaltar(this)' onmouseover='resaltar(this)' onclick='editarI(this)'>nombre de la caja</h3>
                <p onmouseout='desaltar(this)' onmouseover='resaltar(this)' onclick='editarI(this)'>descipcion del contenido de la caja</p>
                <div id='avisopublico'><img src='./img/candado_abierto.png'></div>
                <div id='avisocerrado'><img src='./img/candado_cerrado.png'></div>
                <div class='documentos'>
                </div>
                <div class='hijos'
                     ondrop="drop(event,this)"
                     ondragover="allowDrop(event,this)"
                     ondragleave="limpiarAllow()">
                </div>
            </div>
        </div>

        <div id="archivos">
        	
            <form action='' enctype='multipart/form-data' method='post' id='uploader' ondragover='resDrFile(event)' ondragleave='desDrFile(event)'>
                <div id='contenedorlienzo'>									
                    <div id='upload'>
                        <label>Arrastre todos los archivos aquí.</label>
                        <input multiple='' id='uploadinput' type='file' name='upload' value='' onchange='cargarCmp(this);'>
                    </div>
                </div>
                <div id='contenedorlienzo'>									
                    <div id='upload'>
                        <label>O cree un link aquí.</label>
                        <a id='uploadinputlink' name='uploadlink' onclick='formcrearlink(event,this)'>O cree un link aquí.</a>
                    </div>
                </div>
            </form>
            <div id="listadosubiendo">
                <label>archivos subiendo...</label>
            </div>
            <div id="listadoaordenar">
                <label>archivos subidos.</label>
            </div>

            <div id="eliminar"
                 ondragover="allowDropFile(event,this)"
                 ondragleave="limpiarAllowFile()"
                 ondrop='dropTacho(event,this)'>
                <br>X
                <span>tacho de basura</span>
            </div>		
            
            <a id='botonanadir' onclick='anadirItem("0")'>+ <br><span>nueva <br> caja</span></a>
        </div>	

        <div id="contenidoextenso" idit='0'>
            
            <div class='hijos'
                 nivel="0"
                 ondrop="drop(event,this)" 
                 ondragover="allowDrop(event,this);resaltaHijos(event,this)" 
                 ondragleave="desaltaHijos(this)">
            </div>
        </div>
    </div>	
</div>


<form id="editordoc" onsubmit="guardarD(event,this)">
	<div id='autor'><label>por: <span id='nombreapellido'></span></label></div>
    <input name='id' type='hidden'>
    <label id='nombre'>--</label>
    <textarea name='descripcion'></textarea>
    <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
    <input type='submit' value='guardar'>
    <a style='display:none' id='botonelimina' onclick='eliminarD(event,this)'>eliminar</a>
    <a id='botondescarga'><img alt='descargar' src='./img/descargar_archivo.png'></a>
</form>

<form id="editoritem" onsubmit="guardarI(event,this)">
    <label>Nombre de la caja</label>
    <input name='nombre'>
    <input name='id' type='hidden'>
    <label>Descripcion del contenido de la caja</label>
    <textarea name='descripcion'></textarea>
    
    <label>Contenidos</label>
    <div id='contenidos'>
    	
    	
    </div>
    
    <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
    <input type='submit' value='guardar'>
    <a id='botonelimina' onclick='eliminarI(event,this)'>eliminar</a>
    <a id='botonanadir' onclick="anadirItem(this.parentNode.querySelector('input[name=\'id\']').value)">+ <br><span>nueva <br> caja</span></a>
    
    <div id='publicacion'>Publicación <br><select name='publica'>
    	<option value='no'>no</option>    	
    	<option value='usuarie'>Publicar para usuaries geoGEC</option>
    	<option value='cualquiera'>Publicar para cualquiera</option>
    </select></div>
</form>

<form id="formcrearlink" onsubmit="cargarCmpLink(event,this)">
    <label>Ingresar Link</label>
    <label>Nombre del Link</label>
    <input name='linkName' type='text'>
    <label>URL</label>
    <input name='linkUrl' type='url'>
    <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
    <input type='submit' value='guardar'>
    <a id='botonelimina' onclick='eliminarLink(event,this)'>eliminar</a>
</form>

<form id="editarlink" onsubmit="guardarLink(event,this)">
    <input name='id' type='hidden'>
    <label id='nombre'>--</label>
    <label id='linkUrl'>URL</label>
    <textarea name='descripcion'></textarea>
    <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
    <input type='submit' value='guardar'>
    <a style='display:none' id='botonelimina' onclick='eliminarLink(event,this)'>eliminar</a>
    <a id='botondescarga' target='blank'><img alt='ir a link' src='./img/dirigir_link.png'></a>
</form>



<script type='text/javascript'>

	var _ConsultasPHP = {
		"cons_real" : 0,  //cantidad de consultas realizadas hasta el momento   
		"cons_resp" : 0,  //cantidad de consultas respondidas hasta el momento
		"consultas" : {},
		"mensajes" : []
	};
	
	
    ///funciones para cargar información base
    var _ModoAcc='<?php echo $_SESSION["geogec"]["usuario"]['id'];?>';
    
    var _IdMarco='<?php echo $ID;?>';
    var _CodMarco='<?php echo $COD;?>';	
    var _Items=Array();
    var _Docs=Array();
    var _DocLinks=Array();
    var _Orden=Array();

    var _nFile=0;
    var _nLink=0;

    var xhr=Array();
    var inter=Array();
</script>


<script type="text/javascript" src="./comun_interac/comun_interac.js?t=<?php echo time();?>"></script> <!-- definicion de funcions comunes como la interpretacion de respuestas ajax-->

<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->

<script type="text/javascript" src="./app_docs/app_docs_consultas.js?v=<?php echo time();?>"></script>
<script type="text/javascript" src="./app_docs/app_docs_muestra.js?v=<?php echo time();?>"></script>
<script type="text/javascript" src="./app_docs/app_docs_interaccion.js?v=<?php echo time();?>"></script>


<script type='text/javascript'>

	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
	
	if(_ModoAcc=='-1'){
		
		document.querySelector('body').setAttribute('modopublico','si');
		
		_tas=document.querySelectorAll('textarea');
		for(_tan in _tas){
			if(typeof _tas[_tan] !='object'){continue;}
			_tas[_tan].setAttribute('readonly','true');
		}
		//document.querySelector('#archivos').style.display='none';
		//document.querySelector('#menuacciones').style.display='none';
		cargaBasePublica();
	}else{
		cargaBase();	
	}
    
</script>

</body>
