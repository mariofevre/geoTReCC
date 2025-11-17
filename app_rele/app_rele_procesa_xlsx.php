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

if(!isset($_POST['idcampa']) || $_POST['idcampa']<1){
	$Log['res']='error';
	$Log['tx'][]='falta id de campania';	
	terminar($Log);
}

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

$Acc=0;
$minAcc=2;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<$minAcc){
    $Log['mg'][]=utf8_encode('No cuenta con permisos (nivel '.$minAcc.' vs nivel '.$Acc.') para consultar un indicador. En el marco de investigación código '.$_POST['codMarco']);
    $Log['res']='err';
    terminar($Log);	
}


$idUsuario = $_SESSION[$CU]["usuario"]['id'];

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = date("Y-m-d");




$query="
SELECT 
	id, nombre, descripcion, id_p_ref_capasgeo, ic_p_est_02_marcoacademico, 
	usu_autor, zz_borrada, zz_publicada
	FROM $DB.ref_rele_campa
	WHERE
	id = '".$_POST['idcampa']."'
	AND
	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
";
$Consulta = pg_query($ConecSIG,utf8_encode($query));
//$Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
    $Log['res']='error';
    $Log['tx'][]='error al insertar registro en la base de datos';
    $Log['tx'][]=pg_errormessage($ConecSIG);
    $Log['tx'][]=$query;
    terminar($Log);
}	
$f=pg_fetch_assoc($Consulta);


if($f['id_p_ref_capasgeo']>0){	
	$IdCapa=$f['id_p_ref_capasgeo'];
}else{	
	$Log['res']='err';
	$Log['tx'][]='no se encontro la campana';	
	terminar($Log);	
}



$query="
	INSERT INTO
		$DB.ref_rele_registros(
			id_p_ref_rele_campa,
			zz_auto_crea_usu
		)
	
		VALUES(
			'".$_POST['idcampa']."',
			'".$idUsuario."'
		
		)
	RETURNING	id			
";
$Consulta = pg_query($ConecSIG,utf8_encode($query));
//$Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
	$Log['res']='error';
	$Log['tx'][]='error al insertar registro en la base de datos';
	$Log['tx'][]=pg_errormessage($ConecSIG);
	$Log['tx'][]=$query;
	terminar($Log);
}	

$fila=pg_fetch_assoc($Consulta);
$_POST['id_registro']=$fila['id'];
$Log['data']['registro_nuevo']='si';







$carpeta=$GeoGecPath;
$carpeta.='/documentos/subidas/rele/';
$carpeta.=str_pad($_POST['idcampa'],8,"0",STR_PAD_LEFT);

$carpeta.='/'.str_pad($idUsuario,8,"0",STR_PAD_LEFT);
$carpeta.='/xlsx';

$filename=$carpeta.'/'.$_POST['filename'];


require_once('./external/simplexlsx/src/SimpleXLSX.php');
//echo $file;
$Log['tx'][]=$filename;
$tabla = SimpleXLSX::parse($filename, 0);
$Filas=$tabla->rows();



$query="
	SELECT 
		id, 
		nombre, 
		inputattributes, opciones, unidaddemedida, tipo
	FROM 
		$DB.ref_rele_campos
	WHERE
		id_p_ref_rele_campa = '".$_POST['idcampa']."'
	AND
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	AND
		zz_borrada='0'
";
$Consulta = pg_query($ConecSIG,utf8_encode($query));
//$Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
    $Log['res']='error';
    $Log['tx'][]='error al insertar registro en la base de datos';
    $Log['tx'][]=pg_errormessage($ConecSIG);
    $Log['tx'][]=$query;
    terminar($Log);
}	

$Campos=Array();
$Nombres_campos=Array();
while($row=pg_fetch_assoc($Consulta)){
	$Campos[$row['id']]=$row;
	$Campos[$row['id']]['n_col']='-1';
	$Nombres_campos[$row['nombre']]=$row['id'];
}


