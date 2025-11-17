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
	'idco' =>'mayor0',
	'avance' =>'set'
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


if($_POST['idco']!='12'){
	$Log['tx'][]="variable necesaria indefinida ($v)";
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
		geogec.ref_proc_componentes_precedentes as prec
		
	LEFT JOIN 	
		geogec.ref_proc_instancias_componentes as in_co
			ON
			prec.id_p_ref_proc_componente_precedente = in_co.id_p_ref_proc_componentes
			
	LEFT JOIN 
		geogec.ref_proc_instancias as ins
			ON
			in_co.id_p_ref_proc_instancias = ins.id
			
	LEFT JOIN 
		geogec.ref_capasgeo as _cap
			ON
			_cap.id = in_co.id_ref AND in_co.tabla='ref_capasgeo'
			
    WHERE 
		prec.id_p_ref_proc_componente_posterior='".$_POST['idco']."'
	AND
		ins.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
    AND
		in_co.id_p_ref_proc_instancias='".$_POST['idinst']."'
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
	$Log['data']['Componentes'][$fila['idco']]=$fila;	
}



//idco 1: centros poblados. / localización de centros poblados. estos cenrán utilizados como unidad de análisis
	//ref_capasgeo
	$tab_1_cen=$Log['data']['Componentes'][1]['tabla'];
	$reg_1_cen=$Log['data']['Componentes'][1]['id_ref'];

//idco 2: radios viviendas según ruralidad.
	//ref_capasgeo
	$tab_2_rad=$Log['data']['Componentes'][2]['tabla'];
	$reg_2_rad=$Log['data']['Componentes'][2]['id_ref'];

//idco 6: departamentos . text3: campo con codigo departamento-provincia 5( digitos)
	//ref_capasgeo
	$tab_6_dep=$Log['data']['Componentes'][6]['tabla'];
	$reg_6_dep=$Log['data']['Componentes'][6]['id_ref'];
	
	
	
$query="
	SELECT 
		cen.id, 
		ST_AsText(cen.geom_point), 
		cen.texto1, 
		cen.texto2, 
		dep.texto3 as texto3_depto

	FROM	
		(SELECT * FROM	geogec.ref_capasgeo_registros WHERE id_ref_capasgeo = '".$reg_2_rad."' AND zz_borrada='0' AND (numero1 + numero2)>=numero3) as cen	
	LEFT JOIN
		(SELECT * FROM	geogec.ref_capasgeo_registros WHERE id_ref_capasgeo = '".$reg_1_cen."' AND zz_borrada='0') as cen		
	JOIN 		
		(SELECT * FROM	geogec.ref_capasgeo_registros WHERE id_ref_capasgeo = '".$reg_6_dep."' AND zz_borrada='0') as dep
		ON ST_Intersects(dep.geom, cen.geom_point)	
";



