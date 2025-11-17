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

$idUsuario = $_SESSION[$CU]["usuario"]['id'];

if($_POST['idgeom']!=''){
	$query="
		SELECT  
			
			id, zz_auto_crea_usu, 
			col_texto1_dato, 
			col_numero1_dato, 
			id_p_ref_capas_registros, zz_auto_supera_id, 
			zz_auto_crea_fechau, zz_archivada, zz_archivada_fecha

			

		FROM    
			$DB.ref_rele_registros
		
		WHERE 
			zz_borrado = '0'
		AND
			zz_superado = '".$_POST['zz_superado']."'
		AND
            id_p_ref_rele_campa = '".$_POST['idcampa']."'	
        AND 
			id_p_ref_capas_registros='".$_POST['idgeom']."'
			";
	
}else{
	
	$query="
		SELECT  
			
			a.id, a.zz_auto_crea_usu, 
			a.col_texto1_dato, 
			a.col_numero1_dato, 
			a.id_p_ref_capas_registros, 
			a.zz_auto_supera_id, 
			a.zz_auto_crea_fechau, 
			a.zz_archivada, a.zz_archivada_fecha

			

		FROM    
			$DB.ref_rele_registros as a
		LEFT JOIN
			$DB.ref_capasgeo_registros as b
				ON
				a.id_p_ref_capas_registros = b.id
				
		WHERE 
			a.zz_borrado = '0'
		AND
			a.zz_superado = '".$_POST['zz_superado']."'
		AND
            a.id_p_ref_rele_campa = '".$_POST['idcampa']."'	
        AND
			b.zz_borrada='0'
			
		ORDER BY col_texto1_dato
            
	
	";
	
	$wherereg="AND id_p_ref_capas_registros is not null";
}
$Log['data']['idgeom']=$_POST['idgeom'];


$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$Log['data']['registrosOrden']=array();
$Log['data']['registros']=array();
if (pg_num_rows($Consulta) <= 0){
    $Log['tx'][]= "No se encontraron valores existentes.";
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de indicadores valor existentes";
    while ($fila=pg_fetch_assoc($Consulta)){	
		$Log['data']['registrosOrden'][]=$fila['id'];
		$Log['data']['registros'][$fila['id']]=$fila;
		$idreg=$fila['id'];
	}
	
	if($_POST['idgeom']!=''){
		
		$Log['data']['idreg']=$idreg;
	}
}

$Log['res']="exito";
terminar($Log);
