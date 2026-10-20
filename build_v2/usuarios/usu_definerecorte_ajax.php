<?php

if(!isset($_SESSION)) { session_start(); }

chdir('..');
ini_set('display_errors', true);


// funciones frecuentes
// funciones frecuentes
// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/pgqonect.php");

$Log=array();
function terminar($Log){
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	echo $res;
	exit;
}


if(!isset($_POST['RecorteDeTrabajo'])){
	$Log['tx'][]='falta varaible: RecorteDeTrabajo';
	$Log['res']='err';
	terminar($Log);	
}

$_SESSION["geogec"]["usuario"]["recorte"]=$_POST['RecorteDeTrabajo'];
		
$Log['res']='exito';
terminar($Log);	
