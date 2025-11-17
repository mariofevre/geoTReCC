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
	global $PROCESANDO;
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	if(isset($PROCESANDO)){
		return;	
	}else{
		echo $res;
		exit;
	}	
}

$Acc=0;
if(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<3){
    $Log['mg'][]=utf8_encode('No cuenta con permisos (nivel 3 vs nivel '.$Acc.') para consultar un indicador.');
    $Log['res']='err';
    terminar($Log);	
}
 
if(!isset($_POST['codigo'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para crear un nuevo marco academico. Consulte al administrador');
	$Log['tx'][]='error, no se recibio la variable descripcion';
	$Log['res']='err';
	terminar($Log);	
}


	$query="
		SELECT
			*
			FROM
			$DB.ref_02_marcoacademico_link_acciones
		WHERE 
			ic_p_est_02_marcoacademico = '".$_POST['codigo']."'
	";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
$Acc=Array();
while( $fila= pg_fetch_assoc($Consulta)){
	$Acc[$fila['codigo_p_sis_acciones']]=$fila['activo'];	
}


foreach($_POST as $k => $v){
	if(substr($k,0,4)=='app_'){
		
		if(!isset($Acc[$k])){
			$query="		
				INSERT INTO
				$DB.ref_02_marcoacademico_link_acciones
				(
					ic_p_est_02_marcoacademico,
					codigo_p_sis_acciones,
					activo
				)
				VALUES
				(
					'".$_POST['codigo']."',
					'".$k."',
					'".$v."'
				)
			";
			$Consulta = pg_query($ConecSIG, $query);
			if(pg_errormessage($ConecSIG)!=''){
				$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
				$Log['tx'][]='query: '.$query;
				$Log['mg'][]='error interno';
				$Log['res']='err';
				terminar($Log);	
			}
		}else{
			$query="		
				UPDATE
				$DB.ref_02_marcoacademico_link_acciones
				SET
				activo='".$v."'
				WHERE
					ic_p_est_02_marcoacademico='".$_POST['codigo']."'
					AND
					codigo_p_sis_acciones='".$k."'
			";
			$Consulta = pg_query($ConecSIG, $query);
			if(pg_errormessage($ConecSIG)!=''){
				$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
				$Log['tx'][]='query: '.$query;
				$Log['mg'][]='error interno';
				$Log['res']='err';
				terminar($Log);	
			}		
		}
		$Log['tx'][]='query: '.$query;
	}
}


$Log['res']='exito';
terminar($Log);		
