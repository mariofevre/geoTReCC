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

if(!isset($_POST['id_rele_campa'])){
	$Log['tx'][]='no fue enviada la variable id_rele_campa';
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



$idUsuario = $_SESSION[$CU]["usuario"]['id'];




$query="
	SELECT 
	
	ref_indicadores_indicadores.*,
	ref_capasgeo.tipogeometria
		
	FROM 
		$DB.ref_indicadores_indicadores
	LEFT JOIN
		$DB.ref_capasgeo 
			ON ref_capasgeo.id = ref_indicadores_indicadores.id_p_ref_capasgeo 
			AND ref_capasgeo.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	WHERE 
    	ref_indicadores_indicadores.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
        ref_indicadores_indicadores.zz_borrada = '0'
    AND 
    	ref_indicadores_indicadores.id = '".$_POST['idindicador']."'	
    	
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
    $Log['tx'][]= "No se encontro el indicador solicitado.";
    $Log['data']=null;
	$Log['res']="err";
	terminar($Log);
}

//Asumimos que solo devuelve una fila
$fila=pg_fetch_assoc($Consulta);
$Log['tx'][]= "Consulta de indicador existente id: ".$fila['id'];
//$Log['data']=$fila;
foreach($fila as $k => $v){
	$Log['data']['indicador'][$k]=$v;
}


$years = array();
$i = 0;
for ($nYear = date('Y',strtotime($Log['data']['indicador']['fechadesde'])); 
        $nYear <= date('Y',strtotime($Log['data']['indicador']['fechahasta'])); $nYear++) {
    $years[$i] = $nYear;
    $i++;
}
$periodicidad = $Log['data']['indicador']['periodicidad'];

if($Log['data']['indicador']['id_p_ref_capasgeo']<1){
	$Log['mg'][]=utf8_encode('este indicador no está completo en su definición. No tiene una capa asociada.');	
	
}else{
		
	$Log['data']['periodos'] = array();
	$mes = date('n',strtotime($Log['data']['indicador']['fechadesde']));
	$dia = date('j',strtotime($Log['data']['indicador']['fechadesde']));
		
	foreach ($years as $ano) {
		
		$Log['data']['periodos'][$ano] = array();		
		
		if ($periodicidad == 'anual'){
			$Log['data']['periodos'][$ano][$mes]= array();
			$Log['data']['periodos'][$ano][$mes][$dia] = array();
			$Log['data']['periodos'][$ano][$mes][$dia]['estado']='sin carga';	
			$Log['data']['periodos'][$ano][$mes][$dia]['id_p_ref_capas_registros']=array();
			
		}else{
			
			if ($mes > 12){$mes = 1;}	
			while ($mes <= 12) {
				
				$Log['data']['periodos'][$ano][$mes]= array();
				
				if ($periodicidad == 'mensual'){
					
					$Log['data']['periodos'][$ano][$mes][$dia] = array();
					$Log['data']['periodos'][$ano][$mes][$dia]['estado']='sin carga';
					$Log['data']['periodos'][$ano][$mes][$dia]['id_p_ref_capas_registros']=array();
					
					if (				
						$ano == date('Y',strtotime($Log['data']['indicador']['fechahasta']))
						&& 
						$mes > date('n',strtotime($Log['data']['indicador']['fechahasta']))
					){break;}	
														
				}elseif($periodicidad == 'diario'){
					
					$diamax=diasenelmesano($ano.'-'.$mes.'-1');
					while($dia <= $diamax){
						
						$Log['data']['periodos'][$ano][$mes][$dia] = array();
						$Log['data']['periodos'][$ano][$mes][$dia]['estado']='sin carga';						
						$Log['data']['periodos'][$ano][$mes][$dia]['id_p_ref_capas_registros']=array();
						
						if(	
							$ano == date('Y',strtotime($Log['data']['indicador']['fechahasta']))
							&& 
							$mes == date('n',strtotime($Log['data']['indicador']['fechahasta']))
							&&
							$dia > date('j',strtotime($Log['data']['indicador']['fechahasta']))								 
						){break 2;}						
						$dia++;
						
					}
					$dia=1;						
				}								
	            $mes++;
	        }			
		}
	}	
}	
	
