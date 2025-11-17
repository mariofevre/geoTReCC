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
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}
$minacc=2;
if($Acc<$minacc){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificación de este marco académico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
	$Log['tx'][]=print_r($Usu,true);
	$Log['res']='err';
	terminar($Log);
}
$idUsuario = $_SESSION[$CU]["usuario"]['id'];

if(!isset($_POST['modo_recorte'])){
	$Log['tx'][]='no fue enviada la variable modo_recorte';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['link_dto'])){
	$Log['tx'][]='no fue enviada la variable link_dto';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['idcapa_recorte'])){
	$Log['tx'][]='no fue enviada la variable idcapa_recorte';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['id_reg_recorte'])){
	$Log['tx'][]='no fue enviada la variable id_reg_recorte';
	$Log['res']='err';
	terminar($Log);	
}



$query = "
	SELECT
		ref_capasgeo.id,
		ref_capasgeo.tipogeometria,
		ref_capasgeo.link_capa,
		ref_capasgeo.link_capa_campo_local,
		ref_capasgeo.link_capa_campo_externo,
		ref_capasgeo.modo_publica,
		sis_usu_accesos.id as idacc
		FROM
		
		$DB.ref_capasgeo
		
		LEFT JOIN
			$DB.sis_usu_accesos
				ON sis_usu_accesos.id_p_sis_usu_registro='".$idUsuario."' 
				AND sis_usu_accesos.tabla='est_02_marcoacademico'
				AND sis_usu_accesos.elemento=ref_capasgeo.ic_p_est_02_marcoacademico
				AND sis_usu_accesos.nivel >= 2
				AND sis_usu_accesos.zz_borrada = 0
		WHERE 
			ref_capasgeo.id='".$_POST['idcapa']."' 
			AND 
				(
				ref_capasgeo.modo_publica='publica' 
				OR 
				ref_capasgeo.modo_publica='gec' 
				OR
				sis_usu_accesos.id IS NOT NULL
				)
				
	LIMIT 1
";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
if(pg_num_rows($Consulta)==0){
	$Log['tx'][]='la capa de origen no parece habilitada para este usuario: '.$query;	
	$Log['res']='exito';
	terminar($Log);
}

$Log['data']['caparef']=pg_fetch_assoc($Consulta);		


$na= $_SESSION[$CU]["usuario"]["nombre"].' '. $_SESSION[$CU]["usuario"]["apellido"];


if($_POST['modo_recorte']=='capa'){
	$Log['mg'][]='recorte por capa no se encuentra disponible aun.';
	$Log['tx'][]='recorte por capa no se encuentra disponible aun.';
	$Log['res']='err';
	terminar($Log);	
}elseif($_POST['modo_recorte']=='no'){	
	$nombre_pre="Copia de capa: ";
	$descripcion_pre="Copia realizada por ".$na."(".date("Y-m-d").") ";
}elseif($_POST['modo_recorte']=='proyecto'){
	$nombre_pre="Recorte de capa: ";
	$descripcion_pre="Recorte realizado por ".$na."(".date("Y-m-d")."). lmitado al proyecto ".$_POST['codMarco'].". ";	
}elseif($_POST['modo_recorte']=='departamento'){
	$nombre_pre="Recorte de capa: ";
	$descripcion_pre="Recorte realizado por ".$na."(".date("Y-m-d")."). lmitado al departamento ".$_POST['link_dto'].". ";	
	if($_POST['link_dto']==''){
		$Log['mg'][]='falta definir departamento.';
		$Log['res']='err';
		terminar($Log);	
	}
		
}else{
	$Log['tx'][]='valor para modo_recorte inesperado';
	$Log['res']='err';
	terminar($Log);	
}



$query = "
	INSERT INTO $DB.ref_capasgeo(
	
		ic_p_est_02_marcoacademico, zz_borrada, 
		nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, 
		cod_col_text1, cod_col_text2, cod_col_text3, cod_col_text4, cod_col_text5, cod_col_num1, cod_col_num2, cod_col_num3, cod_col_num4, cod_col_num5, 
		autor, nombre, descripcion, 
		srid, sld, tipogeometria, modo_defecto, wms_layer, 
		
		
		zz_publicada,  zz_instrucciones, zz_aux_ind, zz_aux_rele, 
		
		modo_publica, tipo_fuente, link_capa, link_capa_campo_local, link_capa_campo_externo, 
		
		fecha_ano, fecha_mes, fecha_dia, 
		zz_cache_extent
	)
	(SELECT
			'".$_POST['codMarco']."', zz_borrada, 
			
			nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, 
			cod_col_text1, cod_col_text2, cod_col_text3, cod_col_text4, cod_col_text5, cod_col_num1, cod_col_num2, cod_col_num3, cod_col_num4, cod_col_num5, 
			'".$idUsuario."', CONCAT('".$nombre_pre."', nombre), CONCAT('".$descripcion_pre."', descripcion), 
			srid, sld, tipogeometria, modo_defecto, wms_layer, 
			
			'0',  zz_instrucciones, zz_aux_ind, zz_aux_rele, 
			
			'personal', tipo_fuente, link_capa, link_capa_campo_local, link_capa_campo_externo, 
			
			fecha_ano, fecha_mes, fecha_dia, 
			zz_cache_extent
			
		FROM
			$DB.ref_capasgeo
		WHERE 
			id='".$_POST['idcapa']."' 
			AND 
			(modo_publica='publica' 
			OR 
			modo_publica='gec')
		
	)
	LIMIT 1
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
$fila=pg_fetch_assoc($Consulta);

