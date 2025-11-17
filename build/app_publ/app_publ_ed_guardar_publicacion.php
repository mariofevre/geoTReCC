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

$minacc=2;
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

$idUsuario = $_SESSION[$CU]["usuario"]['id'];

$Log['data']['id']=$_POST['idPubl'];
if($_POST['idPubl']==='0'){
	
	$query="
		INSERT INTO
			$DB.ref_publ_publicacion(
				ic_p_est_02_marcoacademico			
			)
			VALUES(
				'".$_POST['codMarco']."'			
			)
			RETURNING id
	";
	
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}

	$row=pg_fetch_assoc($Consulta);
	
	if($row['id']<1){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}
	
	$_POST['idPubl']=$row['id'];
	$Log['data']['nid']=$row['id'];
	
}


$geom='';
if($_POST['areatx']!=''&&$_POST['areatx']!='undefined'){
	
	$geom="area= ST_GeomFromText('".$_POST['areatx']."', 3857), ";
		
}else if(count($_POST['municipios'])>0){
	
	$where='';
	foreach($_POST['municipios'] as $v){
		$where.="\"COD_DEPTO_\" = '".$v."' OR ";	
	}
	$where = substr($where, 0, -3);
	
	$query="
	SELECT 
		ST_AsText(
			ST_Simplify(
				ST_Transform(
					ST_Multi(
						ST_Union(
							geo
							)
					),
					3857
				),
				10000
			)
		)as geom			
		FROM
		$DB.est_01_municipios
		WHERE
		$where
	";
		
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}	
	$row=pg_fetch_assoc($Consulta);
	
	if($row['geom']==''){
		$geom='';
	}else{
		$geom="area= ST_GeomFromText('".$row['geom']."', 3857), ";
	}
}



if($_POST['ano']==''){
	$ano='NULL';
}else{
	$ano="'".$_POST['ano']."'";
}

if($_POST['mes']==''){
	$mes='NULL';
}else{
	$mes="'".$_POST['mes']."'";
}

if($_POST['titulo']==''){
	$_POST['titulo']='- Sin Título cargado -';
}


//datos generales de la sesion
$query="
	UPDATE 
		$DB.ref_publ_publicacion
	SET 
		titulo='".$_POST['titulo']."', 
		autoria='".$_POST['autoria']."',
		$geom 
		url='".$_POST['url']."',
		id_p_ref_01='".$_POST['id_p_ref_01']."',
		observaciones='".$_POST['observaciones']."',
		id_p_ref_publ_tipos='".$_POST['id_p_ref_publ_tipos']."',
		ano=$ano,
		mes=$mes,
		usu_ultimaed='".$_SESSION[$CU]["usuario"]['id']."',		
		fechau_ultimaed='".time()."'
	WHERE 
		id='".$_POST['idPubl']."'
	AND
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
 ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}


$query="
	UPDATE 
		$DB.ref_01_documentos
	SET 
		zz_preliminar='0'
	WHERE 
		id='".$_POST['id_p_ref_01']."'
	AND
		ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
	


//print_r($_POST['municipios']);


$query="
	SELECT 
			id, 
			id_p_ref_publ_publicacion, 
			ic_p_est_01_municipios
		FROM 
			$DB.ref_publ_link_publicacion_est_01
		WHERE
			id_p_ref_publ_publicacion  = '".$_POST['idPubl']."'
			AND 
			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'			
";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$aborrar=array();
while($row = pg_fetch_assoc($Consulta)){		
	$aborrar[$row['id']]=$row['id'];
}	
	
$acrear=$_POST['municipios'];



foreach($acrear as $k => $v){
	$Log['tx'][]='a crear '.$v;
	if(isset($aborrar[$k])){
		$Log['tx'][]='des a borrar '.$k;
		unset($aborrar[$k]);
		$Log['tx'][]='des a crear '.$k;
		unset($acrear[$k]);
	}
}

$Log['tx'][]='a crear resultante: '.print_r($acrear,TRUE);
$Log['tx'][]='a borrar resultante: '.print_r($aborrar,true);

foreach($acrear as $k => $v){
	if($_POST['codMarco']==''){continue;}
	if($k < 1){continue;}
	if($_POST['idPubl']<1 ){continue;}
	
	$query="
		INSERT INTO 
			$DB.ref_publ_link_publicacion_est_01(
				id_p_ref_publ_publicacion, 
				ic_p_est_01_municipios, 
				ic_p_est_02_marcoacademico
			)
			VALUES (
				'".$_POST['idPubl']."', 
				'".$k."',
				'".$_POST['codMarco']."'
			)
		
			RETURNING id
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
		

foreach($aborrar as $k => $v){
	$query="
		DELETE FROM 
			$DB.ref_publ_link_publicacion_est_01
		WHERE
			id_p_ref_publ_publicacion='".$_POST['idPubl']."'
			AND 
			ic_p_est_01_municipios='".$k."'
			AND 
			ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
			
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
		



$Log['res']="exito";
terminar($Log);
