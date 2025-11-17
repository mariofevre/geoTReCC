function modoA(_nuevomodo){
	if(_nuevomodo==_Modo){return;}
	_Modo=_nuevomodo;
	document.querySelector('#page').setAttribute('modo',_nuevomodo);
	cargarPlan('','','');
}


function borrarPlan(_id,_nivel){						
    
    if(document.querySelector(".contenidos [nivel='"+_nivel+"'][iddb='"+_id+"']")!=null){
		_el=document.querySelector(".contenidos [nivel='"+_nivel+"'][iddb='"+_id+"']");
		_el.parentNode.removeChild(_el);
	}else{
		console.log('no se encontró:'+".contenidos [nivel='"+_nivel+"'][iddb='"+_id+"']");
	}
}



function cargarPlan(_actualizar_id,_actualizar_nivel,_actualizar_modo){						
    var parametros = {
        'panid': _PanId,
    	'actualizar_id':_actualizar_id,
    	'actualizar_nivel':_actualizar_nivel,
    	'actualizar_modo':_actualizar_modo
    };				
    $.ajax({
        data:  parametros,
        url:   './app_plae/app_plae_consulta.php',
        type:  'post',
        error: function (response){alert('error al contactar al servidor');},
        success:  function (response) {
            var _res = PreprocesarRespuesta(response);
            //console.log(_res);
            
            analizarCategoriasEstandar(_res);
            	
            
            
            _DataPlan=_res.data;
        	if(_Modo=='tabla'){
        		generarFilas(_res.data);
        		return;
        	}else if(_Modo=='fichas'){
        		generarFichas(_res.data);
        		return;
        	}else if(_Modo=='texto'){
        		generarTexto(_res.data);
        		return;
        	}else if(_Modo=='cronograma'){
        		generarCronograma(_res.data);
        		return;
        	}       	
        	document.querySelector('#page > #plan > #nombre > #dat').innerHTML=_res.data.PLA.general.nombre;
            document.querySelector('#page > #plan > #descripcion > #dat').innerHTML=_res.data.PLA.general.descripcion;
            document.querySelector('#encabezadopagina').innerHTML=_res.data.PLA.general.encabezado;
            
            if(document.querySelector('#page > #plan').clientHeight>=58){
            	document.querySelector('#page > #plan').setAttribute('ampliado','-1');
            }else{
            	document.querySelector('#page > #plan').setAttribute('ampliado','0');
            }
            	
        	if(_res.data.actualizar_id==''){	   
        		
        	
        		             	
            	generarPlan(_res.data);
            	
            	         
            }else if(
            	_res.data.actualizar_id > 0
            	&&
            	_res.data.actualizar_nivel != ''
            	&&
            	(_res.data.actualizar_modo == 'actualizar'
            	||
            	_res.data.actualizar_modo == 'insertar')
            ){
            	
            	_Actores=	_res.data.Actores;
            	
            	
            	for(_n1 in _res.data.PLA.PLAn1.componentes){
					_n1id = _res.data.PLA.PLAn1.componentes[_n1].id;
					_n1d = _res.data.PN1[_n1id];
					
					
					if(_res.data.actualizar_nivel=='PLAn1' && _res.data.actualizar_id == _n1id){
						_ref=_res.data.PLA.PLAn1.componentes[_n1-1];
						if(_ref==undefined){_refid=null;}else{_refid=_ref.id;}
						_padreNivel='page';
						_padreId='';
						_data=_n1d;
					}
					
					for(_n2 in _res.data.PLA.PLAn1.componentes[_n1].PLAn2.componentes){						
						_n2id = _res.data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2].id;
						_n2d = _res.data.PN2[_n2id];
						
						if(_res.data.actualizar_nivel=='PLAn2' && _res.data.actualizar_id == _n2id){
							_ref=_res.data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2-1];
							if(_ref==undefined){_refid=null;}else{_refid=_ref.id;}
							_padreNivel='PLAn1';
							_padreId=_n1id;
							_data=_n2d;
						}			
									
						for(_n3 in _res.data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2].PLAn3.componentes){
							
							_n3id = _res.data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2].PLAn3.componentes[_n3].id;
							_n3d = _res.data.PN3[_n3id];
							
							if(_res.data.actualizar_nivel=='PLAn3' && _res.data.actualizar_id == _n3id){
								_ref=_res.data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2].PLAn3.componentes[_n3-1];
								if(_ref==undefined){_refid=null;}else{_refid=_ref.id;}
								_padreNivel='PLAn2';
								_padreId=_n2id;
								_data=_n3d;
							}

						}  	
					}  	
				}  
            	
            	if(_res.data.actualizar_modo == 'actualizar'){
            		_div = document.querySelector('div#page div[nivel="'+_res.data.actualizar_nivel+'"][iddb="'+_res.data.actualizar_id+'"]');
            		_padre=_div.parentNode;
            		
            		_enc = document.querySelector('div#page div[nivel="'+_res.data.actualizar_nivel+'"][iddb="'+_res.data.actualizar_id+'"] > div.encabezado');
            		_cont = generarEncabezado(_data,_res.data.actualizar_nivel);					
					_enc.innerHTML=_cont.innerHTML;
					
            	}else if(_res.data.actualizar_modo == 'insertar'){
            		_div = crearDivComp(_data,_res.data.actualizar_nivel);
            		
            		_enc = generarEncabezado(_data,_res.data.actualizar_nivel);
            		_div.appendChild(_enc);
            		
            		_Cont2=document.createElement('div');
					_Cont2.setAttribute('class','contenidos');
					_div.appendChild(_Cont2);	
					
					if(_res.data.actualizar_nivel=='PLAn1'){_nivelhijos='PLAn2';}
					if(_res.data.actualizar_nivel=='PLAn2'){_nivelhijos='PLAn3';}
					if(_res.data.actualizar_nivel=='PLAn3'){_nivelhijos='';}
					if(_nivelhijos!=''){
						_divAn=crearDivAnadir(_nivelhijos);
						_Cont2.appendChild(_divAn);	
					}
					                		
            		_padre=document.querySelector('div[nivel="'+_padreNivel+'"][iddb="'+_padreId+'"] > .contenidos');
            	}
            	
            	if(_ref==undefined){
            		_padre.insertBefore(_div, _padre.firstChild);
            	}else{
            		_refObj=document.querySelector('div#page div[nivel="'+_res.data.actualizar_nivel+'"][iddb="'+_refid+'"]');
            		_padre.insertBefore(_div, _refObj.nextSibling);
            	}
            	
				_rgb=hexToRgb(_data.CO_color);					
				if(_res.data.actualizar_nivel=='PLAn1'){_alpha=1;}
				if(_res.data.actualizar_nivel=='PLAn2'){_alpha=0.4;}
				if(_res.data.actualizar_nivel=='PLAn3'){_alpha=0.4;}
				_div.style.backgroundColor='rgba('+_rgb.r+', '+_rgb.g+', '+_rgb.b+', '+_alpha+')';		
							
						
				_altoventana=window.innerHeight;		            
	            $([document.documentElement, document.body]).animate({
			        scrollTop: $("div[iddb='"+_res.data.actualizar_id+"'][nivel='"+_res.data.actualizar_nivel+"']").offset().top - (_altoventana/2)
			    }, 2000);
			    _div.setAttribute('editada','no');
			    //_div.setAttribute('editada','si');
			    setTimeout(function(){_div.setAttribute('editada','si'); }, 50);       
            }
        }
    });
}    



