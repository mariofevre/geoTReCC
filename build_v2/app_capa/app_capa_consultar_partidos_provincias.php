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


$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

if(!isset($_POST['modo'])){
	$_POST['modo']='normal';
	//modo = forzado, arroja todos los registros sin importar el peso.
}


$Id_Capa_Provincias='304';
$campo_link_provincia='texto1';
$campo_nombre_provincia='texto2';

$Id_Capa_Departamen='237';
$campo_link_departamen='texto1';
$campo_nombre_departamen='texto2';
$campo_id_prov_departamen='texto3';




$query="SELECT
		id,
		".$campo_link_provincia." as link,
		".$campo_nombre_provincia." as nombre
	FROM    
		geogec.ref_capasgeo_registros as r
	WHERE 
		id_ref_capasgeo = '".$Id_Capa_Provincias."'
	ORDER BY nombre ASC
";
 
$Consulta = pg_query($ConecSIG, $query);
$Log['tx'][]='query: '.utf8_encode($query);
 //  echo $query;
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.utf8_encode($query);
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if (pg_num_rows($Consulta) <= 0){
    $Log['tx'][]= "No se encontraron registros para esta capa.";
    $Log['data']['registros']=null;
} else {
	
	$Log['tx'][]= "Registros cargados:: ".pg_num_rows($Consulta);
    $Log['tx'][]= "Consulta de capa existente id: ".$Id_Capa_Provincias;
		
	$c=0;
    while ($fila=pg_fetch_assoc($Consulta)){
		$id=$fila['id'];
		$Log['data']['provincias'][$fila['link']]=$fila;
		$Log['data']['provinciasOrden'][]=$fila['link'];
    }
}



$query="SELECT
		id,
		".$campo_link_departamen." as link,
		".$campo_nombre_departamen." as nombre,
		".$campo_id_prov_departamen." as link_prov
	FROM    
		geogec.ref_capasgeo_registros as r
	WHERE 
		id_ref_capasgeo = '".$Id_Capa_Departamen."'
	ORDER BY nombre ASC
";
 
$Consulta = pg_query($ConecSIG, $query);
$Log['tx'][]='query: '.utf8_encode($query);
 //  echo $query;
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.utf8_encode($query);
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if (pg_num_rows($Consulta) <= 0){
    $Log['tx'][]= "No se encontraron registros para esta capa.";
    $Log['data']['registros']=null;
} else {
	
	$Log['tx'][]= "Registros cargados:: ".pg_num_rows($Consulta);
    $Log['tx'][]= "Consulta de capa existente id: ".$Id_Capa_Provincias;
		
	$c=0;
    while ($fila=pg_fetch_assoc($Consulta)){
		$id=$fila['id'];
		$Log['data']['provincias'][$fila['link_prov']]['departamentos'][$fila['link']]=$fila;
		$Log['data']['provincias'][$fila['link_prov']]['departamentosOrden'][]=$fila['link'];
    }
}

$Log['res']="exito";
terminar($Log);
