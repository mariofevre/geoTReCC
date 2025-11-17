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

	$Idtipo=$_GET['idtipo'];
	$Idaccion=$_GET['idaccion'];
	
		
	$Config=$_SESSION['configuracion'];
	$e=explode("/",$Config['pla-nivel1']);
	$N1=$e[0];
	$Ns1=$e[1];
	$e=explode("/",$Config['pla-nivel2']);
	$N2=$e[0];
	$Ns2=$e[1];
	$e=explode("/",$Config['pla-nivel3']);
	$N3=$e[0];
	$Ns3=$e[1];

?>

<head>
	<title>Panel de control</title>
	<link rel="stylesheet" type="text/css" href="./css/panelbase.css">
	<link rel="stylesheet" type="text/css" href="./css/PLA.css">
	<?php 
	include("./comun_general/meta.php");
	?>

	<style type="text/css">
	
	<?php 
	if($_GET['modo']=='resumen'){
		echo"
			body{
				padding:0;
				background-image:none;
				margin:0px;
			}
		";		
	}	
		
	echo "
		#pageborde {
	}
	
	";	

	?>
	
	.nivel1{
		page-break-inside:auto;
	}
	.nivel1 div{
		page-break-inside:auto;
	}
	.nivel2 {
		page-break-after:always;
		}	
			a[disabled='disabled']{
		color:silver;
	}
	
	</style>
