
<!--- ./usuarios/usu_acceso.php--->

<script>
	$('head').append('<link rel="stylesheet" type="text/css" href="./sis_usuarios/usuarios.css?t=<?php echo time();?>">');
	$('head').append('<link rel="stylesheet" type="text/css" href="./sis_usuarios/usuariosB.css?t=<?php echo time();?>">');
	$('head').append('<link rel="stylesheet" type="text/css" href="./sis_usuarios/usuariosF.css?t=<?php echo time();?>">');
	$('head').append('<link rel="stylesheet" type="text/css" href="./sis_usuarios/usuariosC.css?t=<?php echo time();?>">');
	$('head').append('<link rel="stylesheet" type="text/css" href="./sis_usuarios/configC.css?t=<?php echo time();?>">');
	
</script>	

<div id='navegador'>
	<div id='home'><a href='index.php'><img src='./comun_img/home.png'></a></div>
	<div id='buscadatos'>
		<div id='cargandogif' cargando='no'><img src='./comun_img/cargando.gif'></div>
		<input type='text' placeholder='buscar...' id='inputbuscadatos' name="inputbuscadatos" onkeyup='buscadatos(this,event)'>
	</div>
	<div id='configuracion'>
		<div id='cargandogif' cargando='no'><img src='./comun_img/cargando.gif'></div>
		<a id='botonconfig' onclick='config()'><img src='./comun_img/configurar.png'></a>
		<a id='botonusuario' onclick='usuarios()'><img src='./comun_img/usu.png'></a>
	</div>	
	<div id='acceso'>
		<a id='hola' onclick='formUsuario("edita")'></a>
		<a id='botonacceder' onclick='formUsuario("accede")'><span class='auxtx'>acceder</span><img alt='acceder' src='./comun_img/login.png'></a>
		<a id='botonsalir' onclick='salir()'>salir</a>
	</div>	
</div>

<div id='menubuscado'>
	<h1>Resultados:</h1>
	<a id='cerr' onclick='cerrarVentana(this.parentNode);'>cerrar</a>
	<div id='contenido'>
	<div id='listado'></div>
	</div>
</div>	


<div id='muestraresultado'>
	<a id='cerr' onclick='cerrarVentana(this.parentNode);'>cerrar</a>
	<a onclick="irElementoTarjeta(this)" id='irobjeto'>ir</a>
	<h1 id='nombre'></h1>
	<p>por: <span id='autoria'></span></p>
	<p id='acceso'></p>
	
	<p id='tipocontenido'></p>
	<p id='descripcion'></p>	
	<h3>Dentro del proyecto</h3>	
	<p><span id='marcoacademico'></span>: <span id='marcoacademicotx'></span></p>
	<h3>Otros usuarios parcicipantes en el mismo proyecto</h3>
	<p id='referentes'></p>	
</div>		
	
<div id='formacceso'>
	<h1>Bienvenide</h1>
	<a id='cerr' onclick='cerrarVentana(this.parentNode);'>cerrar</a>
	<div id='dataacceso'>
		<p>log </p><input name='log' autocomplete="off" id="inputUsuarioLogNombre" onkeypress="handleKeyPressUsuario(event)">
		<span class='contrasenas'>
		<p>Contraseña </p><input type='password' name='password' autocomplete='off' id="inputUsuarioLogPass" onkeypress="handleKeyPressPass(event)">
		</span>
		<input id='acceder' type="button" onclick='acceder(this);' value="acceder">
	</div>
	<div id='dataregistro'>		
		<input name='id' type='hidden' autocomplete='off'>
		<p>Nombre</p><input name='nombre' autocomplete='off'>
		<a id='botoncambiocontrasena' onclick='activarCambioContrasena()'>Cambiar contraseña</a>
		<input name='cambiocontrasena' type='hidden' autocomplete='off' value='no'>
		<span class='contrasenas'>
		<p>Confirmar Contraseña</p><input type='password' name='password2' autocomplete='off'>
		</span>
		<p>Apellido</p><input name='apellido' autocomplete='off'>
		<p>Mail de referencia</p><input name='mail' autocomplete='off'>
		
		<p>Pronombre de preferencia</p><select name='pronombre' onchange='activaOtroPronombre()'>
			<option value=''>- elegir -</option>
			<option value='El'>El</option>
			<option value='Ella'>Ella</option>
			<option value='Elle'>Elle</option>
			<option value='es indistinto'>es indistinto</option>
			<option value='__otro__'>otro</option>
		</select>
		<span id='parrafo_pronombre' estado='inactivo'>
			<p>cual pronombre preferís:</p><input name='pronombre_otro' autocomplete='off'>
		</span>
		
		
		<p>Estado de pertenencia:</p><select name='isopais'>
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
		
		
		<p>(DNI) Número de Identificación para dicho estado:</p><input name='numeroid' autocomplete='off'>
		
		<a id='editar' onclick='actualizarUsu(this);'>actualizar datos</a>
		<a id='registrarse' onclick='registrar(this);'>registrarse</a>
	</div>
	<a id='ampliar' onclick='formUsuario("registra");'>registrarse como nuevo usuario</a>			
