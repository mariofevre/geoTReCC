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

$Accion='app_publ';
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$Accion])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$Accion];
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

if(!isset($_POST['idcampa'])){
	$Log['tx'][]='no fue enviada la variable idcampa';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['geotx'])){
	$Log['tx'][]='no fue enviada la variable geotx';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['opinion'])){
	$Log['tx'][]='no fue enviada la variable opinion';
	$Log['res']='err';
	terminar($Log);	
}



$Log['data']['idrele']=$_POST['idcampa'];




$query="
	SELECT
		ref_rele_campa.*,
		ref_comu_consultas.id_p_ref_rele_campos,
		ref_capasgeo.id as idcapa
		FROM
		$DB.ref_rele_campa
		LEFT JOIN
			$DB.ref_comu_consultas 
				ON ref_comu_consultas.id_p_ref_rele_campa = ref_rele_campa.id
		LEFT JOIN
			$DB.ref_capasgeo
			ON ref_capasgeo.zz_aux_rele = ref_rele_campa.id
	WHERE 
		ref_rele_campa.id='".$_POST['idcampa']."'
	AND
		ref_comu_consultas.cerrado='0'
	AND
		ref_rele_campa.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
";



$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
if(pg_num_rows($Consulta)==0){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

$row=pg_fetch_assoc($Consulta);



$query="INSERT INTO 
		$DB.ref_capasgeo_registros(
			geom_point, 
			id_ref_capasgeo
		)
		VALUES (
			ST_GeomFromText('".$_POST['geotx']."', 3857),
			'".$row['idcapa']."'
		)

		RETURNING id;
";


$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
        $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
        $Log['tx'][]='query: '.$query;
        $Log['mg'][]='error interno';
        $Log['res']='err';
        terminar($Log);	
}
$row2 = pg_fetch_assoc($Consulta);
$idgeo=$row2['id'];


$query="
		
	INSERT INTO
		$DB.ref_rele_registros(
		
			id_p_ref_rele_campa,
			zz_auto_crea_usu,
			id_p_ref_capas_registros
			
		)
	
		VALUES(
			'".$row['id']."',
			'-1',
			'".$idgeo."'
		)
	RETURNING	id			
";
$Consulta = pg_query($ConecSIG,utf8_encode($query));
//$Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
	$Log['res']='error';
	$Log['tx'][]='error al insertar registro en la base de datos';
	$Log['tx'][]=pg_errormessage($ConecSIG);
	$Log['tx'][]=$query;
	terminar($Log);
}	
$fila=pg_fetch_assoc($Consulta);
$idreg=$fila['id'];


$query="
	
	INSERT INTO 
		$DB.ref_rele_registros_datos(		
				ic_p_est_02_marcoacademico,
				id_p_ref_rele_campa, 
				id_p_ref_rele_campos, 
				id_p_ref_rele_registros, 
				data_texto
				
		)VALUES (
			'".$_POST['codMarco']."',
			'".$row['id']."',
			'".$row['id_p_ref_rele_campos']."',
			'".$idreg."',
			'".$_POST['opinion']."'

		)
	RETURNING	id			
";
$Consulta = pg_query($ConecSIG,utf8_encode($query));
//$Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
	$Log['res']='error';
	$Log['tx'][]='error al insertar registro en la base de datos';
	$Log['tx'][]=pg_errormessage($ConecSIG);
	$Log['tx'][]=$query;
	terminar($Log);
}	
pg_fetch_assoc($Consulta);
	

$Log['res']="exito";
terminar($Log);
