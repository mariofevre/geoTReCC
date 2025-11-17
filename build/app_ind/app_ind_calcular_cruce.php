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



if(!isset($_SESSION[$CU])){
	$Log['tx'][]='sesión caduca';
	$Log['acc'][]='login';
	terminar($Log);	
}

$idUsuario = $_SESSION[$CU]["usuario"]['id'];


$query="SELECT  
			ref_indicadores_indicadores.*,
			ref_capasgeo.tipogeometria
        FROM   
        	$DB.ref_indicadores_indicadores
        LEFT JOIN
			$DB.ref_capasgeo
			ON ref_capasgeo.id = ref_indicadores_indicadores.id_p_ref_capasgeo
		WHERE
			ref_indicadores_indicadores.zz_borrada='0'
     ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

while($row=pg_fetch_assoc($Consulta)){
	$Log['data']['indicadores'][$row['id']]=$row;
}

if($Log['data']['indicadores'][$_POST['id_ind_1']]['tipogeometria']=='Point'){
	$campogeo='geom_point';
}elseif($Log['data']['indicadores'][$_POST['id_ind_1']]['tipogeometria']=='LineString'){
	$campogeo='geom_line';
}else{
	$campogeo='geom';
}


if($Log['data']['indicadores'][$_POST['id_ind_2']]['tipogeometria']=='Point'){
	$campogeo2='geom_point';
}elseif($Log['data']['indicadores'][$_POST['id_ind_2']]['tipogeometria']=='LineString'){
	$campogeo2='geom_line';
}else{
	$campogeo2='geom';
}

if($campogeo=='geom'){
	$superp='i1.'.$campogeo.', i2.'.$campogeo2;
	
}else{
	$superp='i2.'.$campogeo2.', i1.'.$campogeo;
}


$Log['data']['variable_principal']['indicador']=$Log['data']['indicadores'][$_POST['id_ind_1']];
$Log['data']['variable_principal']['nombre']=$Log['data']['indicadores'][$_POST['id_ind_1']][$_POST['campo_ind_1'].'_nom'];
$Log['data']['variable_principal']['unidad']=$Log['data']['indicadores'][$_POST['id_ind_1']][$_POST['campo_ind_1'].'_unidad'];

$Log['data']['variable_secundaria']['indicador']=$Log['data']['indicadores'][$_POST['id_ind_2']];
$Log['data']['variable_secundaria']['nombre']=$Log['data']['indicadores'][$_POST['id_ind_2']][$_POST['campo_ind_2'].'_nom'];
$Log['data']['variable_secundaria']['unidad']=$Log['data']['indicadores'][$_POST['id_ind_2']][$_POST['campo_ind_2'].'_unidad'];

$query="SELECT  
			*
        FROM   
        	$DB.ref_capasgeo
        WHERE 
            id = '".$_POST['id_capa_3']."'
     ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

while($row=pg_fetch_assoc($Consulta)){
	$Log['data']['capa']=$row;
}

$str_limpio=str_replace('ogc:','',$Log['data']['capa']['sld']); // las etiquetas sld contienen ogc: como parte del nombre, pero SimpleXMLElement no los interpreta, por eso los renombramos.
$c3_sld = new SimpleXMLElement($str_limpio); 
$estilo = $c3_sld->NamedLayer->UserStyle->FeatureTypeStyle;

$Log['data']['capa']['reglas']=array();
foreach($estilo->Rule as $rule){
	
	$arr=array();
	$arr['nombre']=$rule->Name;
	
	$arr['campo']=$rule->Filter->And->PropertyIsGreaterThanOrEqualTo->PropertyName;
	$arr['min']=$rule->Filter->And->PropertyIsGreaterThanOrEqualTo->Literal;
	$arr['max']=$rule->Filter->And->PropertyIsLessThan->Literal;

	$arr['color']=(string)$rule->PolygonSymbolizer->Fill->CssParameter[0];
	
	$Log['data']['capa']['reglas'][]=$arr;
}



//para 3 compoenentes
//para geometrias fijas
//para mensuales con darios
$query="
	SELECT
			i1.id as id1,
			i2.id as id2,
			c3.id as id3,
			val_i1.id as iv1,
			val_i2.id as iv2,
			
			val_i1.".$_POST['campo_ind_1']."_dato as campo_i_1,
			ST_AsText(i1.".$campogeo.") as geotx,
			i1.$campogeo ,
			val_i2.".$_POST['campo_ind_2']."_dato as campo_i_2,
			c3.texto1,
			c3.texto2,
			c3.texto3,
			c3.texto4,
			c3.texto5,
			c3.numero1,
			c3.numero2,
			c3.numero3,
			c3.numero4,
			c3.numero5,
			val_i1.ano as i1_ano,
			val_i1.mes as i1_mes,
			val_i1.dia as i1_dia,
			val_i2.ano as i2_ano,
			val_i2.mes as i2_mes,
			val_i2.dia as i2_dia
			
			FROM    
            $DB.ref_capasgeo_registros as i1,
            $DB.ref_indicadores_valores as val_i1,
            
            $DB.ref_capasgeo_registros as i2,
            $DB.ref_indicadores_valores as val_i2,
            
            $DB.ref_capasgeo_registros as c3
        WHERE
            ST_Contains($superp) IS TRUE
            AND
            ST_Contains(c3.geom, i1.geom_point) IS TRUE
			AND
			i1.id_ref_capasgeo='".$Log['data']['indicadores'][$_POST['id_ind_1']]['id_p_ref_capasgeo']."'
			AND
			i2.id_ref_capasgeo='".$Log['data']['indicadores'][$_POST['id_ind_2']]['id_p_ref_capasgeo']."'
			AND
			c3.id_ref_capasgeo='".$_POST['id_capa_3']."'
			
			AND 
				val_i1.id_p_ref_capas_registros = i1.id
			AND
				val_i1.zz_superado=0
			AND 
				val_i2.id_p_ref_capas_registros = i2.id
			AND
				val_i2.zz_superado=0
					
			AND 
				val_i1.id_p_ref_indicadores_indicadores = '".$_POST['id_ind_1']."'
			AND 
				val_i2.id_p_ref_indicadores_indicadores = '".$_POST['id_ind_2']."'
			AND
				val_i1.ano=val_i2.ano
			AND
				val_i1.mes=val_i2.mes 
