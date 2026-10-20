<?php

ini_set('display_errors', 1);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";
include($GeoGecPath.'/includes/encabezado.php');
include($GeoGecPath."/includes/pgqonect.php");

include_once($GeoGecPath."/usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php






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

if(!isset($_POST['id'])){
	$Log['tx'][]='error, no se registra log.';
	$Log['res']='err';
	terminar($Log);	
}
if($_POST['id']!=$_SESSION["geogec"]["usuario"]['id']){
	$Log['tx'][]='error en la validacion de permisos de usuario.';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['log'])){
	$Log['tx'][]='error, no se registra log.';
	$Log['res']='err';
	terminar($Log);	
}


if(strlen($_POST['log'])<5){
	$Log['tx'][]=utf8_encode('error, el log debe tener mas de 4 caracteres.'.$_POST['log']);
	$Log['mg'][]=utf8_encode('error, el log debe tener mas de 4 caracteres.'.$_POST['log']);
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['nombre'])){
	$Log['tx'][]='error, no se registra nombre.';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['apellido'])){
	$Log['tx'][]='error, no se registra apellido.';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['email'])){
	$Log['tx'][]='error, no se registra direccion de correo electronico.';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['pronombre'])){
	$Log['tx'][]='error, no se registra pronombre.';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['isopais'])){
	$Log['tx'][]='error, no se registra el pais.';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['numeroid'])){
	$Log['tx'][]='error, no se registra el dni.';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['password'])){
	$Log['tx'][]='error, no se registra password.';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['password2'])){
	$Log['tx'][]='error, no se registra el password2.';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['cambiocontrasena'])){
	$Log['tx'][]='error, no se registra el cambiocontrasena.';
	$Log['res']='err';
	terminar($Log);	
}



