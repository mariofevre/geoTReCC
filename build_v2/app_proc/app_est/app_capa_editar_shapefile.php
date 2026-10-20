<?php 
/**
*
* aplicación para actualizar una versión candidata para la carga de archivos shapefile a una base de datos espacial
*  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrolló sobre una publicación GNU 2017 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (GPL-3.0)
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
*/

ini_set('display_errors', 1);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";

if(!isset($_SESSION)) { session_start(); }

// funciones frecuentes
include($GeoGecPath."/includes/fechas.php");
include($GeoGecPath."/includes/cadenas.php");
include($GeoGecPath."/includes/pgqonect.php");
include_once($GeoGecPath."/usuarios/usu_validacion.php");
$Usu= validarUsuario();


require_once($GeoGecPath.'/classes/php-shapefile/src/ShapeFileAutoloader.php');
\ShapeFile\ShapeFileAutoloader::register();
// Import classes
use \ShapeFile\ShapeFile; 
use \ShapeFile\ShapeFileException;

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

$Log['data']=array();
$Log['tx']=array();
$Log['mg']=array();
$Log['res']='';
function terminar($Log){
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	echo $res;
	exit;	
}

if(!isset($_POST['id'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para guardar una versión. Consulte al administrador');
	$Log['tx'][]='error, no se recibió la variable id de la capa';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<2){
    $Log['mg'][]=utf8_encode('No cuenta con permisos (nivel 2 vs nivel '.$Acc.') para generar una nueva capa en la plataforma geoGEC. En el marco de investigación código '.$_POST['codMarco']);
    $Log['res']='err';
    terminar($Log);	
}

if(!isset($_POST['instrucciones'])){
	$Log['tx'][]='no fue enviada la variable instrucciones';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['fi_prj'])){
	$Log['tx'][]='no fue enviada la variable fi_prj';
	$Log['res']='err';
	terminar($Log);	
}

if($_POST['fi_prj']==''){
	$Log['tx'][]='no fue enviado un valor valido la variable fi_prj';
	$Log['mg'][]='por favor selecciones la proyección de los datos antes de procesar la capa';
	$Log['res']='err';
	terminar($Log);
}

//el json de las instrucciones se guarda en la session

$_SESSION['instrucciones'] = $_POST['instrucciones'];
$_SESSION['fi_prj'] = $_POST['fi_prj'];



$query="SELECT  *
        FROM    
        	geogec.ref_capasgeo
        WHERE 
	 	 	zz_borrada = '0'
	  	AND
	 	 	zz_publicada = '0'
	  	AND
	  		autor = '".$_SESSION["geogec"]["usuario"]['id']."'
	  	AND
	  		id = '".$_POST['id']."'
	  	AND
			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
 ";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if(pg_num_rows($ConsultaVer)<0){
	$Log['mg'][]='error interno no se encontro la versión con el id enviado';
	$Log['res']='err';
	terminar($Log);	
}

$query="
	UPDATE 
		geogec.ref_capasgeo
   	SET 
       	zz_instrucciones='".$_POST['instrucciones']."', 
       	srid='".$_POST['fi_prj']."'
 	WHERE 
 		id='".$_POST['id']."'
	AND
		autor = '".$_SESSION["geogec"]["usuario"]['id']."'
	AND
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}



$Log['res']='exito';
terminar($Log);
