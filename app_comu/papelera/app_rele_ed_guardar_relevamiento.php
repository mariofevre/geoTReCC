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


$minacc=2;
if(isset($_POST['nivelPermiso'])){
    $minacc=$_POST['nivelPermiso'];
}

$Acc=0;

$Accion='app_publ';
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$Accion])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$Accion];
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

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

$Log['data']['id']=$_POST['idRele'];

if($_POST['idRele']==='0'){
	
	$query="
		INSERT INTO
			geogec.ref_rele_campa(
				nombre,
				ic_p_est_02_marcoacademico,
				usu_autor	
			)
			VALUES(
				'relevamiento sin nombre',
				'".$_POST['codMarco']."',
				'".$idUsuario."'			
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

	$row=pg_fetch_assoc($Consulta);
	
	if($row['id']<1){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}
	
	$_POST['idRele']=$row['id'];
	$Log['data']['nid']=$row['id'];
	$Log['data']['id']=$row['id'];
	
	
	
	
	
}else{
	
	$query="
		SELECT
			*
			FROM
			geogec.ref_rele_campa
		WHERE 
			id='".$_POST['idRele']."'
		AND
			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	";
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}	
	
	
	
	$Campa=pg_fetch_assoc($Consulta);
	$nidcapa='';
	if(	$Campa['id_p_ref_capasgeo']==''
		&&
		($_POST['tipogeometria']=='LineString'
		||
		$_POST['tipogeometria']=='Polygon'
		||
		$_POST['tipogeometria']=='Point')
	){
			
		$query = "
			INSERT INTO geogec.ref_capasgeo
		        (
					autor, 
					ic_p_est_02_marcoacademico, 
					srid, 
					zz_borrada, 
					zz_publicada, 
					nombre, 
					zz_aux_rele, 
					tipogeometria
			)VALUES(
				'".$idUsuario."', 
				'".$_POST['codMarco']."', 
				3857, 
				0, 
				0, 
				'".utf8_encode('capa automática para relevamiento')."',
				'".$_POST['idRele']."', 
				'".$_POST['tipogeometria']."'
			)
		    RETURNING ID;
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
		$Log['tx'][]='se creo la capa id: '.$fila['id'];
		$Campa['id_p_ref_capasgeo']=$fila['id'];
		
		$query="
			UPDATE 
				geogec.ref_rele_campa
			SET
				id_p_ref_capasgeo = '".$Campa['id_p_ref_capasgeo']."'
			WHERE 
				id='".$_POST['idRele']."'
			AND
				ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
		 ";
		
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
		    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		    $Log['tx'][]='query: '.$query;
		    $Log['mg'][]='error interno';
		    $Log['res']='err';
		    terminar($Log);	
		}	
		$Log['tx'][]='asociada capa al relevamiento: '.$fila['id'];
		
	}
	
	
	
	//datos generales de la sesion
	$query="
		UPDATE 
			geogec.ref_rele_campa
		SET 
			nombre = '".$_POST['nombre']."',
			descripcion = '".$_POST['descripcion']."',
			unidadanalisis = '".$_POST['unidadanalisis']."',
			zz_carga_publica = '".$_POST['zz_carga_publica']."'
		WHERE 
			id='".$_POST['idRele']."'
		AND
			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
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
