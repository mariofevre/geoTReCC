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

/*
$minacc=0;//se permite esta consulta aún a no usuarios. TODO verificar seguridad de esto.
if(isset($_POST['nivelPermiso'])){
    $minacc=$_POST['nivelPermiso'];
}

$Acc=0;

$Accion='app_publ';
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$Accion])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$Accion];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<$minacc){
    $Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificación de este marco académico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
    $Log['tx'][]=print_r($Usu,true);
    $Log['res']='err';
    terminar($Log);
}
*/
if(!isset($_POST['busqueda'])){
	$Log['tx'][]='no fue enviada la variable zz_publicada';
	$Log['res']='err';
	terminar($Log);	
}
if(strlen($_POST['busqueda'])<3){$Log['res']="exito";terminar($Log);}

if(isset($_SESSION[$CU]["usuario"])){
	$idUsuario = $_SESSION[$CU]["usuario"]['id'];
}else{
	$idUsuario='';
}

$Log['data']['busquedatipo']='publ';

//datos generales de la sesion
$query="
	SELECT 
		ref_publ_publicacion.id, 
		ref_publ_publicacion.titulo as nombre, 
		ref_publ_publicacion.autoria,
		ST_AsText(ref_publ_publicacion.area) as areatx,
		ref_publ_publicacion.url, 
		ref_publ_publicacion.id_p_ref_01, 
		ref_publ_publicacion.ic_p_est_02_marcoacademico, 
		ref_publ_publicacion.zz_borrada, 
		ref_publ_publicacion.ano, 
		ref_publ_publicacion.mes, 
		ref_publ_publicacion.usu_ultimaed, 
		ref_publ_publicacion.fechau_ultimaed,
		
		ref_publ_publicacion.observaciones as descripcion,
		ref_publ_publicacion.id_p_ref_publ_tipos,
		
		
		ref_01_documentos.nombre as doc_nombre, 
		ref_01_documentos.archivo as doc_archivo,
		ref_01_documentos.descripcion as doc_descripcion,
		
		
		sis_usu_registro.nombre as doc_unombre,
		sis_usu_registro.apellido as doc_uapellido
		
	FROM 
		$DB.ref_publ_publicacion
	LEFT JOIN
		$DB.ref_01_documentos
		ON
			ref_01_documentos.id = ref_publ_publicacion.id_p_ref_01
			AND
			ref_01_documentos.ic_p_est_02_marcoacademico = ref_publ_publicacion.ic_p_est_02_marcoacademico
			
	LEFT JOIN
  		$DB.sis_usu_registro ON sis_usu_registro.id = ref_01_documentos.zz_auto_crea_usu
	
	WHERE 
        ref_publ_publicacion.zz_borrada = '0'
 		AND
 	  (
	 	  ref_publ_publicacion.titulo LIKE '%".$_POST['busqueda']."%'
	 	  OR
	 	  ref_publ_publicacion.autoria LIKE '%".$_POST['busqueda']."%'
	 	  OR
	 	  ref_publ_publicacion.observaciones LIKE '%".$_POST['busqueda']."%'
	 	  OR
	 	  ref_01_documentos.descripcion LIKE '%".$_POST['busqueda']."%'
	 	  OR
	 	  ref_01_documentos.nombre LIKE '%".$_POST['busqueda']."%'
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

$Log['data']['resultados']=Array();
while($row = pg_fetch_assoc($Consulta)){
	$Log['data']['resultados'][$row['id']]=$row;
}



$Log['res']="exito";
terminar($Log);
