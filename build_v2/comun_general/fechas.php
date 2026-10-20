<?php 
/**
* fechas.php
*
* fechas se incorpora en la carpeta includes 
* ya que contiene funciones genéricas de operación de cadenas (strings)
* 
* @package    	intraTReCC
* @subpackage 	Comun
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2010-2013 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 (GPL-3.0)
* Este archivo es parte de TReCC(tm) intraTReCC y de sus proyectos hermanos: baseobra(tm) y TReCC(tm) paneldecontrol.
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
* cambia formato de fecha
*
* @param string $fecha Fecha en formato yyyy-mm-dd
* @return string Retorna fecha en formato mes de año en castellano (enero de 2011)
*/
function mesano($fecha){
	$Ano = ano($fecha);
	$Mes = mesdos($fecha);
	$fecha = $Mes . " de " . $Ano;
	return ($fecha);	
}


/**
* calcula primer día del período siguiente.
* esta función recurre a otras funciones definidas más adelante.
*
* @param date $fecha Fecha en formato yyyy-mm-dd o yyyy-m-d
* @param string $periodo periodidicad analizada (daio, semanal, mensual, bimestral
* @return string Retorna mismo día del mes siguiente yyyy-mm-dd
*/
function periodosiguiente($fecha,$periodo){
	if($periodo=='anual'){
		return anosiguiente($fecha);
	}elseif($periodo=='trimestral'){
		return trimestresiguiente($fecha);
	}elseif($periodo=='mensual'){
		return messiguiente($fecha);
	}elseif($periodo=='cuarto de mes'){
		return cuartomessiguiente($fecha);
	}elseif($periodo=='semanal'){
		return cuartomessiguiente($fecha);
	}elseif($periodo=='diario'||$periodo=='diaria'){
		return diaLaSsiguiente($fecha);
	}else{
		//echo 'error en el periodo';
		return cuartomessiguiente($fecha);
	}
}

/**
* definida una periodicidad suma un ciclo a la fecha cargada.
*
* @param date $fecha Fecha en formato yyyy-mm-dd o yyyy-m-d
* @param string $periodo periodidicad analizada (daio, semanal, mensual, bimestral
* @return string Retorna mismo día del mes siguiente yyyy-mm-dd
*/
function periodosuma($fecha,$periodo){
	if($periodo=='anual'){
		$fecha=sumaanos($fecha,1);
		return $fecha;
	}elseif($periodo=='trimestral'){
		$fecha=sumames($fecha,3);
		return $fecha;
	}elseif($periodo=='mensual'){
		$fecha=sumames($fecha,1);
		return $fecha;
	}elseif($periodo=='quinsenal'){
		$fecha=sumaquincena($fecha,1);
		return $fecha;	
	}elseif($periodo=='bisemanal'){
		$fecha=sumadias($fecha,14);
		return $fecha;					
	}elseif($periodo=='cuarto de mes'){
		$fecha=sumadias($fecha,7);
		return $fecha;
	}elseif($periodo=='semanal'){
		$fecha=sumadias($fecha,7);
		return $fecha;
	}elseif($periodo=='diario'||$periodo=='diaria'){
		return diaLaSsiguiente($fecha);
	}else{
		//echo 'error en el periodo';
		return cuartomessiguiente($fecha);
	}
}

