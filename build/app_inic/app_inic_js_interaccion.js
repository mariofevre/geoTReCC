
function cambiapronombre(){
	
	_val=document.querySelector('#ingresoModal [name="pronombre"]').value;
	_row_reg=document.querySelector('#pronotro');
	
	if(_val=='__otro__'){		
		_row_reg.setAttribute('estado','activo');
	} else {
		_row_reg.setAttribute('estado','inactivo');
	}
}


function cambiapreactivo(){	
	_val=document.querySelector('#ingresoModal [name="preactivo"]:checked').value;
	_row_reg=document.querySelector('#ingresoModal #registro');
	_row_reg2=document.querySelector('#ingresoModal #registro2');
	_row_reg3=document.querySelector('#ingresoModal #registro3');
	
	if(_val=='no'){		
		_row_reg.setAttribute('estado','activo');
		_row_reg2.setAttribute('estado','activo');
		_row_reg3.setAttribute('estado','activo');
	} else {	
		_row_reg.setAttribute('estado','inactivo');
		_row_reg2.setAttribute('estado','inactivo');
		_row_reg3.setAttribute('estado','inactivo');
	}		
}
	

function checkRegistro(){
	
	_dni=document.querySelector('#ingresoModal [name="dni"]').value;
	_in=document.querySelector('#ingresoModal [name="preactivo"]:checked');
	if(_in==null){
		_preactivo="no";
	}else{
		_preactivo=document.querySelector('#ingresoModal [name="preactivo"]:checked').value;
	}
	
	_pass=document.querySelector('#ingresoModal [name="contrasena"]').value;
	
	if(
		_dni.length>6
		&&
		_preactivo=='si'
		&&
		_pass.length>=4		
	){
		consultarAcceso();
	}
	
}
	
		


	
function consultarAcceso(){

	var parametros = {
		'dni': document.querySelector('input[name="dni"]').value,
		'password': document.querySelector('input[name="contrasena"]').value
	};
	
	$.ajax({
		data:  parametros,
		url:   './usuarios/usu_acceso_ajax.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='caduca'){location.reload();}
			if(_res.res!='exito'){return;}
				
			if(_res.data.permisos !=undefined){				
				_UsuarioA= _res.data;

				document.querySelector("#ingresoModal").setAttribute('registrado','si');
				consultarIniciosUsuario();
				
				if(document.querySelector("#ingresoModal").getAttribute('soloingreso')=='si'){
					$('#ingresoModal').modal("hide");	
				}	
			}		
		}
	});		
}


function salirAcceso(){		
	
	var parametros = {};
	
	$.ajax({
		data:  parametros,
		url:   './usuarios/usu_salir_ajax.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='caduca'){location.reload();}
			if(_res.res!='exito'){return;}
			
			_UsuarioA={};
			_IdUsu='';
			_DataInicio={};
			
			formularInicio();
			ocument.querySelector("#ingresoModal").setAttribute('registrado','no');
		}
	});
}




function stickybar(){
	$(window).scroll(function(){
		var barra = $(window).scrollTop();
		var posicion =  (barra * .2);
		
		$('.page-header').css({
			'transform': 'translate3d(0px, '+ posicion + 'px, 0px)'
		});
		
		var navbar = document.getElementById("sectionsNav");
		var sticky = navbar.offsetTop;// Get the offset position of the navbar
		
		console.log(window.pageYOffset +'/'+ sticky);
		if (window.pageYOffset > sticky) {
			navbar.classList.add("fixed-top")
			navbar.classList.remove("navbar-transparent");
		} else {
			  
			navbar.classList.add("navbar-transparent")
			navbar.classList.remove("fixed-top");
		  }	 
	});
}


	
function consultarProvincias(){


	var parametros = {
		
	};
	
	$.ajax({
		data:  parametros,
		url:   './app_inic/app_inic_consultar_partidos_provincias.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='caduca'){location.reload();}
			if(_res.res!='exito'){return;}
				
			_DataProvincias=_res.data;
			
			_sel=document.querySelector('#lugar [name="provincia"]');
			_sel.innerHTML='<option value="" selected="">Provincia</option>';
			for(_np in _DataProvincias.provinciasOrden){
				_idp=_DataProvincias.provinciasOrden[_np];
				_datp=_DataProvincias.provincias[_idp];
				
				_op=document.createElement('option');
				_op.value=_datp.link;
				_op.innerHTML=_datp.nombre;
				_sel.appendChild(_op);
			}
		}
	});		
}


