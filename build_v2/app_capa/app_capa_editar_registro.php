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
$Usu = validarUsuario(); // en ./usu_valudacion.php

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

$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}
$minacc=2;
if($Acc<$minacc){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificación de este marco académico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
	$Log['tx'][]=print_r($Usu,true);
	$Log['res']='err';
	terminar($Log);
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

if(!isset($_POST['idgeom'])){
	$Log['res']='error';
	$Log['tx'][]='falta id de idgeom';	
	terminar($Log);
}


if(!isset($_POST['idcapa'])){
	$Log['res']='error';
	$Log['tx'][]='falta id de idcapa';	
	terminar($Log);
}

if(!isset($_POST['geomtx'])){
	$Log['res']='error';
	$Log['tx'][]='falta id de geomtx';	
	terminar($Log);
}

if(!isset($_POST['tipogeom'])){
	$Log['res']='error';
	$Log['tx'][]='falta id de tipogeom';	
	terminar($Log);
}


$query="SELECT  *
        FROM    geogec.ref_capasgeo
        WHERE 
  		id='".$_POST['idcapa']."'
  	AND
 	 	zz_borrada = '0'
 	AND
 		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
if(pg_num_rows($Consulta)<1){
	$Log['tx'][]=utf8_encode('error: No se encotró la capa solicitad');
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

$fila=pg_fetch_assoc($Consulta);

if($fila['zz_aux_rele']==null){
	$Log['tx'][]=utf8_encode('esta funcion solo está habilitada por ahora para capas auxiliares a relevamientos');
	$Log['mg'][]=utf8_encode('esta funcion solo está habilitada por ahora para capas auxiliares a relevamientos');
	$Log['res']='err';
	terminar($Log);
}

if(
	$_POST['tipogeom']=='Polygon'
){
	$setgeom="geom = ST_GeomFromText('".$_POST['geomtx']."', 3857)";
}elseif(
	$_POST['tipogeom']=='LineString'
){
$setgeom="geom_line = ST_GeomFromText('".$_POST['geomtx']."', 3857)";	
}elseif(
	$_POST['tipogeom']=='Point'
){
$setgeom="geom_point = ST_GeomFromText('".$_POST['geomtx']."', 3857)";	
}else{
	$Log['tx'][]='error: No puedo interpretar el tipo de geometría que se pretende guardar';
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
$query = "
UPDATE 
	geogec.ref_capasgeo_registros
	SET
	".$setgeom."
	WHERE 
		id='".$_POST['idgeom']."'
	AND
		id_ref_capasgeo='".$_POST['idcapa']."'
";


$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
        $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
        $Log['tx'][]='query: '.$query;
        $Log['mg'][]='error interno';
        $Log['res']='err';
        terminar($Log);	
}


$Log['tx'][]="Editada capa id: ".$_POST['idcapa'];
$Log['data']['idgeom']=$_POST['idgeom'];
$Log['data']['idcapa']=$_POST['idcapa'];
$Log['res']="exito";

terminar($Log);
