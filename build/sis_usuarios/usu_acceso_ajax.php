<?php
session_destroy();
session_start();

ini_set('display_errors', 1);
chdir('..');

include_once("./_config_acc/claveunica.php");
include_once("./comun_general/encabezado.php");
include_once("./comun_general/pgqonect.php");
$DB=$_SESSION[$CU]['db_settings']->DATABASE_DB;


$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;


$Log=array();
$Log['tx']=array();
$Log['data']=array();
$Log['res']='';

function terminar($Log){
	$res=json_encode($Log);
	if($res==''){
		print_r($Log);
	}else{
		echo $res;
	}
	exit;
}



if(
	!isset($_POST['log'])
	&&
	!isset($_POST['dni'])
){
	$Log['tx'][]='error, no se registra log ni dni.';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['password'])){
	$Log['tx'][]='error, no se registra constrasena.';
	$Log['res']='err';
	terminar($Log);	
}


if(isset($_POST['log'])){
	$where = "log='".$_POST['log']."'";
}else{
	$where = "numeroid='".$_POST['dni']."'";	
}


$query="
SELECT 
	id, log, pass, email, nombre, apellido, isopais, numeroid, pronombre, zz_externo, ic_p_est_02_marcoacademico
	FROM $DB.sis_usu_registro
	WHERE ".$where;
	
$ConsultaUsu = pg_query($ConecSIG, $query);

if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]=utf8_encode('error: '.pg_errormessage($ConecSIG));
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);	
}	

if(pg_num_rows($ConsultaUsu)!=1){
	$Log['tx'][]=utf8_encode('error, no se registra usuario asociado al log ingresado.');
	$Log['tx'][]='query: '.$query;
	$Log['res']='exito';
	terminar($Log);	
}

$fila=pg_fetch_assoc($ConsultaUsu);

if($fila['pass']!=md5($_POST['password'])){
	$Log['tx'][]=utf8_encode('la contraseña no coincide con nuestro registro.');
	$Log['res']='exito';
	terminar($Log);	
}
	
$_SESSION[$CU]["usuario"]=$fila;
$_SESSION[$CU]["usuario"]["recorte"]='';
unset($_SESSION[$CU]["usuario"]['password']);

include_once("./sis_usuarios/usu_validacion.php");
$Usu= validarUsuario();

foreach($fila as $k => $v){
	$filaD[$k]=utf8_encode($v);
}
$filaD['permisos']=$Usu;
$Log['tx'][]='se accedio correctamente.';
$Log['data']=$filaD;
$Log['res']='exito';
terminar($Log);	
