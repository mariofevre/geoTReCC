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

if(!isset($_POST['id']) || $_POST['id']<1){
	$Log['res']='error';
	$Log['tx'][]='falta id de capa';	
	terminar($Log);
}
if(!isset($_POST['wms_layer'])){
	$Log['res']='error';
	$Log['tx'][]='falta nombre vista wms a consultar';	
	terminar($Log);	
}



$query="SELECT  *
        FROM    $DB.ref_capasgeo
        WHERE 
  		id='".$_POST['id']."'
  	AND
 	 	zz_borrada = '0'
  	AND
  		autor = '".$idUsuario."'
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


if($fila==null){
	$Log['tx'][]='no se encontró la vcapa válida solicirtada';
	$Log['res']='err';
	terminar($Log);	
}

if($fila['zz_borrada']=='1'){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='esta capa figura como borrada. no puede proseguir';
	$Log['res']='err';
	terminar($Log);	
}

	
$query="
	SELECT 
		id, autor, nombre, ic_p_est_02_marcoacademico, zz_borrada, descripcion, nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, zz_publicada, srid, sld, tipogeometria, zz_instrucciones
	FROM 
		$DB.ref_capasgeo
	WHERE 
		id='".$_POST['id']."'
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
$campos='';
$fila=pg_fetch_assoc($Consulta);

foreach($fila as $k => $v){
	$Log['data']['capa'][$k]=utf8_encode($v);		
}

$campogeom='geom';
if($fila['tipogeometria']=='Point'){$campogeom='geom_point';}
if(	$fila['tipogeometria']=='Line'){$campogeom='geom_line';}

if(strpos($_POST['wms_layer'],":")!==false){
	$c=explode(':',	$_POST['wms_layer']);
	$capawms=$c[1];		
}else{
	$capawms=$_POST['wms_layer'];
}


$query = "UPDATE
                 $DB.ref_capasgeo
          SET    
                 wms_layer = '".$capawms."'
          WHERE
                 ref_capasgeo.id = '".$_POST['id']."'
          AND
                 ref_capasgeo.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
          AND
                 ref_capasgeo.autor='".$idUsuario."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

if($_POST['wms_layer']==''){
	$Log['tx'][]=utf8_encode("se apagó la publicación wms de esta capa");
	$Log['res']="exito";
	terminar($Log);
}


if($capawms!="v_capas_registros_capa_".$_POST['id']){
	$Log['tx'][]=utf8_encode("falla en la seguridad, nombre de vista wms improbable: (".$capawms."!="."v_capas_registros_capa_".$_POST['id'].")");
	$Log['res']="err";
	terminar($Log);
}


$query = "
DROP VIEW IF EXISTS $DB.".$capawms."
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
        $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
        $Log['tx'][]='query: '.$query;
        $Log['mg'][]='error interno';
        $Log['res']='err';
        terminar($Log);	
}



$query = "
CREATE OR REPLACE VIEW $DB.".$capawms." AS
 SELECT ref_capasgeo_registros.id,
    ref_capasgeo_registros.texto1,
    ref_capasgeo_registros.texto2,
    ref_capasgeo_registros.texto3,
    ref_capasgeo_registros.texto4,
    ref_capasgeo_registros.texto5,
    ref_capasgeo_registros.numero1,
    ref_capasgeo_registros.numero2,
    ref_capasgeo_registros.numero3,
    ref_capasgeo_registros.numero4,
    ref_capasgeo_registros.numero5,
    ref_capasgeo_registros.".$campogeom." AS geom,
    ref_capasgeo_registros.id_ref_capasgeo
   FROM $DB.ref_capasgeo_registros
  WHERE ref_capasgeo_registros.id_ref_capasgeo = ".$_POST['id'].";
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
	ALTER VIEW $DB.".$capawms."
	OWNER TO general
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
        $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
        $Log['tx'][]='query: '.$query;
        $Log['mg'][]='error interno';
        $Log['res']='err';
        terminar($Log);	
}




//$_POST['capa_ver']='014_v001';

// Open log file
$logfh = fopen($GeoGecPath."/app_capa/geoserver/GeoserverPHP.log", 'w') or die("can't open log file");


$Log['tx'][]='curl de consulta iniciado';


$service = "http://190.111.246.33:8080/geoserver/"; 
$request = "rest/workspaces/geoGEC/layers.json"; 
$url = $service . $request;
$ch = curl_init($url);

