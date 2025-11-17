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

$_POST=$_GET;
if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['id_raster'])){
	$Log['tx'][]='no fue enviada la variable id_raster';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['tabla_raster_banda'])){
	$Log['tx'][]='no fue enviada la variable tabla_raster_banda';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['px_alto'])){
	$Log['tx'][]='no fue enviada la variable px_alto';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['px_ancho'])){
	$Log['tx'][]='no fue enviada la variable px_ancho';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['min_x'])){
	$Log['tx'][]='no fue enviada la variable min_x';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['max_x'])){
	$Log['tx'][]='no fue enviada la variable max_x';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['min_y'])){
	$Log['tx'][]='no fue enviada la variable min_y';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['max_y'])){
	$Log['tx'][]='no fue enviada la variable max_y';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['mapearcolor'])){
	$Log['tx'][]='no fue enviada la variable mapearcolor';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['modo_salida'])){
	$_POST['modo_salida']='url_imagen';
}

$ancho = abs($_POST['max_x']-$_POST['min_x']);
$alto = abs($_POST['max_y']-$_POST['min_y']);
///COMPLETO

$pre_n_tabla='';

if($ancho>10000 || $alto>10000){
	$pre_n_tabla='o_10_';
}
if($ancho>100000 || $alto>100000){
	$pre_n_tabla='o_100_';	
}


$minx=min($_POST['min_x'],$_POST['max_x']);
$maxx=max($_POST['min_x'],$_POST['max_x']);
$miny=min($_POST['min_y'],$_POST['max_y']);
$maxy=max($_POST['min_y'],$_POST['max_y']);

$str_mc_ini='';
$str_mc_fin='';

if($_POST['mapearcolor']=='si'){
$str_mc_ini="ST_ColorMap(";
$str_mc_fin=",
				1, 
				'grayscale'	
			)";
}


if($_POST['modo_salida']=='url_imagen'){

	$query="

		SELECT 
			
			ST_AsPNG(
				$str_mc_ini
				
					ST_Clip(
						ST_Resample(		
							ST_Transform(
									
								ST_UNION(rast)
								,3857
							),					
							".$_POST['px_ancho'].",
							".$_POST['px_alto']."				
						),
						ST_MakeEnvelope( 
							".$minx.", 
							".$miny.", 
							".$maxx.", 
							".$maxy.", 
							3857
						)
					)	
				$str_mc_fin
			)
			 
		FROM
			geogec_raster.".$pre_n_tabla.$_POST['tabla_raster_banda']."
		WHERE ST_Intersects(
			rast,
			ST_Transform(
				ST_MakeEnvelope( 
					".$minx.", 
					".$miny.", 
					".$maxx.", 
					".$maxy.", 
					3857
				),
				ST_SRID(rast)
			) 
		)
	";
}elseif($_POST['modo_salida']=='url_json'){

	$query="

		SELECT 
			
			ST_AsPNG(
				$str_mc_ini
					ST_Resample(		
					ST_Clip(
						ST_UNION(rast),
						ST_Transform(
							ST_MakeEnvelope( 
								".$minx.", 
								".$miny.", 
								".$maxx.", 
								".$maxy.", 
								3857
							),
							ST_SRID(ST_UNION(rast))
						)
					),										
					".$_POST['px_ancho'].",
					".$_POST['px_alto']."					
					)
				$str_mc_fin
			),
			ST_XMax(
				ST_Envelope(
					ST_Clip(
						ST_UNION(rast),
						ST_Transform(
							ST_MakeEnvelope( 
								".$minx.", 
								".$miny.", 
								".$maxx.", 
								".$maxy.", 
								3857
							),
							ST_SRID(ST_UNION(rast))
						)
					)
				)
			) As xmax,
			ST_XMin(ST_Envelope(
					ST_Clip(
						ST_UNION(rast),
						ST_Transform(
							ST_MakeEnvelope( 
								".$minx.", 
								".$miny.", 
								".$maxx.", 
								".$maxy.", 
								3857
							),
							ST_SRID(ST_UNION(rast))
						)
					)	)
			) As xmin,		
			ST_YMax(ST_Envelope(
					ST_Clip(
						ST_UNION(rast),
						ST_Transform(
							ST_MakeEnvelope( 
								".$minx.", 
								".$miny.", 
								".$maxx.", 
								".$maxy.", 
								3857
							),
							ST_SRID(ST_UNION(rast))
						)
					)	)
			) As ymax,
			ST_yMin(ST_Envelope(
					ST_Clip(
						ST_UNION(rast),
						ST_Transform(
							ST_MakeEnvelope( 
								".$minx.", 
								".$miny.", 
								".$maxx.", 
								".$maxy.", 
								3857
							),
							ST_SRID(ST_UNION(rast))
						)
					)	)
			) As ymin,		
			ST_SRID(ST_UNION(rast)) as srid
			
			
			 
		FROM
			geogec_raster.".$pre_n_tabla.$_POST['tabla_raster_banda']."
		WHERE ST_Intersects(
			rast,
			ST_Transform(
				ST_MakeEnvelope( 
					".$minx.", 
					".$miny.", 
					".$maxx.", 
					".$maxy.", 
					3857
				),
				ST_SRID(rast)
			) 
		)
	";	
	
	
}

$Resultado = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


$fila=pg_fetch_assoc($Resultado);

if($_POST['modo_salida']=='url_imagen'){
	header('Content-type: image/jpeg');
	echo pg_unescape_bytea($fila['st_aspng']);
}elseif($_POST['modo_salida']=='url_json'){
	$Log['data']['img_data']=base64_encode(pg_unescape_bytea($fila['st_aspng']));
	$Log['data']['srid']=$fila['srid'];
	$Log['data']['extend']=array($fila['xmin'], $fila['ymin'], $fila['xmax'], $fila['ymax']);
	$Log['res']='exito';
	terminar($Log);	
	
}



//echo $query;



?>
