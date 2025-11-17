<?php
/**
 *DAA una accion, infrorma al cliente si el usuario activo tiene parmisos para trabajar en esta accion 
 * 
 *
* 
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrolló sobre una publicación GNU 2017 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (GPL-3.0)
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
*/

ini_set('display_errors', 1);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";
include($GeoGecPath.'/includes/encabezado.php');
include($GeoGecPath."/includes/pgqonect.php");

include_once($GeoGecPath."/usuarios/usu_validacion.php");
global $ConecSIG;
$Usu = validarUsuario(); // en ./usu_valudacion.php

$Hoy_a = date("Y");
$Hoy_m = date("m");
$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

$Log['data']=array();
$Log['tx']=array();
$Log['mg']=array();
$Log['acc']=array();
$Log['res']='';

function terminar($Log){
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	echo $res;
	exit;
}


if(!isset($_POST['accion'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_SESSION["geogec"])){
	$Log['tx'][]='sesion caduca';
	$Log['acc'][]='login';
	$Log['res']="exito";
	terminar($Log);	
}
if($_SESSION["geogec"]["usuario"]['id']=='-1'){
	$Log['tx'][]='sesion caduca';
	$Log['acc'][]='login';
	$Log['res']="exito";
	terminar($Log);
}


	

$query="SELECT nombre,nombre_oficial,codigo FROM geogec.est_02_marcoacademico";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
while($fila=pg_fetch_assoc($Consulta)){	
	$Acc=0;
	if(isset($Usu['acc']['est_02_marcoacademico'][$fila['codigo']][$_POST['accion']])){
		$Acc=$Usu['acc']['est_02_marcoacademico'][$fila['codigo']][$_POST['accion']];
	}elseif(isset($Usu['acc']['est_02_marcoacademico'][$fila['codigo']]['general'])){
		$Acc=$Usu['acc']['est_02_marcoacademico'][$fila['codigo']]['general'];
	}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
		$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
	}elseif(isset($Usu['acc']['general']['general']['general'])){
		$Acc=$Usu['acc']['general']['general']['general'];
	}
	
	$minacc=2;
	if($Acc<$minacc){
		continue;
	}
	
	$Log['data'][$fila['codigo']]=$fila;
}



$Log['res']="exito";
terminar($Log);
