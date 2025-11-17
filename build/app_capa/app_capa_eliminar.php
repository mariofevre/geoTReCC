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

if(!isset($_POST['id'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para guardar una versión. Consulte al administrador');
	$Log['tx'][]='error, no se recibió la variable id';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['codMarco'])){
	$Log['mg'][]=utf8_encode('error en las variables codMarco.');
	$Log['tx'][]='error, no se recibió la variable id';
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
	
$query="SELECT  *
        FROM    $DB.ref_capasgeo
        WHERE 
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

if(pg_num_rows($ConsultaVer)<1){
	$Log['mg'][]='error interno no se encontro la capa con el id enviado';
	$Log['res']='err';
	terminar($Log);	
}

$f=pg_fetch_assoc($ConsultaVer);
	
if($f['zz_borrada']=='1'){
	$Log['tx'][]='error: esta versión ha sido eliminada previamente';
	$Log['mg'][]='error: esta versión ha sido eliminada previamente';
	$Log['res']='err';
	terminar($Log);	
}


if($f['zz_publicada']=='1'&&$Acc<3){
	$Log['tx'][]='error: esta capa ya fue publicada, no puede eliminarse con sus permisos.';
	$Log['mg'][]='error: esta capa ya fue publicada, no puede eliminarse con sus permisos.';
	$Log['res']='err';
	terminar($Log);	
}

$query="UPDATE $DB.ref_capasgeo
    SET 
        	zz_borrada = '1',
        	zz_auto_borra_usu =  '".$_SESSION[$CU]["usuario"]['id']."',
        	zz_auto_borra_fechau = '".time()."'
    WHERE 
		id = '".$_POST['id']."'
	AND
		zz_borrada = '0'
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


$query="UPDATE $DB.ref_capasgeo_registros
    SET 
        	zz_borrada = '1',
        	zz_auto_borra_usu =  '".$_SESSION[$CU]["usuario"]['id']."',
        	zz_auto_borra_fechau = '".time()."'
    WHERE 
		id_ref_capasgeo = '".$_POST['id']."'
	AND
		zz_borrada = '0'
	
  	
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
