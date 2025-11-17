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
	$Log['tx'][]='no fue enviada la varaible idMarco';
        $Log['mg'][]='no fue enviada la variable idMarco o codMarco';
	$Log['res']='err';
	terminar($Log);	
}	

if(!isset($_POST['idit'])){
	$Log['tx'][]='no fue enviada la varaible idit que indica la plafinicacion a la cual se agna el responsable';
	$Log['res']='err';
	terminar($Log);	
}	

if(!isset($_POST['iddoc'])){
	$Log['tx'][]='no fue enviada la varaible iddoc que indica el id del documento';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['nuevoestado'])){
	$Log['tx'][]='no fue enviada la varaible nuevoestado (incluido/excluido)';
	$Log['res']='err';
	terminar($Log);	
}
$comentario = '';
if(isset($_POST['comentario'])){
    $comentario = $_POST['comentario'];
}	
$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_plan'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_plan'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}
$minacc=2;
if($Acc<$minacc){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para modificar los documentos asociados a la planificación de este marco académico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
	$Log['tx'][]=print_r($Usu,true);
	$Log['res']='err';
	terminar($Log);
}

$query="SELECT 
                id, 
                id_sis_planif_plan,
                id_ref_doc_links,
                comentario
	FROM
		$DB.sis_planif_links
	WHERE
		id_sis_planif_plan='".$_POST['idit']."'
	AND
		id_ref_doc_links='".$_POST['iddoc']."'
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}

if($_POST['nuevoestado']=='excluido'){
    if(pg_num_rows($ConsultaProy)!=0){
        $query="DELETE FROM $DB.sis_planif_links
                WHERE 
                        id_sis_planif_plan='".$_POST['idit']."'
                AND
                        id_ref_doc_links='".$_POST['iddoc']."'
        ";
    } else {
        //Si no encontro la relacion, no hace falta borrarla
        $Log['tx'][]='No hay nada que actualizar';
        $Log['res']='exito';
        terminar($Log);
    }
}elseif($_POST['nuevoestado']=='incluido'){
    if ($comentario == ''){
        $comentario = "NULL";
    } else {
        $comentario = "'".$comentario."'";
    }
    if(pg_num_rows($ConsultaProy)==0){
        $query="INSERT INTO $DB.sis_planif_links
                        (id_sis_planif_plan, 
                        id_ref_doc_links, 
                        comentario)
                VALUES (
                        '".$_POST['idit']."', 
                        '".$_POST['iddoc']."', 
                        ".$comentario."
                );
        ";	
    } else {
        $query="UPDATE
                        $DB.sis_planif_links
                SET 
                        comentario=".$comentario."
                WHERE 
                        id_sis_planif_plan='".$_POST['idit']."'
                AND 
                        id_ref_doc_links='".$_POST['iddoc']."'
        ";		
    }
} else {
    $Log['tx'][]='error: valor inesperado para la variable nuevoestado';
    $Log['res']='err';
    terminar($Log);	
}

$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['res']='err';
    terminar($Log);
}

$Log['data']=$_POST;


$Log['res']='exito';
terminar($Log);
?>