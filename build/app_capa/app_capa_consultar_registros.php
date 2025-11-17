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

if(!isset($_POST['idcapa'])){
	$Log['tx'][]='no fue enviada la variable idcapa';
	$Log['res']='err';
	terminar($Log);	
}

$idUsuario = $_SESSION[$CU]["usuario"]['id'];

if(!isset($_POST['modo'])){
	$_POST['modo']='normal';
	//modo = forzado, arroja todos los registros sin importar el peso.
}

$Log['data']['idcapa']=$_POST['idcapa'];
$Log['data']['registros']=array();


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
		$DB.ref_capasgeo as c
	
	WHERE 
		id = '".$_POST['idcapa']."'
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
$ref_campos=array();;
$campos='';
$fila=pg_fetch_assoc($Consulta);
foreach($fila as $k => $v){
	if($v==''){continue;}
	if(substr($k,0,8)=='nom_col_'){
		$campo=str_replace('nom_col_', '', $k);
		$campo=str_replace('text', 'texto', $campo);
		$campo=str_replace('num', 'numero', $campo);
		$campos.=' r.'.$campo.', ';
		
		$ref_campos[$k]=$campo;
		
	}
}   
//$campos=substr($campos,0,-2);

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


$lim_base=150000;

$campogeom='';


$tipogeom=$fila['tipogeometria'];
if(
	$tipogeom=='Polygon'
	||
	$tipogeom=='LineString'
	||
	$tipogeom=='Point'
	){
		
	$fuentegeometria='local';
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
		
		$fuentegeometria='externa_capa';
			
		$query="
			SELECT 
				c.id, c.autor, c.nombre, 
				c.ic_p_est_02_marcoacademico,
				c.tipogeometria, c.zz_instrucciones
				
			FROM 
				$DB.ref_capasgeo as c
			
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
		$tipogeom=$link['tipogeometria'];
		
	}elseif(
		$fila['link_capa']=='-1'
		&&
		$fila['link_capa_campo_local']!=''
		&&
		$fila['link_capa_campo_externo']!=''	
	){
				
		$fuentegeometria='externa_est01';
		$tipogeom='Polygon';
	}

}else{
	$Log['tx'][]= "No se encontraron registros para esta capa.";
	$Log['tx'][]= utf8_encode("Esta capa aún no cuenta con un tipo de geometría definida.");    
	$Log['res']="exito";
	terminar($Log);
}


$query="SELECT  count(*)
        FROM    
            $DB.ref_capasgeo_registros as r
        WHERE 
			zz_borrada='0'
			AND
  			id_ref_capasgeo = '".$_POST['idcapa']."'
	";
$Consulta = pg_query($ConecSIG, $query);
 //  echo $query;
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.utf8_encode($query);
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
$countfila=pg_fetch_assoc($Consulta);
$Log['data']['cant_reg']=$countfila['count'];



$mag=1;
if(
	$tipogeom=='Point'
){
	$campogeom='geom_point';
	$mag=1;
}


if(
	$tipogeom=='Line'
	||
	$tipogeom=='LineString'
){
	$campogeom='geom_line';
	$mag=4;
}

if(
	$tipogeom=='Polygon'
){
	$campogeom='geom';
	$mag=16;//valor para pnderar preliminarmente el peso de la información
}

$Lim='';
if($_POST['modo']!='forzado'){
	$Lim= "LIMIT ".(2*$lim_base/$mag);
}

$query="";




if(!isset($_POST['RecorteDeTrabajo'])){$_POST['RecorteDeTrabajo']='';}