function formularDepartamentos(){
	_seld=document.querySelector('#lugar [name="departamento"]');
	_selp=document.querySelector('#lugar [name="provincia"]');
	if(_selp.value==''){
		_seld.innerHTML='<option value="" selected="">Elegir primero una provincia</option>';
		return;
	}
	_seld.innerHTML='<option value="" selected="">Departamento</option>';
	_idp=_selp.value;
	for(_nd in _DataProvincias.provincias[_idp].departamentosOrden){
		_idd=_DataProvincias.provincias[_idp].departamentosOrden[_nd];
		_datd=_DataProvincias.provincias[_idp].departamentos[_idd];
		_op=document.createElement('option');
		_op.value=_datd.link;
		_op.innerHTML=_datd.nombre;
		_seld.appendChild(_op);
	}
}


function consultarIndicadoresModelo(){
	
	var parametros = {
		
	};	
	$.ajax({
		data:  parametros,
		url:   './app_ind/app_ind_consultar_listado_modelos.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='caduca'){location.reload();}
			if(_res.res!='exito'){return;}
				
			_DataListaModelos=_res.data;
			
			formularPreguntasModelos();
			
		}
	});		
}



function formularPreguntasModelos(){
	_cont=document.querySelector('#ingresoModal form #preguntasperfil');
	_cont.innerHTML='';
	for(_tn in _DataListaModelos.tagsTiposOrden){
			
		_idti=_DataListaModelos.tagsTiposOrden[_tn];
		_datti=_DataListaModelos.tagsTipos[_idti];
		_tipo=_datti.tipo;
				
		_d=document.createElement('div');
		_d.setAttribute('class','row');
		_d.setAttribute('id','tipo_'+_idti);
		_cont.appendChild(_d);
		
		_h=document.createElement('h5');
		_h.innerHTML=_datti.consigna;
		_d.appendChild(_h);
		
		_c=document.createElement('div');
		_c.setAttribute('class','col');
		_d.appendChild(_c);
			
		for(_nt in _DataListaModelos.tagsOrden[_tipo]){
			_idt=_DataListaModelos.tagsOrden[_tipo][_nt];
			_dattag=_DataListaModelos.tags[_idt];
			_d=document.createElement('div');
			_d.setAttribute('class','form-check form-switc');
			_c.appendChild(_d);
				
			_l=document.createElement('input');
			_d.appendChild(_l);
			_l.setAttribute('class',"form-check-input");
			_l.setAttribute('role',"switch");
			_l.setAttribute('id',_idt);
			_l.setAttribute('name',_idt);
			_l.setAttribute('data-toggle',"tooltip");
			_l.setAttribute('data-placemente',"top");
			_l.setAttribute('onchange',"actualizaFormPerfil()");
			_l.title=_dattag.ayuda;
			
			if(_datti.condicion=='unico'){
				_type='radio'
				_name=_tipo;
				_l.setAttribute('name',_name);
				_val=_idt;
			}else{
				_type='checkbox'
				_name=_idt;
			}
			_l.setAttribute('type',_type);

			_l=document.createElement('label');
			_d.appendChild(_l);
			_l.setAttribute('class',"form-check-label");			
			_l.setAttribute('for',_idt);
			_l.innerHTML=_dattag.des_formulario;
			
			if(_dattag.ayuda!=null){
				_a=document.createElement('button');
				_d.appendChild(_a);						
				_a.setAttribute("class","tag_"+_idt);
				_a.setAttribute("id","button"+"_tag_"+_idt);
				
				_a.setAttribute("role","button");	
				_a.setAttribute("onmouseover","show(this)");
				_a.setAttribute("onmouseout","hide(this)");			
				_a.setAttribute("aria-describedby","tooltip"+"_tag_"+_idt);
				_a.innerHTML="?";

				_t=document.createElement('div');
				_d.appendChild(_t);	
				_t.setAttribute("id","tooltip"+"_tag_"+_idt);
				_t.setAttribute("role","tooltip");	
				_t.innerHTML=_dattag.ayuda+'<div id="arrow" data-popper-arrow>';
				popperInstance = Popper.createPopper(_a, _t, {placement: 'right'});
				_InstanciasPopper["tag_"+_idt]={'id':"tag_"+_idt,'instancia':null};
				_InstanciasPopper["tag_"+_idt].instancia = Popper.createPopper(_a, _t, {placement: 'right'});	
			}
		}	
	}
}

