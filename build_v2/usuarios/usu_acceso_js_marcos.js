
	
function config(){
	if(_UsuarioAcceso.general.general.general<3){return;}

	document.querySelector('#formconfig').setAttribute('estado','activo');
		
	if(_SelecElemCod!=''&&_SelecTabla=='est_02_marcoacademico'){
		document.querySelector('#formconfig #btmmodifM').style.display='inline-block';
	}else{
		document.querySelector('#formconfig #btmmodifM').style.display='none';
	}
	
	document.querySelector('#formconfig #crearMarco').style.display='none';
	document.querySelector('#formconfig #editarMarco').style.display='none';
	
	document.querySelector('#formconfig #crearMarco [name="geomtx"]').value='';
	
	if(_SelecGeom == undefined ){
		
	}else{
		if(_SelecGeom!=null){
			document.querySelector('#formconfig #crearMarco [name="geomtx"]').value=_SelecGeom;
		}
	}
	
	_lista=document.querySelector('#formconfig #editarMarco #listaaccionesactivas');
	_lista.innerHTML='';
	for(_accnom in _DataConf.acciones){
				
		_accndata=_DataConf.acciones[_accnom];	
		
		
		
		_li=document.createElement('input');
		_lista.appendChild(_li);
		_li.setAttribute('type','checkbox');
		_li.setAttribute('name',_accnom);
		if(_accndata.activo==1){
			_li.checked=true;
		}

		_sp=document.createElement('span');
		_lista.appendChild(_sp);		
		_sp.setAttribute('class','img');
		_la=document.createElement('img');
		_la.setAttribute('src','./img/'+_accnom+'.png');
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

_drawMarcoPropuesto={};  
function dibujarGeometriaMarco(){
	alert('dibuje en el mapa el área de trabajo');
	
	document.querySelector('#formconfig').setAttribute('modo','dibujando');	
	_sourceMarcoPropuesto.clear();
	mapa.removeInteraction(_drawMarcoPropuesto);

	_drawMarcoPropuesto = new ol.interaction.Draw({
		source: _sourceMarcoPropuesto,
		type: 'Polygon'
	});
	
	mapa.addInteraction(_drawMarcoPropuesto);	
	_dibujandoMarcoPropuesto='si';
}


function crearMarco(_event,_this){		
		_event.preventDefault();
		
		_stop='no';
		_form=_this;
		if(_form.querySelector('input[name="codigo"]').value == ''){
			_form.querySelector('input[name="codigo"]').backgroundColor='#fda';
			alert('debe completar el código');
			_stop='si';
		}
		
		_str=_form.querySelector('input[name="codigo"]').value.toUpperCase();
		if(_str.indexOf(" ") != -1){
			_form.querySelector('input[name="codigo"]').backgroundColor='#fda';
			alert('el código no puede contener espacios ni caracteres especiales');
			_stop='si';
		}
		if(_str.indexOf('Á') != -1){
			_form.querySelector('input[name="codigo"]').backgroundColor='#fda';
			alert('el código no puede contener espacios ni caracteres especiales');
			_stop='si';
		}
		if(_str.indexOf("É") != -1){
			_form.querySelector('input[name="codigo"]').backgroundColor='#fda';
			alert('el código no puede contener espacios ni caracteres especiales');
			_stop='si';
		}
		if(_str.indexOf("Í") !=  -1){
			_form.querySelector('input[name="codigo"]').backgroundColor='#fda';
			alert('el código no puede contener espacios ni caracteres especiales');
			_stop='si';
		}
		if(_str.indexOf("Ó") !=  -1){
			_form.querySelector('input[name="codigo"]').backgroundColor='#fda';
			alert('el código no puede contener espacios ni caracteres especiales');
			_stop='si';
		}
		if(_str.indexOf("Ú") !=  -1){
			_form.querySelector('input[name="codigo"]').backgroundColor='#fda';
			alert('el código no puede contener espacios ni caracteres especiales');
			_stop='si';
		}
		
		if(_form.querySelector('input[name="nombre"]').value == ''){
			_form.querySelector('input[name="nombre"]').backgroundColor='#fda';
			alert('debe completar el nombre corto');
			_stop='si';
		}
		if(_form.querySelector('input[name="nombre_oficial"]').value == ''){
			_form.querySelector('input[name="nombre_oficial"]').backgroundColor='#fda';
			alert('debe completar el nombre oficial');
			_stop='si';
		}
				
		if(_stop=='si'){
			return;
		}
		
		var parametros = {
			'nombre': _form.querySelector('input[name="nombre"]').value,
			'nombre_oficial': _form.querySelector('input[name="nombre_oficial"]').value,
			'codigo': _form.querySelector('input[name="codigo"]').value,
			'descripcion': _form.querySelector('[name="descripcion"]').value,
			'tabla': _form.querySelector('select[name="tabla"]').value,
			'geomtx': _form.querySelector('[name="geomtx"]').value			
		};
		
		$.ajax({
			data:  parametros,
			url:   './app_est/ed_candidato_crea.php',
			type:  'post',
			success:  function (response){
				var _res = $.parseJSON(response);
				console.log(_res);
				if(_res.res=='exito'){
					
					document.querySelector('#formconfig').setAttribute('estado','inactivo');
					_url='./app_est.php?cod='+_res.data.codigo;
					location.assign(_url);
					
								
				}else{
					alert('error')
				}
				
			}
		});
	
	}	
	
function formCrearMarco(){
	
	document.querySelector('#formconfig #crearMarco').style.display='block';
	document.querySelector('#formconfig #editarMarco').style.display='none';
}


function formModifMarco(){
	
	
	document.querySelector('#formconfig #crearMarco').style.display='none';
	document.querySelector('#formconfig #editarMarco').style.display='block';			
	document.querySelector('#formconfig #editarMarco input[name="nombre"]').value=_DataElem.nombre;
	document.querySelector('#formconfig #editarMarco input[name="nombre_oficial"]').value=_DataElem.nombre_oficial;	
	document.querySelector('#formconfig #editarMarco span[name="codigo"]').innerHTML=_DataElem.codigo;	
	document.querySelector('#formconfig #editarMarco textarea[name="descripcion"]').value=_DataElem.descripcion;	
	document.querySelector('#formconfig #editarMarco textarea[name="geomtx"]').value=_DataElem.geotx;
	
	document.querySelector('#formconfig #editarMarco .punto[idp="0"] [name="geotx"]').value=_DataMarco.personalizacion.centro;
	
	for(_ns in _DataMarco.personalizacion.sitios){
		
		_ds=_DataMarco.personalizacion.sitios[_ns];
		
		_mod=document.querySelector('#componente_centros .modelo.punto');
		_clon=_mod.cloneNode(true);
		_l=document.querySelector('#componente_centros #listado_puntos');
		_l.appendChild(_clon);
		_clon.setAttribute('class','punto');
		_clon.querySelector('[name="nombre"]').value=_ds.nombre;
		_clon.querySelector('[name="geotx"]').value=_ds.centro;
		_clon.setAttribute('idp',_ds.id);
	}
	
	
}


function guardacampoMarco(_this){
	_name=_this.getAttribute('for');
	_val=_this.parentNode.querySelector('[name="'+_name+'"]').value;
	_codigo=_this.parentNode.querySelector('[name="codigo"]').innerHTML;
	_parametros = {
		'campo': _name,
		'valor':_val,
		'codigo':_codigo			
	};
	
	_this.parentNode.querySelector('[for="'+_name+'"]').setAttribute('estado','guardando');
	
	$.ajax({
		data:  _parametros,
		url:   './app_est/app_est_ed_cambia_campo_marco.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			document.querySelector('#formconfig #editarMarco [for="'+_res.data.campo+'"]').setAttribute('estado','listo');
			
			//console.log(_res);
			if(_res.res=='exito'){
				cerrar();					
			}else{
				alert('error')
			}
			
		}
	});	
}

function guardaaccionesMarco(){
	
	_ins=document.querySelectorAll('#formconfig #editarMarco #listaaccionesactivas input');	
	_parametros={
		'codigo':_CodMarco
	}
	
	for(_inn in _ins){
		if(typeof _ins[_inn] != 'object'){continue;}
		if(_ins[_inn].checked){_s=1}else{_s=0}
		_parametros[_ins[_inn].getAttribute('name')]=_s;
	}
	
	$.ajax({
		data:  _parametros,
		url:   './app_est/app_est_ed_cambia_acciones_marco.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			document.querySelector('#formconfig #editarMarco [for="'+_res.data.campo+'"]').setAttribute('estado','listo');
					
			//console.log(_res);
			if(_res.res=='exito'){
				cerrar();					
			}else{
				alert('error')
			}
			
		}
	});	
}

