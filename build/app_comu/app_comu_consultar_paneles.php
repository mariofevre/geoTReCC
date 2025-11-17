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

$query="SELECT  
			*
        FROM    
        	$DB.ref_comu_paneles
        
        WHERE 
            ref_comu_paneles.zz_borrada = '0'
        AND
    		ref_comu_paneles.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
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
    $Log['tx'][]= "No se encontraron relevamientos existentes.";
    $Log['tx'][]= "Query: ".$query;
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de indicadores existentes";
    while ($fila=pg_fetch_assoc($Consulta)){	
		$Log['data']['paneles'][$fila['id']]=$fila;
		$Log['data']['paneles'][$fila['id']]['colecciones']=array();
		$Log['data']['paneles'][$fila['id']]['capas']=array();
		$Log['data']['paneles'][$fila['id']]['capasraster']=array();
		$Log['data']['paneles'][$fila['id']]['reles']=array();
    }
}




$query="
	SELECT 
	
		ref_capasgeo.id,
		ref_capasgeo.nom_col_text1,
		ref_capasgeo.nom_col_text2,
		ref_capasgeo.nom_col_text3,
		ref_capasgeo.nom_col_text4,
		ref_capasgeo.nom_col_text5,
		ref_capasgeo.nom_col_num1,
		ref_capasgeo.nom_col_num2,
		ref_capasgeo.nom_col_num3,
		ref_capasgeo.nom_col_num4,
		ref_capasgeo.nom_col_num5,
		ref_capasgeo.sld,

		
		ref_comu_capas.id_p_ref_comu_paneles, 
		ref_comu_capas.id_p_ref_capasgeo, 
		ref_comu_capas.simbologia,
		ref_comu_capas.simbologia_raster,
		ref_comu_capas.fi_raster, ref_comu_capas.fi_raster_ovr,
		ref_comu_capas.modo_fusion,
						
		ref_capasgeo.nombre,
		ref_capasgeo.descripcion,
		
		a.id as id_colec,
		a.id_p_ref_comu_paneles as id_panel_colec
		
	FROM 
		$DB.ref_capasgeo 
		LEFT JOIN
		$DB.ref_comu_capas
			ON
			ref_capasgeo.id = ref_comu_capas.id_p_ref_capasgeo
		LEFT JOIN
		$DB.ref_comu_colecciones as a
			ON
			a.id_p_ref_capasgeo = ref_capasgeo.id
		
		
	WHERE 
            ref_capasgeo.zz_borrada = '0'
        AND
    		ref_capasgeo.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    	AND(
			a.id>0
			OR
			ref_comu_capas.id>0
		)
 ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

while ($fila=pg_fetch_assoc($Consulta)){	
	if(!isset($Log['data']['paneles'][$fila['id_p_ref_comu_paneles']])){
		continue;
    }
    if($fila['id_p_ref_capasgeo']!=''){
		$Log['data']['paneles'][$fila['id_p_ref_comu_paneles']]['capas'][$fila['id']]=$fila;
		$Log['data']['paneles'][$fila['id_panel_colec']]['capas'][$fila['id']]=$fila;
	}else{
		$Log['data']['paneles'][$fila['id_p_ref_comu_paneles']]['capasraster'][$fila['id']]=$fila;
		$Log['data']['paneles'][$fila['id_panel_colec']]['capasraster'][$fila['id']]=$fila;
	}
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
    	ref_comu_capas.fi_raster!=''
    	AND
    	ref_comu_capas.fi_raster IS NOT NULL
 ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

while ($fila=pg_fetch_assoc($Consulta)){	
	if(!isset($Log['data']['paneles'][$fila['id_p_ref_comu_paneles']])){
		continue;
    }

	$Log['data']['paneles'][$fila['id_p_ref_comu_paneles']]['capasraster'][$fila['id']]=$fila;
	
}






$query="
	SELECT 
		a.id, a.ic_p_est_02_marcoacademico, 
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
 ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

while ($fila=pg_fetch_assoc($Consulta)){	
	if(!isset($Log['data']['paneles'][$fila['id_p_ref_comu_paneles']])){
		continue;
    }
	$Log['data']['paneles'][$fila['id_p_ref_comu_paneles']]['colecciones'][$fila['id']]=$fila;
}



$query="
	SELECT 
		id, id_p_ref_rele_campa, id_p_ref_comu_paneles,
		cerrado, consigna
			
	FROM 
		$DB.ref_comu_consultas
	
	WHERE 
		zz_borrada = '0'
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

while ($fila=pg_fetch_assoc($Consulta)){	
	if(!isset($Log['data']['paneles'][$fila['id_p_ref_comu_paneles']])){
		continue;
    }
	$Log['data']['paneles'][$fila['id_p_ref_comu_paneles']]['consultas'][$fila['id']]=$fila;
}


$Log['res']="exito";

if($Log['data']['acc']<1){
	
	if($_POST['idpanel']!=''){
		$idp=$_POST['idpanel'];
		$Log['data']['paneles']= array(
			$idp => $Log['data']['paneles'][$idp]
		);
	}else{
		$Log['data']['paneles']= array();
		$Log['mg'][]=utf8_encode('No ha solicitado un panl específico. Verifique la dirección ingresada');
		$Log['res']="err";
	}
	
}





terminar($Log);
