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


if(!isset($_POST['crear_capa'])){
	$_POST['crear_capa']='no';
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];





if(!isset($_POST['idcapa'])){$_POST['idcapa']="";}


if($_POST['idcapa']==""){
	$Log['tx'][]= utf8_encode("No se envió id de capa. se asume que quiere acceder a la capa en creación del usuario.");	
	

	$query="
		SELECT  
			*
		FROM    
			geogec.ref_capasgeo
		WHERE 
			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
		AND
			zz_borrada = '0'
		AND 
			zz_publicada = '0' 
		AND 
			zz_aux_ind is null 
		AND 
			zz_aux_rele is null 
		AND 
			autor = '".$idUsuario."'
	";

	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}	

	$Log['data']['link_capa']=0;	
	
	if(pg_num_rows($Consulta) > 0){
		$Log['tx'][]= "encontrada capa en formulacion.";
		$row=pg_fetch_assoc($Consulta);
		$_POST['idcapa']=$row['id'];	
	}elseif(pg_num_rows($Consulta) <= 0){
		$Log['tx'][]= "No se encontraron capas existentes para este usuario. Creando capa.";	
		$Log['tx'][]= "Post crear_capa: ".$_POST['crear_capa'];	
		if($_POST['crear_capa']=='si'){
			$query="
			
				INSERT INTO 
					geogec.ref_capasgeo(
						autor, nombre, ic_p_est_02_marcoacademico, descripcion, 
						zz_publicada
				
					)VALUES(
						'".$idUsuario."', '- nueva capa -','".$_POST['codMarco']."',  '- nueva capa -',
						'0'

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

			$Log['data']['link_capa']=0;
			$row=pg_fetch_assoc($Consulta);
			$Log['tx'][]= "creada capa id: ".$row['id'];
			$_POST['idcapa']=$row['id'];
		}else{
			$Log['tx'][]= "no fue enviada la confirmación de crear capa.";
		}
	}
}



if($_POST['zz_publicada']==0){
	$wherepub= " AND zz_publicada = '0' AND zz_aux_ind is null AND zz_aux_rele is null AND autor = '".$idUsuario."'";	
}else{
	$wherepub= " AND (zz_publicada = '1' OR autor = '".$idUsuario."')";
}

if (isset($_POST['idcapa'])){
	$whereidcapa= " AND id = '".$_POST['idcapa']."'";	
}else{
	$whereidcapa= " ";
	$wherepub= " AND zz_publicada = '0' AND zz_aux_ind is null AND zz_aux_rele is null AND autor = '".$idUsuario."'";	
}

$query="
	SELECT  
		*
    FROM    
		geogec.ref_capasgeo
    WHERE 
    	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
        zz_borrada = '0'
    $whereidcapa
    $wherepub
    ORDER BY id desc
";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}	

$Log['data']['link_capa']=0;


if (pg_num_rows($Consulta) <= 0){
	$Log['tx'][]= "No se encontraron capas existentes para este usuario. Saliendo.";
	terminar($Log);
} else {
	//Asumimos que solo devuelve una fila
	$fila=pg_fetch_assoc($Consulta);
	$Log['tx'][]= "Consulta de capa existente id: ".$fila['id'];
	foreach($fila as $k => $v){
		$Log['data'][$k]=$v;
	}
}



if(isset($_POST['idcapa'])){
	$whereidcapa= " AND id_p_ref_capasgeo = '".$_POST['idcapa']."'";	
}else{
	$whereidcapa= " ";
}


$Log['data']['simbologias']=array();

//id, ic_p_est_02_marcoacademico, zz_borrada, sld, id_p_ref_capasgeo
$query="
	SELECT 
		id,  sld, nombre
	FROM 
		geogec.ref_capasgeo_simbologias
	WHERE
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	AND
		zz_borrada = '0'
	$whereidcapa
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

while($row=pg_fetch_assoc($Consulta)){
	$Log['data']['simbologias'][$row['id']]['sld']=$row['sld'];	
	$Log['data']['simbologias'][$row['id']]['nombre']=$row['nombre'];	
}


if(isset($Log['data']['link_capa'])){

	if($Log['data']['link_capa']>0){
		
		$query="
			SELECT  *
			FROM    geogec.ref_capasgeo
			WHERE 
			id = '".$Log['data']['link_capa']."'
		";	
		
		$Consulta = pg_query($ConecSIG, $query);
		
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		$row=pg_fetch_assoc($Consulta);
		$Log['data']['muestra_link_capa']=$row['nombre'];
		
		
		if($Log['data']['link_capa_campo_externo']!=''){		
			
			
			
			$campo=str_replace('nom_col_', '', $Log['data']['link_capa_campo_externo']);
			$campo=str_replace('text', 'texto', $campo);
			$campo=str_replace('num', 'numero', $campo);
			if(isset($row[$campo])){
				$Log['data']['muestra_link_capa_campo_externo']=$row[$campo];
			}else{
				$Log['data']['muestra_link_capa_campo_externo']='';
				}
		}	
		
	}

	if($Log['data']['link_capa']=='-1'){
		
		$Log['data']['muestra_link_capa']=utf8_encode('Departamentos costeros república argentina');
			
		if($Log['data']['link_capa_campo_externo']=='COD_DEPTO_'){		
			$Log['data']['muestra_link_capa_campo_externo']='COD_DEPTO_';
		}			
	}

}


$Log['res']="exito";
terminar($Log);
