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

if(!isset($_POST['zz_publicada'])){
	$Log['tx'][]='no fue enviada la variable zz_publicada';
	$Log['res']='err';
	terminar($Log);	
}

$idUsuario = $_SESSION[$CU]["usuario"]['id'];


if($_POST['zz_publicada']==0){
	$wherepub= " AND zz_publicada = '0' AND autor = '".$idUsuario."'";	
}else{
	$wherepub= " AND zz_publicada = '1' ";
}

if (isset($_POST['idcapa'])){
	$whereidcapa= " AND id = '".$_POST['idcapa']."'";	
}else{
	$whereidcapa= " ";
}

$query="
	SELECT  
		id, 
		nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, 
		srid, sld, tipogeometria, modo_defecto, wms_layer, zz_aux_ind
    FROM    $DB.ref_capasgeo
    WHERE 
    	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
        zz_borrada = '0'
    $whereidcapa
    $wherepub
";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if ($query != ''){
    if (pg_num_rows($Consulta) <= 0){
        $Log['tx'][]= "No se encontraron capas existentes para este usuario.";
        $Log['data']=null;
    } else {
        //Asumimos que solo devuelve una fila
        $fila=pg_fetch_assoc($Consulta);
        $Log['tx'][]= "Consulta de capa existente id: ".$fila['id'];
        $Log['data']=$fila;
    }
} else {
    $Log['tx'][]="Error al crear la query para consultar capa";
    $Log['res']="error";
}

$Log['res']="exito";
terminar($Log);
