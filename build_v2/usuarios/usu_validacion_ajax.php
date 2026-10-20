<?php 

function validarUsuario(){
        //niveles de acceso
	// 3:  equipo de dearrollo GEC
	// 2:  colaboradores GEC
	// 1:  colaboradores externos
	// 0:  sin privilegios
	if(!isset($_SESSION)) { session_start(); }
	
	chdir('..');
	ini_set('display_errors', true);
	
	
	$Log=array();
	function terminar($Log){
		$res=json_encode($Log);
		if($res==''){
			print_r($Log);
		}else{
			echo $res;
		}
		exit;
	}
		
	
	// funciones frecuentes
	// funciones frecuentes
	// funciones frecuentes
	include("./includes/fechas.php");
	include("./includes/cadenas.php");
	include("./includes/pgqonect.php");
	include_once("./usuarios/usu_validacion.php");
	global $ConecSIG;
	$Usu = validarUsuario(); // en ./usu_valudacion.php
	
	$Log['res']='exito';
	$Log['data']=$USU;
	terminar($Log);
}
