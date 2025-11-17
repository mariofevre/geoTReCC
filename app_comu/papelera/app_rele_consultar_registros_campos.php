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


if(!isset($_POST['idcampa'])){
	$Log['tx'][]='no fue enviada la variable idcampa';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['idregistrocapa'])){
	$Log['tx'][]='no fue enviada la variable idregistrocapa';
	$Log['res']='err';
	terminar($Log);	
}



$query="SELECT  *
    FROM    
    	geogec.ref_rele_registros
    WHERE 
            zz_borrado = '0'
    AND
            zz_superado = '0'
    AND
            id_p_ref_rele_campa = '".$_POST['idcampa']."'
    AND
    		id_p_ref_capas_registros='".$_POST['idregistrocapa']."'
 	ORDER BY 
			zz_archivada_fecha ASC   
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
if(pg_num_rows($Consulta)==0){
    $Log['tx'][]=utf8_encode('no se encontró registro para la geometría consultada');
	//$Log['mg'][]=utf8_encode('no se encontró el registro consultado:'.$_POST['idregistrocapa']);
    $Log['res']='exito';	
	$Log['data']=null;
    terminar($Log);		
}




$Log['data']['registro']=array();
$Log['data']['campos']=array();
$Log['data']['historicosOrden']=array();
$Log['data']['historicos']=array();


while($row=pg_fetch_assoc($Consulta)){
	
	if($row['zz_archivada']!=1){
		$Log['data']['registro']=$row;
	}else{
		$Log['data']['historicosOrden'][]=$row['id'];
		$Log['data']['historicos'][$row['id']]['registro']=$row;
	}
}




$query="
	SELECT 	
		ref_rele_registros_datos.id, 
		ref_rele_registros_datos.id_p_ref_rele_campa, 
		ref_rele_registros_datos.id_p_ref_rele_campos, 
		ref_rele_registros_datos.id_p_ref_rele_registros, 
		ref_rele_registros_datos.data_texto, 
		ref_rele_registros_datos.data_numero, 
		ref_rele_registros_datos.data_documento
	FROM 
		geogec.ref_rele_registros_datos
	LEFT JOIN
		geogec.ref_rele_registros 
		ON ref_rele_registros_datos.id_p_ref_rele_registros = ref_rele_registros.id
	WHERE
		ref_rele_registros_datos.zz_borrada='0'
	AND
		ref_rele_registros_datos.zz_superado='0'
	AND
		ref_rele_registros_datos.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
	AND
		ref_rele_registros.id_p_ref_capas_registros='".$_POST['idregistrocapa']."'

	";
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
//$Log['mg'][]='encontramos datos para '.pg_num_rows($Consulta).' campos.';


while ($row=pg_fetch_assoc($Consulta)){
	
	if(isset($Log['data']['registro']['id'])){
		if($Log['data']['registro']['id']==$row['id_p_ref_rele_registros']){
			$Log['data']['campos'][$row['id_p_ref_rele_campos']]=$row;
			continue;
		}	
	}
	
	if(!isset($Log['data']['historicos'][$row['id_p_ref_rele_registros']])){continue;}
	
	$Log['data']['historicos'][$row['id_p_ref_rele_registros']]['campos'][$row['id_p_ref_rele_campos']]=$row;
	
	
}

//$Log['mg'][]='consulta exitosa.';
$Log['res']="exito";
terminar($Log);