/**
* definida una periodicidad y una fecha determina las fechas límites del período que contiene la fecha dada.
 * muachas de las periodicidades aun deber ser programadas
*
* @param date $fecha Fecha en formato yyyy-mm-dd o yyyy-m-d
* @param string $periodo periodidicad analizada (daio, semanal, mensual, bimestral
* @return string Retorna mismo día del mes siguiente yyyy-mm-dd
*/
function periodofechas($fecha,$periodo){

	if($periodo=='anual'){
		$fechas['primerdia']=ano($fecha)."-01-01";
		$fechas['ultimodia']=ano($fecha)."-12-31";
		$fechas['diasiguiente']=(ano($fecha)+1)."-01-01";
		$fechas['diaanterior']=(ano($fecha)-1)."-12-31";
		return $fechas;
	}elseif($periodo=='trimestral'){
		$fechas['primerdia']="ERROR";
		$fechas['ultimodia']="ERROR";
		$fechas['diasiguiente']="ERROR";
		$fechas['diaanterior']="ERROR";
		return $fechas;
	}elseif($periodo=='mensual'){
		$fechas['primerdia']=ano($fecha)."-".mes($fecha)."-01";
		$fechas['ultimodia']=ano($fecha)."-".mes($fecha)."-".diasenelmes($fecha);
		$fechas['diasiguiente']=sumadias($fechas['ultimodia'],1);
		$fechas['diaanterior']=sumadias($fechas['primerdia'],-1);
		return $fechas;
	}elseif($periodo=='quinsenal'){
		$fechas['primerdia']="ERROR";
		$fechas['ultimodia']="ERROR";
		$fechas['diasiguiente']="ERROR";
		$fechas['diaanterior']="ERROR";
		return $fechas;	
	}elseif($periodo=='bisemanal'){
		$fechas['primerdia']="ERROR";
		$fechas['ultimodia']="ERROR";
		$fechas['diasiguiente']="ERROR";
		$fechas['diaanterior']="ERROR";
		return $fechas;					
	}elseif($periodo=='cuarto de mes'){
		$fechas['primerdia']="ERROR";
		$fechas['ultimodia']="ERROR";
		$fechas['diasiguiente']="ERROR";
		$fechas['diaanterior']="ERROR";
		return $fechas;
	}elseif($periodo=='semanal'){
		$fechas['primerdia']="ERROR";
		$fechas['ultimodia']="ERROR";
		$fechas['diasiguiente']="ERROR";
		$fechas['diaanterior']="ERROR";
		return $fechas;
	}elseif($periodo=='diario'||$periodo=='diaria'){
		$fechas['primerdia']="ERROR";
		$fechas['ultimodia']="ERROR";
		$fechas['diasiguiente']="ERROR";
		$fechas['diaanterior']="ERROR";
	}else{
		//echo 'error en el periodo';
		$fechas['primerdia']="ERROR";
		$fechas['ultimodia']="ERROR";
		$fechas['diasiguiente']="ERROR";
		$fechas['diaanterior']="ERROR";
		return $fechas;		
	}
}

/**
* calcula primer día del ano siguiente
*
* @param string $fecha Fecha en formato yyyy-mm-dd o yyyy-m-d
* @return string Retorna mismo día del mes siguiente yyyy-mm-dd
*/
function anosiguiente($fecha){
	$a=explode("-", $fecha);
	$Ano = (int)$a[0]+1;
	$fecha = $Ano."-01-01";
	return $fecha;
}

/**
* calcula primer día del trimestre siguiente
*
* @param string $fecha Fecha en formato yyyy-mm-dd o yyyy-m-d
* @return string Retorna mismo día del mes siguiente yyyy-mm-dd
*/
function trimestresiguiente($fecha){
	$a=explode("-", $fecha);
	$Ano = (int)$a[0];
	$Mes = (int)$a[1];
	$Dia = 1;
	
	if($Mes < 4){$Mes="04";}
	elseif($Mes < 7){$Mes="07";}
	elseif($Mes < 10){$Mes="10";}
	else{$Mes="01";$Ano++;}
	$fecha = "$Ano-$Mes-$Dia";
	return $fecha;
}

/**
* calcula primer día del mes siguiente
*
* @param string $fecha Fecha en formato yyyy-mm-dd o yyyy-m-d
* @return string Retorna mismo día del mes siguiente yyyy-mm-dd
*/
function messiguiente($fecha){
	$a=explode("-", $fecha);
	$Ano = (int)$a[0];
	$Mes = (int)$a[1];
	$Dia = (int)$a[2];
	if  ($Mes < 10){$Mes="0".$Mes;}
	$fecha = $Ano."-".$Mes."-01";
	return sumames($fecha,'1');
}

