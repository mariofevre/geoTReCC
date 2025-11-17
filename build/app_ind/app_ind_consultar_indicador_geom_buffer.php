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
			*,
			calc_buffer
			funcionalidad
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


if(!isset($_POST['ano'])){
	$Log['tx'][]='no fue enviada la variable ano';
	$Log['res']='err';
	terminar($Log);	
}


if($Log['data']['indicador']['calc_buffer']<0.1){
	$Log['tx'][]='no fue identificada una distacia buffer vàlida ( > 0.1)';
	$Log['res']='err';
	terminar($Log);	
}


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
        id = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
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


$extrawhere = "";
$extracampo = "";
$extravalor = "";
$Log['data']['periodo']['ano']=$_POST['ano'];
$Log['data']['periodo']['mes']='';
/*
if ($Log['data']['indicador']['periodicidad'] == 'mensual'){
	$extrawhere = "
		AND
			mes = '".$_POST['mes']."'
		";
	$extracampo = ' mes, ';
	$extravalor = "'".$_POST['mes']."', ";
	$Log['data']['periodo']['mes']=$_POST['mes'];
}*/

$extrawhere = "
		AND
			mes = '".$_POST['mes']."'
		AND
			dia = '".$_POST['dia']."'
		";
	$extracampo = ' mes, dia';
	$extravalor = "'".$_POST['mes']."', '".$_POST['dia']."'";
	$Log['data']['periodo']['mes']=$_POST['mes'];
	$Log['data']['periodo']['dia']=$_POST['dia'];

$query="
	SELECT  
                
        ST_AsText(
        	ST_Multi(ST_Union(
	            ST_Transform(
					ST_Buffer(
						ST_Transform(".$campogeo.",22175),				
						".$Log['data']['indicador']['calc_buffer'].", 
						'endcap=round join=round'
					),
					3857
				)
			))
		) as geotx,
        
        
        zz_borrada
        
    FROM
       
		(SELECT
			geom_point,
			geom_line,
			geom,
			ref_indicadores_valores.id, 
			ref_indicadores_valores.id_p_ref_indicadores_indicadores, 
			ref_indicadores_valores.ano, 
			ref_indicadores_valores.mes, 
			ref_indicadores_valores.dia, 
			ref_indicadores_valores.usu_autor, 
			ref_indicadores_valores.fechadecreacion, 
			ref_indicadores_valores.zz_superado, 
			ref_indicadores_valores.zz_borrado, 
			ref_indicadores_valores.id_p_ref_capas_registros, 
			ref_indicadores_valores.fechadesde, 
			ref_indicadores_valores.fechahasta,
			ref_capasgeo_registros.id_ref_capasgeo,
			ref_capasgeo_registros.zz_borrada
			
		FROM 
			$DB.ref_capasgeo_registros
		LEFT JOIN
			$DB.ref_indicadores_valores ON ref_indicadores_valores.id_p_ref_capas_registros = ref_capasgeo_registros.id
			
		WHERE
			ano = '".$_POST['ano']."'
			$extrawhere
	        AND		        
			zz_superado = '0'
	        AND
			zz_borrado = '0'
	        AND
			id_p_ref_indicadores_indicadores = '".$_POST['id']."'
			
		) as registros_periodo		

    WHERE
		id_ref_capasgeo = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
	AND
		zz_borrada='0'
	       
	GROUP BY 
		zz_borrada;
       
 ";
 

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$Log['data']['geom']=array();

$Buffertx='';
if (pg_num_rows($Consulta) <= 0){
    $Log['tx'][]= "No se encontraron registros para la capa id ".$Log['data']['indicador']['id_p_ref_capasgeo']." asociada al indicador ".$_POST['id'];
    $Log['data']['geom']=array();
    $Log['tx'][]=$query;
} else {
    $Log['tx'][]= "Consulta de capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
    while ($fila=pg_fetch_assoc($Consulta)){
    	$arr=$fila;
        $Log['data']['geom'][]=$fila;
        $Buffertx=$fila['geotx'];
    }
}
/*
$Log['res']='exito';
terminar($Log);
*/