";

//echo $query;
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}


$Log['data']['resumen']['x']=array();
$Log['data']['resumen']['y']=array();

$Agrupado=array();
while($row=pg_fetch_assoc($Consulta)){
	
	if(!isset($Agrupado[$row['iv1']])){
		$Agrupado[$row['iv1']]=array();		
		$Agrupado[$row['iv1']]['suma']=0;
		$Agrupado[$row['iv1']]['cant']=0;
	}
	
	$Agrupado[$row['iv1']]['suma']+=(float)$row['campo_i_2'];		
	$Agrupado[$row['iv1']]['cant']++;
	
	if(!isset($Log['data']['resumen']['x']['max'])){$Log['data']['resumen']['x']['max']=(float)$row['campo_i_1'];}
	if(!isset($Log['data']['resumen']['x']['min'])){$Log['data']['resumen']['x']['min']=(float)$row['campo_i_1'];}
	if(!isset($Log['data']['resumen']['y']['max'])){$Log['data']['resumen']['y']['max']=(float)$row['campo_i_2'];}
	if(!isset($Log['data']['resumen']['y']['min'])){$Log['data']['resumen']['y']['min']=(float)$row['campo_i_2'];}
	
	$Log['data']['resumen']['x']['max']=max($Log['data']['resumen']['x']['max'],(float)$row['campo_i_1']);
	$Log['data']['resumen']['x']['min']=min($Log['data']['resumen']['x']['min'],(float)$row['campo_i_1']);
	$Log['data']['resumen']['y']['max']=max($Log['data']['resumen']['y']['max'],(float)$row['campo_i_2']);
	$Log['data']['resumen']['y']['min']=min($Log['data']['resumen']['y']['min'],(float)$row['campo_i_2']);
			
			
			
	foreach($Log['data']['capa']['reglas'] as $r){
					
		if(
			$row[(string)$r['campo']]>=$r['min']
			&&
			$row[(string)$r['campo']]<$r['max']
		){
			$row['color']=$r['color'];
			break;
		}	
	}
	
	
	$Log['data']['cruce'][$row['iv1']]['id1']=$row['id1'];
	$Log['data']['cruce'][$row['iv1']]['id2'][]=$row['id2'];
	$Log['data']['cruce'][$row['iv1']]['iv1']=$row['iv1'];
	$Log['data']['cruce'][$row['iv1']]['iv2'][]=$row['iv2'];
	$Log['data']['cruce'][$row['iv1']]['campo_i_1']=$row['campo_i_1'];
	$Log['data']['cruce'][$row['iv1']]['campo_i_2'][]=$row['campo_i_2'];
	$Log['data']['cruce'][$row['iv1']]['campo_i_2_suma']=$Agrupado[$row['iv1']]['suma'];
	$Log['data']['cruce'][$row['iv1']]['campo_i_2_promedio']=$Agrupado[$row['iv1']]['suma'] / $Agrupado[$row['iv1']]['cant'];	
	$Log['data']['cruce'][$row['iv1']]['texto1']=$row['texto1'];
	$Log['data']['cruce'][$row['iv1']]['texto2']=$row['texto2'];
	$Log['data']['cruce'][$row['iv1']]['texto3']=$row['texto3'];
	$Log['data']['cruce'][$row['iv1']]['texto4']=$row['texto4'];
	$Log['data']['cruce'][$row['iv1']]['texto5']=$row['texto5'];
	$Log['data']['cruce'][$row['iv1']]['numero1']=$row['numero1'];
	$Log['data']['cruce'][$row['iv1']]['numero2']=$row['numero2'];
	$Log['data']['cruce'][$row['iv1']]['numero3']=$row['numero3'];
	$Log['data']['cruce'][$row['iv1']]['numero4']=$row['numero4'];
	$Log['data']['cruce'][$row['iv1']]['numero5']=$row['numero5'];
	$Log['data']['cruce'][$row['iv1']]['i1_ano']=$row['i1_ano'];
	$Log['data']['cruce'][$row['iv1']]['i1_mes']=$row['i1_mes'];
	$Log['data']['cruce'][$row['iv1']]['i1_dia']=$row['i1_dia'];
	$Log['data']['cruce'][$row['iv1']]['i2_ano'][]=$row['i2_ano'];
	$Log['data']['cruce'][$row['iv1']]['i2_mes'][]=$row['i2_mes'];
	$Log['data']['cruce'][$row['iv1']]['i2_dia'][]=$row['i2_dia'];
	$Log['data']['cruce'][$row['iv1']]['color']=$row['color'];
}






$Log['res']="exito";
terminar($Log);
