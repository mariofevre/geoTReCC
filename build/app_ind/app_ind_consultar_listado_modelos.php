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
/*
if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}
*/

/*
if(!isset($_POST['zz_publicada'])){
	$Log['tx'][]='no fue enviada la variable zz_publicada';
	$Log['res']='err';
	terminar($Log);	
}
*/
$_POST['zz_publicada']='1';


$minacc=0;
if(isset($_POST['nivelPermiso'])){
    $minacc=$_POST['nivelPermiso'];
}

$Acc=0;
/*
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_ind'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_ind'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}
*/

if($Acc<$minacc){
    $Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificación de este marco académico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
    $Log['tx'][]=print_r($Usu,true);
    $Log['res']='err';
    terminar($Log);
}


$idUsuario = $_SESSION[$CU]["usuario"]['id'];



$query="
	SELECT 
		id, tipo, orden, condicion, consigna
	FROM 
		$DB.ref_indicadores_modelos_tag_tipo
	ORDER BY 
		orden ASC
	";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
	
while ($fila=pg_fetch_assoc($Consulta)){		
	$Log['data']['tagsTiposOrden'][]=$fila['id'];
	$Log['data']['tagsTipos'][$fila['id']]=$fila;
}
	
	


$query="SELECT 
		id, tipo, nombre, des_formulario, orden_formulario, defec_formulario, ayuda
	FROM 
		$DB.ref_indicadores_modelos_tags
	ORDER BY
		orden_formulario
 ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$tags_link_plantilla=array();

while ($fila=pg_fetch_assoc($Consulta)){	
	$tags_link_plantilla[$fila['id']]['activo']=$fila['defec_formulario'];
	$Log['data']['tagsOrden'][$fila['tipo']][]=$fila['id'];
	$Log['data']['tags'][$fila['id']]=$fila;
}


$query="SELECT  
			ref_indicadores_modelos.*,
			sis_usu_registro.nombre as autornom,
			sis_usu_registro.apellido as autorape
        FROM    
        	$DB.ref_indicadores_modelos
        LEFT JOIN
			$DB.sis_usu_registro ON sis_usu_registro.id = ref_indicadores_modelos.usu_autor
        WHERE 
            ref_indicadores_modelos.zz_borrada = '0'
        AND
            (zz_publicada = '".$_POST['zz_publicada']."'        
			OR
            ref_indicadores_modelos.usu_autor = '".$idUsuario."')
 ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

if(pg_num_rows($Consulta) <= 0){
    $Log['tx'][]= "No se encontraron modelos de indicadores existentes.";
    $Log['tx'][]= "Query: ".$query;
    $Log['data']=null;
}else{
    $Log['tx'][]= "Consulta de modelos de indicadores existentes";
    while ($fila=pg_fetch_assoc($Consulta)){	
		$Log['data']['modelos'][$fila['id']]=$fila;
		$Log['data']['modelosOrden'][]=$fila['id'];
		$Log['data']['modelos'][$fila['id']]['tag_link']=$tags_link_plantilla;
    }
}



$query="
	SELECT 
		id, id_p_indicadores_modelos, id_p_indicadores_modelos_tags, comentarios, zz_auto_fechau_crea, zz_auto_usu_crea, activo
	FROM 
		$DB.ref_indicadores_modelos_tags_links
 ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$tags_link_plantilla=array();

while ($fila=pg_fetch_assoc($Consulta)){	
	$Log['data']['modelos'][$fila['id_p_indicadores_modelos']]['tag_link'][$fila['id_p_indicadores_modelos_tags']]['activo']=$fila['activo'];
	$Log['data']['modelos'][$fila['id_p_indicadores_modelos']]['tag_link'][$fila['id_p_indicadores_modelos_tags']]['comentarios']=$fila['comentarios'];
}



$query="
	SELECT 
		id, descripcion, id_p_ref_indicadores_modelos, 
		app, id_modelo_en_app, id_p_ref_capasgeo, 
		id_arr_modelo_en_app
	FROM $DB.ref_indicadores_modelos_requerimientos;
 ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$tags_link_plantilla=array();

while ($fila=pg_fetch_assoc($Consulta)){	
	$Log['data']['modelos'][$fila['id_p_ref_indicadores_modelos']]['requerimientos'][$fila['app']]=$fila;
}

$Log['res']="exito";
terminar($Log);
