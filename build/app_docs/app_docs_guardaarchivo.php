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



//si no se pueden completar las cargas tal vez quieras revisar estos:
$Log['tx'][]="max_execution_time:".ini_get('max_execution_time');
$Log['tx'][]="max_input_time:".ini_get('max_input_time');

// también puese ser esto una limitante
$Log['tx'][]="upload_max_filesize:".ini_get('upload_max_filesize'); 
$Log['tx'][]="post_max_size :".ini_get('post_max_size'); 

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la varaible codMarco';
	$Log['res']='err';
	terminar($Log);	
}	
$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_docs'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_docs'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}
if($Acc<2){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]=utf8_encode('no cuenta con permisos para generar una nueva versión de una capa estructural de la plataforma geoGEC');
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['nfile'])){
	$Log['tx'][]='no fue definido el numero de archivo';
	$Log['res']='err';
	terminar($Log);
}
$Log['data']['nfile']=$_POST['nfile'];

if(!isset($_FILES['upload'])){
	$Log['tx'][]='no fue enviada la imagen en la variable FILES[upload]';
	$Log['res']='err';
	terminar($Log);
}


$modo='normal';
if(isset($_POST['modo'])){
	if($_POST['modo']=='pre_Publ'){
		$modo='pre_Publ';//carga archivo de forma preliminar, no será visible hata que se guarde la publicación compartida..		
	}
	if($_POST['modo']=='def_Rele'){
		$modo='def_Rele';//carga archivo de forma permanente vinculado a un relevamiento.		
	}
}




$Log['tx'][]= "archivo enviado";

$ArchivoOrig = $_FILES['upload']['name'];	
$Log['tx'][]= "cargando: ".$ArchivoOrig;

$b = explode(".",$ArchivoOrig);
$ext = strtolower($b[(count($b)-1)]);	

$idmarcpad=str_pad($_POST['codMarco'],8,"0",STR_PAD_LEFT);
$PathBase="./documentos/referencias/".$idmarcpad."/";

$path=$PathBase;
$carpetas= explode("/",$path);	
$rutaacumulada="";			
foreach($carpetas as $valor){		
$Log['tx'][]= "instancia de ruta: $valor ";
$rutaacumulada.=$valor."/";
	if (!file_exists($rutaacumulada)&&$valor!=''){
		$Log['tx'][]="creando: $rutaacumulada ";
	    mkdir($rutaacumulada, 0777, true);
	    chmod($rutaacumulada, 0777);
	}
}		
// FIN verificar y crear directorio				
									
$nombretipo = "ref_01_[NID]_";
$nombre=$nombretipo;
$nombreprliminar='si';//indica que el documento debe ser renombrado luego de creado el registro.			

$c=explode('.',$nombre);

$cod = cadenaArchivo(10); // define un código que evita la predictivilidad de los documentos ante búsquedas maliciosas
$nombre=$path.$c[0].$cod.".".$ext;

/*
$extVal['jpg']='1';
$extVal['png']='1';
$extVal['tif']='1';
$extVal['bmp']='1';
$extVal['gif']='1';
//$extVal['pdf']='1';
//$extVal['zip']='1';
*/

//if(isset($extVal[strtolower($ext)])){
$Log['tx'][]= "guardado en: ".$nombre."<br>";

if($_FILES['upload']['tmp_name']==''){
	$Log['tx'][]= "Archivo de entrada vacio. Verificar que el servidor no lo este filtrando por exceso de tamaño en configuraicon php.ini";
	$Log['res']='err';
	terminar($Log);
}


if (!copy($_FILES['upload']['tmp_name'], $nombre)) {
   	$Log['tx'][]= "Error al copiar $pathI...\n";
	$Log['res']='err';
	terminar($Log);
}else{
	chmod($nombre, 0777);
	$Log['tx'][]= "archivo guardado";
}
/*}else{
	$ms="solo se aceptan los formatos:";
	foreach($extVal as $k => $v){$ms.=" $k,";}
	$Log['mg'][]= $ms;
	$ArchivoOrig='';
	$Log['res']='err';
	terminar($Log);
}*/	

