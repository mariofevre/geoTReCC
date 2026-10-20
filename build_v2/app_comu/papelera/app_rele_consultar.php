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

$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<1){
    $Log['mg'][]=utf8_encode('No cuenta con permisos (nivel  vs nivel '.$Acc.') para consultar un indicador. En el marco de investigación código '.$_POST['codMarco']);
    $Log['res']='err';
    terminar($Log);	
}



$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

$query="
	SELECT  
		ref_rele_campa.*,
		ref_capasgeo.tipogeometria
    FROM    geogec.ref_rele_campa
    LEFT JOIN
		geogec.ref_capasgeo ON ref_capasgeo.id = ref_rele_campa.id_p_ref_capasgeo AND ref_capasgeo.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    WHERE 
    	ref_rele_campa.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
        ref_rele_campa.zz_borrada = '0'
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

if (pg_num_rows($Consulta) == 0){
    $Log['tx'][]= "No se encontro el relevamiento solicitado.";
    $Log['data']=null;
	$Log['res']="err";
	terminar($Log);
}

//Asumimos que solo devuelve una fila
$fila=pg_fetch_assoc($Consulta);
$Log['tx'][]= "Consulta de indicador existente id: ".$fila['id'];
//$Log['data']=$fila;
foreach($fila as $k => $v){
	$Log['data'][$k]=$v;
}

$query="
	SELECT  
		id, 
		nombre, 
		ayuda, 
		inputattributes, 
		opciones, 
		unidaddemedida, 
		tipo, 
		ic_p_est_02_marcoacademico
    FROM    
    	geogec.ref_rele_campos
    WHERE 
    	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
        zz_borrada = '0'
    AND 
    	id_p_ref_rele_campa = '".$_POST['idcampa']."'
    order by orden asc, id asc
";


$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

$Log['data']['camposOrden']=array();
$Log['data']['campos']=array();
while($row = pg_fetch_assoc($Consulta)){
	$Log['data']['camposOrden'][]=$row['id'];
	foreach($row as $k => $v){
		$Log['data']['campos'][$row['id']][$k]=$v;
	}
}




$Log['res']="exito";
terminar($Log);