</head>
<body>
		
		
<?php	

	insertarmenu();		
	$Cons=consultaPLA();	
	$CSV="idACC;numACC;nombACC;desACC;idPRO;nombPRO;idPLA;nombPLA".PHP_EOL;	
	foreach($Cons['PLA'] as $n1 => $n1d){
		foreach($n1d as $n2 => $n2d){			
			unset($o);
			unset($a);
			unset($b);
			foreach($n2d as $n3 => $n3d){
				$o[]=$Cons['PN3'][$n3]['numero'];
				$a[$n3]=$n3;
				
				$des=preg_replace( "/\r|;|\n/", "", $Cons['PN3'][$n3]['descripcion'] );
				$des=str_replace(";",", ",$des);
				
				$CSV.="$n3;".str_replace(";",", ",$Cons['PN3'][$n3]['numero']).";".str_replace(";",", ",$Cons['PN3'][$n3]['nombre']).";".$des.";$n2;".str_replace(";",", ",$Cons['PN2'][$n2]['nombre']).";$n1;".str_replace(";",", ",$Cons['PN1'][$n1]['nombre']).PHP_EOL;
			}
			
			array_multisort($o, SORT_NUMERIC,$a);
			foreach($a as $id){
				$b[$id]=$id;
			}
			
			if(count($b)>0){
			$Cons['PLA'][$n1][$n2]=$b;
			}
			
			unset($o);
			unset($a);
			unset($b);

		}		
	}
	


	foreach($Cons['PLA'] as $n1 => $n1d){
		
			unset($o);
			unset($a);
			unset($b);
			foreach($n1d as $n2 => $n2d){
				$o[]=$Cons['PN2'][$n2]['numero'];
				$a[$n2]=$n2;
				$d[$n2]=$n2d;
			}
			
			array_multisort($o,$a);
			foreach($a as $id){
				$b[$id]=$d[$id];
			}
			
			if(count($b)>0){
			$Cons['PLA'][$n1]=$b;
			}
			
			unset($o);
			unset($a);
			unset($b);			
	}
	

	

		
	echo"<div id='recuadro'></div>";	
	echo "<iframe class='recuadros' id='recuadro2' name='recuadro2'></iframe>";		
	echo "<iframe class='recuadros' id='recuadro5' name='recuadro5'></iframe>";	

	echo '<div id="pageborde">';
	echo '<div id="page">';		
		
		//echo "<h1>Relevamiento</h1>";	
		echo '<h2><a disabled="disabled"  href="./PLA.php">ver modo gestion</a></h2>';		
		echo '<h2><a href="./PLA_fichas.php?idrel='.$_GET['idrel'].'&idtipo='.$_GET['idtipo'].'&idaccion='.$_GET['idaccion'].'">ver modo fichas</a></h2>';
		echo '<h2><a href="./PLA_tabla.php?idrel='.$_GET['idrel'].'&idtipo='.$_GET['idtipo'].'&idaccion='.$_GET['idaccion'].'">ver modo tabla</a></h2>';
		echo '<h2><a href="./PLA_listado.php?idrel='.$_GET['idrel'].'&idtipo='.$_GET['idtipo'].'&idaccion='.$_GET['idaccion'].'">ver modo listado</a></h2>';		
		
		$carpeta="./documentos/p_$PanelI/temp";
		if(!file_exists($carpeta)){
			mkdir($carpeta, 0777, true);
			chmod($carpeta, 0777); 
		}
		file_put_contents ($carpeta.'/'.'Acciones.csv' , $CSV);
		chmod($carpeta.'/'.'Acciones.csv',0777);
		//echo fread($temp, 1024);
		
		echo "<a href='$carpeta/Acciones.csv'>decargar tabla de acciones</a><br>";
		
		/*
		echo "Relevemianto activo:";
		echo "<form method='get' action='./REL.php'>";
		echo "<input type='hidden' value='".$_GET['idtipo']."' name='idtipo'>";
		echo "<input type='hidden' value='".$_GET['idaccion']."' name='idaccion'>";		
		echo "<select name='idrel' onchange='this.parentNode.submit();'>";
		echo "<option value=''>-elegir-</option>";
			foreach($Relevamientos as $idr => $rdata){
				if($idr==$IDrel){$chk=selected;}else{$chk='';}
				$n= $rdata['nombre'];
				if($n==''){$n="S/N";}
				echo "<option value='$idr' $chk> $n </option>";
			}
		echo "</select>"; 
		echo "</form>";
		echo " - <a href='./agrega_f.php?tabla=RELrelevamientos&accion=cambia&id=$IDrel&salida=REL'>editar</a> - <a href='./agrega_f.php?tabla=RELrelevamientos&accion=agrega&salida=REL'>agregar</a>";		
		if($HabilitadoEdicion=='si'){
			//echo "<a href='./agrega_f.php?pathFI_foto=relevamientos/fotos&tabla=".$Tabla."&id_p_RELrelevamiento=".$RelId."&salida=REL'>agregar punto relevado</a>";
		}

		echo "<br>Filtrado por grupo:";
		echo "<form method='get' action='./REL.php'>";
		echo "<input type='hidden' value='".$_GET['idrel']."' name='idrel'>";		
		echo "<select name='idtipo' onchange='this.parentNode.submit();'>";
		echo "<option value=''>-filtrar por-</option>";
			foreach($Tipos as $idt => $tdata){
				if($idt==$_GET['idtipo']){$chk=selected;}else{$chk='';}
				$n= $tdata['nombre'];
				if($n==''){$t="S/N";}
				echo "<option value='$idt' $chk> $n </option>";
			}
		echo "</select>"; 
		echo "</form>";
		
		echo "<br>Filtrado por Accion en curso:";
		
		echo "<form method='get' action='./REL.php'>";
		echo "<input type='hidden' value='".$_GET['idrel']."' name='idrel'>";		
		echo "<select name='idaccion' onchange='this.parentNode.submit();'>";
		echo "<option value=''>-filtrar por-</option>";
			foreach($Acciones as $idt => $tdata){
				if($idt==$_GET['idaccion']){$chk=selected;}else{$chk='';}
				$n= $tdata['nombre'];
				if($n==''){$t="S/N";}
				echo "<option value='$idt' $chk> $n </option>";
			}
		echo "</select>"; 
		echo "</form>";
		*/

	
		if($HabilitadoEdicion=='si'){
			//echo "<a href='./agrega_f.php?pathFI_foto=relevamientos/fotos&tabla=".$Tabla."&id_p_RELrelevamiento=".$RelId."&salida=REL'>agregar punto relevado</a>";
		}
	

	foreach($Cons['PLA'] as $n1 => $n1d){
		
		echo"<div iddb='$n1' tadb='PLAn1' class='nivel1'>";
			echo"<div class='encabezado'>";
				echo"<div class='numero'><div class='aux num' title='identificador único para el nivel 1 de planificación'>n1 $n1</div>$N1 ".$Cons['PN1'][$n1]['numero'];
					if($HabilitadoEdicion=='si'){
						echo "<a href='./agrega_f.php?tabla=PLAn1&accion=cambia&id=$n1&salida=PLA'>editar $N1</a>";
					}
				echo "</div>";
				echo"<div class='nombre'>".$Cons['PN1'][$n1]['nombre']."</div>";

				echo"<div class='decripcion'>";
					echo "<div class='subtitulo'>Descripción:</div>";
					echo $Cons['PN1'][$n1]['descripcion'];
				echo "</div>";

				echo "<div class='subtitulo'>Responsables:</div>";
				
				echo $Cons['Actores'][$Cons['PN1'][$n1]['id_p_GRAactores']]['nombre']." ".$Cons['Actores'][$Cons['PN1'][$n1]['id_p_GRAactores']]['apellido'];
				if($HabilitadoEdicion=='si'){
					if($Cons['PN1'][$n1]['id_p_GRAactores']<'1'){
						echo" <a onclick='crearFormularioCambiaActor(this)'>asignar responsable</a>";
					}else{
						echo" <a onclick='crearFormularioCambiaActor(this)'>cambiar responsable</a>";
					}
				}
				
				echo"<div class='estado'>";
					echo "<div class='subtitulo'>Estados:</div>";
					foreach($Cons['PN1'][$n1]['estados'] as $Ke => $Ve){							
						echo $Ve['nombre']." desde: ".$Ve['desde'];
						if($HabilitadoEdicion=='si'){
							echo " <a href='./agrega_f.php?accion=cambia&id=$Ke&tabla=PLAestados&salida=PLA&campos[]=desde&campos[]=nombre'>ajustar</a>";
						}	
					}
					
					echo "<br><a href='./agrega_f.php?accion=agrega&tabla=PLAestados&salida=PLA&C-desde=$Hoy&campofijo_c=$k&campofijob=id_p_PLAn1&campofijob_c=$n1&campofijoc=id_p_PLAn2&campofijoc_c=&campofijod=id_p_PLAn3&campofijod_c='>cargar nuevo estado</a>";			
				echo"</div>";	


				foreach($Cons['CAT']['PN1'] as $k => $v){
					echo "<div class='subtitulo'>".$v['nombre']." <a href='./agrega_f.php?eliminarhabilitado=si&accion=cambia&tabla=PLAcategorias&salida=PLA&campos[]=nombre&campos[]=orden&id=$k'>editar</a></div>";
					if(count($Cons['PN1'][$n1]['categorias'][$k])==0){
						echo "<a href='./agrega_f.php?accion=agrega&tabla=PLAcategoriasVal&salida=PLA&campofijo=id_p_PLAcategorias&campofijo_c=$k&campofijob=id_p_PLAn1&campofijob_c=$n1&campofijoc=id_p_PLAn3&campofijoc_c=&campofijod=id_p_PLAn2&campofijod_c='>definir valor para esta categoría</a>";
					}
					foreach($Cons['PN1'][$n1]['categorias'][$k] as $Kv => $Vv){
						echo $Vv['valor'];
						if($HabilitadoEdicion=='si'){
							echo" <a href='./agrega_f.php?eliminarhabilitado=si&campo=valor&accion=acambia&tabla=PLAcategoriasVal&salida=PLA&id=$Kv'>editar</a>";
						}
					}
				}

				if($HabilitadoEdicion=='si'){
					echo "<br><br><a href='./agrega_f.php?acciona=agrega&tabla=PLAcategorias&salida=PLA&campofijo=nivel&campofijo_c=PN1'>añadir categoría</a>";
				}
								
			echo"</div>";			
			echo"<div class='contenidos'>";		
				foreach($n1d as $n2 => $n2d){
					echo"<div iddb='$n2' tadb='PLAn2' class='nivel2'>";
						echo"<div class='encabezado'>";
							if($Cons['PN2'][$n2]['CO_color']!=''){
								echo"<div class='color' style='background-color:".$Cons['PN2'][$n2]['CO_color'].";'></div>";
							}
							
							echo"<div class='numero'><div class='aux num' title='identificador único para el nivel 2 de planificación'>n2 $n2</div>$N2 ".$Cons['PN2'][$n2]['numero'];
							if($HabilitadoEdicion=='si'){
								echo" <a href='./agrega_f.php?tabla=PLAn2&accion=cambia&id=$n2&salida=PLA'>editar $N2</a>";
								}
							echo "</div>";
							echo"<div class='nombre'>".$Cons['PN2'][$n2]['nombre']."</div>";
							
							echo"<div class='decripcion'>";
								echo "<div class='subtitulo'>Descripción:</div>";
								echo $Cons['PN2'][$n2]['descripcion'];
							echo "</div>";
							
							echo "<div class='subtitulo'>Responsables:</div>";
							echo $Cons['Actores'][$Cons['PN2'][$n2]['id_p_GRAactores']]['nombre']." ".$Cons['Actores'][$Cons['PN1'][$n1]['id_p_GRAactores']]['apellido'];

							if($HabilitadoEdicion=='si'){
								if($Cons['PN2'][$n2]['id_p_GRAactores']<'1'){
									echo" <a onclick='crearFormularioCambiaActor(this)'>asignar responsable</a>";
								}else{
									echo" <a onclick='crearFormularioCambiaActor(this)'>cambiar responsable</a>";
								}
							}
							
							echo"<div class='estado'>";
								echo "<div class='subtitulo'>Estados:</div>";
								foreach($Cons['PN2'][$n2]['estados'] as $Ke => $Ve){							
										echo $Ve['nombre']." desde: ".$Ve['desde'];
									if($HabilitadoEdicion=='si'){
										echo " <a href='./agrega_f.php?accion=cambia&id=$Ke&tabla=PLAestados&salida=PLA&campos[]=desde&campos[]=nombre'>ajustar</a>";
									}	
								}
									
								echo "<br><a href='./agrega_f.php?accion=agrega&tabla=PLAestados&salida=PLA&C-desde=$Hoy&campofijob=id_p_PLAn1&campofijob_c=&campofijoc=id_p_PLAn2&campofijoc_c=$n2&campofijod=id_p_PLAn3&campofijod_c='>cargar nuevo estado</a>";
							echo"</div>";
			
							
							foreach($Cons['CAT']['PN2'] as $k => $v){
								echo "<div class='subtitulo'>".$v['nombre']." <a href='./agrega_f.php?eliminarhabilitado=si&accion=cambia&tabla=PLAcategorias&salida=PLA&campos[]=nombre&campos[]=orden&id=$k'>editar</a></div>";
								if(count($Cons['PN2'][$n2]['categorias'][$k])==0){
									echo "<a href='./agrega_f.php?accion=agrega&tabla=PLAcategoriasVal&salida=PLA&campofijo=id_p_PLAcategorias&campofijo_c=$k&campofijob=id_p_PLAn1&campofijob_c=&campofijoc=id_p_PLAn3&campofijoc_c=&campofijod=id_p_PLAn2&campofijod_c=$n2'>definir valor para esta categoría</a>";
								}
								
								foreach($Cons['PN2'][$n2]['categorias'][$k] as $Kv => $Vv){
									echo $Vv['valor']." <a href='./agrega_f.php?eliminarhabilitado=si&campo=valor&accion=acambia&tabla=PLAcategoriasVal&salida=PLA&id=$Kv'>editar</a>";
								}
							}

							if($HabilitadoEdicion=='si'){
								echo "<br><a href='./agrega_f.php?acciona=agrega&tabla=PLAcategorias&salida=PLA&campofijo=nivel&campofijo_c=PN2'>añadir categoría</a>";			
							}
											
						echo"</div>";
						echo"<div class='contenidos'>";
						
							foreach($n2d as $n3 => $n3d){
								echo"<div iddb='$n3' tadb='PLAn3' class='nivel3'>";
									echo"<div class='encabezado'>";
										echo"<div class='numero'><div class='aux num' title='identificador único para el nivel 3 de planificación'>n3 $n3</div>$N3 ".$Cons['PN3'][$n3]['numero'];
										if($HabilitadoEdicion=='si'){
											echo" <a href='./agrega_f.php?tabla=PLAn3&accion=cambia&id=$n3&salida=PLA'>editar $N3</a>";
										}
										echo "</div>";
										echo"<div class='nombre'>".$Cons['PN3'][$n3]['nombre']."</div>";
										if($Cons['PN3'][$n3]['FI_imagen']!=''){
											if(file_exists($Cons['PN3'][$n3]['FI_imagen'])){
												echo"<img class='imagen' src='".$Cons['PN3'][$n3]['FI_imagen']."'>";
												$tx='cambiar ';										
											}else{
												$tx='cargar ';	
											}
										}else{
											$tx='cargar ';
										}
										if($HabilitadoEdicion=='si'){
											echo "<a href='./agrega_f.php?tabla=PLAn3&accion=cambia&id=$n3&campo=FI_imagen&salida=PLA'>$tx imagen</a>";
										}
										
										echo"<div class='decripcion'>";
											echo "<div class='subtitulo'>Descripción:</div>";
											echo $Cons['PN3'][$n3]['descripcion'];
										echo "</div>";
										
										echo "<div class='subtitulo'>Responsables:</div>";
										echo $Cons['Actores'][$Cons['PN3'][$n3]['id_p_GRAactores']]['nombre']." ".$Cons['Actores'][$Cons['PN1'][$n1]['id_p_GRAactores']]['apellido'];
										
										if($HabilitadoEdicion=='si'){
											if($Cons['PN3'][$n3]['id_p_GRAactores']<'1'){
												echo" <a onclick='crearFormularioCambiaActor(this)'>asignar responsable</a>";
											}else{
												echo" <a onclick='crearFormularioCambiaActor(this)'>cambiar responsable</a>";
											}
										}
										
										echo"<div class='estado'>";
											echo "<div class='subtitulo'>Estados:</div>";
											echo "<ul>";
											foreach($Cons['PN3'][$n3]['estados'] as $Ke => $Ve){
												echo "<li>";							
												echo $Ve['nombre']." desde: ".$Ve['desde'];
												if($HabilitadoEdicion=='si'){
													echo " <a href='./agrega_f.php?accion=cambia&id=$Ke&tabla=PLAestados&eliminarhabilitado=si&salida=PLA&campos[]=desde&campos[]=nombre'>ajustar</a>";
												}
												echo "</li>";	
											}
											echo "<li><a href='./agrega_f.php?accion=agrega&tabla=PLAestados&salida=PLA&C-desde=$Hoy&campofijob=id_p_PLAn1&campofijob_c=&campofijoc=id_p_PLAn2&campofijoc_c=&campofijod=id_p_PLAn3&campofijod_c=$n3'>cargar nuevo estado</a></li>";
											echo "</ul>";	
										echo"</div>";
										
							
										foreach($Cons['CAT']['PN3'] as $k => $v){
											echo "<div class='subtitulo'>".$v['nombre'];
											if($HabilitadoEdicion=='si'){
												echo" <a href='./agrega_f.php?eliminarhabilitado=si&accion=cambia&tabla=PLAcategorias&salida=PLA&campos[]=nombre&campos[]=orden&id=$k'>editar</a>";
												}
											echo "</div>";
											if(count($Cons['PN3'][$n3]['categorias'][$k])==0){
												if($HabilitadoEdicion=='si'){
													echo "<a href='./agrega_f.php?accion=agrega&tabla=PLAcategoriasVal&salida=PLA&campofijo=id_p_PLAcategorias&campofijo_c=$k&campofijob=id_p_PLAn1&campofijob_c=&campofijoc=id_p_PLAn2&campofijoc_c=&campofijod=id_p_PLAn3&campofijod_c=$n3'>definir valor para esta categoría</a>";
												}
											}
											
											foreach($Cons['PN3'][$n3]['categorias'][$k] as $Kv => $Vv){
												echo $Vv['valor'];
												if($HabilitadoEdicion=='si'){
													echo " <a href='./agrega_f.php?eliminarhabilitado=si&campo=valor&accion=acambia&tabla=PLAcategoriasVal&salida=PLA&id=$Kv'>editar</a>";
												}
											}
										}
										if($HabilitadoEdicion=='si'){
											echo "<br><a href='./agrega_f.php?acciona=agrega&tabla=PLAcategorias&salida=PLA&campofijo=nivel&campofijo_c=PN3'>añadir categoría</a>";
										}											
									echo"</div>";	
								echo"</div>";						
							}	
							if($HabilitadoEdicion=='si'){
							echo"<div class='nivel3 anade'>";
								echo"<div class='encabezado'>";
									echo "<a onclick='agregarN1(this);'>añadir ".$N3."</a><form class='oculto' target='recuadro5' action='agregamini.php' method='post' enctype='multipart/form-data'>";
										echo"<input type='hidden' name='tabla' value='PLAn3'>";	
										echo"<input type='hidden' name='postaccion' value='reload'>";	
										echo"<input type='hidden' name='accion' value='agrega'>";	
										echo"<input type='hidden' name='id_p_PLAn2' value='$n2'>";							
										echo"numero:<br>";
										echo"<input class='nombre' type='text' name='numero'>";
										echo"<br>nombre:<br>";
										echo"<input class='nombre' type='text' name='nombre'>";
										echo"<br>descripcion:<br>";
										echo"<textarea class='decripcion' class='campo' name='descripcion'></textarea>";
										echo"<br><input  type='submit' value='crear'><input  type='button' value='cancelar' onclick='cancelarN1(this);'>";
									echo"</form>";
								echo"</div>";	
							echo"</div>";
							}	
						echo "</div>";		
					echo "</div>";		
				}	
			
				if($HabilitadoEdicion=='si'){
				echo"<div class='nivel2  anade' >";
					echo"<div class='encabezado'>";
						echo "<a onclick='agregarN1(this);'>añadir ".$N2."</a><form class='oculto' target='recuadro5' action='agregamini.php' method='post' enctype='multipart/form-data'>";
							echo"<input type='hidden' name='tabla' value='PLAn2'>";	
							echo"<input type='hidden' name='accion' value='agrega'>";
							echo"<input type='hidden' name='postaccion' value='reload'>";
							echo"<input type='hidden' name='id_p_PLAn1' value='$n1'>";							
							echo"numero:<br>";
							echo"<input class='nombre' type='text' name='numero'>";
							echo"<br>nombre:<br>";
							echo"<input class='nombre' type='text' name='nombre'>";
							echo"<br>descripcion:<br>";
							echo"<textarea class='decripcion' class='campo' name='descripcion'></textarea>";
							echo"<br><input  type='submit' value='crear'><input  type='button' value='cancelar' onclick='cancelarN1(this);'>";
						echo"</form>";
					echo "</div>";
				echo "</div>";
				}	
			echo "</div>";	
		echo "</div>";
	}
		if($HabilitadoEdicion=='si'){
		echo"<div class='nivel1 anade'>";
			echo"<div class='encabezado'>";
				echo "<a onclick='agregarN1(this);'>añadir ".$N1."</a><form class='oculto' target='recuadro5' action='agregamini.php' method='post' enctype='multipart/form-data'>";
					echo"<input type='hidden' name='tabla' value='PLAn1'>";	
					echo"<input type='hidden' name='accion' value='agrega'>";
					echo"<input type='hidden' name='postaccion' value='reload'>";
					echo"numero:<br>";
					echo"<input class='nombre' type='text' name='numero'>";
					echo"<br>nombre:<br>";
					echo"<input class='nombre' type='text' name='nombre'>";
					echo"<br>descripcion:<br>";
					echo"<textarea class='decripcion' class='campo' name='descripcion'></textarea>";
					echo"<br><input type='submit' value='crear'><input  type='button' value='cancelar' onclick='cancelarN1(this);'>";
				echo"</form>";
			echo"</div>";
			}
		echo "</div>";
					
