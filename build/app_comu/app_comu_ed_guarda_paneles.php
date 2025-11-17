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

$minacc=0;
if(isset($_POST['nivelPermiso'])){
    $minacc=$_POST['nivelPermiso'];
}

$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_rele'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_rele'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<$minacc){
    $Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificación de este marco académico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
    $Log['tx'][]=print_r($Usu,true);
    $Log['res']='err';
    terminar($Log);
}
$Log['data']['acc']=$Acc;

$idUsuario = $_SESSION[$CU]["usuario"]['id'];


if(!isset($_POST['idpanel'])){
	$_POST['idpanel']='';//no se envió solicitud de un panel específico (a un usuario con permisos se le enviarán todos los paneles, a un usuariosin permisos se le enviará uno solo
}


$variables_obligatorias=array(
	'codMarco'=>'',
	'nombre'=>'',
	'descripcion'=>'',
	'visible'=>'',
	'mba'=>'',
	'x'=>'',
	'y'=>'',
	'z'=>'',
	'muestra_marco'=>'',

	'col_fi_img_raster'=>'',
	'col_fi_img_raster_ovr'=>'',
	
	'col_id_p_ref_capasgeo'=>'',
	'col_campo'=>'',
	'col_campob'=>'',

			
	'cap_fi_raster'=>'',
	'cap_fi_raster_ovr'=>'',	
	'cap_simbologia_raster'=>'',
	'cap_etiquetas_raster'=>'',
	
	
	'cap_id_p_ref_capasgeo'=>'',
	'cap_simbologia'=>''
	
);

foreach($variables_obligatorias as $k => $v){
	if(!isset($_POST[$k])){
		$Log['tx'][]='falta enviar variable '.$k;
		$Log['res']='err';
		terminar($Log);		 
	}
}

if($_POST['x']===''){$linea_x= "x = null";}else{$linea_x="x = '".$_POST['x']."'";}
if($_POST['y']===''){$linea_y= "y = null";}else{$linea_y="y = '".$_POST['y']."'";}
if($_POST['z']===''){$linea_z= "z = null";}else{$linea_z="z = '".$_POST['z']."'";}


$query="UPDATE
			$DB.ref_comu_paneles
		SET
			titulo = '".$_POST['nombre']."',
			descripcion = '".$_POST['descripcion']."',
			visible = '".$_POST['visible']."',
			mba = '".$_POST['mba']."',
			$linea_x, 
			$linea_y, 
			$linea_z, 
			muestra_marco = '".$_POST['muestra_marco']."'
		WHERE
		 id = '".$_POST['idp']."'
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


$query="
	SELECT 
		a.id, 
		a.ic_p_est_02_marcoacademico, 
		a.id_p_ref_capasgeo, 
		a.id_p_ref_comu_paneles,
		a.titulo, a.simbologia, a.campo, a.campob,
		a.fi_img_raster, a.fi_img_raster_ovr
	FROM 
		$DB.ref_comu_colecciones as a
	WHERE 
            zz_borrada = '0'
        AND
    		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    	AND
			id_p_ref_comu_paneles='".$_POST['idp']."'
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
	$query="INSERT INTO
			$DB.ref_comu_colecciones
			(
				ic_p_est_02_marcoacademico, 
				id_p_ref_comu_paneles,
				titulo
			)
			VALUES(
				
				'".$_POST['codMarco']."',
				'".$_POST['idp']."',
				''				
			)
			RETURNING id
	";
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	$fila=pg_fetch_assoc($Consulta);
	$Log['data']['nid']=$fila['id'];
}



$query="UPDATE
			$DB.ref_comu_colecciones
		SET
			fi_img_raster	  = '".$_POST['col_fi_img_raster']."',
			fi_img_raster_ovr ='".$_POST['col_fi_img_raster_ovr']."',
			id_p_ref_capasgeo ='".$_POST['col_id_p_ref_capasgeo']."',
			campo ='".$_POST['col_campo']."',
			campob ='".$_POST['col_campob']."'
		WHERE
			ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
			AND
			id_p_ref_comu_paneles='".$_POST['idp']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


	

$query="
	SELECT 
		ref_comu_capas.id,
		ref_comu_capas.id_p_ref_comu_paneles, 
		ref_comu_capas.id_p_ref_capasgeo, 
		ref_comu_capas.simbologia,
		ref_comu_capas.simbologia_raster,
		ref_comu_capas.etiquetas_raster,
		ref_comu_capas.fi_raster, ref_comu_capas.fi_raster_ovr
	FROM 
		$DB.ref_comu_capas 

	WHERE 
    	ref_comu_capas.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    	AND
    	id_p_ref_comu_paneles='".$_POST['idp']."'
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

	$query="INSERT INTO
			$DB.ref_comu_capas(
				ic_p_est_02_marcoacademico, 
				id_p_ref_comu_paneles
			)VALUES(				
				'".$_POST['codMarco']."',
				'".$_POST['idp']."'
			)
			RETURNING id
	";
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	$fila=pg_fetch_assoc($Consulta);
	$Log['data']['nid']=$fila['id'];
}

$query="UPDATE
			$DB.ref_comu_capas
		SET
			fi_raster	  = '".$_POST['cap_fi_raster']."',
			fi_raster_ovr ='".$_POST['cap_fi_raster_ovr']."',
			simbologia_raster = '".$_POST['cap_simbologia_raster']."',
			etiquetas_raster = '".$_POST['cap_etiquetas_raster']."',
			id_p_ref_capasgeo = '".$_POST['cap_id_p_ref_capasgeo']."',
			simbologia = '".$_POST['cap_simbologia']."'
		WHERE
			ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
			AND
			id_p_ref_comu_paneles='".$_POST['idp']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

	


			


$Log['res']="exito";
terminar($Log);
