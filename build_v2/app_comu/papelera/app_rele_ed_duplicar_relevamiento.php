<?php
/**
* 
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrolló sobre una publicación GNU 2017 TReCC SA
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

ini_set('display_errors', 1);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";
include($GeoGecPath.'/includes/encabezado.php');
include($GeoGecPath."/includes/pgqonect.php");

include_once($GeoGecPath."/usuarios/usu_validacion.php");
$Usu= validarUsuario();

$Hoy_a = date("Y");
$Hoy_m = date("m");
$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

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


$minacc=2;
if(isset($_POST['nivelPermiso'])){
    $minacc=$_POST['nivelPermiso'];
}

$Acc=0;

$Accion='app_publ';
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$Accion])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$Accion];
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

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

$Log['data']['id']=$_POST['idcampa'];

if($_POST['idcampa']==='0'){
	$Log['tx'][]='falta idrele';
    $Log['res']='err';
    terminar($Log);
    
}   

//consulta datos del relevamietno de origen de copia
$query="
	SELECT
		id_p_ref_capasgeo
	FROM geogec.ref_rele_campa
	
	WHERE 
		zz_borrada=0
	AND
		id='".$_POST['idcampa']."'
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
$row=pg_fetch_assoc($Consulta);
$Log['data']['capa_fuente']=$row['id_p_ref_capasgeo'];



//duplica campaña de relevamiento
$query="
	INSERT INTO geogec.ref_rele_campa(
		nombre, 
		descripcion, 
		id_p_ref_capasgeo, 
		ic_p_est_02_marcoacademico, 
		fechadesde, fechahasta, 
		usu_autor, 
		representar_campo, 
		representar_val_max, 
		representar_val_min, 
		unidadanalisis
	)
	SELECT
			nombre, 
			descripcion, 
			id_p_ref_capasgeo, 
			ic_p_est_02_marcoacademico, 
			fechadesde, fechahasta, 
			'".$_SESSION["geogec"]["usuario"]['id']."', 
			representar_campo, 
			representar_val_max, 
			representar_val_min, 
			unidadanalisis
	
	FROM geogec.ref_rele_campa
	
	WHERE 
		zz_borrada=0
	AND
		id='".$_POST['idcampa']."'
	AND
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
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
$Log['data']['nidrel']=$row['id'];


 
//duplica capa geográfica de UAs 
$query="
	INSERT INTO geogec.ref_capasgeo(
		autor, 
		nombre, 
		ic_p_est_02_marcoacademico, 
		zz_borrada, 
		descripcion, 
		nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, 
		nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, 
		zz_publicada, srid, sld, tipogeometria, zz_instrucciones, modo_defecto, wms_layer, 
		zz_aux_ind, 
		zz_aux_rele, 
		modo_publica, tipo_fuente, link_capa, link_capa_campo_local, 
		link_capa_campo_externo, fecha_ano, fecha_mes, fecha_dia, 
		cod_col_text1, cod_col_text2, cod_col_text3, cod_col_text4, cod_col_text5, 
		cod_col_num1, cod_col_num2, cod_col_num3, cod_col_num4, cod_col_num5, 
		zz_auto_borra_usu, zz_auto_borra_fechau, zz_cache_extent
	)
	SELECT
		'".$_SESSION["geogec"]["usuario"]['id']."', 
		concat(nombre,' [copia]'), 
		ic_p_est_02_marcoacademico,
		zz_borrada, 
		descripcion, 
		nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, 
		nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, 
		zz_publicada, srid, sld, tipogeometria, zz_instrucciones, modo_defecto, wms_layer, 
		zz_aux_ind, 
		'".$Log['data']['nidrel']."', 
		modo_publica, tipo_fuente, link_capa, link_capa_campo_local, 
		link_capa_campo_externo, fecha_ano, fecha_mes, fecha_dia, cod_col_text1, cod_col_text2, 
		cod_col_text3, cod_col_text4, cod_col_text5, cod_col_num1, cod_col_num2, cod_col_num3, cod_col_num4, cod_col_num5, 
		zz_auto_borra_usu, zz_auto_borra_fechau, zz_cache_extent
	
	FROM 
		geogec.ref_capasgeo
	
	WHERE 
		zz_borrada=0
	AND
		id='".$Log['data']['capa_fuente']."'
	AND
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
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
$Log['data']['nidcapa']=$row['id'];


//duplica registros de capa geográfica de UAs
$query="
	INSERT INTO geogec.ref_capasgeo_registros(
		geom, texto1, texto2, texto3, texto4, texto5, 
		numero1, numero2, numero3, numero4, numero5, 
		geom_point, geom_line, 
		id_ref_capasgeo, 
		zz_auto_crea_usu, zz_auto_crea_fechau, zz_auto_borra_usu, zz_auto_borra_fechau, 
		zz_copia_de	
	)
	SELECT
		geom, 
		texto1, texto2, texto3, texto4, texto5, 
		numero1, numero2, numero3, numero4, numero5, 
		geom_point, geom_line, 
		'".$Log['data']['nidcapa']."', 
		zz_auto_crea_usu, zz_auto_crea_fechau, zz_auto_borra_usu, zz_auto_borra_fechau, 
		id
	FROM
		geogec.ref_capasgeo_registros
	 WHERE
		zz_borrada=0
	 AND
		id_ref_capasgeo = '".$Log['data']['capa_fuente']."'
	 
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



$query="
	SELECT
		id, zz_copia_de 
	FROM 
		geogec.ref_capasgeo_registros
	WHERE
		id_ref_capasgeo = '".$Log['data']['nidcapa']."'
";


$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}	

$georeg_copia_origen=array();
$georeg_origen_copia=array();

while($row=pg_fetch_assoc($Consulta)){
	
	$georeg_copia_origen[$row['id']]=$row['zz_copia_de'];
	$georeg_origen_copia[$row['zz_copia_de']]=$row['id'];
}





//Actualiza id de capa en el relevamiento generado
$query="
	UPDATE 
		geogec.ref_rele_campa
	SET 
		id_p_ref_capasgeo='".$Log['data']['nidcapa']."'
	
	WHERE 
		id='".$Log['data']['nidrel']."'
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
 
 
$query="
 
INSERT INTO geogec.ref_rele_campos(
		id_p_ref_rele_campa, 
		nombre, 
		ayuda, inputattributes, opciones, 
		unidaddemedida, tipo, ic_p_est_02_marcoacademico, orden,
		zz_copia_de
	)
	SELECT
		'".$Log['data']['nidrel']."', 
		concat(nombre,' [copia]'), 
		ayuda, inputattributes, opciones, 
		unidaddemedida, tipo, ic_p_est_02_marcoacademico, orden,
		id
	FROM
		geogec.ref_rele_campos
	WHERE
		zz_borrada='0'
	AND
		id_p_ref_rele_campa='".$_POST['idcampa']."'
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
 
$query="
	SELECT
	id, zz_copia_de 
	FROM 
	geogec.ref_rele_campos
	WHERE
	id_p_ref_rele_campa = '".$Log['data']['nidrel']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}	




$campos_copia_origen=array();
$campos_origen_copia=array();

while($row=pg_fetch_assoc($Consulta)){
	$campos_copia_origen[$row['id']]=$row['zz_copia_de'];
	$campos_origen_copia[$row['zz_copia_de']]=$row['id'];
}

 
 
 
 
 
 
$query="  
	INSERT INTO geogec.ref_rele_registros(
		
		id_p_ref_rele_campa, 
		zz_auto_crea_usu, 
		zz_superado,
		
		col_texto1_dato, 
		col_numero1_dato,
		
		id_p_ref_capas_registros, 
		zz_auto_crea_fechau, 
		zz_archivada, 
		zz_archivada_fecha, 
		zz_copia_de


		
	)
	SELECT
	
		'".$Log['data']['nidrel']."',
		ref_rele_registros.zz_auto_crea_usu, 
		ref_rele_registros.zz_superado, 
		
		ref_rele_registros.col_texto1_dato, 
		ref_rele_registros.col_numero1_dato,
		
		
		ref_capasgeo_registros.id, 
		ref_rele_registros.zz_auto_crea_fechau, 
		ref_rele_registros.zz_archivada, 
		ref_rele_registros.zz_archivada_fecha, 
		ref_rele_registros.id
	
	FROM
		geogec.ref_rele_registros
	LEFT JOIN
		geogec.ref_capasgeo_registros ON ref_capasgeo_registros.zz_copia_de = ref_rele_registros.id_p_ref_capas_registros
	WHERE
		ref_rele_registros.zz_borrado='0'
		AND
		ref_rele_registros.id_p_ref_rele_campa='".$_POST['idcampa']."'
		
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}	


 
$query="   
	INSERT INTO 
		geogec.ref_rele_registros_datos(
			id_p_ref_rele_campa, 
			id_p_ref_rele_campos, 
			id_p_ref_rele_registros, 
			
			data_texto, data_numero, data_documento, 
			ic_p_est_02_marcoacademico, 
			zz_superado
	)
	SELECT
			'".$Log['data']['nidrel']."',
			ref_rele_campos.id,
			ref_rele_registros.id, 
			
			ref_rele_registros_datos.data_texto, ref_rele_registros_datos.data_numero, ref_rele_registros_datos.data_documento, 
			ref_rele_registros_datos.ic_p_est_02_marcoacademico, 
			ref_rele_registros_datos.zz_superado
	FROM
		geogec.ref_rele_registros_datos
	
	LEFT JOIN
		geogec.ref_rele_campos ON ref_rele_campos.zz_copia_de = ref_rele_registros_datos.id_p_ref_rele_campos
		
	LEFT JOIN
		geogec.ref_rele_registros ON ref_rele_registros.zz_copia_de = ref_rele_registros_datos.id_p_ref_rele_registros		
		
	WHERE
		ref_rele_registros_datos.zz_borrada=0
	AND
		ref_rele_registros_datos.id_p_ref_rele_campa='".$_POST['idcampa']."'
		
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
