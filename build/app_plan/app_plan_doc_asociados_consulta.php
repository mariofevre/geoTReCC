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
	$Log['tx'][]='no fue enviada la variable idMarco o codMarco';
	$Log['mg'][]='no fue enviada la variable idMarco o codMarco';
	$Log['res']='err';
	terminar($Log);	
}	

if($_POST['codMarco']==''){
	$Log['tx'][]=utf8_encode('no fue solicitado un cod válido para marco académico');
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
	$Log['mg'][]=utf8_encode('no cuenta con permisos para consultar la caja de documentnos de este marco académico. \n minimo requerido: 1 \ nivel disponible: '.$Acc);
	$Log['res']='err';
	terminar($Log);	
}

$ActividadId = '';

if(isset($_POST['idactividad'])){
    if($_POST['idactividad']!=''){
        $ActividadId = $_POST['idactividad'];
    }
}

$hayResultados = 0;
$Log['data']['psdir']=array();
$Log['data']['psdir']['archivos']=array();//aca pasamos los datos de documentos linkeados
$Log['data']['psdir']['archivolinks']=array();//aca pasamos los datos de link url linkeados
$Log['data']['psdir']['carpetas']=array();//aca pasamos los datos de pseudocarpetas linkeadas

$query="SELECT 
                $DB.sis_planif_docs.id,
                $DB.sis_planif_docs.id_sis_planif_plan,
                $DB.sis_planif_docs.id_ref_01_documentos,
                $DB.sis_planif_docs.comentario,
                $DB.ref_01_documentos.id_p_est_02_marcoacademico,
                $DB.ref_01_documentos.zz_borrada,
                $DB.ref_01_documentos.nombre,
                $DB.ref_01_documentos.descripcion,
                $DB.ref_01_documentos.archivo,
                $DB.ref_01_documentos.id_p_ref_02_pseudocarpetas,
                $DB.ref_01_documentos.orden
        FROM 
                $DB.sis_planif_docs
        JOIN 
                $DB.ref_01_documentos
        ON 
                $DB.sis_planif_docs.id_ref_01_documentos = $DB.ref_01_documentos.id
        WHERE
                $DB.ref_01_documentos.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'";

if ($ActividadId != ''){
    $query = $query."AND $DB.sis_planif_docs.id_sis_planif_plan='".$ActividadId."'";
}
        
$query = $query."
        AND 
                $DB.ref_01_documentos.zz_borrada = 0
        ORDER BY 
		$DB.ref_01_documentos.orden
";

$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
if(pg_num_rows($ConsultaProy)>0){
    $hayResultados = $hayResultados + 1;
}
while ($fila=pg_fetch_assoc($ConsultaProy)){	
	$Log['data']['psdir']['archivos'][$fila['id']]=$fila;
	$Log['data']['psdir']['ordenarchivos'][]=$fila['id'];
}

$query="SELECT 
                $DB.sis_planif_links.id,
                $DB.sis_planif_links.id_sis_planif_plan,
                $DB.sis_planif_links.id_ref_doc_links,
                $DB.sis_planif_links.comentario,
                $DB.ref_doc_links.ic_p_est_02_marcoacademico,
                $DB.ref_doc_links.id_p_ref_02_pseudocarpetas,
                $DB.ref_doc_links.zz_borrada,
                $DB.ref_doc_links.nombre,
                $DB.ref_doc_links.url,
                $DB.ref_doc_links.orden,
                $DB.ref_doc_links.descripcion
        FROM 
                $DB.sis_planif_links
        JOIN 
                $DB.ref_doc_links
        ON 
                $DB.sis_planif_links.id_ref_doc_links = $DB.ref_doc_links.id
        WHERE
                $DB.ref_doc_links.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
";

if ($ActividadId != ''){
    $query = $query."AND $DB.sis_planif_links.id_sis_planif_plan='".$ActividadId."'";
}
        
$query = $query."
        AND 
                $DB.ref_doc_links.zz_borrada = 0
        ORDER BY 
		$DB.ref_doc_links.orden
";

$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
if(pg_num_rows($ConsultaProy)>0){
    $hayResultados = $hayResultados + 1;
}
while ($fila=pg_fetch_assoc($ConsultaProy)){	
	$Log['data']['psdir']['archivolinks'][$fila['id']]=$fila;
	$Log['data']['psdir']['ordenarchivolinks'][]=$fila['id'];
}

if($hayResultados == 0){
    $Log['tx'][]='no se encontraron documentos asociados para este marco academico';
}



$hayResultados = 0;


$query="SELECT 
                $DB.sis_planif_pseudocarpetas.id,
                $DB.sis_planif_pseudocarpetas.id_sis_planif_plan,
                $DB.sis_planif_pseudocarpetas.id_ref_02_pseudocarpetas,
                $DB.sis_planif_pseudocarpetas.comentario,
                $DB.ref_02_pseudocarpetas.id_p_est_02_marcoacademico,
                $DB.ref_02_pseudocarpetas.zz_borrada,
                $DB.ref_02_pseudocarpetas.nombre,
                $DB.ref_02_pseudocarpetas.descripcion,
                $DB.ref_02_pseudocarpetas.id_p_ref_02_pseudocarpetas,
                $DB.ref_02_pseudocarpetas.orden
        FROM 
                $DB.sis_planif_pseudocarpetas
        JOIN 
                $DB.ref_02_pseudocarpetas
        ON 
                $DB.sis_planif_pseudocarpetas.id_ref_02_pseudocarpetas = $DB.ref_02_pseudocarpetas.id
        WHERE
                $DB.ref_02_pseudocarpetas.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'";

if ($ActividadId != ''){
    $query = $query."AND $DB.sis_planif_pseudocarpetas.id_sis_planif_plan='".$ActividadId."'";
}
        
$query = $query."
        AND 
                $DB.ref_02_pseudocarpetas.zz_borrada = 0
        ORDER BY 
		$DB.ref_02_pseudocarpetas.orden
";

$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
if(pg_num_rows($ConsultaProy)>0){
    $hayResultados = $hayResultados + 1;
}
while ($fila=pg_fetch_assoc($ConsultaProy)){
	foreach($fila as $k => $v){
		$Log['data']['psdir']['carpetas'][$fila['id_ref_02_pseudocarpetas']][$k]=$v;	
	}	
}

$Log['res']='exito';
terminar($Log);
?>