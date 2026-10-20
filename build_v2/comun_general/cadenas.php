<?php 
/**
* cadenas.php
*
* cadena se incorpora en la carpeta includes 
* ya que contiene funciones genéricas de operación de cadenas (strings)
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

header('Content-Type:text/html; charset=cp-1252');

/**
* genera una cadena aleatoria compatible con nombre de archivo (solo letras del alfabeto)
*
* @param integer $Largo cantidad de caracteres esperados
* @return string Retorna una cadena aleatoria compatible con nombre de archivo (solo letras del alfabeto)(diciembre de 2013)
*/
function cadenaArchivo( $Largo ) {
	$habilitados = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";	

	$largobase = strlen( $habilitados );
	$resultado='';
	for( $i = 0; $i < $Largo; $i++ ) {
		$resultado .= $habilitados[ rand( 0, $largobase - 1 ) ];
	}

	return $resultado;
}

/**
* genera un explode a partr de múltiples delimitadores
*
* @param array $delim array de delimietadores
* @param string $dato texto a explotar
* @return string Retorna un array resultado de los sucesivos explode(diciembre de 2013)
*/
function explodemulti($delim,$dato) {
	$array = explode($delim[0],$dato);
    array_shift($delim);
    foreach($array as $key => $texto) {
         $array[$key] = explodemulti($delim, $texto);
    }
    return  $array;
}

/**
* analiza que una cadena no contenga caracteres especiales
*
* @param string $filename cadena a testear
* @return boolean Retorna true o false
*/
function filenameseguro($filename) {
	
	$permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_ ";
	for($i=0 ; $i < strlen($filename) ; $i++){
		if(strpos($permitidos, $filename[$i]) === false)
		return false;
	}	
	
	return true;		
}

/**
* elimina de una cadena los caracteres especiales
*
* @param string $filename cadena a testear
* @return boolean Retorna true o false
*/
function asegurarfilename($filename) {
	$acum='';
	$permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_ ";
	for($i=0 ; $i < strlen($filename) ; $i++){
		if(strpos($permitidos, $filename[$i]) === false){
			
		}else{
			$acum.=$filename[$i];
		}
	}		
	return $acum;		
}

/**
* analiza que una cadena solo tenga caracteres válidos para una dirección de mail
*
* @param string $mail dirección de email
* @return boolean Retorna true o false
*/
function mailvalido($mail) {
	$permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.@";
	for($i=0 ; $i < strlen($mail) ; $i++){
		if(strpos($permitidos, $mail[$i]) === false)
		return false;
	}
	
	$necesarios="@.";
	for($i=0 ; $i < strlen($necesarios) ; $i++){
		if(strpos($mail, $necesarios[$i]) === false)
		return false;
	}	
	
	return true;		
}


/**
* analiza que una cadena solo tenga caracteres válidos para un nombre de usuario (log)
*
* @param string $log cuenta
* @return boolean Retorna true o false
*/
function logvalido($log){
	$permitidos = "abcdefghijklmnñopqrstuvwxyzáéíóúàèìòùäëïöüABCDEFGHIJKLMNÑOPQRSTUVWXYZÁÉÍÓÚÀÈÌÒÙÄËÏÖÜ0123456789_-";
	
	if(strlen($log)>4){
		for($i=0 ; $i < strlen($log) ; $i++)
		{
			if(strpos($permitidos, $log[$i]) === false)
			return false;
		}
		return true;	
	}
	
	return false;
}
/**
* analiza que una cadena solo tenga caracteres válidos para una contraseña
*
* @param string $pass cuenta
* @return boolean Retorna true o false
*/
function passvalido($pass){
	$permitidos = " abcdefghijklmnñopqrstuvwxyzáéíóúàèìòùäëïöüABCDEFGHIJKLMNÑOPQRSTUVWXYZÁÉÍÓÚÀÈÌÒÙÄËÏÖÜ0123456789_-";
	
	if(strlen($pass)>4){
		for($i=0 ; $i < strlen($pass) ; $i++)
		{
			if(strpos($permitidos, $pass[$i]) === false)
			return false;
		}
		return true;			
	}
	return false;
}

