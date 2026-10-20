<?php 
/**

* consulta de un elemento puntual de alguna de las tablas de la base de datos.
 * 
 *  
* @package    	geoTReCC
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar>
* @author    	http://www.trecc.com.ar
* @author		based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
* @copyright	2024 TReCC SA
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2018 - Universidad de Buenos Aires
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
include_once("./comun_general/encabezado.php");
include_once("./comun_general/pgqonect.php");
$DB=$_SESSION[$CU]['db_settings']->DATABASE_DB;

include_once("./sis_usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_validacion.php

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;

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

if(!isset($_POST['tabla'])){
	$Log['tx'][]='no fue enviada la varaibla tabla indicando una tabla o un conjunto de estas';
	$Log['res']='err';
	terminar($Log);	
}	

if(!isset($_POST['id'])&&!isset($_POST['cod'])){
	$Log['tx'][]='no fue enviada alguna variable de identificacion id o cod';
	$Log['res']='err';
	terminar($Log);	
}	

if(!isset($_POST['id'])){$_POST['id']=0;}
if($_POST['id']==''){$_POST['id']=0;}


$Acc=0;
if(isset($_SESSION[$CU]["usuario"]['id'])){

	include_once('./sis_usuarios/usu_validacion.php');
	global $ConecSIG;
	$Usu =validarUsuario();//en include_once("./usuarios/usu_validacion.php");
	
	if(isset($Usu['acc']['general']['general']['general'])){
		$Acc=$Usu['acc']['general']['general']['general'];
	}
	if(isset($Usu['acc'][$_POST['tabla']]['general']['general'])){
		$Acc=$Usu['acc'][$_POST['tabla']]['general']['general'];
	}
}

$query="
	SELECT 		
		id, 
		campo_id_geo, campo_id_humano, campo_desc_humano,
		tabla, nombre_humano, descripcion, 
	    resumen, tipo_geometria, crs, categoria_tabla_geogec
	  FROM $DB.sis_tablas_config
	  WHERE
	  tabla = '".$_POST['tabla']."'
	";
	$ConsultaProy = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	}
	if(pg_num_rows($ConsultaProy)<1){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]=utf8_encode('no se encontró el proyecto solicitado en la base de datos');
		$Log['res']='err';
		terminar($Log);	
	}
	

	while($fila=pg_fetch_assoc($ConsultaProy)){
		//if($fila=='categoria_tabla_geogec'){continue;}
		$Log['data']['tablasConf']=$fila;
		$Log['data']['tablasConf']['acciones']=array();
	}	

	$query="
	SELECT 
		tabla, accion, resumen, accmin
	  FROM 
	  	$DB.sis_tablas_acciones,
	  	$DB.sis_acciones
	  WHERE 
	  	tabla='".$_POST['tabla']."'
	  	AND 
	  	sis_acciones.codigo=sis_tablas_acciones.accion
	";
	$ConsultaProy = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	}
	
	while($fila=pg_fetch_assoc($ConsultaProy)){
		$Log['data']['tablasConf']['acciones'][$fila['accion']]=$fila;
		$Log['data']['tablasConf']['acciones'][$fila['accion']]['activo']='1';//por defecto se asume que el marco academico tiene activa la acción.	
		//if($fila=='categoria_tabla_geogec'){continue;}
	}	

	
	$Log['tx'][]=$_POST['tabla'];
	if($_POST['tabla']=='est_02_marcoacademico'){
		     $Log['tx'][]=$_POST['cod'];     
		$query="
			SELECT 
				a.id, 
				a.ic_p_est_02_marcoacademico, 
				a.codigo_p_sis_acciones, 
				a.activo
			FROM 
				$DB.ref_02_marcoacademico_link_acciones as a
		    WHERE 
		    	a.ic_p_est_02_marcoacademico = '".$_POST['cod']."'
		";
		
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		$Log['tx'][]='consultado';
		while($row = pg_fetch_assoc($Consulta)){
			$Log['tx'][]=$row['codigo_p_sis_acciones'];
			
			if(!isset($Log['data']['tablasConf']['acciones'][$row['codigo_p_sis_acciones']])){continue;}
			
			$Log['tx'][]='esta';
			$Log['data']['tablasConf']['acciones'][$row['codigo_p_sis_acciones']]['activo']=$row['activo'];	
		}
		
		
		$query="
			SELECT 
				b.css_color_botones,
				b.css_borde_botones,
				ST_AsText(b.centro) as centro	
			FROM 
				$DB.per_02_marcoacademico as b
		    WHERE 
		    	b.ic_p_est_02_marcoacademico = '".$_POST['cod']."'
		";
		
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		$Log['tx'][]='consultado';
		while($row = pg_fetch_assoc($Consulta)){
			$Log['data']['personalizacion']=$row;	
			$Log['data']['personalizacion']['sitios']=array();	
		}
		
		
		
		$query="
		
		
			SELECT 
				nombre, 
				ST_AsText(centro) as centro,
				id as idp
			FROM 
				$DB.per_02_marcoacademicio_sitios as b
		    WHERE 
				zz_borrada!='1'
				AND
		    	b.ic_p_est_02_marcoacademico = '".$_POST['cod']."'
		";
		
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		$Log['tx'][]='consultado';
		while($row = pg_fetch_assoc($Consulta)){
			$Log['data']['personalizacion']['sitios'][$row['idp']]=$row;	
		}
	}
	
	
	$llamadageo="ST_AsText(geo) as geotx";
		
	$query="
		SELECT 
			*,
			$llamadageo
		FROM 
			$DB.".$_POST['tabla']."
		WHERE 
		 zz_obsoleto = '0'
		 AND
		 (
			\"".$Log['data']['tablasConf']['campo_id_geo']."\" = '".$_POST['cod']."'
		OR			
			id = '".$_POST['id']."'
			)
			
			
	";
	$ConsultaProy = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	}
	if(pg_num_rows($ConsultaProy)<1){
		$Log['tx'][]='error';
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='no se encontraron tablas disponibles';
		$Log['res']='err';
		terminar($Log);	
	}
	
	while($fila=pg_fetch_assoc($ConsultaProy)){
			
		if(isset($Usu['acc'])){
			if(isset($Usu['acc'][$_POST['tabla']][$_POST['cod']]['general'])){
				$Acc=$Usu['acc'][$_POST['tabla']][$_POST['cod']]['general'];
			}
		}
		
		if(isset($fila['zz_accesolibre'])){
			if($fila['zz_accesolibre']=='1'){
				$Acc=max('2',$Acc);
			}
		}
		
		$Log['data']['elemento']=$fila;
		$Log['data']['elemento']['acceso']=$Acc;
		
		foreach($Log['data']['tablasConf']['acciones'] as $accion => $accdata){
			$accAccion=$Acc;
			if(isset($Usu['acc'][$_POST['tabla']][$_POST['cod']][$accion])){
				$accAccion=$Usu['acc'][$_POST['tabla']][$_POST['cod']][$accion];
			}
			$Log['data']['elemento']['accesoAccion'][$accion]=$Acc;
		}
	}	

$Log['res']='exito';
terminar($Log);		
?>
