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

$Log=array();
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




if(isset($_POST['actualizar_id'])){$Log['data']['actualizar_id']=$_POST['actualizar_id'];}
if(isset($_POST['actualizar_nivel'])){$Log['data']['actualizar_nivel']=$_POST['actualizar_nivel'];}
if(isset($_POST['actualizar_modo'])){$Log['data']['actualizar_modo']=$_POST['actualizar_modo'];}


	$query="
		SELECT 
			ref_plae_categorias.id,
		    ref_plae_categorias.nivel,
		    ref_plae_categorias.nombre,
		    ref_plae_categorias.orden,
		    ref_plae_categorias.ic_p_est_02_marcoacademico
		FROM $DB.ref_plae_categorias
		Where ic_p_est_02_marcoacademico='".$_POST['codMarco']."' 
		ORDER by orden
	";		
	$Consulta=	pg_query($ConecSIG, $query);;
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	    $Log['res']='err';
	    terminar($Log);
	}
	$CAT['PLAn1']=array();
	$CAT['PLAn2']=array();
	$CAT['PLAn3']=array();
	while($row = pg_fetch_assoc($Consulta)){
		foreach($row as $k => $v){
			$CAT[$row['nivel']][$row['id']][$k]=$v;
		}	
	}	
	

	$query="
		SELECT 
			ref_plae_categorias_valor.id,
		    ref_plae_categorias_valor.id_p_plae_categorias ,
		    ref_plae_categorias_valor.id_p_plae_nivel1,
		    ref_plae_categorias_valor.id_p_plae_nivel2,
		    ref_plae_categorias_valor.id_p_plae_nivel3,
		    ref_plae_categorias_valor.valor,
		    ref_plae_categorias_valor.ic_p_est_02_marcoacademico
		FROM $DB.ref_plae_categorias_valor
		Where ic_p_est_02_marcoacademico='".$_POST['codMarco']."' 
	";		
	$Consulta=	pg_query($ConecSIG, $query);;
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	    $Log['res']='err';
	    terminar($Log);
	}
	while($row = pg_fetch_assoc($Consulta)){
		if($row['id_p_plae_nivel1']>0){
			$PNCatVals['PLAn1'][$row['id_p_plae_nivel1']][$row['id_p_plae_categorias ']]=$row['valor'];	
		}	
		if($row['id_p_plae_nivel2']>0){
			$PNCatVals['PLAn2'][$row['id_p_plae_nivel2']][$row['id_p_plae_categorias ']]=$row['valor'];	
		}	
		if($row['id_p_plae_nivel3']>0){
			$PNCatVals['PLAn3'][$row['id_p_plae_nivel3']][$row['id_p_plae_categorias ']]=$row['valor'];	
		}	
	}	

	$query="
		SELECT 
		 	ref_plae_estados.id,
		    ref_plae_estados.nombre,
		    ref_plae_estados.desde,
		    ref_plae_estados.ic_p_est_02_marcoacademico,
		    ref_plae_estados.id_p_plae_nivel1,
		    ref_plae_estados.id_p_plae_nivel2,
		    ref_plae_estados.id_p_plae_nivel3
		FROM 
			$DB.ref_plae_estados
		Where 
			ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
		order by desde desc 
	";		
	$Consulta=	pg_query($ConecSIG, $query);;
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	    $Log['res']='err';
	    terminar($Log);
	}
	
	$PNestados['PN1']=array();
	$PNestados['PN2']=array();
	$PNestados['PN3']=array();
	while($row = pg_fetch_assoc($Consulta)){
		if($row['id_p_plae_nivel1']>0){
			
			$arr=array();
			foreach($row as $k => $v){
				$arr[$k]=utf8_encode($v);	
			}	
			$PNestados['PN1'][$row['id_p_plae_nivel1']][]=$arr;
		}	
		
		if($row['id_p_plae_nivel2']>0){
			$arr=array();
			foreach($row as $k => $v){
				$arr[$k]=utf8_encode($v);	
			}	
			$PNestados['PN2'][$row['id_p_plae_nivel2']][]=$arr;
		
		}			
		if($row['id_p_plae_nivel3']>0){
			
			$arr=array();
			foreach($row as $k => $v){
				$arr[$k]=utf8_encode($v);	
			}	
			$PNestados['PN3'][$row['id_p_plae_nivel3']][]=$arr;
		}	
	}

	