if($Log['data']['indicador']['calc_superp']>0){
	//superpone el resultado con una cada de valores
																				
	
	$query="
		SELECT 
			id, autor, nombre, ic_p_est_02_marcoacademico, zz_borrada, descripcion, nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, zz_publicada, srid, sld, tipogeometria, zz_instrucciones
		FROM 
			$DB.ref_capasgeo
		WHERE 
			id = '".$Log['data']['indicador']['calc_superp']."'
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
	    $Log['tx'][]= "No se encuentra la capa solicitdad.";
	    $Log['data']=null;
	    $Log['res']='err';
	    terminar($Log);	
	}
	
	$campos='';
	$fila=pg_fetch_assoc($Consulta);
	$Log['data']['capa_superp']=$fila;
	foreach($fila as $k => $v){
		if($v==''){continue;}
		if(substr($k,0,8)=='nom_col_'){
			$campo=str_replace('nom_col_', '', $k);
			$campo=str_replace('text', 'texto', $campo);
			$campo=str_replace('num', 'numero', $campo);
			$campos.=' '.$campo.', ';
		}
	}   
	
	$campogeom='geom';
	
	if(
		$fila['tipogeometria']=='Point'
	){
		$campogeom='geom_point';
	}
	
	if(
		$fila['tipogeometria']=='Line'
	){
		$campogeom='geom_line';
	}
																						
																																											
	$query="
		SELECT
			SUM (numero1) AS superp_max_numero1,
			SUM (numero2) AS superp_max_numero2,
			SUM (numero3) AS superp_max_numero3,
			SUM (numero4) AS superp_max_numero4,
			SUM (numero5) AS superp_max_numero5			
		FROM
		 	$DB.ref_capasgeo_registros
    	WHERE 
  			id_ref_capasgeo = '".$Log['data']['indicador']['calc_superp']."'
	";	
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}
	$Log['data']['geom_superp_max']=pg_fetch_assoc($Consulta);
	
	

																																											
	$query="
		SELECT
			id,
	        ".$campos."
	        ST_Area(capa.geom) as area_orig,
			ST_AsText(ST_Intersection(buffer.geom, capa.geom)) as geom_intersec,		
			ST_Area(ST_Intersection(buffer.geom, capa.geom)) as area_intersec
	
		FROM
			(
				SELECT      
			        
			        	ST_Multi(ST_Union(
				            ST_Transform(
								ST_Buffer(
									ST_Transform(".$campogeo.",22175),				
									".$Log['data']['indicador']['calc_buffer'].", 
									'endcap=round join=round'
								),
								3857
							)
						)) as geom,
			        
			        
			        zz_borrada
			        
			    FROM
			       
					(SELECT
						geom_point,
						geom_line,
						geom,
						ref_indicadores_valores.id, 
						ref_indicadores_valores.id_p_ref_indicadores_indicadores, 
						ref_indicadores_valores.ano, 
						ref_indicadores_valores.mes, 
						ref_indicadores_valores.dia, 
						ref_indicadores_valores.usu_autor, 
						ref_indicadores_valores.fechadecreacion, 
						ref_indicadores_valores.zz_superado, 
						ref_indicadores_valores.zz_borrado, 
						ref_indicadores_valores.id_p_ref_capas_registros, 
						ref_indicadores_valores.fechadesde, 
						ref_indicadores_valores.fechahasta,
						ref_capasgeo_registros.id_ref_capasgeo,
						ref_capasgeo_registros.zz_borrada,
						ref_capasgeo_registros.numero1,
						ref_capasgeo_registros.numero2,
						ref_capasgeo_registros.numero3,
						ref_capasgeo_registros.numero4
						
					FROM 
						$DB.ref_capasgeo_registros
					LEFT JOIN
						$DB.ref_indicadores_valores ON ref_indicadores_valores.id_p_ref_capas_registros = ref_capasgeo_registros.id
						
					WHERE
						ano = '".$_POST['ano']."'
						$extrawhere
				        AND		        
						zz_superado = '0'
				        AND
						zz_borrado = '0'
				        AND
						id_p_ref_indicadores_indicadores = '".$_POST['id']."'
						
					) as registros_periodo		
			
			    WHERE
					id_ref_capasgeo = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
				AND
					zz_borrada='0'
				       
				GROUP BY 
					zz_borrada
					
			) as buffer
		LEFT JOIN	
			$DB.ref_capasgeo_registros as capa ON '1'='1'
	    WHERE 
	  		id_ref_capasgeo = '".$Log['data']['indicador']['calc_superp']."'
	
	";
	
	
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}
	 $Log['tx'][]='query: '.$query;
	$Log['data']['geom_superp']=array();
	
	
	$campo_sum='numero1';
	
	$Suma=0;
	
	$Sumas=Array(
		'numero1'=>0,
		'numero2'=>0,
		'numero3'=>0,
		'numero4'=>0
	);
	
	if (pg_num_rows($Consulta) <= 0){
	    $Log['tx'][]= "No se encontraron registros para la capa id ".$Log['data']['indicador']['id_p_ref_capasgeo']." asociada al indicador ".$_POST['id'];
	    $Log['data']['geom_superp']=array();
	} else {
	    $Log['tx'][]= "Consulta de capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
	    
	    
	    while ($fila=pg_fetch_assoc($Consulta)){
	        if($fila['geom_intersec'] == 'GEOMETRYCOLLECTION EMPTY'){
	        	continue;        		
	        }
	        $Log['data']['geom_superp'][$fila['id']]=$fila;
	        if($fila['area_orig']>0){// averiguar porque a veces da 0
				
				$Suma += ($fila[$campo_sum]/$fila['area_orig'])*$fila['area_intersec'];
				
				$fac=(1/$fila['area_orig'])*$fila['area_intersec'];
				
				if(isset($fila['numero1'])){
					$Sumas['numero1']+=$fila['numero1']*$fac;
				}
				if(isset($fila['numero2'])){
					$Sumas['numero2']+=$fila['numero2']*$fac;
				}
				
				if(isset($fila['numero3'])){
					$n=$fila['numero3']*$fac;
					$Sumas['numero3']+=$n;
				}
				
				if(isset($fila['numero4'])){
					$n=$fila['numero4']*$fac;
					$Sumas['numero4']+=$n;
				}
				
				
			}
	    }
	}
	$Log['data']['intersec_sum']=$Suma;																			
	$Log['data']['intersec_sumas']=$Sumas;	

	$query="
	SELECT 
		id
		FROM 
			$DB.ref_indicadores_resumen
		WHERE
			id_p_ref_indicadores_indicadores ='".$_POST['id']."'
		AND
			ano = '".$_POST['ano']."'
		$extrawhere
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
	   		
		if($Buffertx!=''){
			$Bufferquery="ST_GeomFromText('".$Buffertx."',3857)";
		}else{
			$Bufferquery="null";
		}
	   $query="
			INSERT INTO 
				$DB.ref_indicadores_resumen( 
					geom_buffer, 
					superp_sum, 
					id_p_ref_indicadores_indicadores, 
					".$extracampo."
					ano,
					superp_max_numero1
				)
				VALUES (
					".$Bufferquery.",
					'".$Suma."',
					'".$_POST['id']."',
					".$extravalor."
					'".$_POST['ano']."',
					'".$Log['data']['geom_superp_max']['superp_max_numero1']."'
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
	}else{
		if($Buffertx!=''){
			$Bufferquery="ST_GeomFromText('".$Buffertx."',3857)";
		}else{
			$Bufferquery="null";
		}
	   $query="
			UPDATE 
				$DB.ref_indicadores_resumen
				
			SET( 
				geom_buffer, 
				superp_sum,
				superp_max_numero1
			)
			= (
				".$Bufferquery.",
				'".$Suma."',
				'".$Log['data']['geom_superp_max']['superp_max_numero1']."'
			)
			WHERE
				id_p_ref_indicadores_indicadores = '".$_POST['id']."'
				".$extrawhere."
			AND 
				ano = '".$_POST['ano']."'
		
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
}





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
            id = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
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
$Log['data']['capa']=$Capa;


