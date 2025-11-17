<?php
/**
* 
 * 
 ***  
* @package    	geoTReCC
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar>
* @author    	http://www.trecc.com.ar
* @author		based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
* @copyright	2025 TReCC SA* 
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2018 - Universidad de Buenos Aires - geoGEC
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2017 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (GPL-3.0)
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
* 
*
*/

ini_set('display_errors', 1);
chdir('..');
include_once("./_config_acc/claveunica.php");
include_once("./_config_inst/config.php");

// verificación de seguridad 
if(!isset($_SESSION)) {
	session_start(); 
	if(!isset($_SESSION[$CU]["usuario"]['id'])){
		$_SESSION[$CU]["usuario"]['id']='-1';
	}
}

include_once('./comun_general/encabezado.php');
include_once("./comun_general/pgqonect.php");
include_once("./sis_usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php

// funciones frecuentes
include("./comun_general/fechas.php");
include("./comun_general/cadenas.php");

$COD = isset($_GET['cod'])?$_GET['cod'] : '';
$ID = isset($_GET['id'])?$_GET['id'] : '';
if($ID==''&&$COD==''){
	header('location: ./index.php');
}

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d"); $HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

$idUsuario = $_SESSION[$CU]["usuario"]['id'];




global $PROCESANDO;
$PROCESANDO='si';

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

 
list($type, $data) = explode(';', $_POST['png']);
list(, $data)      = explode(',', $data);
$data = base64_decode($data);

$carpeta=$GeoGecPath;
$carpeta.='/documentos/auxiliares/rele/';
$carpeta.=str_pad($_POST['idcampa'],8,"0",STR_PAD_LEFT);
$carpeta.='/'.str_pad($idUsuario,8,"0",STR_PAD_LEFT);

$carpetabase=$carpeta;

$carpetalink=str_replace($_SERVER["DOCUMENT_ROOT"],$_SERVER['HTTP_HOST'],$carpeta);

if(!file_exists($carpeta)){
    $Log['tx'][]="creando carpeta $carpeta";
    mkdir($carpeta, 0777, true);
    chmod($carpeta, 0777);	
}

$carpeta.='/'.time();

if(!file_exists($carpeta)){
    $Log['tx'][]="creando carpeta $carpeta";
    mkdir($carpeta, 0777, true);
    chmod($carpeta, 0777);	
}





$nombre = "captura".time().'.png';
$Log['data']['nombreorig'] = $nombre;
$b = explode(".",$nombre);
$extO = $b[(count($b)-1)];
$ext = strtolower($extO);
$nombreSinExt = str_replace(".".$extO, "", $nombre);




$fn = $nombre;	
$nom=substr($fn,0,-4);
$nom=str_replace("-","_",$nom);
$nom=str_replace(" ","_",$nom);
$e=explode("_",$nom);

$nuevonombre= $carpeta.'/'.$nombreSinExt.'.'.strtolower($ext);
$nombrerel='./'.$nombreSinExt.'.'.strtolower($ext);
//echo "<br>var:".$e[0] ."esc:".$e[1]."per:".$e[2].alt:".$e[3];	

if (!file_put_contents($nuevonombre, $data)){
    $Log['tx'][]="Error al copiar $nuevonombre";
    $Log['res']="err";
    terminar($Log);
}

$Log['tx'][]="copiado archivo raster ".strtolower($nuevonombre);
$nombredxf=$carpeta.'/'."captura".time().'.dxf';


require dirname(__FILE__) . '/DXFcreator/Color.php';
require dirname(__FILE__) . '/DXFcreator/LineType.php';
require dirname(__FILE__) . '/DXFcreator/Creator.php';

use adamasantares\dxf\Creator;
use adamasantares\dxf\Color;
use adamasantares\dxf\LineType;

//setting image data
$size=getimagesize($nuevonombre);
$width=$size[0];
$height=$size[1];


$dxf = new Creator();
$dxf
    ->setLayer('cyan', Color::CYAN)
    ->addLine(25, 0, 0, 100, 0, 0)
	->addImage($_POST['minx']	, $_POST['miny'], 0, $_POST['maxx'], $_POST['maxy'], 0, $nombrerel, $width, $height)
    ->saveToFile($nombredxf);

 
$Log['tx'][]="copiado archivo dxf ".strtolower($nombredxf);


$nomdescarga='DXFcaptura.'.time().'.zip';
chdir($carpetabase);
$comando='zip -r -j '.$carpetabase.'/'.$nomdescarga.' '.$carpeta.'/*.*';

exec($comando,$exec_res);
$Log['tx'][]=$comando;
$Log['tx'][]='creando dxf: '.print_r($exec_res,true);
$Log['data']['descarga']='http://'.$carpetalink.'/'.$nomdescarga;


//$Log['tx'][]=$dxf->error;
$Log['res']="exito";
terminar($Log);
/*