echo"</div>";

echo"<div id='auxiliares'>";
echo "<form id='modeloresponsable' target='recuadro5' action='cambiamini.php' method='post' enctype='multipart/form-data' onsubmit='cargaNuevoActor(this);'>";
echo "<input type='hidden' name='tabla' value=''>";
echo "<input type='hidden' name='accion' value='cambia'>";
echo "<input type='hidden' name='id' value=''>";
echo "<select name='id_p_GRAactores' value='-elegir-' onchange='evaluaActor(this)'>";
	echo "<option value=''>-elegir-</option>";
	echo "<option value='0'>-ninguno-</option>";
	echo "<option value='n'>-crear un nuevo actor-</option>";
	foreach($Cons['Actores'] as $idA => $Adata){
		echo "<option value='$idA'>".$Adata['nombre']." ".$Adata['apellido']."</option>";
	}	
echo "</select>";
echo "<input type='text' style='display:none;width:70%;' name='id_p_GRAactores_n' value=''>";
echo "<input type='submit' style='display:none;width:30%;' value='crear'>";	
echo "</form>";
echo "</div>";

	?>
	
<script type="text/javascript">
//Funciones de generación de formularios

	function evaluaActor(_this){	
		if(_this.value=='n'){
			_this.style.display='none';
			_this.nextSibling.style.display='inline-block';
			_this.nextSibling.nextSibling.style.display='inline-block';			
		}else if(_this.value===''){
			
		}else{
			cambiaActor(_this.parentNode);
			_this.parentNode.nextSibling.style.display='inline-block';
			_this.parentNode.submit();
			_this.parentNode.parentNode.removeChild(_this.parentNode);
		}
	}

	function cambiaActor(_this){
		console.log(_this.previousSibling.value);
		_this.previousSibling.innerHTML=_this.childNodes[3].options[_this.childNodes[3].selectedIndex].text;
		
	}
	
	function cargaNuevoActor(_this){
		if(_this.childNodes[4].value!=''){
		console.log(_this.previousSibling.value);
		_this.previousSibling.innerHTML=_this.childNodes[4].value;
		_this.nextSibling.style.display='inline-block';		
		_this.style.display='none';	
		_dest=document.getElementById('auxiliares');
		_dest.appendChild(_this);		
		//_this.submit();		
		//_this.parentNode.removeChild(_this);
		}
	}
		
	function crearFormularioCambiaActor(_this){
		_modelo=document.getElementById("modeloresponsable");
		_clon=_modelo.cloneNode(true);
		
		_tabla=_this.parentNode.parentNode.getAttribute('tadb');		
		_clon.childNodes[0].value=_tabla;
		_iddb=_this.parentNode.parentNode.getAttribute('iddb');		
		_clon.childNodes[2].value=_iddb;
		_clon.setAttribute("id","candidato");
		
		_dest=_this.parentNode;
		//_dest.apendchild(_clon);
		_dest.insertBefore(_clon,_this);	
		
		_this.innerHTML='cambiar responsable';
		_this.style.display='none';
	}	


	function agregarN1(_this){
		_this.nextSibling.style.display='block';
	}	
	function cancelarN1(_this){
		_this.parentNode.style.display='none';
	}	
	
	//creador de filas candidatos, nuevas localizaciones a validar por agrega_mini.php
	function crearcandidato(_this){

		_modelo=document.getElementById("modelo");
		_clon=_modelo.cloneNode(true);
		_clon.setAttribute("id","candidato");
		_dest=document.getElementById("localizaciones")
		_dest.insertBefore(_clon,_dest.childNodes[2]);
		var _campos = _this.parentNode.getElementsByClassName("campo");

		_contenedores=document.getElementById("candidato").childNodes;
		_contenedores[0].childNodes[0].innerHTML=_campos[3].value;
		_contenedores[0].childNodes[1].innerHTML=_campos[4].value;
		_contenedores[0].childNodes[2].innerHTML=_campos[5].value;
		
		_contenedores[1].innerHTML=_campos[6].value;		
		_contenedores[2].innerHTML=_campos[7].value;					
		_contenedores[3].childNodes[0].src='';
		
	}
	
	
	//creador de formulario y puntos candidatos, nuevas localizaciones a validar por agrega_mini.php	
	_formularLocalizacionSeteado='no';	
	function formularLocalizacion(event){		

		if(_formularLocalizacionSeteado=='si'){
			_viejos=document.getElementById('formulariolocalizacion');
			_viejos.parentNode.removeChild(_viejos);
		}		
		
		if(_accionactiva=='nuevaincidencia'){//para nuevas incidencias						
			_modelo=document.getElementById('modeloformulariolocalizacion');		
			var _copia = _modelo.cloneNode(true);
			_copia.setAttribute('id','formulariolocalizacion');
							
			_formularios=document.getElementById('formularios');	
			_formularios.appendChild(_copia);	
			
			_bb=document.getElementsByTagName('body');//captura el desplazamiento vertical de la pantalla para compenzar el valor obtenido de la localización del click.
			_scroll=_bb[0].scrollTop;		
			_posy = event.pageY-document.getElementById("plano"+_selectoPlano).getBoundingClientRect().top-_scroll;			
			_posx = event.pageX-document.getElementById("plano"+_selectoPlano).getBoundingClientRect().left;			

			
			//alert(_posx+" - "+ _posy);
					
			_copia.childNodes[1].value=_posx;
			_copia.childNodes[2].value=_posy;		
			_copia.childNodes[3].value=_selectoPlano;
	
			_formularLocalizacionSeteado='si';
			
			_CP=document.getElementById("candidatoP");		
			if(_CP != null){	
				_CP.parentNode.removeChild(_CP);
			}
	
			//crea candidato de punto de localización a validar 
			_modeloP=document.getElementById("modeloP");		
			_clon=_modeloP.cloneNode(true);		
			_clon.setAttribute("id","candidatoP");		
			_clon.style.left=_posx-3;
			_clon.style.top=_posy-3;			
			document.getElementById("plano"+_selectoPlano).appendChild(_clon);
					
		}else if(_accionactiva=='nuevopunto'){//para puntos adicionales a una incidencia previa
			//añade puntos a una incidencia existente
			_modelo=document.getElementById('modeloNuevopunto');		
			var _copia = _modelo.cloneNode(true);
			_copia.setAttribute('id','formulariolocalizacion');
							
			_formularios=document.getElementById('formularios');	
			_formularios.appendChild(_copia);	
			
			_posx = event.pageX-document.getElementById("plano"+_selectoPlano).getBoundingClientRect().left;			
			_posy = event.pageY-document.getElementById("plano"+_selectoPlano).getBoundingClientRect().top;
			
			//alert(_posx+" - "+ _posy);
					
			_copia.childNodes[1].value=_posx;
			_copia.childNodes[2].value=_posy;					
			_copia.childNodes[3].value=_selectoPlano;
			_copia.childNodes[4].value=_idLocNuevopunto;
				
			_formularLocalizacionSeteado='si';
	
			
			_CP=document.getElementById("candidatoP");		
			if(_CP != null){	
				_CP.parentNode.removeChild(_CP);
			}
	
			//crea candidato de punto de localización a validar 
			_modeloP=document.getElementById("modeloP");		
			_clon=_modeloP.cloneNode(true);		
			_clon.setAttribute("id","candidatoP");		
			_clon.style.left=_posx-3;
			_clon.style.top=_posy-3;			
			document.getElementById("plano"+_selectoPlano).appendChild(_clon);					
		}
	}

