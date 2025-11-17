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


$Log=array();
$Log['data']=array();
$Log['tx']=array();
$Log['res']='';
$Log['acc']=array();
function terminar($Log){
    $res=json_encode($Log);
    if($res==''){$Log['tx'][]=erroresJson();$res=print_r($Log,true);}
    echo $res;
    exit;
}



foreach($_POST as $k => $v){
	$_POST[$k] = utf8_decode($v);
}

if(!isset($_POST['id'])){
    $Log['tx'][]='falta variable id';
    $Log['res']='err';
    terminar($Log);
}

if(!isset($_POST['nivel'])){
    $Log['tx'][]='falta variable nivel';
    $Log['res']='err';
    terminar($Log);
}

$Validos['nivel']=array('PLAn1'=>'','PLAn2'=>'','PLAn3'=>'');
if(!isset($Validos['nivel'][$_POST['nivel']])){
    $Log['tx'][]='Variable nivel contiene un valor inváido ('.$_POST['nivel'].'). Solo se aceptan: '.print_r($Validos['nivel'],true);
    $Log['res']='err';
    terminar($Log);
}

if($_POST['nivel']=='PLAn1'){
	$Log['mg'][]=utf8_encode('Ese componente ya se encuentra en el mpaximo nivel');
    $Log['res']='err';
    terminar($Log);
}

$Id=$_POST['id'];	
$Log['data']['id']=$Id;
$Log['data']['nivel']=$_POST['nivel'];
//$Log['data']['modo']=$_POST['modo'];


$query = "
	SELECT 
		*
	FROM ".$_POST['nivel']."
	
	WHERE 
	id='".$_POST['id']."'
	AND
	zz_AUTOPANEL='".$PanelI."'
";
$Consulta=	$Conec1->query($query);
if($Conec1->error!=''){
    $Log['tx'][]='error al consultar columnas';
    $Log['tx'][]=utf8_encode($query);
    $Log['tx'][]=utf8_encode($Conec1->error);
    $Log['res']='err';
    terminar($Log);
}
while($row = $Consulta->fetch_assoc()){
	
	$Componente=$row;		
	
}


if($_POST['nivel']=='PLAn2'){
	$_POST['nivelME1']='PLAn3';
	$_POST['nivelMA1']='PLAn1';
	$_POST['nivelMA2']=null;
}elseif($_POST['nivel']=='PLAn3'){		
	$_POST['nivelME1']=null;
	$_POST['nivelMA1']='PLAn2';
	$_POST['nivelMA2']='PLAn1';
}

$Log['data']['id']=$_POST['id'];
$Log['data']['nivel']=$_POST['nivel']; 
$Log['data']['nnivel']=$_POST['nivelMA1'];


$SubComponentes=array();
if($_POST['nivelME1']!=null){
	$query = "
		SELECT 
			*
		FROM 
			".$_POST['nivelME1']."	
		WHERE 
			id_p_".$_POST['nivel']."='".$_POST['id']."'
			AND
			zz_AUTOPANEL='".$PanelI."'
			AND
			zz_borrada='0'
	";	
	$Consulta=	$Conec1->query($query);
		

	if($Conec1->error!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['res']='err';
		terminar($Log);
	}

	while($row = $Consulta->fetch_assoc()){
		$SubComponentes[$row['id']]=$row;	
	}
	$Log['tx'][]=count($SubComponentes)." subcomponentes detectados.";
}




$query = "
	SELECT 
		*
	FROM ".$_POST['nivelMA1']."
	
	WHERE 
	id='".$Componente['id_p_'.$_POST['nivelMA1']]."'
	AND
	zz_AUTOPANEL='".$PanelI."'
";	
$Consulta=	$Conec1->query($query);
	
    $Log['tx'][]=utf8_encode($query);
if($Conec1->error!=''){
    $Log['tx'][]='error al consultar columnas';
    $Log['tx'][]=utf8_encode($query);
    $Log['tx'][]=utf8_encode($Conec1->error);
    $Log['res']='err';
    terminar($Log);
}
while($row = $Consulta->fetch_assoc()){
	$SuperComponente=$row;	
}


