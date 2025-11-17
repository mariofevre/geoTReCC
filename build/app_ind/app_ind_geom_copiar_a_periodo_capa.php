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


if(!isset($_POST['idCapa'])){
	$Log['tx'][]='falta capa';
	terminar($Log);		
}

if(!isset($_POST['idIndicador'])){
	$Log['tx'][]='falta indicador';
	terminar($Log);		
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

function valorNulableQuery($valorAValidar){
    $valorParaQuery = null;
    
    if (IsNullOrEmptyString($valorAValidar) || $valorAValidar == 'NULL'){
        $valorParaQuery = 'NULL';
    } else {
        $valorParaQuery = "'".$valorAValidar."'";
    }
    
    return $valorParaQuery;
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


$query="
		SELECT 
			id, autor, nombre, ic_p_est_02_marcoacademico, 
			zz_borrada, 
			descripcion, 
			nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, 
			nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, 
			zz_publicada, srid, sld, tipogeometria, 
			zz_instrucciones, modo_defecto, wms_layer, zz_aux_ind
		FROM 
			$DB.ref_capasgeo
		WHERE
            id = '".$_POST['idCapa']."'
     ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
$Capa=pg_fetch_assoc($Consulta);

if($Capa['tipogeometria']=='Point'){
	$campogeo='geom_point';
}elseif($Capa['tipogeometria']=='LineString'){
	$campogeo='geom_line';
}else{
	$campogeo='geom';
}


$query="
	SELECT 
		id, nombre, descripcion, funcionalidad, 
		id_p_ref_capasgeo, ic_p_est_02_marcoacademico, periodicidad, 
		fechadesde, fechahasta, usu_autor, zz_borrada, zz_publicada, 
		col_texto1_nom, col_texto2_nom, col_texto3_nom, col_texto4_nom, col_texto5_nom, col_numero1_nom, col_numero2_nom, col_numero3_nom, col_numero4_nom, col_numero5_nom, col_texto1_unidad, col_texto2_unidad, col_texto3_unidad, col_texto4_unidad, col_texto5_unidad, col_numero1_unidad, col_numero2_unidad, col_numero3_unidad, col_numero4_unidad, col_numero5_unidad, representar_campo, representar_val_max, representar_val_min, zz_borrada_usu, zz_borrada_utime, calc_buffer, calc_superp, calc_zonificacion
	FROM 
		$DB.ref_indicadores_indicadores
	
		WHERE
             id = '".$_POST['idIndicador']."'
     ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
$Indicador=pg_fetch_assoc($Consulta);
if($Capa['tipogeometria']=='Point'){
	$campogeo='geom_point';
}elseif($Capa['tipogeometria']=='LineString'){
	$campogeo='geom_line';
}else{
	$campogeo='geom';
}
$Log['data']['idInd']=$Indicador['id'];
$Log['data']['periodicidad']=$Indicador['periodicidad'];


/* geom, texto1, texto2, texto3, texto4, texto5, 
		numero1, numero2, numero3, numero4, numero5, 
		geom_point, geom_line, id_ref_capasgeo, zz_borrada*/
		

	$query="
	
		SELECT
			ref_capasgeo_registros.*
		FROM
		 	$DB.ref_capasgeo_registros		
		WHERE
			ref_capasgeo_registros.zz_borrada='0'
		AND
			ref_capasgeo_registros.id_ref_capasgeo='".$_POST['idCapa']."'	
	
	";
	$Log['tx'][]='query: '.utf8_encode($query);

	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}
	
	
function Nnull($val){
	if($val==''){
		return 'null';
	}else{
		return $val;
	}
}

while($row = pg_fetch_assoc($Consulta)){
	
	$query="
		INSERT INTO $DB.ref_capasgeo_registros(
			".$campogeo.",
			id_ref_capasgeo,
			texto1, 
			texto2, 
			texto3, 
			texto4, 
			texto5, 
			numero1, 
			numero2, 
			numero3, 
			numero4, 
			numero5
		)
		VALUES (
			'".$row[$campogeo]."',
			'".$Indicador['id_p_ref_capasgeo']."',
			'".$row['texto1']."',
			'".$row['texto2']."',
			'".$row['texto3']."',
			'".$row['texto4']."',
			'".$row['texto5']."',
			".Nnull($row['numero1']).",
			".Nnull($row['numero2']).",
			".Nnull($row['numero3']).",
			".Nnull($row['numero4']).",
			".Nnull($row['numero5'])."
		)
		RETURNING
			id
	";	

	$ConsultaB = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}
	$fila=pg_fetch_assoc($ConsultaB);
	$RegCapaId=$fila['id'];

/*
	$extracampo='';
	$extravalor='';
    if ($Indicador['periodicidad'] == 'mensual'){
        $extracampo= " mes, ";
		$extravalor="'".$_POST['mes']."', ";
    }*/
    
     $extracampo= " mes, dia, ";
	$extravalor="'".$_POST['mes']."', '".$_POST['dia']."', ";

	$query = "
		INSERT INTO   
			$DB.ref_indicadores_valores
			
	        (   
	        	id_p_ref_indicadores_indicadores, 
	            ano,
	            ".$extracampo."
	            usu_autor, 
	            fechadecreacion,    
	            zz_superado,
	            zz_borrado,
	            id_p_ref_capas_registros
	        )
	
	        VALUES
	        (   '".$_POST['idIndicador']."',
	            '".$_POST['ano']."',
				".$extravalor."
	            ".$idUsuario.",
	            ".validarFechaQuery($_POST['fechadecreacion']).",
	            0,
	            0,
	            ".$RegCapaId."
	        )
	        RETURNING id;
	 ";

	$ConsultaC = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}
	
}

$Log['res']="exito";

//$Log['tx'][]='Query: '.$query;

terminar($Log);