</script>

<script type="text/javascript">
//Funciones de arrastre para mover puntos.
 
	_mouseY=0;
	_mouseX=0;
	
	_auxX = document.getElementById('auxX');
	_auxY = document.getElementById('auxY');

	//activar drag an drop para los puntos de relevamiento
	function handleDragStart(e) {
		
		this.style.opacity = '0.4';  // this / e.target is the source node.
		//this.style.overflow = 'hidden'; 
		//this.style.border='none';		
		_mouseX=e.pageX;
		_mouseY=e.pageY;	
		_elementoDrag=this;
		//_auxX.innerHTML=_mouseX;
		//_auxY.innerHTML=_mouseY;		
		
	}
	
	//acciones al pasar sobre un objeto
	function handleDragOver(e) {
		
		if (e.preventDefault) {
			e.preventDefault(); // Necessary. Allows us to drop.
		}
		//this.style.opacity = '0.8';
		//e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.	
		return false;
	}	
	
	
	//funcion disparada al soltar un punto sobre un plano
	function handleDrop(e) {
		
		
		
		this.style.opacity = '1';  // this / e.target is the source node.	  
		_varX= e.pageX-_mouseX;
		_varY= e.pageY-_mouseY;
		_elementoDrag.style.opacity = '1';
		
		_valx = parseInt(_elementoDrag.style.left.replace('px', ''));
		_valx = _valx +  (e.pageX-_mouseX);

		_valy = parseInt(_elementoDrag.style.top.replace('px', ''));
		_valy = _valy +  (e.pageY-_mouseY);		
		_auxX.innerHTML=_valx;
		_auxY.innerHTML=_valy;
		
		_elementoDrag.style.left=(_valx + "px");		
		_elementoDrag.style.top =(_valy + "px");		
		//alert(_varX + " , "+ _varY);
		
		_dbid=_elementoDrag.getAttribute('dbid');

		document.getElementById('cambiaminiTabla').value='RELlocalizaciones';			
		document.getElementById('cambiaminiPosId').value=_dbid;		
		document.getElementById('cambiaminiPosX').value=_valx+3;	
		document.getElementById('cambiaminiPosY').value=_valy+3;		
		
		_dbidPA=_elementoDrag.getAttribute('dbidPA');//identifica a los elementos de la tabla de putnos adicionales	
		if(_dbidPA>0){
			document.getElementById('cambiaminiTabla').value='RELlocpuntosadicionales';				
			document.getElementById('cambiaminiPosId').value=_dbidPA;					
		}
	
		document.getElementById('cambiaminiPosY').parentNode.submit();	

	}
	
	//activar funcion (event listener) para handleDragStart para puntos principales y adicionales
	var cols = document.querySelectorAll('.loc');
	[].forEach.call(cols, function(col) {
		col.addEventListener('dragstart', handleDragStart, false);
	});
	
	//activar funcion (event listener) para handleDragOver para planos	
	var cols = document.querySelectorAll('.imagenplano');
	[].forEach.call(cols, function(col) {
 		col.addEventListener('dragover', handleDragOver, false);  
 		col.addEventListener('drop', handleDrop, false);
	});	
		