$codes=array(
'AD'=>'', 'AE'=>'', 'AF'=>'', 'AG'=>'', 'AI'=>'', 'AL'=>'', 'AM'=>'', 'AO'=>'', 
'AQ'=>'', 'AR'=>'', 'AS'=>'', 'AT'=>'', 'AU'=>'', 'AW'=>'', 'AX'=>'', 'AZ'=>'', 
'BA'=>'', 'BB'=>'', 'BD'=>'', 'BE'=>'', 'BF'=>'', 'BG'=>'', 'BH'=>'', 'BI'=>'', 
'BJ'=>'', 'BL'=>'', 'BM'=>'', 'BN'=>'', 'BO'=>'', 'BQ'=>'', 'BR'=>'', 'BS'=>'', 
'BT'=>'', 'BV'=>'', 'BW'=>'', 'BY'=>'', 'BZ'=>'', 'CA'=>'', 'CC'=>'', 'CD'=>'', 
'CF'=>'', 'CG'=>'', 'CH'=>'', 'CI'=>'', 'CK'=>'', 'CL'=>'', 'CM'=>'', 'CN'=>'', 
'CO'=>'', 'CR'=>'', 'CU'=>'', 'CV'=>'', 'CW'=>'', 'CX'=>'', 'CY'=>'', 'CZ'=>'', 
'DE'=>'', 'DJ'=>'', 'DK'=>'', 'DM'=>'', 'DO'=>'', 'DZ'=>'', 'EC'=>'', 'EE'=>'', 
'EG'=>'', 'EH'=>'', 'ER'=>'', 'ES'=>'', 'ET'=>'', 'FI'=>'', 'FJ'=>'', 'FK'=>'', 
'FM'=>'', 'FO'=>'', 'FR'=>'', 'GA'=>'', 'GB'=>'', 'GD'=>'', 'GE'=>'', 'GF'=>'', 
'GG'=>'', 'GH'=>'', 'GI'=>'', 'GL'=>'', 'GM'=>'', 'GN'=>'', 'GP'=>'', 'GQ'=>'', 
'GR'=>'', 'GS'=>'', 'GT'=>'', 'GU'=>'', 'GW'=>'', 'GY'=>'', 'HK'=>'', 'HM'=>'', 
'HN'=>'', 'HR'=>'', 'HT'=>'', 'HU'=>'', 'ID'=>'', 'IE'=>'', 'IL'=>'', 'IM'=>'', 
'IN'=>'', 'IO'=>'', 'IQ'=>'', 'IR'=>'', 'IS'=>'', 'IT'=>'', 'JE'=>'', 'JM'=>'', 
'JO'=>'', 'JP'=>'', 'KE'=>'', 'KG'=>'', 'KH'=>'', 'KI'=>'', 'KM'=>'', 'KN'=>'', 
'KP'=>'', 'KR'=>'', 'KW'=>'', 'KY'=>'', 'KZ'=>'', 'LA'=>'', 'LB'=>'', 'LC'=>'', 
'LI'=>'', 'LK'=>'', 'LR'=>'', 'LS'=>'', 'LT'=>'', 'LU'=>'', 'LV'=>'', 'LY'=>'', 
'MA'=>'', 'MC'=>'', 'MD'=>'', 'ME'=>'', 'MF'=>'', 'MG'=>'', 'MH'=>'', 'MK'=>'', 
'ML'=>'', 'MM'=>'', 'MN'=>'', 'MO'=>'', 'MP'=>'', 'MQ'=>'', 'MR'=>'', 'MS'=>'', 'MT'=>'', 
'MU'=>'', 'MV'=>'', 'MW'=>'', 'MX'=>'', 'MY'=>'', 'MZ'=>'', 'NA'=>'', 'NC'=>'', 
'NE'=>'', 'NF'=>'', 'NG'=>'', 'NI'=>'', 'NL'=>'', 'NO'=>'', 'NP'=>'', 'NR'=>'', 
'NU'=>'', 'NZ'=>'', 'OM'=>'', 'PA'=>'', 'PE'=>'', 'PF'=>'', 'PG'=>'', 'PH'=>'', 
'PK'=>'', 'PL'=>'', 'PM'=>'', 'PN'=>'', 'PR'=>'', 'PS'=>'', 'PT'=>'', 'PW'=>'', 
'PY'=>'', 'QA'=>'', 'RE'=>'', 'RO'=>'', 'RS'=>'', 'RU'=>'', 'RW'=>'', 'SA'=>'', 
'SB'=>'', 'SC'=>'', 'SD'=>'', 'SE'=>'', 'SG'=>'', 'SH'=>'', 'SI'=>'', 'SJ'=>'', 
'SK'=>'', 'SL'=>'', 'SM'=>'', 'SN'=>'', 'SO'=>'', 'SR'=>'', 'SS'=>'', 'ST'=>'', 
'SV'=>'', 'SX'=>'', 'SY'=>'', 'SZ'=>'', 'TC'=>'', 'TD'=>'', 'TF'=>'', 'TG'=>'', 
'TH'=>'', 'TJ'=>'', 'TK'=>'', 'TL'=>'', 'TM'=>'', 'TN'=>'', 'TO'=>'', 'TR'=>'', 
'TT'=>'', 'TV'=>'', 'TW'=>'', 'TZ'=>'', 'UA'=>'', 'UG'=>'', 'UM'=>'', 'US'=>'', 
'UY'=>'', 'UZ'=>'', 'VA'=>'', 'VC'=>'', 'VE'=>'', 'VG'=>'', 'VI'=>'', 'VN'=>'', 
'VU'=>'', 'WF'=>'', 'WS'=>'', 'YE'=>'', 'YT'=>'', 'ZA'=>'', 'ZM'=>'', 'ZW'=>'');

if(!isset($codes[$_POST['isopais']])){
	$Log['tx'][]='error, al reconocer del pais en la base de datos.';
	$Log['mg'][]='error, al reconocer del pais en la base de datos.';
	$Log['res']='err';
	terminar($Log);	
}



$query="
	SELECT 
		sis_usu_registro.*
	FROM
		geogec.sis_usu_registro
	WHERE  
	
	id!='".$_SESSION["geogec"]["usuario"]['id']."'
	AND
	log='".$_POST['log']."'
	";
	
/*$link=mysql_connect($server,$dbuser,$dbpass);
$result=mysql_db_query($database,$query,$link);*/

