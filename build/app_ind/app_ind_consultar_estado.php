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
$Log['acc']=array();
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

if(!isset($_POST['zz_publicada'])){
	$Log['tx'][]='no fue enviada la variable zz_publicada';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['id'])){
	$Log['tx'][]='no fue enviada la variable id';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_SESSION[$CU])){
	$Log['tx'][]='sesión caduca';
	$Log['acc'][]='login';
	terminar($Log);	
}

$idUsuario = $_SESSION[$CU]["usuario"]['id'];

$query="SELECT  
			*
        FROM    
				$DB.ref_indicadores_indicadores
        WHERE 
                zz_borrada = '0'
        AND
                zz_publicada = '".$_POST['zz_publicada']."'
        AND
                id = '".$_POST['id']."'
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
    $Log['tx'][]= "No se encontro el indicador id ".$_POST['id'];
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de indicador valido";
    $fila = pg_fetch_assoc($Consulta);
    $Log['data']['indicador']=$fila;
}


if ($Log['data']['indicador']['fechahasta'] < $Log['data']['indicador']['fechadesde']){
    $Log['tx'][]='la fechadesde es mayor a la fechahasta en el indicador con id '.$Log['data']['indicador']['id'];
    $Log['res']='err';
    terminar($Log);	
}


$valores=array();


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
			
		}else{
			
			if ($mes > 12){$mes = 1;}	
			while ($mes <= 12) {
				
				$Log['data']['periodos'][$ano][$mes]= array();
				
				if ($periodicidad == 'mensual'){
					
					$Log['data']['periodos'][$ano][$mes][$dia] = array();
					$Log['data']['periodos'][$ano][$mes][$dia]['estado']='sin carga';
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
	
	$query="
		SELECT 
			nombre, descripcion, 
			nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, 
			nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5,
			srid, sld, tipogeometria, 
			modo_defecto, wms_layer, zz_aux_ind, zz_aux_rele, modo_publica, 
			tipo_fuente, link_capa, link_capa_campo_local, link_capa_campo_externo, 
			fecha_ano, fecha_mes, 
			fecha_dia, cod_col_text1, cod_col_text2, cod_col_text3, cod_col_text4, cod_col_text5, 
			cod_col_num1, cod_col_num2, cod_col_num3, cod_col_num4, cod_col_num5, 
			zz_auto_borra_usu, zz_auto_borra_fechau, zz_cache_extent
			
		FROM 
			$DB.ref_capasgeo
		WHERE id = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
	";
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	
	$Log['data']['indicador']['capa']=pg_fetch_assoc($Consulta);
	
	if($Log['data']['indicador']['capa']['tipogeometria']=='Polygon'){
		$campogeo='geom';
	}elseif($Log['data']['indicador']['capa']['tipogeometria']=='Point'){
		$campogeo='geom_point';
	}if($Log['data']['indicador']['capa']['tipogeometria']=='LineString'){
		$campogeo='geom_line';
	}

	//TODO  traer geometrías de capas linkeadas
	
	$query="
		SELECT  
			id,
			ST_AsText(".$campogeo.") as geotx,
			texto1, texto2,  texto3,  texto4, texto5, 
			numero1,  numero2,  numero3,  numero4,  numero5
		FROM    
			$DB.ref_capasgeo_registros
		WHERE 
			id_ref_capasgeo = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
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
			$Log['tx'][]= "No se encontraron registros para la capa id ".$Log['data']['indicador']['id_p_ref_capasgeo']." asociada al indicador ".$_POST['id'];
		//$Log['data']=null;
	} else {
		//$Log['tx'][]= "Consulta de capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
		while ($fila=pg_fetch_assoc($Consulta)){	
			$Log['data']['geometrias'][$fila['id']]=$fila;
		}
	}
			
	$query="
			SELECT 
				id, 
				ano, mes, dia, 
				usu_autor, fechadecreacion, 
				col_texto1_dato, col_texto2_dato, col_texto3_dato, col_texto4_dato, col_texto5_dato, 
				col_numero1_dato, col_numero2_dato, col_numero3_dato, col_numero4_dato, col_numero5_dato, 
				id_p_ref_capas_registros, 
				fechadesde, 
				fechahasta
			FROM 
				$DB.ref_indicadores_valores
			WHERE
				zz_superado = '0'
			AND
				zz_borrado = '0'
			AND
				id_p_ref_indicadores_indicadores = '".$_POST['id']."'
	";
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	
	$valores=array();

	while ($fila=pg_fetch_assoc($Consulta)){
		if(!isset($Log['data']['geometrias'][$fila['id_p_ref_capas_registros']])){continue;}	
		if(!isset($Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']])){continue;}	
		$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]['ind_valores']=$fila;			
		$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='completo';
		$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['estado']='completo';
		
		
		foreach($fila as $k => $v){
			if (strpos($k, 'col_') === false){continue;}
			$nom=str_replace('_dato','',$k).'_nom';
			if(
				$Log['data']['indicador'][$nom]!=''	&& $v === null
			){					
				$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='incompleto';
				$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['estado']='incompleto';					
			}				
			$valores[$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]=$fila;
		}
	}	
}	
	
		
		

		
		
		
		
	
	
	
	
/*	
	if ($periodicidad == 'anual'){
	
	
			
		
	    $mes = date('n',strtotime($Log['data']['indicador']['fechadesde']));
		$dia = date('j',strtotime($Log['data']['indicador']['fechadesde']));
	    foreach ($years as $ano) {
	        $Log['data']['periodos'][$ano] = array();
	        $Log['data']['periodos'][$ano][$mes]= array();
	        $Log['data']['periodos'][$ano][$mes][$dia] = array();
			$Log['data']['periodos'][$ano][$mes][$dia]['estado']='sin carga';			
	    }	
					
		$query="
			SELECT 
				nombre, descripcion, 
				nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, 
				nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5,
				srid, sld, tipogeometria, 
				modo_defecto, wms_layer, zz_aux_ind, zz_aux_rele, modo_publica, 
				tipo_fuente, link_capa, link_capa_campo_local, link_capa_campo_externo, 
				fecha_ano, fecha_mes, 
				fecha_dia, cod_col_text1, cod_col_text2, cod_col_text3, cod_col_text4, cod_col_text5, 
				cod_col_num1, cod_col_num2, cod_col_num3, cod_col_num4, cod_col_num5, 
				zz_auto_borra_usu, zz_auto_borra_fechau, zz_cache_extent
				
			FROM 
				$DB.ref_capasgeo
			WHERE id = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
		";
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		$Log['data']['indicador']['capa']=pg_fetch_assoc($Consulta);
		
		if($Log['data']['indicador']['capa']['tipogeometria']=='Polygon'){
			$campogeo='geom';
		}elseif($Log['data']['indicador']['capa']['tipogeometria']=='Point'){
			$campogeo='geom_point';
		}if($Log['data']['indicador']['capa']['tipogeometria']=='LineString'){
			$campogeo='geom_line';
		}
		
		//TODO  traer geometrías de capas linkeadas
		
		$query="
			SELECT  
				id,
				ST_AsText(".$campogeo.") as geotx,
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
			FROM    
				$DB.ref_capasgeo_registros
			WHERE 
				id_ref_capasgeo = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
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
				$Log['tx'][]= "No se encontraron registros para la capa id ".$Log['data']['indicador']['id_p_ref_capasgeo']." asociada al indicador ".$_POST['id'];
			//$Log['data']=null;
		} else {
			//$Log['tx'][]= "Consulta de capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
			while ($fila=pg_fetch_assoc($Consulta)){	
				$Log['data']['geometrias'][$fila['id']]=$fila;
			}
		}	
		
		$query="SELECT 
						id, 
						ano, 
						mes,
						dia, 
						usu_autor, 
						fechadecreacion, 
						col_texto1_dato, 
						col_texto2_dato, 
						col_texto3_dato, 
						col_texto4_dato, 
						col_texto5_dato, 
						col_numero1_dato, 
						col_numero2_dato, 
						col_numero3_dato, 
						col_numero4_dato, 
						col_numero5_dato, 
						id_p_ref_capas_registros, 
						fechadesde, 
						fechahasta

				FROM 
						$DB.ref_indicadores_valores
				WHERE
						zz_superado = '0'
				AND
						zz_borrado = '0'
				AND
						id_p_ref_indicadores_indicadores = '".$_POST['id']."'
		";
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		$valores=array();

		while ($fila=pg_fetch_assoc($Consulta)){
			if(!isset($Log['data']['geometrias'][$fila['id_p_ref_capas_registros']])){continue;}	
			if(!isset($Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']])){continue;}	
			$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]['ind_valores']=$fila;			
			$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='completo';
			$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['estado']='completo';
					
			foreach($fila as $k => $v){
				if (strpos($k, 'col_') === false){continue;}
				
				$nom=str_replace('_dato','',$k).'_nom';
				if(
					$Log['data']['indicador'][$nom]!=''
					&&
					$v === null
				){
					$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='incompleto';
					$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['estado']='incompleto';
				}
				
				$valores[$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]=$fila;
				
				
			}
		}	
}



if ($periodicidad == 'mensual'){
	

	    $mes = date('n',strtotime($Log['data']['indicador']['fechadesde']));
		$dia = date('j',strtotime($Log['data']['indicador']['fechadesde']));
		
	    foreach ($years as $ano) {
	        $Log['data']['periodos'][$ano] = array();
	        
	        if ($mes > 12){
	            $mes = 1;
	        }
			
	        while ($mes <= 12) {
				
				$Log['data']['periodos'][$ano][$mes]= array();
				$Log['data']['periodos'][$ano][$mes][$dia] = array();
				$Log['data']['periodos'][$ano][$mes][$dia]['estado']='sin carga';		
				
				$diamax=diasenelmesano($ano.'-'.$mes.'-1');
				
				if (				
					$ano == date('Y',strtotime($Log['data']['indicador']['fechahasta']))
					&& 
					$mes > date('n',strtotime($Log['data']['indicador']['fechahasta']))
				){
					break;
				}
				
	            $mes++;
	        }
	    }
	    
	    
					
					
					
		$query="
			SELECT 
				nombre, descripcion, 
				nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, 
				nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5,
				srid, sld, tipogeometria, 
				modo_defecto, wms_layer, zz_aux_ind, zz_aux_rele, modo_publica, 
				tipo_fuente, link_capa, link_capa_campo_local, link_capa_campo_externo, 
				fecha_ano, fecha_mes, 
				fecha_dia, cod_col_text1, cod_col_text2, cod_col_text3, cod_col_text4, cod_col_text5, 
				cod_col_num1, cod_col_num2, cod_col_num3, cod_col_num4, cod_col_num5, 
				zz_auto_borra_usu, zz_auto_borra_fechau, zz_cache_extent
				
			FROM 
				$DB.ref_capasgeo
			WHERE id = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
		";
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		$Log['data']['indicador']['capa']=pg_fetch_assoc($Consulta);
		
		if($Log['data']['indicador']['capa']['tipogeometria']=='Polygon'){
			$campogeo='geom';
		}elseif($Log['data']['indicador']['capa']['tipogeometria']=='Point'){
			$campogeo='geom_point';
		}if($Log['data']['indicador']['capa']['tipogeometria']=='LineString'){
			$campogeo='geom_line';
		}
		
		
		
		//TODO  traer geometrías de capas linkeadas
		
		
		
		$query="
			SELECT  
				id,
				ST_AsText(".$campogeo.") as geotx,
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
			FROM    
				$DB.ref_capasgeo_registros
			WHERE 
				id_ref_capasgeo = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
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
				$Log['tx'][]= "No se encontraron registros para la capa id ".$Log['data']['indicador']['id_p_ref_capasgeo']." asociada al indicador ".$_POST['id'];
			//$Log['data']=null;
		} else {
			//$Log['tx'][]= "Consulta de capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
			while ($fila=pg_fetch_assoc($Consulta)){	
				$Log['data']['geometrias'][$fila['id']]=$fila;
			}
		}
		
		
		$query="SELECT 
						id, 
						ano, 
						mes,
						dia, 
						usu_autor, 
						fechadecreacion, 
						col_texto1_dato, 
						col_texto2_dato, 
						col_texto3_dato, 
						col_texto4_dato, 
						col_texto5_dato, 
						col_numero1_dato, 
						col_numero2_dato, 
						col_numero3_dato, 
						col_numero4_dato, 
						col_numero5_dato, 
						id_p_ref_capas_registros, 
						fechadesde, 
						fechahasta

				FROM 
						$DB.ref_indicadores_valores
				WHERE
						zz_superado = '0'
				AND
						zz_borrado = '0'
				AND
						id_p_ref_indicadores_indicadores = '".$_POST['id']."'
		";
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		$valores=array();

		while ($fila=pg_fetch_assoc($Consulta)){
			if(!isset($Log['data']['geometrias'][$fila['id_p_ref_capas_registros']])){continue;}	
			if(!isset($Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']])){continue;}	
			$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]['ind_valores']=$fila;			
			$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='completo';
			$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['estado']='completo';
			
			
			foreach($fila as $k => $v){
				if (strpos($k, 'col_') === false){continue;}
				
				$nom=str_replace('_dato','',$k).'_nom';
				
				
				if(
					$Log['data']['indicador'][$nom]!=''
					&&
					$v === null
				){
					
					$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='incompleto';
					$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['estado']='incompleto';
					
				}
				
				$valores[$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]=$fila;
				
				
			}
		}
		
    
}


if ($periodicidad == 'diario'){

	
		// Genera listado de períodos
		
	   
	    foreach ($years as $ano) {
	        $Log['data']['periodos'][$ano] = array();
	        
	        if ($mes > 12){
	            $mes = 1;
	        }
			
	        while ($mes <= 12) {
				
				$Log['data']['periodos'][$ano][$mes]= array();
				
				$diamax=diasenelmesano($ano.'-'.$mes.'-1');
				
				if (				
					$ano == date('Y',strtotime($Log['data']['indicador']['fechahasta']))
					&& 
					$mes > date('n',strtotime($Log['data']['indicador']['fechahasta']))
				){
					break;
				}
				
				while($dia <= $diamax){
					
					if(	
						$ano == date('Y',strtotime($Log['data']['indicador']['fechahasta']))
						&& 
						$mes == date('n',strtotime($Log['data']['indicador']['fechahasta']))
						&&
						$dia > date('j',strtotime($Log['data']['indicador']['fechahasta']))
						     
					){
						break 2;
					}
					
					$Log['data']['periodos'][$ano][$mes][$dia] = array();
					$Log['data']['periodos'][$ano][$mes][$dia]['estado']='sin carga';
					
					$dia++;
				}
				$dia=1;	
	            $mes++;
	        }
	    }
	    
	    
					
					
					
		$query="
			SELECT 
				nombre, descripcion, 
				nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, 
				nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5,
				srid, sld, tipogeometria, 
				modo_defecto, wms_layer, zz_aux_ind, zz_aux_rele, modo_publica, 
				tipo_fuente, link_capa, link_capa_campo_local, link_capa_campo_externo, 
				fecha_ano, fecha_mes, 
				fecha_dia, cod_col_text1, cod_col_text2, cod_col_text3, cod_col_text4, cod_col_text5, 
				cod_col_num1, cod_col_num2, cod_col_num3, cod_col_num4, cod_col_num5, 
				zz_auto_borra_usu, zz_auto_borra_fechau, zz_cache_extent
				
			FROM 
				$DB.ref_capasgeo
			WHERE id = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
		";
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		$Log['data']['indicador']['capa']=pg_fetch_assoc($Consulta);
		
		if($Log['data']['indicador']['capa']['tipogeometria']=='Polygon'){
			$campogeo='geom';
		}elseif($Log['data']['indicador']['capa']['tipogeometria']=='Point'){
			$campogeo='geom_point';
		}if($Log['data']['indicador']['capa']['tipogeometria']=='LineString'){
			$campogeo='geom_line';
		}
		
		
		
		//TODO  traer geometrías de capas linkeadas
		
		
		
		$query="
			SELECT  
				id,
				ST_AsText(".$campogeo.") as geotx,
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
			FROM    
				$DB.ref_capasgeo_registros
			WHERE 
				id_ref_capasgeo = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
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
				$Log['tx'][]= "No se encontraron registros para la capa id ".$Log['data']['indicador']['id_p_ref_capasgeo']." asociada al indicador ".$_POST['id'];
			//$Log['data']=null;
		} else {
			//$Log['tx'][]= "Consulta de capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
			while ($fila=pg_fetch_assoc($Consulta)){	
				$Log['data']['geometrias'][$fila['id']]=$fila;
			}
		}
		
		
		$query="SELECT 
						id, 
						ano, 
						mes,
						dia, 
						usu_autor, 
						fechadecreacion, 
						col_texto1_dato, 
						col_texto2_dato, 
						col_texto3_dato, 
						col_texto4_dato, 
						col_texto5_dato, 
						col_numero1_dato, 
						col_numero2_dato, 
						col_numero3_dato, 
						col_numero4_dato, 
						col_numero5_dato, 
						id_p_ref_capas_registros, 
						fechadesde, 
						fechahasta

				FROM 
						$DB.ref_indicadores_valores
				WHERE
						zz_superado = '0'
				AND
						zz_borrado = '0'
				AND
						id_p_ref_indicadores_indicadores = '".$_POST['id']."'
		";
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		$valores=array();

		while ($fila=pg_fetch_assoc($Consulta)){
			if(!isset($Log['data']['geometrias'][$fila['id_p_ref_capas_registros']])){continue;}	
			if(!isset($Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']])){continue;}	
			$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]['ind_valores']=$fila;			
			$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='completo';
			$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['estado']='completo';
			
			
			foreach($fila as $k => $v){
				if (strpos($k, 'col_') === false){continue;}
				
				$nom=str_replace('_dato','',$k).'_nom';
				
				
				if(
					$Log['data']['indicador'][$nom]!=''
					&&
					$v === null
				){
					
					$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='incompleto';
					$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['estado']='incompleto';
					
				}
				
				$valores[$fila['ano']][$fila['mes']][$fila['dia']]['geom'][$fila['id_p_ref_capas_registros']]=$fila;
				
				
			}
		}

	}
}
*/


$Log['data']['valores']=$valores;
$amplitud=$Log['data']['indicador']['representar_val_max']-$Log['data']['indicador']['representar_val_min'];
	
foreach($valores as $ano => $av){
	foreach($av as $mes => $mv){
		foreach($mv as $dia => $dv){
			
			$acc=array(
				'col_numero1_dato' => 0, 
				'col_numero2_dato' => 0, 
				'col_numero3_dato' => 0, 
				'col_numero4_dato' => 0, 
				'col_numero5_dato' => 0
			);
			
			$cant=array(
				'col_numero1_dato' => 0, 
				'col_numero2_dato' => 0, 
				'col_numero3_dato' => 0, 
				'col_numero4_dato' => 0, 
				'col_numero5_dato' => 0
			);


			foreach($dv['geom'] as $idgeom => $datageom){
				foreach($datageom as $k => $v){
					if(substr($k,0,10)=='col_numero'){
						$cant[$k]++;
						$acc[$k]+=$v;
					}
				}
			}
			
			foreach($acc as $nom => $val_acc){
				if($cant[$nom]>0){
					$prom = $val_acc/$cant[$nom];
				}
				$sum = $val_acc;
				if($amplitud==0){$amplitud=0.5;}
				$porc=($prom-$Log['data']['indicador']['representar_val_min'])/$amplitud;
				
				$Log['data']['periodos'][$ano][$mes][$dia]['representa'][$nom]['valora']=$porc;
				$Log['data']['periodos'][$ano][$mes][$dia]['representa'][$nom]['suma']=$sum;
				$Log['data']['periodos'][$ano][$mes][$dia]['representa'][$nom]['media']=$prom;
			}
		}
	}
}

						
								    

$query="

SELECT 
	id, geom_buffer, 
	superp_sum, id_p_ref_indicadores_indicadores, ano, mes,dia, superp_max_numero1,
	sum_numero1, sum_numero2, sum_numero3, sum_numero4, sum_numero5, 
	prom_numero1, prom_numero2, prom_numero3, prom_numero4, prom_numero5
	

	FROM $DB.ref_indicadores_resumen
	WHERE
	id_p_ref_indicadores_indicadores='".$_POST['id']."'
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
	

		if(!isset($Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']])){continue;}
		$Log['data']['periodos'][$fila['ano']][$fila['mes']][$fila['dia']]['resumen']=$fila;
	
}



$Log['res']="exito";
terminar($Log);
