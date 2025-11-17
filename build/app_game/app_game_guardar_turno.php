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

if(!isset($_POST['idSesion'])){
	$Log['tx'][]='no fue enviada la variable idSesion';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['tipogeometria'])){
	$Log['tx'][]='no fue enviada la variable tipogeometria';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['arr_wkt'])){
	//turno vacio.
	$_POST['arr_wkt']=array();
}

$minacc=0;
if(isset($_POST['nivelPermiso'])){
    $minacc=$_POST['nivelPermiso'];
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

if($Acc<$minacc){
    $Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificación de este marco académico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
    $Log['tx'][]=print_r($Usu,true);
    $Log['res']='err';
    terminar($Log);
}

$idUsuario = $_SESSION[$CU]["usuario"]['id'];


$MC=microtime(TRUE);


//datos generales de la sesion
$query="
	SELECT 
		id, id_p_indicadores_indicadores, ic_p_est_02_marcoacademico, 
		nombre, presentacion, costounitario, limiteunitarioporturno,
		zz_borrada, 
		ref_game_sesiones.*
	FROM 
		$DB.ref_game_sesiones
	WHERE 
        zz_borrada = '0'
    AND
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	AND
		id = '".$_POST['idSesion']."'
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
    $Log['tx'][]= "No se encontraron sesiones existentes.";
    $Log['tx'][]= "Query: ".$query;
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de sesiones existentes";
	$Log['data']['sesion']=pg_fetch_assoc($Consulta);
	$Log['data']['sesion']['presentacionBR']=nl2br($Log['data']['sesion']['presentacion']);
}


$query="SELECT  
			id, funcionalidad, id_p_ref_capasgeo, ic_p_est_02_marcoacademico, periodicidad, fechadesde, fechahasta, 
			calc_buffer, calc_superp, calc_zonificacion, calc_superp_campo
		FROM    
			$DB.ref_indicadores_indicadores		
        WHERE 
                zz_borrada = '0'
        AND
                id = '".$Log['data']['sesion']['id_p_indicadores_indicadores']."'
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
    $Log['tx'][]= "No se encontro el indicador id ".$Log['data']['sesion']['id_p_indicadores_indicadores'];
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de indicador valido";
    $fila = pg_fetch_assoc($Consulta);
    $Log['data']['indicador']=$fila;
}





if($_POST['partida']=='nueva'){
	//se registra la existencia de una nueva partida y este es el turno 1	
	$Turno=1;
	
	$query="
	INSERT INTO $DB.ref_game_partidas(
		fecha, id_p_ref_game_sesiones)
	VALUES (
		'".$HOY."', '".$_POST['idSesion']."')
	RETURNING 
		id
	";
	
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}
	$row= pg_fetch_assoc($Consulta);
	$Log['data']['nid']['partida']=$row['id'];
	$_POST['partida']=$Log['data']['nid']['partida'];
	
}else{	
	$Log['data']['nid']['partida']=$_POST['partida'];
}

$query="
	SELECT 
		id, resultados, numero
	FROM 
		$DB.ref_game_turnos
	WHERE
		id_p_ref_game_sesiones='".$_POST['idSesion']."'
	AND
		id_p_ref_game_partidas ='".$_POST['partida']."'
	ORDER BY numero asc
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$turnV=0;
while($row= pg_fetch_assoc($Consulta)){
	$turnV=$row['numero'];
}

$Turno=$turnV+1;

$query="
	INSERT INTO $DB.ref_game_turnos(
		id_p_ref_game_sesiones, id_p_ref_game_partidas, numero
	)
	VALUES (
		'".$_POST['idSesion']."', '".$_POST['partida']."', '".$Turno."'
	)
	RETURNING 
		id
	";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
$row= pg_fetch_assoc($Consulta);
$Log['data']['nid']['turno']=$row['id'];
$Log['data']['turno']=$Turno;
	
