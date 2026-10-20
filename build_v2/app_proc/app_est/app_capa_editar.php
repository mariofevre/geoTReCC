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
$minacc=2;
if($Acc<$minacc){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificación de este marco académico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
	$Log['tx'][]=print_r($Usu,true);
	$Log['res']='err';
	terminar($Log);
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

if(!isset($_POST['id']) || $_POST['id']<1){
	$Log['res']='error';
	$Log['tx'][]='falta id de capa';	
	terminar($Log);
}

$query="SELECT  *
        FROM    geogec.ref_capasgeo
        WHERE 
  		id='".$_POST['id']."'
  	AND
 	 	zz_borrada = '0'
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

$fila=pg_fetch_assoc($Consulta);

if($fila['zz_borrada']=='1'){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='esta capa figura como borrada. no puede proseguir';
	$Log['res']='err';
	terminar($Log);	
}
if($fila['autor']!=$idUsuario){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='usted no figura como el autor de esta capa: '.$idUsuario;
	$Log['res']='err';
	terminar($Log);	
}

$query = '';

if(isset($_POST['nombre'])){
    $query = "UPDATE
                    geogec.ref_capasgeo
             SET    
                    nombre='".$_POST['nombre']."'
             WHERE
                    ref_capasgeo.id = '".$_POST['id']."'
             AND
                    ref_capasgeo.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
             AND
                    ref_capasgeo.autor='".$idUsuario."'
    ;";
}

if(isset($_POST['descripcion'])){
    $query = "UPDATE
                    geogec.ref_capasgeo
             SET    
                    descripcion='".$_POST['descripcion']."'
             WHERE
                    ref_capasgeo.id = '".$_POST['id']."'
             AND
                    ref_capasgeo.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
             AND
                    ref_capasgeo.autor='".$idUsuario."'
    ;";
}

if(isset($_POST['nombre']) && isset($_POST['descripcion'])) {
    $query = "UPDATE
                     geogec.ref_capasgeo
              SET    
                     nombre = '".$_POST['nombre']."',  
                     descripcion = '".$_POST['descripcion']."'
              WHERE
                     ref_capasgeo.id = '".$_POST['id']."'
              AND
                     ref_capasgeo.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
              AND
                     ref_capasgeo.autor='".$idUsuario."'
    ;";
}


$haycampos='buscando';
$campos='';
foreach($_POST as $k => $v){
	if(substr($k,0,8)=='nom_col_'){
		$campos.=" ".$k." ='".$v."',";
		$haycampos='si';
	}	
}
$campos=substr($campos,0,-1);
if($haycampos=='buscando'){$haycampos='no';}

if($haycampos=='si'){
    
    $query = "UPDATE
                    geogec.ref_capasgeo
             SET    
                   $campos
             WHERE
                    ref_capasgeo.id = '".$_POST['id']."'
             AND
                    ref_capasgeo.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
             AND
                    ref_capasgeo.autor='".$idUsuario."'
    ;";
}

if(isset($_POST['sld'])){
    $query = "UPDATE
                    geogec.ref_capasgeo
             SET    
                    sld='".$_POST['sld']."'
             WHERE
                    ref_capasgeo.id = '".$_POST['id']."'
             AND
                    ref_capasgeo.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
             AND
                    ref_capasgeo.autor='".$idUsuario."'
    ;";
}

if ($query != ''){
    $Consulta = pg_query($ConecSIG, $query);
    if(pg_errormessage($ConecSIG)!=''){
            $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
            $Log['tx'][]='query: '.$query;
            $Log['mg'][]='error interno';
            $Log['res']='err';
            terminar($Log);	
    }

    $fila=pg_fetch_assoc($Consulta);
    
    $Log['tx'][]="Editada capa id: ".$_POST['id'];
    $Log['data']['id']=$_POST['id'];
    $Log['res']="exito";
} else {
    $Log['tx'][]="Error al editar capa";
    $Log['res']="error";
}

terminar($Log);