if($fuentegeometria=='local'){
	
	if($_POST['RecorteDeTrabajo']!=''){
		$c=$_POST['RecorteDeTrabajo'];
		$whererecorte="AND ST_Intersects(r.".$campogeom.", 'SRID=3857;POLYGON(($c[0] $c[1], $c[2] $c[1], $c[2] $c[3], $c[0] $c[3], $c[0] $c[1]))')";
	}else{
		$whererecorte='';
	}
	
	$query="SELECT  
            r.id,
            ".$campos."
            ST_AsText(ST_SnapToGrid(r.".$campogeom.",0.01)) as geotx
            
                
        FROM    
            $DB.ref_capasgeo_registros as r
        WHERE 
  			r.zz_borrada='0'
  			AND
  			id_ref_capasgeo = '".$_POST['idcapa']."'
  			$whererecorte
  		$Lim
	";
 
}elseif($fuentegeometria=='externa_capa'){

	if($_POST['RecorteDeTrabajo']!=''){
		$c=$_POST['RecorteDeTrabajo'];
		$whererecorte="AND ST_Intersects(lr.".$campogeom.", 'SRID=3857;POLYGON(($c[0] $c[1], $c[2] $c[1], $c[2] $c[3], $c[0] $c[3], $c[0] $c[1]))')";
	}else{
		$whererecorte='';
	}
	
	$query="SELECT  
            r.id,
            ".$campos."
            ST_AsText(ST_SnapToGrid(lr.".$campogeom.",0.01)) as geotx
                
        FROM    
           $DB.ref_capasgeo_registros as r
           FULL OUTER JOIN
           $DB.ref_capasgeo_registros as lr 
				ON lr.".nombre_a_campo($fila['link_capa_campo_externo'])." = r.".$ref_campos[$fila['link_capa_campo_local']]." 
				
				AND r.id_ref_capasgeo = '".$_POST['idcapa']."'
        WHERE 
  			r.zz_borrada='0'
  			AND
  			lr.id_ref_capasgeo = '".$fila['link_capa']."'
  			$whererecorte
  		$Lim
 ";


	
}elseif($fuentegeometria=='externa_est01'){

	$campogeom='geo';
	
	$query='SELECT  
            r.id,
            '.$campos.'
            ST_AsText(ST_SnapToGrid(lr."'.$campogeom.'",0.01)) as geotx
                
        FROM    
           $DB.ref_capasgeo_registros as r
           LEFT JOIN
          
           $DB.est_01_municipios as lr ON lr."'.$fila['link_capa_campo_externo'].'" = r.'.$ref_campos[$fila['link_capa_campo_local']].'
        WHERE 
			r.zz_borrada=\'0\'
			AND
  			r.id_ref_capasgeo = \''.$_POST['idcapa'].'\' 
  			
  		'.$Lim;
	
}

	
if($query!=''){
	$Consulta = pg_query($ConecSIG, $query);
	$Log['tx'][]='query: '.utf8_encode($query);
	 //  echo $query;
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.utf8_encode(pg_errormessage($ConecSIG));
		$Log['tx'][]='query: '.utf8_encode($query);
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
}else{
		
		$Log['mg'][]=utf8_encode('error en la configuración de vinculacion a otras capas');
		$Log['res']='err';
		terminar($Log);	
		
}




if (pg_num_rows($Consulta) <= 0){
    $Log['tx'][]= "No se encontraron registros para esta capa.";
    
} else {
	
	$Log['tx'][]= "Registros cargados:: ".pg_num_rows($Consulta);
    $Log['tx'][]= "Consulta de capa existente id: ".$_POST['idcapa'];
	
	
	if((pg_num_rows($Consulta)*$mag)>100000&&$_POST['modo']!='forzado'){
		$Log['tx'][]= utf8_encode("La cantidad de registros supera el límite de seguridad:: ".pg_num_rows($Consulta));
	    $Log['tx'][]= utf8_encode("Abortamos el envío de registros");
	    $Log['mg'][]= utf8_encode("Dado el gran tamaño, enviamos solo una muestra. Recomendamos activar el modo wms para esta capa");
		$max=2000;	
		if($_POST['RecorteDeTrabajo']!=''){
			$max=3000;	
		}
	}
	
	$c=0;
    while ($fila=pg_fetch_assoc($Consulta)){
		$id=$fila['id'];
		if($fila['id']==null||$fila['id']==''){
			$id='aux'.$c;
		}
		$Log['data']['registros'][$id]=$fila;
		$c++;
		
    	if(isset($max)){
    		if($c>$max){
    			break;
    		}
		}
    }
}

$Log['res']="exito";
terminar($Log);