$tabla='ref_plae_nivel3';
		$mascampos='c.id_p_plae_nivel2,';
		$query="
		SELECT 
			c.id, c.nombre, c.numero, c.co_color, c.descripcion,
			$mascampos			
			c.id_p_rele_campa, c.id_p_rele_campo, c.filtro_rele_valor 
			FROM 
			$DB.$tabla as c
			WHERE 
				c.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
				AND c.zz_borrada='0'
			ORDER BY 
			c.numero asc	
		";
	
	$Consulta=	pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	    $Log['res']='err';
	    terminar($Log);
	}
	$PN3=array();
	while($row = pg_fetch_assoc($Consulta)){
		/*
		foreach($row as $k => $v){
			$PN3[$row['id']][$k]=utf8_encode($v);	
		}*/
		$PN3[$row['id']]=$row;		
		
		$b['id']=$row['id'];
		$plas2[$row['id_p_plae_nivel2']][]=$b;
		
		$PN3[$row['id']]['categorias']=Array();
		$PN3[$row['id']]['geometrias']=Array();
		$PN3[$row['id']]['indicadores']=Array();
		
		if(isset($PNCatVals['PLAn3'][$row['id']])){
			$PN3[$row['id']]['categorias']=$PNCatVals['PLAn3'][$row['id']];					
		}
		if(!isset($PNestados['PN3'][$row['id']])){$PNestados['PN3'][$row['id']]=array();}
		$PN3[$row['id']]['estados']=$PNestados['PN3'][$row['id']];	
		$PN3[$row['id']]['documentos']=array();
		$Log['data']['PN3_orden'][]=$row['id'];
	}



		$tabla='ref_plae_nivel2';
		$mascampos='c.id_p_plae_nivel1,' ;
		$query="
		SELECT 
			c.id, c.nombre, c.numero, c.co_color, c.descripcion,
			$mascampos			
			c.id_p_rele_campa, c.id_p_rele_campo, c.filtro_rele_valor
			FROM 
			$DB.$tabla as c
			 
			WHERE 
				c.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
				AND c.zz_borrada='0'
			ORDER BY 
			c.numero asc
				
		";			
	
	$Consulta=	pg_query($ConecSIG, $query);;
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	    $Log['res']='err';
	    terminar($Log);
	}
	$PN2=array();
	while($row = pg_fetch_assoc($Consulta)){
		/*
		foreach($row as $k => $v){
			$PN2[$row['id']][$k]=utf8_encode($v);	
		}*/
		$PN2[$row['id']]=$row;	
		
		if(!isset($plas2[$row['id']])){
			$plas2[$row['id']]=array();
		}	
		
		$b['id']=$row['id'];
		$b['PLAn3']['componentes']=$plas2[$row['id']];
		$plas1[$row['id_p_plae_nivel1']][]=$b;
			
		$PN2[$row['id']]['categorias']=Array();
		$PN2[$row['id']]['geometrias']=Array();
		$PN2[$row['id']]['indicadores']=Array();
		
		if(isset($PNCatVals['PLAn2'][$row['id']])){
			$PN2[$row['id']]['categorias']=$PNCatVals['PLAn2'][$row['id']];					
		}	
		if(!isset($PNestados['PN2'][$row['id']])){$PNestados['PN2'][$row['id']]=array();}
		$PN2[$row['id']]['estados']=$PNestados['PN2'][$row['id']];			
		$PN2[$row['id']]['documentos']=array();
		$Log['data']['PN2_orden'][]=$row['id'];
	}	



		$tabla='ref_plae_nivel1';
		$mascampos='' ;
		
		$query="
		SELECT 
			c.id, c.nombre, c.numero, c.co_color, c.descripcion,
			$mascampos
			c.id_p_rele_campa, c.id_p_rele_campo, c.filtro_rele_valor
			FROM 
			$DB.$tabla as c
			WHERE 
				c.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
				AND c.zz_borrada='0'
			ORDER BY 
			c.numero asc
				
		";			

	
	$Consulta=	pg_query($ConecSIG, $query);;
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	    $Log['res']='err';
	    terminar($Log);
	}
	$PLA=array();
	$PLA['PLAn1']=array();
	$PN1=array();
	while($row = pg_fetch_assoc($Consulta)){
		/*
		foreach($row as $k => $v){
			$PN1[$row['id']][$k]=utf8_encode($v);	
		}*/
		$PN1[$row['id']]=$row;	
		
		if(!isset($plas1[$row['id']])){
			$plas1[$row['id']]=array();
		}	
		
		$b['id']=$row['id'];
		$b['PLAn2']['componentes']=$plas1[$row['id']];		
		$PLA['PLAn1']['componentes'][]=$b;
		
		
		$PN1[$row['id']]['categorias']=Array();
		$PN1[$row['id']]['geometrias']=Array();
		$PN1[$row['id']]['indicadores']=Array();
		
		
		if(isset($PNCatVals['PLAn1'][$row['id']])){
			$PN1[$row['id']]['categorias']=$PNCatVals['PLAn1'][$row['id']];
		}	
		if(!isset($PNestados['PN1'][$row['id']])){$PNestados['PN1'][$row['id']]=array();}
		$PN1[$row['id']]['estados']=$PNestados['PN1'][$row['id']];					
		$PN1[$row['id']]['documentos']=array();	
		$Log['data']['PN1_orden'][]=$row['id'];				
	}	

	$Log['data']['PLA']=$PLA;
	$Log['data']['CAT']=$CAT;//categorías para cada nivel	
	
	


	$query="
		SELECT 
			id, nombre, descripcion
		FROM $DB.ref_plae_planes
		Where ic_p_est_02_marcoacademico='".$_POST['codMarco']."' 
		
	";		
	$Consulta=	pg_query($ConecSIG, $query);;
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	    $Log['res']='err';
	    terminar($Log);
	}
	if(pg_num_rows($Consulta)==0){
		$query="
			INSERT INTO
			 	$DB.ref_plae_planes(
			 		nombre, 
			 		descripcion, 
			 		ic_p_est_02_marcoacademico
			 	)
				values(
					'- sin nombre -',
					'- sin descripcion -',
					'".$_POST['codMarco']."' 
				)	
		";		
		$Consulta=	pg_query($ConecSIG, $query);;
		if(pg_errormessage($ConecSIG)!=''){
		    $Log['tx'][]='error al consultar columnas';
		    $Log['tx'][]=utf8_encode($query);
		    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
		    $Log['res']='err';
		    terminar($Log);
		}
		$query="
			SELECT 
				id, nombre, encabezado, descripcion
			FROM $DB.ref_plae_planes
			Where ic_p_est_02_marcoacademico='".$_POST['codMarco']."' 
			
		";		
		$Consulta=	pg_query($ConecSIG, $query);;
		if(pg_errormessage($ConecSIG)!=''){
		    $Log['tx'][]='error al consultar columnas';
		    $Log['tx'][]=utf8_encode($query);
		    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
		    $Log['res']='err';
		    terminar($Log);
		}		
	}
	
	$CAT['PLAn1']=array();
	$CAT['PLAn2']=array();
	$CAT['PLAn3']=array();
	while($row = pg_fetch_assoc($Consulta)){
		foreach($row as $k => $v){
			$Log['data']['PLA']['general'][$k]=$v;	
		}	
	}	
	
	
	$query="
		SELECT 
			l.id_p_ref_plae_nivel1, 
			l.id_p_ref_plae_nivel2, 
			l.id_p_ref_plae_nivel3, 
			l.id as linkid,
			d.id as docid,
			d.nombre,
			d.archivo,
			d.descripcion
		FROM 
			geogec.ref_plae_link_ref_01_documentos as l
			LEFT JOIN
			geogec.ref_01_documentos as d ON d.id = l.id_p_ref_01_documentos
			
		WHERE l.zz_borrada='0' 
		AND l.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."' 
		AND d.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."' 
		
	";
	$Consulta=	pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
		$Log['res']='err';
		terminar($Log);
	}	
	while($row = pg_fetch_assoc($Consulta)){
		$Log['tx'][]=$row['id_p_ref_plae_nivel1']. ' - ' .$row['id_p_ref_plae_nivel2']. ' - ' .$row['id_p_ref_plae_nivel3'];
		if($row['id_p_ref_plae_nivel1']!=null){
			if(!isset($PN1[$row['id_p_ref_plae_nivel1']])){continue;}
			$arr=array();
			foreach($row as $k => $v){
				$arr[$k]=utf8_encode($v);
			}
			$PN1[$row['id_p_ref_plae_nivel1']]['documentos'][$row['linkid']]=$arr;	
		}
		if($row['id_p_ref_plae_nivel2']!=null){
			if(!isset($PN2[$row['id_p_ref_plae_nivel2']])){continue;}
			$arr=array();
			foreach($row as $k => $v){
				$arr[$k]=utf8_encode($v);
			}
			$PN2[$row['id_p_ref_plae_nivel2']]['documentos'][$row['linkid']]=$arr;	
		}
		if($row['id_p_ref_plae_nivel3']!=null){
			if(!isset($PN3[$row['id_p_ref_plae_nivel3']])){continue;}
			$arr=array();
			foreach($row as $k => $v){
				$arr[$k]=utf8_encode($v);
			}
			$PN3[$row['id_p_ref_plae_nivel3']]['documentos'][$row['linkid']]=$arr;	
		}
	}	
	
	
	
	///CONSULTA GEOMETRÍAS

	$tablas=array('ref_plae_nivel1','ref_plae_nivel2','ref_plae_nivel3');
	
	foreach($tablas as $tabla){

		$query="
		SELECT 
			c.id,
			reg.id as reg_id,
			concat( ST_AsText(ST_Transform(geo.geom, 3857)), 
			ST_AsText(ST_Transform(geo.geom_line, 3857)), 
			ST_AsText(ST_Transform(geo.geom_point, 3857)),
			ST_AsText(ST_Transform(geo2.geom, 3857)), 
			ST_AsText(ST_Transform(geo2.geom_line, 3857)), 
			ST_AsText(ST_Transform(geo2.geom_point, 3857))
			) as geotx 
			FROM 
			$DB.$tabla as c
			 
			LEFT JOIN $DB.ref_rele_registros_datos as rd 
			ON rd.id_p_ref_rele_campa = c.id_p_rele_campa 
			AND rd.ic_p_est_02_marcoacademico = c.ic_p_est_02_marcoacademico
			AND c.id_p_rele_campo>'0'
			AND rd.id_p_ref_rele_campos = c.id_p_rele_campo 
			AND ( rd.data_texto = c.filtro_rele_valor) 
			AND rd.zz_superado='0'
			AND rd.zz_borrada='0'
			
			LEFT JOIN $DB.ref_rele_registros as reg 
			ON reg.id = rd.id_p_ref_rele_registros 
			AND reg.zz_superado='0'
			AND reg.zz_borrado='0'
			
			LEFT JOIN $DB.ref_capasgeo_registros as geo 
			ON reg.id_p_ref_capas_registros = geo.id 
			AND geo.zz_borrada='0'
		
			LEFT JOIN $DB.ref_rele_registros as reg2 
			ON reg2.id_p_ref_rele_campa = c.id_p_rele_campa 
			AND reg2.col_texto1_dato = c.filtro_rele_valor 
			AND c.id_p_rele_campo='-1' 
			AND reg2.zz_superado='0'
			AND reg2.zz_borrado='0'

			LEFT JOIN $DB.ref_capasgeo_registros as geo2 
			ON reg2.id_p_ref_capas_registros = geo2.id 
			AND geo2.zz_borrada='0'

			WHERE 
				c.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
				AND c.zz_borrada='0'
				AND (geo2.zz_borrada='0' or geo.zz_borrada='0')
			ORDER BY 
			c.numero asc
				
			";	
			
		$Consulta=	pg_query($ConecSIG, $query);;
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error al consultar columnas';
			$Log['tx'][]=utf8_encode($query);
			$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
			$Log['res']='err';
			terminar($Log);
		}
		
		while($row = pg_fetch_assoc($Consulta)){
			/*
			foreach($row as $k => $v){
				$PN1[$row['id']][$k]=utf8_encode($v);	
			}*/
			if($tabla=='ref_plae_nivel1'){
				$PN1[$row['id']]['geometrias'][]=$row['geotx'];
			}elseif($tabla=='ref_plae_nivel2'){
				$PN2[$row['id']]['geometrias'][]=$row['geotx'];
			}elseif($tabla=='ref_plae_nivel3'){
				$PN3[$row['id']]['geometrias'][]=$row['geotx'];
			}
						
		}	
	}

	