</div>	


<div id='formusuarios'>
	<h1>Usuarios</h1>
	<a id='cerr' onclick='cerrarVentana(this.parentNode)'>cerrar</a>
	<div id='permisos'>
		<h2>Accesos permitidos a la plataforma</h2>
		
		<form id='crearpermiso' onsubmit='crearPermiso(event,this)'>
		
		<label>Buscar: <input name='busqueda' onkeyup="actualizarBusquedaUsuario()"></label>
		<div id='encabezado'><!---
			--><div>Usuario</div><!---
			--><div>a Tabla</div><!---
			--><div>a Elemento</div><!---
			--><span id='acc'>a la accion</span><!---
			--><span id='niv'>con nivel</span><!---
		--></div>
		<div id='datos'>
			<select id='usu'>
				<option>-elegir-</option>
			</select><!---
			--><select id='tabla' onchange='actualizarElementos(this.value)'>
				<option>-elegir-</option>
				<option value='general' confirm='Atención, el valor "general" para la tabla de acceso: significa que se le está brindando acceso a toda la plataforma'>¡general!</option>
			</select><!---
			--><select id='elemento'>
				<option>-elegir-</option>
				<option value='general' confirm='Atención, el valor "general" para el elemento de acceso: significa que se le está brindando acceso a todo elemento accesible desde esta tabla'>¡general!</option>
			</select><!---
			--><select id='accion'>
				<option>-elegir-</option>
				<option value='general'>¡general!</option>
				<option value='app_docs'>gestión de la documentación</option>
				<option value='app_plan'>gestión de la planificación</option>
			</select><!---
			--><select id='nivel'>
				<option>-elegir-</option>
				<option value='3'>¡3! (administrador)</option>
				<option value='2'> 2  (investigador)</option>
				<option value='1'> 1  (auditor)</option>
			</select>
			<input type='submit' value='crear'>
		</div>	
		</form>
		
		<form id='eliminarpermiso'>
		</form>
		<p>en desarrollo</p>
	</div>
	<div id='perfil'>
		<h2>Configuración de tu perfil de usuario</h2>
		<p>en desarrollo</p>
	</div>		
</div>

<div id='formconfig'>
	<h1>Configuración</h1>
	<a id='cerr' onclick='cerrarVentana(this.parentNode)'>cerrar</a>
	<a id='btncrearM' onclick='formCrearMarco()'>Crear Proyecto en desarrollo</a>
	<a id='btmmodifM' onclick='formModifMarco()'>Modificar proyecto activo <span id='projnom'></span></a>
	<h2>Configuración de la plataforma</h2>

	<form id='crearMarco' onsubmit='crearMarco(event,this)'>
		<h3>Crear Marco Académico (espacio de trabajo)</h3>
		<p>Nombre Corto:</p>
		<input type='text' name='nombre' value=''>
		
		<p>Nombre Oficial:</p>
		<input type='text' name='nombre_oficial' value=''>
		
		<p>Código: (sin espacios ni caracteres especiales A-Z 0-9 _ . - )</p>
		<input type='text' name='codigo' value=''>
		
		<p>Descripción:</p>
		<textarea name='descripcion'></textarea>
		
		<p>Tabla: </p>
		<select name='tabla'>
			<option value='est_02_marcoacademico' >est_02_marcoacademico</option>
		</select>
		
		<p>Geometría (WKT espg:3857): <input type='button' value='dibujar' onclick='dibujarGeometriaMarco()'></p>
		<textarea name='geomtx'></textarea>
		
		<br>
		<input type='submit' value='crear Marco Académico'>

	</form>

	<form id='editarMarco' onsubmit='crearMarco(event,this)'>
		<h3>Modificar Proyecto</h3>
		<p>Nombre Corto:</p>
		<input type='text' name='nombre' value=''>
		<a class='guarda' for='nombre' onclick='guardacampoMarco(this)'>
			<img src='./comun_img/disquito.png'>
			<img id='listo' src='./comun_img/check-sinborde.png'>
			<img id='cargando' src='./comun_img/cargando.gif'>
		</a>
		
		<p>Nombre Oficial:</p>
		<input type='text' name='nombre_oficial' value=''>
		<a class='guarda' for='nombre_oficial' onclick='guardacampoMarco(this)'>
			<img src='./comun_img/disquito.png'>
			<img id='listo' src='./comun_img/check-sinborde.png'>
			<img id='cargando' src='./comun_img/cargando.gif'>
		</a>
		
		<p>Código: (sin espacios ni caracteres especiales A-Z 0-9 _ . - )</p>
		<span type='text' name='codigo'></span>
			
		<p>Descripción:</p>
		<textarea name='descripcion'></textarea>
		<a class='guarda' for='descripcion' onclick='guardacampoMarco(this)'>
			<img src='./comun_img/disquito.png'>
			<img id='listo' src='./comun_img/check-sinborde.png'>
			<img id='cargando' src='./comun_img/cargando.gif'>
		</a>
		
		<p>Geometría (WKT espg:3857):</p>
		<textarea name='geomtx'></textarea>
		<a class='guarda' for='geomtx' onclick='guardacampoMarco(this)'>
			<img src='./comun_img/disquito.png'>
			<img id='listo' src='./comun_img/check-sinborde.png'>
			<img id='cargando' src='./comun_img/cargando.gif'>
		</a>
		<div id='modulos'>
			<p>Modulos activos:</p>
			<a class='guarda' for='geomtx' onclick='guardaaccionesMarco()'>
				<img src='./comun_img/disquito.png'>
				<img id='listo' src='./comun_img/check-sinborde.png'>
				<img id='cargando' src='./comun_img/cargando.gif'>
			</a>
			<div id='listaaccionesactivas'></div>
		</div>
		
		<div class='componentecarga' id='componente_centros'>
			<p>Centros de interés:</p>
			
			
			<a class="guarda" for="sitios" onclick="guardaSitiosMarco()">
				<img src="./comun_img/disquito.png">
				<img id="listo" src="./comun_img/check-sinborde.png">
				<img id="cargando" src="./comun_img/cargando.gif">
			</a>
			
			<div class='punto' idp='0'>
				<span>Centro del Marco Académico</span>
				<input name='geotx'>
				<input name='nombre' type='hidden'>
				<a onclick='atcivarCoordenadasParaInput("0")'>tomar punto</a>
			</div>
						
			<a onclick='crearSitioDeInteres()'><img src='./comun_img/agregar.png'>nuevo punto de interés</a>	
			<div id='listado_puntos'></div>
			<div class='modelo punto'>
				<input name='nombre'>
				<input name='geotx'>
				<a onclick='atcivarCoordenadasParaInput(this.parentNode.getAttribute("idp"));'>tomar punto</a>
			</div>
			
		</div>
		
		
	</form>	
