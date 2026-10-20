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
$Log['res']='';
function terminar($Log){
    $res=json_encode($Log);
    if($res==''){$Log['tx'][]=erroresJson();$res=print_r($Log,true);}
    echo $res;
    exit;
}


if(!isset($_POST['nivel'])){
    $Log['tx'][]='falta variable nivel';
    $Log['res']='err';
    terminar($Log);
}

$Validos['nivel']=array('PLAn1'=>'','PLAn2'=>'','PLAn3'=>'');
if(!isset($Validos['nivel'][$_POST['nivel']])){
    $Log['tx'][]='Variable nivel contiene un valor inváido ('.$_POST['nivel'].'). Solo se aceptan: '.print_r($Validos['nivel'],true);
    $Log['res']='err';
    terminar($Log);
}

$Id=$_POST['id'];	

if($Id == ''){
	
	$tabla_n=str_replace('PLAn','ref_plae_nivel',$_POST['nivel']);
	
	if($_POST['nivel']=='PLAn1'){
		$extrac='';	
		$extrav='';	
	}elseif($_POST['nivel']=='PLAn2'){
		$extrac="id_p_plae_nivel1,";
		$extrav=" '".$_POST['id_p_PLA']."',";
	}elseif($_POST['nivel']=='PLAn3'){
		$extrac="id_p_plae_nivel2,";
		$extrav=" '".$_POST['id_p_PLA']."',";
	}
		
	
    $query="		
        INSERT INTO 
			$DB.".$tabla_n."(
				ic_p_est_02_marcoacademico,
				$extrac
				zz_preliminar
			)
			VALUES(
				'".$_POST['codMarco']."',
				$extrav
				'1'
			)
			returning id
    ";							
    $Consulta=pg_query($ConecSIG, $query);
    if(pg_errormessage($ConecSIG)!=''){
        $Log['tx'][]='error al consultar columnas';
        $Log['tx'][]=utf8_encode($query);
        $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
        $Log['res']='err';
        terminar($Log);
    }
    $row=pg_fetch_assoc($Consulta);
    $Id = $row['id'];
    
    
    if($Id<1){
        $Log['tx'][]='error al generar un nuevo id: '.$Id;
        $Log['tx'][]=utf8_encode($query);
        $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
        $Log['res']='err';
        terminar($Log);
    }

}	