function analizarCategoriasEstandar(_res){
	//identifica plazo de obra
    if(
    	_DatosCategorias.estandar[1].usadoennivel!=null
    	&&
    	_DatosCategorias.estandar[2].usadoennivel!=null
    	){	
    		_min=9999;
    		_max=-9999;
    		
    		if(_DatosCategorias.estandar[1].usadoennivel['PLAn1']!=undefined){
    			_idcat_ini=_DatosCategorias.estandar[1].usadoennivel['PLAn1'];
        		for(_idp in _res.data.PN1){
        			if(_res.data.PN1[_idp].categorias[_idcat_ini]!=undefined){
						_min=Math.min(_min,_res.data.PN1[_idp].categorias[_idcat_ini]);	            				
        			}
        		}	
    		}
    		
    		if(_DatosCategorias.estandar[2].usadoennivel['PLAn1']!=undefined){
    			_idcat_fin=_DatosCategorias.estandar[2].usadoennivel['PLAn1'];
        		for(_idp in _res.data.PN1){
        			if(_res.data.PN1[_idp].categorias[_idcat_fin]!=undefined){
						_max=Math.max(_max,_res.data.PN1[_idp].categorias[_idcat_fin]);	            				
        			}
        		}	
    		}
    	
    		if(_DatosCategorias.estandar[1].usadoennivel['PLAn2']!=undefined){
    			_idcat_ini=_DatosCategorias.estandar[1].usadoennivel['PLAn2'];
        		for(_idp in _res.data.PN2){
        			if(_res.data.PN2[_idp].categorias[_idcat_ini]!=undefined){
						_min=Math.min(_min,_res.data.PN2[_idp].categorias[_idcat_ini]);	            				
        			}
        		}	
    		}
    		
    		if(_DatosCategorias.estandar[2].usadoennivel['PLAn2']!=undefined){
    			_idcat_fin=_DatosCategorias.estandar[2].usadoennivel['PLAn2'];
        		for(_idp in _res.data.PN2){
        			if(_res.data.PN2[_idp].categorias[_idcat_fin]!=undefined){
						_max=Math.max(_max,_res.data.PN2[_idp].categorias[_idcat_fin]);	            				
        			}
        		}	
    		}
    		
    		if(_DatosCategorias.estandar[1].usadoennivel['PLAn3']!=undefined){
    			_idcat_ini=_DatosCategorias.estandar[1].usadoennivel['PLAn3'];
        		for(_idp in _res.data.PN3){
        			if(_res.data.PN3[_idp].categorias[_idcat_ini]!=undefined){
						_min=Math.min(_min,_res.data.PN3[_idp].categorias[_idcat_ini]);	            				
        			}
        		}	
    		}
    		
    		if(_DatosCategorias.estandar[2].usadoennivel['PLAn3']!=undefined){
    			_idcat_fin=_DatosCategorias.estandar[2].usadoennivel['PLAn3'];
        		for(_idp in _res.data.PN3){
        			if(_res.data.PN3[_idp].categorias[_idcat_fin]!=undefined){
						_max=Math.max(_max,_res.data.PN3[_idp].categorias[_idcat_fin]);	            				
        			}
        		}	
    		}
    		
    		if(_min<9999){
    			_VariablesEstandar['_mes_min']=_min;
    		}
    		if(_max>-9999){
    			_VariablesEstandar['_mes_max']=_max;
    		}
    }
}

