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


$query="
SELECT 
	id, log, email, nombre, apellido
	FROM geogec.sis_usu_registro
	order by apellido asc, nombre asc
";
	
$ConsultaUsu = pg_query($ConecSIG, $query);

if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]=utf8_encode('error: '.pg_errormessage($ConecSIG));
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);	
}	


while($fila=pg_fetch_assoc($ConsultaUsu)){
	$Usuario[$fila['id']]=$fila;
}


$query="
	SELECT 
	id, id_p_sis_usu_registro, tabla, accion, nivel, elemento, desdeu, id_p_sis_usu_registro_por, zz_borrada
	FROM 
	geogec.sis_usu_accesos
	ORDER BY
	zz_borrada asc	
";
	
$ConsultaUsu = pg_query($ConecSIG, $query);

if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]=utf8_encode('error: '.pg_errormessage($ConecSIG));
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);	
}	

while($fila=pg_fetch_assoc($ConsultaUsu)){
	$Log['data']['permisos'][$fila['id']]=$fila;
	if(!isset($Usuario[$fila['id_p_sis_usu_registro']])){continue;}
	$udat=$Usuario[$fila['id_p_sis_usu_registro']];
	$Log['data']['permisos'][$fila['id']]['U_nom']=$udat['apellido'].', '.$udat['nombre'];
	
	$Log['data']['permisosOrden'][]=$fila['id'];
}
				
$Log['res']='exito';
terminar($Log);	