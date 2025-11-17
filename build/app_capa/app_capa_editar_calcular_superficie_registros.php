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
$minacc=2;
if($Acc<$minacc){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificación de este marco académico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
	$Log['tx'][]=print_r($Usu,true);
	$Log['res']='err';
	terminar($Log);
}

$idUsuario = $_SESSION[$CU]["usuario"]['id'];

if(!isset($_POST['idgeom'])){
	$Log['res']='error';
	$Log['tx'][]='falta id de idgeom';	
	terminar($Log);
}


if(!isset($_POST['idcapa'])){
	$Log['res']='error';
	$Log['tx'][]='falta id de idcapa';	
	terminar($Log);
}


if(!isset($_POST['modo'])){
	$Log['res']='error';
	$Log['tx'][]='falta id de modo';	
	terminar($Log);
}
$modosPermitidos=Array(
	's_km2'=>array('mult'=>'/1000000','tipo'=>'sup'),
	's_Ha'=>array('mult'=>'/10000','tipo'=>'sup'),
	's_m2'=>array('mult'=>'/1','tipo'=>'sup'),
	'l_km'=>array('mult'=>'/1000','tipo'=>'lon'),
	'l_m'=>array('mult'=>'/1','tipo'=>'lon')
);
if(!isset($modosPermitidos[$_POST['modo']])){
	$Log['res']='error';
	$Log['tx'][]='modo invalido';	
	terminar($Log);
}
$ModData=$modosPermitidos[$_POST['modo']];


if(!isset($_POST['campo'])){
	$Log['res']='error';
	$Log['tx'][]='falta id de campo';	
	terminar($Log);
}

$camposPermitidos=Array(
	'campo_numero_1'=>'',
	'campo_numero_2'=>'',
	'campo_numero_3'=>'',
	'campo_numero_4'=>'',
	'campo_numero_5'=>''
);

if(!isset($camposPermitidos[$_POST['campo']])){
	$Log['res']='error';
	$Log['tx'][]='campo invalido';	
	terminar($Log);
}



$query="SELECT  *
        FROM    $DB.ref_capasgeo
        WHERE 
  		id='".$_POST['idcapa']."'
  	AND
 	 	zz_borrada = '0'
 	AND
 		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
if(pg_num_rows($Consulta)<1){
	$Log['tx'][]=utf8_encode('error: No se encotró la capa solicitad');
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

$fila=pg_fetch_assoc($Consulta);


$campo=str_replace("campo_","",$_POST['campo']);
$campo=str_replace("_","",$campo);

if(
	$fila['tipogeometria']=='Polygon'
){
	
	if($ModData['tipo']!='sup'){
		$Log['tx'][]=utf8_encode('el tipo de calculo no es compatible con el tipo de geometría declarada para esta capa');
		$Log['mg'][]=utf8_encode('el tipo de calculo no es compatible con el tipo de geometría declarada para esta capa');
		$Log['res']='err';
		terminar($Log);
	}

	
	$query = "
	UPDATE 
		$DB.ref_capasgeo_registros	
		SET
			".$campo." = ST_Area(ST_Transform(geom, 22175))".$ModData['mult']."
		WHERE 
			id_ref_capasgeo='".$_POST['idcapa']."'
	";


}elseif(
	$fila['tipogeometria']=='LineString'
){
	
	if($ModData['tipo']!='lon'){
		$Log['tx'][]=utf8_encode('el tipo de calculo no es compatible con el tipo de geometría declarada para esta capa');
		$Log['mg'][]=utf8_encode('el tipo de calculo no es compatible con el tipo de geometría declarada para esta capa');
		$Log['res']='err';
		terminar($Log);
	}

	
	$query = "
	UPDATE 
		$DB.ref_capasgeo_registros	
		SET
			".$campo." = ST_Area(ST_Transform(geom_line, 4326))".$ModData['mult']."
		WHERE 
			id_ref_capasgeo='".$_POST['idcapa']."'
	";
	
	
}elseif(
	$fila['tipogeometria']=='Point'
){
	$Log['tx'][]='error: No puedo interpretar el tipo de geometría que se pretende guardar';
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}else{
	$Log['tx'][]='error: No puedo interpretar el tipo de geometría que se pretende guardar';
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}



$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
        $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
        $Log['tx'][]='query: '.$query;
        $Log['mg'][]='error interno';
        $Log['res']='err';
        terminar($Log);	
}
$Log['tx'][]=$query;

$Log['tx'][]="Editada capa id: ".$_POST['idcapa'];
$Log['data']['idgeom']=$_POST['idgeom'];
$Log['data']['idcapa']=$_POST['idcapa'];
$Log['res']="exito";

terminar($Log);
