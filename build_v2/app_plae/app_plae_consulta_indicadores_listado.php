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
$Usu = validarUsuario(); // en ./usu_valudacion.php



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

$idUsuario = $_SESSION[$CU]["usuario"]['id'];

$query="SELECT 
		id, nombre, descripcion, funcionalidad, id_p_ref_capasgeo, ic_p_est_02_marcoacademico, periodicidad, fechadesde, 
		fechahasta, usu_autor, zz_borrada, zz_publicada, col_texto1_nom, col_texto2_nom,
		 col_texto3_nom, col_texto4_nom, col_texto5_nom, col_numero1_nom, col_numero2_nom, 
		 col_numero3_nom, col_numero4_nom, col_numero5_nom, col_texto1_unidad, col_texto2_unidad, 
		 col_texto3_unidad, col_texto4_unidad, col_texto5_unidad, col_numero1_unidad, col_numero2_unidad, 
		 col_numero3_unidad, col_numero4_unidad, col_numero5_unidad, representar_campo, representar_val_max, 
		 representar_val_min, zz_borrada_usu, zz_borrada_utime, calc_buffer, calc_superp, calc_zonificacion,
		  calc_superp_campo, unidad_medida, relevancia_acc, limitaciones, ejemplo, datos_input, fuentes_input,
		   calculo, escala_espacial, desagrgacion, valoracion, col_numero1_agregacion, col_numero2_agregacion,
		    col_numero3_agregacion, col_numero4_agregacion, col_numero5_agregacion
	FROM $DB.ref_indicadores_indicadores
        WHERE 
            zz_borrada = '0'
        AND
          zz_publicada = '1'
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

if (pg_num_rows($Consulta) <= 0){
    $Log['tx'][]= "No se encontraron indicadores existentes.";
    $Log['tx'][]= "Query: ".$query;
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de indicadores existentes";
    while ($fila=pg_fetch_assoc($Consulta)){	
		$Log['data'][$fila['id']]=$fila;
		$Log['data'][$fila['id']]['campos']=array();
		$Log['data'][$fila['id']]['registros']=array();
    }
}


$query="	SELECT r.* 
			FROM $DB.ref_indicadores_valores as r
			LEFT JOIN $DB.ref_indicadores_indicadores as i
			on i.id=r.id_p_ref_indicadores_indicadores
        WHERE 
            r.zz_borrado = '0'
        AND
			r.zz_superado = '0'
        AND
			i.zz_borrada = '0'
        AND
    		i.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    		order by r.ano desc, r.mes desc
    		";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$Log['tx'][]= "Consulta de indicadores existentes";
while ($fila=pg_fetch_assoc($Consulta)){
	if(!isset($Log['data'][$fila['id_p_ref_indicadores_indicadores']])){continue;}	
	
	if(isset($Log['data'][$fila['id_p_ref_indicadores_indicadores']]['registros'][$fila['ano']][$fila['mes']])){continue;}	
	$Log['data'][$fila['id_p_ref_indicadores_indicadores']]['registros'][$fila['ano']][$fila['mes']]['n1']=$fila['col_numero1_dato'];
	$Log['data'][$fila['id_p_ref_indicadores_indicadores']]['periodos_cargados'][]=$fila['mes'] .' / '.$fila['ano'];
}


$Log['res']="exito";
terminar($Log);