$extrac="";	
$extrav="";	
if($_POST['nivelMA2']!=null){
    $Log['tx'][]=utf8_encode(print_r($SuperComponente,true));
	$extrac="`id_p_".$_POST['nivelMA2']."`, ";	
	$extrav=" '".$SuperComponente['id_p_'.$_POST['nivelMA2']]."', ";		
	
}

$query=" 
	INSERT INTO 
	`paneles`.`".$_POST['nivelMA1']."`
	(
		`zz_AUTOPANEL`,
		zz_preliminar,
		".$extrac."
		nombre,
		numero,
		descripcion,
		id_p_GRAactores,		
		zz_publico,
		CO_color,
		zz_borrada,
		zz_ultimoestado,
		zz_ultimoestado_desde	
	)
	VALUES
	(
		'".$PanelI."',
		'0',
		".$extrav."
		'".$Componente['nombre']."',
		'".$Componente['numero']."',
		'".$Componente['descripcion']."',
		'".$Componente['id_p_GRAactores']."',
		'".$Componente['zz_publico']."',
		'".$Componente['CO_color']."',
		'".$Componente['zz_borrada']."',
		'".$Componente['zz_ultimoestado']."',
		'".$Componente['zz_ultimoestado_desde']."'	
	)
	
";							
$Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]='error al consultar columnas';
	$Log['tx'][]=utf8_encode($query);
	$Log['tx'][]=utf8_encode($Conec1->error);
	$Log['res']='err';
	terminar($Log);
}


$Log['data']['nid'] = $Conec1->insert_id;
$Log['tx'][]='Creada copia de componente principal. nid: '.$Log['data']['nid'] ;

if($Log['data']['nid']<1){
	$Log['tx'][]='error al generar un nuevo id: '.$Log['data']['nid'];
	$Log['tx'][]=utf8_encode($query);
	$Log['tx'][]=utf8_encode($Conec1->error);
	$Log['res']='err';
	terminar($Log);
}


$query="
	UPDATE 
		paneles.".$_POST['nivel']."
	SET
		zz_migrado_a='".$Log['data']['nid']."',
		zz_borrada='1'
	WHERE 
	id='".$_POST['id']."'
";
$Conec1->query($query);;
if($Conec1->error!=''){
	$Log['tx'][]='error al consultar columnas';
	$Log['tx'][]=utf8_encode($query);
	$Log['tx'][]=utf8_encode($Conec1->error);
	$Log['res']='err';
	terminar($Log);
}
$Log['tx'][]=utf8_encode('Eliminada versión previa.');

	
//mover estados
$query="
	INSERT INTO PLAestados(
		nombre, desde, zz_AUTOPANEL, id_p_".$_POST['nivelMA1']."
	)
	SELECT 
	   nombre, desde, zz_AUTOPANEL, '".$Log['data']['nid']."'
	FROM 
	   PLAestados
	WHERE
	   id_p_".$_POST['nivel']."='".$_POST['id']."'
	   
	  AND
	  zz_AUTOPANEL='".$PanelI."' 
	;
";
$Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]='error al consultar columnas';
	$Log['tx'][]=utf8_encode($query);
	$Log['tx'][]=utf8_encode($Conec1->error);
	$Log['res']='err';
	terminar($Log);
}
$Log['tx'][]='estados del componente principal copiados.';

//mover PLAdocumentos
$query="
	INSERT INTO PLAdocumentos(
		id_p_".$_POST['nivelMA1']."_id,
		descripcion, FI_documento, FI_nombreorig, zz_AUTOPANEL, tipo)
	
	SELECT 
		'".$Log['data']['nid']."',
		descripcion, FI_documento, FI_nombreorig, zz_AUTOPANEL, tipo
	FROM 
	   PLAdocumentos
	WHERE
	   id_p_".$_POST['nivel']."_id='".$_POST['id']."'
	   AND
	   zz_borrada='0'
";
$Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]='error al consultar columnas';
	$Log['tx'][]=utf8_encode($query);
	$Log['tx'][]=utf8_encode($Conec1->error);
	$Log['res']='err';
	terminar($Log);
}
$Log['tx'][]='documentos del componente principal copiados.';


//mover valor categoria
$query="
	INSERT INTO PLAcategoriasVal(
		id_p_PLAcategorias, valor, zz_AUTOPANEL, id_p_".$_POST['nivelMA1']."
	)
	SELECT 
		id_p_PLAcategorias, valor, zz_AUTOPANEL, '".$Log['data']['nid']."'
	FROM 
	   PLAcategoriasVal
	WHERE
		id_p_".$_POST['nivel']."='".$_POST['id']."'
