<?php 
/**
* colores.php
*
* colores se incorpora en la carpeta includes 
* ya que contiene funciones genéricas de operación de colores
* 
* @package    	intraTReCC
* @subpackage 	Comun
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2013 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 (GPL-3.0)
* Este archivo es parte de TReCC(tm) intraTReCC y de sus proyectos hermanos: baseobra(tm) y TReCC(tm) paneldecontrol.
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU General Public License" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
*/


/**
* cambia la notación de color de formato hexagecimal #rrggbbuna a rgb(rrr,ggg,bbb) a
* @param string $Color color a transformar. formato rgb(rrr,ggg,bbb)
* @return string Retorna color transformado a formato hexagecimal #rrggbbuna
*/
function rgbhexa($Color) {
	$rgb = str_replace("rgb(", "", $Color);
	$rgb = str_replace(")", "", $rgb);
	$rgb = explode(",",$rgb);
	
	$r = str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
	$g = str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
	$b = str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

	$resultado = '#'.$r.$g.$b;
   
	return $resultado;
}

/**
* cambia la notación de color de formato rgb(rrr,ggg,bbb) a hexagecimal #rrggbbuna
* @param string $Color color a transformar. formato hexagecimal #rrggbbuna
* @return string Retorna color transformado a formato rgb(rrr,ggg,bbb)
*/
function hexargb($Color) {
	
	 $hexa = str_replace("#", "", $Color);
	
   if(strlen($hexa) == 3) {
      $r = hexdec(substr($hexa,0,1).substr($hexa,0,1));
      $g = hexdec(substr($hexa,1,1).substr($hexa,1,1));
      $b = hexdec(substr($hexa,2,1).substr($hexa,2,1));
   }else{
      $r = hexdec(substr($hexa,0,2));
      $g = hexdec(substr($hexa,2,2));
      $b = hexdec(substr($hexa,4,2));
   }
   $resultado = 'rgb('.$r.','.$g.','.$b.')';

	return $resultado;
}


/**
* interpola los valores de composivción de dos colores. acepta rgg() y exa
* @param string $Color1 color inicial, el formato en que se carga esta variable define el formato de salida.
* @param string $Color2 color final.
* @param string $Porcentaje valor de interpolación: 0% = $Color1, 100% = $Color2.
* @return string Retorna color interpolado en el mismo formato que $Color1.
*/
function colormix($Color1, $Color2, $Porcentaje){
	
	if(substr($Color1,0,3)!='rgb' && substr($Color1,0,3)!='RGB'){
		$Color1 = hexargb($Color1);
		$modo ="hexa";
	}else{
		$modo ="rgb";
	}
	if(substr($Color2,0,3)!='rgb' && substr($Color2,0,3)!='RGB'){
		$Color2 = hexargb($Color2);
	}
	
	
	$rgbO = str_replace("rgb(", "", $Color1);
	$rgbO = str_replace(")", "", $rgbO);
	$rgbO = explode(",",$rgbO);	
	
	$rO = $rgbO[0];
	$gO = $rgbO[1];
	$bO = $rgbO[2];	
	
	$rgbF = str_replace("rgb(", "", $Color2);
	$rgbF = str_replace(")", "", $rgbF);
	$rgbF = explode(",",$rgbF);	
	
	$rF = $rgbF[0];
	$gF = $rgbF[1];
	$bF = $rgbF[2];	
	
	$rD = $rF - $rO;
	$gD = $gF - $gO;	
	$bD = $bF - $bO;	
	
	
	$rM = round($rO + $rD * $Porcentaje / 100);
	$gM = round($gO + $gD * $Porcentaje / 100);
	$bM = round($bO + $bD * $Porcentaje / 100);

	$rgb ='rgb('.$rM.','.$gM.','.$bM.')';
	
	if($modo=="rgb"){
		$resultado = $rgb;
	}elseif($modo=="hexa"){
		$resultado = rgbhexa($rgb);
	}else{
		$resultado = "ERROR";
	}

	return $resultado;
}