$query="
	SELECT  
		ref_rele_campa.*,
		ref_capasgeo.tipogeometria
    FROM    
		$DB.ref_rele_campa
    LEFT JOIN
		$DB.ref_capasgeo ON ref_capasgeo.id = ref_rele_campa.id_p_ref_capasgeo AND ref_capasgeo.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    WHERE 
    	ref_rele_campa.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
        ref_rele_campa.zz_borrada = '0'
    AND 
    	ref_rele_campa.id = '".$_POST['id_rele_campa']."'
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
$Log['tx'][]= "Consulta de indicador relevamiento (campa) id: ".$fila['id'];
//$Log['data']=$fila;
foreach($fila as $k => $v){
	$Log['data']['campa'][$k]=$v;
}


if($Log['data']['indicador']['tipogeometria']!=$Log['data']['campa']['tipogeometria']){
	$Log['tx'][]='La capa del relevamiento elegido tiene un tipo de geometria diferente';
	$Log['mg'][]='La capa del relevamiento elegido tiene un tipo de geometria diferente';
	$Log['res']='err';
	terminar($Log);	
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
    	$DB.ref_rele_campos
    WHERE 
    	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
        zz_borrada = '0'
    AND 
    	id_p_ref_rele_campa = '".$_POST['id_rele_campa']."'
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

$Log['data']['campos_importar']=array(
	array('id'=>$_POST['campo_importar_1']),
	array('id'=>$_POST['campo_importar_2']),
	array('id'=>$_POST['campo_importar_3']),
	array('id'=>$_POST['campo_importar_4'])
);




if(!$Log['data']['campa']['id_p_ref_capasgeo']>0){
	$Log['mg'][]='el relevamiento importado no tiene capa asociada';
	$Log['res']='err';
	terminar($Log);
}



if($Log['data']['indicador']['tipogeometria']=='Polygon'){$campogeom='geom';}
if($Log['data']['indicador']['tipogeometria']=='LineString'){$campogeom='geom_line';}
if($Log['data']['indicador']['tipogeometria']=='Point'){$campogeom='geom_point';}


$query="
	SELECT  
		ref_rele_registros.*,
		ref_capasgeo_registros.$campogeom
    FROM    
    	$DB.ref_rele_registros    
    LEFT JOIN
		$DB.ref_capasgeo_registros 
		ON ref_capasgeo_registros.id = ref_rele_registros.id_p_ref_capas_registros 
    WHERE 
			ref_capasgeo_registros.zz_borrada = '0'
    AND
            ref_rele_registros.zz_borrado = '0'
    AND
            ref_rele_registros.zz_superado = '0'
    AND
			ref_rele_registros.zz_archivada='1'
    AND
            ref_rele_registros.id_p_ref_rele_campa = '".$_POST['id_rele_campa']."'
 	ORDER BY 
			ref_rele_registros.zz_archivada_fecha ASC   
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
if(pg_num_rows($Consulta)==0){
    $Log['tx'][]=utf8_encode('no se encontró registro para la geometría consultada');
	//$Log['mg'][]=utf8_encode('no se encontró el registro consultado:'.$_POST['idregistrocapa']);
    $Log['res']='exito';	
	$Log['data']=null;
    terminar($Log);		
}


$Log['data']['historicos']=array();


while($row=pg_fetch_assoc($Consulta)){
	$Log['data']['historicos'][$row['id']]['registro']=$row;
}



$query="
	SELECT 	
		ref_rele_registros_datos.id, 
		ref_rele_registros_datos.id_p_ref_rele_campa, 
		ref_rele_registros_datos.id_p_ref_rele_campos, 
		ref_rele_registros_datos.id_p_ref_rele_registros, 
		ref_rele_registros_datos.data_texto, 
		ref_rele_registros_datos.data_numero, 
		ref_rele_registros_datos.data_documento,col_texto1_dato
		
	FROM 
		$DB.ref_rele_registros_datos
	LEFT JOIN
		$DB.ref_rele_registros 
		ON ref_rele_registros_datos.id_p_ref_rele_registros = ref_rele_registros.id
		
	LEFT JOIN
		$DB.ref_rele_campa 
		ON ref_rele_registros_datos.id_p_ref_rele_campa = ref_rele_campa.id	
		
	WHERE
		ref_rele_registros_datos.zz_borrada='0'
	AND
		ref_rele_registros_datos.zz_superado='0'
			
	AND
		ref_rele_registros.zz_archivada='1'
	AND
		ref_rele_registros_datos.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'		
	AND (
		id_p_ref_rele_campos='".$Log['data']['campos_importar'][0]['id']."'
		OR
		id_p_ref_rele_campos='".$Log['data']['campos_importar'][1]['id']."'
		OR
		id_p_ref_rele_campos='".$Log['data']['campos_importar'][2]['id']."'
		OR
		id_p_ref_rele_campos='".$Log['data']['campos_importar'][3]['id']."'
		
		)
";
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
//$Log['mg'][]='encontramos datos para '.pg_num_rows($Consulta).' campos.';

    
while ($row=pg_fetch_assoc($Consulta)){
	
	if(!isset($Log['data']['historicos'][$row['id_p_ref_rele_registros']])){continue;}
	
	$Log['data']['historicos'][$row['id_p_ref_rele_registros']]['campos'][$row['id_p_ref_rele_campos']]=$row;
	
}


///////////////////////////////////
/// Alinear registros con periodos
///
///
foreach($Log['data']['historicos'] as $id_reg => $dat_hist){
	
	$fe=$dat_hist['registro']['zz_archivada_fecha'];
	$e=explode('-',$fe);
	
	
	if($Log['data']['indicador']['periodicidad']=='diario'){
		if(!isset($Log['data']['periodos'][$e[0]][intval($e[1])][intval($e[2])])){
			$Log['tx'][]=$e[0].'-'.intval($e[1]).'-'.intval($e[2]);
			continue;//este registro tiene una fecha ajena al rango propuesto para elindicador.		
		}
		$ano=intval($e[0]);
		$mes=intval($e[1]);
		$dia=intval($e[2]);		
	}
	
	if($Log['data']['indicador']['periodicidad']=='mensual'){		
		if(!isset($Log['data']['periodos'][$e[0]][intval($e[1])])){
			continue;//este registro tiene una fecha ajena al rango propuesto para elindicador.			
		}		
		$ano=intval($e[0]);
		$mes=intval($e[1]);
		$dia=array_keys($Log['data']['periodos'][$e[0]][intval($e[1])])[0];			
	}	
	
	if($Log['data']['indicador']['periodicidad']=='anual'){		
		if(!isset($Log['data']['periodos'][$e[0]])){
			continue;//este registro tiene una fecha ajena al rango propuesto para elindicador.			
		}		
		$ano=intval($e[0]);
		$mes=array_keys($Log['data']['periodos'][$ano])[0];	
		$dia=array_keys($Log['data']['periodos'][$ano][$mes])[0];			
	}	
	
	//echo $ano.' - '.$mes.' - '.$dia;
	//print_r($Log['data']['periodos'][$ano][$mes][$dia]['id_p_ref_capas_registros']);
	//print_r($dat_hist);
	//print_r($dat_hist['registro']['id_p_ref_capas_registros']);
	if(!isset(
		$Log['data']['periodos'][$ano][$mes][$dia]['id_p_ref_capas_registros'][$dat_hist['registro']['id_p_ref_capas_registros']])
	){
		$Log['data']['periodos'][$ano][$mes][$dia]['id_p_ref_capas_registros'][$dat_hist['registro']['id_p_ref_capas_registros']]=array();
		$Log['data']['periodos'][$ano][$mes][$dia]['id_p_ref_capas_registros'][$dat_hist['registro']['id_p_ref_capas_registros']]['rele_reg_ult']=$id_reg;
		$Log['data']['periodos'][$ano][$mes][$dia]['id_p_ref_capas_registros'][$dat_hist['registro']['id_p_ref_capas_registros']]['imporar_a_capa_registro']=''; //aquí irá el id de un nuevo registro producto de la copia de id_p_ref_capas_registros
		$Log['data']['periodos'][$ano][$mes][$dia]['id_p_ref_capas_registros'][$dat_hist['registro']['id_p_ref_capas_registros']]['importar_multi_reg']=array();
		$Log['data']['periodos'][$ano][$mes][$dia]['id_p_ref_capas_registros'][$dat_hist['registro']['id_p_ref_capas_registros']]['nombre_geometria']=$dat_hist['registro']['col_texto1_dato'];
		
	}
	$Log['data']['periodos'][$ano][$mes][$dia]['id_p_ref_capas_registros'][$dat_hist['registro']['id_p_ref_capas_registros']]['importar_multi_reg'][]=$id_reg;
		
}



///////////////////////////////////
/// Copiar geometrías
///
///
$id_p_ref_capas_registros_a_copia=array();
foreach($Log['data']['periodos'] as $ano => $da){
	foreach($da as $mes => $dm){	
		foreach($dm as $dia => $dd){
			foreach($dd['id_p_ref_capas_registros'] as $id_reg_capa => $data_import){
	
				if($Log['data']['indicador']['funcionalidad']=='geometriaExistente'){// en esta modalidad una misma geometría adotva diferentes valores a lo largo del tiempo
					if(isset($id_p_ref_capas_registros_a_copia[$id_reg_capa])){
						$Log['data']['periodos'][$ano][$mes][$dia]['id_p_ref_capas_registros'][$id_reg_capa]['imporar_a_capa_registro']=$id_p_ref_capas_registros_a_copia[$id_reg_capa];
						continue;
					}
				}else{
					// en esta modalidad una cada fecha tiene sus propias geometrías
				}
				
				
				
				$query="
					INSERT INTO 
						$DB.ref_capasgeo_registros(
							geom, texto1, texto2, texto3, texto4, texto5, numero1, numero2, numero3, numero4, numero5, geom_point, geom_line, 
							id_ref_capasgeo, 
							zz_auto_crea_usu, zz_auto_crea_fechau
						)
						SELECT
							geom, 
							'".$data_import['nombre_geometria']."', 
							
							texto2, texto3, texto4, texto5, numero1, numero2, numero3, numero4, numero5, geom_point, geom_line, 
							'".$Log['data']['indicador']['id_p_ref_capasgeo']."', 
							'".$idUsuario."', '".time()."'
						FROM	
							$DB.ref_capasgeo_registros
						WHERE
							id='".$id_reg_capa."'
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
				
				$id_p_ref_capas_registros_a_copia[$id_reg_capa]=$row['id'];
				
				$Log['data']['periodos'][$ano][$mes][$dia]['id_p_ref_capas_registros'][$id_reg_capa]['imporar_a_capa_registro']=$row['id'];
				
			}
		}
	}	
}




///////////////////////////////////
/// Copiar guardar registro para cada período para cada geometria.
///
///
foreach($Log['data']['periodos'] as $ano => $da){
	foreach($da as $mes => $dm){	
		foreach($dm as $dia => $dd){
			foreach($dd['id_p_ref_capas_registros'] as $id_reg_capa => $data_import){

				if(!isset($data_import['imporar_a_capa_registro'])){
					$Log['tx'][]=utf8_encode('no se encontró geometría nueva '.$id_reg_capa.'. registro saletado');
					continue;
				}
				if($data_import['imporar_a_capa_registro']==''){
					$Log['tx'][]=utf8_encode('no se encontró geometría nueva '.$id_reg_capa.'. registro saletado');
					continue;
				}
				
				
				$id_reg_geom_link=$data_import['imporar_a_capa_registro'];			
				
				$rele_reg_ult=$data_import['rele_reg_ult'];
				
				if(!isset($Log['data']['historicos'][$rele_reg_ult]['campos'])){
					$Log['tx'][]=utf8_encode('no se encontraron datos asociados a esta geometría nueva '.$id_reg_geom_link.'. registro saletado, registro:'.$rele_reg_ult);
					continue;
				}
				
				$insert_campos='';
				$insert_sets='';
				$id_campo_1=$Log['data']['campos_importar'][0]['id'];
				if($id_campo_1>0){
					$insert_campos.='col_numero1_dato, ';					
					$insert_sets.="'". $Log['data']['historicos'][$rele_reg_ult]['campos'][$id_campo_1]['data_numero']."', ";
				}
								
				$id_campo_2=$Log['data']['campos_importar'][1]['id'];
				if($id_campo_2>0){
					$insert_campos.='col_numero2_dato, ';					
					$insert_sets.= "'".$Log['data']['historicos'][$rele_reg_ult]['campos'][$id_campo_2]['data_numero']."', ";
				}
				
				$id_campo_3=$Log['data']['campos_importar'][2]['id'];
				if($id_campo_3>0){
					$insert_campos.='col_numero3_dato, ';					
					$insert_sets.= "'".$Log['data']['historicos'][$rele_reg_ult]['campos'][$id_campo_3]['data_numero']."', ";
				}
				
				$id_campo_4=$Log['data']['campos_importar'][3]['id'];
				if($id_campo_4>0){
					$insert_campos.='col_numero4_dato, ';					
					$insert_sets.= "'".$Log['data']['historicos'][$rele_reg_ult]['campos'][$id_campo_4]['data_numero']."', ";
				}
				
				$query="
					INSERT INTO
						$DB.ref_indicadores_valores(
							id_p_ref_indicadores_indicadores, 
							ano, mes, dia, 
							id_p_ref_capas_registros,
							col_texto1_dato,
							
							$insert_campos
							
							usu_autor, fechadecreacion
							
							
							
							)
						VALUES (
							'".$_POST['idindicador']."', 
							'".$ano."', '".$mes."', '".$dia."',
							'".$id_reg_geom_link."',
							'".$Log['data']['historicos'][$rele_reg_ult]['registro']['col_texto1_dato']."',
							
							$insert_sets
							
							'".$idUsuario."', '".date('Y-m-d')."'
							
						)			
				";
				
				$Log['tx'][]=$query;
						
				$Consulta = pg_query($ConecSIG, $query);
				if(pg_errormessage($ConecSIG)!=''){
					$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
					$Log['tx'][]='query: '.$query;
					$Log['mg'][]='error interno';
					$Log['res']='err';
					terminar($Log);	
				}
			}
		}
	}	
}



///////////////////////////////////
/// Cambiamos el nombre asignado a cada valor de indicador
///
///
$Log['data']['campos'][0]['nombre']='';
$query="

	UPDATE 
		$DB.ref_indicadores_indicadores
			SET 
				col_numero1_nom='".$Log['data']['campos'][$Log['data']['campos_importar'][0]['id']]['nombre']."',
				col_numero2_nom='".$Log['data']['campos'][$Log['data']['campos_importar'][1]['id']]['nombre']."',
				col_numero3_nom='".$Log['data']['campos'][$Log['data']['campos_importar'][2]['id']]['nombre']."',
				col_numero4_nom='".$Log['data']['campos'][$Log['data']['campos_importar'][3]['id']]['nombre']."'
				
		WHERE 
			id='".$_POST['idindicador']."'
";
	
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
	
	
	
	
	
	

$Log['res']="exito";
terminar($Log);

	
	
	
	
	
	
	
	