if(!isset($_POST['campos_rellenos'])){$_POST['campos_rellenos']=array();}
foreach($_POST['campos_rellenos'] as $k => $v){
	
	if(substr($k,0,6)=='campo_'){
		$idcampo=substr($k,6);
		if(!isset($Campos[$idcampo])){
			 $Log['tx'][]='error al detectar campo';
		}
		$n_col=$v;
		$Campos[$idcampo]['n_col']=$n_col;
	}
}




foreach($_POST['campos_creados'] as $k => $v){	
		if(isset($Nombres_campos[$v['nombre']])){
			 $Log['tx'][]='Campo salteado. Ya existe un campo con el nombre: '.$v['nombre'];
			 $Log['mg'][]='Campo salteado. Ya existe un campo con el nombre: '.$v['nombre'];
			 continue;
		}
		$query="
			INSERT INTO $DB.ref_rele_campos(
				id_p_ref_rele_campa, 
				nombre, 
				tipo, 
				ic_p_est_02_marcoacademico
				)
			VALUES (
				'".$_POST['idcampa']."', 
				'".utf8_decode($v['nombre'])."',
				'".$v['tipo']."',
				'".$_POST['codMarco']."')
			RETURNING id
		";
		
		$Consulta = pg_query($ConecSIG,utf8_encode($query));
		if(pg_errormessage($ConecSIG)!=''){
			$Log['res']='error';
			$Log['tx'][]='error al insertar registro en la base de datos';
			$Log['tx'][]=pg_errormessage($ConecSIG);
			$Log['tx'][]=$query;
			terminar($Log);
		}	

		$row=pg_fetch_assoc($Consulta);
		$idcampo=$row['id'];
		if($idcampo<1){
			$Log['res']='error';
			$Log['tx'][]='error al insertar registro, campo, en la base de datos';
			$Log['tx'][]=$query;
			terminar($Log);
		}		
		$Campos[$idcampo]['nombre']=$v['nombre'];
		$Campos[$idcampo]['tipo']=$v['tipo'];
		$Campos[$idcampo]['n_col']=$v['col'];	
}



$query="
	SELECT 
		id, id_p_ref_rele_campa, 
		zz_auto_crea_usu, zz_superado, 
		zz_borrado, 
		col_texto1_dato, col_texto2_dato, col_texto3_dato, col_texto4_dato, col_texto5_dato, col_texto6_dato, col_texto7_dato, col_texto8_dato, col_texto9_dato, col_texto10_dato, 
		col_numero1_dato, col_numero2_dato, col_numero3_dato, col_numero4_dato, col_numero5_dato, col_numero6_dato, col_numero7_dato, col_numero8_dato, col_numero9_dato, col_numero10_dato, 
		id_p_ref_capas_registros, zz_auto_supera_id, zz_auto_crea_fechau, zz_archivada, zz_archivada_fecha
	FROM 
		$DB.ref_rele_registros
	WHERE
		
		id_p_ref_rele_campa = '".$_POST['idcampa']."'
		
	";
	
$NombresUAs_Idgeom=array();
$Consulta = pg_query($ConecSIG,utf8_encode($query));

while($row=pg_fetch_assoc($Consulta)){
	$NombresUAs_Idgeom[$row['col_texto1_dato']]=$row['id_p_ref_capas_registros'];
}

//$Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
	$Log['res']='error';
	$Log['tx'][]='error al insertar registro en la base de datos';
	$Log['tx'][]=pg_errormessage($ConecSIG);
	$Log['tx'][]=$query;
	terminar($Log);
}	
	



$_POST['avance']=0;
$carga=0;
	
