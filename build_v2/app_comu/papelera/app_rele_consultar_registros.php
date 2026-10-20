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


if(!isset($_POST['idcampa'])){
	$Log['tx'][]='no fue enviada la variable idcampa';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['idgeom'])){
	$_POST['idgeom']='';
	$Log['tx'][]='no fue enviada la variable idgeom';
	//$Log['res']='err';
	//terminar($Log);	
}
if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['zz_superado'])){
	$Log['tx'][]='no fue enviada la variable zz_superado';
	$Log['res']='err';
	terminar($Log);	
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

if($_POST['idgeom']!=''){
	$wherereg="AND id_p_ref_capas_registros='".$_POST['idgeom']."'";
}else{
	$wherereg="";
}
$Log['data']['idgeom']=$_POST['idgeom'];

$query="SELECT  *
    FROM    geogec.ref_rele_registros
    WHERE 
            zz_borrado = '0'
    AND
            zz_superado = '".$_POST['zz_superado']."'
    AND
            id_p_ref_rele_campa = '".$_POST['idcampa']."'
    $wherereg
 ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$Log['data']['registros']=array();
if (pg_num_rows($Consulta) <= 0){
    $Log['tx'][]= "No se encontraron valores existentes.";
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de indicadores valor existentes";
    while ($fila=pg_fetch_assoc($Consulta)){	
		$Log['data']['registros'][$fila['id']]=$fila;
		$idreg=$fila['id'];
	}
	
	if($_POST['idgeom']!=''){
		
		$Log['data']['idreg']=$idreg;
	}
}



$Log['res']="exito";
terminar($Log);
