<?php
/**
* aplicación de visualización y gestion de documentos de trabajo. consulta carga y genera la interfaz de configuración de lo0s mismos.
 * 
 ***  
* @package    	geoTReCC
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar>
* @author    	http://www.trecc.com.ar
* @author		based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
* @copyright	2024 TReCC SA
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2018 - Universidad de Buenos Aires
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
include_once("./_config_acc/claveunica.php");
include_once("./_config_inst/config.php");

// verificación de seguridad 
if(!isset($_SESSION)) {
	session_start(); 
	if(!isset($_SESSION[$CU]["usuario"]['id'])){
		$_SESSION[$CU]["usuario"]['id']='-1';
	}
}

// funciones frecuentes
include("./comun_general/fechas.php");
include("./comun_general/cadenas.php");

$COD = isset($_GET['cod'])?$_GET['cod'] : '';
$ID = isset($_GET['id'])?$_GET['id'] : '';
if($ID==''&&$COD==''){
	header('location: ./index.php');
}

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d"); $HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

?><!DOCTYPE html><?

$Config['pla-nivel1']='Lineamiento/Lineamientos';
$Config['pla-nivel2']='Programa/Programas';
$Config['pla-nivel3']='Proyecto/Proyectos';

$e=explode("/",utf8_decode($Config['pla-nivel1']));
$N1=$e[0];
$Ns1=$e[1];
$e=explode("/",utf8_decode($Config['pla-nivel2']));
$N2=$e[0];
$Ns2=$e[1];
$e=explode("/",utf8_decode($Config['pla-nivel3']));
$N3=$e[0];
$Ns3=$e[1];

if(!isset($_GET['modo'])){$_GET['modo']='';}

?>
<!DOCTYPE html>
<head>
    <title><?php echo $CONF['plataforma']['nombre'];?></title>
	<link rel="stylesheet" type="text/css" href="./css/geogecgeneral.css">
	<link rel="stylesheet" type="text/css" href="./css/geogec_app_plae.css">

	<style type="text/css">

	body #encabezadopagina{
		display:none;
		position:fixed;
		width:700px;
		height:10px;
		z-index:1000;
		text-align:left;
		top:0px;
		margin-left:50px
	}
	#page[modo="fichas"] .ficha{
		margin-top:90px;
	}
@media print{
	body #encabezadopagina{
		display:block;
	}
	div.anade{
		 	display:none;
	}
	
	body{
		 	background-image:none;
	}
	 
	#page{
		 	background-image:none;
		 	border:0px;
	}
		
	#pageborde{
		 	border:0px;
		 	background-color:transparent;
	}
				 
	 a{
		display:none;
	}
		
	.aux{
	 	display:none;
	} 
	 
	a#botontabla, a#botonfichas, a#botongestion,  a#botoncronograma, a#botondescargatabla, #buscador, a#botoneditartextoplan, a#botonmenos, #plan[ampliado="-1"] a#botonmas, #plan[ampliado="1"] a#botonmenos{
	 	display:none;
	}
	 
	body > #encabezado{
		display:none;
	}
	
}

#coladesubidas{
		position:fixed;
		bottom:0px;
		right:0px;
		background-color:#fff;
		max-width:90px;
		min-width: 5px;
		min-height: 5px;
		z-index:5000;
	}
	
	
	
	.archivo{
	  	border: 1px solid #08afd9;
		margin: 1px;
		color: #444;
		margin-top:12px;
	}
	
	.archivo img{
		margin: 1px;
		vertical-align:middle;
	}
	
	.archivo #nom{
		top:0px;
		left:0px;
		position:absolute;
		margin:1px;
	}
	
	#coladesubidas .archivo{
		position:relative;
	  	transition: right 4s, bottom 4s;
	  	background-color:#fff;
	  	display: block;
	}
		
	#coladesubidas .archivo img{
		width:12px;
		position:relative;
	}
	
	#coladesubidas .archivo #nom{
		display:none;
		position:relative;
		
	}
	#coladesubidas .archivo:hover span#nom{
		display:block;
		position:absolute;
		left:-150px;
		top:6px;
		width:150px;
		text-align:right;
		background-color:lightblue;
	}

	.archivo #barra{
		background-color:#ff944d;
		border-left:1px solid #ff6600; 
		display:block;
		position:absolute;
		z-index:1;
		height:100%;
		max-width: calc(100% - 1px);
	}
	
	.archivo div, .archivo span, .archivo img{
		z-index:2;
		position:relative;
	}
	.archivo .cargando{
		position:relative;
		top:-14px;
		left:0px;
		display:block;
		width:70px;
	}
	
	#coladesubidas .archivo .cargando{
		top:0px;
	}
	
	
	.archivo .cargando img{
		width: 11px;
	}
	
	.archivo .cargando #val{
		font-size:10px;
	}
	.archivo  #nom{
		width:calc(100% - 13px);
		height:15px;
		overflow:hidden;
	}
	
	#listadosubiendo > p.archivo{
		position:relative;
		margin-top:14px;
	}
	
	
	</style>
</head>


<body>
		
	<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>		
	<script type="text/javascript" src="./js/tinymce.5.10.0/tinymce.min.js"></script>
	  
	<div id="pageborde">
	<div id="page" nivel='page' iddb='' modo='gestion'>
		
		<?php include('./sis_usuarios/usu_acceso.php');?>
		
        <div id='encabezado'>
		<a href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
			<h2><?php echo $CONF['plataforma']['nombre'];?></h2>
			<p><?php echo $CONF['plataforma']['descrip'];?></p>
		</a>

		
		<a id='botongestion' onclick='modoA("gestion");' >ver modo gestion</a>
		<a id='botonfichas'  onclick='modoA("fichas");' >ver modo fichas</a>
		<a id='botontabla'  onclick='modoA("tabla");' >ver modo tabla</a>
		<a id='botontexto'  onclick='modoA("texto");' >ver modo texto</a>
		<a id='botoncronograma'  onclick='modoA("cronograma");' >ver cronograma</a>
		<div id='buscador'><label>buscar:</label><input name='busqueda' onkeyup='tecleaBusqueda(this,event)'></div>
		<br>
		<a id='botondescargatabla' onclick='alert("función en desarrollo");'>decargar tabla de acciones</a><br>
		<a id='botonencabezado' onclick='editarPlanEncabeza();'>configurar encabezado</a><br>
		
		<div id='plan'>
			<a id='botonmas' onclick='ampliaCierraTextoplan()'>ver mas</a>
			<a id='botonmenos' onclick='ampliaCierraTextoplan()'>ver menos</a>
			<div id='nombre'><span id='dat'></span><a id='botoneditartextoplan' onclick='editarPlanGral()'>editar</a></div>	
			<div id='descripcion'><span id='dat'></span></div>
		</div>
		<div id='contenidos' class='contenidos'></div>
	</div>
	</div>
	
	<div id='form_pla_encabeza' draggable='true' ondragstart='drag_start(event,this);'>
		<div id='dBordeL' class='dragborde izquierdo'></div>
		<div class='escroleable'>
          				
		   	<br>contenido:<br>
			<textarea class='mceEditable' id='encabezado' name='encabezado'></textarea>

			<input id='ejec' type="submit" onclick='guardarPlanEnca(this)' class="general" value="guardar	">
			<input type="button" class="cancela general" value="cancelar" onclick="cancelarPlanGral(this);">
			
		</div>   
	</div>
	
	<div id='form_pla_gral' draggable='true' ondragstart='drag_start(event,this);'>
		<div id='dBordeL' class='dragborde izquierdo'></div>
		<div class='escroleable'>
		    <input type="hidden" name="id" value="">	
		    <input type='hidden' name='nivel' value='PLAn0'>
		    <input type='hidden' name='accion' value='agrega'>
		    
		    
  
        	<h2>Nombre</h2>
        	<input class='nombre' type='text' name='nombre'>
        				
		   	<br>descripcion:<br>
			<textarea class='mceEditable' id='descripciongral' name='descripcion'></textarea>

			<input id='ejec' type="submit" onclick='guardarPlanGral(this)' class="general" value="crear">
			<input type="button" class="cancela general" value="cancelar" onclick="cancelarPlanGral(this);">
			
		</div>   
	</div>
		
	<div id='form_pla' draggable='true' ondragstart='drag_start(event,this);' nivel=''>	
	    <div id='dBordeL' class='dragborde izquierdo'></div>
	    
	    <div class='escroleable'>
		    <input type="hidden" name="id" value="">	
		    <input type='hidden' name='nivel' value='PLAn1'>
		    <input type='hidden' name='accion' value='agrega'>
			
		    <h2 style='display:none'>Añadir <span id='nombreElemento'></span><span id='aclaracion'>en plan 1</span></h2>

		    
	    	    
	        <div class='paquete identificacion'>
	        	<h2>Nombre</h2>
	        	<input class='nombre' type='text' name='nombre'>
	        	<h2>Número</h2>
				<input class='nombre' type='text' name='numero'>				
				Color: <input type='color' name='CO_color' value='agrega'> Público: <input id='zz_publico' name='zz_publico' type='hidden'>
				<input for='zz_publico' type='checkbox' onclick="alterna01(this);">
				
				<h2 id='responsables'>Responsable:</h2>
				<div>	
			  	<input type='hidden' id='id_p_GRAactores' name='id_p_GRAactores'>
	            <input type='text' class='chico' id='id_p_GRAactores_n' name='id_p_GRAactores_n'
	                onKeyUp="actualizarResponsables(event,this);"
	            >
	            <div class='sugerencia uno'>
	                <a idg='0' onclick="cargarOpcion(this);">-vacio-</a><br>
	            </div>
	            <a id='muestramas' onclick="this.parentNode.querySelector('.sugerencia.dos').style.display='block';">mostrar más</a>	
	            <div class='sugerencia dos'>    
	            </div>
	            </div>
	            
				<h2 id='estados'>Estados:</h2>
				<div id='listaestados'>
					
					
					
				</div>
	            <div class='estado' id='nuevoestado'> 
					nuevo estado: <input type='text' name='estado'> 
					desde:   <input type='date' name='desde'>
				</div>	
	            <div id='adjuntos' class='adjuntos'>
		            <h2>
		                Documentos Subidos:
		                <form action='' enctype='multipart/form-data' method='post' id='uploader' ondragover='resDrFile(event)' ondragleave='desDrFile(event)'>
		                    <div id='contenedorlienzo'>									
		                        <div id='upload'>
		                            <label>Arraste todos los archivos aquí.</label>
		                            <input exo='si' multiple='' id='uploadinput' type='file' name='upload' value='' onchange='cargarCmp(this);'></label>
		                        </div>
		                    </div>
		                </form>
		            </h2>
		            <div id="listadosubiendo">
		            </div>
		            
		            <div id='adjuntoslista'></div>
		        </div>
            </div>

 			<div id='paquetecategorias' class='paquete atributos'>
	        	<h2>Atributos</h2>
	        	<div id='atributoslista'></div>
	        	<a onclick='document.querySelector("#paquetecategorias #tiposcategorias").style.display="block";'>+ categría</a>
	        	<div id='tiposcategorias'>
	        		<h4>Elija el tipo de categoría que prefiere</h4>
		        	<div id='opciones'>
		        		<a onclick='document.querySelector("#paquetecategorias #nombrecategoria").style.display="block";document.querySelector("#paquetecategorias #tiposcategorias").removeAttribute("style")'>Libre</a>
		        		<div id='estandar'>
		        		</div>
	        		</div>
	        		<div id='descripcion'>
	        		</div>	        		
	        		
	        	</div>
	        	<div id='nombrecategoria'>
	        		<h4>Elija un nombre para la categoría</h4>
	        		<input name='catnom' value=''>
	        		<a onclick='crearCategoria("",this.parentNode.querySelector("[name=\"catnom\"]").value)'>crear categoría</a>
	        	</div>		
	        </div>
	        	
			<div class='paquete desarrollo'>
			   	<br>descripcion:<br>
				<textarea class='mceEditable' id='descripcion' name='descripcion'></textarea>
				
	        </div>
		  
			    
		</div>    
	    
		<input id='ejec' type="submit" onclick='guardarPlan(this)' class="general" value="crear">
		<input type="button" class="imprimir general" value="grd/impr." onclick="imprimir();">				
		<input type="button" class="cancela general" value="cancelar" onclick="cancelarPlan(this);">
		<input id='elim' type="button" onclick='eliminarPlan(this)' class="eliminar" value="eliminar">	
		<input type="button" class="moverN general" value="^ mover" onclick="moverNivelMenu();">
		<div id='menumover'>
			<a>cerrar</a>
			<div id='listamover'>
			
			</div>
		</div>
		<input type="button" class="subirN general" value="<+ nivel" onclick="subirNivel(this);">
		<input type="button" class="bajarN general" value="nivel ->" onclick="bajarNivelMenu();">
	</div>
	
	
	<div id='coladesubidas'></div>	

	
<script type="text/javascript">
	var _IdUsu="<?php echo $_SESSION[$CU]["usuario"]['id'];?>";
	var _Acc = "proc";
	
</script>

<script type="text/javascript" src="./sis_gral/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->

<script type='text/javascript' src='./app_plae/app_plae_cargainicial.js'></script>
<script type='text/javascript' src='./app_plae/app_plae_carga_modo_tabla.js'></script>
<script type='text/javascript' src='./app_plae/app_plae_carga_modo_fichas.js'></script>
<script type='text/javascript' src='./app_plae/app_plae_carga_modo_texto.js'></script>
<script type='text/javascript' src='./app_plae/app_plae_carga_modo_cronograma.js'></script>
<script type='text/javascript' src='./app_plae/app_plae_pagina.js'></script>
<script type='text/javascript' src='./app_plae/app_plae_envios_iniciales.js'></script>
<script type='text/javascript' src='./app_plae/app_plae_envios.js'></script>

<script type='text/javascript'>

    document.querySelector('#buscador input[name="busqueda"]').focus();       
    consultarGrupos();	    
    consultarCategorias();	    
    
	<?php if(!isset($_GET['idseg'])){$_GET['idseg']='';} ?>
	<?php if(!isset($_GET['idacc'])){$_GET['idacc']='';} ?>
	
	_Idsel='<?php echo $_GET['idseg'];?>'; // id del elemento seleccionado 
	_Nivelsel='<?php echo $_GET['idacc'];?>'; // nivle dle elemento seleccionado
		    
    function llamarElementosIniciales(){
		if(_DatosUsuarios.delPanel==undefined){return;}
		if(_DatosGruposCargado=='no'){return;}
		if(_DatosCategoriasCargado=='no'){return;}		
		if(_DataPlanCargado=='no'){return;}		
		
		if(_Idsel=='' && _Nivelsel==''){return;}
		iraPlan(_Idsel,_Nivelsel,"");
  		  		
  		_Idsel='';
        _Nivelsel='';
	}
</script>

</body>
