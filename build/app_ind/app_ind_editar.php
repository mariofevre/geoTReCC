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

function IsNullOrEmptyString($string){
    return (!isset($string) || trim($string)==='');
}

function verifyDate($date, $strict = true)
{
    $dateTime = DateTime::createFromFormat('Y-m-d', $date);
    if ($strict) {
        $errors = DateTime::getLastErrors();
        if (!empty($errors['warning_count'])) {
            return false;
        }
    }
    return $dateTime !== false;
}

function validarFechaQuery($fechaPorValidar){
    $fechaNueva = null;
    if (IsNullOrEmptyString($fechaPorValidar) || $fechaPorValidar == 'NULL'){
        $fechaNueva = 'NULL';
    } else {
        if (verifyDate($fechaPorValidar, true)){
            $fechaNueva = "'".$fechaPorValidar."'";
        } else {
            $Log['tx'][]='error, la fecha es incorrecta: -|'.$fechaPorValidar.'|-';
            $Log['res']='err';
            terminar($Log);
        }
    }
    
    return $fechaNueva;
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

$idUsuario = $_SESSION[$CU]["usuario"]['id'];

if(!isset($_POST['id']) || $_POST['id']<1){
	$Log['res']='error';
	$Log['tx'][]='falta id del indicador';	
	terminar($Log);
}

$query="SELECT  *
        FROM    $DB.ref_indicadores_indicadores
        WHERE 
  		id='".$_POST['id']."'
  	AND
 	 	zz_borrada = '0'
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
	$Log['mg'][]='este indicador figura como borrada. no puede proseguir';
	$Log['res']='err';
	terminar($Log);	
}
if($fila['usu_autor']!=$idUsuario){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='el usuario no figura como el autor de este indicador: '.$idUsuario;
	$Log['res']='err';
	terminar($Log);	
}

$query = '';

if(isset($_POST['nombre'])){
	
	$funcionalidad = "";
    if ($_POST['funcionalidad'] == 'NULL'){
        $funcionalidad = "NULL";
    } else {
        $funcionalidad = "'".$_POST['funcionalidad']."'";
    }
    
    
    $query = "UPDATE
                    $DB.ref_indicadores_indicadores
             SET    
                    nombre='".$_POST['nombre']."',
                    descripcion='".$_POST['descripcion']."',
                    periodicidad='".$_POST['periodicidad']."',
                    funcionalidad='".$_POST['funcionalidad']."',
                    fechadesde=".validarFechaQuery($_POST['fechadesde']).",
                    fechahasta=".validarFechaQuery($_POST['fechahasta'])."
                         
             WHERE
                    ref_indicadores_indicadores.id = '".$_POST['id']."'
             AND
                    ref_indicadores_indicadores.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
             AND
                    ref_indicadores_indicadores.usu_autor='".$idUsuario."'
    ;";
}




if(isset($_POST['idcapa'])){
    $query = "UPDATE
                    $DB.ref_indicadores_indicadores
             SET    
                    id_p_ref_capasgeo='".$_POST['idcapa']."'
             WHERE
                    ref_indicadores_indicadores.id = '".$_POST['id']."'
             AND
                    ref_indicadores_indicadores.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
             AND
                    ref_indicadores_indicadores.usu_autor='".$idUsuario."'
    ;";
    
    
    
    
    $f=explode('-',$fila['fechadesde']);
    
    $query2="
		INSERT INTO $DB.ref_indicadores_valores(
				id_p_ref_indicadores_indicadores, 
				ano, mes, dia,
				usu_autor, fechadecreacion, 
				col_texto1_dato, 
				id_p_ref_capas_registros
				)
		SELECT 
				'".$_POST['id']."',
				'".$f[0]."', '".$f[1]."','".$f[2]."',
				'".$idUsuario."', '".date('Y-m-d')."', 
				texto1, 
				id
				
		FROM 
			$DB.ref_capasgeo_registros
		WHERE
			zz_borrada = 0
			AND
			id_ref_capasgeo = '".$_POST['idcapa']."'	
    ";
    $Consulta = pg_query($ConecSIG, $query2);
    if(pg_errormessage($ConecSIG)!=''){
            $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
            $Log['tx'][]='query: '.$query;
            $Log['mg'][]='error interno';
            $Log['res']='err';
            terminar($Log);	
    }

    $fila2=pg_fetch_assoc($Consulta);
    
    $Log['tx'][]="vinculados registros de la capa de referencia la primera fecha";
    $Log['res']="exito";
 
}


if(isset($_POST['calc_buffer'])){
    
    
    $query = "UPDATE
                    $DB.ref_indicadores_indicadores
             SET    
                    calc_buffer='".$_POST['calc_buffer']."'
             WHERE
                    ref_indicadores_indicadores.id = '".$_POST['id']."'
             AND
                    ref_indicadores_indicadores.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
             AND
                    ref_indicadores_indicadores.usu_autor='".$idUsuario."'
    ;";
}


if(isset($_POST['calc_superp'])){   
    
    $query = "UPDATE
                    $DB.ref_indicadores_indicadores
             SET    
                    calc_superp='".$_POST['calc_superp']."'
             WHERE
                    ref_indicadores_indicadores.id = '".$_POST['id']."'
             AND
                    ref_indicadores_indicadores.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
             AND
                    ref_indicadores_indicadores.usu_autor='".$idUsuario."'
    ;";
}


if(isset($_POST['calc_zonificacion'])){   
    
    $query = "UPDATE
                    $DB.ref_indicadores_indicadores
             SET    
                    calc_zonificacion='".$_POST['calc_zonificacion']."'
             WHERE
                    ref_indicadores_indicadores.id = '".$_POST['id']."'
             AND
                    ref_indicadores_indicadores.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
             AND
                    ref_indicadores_indicadores.usu_autor='".$idUsuario."'
    ;";
}



if(isset($_POST['columna']) && isset($_POST['columnavalor'])){
    $columnavalor = "";
    if ($_POST['columnavalor'] == 'NULL'){
        $columnavalor = "NULL";
    } else {
        $columnavalor = "'".$_POST['columnavalor']."'";
    }
    
    $query = "UPDATE
                    $DB.ref_indicadores_indicadores
             SET    
                    ".$_POST['columna']."=".$columnavalor."
             WHERE
                    ref_indicadores_indicadores.id = '".$_POST['id']."'
             AND
                    ref_indicadores_indicadores.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
             AND
                    ref_indicadores_indicadores.usu_autor='".$idUsuario."'
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
    
    $Log['tx'][]="Editado indicador id: ".$_POST['id'];
    $Log['data']['id']=$_POST['id'];
    $Log['res']="exito";
} else {
    $Log['tx'][]="Error al editar indicador id: ".$_POST['id'];
    $Log['res']="error";
}

terminar($Log);
