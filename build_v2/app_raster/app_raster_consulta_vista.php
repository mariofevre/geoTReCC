<?php
/**
 * genera una salida JSON conteniendo un sample del rasterconsultado.
* 
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018-2023 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrolló sobre una publicación GNU 2017 TReCC SA
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
		
		



//EJEMPLO   - NO FINCIONA AL PARECER:

/*
 * As of PostGIS 2.1.3, out-of-db rasters and all raster drivers are disabled by default. 
 * In order to re-enable these, you need to set the following environment variables 
 * POSTGIS_GDAL_ENABLED_DRIVERS and POSTGIS_ENABLE_OUTDB_RASTERS in the server environment.
 */


ini_set('display_errors', 1);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";
include($GeoGecPath.'/includes/encabezado.php');
include($GeoGecPath."/includes/pgqonect.php");

include_once($GeoGecPath."/usuarios/usu_validacion.php");
//$Usu = validarUsuario(); // en ./usu_valudacion.php

$Hoy_a = date("Y");
$Hoy_m = date("m");
$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

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