$Log['tx'][]= "Creada capa id: ".$fila['id'];
$Log['data']['nid']=$fila['id'];


if(!($Log['data']['nid']>0)){
	$Log['tx'][]='error al duplicar datos generale de la capa: '.$query;	
	$Log['res']='exito';
	terminar($Log);
}


if($Log['data']['caparef']['tipogeometria']=='Polygon'){	
	$campo_geom='geom';
	$fuentegeometria='local';
}elseif($Log['data']['caparef']['tipogeometria']=='LineString'){	
	$campo_geom='geom_line';	
	$fuentegeometria='local';
}elseif($Log['data']['caparef']['tipogeometria']=='Point'){	
	$campo_geom='geom_point';
	$fuentegeometria='local';
}elseif($Log['data']['caparef']['tipogeometria']=='Tabla'){	
	$fuentegeometria='sin geometria';	
	if(
		$Log['data']['caparef']['link_capa']!=''
		&&
		$Log['data']['caparef']['link_capa']!='-1'
		&&
		$Log['data']['caparef']['link_capa_campo_local']!=''
		&&
		$Log['data']['caparef']['link_capa_campo_externo']!=''	
	){
		$fuentegeometria='externa_capa';			
		$query="
			SELECT 
				c.id, c.autor, c.nombre, 
				c.ic_p_est_02_marcoacademico,
				c.tipogeometria, c.zz_instrucciones
				
			FROM 
				$DB.ref_capasgeo as c
			
			WHERE 
				id = '".$Log['data']['caparef']['link_capa']."'
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
		$tipogeom=$link['tipogeometria'];
	}elseif(
		$Log['data']['caparef']['link_capa']=='-1'
		&&
		$Log['data']['caparef']['link_capa_campo_local']!=''
		&&
		$Log['data']['caparef']['link_capa_campo_externo']!=''	
	){				
		$fuentegeometria='externa_est01';
		$tipogeom='Polygon';
	}
	
	
	if($tipogeom=='Polygon'){	
		$campo_geom='geom';
	}elseif($tipogeom=='LineString'){	
		$campo_geom='geom_line';	
	}elseif($tipogeom=='Point'){	
		$campo_geom='geom_point';
	}
	
	
}else{	
    $Log['mg'][]='error al determinar el tipo de geometria';
    $Log['res']='err';
    terminar($Log);		
}



function nombre_a_campo($k){
	// cambia el tipo nom_col_num1 a numero1
	// la primera refiere al campo que define el nombre del campo de una capa.
	// la seguna al campo de la capa correspondiente
	if(substr($k,0,8)=='nom_col_'){
		$campo=str_replace('nom_col_', '', $k);
		$campo=str_replace('text', 'texto', $campo);
		$campo=str_replace('num', 'numero', $campo);
		$ref_campos[$k]=$campo;
		return $campo;
	}else{
		return "ERR";	
	}
}