/**
* calcula primer día del cuanrto de mes siguiente
*
* @param string $fecha Fecha en formato yyyy-mm-dd o yyyy-m-d
* @return string Retorna mismo día del mes siguiente yyyy-mm-dd
*/
function cuartomessiguiente($fecha){
	$a=explode("-", $fecha);
	$Ano = (int)$a[0];
	$Mes = $a[1];
	$Dia = (int)$a[2];
	
	if($Dia<=07){
		$Dia="08";
		$fecha = "$Ano-$Mes-$Dia";
	}elseif($Dia<=15){
		$Dia="16";
		$fecha = "$Ano-$Mes-$Dia";		
	}elseif($Dia<=22){
		$Dia="23";
		$fecha = $Ano."-".$Mes."-".$Dia;		
	}else{
		$fecha = messiguiente($fecha);
	}
	return $fecha;

}

/**
* calcula el siguiente día habil (no tontempla feriados)
*
* @param string $fecha Fecha en formato yyyy-mm-dd o yyyy-m-d
* @return string Retorna el dia siguiente yyyy-mm-dd
*/
function diaLaSsiguiente($fecha){
	$fecha=sumadias($fecha,1);
	$DiaSem = date('N', mktime(0, 0, 0, mes($fecha), dia($fecha), ano($fecha)));
	while($DiaSem == 7){
		$fecha=sumadias($fecha,1);
		$DiaSem = date('N', mktime(0, 0, 0, mes($fecha), dia($fecha), ano($fecha)));
	}
	return $fecha;
}

/**
* cambia formato de fecha, de standar a español
*
* @param string $fecha Fecha en formato yyyy-mm-dd o yyyy-m-d
* @return string Retorna misma fecha reordenada dd-mm-yyyy o d-m-yyyy
*/
function ordencastellano($fecha){
	$a=explode("-", $fecha);
	$Ano = (int)$a[0];
	$Mes = (int)$a[1];
	$Dia = (int)$a[2];	
	return $Dia."-".$Mes."-".$Ano;
}

/**
* de una fecha identifica el ano
*
* @param string $fecha Fecha en formato yyyy-mm-dd o yyyy-m-d
* @return string Retorna el ano de la fecha en el mismo formato (yyyy)
*/
function ano($fecha){
		$a=explode("-", $fecha);
		return $a[0];
}


function mes($fecha){ /* devuelve el mes de ma fecha (01) */
		$a=explode("-", $fecha);
		return $a[1];
}

function dia($fecha){ /* devuelve el dia de la fecha (01) */
		$a=explode("-", $fecha);
		if($a[2]!=''){$resultado=str_pad($a[2], 2, "0", STR_PAD_LEFT);}else{$resultado='';}	 
		return $resultado;
}

/**
* devuelve fecha formateada, primer letra del dia de la semana en castellano
*
* @param string $fecha Fecha en formato yyyy-mm-dd o yyyy-m-d
* @return string Primer letra del nombre del día de la semana
*/
function diaS($fecha){ // devuelve primer letra del dia de la semana de la fecha (L) 
	$DiaSem = date('N', mktime(0, 0, 0, mes($fecha), dia($fecha), ano($fecha)));
	if($DiaSem=='1'){return 'L';}
	elseif($DiaSem=='2'){return 'M';}
	elseif($DiaSem=='3'){return 'M';}
	elseif($DiaSem=='4'){return 'J';}
	elseif($DiaSem=='5'){return 'V';}
	elseif($DiaSem=='6'){return 'S';}
	elseif($DiaSem=='7'){return 'D';}
}




function mesuno($fecha){ /* escribe mes con texto abreviado (ene) */
	$Ano = substr($fecha,0,4);
	$Mesn = substr($fecha,5,2);
	if ($Mesn == 1){$Mes = "ene";
		}elseif($Mesn == 2){$Mes = "feb";
		}elseif($Mesn == 3){$Mes = "mar";
		}elseif($Mesn == 4){$Mes = "abr";
		}elseif($Mesn == 5){$Mes = "may";
		}elseif($Mesn == 6){$Mes = "jun";
		}elseif($Mesn == 7){$Mes = "jul";
		}elseif($Mesn == 8){$Mes = "ago";
		}elseif($Mesn == 9){$Mes = "sep";
		}elseif($Mesn == 10){$Mes = "oct";
		}elseif($Mesn == 11){$Mes = "nov";
		}elseif($Mesn == 12){$Mes = "dic";
	}
$fecha = $Mes;
return ($fecha);	
}

