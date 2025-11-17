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



$minacc=2;
if(isset($_POST['nivelPermiso'])){
    $minacc=$_POST['nivelPermiso'];
}

$Acc=0;
$Accion='app_raster';
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
    $Log['mg'][]=utf8_encode('no cuenta con permisos para gerear capas raster. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
    $Log['tx'][]=print_r($Usu,true);
    $Log['res']='err';
    terminar($Log);
}
$idUsuario = $_SESSION[$CU]["usuario"]['id'];

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['idraster'])){
	$Log['tx'][]='no fue enviada la variable idraster';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['modo'])){
	$Log['tx'][]='no fue enviada la variable modo';
	$Log['res']='err';
	terminar($Log);	
}
$Log['data']['modo']=$_POST['modo'];



if(!isset($_POST['iddoc'])){
	$Log['tx'][]='no fue enviada la variable iddoc';
	$Log['res']='err';
	terminar($Log);	
}
$Log['data']['iddoc']=$_POST['iddoc'];


if(!isset($_POST['id_banda'])){//retomauna banda específica
	$_POST['id_banda']=0;
}

$modo_procesar_banda='no';
if($_POST['id_banda']<1){ //definida la banda se limita a procesar esa banda.
	$modo_procesar_banda='si';
}

$modo_descomprimir='no';

if($_POST['idraster']<1){ //si ya hay raster, el doc ya fue descomprimido.
	
	$modo_descomprimir='si';
	
	$query="
		INSERT INTO $DB.ref_raster_coberturas(
			autor, nombre, descripcion, ic_p_est_02_marcoacademico,		
			id_p_ref_01_documentos,
			zz_estado	
		)
		VALUES (
		 '".$idUsuario."', 
		 'nueva', 
		 'nueva', 
		 '".$_POST['codMarco']."',
		 '".$_POST['iddoc']."',
		 'nueva'
		)
		RETURNING id
		";
	$Resultado = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	$row=pg_fetch_assoc($Resultado);
	$_POST['idraster']=$row['id'];
	$Log['data']['nid']=$_POST['idraster'];
	$Log['tx'][]='sea ha creado un nuevo registro en ref_raster_coberturas';
	
}

$query="
	SELECT id, 
	autor, nombre, descripcion, 
	ic_p_est_02_marcoacademico, tipo, zz_borrada, 
	zz_publicada, srid, modo_publica, fecha_ano, 
	fecha_mes, fecha_dia, zz_auto_borra_usu,
	zz_auto_borra_fechau, geom, hora_utc, id_p_ref_01_documentos, 
	zz_data_procesada, id_p_ref_raster_tipos_diccionario,
	zz_estado
	FROM $DB.ref_raster_coberturas
	WHERE
	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	AND
	id = '".$_POST['idraster']."'
";
$Resultado = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
$row=pg_fetch_assoc($Resultado);
$_POST['idraster']=$row['id'];
$Log['data']['nid']=$_POST['idraster'];
$Log['data']['cobertura']=$row;
$Log['data']['cobertura']['bandas']=array();


$carpetaid=str_pad($_POST['idraster'],8,'0',STR_PAD_LEFT);// id carpeta destino auxiliar para EXTRAER ZIP
///$zipfilename ='/var/www/html/geoGEC/documentos/referencias/DATAENTRY2022/ref_01_767_9H8ADeLEwI.zip'; EJEMPLO
$destino = './documentos/auxiliares/raster/'.$carpetaid.'/';// carpeta destino auxiliar para EXTRAER ZIP
$Log['tx'][]='destino: '.$destino;


if($_POST['iddoc']<1){
	
	// No contamos con datos para añadir. terminamos el comando.	
	$Log['tx'][]="no se definio zip con contenido";
	$Log['res']="exito";
	terminar($Log);	
	
	
}else{
	$query="
		SELECT 
			id, id_p_est_02_marcoacademico, zz_borrada, nombre, archivo, id_p_ref_02_pseudocarpetas, orden, 
			descripcion, ic_p_est_02_marcoacademico, zz_protegida, zz_preliminar, zz_auto_crea_usu
		FROM 
			$DB.ref_01_documentos
		WHERE
			id = '".$_POST['iddoc']."'
		AND
			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'	
	";

	$Resultado = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	while ($row=pg_fetch_assoc($Resultado)){
		$zipfilename=$row['archivo'];
		$nombre=$row['nombre'];
	}	
}


