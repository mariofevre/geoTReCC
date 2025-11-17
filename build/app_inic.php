<?php
/**
* aplicación de inicio de espacio de trabajo.
 * 
 * 
 *  
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

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); 
//error_reporting(-1);
ini_set('display_errors', 1);
// verificación de seguridad 
//include('./includes/conexion.php');
if(!isset($_SESSION)) {
	 session_start(); 

	if(!isset($_SESSION["geogec"]["usuario"]['id'])){
		$_SESSION["geogec"]["usuario"]['id']='-1';
	}
}

$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";


// funciones frecuentes
include($GeoGecPath."/includes/fechas.php");
include($GeoGecPath."/includes/cadenas.php");


$COD = isset($_GET['cod'])?$_GET['cod'] : '';
$ID = isset($_GET['id'])?$_GET['id'] : '';
if($ID==''&&$COD==''){
	//header('location: ./index.php');
}

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	
// medicion de rendimiento lamp 
$starttime = microtime(true);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>GEC - Plataforma Geomática</title>
    <?php include("./includes/meta.php");?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    
    
    
    
    <link href="./external/bootstrap-5.2.0/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

	<link href="./css/geogec_app_inic.css" rel="stylesheet" >

    <style>
 


	
    </style>
    
</head>

<body class='presentation-page sidebar-collapse'>
	
<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./js/ol6.3/ol.js"></script>



<nav class="navbar navbar-color-on-scroll navbar-transparent navbar-expand-lg" color-on-scroll="100" id="sectionsNav" con_proyectos='no' con_inic='no'>
	<div class="container">
		
		<div class="navbar-translate">
			<ul class="nav justify-content-end">
				<li class="nav-item">				
					<div class="navbar-brand">
					geoGEC UBA
					</div>
				</li>	
				<li class="nav-item  nav-link">		
				<button class="navbar-toggler" type="button" data-toggle="collapse" aria-expanded="false" aria-label="Toggle navigation">
				<span class="sr-only">Toggle navigation</span>
				<span class="navbar-toggler-icon"></span>
				<span class="navbar-toggler-icon"></span>
				<span class="navbar-toggler-icon"></span>
				</button>
				</li>	
			</ul>		
		</div>

		<div class="collapse navbar-collapse">		
			
			<ul class="navbar-nav justify-content-end">
				<li class="nav-item  nav-link" id='item_proy' estado='vacio'>
					<div id='contador'>0</div><select class="form-select" onchange="abrirProyecto(this.value)" name='proyectos'>
						<option class='aguante' value='' selected>Abrir proyecto</option>
					</select>
					
				</li>
				<li class="nav-item nav-link" id='item_inic' estado='vacio'>
					<div id='contador'>0</div><select class="form-select" onchange="consultarInicio(this.value)" name='inicios' >
						<option class='aguante' value='' selected>Completar proyecto</option>
					</select>
					
				</li>
			</ul>	
				
			<ul class="navbar-nav ml-auto">
				<li class="button-container nav-item iframe-extern  nav-link">
					<?php 
					include('./usuarios/usu_acceso.php');
					?>
				</li>				
			</ul>			
		</div>
			
	</div>
</nav>
  
<div class="page-header header-filter clear-filter" data-parallax="true" style="background-image: url('./img/ilustraciones/abstracto_pantalla_verde_violeta.jpg');">

	<div class="container">
		<div class="row">
		<div class="col-md-8 ml-auto mr-auto">
		<div class="brand">
		<h1>geoGEC
		<span class="pro-badge">
		UBA
		</span>
		</h1>
		<h3 class="title">
			Un aporte tecnológico para municipios.<br>
			Gestionando la adaptación al cambio climático.
		</h3>
		</div>
		</div>
		</div>
	</div>
</div>

<div class="main main-raised">
	<div class="section">
		<div class="container">
			<div class="row">
			<div class="col-md-8 ml-auto mr-auto">
				<h4 class="description text-center">Damos soporte a su gestión de contención ante futuros eventos extremos.</h4>
			</div>
			
			</div>
			<div class="features-1">
			<div class="row">
					
				<div class="col-md-4" data-bs-toggle="modal" data-bs-target="#ingresoModal">
					<div class="info">
						<div class="image-container">
							<img class="components-macbook" src="./img/app_inic_icon_onoff.png" alt="">
						</div>
						<h4 class="info-title">Iniciar un proyecto</h4>
						<p>Genere un proyecto automáticamente para iniciar un plan de seguimiento o gestión de su partido.</p>
					</div>
				</div>
				
				<div class="col-md-4" onclick='alert("en desarrollo");'>
					<div class="info">
						<div class="image-container">
							<img class="components-macbook" src="./img/app_inic_icon_llamada.png" alt="">
						</div>
						<h4 class="info-title">Contactar al Equipo GEC</h4>
						<p>Contactese con el equipo académico del cantro de Gestión de Espacios costeros, FADU UBA.</p>
						<p>Al ponernos en contacto podremos coordinas capacitaciones, metodologías de gestión y generación de datos para la adaptación al cambio climático.</p>
					</div>
				</div>
				
				<div class="col-md-4" onclick='alert("en desarrollo");'>
					<div class="info">
						<div class="image-container">
							<img class="components-macbook" src="./img/app_inic_icon_documentacion.png" alt="">
						</div>
						<h4 class="info-title">Documentación</h4>
						<p>Explore nuestra propuesta. Lea la descripción de nuestros proyectos</p>
					</div>
				</div>
				
			</div>
			</div>
		</div>
	</div>
	
	
	<div class="section-components">
		<div class="container">
		<div class="row">
		<div class="col-lg-5 col-md-12">
		<h3 class="title">Funciones básicas
		</h3><h6 class="description">Qué permite geoGEC</h6>
		<h5 class="description">
			a desarrollar a desarrollar  a desarrollar  a desarrollar  a desarrollar  a desarrollar a desarrollar 
			a desarrollar a desarrollar  a desarrollar  a desarrollar  a desarrollar  a desarrollar a desarrollar 
			a desarrollar a desarrollar  a desarrollar  a desarrollar  a desarrollar  a desarrollar a desarrollar 
			a desarrollar a desarrollar  a desarrollar  a desarrollar  a desarrollar  a desarrollar a desarrollar 
			a desarrollar a desarrollar  a desarrollar  a desarrollar  a desarrollar  a desarrollar a desarrollar 
		</h5>

		</div>
		
		<div class="col-lg-6 col-md-12 ml-auto">
			<div class="image-container">
				<img class="components-macbook" src="./img/ilustraciones/herramientas.jpg"  style='max-width:100%' alt="">
			</div>
		</div>
		</div>
		</div>
	</div>
	
	
	<div class="section section-cards section-dark">
		<div class="container">
		<div class="row">
		<div class="col-md-7">
			<div class="image-container">
				<img class="components-macbook" src="./img/ilustraciones/software_libre.png" style='width: 400px;max-width:100%' alt="">
			</div>
		</div>
		
		<div class="col-md-4 ml-auto">
			<div class="section-description">
			<h3 class="title">Software Libre, datos privados</h3>
			<h6 class="description">Garantia de autonomía</h6>
			<h5 class="description">
				El software que desarrollamos, también lo publicamos de forma libre. Eso significa que siempre estará disponible para descargarlo y hacer una nueva copia en un nuevo servidor cuando nuestro servidor ya no le resulte conveniente.
				Por otro lado los datos son guardados permitiendo diferentes niveles de acceso, deacuerdo a su política de divulgación.
			</h5>
		</div>
		

		
		</div>
			
		</div>
		</div>
	</div>
	
	<div class="section section-overview">
		<div class="features-5" style="background-image: url('./img/ilustraciones/noche.jpg')">
		<div class="col-md-8 ml-auto mr-auto text-center">
		<h2 class="title">Algunos ejemplos de funcionalidad</h2>
		</div>
		<div class="container">
		<div class="row">
		<div class="col-sm-3">
		<div class="info">
			<div class="image-container">
				<img class="components-macbook" src="./img/app_inic_icon_rele.png" alt="">
			</div>
		
		<h4 class="info-title">Gestión de relevamientos</h4>
		<p>
			a desarrollar a desarrollar  a desarrollar  a desarrollar  a desarrollar  a desarrollar a desarrollar 
			a desarrollar a desarrollar  a desarrollar  a desarrollar  a desarrollar  a desarrollar a desarrollar
			</p>
		</div>
		</div>
		<div class="col-sm-3">
		<div class="info">
			<div class="image-container">
				<img class="components-macbook" src="./img/app_inic_icon_ind.png" alt="">
			</div>
		
		<h4 class="info-title">Evaluación de alternativas</h4>
		<p>
			a desarrollar a desarrollar  a desarrollar  a desarrollar  a desarrollar  a desarrollar a desarrollar 
			a desarrollar a desarrollar  a desarrollar  a desarrollar  a desarrollar  a desarrollar a desarrollar
			 </p>
		</div>
		</div>
		<div class="col-sm-3">
		<div class="info">
			
			<div class="image-container">
				<img class="components-macbook" src="./img/app_inic_icon_capa.png" alt="">
			</div>
		<h4 class="info-title">Analisis territorial</h4>
		<p>
			a desarrollar a desarrollar  a desarrollar  a desarrollar  a desarrollar  a desarrollar a desarrollar 
			a desarrollar a desarrollar  a desarrollar  a desarrollar  a desarrollar  a desarrollar a desarrollar
			</p>
		</div>
		</div>
		<div class="col-sm-3">
		<div class="info">
			<div class="image-container">
				<img class="components-macbook" src="./img/app_inic_icon_game.png" alt="">
			</div>
			
		<h4 class="info-title">Herramientas de divulgación</h4>
		<p>a desarrollar a desarrollar  a desarrollar  a desarrollar  a desarrollar  a desarrollar a desarrollar 
			a desarrollar a desarrollar  a desarrollar  a desarrollar  a desarrollar  a desarrollar a desarrollar
			</p>
		</div>
		</div>
		</div>
		</div>
	</div>
	
</div>
</div>



<footer class="footer footer-white">
	<div class="container">
		<a class="footer-brand" href="https://www.creative-tim.com/product/material-kit-pro">algunos enlaces de interés</a>
		<ul class="pull-center">
			
		</ul>
		<ul class="social-buttons float-right">
			<li>
				<a href="https://twitter.com/CreativeTim" target="_blank" class="btn btn-just-icon btn-link btn-twitter">
				<i class="fa fa-twitter"></i>
				</a>
			</li>
			<li>
				<a href="https://www.facebook.com/CreativeTim" target="_blank" class="btn btn-just-icon btn-link btn-dribbble">
				<i class="fa fa-dribbble"></i>
				</a>
			</li>
			<li>
				<a href="https://www.instagram.com/CreativeTimOfficial" target="_blank" class="btn btn-just-icon btn-link btn-instagram">
				<i class="fa fa-instagram"></i>
				</a>
			</li>
		</ul>
	</div>
</footer>



		<!-- Modal (FORMULARIO) -->
		<div class="modal fade " id="ingresoModal" data-bs-backdrop="static"  data-bs-focus="false"  tabindex="-1" aria-labelledby="ingresoModalLabel" aria-hidden="true" completo='no'>
		  <div class="modal-dialog modal-dialog-scrollable modal-xl">
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title" id="ingresoModalLabel">Armemos un espacio de trabajo</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			  </div>
			  <div class="modal-body">
				<form>
					
					<div id='acceso_inic' class="row">
						<div class="col">
							<input type="text" class="form-control"  id="dni"  name="dni"  placeholder="DNI" aria-describedby="dniHelp">
							<div id="dniHelp" class="form-text">
								Queremos guardar tu trabajo y que esté disponible para cuando vuelvas. 
								<br>Si no tenés DNI argentino, contactate con el equipo del GEC para encontrar una solución.
							</div>
						</div>
						<div class="col">
							<label for="preactivo" class="form-label">¿Ya te habías registrado?:  </label>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="preactivo" id="inlineRadio1" value="si"  onchange="cambiapreactivo()">
								<label class="form-check-label" for="inlineRadio1">Si</label>
							</div>
							<div class="form-check form-check-inline">
								  <input class="form-check-input" type="radio" name="preactivo" id="inlineRadio2" value="no"  onchange="cambiapreactivo()">
								  <label class="form-check-label" for="inlineRadio2">No</label>
							</div> 
						</div>	
						
						<div class="col">
							<input type="password" class="form-control"  name="contrasena"  placeholder="contraseña" aria-describedby="contrasenaHelp">
							<div id="contrasenaHelp" class="form-text">Si no te registraste antes ingresá la contraseña que querés usar a partir de ahora. <br>¡No la olvides!</div>
						</div>	
					</div>
					
					

					<div id='registro' class="row" estado="inactivo">
						<div class="col">
							<input type="text" class="form-control"  id="dni"  name="nombre"  placeholder="Nombres" aria-describedby="dniHelp">
							<div id="dniHelp" class="form-text">
								Ingresá el nombre con el que te reconozcas.
							</div>
						</div>
						<div class="col">
							<input type="text" class="form-control"  id=""  name="apellido"  placeholder="Apellidos" aria-describedby="dniHelp">
							<div id="dniHelp" class="form-text">
								Ingresá el Apellido con el que te reconozcas.
							</div>
						</div>	
						
						<div class="col">
							<input type="password" class="form-control"  name="contrasena2"  placeholder="Confirmación de contraseña" aria-describedby="constrasena2Help">
							<div id="constrasena2Help" class="form-text">debe coincidir con al contraseña ingresada y tener al menos 8 caracteres.</div>
						</div>	
					</div>			


					<div id='registro2' class="row" estado="inactivo">
						
						<div class="col">
							<input type="text" class="form-control"  id="dni"  name="mail"  placeholder="Correo electrónico" aria-describedby="mailHelp">
							<div id="dniHelp" class="form-text">
								Dejanos una dirección de correo electrónico para contactarte.<br>
								Solo la vamos a usar para contactarte si resulta indispensable.
							</div>
						</div>				
						
						<div class="col">
							<select class="form-select" aria-label="Pronombre de preferencia" aria-describedby="pronHelp" onchange="cambiapronombre()" name='pronombre'>
							  <option selected>Pronombre de preferencia</option>
							  <option value="El">El</option>
							  <option value="Ella">Ella</option>
							  <option value="Elle">Elle</option>
							  <option value="__otro__">Otro</option>
							  <option value="es indistinto">Es indistinto</option>
							</select>
							<div id="pronHelp" class="form-text">
								Contanos como preferís que nos refiramos a tu persona.
							</div>
							
							<input type="text" class="form-control"  id="pronotro"  name="pronombre_otro"  placeholder="¿Cuál?" aria-describedby="pronotroHelp">
						</div>
						
						<div class="col">
							<select class="form-select" aria-label="Estado de pertenencia" aria-describedby="paisHelp" name="isopais">
								<option selected>Estado de pertenencia</option>
								<option value="AF">Afganistán</option>
								<option value="AL">Albania</option>
								<option value="DE">Alemania</option>
								<option value="AD">Andorra</option>
								<option value="AO">Angola</option>
								<option value="AI">Anguilla</option>
								<option value="AQ">Antártida</option>
								<option value="AG">Antigua y Barbuda</option>
								<option value="AN">Antillas Holandesas</option>
								<option value="SA">Arabia Saudí</option>
								<option value="DZ">Argelia</option>
								<option value="AR" selected='selected'>Argentina</option>
								<option value="AM">Armenia</option>
								<option value="AW">Aruba</option>
								<option value="AU">Australia</option>
								<option value="AT">Austria</option>
								<option value="AZ">Azerbaiyán</option>
								<option value="BS">Bahamas</option>
								<option value="BH">Bahrein</option>
								<option value="BD">Bangladesh</option>
								<option value="BB">Barbados</option>
								<option value="BE">Bélgica</option>
								<option value="BZ">Belice</option>
								<option value="BJ">Benin</option>
								<option value="BM">Bermudas</option>
								<option value="BY">Bielorrusia</option>
								<option value="MM">Birmania</option>
								<option value="BO">Bolivia</option>
								<option value="BA">Bosnia y Herzegovina</option>
								<option value="BW">Botswana</option>
								<option value="BR">Brasil</option>
								<option value="BN">Brunei</option>
								<option value="BG">Bulgaria</option>
								<option value="BF">Burkina Faso</option>
								<option value="BI">Burundi</option>
								<option value="BT">Bután</option>
								<option value="CV">Cabo Verde</option>
								<option value="KH">Camboya</option>
								<option value="CM">Camerún</option>
								<option value="CA">Canadá</option>
								<option value="TD">Chad</option>
								<option value="CL">Chile</option>
								<option value="CN">China</option>
								<option value="CY">Chipre</option>
								<option value="VA">Ciudad del Vaticano (Santa Sede)</option>
								<option value="CO">Colombia</option>
								<option value="KM">Comores</option>
								<option value="CG">Congo</option>
								<option value="CD">Congo, República Democrática del</option>
								<option value="KR">Corea</option>
								<option value="KP">Corea del Norte</option>
								<option value="CI">Costa de Marfíl</option>
								<option value="CR">Costa Rica</option>
								<option value="HR">Croacia (Hrvatska)</option>
								<option value="CU">Cuba</option>
								<option value="DK">Dinamarca</option>
								<option value="DJ">Djibouti</option>
								<option value="DM">Dominica</option>
								<option value="EC">Ecuador</option>
								<option value="EG">Egipto</option>
								<option value="SV">El Salvador</option>
								<option value="AE">Emiratos Árabes Unidos</option>
								<option value="ER">Eritrea</option>
								<option value="SI">Eslovenia</option>
								<option value="ES">España</option>
								<option value="US">Estados Unidos</option>
								<option value="EE">Estonia</option>
								<option value="ET">Etiopía</option>
								<option value="FJ">Fiji</option>
								<option value="PH">Filipinas</option>
								<option value="FI">Finlandia</option>
								<option value="FR">Francia</option>
								<option value="GA">Gabón</option>
								<option value="GM">Gambia</option>
								<option value="GE">Georgia</option>
								<option value="GH">Ghana</option>
								<option value="GI">Gibraltar</option>
								<option value="GD">Granada</option>
								<option value="GR">Grecia</option>
								<option value="GL">Groenlandia</option>
								<option value="GP">Guadalupe</option>
								<option value="GU">Guam</option>
								<option value="GT">Guatemala</option>
								<option value="GY">Guayana</option>
								<option value="GF">Guayana Francesa</option>
								<option value="GN">Guinea</option>
								<option value="GQ">Guinea Ecuatorial</option>
								<option value="GW">Guinea-Bissau</option>
								<option value="HT">Haití</option>
								<option value="HN">Honduras</option>
								<option value="HU">Hungría</option>
								<option value="IN">India</option>
								<option value="ID">Indonesia</option>
								<option value="IQ">Irak</option>
								<option value="IR">Irán</option>
								<option value="IE">Irlanda</option>
								<option value="BV">Isla Bouvet</option>
								<option value="CX">Isla de Christmas</option>
								<option value="IS">Islandia</option>
								<option value="KY">Islas Caimán</option>
								<option value="CK">Islas Cook</option>
								<option value="CC">Islas de Cocos o Keeling</option>
								<option value="FO">Islas Faroe</option>
								<option value="HM">Islas Heard y McDonald</option>
								<option value="FK">Islas Malvinas</option>
								<option value="MP">Islas Marianas del Norte</option>
								<option value="MH">Islas Marshall</option>
								<option value="UM">Islas menores de Estados Unidos</option>
								<option value="PW">Islas Palau</option>
								<option value="SB">Islas Salomón</option>
								<option value="SJ">Islas Svalbard y Jan Mayen</option>
								<option value="TK">Islas Tokelau</option>
								<option value="TC">Islas Turks y Caicos</option>
								<option value="VI">Islas Vírgenes (EEUU)</option>
								<option value="VG">Islas Vírgenes (Reino Unido)</option>
								<option value="WF">Islas Wallis y Futuna</option>
								<option value="IL">Israel</option>
								<option value="IT">Italia</option>
								<option value="JM">Jamaica</option>
								<option value="JP">Japón</option>
								<option value="JO">Jordania</option>
								<option value="KZ">Kazajistán</option>
								<option value="KE">Kenia</option>
								<option value="KG">Kirguizistán</option>
								<option value="KI">Kiribati</option>
								<option value="KW">Kuwait</option>
								<option value="LA">Laos</option>
								<option value="LS">Lesotho</option>
								<option value="LV">Letonia</option>
								<option value="LB">Líbano</option>
								<option value="LR">Liberia</option>
								<option value="LY">Libia</option>
								<option value="LI">Liechtenstein</option>
								<option value="LT">Lituania</option>
								<option value="LU">Luxemburgo</option>
								<option value="MK">Macedonia, Ex-República Yugoslava de</option>
								<option value="MG">Madagascar</option>
								<option value="MY">Malasia</option>
								<option value="MW">Malawi</option>
								<option value="MV">Maldivas</option>
								<option value="ML">Malí</option>
								<option value="MT">Malta</option>
								<option value="MA">Marruecos</option>
								<option value="MQ">Martinica</option>
								<option value="MU">Mauricio</option>
								<option value="MR">Mauritania</option>
								<option value="YT">Mayotte</option>
								<option value="MX">México</option>
								<option value="FM">Micronesia</option>
								<option value="MD">Moldavia</option>
								<option value="MC">Mónaco</option>
								<option value="MN">Mongolia</option>
								<option value="MS">Montserrat</option>
								<option value="MZ">Mozambique</option>
								<option value="NA">Namibia</option>
								<option value="NR">Nauru</option>
								<option value="NP">Nepal</option>
								<option value="NI">Nicaragua</option>
								<option value="NE">Níger</option>
								<option value="NG">Nigeria</option>
								<option value="NU">Niue</option>
								<option value="NF">Norfolk</option>
								<option value="NO">Noruega</option>
								<option value="NC">Nueva Caledonia</option>
								<option value="NZ">Nueva Zelanda</option>
								<option value="OM">Omán</option>
								<option value="NL">Países Bajos</option>
								<option value="PA">Panamá</option>
								<option value="PG">Papúa Nueva Guinea</option>
								<option value="PK">Paquistán</option>
								<option value="PY">Paraguay</option>
								<option value="PE">Perú</option>
								<option value="PN">Pitcairn</option>
								<option value="PF">Polinesia Francesa</option>
								<option value="PL">Polonia</option>
								<option value="PT">Portugal</option>
								<option value="PR">Puerto Rico</option>
								<option value="QA">Qatar</option>
								<option value="UK">Reino Unido</option>
								<option value="CF">República Centroafricana</option>
								<option value="CZ">República Checa</option>
								<option value="ZA">República de Sudáfrica</option>
								<option value="DO">República Dominicana</option>
								<option value="SK">República Eslovaca</option>
								<option value="RE">Reunión</option>
								<option value="RW">Ruanda</option>
								<option value="RO">Rumania</option>
								<option value="RU">Rusia</option>
								<option value="EH">Sahara Occidental</option>
								<option value="KN">Saint Kitts y Nevis</option>
								<option value="WS">Samoa</option>
								<option value="AS">Samoa Americana</option>
								<option value="SM">San Marino</option>
								<option value="VC">San Vicente y Granadinas</option>
								<option value="SH">Santa Helena</option>
								<option value="LC">Santa Lucía</option>
								<option value="ST">Santo Tomé y Príncipe</option>
								<option value="SN">Senegal</option>
								<option value="SC">Seychelles</option>
								<option value="SL">Sierra Leona</option>
								<option value="SG">Singapur</option>
								<option value="SY">Siria</option>
								<option value="SO">Somalia</option>
								<option value="LK">Sri Lanka</option>
								<option value="PM">St Pierre y Miquelon</option>
								<option value="SZ">Suazilandia</option>
								<option value="SD">Sudán</option>
								<option value="SE">Suecia</option>
								<option value="CH">Suiza</option>
								<option value="SR">Surinam</option>
								<option value="TH">Tailandia</option>
								<option value="TW">Taiwán</option>
								<option value="TZ">Tanzania</option>
								<option value="TJ">Tayikistán</option>
								<option value="TF">Territorios franceses del Sur</option>
								<option value="TP">Timor Oriental</option>
								<option value="TG">Togo</option>
								<option value="TO">Tonga</option>
								<option value="TT">Trinidad y Tobago</option>
								<option value="TN">Túnez</option>
								<option value="TM">Turkmenistán</option>
								<option value="TR">Turquía</option>
								<option value="TV">Tuvalu</option>
								<option value="UA">Ucrania</option>
								<option value="UG">Uganda</option>
								<option value="UY">Uruguay</option>
								<option value="UZ">Uzbekistán</option>
								<option value="VU">Vanuatu</option>
								<option value="VE">Venezuela</option>
								<option value="VN">Vietnam</option>
								<option value="YE">Yemen</option>
								<option value="YU">Yugoslavia</option>
								<option value="ZM">Zambia</option>
								<option value="ZW">Zimbabue</option>			  
							</select>
								
						</div>	


						
						
					</div>	
				  
				  <div id='registro3' class="row" estado="inactivo">
					  <div class="col">
							<button type="button" class="btn btn-primary" onclick='generarUsuario()'>Registrarme</button>
						</div>		
					  <div class="col"></div>	
						<div class="col"></div>	
					</div>	
				  <div class="mb-3"></div>
						  


					<div class='row' id='preguntasperfil'>
					</div>			
					
				
					<div id='lugar' class="row">
					
							<h5>Contanos en que provincia y partido vas a trabajar</h5>			  
							<div class="col">
								<select onchange="actualizaFormPerfil()" class="form-select" aria-label="Estado de pertenencia" aria-describedby="paisHelp" name='provincia'>
								  <option value='' selected>Provincia</option>					  
								</select>
							</div>	
							
							<div class="col">
								<select onchange="actualizaFormPerfil()" class="form-select" aria-label="Estado de pertenencia" aria-describedby="paisHelp" name='departamento'>
								  <option value="__falta_provincia__">Departamento: Elegir primero una provincia</option>					  
								</select>
							</div>	
						
					</div>	
				</div>
				</form>


			  <div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick='solicitaCrearEspacio()'>Guardar preferencias para el nuevo espacio de trabajo</button>
			  </div>
			  
			
		  
		  </div>
		</div>


		<div class="modal" id="alertaModal" data-bs-backdrop="static" role="dialog"  tabindex="1" aria-labelledby="ayudaModalLabel"  aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Alerta</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="$('#alertaModal').hide()"></button>
					</div>
				</div>
				<div class="modal-body" name='registrado'>
				  Para crear un nuevo espacio de trabajo primero necesitamos que te registres.<br>
				  Si tenés problemas para registrarte comunicate con el equipo del GEC.
				</div>
				<div class="modal-body" name='completo'>
				  Aún no has completado todas las categorías. <br>
				  Por favor elegí al menos una opción de cada categoría.<br>
				  Si no te safisface la elección, luego la podés cambiar.
				</div>
				<div class="modal-body" name='provincia'>
					Aún no has elegido una provincia. <br>
					Necesitamos que elijas una provincia y luego un departamento. <br>
					Nos ocuparemos de que tu nuevo espacio de trabajo te esté esperando con datos del lugar.		
				</div>		
				<div class="modal-body" name='departamento'>
					Aún no has elegido un departamento.<br>
					Necesitamos que elijas un departamento.<br>
					Nos ocuparemos de que tu nuevo espacio de trabajo te esté esperando con datos del lugar.		
				</div>		
		  
			   <div class="modal-footer">
					<button type="button" class="btn btn-primary"  data-dismiss="modal" onclick="$('#alertaModal').hide()">Entendido</button>
			   </div> 
			</div> 
		</div>
    </div>
  </div>
</div>
</div>

<!-- Modal (Nombre) -->
<div class="modal" id="nombreModal" data-bs-backdrop="static" role="dialog"  tabindex="3"  aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">¿Qué nombre le ponemos a tu nueva área de trabajo?</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="$('#nombreModal').hide();	"></button>
			</div>
		</div>
		<div class="modal-body">
		  <input name='nombreinic' class="form-control"  placeholder="Nombre" type="text">
		</div>
			
  
		<div class="modal-footer">
			<button type="button" class="btn btn-primary"  data-dismiss="modal" onclick="guardarInicio();">Guardar</button>
		</div> 
	</div> 
</div>




<!-- Modal (PROPUESTA) -->
<div class="modal fade " id="configModal" data-bs-backdrop="static"  data-bs-focus="false"  tabindex="-1" aria-labelledby="configModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title" id="ingresoModalLabel">Esta es la configuración inicial que proponemos para tu perfil</h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	  </div>
	  
	  <div class="modal-body">
		<form>
			
			<div class="row grupo" id='apps'>
				<div class="col">
					<h6>Modulos activos</h6>
				</div>		
				<div class="col" name='apps'></div>	
				
			</div>	
			
			<div class="row grupo" id='app_ind'>
				<div class="col">
					<h6>Indicadores Propuestos</h6>
				</div>		
				<div class="col" name='app_ind'></div>	
				
			</div>	
			
			<div class="row grupo" id='app_rele'>
				<div class="col">
					<h6>Actividades de relevamiento Propuestas</h6>
				</div>		
				<div class="col" name='app_rele'></div>					
			</div>	
			
			<div class="row grupo" id='app_publ'>
				<div class="col">
					<h6>Publicaciones de consulta Propuestas</h6>
				</div>		
				<div class="col" name='app_publ'></div>					
			</div>	
			
			<div class="row grupo" id='app_docs'>
				<div class="col">
					<h6>Documentos y carpetas compartidos propuestos</h6>
				</div>		
				<div class="col" name='app_docs'></div>					
			</div>	
			
		</div>
		</form>
	  </div>

	  <div class="modal-footer">
		<button type="button" class="btn btn-primary" onclick='solicitaCrearEspacio()'>Crear este nuevo espacio de trabajo</button>
	  </div>
	  
	
  
  </div>
</div>



<script type="module" src="./external/popper-2.11.5/popper.min.js"  crossorigin="anonymous"></script>
<script src="./external/bootstrap-5.2.0/js/bootstrap.js" crossorigin="anonymous"></script>
<!---<script src="./external/bootstrap-5.2.0/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>-->

 
 

<script type="text/javascript">

	var _Acc = "rele";
	var _IdUsu='<?php echo $_SESSION["geogec"]["usuario"]['id'];?>';
	
	<?php if(!isset($_GET["idr"])){$_GET["idr"]='';} ?>
	var _idCampa = '<?php echo $_GET["idr"];?>';
		
	//Variable de filtro en búsquedas de datos.
    <?php if(!isset($_SESSION['geogec']['usuario']['recorte'])){$_SESSION['geogec']['usuario']['recorte']='';};?>
	_RecorteDeTrabajo=JSON.parse('<?php echo json_encode($_SESSION['geogec']['usuario']['recorte']);?>');
	
	
	//funciones para consultar datos y mostrarlos
	var _Tablas={};
	var _TablasConf={};
	var _SelecTabla='';//define si la consulta de nuevas tablas estará referido al elmento existente de una pabla en particular; 
	var _SelecElemCod=null;//define el código invariable entre versiones de un elemento a consultar (alternativa a _SelElemId);
	var _SelecElemId=null;//define el id de un elemento a consultar (alternativa a _SelElemCod);

	var _IdMarco = getParameterByName('id');
	var _CodMarco = getParameterByName('cod');	
	var _DataUsuaries={};

	var _Features={};
	var _DataCapa=Array();
	
	var _DataFormAgurp = {};
	var _ColumnasNumericasUsadas = [];
	var _ColumnasTextoUsadas = [];
	
	var _DataProvincias = {};
	var _DataListaModelos = {};

	var _InstanciasPopper={};	

	var _DataInicio = {};
	
	var _DataAcciones = {};
</script>

	
	
	
<!--<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./sistema/sis_acciones.js"></script> <!-- funciones de consulta general del sistema: acciones -->


<script type="text/javascript" src="./app_inic/app_inic_js_mapa.js"></script> <!-- carga funciona de gestión de mapa-->
<script type="text/javascript" src="./app_inic/app_inic_js_consultas.js"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_inic/app_inic_js_uploads.js"></script> <!-- carga funciones de operacion del formulario central para la carga de SHP -->
<script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->

<script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.js"></script>
<script type="text/javascript" src="./app_inic/app_inic_js_popper.js"></script> <!-- ayuda visual interactiva -->



<script type="text/javascript" src="./app_inic/app_inic_js_interaccion.js"></script> <!-- carga funciones de operacion de la pagina -->




<script type="text/javascript">






//MOdificaci{on de elementos generados por sript comunes
$('#acceso #botonacceder').attr('onclick', "usuarioModal()");

$('#acceso #botonsalir').attr('onclick', "salirAcceso()");

                  	

//DECLARACION DE EVENTOS

$(document).ready(function(){stickybar();});
	
const myModal = document.getElementById('ingresoModal');
myModal.addEventListener('show.bs.modal', event => {definirEstoadoIngreso();consultarProvincias();consultarIndicadoresModelo()});



$('#ingresoModal [name="provincia"]').on('change', function(){formularDepartamentos();});

$('#acceso_inic input').on('keyup change', function(){checkRegistro();});

$('#temas input').on('change', function(){formularIndicadores();});



$('#sectionsNav .navbar-toggler').on('click',function(){//reconstruye la accion del boton toggle que no pude hacer funcionar con las nusiones nativas de bootstrap
	$('#sectionsNav .navbar-toggler').toggleClass('toggled');
	$('#sectionsNav').toggleClass('nav-open');
	$('#sectionsNav .collapse').toggleClass('show');
});



/*
const ayudaModal = document.getElementById('ayudaModal');
ayudaModal.addEventListener('show.bs.modal', event => {consultarAyuda();consultarIndicadores()});
*/


if(_IdUsu>0){
	consultarIniciosUsuario();		
}

consultarAcciones();


function reingresaGeneral(){
	consultarAcciones();
	
	
}

	
	
	

</script>

</body>
</html>
