<?php
/**
* 
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
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

ini_set('display_errors', 1);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";
include($GeoGecPath.'/includes/encabezado.php');
include($GeoGecPath."/includes/pgqonect.php");


include_once($GeoGecPath."/usuarios/usu_validacion.php");
//$Usu = validarUsuario(); // en ./usu_valudacion.php
$Usu = validarUsuario(); // en ./usu_valudacion.php

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

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_docs'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_docs'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<1){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para consultar la caja de documentnos de este marco académico. \n minimo requerido: 1 \ nivel disponible: '.$Acc);
	$Log['res']='err';
	terminar($Log);	
}


$idUsuario = $_SESSION["geogec"]["usuario"]['id'];


$Log['data']['linkeables'][-1]=array(

	'autor'=>'GEC',
	'nombre'=>utf8_encode('Departamentos costeros república argentina'),
	'descripcion'=>utf8_encode('Capa utilizada para la organización de la plataforma geoGEC. Los departamentos costerons constituyen el recorte terriotrial sobre el cual esta organización centra sus investigaciones.'),
	'modo_publica'=>'publica',
	'nom_col_text1'=>'COD_DEPTO_'
	
);


$query="
	SELECT  
		ref_capasgeo.*,
		sis_usu_registro.nombre as autornom,
		sis_usu_registro.apellido as autorape,
		est_02_marcoacademico.nombre as marconombre
    FROM
    	geogec.ref_capasgeo
   	LEFT JOIN
		geogec.sis_usu_registro ON sis_usu_registro.id = ref_capasgeo.autor
   	LEFT JOIN
		geogec.est_02_marcoacademico ON ref_capasgeo.ic_p_est_02_marcoacademico = est_02_marcoacademico.codigo
		
    WHERE 
    	
  		zz_borrada = '0'
  	AND
  		zz_aux_ind is null
  	AND
 	 	zz_publicada = '1'
 	AND
		(
 	 	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
 	 	OR
 	 	ref_capasgeo.modo_publica='publica'
 	 	OR
 	 	ref_capasgeo.modo_publica='GEC'
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



if (pg_num_rows($Consulta) <= 0){
    $Log['tx'][]= "No se encontraron capas existentes para este usuario.";
    $Log['data']=null;
} else {
	$Log['tx'][]= "Consulta de capas linikeables";
    while ($fila=pg_fetch_assoc($Consulta)){	
		foreach($fila as $k => $v){
			if($k=='sld'){continue;}
			$Log['data']['linkeables'][$fila['id']][$k]=$v;
		}
    }
}



$Log['res']="exito";
terminar($Log);
