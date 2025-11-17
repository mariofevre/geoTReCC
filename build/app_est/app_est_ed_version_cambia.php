<?php
/**
* 
 * 
 ***  
* @package    	geoTReCC
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar>
* @author    	http://www.trecc.com.ar
* @author		based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
* @copyright	2025 TReCC SA* 
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2018 - Universidad de Buenos Aires - geoGEC
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
chdir('..');
include_once("./_config_acc/claveunica.php");
include_once("./_config_inst/config.php");

// verificación de seguridad 
if(!isset($_SESSION)) {
	session_start(); 
	if(!isset($_SESSION[$CU]["usuario"]['id'])){
		$_SESSION[$CU]["usuario"]['id']='-1';
	}
}

include_once('./comun_general/encabezado.php');
include_once("./comun_general/pgqonect.php");
include_once("./sis_usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php

// funciones frecuentes
include("./comun_general/fechas.php");
include("./comun_general/cadenas.php");

$COD = isset($_GET['cod'])?$_GET['cod'] : '';
$ID = isset($_GET['id'])?$_GET['id'] : '';
if($ID==''&&$COD==''){
	header('location: ./index.php');
}

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d"); $HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

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

if(!isset($_POST['tabla'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para guardar una versión. Consulte al administrador');
	$Log['tx'][]='error, no se recibió la variable tabla';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['accion'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para guardar una versión. Consulte al administrador');
	$Log['tx'][]='error, no se recibió la variable tabla';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['id'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para guardar una versión. Consulte al administrador');
	$Log['tx'][]='error, no se recibió la variable id de varsión';
	$Log['res']='err';
	terminar($Log);	
}

$query="
	SELECT table_name FROM information_schema.tables 
	WHERE table_schema = 'geogec' and table_name = '".$_POST['tabla']."' 
	order by table_name
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
if(pg_num_rows($ConsultaProy)<1){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='no se encontró la tabla solicitada en la base de datos';
	$Log['res']='err';
	terminar($Log);	
}


if($Usu['acc']['est']['gral']<3){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='no cuenta con permisos para generar una nueva version de una capa base de geogec';
	$Log['res']='err';
	terminar($Log);	
}

$query="
        SELECT  *
        FROM    $DB.sis_versiones
        WHERE 
  		tabla = '".$_POST['tabla']."' 
  	AND
 	 	zz_borrada = '0'
  	AND
 	 	zz_publicada = '0'
  	AND
  		usu_autor = '".$_SESSION[$CU]["usuario"]['id']."'
  	AND
  		id = '".$_POST['id']."'
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
UPDATE $DB.sis_versiones
   SET 
       instrucciones='".$_POST['instrucciones']."', fi_prj='".$_POST['fi_prj']."'
 WHERE 
 	id='".$_POST['id']."'
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
