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
$minAcc=1;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<$minAcc){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para ver participantes en un equipo de trabajo');
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_SESSION["geogec"])){
	$Log['tx'][]=utf8_encode('sesión caduca');
	$Log['acc'][]='login';
	terminar($Log);	
}


$query="
SELECT 
	sis_usu_registro.id, 
	sis_usu_registro.log, 
	sis_usu_registro.email, 
	sis_usu_registro.nombre, 
	sis_usu_registro.apellido
	FROM 
		geogec.sis_usu_registro
	JOIN
		geogec.sis_usu_accesos
		ON 
			sis_usu_accesos.id_p_sis_usu_registro = sis_usu_registro.id
	WHERE
			(sis_usu_accesos.tabla = 'est_02_marcoacademico'
			AND
			sis_usu_accesos.elemento = '".$_POST['codMarco']."')
		OR
			(
			sis_usu_accesos.tabla = 'general'
			)
		OR
			(
			sis_usu_accesos.elemento = 'general'
			)
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
	$Log['data']['usu'][$fila['id']]=$fila;
	$Log['data']['usuOrden'][]=$fila['id'];
}
				
$Log['res']='exito';
terminar($Log);	