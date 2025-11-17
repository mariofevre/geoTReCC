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
	$Log['tx'][]='no fue enviada la varaible idMarco o codMarco';
	$Log['mg'][]='no fue enviada la varaible idMarco o codMarco';
	$Log['res']='err';
	terminar($Log);	
}	

if($_POST['codMarco']==''){
	$Log['tx'][]=utf8_encode('no fue solicitado un cod vaálido para marco académico');
	$Log['mg'][]=utf8_encode('no fue solicitado un cod válido para marco académico');
	$Log['res']='err';
	terminar($Log);	
}	
	
$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_docs'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_docs'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<1){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para consultar la caja de documentos de este marco académico. \n minimo requerido: 1 \ nivel disponible: '.$Acc);
	$Log['res']='err';
	terminar($Log);	
}


$query="
	SELECT 
		id, 
		id_p_ref_02_pseudocarpetas, 
		id_p_est_02_marcoacademico,
		ic_p_est_02_marcoacademico,
		orden,
		nombre, 
		descripcion,
		zz_sis,
		publica
	FROM 
  		$DB.ref_02_pseudocarpetas
	WHERE
		ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
	AND
		zz_borrada='0'
	ORDER BY 
		orden
";

$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
if(pg_num_rows($ConsultaProy)<1){
	$Log['tx'][]='no se encontraron pseudocarpetas para este marco academico';
}

$Log['data']['psdir']=array();
$Log['data']['psdir'][0]['archivos']=array();//el id de la caja 0 refiere a archivos localizados en ninguna caja
$Log['data']['psdir'][0]['archivolinks']=array();//el id de la caja 0 refiere a archivos localizados en ninguna caja
$Log['data']['orden']['psdir']=array();

while($fila=pg_fetch_assoc($ConsultaProy)){	
	//$Ord[$fila['id']]=$fila['orden'];	
	$Log['data']['psdir'][$fila['id']]=$fila;
	$Log['data']['psdir'][$fila['id']]['archivos']=Array();
        $Log['data']['psdir'][$fila['id']]['archivoLlinks']=Array();
	$Log['data']['orden']['psdir'][]=$fila['id'];	
}		


$query="
	SELECT 
		ref_01_documentos.id, 
		ref_01_documentos.id_p_est_02_marcoacademico, 
		ref_01_documentos.nombre, 
		ref_01_documentos.descripcion,
		ref_01_documentos.archivo, 
		ref_01_documentos.id_p_ref_02_pseudocarpetas, 
		ref_01_documentos.orden,
		ref_01_documentos.zz_auto_crea_usu,
		sis_usu_registro.nombre as unombre,
		sis_usu_registro.apellido as uapellido
  	FROM 
  		$DB.ref_01_documentos
  	LEFT JOIN
  		$DB.sis_usu_registro ON sis_usu_registro.id = ref_01_documentos.zz_auto_crea_usu
	WHERE
		ref_01_documentos.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'	
	AND
		zz_borrada='0'
	ORDER BY 
		orden
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
while ($fila=pg_fetch_assoc($ConsultaProy)){	
	$Log['data']['psdir'][$fila['id_p_ref_02_pseudocarpetas']]['archivos'][$fila['id']]=$fila;
	$Log['data']['psdir'][$fila['id_p_ref_02_pseudocarpetas']]['ordenarchivos'][]=$fila['id'];
}


$query="SELECT 
		id,
	    ic_p_est_02_marcoacademico,
	    id_p_ref_02_pseudocarpetas,
		zz_borrada,
		nombre,
        url,
		descripcion,
		orden
  	FROM 
  		$DB.ref_doc_links
	WHERE
		ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
	AND
		zz_borrada='0'
	ORDER BY 
		orden
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
while ($fila=pg_fetch_assoc($ConsultaProy)){	
	$Log['data']['psdir'][$fila['id_p_ref_02_pseudocarpetas']]['archivolinks'][$fila['id']]=$fila;
	$Log['data']['psdir'][$fila['id_p_ref_02_pseudocarpetas']]['ordenarchivolinks'][]=$fila['id'];
}


$Log['res']='exito';
terminar($Log);
?>
