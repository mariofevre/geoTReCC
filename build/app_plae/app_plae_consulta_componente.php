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
function terminar($Log){
    $res=json_encode($Log);
    if($res==''){$Log['tx'][]=erroresJson();$res=print_r($Log,true);}
    echo $res;
    exit;
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

if($Id == ''){
	
	if($_POST['nivel']=='PLAn1'){
		$extra='';	
	}elseif($_POST['nivel']=='PLAn2'){
		$extra="`".$_POST['nivel']."`.`id_p_PLAn1` = '".$_POST['id_p_PLA']."',";
	}elseif($_POST['nivel']=='PLAn3'){
		$extra="`".$_POST['nivel']."`.`id_p_PLAn2` = '".$_POST['id_p_PLA']."',";
	}
	
	
    $query="		
        INSERT INTO 
        `paneles`.`".$_POST['nivel']."`
        SET
        `zz_AUTOPANEL` = '".$PanelI."',
        ".$extra."
        `zz_preliminar`='1'
    ";							
    $Conec1->query($query);;
    if($Conec1->error!=''){
        $Log['tx'][]='error al consultar columnas';
        $Log['tx'][]=utf8_encode($query);
        $Log['tx'][]=utf8_encode($Conec1->error);
        $Log['res']='err';
        terminar($Log);
    }
    
    $Id = $Conec1->insert_id;
    
    if($Id<1){
        $Log['tx'][]='error al generar un nuevo id: '.$Id;
        $Log['tx'][]=utf8_encode($query);
        $Log['tx'][]=utf8_encode($Conec1->error);
        $Log['res']='err';
        terminar($Log);
    }

}	

unset($_POST['id']);


	$query="
		SELECT 
			`GRAactores`.`id`,
		    `GRAactores`.`nombre`,
		    `GRAactores`.`apellido`,
		    `GRAactores`.`mail`,
		    `GRAactores`.`reporte`
		FROM 
			`paneles`.`GRAactores`
		where  
			`GRAactores`.`zz_AUTOPANEL`='$PanelI'
	";		
	$Consulta=	$Conec1->query($query);;
	if($Conec1->error!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode($Conec1->error);
	    $Log['res']='err';
	    terminar($Log);
	}
	$Actores=array();
	$Log['data']['ActoresAsignados']=array();
	while($row = $Consulta->fetch_assoc()){
		foreach($row as $k => $v){
			$Actores[$row['id']][$k]=utf8_encode($v);	
			$Actores[$row['id']]['elementos']=0;	
		}
	}


	$query="
		SELECT 
			`PLAcategorias`.`id`,
		    `PLAcategorias`.`nivel`,
		    `PLAcategorias`.`nombre`,
		    `PLAcategorias`.`orden`,
		    `PLAcategorias`.`zz_AUTOPANEL`
		FROM `paneles`.`PLAcategorias`
		Where zz_AUTOPANEL='$PanelI' 
		ORDER by orden
	";		
	$Consulta=	$Conec1->query($query);;
	if($Conec1->error!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode($Conec1->error);
	    $Log['res']='err';
	    terminar($Log);
	}
	$CAT['PN1']=array();
	$CAT['PN2']=array();
	$CAT['PN3']=array();
	while($row = $Consulta->fetch_assoc()){
		foreach($row as $k => $v){
			$CAT[$row['nivel']][$row['id']][$k]=utf8_encode($v);	
		}	
	}	
	

	$query="
		SELECT 
			`PLAcategoriasVal`.`id`,
		    `PLAcategoriasVal`.`id_p_PLAcategorias`,
		    `PLAcategoriasVal`.`id_p_PLAn1`,
		    `PLAcategoriasVal`.`id_p_PLAn2`,
		    `PLAcategoriasVal`.`id_p_PLAn3`,
		    `PLAcategoriasVal`.`valor`,
		    `PLAcategoriasVal`.`zz_AUTOPANEL`
		FROM `paneles`.`PLAcategoriasVal`
		Where zz_AUTOPANEL='$PanelI' 
	";		
	$Consulta=	$Conec1->query($query);;
	if($Conec1->error!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode($Conec1->error);
	    $Log['res']='err';
	    terminar($Log);
	}
	while($row = $Consulta->fetch_assoc()){
		if($row['id_p_PLAn1']>0){
			foreach($row as $k => $v){
				$PNCatVals['PN1'][$row['id_p_PLAn1']][$row['id_p_PLAcategorias']]=utf8_encode($row['valor']);	
			}	
		}	
		if($row['id_p_PLAn2']>0){
			foreach($row as $k => $v){
				$PNCatVals['PN2'][$row['id_p_PLAn2']][$row['id_p_PLAcategorias']]=utf8_encode($row['valor']);	
			}	
		}	
		if($row['id_p_PLAn3']>0){
				$PNCatVals['PN3'][$row['id_p_PLAn3']][$row['id_p_PLAcategorias']]=utf8_encode($row['valor']);
		}	
	}	

	$query="
		SELECT 
		 	`PLAestados`.`id`,
		    `PLAestados`.`nombre`,
		    `PLAestados`.`desde`,
		    `PLAestados`.`zz_AUTOPANEL`,
		    `PLAestados`.`id_p_PLAn1`,
		    `PLAestados`.`id_p_PLAn2`,
		    `PLAestados`.`id_p_PLAn3`
		FROM 
			`paneles`.`PLAestados`
		Where 
			zz_AUTOPANEL='$PanelI' 
		order by desde desc 
	";		
	$Consulta=	$Conec1->query($query);;
	if($Conec1->error!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode($Conec1->error);
	    $Log['res']='err';
	    terminar($Log);
	}
	
	$PNestados['PN1']=array();
	$PNestados['PN2']=array();
	$PNestados['PN3']=array();
	while($row = $Consulta->fetch_assoc()){
		if($row['id_p_PLAn1']>0){
			$arr=array();
			foreach($row as $k => $v){
				$arr[$k]=utf8_encode($v);	
			}	
			$PNestados['PN1'][$row['id_p_PLAn1']][]=$arr;
		}	
		if($row['id_p_PLAn2']>0){
			
			$arr=array();
			foreach($row as $k => $v){
				$arr[$k]=utf8_encode($v);	
			}	
			$PNestados['PN2'][$row['id_p_PLAn2']][]=$arr;
	
		}			
		if($row['id_p_PLAn3']>0){
			
			$arr=array();
			foreach($row as $k => $v){
				$arr[$k]=utf8_encode($v);	
			}	
			$PNestados['PN3'][$row['id_p_PLAn3']][]=$arr;
		}	
	}

	$query="
		SELECT 
			`PLAn3`.`id`,
		    `PLAn3`.`nombre`,
		    `PLAn3`.`descripcion`,
		    `PLAn3`.`FI_imagen`,		    
		    `PLAn3`.`id_p_PLAn2`,
		    `PLAn3`.`zz_AUTOPANEL`,
		   	`PLAn3`.`CO_color`,  		    
		    `PLAn3`.`numero`,
		    `PLAn3`.`id_p_GRAactores`
		FROM `paneles`.`PLAn3`
		Where zz_AUTOPANEL='$PanelI' 
	";	
	$PN3=array();	
	$Consulta=	$Conec1->query($query);;
	if($Conec1->error!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode($Conec1->error);
	    $Log['res']='err';
	    terminar($Log);
	}
	while($row = $Consulta->fetch_assoc()){
		
		if(isset($Actores[$row['id_p_GRAactores']])){$Actores[$row['id_p_GRAactores']]['elementos']++;}else{$row['id_p_GRAactores']='';}
		
		foreach($row as $k => $v){
			$PN3[$row['id']][$k]=utf8_encode($v);	
		}	
		
		$PN3[$row['id']]['documentos']=array();
		
		$b['id']=$row['id'];
		$plas2[$row['id_p_PLAn2']][]=$b;
		
		
		
		$PN3[$row['id']]['categorias']=Array();
		
			//echo "$k , ".$v['nombre']." <br>"; 
			//print_r($PNCatVals['PN1'][$row['id']][$k]);
		if(isset($PNCatVals['PN3'][$row['id']])){
			$PN3[$row['id']]['categorias']=$PNCatVals['PN3'][$row['id']];
		}					
	
		
		if($row['id_p_GRAactores']>0){
			if(isset($Log['data']['Actores'][$row['id_p_GRAactores']])){
				$val=array();
				$val['id']=$row['id'];
				$Log['data']['ActoresAsignados'][$row['id_p_GRAactores']]['PLAn3'][]=$val;
			}
		}
		
		if(!isset($PNestados['PN3'][$row['id']])){$PNestados['PN3'][$row['id']]=array();}
		$PN3[$row['id']]['estados']=$PNestados['PN3'][$row['id']];	
	}


	$query="
		SELECT `PLAn2`.`id`,
		    `PLAn2`.`nombre`,
		    `PLAn2`.`numero`,
		   	`PLAn2`.`CO_color`,   
		    `PLAn2`.`descripcion`,
		    `PLAn2`.`id_p_PLAn1`,
		    `PLAn2`.`zz_AUTOPANEL`,
		   	`PLAn2`.id_p_GRAactores
		FROM `paneles`.`PLAn2`
		Where zz_AUTOPANEL='$PanelI' 
	";		
	$Consulta=	$Conec1->query($query);;
	if($Conec1->error!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode($Conec1->error);
	    $Log['res']='err';
	    terminar($Log);
	}
	$PN2=array();
	while($row = $Consulta->fetch_assoc()){
		if(isset($Actores[$row['id_p_GRAactores']])){$Actores[$row['id_p_GRAactores']]['elementos']++;}else{$row['id_p_GRAactores']='';}
		foreach($row as $k => $v){
			$PN2[$row['id']][$k]=utf8_encode($v);	
		}
		$PN2[$row['id']]['documentos']=array();
		
		if(!isset($plas2[$row['id']])){
			$plas2[$row['id']]=array();
		}	
		
		$b['id']=$row['id'];
		$b['PLAn3']['componentes']=$plas2[$row['id']];
		$plas1[$row['id_p_PLAn1']][]=$b;
		
		
		
		
		$PN2[$row['id']]['categorias']=Array();
		if(isset($PNCatVals['PN2'][$row['id']])){
			$PN2[$row['id']]['categorias']=$PNCatVals['PN2'][$row['id']];
		}		
		
		if($row['id_p_GRAactores']>0){
			if(isset($Log['data']['Actores'][$row['id_p_GRAactores']])){
				$val=array();
				$val['id']=$row['id'];
				$Log['data']['ActoresAsignados'][$row['id_p_GRAactores']]['PLAn2'][]=$val;
			}
		}
		if(!isset($PNestados['PN2'][$row['id']])){$PNestados['PN2'][$row['id']]=array();}
		$PN2[$row['id']]['estados']=$PNestados['PN2'][$row['id']];			
	}	

	$query="
		SELECT 
			`PLAn1`.`id`,
		    `PLAn1`.`nombre`,
		    `PLAn1`.`numero`,
		    `PLAn1`.`CO_color`,
		    `PLAn1`.`descripcion`,
		    `PLAn1`.id_p_GRAactores
		FROM 
			`paneles`.`PLAn1`
		Where 
			zz_AUTOPANEL='$PanelI'
			AND
			zz_borrada='0' 
	";		
	$Consulta=	$Conec1->query($query);;
	if($Conec1->error!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode($Conec1->error);
	    $Log['res']='err';
	    terminar($Log);
	}
	$PLA=array();
	$PLA['PLAn1']=array();
	$PN1=array();
	while($row = $Consulta->fetch_assoc()){
		if(isset($Actores[$row['id_p_GRAactores']])){$Actores[$row['id_p_GRAactores']]['elementos']++;}else{$row['id_p_GRAactores']='';}
		foreach($row as $k => $v){
			$PN1[$row['id']][$k]=utf8_encode($v);	
		}
		$PN1[$row['id']]['documentos']=array();
		
		if(!isset($plas1[$row['id']])){
			$plas1[$row['id']]=array();
		}	
		
		$b['id']=$row['id'];
		$b['PLAn2']['componentes']=$plas1[$row['id']];		
		$PLA['PLAn1']['componentes'][]=$b;
		
		

		
		$PN1[$row['id']]['categorias']=Array();
		
		if(isset($PNCatVals['PN1'][$row['id']])){
			$PN1[$row['id']]['categorias']=$PNCatVals['PN1'][$row['id']];
		}
			
		
		if($row['id_p_GRAactores']>0){
			if(isset($Log['data']['Actores'][$row['id_p_GRAactores']])){
				$val=array();
				$val['id']=$row['id'];
				$Log['data']['ActoresAsignados'][$row['id_p_GRAactores']]['PLAn1'][]=$val;
			}
		}
		
		if(!isset($PNestados['PN1'][$row['id']])){$PNestados['PN1'][$row['id']]=array();}
		$PN1[$row['id']]['estados']=$PNestados['PN1'][$row['id']];					
		
	}	

	$query="
		SELECT 
			id, 
			id_p_PLAn1_id, id_p_PLAn2_id, id_p_PLAn3_id, 
			descripcion, FI_documento, FI_nombreorig, tipo
		FROM 
			PLAdocumentos
		Where 
			zz_AUTOPANEL='$PanelI' 
			AND
			zz_borrada='0'
	";		
	$Consulta=	$Conec1->query($query);;
	if($Conec1->error!=''){
	    $Log['tx'][]='error al consultar columnas';
	    $Log['tx'][]=utf8_encode($query);
	    $Log['tx'][]=utf8_encode($Conec1->error);
	    $Log['res']='err';
	    terminar($Log);
	}
	while($row = $Consulta->fetch_assoc()){
		
		if($row['id_p_PLAn1_id']>0){
			foreach($row as $k => $v){
				if(!isset($PN1[$row['id_p_PLAn1_id']])){continue;}
				$PN1[$row['id_p_PLAn1_id']]['documentos'][$row['id']][$k]=utf8_encode($v);
			}
		}elseif($row['id_p_PLAn2_id']>0){
			foreach($row as $k => $v){
				if(!isset($PN2[$row['id_p_PLAn2_id']])){continue;}
				$PN2[$row['id_p_PLAn2_id']]['documentos'][$row['id']][$k]=utf8_encode($v);
			}
		}elseif($row['id_p_PLAn3_id']>0){
			foreach($row as $k => $v){
				if(!isset($PN3[$row['id_p_PLAn3_id']])){continue;}
				$PN3[$row['id_p_PLAn3_id']]['documentos'][$row['id']][$k]=utf8_encode($v);
			}
		}
		
	}	

	//echo "<pre>";print_r($PN1);echo "</pre>";
	
	$CSV="idACC;numACC;nombACC;desACC;idPRO;nombPRO;idPLA;nombPLA".PHP_EOL;	
	 /* 
	foreach($PLA as $n1 => $n1d){
		foreach($n1d as $n2 => $n2d){			
			$o=array();
			$a=array();
			$b=array();
			foreach($n2d as $n3 => $n3d){
				$o[]=$PN3[$n3]['numero'];
				$a[$n3]=$n3;
				
				$des=preg_replace( "/\r|;|\n/", "", $PN3[$n3]['descripcion'] );
				$des=str_replace(";",", ",$des);
				
				$CSV.="$n3;".str_replace(";",", ",$PN3[$n3]['numero']).";".str_replace(";",", ",$PN3[$n3]['nombre']).";".$des.";$n2;".str_replace(";",", ",$PN2[$n2]['nombre']).";$n1;".str_replace(";",", ",$PN1[$n1]['nombre']).PHP_EOL;
			}
			
			array_multisort($o, SORT_NUMERIC,$a);
			foreach($a as $id){
				$b[$id]=$id;
			}
			
			if(count($b)>0){
				$PLA[$n1][$n2]=$b;
			}
			
			unset($o);
			unset($a);
			unset($b);

		}		
	}
	*/

	$carpeta="./documentos/p_$PanelI/temp";
	if(!file_exists($carpeta)){
		mkdir($carpeta, 0777, true);
		chmod($carpeta, 0777); 
	}
	file_put_contents ($carpeta.'/'.'Acciones.csv' , $CSV);
	//chmod($carpeta.'/'.'Acciones.csv',0777);
	//echo fread($temp, 1024);
	/*	
	foreach($PLA as $n1 => $n1d){	
			$o=array();
			$a=array();
			$b=array();
			foreach($n1d as $n2 => $n2d){
				$o[]=$PN2[$n2]['numero'];
				$a[$n2]=$n2;
				$d[$n2]=$n2d;
			}
			
			array_multisort($o,$a);
			foreach($a as $id){
				$b[$id]=$d[$id];
			}
			
			if(count($b)>0){
				$PLA[$n1]=$b;
			}
			
			unset($o);
			unset($a);
			unset($b);			
	}
	*/
	$Log['data']['componente']['nivel']=$_POST['nivel'];
	if($_POST['nivel']=='PLAn1'){
		$Log['data']['componente']=$PN1[$Id];
	}elseif($_POST['nivel']=='PLAn2'){
		$Log['data']['componente']=$PN2[$Id];
	}elseif($_POST['nivel']=='PLAn3'){
		$Log['data']['componente']=$PN3[$Id];
	}
	$Log['data']['componente']['nivel']=$_POST['nivel'];
	$Log['data']['PLA']=$PLA;
	$Log['data']['CAT']=$CAT;//categorías para cada nivel	
	$Log['data']['PN1']=$PN1;	
	$Log['data']['PN2']=$PN2;	
	$Log['data']['PN3']=$PN3;	
	$Log['data']['Actores']=$Actores;		
	
	
	
	
$Log['res']='exito';
terminar($Log);
	

	
	

