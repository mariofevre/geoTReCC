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

if(!isset($_POST['fuentes'])){
	$Log['tx'][]='no fue enviada la variable fuentes';
	$Log['res']='err';
	terminar($Log);	
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

if(count($_POST['fuentes'])<1){
	$Log['tx'][]='no fue enviada ninguna fuente dentro de la variable fuentes';
	$Log['res']='err';
	terminar($Log);
}
	

	$query="
	SELECT 
		ref_capasgeo.tipogeometria
	FROM 
		geogec.ref_rele_campa
	LEFT JOIN
		geogec.ref_capasgeo ON ref_capasgeo.id = ref_rele_campa.id_p_ref_capasgeo
	WHERE
		ref_rele_campa.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	AND
		ref_rele_campa.id = '".$_POST['idcampa']."'
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
    $Log['tx'][]= "No se encontro campana.";
    $Log['data']=null;
    $Log['res']='err';
    terminar($Log);		
}

$campa=pg_fetch_assoc($Consulta);





	$query="
	
	SELECT 
		id, nombre, ayuda, inputattributes, opciones, unidaddemedida, tipo
	FROM 
		geogec.ref_rele_campos
	WHERE
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	AND
		id_p_ref_rele_campa = '".$_POST['idcampa']."'
	AND
		zz_borrada='0'
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
    $Log['tx'][]= "No se encontraron campos.";
    $Log['data']=null;
    $Log['res']='err';
    terminar($Log);		
}

while ($fila=pg_fetch_assoc($Consulta)){	
	$campos[$fila['id']]=$fila;
	

}


	
	$instertcampo='';
	$insertval='';
	
	
	$insertregcampos='';
	$JOINS='';
	$COLS='';
	$countt=1;
	$countn=0;
	
	foreach($_POST['fuentes'] as $f){
		
		if($f['fue']='nombreUA'){
			
			$Log['tx'][]='nombre de UA como campo';
			$arr_input=array();
			
			
				//el checbox con valor si se exporta ocmo numérico de valor 1
				$countt++;
				
				$instertcampo.='nom_col_text'.$countt.', ';
				$insertval.="'".$f['nom']."', ";
				$insertregcampos.='texto'.$countt.', ';
				
				
				$COLS.="
				datotexto".$countt.".dato as datotexto".$countt.",";	
				$JOINS.="
				
					LEFT JOIN
						(SELECT id, texto1 as dato FROM geogec.ref_capasgeo_registros WHERE zz_borrada='0' )
						as datotexto".$countt." 
						ON datotexto".$countt.".id = ref_rele_registros.id_p_ref_capas_registros
       	
    	
				";
			
				$Log['tx'][]=$COLS;
				$Log['tx'][]=$JOINS;
			
			
		}elseif($campos[$f['fue']]['tipo']=='texto' || $campos[$f['fue']]['tipo']=='fecha'){
			
			$Log['tx'][]=$campos[$f['fue']]['inputattributes'];
			$arr_input=json_decode($campos[$f['fue']]['inputattributes'], true);
			
			$Log['tx'][]=$arr_input;;
			if($arr_input['type']=='checkbox'){			
				//el checkbox con valor si se exporta ocmo numérico de valor 1
				$countn++;
				
				$instertcampo.='nom_col_num'.$countn.', ';
				$insertval.="'".$f['nom']."', ";
				$insertregcampos.='numero'.$countn.', ';
				
				$COLS.="
				datotexto".$countt.".dato as datotexto".$countt.",";	
				$JOINS.="
					LEFT JOIN
					(SELECT 
						id_p_ref_rele_registros, 
						 CASE 
							WHEN data_texto='si' THEN 1
							ELSE 0
							END as dato
						FROM geogec.ref_rele_registros_datos 
						WHERE 
							id_p_ref_rele_campos = '".$f['fue']."'
					)as datotexto".$countt." 
						ON datotexto".$countt.".id_p_ref_rele_registros = ref_rele_registros.id
				";
			
				$Log['tx'][]=$COLS;
				$Log['tx'][]=$JOINS;
				
			}else{
				
				$countt++;
			
				$instertcampo.='nom_col_text'.$countt.', ';
				$insertval.="'".$f['nom']."', ";
				$insertregcampos.='texto'.$countt.', ';
				
				$COLS.="
				datotexto".$countt.".dato as datotexto".$countt.",";	
				$JOINS.="
					LEFT JOIN
					(SELECT 
						id_p_ref_rele_registros, data_texto as dato 
						FROM geogec.ref_rele_registros_datos 
						WHERE 
							id_p_ref_rele_campos = '".$f['fue']."'
					)as datotexto".$countt." 
						ON datotexto".$countt.".id_p_ref_rele_registros = ref_rele_registros.id
				";
				
			}
			
		}elseif($campos[$f['fue']]['tipo']=='numero'){
			$countn++;
			
			$instertcampo.='nom_col_num'.$countn.', ';
			$insertval.="'".$f['nom']."', ";
			$insertregcampos.='numero'.$countn.', ';
			
			$COLS.="
			datonumero".$countn.".dato as datonumero".$countn.",";	
			$JOINS.="
				LEFT JOIN
    			(SELECT 
					id_p_ref_rele_registros, data_numero as dato 
					FROM geogec.ref_rele_registros_datos 
					WHERE 
						id_p_ref_rele_campos = '".$f['fue']."' 
				)as datonumero".$countn." 
					ON datonumero".$countn.".id_p_ref_rele_registros = ref_rele_registros.id
			";
		
		}else{
			    $Log['tx'][]= "error de tipo de campo:".$campos[$f['fue']]['tipo'];
		}
		
	}


	$query="
	INSERT INTO 
		geogec.ref_capasgeo(
		autor, nombre, ic_p_est_02_marcoacademico, descripcion,
		nom_col_text1,
		$instertcampo
		zz_publicada, tipogeometria)
	VALUES (
		'".$idUsuario."', 'capa exportada desde relevamiento ".$_POST['idcampa']."', '".$_POST['codMarco']."', 'capa exportada desde relevamiento ".$_POST['idcampa']." ".date('Y-m-d')."',
		'UnidadAnalisis',
		$insertval
		'1', '".$campa['tipogeometria']."')
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
$fila=pg_fetch_assoc($Consulta);
$Log['data']['nidcapa']=$fila['id'];	




$query="
INSERT INTO geogec.ref_capasgeo_registros(
	geom,
	geom_point,
	geom_line,
	texto1,
	$insertregcampos
	id_ref_capasgeo, 
	zz_borrada, 
	zz_auto_crea_usu, 
	zz_auto_crea_fechau)
	
	SELECT
		geom,
		geom_point,
		geom_line,
		ref_rele_registros.col_texto1_dato as nombreua,
		$COLS 
		'".$Log['data']['nidcapa']."' as id_ref_capasgeo,
		'0',
		'".$idUsuario."',
		'".time()."'
		
    FROM    
    	geogec.ref_rele_registros
    	LEFT JOIN
    	(SELECT id, geom, geom_point, geom_line  FROM geogec.ref_capasgeo_registros WHERE zz_borrada='0' )as registrosgeo ON registrosgeo.id = ref_rele_registros.id_p_ref_capas_registros
    	
    	$JOINS
    WHERE 
            zz_borrado = '0'
    AND
            zz_superado = '0'
    AND
            id_p_ref_rele_campa = '".$_POST['idcampa']."'
	AND (
		geom IS NOT null
		OR
		geom_point IS NOT null
		OR
		geom_line IS NOT null
	)
    
 ";
$Log['tx'][]=$query;
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}


$Log['res']="exito";
terminar($Log);