";
$Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]='error al consultar columnas';
	$Log['tx'][]=utf8_encode($query);
	$Log['tx'][]=utf8_encode($Conec1->error);
	$Log['res']='err';
	terminar($Log);
}
$Log['tx'][]='valor categoria del componente principal copiados.';




foreach($SubComponentes as $subid => $subdat){
	//TODO crar version elevada del sub
	$extrac="`id_p_".$_POST['nivelMA1']."`, ";	
	$extrav=" '".$Log['data']['nid']."', ";		
	$query=" 
		INSERT INTO 
		`paneles`.`".$_POST['nivel']."`
		(
			`zz_AUTOPANEL`,
			zz_preliminar,
			".$extrac."
			nombre,
			numero,
			descripcion,
			id_p_GRAactores,			
			zz_publico,
			CO_color,
			zz_borrada,
			zz_ultimoestado,
			zz_ultimoestado_desde
		)
		VALUES
		(
			'".$PanelI."',
			'0',
			".$extrav."
			'".$subdat['nombre']."',
			'".$subdat['numero']."',
			'".$subdat['descripcion']."',
			'".$subdat['id_p_GRAactores']."',
			'".$subdat['zz_publico']."',
			'".$subdat['CO_color']."',
			'".$subdat['zz_borrada']."',
			'".$subdat['zz_ultimoestado']."',
			'".$subdat['zz_ultimoestado_desde']."'
		)
	";							
	$Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['res']='err';
		terminar($Log);
	}
	$SubComponentes[$subid]['nid']=$Conec1->insert_id;
	$Log['tx'][]='Copiado subcomponente. nid: '.$SubComponentes[$subid]['nid'];
	//TODO borrar version antigua del sub

	$query="
		UPDATE 
			paneles.".$_POST['nivelME1']."
		SET
			zz_migrado_a='".$SubComponentes[$subid]['nid']."',
			zz_borrada='1'
		WHERE 
		id='".$subid."'
	";
	$Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['res']='err';
		terminar($Log);
	}		
	$Log['tx'][]=utf8_encode('Eliminada versión previa');
	
	
		
	//mover estados
	$query="
		INSERT INTO PLAestados(
			nombre, desde, zz_AUTOPANEL, id_p_".$_POST['nivel']."
		)
		SELECT 
		   nombre, desde, zz_AUTOPANEL, '".$SubComponentes[$subid]['nid']."'
		FROM 
		   PLAestados
		WHERE
		   id_p_".$_POST['nivelME1']."='".$subid."'
		  AND
		  zz_AUTOPANEL='".$PanelI."' 
		  ;
	";
	$Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['res']='err';
		terminar($Log);
	}
	$Log['tx'][]='estados del componente interior copiados.';


	// mover PLAdocumentos
	$query="
	INSERT INTO PLAdocumentos(
			id_p_".$_POST['nivel']."_id,
			descripcion, FI_documento, FI_nombreorig, zz_AUTOPANEL, tipo)
		
		SELECT 
			'".$SubComponentes[$subid]['nid']."',
			descripcion, FI_documento, FI_nombreorig, zz_AUTOPANEL, tipo
		FROM 
		   PLAdocumentos
		WHERE
		   id_p_".$_POST['nivelME1']."_id='".$subid."'
		   AND
		   zz_borrada='0'
	";
	$Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['res']='err';
		terminar($Log);
	}
	$Log['tx'][]='documentos del componente interior copiados.';



	//mover valor categoria
	$query="
	INSERT INTO PLAcategoriasVal(
			id_p_PLAcategorias, valor, zz_AUTOPANEL, id_p_".$_POST['nivel']."
		)
		SELECT 
			id_p_PLAcategorias, valor, zz_AUTOPANEL, '".$SubComponentes[$subid]['nid']."'
		FROM 
		   PLAcategoriasVal
		WHERE
			id_p_".$_POST['nivelME1']."='".$subid."'
	";
	$Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['res']='err';
		terminar($Log);
	}
	$Log['tx'][]='valor categoria del componente principal copiados.';
	
	
}



$Log['res']='exito';
terminar($Log);
