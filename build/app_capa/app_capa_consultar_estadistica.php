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

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['idcapa'])){
	$Log['tx'][]='no fue enviada la variable idcapa';
	$Log['res']='err';
	terminar($Log);	
}


$idUsuario = $_SESSION[$CU]["usuario"]['id'];


if (isset($_POST['idcapa'])){
	$whereidcapa= " AND id = '".$_POST['idcapa']."'";	
}else{
	$whereidcapa= " ";
}

$query="
	SELECT  *
    FROM    $DB.ref_capasgeo
    WHERE 
    	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
      id = '".$_POST['idcapa']."'
";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if ($query != ''){
    if (pg_num_rows($Consulta) <= 0){
        $Log['tx'][]= "No se encontraron capas existentes para este usuario.";
        $Log['data']=null;
    } else {
        //Asumimos que solo devuelve una fila
        $fila=pg_fetch_assoc($Consulta);
        $Log['tx'][]= "Consulta de capa existente id: ".$fila['id'];
        $Log['data']=$fila;
    }
} else {
    $Log['tx'][]="Error al crear la query para consultar capa";
    $Log['res']="error";
}



if($Log['data']['link_capa']>0){
	
	$query="
		SELECT  *
		FROM    $DB.ref_capasgeo
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
	
	$Log['data']['muestra_link_capa']=$Consulta['nombre'];
	
	
	if($Log['data']['link_capa_campo_externo']!=''){		
		$campo=str_replace('nom_col_', '', $Log['data']['link_capa_campo_externo']);
		$campo=str_replace('text', 'texto', $campo);
		$campo=str_replace('num', 'numero', $campo);
		$Log['data']['muestra_link_capa_campo_externo']=$Consulta[$campo];
	}	
}

if($Log['data']['link_capa']=='-1'){
	
	$Log['data']['muestra_link_capa']=utf8_encode('Departamentos costeros república argentina');
		
	if($Log['data']['link_capa_campo_externo']=='COD_DEPTO_'){		
		$Log['data']['muestra_link_capa_campo_externo']='COD_DEPTO_';
	}			
}


$tipogeom=$Log['data']['tipogeometria'];
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
		$Log['data']['link_capa']!=''
		&&
		$Log['data']['link_capa']!='-1'
		&&
		$Log['data']['link_capa_campo_local']!=''
		&&
		$Log['data']['link_capa_campo_externo']!=''	
	){
		
		$fuentegeometria='externa_capa';
			
		$query="
			SELECT 
				c.id, c.autor, c.nombre, 
				c.ic_p_est_02_marcoacademico,
				c.tipogeometria, c.zz_instrucciones
				
			FROM 
				$DB.ref_capasgeo as c
			
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
		$link=pg_fetch_assoc($Consulta);	
		$tipogeom=$link['tipogeometria'];
		
	}elseif(
		$Log['data']['link_capa']=='-1'
		&&
		$Log['data']['link_capa_campo_local']!=''
		&&
		$Log['data']['link_capa_campo_externo']!=''	
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


if($fuentegeometria=='local'){

	
	
		$query="SELECT  
					count(*), 
					SUM (numero1) AS sum_numero1 , avg(numero1) AS avg_numero1,
					SUM (numero2) AS sum_numero2 , avg(numero2) AS avg_numero2,
					SUM (numero3) AS sum_numero3 , avg(numero3) AS avg_numero3,
					SUM (numero4) AS sum_numero4 , avg(numero4) AS avg_numero4,
					SUM (numero5) AS sum_numero5 , avg(numero5) AS avg_numero5,
					ROUND(SUM (ST_Area(geom))/1000000) as sum_sup, ROUND(avg (ST_Area(geom))/1000000) as avg_sup,
					ROUND(SUM (ST_Length(geom_line))/1000000) as sum_larg, ROUND(avg (ST_Area(geom_line))/1000000) as avg_larg
				FROM    
					$DB.ref_capasgeo_registros as r
				WHERE 
					zz_borrada='0'
					AND
					id_ref_capasgeo = '".$_POST['idcapa']."'
			";

 
}elseif($fuentegeometria=='externa_capa'){

	
	$query="SELECT  
	
	
			count(*), 
			SUM (r.numero1) AS sum_numero1 , avg(r.numero1) AS avg_numero1,
			SUM (r.numero2) AS sum_numero2 , avg(r.numero2) AS avg_numero2,
			SUM (r.numero3) AS sum_numero3 , avg(r.numero3) AS avg_numero3,
			SUM (r.numero4) AS sum_numero4 , avg(r.numero4) AS avg_numero4,
			SUM (r.numero5) AS sum_numero5 , avg(r.numero5) AS avg_numero5,
			ROUND(SUM (ST_Area(lr.geom))/1000000) as sum_sup, ROUND(avg (ST_Area(lr.geom))/1000000) as avg_sup,
			ROUND(SUM (ST_Length(lr.geom_line))/1000000) as sum_larg, ROUND(avg (ST_Area(lr.geom_line))/1000000) as avg_larg
			
        FROM    
           $DB.ref_capasgeo_registros as r
           FULL OUTER JOIN
           $DB.ref_capasgeo_registros as lr 
				ON lr.".nombre_a_campo($Log['data']['link_capa_campo_externo'])." = r.".nombre_a_campo($Log['data']['link_capa_campo_local'])." 
				
				AND r.id_ref_capasgeo = '".$_POST['idcapa']."'
        WHERE 
  			lr.id_ref_capasgeo = '".$Log['data']['link_capa']."'
  			AND
  			r.zz_borrada='0'
					

	";


	
}elseif($fuentegeometria=='externa_est01'){

	$campogeom='geo';
	
	$query="SELECT  
			count(*), 
			SUM (r.numero1) AS sum_numero1 , avg(r.numero1) AS avg_numero1,
			SUM (r.numero2) AS sum_numero2 , avg(r.numero2) AS avg_numero2,
			SUM (r.numero3) AS sum_numero3 , avg(r.numero3) AS avg_numero3,
			SUM (r.numero4) AS sum_numero4 , avg(r.numero4) AS avg_numero4,
			SUM (r.numero5) AS sum_numero5 , avg(r.numero5) AS avg_numero5,
			
			ROUND(SUM (ST_Area(lr.geom))/1000000) as sum_sup, ROUND(avg (ST_Area(lr.geom))/1000000) as avg_sup,
			ROUND(SUM (ST_Length(lr.geom_line))/1000000) as sum_larg, ROUND(avg (ST_Area(lr.geom_line))/1000000) as avg_larg
                
        FROM    
           $DB.ref_capasgeo_registros as r
           LEFT JOIN
          
           $DB.est_01_municipios as lr ON lr.".$Log['data']['link_capa_campo_externo']." = r.".nombre_a_campo($Log['data']['link_capa_campo_local'])."
        WHERE 
  			r.id_ref_capasgeo = '".$_POST['idcapa']."' 
  			AND
  			r.zz_borrada='0'
  		";
	
}



$Consulta = pg_query($ConecSIG, $query);
 //  echo $query;
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.utf8_encode($query);
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
$countfila=pg_fetch_assoc($Consulta);
$Log['data']['estadisticas']=$countfila;




$Log['res']="exito";
terminar($Log);