$ConsultaUsu = pg_query($ConecSIG, $query);

if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);	
}	

if(pg_num_rows($ConsultaUsu)>0){
	$Log['tx'][]='error, ya se encuentra registrado el log solicitado.';
	$Log['res']='err';
	terminar($Log);	
}




$query="
	SELECT 
		sis_usu_registro.*
	FROM
		geogec.sis_usu_registro
	WHERE  		
		id='".$_SESSION["geogec"]["usuario"]['id']."'
	
	";
	
/*$link=mysql_connect($server,$dbuser,$dbpass);
$result=mysql_db_query($database,$query,$link);*/

$ConsultaUsu = pg_query($ConecSIG, $query);

if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);	
}	

$usudat=pg_fetch_assoc($ConsultaUsu);




if(
	$usudat['numeroid']==''
	&&
	$_POST['numeroid']!=''
){


	$query="
		SELECT
			*
			FROM
			
			geogec.sis_usu_registro 
			
			WHERE
				numeroid='".$_POST['numeroid']."'
				AND
				isopais='".$_POST['isopais']."'
				AND
				id != '".$_SESSION["geogec"]["usuario"]['id']."'
			
	";
		
	$ConsultaUsu = pg_query($ConecSIG, $query);

	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.utf8_encode($query);
		$Log['res']='err';
		terminar($Log);	
	}	

	if(pg_num_rows($ConsultaUsu)>0){
		$Log['tx'][]='error: el número de DNI ya está registrado. COnsulte a su administrador';
		$Log['mg'][]='error: el número de DNI ya está registrado. COnsulte a su administrador';
		$Log['res']='err';
		terminar($Log);	
	}


	$query="
		UPDATE 
			geogec.sis_usu_registro
			SET
				numeroid = 	'".$_POST['numeroid']."'
			WHERE
				id='".$_SESSION["geogec"]["usuario"]['id']."'
	";
		
	$ConsultaUsu = pg_query($ConecSIG, $query);

	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.utf8_encode($query);
		$Log['res']='err';
		terminar($Log);	
	}	

}

$query="
	UPDATE 
	
		geogec.sis_usu_registro
		
		SET		
			log ='".$_POST['log']."',
			nombre ='".$_POST['nombre']."', 
			apellido ='".$_POST['apellido']."',
			email ='".$_POST['email']."',
			numeroid = '".$_POST['numeroid']."',
			isopais ='".$_POST['isopais']."',
			pronombre = '".$_POST['pronombre']."'
			
		WHERE
		
			id='".$_SESSION["geogec"]["usuario"]['id']."'
";
	
$ConsultaUsu = pg_query($ConecSIG, $query);

if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.utf8_encode($query);
	$Log['res']='err';
	terminar($Log);	
}	




if(!isset($_POST['password'])){
	$Log['tx'][]='error, no se registra password.';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['password2'])){
	$Log['tx'][]='error, no se registra el password2.';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['cambiocontrasena'])){
	$Log['tx'][]='error, no se registra el cambiocontrasena.';
	$Log['res']='err';
	terminar($Log);	
}


		
if($_POST['cambiocontrasena']=='si'){
	
	if(strlen($_POST['password'])<4){
		$Log['tx'][]='la contrasena no alcanza el mínimmo de 4 caracteres.';
		$Log['res']='err';
		terminar($Log);	
	}

	if($_POST['password2']!==$_POST['password']){
		$Log['tx'][]='error, no coinciden las dos conrtasenas suministradas.';
		$Log['res']='err';
		terminar($Log);	
	}
		
	$query="
		UPDATE 
		
			geogec.sis_usu_registro
			
			SET		
				pass ='".md5($_POST['password'])."'
				
			WHERE			
				id='".$_SESSION["geogec"]["usuario"]['id']."'
	";
		
	$ConsultaUsu = pg_query($ConecSIG, $query);

	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.utf8_encode($query);
		$Log['res']='err';
		terminar($Log);	
	}	
}
		
		
		
		
$Log['tx'][]='se edito correctamente.';
$Log['res']='exito';
terminar($Log);	
