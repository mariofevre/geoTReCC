/*
 * 
 *  definicion de variables y funciones comunes de uso recurrente
 * */



function preprocesarRespuestaAjax(_data, _textStatus, _jqXHR){
	
    try {
        JSON.parse(_data);
    } catch (e) {
		alert('Error. La respuesta del servidor no tiene un forma Json válido');
        return false;
    }
    
    _res = JSON.parse(_data);
    
    if(_res.res != 'exito'){
		console.log(_res);
		alert('Ocurrió un error durante el procesamiento y no se obtuvo el resultado esperado.');
		
		return false;
	}
	
	for(_nm in _res.mg){
		alert(_res.mg[_nm]);
	}
	
    return _res;
}


function consultasPHP_nueva(_url){
    _ConsultasPHP.cons_real ++;
    _cons = {
        "url": _url,
        "estado": "realizado",
        "tporespuesta": null,
        "id": _ConsultasPHP.cons_real,
        "mensajes": []  
    };
    _ConsultasPHP.consultas[_ConsultasPHP.cons_real] = _cons;

    _cartel=document.querySelector('div#cartel_consultando');
	_cartel.setAttribute('activo','si');
	_cartel.setAttribute("estado","cargando");

    //console.log(_ConsultasPHP);

    _div = document.createElement('div');
    _div.setAttribute('cn',_ConsultasPHP.cons_real);

    _img_err = document.createElement('img');
    _img_err.setAttribute('id','cons_error');
    _img_err.setAttribute('src','./img/error.png');
    _div.appendChild(_img_err);
    
    _img_ex = document.createElement('img');
    _img_ex.setAttribute('id','cons_exito');
    _img_ex.setAttribute('src','./img/check-sinborde.png');
    _div.appendChild(_img_ex);
    
    _img_carga = document.createElement('img');
    _img_carga.setAttribute('id','cons_cargando');
    _img_carga.setAttribute('src','./img/cargando.gif');
    _div.appendChild(_img_carga);   
        
    _div.innerHTML += `${_ConsultasPHP.cons_real} : ${_url} `;
        
    document.querySelector('#cartel_consultando #consultas').appendChild(_div);

    return _ConsultasPHP.cons_real;
}	


function consultasPHP_respuesta(_resultado,_cn,_mg,_res){
	
    _ConsultasPHP.cons_resp ++;
    
    if (_resultado=="err"){
		document.querySelector("#cartel_consultando").setAttribute("estado","error");	
		document.querySelector("#cartel_consultando #mensajes").innerHTML="Error en el servidor";
		
		document.querySelector("#cartel_consultando #consultas div[cn='"+_cn+"']").setAttribute('estado','error');
		document.querySelector("#cartel_consultando #consultas div[cn='"+_cn+"']").setAttribute('title','error al contactar el servidor');
	} 
	
	if (_resultado=="acc"){
		document.querySelector("#cartel_consultando").setAttribute("estado","sinacceso");	
		document.querySelector("#cartel_consultando #mensajes").innerHTML="Falta de acceso";
		
		document.querySelector("#cartel_consultando #consultas div[cn='"+_cn+"']").setAttribute('estado','error');
		document.querySelector("#cartel_consultando #consultas div[cn='"+_cn+"']").setAttribute('title','error al validar acceso');
	} 
	
	
    if (_resultado=="exito"){	
		document.querySelector("#cartel_consultando #consultas div[cn='"+_cn+"']").setAttribute('estado','exito');
		
		if(_res!='exito'){
			document.querySelector("#cartel_consultando").setAttribute("estado","error");	
			document.querySelector("#cartel_consultando #mensajes").innerHTML="El servidor detectó un error en la consulta";
			
			document.querySelector("#cartel_consultando #consultas div[cn='"+_cn+"']").setAttribute('estado','error');
		}
				
		_txtitle='';		
		for(_nm in _mg){
			_txtitle +=_mg[_nm]+'\n';
		}
		document.querySelector("#cartel_consultando #consultas div[cn='"+_cn+"']").setAttribute('title',_txtitle);
	}
    
    if (_ConsultasPHP.cons_resp == _ConsultasPHP.cons_real 
    &&
    document.querySelector("#cartel_consultando").getAttribute("estado")!="error"){
        _cartel=document.querySelector('div#cartel_consultando');
        _cartel.removeAttribute('activo');
    }
}


function cerrarCartelConsultando(){
	document.querySelector("#cartel_consultando").setAttribute("estado","");	
	document.querySelector("#cartel_consultando").removeAttribute("activo");	
	document.querySelector("#cartel_consultando #mensajes").innerHTML="";
}
