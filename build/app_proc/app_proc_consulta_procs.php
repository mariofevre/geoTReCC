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

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_docs'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_docs'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<1){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para consultar la caja de documentnos de este marco académico. \n minimo requerido: 1 \ nivel disponible: '.$Acc);
	$Log['res']='err';
	terminar($Log);	
}



$idUsuario = $_SESSION[$CU]["usuario"]['id'];

$query="
	SELECT 
		id, nombre, descripcion
	FROM 
		$DB.ref_proc_procesos
	
    WHERE 
  		zz_borrada = '0'
  	AND
 	 	zz_publicada = '1'
  		
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


$Log['data']['Proc']=array();


while($fila=pg_fetch_assoc($Consulta)){	
	foreach($fila as $k => $v){		
		$Log['data']['Proc'][$fila['id']][$k]=$v;		
	}  
}





$query="
	SELECT 
		pr.id, pr.nombre, pr.descripcion,
		
		pr_co.id as idco, 
		pr_co.nombre as nombre_co, 
		pr_co.descripcion as descripcion_co, 
		pr_co.campo_t_a, 
		pr_co.campo_t_b, 
		pr_co.campo_t_c, 
		pr_co.campo_t_d, 
		pr_co.campo_t_e, 
		pr_co.campo_n_a, 
		pr_co.campo_n_b,  
		pr_co.campo_n_c,  
		pr_co.campo_n_d,  
		pr_co.campo_n_e,  
		pr_co.tipo,  
		pr_co.flujo_modo,
		pr_co.id_local
		
	FROM 
		$DB.ref_proc_procesos as pr
		
	LEFT JOIN 
		$DB.ref_proc_componentes as pr_co
		ON
		pr_co.id_p_proc_proceso = pr.id
		
    WHERE 
  		zz_borrada = '0'
  	AND
 	 	zz_publicada = '1'
  		
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
	
	$Log['data']['Proc'][$fila['id']]['nombre']=$fila['nombre'];
	$Log['data']['Proc'][$fila['id']]['descripcion']=$fila['descripcion'];

	$Log['data']['Proc'][$fila['id']]['componentes'][$fila['idco']]['nombre']=$fila['nombre_co'];
	$Log['data']['Proc'][$fila['id']]['componentes'][$fila['idco']]['descripcion']=$fila['descripcion_co'];
	$Log['data']['Proc'][$fila['id']]['componentes'][$fila['idco']]['tipo']=$fila['tipo'];
	$Log['data']['Proc'][$fila['id']]['componentes'][$fila['idco']]['flujo_modo']=$fila['flujo_modo'];
	$Log['data']['Proc'][$fila['id']]['componentes'][$fila['idco']]['id_local']=$fila['id_local'];
	$Log['data']['Proc'][$fila['id']]['componentes'][$fila['idco']]['estado']='vacio';
	
	$Log['data']['Proc'][$fila['id']]['componentes'][$fila['idco']]['precedentes']=array();
	$Log['data']['Proc'][$fila['id']]['componentes'][$fila['idco']]['posteriores']=array();

	$Log['data']['Proc'][$fila['id']]['componentes'][$fila['idco']]['reportes']=array();
	$Log['data']['Proc'][$fila['id']]['componentes'][$fila['idco']]['generadores']=array();
	
	$ruta='./app_proc/procesos';
	$ruta.='/'.str_pad($fila['id'],5,"0",STR_PAD_LEFT);
	$ruta.='/'.str_pad($fila['idco'],3,"0",STR_PAD_LEFT);
		
	if(file_exists($ruta)){
		$scan=scandir($ruta);		
		foreach($scan as $file){	
			if($file=='.'){continue;}
			if($file=='..'){continue;}
			$e=explode('_',$file);
			if($e[0]!='proc'){continue;}
			if($e[1]=='reporte'){
				$Log['data']['Proc'][$fila['id']]['componentes'][$fila['idco']]['reportes'][]=$file;
			}
			if($e[1]=='genera'){
				$Log['data']['Proc'][$fila['id']]['componentes'][$fila['idco']]['generadores'][]=$file;
			}
		}
	}
	
	
	foreach($fila as $k => $v){
		if(substr($k,0,5)=='campo'){
			$Log['data']['Proc'][$fila['id']]['componentes'][$fila['idco']]['campos'][$k]['nombre']=$v;
		}
	}  
}



$query="
	SELECT 
		copre.id as idcopre,
		copre.nombre as nombrepre, 
		copre.descripcion as descripcionpre, 
		copre.id_p_proc_proceso,
	 
		copos.id as idcopos,
		copos.nombre as nombrepos, 
		copos.descripcion as descripcionpos, 
		
		pr.id_p_ref_proc_componente_precedente as id_precedente, 
		pr.alternativa_precedencia as alternativa_precedencia,
		pr.id as prid, 
		pr.id_p_ref_proc_componente_posterior as id_posterior, 
		pr.comentarios
		
	FROM
		$DB.ref_proc_componentes_precedentes as pr
	LEFT JOIN
		$DB.ref_proc_componentes as copre
		ON copre.id = pr.id_p_ref_proc_componente_precedente 
	LEFT JOIN 
		$DB.ref_proc_componentes as copos
		ON copos.id = pr.id_p_ref_proc_componente_posterior 
	";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