if($modo_descomprimir=='si'){
	$comando='unzip '.$zipfilename.' -d '.$destino;
	$Log['tx'][]=$comando;

	$exec_res=array();
	exec($comando,$exec_res);

	$c=0;
	foreach($exec_res as $k => $v){
		$c++;
		if($c>20){break;}
		$Log['tx'][]='dezipeando ['.$k.']: '.$v;
	}
	
	$Log['tx'][]='datos descomprimidos. avance parcial';
	$Log['avance']='10';
	$Log['res']='exito';
	terminar($Log);	
}

//ANALIZANDO CONTENIDO
$dezip=scandir($destino);

foreach($dezip as $c){
	if($c=='.'){continue;}
	if($c=='..'){continue;}
	$Log['tx'][]='carpeta raiz: '.$c;
	$carpetaraiz = $c;
}

if(!isset($carpetaraiz)){
	$Log['tx'][]='No pudo interpretarse el contenido el archivo comrimido';
	$Log['mg'][]='No pudo interpretarse el contenido el archivo comrimido';
	$Log['res']='err';
	terminar($Log);
}

$Tipo='no reconocido';
$Tipo_id=0;

$query="
	SELECT 
		id, nombre, descripcion, url_consulta, nombre_archivo_en_carpeta_raiz as n_a_raiz
	FROM 
		$DB.ref_raster_tipos_diccionario
";
$Resultado = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
$Tipos=array();
while($row=pg_fetch_assoc($Resultado)){	
	$Tipos[$row['id']]=$row;	
}


foreach($Tipos as $idt => $datt){
//DETECCION SENTINEL LEVEL 2A
///encontrar  MTD_MSIL2A.xml (en carpeta única en raiz)

	$filename ='./documentos/auxiliares/raster/'.$carpetaid.'/'.$carpetaraiz.'/'.$datt['n_a_raiz'];
	if(file_exists($filename)){
		$Tipo=$datt;
		$Tipo['bandas']=array();
		$Tipo_id=$idt;
		$Log['tx'][]='tipo encontrado: '.$Tipo['nombre'];
		break;
	}
}

if(!isset($Tipo)){
	$Log['mg'][]='El tipo de contenido raster no pudo ser reconocido';
	$Log['res']='err';
	terminar($Log);	
}	
if($Log['data']['cobertura']['id_p_ref_raster_tipos_diccionario'] > 0){
	if($Tipo_id != $Log['data']['cobertura']['id_p_ref_raster_tipos_diccionario']){
		$Log['mg'][]='El tipo de contenido raster no coincide con su registro existente';
		$Log['res']='err';
		terminar($Log);	
	}
}



$query="
	SELECT 
		id, 
		numero, indice, nombre, descripcion, longitud_central, ancho, resolucion,
		ident_nombrearchivo, subcarpeta
	FROM 
		$DB.ref_raster_tipos_bandas_diccionario
	WHERE
		id_p_ref_raster_tipos_diccionario='".$Tipo_id."'
";
$Resultado = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


while($row=pg_fetch_assoc($Resultado)){
	$Tipo['bandas'][$row['id']]=$row;
	$Tipo['bandas'][$row['id']]['estado']='SD';
	$Tipo['bandas'][$row['id']]['bid']='SD';
}
$Log['data']['cobertura']['bandas']=$Tipo['bandas'];



$query="
	SELECT 
		id as bid, id_p_ref_raster_tipos_bandas_diccionario as id_tipo, estado, 
		anotaciones,  
		tabla
	
	FROM 
		$DB.ref_raster_bandas
	WHERE
		id_p_ref_raster_coberturas = '".$_POST['idraster']."'
		AND
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'	
		AND
		zz_borrada = '0'
";

$Resultado = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