$query="

	SELECT 
		id, 
		id_p_ref_indicadores_indicadores, 
		ano, 
		mes, 
		usu_autor, 
		fechadecreacion, 
		zz_superado, 
		zz_borrado, 
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
		ano = '".$_POST['ano']."'
        AND
		zz_superado = '0'
        AND
		zz_borrado = '0'
        AND
		id_p_ref_indicadores_indicadores = '".$_POST['id']."'
";
/*
if ($Log['data']['indicador']['periodicidad'] == 'mensual'){
    $query=$query."
        AND
		mes = '".$_POST['mes']."'";
}
*/
    $query.="
        AND
		mes = '".$_POST['mes']."'
		AND
		dia = '".$_POST['dia']."'
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
    if(!isset($Log['data']['geom'][$fila['id_p_ref_capas_registros']])){continue;}
    $Log['data']['geom'][$fila['id_p_ref_capas_registros']]['valores'][]=$fila;
}


$query="
	SELECT 
		ref_indicadores_valores.id,
		ref_indicadores_valores.id_p_ref_capas_registros,
	    ref_indicadores_valores.ano,
	    ref_indicadores_valores.mes,
		
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_texto1_nom = '' OR ref_indicadores_indicadores.col_texto1_nom is null) 
			AND (ref_indicadores_valores.col_texto1_dato is null OR ref_indicadores_valores.col_texto1_dato = '')  
			THEN 0 ELSE 1 END AS stat_col_texto1_nom,
		
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_texto2_nom = '' OR ref_indicadores_indicadores.col_texto2_nom is null ) 
			AND (ref_indicadores_valores.col_texto2_dato is null OR ref_indicadores_valores.col_texto2_dato = '')  
			THEN 0 ELSE 1 END AS stat_col_texto2_nom,

		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_texto3_nom = '' OR ref_indicadores_indicadores.col_texto3_nom is null ) 
			AND (ref_indicadores_valores.col_texto3_dato is null OR ref_indicadores_valores.col_texto3_dato = '')  
			THEN 0 ELSE 1 END AS stat_col_texto3_nom,
			
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_texto4_nom = '' OR ref_indicadores_indicadores.col_texto4_nom is null) 
			AND (ref_indicadores_valores.col_texto4_dato is null OR ref_indicadores_valores.col_texto4_dato = '')  
			THEN 0 ELSE 1 END AS stat_col_texto4_nom,
		
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_texto5_nom = '' OR ref_indicadores_indicadores.col_texto5_nom is null) 
			AND (ref_indicadores_valores.col_texto5_dato is null OR ref_indicadores_valores.col_texto5_dato = '')  
			THEN 0 ELSE 1 END AS stat_col_texto5_nom,


		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_numero1_nom = '' OR ref_indicadores_indicadores.col_numero1_nom is null) 
			AND (ref_indicadores_valores.col_numero1_dato is null )  
			THEN 0 ELSE 1 END AS stat_col_numero1_nom,
		
					
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_numero2_nom = '' OR ref_indicadores_indicadores.col_numero2_nom is null) 
			AND (ref_indicadores_valores.col_numero2_dato is null )  
			THEN 0 ELSE 1 END AS stat_col_numero2_nom,
		
		
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_numero3_nom = '' OR ref_indicadores_indicadores.col_numero3_nom is null) 
			AND (ref_indicadores_valores.col_numero3_dato is null )  
			THEN 0 ELSE 1 END AS stat_col_numero3_nom,
		
		
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_numero4_nom = '' OR ref_indicadores_indicadores.col_numero4_nom is null) 
			AND (ref_indicadores_valores.col_numero4_dato is null )  
			THEN 0 ELSE 1 END AS stat_col_numero4_nom,
		
		
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_numero5_nom = '' OR ref_indicadores_indicadores.col_numero5_nom is null) 
			AND (ref_indicadores_valores.col_numero5_dato is null )  
			THEN 0 ELSE 1 END AS stat_col_numero5_nom
		
	FROM
		$DB.ref_indicadores_indicadores
		
	LEFT JOIN
		$DB.ref_capasgeo_registros 
        ON 
                $DB.ref_indicadores_indicadores.id_p_ref_capasgeo = $DB.ref_capasgeo_registros.id_ref_capasgeo
		AND 
        		$DB.ref_capasgeo_registros.zz_borrada='0'	
			
	LEFT JOIN
		$DB.ref_indicadores_valores
        ON 
                $DB.ref_indicadores_valores.id_p_ref_indicadores_indicadores = $DB.ref_indicadores_indicadores.id
        AND
                $DB.ref_indicadores_valores.id_p_ref_capas_registros = $DB.ref_capasgeo_registros.id
        AND 
        		$DB.ref_capasgeo_registros.zz_borrada='0'
		
	WHERE
		$DB.ref_indicadores_valores.ano = '".$_POST['ano']."'
        AND
		$DB.ref_indicadores_valores.zz_superado = '0'
        AND
		$DB.ref_indicadores_valores.zz_borrado = '0'
        AND
		$DB.ref_indicadores_indicadores.id = '".$_POST['id']."'

