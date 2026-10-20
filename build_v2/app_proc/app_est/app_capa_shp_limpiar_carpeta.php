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


function terminar($Log){
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	echo $res;
	exit;
}

if(!isset($_POST['id']) || $_POST['id']<1){
	$Log['res']='error';
	$Log['tx'][]='falta id de capa';	
	terminar($Log);
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

$query="SELECT  *
        FROM    geogec.ref_capasgeo
        WHERE 
  		id='".$_POST['id']."'
  	AND
 	 	zz_borrada = '0'
  	AND
 	 	(zz_publicada = '0'
 	 	OR
 	 	zz_publicada = '1')
  	AND
  		autor = '".$idUsuario."'
 ";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

$fila=pg_fetch_assoc($ConsultaVer);

if($fila['zz_borrada']=='1'){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='esta capa figura como borrada. no puede proseguir';
	$Log['res']='err';
	terminar($Log);	
}
if($fila['autor']!=$idUsuario){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='usted no figura como el autor de esta capa: '.$idUsuario;
	$Log['res']='err';
	terminar($Log);	
}

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = date("Y-m-d");


$carpeta=$GeoGecPath.'/documentos/subidas/capa/'.str_pad($_POST['id'],8,"0",STR_PAD_LEFT);
if(file_exists($carpeta)){
    $it = new RecursiveDirectoryIterator($carpeta, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()){
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
}

$Log['tx'][]="borrados archivos de carpeta ".$carpeta;
$Log['res']="exito";
terminar($Log);
