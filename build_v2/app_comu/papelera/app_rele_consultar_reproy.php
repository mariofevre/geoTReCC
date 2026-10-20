<?php
/**
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
//$Usu = validarUsuario(); // en ./usu_valudacion.php

$Hoy_a = date("Y");
$Hoy_m = date("m");
$Hoy_d = date("d");
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

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_SESSION["geogec"])){
	$Log['tx'][]='sesión caduca';
	$Log['acc'][]='login';
	terminar($Log);	
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];



if(!isset($_POST['lat'])){
	$Log['tx'][]='no fue enviada la variable lat';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['lon'])){
	$Log['tx'][]='no fue enviada la variable lon';
	$Log['res']='err';
	terminar($Log);	
}


$query="
	SELECT 
		ST_AsText(
			ST_Transform(
				ST_GeomFromText('POINT(".$_POST['lon']." ".$_POST['lat'].")',4326),
				3857
			)
		) As geotx,
		ST_X(
			ST_Transform(
				ST_GeomFromText('POINT(".$_POST['lon']." ".$_POST['lat'].")',4326),
				3857
			)
		) as geo_x,
		ST_Y(
			ST_Transform(
				ST_GeomFromText('POINT(".$_POST['lon']." ".$_POST['lat'].")',4326),
				3857
			)
		) as geo_y
";


$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

$Log['data']=pg_fetch_assoc($Consulta);

	
$Log['res']="exito";
terminar($Log);