///CONSULTA INDICADORES

	$tablas=array('ref_plae_nivel1','ref_plae_nivel2','ref_plae_nivel3');
	
	
	
	foreach($tablas as $tabla){

		$campolink='id_p_'.$tabla;
		
		$query="
		SELECT 
			c.id as idcomp,
			lki.id as id_lk, 
			lki.caracter, 
			lki.ldb_ano, 
			lki.ldb_mes, 
			lki.ldb_valor, 
			lki.objetivo,
			
			ind.id as id_ind,
			ind.nombre,
			ind.descripcion,
			ind.funcionalidad,
			ind.id_p_ref_capasgeo,
			ind.periodicidad, 
			ind.fechadesde, 
			ind.fechahasta
			
			FROM 
			$DB.$tabla as c
			 
			LEFT JOIN $DB.ref_plae_link_ref_indicadores_indicadores as lki 
			ON lki.$campolink = c.id
			
			LEFT JOIN $DB.ref_indicadores_indicadores as ind
			ON ind.id=lki.id_p_ref_indicadores_indicadores
			AND ind.ic_p_est_02_marcoacademico=c.ic_p_est_02_marcoacademico

			WHERE 
				c.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
				AND c.zz_borrada='0'
				AND lki.zz_borrada='0'
			ORDER BY 
			c.numero asc
							
			";	
			
		$Consulta=	pg_query($ConecSIG, $query);;
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error al consultar columnas';
			$Log['tx'][]=utf8_encode($query);
			$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
			$Log['res']='err';
			terminar($Log);
		}
		$Log['tx'][]=utf8_encode($query);
		
		while($row = pg_fetch_assoc($Consulta)){
			/*
			foreach($row as $k => $v){
				$PN1[$row['id']][$k]=utf8_encode($v);	
			}*/
			if($tabla=='ref_plae_nivel1'){
				$PN1[$row['idcomp']]['indicadores'][$row['id_lk']]=$row;
			}elseif($tabla=='ref_plae_nivel2'){
				$PN2[$row['idcomp']]['indicadores'][$row['id_lk']]=$row;
			}elseif($tabla=='ref_plae_nivel3'){
				$PN3[$row['idcomp']]['indicadores'][$row['id_lk']]=$row;
			}
						
		}	
	}	
	
	
	
	
	$Log['data']['PN1']=$PN1;	
	$Log['data']['PN2']=$PN2;	
	$Log['data']['PN3']=$PN3;	
	
	
	

$Log['res']='exito';
terminar($Log);
	

	
	