function actualizaFormPerfil(){

	_form=document.querySelector('#ingresoModal form');

	_tipoisncomletos=0;
	for(_ntt in _DataListaModelos.tagsTipos){
		
		_tipoinps=_form.querySelectorAll('#ingresoModal form #tipo_'+_ntt+' input');
		
		_tipo_tagsmarcados=0;
		for(_ni in _tipoinps){
			if(typeof _tipoinps[_ni] !='object'){continue;}
			
			if(_tipoinps[_ni].checked){
				_tipo_tagsmarcados++;
			}
		}
		
		console.log(_ntt+': '+_tipo_tagsmarcados);
		
		if(_tipo_tagsmarcados==0){
			_tipoisncomletos++;
			_form.querySelector('#ingresoModal form #tipo_'+_ntt).setAttribute('completo','no');
		}else{
			_form.querySelector('#ingresoModal form #tipo_'+_ntt).setAttribute('completo','si');
		}
	}

	if(_tipoisncomletos==0){
		console.log('completo');
		document.querySelector('#ingresoModal').setAttribute('completo','si');
	}else{
		console.log('incompleto');
		document.querySelector('#ingresoModal').setAttribute('completo','no');
	}
	
	if(_form.querySelector('[name="provincia"]').value==''){
		document.querySelector('#ingresoModal').setAttribute('completo','no');
		console.log('falta prv');
	}
	
	if(_form.querySelector('[name="departamento"]').value==''||_form.querySelector('[name="departamento"]').value=='__falta_provincia__'){
		console.log('falta dtp');
		document.querySelector('#ingresoModal').setAttribute('completo','no');
	}
}

function usuarioModal(){
	
	$('#ingresoModal').modal("show");
	$('#ingresoModal').attr('soloingreso','si');
	
}

function definirEstoadoIngreso(){
	if(_IdUsu>0){
		document.getElementById('ingresoModal').setAttribute('registrado','si');
	}
}


function solicitaCrearEspacio(){
	_form=document.querySelector('#ingresoModal');
	_alertamodal=document.querySelector('#alertaModal');
	//_alertamodal.classList.add('show');
	if(_form.getAttribute('registrado')!='si'){
		$('#alertaModal').show();
		_alertamodal.setAttribute('modo','registrado');
		return;
	}
	if(_form.getAttribute('completo')!='si'){
		$('#alertaModal').show();
		_alertamodal.setAttribute('modo','completo');
		return;
	}	
	if(_form.querySelector('[name="provincia"]').value==''){
		$('#alertaModal').show();
		_alertamodal.setAttribute('modo','provincia');
		return;
	}
	if(_form.querySelector('[name="departamento"]').value==''){
		$('#alertaModal').show();
		_alertamodal.setAttribute('modo','departamento');
		return;
	}
	
	preguntarNombreEspacio();
	
}

function preguntarNombreEspacio(){
	$('#nombreModal').show();	
}


function guardarInicio(){
	_form=document.querySelector('#preguntasperfil');
	
	_parametros={
		'provincia':document.querySelector('#ingresoModal [name="provincia"]').value,
		'departamento':document.querySelector('#ingresoModal [name="departamento"]').value,
		'nombreinic':document.querySelector('#nombreModal [name="nombreinic"]').value,
		'tagsmarcados':[]
	}
	
	_checks=_form.querySelectorAll('input:checked');
	for(_nc in _checks){
		if(typeof _checks[_nc] != 'object'){continue;}
		_parametros['tagsmarcados'].push(_checks[_nc].getAttribute('id'));
	}
	
	$.ajax({
		data:  _parametros,
		url:   './app_inic/app_inic_ed_crear_inic.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='caduca'){location.reload();}
			if(_res.res!='exito'){return;}
			
			$('#nombreModal').modal('hide');
			$('#ingresoModal').modal('hide');
			
			consultarInicio(_res.data.nid);
			
		}
	});			
}


