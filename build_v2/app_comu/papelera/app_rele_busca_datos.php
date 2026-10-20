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
$Usu= validarUsuario();

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

/*
$minacc=2;
if(isset($_POST['nivelPermiso'])){
    $minacc=$_POST['nivelPermiso'];
}

$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_rele'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_rele'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<$minacc){
    $Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificación de este marco académico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
    $Log['tx'][]=print_r($Usu,true);
    $Log['res']='err';
    terminar($Log);
}
*/

if(!isset($_POST['busqueda'])){
	$Log['tx'][]='no fue enviada la variable zz_publicada';
	$Log['res']='err';
	terminar($Log);	
}
if(strlen($_POST['busqueda'])<3){$Log['res']="exito";terminar($Log);}

if(isset($_SESSION["geogec"]["usuario"])){
	$idUsuario = $_SESSION["geogec"]["usuario"]['id'];
}else{
	$idUsuario='';
}

$Log['data']['busquedatipo']='rele';


$query="
	SELECT  
		ref_rele_campa.*,
		sis_usu_registro.nombre as autornom,
		sis_usu_registro.apellido as autorape,
		
		ref_capasgeo.tipogeometria,
		ref_capasgeo.link_capa,
		ref_capasgeo.link_capa_campo_local,
		ref_capasgeo.link_capa_campo_externo
		
	FROM    
		geogec.ref_rele_campa
	LEFT JOIN
		geogec.sis_usu_registro ON sis_usu_registro.id = ref_rele_campa.usu_autor
	LEFT JOIN
		geogec.ref_capasgeo ON id_p_ref_capasgeo = ref_capasgeo.id
	WHERE 
		ref_capasgeo.zz_borrada = '0'
	AND
	  (
		  ref_rele_campa.nombre LIKE '%".$_POST['busqueda']."%'
		  OR
		  ref_rele_campa.descripcion LIKE '%".$_POST['busqueda']."%'
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

$Log['data']['resultados']=Array();
while($row = pg_fetch_assoc($Consulta)){
	$Log['data']['resultados'][$row['id']]=$row;
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
	foreach($Log['data']['resultados'] as $idrele => $datcapa ){	
		
		$idcapa=$datcapa['id_p_ref_capasgeo'];
		
		//print_r($datcapa);
		if($datcapa['tipogeometria']==''){
			$datcapa['tipogeometria']='Polygon';
		}
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

		
			$Log['tx'][]=$fuentegeometria;
			$Log['tx'][]=$tipogeom;

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
								lr.".nombre_a_campo($datcapa['link_capa_campo_externo'])." = r.".nombre_a_campo($datcapa['link_capa_campo_local'])." 
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
		$Log['data']['resultados'][$idrele]['count']=print_r($fila,true);
		
		if($fila['count']==0){
			unset($Log['data']['resultados'][$idrele]);
		}
	}
}

$Log['res']="exito";
terminar($Log);