if($_POST['tipogeometria']=='LineString'){
	$campogeo='geom_line';//TODO ajustar según la configuración de la capa vinculada;
}elseif($_POST['tipogeometria']=='Point'){
	$campogeo='geom_point';//TODO ajustar según la configuración de la capa vinculada;
}elseif($_POST['tipogeometria']=='Polygon'){
	$campogeo='geom';//TODO ajustar según la configuración de la capa vinculada;
}
foreach($_POST['arr_wkt'] as $wkt){
	
	
	$query="
		INSERT INTO 
			$DB.ref_game_geometrias(
				id_p_ref_game_sesiones, id_p_ref_game_partidas, id_p_ref_game_turnos, 
				".$campogeo."
			)
			VALUES (
				'".$_POST['idSesion']."', '".$_POST['partida']."', '".$Log['data']['nid']['turno']."',
				ST_GeomFromText('".$wkt."',3857)
			)
			RETURNING 
				id
		";
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}
	$row= pg_fetch_assoc($Consulta);
	
}




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
        ST_AsText(
        	ST_Multi(ST_Union(
	           ".$campogeo."
			))
		) as geotx,
        zz_borrada
        
    FROM
       
		(SELECT
			id_p_ref_game_sesiones, id_p_ref_game_partidas, id_p_ref_game_turnos, geom_poly, geom_line, geom_point, zz_borrada
		FROM 
			$DB.ref_game_geometrias
			
		WHERE
				id_p_ref_game_sesiones='".$_POST['idSesion']."'
			AND
				id_p_ref_game_partidas ='".$_POST['partida']."'
			AND
				id_p_ref_game_turnos <='".$Log['data']['nid']['turno']."'
			AND
				zz_borrada='0'
		) as geometrias_del_turno		
   
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

$Log['data']['geom']['buffer']=pg_fetch_assoc($Consulta);


			
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
	
	
	
$nMC=microtime(TRUE);
$dif=$nMC-$MC;
$Log['tx'][]=utf8_encode('fase 1 terminada: guardar geomentrias y tomar información de configuración. ('.$dif.')');
$MC=$nMC;
	
																
																																										
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


	
$nMC=microtime(TRUE);
$dif=$nMC-$MC;
$Log['tx'][]=utf8_encode('fase 2 terminada: consultar sumas de geomentrias de la capa de superposición. ('.$dif.')');
$MC=$nMC;																					

																																										
$query="
	SELECT
		id,
        ".$campos."
        ST_Area(capa.geom) as area_orig,
		ST_AsText(ST_Intersection(buffer.geom, capa.geom)) as geotx,		
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
				$DB.ref_game_geometrias
				
			WHERE
					id_p_ref_game_sesiones='".$_POST['idSesion']."'
				AND
					id_p_ref_game_partidas ='".$_POST['partida']."'
				AND
					id_p_ref_game_turnos <='".$Log['data']['nid']['turno']."'
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
 
 $query="
SELECT 
	 id, ".$campos." ST_Area(capa.geom) as area_orig,
	 ST_AsText(ST_Intersection(buffer.geom, capa.geom)) as geotx, 
	 ST_Area(ST_Intersection(buffer.geom, capa.geom)) as area_intersec
