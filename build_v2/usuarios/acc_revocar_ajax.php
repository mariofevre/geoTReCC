<?php
session_destroy();
session_start();

chdir('..');
ini_set('display_errors', true);


// funciones frecuentes
// funciones frecuentes
// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/pgqonect.php");
include_once("./usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

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

$Acc=0;
if(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<3){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]=utf8_encode('no cuenta con permisos para configurar la plataforma geoGEC');
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['id_acc'])){
	$Log['tx'][]='error, no se registra id_acc.';
	$Log['res']='err';
	terminar($Log);	
}
if($_POST['id_acc']<1){
	$Log['tx'][]='error, no se registra id_acc.';
	$Log['res']='err';
	terminar($Log);	
}

$query="
	UPDATE geogec.sis_usu_accesos
		SET
		zz_borrada='1'
		WHERE
		id='".$_POST['id_acc']."'
		AND
		(
			tabla!='general' 
			OR
			elemento!='general'
			OR
			accion!='general'
			OR
			nivel<3
		)
";
	
$ConsultaUsu = pg_query($ConecSIG, $query);

if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]=utf8_encode('error: '.pg_errormessage($ConecSIG));
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);	
}	
		
$Log['res']='exito';
terminar($Log);	