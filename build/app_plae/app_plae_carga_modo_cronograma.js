//generador HTML para mod tabla
 function generarCronograma(_data){	
	
	if(
		_DatosCategorias.estandar[1].usadoennivel['PLAn3']==undefined
		 ||
		_DatosCategorias.estandar[2].usadoennivel['PLAn3']==undefined
	){
		alert('No podés generar un cronograma si tus accones no tienen mes de inicio y mes de fin');
		return;
	}
	
	
	
	_Actores=_data.Actores;
	_CAT =_data.CAT;
						
	_cont=document.querySelector('#contenidos');
	
	_cont.innerHTML='<h2>Cronograma</h2>';
	
	
	_tabla=document.createElement('table');
	_cont.appendChild(_tabla);

	_duracion=_VariablesEstandar._mes_max-_VariablesEstandar._mes_min;
	
	
	_cron=document.createElement('div');
	_cron.setAttribute('class','cronograma');
	_cont.appendChild(_cron);
	
	
	_cont=document.createElement('div');
	_cont.setAttribute('class','contenido');
	_cron.appendChild(_cont);
	
	_salto=1;
	//ancho 900px
	if((580/(_duracion+2))<20){
		_salto=2;
	}
	
	_ancho=580/(_duracion+2);
							
	for(_n1 in _data.PLA.PLAn1.componentes){
		_n1id = _data.PLA.PLAn1.componentes[_n1].id;
		_n1d = _data.PN1[_n1id];
			
		_fila = crearfilacron(_n1d,'PLAn1',_salto,_ancho,_duracion);		
		_tabla.appendChild(_fila);
			
		for(_n2 in _data.PLA.PLAn1.componentes[_n1].PLAn2.componentes){
			_n2id = _data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2].id;
			_n2d = _data.PN2[_n2id];
			
			_fila = crearfilacron(_n2d,'PLAn2',_salto,_ancho,_duracion);	
			_tabla.appendChild(_fila);
			
		
			for(_n3 in _data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2].PLAn3.componentes){
				_n3id = _data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2].PLAn3.componentes[_n3].id;
				_n3d = _data.PN3[_n3id];
				
				_fila = crearfilacron(_n3d,'PLAn3',_salto,_ancho,_duracion);		
				_tabla.appendChild(_fila);
			}  		
		}
	}  		
}   
	
function crearfilacron(_data,_nivel,_salto,_ancho,_duracion){
	


	_fila=document.createElement('tr');
	_fila.setAttribute('nivel',_nivel);
	
	if(_nivel=='PLAn1'&&_data.CO_color!='#ffffff'){
		_fila.style.backgroundColor=_data.CO_color;
	}else if(_nivel=='PLAn2'&&_data.CO_color!='#ffffff'){
		_fila.style.backgroundColor=_data.CO_color;
	}else if(_nivel=='PLAn3'){
		_fila.style.backgroundColor=_data.CO_color;
	}
	console.log(_data.CO_color);
	_fila.setAttribute('iddb',_data.id);
	_fila.setAttribute('tadb',_nivel);
	
	_td=document.createElement('td');
	_td.setAttribute('class','numero');
	_td.innerHTML="<span class='nom'>"+_NomN[_nivel]+"</span>";
	_td.innerHTML+=_data.numero;
	_fila.appendChild(_td);
	
	_td=document.createElement('td');
	_td.setAttribute('class','nombre');
	_td.innerHTML="<div class='nombreplan2'>"+_data.nombre+"</div>";
	_fila.appendChild(_td);

	_cont=document.createElement('div');
	_cont.setAttribute('class','contienemeses');
	_fila.appendChild(_cont);
	
	_s=0;
	for(i=_VariablesEstandar._mes_min;i<_VariablesEstandar._mes_max+1;i++){
		_s++;
		if(_s==_salto){_s=0}
		if(i==0){_s=1;}
		if(i==_VariablesEstandar._mes_max){_s=1;}
		if(0-i<_salto&&0-i>0){_s=0;}
		if(_VariablesEstandar._mes_max-i<_salto&&_VariablesEstandar._mes_max-i>0){_s=0;}
		
		
		
		_mes=document.createElement('div');
		
		
		_mes.style.width=_ancho+'px';
		_cont.appendChild(_mes);
		
		if(_s=='1'){
			_mes.style.borderLeft='1px solid #000';
		}
		
		if(_nivel=='PLAn1'){
			_mes.setAttribute('class','mes');
			
		}else if(_nivel=='PLAn2'){
			_mes.setAttribute('class','mes');
			if(_s=='1'){
				_mes.innerHTML='mes '+i;
			}
		}else if(_nivel=='PLAn3'){
			_mes.setAttribute('class','barra');			
			_catini=_DatosCategorias.estandar[1].usadoennivel['PLAn3'];
			_catfin=_DatosCategorias.estandar[2].usadoennivel['PLAn3'];
				
			if(
				i>=_data.categorias[_catini]
				&&
				i<_data.categorias[_catfin]
			){
				_mes.setAttribute('estado','activa');
				
			}
				
		}		
	}
	
	return _fila;	
		
}  	