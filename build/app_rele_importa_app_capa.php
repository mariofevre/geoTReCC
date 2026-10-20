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
$DB=$_SESSION[$CU]['db_settings']->DATABASE_DB;
include_once("./sis_usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_validacion.php

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d"); $HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	


$Log['data']=array();
$Log['tx']=array();
$Log['mg']=array();
$Log['res']='';

function terminar($Log){
    $res=json_encode($Log);
    //print_r($Log);
    restore_error_handler();
    
    if($res==''){
        echo "err";
        $res=print_r($Log,true);
    }
    echo $res;
    exit;
}



if(!isset($_POST['idcampa'])){
    $Log['tx'][]='no fue enviada la variable idcampa';
    $Log['res']='err';
    terminar($Log);	
}

if(!isset($_POST['id_capa'])){
    $Log['tx'][]='no fue enviada la variable id_capa';
    $Log['res']='err';
    terminar($Log);	
}



//FALTA verificar que la capa corresponda al marco.

$query="SELECT id, autor, nombre, ic_p_est_02_marcoacademico, zz_borrada, descripcion, nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, zz_publicada, srid, sld, tipogeometria, zz_instrucciones, modo_defecto, wms_layer, zz_aux_ind, zz_aux_rele, modo_publica, tipo_fuente, link_capa, link_capa_campo_local, link_capa_campo_externo, fecha_ano, fecha_mes, fecha_dia, cod_col_text1, cod_col_text2, cod_col_text3, cod_col_text4, cod_col_text5, cod_col_num1, cod_col_num2, cod_col_num3, cod_col_num4, cod_col_num5, zz_auto_borra_usu, zz_auto_borra_fechau, zz_cache_extent
	FROM geogec.ref_capasgeo
	WHERE id ='".$_POST['id_capa']."'";
$Consulta = pg_query($ConecSIG,$query);
if(pg_last_error($ConecSIG)!=''){
	$Log['res']='error';
	$Log['tx'][]='error al insertar registro en la base de datos';
	$Log['tx'][]=pg_last_error($ConecSIG);
	$Log['tx'][]=$query;
	terminar($Log);
}			
$o=pg_fetch_assoc($Consulta);	

if($_POST['codMarco']!=$o['ic_p_est_02_marcoacademico']){
	$Log['res']='error';
	$Log['tx'][]='el relevamiento no paree coincidir con el marco de trabajo activo';
	terminar($Log);	
}


$query="
	SELECT 
		id, id_p_ref_capasgeo as id_capa_destino, ic_p_est_02_marcoacademico
	FROM 
		geogec.ref_rele_campa
	
		
	WHERE 
		id = '".$_POST['idcampa']."'
		AND
		ic_p_est_02_marcoacademico='".$o['ic_p_est_02_marcoacademico']."'
";
$Consulta = pg_query($ConecSIG,$query);
if(pg_last_error($ConecSIG)!=''){
	$Log['res']='error';
	$Log['tx'][]='error al insertar registro en la base de datos';
	$Log['tx'][]=pg_last_error($ConecSIG);
	$Log['tx'][]=$query;
	terminar($Log);
}			
$f=pg_fetch_assoc($Consulta);

if($f['id_capa_destino']==null){
	$Log['tx'][]='error al validar acceso de relevamiento a capa';
	terminar($Log);
}

$time=time();

$query="
	INSERT INTO 
		$DB.ref_capasgeo_registros(
			geom, 
			texto1, texto2, texto3, texto4, texto5, 
			numero1, numero2, numero3, numero4, numero5, 
			geom_point, 
			geom_line, 
			id_ref_capasgeo, 
			zz_auto_crea_usu, 
			zz_auto_crea_fechau
		)
	SELECT
		r.geom, 
		r.texto1, r.texto2, r.texto3, r.texto4, r.texto5, 
		r.numero1, r.numero2, r.numero3, r.numero4, r.numero5, 
		r.geom_point, 
		r.geom_line, 
		'".$f['id_capa_destino']."', 
		'".$_SESSION[$CU]["usuario"]['id']."', 
		'".$time."'
	FROM
		$DB.ref_capasgeo_registros as r
		WHERE 
		id_ref_capasgeo='".$_POST['id_capa']."'
		AND
		zz_borrada=0
";
$Consulta = pg_query($ConecSIG,$query);
if(pg_last_error($ConecSIG)!=''){
	$Log['res']='error';
	$Log['tx'][]='error al insertar registro en la base de datos';
	$Log['tx'][]=pg_last_error($ConecSIG);
	$Log['tx'][]=$query;
	terminar($Log);
}	

if($_POST['col']!=''){
	
	$col= str_replace('nom_col_','',$_POST['col']);
	$col= str_replace('text','texto',$col);
	$col= str_replace('num','numero',$col);
			
	$query="
		INSERT INTO 
			$DB.ref_rele_registros(
				id_p_ref_rele_campa, 
				zz_auto_crea_usu, 
				zz_superado, 
				zz_borrado, 
				col_texto1_dato, 
				id_p_ref_capas_registros, 
				zz_auto_crea_fechau, 
				ic_p_est_02_marcoacademico
			)
			SELECT
				'".$_POST['idcampa']."',
				'".$_SESSION[$CU]["usuario"]['id']."', 
				'0',
				'0',
				$col,
				id,
				'".time()."',
				'".$o['ic_p_est_02_marcoacademico']."'
			
			FROM
				$DB.ref_capasgeo_registros
			WHERE
				id_ref_capasgeo = '".$f['id_capa_destino']."'
				AND 			
				zz_auto_crea_fechau = '".$time."'
	";
	$Consulta = pg_query($ConecSIG,$query);
	if(pg_last_error($ConecSIG)!=''){
		$Log['res']='error';
		$Log['tx'][]='error al insertar registro en la base de datos';
		$Log['tx'][]=pg_last_error($ConecSIG);
		$Log['tx'][]=$query;
		terminar($Log);
	}
}

$Log['res']='exito';	
terminar($Log);
