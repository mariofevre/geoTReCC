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

if(!isset($_POST['idSesion'])){
	$Log['tx'][]='no fue enviada la variable idSesion';
	$Log['res']='err';
	terminar($Log);	
}


$minacc=0;
if(isset($_POST['nivelPermiso'])){
    $minacc=$_POST['nivelPermiso'];
}

$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_ind'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_ind'];
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

$idUsuario = $_SESSION[$CU]["usuario"]['id'];


//datos generales de la sesion
$query="
	SELECT 
		id, id_p_indicadores_indicadores, ic_p_est_02_marcoacademico, 
		nombre, presentacion, costounitario, limiteunitarioporturno,
		zz_borrada, 
		ref_game_sesiones.*
	FROM 
		$DB.ref_game_sesiones
	WHERE 
        zz_borrada = '0'
    AND
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	AND
		id = '".$_POST['idSesion']."'
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
    $Log['tx'][]= "No se encontraron sesiones existentes.";
    $Log['tx'][]= "Query: ".$query;
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de sesiones existentes";
	$Log['data']['sesion']=pg_fetch_assoc($Consulta);
	$Log['data']['sesion']['presentacionBR']=nl2br($Log['data']['sesion']['presentacion']);
}

if($Log['data']['sesion']['id_p_indicadores_indicadores']>0){
$query="SELECT  
			i.id, 
			i.funcionalidad, 
			i.id_p_ref_capasgeo, 
			i.ic_p_est_02_marcoacademico, 
			i.periodicidad, 
			i.fechadesde, 
			i.fechahasta, 
			i.calc_buffer, 
			i.calc_superp, 
			i.calc_zonificacion,			
			ref_capasgeo.tipogeometria
		FROM    
			$DB.ref_indicadores_indicadores as i	
		LEFT JOIN
			$DB.ref_capasgeo ON ref_capasgeo.id = i.id_p_ref_capasgeo
        WHERE 
                i.zz_borrada = '0'
        AND
                i.id = '".$Log['data']['sesion']['id_p_indicadores_indicadores']."'
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
    $Log['tx'][]= "No se encontro el indicador id ".$Log['data']['sesion']['id_p_indicadores_indicadores'];
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de indicador valido";
    $fila = pg_fetch_assoc($Consulta);
    $Log['data']['indicador']=$fila;
}


if($Log['data']['indicador']['calc_buffer']<1){
	//fina abrupto por error en la configuraci{on del indicador asociado
    $Log['tx'][]='error: en la distancia de influencia del indicador asociado: '.$Log['data']['indicador']['calc_buffer'];
    $Log['mg'][]='error: en la distancia de influencia del indicador asociado no se incorpora la geometria de cobertura)';
    $Log['res']='exito';
    terminar($Log);		
}

//datos de la geometria del marco de trabajo
$query="
	SELECT 
			ST_AsText(geo) as geo_ai_tx,
			ST_AsText(ST_Centroid(geo)) as geo_centroide_tx,
			ST_x(ST_Centroid(geo)) as geo_centroide_x,
			ST_y(ST_Centroid(geo)) as geo_centroide_y,
			ST_AsText(	        	
	            ST_Transform(
					ST_Buffer(
						ST_Transform(ST_Centroid(geo),22175),				
						".$Log['data']['indicador']['calc_buffer'].", 
						'endcap=round join=round'
					),
					3857
				)
			) as geo_buffer_centroide_tx
		FROM 
			$DB.est_02_marcoacademico
		WHERE 
		 zz_obsoleto = '0'
		 AND
		 	codigo = '".$_POST['codMarco']."'
	";
	$ConsultaProy = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	}
	if(pg_num_rows($ConsultaProy)<1){
		$Log['tx'][]='error';
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='no se encontraron tablas disponibles';
		$Log['res']='err';
		terminar($Log);	
	}
	$Log['data']['geomGame']=pg_fetch_assoc($ConsultaProy);
}	


$Log['res']="exito";
terminar($Log);
