
function buscadatos(_this,_event){
	
	
	if ( 
        _event.keyCode == '9'//presionó tab no es un nombre nuevo
        ||
        _event.keyCode == '13'//presionó enter
        ||
        _event.keyCode == '32'//presionó espacio
        ||
        _event.keyCode == '37'//presionó direccional
        ||
        _event.keyCode == '38'//presionó  direccional
        ||
        _event.keyCode == '39'//presionó  direccional
        || 
        _event.keyCode == '40'//presionó  direccional		  		
    ){
    	return;
    }
	
	console.log(_event.keyCode);
	/*if ( 
		_event.keyCode == '27'//presionó tab no es un nombre nuevo
	){
		_this.value='';
	}*/
	_val=_this.value;
				
	_hatch=_val.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
	_hatch=_hatch.replace('/[^A-Za-z0-9\-]/gi', '');
	_hatch=_hatch.replace(/ /g, '');
	_hatch=_hatch.toLowerCase();
	
	
	buscarDatos(_val,'capa');
	buscarDatos(_val,'ind');
	buscarDatos(_val,'docs');
	buscarDatos(_val,'publ');
	buscarDatos(_val,'rele');
	buscarDatos(_val,'game');
	
	/*
	buscarCapas(_val);
	buscarIndicadores(_val);
	buscarDocumentos(_val);
	buscarPublicaciones(_val);
	buscarRelevamientos(_val);
	buscarGamificaciones(_val);
	*/

}




function buscarDatos(_val,_tipo){
	
	if(_RecorteDeTrabajo==undefined){_RecorteDeTrabajo='';}
	
	var parametros = {
		'busqueda':_val,
		'codMarco':_CodMarco,
		'RecorteDeTrabajo' : _RecorteDeTrabajo
	};		
	
	_url='./app_'+_tipo+'/app_'+_tipo+'_busca_datos.php';	
	$.ajax({
		data:  parametros,
		url:   _url,
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
            for(_nm in _res.mg){alert(_res.mg[_nm]);}
            if(_res.res!='exito'){
            	alert('error al consultar la base de datos');
            }	
			
			_tipo=_res.data.busquedatipo;
	
            _elems=document.querySelectorAll('#menubuscado #contenido .'+_tipo);
            for(_nc in _elems){
				if( typeof _elems[_nc] != 'object'){continue;}
				_elems[_nc].parentNode.removeChild(_elems[_nc]);
			}		
			
			_Busqueda[_tipo]={};
			
			for(_nd in _res.data.resultados){
				_dat=_res.data.resultados[_nd];
				
				_Busqueda[_tipo][_dat.id]=_dat;
				
				_el=document.createElement('a');
				_el.title=_dat.descripcion;
				_el.setAttribute('class',_tipo);
				_el.setAttribute('tipo',_tipo);
				_el.setAttribute('idr',_dat.id);
				_el.setAttribute('onclick','muestraTarjeta(this.getAttribute("tipo"),this.getAttribute("idr"))');
				document.querySelector('#menubuscado #contenido').appendChild(_el);
				
				_logo=document.createElement('span');
				_logo.setAttribute('class','logo');
				_el.appendChild(_logo);
				
				_img=document.createElement('img');
				_img.setAttribute('src','./img/app_'+_tipo+'.png');
				_logo.appendChild(_img);
				
				_nom=document.createElement('span');
				_nom.setAttribute('class','nom');
				_nom.innerHTML=_dat.nombre;
				_el.appendChild(_nom);
				
				_cod=document.createElement('span');
				_cod.setAttribute('class','marco');
				_cod.innerHTML=_dat.ic_p_est_02_marcoacademico;
				_el.appendChild(_cod);			
			}

			_contenidos=document.querySelectorAll('#menubuscado #contenido > a');
			
			if(Object.keys(_contenidos).length > 0){
				document.querySelector('#menubuscado').setAttribute('estado','activo');
			}else{
				document.querySelector('#menubuscado').removeAttribute('estado');
			}
		}
	});	
}
	



