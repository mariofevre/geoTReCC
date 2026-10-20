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
/*
if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}*/
/*
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
*/

$Log['data']['busquedatipo']='capa';
if(!isset($_POST['solopublicas'])){$_POST['solopublicas']='no';}
if(!isset($_POST['sinfiltro'])){$_POST['sinfiltro']='no';}

$wherefiltro='';
if($_POST['sinfiltro']=='no'){
	if(!isset($_POST['busqueda'])){
		$Log['tx'][]='no fue enviada la variable zz_publicada';
		$Log['res']='err';
		terminar($Log);	
	}
	if(strlen($_POST['busqueda'])<3){$Log['res']="exito";terminar($Log);}
	
	$wherefiltro="
	AND
 	  (
	 	  ref_capasgeo.nombre LIKE '%".$_POST['busqueda']."%'
	 	  OR
	 	  ref_capasgeo.descripcion LIKE '%".$_POST['busqueda']."%'
	  )
	";
}

if(isset($_SESSION["geogec"]["usuario"])){
	$idUsuario = $_SESSION["geogec"]["usuario"]['id'];
}else{
	$idUsuario='';
}

$wherepub='';
if($_POST['solopublicas']=='si'){
		$wherepub=" AND (modo_publica ='publica' OR modo_publica = 'gec')  ";
}

$query="
	SELECT  
		ref_capasgeo.*,
		sis_usu_registro.nombre as autornom,
		sis_usu_registro.apellido as autorape
		
    FROM
    	geogec.ref_capasgeo
   	LEFT JOIN
		geogec.sis_usu_registro ON sis_usu_registro.id = ref_capasgeo.autor
		
   	
    WHERE 
    
  		zz_borrada = '0'
  	AND
  		zz_aux_ind is null
  	AND
 	 	zz_publicada = '1'
 	 	
 	$wherefiltro
 	
	$wherepub
  		
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

$Log['data']['resultados']=Array();

//$campos='';
while ($fila=pg_fetch_assoc($Consulta)){	
	$Log['data']['resultados'][$fila['id']]=$fila;
	
	$Log['data']['resultados'][$fila['id']]['ref_campos']=array();
	$Log['data']['resultados'][$fila['id']]['campos']='';
	foreach($fila as $k => $v){
		if($v==''){continue;}
		if(substr($k,0,8)=='nom_col_'){
			$campo=str_replace('nom_col_', '', $k);
			$campo=str_replace('text', 'texto', $campo);
			$campo=str_replace('num', 'numero', $campo);
			//$campos.=' r.'.$campo.', ';			
			$Log['data']['resultados'][$fila['id']]['ref_campos'][$k]=$campo;
		}
	}   
}







function nombre_a_campo($k){
	// cambia el tipo nom_col_num1 a numero1
	// la primera refiere al campo que define el nombre del campo de una capa.
	// la seguna al campo de la capa correspondiente
	if(substr($k,0,8)=='nom_col_'){
		$campo=str_replace('nom_col_', '', $k);
		$campo=str_replace('text', 'texto', $campo);
		$campo=str_replace('num', 'numero', $campo);
		$ref_campos[$k]=$campo;
		return $campo;
	}else{
		return "ERR";	
	}
}


if(!isset($_POST['RecorteDeTrabajo'])){
	$_POST['RecorteDeTrabajo']='';
}

if($_POST['RecorteDeTrabajo']!=''){
$c=$_POST['RecorteDeTrabajo'];

$Log['tx'][]='filtrando por recorte espacial: '.print_r($c,true);
	foreach($Log['data']['resultados'] as $idcapa => $datcapa ){	
		
		
		//print_r($datcapa);
				
		$tipogeom=$datcapa['tipogeometria'];
		if(
			$tipogeom=='Polygon'
			||
			$tipogeom=='LineString'
			||
			$tipogeom=='Point'
			){
			$fuentegeometria='local';
		}elseif(
			$tipogeom=='Tabla'
		){
			$fuentegeometria='sin geometria';
			
			if(
				$datcapa['link_capa']!=''
				&&
				$datcapa['link_capa']!='-1'
				&&
				$datcapa['link_capa_campo_local']!=''
				&&
				$datcapa['link_capa_campo_externo']!=''	
			){
				
				$fuentegeometria='externa_capa';
					
				$query="
					SELECT 
						c.id, c.autor, c.nombre, 
						c.ic_p_est_02_marcoacademico,
						c.tipogeometria, c.zz_instrucciones
						
					FROM 
						geogec.ref_capasgeo as c
					
					WHERE 
						id = '".$datcapa['link_capa']."'
				";
				$Consultab = pg_query($ConecSIG, $query);
				if(pg_errormessage($ConecSIG)!=''){
					$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
					$Log['tx'][]='query: '.$query;
					$Log['mg'][]='error interno';
					$Log['res']='err';
					terminar($Log);	
				}	
				$link=pg_fetch_assoc($Consultab);	
				$tipogeom=$link['tipogeometria'];
				
			}elseif(
				$datcapa['link_capa']=='-1'
				&&
				$datcapa['link_capa_campo_local']!=''
				&&
				$datcapa['link_capa_campo_externo']!=''	
			){
				$fuentegeometria='externa_est01';
				$tipogeom='Polygon';
			}

		}else{
			$Log['tx'][]= "No se encontraron registros para esta capa.";
			$Log['tx'][]= utf8_encode("Esta capa aún no cuenta con un tipo de geometría definida.");    
			$Log['res']="exito";
			terminar($Log);
		}


		$mag=1;
		if(
			$tipogeom=='Point'
		){
			$campogeom='geom_point';
			$mag=1;
		}

		if(
			$tipogeom=='Line'
		){
			$campogeom='geom_line';
			$mag=4;
		}

		if(
			$tipogeom=='Polygon'
		){
			$campogeom='geom';
			$mag=16;//valor para pnderar preliminarmente el peso de la información
		}

		if($fuentegeometria=='local'){

			
				$query="
					SELECT  
						COUNT(*)
					FROM    
						geogec.ref_capasgeo_registros as r
					WHERE 
						id_ref_capasgeo = '".$idcapa."'
						AND ST_Intersects(r.".$campogeom.", 'SRID=3857;POLYGON(($c[0] $c[1], $c[2] $c[1], $c[2] $c[3], $c[0] $c[3], $c[0] $c[1]))')
				";
		 
		}elseif($fuentegeometria=='externa_capa'){
			$Log['tx'][]='capa externa';
			$query="SELECT 
						count(*)
						
					FROM    
					   geogec.ref_capasgeo_registros as r
					   FULL OUTER JOIN
					   geogec.ref_capasgeo_registros as lr 
							ON 
								lr.".nombre_a_campo($datcapa['link_capa_campo_externo'])." = r.".$datcapa['ref_campos'][$datcapa['link_capa_campo_local']]." 
								AND 
								r.id_ref_capasgeo = '".$idcapa."'
					WHERE 
						lr.id_ref_capasgeo = '".$datcapa['link_capa']."'
					AND ST_Intersects(lr.".$campogeom.", 'SRID=3857;POLYGON(($c[0] $c[1], $c[2] $c[1], $c[2] $c[3], $c[0] $c[3], $c[0] $c[1]))')
			";
		}
		
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
			
		$fila=pg_fetch_assoc($Consulta);
		//print_r($fila);
		if($fila['count']==0){
			unset($Log['data']['resultados'][$idcapa]);
		}
	}
}



$Log['res']="exito";
terminar($Log);
