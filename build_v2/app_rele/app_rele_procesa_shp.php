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
$Usu = validarUsuario(); // en ./usu_validacion.php

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d"); $HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

// Import classes
use Shapefile\ShapefileReader;
use Shapefile\Shapefile;
use Shapefile\ShapefileException;
if (!class_exists('Shapefile\ShapefileAutoloader')) {
    require_once('./terceras_php/php-shapefile.v4.0/src/Shapefile/ShapefileAutoloader.php');
    \Shapefile\ShapefileAutoloader::register();
}


global $PROCESANDO;
$PROCESANDO='si';

$Log2['data']=array();
$Log2['tx']=array();
$Log2['mg']=array();
$Log2['res']='';

function terminar2($Log2){
    $res=json_encode($Log2);
    //print_r($Log2);
    restore_error_handler();
    
    if($res==''){
        echo "err";
        $res=print_r($Log2,true);
    }
    echo $res;
    exit;
}

chdir('./app_rele');
include('./app_rele_upload_validar_shp.php');


set_error_handler(function($errno, $errstr, $errfile, $errline) {
	// Convertir errores en excepciones // ESTO HACE QUE FUNCIONEN LOS TRY Y CAtcH
	throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});


foreach($Log['tx'] as $v){
    $Log2['tx'][]=$v;
}

if($Log['res']=='err'){
    $Log2['tx'][]='error al consultar version';
    $Log2['tx'][]=print_r($Log['tx']['shp'],true);
    $Log2['res']='err';
    terminar2($Log2);
}