// Optional settings for debugging
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, $logfh); // logs curl messages
$passwordStr = "general:mostaza"; // replace with your username:password
curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
$buffer = curl_exec($ch); // Execute the curl request
curl_close($ch); // free resources if curl handle will not be reused
fclose($logfh);  // close logfile

$capas=json_decode($buffer, true); //el parametro true fuerza la salida como array, no stdClass
$Log['tx'][]='curl consulta ejecutado';
$elmiinarantes='no';
$salteracreacion='no';

foreach($capas['layers']['layer'] as $layer){
	if($layer['name']==$capawms){
		$Log['tx'][]=utf8_encode('la capa ya esta publicada en el servidor wms, se saltea la creación de una nueva publicación de capa');
		$Log['data']['creacionWMS']='exito';// fue creada en el pasado pero al parecer no fue registrado
		//$elmiinarantes='si';
		$salteracreacion='si';
	}
}


//consultar geometría
$query="
	SELECT 
		ST_Extent(".$campogeom.") as bextent,
		ST_Extent(ST_Transform(".$campogeom.",4326)) as bextentg
		
		FROM
			$DB.ref_capasgeo_registros
	WHERE 
		ref_capasgeo_registros.id_ref_capasgeo = ".$_POST['id'].";
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


if($fila['bextent']==''){
	$Log['tx'][]='sin geometria accesible para epublica en wms';
	$Log['res']="exito";
	terminar($Log);
}



$Log['tx'][]='bextent: '.$fila['bextent'];

$coords=substr($fila['bextent'],4,-1);
$Log['tx'][]='coord: '.$coords;
$co=explode(',',$coords);
$c=explode(' ',$co[0]);
$xmin=$c[0];
$ymin=$c[1];
$c=explode(' ',$co[1]);
$xmax=$c[0];
$ymax=$c[1];



$query = "UPDATE
                 $DB.ref_capasgeo
          SET    
                 zz_cache_extent = '".json_encode($coords)."'
          WHERE
                 ref_capasgeo.id = '".$_POST['id']."'
          AND
                 ref_capasgeo.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
          AND
                 ref_capasgeo.autor='".$idUsuario."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}




$coords=substr($fila['bextentg'],4,-1);
$co=explode(',',$coords);
$c=explode(' ',$co[0]);
$gxmin=$c[0];
$gymin=$c[1];
$c=explode(' ',$co[1]);
$gxmax=$c[0];
$gymax=$c[1];







