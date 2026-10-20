<?php 
/**
*
* aplicación para procesar una versión candidta como definitiva.
 * 
 *  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2017 TReCC SA
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

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificación de seguridad 
//include('./includes/conexion.php');
ini_set('display_errors', 1);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";

// funciones frecuentes
include($GeoGecPath."/includes/encabezado.php");
include($GeoGecPath."/includes/pgqonect.php");

include_once($GeoGecPath."/usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php
$idUsuario = $_SESSION["geogec"]["usuario"]['id'];


ini_set('display_errors', 0);
global $PROCESANDO;
$PROCESANDO='si';

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

if(!isset($_POST['idcampa']) || $_POST['idcampa']<1){
	$Log['res']='err';
	$Log['tx'][]='falta id de campania';	
	terminar($Log);
}

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['id_registro_rele']) || $_POST['id_registro_rele']<1){
	$Log['res']='err';
	$Log['tx'][]='falta id de id_registro_rele';	
	terminar($Log);
}

$Acc=0;
$minAcc=2;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<$minAcc){
    $Log['mg'][]=utf8_encode('No cuenta con permisos (nivel '.$minAcc.' vs nivel '.$Acc.') para consultar un indicador. En el marco de investigación código '.$_POST['codMarco']);
    $Log['res']='err';
    terminar($Log);	
}

$Log['data']['id_registro_rele']=$_POST['id_registro_rele'];


$query="
UPDATE 
	geogec.ref_rele_registros
	SET
		zz_superado='1'
	WHERE
		zz_superado='0'
		AND
		zz_borrado='0'
		AND
		zz_archivada='1'
		AND
		id_p_ref_rele_campa='".$_POST['idcampa']."'
		AND
		id='".$_POST['id_registro_rele']."'	
		
";
$Consulta = pg_query($ConecSIG,utf8_encode($query));
$row=pg_fetch_assoc($Consulta);
//$Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
    $Log['res']='error';
    $Log['tx'][]='error al insertar registro en la base de datos';
    $Log['tx'][]=pg_errormessage($ConecSIG);
    $Log['tx'][]=$query;
    terminar($Log);
}	


	
$query="

	UPDATE 
		geogec.ref_rele_registros_datos
	SET
		zz_superado='1'

	WHERE
		ref_rele_registros_datos.id_p_ref_rele_registros = '".$_POST['id_registro_rele']."'
	AND
		ref_rele_registros_datos.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'		

	
";
//$Log['tx'][]=$query;
$Consulta = pg_query($ConecSIG,utf8_encode($query));
$row=pg_fetch_assoc($Consulta);
$Log['data']['registroscampos_nids'][]=$nid;
$Log['tx'][]=utf8_encode($query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['res']='error';
	$Log['tx'][]='error al insertar registro-campo en la base de datos';
	$Log['tx'][]=pg_errormessage($ConecSIG);
	$Log['tx'][]=$query;
	terminar($Log);
}	




$Log['res']='exito';	
terminar($Log);
