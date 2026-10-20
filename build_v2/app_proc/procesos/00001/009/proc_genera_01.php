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

$Log['data']['avance']=$_POST['avance'];

if($_POST['idco']!='9'){
	$Log['tx'][]="componente solicitado inesperado  ($v)";
	$Log['res']='err';
	terminar($Log);	
}


$Log['data']['idco']=$_POST['idco'];
$Log['data']['alternativa']=substr(__FILE__,-6,2);



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
		in_co.zz_borrada='0'
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



//idco 1: centros poblados. / localización de centros poblados. estos senrán utilizados como unidad de análisis
	//ref_capasgeo
	$tab_1=$Log['data']['Componentes'][1]['tabla'];
	$reg_1=$Log['data']['Componentes'][1]['id_ref'];

//idco 5: inundabilidad regional
	$tab_5=$Log['data']['Componentes'][5]['tabla'];
	$reg_5=$Log['data']['Componentes'][5]['id_ref'];

//idco 12: radios por centro
	//ref_capasgeo
	$tab_12=$Log['data']['Componentes'][12]['tabla'];
	$reg_12=$Log['data']['Componentes'][12]['id_ref'];
	


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
				$Log['tablas'][$fila['id']]['ref_campos'][$k]=$campo;
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
			
			if(	$tipogeom=='Point'		){	$campogeom='geom_point';	}
			if(	$tipogeom=='Line'		){	$campogeom='geom_line';		}
			if(	$tipogeom=='Polygon'	){	$campogeom='geom';			}
			
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
					r.".$campogeom." 
				from 
					(select * FROM geogec.ref_capasgeo_registros where id_ref_capasgeo='".$fila['id']."') as r
			";
						
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
				
				
				if(	$link['tipogeometria']=='Point'		){	$campogeom='geom_point';	}

				if(	$link['tipogeometria']=='Line'		){	$campogeom='geom_line';		}
	
				if(	$link['tipogeometria']=='Polygon'	){	$campogeom='geom';			}

				
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
					FULL OUTER JOIN
						geogec.ref_capasgeo_registros as lr 
							ON lr.".$Log['tablas'][$fila['id']]['campos'][$fila['link_capa_campo_externo']]." = r.".$Log['tablas'][$fila['id']]['campos'][$fila['link_capa_campo_local']]." 
							AND 
							r.id_ref_capasgeo = '".$fila['id']."'
					WHERE 
						lr.id_ref_capasgeo = '".$Log['tablas'][$fila['id']]['link_capa']."'		
				
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



//seleccion de regiones de inundabilidad
$query="

		SELECT 
			
			ra.numero3,				
			ra.geom
			FROM	
				(".$Log['tablas'][$reg_5]['selectquery']."
				order by numero3 asc
			) as ra	
";
$Log['tx'][]='query: '.$query;	
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


$clasif=array(
	3=>'alto',
	2=>'medio',
	1=>'bajo');


$acum=0;
while($fila=pg_fetch_assoc($Consulta)){
	$reg[]=$fila['numero3'];		
	if($fila['numero3']==''||$fila['numero3']==0){continue;}
	$acum++;
}
$Log['tx'][]='acum:'.$acum;
$RangosInund=3;
$PasoInund=$acum/$RangosInund;
$Log['tx'][]='pasoinund:'.$PasoInund;

$minn=1;
$maxn=1;

$p=1;
While($p<=$RangosInund){
	
	$minn=$maxn;
	$maxn=round($p*$PasoInund);
	
	$rp=0;
	
	$maxc=null;
	$minc=null;
	$Log['tx'][]=$clasif[$p];
	
	foreach($reg as $casos){
		if($casos==0){continue;}
		$rp++;
		$Log['tx'][]='rp:'.$rp. ' min:'.$minn.'max:'.$maxn.'casos:'.$casos;
		if($rp<$minn){continue;}
		if($rp>$maxn){continue;}
		if($minc==null){$minc=$casos;}
		$maxc=$casos;
	}
	$EscalaInund[$p]=array(
		'nombre'=>$clasif[$p],
		'min'=>$minc,
		'max'=>$maxc
	);
	
	$p++;
}
$EscalaInund[0]=array(
	'nombre'=>'sin reg. históricos',
	'min'=>0,
	'max'=>0
);

