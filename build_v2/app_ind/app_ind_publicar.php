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
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_ind'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_ind'];
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
	$Log['tx'][]='falta id del indicador';	
	terminar($Log);
}

$query="SELECT  
			*,
			funcionalidad,
			id_p_ref_capasgeo
        FROM    
        	geogec.ref_indicadores_indicadores
        WHERE 
  		id='".$_POST['id']."'
  	AND
 	 	zz_borrada = '0'
  	AND
 	 	zz_publicada = '0'
  	AND
  		usu_autor = '".$idUsuario."'
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
	$Log['mg'][]='este indicador figura como borrado. no puede proseguir';
	$Log['res']='err';
	terminar($Log);	
}
if($fila['zz_publicada']=='1'){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='este indicador figura como publicado. no puede proseguir';
	$Log['res']='err';
	terminar($Log);	
}
if($fila['usu_autor']!=$idUsuario){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='El usuario no figura como el autor de este indicador: '.$idUsuario;
	$Log['res']='err';
	terminar($Log);	
}


$query = "UPDATE
                geogec.ref_indicadores_indicadores
         SET    
                zz_publicada='1'
         WHERE
                ref_indicadores_indicadores.id = '".$_POST['id']."'
         AND
                ref_indicadores_indicadores.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
         AND
                ref_indicadores_indicadores.usu_autor='".$idUsuario."'
;";

if ($query != ''){
    $Consulta = pg_query($ConecSIG, $query);
    if(pg_errormessage($ConecSIG)!=''){
            $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
            $Log['tx'][]='query: '.$query;
            $Log['mg'][]='error interno';
            $Log['res']='err';
            terminar($Log);	
    }

    pg_fetch_assoc($Consulta);
    
    $Log['tx'][]="Editado indicador id: ".$_POST['id'];
    $Log['data']['id']=$_POST['id'];

} else {
    $Log['tx'][]="Error al editar indicador";
    $Log['res']="error";
}




if(
	$fila['funcionalidad']=='nuevaGeometria'
){
	
	if(!isset($_POST['tipogeometria'])){
		$_POST['tipogeometria']='Point';
	}

	$query="
		INSERT INTO 
			geogec.ref_capasgeo(
				autor, 
				nombre, 
				ic_p_est_02_marcoacademico, 
				descripcion, 
				srid, 
				tipogeometria, 
				zz_aux_ind,
				zz_borrada
			)
			VALUES (
				'".$idUsuario."', 
				'auxiliar', 
				'".$_POST['codMarco']."',
				'Capa auxiliar para indicador ".$fila['nombre']."', 
				'3857',
				'".$_POST['tipogeometria']."', 
				'".$_POST['id']."',
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
	
    $fila=pg_fetch_assoc($Consulta);
			
	$Log['data']['capa_nid']=$fila['id'];
	
	$query = "UPDATE
	                geogec.ref_indicadores_indicadores
	         SET    
	                id_p_ref_capasgeo = '".$Log['data']['capa_nid']."'
	         WHERE
	                ref_indicadores_indicadores.id = '".$_POST['id']."'
	         AND
	                ref_indicadores_indicadores.id_p_ref_capasgeo is NULL
	         AND
	                ref_indicadores_indicadores.usu_autor='".$idUsuario."'
	";
	$Consulta = pg_query($ConecSIG, $query);
    if(pg_errormessage($ConecSIG)!=''){
            $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
            $Log['tx'][]='query: '.$query;
            $Log['mg'][]='error interno';
            $Log['res']='err';
            terminar($Log);	
    }
	
	
}

$Log['res']="exito";
terminar($Log);