function abrirProyecto(_codproy){
	_url="./index.php?est=est_02_marcoacademico&cod="+_codproy;
	location.assign(_url);	
}


function consultarInicio(_idinic){
	
	_parametros={
		'idinic':_idinic
	}	
	
	$.ajax({
		data:  _parametros,
		url:   './app_inic/app_inic_consultar_propuesta.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='caduca'){location.reload();}
			if(_res.res!='exito'){return;}
				
			_DataInicio=_res.data;
			
			formularPropuesta();
		}
	});			
}


function consultarAcciones(){
	
	_parametros={
		'tabla':'no'
	}	
	
	$.ajax({
		data:  _parametros,
		url:   './sistema/sis_consulta_acciones.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='caduca'){location.reload();}
			if(_res.res!='exito'){return;}
			
			_DataAcciones=_res.data;
			console.log(_DataAcciones);
			
		}
	});			
}





function formularPropuesta(){
	
	$('#configModal').modal('show');
	_cont=document.querySelector('#configModal [name="app_ind"]');
	
	document.querySelector('#configModal #app_ind').setAttribute('estado','vacio');
	for(_idm in _DataInicio.indicadoresmodelo_propuestos){
		_datmod=_DataInicio.indicadoresmodelo[_idm];
		_div=document.createElement('div');
		_cont.appendChild(_div);
		_div.setAttribute('class',"form-check form-check-inline");
		
		_in=document.createElement('input');
		_div.appendChild(_in);
		_in.setAttribute('class',"form-check-input");
		_in.setAttribute('type',"radio");
		_in.setAttribute('value',"no");
		_in.setAttribute('id',"modelo_"+_idm);
		
		_la=document.createElement('label');
		_div.appendChild(_la);
		_la.setAttribute('class',"form-check-label");
		_la.setAttribute('for',"modelo_"+_idm);
		_la.innerHTML=_datmod.nombre;
		
		/*
		_a=document.createElement('button');
		_div.appendChild(_a);						
		_a.setAttribute("class","tag_"+_idt);
		_a.setAttribute("id","button"+"_tag_"+_idt);
		
		_a.setAttribute("role","button");	
		_a.setAttribute("onmouseover","show(this)");
		_a.setAttribute("onmouseout","hide(this)");			
		_a.setAttribute("aria-describedby","tooltip"+"_tag_"+_idt);
		_a.innerHTML="?";

		_t=document.createElement('div');
		_div.appendChild(_a);						
		_t.setAttribute("id","tooltip"+"_tag_"+_idt);
		_t.setAttribute("role","tooltip");	
		
		_t.innerHTML=_datmod.resumen;
		_t.innerHTML+='<br>';
		_t.innerHTML=_datmod.unidad_medida;
		_t.innerHTML+='<br>';
		_t.innerHTML=_datmod.relevancia_acc;
		_t.innerHTML+='<br>';
		_t.innerHTML=_datmod.ejemplo;
		_t.innerHTML+='<br>';
		_t.innerHTML=_datmod.datos_input;
		_t.innerHTML+='<br>';
		_t.innerHTML=_datmod.fuentes_input;
		_t.innerHTML+='<br>';
		_t.innerHTML=_datmod.valoracion;
		_t.innerHTML+='<br>';
		_t.innerHTML+='<h6>requrimientos</h6>';
		
		_ul=document.createElement('ul');
		_t.appendChild(_ul);
		
		for(_idreq in _datmod.requerimientos){
			_datreq=_datmod.requerimientos[_idreq];
			_li=document.createElement('li');
			_ul.appendChild(_li);
			_li.inneHTML= _datreq.descripcion + 'en módulo: '+_datreq.app;
		}			
		
		_t.innerHTML+='<div id="arrow" data-popper-arrow>';
		popperInstance = Popper.createPopper(_a, _t, {placement: 'right'});
		_InstanciasPopper["tag_"+_idt]={'id':"tag_"+_idt,'instancia':null};
		_InstanciasPopper["tag_"+_idt].instancia = Popper.createPopper(_a, _t, {placement: 'right'});		
		* */
	}
	
	document.querySelector('#configModal #apps').setAttribute('estado','vacio');
		
	_lista=document.querySelector('#configModal #apps [name="apps"]');
	_lista.innerHTML='';
	
	for(_accnom in _DataAcciones.acciones){	
		_accndata=_DataAcciones.acciones[_accnom];	
		_li=document.createElement('input');
		_lista.appendChild(_li);
		_li.setAttribute('type','checkbox');
		_li.setAttribute('name',_accnom);
		if(_accndata.activo==1){
			_li.checked=true;
			document.querySelector('#configModal #apps').setAttribute('estado','lleno');
		}
		_sp=document.createElement('span');
		_lista.appendChild(_sp);		
		_sp.setAttribute('class','img');
		_la=document.createElement('img');
		_la.setAttribute('src','./comun_img/'+_accnom+'.png');
		_la.setAttribute('alt',_accnom);
		_la.setAttribute('title',_accndata.resumen);
		_sp.appendChild(_la);
		
		_la=document.createElement('span');
		_la.innerHTML=_accndata.resumen;
		_lista.appendChild(_la);
		
		_br=document.createElement('br');
		_lista.appendChild(_br);
	}
}