$Log['tx'][]=$EscalaInund;


/* tiene que salir asi
	  SELECT 
	viv.texto2 as cod_cen,
	viv.viv,
	cen.geom_point,
	reg.numero3 as casos_inund
	FROM
	(SELECT 
		ra.texto2,
		sum(ra.numero1) as viv
		FROM	
			(
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
		r.numero5
	from 
		(select * FROM geogec.ref_capasgeo_registros where id_ref_capasgeo='152') as r
			order by numero1
		) as ra
		group by texto2
		order by sum(ra.numero1) asc
	)as viv

	LEFT JOIN
		geogec.ref_capasgeo_registros as cen ON cen.texto3 = viv.texto2 AND id_ref_capasgeo='118'
	* 
	* */

//seleccion de centros por tamaño
$query="
	SELECT 
	viv.texto2 as cod_cen,
	viv.viv,
	cen.geom_point
	FROM
	(
		SELECT 
			ra.texto2,
			sum(ra.numero1) as viv
			FROM	
				(".$Log['tablas'][$reg_12]['selectquery']."
				order by numero1
			) as ra
			group by texto2
			order by sum(ra.numero1) asc
	) as viv
	
	LEFT JOIN
	geogec.ref_capasgeo_registros as cen ON cen.texto3 = viv.texto2 AND id_ref_capasgeo='118'
";
	
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

$reg_uno=array();//control extra para evitar duplicados que se escaparan en la consulta
	
$acum=0;
while($fila=pg_fetch_assoc($Consulta)){
	
	if(isset($reg_uno[$fila['cod_cen']])){continue;}	
	$reg_uno[$fila['cod_cen']]='';
	
	$cant=0;
	if($fila['viv']!=null){
		$cant=$fila['viv'];
	}
	$acum+=$cant;
	
}
$Log['tx'][]='centros contaodos para acumulado de viviendas: '.count($reg_uno);


$RangosTam=4;
$PasoTam=$acum/$RangosTam;

$clasif=array(
4=>'Muy grande',
3=>'Grande',
2=>'Mediano',
1=>'Chico');

$min=1;
$max=1;
$p=1;

While($p<=$RangosTam){
	$min=$max;
	$max=round($p*$PasoTam);
	$EscalaTam[$p]=array(
		'nombre'=>$clasif[$p],
		'minacc'=>$min,
		'maxacc'=>$max
	);
	$p++;
}

$Log['tx'][]=$EscalaTam;


$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}










$regsporpaso=50;

$Log['data']['tot_reg']=count($reg_uno);
if($Log['data']['tot_reg'] < $regsporpaso){
	$Log['tx'][]='query: '.$query;	
	$Log['mg'][]='error en la cantidad esperada del registro';
	$Log['res']='err';
	terminar($Log);	
}

$Log['data']['totalpasos']=ceil($Log['data']['tot_reg']/$regsporpaso);