function muestraTarjeta(_tipo,_id){
	
	limpiarTarjeta();
	
	
	document.querySelector('#muestraresultado').setAttribute('tipo',_tipo);
	document.querySelector('#muestraresultado').setAttribute('marco',_Busqueda[_tipo][_id].ic_p_est_02_marcoacademico);
	document.querySelector('#muestraresultado').setAttribute('idr',_id);
	document.querySelector('#muestraresultado').setAttribute('estado','activo');
	
	document.querySelector('#muestraresultado #nombre').innerHTML=_Busqueda[_tipo][_id].nombre;
	document.querySelector('#muestraresultado #tipocontenido').innerHTML=_Busqueda[_tipo][_id].nombre;
	document.querySelector('#muestraresultado #descripcion').innerHTML=_Busqueda[_tipo][_id].descripcion;
	document.querySelector('#muestraresultado #marcoacademico').innerHTML=_Busqueda[_tipo][_id].ic_p_est_02_marcoacademico;

	consultaCompletaTarjeta(_tipo,_id);
	
}



function consultaCompletaTarjeta(_tipo,_id){
	
	var parametros = {
		'tipo':_tipo,
		'id':_id
	};		
	
	_url='./app_'+_tipo+'/app_'+_tipo+'_busca_elemento.php';	
	$.ajax({
		data:  parametros,
		url:   _url,
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
            for(_nm in _res.mg){alert(_res.mg[_nm]);}
            if(_res.res!='exito'){
            	alert('error al consultar la base de datos');
            }	
			
			_nivel={
				'1':'visita',
				'2':'investiga',
				'3':'administra'
			}
			
			document.querySelector('#muestraresultado #acceso').innerHTML=_nivel[_res.data.nivel_acceso];
			
			document.querySelector('#muestraresultado #autoria').innerHTML=_res.data.resultado.autoria_nombre+' '+_res.data.resultado.autoria_apellido+' ('+_res.data.resultado.autoria_email+')';
			
			document.querySelector('#muestraresultado #marcoacademicotx').innerHTML=_res.data.marco.nombre+'<br>'+_res.data.marco.nombre_oficial;
			document.querySelector('#muestraresultado #marcoacademicotx').title=_res.data.marco.descripcion;
			
			for(_rn in _res.data.referentes){
				_da=_res.data.referentes[_rn];
				_r=document.createElement('p');
				_r.innerHTML=_da.nombre+' '+_da.apellido+' ('+_da.email+')';
				document.querySelector('#muestraresultado #referentes').appendChild(_r);
			}
			
		}
	});	
}
	




function limpiarTarjeta(){
	
	document.querySelector('#muestraresultado').removeAttribute('tipo');
	document.querySelector('#muestraresultado').removeAttribute('marco');
	document.querySelector('#muestraresultado').removeAttribute('idr');
	document.querySelector('#muestraresultado').removeAttribute('estado');
	
	document.querySelector('#muestraresultado #nombre').innerHTML='';
	document.querySelector('#muestraresultado #tipocontenido').innerHTML='';
	document.querySelector('#muestraresultado #descripcion').innerHTML='';
	document.querySelector('#muestraresultado #marcoacademico').innerHTML='';
	
	document.querySelector('#muestraresultado #acceso').innerHTML='';
	document.querySelector('#muestraresultado #autoria').innerHTML='';
	document.querySelector('#muestraresultado #marcoacademicotx').innerHTML='';
	document.querySelector('#muestraresultado #marcoacademicotx').title='';
	document.querySelector('#muestraresultado #referentes').innerHTML='';
	
}


function irElementoTarjeta(_this){
	
	_t=_this.parentNode.getAttribute('tipo');
	_i=_this.parentNode.getAttribute('idr');
	_m=_this.parentNode.getAttribute('marco');
	
	_url='./app_'+_t+'.php?cod='+_m+'&idr='+_i
	
	window.location.assign(_url);
}