function consultarIniciosUsuario(){
	
	_parametros={
		'idusu':_IdUsu
	}
	
	$.ajax({
		data:  _parametros,
		url:   './app_inic/app_inic_consultar_inicios.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='caduca'){location.reload();}
			if(_res.res!='exito'){return;}
				
			_DataInicio=_res.data;			
			formularInicio();
		}
	});			
	
	
}
function formularInicio(){
	
	_cont=document.querySelector("#sectionsNav #item_inic");
	
	_ops=_cont.querySelectorAll('option');
	_cont.setAttribute('estado','vacio');
	_contador=_cont.querySelector("#contador");
	
	for(_no in _ops){
		if(typeof _ops[_no] != 'object'){continue;}
		if(_ops[_no].getAttribute('class')=='aguante'){continue;}
		_cont.querySelector("select").removeChild(_ops[_no]);
	}
	
	
	_c=0;
	for(_no in _DataInicio.iniciosOrden){
		_idi=_DataInicio.iniciosOrden[_no];
		_dati=_DataInicio.inicios[_idi];
		
		_cont.setAttribute('estado','lleno');		
		_c++;
		console.log('p');
		_op=document.createElement('option');
		console.log(_op);
		_cont.querySelector("select").appendChild(_op);
		_op.value=_idi;
		_op.innerHTML=_dati.nombre;
		
		_sp=document.createElement('span');
		_sp=document.createElement('span');
		
		_date = new Date(_dati.zz_auto_crea_fechau*1000);
		_sp.innerHTML=_date.toLocaleDateString('en-GB');
	}
	_contador.innerHTML=_c;
	
	
	_cont=document.querySelector("#sectionsNav #item_proy");
	_ops=_cont.querySelectorAll('option');
	_cont.setAttribute('estado','vacio');
	_contador=_cont.querySelector("#contador");	
	for(_no in _ops){
		if(typeof _ops[_no] != 'object'){continue;}
		if(_ops[_no].getAttribute('class')=='aguante'){continue;}
		_cont.querySelector("select").removeChild(_ops[_no]);
	}
	_c=0;
	if(_UsuarioA.permisos!=undefined){
		for(_cod in _UsuarioA.permisos.marcos){
			_mdat=_UsuarioA.permisos.marcos[_cod];
			if(_mdat.zz_accesolibre!=1 && _mdat.maxacc=='0'){continue;}
			
			_cont.setAttribute('estado','lleno');		
			_c++;
			_op=document.createElement('option');
			_cont.querySelector("select").appendChild(_op);
			_op.value=_cod;
			_op.innerHTML=_mdat.nombre;
			
		}
	}
	_contador.innerHTML=_c;
}





