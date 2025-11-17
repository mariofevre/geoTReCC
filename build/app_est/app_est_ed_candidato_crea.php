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
	global $PROCESANDO;
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	if(isset($PROCESANDO)){
		return;	
	}else{
		echo $res;
		exit;
	}	
}

$Acc=0;
if(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<3){
    $Log['mg'][]=utf8_encode('No cuenta con permisos (nivel 3 vs nivel '.$Acc.') para consultar un indicador.');
    $Log['res']='err';
    terminar($Log);	
}
 
if(!isset($_POST['tabla'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para crear un nuevo marco academico. Consulte al administrador');
	$Log['tx'][]='error, no se recibio la variable tabla';
	$Log['res']='err';
	terminar($Log);	
}
$e=explode('_',$_POST['tabla']);
if($e[0]!='est'){
	$Log['mg'][]=utf8_encode('La variable tabla no tiene la estructura de una tabla estructural');
	$Log['tx'][]='error, no se recibio la variable nombre';
	$Log['res']='err';
	terminar($Log);
}

$postes=array('nombre', 'nombre_oficial', 'codigo', 'descripcion','geomtx');
foreach($postes as $v){
	if(!isset($_POST[$v])){
		$Log['mg'][]=utf8_encode('error en las variables enviadas para crear un nuevo marco academico. Consulte al administrador');
	    $Log['tx'][]="no se envió la variable necesaria: ".$v;
	    $Log['res']='err';
	    terminar($Log);		
	}
}




$conespacios=asegurarfilename($_POST['codigo']);
$sinespacios=str_replace(' ', '', $conespacios);
$codigo=strtoupper($sinespacios);
if(strlen($codigo)<8){
	$Log['mg'][]=utf8_encode('error, la variable código debe tener al menos 8 caracteres válidos.');
	$Log['tx'][]='error, variable codigo muy corta'.$codigo;
	$Log['res']='err';
	terminar($Log);	
}


$Log['data']['codigo']=$codigo;

$query="
	SELECT 
		id
	FROM 
		$DB.".$_POST['tabla']."
	WHERE 
		codigo = '".$codigo."'
	AND
		zz_obsoleto='0'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if(pg_num_rows($Consulta)>0){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='ya existe un marco con el código propuesto';
	$Log['res']='err';
	terminar($Log);	
}


$query="
	SELECT 
		id
	FROM 
		$DB.est_03_candidatos
	WHERE 
		codigo = '".$codigo."'
	AND
		zz_obsoleto='0'
	AND 
		tabla='".$_POST['tabla']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if(pg_num_rows($Consulta)>0){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='ya existe un candidato con el código propuesto para la misma tabla';
	$Log['res']='err';
	terminar($Log);	
}


$query="
INSERT INTO 
	$DB.est_03_candidatos( 
	   nombre,
	   nombre_oficial,
	   codigo,
	   descripcion,
	   id_p_sis_usu_registro,
	   tabla,
	   geo
	)VALUES(
       '".$_POST['nombre']."',
       '".$_POST['nombre_oficial']."', 
       '".$codigo."',
       '".$_POST['descripcion']."',
       '".$_SESSION[$CU]["usuario"]['id']."',
       '".$_POST['tabla']."',
       ST_GeomFromText('".$_POST['geomtx']."', 3857)
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

while($fila=pg_fetch_assoc($Consulta)){
	$Nid=$fila['id'];
	$Log['tx'][]='marco preliminar creado, id:'.$Nid;
	$Log['data']['nid']=$Nid;
}

	
$Log['res']='exito';
terminar($Log);		