$nombreGuard=str_replace("../", "./", $nombre);

if($modo=='pre_Publ'){	
	$prel='1';
}else{
	$prel='0';
}

$query="
INSERT INTO 
	$DB.ref_01_documentos(
		archivo,
		nombre,
		ic_p_est_02_marcoacademico,
		zz_preliminar,
		zz_auto_crea_usu
	)
	VALUES(
		'".$nombreGuard."',
		'".$ArchivoOrig."',
		'".$_POST['codMarco']."',
		'".$prel."',
		'".$_SESSION[$CU]["usuario"]['id']."'	
	)
	RETURNING id
";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
while($fila=pg_fetch_assoc($ConsultaVer)){
	$NID=$fila['id'];
	$Log['tx'][]='item creado, id:'.$NID;
	$Log['data']['nid']=$NID;
}

$nuevonombre=str_replace("[NID]", $NID, $nombre);
$nuevonombreGuard=str_replace("../", "./", $nuevonombre);
$Log['data']['ruta']=$nuevonombreGuard;

if(!rename($nombre,$nuevonombre)){		
 	$Log['tx'][]=" error al renombrar el documento ".$origen['nombre']." con el nuevo id => $nuevonombre";
	$Log['res']='err';
	terminar($Log);	
}else{
 	$query="
 		UPDATE 
 			$DB.ref_01_documentos
 		SET 
 			archivo = '".$nuevonombreGuard."'
 		WHERE
 			id='".$NID."'
 			AND
 			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
 	";
 	
	 pg_query($ConecSIG, $query);

	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}	
}



if($modo=='pre_Publ'||$modo=='def_Rele'){
	$e=explode('_',$modo);
	$sis=$e[1];
	$query="
		SELECT 
			id, id_p_ref_02_pseudocarpetas, id_p_est_02_marcoacademico, 
			orden, zz_borrada, nombre, descripcion, 
			zz_protegida, zz_sis
		FROM 
			$DB.ref_02_pseudocarpetas
		WHERE
			zz_sis = '".$sis."'
			AND
			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
			AND
			zz_borrada='0'
	";
	$ConsultaPseudoC = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	if( pg_num_rows($ConsultaPseudoC)<1){
					
		if($modo=='pre_Publ'){		
			$desc='carpeta generada automáticamente al compartir publicaciones académicas';
		}elseif($modo=='def_Rele'){
			$desc='carpeta generada automáticamente al compartir relevamientos fotográficos';
		}
		
		$e=explode('_',$modo);
		$sis=$e[1];
	
		$query="
			INSERT INTO 
				$DB.ref_02_pseudocarpetas(
					id_p_ref_02_pseudocarpetas, orden, 
					nombre, 
					descripcion, 
					ic_p_est_02_marcoacademico, zz_sis
				)
				VALUES( 
					'0',  '9999999', 
					'".utf8_encode('publicaciones compartidas (sistema)')."', 
					'".utf8_encode($desc)."', 
					'".$_POST['codMarco']."',
					'".$sis."'
				)
			RETURNING id
		";
		$ConsultaInPseudoC = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		$row=pg_fetch_assoc($ConsultaInPseudoC);
		$pse=$row['id'];
		
	}else{
		$row=pg_fetch_assoc($ConsultaPseudoC);
		$pse=$row['id'];
	}

	$query="
 		UPDATE 
 			$DB.ref_01_documentos
 		SET 
 			id_p_ref_02_pseudocarpetas = '".$pse."'
 		WHERE
 			id='".$NID."'
 			AND
 			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
 	";
 	
	pg_query($ConecSIG, $query);

	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}	
}



//echo $query;
$Log['data']['nid']=$NID;
$Log['data']['nf']=$_POST['nfile'];
$Log['data']['ruta']=$nuevonombreGuard;
$Log['tx'][]='completado';
$Log['res']='exito';
terminar($Log);
?>