function generarUsuario(){
	
	
	if(!validarInputsRegistrarUsuario()){
		return;
	}
	_form=document.querySelector('#ingresoModal');
	
	
	
	_pn=document.querySelector('#registro2 [name="pronombre"]').value;
	if(_pn =='__otro__'){
		_pn=document.querySelector('#registro2 [name="pronombre_otro"]').value;
	}
	
	_parametros={
		'log':_form.querySelector('#acceso_inic [name="dni"]').value,
		'numeroid':_form.querySelector('#acceso_inic [name="dni"]').value,
		'password':_form.querySelector('#acceso_inic [name="contrasena"]').value,
		
		'nombre':_form.querySelector('#registro [name="nombre"]').value,
		'apellido':_form.querySelector('#registro [name="apellido"]').value,
		'password2':_form.querySelector('#registro [name="contrasena2"]').value,
		
		'email':_form.querySelector('#registro2 [name="mail"]').value,
		'pronombre': _pn,
		'isopais':_form.querySelector('#registro2 [name="isopais"]').value
	}

	$.ajax({
		data:  _parametros,
		url:   './usuarios/usu_registro_ajax.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			//console.log(_res);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='caduca'){location.reload();}
				
			if(_res.res!='exito'){
				return;
			}
			
			document.querySelector('#ingresoModal #acceso_inic [name="preactivo"][value="si"]').checked=true;
			document.querySelector('#ingresoModal #acceso_inic [name="preactivo"][value="si"]').onchange();
			checkRegistro();	
		}
	});			
}



function validarInputsRegistrarUsuario(){		
	
	_stop='no';
	_form=document.querySelector('#ingresoModal');
	if(_form.querySelector('input[name="contrasena"]').value !=_form.querySelector('input[name="contrasena2"]').value){
		_form.querySelector('input[name="contrasena2"]').style.backgroundColor='#fda';
		alert('no coinciden las contraseñas');
		_stop='si';
	}
	
	if(_form.querySelector('input[name="contrasena"]').value.length < 4){
		_form.querySelector('input[name="contrasena"]').style.backgroundColor='#fda';
		alert('la contraseña requiere al menos 4 caracteres');
		_stop='si';
	}
	
	if(_form.querySelector('input[name="nombre"]').value.length < 1){
		_form.querySelector('input[name="nombre"]').style.backgroundColor='#fda';
		alert('falta ingresar su nombre');
		_stop='si';
	}
	
	if(_form.querySelector('input[name="apellido"]').value.length < 1){
		_form.querySelector('input[name="apellido"]').style.backgroundColor='#fda';
		alert('falta ingresar su apellido');
		_stop='si';
	}
	
	if(_form.querySelector('input[name="mail"]').value.length < 6){
		_form.querySelector('input[name="mail"]').style.backgroundColor='#fda';
		alert('falta ingresar su correo electrónico');
		_stop='si';
	}
	
	if(_form.querySelector('input[name="dni"]').value.length < 6){
		_form.querySelector('input[name="dni"]').style.backgroundColor='#fda';
		alert('falta ingresar su numero de identificación');
		_stop='si';
	}
	
	if(_form.querySelector('select[name="isopais"]').value==''){
		_form.querySelector('select[name="isopais"]').style.backgroundColor='#fda';
		alert('falta ingresar su estado de pertenencia');
		_stop='si';
	}
	
	if(_form.querySelector('[name="pronombre"]').value==''){
		_form.querySelector('[name="pronombre"]').style.backgroundColor='#fda';
		alert('falta ingresar su pronombre de preferencia');
		_stop='si';
	}
	
	_pn=_form.querySelector('[name="pronombre"]').value;
	if(_form.querySelector('[name="pronombre"]').value=='__otro__'){
		_pn=_form.querySelector('input[name="pronombre_otro"]').value;
		
		if(_form.querySelector('input[name="pronombre_otro"]').value==''){
			_form.querySelector('input[name="pronombre_otro"]').style.backgroundColor='#fda';
			alert('falta ingresar su pronombre de preferencia');
			_stop='si';			
		}
	}
	
	
	if(_stop=='si'){
		return false;
	}else{
		return true;
	}
	
}	