while($row=pg_fetch_assoc($Resultado)){
	if($Tipo['bandas'][$row['id_tipo']]){		
		$Log['tx'][]='error, existente banda no reconocida: '.utf8_encode(print_r($row,true));
		$Log['tx'][]='bandas reconocidas: '.utf8_encode(print_r($Tipo['bandas'],true));
	}
	foreach($row as $k => $v){
		$Log['data']['cobertura']['bandas'][$row['id_tipo']][$k]=$v;	
	}
}


if($Tipo['nombre']=='sentinel-2. nivel: 2A'){
	
	if($Log['data']['cobertura']['zz_estado']=='nueva'){
		
		//PARSEAR XML  
		$contenido = file_get_contents($filename);
		$contenido = str_replace('n1:Geometric_Info','Geometric_Info',$contenido); //NO PUDE LOGRAR parsear elementos con prefijo (n1) dentro de elementos con prefijo (n1)
		$contenido = str_replace('n1:General_Info','General_Info',$contenido); //NO PUDE LOGRAR parsear elementos con prefijo (n1) dentro de elementos con prefijo (n1)
		$data = new SimpleXMLElement($contenido);

		$global_footprint = $data->Geometric_Info->Product_Footprint->Product_Footprint->Global_Footprint->EXT_POS_LIST;

		$v = explode(' ',$global_footprint);

		$par='1';

		$wkt='POLYGON((';
		foreach($v as $k => $val){			
			$par*=-1;	
			if($par=='-1'){
				if(!isset($v[$k+1])){break;}
				$nueva_coor=$v[$k+1].' '.$v[$k].', ';
				$wkt.=$nueva_coor;
			}			
		}
		$wkt=substr($wkt,0,-2);
		$wkt.='))';
		$srid='4326';

		$t=$data->General_Info->Product_Info->PRODUCT_START_TIME;

		$e=explode('T',$t);
		$h=explode('.',$e[1]);
		$f=explode('-',$e[0]);

		$query="
			UPDATE 
				$DB.ref_raster_coberturas
			SET 
				nombre = '".$Tipo['nombre']." ".$f[0]."-region a definir-',
				geom =  ST_GeomFromText('".$wkt."', $srid),
				tipo='".$Tipo['nombre']."',
				id_p_ref_raster_tipos_diccionario='".$Tipo_id."',
				fecha_ano= '".$f[0]."',
				fecha_mes= '".$f[1]."',
				fecha_dia= '".$f[2]."',
				hora_utc = '".$h[0]."',
				id_p_ref_01_documentos = '".$_POST['iddoc']."',
				zz_estado='localizada'
			WHERE id='".$_POST['idraster']."'
		";
		$Resultado = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.utf8_encode($query);
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		$Log['tx'][]="guardados los limites de la cobertura";

	}
	
	
	//BUSCAR ARCHIVOS DE IMAGEN
	$dir_raiz='./documentos/auxiliares/raster/'.$carpetaid.'/';
	$dir_granule='./documentos/auxiliares/raster/'.$carpetaid.'/'.$carpetaraiz.'/GRANULE/';
	$grs=scandir($dir_granule);
	$cant=0;
	foreach($grs as $c){
		if($c=='.'){continue;}
		if($c=='..'){continue;}
		$Log['tx'][]='carpeta en granule: '.$c;
		$cant++;
		$carpeta_engranule = $c;
	}
	if($cant>1){
		$Log['tx'][]='mas de una carpeta en granule';
		$Log['mg'][]=utf8_encode('Atención. Al parecer el zip que está procesando se compone de más de una imagen. Verifiquelo en la carpeta granule, en este momento solo procesamos una imagen por banda por zip');
	}
	if($cant<1){
		$Log['tx'][]='ninguna carpeta en granule';
		$Log['mg'][]=utf8_encode('Atención. Al parecer el zip que está procesando no contiene imágenes en la carpeta granule. Verifiquelo, en este momento solo procesamos imágenes en esa carpeta');
		$Log['res']='err';
		terminar($Log);	
	}

	$dir_img=$dir_granule.$carpeta_engranule.'/IMG_DATA';

	$bn=0;
	foreach($Tipo['bandas'] as $btid => $btdat){
		
		$bn++;
		if($Log['data']['cobertura']['bandas'][$btid]['estado']=='procesado'){continue;}
		
		$Log['tx'][]='nueva banda:'.$btdat['numero'].$btdat['indice'].' '.$btdat['nombre'];
		// ej: $dir_img.'/R10m'
		$dir=scandir($dir_img.'/'.$btdat['subcarpeta']);

		$file_img='';
		foreach($dir as $f){  // EJ: "/R10m/T19FEF_20211215T141051_B02_10m.jp2";
			if($c=='.'){continue;}
			if($c=='..'){continue;}
			$e=explode('_',$f);
			if(!isset($e[2])){continue;}
			if($e[2]==''){continue;}
			
			if($e[2]==$btdat['ident_nombrearchivo']){
				$file_img=$f;
				$Log['tx'][]='archivo raster definido:'.$f;
			}
		}
		

		if(!file_exists($dir_img.'/'.$btdat['subcarpeta'].'/'.$file_img)){
			$Log['tx'][]='No se encontro el archivo de imagen: '.$dir_img.'/'.$btdat['subcarpeta'].'/'.$file_img;
			$Log['mg'][]=utf8_encode('Atención. No se encontró el archivo de imagen en la carpeta esperada:'.$dir_img.'/'.$btdat['subcarpeta'].'/'.$file_img. ' \n Consulte a el/la/le responsable de programación');
			$Log['res']='err';
			terminar($Log);	
		}				
	
		//registra este tipo de banda para esta cobertura
		$query="
		INSERT INTO 
			$DB.ref_raster_bandas(
				id_p_ref_raster_tipos_bandas_diccionario, 
				estado, anotaciones, 
				ic_p_est_02_marcoacademico, 
				id_p_ref_raster_coberturas
			)
			VALUES (
				'".$btid."', 
				'disponible', 
				'archivo localizado',
				'".$_POST['codMarco']."', 
				'".$_POST['idraster']."'
			)
			RETURNING id
		";
		$Resultado = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.utf8_encode($query);
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		$Log['tx'][]="creada banda para esta cobertura";
		$row=pg_fetch_assoc($Resultado);
		$bid=$row['id'];
		
		
		
		
		//CARGAR BANDA en BASE DE DATOS (nueva tabla)

		$file_img_tif=substr($file_img,0,-3)."tif";

		$Log['tx'][]=$file_img_tif;
		$Log['tx'][]=$file_img;
		
		$exec ="gdal_translate -of GTIFF ".$dir_img.'/'.$btdat['subcarpeta'].'/'.$file_img."  ".$dir_img.'/'.$btdat['subcarpeta'].'/'.$file_img_tif;
		$Log['tx'][]='ejecutando: '.$exec;
		
		$exec_res=array();
		exec($exec,$exec_res);

		$c=0;
		foreach($exec_res as $k => $v){
			$c++;
			if($c>20){break;}
			$Log['tx'][]='traduciendo formato .jp2 a .tif ['.$k.']: '.$v;
		}

		$nombretabla='r'.str_pad($_POST['idraster'],8,'0',STR_PAD_LEFT).'_'.$bid;

		$exec="raster2pgsql -c -C -f rast -F -I -M -l 10,100 -t 100x100 ".$dir_img.'/'.$btdat['subcarpeta'].'/'.$file_img_tif." geogec_raster.".$nombretabla." | PGPASSWORD=".$_SESSION["AppSettings"]->DATABASE_PASSWORD." psql -U ".$_SESSION["AppSettings"]->DATABASE_USERNAME." -d www-data -h localhost -p 5432";
		$Log['tx'][]='ejecutando: '.$exec;
		
		
		$exec_res=array();
		exec($exec,$exec_res);

		
		$c=0;
		foreach($exec_res as $k => $v){
			$c++;
			if($c>20){break;}
			$Log['tx'][]='importando tif a base de datos ['.$k.']: '.$v;
		}


	
		$query="
			SELECT EXISTS (
			   SELECT 1 
			   FROM pg_tables
			   WHERE schemaname = 'geogec'
			   AND tablename = '".$nombretabla."'
			) as existe;
		";
		$Resultado=pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.utf8_encode($query);
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		$row=pg_fetch_assoc($Resultado);
		if($row['existe']=='false'){
			$Log['tx'][]='error al incorpporar raster como tabla: '.utf8_encode($nombretabla);
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		
		
		$query="
			ALTER TABLE geogec_raster.".$nombretabla."
			ADD COLUMN id_p_ref_raster_bandas integer;
		";
		pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.utf8_encode($query);
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
			
		$query="
			UPDATE 
				geogec_raster.".$nombretabla."
			SET 
				id_p_ref_raster_bandas = '".$bid."'
			WHERE
				filename='".$file_img_tif."'
		";
		pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.utf8_encode($query);
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		//actualiza estado de registro de banda raster
		$query="
		UPDATE
			$DB.ref_raster_bandas
			SET
				estado='procesado', 
				tabla='".$nombretabla."'
			WHERE 
				id = '".$bid."'
		";
		$Resultado = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.utf8_encode($query);
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		$Log['tx'][]="creada banda para esta cobertura";
		$row=pg_fetch_assoc($Resultado);
		$bid=$row['id'];
		
		
		//BORRA JPG2 original
		$comando='rm -r '.$dir_img.'/'.$btdat['subcarpeta'].'/'.$file_img;
		$Log['tx'][]=$comando;

		$exec_res=array();
		exec($comando,$exec_res);

		$c=0;
		foreach($exec_res as $k => $v){
			$c++;
			if($c>20){break;}
			$Log['tx'][]='eliminando datos de una imagen original en carpeta auxiliar generada: ['.$k.']: '.$v;
		}
		
		
		//BORRA JPG generado
		$comando='rm -r '.$dir_img.'/'.$btdat['subcarpeta'].'/'.$file_img_tif;
		$Log['tx'][]=$comando;

		$exec_res=array();
		exec($comando,$exec_res);

		$c=0;
		foreach($exec_res as $k => $v){
			$c++;
			if($c>20){break;}
			$Log['tx'][]='eliminando datos de imagen traducida en carpeta auxiliar generada: ['.$k.']: '.$v;
		}		
		
		$Log['tx'][]='banda procesada';
		$Log['avance']=(10+$bn);
		$Log['res']='exito';
		terminar($Log);
	}			

	$comando='rm -r '.$dir_img;
	$Log['tx'][]=$comando;

	$exec_res=array();
	exec($comando,$exec_res);

	$c=0;
	foreach($exec_res as $k => $v){
		$c++;
		if($c>20){break;}
		$Log['tx'][]='eliminando datos de imagenes no utilizadas en carpeta auxiliar generada: ['.$k.']: '.$v;
	}
	
	
	$tx_id_r='r'.str_pad($_POST['idraster'],8,'0',STR_PAD_LEFT);
	$dir_metadato=$DIR.'/documentos/raster/'.$tx_id_r;
	if(!file_exists($dir_metadato)){
		mkdir($dir_metadato, 0777, true);
	}
	
	
	$nuevo_zipfilename=$dir_metadato.'/metadatos_'.$tx_id_r.'.zip';
	
	
	chdir($dir_raiz);
	
	$comando='zip -r '.$nuevo_zipfilename.' ./';
	$Log['tx'][]=$comando;


	$exec_res=array();
	exec($comando,$exec_res);

	$c=0;
	foreach($exec_res as $k => $v){
		$c++;
		if($c>20){break;}
		$Log['tx'][]='archivando metadata zipeada: ['.$k.']: '.$v;
	}
	
	chdir($DIR);
	
}else{
	
	$Log['mg'][]=utf8_encode('El tipo de contenido raster parece estar definido en el diccionario pero no tiene preparado un proceamiento. contáctece con el desarrollador web.');
	$Log['res']='err';
	terminar($Log);	
}


$Log['tx'][]='todas las bandas procesadas. avance final';
$Log['avance']='final';
$Log['res']='exito';
terminar($Log);

?>