function mesdos($fecha){ // escribe mes con texto comleto (enero)
	$Mesn = mes($fecha);
	if ($Mesn == 1){$Mes = "enero ";
		}elseif($Mesn == 2){$Mes = "febrero ";
		}elseif($Mesn == 3){$Mes = "marzo ";
		}elseif($Mesn == 4){$Mes = "abril ";
		}elseif($Mesn == 5){$Mes = "mayo ";
		}elseif($Mesn == 6){$Mes = "junio ";
		}elseif($Mesn == 7){$Mes = "julio ";
		}elseif($Mesn == 8){$Mes = "agosto ";
		}elseif($Mesn == 9){$Mes = "septiembre ";
		}elseif($Mesn == 10){$Mes = "octubre ";
		}elseif($Mesn == 11){$Mes = "noviembre ";
		}elseif($Mesn == 12){$Mes = "diciembre ";
	}
		
$fecha = $Mes;
return ($fecha);	
}


function sumames($fecha,$cantidad){ /* suma cantidad de meses deseados a una fecha | permite suma y resta */

	$a=explode("-", $fecha);
	$Ano = (int)$a[0];
	$Mesn = ((int)$a[1])+(int)$cantidad;
	$Dia = (int)$a[2];	
	
	if ($Mesn > 12){ /* suma años */
			$div = floor($Mesn / 12);
			$resto = $Mesn - ($div * 12);
			$Ano = $Ano + $div;
			$Mesn = $resto;
	}
	
	if ($Mesn < 1){ /* resta años */
			$Mesn --;
			$div = floor(abs($Mesn) / 12);
			$resto = abs($Mesn) - ($div * 12);
			$Ano = $Ano -1 -$div;
			$Mesn = 13 - $resto;
	}	
	
	$diasenlafechaoriginal=dia($fecha);
	$diasenelmesnuevo=diasenelmesano($Ano . "-" . $Mesn . "-" . "01");
	if($diasenelmesnuevo<$diasenlafechaoriginal){
		$Dia=$diasenelmesnuevo;
	}
	
	$Mesn=str_pad($Mesn,2,'0',STR_PAD_LEFT);
	$Dia=str_pad($Dia,2,'0',STR_PAD_LEFT);
	
	$fecha = $Ano . "-" . $Mesn . "-" . $Dia;
	return ($fecha);	
}

function sumaquincena($fecha,$cantidad){ /* suma cantidad de meses deseados a una fecha | permite suma y resta */

	$a=explode("-", $fecha);	
	$Ano = (int)$a[0];
	$Mes = (int)$a[1];
	$Dia = (int)$a[2];		
	
	if($Dia<15){$Dia=1;$quincena=1;}
	elseif($Dia>=15){$Dia=15;$quincena=2;}	
	
	$cantidad=((int)$cantidad)/2;
	$c = explode(".",$cantidad);
	
	
	$cantidad=$c[0];
	$resto=isset($c[1])?$c[1]:0;	
	if($resto!=0){$resto='1';}
	
	$Mesn = $Mes+(int)$cantidad;	

	if($quincena==2&&$resto=='1'){$Mesn++;$Dia='1';}
	elseif($quincena==1&&$resto=='1'){$Dia='15';}
	
	if ($Mesn > 12){ /* suma años */
			$div = floor($Mesn / 12);
			$resto = $Mesn - ($div * 12);
			$Ano = $Ano + $div;
			$Mesn = $resto;
	}
	
	if ($Mesn < 1){ /* resta años */
			$Mesn --;
			$div = floor(abs($Mesn) / 12);
			$resto = abs($Mesn) - ($div * 12);
			$Ano = $Ano -1 -$div;
			$Mesn = 13 - $resto;
	}	
	
	if  ($Mesn < 10){$Mesn="0".$Mesn;}  /* transforma numero entero en string iniciada en 0 para valores menores a 10 */
	if  ($Dia < 10){$Dia="0".$Dia;}  /* transforma numero entero en string iniciada en 0 para valores menores a 10 */
	
	$fecha = $Ano . "-" . $Mesn . "-" . $Dia;
	return ($fecha);	

}


