<?php 
/**
*
* consulta de centroides de los elementos contenidos en la versión actual de una capa. 
 * Se utiliza para generar una capa interactiva liviana en el mapa online. 
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
	$Log['tx'][]='error: no fue enviada la variable con el nombre de la tabla';
	$Log['mg'][]='error: no fue enviada la variable con el nombre de la tabla';
	$Log['res']='err';
	terminar($Log);
}


$query="
	SELECT * FROM $DB.sis_tablas_config
	WHERE tabla='".$_POST['tabla']."'
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
while($fila=pg_fetch_assoc($ConsultaProy)){
	$Conf=$fila;
}	


$query="
	SELECT id, \"".$Conf['campo_id_humano']."\" nom, \"".$Conf['campo_id_geo']."\" cod, ST_AsText(ST_SnapToGrid(ST_Centroid(geo),1)) geo
	FROM $DB.".$_POST['tabla']." 
	WHERE zz_obsoleto = '0'
	order by nom
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
	$Log['mg'][]=utf8_encode('no se encontraron registros para la tabla solicitada en la base de datos');
	$Log['res']='err';
	terminar($Log);	
}
	$Log['data']['tabla']=$_POST['tabla'];
while($fila=pg_fetch_assoc($ConsultaProy)){
	$Log['data']['centroides'][$fila['id']]=$fila;
	$Log['data']['centroidesOrden'][]=$fila['id'];
	//$Log['data']['centroides'][$fila['id']]['nom']=utf8_encode($fila['nom']);
}	

$Log['res']='exito';
terminar($Log);		
?>
