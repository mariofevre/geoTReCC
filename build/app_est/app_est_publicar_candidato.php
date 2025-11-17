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
/*
if($Acc<3){
    $Log['mg'][]=utf8_encode('No cuenta con permisos (nivel 3 vs nivel '.$Acc.') para consultar un indicador.');
    $Log['res']='err';
    terminar($Log);	
}
 */
if(!isset($_POST['idcand'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para crear un nuevo marco academico. Consulte al administrador');
	$Log['tx'][]='error, no se recibio la variable descripcion';
	$Log['res']='err';
	terminar($Log);	
}


$query="
	SELECT 
		*,
		 ST_IsValid(geo) as validageom
	FROM 
		$DB.est_03_candidatos
	WHERE 
		codigo = '".$_POST['codigo']."'
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

if(pg_num_rows($Consulta)<1){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='no existe el candidato csolicitado';
	$Log['res']='err';
	terminar($Log);	
}

$fila=pg_fetch_assoc($Consulta);

if($fila['validageom']=='f'){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='la geometría cargada no es valida';
	$Log['res']='err';
	terminar($Log);
}
$Tabla=$fila['tabla'];

$query="
	SELECT 
		id
	FROM 
		$DB.".$Tabla."
	WHERE 
		codigo = '".$fila['codigo']."'
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
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='ya existe un elemento en esa tabla con el mismo codigo';
	$Log['res']='err';
	terminar($Log);	
}



$query="
	SELECT 
		id, tabla, nombre, fechau, zz_borrada, zz_publicada, 
	      zz_obsoleto
	  FROM $DB.sis_versiones
	  WHERE 
	  	tabla = '".$Tabla."'
	  AND
	  	zz_borrada = '0'
	  AND
	  	zz_publicada = '1'
	  AND
	  	zz_obsoleto = '0'
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

$VersionVigenteID=$fila['id'];
if($VersionVigenteID<1){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error al identificar la version vigente';
	$Log['res']='err';
	terminar($Log);		
}

$num = $fila['nombre'];
preg_replace('/[^0-9]/', '', $num);
$Vsuperar=$fila['id'];
if($num>0){
		
		$Nombre = $num + 1;	
		
	}else{
		
		$query="
			SELECT 
					id, tabla, nombre, fechau, zz_borrada, zz_publicada, 
			       	zz_obsoleto
			  	FROM 
			  		$DB.sis_versiones
			  	WHERE 
			  		tabla = '".$Tabla."'
			  	AND
			  		zz_borrada = '0'
		 ";
		$Consultaver = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		$Nombre = pg_num_rows($Consultaver) + 1;
		
	}
	


$query="
	INSERT INTO 
		$DB.sis_versiones(
            tabla, 
            nombre, 
            usu_autor,
            fechau,
            zz_publicada)
    	VALUES (
    		'".$Tabla."', 
    		'".$Nombre."', 
    		'".$_SESSION[$CU]["usuario"]['id']."',
    		'".time()."',
    		'1')
    	RETURNING id
";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

while($fila=pg_fetch_assoc($ConsultaVer)){
	$Nid=$fila['id'];
	$Log['tx'][]='version creada, id:'.$Nid;
	$Log['data']['nid']=$Nid;
}



$query="
SELECT 
		*
  FROM 
		$DB.sis_versiones
  WHERE 
		tabla = '".$Tabla."' 
  	AND
	 	zz_borrada = '0'
  	AND
	 	zz_publicada = '1'
  	AND
		usu_autor = '".$_SESSION[$CU]["usuario"]['id']."'
  	AND
		id ='".$Nid."'
 ";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if(pg_num_rows($ConsultaVer)<1){
	$Log['tx'][]='por algun motivo no encuentro la version recien generada.';
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);
}


$query="
	UPDATE 
		$DB.sis_versiones
	SET 
		zz_obsoleto='1'
	WHERE
		tabla = '".$Tabla."' 
	AND
		zz_publicada='1'
	AND
		id!='".$Nid."'
	AND
		zz_obsoleto='0'
";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

	$Log['tx'][]='VERSION VIGENTE: '.$VersionVigenteID;
//copia los elementos vigentes de la tabal marco
$query="
	INSERT INTO 
		$DB.est_02_marcoacademico( 
		   	nombre, nombre_oficial, codigo, id_sis_versiones, 	zz_obsoleto, geo, zz_accesolibre, descripcion
		)
	SELECT 
			nombre, nombre_oficial, codigo, '".$Nid."', 		zz_obsoleto, geo, zz_accesolibre, descripcion
		FROM $DB.est_02_marcoacademico 
	WHERE 
		id_sis_versiones='".$VersionVigenteID."'
";
$Log['tx'][]='query: '.$query;
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

//añade el nuevo elementos a la tabla marco
$query="
	INSERT INTO 
		$DB.est_02_marcoacademico( 
		   nombre, nombre_oficial, codigo, id_sis_versiones, zz_obsoleto, geo, descripcion
		)
		SELECT nombre, nombre_oficial, codigo, '".$Nid."', '0', geo, descripcion
		FROM 
		$DB.est_03_candidatos
		WHERE codigo='".$_POST['codigo']."'
";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


$query="
	UPDATE 
		$DB.est_02_marcoacademico
	SET 
		zz_obsoleto='1'
	WHERE 
		id_sis_versiones!='".$Nid."'
";

$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

$query="
UPDATE $DB.est_03_candidatos
	SET zz_obsoleto='1'
	WHERE codigo='".$_POST['codigo']."'
";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

	
$Log['res']='exito';
terminar($Log);		
