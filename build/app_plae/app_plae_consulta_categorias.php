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
$Log['mg']=array();
$Log['res']='';
function terminar($Log){
    $res=json_encode($Log);
    if($res==''){$Log['tx'][]=erroresJson();$res=print_r($Log,true);}
    echo $res;
    exit;
}


if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_docs'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_docs'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<1){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para consultar la caja de documentnos de este marco académico. \n minimo requerido: 1 \ nivel disponible: '.$Acc);
	$Log['res']='err';
	terminar($Log);	
}



$idUsuario = $_SESSION[$CU]["usuario"]['id'];


	
	
	$Log['data']['categorias']=array();
	
	
	
	
	$query="
	SELECT 
		`id`, `codigo`, `nombre`, `funcionamiento` 
	FROM 
		`PLAcategoria_estandar` 
	ORDER BY codigo
	";
	$Consulta=	$Conec1->query($query);;
	if($Conec1->error!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode($Conec1->error);
	    $Log['res']='err';
	    terminar($Log);
	}
	$Log['data']['categorias']['estandar']=array();
	while($row = $Consulta->fetch_assoc()){
		foreach($row as $k => $v){
			$Log['data']['categorias']['estandar'][$row['id']][$k]=utf8_encode($v);
		}
		$Log['data']['categorias']['estandar'][$row['id']]['usadoennivel']['PLAn1']=array();
		$Log['data']['categorias']['estandar'][$row['id']]['usadoennivel']['PLAn2']=array();
		$Log['data']['categorias']['estandar'][$row['id']]['usadoennivel']['PLAn3']=array();
	}
	
		
	$query="
		SELECT 
			`id`, 
			`nivel`, 
			`nombre`, 
			`orden`, 
			`zz_borrada`, 
			`id_p_PLAcategoria_estandar` 
		FROM 
			`PLAcategorias` 
		WHERE 
			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
			AND
			zz_borrada='0'
		ORDER by orden
		";
	$Consulta=	$Conec1->query($query);;
	if($Conec1->error!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode($Conec1->error);
	    $Log['res']='err';
	    terminar($Log);
	}
	$Log['data']['categorias']['depanel']=array();
	while($row = $Consulta->fetch_assoc()){
		foreach($row as $k => $v){
			$Log['data']['categorias']['depanel'][$row['id']][$k]=utf8_encode($v);
			if(isset($Log['data']['categorias']['estandar'][$row['id_p_PLAcategoria_estandar']])){
				$Log['data']['categorias']['estandar'][$row['id_p_PLAcategoria_estandar']]['usadoennivel'][$row['nivel']]=$row['id'];				
			}
		}
	}


	
$Log['res']='exito';
terminar($Log);
	

	
	

