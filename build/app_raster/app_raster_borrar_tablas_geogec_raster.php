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



$minacc=2;
if(isset($_POST['nivelPermiso'])){
    $minacc=$_POST['nivelPermiso'];
}

$Acc=0;
$Accion='app_raster';
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
    $Log['mg'][]=utf8_encode('no cuenta con permisos para gerear capas raster. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
    $Log['tx'][]=print_r($Usu,true);
    $Log['res']='err';
    terminar($Log);
}
$idUsuario = $_SESSION[$CU]["usuario"]['id'];




if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['idraster'])){
	$Log['tx'][]='no fue enviada la variable idraster';
	$Log['res']='err';
	terminar($Log);	
}
if($_POST['idraster']<1){ //si ya hay raster, el doc ya fue descomprimido.
	$Log['tx'][]='no fue enviada la variable idraster MAYOR A 0';
	$Log['res']='err';
	terminar($Log);	
}
	

if(!isset($_POST['modo'])){
	$Log['tx'][]='no fue enviada la variable modo';
	$Log['res']='err';
	terminar($Log);	
}
$Log['data']['modo']=$_POST['modo'];

if($_POST['modo']=='unabanda'){
	
	if(!isset($_POST['id_banda'])){//retomauna banda específica	
		$Log['tx'][]='no fue enviada la variable id_banda';
		$Log['res']='err';
		terminar($Log);	
	}
	if($_POST['id_banda']<1){//retomauna banda específica	
		$Log['tx'][]='no fue enviada la variable id_banda mayor a 0';
		$Log['res']='err';
		terminar($Log);	
	}
	
}elseif($_POST['modo']=='completo'){
	$Log['tx'][]='inicia eliminacion de contenidos raster en modo completo';
}else{
	$Log['tx'][]='modo desconocido';
	$Log['res']='err';
	terminar($Log);	
}




if(!isset($_POST['id_banda'])){//retomauna banda específica
	$_POST['id_banda']=0;
}



$query="
	SELECT id, 
	autor, nombre, descripcion, 
	ic_p_est_02_marcoacademico, tipo, zz_borrada, 
	zz_publicada, srid, modo_publica, fecha_ano, 
	fecha_mes, fecha_dia, zz_auto_borra_usu,
	zz_auto_borra_fechau, geom, hora_utc, id_p_ref_01_documentos, 
	zz_data_procesada, id_p_ref_raster_tipos_diccionario,
	zz_estado
	FROM $DB.ref_raster_coberturas
	WHERE
	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	AND
	id = '".$_POST['idraster']."'
";
$Resultado = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
$row=pg_fetch_assoc($Resultado);
$_POST['idraster']=$row['id'];
$Log['data']['nid']=$_POST['idraster'];
$Log['data']['cobertura']=$row;
$Log['data']['cobertura']['bandas']=array();



$query="
	SELECT 
		id as bid, id_p_ref_raster_tipos_bandas_diccionario as id_tipo, estado, 
		anotaciones,  
		tabla
	
	FROM 
		$DB.ref_raster_bandas
	WHERE
		id_p_ref_raster_coberturas = '".$_POST['idraster']."'
		AND
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'	
		AND
		zz_borrada = '0'
";

$Resultado = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

while($row=pg_fetch_assoc($Resultado)){
	
	foreach($row as $k => $v){
		$Log['data']['cobertura']['bandas'][$row['id_tipo']][$k]=$v;	
	}
}

		
foreach($Log['data']['cobertura']['bandas'] as $idtipo_b => $v){
	
	$bid=$v['bid'];
	
	if($_POST['modo']=='unabanda'){
		if($bid!=$_POST['id_banda']){continue;}
	}
	
	$nombretabla='r'.str_pad($_POST['idraster'],8,'0',STR_PAD_LEFT).'_'.$bid;

	$query="
		DROP TABLE IF EXISTS
			geogec_raster.".$nombretabla."
		;
	";
	pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.utf8_encode($query);
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	
	//Elimina overview x10
	$query="
		DROP TABLE IF EXISTS
			geogec_raster.o_10_".$nombretabla."
		;
	";
	pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.utf8_encode($query);
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	
	//Elimina overview x100		
	$query="
		DROP TABLE IF EXISTS
			geogec_raster.o_100_".$nombretabla."
		;
	";
	pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.utf8_encode($query);
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	
	//actualiza estado de registro de banda raster
	$query="
	UPDATE
		$DB.ref_raster_bandas
		SET
			estado='elminado', 
			tabla='".$nombretabla."'
		WHERE 
			id = '".$bid."'
	";
	$Resultado = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.utf8_encode($query);
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}	
}			





$query="
	UPDATE 
		$DB.ref_raster_coberturas
		
	SET 
		zz_borrada='1'
	WHERE
		id = '".$_POST['idraster']."'
		AND
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'	
		AND
		zz_borrada = '0'
";

$Resultado = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}




$Log['tx'][]='todas las bandas procesadas. avance final';
$Log['avance']='final';
$Log['res']='exito';
terminar($Log);

?>