if($_POST['avance']==0){

	$query = "
		INSERT INTO geogec.ref_capasgeo
			(
				tipogeometria, autor, 
				nombre,
				
				ic_p_est_02_marcoacademico, srid, zz_borrada, zz_publicada,
				
				nom_col_text1, nom_col_text2, nom_col_text3,  nom_col_text4, 
				nom_col_num1
			)        
		VALUES
			(
				'Point', '".$idUsuario."', 
				'Centros clasificados por cantidad de viviendas y zona de inundabilidad',
				
				'".$_POST['codMarco']."', 3857, 0, 1,
				'nombre centro', 'codigo centro', 'tamaño', 'nivel de riesgo regional', 
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
		UPDATE
			geogec.ref_proc_instancias_componentes
			
			SET zz_borrada='1'		
			
		WHERE
			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
			AND
			id_p_ref_proc_componentes = '".$_POST['idco']."'
			AND
			id_p_ref_proc_instancias = '".$_POST['idinst']."'
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
			'nom_col_text1', 'nom_col_text2', 'nom_col_text3', 'nom_col_text4', 'nom_col_text5',
			'nom_col_num1'
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
	SELECT 
	cen.texto1 as nom_centro,	
	viv.texto2 as cod_cen,
	viv.viv as viviendas,
	cen.geom_point
	FROM
	(
		SELECT 
			ra.texto2,
			sum(ra.numero1) as viv
			FROM	
				(".$Log['tablas'][$reg_12]['selectquery']."
				order by numero1
			) as ra
			group by texto2
			order by sum(ra.numero1) asc
	) as viv
	
	LEFT JOIN
	geogec.ref_capasgeo_registros as cen ON cen.texto3 = viv.texto2 AND id_ref_capasgeo='118'
	ORDER BY viviendas asc
";
	
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}



$acum=0;
$Inputs_Orden=array();
while($fila=pg_fetch_assoc($Consulta)){		
	//terminar($Log);
	if($fila['viviendas']==''||$fila['viviendas']==0){$Log['data']['descartados']++;continue;}
	
	$Inputs[$fila['cod_cen']]=$fila;
	$Inputs_Orden[]=$fila['cod_cen'];
	$tam='???';	
	
	
	$acum+=$fila['viviendas'];
	//$Log['tx'][]=$fila['cen_cod']; //$Log['tx'][]='ac:'.$acum;
	
	foreach($EscalaTam as $p => $d){
		if(
			$acum>=$d['minacc']
			 &&
			$acum<=$d['maxacc']
		){
			$tam=$d['nombre'];
			$Log['tx'][]='ac:'.$acum.' - '.$tam;		
			break;
		}		
	}
	
	if($tam=='???'){
		$Log['tx'][]='ac:'.$acum.' - '.$tam;
	}
	
	$Inputs[$fila['cod_cen']]['tamaño']=$tam;

}

$Log['tx'][]='centros a analizar:'.count($Inputs);




$query="
	SELECT 
	viv.texto2 as cod_cen,
	cen.texto1 as nom_centro,
	reg.numero3 as casos_inundables
	FROM
	(
		SELECT 
			ra.texto2,
			sum(ra.numero1) as viv
			FROM	
				(".$Log['tablas'][$reg_12]['selectquery']."
				order by numero1
			) as ra
			group by texto2
			order by sum(ra.numero1) asc
	) as viv
	
	LEFT JOIN
	geogec.ref_capasgeo_registros as cen ON cen.texto3 = viv.texto2 AND id_ref_capasgeo='118'
	
	left JOIN
	(".$Log['tablas'][$reg_5]['selectquery']."
	) as reg
	ON ST_Contains(reg.geom, cen.geom_point)
	
	order by cod_cen desc
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
while($fila=pg_fetch_assoc($Consulta)){				
	$Inputs[$fila['cod_cen']]['casos_inundables']=$fila['casos_inundables'];
}	

foreach($Inputs as $cod_cen => $fila){
	$inu='???';	
	foreach($EscalaInund as $p => $d){
		if(
			     
			$fila['casos_inundables']>=$d['min']
			 &&
			$fila['casos_inundables']<=$d['max']
		){
			$inu=$d['nombre'];
			break;
		}		
	}
	if($inu=='???'){
		$Log['tx'][]='ac:'.$fila['casos_inundables'].' - '.$inu;
	}
	$Inputs[$cod_cen]['inundabilidad_region']=$inu;

}


$Log['tx'][]='centros a analizar:'.count($Inputs);

$inpus_efect=array_slice($Inputs,$offset,$regsporpaso);

foreach($inpus_efect as $fila){
	
	$query="
		insert into geogec.ref_capasgeo_registros(
			geom_point,
			id_ref_capasgeo,
			texto1,
			texto2,
			texto3,
			texto4,
			
			numero1
			
			
		)VALUES(			
			'".$fila['geom_point']."',
			'".$Log['data']['encapa']."',
			'".$fila['nom_centro']."',
			'".$fila['cod_cen']."',
			'".$fila['inundabilidad_region']."',				
			'".$fila['tamaño']."',	
						
			'".$fila['viviendas']."'				
		)
	";
	//$Log['tx'][]=$query;
	
	$ConsultaB = pg_query($ConecSIG, $query);
	
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
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
		AND
		zz_borrada='0'
		
";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


if($Log['data']['avance']==$Log['data']['totalpasos']){
	$estado='cálculo completo';
	$Log['data']['estado']='completo';
}else{
	$Log['data']['avance']=$_POST['avance']+1;	
	$Log['data']['estado']='parcial';
}



$Log['res']='exito';
terminar($Log);

