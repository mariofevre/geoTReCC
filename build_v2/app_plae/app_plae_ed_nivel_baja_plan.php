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
$Usu = validarUsuario(); // en ./usu_valudacion.php




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

if($_POST['nivel']=='PLAn3'){
	$Log['mg'][]=utf8_encode('Ese componente ya se encuentra en el mpaximo nivel');
    $Log['res']='err';
    terminar($Log);
}

$Id=$_POST['id'];	
$Log['data']['id']=$Id;
$Log['data']['nivel']=$_POST['nivel'];
//$Log['data']['modo']=$_POST['modo'];

$tabla=str_replace('PLAn','ref_plae_nivel',$_POST['nivel']);

$query = "
	SELECT 
		*
	FROM 
		$DB.$tabla 
	
	WHERE 
	id='".$_POST['id']."'
	AND
	ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
";
$Consulta=	pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error al consultar columnas';
    $Log['tx'][]=utf8_encode($query);
    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
    $Log['res']='err';
    terminar($Log);
}

while($row = pg_fetch_assoc($Consulta)){
	
	$Componente=$row;		
	
}




if($_POST['nivel']=='PLAn1'){
	$_POST['nivelME1']='PLAn2';
	$_POST['nivelMA1']=null;
	$_POST['nivelMA2']=null;
}elseif($_POST['nivel']=='PLAn2'){
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



//TODO control de integridad nivel_dest, menor o igual que nivel
//TODO control de integridad id_dest, no descendiente de id

$tablaDest=str_replace('PLAn','ref_plae_nivel',$_POST['nivel_dest']);

$query = "
	SELECT 
		*
	FROM 
		$DB.$tablaDest
	WHERE 
		id='".$_POST['id_dest']."'
	AND
		ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
";	
$Consulta=	pg_query($ConecSIG, $query);
	

if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error al consultar columnas';
    $Log['tx'][]=utf8_encode($query);
    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
    $Log['res']='err';
    terminar($Log);
}
while($row = pg_fetch_assoc($Consulta)){
	$SuperComponente[$row['id']]=$row;	
}


$tablaME1=str_replace('PLAn','ref_plae_nivel',$_POST['nivelME1']);
$campo=str_replace('PLAn','id_p_plae_nivel',$_POST['nivel']);
$query = "
	SELECT 
		*
	FROM 
		$DB.$tablaME1
	WHERE 
		$campo ='".$_POST['id']."'
		AND
		ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
		AND
		zz_borrada='0'
";	
$Consulta=	pg_query($ConecSIG, $query);
	

if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error al consultar columnas';
    $Log['tx'][]=utf8_encode($query);
    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
    $Log['res']='err';
    terminar($Log);
}
$SubComponentes=array();
while($row = pg_fetch_assoc($Consulta)){
	$SubComponentes[$row['id']]=$row;	
}
$Log['tx'][]=count($SubComponentes)." subcomponentes detectados.";


if($_POST['nivel_dest']=='PLAn1'){
	$_POST['nivel_destME2']='PLAn3';
	$_POST['nivel_destME1']='PLAn2';
	$_POST['nivel_destMA1']=null;
	$_POST['nivel_destMA2']=null;
}elseif($_POST['nivel_dest']=='PLAn2'){
	$_POST['nivel_destME2']=null;
	$_POST['nivel_destME1']='PLAn3';
	$_POST['nivel_destMA1']='PLAn1';
	$_POST['nivel_destMA2']=null;
}elseif($_POST['nivel_dest']=='PLAn3'){		
	$_POST['nivel_destME2']=null;
	$_POST['nivel_destME1']=null;
	$_POST['nivel_destMA1']='PLAn2';
	$_POST['nivel_destMA2']='PLAn1';
}


$Log['data']['nnivel']=$_POST['nivel_destME1'];

$tablaDestME1=str_replace('PLAn','ref_plae_nivel',$_POST['nivel_destME1']);
$campoDest=str_replace('PLAn','id_p_plae_nivel',$_POST['nivel_dest']);