function generarPlan(_data){	
	
	_Actores=_data.Actores;
	_CAT =_data.CAT;
	
	_cont=document.querySelector('#page > .contenidos');
	_cont.innerHTML='';
						
	for(_n1 in _data.PLA.PLAn1.componentes){
		_n1id = _data.PLA.PLAn1.componentes[_n1].id;
		//console.log(_n1);
		//console.log(_n1id);
		_n1d = _data.PN1[_n1id];
		
		_div = crearDivComp(_n1d,'PLAn1');		
		
		document.querySelector('#page > .contenidos').appendChild(_div);
		
		_divE=generarEncabezado(_n1d,'PLAn1');
		_div.appendChild(_divE);
		
		_Cont=document.createElement('div');
		_Cont.setAttribute('class','contenidos');
		_div.appendChild(_Cont);	
		
			for(_n2 in _data.PLA.PLAn1.componentes[_n1].PLAn2.componentes){
				_n2id = _data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2].id;
				_n2d = _data.PN2[_n2id];
				
				_div = crearDivComp(_n2d,'PLAn2');		
				
				_Cont.appendChild(_div);
				
				_divE=generarEncabezado(_n2d,'PLAn2');
				_div.appendChild(_divE);
				
				_Cont2=document.createElement('div');
				_Cont2.setAttribute('class','contenidos');
				_div.appendChild(_Cont2);	
				
					for(_n3 in _data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2].PLAn3.componentes){
						_n3id = _data.PLA.PLAn1.componentes[_n1].PLAn2.componentes[_n2].PLAn3.componentes[_n3].id;
						_n3d = _data.PN3[_n3id];
						
						_div = crearDivComp(_n3d,'PLAn3');
						_Cont2.appendChild(_div);
						
						_divE=generarEncabezado(_n3d,'PLAn3');
						_div.appendChild(_divE);	
					}  	
					
					_divAn=crearDivAnadir('PLAn3');
					_Cont2.appendChild(_divAn);	
				
			}  				
			_divAn=crearDivAnadir('PLAn2');
			_Cont.appendChild(_divAn);	
			
	}   
	
	_divAn=crearDivAnadir('PLAn1');
	document.querySelector('#page > .contenidos').appendChild(_divAn);	
				 
}	


 
function crearDivAnadir(_nivel){
	
	_divAN=document.createElement('div');
	_divAN.setAttribute('class',_nivel+' anade');
	_divAN.setAttribute('nivel',_nivel);
	
	_div2=document.createElement('div');
	_div2.setAttribute('class','encabezado');
	_div2.setAttribute('onclick','iraPlan("",this.parentNode.getAttribute("nivel"),this.parentNode.parentNode.parentNode.getAttribute("iddb"))');
	_divAN.appendChild(_div2);
	
	_aaa=document.createElement('a');
	_aaa.innerHTML="añadir "+_NomN[_nivel];
	_div2.appendChild(_aaa);
	
	return _divAN; 
	
}

 
function crearDivComp(_data,_nivel){
	
	_div=document.createElement('div');
	_div.setAttribute('iddb',_data.id);
	_div.setAttribute('class',_nivel);
	_div.setAttribute('nivel',_nivel);
	//console.log('d');
	//console.log(_data);
	//console.log(_data.CO_color);
	_rgb=hexToRgb(_data.CO_color);
	
	if(_nivel=='PLAn1'){_alpha=1;}
	if(_nivel=='PLAn2'){_alpha=0.4;}
	if(_nivel=='PLAn3'){_alpha=0.4;}
	_div.style.backgroundColor='rgba('+_rgb.r+', '+_rgb.g+', '+_rgb.b+', '+_alpha+')';
		
	return _div
}


