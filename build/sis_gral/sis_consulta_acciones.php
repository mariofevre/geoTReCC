<?php 
/**

* consulta de un elemento puntual de alguna de las tablas de la base de datos.
 * 
 *  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @copyright	2018 Universidad de Buenos Aires
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
* 
*
*/

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificación de seguridad 
//include('./includes/conexion.php');
ini_set('display_errors', '1');
chdir('..');

if(!isset($_SESSION)) { session_start(); }

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/pgqonect.php");


$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
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

if(!isset($_POST['tabla'])){
	$Log['tx'][]='no fue enviada la varaible tabla indicando una tabla o un conjunto de estas';
	$Log['res']='err';
	terminar($Log);	
}	

if($_POST['tabla']=='no'){ //se pide el listado de aciones completo no asociadas a ningun marcoacadémico.
	
	$query="
	SELECT 
		codigo, resumen, descripcion
	  FROM
	   	geogec.sis_acciones
	  WHERE
		accmin < 3
	";
	$ConsultaProy = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	}
	
	while($fila=pg_fetch_assoc($ConsultaProy)){
		$Log['data']['acciones'][$fila['codigo']]=$fila;
		$Log['data']['acciones'][$fila['codigo']]['activo']='0';
		//if($fila=='categoria_tabla_geogec'){continue;}
	}	

$Log['res']='exito';
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
if(isset($_SESSION["geogec"]["usuario"]['id'])){

	include_once('./usuarios/usu_validacion.php');
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
		tabla, accion, resumen, descripcion, accmin,
		
		CASE
		    WHEN sis_tablas_acciones.accion is not null THEN 1
		    ELSE 0
		END 
		  AS activa
		  
	  FROM
	   	geogec.sis_acciones
	  LEFT JOIN 
	  	geogec.sis_tablas_acciones 
	  	ON sis_acciones.codigo=sis_tablas_acciones.accion 
	  	AND sis_tablas_acciones.tabla='".$_POST['tabla']."'
	  	
	";
	$ConsultaProy = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	}
	
	while($fila=pg_fetch_assoc($ConsultaProy)){
		$Log['data']['acciones'][$fila['accion']]=$fila;
		//if($fila=='categoria_tabla_geogec'){continue;}
	}	

$Log['res']='exito';
terminar($Log);		
?>