while($carga<50000){	
	$_POST['avance']++;
	$carga++;
	
	$tot = count($Filas)-1;
	//$Log['tx'][]=$tot." vs  ".$_POST['avance'];
	if($_POST['avance']>$tot){		
		$Log['tx'][]="se alcanzo la cantidad total de ".(count($Filas)-1)." registros";
		$Log['data']['avance']='final';
		$Log['res']='exito';
		terminar($Log);
	}
	
	
	if(!isset($Filas[$_POST['avance']])){
		$Log['tx'][]='error inesperado intentando leer fila num:'.$_POST['avance'];
		continue;
	}
	$reg=$Filas[$_POST['avance']];



	if($_POST['modo_importacion']=='asignar'){//funcion disponible
	}
	if($_POST['modo_importacion']=='geo_capa'){//funcion en desarrollo
		
		$Log['tx'][]='funcion en desarrollo, revise el resultado';
	
	}
	if($_POST['modo_importacion']=='geo_coord'){//funcionen desarrollo
		
		
		$Log['res']='error';
		$Log['tx'][]='funcion en desarrollo';
		$Log['mg'][]=utf8_encode('funcion en desarrollo. Acción interrumpida');
		terminar($Log);
	}
	if($_POST['modo_importacion']=='geo_wkt'){//funcion en desarrollo
		
		$Log['tx'][]='funcion en desarrollo, revise el resultado';
	}


	if($_POST['modo_importacion']=='geo_capa'){	
	
		$nom_UA=$reg[$_POST['nombreua']];
		
		$query="
			INSERT INTO $DB.ref_capasgeo_registros(
				geom, geom_point, geom_line,
				texto1,
				id_ref_capasgeo, 
				zz_auto_crea_usu, zz_auto_crea_fechau
			)
			SELECT 
				geom, geom_point, geom_line,
				'".$nom_UA."', 
				'".$IdCapa."', 
				'".$idUsuario."', '".time()."'
			FROM 
				$DB.ref_capasgeo_registros
			WHERE 
				id_ref_capasgeo='".$_POST['capa_fuente']."' 
				AND ".$_POST['campo_link_en_capa']." = '".$reg[$_POST['columna_link_en_planillla']]."'
				
			RETURNING id
		";
		$Consulta = pg_query($ConecSIG,utf8_encode($query));
		if(pg_errormessage($ConecSIG)!=''){
			$Log['res']='error';
			$Log['tx'][]='error al insertar registro en capa registros, en la base de datos';
			$Log['tx'][]=pg_errormessage($ConecSIG);
			$Log['tx'][]=$query;
			terminar($Log);
		}	
		$row=pg_fetch_assoc($Consulta);
		$idua=$row['id'];
		
		
		$Log['tx'][]='error al insertar registro en capa registros, en la base de datos par ala fila: '.$_POST['avance'].' salteada';

			
			
		$NombresUAs_Idgeom[$nom_UA]=$idua;
	
	}
	
	
	
	if($_POST['modo_importacion']=='geo_wkt'){	
	
		$nom_UA=$reg[$_POST['nombreua']];
		
		
		$wkt=$reg[$_POST['columna_wkt_en_planillla']];
		$wkt=str_replace(',','.',$wkt);
		$s=substr($wkt,0,4);
		if($s=='POIN'){
			$campogeom='geom_point';
		}elseif($s=='LINE'){
			$campogeom='geom_line';
		}elseif($s=='POLY'){
			$campogeom='geom';
		}else{
			$Log['mg'][]=utf8_encode('No se reconoce como geometría válida: '.$nom_UA.' Fila '.$_POST['avance'].' salteada');
			continue;
		}
		
		$srid=$_POST['srid_wkt'];
		
		$query="
			INSERT INTO $DB.ref_capasgeo_registros(
				".$campogeom.",
				texto1,
				id_ref_capasgeo, 
				zz_auto_crea_usu, zz_auto_crea_fechau
			)
			SELECT 
				
				ST_GeomFromText('".$wkt."',".$srid."),
				'".$nom_UA."', 
				'".$IdCapa."', 
				'".$idUsuario."', '".time()."'
			RETURNING id
		";
		$Consulta = pg_query($ConecSIG,utf8_encode($query));
		$row=pg_fetch_assoc($Consulta);
		$idua=$row['id'];
		if($idua<1){
			$Log['res']='error';
			$Log['tx'][]='error al insertar registro, campo, en la base de datos';
			$Log['tx'][]=$query;
			terminar($Log);
			
		}				
		$NombresUAs_Idgeom[$nom_UA]=$idua;
	
	}
	
		
	
	
	
	
	$nom_UA=$reg[$_POST['nombreua']];
	if( !isset($NombresUAs_Idgeom[$nom_UA]) ){
		$Log['mg'][]=utf8_encode('No se encuentra la unidad de analisis: '.$nom_UA.' Fila '.$_POST['avance'].' salteada');
		continue;
	}
	
	
	$idgeom=$NombresUAs_Idgeom[$nom_UA];

	$query="
		INSERT INTO $DB.ref_rele_registros(
			id_p_ref_rele_campa, 	zz_auto_crea_usu, 		zz_auto_crea_fechau, 
			col_texto1_dato, 		col_texto2_dato, 		col_texto3_dato, 	col_texto4_dato, 	col_texto5_dato, 	col_texto6_dato, 	col_texto7_dato, 	col_texto8_dato, 	col_texto9_dato, 	col_texto10_dato, 
			col_numero1_dato, 		col_numero2_dato, 		col_numero3_dato, 	col_numero4_dato, 	col_numero5_dato, 	col_numero6_dato, 	col_numero7_dato, 	col_numero8_dato, 	col_numero9_dato, 	col_numero10_dato, 
			id_p_ref_capas_registros,
			zz_archivada,
			zz_archivada_fecha
			)
		VALUES (
			'".$_POST['idcampa']."', '".$idUsuario."', 		'".time()."', 
			'".utf8_decode($nom_UA)."', 	'', 			'', 				'', 				'', 				'', 				'', 				'', 				'', 				'', 
			'1', 							null, 			null, 				null, 				null, 				null, 				null, 				null, 				null, 				null, 
			'".$idgeom."',
			'0',
			'0001-01-01'
		)
		RETURNING id
			
	";
	//$Log['tx'][]=$query;
	$Consulta = pg_query($ConecSIG,utf8_encode($query));
	$row=pg_fetch_assoc($Consulta);
	$nid=$row['id'];
	if($nid<1){
		$Log['res']='error';
		$Log['tx'][]='error al insertar registro en la base de datos';
		$Log['tx'][]=$query;
	}
	
	$Log['data']['nid']=$nid;
	//$Log['tx'][]=$query;
	if(pg_errormessage($ConecSIG)!=''){
		$Log['res']='error';
		$Log['tx'][]='error al insertar registro en la base de datos';
		$Log['tx'][]=pg_errormessage($ConecSIG);
		$Log['tx'][]=$query;
		terminar($Log);
	}	

	$query="
	UPDATE 
		$DB.ref_rele_registros
		SET
			zz_superado='1',
			zz_auto_supera_id='".$nid."'	
		WHERE
			zz_superado='0'
			AND
			zz_borrado='0'
			AND
			id_p_ref_rele_campa='".$_POST['idcampa']."'
			AND
			id_p_ref_capas_registros='".$idgeom."'
			AND
			id!='".$nid."'	
			AND
			zz_archivada='0'
			
	";
	$Consulta = pg_query($ConecSIG,utf8_encode($query));
	$row=pg_fetch_assoc($Consulta);
	//$Log['tx'][]=$query;
	if(pg_errormessage($ConecSIG)!=''){
		$Log['res']='error';
		$Log['tx'][]='error al insertar registro en la base de datos';
		$Log['tx'][]=pg_errormessage($ConecSIG);
		$Log['tx'][]=$query;
		terminar($Log);
	}	

	$Log['tx'][]='UA:'.$nom_UA;

	$_POST['zz_archivada']=0;				
	$_POST['fecha_archivo']='0001-01-01';

	foreach($Campos as $k => $val){	
		if($Campos[$k]['n_col']==-1){
			//$Log['tx'][]='campo salteado sin columna asignada:'.$Campos[$k]['nombre'].'(id:'.$Campos[$k]['id'].')';
			continue;
		}
		$v= $reg[$Campos[$k]['n_col']];
		

		if($Campos[$k]['tipo']=='texto'){
			
			$defcampo="
				data_texto,  
				data_numero,
				data_documento
			";
			$setcampo="
				'".utf8_decode($v)."',
				null,
				null
			";
			
		}elseif($Campos[$k]['tipo']=='numero'){
			if($v===null||$v===''){
				$v= "null";
			}
			$defcampo="
				data_texto,  
				data_numero,
				data_documento
			";
			
			if(strpos($v, ',')!==false){		 
				if(strpos($v, '.')!==false){		 		
					if(strpos($v, '.') > strpos($v, ',')){		 
						$v=str_replace(',','',$v);
					}else{
						$v=str_replace('.','',$v);
						$v=str_replace(',','.',$v);					
					}
				}else{
					$v=str_replace(',','.',$v);	
				}
			}
			
			$setcampo="
				null,
				".(float)$v.",
				null
			";
			
		}elseif($Campos[$k]['tipo']=='fecha'){
			
			$e=explode(' ',$v);
			$v=$e[0];
			
			
			
			$att=json_decode($Campos[$k]['inputattributes'], true);
			$Log['tx'][]=$v;
			if(isset($att['es_fecha_archivo'])){
				if($att['es_fecha_archivo']=='si'){
					$_POST['zz_archivada']=1;				
					$_POST['fecha_archivo']=$v;
				}
			}
			if($v===null||$v===''){
				$v= "null";
			}
			
			$defcampo="
				data_texto,  
				data_numero,
				data_documento
			";
			$setcampo="
				'".$v."',
				null,
				null
			";
		}elseif($Campos[$k]['tipo']=='coleccion_imagenes'){
			$defcampo="
				data_texto,  
				data_numero,
				data_documento
			";
			$setcampo="
				null,
				null,
				'".$v."'
			";			
		}
		
		$query="
			INSERT INTO 
			$DB.ref_rele_registros_datos(
				
					ic_p_est_02_marcoacademico,
					id_p_ref_rele_campa, 
					id_p_ref_rele_campos, 
					id_p_ref_rele_registros, 
					$defcampo
					
			)VALUES (
				'".$_POST['codMarco']."',
				'".$_POST['idcampa']."', 
				'".$k."',
				'".$nid."',
				$setcampo
			)
			RETURNING id
		";
		
		//$Log['tx'][]=$query;
		$Consulta = pg_query($ConecSIG,utf8_encode($query));
		$row=pg_fetch_assoc($Consulta);
		$nid_rc=$row['id'];
		$Log['data']['registroscampos_nids'][]=$nid_rc;
		//$Log['tx'][]=$query;
		   
		if(pg_errormessage($ConecSIG)!=''){
			$Log['res']='error';
			$Log['tx'][]='error al insertar registro-campo en la base de datos';
			$Log['tx'][]=pg_errormessage($ConecSIG);
			$Log['tx'][]=utf8_encode($query);
			terminar($Log);
		}


		$query="
		
			UPDATE 
				$DB.ref_rele_registros_datos
			SET
				zz_superado='1',
				zz_auto_supera_id='".$nid_rc."'
				
			FROM 
				$DB.ref_rele_registros
			WHERE
				ref_rele_registros_datos.id_p_ref_rele_registros = ref_rele_registros.id
			AND
				ref_rele_registros.id != $nid
			AND
				ref_rele_registros.id_p_ref_capas_registros = '".$idgeom."'
			AND
				ref_rele_registros_datos.zz_superado='0'
			AND
				ref_rele_registros_datos.zz_borrada='0'
			AND
				ref_rele_registros_datos.id_p_ref_rele_campa='".$_POST['idcampa']."'		
			AND
				ref_rele_registros_datos.id_p_ref_rele_campos='".$k."'
			AND
				ref_rele_registros_datos.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'		
				
		";
		//$Log['tx'][]=$query;
		$Consulta = pg_query($ConecSIG,utf8_encode($query));
		$row=pg_fetch_assoc($Consulta);
		$Log['data']['registroscampos_nids'][]=$nid;
		//$Log['tx'][]=utf8_encode($query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['res']='error';
			$Log['tx'][]='error al insertar registro-campo en la base de datos';
			$Log['tx'][]=pg_errormessage($ConecSIG);
			$Log['tx'][]=$query;
			terminar($Log);
		}					
	}

	$query="
		UPDATE
			$DB.ref_rele_registros
		SET
			zz_archivada='".$_POST['zz_archivada']."',
			zz_archivada_fecha='".$_POST['fecha_archivo']."'
		WHERE 
			id='".$nid."'	
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

$Log['res']="exito";
terminar($Log);