/*if($Log['data']['version']['id']!=$_POST['id']){
    $Log2['tx'][]=utf8_encode('error al validar el id de version como ultima versión no publicada para este usuario y esta version');
    $Log2['mg'][]=utf8_encode('se produjo un error de sistema. consulte al administrador. #444521');
    $Log2['res']='err';
    terminar2($Log2);	
}*/
if(!isset($_POST['idcampa'])){
    $Log2['tx'][]='no fue enviada la variable idcampa';
    $Log2['res']='err';
    terminar2($Log2);	
}
print_r($Log['data']['extarchivos']);
$Log2['data']['idcampa']=$_POST['idcampa'];

	if($Log['data']['shp']['stat']!='viable'){
		$Log2['tx'][]='error al validar shapefile';
		$Log2['tx'][]=print_r($Log['data']['shp'],true);
		$Log2['res']='err';
		terminar2($Log2);	
	}

	
	if($Log['data']['prj']['stat']!='viable'&&$Log['data']['prj']['stat']!='viableobs'){
		$Log2['tx'][]='prj stat: '.$Log['data']['prj']['stat'];
		$Log2['tx'][]='error al validar sistema de referencia crs';
		$Log2['tx'][]=print_r($Log['data']['prj'],true);
		$Log2['res']='err';
		terminar2($Log2);	
	}

	if($Log['data']['dbf']['stat']!='viable'){
		$Log2['tx'][]='error al validar campos de la tabla';
		$Log2['tx'][]=print_r($Log['data']['dbf'],true);
		$Log2['res']='err';
		terminar2($Log2);	
	}

	$Log2['tx'][]=print_r($Log['data']['columnas'],true);
	$carga=0;
	$Log['data']['reg_inviables']=0;
	
		echo "GGG";
	while($carga<50000){	
		$_POST['avance']++;
		echo "aaa";
		try {
			$ShapeFile->setCurrentRecord($_POST['avance']);
		} catch (\Shapefile\ShapefileException $e) {
			$Log2['tx'][]='registro inviable: '.$e->getMessage();
			$Log['data']['reg_inviables']++;
			continue;
		}
		echo "bbb";
		echo$_POST['avance'];
		try {
			$reg=$ShapeFile->current();
		} catch (\Shapefile\ShapefileException $e) {
			$Log2['tx'][]='registro inviable: '.$e->getMessage();
			$Log['data']['reg_inviables']++;
			continue;
		}
		
		echo "GGG";
		$carga+=strlen($reg->getWKT());
		
		$campos='';
		$valores='';
		foreach($Log['data']['columnas'] as $tnom => $ttipo){
			
			if($tnom=='id'){continue;}
			if($tnom=='geo'){continue;}
			if($tnom=='id_ref_capasgeo'){continue;}
			if($tnom=='zz_obsoleto'){continue;}
			
			if ( isset($Log['data']['columnasCubiertas'][$tnom]['dbfnom']) ){
				$campos.='"'.$tnom.'", ';
				$nrefdbf=$Log['data']['columnasCubiertas'][$tnom]['dbfnom'];
				$valores.="'".str_replace("'",'\"',$reg->getData($nrefdbf))."', ";    
			}
		}

		$campos=substr($campos,0,-2);
		$valores=substr($valores,0,-2);
		
		$isUTF8 = preg_match('//u', $valores);
		if ($isUTF8 != 0){ //esta encoded en UTF8
			//$Log2['tx'][]='isUTF8: '.$isUTF8.' usando utf8_decode';
			$valores = iconv('UTF-8', 'Windows-1252//TRANSLIT', $valores);
		} else {
			$Log2['res']='error';
			$Log2['mg'][]='El shapefile debe estar guardado usando la codificacion de caracteres UTF-8';
			terminar($Log2);
		}
		
		echo "GGG";
		$geomTX= "ST_GeomFromText('".$reg->getWKT()."',".$Log['data']['prj']['def'].")";
		$geomTX= "ST_Transform(".$geomTX.", 3857)";

	
		try {
			$geotipo=$ShapeFile->getShapeType(Shapefile::FORMAT_STR);
		} catch (\Shapefile\ShapefileException $e) {
			$Log2['tx'][]='registro inviable: '.$e->getMessage();
			$Log['data']['reg_inviables']++;
			continue;
		}
		

		if($geotipo == 'Polygon'){
			$campo_g = 'geom';
		} elseif ($geotipo == 'Point'){
			$campo_g = 'geom_point';
		} elseif ($geotipo == 'LineString'){
			$campo_g = 'geom_line';
		} elseif ($geotipo == 'PolyLine'){
			$campo_g = 'geom_line';
		} else {
			$Log2['res']='error';
			$Log2['mg'][]=utf8_encode('No reconocí el tipo de geometria ShapeType: ').$geotipo;
			terminar2($Log2);
		}

		if($campos!=''){$campos=", ".$campos;}
		if($valores!=''){$valores=", ".$valores;}
		
		$query="INSERT INTO
					$DB.ref_capasgeo_registros(
					".$campo_g.", 
					id_ref_capasgeo
					$campos
				)VALUES(
					".$geomTX.",
					'".$_POST['idcapa']."'
					".$valores."
				)

				RETURNING id;
		";
		//$Log2['tx'][]=$query;

		echo "GGG";
		try {			
			$query_utf8=iconv('Windows-1252', 'UTF-8', $query);			
			$Consulta = pg_query($ConecSIG,$query_utf8);
		} catch (Exception $e) {	
			$error_message = $e->getMessage();
			$Log2['tx'][]=$error_message;
			
			$mgerr='pg_query(): Query failed: ERROR:  transform: Invalid coordinate';
			if(substr($error_message,0,strlen($mgerr))==substr($mgerr,0,strlen($mgerr))){
				$Log2['mg'][]='Error al tranformar las ccoordenadas. Se recomienda verificar les sistema de coordenadas en el formulario';
			}
			$Log2['res']='error';
			terminar2($Log2);
			// Manejar el error
		}
		
		//$Log['tx'][]=$query;
		if(pg_last_error($ConecSIG)!=''){
			$Log2['res']='error';
			$Log2['tx'][]='error al insertar registro en la base de datos';
			$Log2['tx'][]=pg_last_error($ConecSIG);
			$Log2['tx'][]=$query;
			terminar($Log2);
		}			
		$f=pg_fetch_assoc($Consulta);
		$Log2['data']['inserts'][]=$f['id'];
		
		
		
		
			
		$query="
		INSERT INTO 
			$DB.ref_rele_registros(
				id_p_ref_rele_campa, 
				zz_auto_crea_usu, 
				col_texto1_dato, 
				id_p_ref_capas_registros, 
				ic_p_est_02_marcoacademico)
			
			SELECT 
				'".$_POST['idcampa']."', 
				'".$_SESSION[$CU]["usuario"]['id']."', 
				texto1, 
				'".$_POST['idcapa']."', 
				'".$_POST['codMarco']."'
			FROM
				geogec.ref_capasgeo_registros
			
			WHERE
				id_ref_capasgeo = '".$_POST['idcapa']."'
				AND
				id='".$f['id']."'
			
			RETURNING id;
		";

		
		try {			
			$query_utf8=iconv('Windows-1252', 'UTF-8', $query);			
			$Consulta = pg_query($ConecSIG,$query_utf8);
		} catch (Exception $e) {	
			$error_message = $e->getMessage();
			$Log2['tx'][]=$error_message;
			
			$mgerr='pg_query(): Query failed: ERROR:  transform: Invalid coordinate';
			if(substr($error_message,0,strlen($mgerr))==substr($mgerr,0,strlen($mgerr))){
				$Log2['mg'][]='Error al tranformar las ccoordenadas. Se recomienda verificar les sistema de coordenadas en el formulario';
			}
			$Log2['res']='error';
			terminar2($Log2);
			// Manejar el error
		}
		
		//$Log['tx'][]=$query;
		if(pg_last_error($ConecSIG)!=''){
			$Log2['res']='error';
			$Log2['tx'][]='error al insertar registro en la base de datos';
			$Log2['tx'][]=pg_last_error($ConecSIG);
			$Log2['tx'][]=$query;
			terminar($Log2);
		}			
	
		
		
		

		$tot = $ShapeFile->getTotRecords();
		$Log2['data']['avanceP']=round((100/$tot)*$_POST['avance']);

		if($_POST['avance']==$tot){		
			$Log2['tx'][]="se alcanzo la cantidad total de ".$ShapeFile->getTotRecords()." registros";
			
			$carpeta='./documentos/subidas/rele/'.str_pad($_POST['idcampa'],8,"0",STR_PAD_LEFT).'/procesadas';
			if(!file_exists($carpeta)){$Log['tx'][]="creando carpeta $carpeta";mkdir($carpeta, 0777, true);chmod($carpeta, 0777);}			
			$carpetaOrig='./documentos/subidas/rele/'.str_pad($_POST['id'],8,"0",STR_PAD_LEFT);	    
			$v=$Log['data']['extarchivos']['shp'][0]['nom'];
			$e=explode('.',$v);
			$ext=$e[(count($e)-1)];
			$largo=strlen($ext)+1;
			$nom=substr($v,0,-1*$largo);			
			$comando='zip '.$carpeta.'/'.$nom.'.zip '.$carpetaOrig.'/'.$nom.'.*';
			// echo PHP_EOL;echo $comando;echo PHP_EOL; 
			exec($comando,$exec_res);
			$Log2['tx'][]=$comando;
			$Log2['tx'][]='el shapefile utilizado fue comprimido: '.print_r($exec_res,true);
			$a=array_map('unlink', glob($carpetaOrig.'/'.$nom.'.*'));
			$Log2['tx'][]='el shapefile utilizado fue archivado: '.print_r($a,true);
			
			$Log2['data']['avance']='final';
			$Log2['res']='exito';
			terminar2($Log2);
		}
	}

	if($Log['data']['reg_inviables']>0){
		$Log2['mg'][]='identificamos: '. $Log['data']['reg_inviables'].' registros inviables (fueron salteados)';	
	}
	
	$Log2['data']['avance']=$_POST['avance'];	
	$Log2['res']='exito';	
	terminar2($Log2);