function diasentrefechas($fecha2,$fecha1){  /* calcula la cantidad de días entre dos fechas */

	$fmin=min($fecha2,$fecha1);
	$fmax=max($fecha2,$fecha1);


	$a=explode("-", $fmax);
	$Ano2 = (int)$a[0]; 
	$Mes2 = (int)$a[1];
	$Dia2 = (int)$a[2];			
	
	$a=explode("-", $fmin);
	$Ano1 = (int)$a[0];
	$Mes1 = (int)$a[1];
	$Dia1 = (int)$a[2];	

	$dAno = $Ano2 - $Ano1;
	$dMes = $Mes2 - $Mes1;
	$dDia = $Dia2 - $Dia1;
	
	
	if($dAno > 0){
		$cuentano = 1;
		$dias = diasenelano($Ano1) - diadelano($fmin);
		while($cuentano < $dAno){
			$dias += diasenelano($Ano1+$cuentano);
			$cuentano ++;
		}
		$dias += diadelano($fmax);

	}elseif($dAno == 0){
		$dias = (diadelano($fmax) - diadelano($fmin));
	}else{
		$dias = "error ";
	}
	
	if($fecha1>$fecha2){$dias=$dias*(-1);}

		return $dias;
  }

function mesesentrefechas($fecha2,$fecha1){  // calcula la cantidad de días entre dos fechas 

	$Ano2 = ano($fecha2); 
	$Mes2 = mes($fecha2);		
	$Dia2 = dia($fecha2);	

	$Ano1 = ano($fecha1); 
	$Mes1 = mes($fecha1);	
	$Dia1 = dia($fecha1);			
	

	$dAno = $Ano2 - $Ano1;
	$dMes = $Mes2 - $Mes1;
	$dDia = $Dia2 - $Dia1;
	
	$meses = (12*$dAno)+$dMes+($dDia/30);
	
	return $meses;
  }


function horasentrefechas($fecha2,$fecha1){  /* calcula la cantidad de días entre dos fechas yyyy-mm-dd hh:mm:ss */
	/*echo " <br> -- inicia dias entre fechas (en trecc.php)-- <br>";	
	echo "f1: ".$fecha1."<br>";
	echo "f2: ".$fecha2."<br>";*/
	
	$a=explode(" ", $fecha2);
	$Fecha2 = $a[0];
	$hora = $a[1];
		
	
	$a=explode(":", $hora);
	$Hora2 = (int)$a[0];
	$Min2 = (int)$a[1];
	$Seg2 = (int)$a[2];
	
	
	$a=explode(" ", $fecha1);
	$Fecha1 = $a[0];
	$hora = $a[1];		
	
	$a=explode(":", $hora);
	$Hora1 = (int)$a[0];
	$Min1 = (int)$a[1];
	$Seg1 = (int)$a[2];
			
	$dHora = $Hora2 - $Hora1;
	$dMin = $Min2 - $Min1;
	$dSeg = $Seg2 - $Seg1;	
	
/*	echo "diferencia de años: " . $dAno."<br>";
	echo "diferencia de meses: " . $dMes."<br>";
	echo "diferencia de dias: " . $dDia."<br>";

*/	
	$dias = diasentrefechas($Fecha2, $Fecha1);
	$horas = $dias * 24;
	
	$horas += $dHora + ($dMin + $dSeg/60)/60;
		
	return $horas;
  }

function diadelano($fecha){
/*	echo "<br>---inicia diadelano (en trecc.php)---";
*/	$a=explode("-", $fecha);
	$Ano = (int)$a[0];
	$Mes = (int)$a[1];
	$Dia = (int)$a[2];			

	$dfebrero = 28;
	if ($Ano%400 == 0){
		$dfebrero = 29;
	}elseif ($Ano%100 != 0 && $Ano%4 == 0){
		$dfebrero = 29;
	}

	$cuentames = 1;
	$dmeses = 0;
	while($cuentames < $Mes){
		if($cuentames == 1 || $cuentames == 3 || $cuentames == 5 || $cuentames == 7 || $cuentames == 8 || $cuentames == 10 || $cuentames == 12){
			$dmeses = $dmeses + 31;
		}elseif($cuentames == 2){
			$dmeses = $dmeses + $dfebrero;
		}else{
			$dmeses = $dmeses + 30;
		}
		$cuentames ++;
	}
/*	echo "<br>---finaliza diadelano (en trecc.php)---";
*/	return $dmeses + $Dia;
}