</div>
	
<?php

	ini_set('display_errors',1);
	include_once("./comun_general/fechas.php");
	include_once("./comun_general/cadenas.php");
	include_once("./comun_general/pgqonect.php");

	include_once("./sis_usuarios/usu_validacion.php");
	$Usu= validarUsuario();
	
	$dataU=array();
	if(isset($_SESSION[$CU]) && isset($_SESSION[$CU]["usuario"])){
		foreach($_SESSION[$CU]["usuario"] as $k => $v){
			if(is_array($v)){continue;}
			$dataU[$k]=utf8_encode($v);
		}
	}
	
	
	if(isset($Usu['marcos'])){
		foreach($Usu['marcos'] as $mcod => $mv){
			foreach($mv as $k => $v){
				if(is_array($v)){continue;}
				$dataU['permisos']['marcos'][$mcod][$k]=$v;
			} 
		}
	}
	
	unset($dataU['pass']);	
	$jsondatau=json_encode($dataU);
	
?>	

<script type="text/javascript" src="./app_index/index_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones consulta de datos-->

<script>
	var _CodMarco = getParameterByName('cod');
	
	var _UsuarioAcceso= $.parseJSON('<?php echo json_encode($Usu['acc']);?>');
	var _Consultando='no';
	
	var _Busqueda={
		'capa':{},
		'ind':{},
		'rele':{},
		'game':{},
		'publ':{}
		};

	function activarConsultando(){
		_Consultando='si';
		document.querySelector('#cargandogif').setAttribute('cargando',_Consultando);
	}
	
	function desactivarConsultando(){
		_Consultando='no';
		document.querySelector('#cargandogif').setAttribute('cargando',_Consultando);
	}
	
	function controlConsultando(){
		if(_Consultando=='si'){
			return true;
		}else{
			return false;
		}
	}
	
	if(_UsuarioAcceso.general.general.general<3){
		_botn=document.querySelector('#configuracion');
		_botn.style.display='none';	
	}

	if('<?php echo $jsondatau?>'!=''){
		var _UsuarioA= $.parseJSON('<?php echo json_encode($dataU);?>');
	}
	
	if(_UsuarioA==null){
		_UsuarioA={'nombre':'Anónimo','apellido':''}
	}

	function cerrarVentana(_this){
		_this.removeAttribute('estado');
	}

</script>

<script type="text/javascript" src="./sis_usuarios/usu_acceso_js_acceder.js?t=<?php echo time();?>"></script> <!-- carga funciones para acceder usuarios-->
<script type="text/javascript" src="./sis_usuarios/usu_acceso_js_config.js?t=<?php echo time();?>"></script> <!-- carga funciones consulta de configuración de usuarios-->
<script type="text/javascript" src="./sis_usuarios/usu_acceso_js_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones consulta de informacion de usuarios-->
<script type="text/javascript" src="./sis_usuarios/usu_acceso_js_marcos.js?t=<?php echo time();?>"></script> <!-- carga funciones consulta de marcos academicos-->
<script type="text/javascript" src="./sis_usuarios/usu_acceso_js_buscadatos.js?t=<?php echo time();?>"></script> <!-- carga funciones busqueda de datos-->
<script type="text/javascript" src="./sis_usuarios/usu_acceso_js_interaccion.js?t=<?php echo time();?>"></script> <!-- carga funciones de interaccon. cierr de formulario-->

<script>
	cargarusuario();	
</script>	
