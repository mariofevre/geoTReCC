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

if (!class_exists('Shapefile\ShapefileAutoloader')) {
    require_once('./external/php-shapefile.v4.0/src/Shapefile/ShapefileAutoloader.php');
    \Shapefile\ShapefileAutoloader::register();
}

// Import classes
use \Shapefile\Shapefile; 
use \Shapefile\ShapefileException;


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


include('./app_capa/app_capa_validar.php');


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
if(!isset($_POST['id'])){
    $Log2['tx'][]='no fue enviada la variable id';
    $Log2['res']='err';
    terminar2($Log2);	
}

$Log2['data']['id']=$_POST['id'];

if($Log['data']['version']['tipogeometria']!='Tabla'){

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

	while($carga<50000){	
		$_POST['avance']++;
		$ShapeFile->setCurrentRecord($_POST['avance']);
		$reg=$ShapeFile->current();

		$carga+=strlen($reg->getWKT());

		//print_r($reg['shp']['wkt']);
		/*print_r($reg['dbf']);		 
		foreach($reg['dbf'] as $k => $v){
				echo utf8_decode($v)."<br>";
		}*/

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
				//echo $nrefdbf .' -- '.$reg['dbf'][$nrefdbf]." | ";
				/*
				if (!get_magic_quotes_gpc()){
					$valores.="'".addslashes(str_replace("`","'",$reg['dbf'][$nrefdbf]))."', ";
				} else {
					$valores.="'".str_replace("'",'\"',$reg['dbf'][$nrefdbf])."', ";    
				}*/
				
				$valores.="'".str_replace("'",'\"',$reg->getData($nrefdbf))."', ";    
				//$valores.="'".str_replace("'","&#39;",$reg['dbf'][$nrefdbf])."', ";
			}
		}

		$campos=substr($campos,0,-2);
		$valores=substr($valores,0,-2);
		
		//$Log2['tx'][]='get_magic_quotes_gpc: '.((get_magic_quotes_gpc()) ? 'true' : 'false');
		$isUTF8 = preg_match('//u', $valores);
		if ($isUTF8 != 0){ //esta encoded en UTF8
			//$Log2['tx'][]='isUTF8: '.$isUTF8.' usando utf8_decode';
			$valores = iconv('UTF-8', 'Windows-1252//TRANSLIT', $valores);
		} else {
			$Log2['res']='error';
			$Log2['mg'][]='El shapefile debe estar guardado usando la codificacion de caracteres UTF-8';
			terminar($Log2);
		}

		$geomTX= "ST_GeomFromText('".$reg->getWKT()."',".$Log['data']['prj']['def'].")";
		$geomTX= "ST_Transform(".$geomTX.", 3857)";


		if($ShapeFile->getShapeType(Shapefile::FORMAT_STR) == 'Polygon'){
			$campo_g = 'geom';
		} elseif ($ShapeFile->getShapeType(Shapefile::FORMAT_STR) == 'Point'){
			$campo_g = 'geom_point';
		} elseif ($ShapeFile->getShapeType(Shapefile::FORMAT_STR) == 'LineString'){
			$campo_g = 'geom_line';
		} elseif ($ShapeFile->getShapeType(Shapefile::FORMAT_STR) == 'PolyLine'){
			$campo_g = 'geom_line';
		} else {
			$Log2['res']='error';
			$Log2['mg'][]=utf8_encode('No reconocí el tipo de geometria ShapeType: ').$ShapeFile->getShapeType(Shapefile::FORMAT_STR);
			terminar2($Log2);
		}

		if($campos!=''){$campos=", ".$campos;}
		if($valores!=''){$valores=", ".$valores;}
		
		$query="INSERT INTO
						$DB.ref_capasgeo_registros(
							".$campo_g.", 
							id_ref_capasgeo
							$campos
						)
				VALUES (
							".$geomTX.",
							'".$_POST['id']."'
							".$valores."
						)

				RETURNING id;
		";
		//$Log2['tx'][]=$query;


		
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

		$tot = $ShapeFile->getTotRecords();
		$Log2['data']['avanceP']=round((100/$tot)*$_POST['avance']);

		if($_POST['avance']==$tot){		
			$Log2['tx'][]="se alcanzo la cantidad total de ".$ShapeFile->getTotRecords()." registros";
			
			$carpeta='./documentos/subidas/capa/'.str_pad($_POST['id'],8,"0",STR_PAD_LEFT).'/procesadas';
			if(!file_exists($carpeta)){$Log['tx'][]="creando carpeta $carpeta";mkdir($carpeta, 0777, true);chmod($carpeta, 0777);}			
			$carpetaOrig='./documentos/subidas/capa/'.str_pad($_POST['id'],8,"0",STR_PAD_LEFT);	    
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

	$Log2['data']['avance']=$_POST['avance'];	
	$Log2['res']='exito';	
	terminar2($Log2);


}else{
	
	if($Log['data']['xlsx']['stat']!='viable'){
		$Log2['tx'][]='error al validar campos de la tabla';
		$Log2['tx'][]=print_r($Log['data']['xlsx'],true);
		$Log2['res']='err';
		terminar2($Log2);	
	}
	
		
	$carga=0;
	
	while($carga<50000){	
		$_POST['avance']++;
		
		$reg=$Filas[$_POST['avance']];

		$campos='';
		$valores='';
		$Log2['tx'][]=print_r($reg,true);
		$Log2['tx'][]=print_r($Log['data']['columnasCubiertas'],true);
		$Log2['tx'][]=print_r($Columnas,true);
		$Log2['tx'][]=print_r($reg,true);
		$Log2['data']['carga']=$carga;
		foreach($Log['data']['columnas'] as $tnom => $ttipo){
			if($tnom=='id'){continue;}
			if($tnom=='geo'){continue;}
			if($tnom=='id_ref_capasgeo'){continue;}
			if($tnom=='zz_obsoleto'){continue;}
			
			
			if (isset($Log['data']['columnasCubiertas'][$tnom]['dbfnom'])){
				$campos.='"'.$tnom.'", ';
				$nrefdbf=$Log['data']['columnasCubiertas'][$tnom]['dbfnom'];
				$Log2['tx'][]=$nrefdbf;
				$valores.="'".str_replace("'",'\"',$reg[$Columnas[$nrefdbf]])."', ";    
			}
		}

		$campos=substr($campos,0,-2);
		$valores=substr($valores,0,-2);
		
		//$Log2['tx'][]='get_magic_quotes_gpc: '.((get_magic_quotes_gpc()) ? 'true' : 'false');
		$isUTF8 = preg_match('//u', $valores);
		if ($isUTF8 != 0){ //esta encoded en UTF8
			//$Log2['tx'][]='isUTF8: '.$isUTF8.' usando utf8_decode';
			$valores = utf8_decode($valores);
		} else {
			$Log2['res']='error';
			$Log2['mg'][]='El shapefile debe estar guardado usando la codificacion de caracteres UTF-8';
			terminar($Log2);
		}


		$query="INSERT INTO 
						$DB.ref_capasgeo_registros(
							id_ref_capasgeo,
							$campos
						)
				VALUES (
							'".$_POST['id']."',
							".$valores."
						)
				RETURNING id;
		";
		//$Log2['tx'][]=$query;
		$Consulta = pg_query($ConecSIG,utf8_encode($query));
		//$Log['tx'][]=$query;
		if(pg_errormessage($ConecSIG)!=''){
			$Log2['res']='error';
			$Log2['tx'][]='error al insertar registro en la base de datos';
			$Log2['tx'][]=pg_errormessage($ConecSIG);
			$Log2['mg'][]='error al insertar registro en la base de datos';
			$Log2['mg'][]=pg_errormessage($ConecSIG);
			$Log2['tx'][]=$query;
			terminar2($Log2);
		}	
		$f=pg_fetch_assoc($Consulta);
		$Log2['data']['inserts'][]=$f['id'];

		$tot = count($Filas)-1;
		$Log2['data']['avanceP']=round((100/$tot)*$_POST['avance']);

		if($_POST['avance']==$tot){		
			$Log2['tx'][]="se alcanzo la cantidad total de ".(count($Filas)-1)." registros";
			$Log2['data']['avance']='final';
			$Log2['res']='exito';
			terminar2($Log2);
		}
	}
	

	$Log2['data']['avance']=$_POST['avance'];	
	$Log2['res']='exito';	
	terminar2($Log2);
	
}