function generarEncabezado(_data,_nivel){
	//console.log(_data);
	_divE=document.createElement('div');
	_divE.setAttribute('class','encabezado');
	_divE.setAttribute('onclick','iraPlan(this.parentNode.getAttribute("iddb"),this.parentNode.getAttribute("nivel"),"")');
	
	_divN=document.createElement('div');
	_divN.setAttribute('class','numero');
	_divE.appendChild(_divN);
	
	/*_divA=document.createElement('div');
			_divA.setAttribute('class','aux num');
			_divA.title="identificador único para el nivel 2 de planificación";
			_divA.innerHTML="n2 "+_n2;
			_divN.appendChild(_divA);*/
	
	_divN.innerHTML+=_NomN[_nivel];
	_divN.innerHTML+=' <span class="val">'+_data.numero+'</span>';
				
	_divN=document.createElement('div');
	_divN.setAttribute('class','nombre');
	_divN.innerHTML=_data.nombre;
	_divE.appendChild(_divN);

	_divN=document.createElement('div');
	_divN.setAttribute('class','decripcion');
	_divN.innerHTML="<div class='subtitulo'>Descripción:</div>"+_data.descripcion;
	_divE.appendChild(_divN);	
	
	_divN=document.createElement('div');
	_divN.setAttribute('class','subtitulo');
	_divN.innerHTML="Responsables:";
	_divE.appendChild(_divN);	

	
	if(_data.id_p_GRAactores!=''){
		
		if(_Actores[_data.id_p_GRAactores]!=undefined){
			_divE.innerHTML+=_Actores[_data.id_p_GRAactores].nombre+ " "+_Actores[_data.id_p_GRAactores].apellido;		
		}
	}			
	
	_divN=document.createElement('div');
	_divN.setAttribute('class','estado');
	_divE.appendChild(_divN);	
	
	
	_divS=document.createElement('div');
	_divS.setAttribute('class','subtitulo');
	_divS.innerHTML="Estados:";
	_divN.appendChild(_divS);	
	
	
	_nl=document.createElement('ul');
	_nl.setAttribute('class','listaestados');
	_divN.appendChild(_nl);		
	
	for(_Ke in _data.estados){
		_Ve = _data.estados[_Ke];
		_li=document.createElement('li');
		_li.innerHTML="<span class='estnombre'>"+_Ve.nombre+"</span><span class='desde'>desde:</span>"+_Ve.desde;
		_nl.appendChild(_li);		
	}
	
	for(_nc in _CAT[_nivel]){
		_vc = _CAT[_nivel][_nc];
		
		
		_divS=document.createElement('div');
		_divS.setAttribute('class','categoria');
		_divS.innerHTML="<label>"+_vc.nombre+"</label>";
		_divE.appendChild(_divS);
		
		_valor='';
		if(_data.categorias[_nc]!=undefined){
			_valor=_data.categorias[_nc];
		}
		
		_divS.innerHTML += "<span class='categoriavalor'>"+_valor+"</span>";
		
	}
	
	
	_divN=document.createElement('div');
	_divN.setAttribute('class','documentos');
	_divE.appendChild(_divN);	
	
	if(Object.keys(_data.documentos).length>0){
		for(_na in _data.documentos){
			if(typeof _data.documentos[_na] != 'object'){continue;}							
			_adat=_data.documentos[_na];							
			
			if(_adat.mostrar=='si'){
				_aaa=document.createElement('a');
				_aaa.setAttribute('download',_adat.FI_nombreorig);
				_aaa.setAttribute('onclick','event.stopPropagation()');
				_aaa.setAttribute('href',_adat.FI_documento);
				_aaa.innerHTML='<img src="'+_adat.FI_documento+'"><div>'+_adat.FI_nombreorig+'</div>';
				_aaa.title=_adat.descripcion;
				_divN.appendChild(_aaa);	
				
			}else{
				_aaa=document.createElement('a');
				_aaa.setAttribute('download',_adat.FI_nombreorig);
				_aaa.setAttribute('onclick','event.stopPropagation()');
				_aaa.setAttribute('href',_adat.FI_documento);
				_aaa.innerHTML='<img src="./comun_img/hayarchivo.png"><div>'+_adat.FI_nombreorig+'</div>';
				_aaa.title=_adat.descripcion;
				_divN.appendChild(_aaa);							
			}						
		}
	}

	return _divE;

}	
