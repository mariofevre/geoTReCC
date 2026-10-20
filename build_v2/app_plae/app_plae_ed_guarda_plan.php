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

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}


foreach($_POST as $k => $v){
	//$_POST[$k] = utf8_decode($v);
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

$Id=$_POST['id'];	
$Log['data']['id']=$Id;
$Log['data']['nivel']=$_POST['nivel'];
$Log['data']['modo']=$_POST['modo'];

/*
if($_POST['id_p_GRAactores']=='n'){
	//está solicitando la creación de un nuevo actor
	$query="
		SELECT 
			GRAactores.id,
		    GRAactores.nombre,
		    GRAactores.apellido,
		    GRAactores.mail,
		    GRAactores.reporte
		FROM 
			$DB.GRAactores
		where  
			GRAactores.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
	";		
	$Consulta=	pg_query($ConecSIG, $query);;
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	    $Log['res']='err';
	    terminar($Log);
	}
	$Actores=array();
	$Log['data']['ActoresAsignados']=array();
	while($row = pg_fetch_assoc($Consulta)){
		foreach($row as $k => $v){
			
			$nom=$row['nombre'].$row['apellido'];
			$nom=cadenaLimpiar($nom);
			$nom=str_replace($nom,' ','');
			if(strtolower($_POST['id_p_GRAactores_n'])==$nom){
				//encontramos un actor similar.
				$_POST['id_p_GRAactores']=$row['id'];
				$_POST['id_p_GRAactores_n']='';	
			}	
		}
	}	
}


if($_POST['id_p_GRAactores']=='n'){
	
	$e=explode(' ',$_POST['id_p_GRAactores_n']);
	foreach($e as $k => $v){
		if($v == ''){unset($e[$k]);}
	}
	if(count($e)>=4){
		$ape = $e[(count($e)-2)].' '.$e[(count($e)-1)]; 
	
		$nom='';
		$c=0;
		foreach($e as $k => $v){
			if($c>=(count($e)-2)){break;}
			$nom.=$v.' ';
			$c++;
		}
		$nom=substr($nom, 0,-1);
	}else if(count($e)==1){
		$ape = '';
		$nom='';
		foreach($e as $k => $v){
			$nom.=$v;
		}	
	}else{
		$ape = $e[(count($e)-1)];
		$nom='';
		$c=0;
		foreach($e as $k => $v){
			if($c>=(count($e)-1)){break;}
			$nom.=$v.' ';
			$c++;
		}
		$nom=substr($nom, 0,-1);		
	}
	
	$query="
		INSERT INTO 
			$DB.GRAactores
		SET
			GRAactores.nombre='".$nom."',
		    GRAactores.apellido='".$ape."',
			GRAactores.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
	";
	$Consulta=	pg_query($ConecSIG, $query);;
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	    $Log['res']='err';
	    terminar($Log);
	}
	$Anid=$Conec1->insert_id;
	$_POST['id_p_GRAactores']=$Anid;
}
*/

	$tabla=str_replace('PLAn','ref_plae_nivel',$_POST['nivel']);


$query="
	UPDATE
		$DB.".$tabla."
	SET
	   nombre = '".$_POST['nombre']."',
	   numero = '".$_POST['numero']."',
	   descripcion = '".$_POST['descripcion']."',
	   co_color = '".$_POST['co_color']."',
	   zz_publico = '".$_POST['zz_publico']."',
	   id_p_rele_campa = '".$_POST['id_p_rele_campa']."',
	   id_p_rele_campo = '".$_POST['id_p_rele_campo']."',
	   filtro_rele_valor = '".$_POST['filtro_rele_valor']."'
	    
	Where 
		ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
	AND
	id =   '".$_POST['id']."'
";		

$Consulta=	pg_query($ConecSIG, $query);

if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error al consultar columnas';
    $Log['tx'][]=utf8_encode($query);
    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
    $Log['res']='err';
    terminar($Log);
}
//$Log['tx'][]=utf8_encode($query);


