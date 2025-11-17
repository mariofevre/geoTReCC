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

$idUsuario = $_SESSION[$CU]["usuario"]['id'];


global $PROCESANDO;
$PROCESANDO='si';

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




$Acc=0;
$minAcc=2;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<$minAcc){
    $Log['mg'][]=utf8_encode('No cuenta con permisos (nivel '.$minAcc.' vs nivel '.$Acc.') para consultar un indicador. En el marco de investigación código '.$_POST['codMarco']);
    $Log['res']='err';
    terminar($Log);	
}


if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['idcampa']) || $_POST['idcampa']<1){
	$Log['res']='err';
	$Log['tx'][]='falta id de campania';	
	terminar($Log);
}
if($_POST['modo']==''){
	$Log['res']='err';
	$Log['tx'][]='falta el modo de elmintacion';	
	terminar($Log);
}
if($_POST['modo']!='todos'&&$_POST['modo']!='propios'&&$_POST['modo']!='registro'){
	$Log['res']='err';
	$Log['tx'][]='el modo de eliminacion deber ser uno de estos: todos / propios';	
	terminar($Log);
}


$query="
SELECT 
	id, nombre, descripcion, id_p_ref_capasgeo, ic_p_est_02_marcoacademico, 
	fechadesde, fechahasta, usu_autor, zz_borrada, zz_publicada
	FROM $DB.ref_rele_campa
	WHERE
	id = '".$_POST['idcampa']."'
	AND
	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
";
$Consulta = pg_query($ConecSIG,utf8_encode($query));
//$Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
    $Log['res']='error';
    $Log['tx'][]='error al insertar registro en la base de datos';
    $Log['tx'][]=pg_errormessage($ConecSIG);
    $Log['tx'][]=$query;
    terminar($Log);
}	
$f=pg_fetch_assoc($Consulta);

if($f['id_p_ref_capasgeo']>0){
	
	$IdCapa=$f['id_p_ref_capasgeo'];

}else{
	
	$Log['res']='err';
	$Log['tx'][]='no se encontro la capa';	
	terminar($Log);
	
}

if($_POST['modo']=='todos'){
	
	$query="UPDATE
	           $DB.ref_capasgeo_registros
	       	SET
	           	zz_borrada='1',
	           	zz_auto_borra_usu='".$idUsuario."',
	           	zz_auto_borra_fechau='".time()."'
	        WHERE    
	           id_ref_capasgeo='".$IdCapa."'
	           AND
	           zz_borrada='0'
	";
	
}elseif($_POST['modo']=='propios'){
	
	$query="UPDATE
	           $DB.ref_capasgeo_registros
	       	SET
	           	zz_borrada='1',
	           	zz_auto_borra_usu='".$idUsuario."',
	           	zz_auto_borra_fechau='".time()."'
	           	
	        WHERE    
	           id_ref_capasgeo='".$IdCapa."'
	           AND
	           zz_auto_crea_usu='".$idUsuario."'
	           AND
	           zz_borrada='0'
	";
	
}elseif($_POST['modo']=='registro'){
	
	if(!isset($_POST['idgeom'])){
		$Log['res']='err';
		$Log['tx'][]='falta el idgeom';	
		terminar($Log);	
	}
	
	$Log['data']['idgeom']=$_POST['idgeom'];
	
	$query="UPDATE
	           $DB.ref_capasgeo_registros
	       	SET
	           	zz_borrada='1',
	           	zz_auto_borra_usu='".$idUsuario."',
	           	zz_auto_borra_fechau='".time()."'
	           	
	        WHERE    
	           id_ref_capasgeo='".$IdCapa."'
	           AND
	           zz_borrada='0'
	           AND
	           id='".$_POST['idgeom']."'
	";	
}
//$Log['tx'][]=$query;
$Consulta = pg_query($ConecSIG,utf8_encode($query));
//$Log['tx'][]=$query;
    $Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
    $Log['res']='error';
    $Log['tx'][]='error al insertar registro en la base de datos';
    $Log['tx'][]=pg_errormessage($ConecSIG);
    $Log['tx'][]=$query;
    terminar($Log);
}	


$Log['res']='exito';	
terminar($Log);