if($_POST['modo_recorte']=='capa'){
	$Log['mg'][]='recorte por capa no se encuentra disponible aun.';
	$Log['tx'][]='recorte por capa no se encuentra disponible aun.';
	$Log['res']='err';
	terminar($Log);	
	
	
	



	
}elseif($_POST['modo_recorte']=='no'){
	//copia todos los elementaos de la cap origen

	$query = "
		INSERT INTO $DB.ref_capasgeo_registros(

			id_ref_capasgeo, 
			zz_auto_crea_usu, zz_auto_crea_fechau,
			geom, geom_point, geom_line,
			texto1, texto2, texto3, texto4, texto5, 
			numero1, numero2, numero3, numero4, numero5
		)
		(SELECT
				'".$Log['data']['nid']."', 
				zz_auto_crea_usu, zz_auto_crea_fechau,
				geom, geom_point, geom_line,
				texto1, texto2, texto3, texto4, texto5, 
				numero1, numero2, numero3, numero4, numero5
				
			FROM
				$DB.ref_capasgeo_registros
			WHERE
				zz_borrada = '0'
				AND
				id_ref_capasgeo = '".$_POST['idcapa']."'

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

}elseif($_POST['modo_recorte']=='proyecto'){
	
	//copia todos lo elementaos de la capa origen que se intersectan con EL AREA DE PROYECTo
	
	$Id_Capa_Departamen='237';
	$campo_link_departamen='texto1';
	

	$query = "
	WITH rows AS (
		INSERT INTO $DB.ref_capasgeo_registros(

			id_ref_capasgeo, 
			zz_auto_crea_usu, zz_auto_crea_fechau,
			geom, geom_point, geom_line,
			texto1, texto2, texto3, texto4, texto5, 
			numero1, numero2, numero3, numero4, numero5
		)
		(SELECT
				'".$Log['data']['nid']."', 
				copiables.zz_auto_crea_usu, copiables.zz_auto_crea_fechau,
				copiables.geom, copiables.geom_point, copiables.geom_line,
				copiables.texto1, copiables.texto2, copiables.texto3, copiables.texto4, copiables.texto5, 
				copiables.numero1, copiables.numero2, copiables.numero3, copiables.numero4, copiables.numero5
				
			FROM
				$DB.ref_capasgeo_registros as copiables,
				$DB.est_02_marcoacademico as recorte
				
				
				
			WHERE
				copiables.zz_borrada = '0'
				AND
				copiables.id_ref_capasgeo = '".$_POST['idcapa']."'
				AND
				recorte.codigo = '".$_POST['codMarco']."'
				AND
				ST_Intersects( copiables.".$campo_geom." , recorte.geo ) = TRUE
		)
		RETURNING 1
		)
	SELECT count(*) as insertados FROM rows;
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
	$Log['data']['recgistros copiados']=$fila['insertados'];
	if($fila['insertados']==0){
		$Log['mg'][]='no se encontraron elementos superpuestos a la geometría de recorte';
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);			
	}
	
	$Log['tx'][]=utf8_encode($query);
	
}elseif($_POST['modo_recorte']=='departamento'){
	
	
	//copia todos lo elementos de la capa origen que se intersectan con un departamento
	
	$Id_Capa_Departamen='119';
	$campo_link_departamen='texto3';
	if($_POST['link_dto']=='0202'){
			$_POST['link_dto']='02'; //corrige criterio CABA
	}

	if($fuentegeometria=='local'){
		$campos="copiables.geom, copiables.geom_point, copiables.geom_line,";
		$join="";
		$intersec="ST_Intersects( copiables.".$campo_geom." , recorte.geom ) = TRUE";
	}else{
		$campos="lr.geom, lr.geom_point, lr.geom_line,";	
		$join=" 
					FULL OUTER JOIN
						$DB.ref_capasgeo_registros as lr 
						ON lr.".nombre_a_campo($Log['data']['caparef']['link_capa_campo_externo'])." = copiables.".nombre_a_campo($Log['data']['caparef']['link_capa_campo_local'])."
						AND lr.id_ref_capasgeo = '".$Log['data']['caparef']['link_capa']."'
				";
		$intersec="ST_Intersects( lr.".$campo_geom." , recorte.geom ) = TRUE";
		
		
		
		$query_aux="UPDATE  
						$DB.ref_capasgeo
						SET
						tipogeometria = '".$tipogeom."',
						link_capa = null,
						link_capa_campo_local = null, 
						link_capa_campo_externo =null
					WHERE id ='".$Log['data']['nid']."'
				";
		$Consulta = pg_query($ConecSIG, $query_aux);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.utf8_encode(pg_errormessage($ConecSIG));
			$Log['tx'][]='query: '.utf8_encode($query_aux);
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		
	}
	
	$query = "
	WITH rows AS (
		INSERT INTO $DB.ref_capasgeo_registros(
			id_ref_capasgeo, 
			zz_auto_crea_usu, zz_auto_crea_fechau,
			geom, geom_point, geom_line,
			texto1, texto2, texto3, texto4, texto5, 
			numero1, numero2, numero3, numero4, numero5
		)
		
		(SELECT
				'".$Log['data']['nid']."', 
				copiables.zz_auto_crea_usu, copiables.zz_auto_crea_fechau,
				".$campos."
				copiables.texto1, copiables.texto2, copiables.texto3, copiables.texto4, copiables.texto5, 
				copiables.numero1, copiables.numero2, copiables.numero3, copiables.numero4, copiables.numero5
				
			FROM
				$DB.ref_capasgeo_registros as recorte,
				$DB.ref_capasgeo_registros as copiables				
				".$join."
				
			WHERE
				copiables.zz_borrada = '0'
				AND
				copiables.id_ref_capasgeo = '".$_POST['idcapa']."'
				AND
				recorte.id_ref_capasgeo = '".$Id_Capa_Departamen."'
				AND
				recorte.".$campo_link_departamen." = '".$_POST['link_dto']."'
				AND
				".$intersec."
		)
		RETURNING 1
		)
	SELECT count(*) as insertados FROM rows;
	";
	
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.utf8_encode(pg_errormessage($ConecSIG));
		$Log['tx'][]='query: '.utf8_encode($query);
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	$fila=pg_fetch_assoc($Consulta);
	$Log['data']['recgistros copiados']=$fila['insertados'];
	if($fila['insertados']==0){
		$Log['mg'][]=utf8_encode('no se encontraron elementos superpuestos a la geometría de recorte');
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);			
	}
	$Log['tx'][]=utf8_encode($query);

}else{
	$Log['tx'][]='valor para modo_recorte inesperado';
	$Log['res']='err';
	terminar($Log);	
}




$Log['res']='exito';
terminar($Log);