function diasenelmesano($Fecha){ /* indica la cantidad de días en ese mes */
	$Mes = mes($Fecha);
	$Ano = ano($Fecha);	
	
	if ($Ano%400 == 0){
		$dmes[2]=29;
	}elseif ($Ano%100 != 0 && $Ano%4 == 0){
		$dmes[2]=29;
	}else{
		$dmes[2]=28;
	}
	$dmes[1]=31;
	$dmes[3]=31;
	$dmes[4]=30;
	$dmes[5]=31;
	$dmes[6]=30;
	$dmes[7]=31;
	$dmes[8]=31;
	$dmes[9]=30;
	$dmes[10]=31;
	$dmes[11]=30;
	$dmes[12]=31;
	
	$return = $dmes[(int)$Mes];
	return($return);
}

function ultimodiadelmes($Fecha){ /* calcula la fecha del último dia del mnes de la fecha original */
	$Ano=ano($Fecha);	
	$Mes=mes($Fecha);
	$Dia=diasenelmesano($Fecha);	
	$ultimodia = sprintf("%04d-%02d-%02d", $Ano, $Mes, $Dia);
		
	return $ultimodia;
}

function diasenelano($Ano){ /* calcula la cantidad de días dntro de un año */
	if ($Ano%400 == 0){
		return 366;
	}elseif ($Ano%100 != 0 && $Ano%4 == 0){
		return 366;
	}else{
		return 365;
	}
}

function sumadias($fecha,$dias){ /* suma la cantidad deseada de días a una fecha */

	$dmes[1]=31;
	$dmes[2]=28;
	$dmes[3]=31;
	$dmes[4]=30;
	$dmes[5]=31;
	$dmes[6]=30;
	$dmes[7]=31;
	$dmes[8]=31;
	$dmes[9]=30;
	$dmes[10]=31;
	$dmes[11]=30;
	$dmes[12]=31;

	$a = explode("-", $fecha);
	$ano = (int)$a[0];
	$mes = (int)$a[1];
	$dia = (int)$a[2];	

	$diasenelano = diasenelano($ano);

	if($diasenelano==366){$dmes[2]=29;}else{$dmes[2]=28;}

	$diassumados = 0;
	
	if($dias>0){
		while($diassumados < $dias){
			$diasenelano = diasenelano($ano);
			if($diasenelano==366){$dmes[2]=29;}else{$dmes[2]=28;}
			if ($dia == $dmes[$mes]){
				if ($mes == 12){
					$dia = 1;
					$mes = 1;
					$ano ++;
				}else{
					$dia = 1;
					$mes ++;
				} 
			}else{
				$dia ++;
			}	
		$diassumados ++;
		}
		
	}elseif($dias<0)
		while($diassumados > $dias){
			$diasenelano = diasenelano($ano);
			if($diasenelano==366){$dmes[2]=29;}else{$dmes[2]=28;}
			if ($dia == 1){
				if ($mes == 1){
					$mes = 12;
					$dia = $dmes[$mes];
					$ano --;
				}else{
					$mes --;
					$dia = $dmes[$mes];
				} 
			}else{
				$dia --;
			}	
		$diassumados --;
		}	
	
	
	if  ($mes < 10){$mes="0".$mes;}
	if  ($dia < 10){$dia="0".$dia;}
	return $ano . "-" . $mes .  "-" . $dia;
}

function sumasegundos($tiempo,$segundos){ // suma la cantidad deseada de segundos a un tiempo en formato hh:mm:ss 

$tiempo = explode(":",$tiempo);

$h = $tiempo[0];
$m = $tiempo[1];
$s = $tiempo[2];

$segs=(($h)*60 + $m)*60+$s;

$segsfinales=$segs+$segundos;


$minsfinales=$segsfinales/60;
$horsfinales=$minsfinales/60;
$horsfinalesredodnas=floor($horsfinales);
$minrestantes=($horsfinales-$horsfinalesredodnas)*60;
$minrestantesredondos=floor($minrestantes);
$segsrestantes=($minrestantes-$minrestantesredondos)*60;

$h=str_pad($horsfinalesredodnas, 2, '0',STR_PAD_RIGHT);
$m=str_pad($minrestantesredondos, 2, '0',STR_PAD_RIGHT);
$s=str_pad(floor($segsrestantes), 2, '0',STR_PAD_RIGHT);

	return $h . ":" . $m .  ":" . $s;
}



