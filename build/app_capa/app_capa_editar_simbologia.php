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

if(!isset($_POST['id_capa']) || $_POST['id_capa']<1){
	$Log['res']='error';
	$Log['tx'][]='falta id de capa';	
	terminar($Log);
}

if(!isset($_POST['id_simbologia'])){
	
	$Log['res']='error';
	$Log['tx'][]='falta id_simbologia';	
	terminar($Log);
	
}


$query="SELECT  *
        FROM    $DB.ref_capasgeo
        WHERE 
  		id='".$_POST['id_capa']."'
  	AND
 	 	zz_borrada = '0'
  	AND
  		autor = '".$idUsuario."'
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

$fila=pg_fetch_assoc($Consulta);

if($fila['zz_borrada']=='1'){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='esta capa figura como borrada. no puede proseguir';
	$Log['res']='err';
	terminar($Log);	
}
if($fila['autor']!=$idUsuario){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='usted no figura como el autor de esta capa: '.$idUsuario;
	$Log['res']='err';
	terminar($Log);	
}

$query = '';

if($_POST['id_simbologia']<0){
	$Log['res']='err';
	terminar($Log);	
}

if($_POST['id_simbologia']==0){

	$query = "
		UPDATE	
			$DB.ref_capasgeo
        SET 
			sld='".$_POST['sld']."'
        WHERE
			ref_capasgeo.id = '".$_POST['id_capa']."'
        AND
        	ref_capasgeo.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
		AND
			ref_capasgeo.autor='".$idUsuario."'
    ;";
}elseif($_POST['id_simbologia']>=0){

	$query = "
		UPDATE	
			$DB.ref_capasgeo_simbologias
        SET 
			sld='".$_POST['sld']."'
        WHERE
			id = '".$_POST['id_simbologia']."'
        AND
        	ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
		AND
			id_p_ref_capasgeo = '".$_POST['id_capa']."'
			
    ;";
}



$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
}

$fila=pg_fetch_assoc($Consulta);


$Log['res']="exito";


terminar($Log);
