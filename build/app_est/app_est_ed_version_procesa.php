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

include('./app_est/app_est_ed_version_crea.php');

foreach($Log['tx'] as $v){
	$Log2['tx'][]=$v;
}

if($Log['data']['res']=='err'){
	$Log2['tx'][]='error al consultar version';
	$Log2['tx'][]=print_r($Log['tx']['shp'],true);
	$Log2['res']='err';
	terminar2($Log2);
}


if($Log['data']['version']['id']!=$_POST['id']){
	$Log2['tx'][]=utf8_encode('error al validar el id de version como ultima versión no publicada para este usuario y esta version');
	$Log2['mg'][]=utf8_encode('se produjo un error de sistema. consulte al administrador. #444521');
	$Log2['res']='err';
	terminar2($Log2);	
}


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


if($_POST['avance']==='0'){
	
	
	foreach($instrucc as $k => $v){
	
		if($v['acc']=='crear'){
			
			if(isset($Log['data']['columnas'][$v['nom']])){
				$Log2['tx'][]='no se pudo crear esta columna. ya existia';
				$Log2['res']='err';
				terminar2($Log2);		
			}
			
			
			$type="character varying";			
			foreach($Log['data']['dbf']['campos'] as $dat){
			
				if($dat['nom']==$k&&$dat['type']=='N'){
					$type="numeric";
				}			
			}
			
			
			$query ="			
				ALTER TABLE 
					$DB.".$_POST['tabla']."
			  	ADD COLUMN 
			  		\"".$v['nom']."\" ".$type."
			  ";
			
			
			$ConsultaVer = pg_query($ConecSIG, $query);
			if(pg_errormessage($ConecSIG)!=''){
				$Log2['tx'][]='error: '.pg_errormessage($ConecSIG);
				$Log2['tx'][]='query: '.$query;
				$Log2['mg'][]='error interno';
				$Log2['res']='err';
				terminar2($Log2);	
			}
										
		}
		
	}
	
}





$carga=0;

while($carga<1000000){	
	
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
		if($tnom=='id_sis_versiones'){continue;}
		if($tnom=='zz_obsoleto'){continue;}
		$campos.='"'.$tnom.'", ';
		
		$nrefdbf=$Log['data']['columnasCubiertas'][$tnom]['dbfnom'];
		//echo $nrefdbf .' -- '.$reg['dbf'][$nrefdbf]." | ";
		$valores.="'".$reg['dbf'][$nrefdbf]."', ";
		
	}
	
	$campos=substr($campos,0,-2);
	$valores=substr($valores,0,-2);
	
	$geomTX= "ST_GeomFromText('".$reg['shp']['wkt']."',".$Log['data']['prj']['def'].")";
	$geomTX= "ST_Transform(".$geomTX.", 3857)";
	
	
	
	

			
	$query="
		INSERT INTO 
			$DB.".$_POST['tabla']."(
				geo, 
				id_sis_versiones,
				$campos
			)
	    	VALUES (
	    		".$geomTX.",
	    		'".$_POST['id']."',
	    		$valores	    		
	    	)
	    	
	    RETURNING id;
	";
	//$Log2['tx'][]=$query;
	$Consulta = pg_query($ConecSIG,$query);
	//$Log['tx'][]=$query;
	if(pg_errormessage($ConecSIG)!=''){
		$Log2['res']='error';
		$Log2['tx'][]='error al insertar registro en la base de datos';
		$Log2['tx'][]=pg_errormessage($ConecSIG);
		$Log2['tx'][]=$query;
		terminar($Log);	
	}	
	$f=pg_fetch_assoc($Consulta);
	$Log2['data']['inserts'][]=$f['id'];
	
	$Log2['data']['avanceP']=round((100/$tot)*$_POST['avance']);	
	$tot = $ShapeFile->getTotRecords();
	
	if($_POST['avance']==$tot){
		
		$query="
			UPDATE			
				$DB.".$_POST['tabla']."
			SET
				zz_obsoleto ='1'
			WHERE
				id_sis_versiones != '".$_POST['id']."'		    
		";
		$Consulta = pg_query($ConecSIG,$query);
			//$Log['tx'][]=$query;
		if(pg_errormessage($ConecSIG)!=''){
			$Log2['res']='error';
			$Log2['tx'][]='error al identificar como obsoletos, registros anteriores';
			$Log2['tx'][]=pg_errormessage($ConecSIG);
			$Log2['tx'][]=$query;
			terminar($Log);	
		}			
		
		$query="
			UPDATE			
				$DB.sis_versiones
			SET
				zz_obsoleto ='1'
			WHERE
				tabla='".$_POST['tabla']."'
			AND
				id != '".$_POST['id']."'		    
		";
		$Consulta = pg_query($ConecSIG,$query);
			//$Log['tx'][]=$query;
		if(pg_errormessage($ConecSIG)!=''){
			$Log2['res']='error';
			$Log2['tx'][]='error al identificar como obsoletas, versiones anteriores';
			$Log2['tx'][]=pg_errormessage($ConecSIG);
			$Log2['tx'][]=$query;
			terminar($Log);	
		}			

		
		$query="
			UPDATE			
				$DB.sis_versiones
			SET
				zz_publicada='1'
			WHERE
				tabla = '".$_POST['tabla']."'
			AND
				id = '".$_POST['id']."'		    
		";
		$Consulta = pg_query($ConecSIG,$query);
			//$Log['tx'][]=$query;
		if(pg_errormessage($ConecSIG)!=''){
			$Log2['res']='error';
			$Log2['tx'][]='error al identificar como obsoletas, versiones anteriores';
			$Log2['tx'][]=pg_errormessage($ConecSIG);
			$Log2['tx'][]=$query;
			terminar($Log);	
		}			
			
							
		
		$Log2['tx'][]="se alcanzo la cantidad total de ".$ShapeFile->getTotRecords()." registros";
		$Log2['data']['avance']='final';
		$Log2['res']='exito';	
		terminar2($Log2);
	}
	
	
}

$Log2['data']['avance']=$_POST['avance'];	
$Log2['res']='exito';	
terminar2($Log2);

?>