/**
* cambia formato de fecha, de standar a español
*
* @param string $fecha Fecha en formato yyyy-mm-dd o yyyy-m-d
* @param int $dias cantidad de días hábiles a sumar
* @param bolean $L lunes se considera habil
* @param bolean $M martes se considera habil
* @param bolean $X miercoles se considera habil
* @param bolean $J jueves se considera habil
* @param bolean $V viernes se considera habil
* @param bolean $S sábado se considera habil
* @param bolean $D domingo se considera habil
* @param bolean $S sábado se considera habil
* @param array $feriados array de fechas feriados a considerar
* @return string Retorna misma fecha reordenada dd-mm-yyyy o d-m-yyyy
*/
function sumadiashabiles($fecha,$dias,$L,$M,$X,$J,$V,$S,$D,$feriados){ /* suma la cantidad deseada de días a una fecha */
	
	$diassumados=0;
	$fechasumada=$fecha;
	
	while($diassumados<$dias){
		
		$fechasumada=sumadias($fechasumada,1);
		
		$DiaSem = date('N', mktime(0, 0, 0, mes($fecha), dia($fecha), ano($fecha)));
		
		
		$esferiado='no';
		
		if($feriados[$fechasumada]=='1'){
			$esferiado='si';
		}

		if($DiaSem==1&&$L==0){$esferiado='si';}
		if($DiaSem==2&&$M==0){$esferiado='si';}
		if($DiaSem==3&&$X==0){$esferiado='si';}
		if($DiaSem==4&&$J==0){$esferiado='si';}
		if($DiaSem==5&&$V==0){$esferiado='si';}
		if($DiaSem==6&&$S==0){$esferiado='si';}
		if($DiaSem==7&&$D==0){$esferiado='si';}
		
		if($esferiado=='no'){
			$diassumados++;
		}
	}
		return $fechasumada;
}

function restadias($fecha,$dias){  /* resta la cantidad deseada de días a una fecha */

	$dmes[1]=31;
	$dmes[2]=28;
	$dmes[3]=31;
	$dmes[4]=30;
	$dmes[5]=31;
	$dmes[6]=30;
	$dmes[7]=31;
	$dmes[8]=31;
	$dmes[9]=30;
	$dmes[10]=31;
	$dmes[11]=30;
	$dmes[12]=31;

	$a = explode("-", $fecha);
	$ano = (int)$a[0];
	$mes = (int)$a[1];
	$dia = (int)$a[2];	

	$diasenelano = diasenelano($ano);

	if($diasenelano==366){$dmes[2]=29;}else{$dmes[2]=28;}

	$diasrestados = 0;

	while($diasrestados < $dias){
		$diasenelano = diasenelano($ano);
		if($diasenelano==366){$dmes[2]=29;}else{$dmes[2]=28;}
		
		if ($dia == 1){
			if ($mes == 1){
				$mes = 12;
				$dia = $dmes[$mes];
				$ano -= 1;
			}else{
				$mes -= 1;
				$dia = $dmes[$mes];
			}
		}else{
			$dia -= 1;
		}		
	$diasrestados ++;
	}
	if  ($mes < 10){$mes="0".$mes;}
	if  ($dia < 10){$dia="0".$dia;}
	return $ano . "-" . $mes .  "-" . $dia;
}

