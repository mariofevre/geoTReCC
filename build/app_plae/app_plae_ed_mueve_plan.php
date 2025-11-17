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


$Log=array();
$Log['data']=array();
$Log['tx']=array();
$Log['res']='';
$Log['acc']=array();
function terminar($Log){
    $res=json_encode($Log);
    if($res==''){$Log['tx'][]=erroresJson();$res=print_r($Log,true);}
    echo $res;
    exit;
}


foreach($_POST as $k => $v){
	$_POST[$k] = utf8_decode($v);
}

if(!isset($_POST['id'])){
    $Log['tx'][]='falta variable id';
    $Log['res']='err';
    terminar($Log);
}

if(!isset($_POST['nivel'])){
    $Log['tx'][]='falta variable nivel';
    $Log['res']='err';
    terminar($Log);
}

$Validos['nivel']=array('PLAn1'=>'','PLAn2'=>'','PLAn3'=>'');
if(!isset($Validos['nivel'][$_POST['nivel']])){
    $Log['tx'][]='Variable nivel contiene un valor inváido ('.$_POST['nivel'].'). Solo se aceptan: '.print_r($Validos['nivel'],true);
    $Log['res']='err';
    terminar($Log);
}

if(!isset($_POST['id_dest'])){
    $Log['tx'][]='falta variable id';
    $Log['res']='err';
    terminar($Log);
}


$Id=$_POST['id'];	
$Log['data']['id']=$Id;
$Log['data']['nivel']=$_POST['nivel'];
$Log['data']['id_dest']=$_POST['id_dest'];
//$Log['data']['modo']=$_POST['modo'];


$query = "
	SELECT 
		*
	FROM ".$_POST['nivel']."
	
	WHERE 
	id='".$_POST['id']."'
	AND
	zz_AUTOPANEL='".$PanelI."'
";
$Consulta=	$Conec1->query($query);
if($Conec1->error!=''){
    $Log['tx'][]='error al consultar columnas';
    $Log['tx'][]=utf8_encode($query);
    $Log['tx'][]=utf8_encode($Conec1->error);
    $Log['res']='err';
    terminar($Log);
}
$row = $Consulta->fetch_assoc();
$Componente=$row;		
if(count($Componente)==0){	
    $Log['tx'][]='error al consultar columnas';
    $Log['tx'][]=utf8_encode($query);
    $Log['tx'][]=utf8_encode($Conec1->error);
    $Log['res']='err';
    terminar($Log);
}


if($_POST['nivel']=='PLAn2'){
	$_POST['nivelME1']='PLAn3';
	$_POST['nivelMA1']='PLAn1';
	$_POST['nivelMA2']=null;
}elseif($_POST['nivel']=='PLAn3'){		
	$_POST['nivelME1']=null;
	$_POST['nivelMA1']='PLAn2';
	$_POST['nivelMA2']='PLAn1';
}

$Log['data']['id']=$_POST['id'];
$Log['data']['nivel']=$_POST['nivel']; 
$Log['data']['nnivel']=$_POST['nivelMA1'];



$query = "
	SELECT 
		*
	FROM ".$_POST['nivelMA1']."
	
	WHERE 
	id='".$_POST['id_dest']."'
	AND
	zz_AUTOPANEL='".$PanelI."'
";	
$Consulta=	$Conec1->query($query);
	

if($Conec1->error!=''){
    $Log['tx'][]='error al consultar columnas';
    $Log['tx'][]=utf8_encode($query);
    $Log['tx'][]=utf8_encode($Conec1->error);
    $Log['res']='err';
    terminar($Log);
}
$row = $Consulta->fetch_assoc();
$SuperComponente=$row;	


if(count($SuperComponente)==0){	
    $Log['tx'][]='error al consultar columnas';
    $Log['tx'][]=utf8_encode($query);
    $Log['tx'][]=utf8_encode($Conec1->error);
    $Log['res']='err';
    terminar($Log);
}



$query = "
	UPDATE 
		paneles.".$_POST['nivel']."
	SET
		id_p_".$_POST['nivelMA1']."='".$_POST['id_dest']."' 
	WHERE 
	id='".$_POST['id']."'
	AND
	zz_AUTOPANEL='".$PanelI."'
";
$Consulta=	$Conec1->query($query);
if($Conec1->error!=''){
    $Log['tx'][]='error al consultar columnas';
    $Log['tx'][]=utf8_encode($query);
    $Log['tx'][]=utf8_encode($Conec1->error);
    $Log['res']='err';
    terminar($Log);
}
    $Log['tx'][]=utf8_encode($query);


$Log['res']='exito';
terminar($Log);
