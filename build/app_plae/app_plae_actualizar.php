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
	if($res==''){$res=print_r($Log,true);}
	echo $res;
	exit;
}


$PanelI = $_SESSION['panelcontrol'] -> PANELI;

$Hoy=date("Y-m-d");

$_SESSION['panelcontrol'] -> McTi_Rdoc = microtime(true);		
$Log['data']['MOD']='PLA';

	
	$e['totPLAn1']=0; //total de programas
	$e['alertaPLAn1']=0; //total de programas
	$e['alertaPLAn1_Tx']=''; //total de programas
	
	$e['totPLAn2']=0; //total de subprogramas
	$e['alertaPLAn2']=0; //total de programas
	$e['alertaPLAn2_Tx']=''; //total de programas
	
	$e['totPLAn3']=0; //total de acciones
	$e['alertaPLAn3']=0; //total de programas
   	$e['alertaPLAn3_Tx']=''; //total de programas

	$query="
		SELECT 
			`PLAn3`.`id`,
		    `PLAn3`.`nombre`,
		    `PLAn3`.`numero`,
		   	`PLAn3`.`zz_ultimoestado`
		FROM 
			`paneles`.`PLAn3`
		Where
			zz_AUTOPANEL='$PanelI' 
		AND
			zz_borrada='0'
	";		
	$Consulta=	$Conec1->query($query);
	if($Conec1->error!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode($Conec1->error);
	    $Log['res']='err';
	    terminar($Log);
	}
	$PN3=array();
	while($row = $Consulta->fetch_assoc()){
			
		$e['totPLAn3']++; //total de acciones
		
		if($row['zz_ultimoestado']=='alerta'){
			$e['alertaPLAn3']++; //total de programas
			$e['alertaPLAn3_Tx'].=$row['numero'].': '.$row['nombre']. ' / ';
		}
		
		
	}


	$query="
		SELECT `PLAn2`.`id`,
		    `PLAn2`.`nombre`,
		    `PLAn2`.`numero`,
		   	`PLAn2`.`zz_ultimoestado`
		FROM 
			`paneles`.`PLAn2`
		Where 
			zz_AUTOPANEL='$PanelI' 
		AND
			zz_borrada='0'
		ORDER BY 
			numero asc
	";		
	$Consulta=	$Conec1->query($query);;
	if($Conec1->error!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode($Conec1->error);
	    $Log['res']='err';
	    terminar($Log);
	}
	$PN2=array();
	while($row = $Consulta->fetch_assoc()){
					
		$e['totPLAn2']++; //total de acciones
		
		if($row['zz_ultimoestado']=='alerta'){
			$e['alertaPLAn2']++; //total de programas
			$e['alertaPLAn2_Tx'].=$row['numero'].': '.$row['nombre']. ' / ';
		}
	}	

	$query="
		SELECT 
			`PLAn1`.`id`,
		    `PLAn1`.`nombre`,
		    `PLAn1`.`numero`,
		   	`PLAn1`.`zz_ultimoestado`
		    
		FROM
			`paneles`.`PLAn1`
		Where 
			zz_AUTOPANEL='$PanelI' 
		AND
			zz_borrada='0'
		ORDER BY 
			numero ASC
	";		
	$Consulta=	$Conec1->query($query);;
	if($Conec1->error!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode($Conec1->error);
	    $Log['res']='err';
	    terminar($Log);
	}
	$PLA=array();
	$PLA['PLAn1']=array();
	$PN1=array();
	while($row = $Consulta->fetch_assoc()){
		$e['totPLAn1']++; //total de acciones
		
		if($row['zz_ultimoestado']=='alerta'){
			$e['alertaPLAn1']++; //total de programas
			$e['alertaPLAn1_Tx'].=$row['numero'].': '.$row['nombre']. ' / ';
		}	
		
	}	



$query="
		INSERT INTO
			PANestadisticasPLA
		SET
		    fechaU = '".time()."',
		    mes = '".date("m")."',
		    ano = '".date("Y")."',
		    totPLAn1 = '".$e['totPLAn1']."',
		    alertaPLAn1 = '".$e['alertaPLAn1']."',
		    totPLAn2 = '".$e['totPLAn2']."',
		    alertaPLAn2 = '".$e['alertaPLAn2']."',
		    totPLAn3 = '".$e['totPLAn3']."',
		    alertaPLAn3 = '".$e['alertaPLAn3']."',
			zz_AUTOPANEL='".$PanelI."'
	";
	$Con = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]='error al consultar base de datos';
		$Log['tx'][]=$Conec1->error;
		$Log['tx'][]=$query;
		$Log['res']='err';
		terminar($Log);
	}
	
$Log['res']='exito';
terminar($Log);



?>