function traducirdiasemana($dia){ /* entra día en inges sale en castellano */
	if ($dia == "Mon"){$diae = "Lun";}
	if ($dia == "Tue"){$diae = "Mar";}
	if ($dia == "Wed"){$diae = "Mie";}
	if ($dia == "Thu"){$diae = "Jue";}
	if ($dia == "Fri"){$diae = "Vie";}
	if ($dia == "Sat"){$diae = "Sab";}
	if ($dia == "Sun"){$diae = "Dom";}
	return $diae;
}
function traducirdiasemanados($fecha){ /* entra día en inges sale en castellano */
	$dia = date("D", mktime(0, 0, 0, mes($fecha), dia($fecha), ano($fecha)));
	if ($dia == "Mon"){$diae = "Lunes";}
	if ($dia == "Tue"){$diae = "Martes";}
	if ($dia == "Wed"){$diae = "Miércoles";}
	if ($dia == "Thu"){$diae = "Jueves";}
	if ($dia == "Fri"){$diae = "Viernes";}
	if ($dia == "Sat"){$diae = "Sábado";}
	if ($dia == "Sun"){$diae = "Domingo";}
	return $diae;
}

function semanaanumero($dia){ /* entra día en inges fomato php: date("D") sale en números */

	if ($dia == "Mon"){$diae = 2;}
	if ($dia == "Tue"){$diae = 3;}
	if ($dia == "Wed"){$diae = 4;}
	if ($dia == "Thu"){$diae = 5;}
	if ($dia == "Fri"){$diae = 6;}
	if ($dia == "Sat"){$diae = 7;}
	if ($dia == "Sun"){$diae = 8;}
	return $diae;
}
function semanaanumeroS1($dia){ // entra día en inges fomato php: date("D") sale en números Sun=1, etc

	if ($dia == "Mon"){$diae = 2;}
	if ($dia == "Tue"){$diae = 3;}
	if ($dia == "Wed"){$diae = 4;}
	if ($dia == "Thu"){$diae = 5;}
	if ($dia == "Fri"){$diae = 6;}
	if ($dia == "Sat"){$diae = 7;}
	if ($dia == "Sun"){$diae = 1;}
	return $diae;
}

function horamin($hora){ /* escribe hora : minutos partiendo de hh:mm:ss */
	$h = explode(":",$hora);
	$fecha = $h[0].":".$h[1];
return ($fecha);	
}

function semanadelano($fecha){ /*numero de orden de la semana del año calculando 4 semanas por mes no semanas reales*/
$return = (mes($fecha)-1)*4 + ceil(dia($fecha)/7);
return($return);
}

function quincenadelano($fecha){ /*numero de quincena del año calculando 2 quinsenal por mes*/
$return = (mes($fecha)-1)*2 +  ceil(dia($fecha)/15);
return($return);
}

function bimestredelano($fecha){ /*numero de orden del bimestre del año*/
$return =  ceil(mes($fecha)/2);
return($return);
}

function trimestredelano($fecha){ /*numero de orden del trimestredelano del año */
$return =  ceil(mes($fecha)/3);
return($return);
}

function cuatrimestredelano($fecha){ /*numero de orden del cuatrimestredelano del año */
$return =  ceil(mes($fecha)/4);
return($return);
}

function semestredelano($fecha){ /*numero de orden del semestredelano del año */
$return =  ceil(mes($fecha)/4);
return($return);
}

function primerdiadelmes($fecha){ // devuelve la fecha del primer día del mes de la fecha introducida 
	$a = explode("-", $fecha);
	$ano = $a[0];
	$mes = $a[1];
	$dia = $a[2];
	
	$primerdia = sprintf("%04d-%02d-%02d", $a[0], $a[1], "01");	
		
	return $primerdia;
}




function reordendmy($fecha){ /* reordena la fecha a dia-mes-ano */
	return dia($fecha)."-".mes($fecha)."-".ano($fecha);
}


function diasenelmes($fecha){ /* devuelve la cantidad de días en el mes de la fecha introducida */

	$dmes[1]=31;
	$dmes[2]=28;
	$dmes[3]=31;
	$dmes[4]=30;
	$dmes[5]=31;
	$dmes[6]=30;
	$dmes[7]=31;
	$dmes[8]=31;
	$dmes[9]=30;
	$dmes[10]=31;
	$dmes[11]=30;
	$dmes[12]=31;

	$a = explode("-", $fecha);
	$ano = $a[0];
	$mes = $a[1];
	$dia = $a[2];	

	$diasenelano = diasenelano($ano);

	if($diasenelano==366){$dmes[2]=29;}else{$dmes[2]=28;}
	
	$diasenelmes = $dmes[(int)$mes];
		
	return $diasenelmes;
}

?>
