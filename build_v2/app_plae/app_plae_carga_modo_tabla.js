//generador HTML para mod tabla
 function generarFilas(_data){	
	
	_Actores=_data.Actores;
	_CAT =_data.CAT;
						
	_cont=document.querySelector('#contenidos');
	_cont.innerHTML='';
	_tabla=document.createElement('table');
	_cont.appendChild(_tabla);
						
	for(_n1 in _data.PLA.PLAn1.componentes){
		_n1id = _data.PLA.PLAn1.componentes[_n1].id;
		_n1d = _data.PN1[_n1id];
			
		_fila = crearfila(_n1d,'PLAn1');		
		_tabla.appendChild(_fila);
			
		for(_n2 in _data.PLA.PLAn1.componentes[_n1].PLAn2.componentes){
			_n2id = _data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2].id;
			_n2d = _data.PN2[_n2id];
			
			
			
			
			_fila = crearfila(_n2d,'PLAn2');		
			_tabla.appendChild(_fila);
			
		
			for(_n3 in _data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2].PLAn3.componentes){
				_n3id = _data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2].PLAn3.componentes[_n3].id;
				_n3d = _data.PN3[_n3id];
				
				_fila = crearfila(_n3d,'PLAn3');		
				_tabla.appendChild(_fila);
			}  		
		}
	}  		
}   
	
function crearfila(_data,_nivel){
	
	_col=0;
	if(_nivel=='PLAn1'){_col=3}
	if(_nivel=='PLAn2'){_col=2}
	if(_nivel=='PLAn3'){_col=1}
	

	_fila=document.createElement('tr');
	_fila.setAttribute('nivel',_nivel);
	_fila.style.backgroundColor=_data.CO_color;
	_fila.setAttribute('iddb',_data.id);
	_fila.setAttribute('tadb',_nivel);
	
	_td=document.createElement('td');
	_td.setAttribute('onclick','iraPlan(this.parentNode.getAttribute("iddb"),this.parentNode.getAttribute("nivel"),"")');
	_td.setAttribute('colspan',_col);
	_td.setAttribute('class','numero');
	
	_td.innerHTML="<span class='nom'>"+_NomN[_nivel]+"</span>";
	
	_td.innerHTML+=_data.numero;
	_fila.appendChild(_td);
	
	_td=document.createElement('td');
	_td.setAttribute('class','nombre');
	_td.setAttribute('onclick','iraPlan(this.parentNode.getAttribute("iddb"),this.parentNode.getAttribute("nivel"),"")');
	_td.innerHTML=_data.nombre;
	_fila.appendChild(_td);
		
	_dcol = 4 -_col;
	_td=document.createElement('td');
	_td.setAttribute('class','descripcion');
	_td.setAttribute('colspan',_dcol);
	
	_contmoderado=document.createElement('div');
	_contmoderado.setAttribute('style','max-height:50px;display-inline-block;overflow:hidden');
	_td.appendChild(_contmoderado);
	
	
	
	_catini=0;
	_catfin=0;
	if(
		_DatosCategorias.estandar[1].usadoennivel['PLAn3']!=undefined
		&&
		_DatosCategorias.estandar[2].usadoennivel['PLAn3']!=undefined
	){
		_catini=_DatosCategorias.estandar[1].usadoennivel[_nivel];
		_catfin=_DatosCategorias.estandar[2].usadoennivel[_nivel];
	}
	_cat=document.createElement('div');		
	_cat.setAttribute('class','categorias');
	_contmoderado.appendChild(_cat);
	
	for(_nc in _CAT[_nivel]){
		_vc = _CAT[_nivel][_nc];
		
		if(_nc==_catfin){
			continue;//esta categoría ya fue representada en el cronograma
		}
		if(_nc==_catini){
			continue;//esta categoría ya fue representada en el cronograma
		}
		
		_divC=document.createElement('div');
		_divC.setAttribute('class','categoria');
		_cat.appendChild(_divC);
		
		_divS=document.createElement('div');
		_divS.setAttribute('class','subtitulo');
		_divS.innerHTML=_vc.nombre+': ';
		_divC.appendChild(_divS);
			
		
			_Vv=_data.categorias[_nc];
			
			_divV=document.createElement('div');
			_divV.setAttribute('class','tx');
			_divS.innerHTML+=_Vv;
		
		
	}
	_contmoderado.innerHTML+=_data.descripcion;
	
	
	_fila.appendChild(_td);	
	
	_res=document.createElement('td');
	_res.setAttribute('class','responsable');
	_fila.appendChild(_res);
	if(_data.id_p_GRAactores!=''){
		if(_Actores[_data.id_p_GRAactores]!=undefined){
			_res.innerHTML=_Actores[_data.id_p_GRAactores].nombre+ " "+_Actores[_data.id_p_GRAactores].apellido;		
		}
	}		
	
	_est=document.createElement('div');
	_est.setAttribute('class','estado');
	if(_data.estados[0]!=undefined){
	_est.innerHTML+=_data.estados[0].nombre+" "+_data.estados[0].desde;
	}	
	_fila.appendChild(_est);	
	
	return _fila;	
		
}  	