";

/*
if ($Log['data']['indicador']['periodicidad'] == 'mensual'){
    $query=$query."
        AND
		$DB.ref_indicadores_valores.mes = '".$_POST['mes']."'";
}
*/
    $query.="
        AND
		$DB.ref_indicadores_valores.mes = '".$_POST['mes']."'
		AND
		$DB.ref_indicadores_valores.dia = '".$_POST['dia']."'
		
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
    $Log['tx'][]= "Consulta de valores asociados a la capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo']." -estadocarga: sin carga";
} else {
    $Log['tx'][]= "Consulta de valores asociados a la capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
    
    while ($fila=pg_fetch_assoc($Consulta)){
    	if(!isset($Log['data']['geom'][$fila['id_p_ref_capas_registros']])){continue;}
    	
        $Log['data']['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='listo';
        $Log['data']['geom'][$fila['id_p_ref_capas_registros']]['controles']=$fila;		

        foreach($fila as $k => $v){
            if (strpos($k, 'stat_col_') === false){continue;}

            if($v=='0'){
                $Log['data']['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='incompleto';
            }
        }
    }
}
	
if($Log['data']['indicador']['funcionalidad']=='nuevaGeometria'){
	
	foreach($Log['data']['geom'] as $idgeom => $data){	
		if($data['estadocarga']=='sin carga'){
			unset($Log['data']['geom'][$idgeom]);
		}
	}	
}
$Log['res']="exito";
terminar($Log);
