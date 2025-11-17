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

function IsNullOrEmptyString($string){
    return (!isset($string) || trim($string)==='');
}


$Acc=0;
if(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}
$minacc=0;

if($Acc<$minacc){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificación de este marco académico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
	$Log['tx'][]=print_r($Usu,true);
	$Log['res']='err';
	terminar($Log);
}


$idUsuario = $_SESSION[$CU]["usuario"]['id'];

if(!isset($_POST['id'])){
	$Log['res']='error';
	$Log['tx'][]='falta id del indicador';	
	terminar($Log);
}
if($_POST['id']<1){
	$Log['res']='error';
	$Log['tx'][]='falta id del indicador';	
	terminar($Log);
}

   
$query="
	UPDATE 
		$DB.ref_indicadores_modelos
	SET 
		nombre='".$_POST['nombre']."', 
		descripcion='".$_POST['descripcion']."', 
		funcionalidad='".$_POST['funcionalidad']."', 
		periodicidad='".$_POST['periodicidad']."', 
		
		representar_campo='".$_POST['representar_campo']."', 
		representar_val_max='".$_POST['representar_val_max']."', 
		representar_val_min='".$_POST['representar_val_min']."', 
		calc_buffer='".$_POST['calc_buffer']."', 
		
		
		calc_superp='".$_POST['calc_superp']."', 
		calc_superp_campo='".$_POST['calc_superp_campo']."', 
		calc_zonificacion='".$_POST['calc_zonificacion']."', 
		
		
		
		unidad_medida='".$_POST['unidad_medida']."', 
		relevancia_acc='".$_POST['relevancia_acc']."', 
		limitaciones='".$_POST['limitaciones']."', 
		ejemplo='".$_POST['ejemplo']."', 
		datos_input='".$_POST['datos_input']."', 
		fuentes_input='".$_POST['fuentes_input']."', 
		calculo='".$_POST['calculo']."', 
		escala_espacial='".$_POST['escala_espacial']."', 
		desagregacion='".$_POST['desagrgacion']."', 
		valoracion='".$_POST['valoracion']."'
	WHERE 
		id='".$_POST['id']."'
	";

$Log['tx'][]='query: '.$query;
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

//LINKEO DE TAGS
$query="
	SELECT 
		id, id_p_indicadores_modelos, id_p_indicadores_modelos_tags, comentarios, zz_auto_fechau_crea, zz_auto_usu_crea, activo
		FROM 
		$DB.ref_indicadores_modelos_tags_links
		WHERE
		id_p_indicadores_modelos='".$_POST['id']."'
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
	$tageos[$fila['id_p_indicadores_modelos_tags']]='';
}
	
foreach($_POST['tags'] as $tagid => $tdat){	
	
	if(!isset($tageos[$tagid])){		
		$query="			
			INSERT INTO 
				$DB.ref_indicadores_modelos_tags_links(
					id_p_indicadores_modelos, 
					id_p_indicadores_modelos_tags, 
					zz_auto_fechau_crea, 
					zz_auto_usu_crea
				) VALUES (
					'".$_POST['id']."', 
					'".$tagid."', 
					'".time()."', 
					'".$idUsuario."'
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
	}
	
	if($tdat['stat']=='false'){
		$estado='0';
	}else{
		$estado='1';
	}
	
	$query="
		UPDATE 
			$DB.ref_indicadores_modelos_tags_links
		SET 
			comentarios='".$tdat['comentario']."',
			activo='".$estado."'
		WHERE 
			id_p_indicadores_modelos='".$_POST['id']."'
			AND
			id_p_indicadores_modelos_tags='".$tagid."'
	";
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}	
}




//LINKEO DE Modulos app


$query="
	SELECT 
		tabla, accion, resumen, accmin
	  FROM 
		$DB.sis_acciones
		LEFT JOIN
	  	$DB.sis_tablas_acciones
	  	ON
	  	sis_acciones.codigo=sis_tablas_acciones.accion
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}

while($fila=pg_fetch_assoc($ConsultaProy)){
	$cod=str_replace('app_','',$fila['accion']);
	$app[$cod]=$fila;
}	


$query="
	SELECT 
		id, 
		descripcion, 
		id_p_ref_indicadores_modelos, 
		app, 
		id_modelo_en_app, 
		id_arr_modelo_en_app
	FROM 
		$DB.ref_indicadores_modelos_requerimientos
	
	WHERE
		id_p_ref_indicadores_modelos='".$_POST['id']."'
		
		
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}

while($fila=pg_fetch_assoc($ConsultaProy)){
	$reqs[$fila['app']]=$fila;
}	


foreach($app as $cod_app => $accdat){

	$activo=0;
	if(isset($_POST['apps']['app_'.$cod_app])){
		if($_POST['apps']['app_'.$cod_app]['stat']=='true'){
			$activo=1;
		}else{
			$activo=-1;
		}
	}
	
	if(
		!isset($reqs[$cod_app])
		&&
		$activo==1
	){		
			
		$query="			
			INSERT INTO 
				$DB.ref_indicadores_modelos_requerimientos(
					id_p_ref_indicadores_modelos, 
					app
				) VALUES (
					'".$_POST['id']."', 
					'".$cod_app."'
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
	}elseif(
		isset($reqs[$cod_app])
		&&
		$activo==-1
	){		
			
		
		$query="			
			DELETE FROM
				$DB.ref_indicadores_modelos_requerimientos
				
				WHERE
				
					id_p_ref_indicadores_modelos = '".$_POST['id']."'
					AND
					app = '".$cod_app."'	
		";
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
	}

	if($activo==1){
		$rdat=$_POST['apps']['app_'.$cod_app];
	
		if($rdat['modelos']==''){$mod='{}';}else{
			$mod=json_encode($rdat['modelos'])	;
		}
		
		$query="
			UPDATE 
				$DB.ref_indicadores_modelos_requerimientos
				SET 
				descripcion='".$rdat['comentario']."', 
				id_arr_modelo_en_app= '".$mod."'
			WHERE 
				id_p_ref_indicadores_modelos = '".$_POST['id']."'
				AND
				app = '".$cod_app."'	
		";
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}	
	}
}
	
	
	
	


$Log['res']='exito';
terminar($Log);
