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

//$idUsuario = $_SESSION[$CU]["usuario"]['id'];



$query = "

	SELECT
 		id, ic_p_est_02_marcoacademico, fechadesde, fechahasta, id_p_ref_02_pseudocarpetas, texto
	FROM 
		$DB.ref_cart_publicaciones
	WHERE
		ic_p_est_02_marcoacademico =  '".$_POST['codMarco']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
while($fila=pg_fetch_assoc($Consulta)){
    $Log['tx'][]= "Consulta de indicador existente id: ".$fila['id'];
    $Log['data']['publicaciones'][$fila['id']]=$fila;
	if(
		($fila['fechadesde']=='' || $fila['fechadesde']<=$HOY)
		&&
		($fila['fechahasta']=='' || $fila['fechahasta']>=$HOY)
	){
		$Log['data']['activas'][$fila['id']]=$fila;
	}
}


$query = "
	SELECT 
		ref_01_documentos.id as iddocs,  
		ref_01_documentos.nombre, 
		ref_01_documentos.archivo,  
		ref_01_documentos.descripcion, 
		
		ref_02_pseudocarpetas.id iddocscarpeta,  
		ref_02_pseudocarpetas.zz_borrada, 
		ref_02_pseudocarpetas.nombre as nombrecarpeta, 
		ref_02_pseudocarpetas.descripcion as descripcioncarpeta,
		ref_02_pseudocarpetas.ic_p_est_02_marcoacademico
		
	FROM 
		$DB.ref_01_documentos
	RIGHT JOIN
		$DB.ref_02_pseudocarpetas ON ref_01_documentos.id_p_ref_02_pseudocarpetas = ref_02_pseudocarpetas.id 
	RIGHT JOIN
		$DB.ref_cart_publicaciones ON ref_cart_publicaciones.id_p_ref_02_pseudocarpetas = ref_02_pseudocarpetas.id
	WHERE
		ref_cart_publicaciones.ic_p_est_02_marcoacademico =  '".$_POST['codMarco']."'
		AND
		ref_01_documentos.zz_borrada='0'
	ORDER BY 
		ref_01_documentos.orden ASC
		
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
while($fila=pg_fetch_assoc($Consulta)){
		$Log['data']['documentos'][$fila['iddocs']]=$fila;
		$Log['data']['documentosOrden'][]=$fila['iddocs'];		
}


$Log['res']="exito";
terminar($Log);