$query=" 
	INSERT INTO 
	$DB.$tablaDestME1
	(
		ic_p_est_02_marcoacademico,
		zz_preliminar,
		$campoDest ,
		nombre,
		numero,
		descripcion,		
		zz_publico,
		co_color,
		zz_borrada
	)
	VALUES
	(
		'".$_POST['codMarco']."',
		'0',
		'".$_POST['id_dest']."',
		'".$Componente['nombre']."',
		'".$Componente['numero']."',
		'".$Componente['descripcion']."',
		'".$Componente['zz_publico']."',
		'".$Componente['co_color']."',
		'".$Componente['zz_borrada']."'
	)
	RETURNING id
	
";							
$Consulta=pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error al consultar columnas';
	$Log['tx'][]=utf8_encode($query);
	$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	$Log['res']='err';
	terminar($Log);
}

$row=pg_fetch_assoc($Consulta);
$Log['data']['nid']=$row['id'];

$Log['tx'][]='Creada copia de componente principal. nid: '.$Log['data']['nid'] ;

if($Log['data']['nid']<1){
	$Log['tx'][]='error al generar un nuevo id: '.$Log['data']['nid'];
	$Log['tx'][]=utf8_encode($query);
	$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	$Log['res']='err';
	terminar($Log);
}


$query="
	UPDATE 
		$DB.$tabla
	SET
		zz_migrado_a='".$Log['data']['nid']."',
		zz_borrada='1'
	WHERE 
		id='".$_POST['id']."'
";
pg_query($ConecSIG, $query);;
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error al consultar columnas';
	$Log['tx'][]=utf8_encode($query);
	$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	$Log['res']='err';
	terminar($Log);
}
$Log['tx'][]=utf8_encode('Eliminada versión previa.');


$campoDestME1=str_replace('PLAn','id_p_plae_nivel',$_POST['nivel_destME1']);

//mover estados
$query="
	INSERT INTO 
		
	$DB.ref_plae_estados(
		nombre, desde, ic_p_est_02_marcoacademico, $campoDestME1
	)
	SELECT 
	   nombre, desde, ic_p_est_02_marcoacademico, '".$Log['data']['nid']."'
	FROM 
	   $DB.ref_plae_estados
	WHERE
	   $campo ='".$_POST['id']."'
	   
	  AND
	  ic_p_est_02_marcoacademico='".$_POST['codMarco']."' 
	
";
pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error al consultar columnas';
	$Log['tx'][]=utf8_encode($query);
	$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	$Log['res']='err';
	terminar($Log);
}
$Log['tx'][]='estados del componente principal copiados.';


$campoDestME1=str_replace('PLAn','id_p_ref_plae_nivel',$_POST['nivel_destME1']);
$campo=str_replace('PLAn','id_p_ref_plae_nivel',$_POST['nivel']);
//mover PLAdocumentos
$query="
	INSERT INTO 
		$DB.ref_plae_link_ref_01_documentos(		
		$campoDestME1 ,
		descripcion, id_p_ref_01_documentos, ic_p_est_02_marcoacademico)
	
	SELECT 
		'".$Log['data']['nid']."',
		descripcion, id_p_ref_01_documentos, ic_p_est_02_marcoacademico
	FROM 
	  $DB.ref_plae_link_ref_01_documentos
	WHERE
	   $campo  ='".$_POST['id']."'
	   AND
	   zz_borrada='0'
";
pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error al consultar columnas';
	$Log['tx'][]=utf8_encode($query);
	$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	$Log['res']='err';
	terminar($Log);
}
$Log['tx'][]='documentos del componente principal copiados.';

/*
//mover valor categoria
$query="
	INSERT INTO PLAcategoriasVal(
		id_p_plae_categorias , valor, ic_p_est_02_marcoacademico, id_p_".$_POST['nivel_destME1']."
	)
	SELECT 
		id_p_plae_categorias , valor, ic_p_est_02_marcoacademico, '".$Log['data']['nid']."'
	FROM 
	   PLAcategoriasVal
	WHERE
		id_p_".$_POST['nivel']."='".$_POST['id']."'
";
pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error al consultar columnas';
	$Log['tx'][]=utf8_encode($query);
	$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	$Log['res']='err';
	terminar($Log);
}
$Log['tx'][]='valor categoria del componente principal copiados.';

*/