if($tabla=='PLAn1'){$idp1=$_POST['id'];}else{$idp1='NULL';}
if($tabla=='PLAn2'){$idp2=$_POST['id'];}else{$idp2='NULL';}
if($tabla=='PLAn3'){$idp3=$_POST['id'];}else{$idp3='NULL';}

if($_POST['estado']!=''&&$_POST['desde']!=''){
	
	$query="
		
		INSERT INTO 
			paneles.PLAestados(
				nombre, desde, 
				ic_p_est_02_marcoacademico, 
				id_p_plae_nivel1, id_p_plae_nivel2, id_p_plae_nivel3
			)
			VALUES(
				'".$_POST['estado']."', '".$_POST['desde']."', 
				'".$_POST['codMarco']."', 
				$idp1, $idp2, $idp3			
			)
	";
	
	$Consulta =	pg_query($ConecSIG, $query);
	
	
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	    $Log['res']='err';
	    terminar($Log);
	}
	
	
	$Log['tx'][]=utf8_encode($query);
	
	$query="
		UPDATE
			$DB.".$tabla."
		SET
			zz_ultimoestado='".$_POST['estado']."',
			zz_ultimoestado_desde='".$_POST['desde']."'
		WHERE
			ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
		AND
			id = '".$_POST['id']."'
	";
	$Consulta=	pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	    $Log['res']='err';
	    terminar($Log);
	}
//$Log['tx'][]=utf8_encode($query);
	
}
	

foreach($_POST as $k => $v){
	$i=explode('_',$k);	
	if($i[0]=='adj'){
		$campo=str_replace($i[0].'_'.$i[1].'_','',$k);
		if($v=='undefined'){$v=0;}
		$query="
		UPDATE
			$DB.ref_plae_link_ref_01_documentos
		SET
		".$campo." = '".$v."'
		WHERE 
		id = '".$i[1]."'
		AND		
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."' 	
		";		
		pg_query($ConecSIG, $query);		
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error al consultar estados configurados de las comunicaciones';
			$Log['tx'][]=utf8_encode($query);
			$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
			$Log['res']='err';
			terminar($Log);
		}
	}					
}


$campolink='id_p_'.str_replace('ref_','',$tabla);

$and=$campolink."= '".$_POST['id']."'";

$query="
	SELECT 
			id, 
			id_p_plae_categorias , 
			valor
		FROM 
			$DB.ref_plae_categorias_valor
		WHERE 
			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."' 	
		AND
			$and
	 ";

$Consulta=pg_query($ConecSIG, $query);		
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error al consultar estados configurados de las comunicaciones';
	$Log['tx'][]=utf8_encode($query);
	$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
	$Log['res']='err';
	terminar($Log);
}

while($row = pg_fetch_assoc($Consulta)){
	$valorescat[$row['id_p_plae_categorias ']]=$row['id'];
}	
		
foreach($_POST as $k => $v){
	$e=explode('_',$k);
	if($e[0]!='categoria'){continue;}
	
	if(!isset($valorescat[$e[1]])){
		
		$query="
			INSERT INTO 
				$DB.ref_plae_categorias_valor
			SET
				id_p_plae_categorias ='".$e[1]."',
				valor = '".$v."', 
				".$campolink." = '".$_POST['id']."',
				ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
		";			
		
		pg_query($ConecSIG, $query);		
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error al crear nuevo registro de valor para categoria en este '.$campolink;
			$Log['tx'][]=utf8_encode($query);
			$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
			$Log['res']='err';
			terminar($Log);
		}
		
	}else{
		
		$query="
			UPDATE 
				$DB.ref_plae_categorias_valor
			SET
				valor = '".$v."'
			WHERE
					id_p_plae_categorias ='".$e[1]."'
				AND 
					".$campolink." = '".$_POST['id']."'
				AND
					ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
		";			
		pg_query($ConecSIG, $query);		
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error al crear nuevo registro de valor para categoria en este '.$campolink;
			$Log['tx'][]=utf8_encode($query);
			$Log['tx'][]=utf8_encode(pg_errormessage($ConecSIG));
			$Log['res']='err';
			terminar($Log);
		}
		//$Log['tx'][]='actualizando: '.$query;
	}
} 

	

$Log['res']='exito';
terminar($Log);
