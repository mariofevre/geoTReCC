<?php
/**
 * 
 aplicación para guardar archivos cargados en el sevidor 
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

// funciones frecuentes
include($GeoGecPath."/includes/encabezado.php");
include($GeoGecPath."/includes/pgqonect.php");

include_once($GeoGecPath."/usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php

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

if(!isset($_POST['idcampa']) || $_POST['idcampa']<1){
	$Log['res']='error';
	$Log['tx'][]='falta id de campania';	
	terminar($Log);
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = date("Y-m-d");


$carpeta=$GeoGecPath;
$carpeta.='/documentos/subidas/rele/';
$carpeta.=str_pad($_POST['idcampa'],8,"0",STR_PAD_LEFT);

$carpeta.='/'.str_pad($idUsuario,8,"0",STR_PAD_LEFT);
$carpeta.='/xlsx';

if(!file_exists($carpeta)){
    $Log['tx'][]="creando carpeta $carpeta";
    mkdir($carpeta, 0777, true);
    chmod($carpeta, 0777);	
}

$nombre = $_FILES['upload']['name'];
$Log['data']['nombreorig'] = $nombre;
$b = explode(".",$nombre);
$extO = $b[(count($b)-1)];
$ext = strtolower($extO);
$nombreSinExt = str_replace(".".$extO, "", $nombre);

$extVal['xlsx']='1';

if(!isset($extVal[strtolower($ext)])){	
    $Log['tx'][]="solo se aceptan los formatos:";
    foreach($extVal as $k => $v){$Log['tx'][]=" $k,";}
    $Log['tx'][]="archivo cargado: ".$nombre;
    $Log['res']='err';
    terminar($Log);
}

$Log['tx'][]="cargando archivo en modo xlsx.";
$Log['data']['cargado']='xlsx';

$fn = $nombre;	
$nom=substr($fn,0,-4);
$nom=str_replace("-","_",$nom);
$nom=str_replace(" ","_",$nom);
$e=explode("_",$nom);

$nuevonombre= $carpeta.'/'.$nombreSinExt.'.'.strtolower($ext);
//echo "<br>var:".$e[0] ."esc:".$e[1]."per:".$e[2].alt:".$e[3];	

if (!copy($_FILES['upload']['tmp_name'], $nuevonombre)){
    $Log['tx'][]="Error al copiar $nuevonombre";
    $Log['res']="err";
    terminar($Log);
}


require_once($GeoGecPath.'/external/simplexlsx/src/SimpleXLSX.php');
//echo $file;
$Log['tx'][]=$nuevonombre;
$tabla = SimpleXLSX::parse($nuevonombre, 0);
$Filas=$tabla->rows();
$Log['data']['columnas']=$Filas[0];
$Log['data']['fila1']=$Filas[1];


$Log['data']['archivo']=$nombreSinExt.'.'.strtolower($ext);
$Log['tx'][]="copiado archivo punto ".strtolower($ext);
$Log['res']="exito";
terminar($Log);