</script>	
<script type="text/javascript">
//funciones de destacado de elementos


	//Funciones para visualizar imagenes
	
	function muestraImagen(_idloc,_idPa){//carga aplicación formiulario para imagenes
		document.getElementById('recuadro2').src='./REL_img.php?idloc='+_idloc+'&idPa='+_idPa;	
	}
	
	function resaltaimagen(_idloc,_idPa){//muestra la imagen correspondiente al punto activo dentro de la muestra
		//_idLoc representa el id de la incidencia o localizacion
		//_idPa representa el id del punto adicional al que corresponde la imagen. 0 representa la imagen original
		_imagenes=document.getElementsByName('fotomini');		
		for (i = 0; i < _imagenes.length; ++i) {
			
			_consultaIdLoc=_imagenes[i].getAttribute('idloc');
			_consultaIdPa=_imagenes[i].getAttribute('idpa');
			
			if(_consultaIdLoc==_idloc){
				_imagenes[i].style.display='none';
				if(_consultaIdPa==_idPa){_imagenes[i].style.display='block';}
			}			
		}				
	}
	



	//funcion para activar (poner visible) un plano
	_selectoPlano='';	
	
	function selectorPlano(_idp){
		
		_grupos=document.getElementsByName('grupo');		
			
			for (i = 0; i < _grupos.length; ++i) {
			   _grupos[i].style.display='none';
			   if(_idp=='TODOS'){_grupos[i].style.display='block';}
			}
			
			document.getElementById('grupo'+_idp).style.display='block';
		
			if(_selectoPlano>0){
				document.getElementById('plano'+_selectoPlano).style.display='none';
				document.getElementById('selectorPlano'+_selectoPlano).setAttribute('class','selector');			
			}
			//alert('a mostrat plano'+_idp);		
			document.getElementById('plano'+_idp).style.display='block';
			document.getElementById('selectorPlano'+_idp).setAttribute('class','selector activo');
			_selectoPlano=_idp;			
	}


	
	//funciones para resaltar puntos en el plano
	_selectoLocalizacion='no';	
	function resaltaLocalizacion(_this){
		
		_dbid=_this.getAttribute('dbid');		
		
		if(_selectoLocalizacion!='no'){
			document.getElementById('localizacion'+_selectoLocalizacion).setAttribute('class','fila');

			_classactual=document.getElementById('puntoLocalizacion'+_selectoLocalizacion).getAttribute('class');
			_classactual=_classactual.replace(' selecto','');
			
			_elementos=document.getElementsByName('puntoLocalizacion'+_selectoLocalizacion);
				
			for (i = 0; i < _elementos.length; ++i) {
			   _elementos[i].setAttribute('class',_classactual);		
			}
			
		}			
		
		if(_dbid!=''){
		
			document.getElementById('localizacion'+_dbid).setAttribute('class','fila selecto');
					
			
			_classactual=document.getElementById('puntoLocalizacion'+_dbid).getAttribute('class');
			_elementos=document.getElementsByName('puntoLocalizacion'+_dbid);			
			for (i = 0; i < _elementos.length; ++i) {
			   _elementos[i].setAttribute('class',_classactual+' selecto');	
			}
	
			_selectoLocalizacion=_dbid;	
		}
	}	


		
	<?php 
	
	if($_GET['planoId']>0){
		echo"
		selectorPlano('".$_GET['planoId']."');
		";
	}	
	?>


</script>
<?php

	/*medicion de rendimiento lamp*/
	$endtime = microtime(true);
	$duration = $endtime - $starttime;
	$duration = substr($duration,0,6);
	//echo "<br>lapso rendimiento lamp : " .$duration. " segundos";
?>
</body>