$tablaDestME2=str_replace('PLAn','ref_plae_nivel',$_POST['nivel_destME2']);
$campoDestME1=str_replace('PLAn','id_p_plae_nivel',$_POST['nivel_destME1']);

foreach($SubComponentes as $subid => $subdat){
	
	$query=" 
		INSERT INTO 
		$DB.$tablaDestME2
		(
			ic_p_est_02_marcoacademico,
			zz_preliminar,
			$campoDestME1, 
			nombre,
			numero,
			descripcion			
			zz_publico,
			co_color,
			zz_borrada
		)
		VALUES
		(
			'".$_POST['codMarco']."',
			'0',
			'".$Log['data']['nid']."', 
			'".$subdat['nombre']."',
			'".$subdat['numero']."',
			'".$subdat['descripcion']."'
			'".$subdat['zz_publico']."',
			'".$subdat['co_color']."',
			'".$subdat['zz_borrada']."'
		)
	";							
	pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
		$Log['res']='err';
		terminar($Log);
	}
	$SubComponentes[$subid]['nid']=$Conec1->insert_id;
	$Log['tx'][]='Copiado subcomponente. nid: '.$SubComponentes[$subid]['nid'];
	//TODO borrar version antigua del sub
	
	$query="
		UPDATE 
			$DB.$tablaME1
		SET
			zz_migrado_a='".$SubComponentes[$subid]['nid']."',
			zz_borrada='1'
		WHERE 
		id='".$subid."'
	";
	pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
		$Log['res']='err';
		terminar($Log);
	}		
	$Log['tx'][]=utf8_encode('Eliminada versión previa');
	
	
	$campoDestME1=str_replace('PLAn','id_p_plae_nivel',$_POST['nivel_destME1']);	
	$campoDestME2=str_replace('PLAn','id_p_plae_nivel',$_POST['nivel_destME2']);	
	//mover estados
	$query="
		INSERT INTO 
		$DB.ref_plae_estados(
			nombre, desde, ic_p_est_02_marcoacademico, $campoDestME2
		)
		SELECT 
		   nombre, desde, ic_p_est_02_marcoacademico, '".$SubComponentes[$subid]['nid']."'
		FROM 
		   PLAestados
		WHERE
		   $campoDestME1 ='".$subid."'
		  AND
		  ic_p_est_02_marcoacademico='".$_POST['codMarco']."' 
		  ;
	";
	pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
		$Log['res']='err';
		terminar($Log);
	}
	$Log['tx'][]='estados del componente interior copiados.';

	
	$campoDestME1=str_replace('PLAn','id_p_ref_plae_nivel',$_POST['nivel_destME1']);
	$campoDestME2=str_replace('PLAn','id_p_ref_plae_nivel',$_POST['nivel_destME2']);
	// mover PLAdocumentos
	$query="
	INSERT INTO 
		$DB.ref_plae_link_ref_01_documentos(
			$campoDestME2,
			descripcion, id_p_ref_01_documentos, ic_p_est_02_marcoacademico)
		
		SELECT 
			'".$SubComponentes[$subid]['nid']."',
			descripcion, id_p_ref_01_documentos, ic_p_est_02_marcoacademico
		FROM 
		   $DB.ref_plae_link_ref_01_documentos
		WHERE
		   $campoDestME1 ='".$subid."'
		   AND
		   zz_borrada='0'
	";
	pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
		$Log['res']='err';
		terminar($Log);
	}
	$Log['tx'][]='documentos del componente interior copiados.';


/*
	//mover valor categoria
	$query="
	INSERT INTO PLAcategoriasVal(
			id_p_plae_categorias , valor, ic_p_est_02_marcoacademico, id_p_".$_POST['nivel_destME2']."
		)
		SELECT 
			id_p_plae_categorias , valor, ic_p_est_02_marcoacademico, '".$SubComponentes[$subid]['nid']."'
		FROM 
		   PLAcategoriasVal
		WHERE
			id_p_".$_POST['nivelME1']."='".$subid."'
	";
	pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error al consultar columnas';
		$Log['tx'][]=utf8_encode($query);
		$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
		$Log['res']='err';
		terminar($Log);
	}
	$Log['tx'][]='valor categoria del componente principal copiados.';
	*/
	
}



$Log['res']='exito';
terminar($Log);