FROM 
	(SELECT 
			ST_Multi(ST_Union(
				ST_Transform(ST_Buffer(
					ST_Transform(".$campogeo.",22175), ".$Log['data']['indicador']['calc_buffer'].", 'endcap=round join=round' 
				), 3857)
			)) as geom, 
			zz_borrada
		FROM 
			$DB.ref_game_geometrias 
		WHERE 
			id_p_ref_game_sesiones='".$_POST['idSesion']."'
		   	AND id_p_ref_game_partidas ='".$_POST['partida']."'
		   	AND id_p_ref_game_turnos <='".$Log['data']['nid']['turno']."'
		   	AND zz_borrada='0'
		GROUP BY zz_borrada
	)as buffer
					  
	LEFT JOIN 
		(SELECT 
		 ref_capasgeo_registros.id,
		 ".$campos."
		 ref_capasgeo_registros.id_ref_capasgeo,
		 ST_Intersects(buffer1.geom, ref_capasgeo_registros.geom) as inters,
		ref_capasgeo_registros.geom
		FROM 
			$DB.ref_capasgeo_registros 
		LEFT JOIN 
		 	(SELECT 
				 	ST_Multi(ST_Union(
				 		ST_Transform(ST_Buffer( 
				 			ST_Transform(".$campogeo.",22175), ".$Log['data']['indicador']['calc_buffer'].", 'endcap=round join=round' 
						), 3857)
					)) as geom,
					zz_borrada
				FROM 
					$DB.ref_game_geometrias 
				WHERE 
					id_p_ref_game_sesiones='".$_POST['idSesion']."'
				   	AND id_p_ref_game_partidas ='".$_POST['partida']."'
				   	AND id_p_ref_game_turnos <='".$Log['data']['nid']['turno']."'
				   	AND zz_borrada='0'
				GROUP BY zz_borrada 
			) as buffer1
		ON 
			ST_Intersects(buffer1.geom, ref_capasgeo_registros.geom)
		WHERE 
			ref_capasgeo_registros.id_ref_capasgeo = '".$Log['data']['indicador']['calc_superp']."'
		AND 
			ST_Intersects(buffer1.geom, ref_capasgeo_registros.geom)
		order by inters desc
	)as capa ON '1'='1' WHERE id_ref_capasgeo = '".$Log['data']['indicador']['calc_superp']."' and  ST_Area(ST_Intersection(buffer.geom, capa.geom)) > 0
";
$Log['tx'][]=utf8_encode('query: '.$query);
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]=utf8_encode('query: '.$query);
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$nMC=microtime(TRUE);
$dif=$nMC-$MC;
$Log['tx'][]=utf8_encode('fase 3 terminada: consultar superposición de geomentrias. ('.$dif.')');
$MC=$nMC;																					
					
					
$Log['data']['geom_superp']=array();


$campo_sum=$Log['data']['indicador']['calc_superp_campo'];
if($campo_sum==''){
	$campo_sum='numero1';	
}



$Suma=0;
if (pg_num_rows($Consulta) <= 0){
    $Log['tx'][]= "No se encontraron registros para las geometrias creadas";
      $Log['tx'][]='query: '.$query;
    $Log['data']['geom_superp']=array();
} else {
    $Log['tx'][]= "Consulta de capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
    
    
    while ($fila=pg_fetch_assoc($Consulta)){
        if($fila['geotx'] == 'GEOMETRYCOLLECTION EMPTY'){
        	continue;        		
        }
        $Log['data']['geom_superp'][$fila['id']]=$fila;
        
        $Suma += ($fila[$campo_sum]/$fila['area_orig'])*$fila['area_intersec'];
    }
}
$Log['data']['intersec_sum']=$Suma;																			


$porc=round(
		(100*$Suma/$Log['data']['geom_superp_max']['superp_max_numero1']),2
	);


$nMC=microtime(TRUE);
$dif=$nMC-$MC;
$Log['tx'][]=utf8_encode('fase 4 terminada: procesar resultados de superposición. ('.$dif.')');
$MC=$nMC;	

$query="
		UPDATE
			$DB.ref_game_partidas
		SET
			puntaje='".$Suma."',
			puntaje_porc='".$porc."'
		WHERE
			id='".$_POST['partida']."'
		AND
			id_p_ref_game_sesiones='".$_POST['idSesion']."'
	";
	
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);
	}

/*
$query="
	UPDATE
		$DB.ref_game_sesiones
	SET 
		id_p_indicadores_indicadores='".$_POST['id_p_indicadores_indicadores']."',
		nombre='".$_POST['nombre']."',
		presentacion='".$_POST['presentacion']."',
		costounitario='".$_POST['costounitario']."',
		limiteunitarioporturno='".$_POST['limiteunitarioporturno']."',		
		modored='".$_POST['modored']."',
		turnos='".$_POST['turnos']."'
	WHERE
		id='".$_POST['idSesion']."'
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
*/

$Log['res']="exito";
terminar($Log);
