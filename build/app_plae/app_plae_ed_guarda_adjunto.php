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


$Tabla='PLAdocumentos';


$Log=array();
$Log['data']=array();
$Log['tx']=array();
$Log['res']='';
function terminar($Log){
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	echo $res;
	exit;
}




if(!isset($_POST['nfile'])){
	$Log['tx'][]='no fue definido el tipo de contenido';
	$Log['res']='err';
	terminar($Log);
}
$Log['data']['nf']=$_POST['nfile'];

if(!isset($_FILES['upload'])){
	$Log['tx'][]='no fue enviada la imagen en la variable FILES[upload]';
	$Log['res']='err';
	terminar($Log);
}

if(!isset($_POST['idpla'])){
	$Log['tx'][]='no fue enviada la imagen en la variable idpla';
	$Log['res']='err';
	terminar($Log);
}
if(!isset($_POST['nivel'])){
	$Log['tx'][]='no fue enviada la imagen en la variable nivel';
	$Log['res']='err';
	terminar($Log);
}
if(!isset($_POST['tipo'])){
	$Log['tx'][]='no fue enviado el tipo, se asume que es un documento adjunto';
	$_POST['tipo']='adjunto';
}
if($_POST['tipo']==''){
	$Log['tx'][]='no fue enviado el tipo, se asume que es un documento adjunto';
	$_POST['tipo']='adjunto';
}


	$Log['tx'][]= "archivo enviado";
	
	$ArchivoOrig = $_FILES['upload']['name'];	
	$Log['tx'][]= "cargando: ".$ArchivoOrig;
	
	$b = explode(".",$ArchivoOrig);
	$ext = strtolower($b[(count($b)-1)]);	

	
if($_POST['tipo']=='origen'){
	$Dest="origen/";
	$nombretipo = $Tabla."[NID]";
	$nombrepreliminar='si';//indica que el documento debe ser renombrado luego de creado el registro.			
	
}elseif($_POST['tipo']=='contenido'){
	$Dest="origen/";
	$nombretipo = $Tabla."[NID]";
	$nombrepreliminar='no';			
	
}elseif($_POST['tipo']=='adjunto'){
	$Dest="adjuntos/";
	$nombretipo = $Tabla."[NID]";
	$nombrepreliminar='no';
}else{
	$Log['tx'][]='no hemos comprendido el tipo de archivo enviado (origne, adjunto, contenido';
	$Log['res']='err';
	terminar($Log);
}




	$PathBase="./documentos/p_".$PanelI."/PLA/".$Dest;
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
										
	$nombretipo = $Tabla."_".$_POST['nivel']."[NID]_";
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
		
		if (!copy($_FILES['upload']['tmp_name'], $nombre)) {
		   	$Log['tx'][]= "Error al copiar $pathI...\n";
			$Log['res']='err';
			terminar($Log);
		}else{
			chmod($nombre, 0777);
			$Log['tx'][]= "imagen guardada";
		}
	/*}else{
		$ms="solo se aceptan los formatos:";
		foreach($extVal as $k => $v){$ms.=" $k,";}
		$Log['mg'][]= $ms;
		$ArchivoOrig='';
		$Log['res']='err';
		terminar($Log);
	}*/	

	if($_POST['nivel']=='PLAn1'){$idp1=$_POST['idpla'];}else{$idp1='NULL';}
	if($_POST['nivel']=='PLAn2'){$idp2=$_POST['idpla'];}else{$idp2='NULL';}
	if($_POST['nivel']=='PLAn3'){$idp3=$_POST['idpla'];}else{$idp3='NULL';}
	if(!isset($_POST['tipo'])){$_POST['tipo']='';}
	$query="
	INSERT INTO 
		`paneles`.`PLAdocumentos`
	SET
	`id_p_PLAn1_id`= $idp1,
	`id_p_PLAn2_id`= $idp2,
	`id_p_PLAn3_id`= $idp3,
	`FI_documento`='".$nombre."',
	`FI_nombreorig`='".$ArchivoOrig."',
	`zz_AUTOPANEL`='".$PanelI."',
	`tipo`='".$_POST['tipo']."'
	";
	
	$Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['res']='err';
		terminar($Log);
	}
	
	$NID = $Conec1->insert_id;
	if($NID<1){
		$Log['tx'][]='error al generar un nuevo id: '.$NID;
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['res']='err';
		terminar($Log);
	}
	
	$nuevonombre=str_replace("[NID]", $NID, $nombre);
	$Log['data']['ruta']=$nuevonombre;
	
	if(!rename($nombre,$nuevonombre)){		
	 	$Log['tx'][]=" error al renombrar el documento ".$origen['nombre']." con el nuevo id => $nuevonombre";
		$Log['res']='err';
		terminar($Log);	
	}else{
	 	$query="
	 		UPDATE 
	 			`paneles`.`PLAdocumentos`
	 		SET 
	 			FI_documento = '$nuevonombre' 
	 		WHERE
	 			id='$NID'
	 	";
		$Conec1->query($query);
	
		if($Conec1->error!=''){
			$Log['tx'][]='error al consultar columnas';
			$Log['tx'][]=utf8_encode($query);
			$Log['tx'][]=utf8_encode($Conec1->error);
			$Log['res']='err';
			terminar($Log);
		}		
	}
	
	
	$query="
 		SELECT 
 		 id, 
 		 id_p_PLAn1_id, 
 		 id_p_PLAn2_id, 
 		 id_p_PLAn3_id, 
 		 descripcion, 
 		 FI_documento, 
 		 FI_nombreorig, 
 		 zz_borrada, 
 		 zz_AUTOPANEL, 
 		 tipo
	FROM `paneles`.`PLAdocumentos`
	WHERE id = '$NID'
 	";
	$Consulta=$Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['res']='err';
		terminar($Log);
	}			
	while($row = $Consulta->fetch_assoc()){
		foreach($row as $k => $v){
			$Log['data'][$k]=utf8_encode($v);
		}
	}

$Log['tx'][]='completado';
$Log['res']='exito';

terminar($Log);

?>
