<?php 
/**
*
*
*  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2017 TReCC SA
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

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificación de seguridad 
//include('./includes/conexion.php');
ini_set('display_errors', true);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";

if(!isset($_SESSION)) { session_start(); }

// funciones frecuentes
include($GeoGecPath."/includes/fechas.php");
include($GeoGecPath."/includes/cadenas.php");
include($GeoGecPath."/includes/pgqonect.php");
include_once($GeoGecPath."/usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php

require_once($GeoGecPath.'/classes/php-shapefile/src/ShapeFileAutoloader.php');
\ShapeFile\ShapeFileAutoloader::register();
// Import classes
use \ShapeFile\ShapeFile; 
use \ShapeFile\ShapeFileException;

$ID = isset($_GET['id'])?$_GET['id'] : '';

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

$Log['data']=array();
$Log['tx']=array();
$Log['mg']=array();
$Log['res']='';
function terminar($Log){
    global $PROCESANDO;
    $res=json_encode($Log);
    if($res==''){
        $res=print_r($Log,true);
    }
    if(isset($PROCESANDO)){
        return;	
    }else{
        echo $res;
        exit;
    }	
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

if($Acc<2){
    $Log['mg'][]=utf8_encode('No cuenta con permisos (nivel 2 vs nivel '.$Acc.') para generar una nueva capa en la plataforma geoGEC. En el marco de investigación código '.$_POST['codMarco']);
    $Log['res']='err';
    terminar($Log);	
}


$VarNec=Array(
	'codMarco' =>'novacio',
	'idproc' =>'mayor0',
	'idinst' =>'mayor0',
	'idco' =>'mayor0'
);

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

foreach($VarNec as $v => $def){
	
	if($def=='mayor0'){
		
		if(!isset($_POST[$v])){
			
		    $Log['tx'][]="variable necesaria indefinida ($v)";
			$Log['res']='err';
			terminar($Log);
		}
		if($_POST[$v]<=0){
			
		    $Log['tx'][]="variable requiere valor mayor a 0 ($v)";
			$Log['res']='err';
			terminar($Log);		
		}

	}elseif($def=='novacio'){
		
		if(!isset($_POST[$v])){
			
		    $Log['tx'][]="variable necesaria indefinida ($v)";
			$Log['res']='err';
			terminar($Log);
		}
		if($_POST[$v]==''){
			
		    $Log['tx'][]="variable requiere valor mayor a 0 ($v)";
			$Log['res']='err';
			terminar($Log);		
		}
			
	}else{
		
		if(!isset($_POST[$v])){			
			$Log['tx'][]="variable necesaria indefinida ($v)";
			$Log['res']='err';
			terminar($Log);	
		}		
	}
}


if($_POST['idco']!='7'){
	$Log['tx'][]="componente solicitado inesperado ($v)";
	$Log['res']='err';
	terminar($Log);	
}


$Log['data']['idco']=$_POST['idco'];




$query="
	SELECT 
		ins.id_p_ref_proc_procesos, 
		ins.titulo, 
		
		in_co.id_p_ref_proc_componentes as idco, 
		in_co.id_p_ref_proc_componentes, 
		in_co.id_p_ref_proc_instancias, 
		in_co.tabla, 
		in_co.id_ref, 
		in_co.estado, 
		in_co.campo_t_a, 
		in_co.campo_t_b, 
		in_co.campo_t_c, 
		in_co.campo_t_d, 
		in_co.campo_t_e, 
		in_co.campo_n_a, 
		in_co.campo_n_b, 
		in_co.campo_n_c, 
		in_co.campo_n_d, 
		in_co.campo_n_e	
		
	FROM 
		
		geogec.ref_proc_instancias_componentes as in_co			
	LEFT JOIN 
		geogec.ref_proc_instancias as ins
			ON
			in_co.id_p_ref_proc_instancias = ins.id			
	LEFT JOIN 
		geogec.ref_capasgeo as _cap
			ON
			_cap.id = in_co.id_ref AND in_co.tabla='ref_capasgeo'
			
    WHERE 
		in_co.id_p_ref_proc_componentes='".$_POST['idco']."'
	AND
		ins.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
    AND
		in_co.id_p_ref_proc_instancias='".$_POST['idinst']."'
	AND
		in_co.zz_borrada ='0'
    AND
  		ins.zz_borrada = '0'
  		
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

while ($fila=pg_fetch_assoc($Consulta)){		
	$Log['data']['Componente']=$fila;	
}


//idco 7: habitaciones. / viviendas por cantidad de habitaciones por tipo de vivienda
	//ref_capasgeo
	$tab_7_hab=$Log['data']['Componente']['tabla'];
	$reg_7_hab=$Log['data']['Componente']['id_ref'];



$dat=$Log['data']['Componente'];

if($dat['tabla'] == 'ref_capasgeo'){

	$query="
		SELECT 
			c.id, c.autor, c.nombre, 
			c.ic_p_est_02_marcoacademico, c.zz_borrada, c.descripcion, 
			c.nom_col_text1, c.nom_col_text2, c.nom_col_text3, c.nom_col_text4, c.nom_col_text5, c.nom_col_num1, c.nom_col_num2, c.nom_col_num3, c.nom_col_num4, c.nom_col_num5, 
			c.zz_publicada, c.srid, c.sld, 
			c.tipogeometria, c.zz_instrucciones,
			c.modo_defecto, c.wms_layer, c.zz_aux_ind, c.zz_aux_rele, c.modo_publica, c.tipo_fuente, 
			c.link_capa, c.link_capa_campo_local, c.link_capa_campo_externo, c.fecha_ano, c.fecha_mes, c.fecha_dia
		
		FROM 
			geogec.ref_capasgeo as c
		
		WHERE 
			id = '".$dat['id_ref']."'
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
	$Log['tabla']=$fila;
	foreach($fila as $k => $v){
		if($v==''){continue;}
		if(substr($k,0,8)=='nom_col_'){
			$campo=str_replace('nom_col_', '', $k);
			$campo=str_replace('text', 'texto', $campo);
			$campo=str_replace('num', 'numero', $campo);
			//$campos.=' r.'.$campo.', ';
			$Log['tabla']['campos'][$k]=$campo;
			$Log['tabla']['campos_rev'][$campo]=$k;
		}
	}   		
	
	
	
	
	
	
		
	$query="
			SELECT
				SUM(r.numero1) as numero1,
				SUM(r.numero2) as numero2,
				SUM(r.numero3) as numero3,
				SUM(r.numero4) as numero4,
				SUM(r.numero5) as numero5
				from 
					geogec.ref_capasgeo_registros as r
				WHERE
					id_ref_capasgeo='".$reg_7_hab."'
					AND
					zz_borrada='0'
			";

}else{
	
	$Log['tx'][]= "tipo de contenido inesperado para procesar.";
	$Log['res']="err";
	terminar($Log);
}


$Log['tx'][]=$query;
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


$htmlstr="<table>";
while($fila = pg_fetch_assoc($Consulta)){
	
	$htmlstr.='<tr>';
	foreach($fila as $campo => $valor){
		if(!isset($Log['tabla']['campos_rev'][$campo])){continue;}
		$htmlstr.='<th>';
		$cpo=$Log['tabla']['campos_rev'][$campo];
		$htmlstr.=$Log['tabla'][$cpo];
		$htmlstr.='</th>';
	}
	$htmlstr.='</tr>';
	
	
	$htmlstr.='<tr>';
	foreach($fila as $campo => $valor){
		if(!isset($Log['tabla']['campos_rev'][$campo])){continue;}
		$htmlstr.='<td>';
		$htmlstr.=$valor;
		$htmlstr.='</td>';
	}
	$htmlstr.='</tr>';
	
}
$htmlstr.='</table>';



$Log['data']['htmlstr']=$htmlstr;
$Log['res']='exito';
terminar($Log);
