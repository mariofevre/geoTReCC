<?php 
/**
*
* aplicación para procesar una versión candidta como definitiva.
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
ini_set('display_errors', 1);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";

if(!isset($_SESSION)) { session_start(); }

require_once($GeoGecPath.'/classes/php-shapefile/src/ShapeFileAutoloader.php');
\ShapeFile\ShapeFileAutoloader::register();
// Import classes
use \ShapeFile\ShapeFile; 
use \ShapeFile\ShapeFileException;


global $PROCESANDO;
$PROCESANDO='si';

$Log2['data']=array();
$Log2['tx']=array();
$Log2['mg']=array();
$Log2['res']='';

function terminar2($Log2){
    $res=json_encode($Log2);
    //print_r($Log2);
    if($res==''){
        echo "err";
        $res=print_r($Log2,true);
    }
    echo $res;
    exit;
}

include($GeoGecPath.'/app_capa/app_capa_validar.php');

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


if($Log['data']['shp']['stat']!='viable'){
    $Log2['tx'][]='error al validar shapefile';
    $Log2['tx'][]=print_r($Log['data']['shp'],true);
    $Log2['res']='err';
    terminar2($Log2);	
}

if($Log['data']['prj']['stat']!='viable'&&$Log['data']['prj']['stat']!='viableobs'){
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

$carga=0;

while($carga<10000){	
    $_POST['avance']++;
    $ShapeFile->setCurrentRecord($_POST['avance']);
    $reg=$ShapeFile->current();

    $carga+=strlen($reg['shp']['wkt']);

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
            
            if (!get_magic_quotes_gpc()){
                $valores.="'".addslashes(str_replace("`","'",$reg['dbf'][$nrefdbf]))."', ";
            } else {
                $valores.="'".str_replace("`","'",$reg['dbf'][$nrefdbf])."', ";    
            }
            //$valores.="'".str_replace("'","&#39;",$reg['dbf'][$nrefdbf])."', ";
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

    $geomTX= "ST_GeomFromText('".$reg['shp']['wkt']."',".$Log['data']['prj']['def'].")";
    $geomTX= "ST_Transform(".$geomTX.", 3857)";

    if($ShapeFile->getShapeType(Shapefile::FORMAT_STR) == 'Polygon'){
        $campo_g = 'geom';
    } elseif ($ShapeFile->getShapeType(Shapefile::FORMAT_STR) == 'Point'){
        $campo_g = 'geom_point';
    } elseif ($ShapeFile->getShapeType(Shapefile::FORMAT_STR) == 'LineString'){
        $campo_g = 'geom_line';
    } else {
        $Log2['res']='error';
        $Log2['mg'][]='No reconci el tipo de geometria ShapeType: '.$ShapeFile->getShapeType(Shapefile::FORMAT_STR);
        terminar($Log2);
    }

    $query="INSERT INTO 
                    geogec.ref_capasgeo_registros(
                        ".$campo_g.", 
                        id_ref_capasgeo,
                        $campos
                    )
            VALUES (
                        ".$geomTX.",
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
        $Log2['tx'][]=$query;
        terminar($Log2);
    }	
    $f=pg_fetch_assoc($Consulta);
    $Log2['data']['inserts'][]=$f['id'];

    $tot = $ShapeFile->getTotRecords();
    $Log2['data']['avanceP']=round((100/$tot)*$_POST['avance']);

    if($_POST['avance']==$tot){		
        $Log2['tx'][]="se alcanzo la cantidad total de ".$ShapeFile->getTotRecords()." registros";
        $Log2['data']['avance']='final';
        $Log2['res']='exito';
        terminar2($Log2);
    }
}

$Log2['data']['avance']=$_POST['avance'];	
$Log2['res']='exito';	
terminar2($Log2);
