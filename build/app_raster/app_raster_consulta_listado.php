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
$mod='app_raster';
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$mod])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$mod];
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



$idUsuario = $_SESSION[$CU]["usuario"]['id'];


//CONSULTA DICCIONARIO

$query="
	SELECT 
		id, nombre, descripcion, url_consulta
	FROM 
		$DB.ref_raster_tipos_diccionario
	";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
$Log['data']['tipos']=array();
while ($fila=pg_fetch_assoc($Consulta)){	
	$Log['data']['tipos'][$fila['id']]=$fila;
	$Log['data']['tipos'][$fila['id']]['bandas']=array();
}




$query="
	SELECT 
		id, id_p_ref_raster_tipos_diccionario, numero, 
		indice, nombre, descripcion, longitud_central, ancho, resolucion, monobanda
	FROM 
		$DB.ref_raster_tipos_bandas_diccionario
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
	$idtipo=$fila['id_p_ref_raster_tipos_diccionario'];
	if(!isset($Log['data']['tipos'][$idtipo])){
		$Log['tx'][]='Tipo de banda ('.$fila['id'].') referida a tipo inexistente ('.$idtipo.')';
		continue;
	}

	$Log['data']['tipos'][$idtipo]['bandas'][$fila['id']]=$fila;
}



		
		

$query="
	SELECT 
		r.id, r.autor, r.nombre, r.descripcion, 
		r.ic_p_est_02_marcoacademico, 
		r.tipo, 
		r.zz_publicada, r.srid, r.modo_publica, r.fecha_ano, 
		r.fecha_mes, r.fecha_dia, 
		ST_AsText(ST_Transform(r.geom,3857)) as geom_tx, 
		r.hora_utc, 
		r.id_p_ref_01_documentos, 
		r.zz_data_procesada,
		r.id_p_ref_raster_tipos_diccionario,
		r.zz_estado,
		d.nombre as doc_nombre
	FROM 
		$DB.ref_raster_coberturas as r
	LEFT JOIN
		$DB.ref_01_documentos as d ON r.id_p_ref_01_documentos = d.id
    WHERE 
    	r.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
  		r.zz_borrada = '0'
  		
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


$Log['data']['coberturas']=array();
while ($fila=pg_fetch_assoc($Consulta)){	
	$Log['data']['coberturas'][$fila['id']]=$fila;
	$Log['data']['coberturas'][$fila['id']]['bandas']=array();
	$idtipo=$fila['id_p_ref_raster_tipos_diccionario'];
	if($idtipo>0){
		if(!isset($Log['data']['tipos'][$idtipo])){continue;}
		$bandas_tipo=$Log['data']['tipos'][$idtipo]['bandas'];
		foreach($bandas_tipo as $id_banda_tipo => $dat){
			$Log['data']['coberturas'][$fila['id']]['bandas'][$id_banda_tipo]=array(
				'numero' => $dat['numero'],
				'indice' => $dat['indice'],
				'nombre' => $dat['nombre'],
				'estado' => 'sin cargar'
			);			
		}
	}
}


$query="
	SELECT 
		id, id_p_ref_raster_coberturas, id_p_ref_raster_tipos_bandas_diccionario, estado, anotaciones
	FROM 
		$DB.ref_raster_bandas
    WHERE 
    	ref_raster_bandas.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
  		ref_raster_bandas.zz_borrada = '0'
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
	
	$id_cob=$fila['id_p_ref_raster_coberturas'];
	$Log['tx'][]='idcob'.$id_cob;	
	if(!isset($Log['data']['coberturas'][$id_cob])){continue;}
	
	$Log['tx'][]='idbanda'.$id_banda_tipo;	
	$id_banda_tipo=$fila['id_p_ref_raster_tipos_bandas_diccionario'];
	if(!isset($Log['data']['coberturas'][$id_cob]['bandas'][$id_banda_tipo])){continue;}
	
	foreach($fila as $k => $v){
		$Log['data']['coberturas'][$id_cob]['bandas'][$id_banda_tipo][$k]=$v;
	}
}



$Log['res']="exito";
terminar($Log);