function guardaSitiosMarco(){
	
	_sitios=document.querySelector('#formconfig #componente_centros .punto');
	for(_sn in _sitios){
		if(typeof _sitios[_sn] != 'object'){continue;}
		
		_parametros={
			'idp':_sitios[_sn].getAttribute('idp'),
			'geotx':_sitios[_sn].querySelector('[name="geotx"]').value,
			'nombre':_sitios[_sn].querySelector('[name="nombre"]').value
		}
		
		$.ajax({
			data:  _parametros,
			url:   './app_est/app_est_ed_punto_interes_cambiar.php',
			type:  'post',
			success:  function (response){
				var _res = $.parseJSON(response);
				
				if(_res.res!='exito'){return;}
				document.querySelector('#formconfig #componente_centros [for="sitios').setAttribute('estado','listo');
				
			}
		});	
		
	}	
}

function guardaSitiosMarco(){
	_sitios=document.querySelectorAll('#formconfig #componente_centros .punto');
	for(_sn in _sitios){
		if(typeof _sitios[_sn] != 'object'){continue;}
		console.log(_sitios[_sn]);
		if(_sitios[_sn].getAttribute('idp')==null){continue;}
		_parametros={
			'idp':_sitios[_sn].getAttribute('idp'),
			'geotx':_sitios[_sn].querySelector('[name="geotx"]').value,
			'nombre':_sitios[_sn].querySelector('[name="nombre"]').value,
			'codigo':_CodMarco
		}
		
		$.ajax({
			data:  _parametros,
			url:   './app_est/app_est_ed_punto_interes_cambiar.php',
			type:  'post',
			success:  function (response){
				var _res = $.parseJSON(response);
				if(_res.res!='exito'){return;}
				document.querySelector('#formconfig #componente_centros [for="sitios').setAttribute('estado','listo');
				
			}
		});	
	}	
}

function crearSitioDeInteres(){
	
	_parametros={
		'codigo':_CodMarco
	}
	
	$.ajax({
		data:  _parametros,
		url:   './app_est/app_est_ed_punto_interes_crear.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			if(_res.res!='exito'){return;}
			document.querySelector('#formconfig #componente_centros [for="sitios').setAttribute('estado','listo');
			
				_mod=document.querySelector('#componente_centros .modelo.punto');
				_clon=_mod.cloneNode(true);
				_l=document.querySelector('#componente_centros #listado_puntos');
				_l.appendChild(_clon);
				_clon.setAttribute('class','punto');
				_clon.querySelector('[name="nombre"]').value='-Nuevo Sitio-';
				_clon.setAttribute('idp',_res.data.nid);
		}
	});		

}
