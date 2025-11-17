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


$Id_Capa_Provincias='304';
$campo_link_provincia='texto1';
$campo_nombre_provincia='texto2';

$Id_Capa_Departamen='237';
$campo_link_departamen='texto1';
$campo_nombre_departamen='texto2';
$campo_id_prov_departamen='texto3';


$idUsuario = $_SESSION[$CU]["usuario"]['id'];




$query="
SELECT 
	i.id, 
	i.id_p_sis_usu_registro, 
	i.nombre, 
	i.link_provincia, 
	i.link_departamento, 
	i.id_p_ref_ind_mod_tag, 
	i.zz_auto_crea_fechau,
	rp.".$campo_nombre_provincia." as provincia,
	rd.".$campo_nombre_departamen." as departamento,
	rd.geom
	
	FROM 
		$DB.ref_inic_inicio as i
	LEFT JOIN 
	$DB.ref_capasgeo_registros as rp 
		ON rp.id_ref_capasgeo = '".$Id_Capa_Provincias."' 
		AND rp.".$campo_link_provincia." = i.link_provincia
	LEFT JOIN 
	$DB.ref_capasgeo_registros as rd 
		ON rd.id_ref_capasgeo = '".$Id_Capa_Departamen."' 
		AND rp.".$campo_link_departamen." = i.link_departamento
		
	WHERE 
		i.id_p_sis_usu_registro ='".$idUsuario."'
	
	";
	
	
$Consulta = pg_query($ConecSIG, $query);


if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.utf8_encode($query);
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


while($fila=pg_fetch_assoc($Consulta)){
	
	$Log['data']['inicio']=$fila;
	
}

	
	
$query="
	SELECT 
	nombre, descripcion as resumen, funcionalidad, periodicidad, 
	usu_autor,
	representar_campo, representar_val_max, representar_val_min,
	   calc_buffer, calc_superp, calc_zonificacion, calc_superp_campo, id,
	    unidad_medida, relevancia_acc, limitaciones, ejemplo, datos_input, fuentes_input, calculo, escala_espacial, desagregacion, valoracion
	FROM 
		$DB.ref_indicadores_modelos
	WHERE zz_borrada='0' AND zz_publicada='1'

";
	 
$Consulta = pg_query($ConecSIG, $query);
$Log['tx'][]='query: '.utf8_encode($query);
 //  echo $query;
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.utf8_encode($query);
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

while($fila=pg_fetch_assoc($Consulta)){
	$id=$fila['id'];
	$Log['data']['indicadoresmodelo'][$fila['id']]=$fila;
	$Log['data']['indicadoresmodelo'][$fila['id']]['requerimientos']=array();
}



$query="
	SELECT 
		i.id_tag,
		l.comentarios as comentarios_tageo,
		m.id as id_modelo
		
	FROM 
		(
        select 
			unnest(ref_inic_inicio.id_p_ref_ind_mod_tag) as id_tag
			from $DB.ref_inic_inicio
			WHERE ref_inic_inicio.id ='".$_POST['idinic']."'
		) as i
	LEFT JOIN
		$DB.ref_indicadores_modelos_tags_links as l
		ON l.id_p_indicadores_modelos_tags = i.id_tag
	LEFT JOIN	
		$DB.ref_indicadores_modelos as m
		ON m.id = l.id_p_indicadores_modelos
		AND m.zz_borrada = '0'
		AND m.zz_publicada = '1'
";
 
$Consulta = pg_query($ConecSIG, $query);
$Log['tx'][]='query: '.utf8_encode($query);
 //  echo $query;
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.utf8_encode($query);
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

while ($fila=pg_fetch_assoc($Consulta)){
	
	if(!isset($Log['data']['indicadoresmodelo'][$fila['id_modelo']])){continue;}
	$Log['data']['indicadoresmodelo_propuestos'][$fila['id_modelo']]=$fila;
}







	
$query="
	SELECT 
		id, descripcion, id_p_ref_indicadores_modelos, app, id_modelo_en_app
	FROM 
		$DB.ref_indicadores_modelos_requerimientos
";
 
$Consulta = pg_query($ConecSIG, $query);
$Log['tx'][]='query: '.utf8_encode($query);
 //  echo $query;
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.utf8_encode($query);
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

while ($fila=pg_fetch_assoc($Consulta)){
	$id=$fila['id'];
	if(!isset($Log['data']['indicadoresmodelo'][$fila['id_p_ref_indicadores_modelos']])){continue;}
	$Log['data']['indicadoresmodelo'][$fila['id_p_ref_indicadores_modelos']]['requerimientos']=array();
}



$Log['res']="exito";
terminar($Log);
