<?php

if(!isset($_SESSION)) { session_start(); }

chdir('..');
ini_set('display_errors', true);


// funciones frecuentes
include("./comun_general/fechas.php");
include("./comun_general/cadenas.php");
include("./comun_general/pgqonect.php");
$DB=$_SESSION[$CU]['db_settings']->DATABASE_DB;

include_once("./sis_usuarios/usu_validacion.php");



$Log=array();
function terminar($Log){
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	echo $res;
	exit;
}


if(!isset($_SESSION[$CU]["usuario"]['id'])){
	$Log['tx'][]='usuario no registrado';
	$Log['res']='err';
	terminar($Log);	
}

$query="
SELECT 
	id, log, email, nombre, apellido
	FROM $DB.sis_usu_registro
	WHERE
	zz_externo = '0'
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
	$Log['data'][$fila['id']]=$fila;
}
		
		
$Log['res']='exito';
terminar($Log);	