if($salteracreacion=="no"){
		/////////////////CREAR CAPA

		// Abre acrvhio log
		$logfh = fopen($GeoGecPath."/app_capa/geoserver/GeoserverPHP.log", 'w') or die("can't open log file");

		// Initiate cURL session
		$service = "http://190.111.246.33:8080/geoserver/"; // replace with your URL
		$request = "rest/workspaces"; // to add a new workspace
		$url = $service . $request;
		$url.="/geoGEC/datastores/geogec/featuretypes";
		$ch = curl_init($url); 
		$Log['tx'][]='curl de creacion iniciado';

		// Optional settings for debugging
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_STDERR, $logfh); // logs curl messages

		//Required POST request settings
		curl_setopt($ch, CURLOPT_POST, True);
		$passwordStr = "general:mostaza";
		curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);


		curl_setopt($ch, CURLOPT_HTTPHEADER,
				array("Content-type: application/xml"));
				$xmlStr = '
		<featureType>
		  <name>'.$capawms.'</name>
		  <nativeName>'.$capawms.'</nativeName>
		  <namespace>
			<name>geoGEC</name>						 
			<atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="alternate" href="'.$service.'rest/namespaces/geoGEC.xml" type="application/xml"/>
		  </namespace>
		  <title>'.$capawms.'</title>
		  <keywords>
			<string>features</string>
			<string>'.$capawms.'</string>
		  </keywords>
		  <srs>EPSG:3857</srs>
		  <nativeBoundingBox>
			<minx>'.$xmin.'</minx>
			<maxx>'.$xmax.'</maxx>
			<miny>'.$ymin.'</miny>
			<maxy>'.$ymax.'</maxy>
		  </nativeBoundingBox>
		  <latLonBoundingBox>
			<minx>'.$gxmin.'</minx>
			<maxx>'.$gxmax.'</maxx>
			<miny>'.$gymin.'</miny>
			<maxy>'.$gymax.'</maxy>
			<crs>GEOGCS[&quot;WGS84(DD)&quot;, 
		  DATUM[&quot;WGS84&quot;, 
			SPHEROID[&quot;WGS84&quot;, 6378137.0, 298.257223563]], 
		  PRIMEM[&quot;Greenwich&quot;, 0.0], 
		  UNIT[&quot;degree&quot;, 0.017453292519943295], 
		  AXIS[&quot;Geodetic longitude&quot;, EAST], 
		  AXIS[&quot;Geodetic latitude&quot;, NORTH]]</crs>
		  </latLonBoundingBox>
		  <projectionPolicy>FORCE_DECLARED</projectionPolicy>
		  <enabled>true</enabled>
		  <metadata>
			<entry key="elevation">
			  <dimensionInfo>
				<enabled>false</enabled>
			  </dimensionInfo>
			</entry>
			<entry key="time">
			  <dimensionInfo>
				<enabled>false</enabled>
				<defaultValue/>
			  </dimensionInfo>
			</entry>
			<entry key="cachingEnabled">false</entry>
		  </metadata>
		  <store class="dataStore">
			<name>geoGEC:geogec</name>
			<atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="alternate" href="'.$service.'/rest/workspaces/geoGEC/datastores/$DB.xml" type="application/xml"/>
		  </store>
		  <maxFeatures>0</maxFeatures>
		  <numDecimals>0</numDecimals>
		  <overridingServiceSRS>false</overridingServiceSRS>
		  <skipNumberMatched>false</skipNumberMatched>
		  <circularArcPresent>true</circularArcPresent>
		 
		</featureType>
		';


		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
		//POST return code
		$successCode = 201;


		$buffer = curl_exec($ch); // Execute the curl request

		$Log['tx'][]='curl ejecutado';

		// Check for errors and process results
		$info = curl_getinfo($ch);

		$Log['tx'][]=$info;   
		if ($info['http_code'] != $successCode) {

		  $msgStr = "# Unsuccessful cURL request to ";
		  $msgStr .= $url." [". $info['http_code']. "]\n";
		  fwrite($logfh, $msgStr);
			$Log['res']='err';
			$Log['tx'][]='error al publicar en geoserver';
			$Log['mg'][]='error al publicar en geoserver';
			$Log['tx'][]=$msgStr;
			$Log['tx'][]=$xmlStr;
			terminar($Log);		  
		} else {
		  $msgStr = "# Successful cURL request to ".$url."\n";
		  fwrite($logfh, $msgStr);
		  $Log['data']['creacionWMS']='exito';
		}
		fwrite($logfh, $buffer."\n");

		$Log['tx'][]=$buffer;

		curl_close($ch); // free resources if curl handle will not be reused
		fclose($logfh);  // close logfile
}





// Abre acrvhio log
$logfh = fopen($GeoGecPath."/app_capa/geoserver/GeoserverPHP.log", 'w') or die("can't open log file");

///CONTROLAR SI EL ESTILO EXISTE
$service = "http://190.111.246.33:8080/geoserver/"; 
$request = "rest/workspaces/geoGEC/styles.json"; 
$url = $service . $request;
$ch = curl_init($url);

// Optional settings for debugging
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, $logfh); // logs curl messages
$passwordStr = "general:mostaza"; // replace with your username:password
curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
$buffer = curl_exec($ch); // Execute the curl request
curl_close($ch); // free resources if curl handle will not be reused
fclose($logfh);  // close logfile
$estilos=json_decode($buffer, true); //el parametro true fuerza la salida como array, no stdClass
$Log['tx'][]='curl consulta styles ejecutado';
$salteracreacion='no';

foreach($estilos['styles']['style'] as $style){
	if($style['name']==$capawms){
		$Log['tx'][]=utf8_encode('el estilo existe en el servidor wms, se saltea la creación de un nuevo estilo con este nombre');
		$Log['data']['creacionestilo']='exito';
		//$elmiinarantes='si';
		$salteracreacion='si';
	}
}