$Log['data']['CapasDisponibles']=array();
while ($fila=pg_fetch_assoc($Consulta)){
	if(!isset($Log['data']['Proc'][$fila['id_p_proc_proceso']]['componentes'][$fila['idcopos']]['precedentes'])){continue;}
	if(!isset($Log['data']['Proc'][$fila['id_p_proc_proceso']]['componentes'][$fila['idcopre']]['posteriores'])){continue;}
	
	$Log['data']['Proc'][$fila['id_p_proc_proceso']]['componentes'][$fila['idcopos']]['precedentes'][$fila['alternativa_precedencia']][$fila['idcopre']]['nombre']=$fila['nombrepre'];
	$Log['data']['Proc'][$fila['id_p_proc_proceso']]['componentes'][$fila['idcopos']]['precedentes'][$fila['alternativa_precedencia']][$fila['idcopre']]['descripcion']=$fila['descripcionpre'];
	$Log['data']['Proc'][$fila['id_p_proc_proceso']]['componentes'][$fila['idcopos']]['precedentes'][$fila['alternativa_precedencia']][$fila['idcopre']]['alternativa_precedencia']=$fila['alternativa_precedencia'];
	
	
	$Log['data']['Proc'][$fila['id_p_proc_proceso']]['componentes'][$fila['idcopre']]['posteriores'][$fila['idcopos']]['nombre']=$fila['nombrepos'];
	$Log['data']['Proc'][$fila['id_p_proc_proceso']]['componentes'][$fila['idcopre']]['posteriores'][$fila['idcopos']]['descripcion']=$fila['descripcionpos'];
	$Log['data']['Proc'][$fila['id_p_proc_proceso']]['componentes'][$fila['idcopre']]['posteriores'][$fila['idcopos']]['alternativa_precedencia']=$fila['alternativa_precedencia'];
	
	
}






$query="
SELECT 
		id, autor, nombre, 
		ic_p_est_02_marcoacademico, zz_borrada, 
		descripcion, 
		nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, 
		nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, 
		zz_publicada, srid, sld, tipogeometria, 
		zz_instrucciones, modo_defecto, wms_layer, 
		zz_aux_ind, zz_aux_rele, modo_publica, 
		tipo_fuente, link_capa, link_capa_campo_local, 
		link_capa_campo_externo, fecha_ano, fecha_mes, fecha_dia
		
	FROM 
	
		$DB.ref_capasgeo
	
	WHERE
		ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


$Log['data']['CapasDisponibles']=array();
while ($fila=pg_fetch_assoc($Consulta)){
	$Log['data']['CapasDisponibles'][$fila['id']]=$fila;		
}




$query="
	SELECT 
		ins.id, 
		ins.id_p_ref_proc_procesos, 
		ins.titulo, 
		
		in_co.id as idco, 
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
		$DB.ref_proc_instancias as ins
	LEFT JOIN 
		$DB.ref_proc_instancias_componentes as in_co
			ON
			in_co.id_p_ref_proc_instancias = ins.id
			
			
	LEFT JOIN 
		$DB.ref_capasgeo as _cap
			ON
			_cap.id = in_co.id_ref AND in_co.tabla='ref_capasgeo'
					
		
    WHERE 
		ins.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
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


$Log['data']['Instancias']=array();
while ($fila=pg_fetch_assoc($Consulta)){
	
	if(!isset($Log['data']['Instancias'][$fila['id']]['componentes'])){$Log['data']['Instancias'][$fila['id']]['componentes']=$Log['data']['Proc'][$fila['id_p_ref_proc_procesos']]['componentes'];}
	
	$Log['data']['Instancias'][$fila['id']]['id_p_ref_proc_procesos']=$fila['id_p_ref_proc_procesos'];		
	$Log['data']['Instancias'][$fila['id']]['titulo']=$fila['titulo'];		
	
	

	
	
	foreach($fila as $k => $v){
		if(
			$k=='id'
			||
			$k=='titulo'
			||
			$k=='id_p_ref_proc_procesos'
		){continue;}
		

		if(substr($k,0,5)=='campo'){
			$Log['data']['Instancias'][$fila['id']]['componentes'][$fila['id_p_ref_proc_componentes']]['campos'][$k]['nombre']=$Log['data']['Proc'][$fila['id']]['componentes'][$fila['id_p_ref_proc_componentes']]['campos'][$k]['nombre'];
			$Log['data']['Instancias'][$fila['id']]['componentes'][$fila['id_p_ref_proc_componentes']]['campos'][$k]['link']=$v;
		}else{
			$Log['data']['Instancias'][$fila['id']]['componentes'][$fila['id_p_ref_proc_componentes']][$k]=$v;		
		}
	} 
	
	
	
	if(
		$fila['tabla']!=''
		&&
		$fila['id_ref']!=''
	){
		if($fila['estado']=='cálculo parcial'){		
			$Log['data']['Instancias'][$fila['id']]['componentes'][$fila['id_p_ref_proc_componentes']]['estado']='vinculado parcial';
		}else{	
			$Log['data']['Instancias'][$fila['id']]['componentes'][$fila['id_p_ref_proc_componentes']]['estado']='vinculado';
		}
	}
	
	
	 
}



$Log['res']="exito";
terminar($Log);