foreach($Log['data']['Componentes'] as $idco => $dat){
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
		$Log['tablas'][$fila['id']]=$fila;
		foreach($fila as $k => $v){
			if($v==''){continue;}
			if(substr($k,0,8)=='nom_col_'){
				$campo=str_replace('nom_col_', '', $k);
				$campo=str_replace('text', 'texto', $campo);
				$campo=str_replace('num', 'numero', $campo);
				//$campos.=' r.'.$campo.', ';
				$Log['tablas'][$fila['id']]['campos'][$k]=$campo;
			}
		}   	
		
		


		$tipogeom=$fila['tipogeometria'];
		if(
			$tipogeom=='Polygon'
			||
			$tipogeom=='LineString'
			||
			$tipogeom=='Point'
			){
				
			$Log['tablas'][$fila['id']]['fuentegeometria']='local';
			$Log['tablas'][$fila['id']]['linkselect']='';
		}elseif(
			$tipogeom=='Tabla'
		){
			$fuentegeometria='sin geometria';
			if(
				$fila['link_capa']!=''
				&&
				$fila['link_capa']!='-1'
				&&
				$fila['link_capa_campo_local']!=''
				&&
				$fila['link_capa_campo_externo']!=''	
			){
				
				$Log['tablas'][$fila['id']]['fuentegeometria']='externa_capa';
				$query="
				
					SELECT 
						c.id, c.autor, c.nombre, 
						c.ic_p_est_02_marcoacademico,
						c.tipogeometria, c.zz_instrucciones
						
					FROM 
						geogec.ref_capasgeo as c
					
					WHERE 
						id = '".$fila['link_capa']."'
				";
				$Consulta = pg_query($ConecSIG, $query);
				if(pg_errormessage($ConecSIG)!=''){
					$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
					$Log['tx'][]='query: '.$query;
					$Log['mg'][]='error interno';
					$Log['res']='err';
					terminar($Log);	
				}	
				$link=pg_fetch_assoc($Consulta);	
				$Log['tablas'][$fila['id']]['tipogeometria']=$link['tipogeometria'];
				
				
				if(
					$link['tipogeometria']=='Point'
				){
					$link['tipogeometria']='geom_point';
					$mag=1;
				}

				if(
					$link['tipogeometria']=='Line'
				){
					$campogeom='geom_line';
					$mag=4;
				}

				if(
					$link['tipogeometria']=='Polygon'
				){
					$campogeom='geom';
					$mag=16;//valor para pnderar preliminarmente el peso de la información
				}

				
				$Log['tablas'][$fila['id']]['selectquery']="
					select 
					
						r.texto1,
						r.texto2,
						r.texto3,
						r.texto4,
						r.texto5,
						r.numero1,
						r.numero2,
						r.numero3,
						r.numero4,
						r.numero5,
						lr.".$campogeom." 
					from 
						(select * FROM geogec.ref_capasgeo_registros where id_ref_capasgeo='".$fila['id']."') as r
					LEFT JOIN 
						geogec.ref_capasgeo_registros as lr 
							ON lr.".$Log['tablas'][$fila['id']]['campos'][$fila['link_capa_campo_externo']]." = r.".$Log['tablas'][$fila['id']]['campos'][$fila['link_capa_campo_local']]." 
							AND lr.id_ref_capasgeo = '".$fila['link_capa']."'
				";
			}elseif(
				$fila['link_capa']=='-1'
				&&
				$fila['link_capa_campo_local']!=''
				&&
				$fila['link_capa_campo_externo']!=''	
			){
				$Log['tablas'][$fila['id']]['fuentegeometria']='externa_est01';
				$Log['tablas'][$fila['id']]['tipogeometria']='Polygon';
				
				
			}

		}else{
			$Log['tx'][]= "No se encontraron registros para esta capa.";
			$Log['tx'][]= "Esta capa aún no cuenta con un tipo de geometría definida.";    
			$Log['res']="exito";
			terminar($Log);
		}
	}
}




$regsporpaso=10;



$query="SELECT
	count(*)
	
	FROM	
		(".$Log['tablas'][$reg_2_rad]['selectquery']."
		WHERE r.id_ref_capasgeo = '".$reg_2_rad."' AND r.zz_borrada='0' AND (r.numero1 + r.numero2)>=r.numero3
		
	) as ra
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
$totreg=$fila['count'];
$Log['data']['tot_reg']=$totreg;
if($totreg < $regsporpaso){
	$Log['mg'][]='error en la cantidad esperada del registro';
	$Log['res']='err';
	terminar($Log);	
}

$Log['data']['totalpasos']=ceil($totreg/$regsporpaso);