/**
* codifica algunos caracteres ascii
*
* @param string $nombre nombre real 
* @return boolean Retorna true o false
*/
function decodeascii(){
	echo "hola";	
}



/**
* analiza que una cadena solo tenga caracteres letras de la a a la z y espacios
*
* @param string $nombre nombre real 
* @return boolean Retorna true o false
*/
function nombrevalido($nombre) {
	$permitidos = " abcdefghijklmnñopqrstuvwxyzáéíóúàèìòùäëïöüABCDEFGHIJKLMNÑOPQRSTUVWXYZÁÉÍÓÚÀÈÌÒÙÄËÏÖÜ";
	
	for($i=0 ; $i < strlen($nombre) ; $i++){
		$a = strpos($permitidos, $nombre[$i]);
		if($a === false){
			return false;
	
		}
	}
	return true;
}

/**
* analiza que una cadena se válida como teléfono
*
* @param string $tel
* @return boolean Retorna true o false
*/
function telvalido($tel) {
	$permitidos = " 0123456789-/()";
	
	if(strlen($tel)>4){
	for($i=0 ; $i < strlen($tel) ; $i++)
		{
			if(strpos($permitidos, $tel[$i]) === false)
			return false;
		}
	return true;	
	}
	return false;
}

/**
* analiza una formula y determina si es segura para ejecutar desde php. Solo permite matemática básica
*
* @param string $formula fórmula a evaluar
* @return boolean Retorna true o false
*/
function formulaphpsegura($formula) {
	$permitidos = "0123456789()[]+-*/ ";
	
	for($i=0 ; $i < strlen($formula) ; $i++)
		{
			if(strpos($permitidos, $formula[$i]) === false){
				return false;
			}
		}
	return true;	

}


/**
* analiza una cadena y determina si es válida como fecha en formato AAAA-mm-dd o AAAA-m-d
*
* @param string $fecha fecha a evaluar
* @return boolean Retorna true o false
*/
function fechavalida($fecha) {
	$a = explode("-",$fecha);
	
	if(count($a)!=3){
		$ERR[]="demasiados términos";		
	}
	
	if(strlen($a[0])!=4||$a[0]<0){
		$ERR[]="año ".$a[0]."invalido";
	}
	if(strlen($a[1])>2){
		$ERR[]="mes ".$a[1]."invalido (largo)";
	}	
		
	if($a[1]<1||$a[1]>12){
		$ERR[]="mes ".$a[1]."invalido fuera de rango";	
	}

	if(strlen($a[2])>2){
		$ERR[]="dia ".$a[2]."invalido (largo)";
	}
	
	$diasdelmes=diasenelmesano($fecha);
		
	if($a[2]<1||$a[2]>$diasdelmes){
		$ERR[]="dia".$a[1]." invalido fuera de rango";	
	}

	if(isset($ERR)){
		return false;
	}else{
		return true;
	}
}

/**
* analiza una cadena HTML y cierrar los tags abiertos
*
* @param string $html codigo html
* @return boolean Retorna true o false
*/
function cerrartagsHTML($html) {
    preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
    $openedtags = $result[1];
    preg_match_all('#</([a-z]+)>#iU', $html, $result);
    $closedtags = $result[1];
    $len_opened = count($openedtags);
    if (count($closedtags) == $len_opened) {
        return $html;
    }
    $openedtags = array_reverse($openedtags);
    for ($i=0; $i < $len_opened; $i++) {
        if (!in_array($openedtags[$i], $closedtags)) {
            $html .= '</'.$openedtags[$i].'>';
        } else {
            unset($closedtags[array_search($openedtags[$i], $closedtags)]);
        }
    }
    return $html;
}
