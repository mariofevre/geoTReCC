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

if(!isset($_POST['id'])){
    $Log['tx'][]='no fue enviada la variable id';
    $Log['res']='err';
    terminar($Log);	
}

$idUsuario = $_SESSION[$CU]["usuario"]['id'];

$query="SELECT  *
        FROM    $DB.ref_indicadores_indicadores
        WHERE 
                id = '".$_POST['id']."'
        AND
                usu_autor = '".$idUsuario."'
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
    $Log['tx'][]= "El usuario no es el autor de el indicador de id: ".$_POST['id'];
    $Log['data']=null;
} else {
    //Asumimos que solo devuelve una fila
    $fila=pg_fetch_assoc($Consulta);

    if ($fila['zz_borrada'] == '1'){
        $Log['tx'][]='El indicador ha sido borrado';
        $Log['mg'][]='El indicador ha sido borrado\n';
        $Log['res']='err';
    }
    
    if ($fila['zz_publicada'] == '1'){
        $Log['tx'][]='El indicador ya fue publicado';
        $Log['mg'][]='El indicador ya fue publicado\n';
        $Log['res']='err';
    }
    
    if ($fila['nombre'] == ''){
        $Log['tx'][]='El indicador necesita un nombre';
        $Log['mg'][]='El indicador necesita un nombre\n';
        $Log['res']='err';
    }
    
    if ($fila['descripcion'] == null || $fila['descripcion'] == ''){
        $Log['tx'][]='El indicador necesita una descripción';
        $Log['mg'][]='El indicador necesita una descripción\n';
        $Log['res']='err';
    }
        
    if ($fila['periodicidad'] == null || $fila['periodicidad'] == ''){
        $Log['tx'][]='El indicador no tiene definida su periodicidad';
        $Log['mg'][]='El indicador no tiene definida su periodicidad\n';
        $Log['res']='err';
    }
    
    if ($fila['fechahasta'] < $fila['fechadesde']){
        $Log['tx'][]='La fecha inicial es mayor a la fecha final';
        $Log['mg'][]='La fecha inicial es mayor a la fecha final\n';
        $Log['res']='err';
        terminar($Log);    
    }
    
    if ($Log['res'] == 'err'){
        terminar($Log);
    }
    
    $Log['tx'][]= "Consulta de indicador publicable id: ".$fila['id'];
    $Log['data']=$fila;
}

$Log['res']="exito";
terminar($Log);