if($_POST['avance']==0){

	$query = "
		INSERT INTO geogec.ref_capasgeo
			(
				tipogeometria, autor, 
				nombre,
				
				ic_p_est_02_marcoacademico, srid, zz_borrada, zz_publicada,
				
				nom_col_text1, nom_col_text2, nom_col_text3,  nom_col_text4, nom_col_text5,
				nom_col_num1
			)        
		VALUES
			(
				'Polygon', '".$idUsuario."', 
				'Radios por centro',
				
				'".$_POST['codMarco']."', 3857, 0, 1,
				'codigo radio', 'codigo centro', 'codigo departamento', 'nombre centro', 'nombre departamento',
				'cantidad de viviendas urb. o rur. agr.'
			)
		RETURNING ID
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

	$Log['tx'][]= "Creada capa id: ".$fila['id'];
	$Log['data']['nid']=$fila['id'];
	$Log['data']['encapa']=$fila['id'];
	
	$query = "
		INSERT INTO 
			geogec.ref_proc_instancias_componentes(
				ic_p_est_02_marcoacademico, 
				id_p_ref_proc_componentes, id_p_ref_proc_instancias, 
				tabla, id_ref, estado, 
				campo_t_a, campo_t_b, campo_t_c, campo_t_d, campo_t_e, 
				campo_n_a
			)
		VALUES (
			'".$_POST['codMarco']."', 
			'".$_POST['idco']."', '".$_POST['idinst']."',
			'ref_capasgeo',  '".$Log['data']['nid']."', 'cálculo parcial',
			'codigo radio', 'codigo centro', 'codigo departamento', 'nombre centro', 'nombre departamento',
			'cantidad de viviendas urb. o rur. agr.'
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
	pg_fetch_assoc($Consulta);


	$offset=0;
	

}else{
	
	if(!isset($_POST['encapa'])){
		$Log['mg'][]='error falta variable encapa';
		$Log['res']='err';
		terminar($Log);	
	}
		
	$Log['data']['encapa']=$_POST['encapa'];

	$offset=$_POST['avance']*$regsporpaso;
	
}




$query="
		insert into geogec.ref_capasgeo_registros(
			geom,
			id_ref_capasgeo,
			texto1,
			texto2,
			texto3,
			texto4,
			numero1
		)
		SELECT 
		
			rad.geom,
			'".$Log['data']['encapa']."' as idcapa,
			rad.texto1 as rad_cod,
			cen.texto3 as cen_cod,
			rad.cod_prov_dpto,
			cen.nom_centro,
			(rad.numero1 + rad.numero2) as viviendas
			
			
		FROM 
			(SELECT 
				ra.texto1,				
			 	CASE substring(texto1,0,3) WHEN '02' THEN substring(texto1,0,3)
				ELSE substring(texto1,0,6)
				END as cod_prov_dpto,
				ra.texto2,
				ra.texto3,
				ra.texto4,
				ra.texto5,
				ra.numero1,
				ra.numero2,
				ra.numero3,
				ra.numero4,
				ra.numero5,
				ra.geom
				FROM	
					(".$Log['tablas'][$reg_2_rad]['selectquery']."
					WHERE r.id_ref_capasgeo = '".$reg_2_rad."' AND r.zz_borrada='0' AND (r.numero1 + r.numero2)>=r.numero3
					
				) as ra
				OFFSET ".$offset."
				LIMIT ".$regsporpaso."
				
			) as rad
			
		JOIN LATERAL(
			
			SELECT 	
				texto1 as nom_centro,
				texto2,
				texto3,
				cod_dpto,
				   
				texto4,
				texto5,
				numero1,
				numero2,
				numero3,
				numero4,
				numero5,
				geom_point
			FROM 
				(
				SELECT 
					c.*,
					CASE substring(texto3,0,3) WHEN '02' THEN substring(texto3,0,3)
					ELSE substring(texto3,0,6)
					END as cod_dpto
					
					FROM	
						geogec.ref_capasgeo_registros as c
				
				WHERE c.id_ref_capasgeo = '".$reg_1_cen."' AND c.zz_borrada='0'
				
				
				) as cen
				WHERE rad.cod_prov_dpto = cod_dpto
					
				ORDER BY rad.geom <-> cen.geom_point
		  LIMIT 1
		) AS cen
		ON true
		
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

while ($fila=pg_fetch_assoc($Consulta)){	
	$Log['data']['regs'][]=$fila;	
}



$estado='cálculo parcial';
if($Log['data']['avance']==$Log['data']['totalpasos']){
	$estado='cálculo completo';
}
$query = "	
	UPDATE 
		geogec.ref_proc_instancias_componentes
		SET 
			estado='".$estado."', 
			calculo_pasos_completos='".$Log['data']['avance']."'
	WHERE 
		ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
		AND
		id_p_ref_proc_componentes='".$_POST['idco']."'
		AND
		id_p_ref_proc_instancias='".$_POST['idinst']."'
		
";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
pg_fetch_assoc($Consulta);



if($Log['data']['avance']==$Log['data']['totalpasos']){
	$estado='cálculo completo';
	$Log['data']['estado']='completo';
}else{
	$Log['data']['avance']=$_POST['avance']+1;	
	$Log['data']['estado']='parcial';
}






$Log['res']='exito';
terminar($Log);