unset($_POST['id']);


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
	$CAT['PN1']=array();
	$CAT['PN2']=array();
	$CAT['PN3']=array();
	while($row = pg_fetch_assoc($Consulta)){
		foreach($row as $k => $v){
			$CAT[$row['nivel']][$row['id']][$k]=utf8_encode($v);	
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
			foreach($row as $k => $v){
				$PNCatVals['PN1'][$row['id_p_plae_nivel1']][$row['id_p_plae_categorias ']]=utf8_encode($row['valor']);	
			}	
		}	
		if($row['id_p_plae_nivel2']>0){
			foreach($row as $k => $v){
				$PNCatVals['PN2'][$row['id_p_plae_nivel2']][$row['id_p_plae_categorias ']]=utf8_encode($row['valor']);	
			}	
		}	
		if($row['id_p_plae_nivel3']>0){
				$PNCatVals['PN3'][$row['id_p_plae_nivel3']][$row['id_p_plae_categorias ']]=utf8_encode($row['valor']);
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

	$query="
		SELECT 
			ref_plae_nivel3.id,
		    ref_plae_nivel3.nombre,
		    ref_plae_nivel3.descripcion,		    
		    ref_plae_nivel3.id_p_plae_nivel2,
		    ref_plae_nivel3.ic_p_est_02_marcoacademico,
		   	ref_plae_nivel3.co_color,  		    
		    ref_plae_nivel3.numero,
		    id_p_rele_campa,
		    id_p_rele_campo,
		    filtro_rele_valor
		FROM $DB.ref_plae_nivel3
		Where ic_p_est_02_marcoacademico='".$_POST['codMarco']."' 
	";	
	$PN3=array();	
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
			$PN3[$row['id']][$k]=utf8_encode($v);	
		}*/	
		$PN3[$row['id']]=$row;			
		$PN3[$row['id']]['documentos']=array();
		
		$b['id']=$row['id'];
		$plas2[$row['id_p_plae_nivel2']][]=$b;
		
		$PN3[$row['id']]['categorias']=Array();
		
		if(isset($PNCatVals['PN3'][$row['id']])){
			$PN3[$row['id']]['categorias']=$PNCatVals['PN3'][$row['id']];
		}					
		if(!isset($PNestados['PN3'][$row['id']])){$PNestados['PN3'][$row['id']]=array();}
		$PN3[$row['id']]['estados']=$PNestados['PN3'][$row['id']];	
	}


	$query="
		SELECT ref_plae_nivel2.id,
		    ref_plae_nivel2.nombre,
		    ref_plae_nivel2.numero,
		   	ref_plae_nivel2.co_color,   
		    ref_plae_nivel2.descripcion,
		    ref_plae_nivel2.id_p_plae_nivel1,
		    ref_plae_nivel2.ic_p_est_02_marcoacademico,
		    id_p_rele_campa,
		    id_p_rele_campo,
		    filtro_rele_valor
		FROM $DB.ref_plae_nivel2
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
	$PN2=array();
	while($row = pg_fetch_assoc($Consulta)){
		/*
		foreach($row as $k => $v){
			$PN2[$row['id']][$k]=utf8_encode($v);	
		}*/
		$PN2[$row['id']]=$row;	
		$PN2[$row['id']]['documentos']=array();
		
		if(!isset($plas2[$row['id']])){
			$plas2[$row['id']]=array();
		}	
		
		$b['id']=$row['id'];
		$b['PLAn3']['componentes']=$plas2[$row['id']];
		$plas1[$row['id_p_plae_nivel1']][]=$b;

		$PN2[$row['id']]['categorias']=Array();
		if(isset($PNCatVals['PN2'][$row['id']])){
			$PN2[$row['id']]['categorias']=$PNCatVals['PN2'][$row['id']];
		}		
		
		if(!isset($PNestados['PN2'][$row['id']])){$PNestados['PN2'][$row['id']]=array();}
		$PN2[$row['id']]['estados']=$PNestados['PN2'][$row['id']];			
	}	

	$query="
		SELECT 
			ref_plae_nivel1.id,
		    ref_plae_nivel1.nombre,
		    ref_plae_nivel1.numero,
		    ref_plae_nivel1.co_color,
		    ref_plae_nivel1.descripcion,
		    id_p_rele_campa,
		    id_p_rele_campo,
		    filtro_rele_valor
		FROM 
			$DB.ref_plae_nivel1
		Where 
			ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
			AND
			zz_borrada='0' 
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
		$PN1[$row['id']]['documentos']=array();
		
		if(!isset($plas1[$row['id']])){
			$plas1[$row['id']]=array();
		}	
		
		$b['id']=$row['id'];
		$b['PLAn2']['componentes']=$plas1[$row['id']];		
		$PLA['PLAn1']['componentes'][]=$b;
		
		$PN1[$row['id']]['categorias']=Array();
		
		if(isset($PNCatVals['PN1'][$row['id']])){
			$PN1[$row['id']]['categorias']=$PNCatVals['PN1'][$row['id']];
		}
		
		if(!isset($PNestados['PN1'][$row['id']])){$PNestados['PN1'][$row['id']]=array();}
		$PN1[$row['id']]['estados']=$PNestados['PN1'][$row['id']];					
		
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
		;
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

		if($row['id_p_ref_plae_nivel1']!=null){
			if(!isset($PN1[$row['id_p_ref_plae_nivel1']])){continue;}
			$PN1[$row['id_p_ref_plae_nivel1']]['documentos'][$row['linkid']]=$row;	
		}
		if($row['id_p_ref_plae_nivel2']!=null){
			if(!isset($PN2[$row['id_p_ref_plae_nivel2']])){continue;}
			$PN2[$row['id_p_ref_plae_nivel2']]['documentos'][$row['linkid']]=$row;	
		}
		if($row['id_p_ref_plae_nivel3']!=null){
			if(!isset($PN3[$row['id_p_ref_plae_nivel3']])){continue;}
			$PN3[$row['id_p_ref_plae_nivel3']]['documentos'][$row['linkid']]=$row;	
		}
	}	
	
	$tabla='';
	$Log['data']['componente']['nivel']=$_POST['nivel'];
	if($_POST['nivel']=='PLAn1'){
		$Log['data']['componente']=$PN1[$Id];
		$tabla='ref_plae_nivel1';
	}elseif($_POST['nivel']=='PLAn2'){
		$Log['data']['componente']=$PN2[$Id];
		$tabla='ref_plae_nivel2';
	}elseif($_POST['nivel']=='PLAn3'){
		$Log['data']['componente']=$PN3[$Id];
		$tabla='ref_plae_nivel3';
	}
	$Log['data']['componente']['nivel']=$_POST['nivel'];



$tablas=array('ref_plae_nivel1','ref_plae_nivel2','ref_plae_nivel3');
	
	
	
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
			
			$Log['data']['componente']['indicadores'][$row['id_lk']]=$row;
						
		}	
				
				

	
	
	
	
	if($Log['data']['componente']['id_p_rele_campa']>'0'){
		
			$query="
				SELECT 
					c.id,
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

					LEFT JOIN $DB.ref_rele_registros as reg 
					ON reg.id = rd.id_p_ref_rele_registros 
					AND reg.zz_superado='0'
					
					LEFT JOIN $DB.ref_capasgeo_registros as geo 
					ON reg.id_p_ref_capas_registros = geo.id 

				
					LEFT JOIN $DB.ref_rele_registros as reg2 
					ON reg2.id_p_ref_rele_campa = c.id_p_rele_campa 
					AND reg2.col_texto1_dato = c.filtro_rele_valor 
					AND c.id_p_rele_campo='-1' 
					AND reg2.zz_superado='0'

					LEFT JOIN $DB.ref_capasgeo_registros as geo2 
					ON reg2.id_p_ref_capas_registros = geo2.id 

					WHERE 
						c.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
						AND c.id='".$Id."'
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
					
					$Log['data']['componente']['geometrias']=$row;
								
				}	
		
	}



/*	
	$CSV="idACC;numACC;nombACC;desACC;idPRO;nombPRO;idPLA;nombPLA".PHP_EOL;	


	$carpeta="./documentos/p_$_POST['codMarco']/temp";
	if(!file_exists($carpeta)){
		mkdir($carpeta, 0777, true);
		chmod($carpeta, 0777); 
	}
	file_put_contents ($carpeta.'/'.'Acciones.csv' , $CSV);
*/


	$Log['data']['PLA']=$PLA;
	$Log['data']['CAT']=$CAT;//categorías para cada nivel	
	$Log['data']['PN1']=$PN1;	
	$Log['data']['PN2']=$PN2;	
	$Log['data']['PN3']=$PN3;		
	
	
	
	
$Log['res']='exito';
terminar($Log);
	

	
	

