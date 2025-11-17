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

if(!isset($_POST['idcapa'])){
	$Log['tx'][]='no fue enviada la variable idcapa';
	$Log['res']='err';
	terminar($Log);	
}

$idUsuario = $_SESSION[$CU]["usuario"]['id'];
$Log['data']['idcapa']=$_POST['idcapa'];

$query="
	SELECT  *
    FROM    $DB.ref_capasgeo
    WHERE 
    	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
        id='".$_POST['idcapa']."'
";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if (pg_num_rows($Consulta) <= 0){
	$Log['tx'][]= "No se encontraron capas existentes para este usuario.";
	$Log['data']=null;
	terminar($Log);	
} else {
	//Asumimos que solo devuelve una fila
	$fila=pg_fetch_assoc($Consulta);
	$Log['tx'][]= "Consulta de capa existente id: ".$fila['id'];
	$Log['data']['capa']=$fila;
}


$strcapa=str_pad($_POST['idcapa'],6,'0',STR_PAD_LEFT);

$Log['data']['ruta']='/documentos/auxiliares/capa/descargas/'.$strcapa.'.shp';

chdir('./documentos/auxiliares/capa/descargas/');

$f='./documentos/auxiliares/capa/descargas/'.$strcapa.'.zip';
if(file_exists($f)){unlink($f);}

$comando='zip ./'.$strcapa.'.zip ./'.$strcapa.'.*';
// echo PHP_EOL;echo $comando;echo PHP_EOL; 


$exec_res=shell_exec($comando);

if($exec_res==null||$exec_res==false){
	
	$Log['tx'][]='error al ejecutar el comando: '.$comando;	
	$Log['res']="err";
	terminar($Log);
}
//exec($comando,$exec_res);
$Log['tx'][]=$comando;
$Log['tx'][]='creando zip desde shapefile: '.print_r($exec_res,true);
$Log['data']['descarga']='./documentos/auxiliares/capa/descargas/'.$strcapa.'.zip';




$Log['res']="exito";
terminar($Log);