if($salteracreacion=='no'){


	$logfh = fopen($GeoGecPath."/app_capa/geoserver/GeoserverPHP.log", 'w') or die("can't open log file");
	///CREAR ESTILO
	$request = "rest/workspaces"; // to add a new workspace
	$url = $service . $request;
	$url.="/geoGEC/styles";
	$ch = curl_init($url); 
	$Log['tx'][]='curl de creacion de estilo iniciado';
	// Optional settings for debugging
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch, CURLOPT_STDERR, $logfh); // logs curl messages
	//Required POST request settings
	curl_setopt($ch, CURLOPT_POST, True);
	$passwordStr = "general:mostaza"; // replace with your username:password
	curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
	//crea un workspace llamado test_ws
	curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-type: application/xml"));

	$xmlStr ='<style>
	  <name>'.$capawms.'</name>
	  <filename>'.$capawms.'.sld</filename>
	</style>
	';

	curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
	//POST return code
	$successCode = 201;
	$buffer = curl_exec($ch); // Execute the curl request
	$Log['tx'][]='curl de estilo ejecutado';
	// Check for errors and process results
	$info = curl_getinfo($ch);

	$Log['tx'][]=$info;   
	if ($info['http_code'] != $successCode) {

	  $msgStr = "# Unsuccessful cURL request to ";
	  $msgStr .= $url." [". $info['http_code']. "]\n";
	  fwrite($logfh, $msgStr);
		$Log['res']='err';
		$Log['tx'][]='error al publicar en geoserver';
		$Log['tx'][]=$msgStr;
		$Log['tx'][]=$xmlStr;
		terminar($Log);		  
	} else {
	  $msgStr = "# Successful cURL request to ".$url."\n";
	  fwrite($logfh, $msgStr);
	  $Log['data']['creacionWMS']='exito';
	}
	fwrite($logfh, $buffer."\n");

	$Log['tx'][]=$buffer;

	curl_close($ch); // free resources if curl handle will not be reused
	fclose($logfh);  // close logfile
	$Log['tx'][]=utf8_encode("se creó la vista solicitada para la consulta wms");
	$Log['data']['id']=$_POST['id'];
	$Log['res']="exito";

	$logfh = fopen($GeoGecPath."/app_capa/geoserver/GeoserverPHP.log", 'w') or die("can't open log file");

}




$carpeta='./documentos/subidas/capa/'.str_pad($_POST['id'],8,"0",STR_PAD_LEFT);
if(!file_exists($carpeta)){
    $Log['tx'][]="creando carpeta $carpeta";
    mkdir($carpeta, 0777, true);
    chmod($carpeta, 0777);	
}
$archivo=$capawms.'.sld';
$myfile = fopen($carpeta.'/'.$archivo, "w");
chmod($carpeta.'/'.$archivo, 0777);
fwrite($myfile, $Log['data']['capa']['sld']);

$exec= 'curl -v -u general:mostaza -XPUT ';
$exec.='-d @'.$carpeta.'/'.$archivo.' ';
$exec.='-H "content-type: application/vnd.ogc.sld+xml" ';
$exec.='http://190.111.246.33:8080/geoserver/rest/workspaces/geoGEC/styles/'.$capawms;
$Log['tx'][]=$exec;
exec($exec,$info,$returnvar);

$Log['tx'][]=print_r($info,true);
$Log['tx'][]=print_r($returnvar,true);
$Log['tx'][]='se cargo la configuracion sld al estilo en geoserver';

/*
$Log['tx'][]=$info;   
if ($info['http_code'] != $successCode) {

  $msgStr = "# Unsuccessful cURL request to ";
  $msgStr .= $url." [". $info['http_code']. "]\n";
  fwrite($logfh, $msgStr);
	$Log['res']='err';
	$Log['tx'][]='error al publicar en geoserver';
	$Log['tx'][]=$msgStr;
	$Log['tx'][]=$xmlStr;
	terminar($Log);		  
} else {
  $msgStr = "# Successful cURL request to ".$url."\n";
  fwrite($logfh, $msgStr);
  $Log['data']['creacionWMS']='exito';
}
fwrite($logfh, $buffer."\n");

$Log['tx'][]=$buffer;

curl_close($ch); // free resources if curl handle will not be reused
fclose($logfh);  // close logfile
$Log['tx'][]=utf8_encode("se creó la vista solicitada para la consulta wms");
$Log['data']['id']=$_POST['id'];
$Log['res']="exito";

$logfh = fopen($GeoGecPath."/app_capa/geoserver/GeoserverPHP.log", 'w') or die("can't open log file");

*/


$Log['data']['id']=$_POST['id'];
$Log['res']="exito";
terminar($Log);
