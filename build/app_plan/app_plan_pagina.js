

/////////////////////
///funciones para mostrar información base
////////////////////	
	function cargaElim(_res){
        //Actualiza la carga al eliminar un registro;
        _contelim=document.querySelector('#contenidoextenso > .hijos .item[idit="'+_res.data.idit+'"]');
        _niv=_contelim.getAttribute('nivel');
        _nnn=parseInt(_niv)+1;
        _itemes=_contelim.querySelectorAll('.item[nivel="'+_nnn+'"]');
        //alert(_nnn);
        for(_ni in _itemes){
            //console.log(_itemes[_ni]);
            if(typeof _itemes[_ni]!='object'){continue;}
            _dest=document.querySelector('#contenidoextenso > .hijos');
            console.log(_dest);
            _dest.appendChild(_itemes[_ni].previousSibling);
            _dest.appendChild(_itemes[_ni]);
        }
        _contelim.parentNode.removeChild(_contelim.previousSibling);
        _contelim.parentNode.removeChild(_contelim);
        nivelar();
        numerar();
	}
	
	function nivelar(){
        //actualiza el valor de nivel de cada item
        _itemes=document.querySelectorAll('#contenidoextenso > .hijos .item');
        for(_ni in _itemes){
            if(typeof _itemes[_ni]!='object'){continue;}
            _nn=parseInt(_itemes[_ni].parentNode.parentNode.getAttribute('nivel'))+1;
            _itemes[_ni].setAttribute('nivel',_nn);
        }
	}
	
	function mostrarResponsables(){
	    //muestra los responsables en cada item cargado
	    _itemes=document.querySelectorAll('#contenidoextenso > .hijos .item');
	    for(_ni in _itemes){
	
	        if(typeof _itemes[_ni]!='object'){continue;}
	
	         _idit=_itemes[_ni].getAttribute('idit');
	         _dat=_Items[_idit];
	         _itemhtml=document.querySelector('#contenidoextenso .item[idit="'+_idit+'"]');
	         _itemhtml.querySelector('#resp').innerHTML='';
	
	         _pck=document.createElement('div');
	         _pck.setAttribute('class','pack');
	         _itemhtml.querySelector('#resp').appendChild(_pck);
	         if(Object.keys(_dat.responsables).length==0){
	            _aaa=document.createElement('a');
	            _aaa.setAttribute('class','vacio');
	            _aaa.title='asignar responsables';
	            _aaa.setAttribute('onclick',"sumarResp(this)");
	            _aaa.innerHTML="+";
	            _pck.appendChild(_aaa);
	         }else{
	            _pckcount=0;
	             for(_nr in _dat.responsables){
	                _pckcount++;
	                if(_pckcount==3){
                        _pck=_pck.cloneNode(false);
                         _itemhtml.querySelector('#resp').appendChild(_pck);
                        _pckcount=0;
	                }
	                _aaa=document.createElement('a');
	                if(_UsuId==_dat.responsables[_nr].id_p_sis_usu_registro){
						_aaa.setAttribute('class','vos');
	                }
	                _aaa.title=_dat.responsables[_nr].nombre+' '+_dat.responsables[_nr].apellido+' \n '+_dat.responsables[_nr].responsabilidad;
	                _aaa.setAttribute('onclick',"sumarResp(this)");
	                _aaa.innerHTML=_dat.responsables[_nr].nombre.substring(0, 1)+_dat.responsables[_nr].apellido.substring(0, 1);
	                _pck.appendChild(_aaa);
	            }
	        }
	    }
	}
	
	function mostrarProgreso(){
	    //muestra el progreso en cada item cargado
	    _itemes=document.querySelectorAll('#contenidoextenso > .hijos .item');
	    for(_ni in _itemes){
	        if(typeof _itemes[_ni]!='object'){continue;}
	
	        _idit=_itemes[_ni].getAttribute('idit');
	        _dat=_Items[_idit];
	        _itemhtml=document.querySelector('#contenidoextenso .item[idit="'+_idit+'"]');
	
	        _itemhtml.querySelector('#estadoActividad').innerHTML='';
	        var divProgreso = _itemhtml.querySelector('#estadoActividad');
	        if(Object.keys(_dat.estados).length==0){
	            var porcentajeProgreso = 0;
	            var divProgresoPropio =document.createElement('div');
	            divProgresoPropio.setAttribute('class','barraProgresoPropio');
	            var divProgresoPropioBar =document.createElement('div');
	            divProgresoPropioBar.setAttribute('class','progressbar-container barraProgresoPropioBar');
	            divProgresoPropioBar.setAttribute('style','width:'+porcentajeProgreso+'%');                
	            divProgresoPropioBar.innerHTML = porcentajeProgreso + '%';
	            divProgresoPropio.appendChild(divProgresoPropioBar);
	            divProgreso.appendChild(divProgresoPropio);
	        } else {
	            var porcentajeProgreso = obtenerProgresoActividad(_idit);
	            var divProgresoPropio =document.createElement('div');
	            divProgresoPropio.setAttribute('class','barraProgresoPropio');
	            var divProgresoPropioBar =document.createElement('div');
	            divProgresoPropioBar.setAttribute('class','progressbar-container barraProgresoPropioBar');
	            divProgresoPropioBar.setAttribute('style','width:'+porcentajeProgreso+'%');                
	            divProgresoPropioBar.innerHTML = porcentajeProgreso + '%';
	            divProgresoPropio.appendChild(divProgresoPropioBar);
	            divProgreso.appendChild(divProgresoPropio);
	        }
	        
	        var actividadesHijas = obtenerItemActividadesHijas(_idit);
	        if (actividadesHijas && actividadesHijas.length > 0){
	            var porcentajeProgreso = calcularProgresoActividadNumerico(_idit,_Items);
	            var divProgresoHerencia =document.createElement('div');
	            divProgresoHerencia.setAttribute('class','barraProgresoHerencia');
	            var divProgresoHerenciaBar =document.createElement('div');
	            divProgresoHerenciaBar.setAttribute('class','progressbar-container barraProgresoHerenciaBar');
	            divProgresoHerenciaBar.setAttribute('style','width:'+porcentajeProgreso+'%');                
	            divProgresoHerenciaBar.innerHTML = porcentajeProgreso + '%';
	            divProgresoHerencia.appendChild(divProgresoHerenciaBar);
	            divProgreso.appendChild(divProgresoHerencia);
	        }
	    }
	}
	
	function calcularProgresoActividad(actividadId){
	    var progresoActividadTotal = calcularProgresoActividadNumerico(actividadId);
	    return "Progreso: " + progresoActividadTotal + '%';
	}
	
	function calcularProgresoActividadNumerico(actividadId){
	    //Esto asume que _Items es global y siempre es correcta
	    var progresoActividadTotal = parseInt(0);
	
	    //if no hijos -> devolver estado de actividad propio
	    //if si hijos -> calcularProgresoActividad (hijo) pesado
	    var actividadesHijas = obtenerItemActividadesHijas(actividadId);
	    if (actividadesHijas.length === 0){
	        var estadoProgreso = obtenerEstadoProgreso(actividadId);
	        if (!estadoProgreso){
	            progresoActividadTotal = parseInt(0);
	        } else {
	            progresoActividadTotal = estadoProgreso.porcentaje_progreso;
	        }
	    } else {
	        var progresoActividadesHijas = new Array();
	        for(actividad in actividadesHijas){
	            var progresoHijaNumerico = calcularProgresoActividadNumerico(actividadesHijas[actividad]);
	            progresoActividadesHijas.push(progresoHijaNumerico);
	        }
	
	        var cantidadHijas = progresoActividadesHijas.length;
	        for(progresoHija in progresoActividadesHijas){
	            progresoActividadTotal += (progresoActividadesHijas[progresoHija] / cantidadHijas);
	        }
	    }
	    return Math.round(progresoActividadTotal);
	}
	
	function obtenerProgresoActividad(actividadId){
	    var progresoActividad = 0;
	    var estadoProgreso = obtenerEstadoProgreso(actividadId);
	    if (estadoProgreso === null){ 
	        progresoActividad = parseInt(0);
	    } else {
	        progresoActividad = estadoProgreso.porcentaje_progreso;
	    }
	    
	    return progresoActividad;
	}
	
	function obtenerItemActividadesHijas(actividadId){
	    var actividadesHijas = new Array();
	    for(actividad in _Items){
	        if (_Items[actividad].id_p_sis_planif_plan == actividadId){
	            actividadesHijas.push(actividad);
	        }
	    }
	    return actividadesHijas;
	}
	
    function generarItemsHTML(){
        //genera un elemento html por cada instancia en el array _Items
        for(_nO in _Orden.psdir){

            _ni=_Orden.psdir[_nO];

            _dat=_Items[_ni];
            _clon=document.querySelector('#modelos .item').cloneNode(true);				
            _clon.setAttribute('idit',_dat.id);				
            if(_dat.nombre==null){_dat.nombre='- planificación sin nombre -';}

            _clon.querySelector('h3').innerHTML=_dat.nombre;
            if(_dat.descripcion==null){_dat.descripcion='- planificación sin descripción -';}
            _clon.querySelector('p').innerHTML=_dat.descripcion;
            _clon.setAttribute('nivel',"1");

            document.querySelector('#contenidoextenso > .hijos').appendChild(_clon);
        }

        //anida los itmes genreados unos dentro de otros
        for(_nO in _Orden.psdir){
            _ni=_Orden.psdir[_nO];
            _el=document.querySelector('#contenidoextenso > .hijos > .item[idit="'+_Items[_ni].id+'"]');

            if(_Items[_ni].id_p_sis_planif_plan!='0'){
                //alert(_Items[_ni].id_p_ESPitems_anidado);

                _dest=document.querySelector('#contenidoextenso > .hijos .item[idit="'+_Items[_ni].id_p_sis_planif_plan+'"] > .hijos');
                if(_dest==null){
                        _dest=document.querySelector('#contenidoextenso > .hijos');
                }
                _dest.appendChild(_el);
            }
        }

        _itemscargados=document.querySelectorAll('#contenidoextenso > .hijos .item');

        for(_nni in _itemscargados){
            if(typeof _itemscargados[_nni]=='object'){
                _esp=document.createElement('div');				
                _esp.setAttribute('class','medio');
                //_esp.innerHTML='<div class="submedio"></div>';
                _esp.setAttribute('ondragover',"allowDrop(event,this);resaltaHijos(event,this)");
                _esp.setAttribute('ondragleave',"desaltaHijos(this)");
                _esp.setAttribute('ondrop',"drop(event,this)");  
				
                //_itemscargados[_nni].parentNode.insertBefore(_esp, _itemscargados[_nni]);
                
                _itemscargados[_nni].appendChild(_esp);
                
                _esp=document.createElement('div');				
                _esp.setAttribute('class','medioA');
                //_esp.innerHTML='<div class="submedio"></div>';
                _esp.setAttribute('ondragover',"allowDrop(event,this);resaltaHijos(event,this)");
                _esp.setAttribute('ondragleave',"desaltaHijos(this)");
                _esp.setAttribute('ondrop',"drop(event,this)");  
				
                //_itemscargados[_nni].parentNode.insertBefore(_esp, _itemscargados[_nni]);
                
                _itemscargados[_nni].appendChild(_esp);
                
            }
        }

        numerar();
        mostrarResponsables();
        mostrarProgreso();
        ajustarTamanosItems();
    }
    
	function cerrar(_this){
		//cierra el formulario que lo contiene
		_this.parentNode.style.display='none';
	}

	function toogle(_elem){
	    _nombre=_elem.parentNode.parentNode.getAttribute('class');

	    elementos = document.getElementsByName(_nombre);
	    for (x=0;x<elementos.length;x++){			
			elementos[x].removeAttribute('checked');
		}
	    _elem.previousSibling.setAttribute('checked','checked');		
	}	
/////////////////////



/////////////////////
///funciones para mostrar documentos
////////////////////	
	function resolverDocumentosAsociados(accion, _this){
	    if (accion == ''){
	        alert('error asfocuefftygh');
	    } else if (accion == 'abrirEditorDocs') {
	        formEditorListaDocs(_this);
	    } else if (accion == 'actualizarDocumentosAsociados') {
	        listarDocumentosAsociados();
	    }
	}
	
	function formEditorListaDocs(_this){
	    console.log(_this);
	
	    var form = document.querySelector('#editorlistadocs');
	    form.querySelector('#excluidos').innerHTML='';
	    form.querySelector('#incluidos').innerHTML='';			
	    form.style.display='block';
	    form.querySelector('input[name="idit"]').value = _idit;
	    
    	
	    for(docItem in _DocItems){
	    	
	    	_carpedat=_DocItems[docItem];
    		
    		var carpeta = document.createElement('div');
            carpeta.setAttribute('class','CarpetaDocs');
            carpeta.setAttribute('tipoDoc', 'carpeta');
            carpeta.setAttribute('onclick','togleCarpAsoc(this)');
            carpeta.setAttribute('idcarp', _carpedat.id);
            var tituloCarpeta = document.createElement('span');
            tituloCarpeta.innerHTML = _carpedat.nombre;
            carpeta.appendChild(tituloCarpeta);
            
            _input=document.createElement('input');
            _input.setAttribute('id','asociacioncarp');
            _input.setAttribute('estado','ok');
            _input.setAttribute('onclick','event.stopPropagation()');
            _input.setAttribute('onkeypress','cargaCarpetaAsociada(event,this)');
            carpeta.appendChild(_input);
            
            
            form.querySelector('#excluidos').appendChild(carpeta);
            
             _clon=carpeta.cloneNode(true);
             form.querySelector('#incluidos').appendChild(_clon);
	    	
	        if ((_DocItems[docItem].archivos && Object.keys(_DocItems[docItem].archivos).length > 0)  ||
	            (_DocItems[docItem].archivolinks && Object.keys(_DocItems[docItem].archivolinks).length > 0)){
	            form.querySelector('#excluidos [idcarp="'+ _DocItems[docItem].id+'"]').setAttribute('contenido','si');
	        }
	        
	        
	        for (archivo in _DocItems[docItem].archivos){
	            if (_DocItems[docItem].archivos[archivo].zz_borrada == '0'){
	                _div = document.createElement('div');
	                _div.setAttribute('iddoc', _DocItems[docItem].archivos[archivo].id);
	                _div.setAttribute('tipoDoc', 'documento');
	                _div.setAttribute('estado','excluido');
	                _span = document.createElement('span');
	                _span.setAttribute('onclick','togleDocAsoc(this.parentNode)');
	                _span.innerHTML = _DocItems[docItem].archivos[archivo].nombre;
	                _div.appendChild(_span);
	
	                _input=document.createElement('input');
	                _input.setAttribute('id','asociacion');
	                _input.setAttribute('estado','ok');
	                _input.setAttribute('onkeypress','cargaDocumentosAsociados(event,this)');
	                _div.appendChild(_input);
	                
					$( "#editorlistadocs #excluidos [idcarp='"+_DocItems[docItem].id+"']" ).after( _div );
					
	                //form.querySelector('#excluidos').appendChild(_div);
	
	                _clon=_div.cloneNode(true);
	                form.querySelector('#incluidos').appendChild(_clon);
	            }
	        }
	        
	        for (archivoLink in _DocItems[docItem].archivolinks){
	            if (_DocItems[docItem].archivolinks[archivoLink].zz_borrada == '0'){
	                _div = document.createElement('div');
	                _div.setAttribute('iddoc', _DocItems[docItem].archivolinks[archivoLink].id);
	                _div.setAttribute('tipoDoc', 'url');
	                _div.setAttribute('estado','excluido');
	                _span = document.createElement('span');
	                _span.setAttribute('onclick','togleDocAsoc(this.parentNode)');
	                _span.innerHTML = _DocItems[docItem].archivolinks[archivoLink].nombre;
	                _div.appendChild(_span);
	
	                _input=document.createElement('input');
	                _input.setAttribute('id','asociacion');
	                _input.setAttribute('estado','ok');
	                _input.setAttribute('onkeypress','cargaDocumentosAsociados(event,this)');
	                _div.appendChild(_input);
	                form.querySelector('#excluidos').appendChild(_div);
	
	                _clon=_div.cloneNode(true);
	                form.querySelector('#incluidos').appendChild(_clon);
	            }
	        }
	    }
	    
	    for(docAsoc in _DocAsoc.archivos){
	        var iddoc = _DocAsoc.archivos[docAsoc].id_ref_01_documentos;
	        document.querySelector('#incluidos div[iddoc="'+iddoc+'"]').setAttribute('estado','incluido');
	        document.querySelector('#excluidos div[iddoc="'+iddoc+'"]').setAttribute('estado','incluido');
	
	        document.querySelector('#incluidos div[iddoc="'+iddoc+'"] input').value = _DocAsoc.archivos[docAsoc].comentario;
	        document.querySelector('#excluidos div[iddoc="'+iddoc+'"] input').value = _DocAsoc.archivos[docAsoc].comentario;
	    }
	    for(docAsoc in _DocAsoc.archivolinks){
	        var iddoc = _DocAsoc.archivolinks[docAsoc].id_ref_doc_links;
	        document.querySelector('#incluidos div[iddoc="'+iddoc+'"]').setAttribute('estado','incluido');
	        document.querySelector('#excluidos div[iddoc="'+iddoc+'"]').setAttribute('estado','incluido');
	
	        document.querySelector('#incluidos div[iddoc="'+iddoc+'"] input').value = _DocAsoc.archivolinks[docAsoc].comentario;
	        document.querySelector('#excluidos div[iddoc="'+iddoc+'"] input').value = _DocAsoc.archivolinks[docAsoc].comentario;
	    }
	    
	    for(_ni in _DocAsoc.carpetas){
	    	var _datcarp = _DocAsoc.carpetas[_ni];
	        document.querySelector('#incluidos div[idcarp="'+_datcarp.id_ref_02_pseudocarpetas+'"]').setAttribute('estado','incluido');
	        document.querySelector('#excluidos div[idcarp="'+_datcarp.id_ref_02_pseudocarpetas+'"]').setAttribute('estado','incluido');
	
	        document.querySelector('#incluidos div[idcarp="'+_datcarp.id_ref_02_pseudocarpetas+'"] input').value = _datcarp.comentario;
	        document.querySelector('#excluidos div[idcarp="'+_datcarp.id_ref_02_pseudocarpetas+'"] input').value = _datcarp.comentario;
	    }
	}
	
	function cargaCarpetaAsociada(_event,_this){
        //console.log(_event.keyCode);
        if(_event.keyCode==9){return;}//tab
	    if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
	    _this.setAttribute('estado','editando');
	    if(_event.keyCode==13){
			_this.setAttribute('estado','guardando');
			_idcarp=_this.parentNode.getAttribute('idcarp');
			var tipoDoc=_this.parentNode.getAttribute('tipoDoc');
			_idit=_this.parentNode.parentNode.parentNode.querySelector('input[name="idit"]').value;
            _nuevoestado='incluido';
         	var comentario=_this.value;
			guardarCambiosCarpeta(tipoDoc,_idcarp,_idit,_nuevoestado,comentario);
	     }
	}	
		
	function togleCarpAsoc(_this){
	    var _nuevoestado;
	    if(_this.parentNode.getAttribute('id')=='incluidos'){
	        _nuevoestado='excluido';
	    }else{
	        _nuevoestado='incluido';
	    }
	    _this.parentNode.parentNode.querySelector('#excluidos div[idcarp="'+_this.getAttribute('idcarp')+'"]').setAttribute('estado',_nuevoestado);
	    _this.parentNode.parentNode.querySelector('#incluidos div[idcarp="'+_this.getAttribute('idcarp')+'"]').setAttribute('estado',_nuevoestado);
	
	    _idit=_this.parentNode.parentNode.querySelector('input[name="idit"]').value;
	    var comentario=_this.parentNode.parentNode.querySelector('#incluidos div[idcarp="'+_this.getAttribute('idcarp')+'"] input').value;
	
	    guardarCambiosCarpeta(_this.getAttribute('tipoDoc'), _this.getAttribute('idcarp'), _idit, _nuevoestado, comentario);
	}
	

	function cargaDocumentosAsociados(_event,_this){
        //console.log(_event.keyCode);
        if(_event.keyCode==9){return;}//tab
	    if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
	    _this.setAttribute('estado','editando');
	    if(_event.keyCode==13){
			_this.setAttribute('estado','guardando');
			_iddoc=_this.parentNode.getAttribute('iddoc');
			var tipoDoc=_this.parentNode.getAttribute('tipoDoc');
			_idit=_this.parentNode.parentNode.parentNode.querySelector('input[name="idit"]').value;
            _nuevoestado='incluido';
         	var comentario=_this.value;
			guardarCambiosDocs(tipoDoc,_iddoc,_idit,_nuevoestado,comentario);
	     }
	}	
		
	function togleDocAsoc(_this){
	    var _nuevoestado;
	    if(_this.parentNode.getAttribute('id')=='incluidos'){
	        _nuevoestado='excluido';
	    }else{
	        _nuevoestado='incluido';
	    }
	    _this.parentNode.parentNode.querySelector('#excluidos div[iddoc="'+_this.getAttribute('iddoc')+'"]').setAttribute('estado',_nuevoestado);
	    _this.parentNode.parentNode.querySelector('#incluidos div[iddoc="'+_this.getAttribute('iddoc')+'"]').setAttribute('estado',_nuevoestado);
	
	    _idit=_this.parentNode.parentNode.querySelector('input[name="idit"]').value;
	    var comentario=_this.parentNode.parentNode.querySelector('#incluidos div[iddoc="'+_this.getAttribute('iddoc')+'"] input').value;
	
	    guardarCambiosDocs(_this.getAttribute('tipoDoc'), _this.getAttribute('iddoc'), _idit, _nuevoestado, comentario);
	}
///////////////////////


/////////////////////
///funciones para mostrar responsables
////////////////////	
	function formResponsables(_this,_usuarios){
                //abre el formulario para edittar item
                console.log(_this);

                _idit=_this.parentNode.parentNode.parentNode.parentNode.getAttribute('idit');
                _num=document.querySelector('#contenidoextenso .item[idit="'+_idit+'"] #num').innerHTML;
                _nom=document.querySelector('#contenidoextenso .item[idit="'+_idit+'"] h3').innerHTML;

                _form=document.querySelector('#editorresp');
                _form.querySelector('#excluidos').innerHTML='';
                _form.querySelector('#incluidos').innerHTML='';			
                _form.style.display='block';
                _form.querySelector('input[name="idit"]').value=_idit;			
                _form.querySelector('h1').innerHTML=_num+' '+_nom;	
                for(_nu in _usuarios.data){
                        _div=document.createElement('div');
                        _div.setAttribute('idusu',_usuarios.data[_nu].id);
                        _div.setAttribute('estado','excluido');
                        _span=document.createElement('span');
                        _span.setAttribute('onclick','togleResp(this.parentNode)');
                        _span.innerHTML=_usuarios.data[_nu].apellido+', '+_usuarios.data[_nu].nombre+' <span id="log">'+_usuarios.data[_nu].log+'</span>';
                        _div.appendChild(_span);

                        _input=document.createElement('input');
                        _input.setAttribute('id','responsabilidad');
                        _input.setAttribute('estado','ok');
                        _input.setAttribute('onkeypress','cargaResponsabilidad(event,this)');
                        _div.appendChild(_input);	

                        _form.querySelector('#excluidos').appendChild(_div);

                        _clon=_div.cloneNode(true);
                        _form.querySelector('#incluidos').appendChild(_clon);
                }	

                cargarResponsablesenForm(_idit);
	}

	
	function togleResp(_this,_usuarios){
		
		if(_this.parentNode.getAttribute('id')=='incluidos'){
			_nuevoestado='excluido';
		}else{
			_nuevoestado='incluido';
		}
		_this.parentNode.parentNode.querySelector('#excluidos div[idusu="'+_this.getAttribute('idusu')+'"]').setAttribute('estado',_nuevoestado);
		_this.parentNode.parentNode.querySelector('#incluidos div[idusu="'+_this.getAttribute('idusu')+'"]').setAttribute('estado',_nuevoestado);
		
		_idit=_this.parentNode.parentNode.querySelector('input[name="idit"]').value;
		_responsabilidad=_this.parentNode.parentNode.querySelector('#incluidos div[idusu="'+_this.getAttribute('idusu')+'"] input').value;
		
		guardarCambiosResp(_this.getAttribute('idusu'),_idit,_nuevoestado,_responsabilidad);
	}
	

	function cargaResponsabilidad(_event,_this){
		console.log(_event.keyCode);
		if(_event.keyCode==9){return;}//tab
		if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
		_this.setAttribute('estado','editando');
		if(_event.keyCode==13){
			_this.setAttribute('estado','guardando');
			_idusu=_this.parentNode.getAttribute('idusu');
			_idit=_this.parentNode.parentNode.parentNode.querySelector('input[name="idit"]').value;
			_nuevoestado='incluido';
			_responsabilidad=_this.value;
			
			guardarCambiosResp(_idusu,_idit,_nuevoestado,_responsabilidad);
			
			
		}
	}
/////////////////////	

/////////////////////
///funciones para mostrar items
////////////////////	
		function resaltar(_this){
			//realta el div del item al que pertenese un título o una descripcion
			
			_dests=document.querySelectorAll('[resaltado="si"]');
			for(_nn in _dests){
				if(typeof _dests[_nn]=='object'){
					_dests[_nn].removeAttribute('resaltado');
				}
			}
			_this.parentNode.setAttribute('resaltado','si');
			
		}
		function desaltar(_this){
			//realta el div del item al que pertenese un título o una descripcion
			_dests=document.querySelectorAll('[resaltado="si"]');
			for(_nn in _dests){
				if(typeof _dests[_nn]=='object'){
					_dests[_nn].removeAttribute('resaltado');
				}
			}
			
		}
		function editarI(_this){
                    //abre el formulario para editar item
                    _idit=_this.parentNode.getAttribute('idit');
                    _form=document.querySelector('#editoritem');
                    _form.style.display='block';
                    _form.querySelector('input[name="nombre"]').value=_Items[_idit].nombre;
                    _form.querySelector('input[name="id"]').value=_Items[_idit].id;
                    _form.querySelector('[name="descripcion"]').value=_Items[_idit].descripcion;
                    var estadoMasNuevo = obtenerEstadoProgreso(_idit);
                    if (estadoMasNuevo !== null){
                        porcentajeProgreso = estadoMasNuevo.porcentaje_progreso;
                        _form.querySelector('input[name="progresoNumber"]').value = porcentajeProgreso;
                        _form.querySelector('input[name="progresoRange"]').value = porcentajeProgreso;
                        _form.querySelector('.autorCambioItem').innerText = estadoMasNuevo.nombre + " " + estadoMasNuevo.apellido;
                        _form.querySelector('input[name="fechaPropuesta"]').value = estadoMasNuevo.fecha_propuesta;
                    } else {
                        _form.querySelector('input[name="progresoNumber"]').value = 0;
                        _form.querySelector('input[name="progresoRange"]').value = 0;
                        _form.querySelector('.autorCambioItem').innerText = " ";
                        _form.querySelector('input[name="fechaPropuesta"]').value = null;
                    }

                    consultarDocumentosAsociados('actualizarDocumentosAsociados', _this)
		}
                
        function listarDocumentosAsociados(){
            var listaDocumentosAsociados = _form.querySelector('.listaDocumentosAsociados');
            listaDocumentosAsociados.innerHTML = '';
            for (var asoc in _DocAsoc.archivos) {
                var archivoLista = document.createElement('a');
                
                _e=_DocAsoc.archivos[asoc].nombre.split('.');
                _ext=_e[(_e.length-1)];
                
                _img='file_desconocido.png';
                if(_ext.toLowerCase()=='png'){_img='file_img.png';}
                if(_ext.toLowerCase()=='jpg'){_img='file_img.png';}
                if(_ext.toLowerCase()=='jpeg'){_img='file_img.png';}
                if(_ext.toLowerCase()=='bmp'){_img='file_img.png';}
                if(_ext.toLowerCase()=='gif'){_img='file_img.png';}
                if(_ext.toLowerCase()=='tif'){_img='file_img.png';}
                if(_ext.toLowerCase()=='tiff'){_img='file_img.png';}
                if(_ext.toLowerCase()=='txt'){_img='file_tx.png';}
                if(_ext.toLowerCase()=='rtf'){_img='file_tx.png';}
                if(_ext.toLowerCase()=='doc'){_img='file_tx.png';}
                if(_ext.toLowerCase()=='docx'){_img='file_tx.png';}
                if(_ext.toLowerCase()=='odt'){_img='file_tx.png';}
                if(_ext.toLowerCase()=='xls'){_img='file_calc.png';}
                if(_ext.toLowerCase()=='xlsx'){_img='file_calc.png';}
                if(_ext.toLowerCase()=='ods'){_img='file_calc.png';}
                if(_ext.toLowerCase()=='pdf'){_img='file_pdf.png';}
                if(_ext.toLowerCase()=='dwg'){_img='file_cad.png';}
                if(_ext.toLowerCase()=='dxf'){_img='file_cad.png';}
                
                archivoLista.setAttribute('class','archivoLista has-tooltip');
                archivoLista.innerHTML = '<span id="img"><img src="./comun_img/'+_img+'"></span>';
                
                archivoLista.innerHTML +='<span id="nombre">'+_DocAsoc.archivos[asoc].nombre+'</span>';
                
                if(_DocAsoc.archivos[asoc].comentario==null){
                	_com='- sin comentarios -';
                }else{
                	_com=_DocAsoc.archivos[asoc].comentario;
                }
                archivoLista.innerHTML +='<span id="comentario">'+_com+'</span>';
                
                archivoLista.setAttribute('href',_DocAsoc.archivos[asoc].archivo);
                archivoLista.setAttribute('download', _DocAsoc.archivos[asoc].nombre);
                if (_DocAsoc.archivos[asoc].comentario && _DocAsoc.archivos[asoc].comentario != ''){
                    archivoLista.setAttribute('data-tooltip', _DocAsoc.archivos[asoc].nombre+': '+_DocAsoc.archivos[asoc].comentario);
                }
                listaDocumentosAsociados.appendChild(archivoLista);
            }
            for (var asoc in _DocAsoc.archivolinks) {
                var archivoLista = document.createElement('a');
                archivoLista.setAttribute('class','archivoLista has-tooltip');
                _img='file_link.png';
                archivoLista.innerHTML = '<span id="img"><img src="./comun_img/'+_img+'"></span>';
                
                archivoLista.innerHTML += '<span id="nombre">'+_DocAsoc.archivolinks[asoc].nombre+'</span>';
                
                if(_DocAsoc.archivolinks[asoc].comentario==null){
                	_com='- sin comentarios -';
                }else{
                	_com=_DocAsoc.archivolinks[asoc].comentario;
                }
                archivoLista.innerHTML +='<span id="comentario">'+_com+'</span>';
                
                archivoLista.setAttribute('href',_DocAsoc.archivolinks[asoc].url);
                archivoLista.setAttribute('download', _DocAsoc.archivolinks[asoc].nombre);
                if (_DocAsoc.archivolinks[asoc].comentario && _DocAsoc.archivolinks[asoc].comentario != ''){
                    archivoLista.setAttribute('data-tooltip', _DocAsoc.archivolinks[asoc].comentario);
                }
                listaDocumentosAsociados.appendChild(archivoLista);
            }
            for (var asoc in _DocAsoc.carpetas) {
                var archivoLista = document.createElement('a');
                archivoLista.setAttribute('class','archivoLista has-tooltip');
                
                
                _img='file_carpeta.png';
                archivoLista.innerHTML = '<span id="img"><img src="./comun_img/'+_img+'"></span>';
                
                archivoLista.innerHTML += '<span id="nombre">'+_DocAsoc.carpetas[asoc].nombre+'</span>';
                
                if(_DocAsoc.carpetas[asoc].comentario==null){
                	_com='- sin comentarios -';
                }else{
                	_com=_DocAsoc.carpetas[asoc].comentario;
                }
                archivoLista.innerHTML +='<span id="comentario">'+_com+'</span>';
                
                
                
                _url='./app_docs.php?cod='+_CodMarco+'&idpseudocarpeta='+_DocAsoc.carpetas[asoc].id_ref_02_pseudocarpetas;
                archivoLista.setAttribute('href',_url);
                archivoLista.setAttribute('target','_blank');
                if (_DocAsoc.carpetas[asoc].comentario && _DocAsoc.carpetas[asoc].comentario != ''){
                    archivoLista.setAttribute('data-tooltip', _DocAsoc.carpetas[asoc].comentario);
                }
                listaDocumentosAsociados.appendChild(archivoLista);
            }
        }
        
        function obtenerEstadoProgreso(idit){
                var estadoMasNuevo = null;
                if ((idit in _Items) && (_Items[idit].estados !== null)){
                    var fecha_cambio = new Date();
                    fecha_cambio.setYear(1);
                    for (estadoId in _Items[idit].estados){
                        var estadoFecha = new Date(_Items[idit].estados[estadoId].fecha_cambio);
                        if (estadoFecha.getTime() > fecha_cambio.getTime()){
                            fecha_cambio = estadoFecha;
                            estadoMasNuevo = _Items[idit].estados[estadoId];
                        }
                    }
                }
                
                return estadoMasNuevo;
        }


		
		function numerar(){
			_oitems=document.querySelectorAll('#contenidoextenso .item');
			_nivel=0;
			_Num=Array();
			
			_ultNiv=0;
			
			//define el nivel de anidamietno de cada item
			for(_ni in _oitems){				
				if(typeof _oitems[_ni] != 'object'){continue;}				
				_padre=_oitems[_ni].parentNode.parentNode;
				_nivelP=_padre.getAttribute('nivel');
				_saltos=1;
				while(_nivelP==undefined){
					_saltos++;
					_padre=_padre.parentNode.parentNode;
					_nivelP=_padre.getAttribute('nivel');
					if(_saltos>10){break;}
				}	
				_nivel= parseInt(_nivelP)+_saltos;				
				//alert(_nivel);
				_oitems[_ni].setAttribute('nivel',_nivel);
				delete _nivelP; 
			}
			
			for(_ni in _oitems){
				if(typeof _oitems[_ni] != 'object'){continue;}
												
				_nivel= _oitems[_ni].getAttribute('nivel');
				//alert(_nivel);
				if(_nivel>_ultNiv){_Num[_nivel]=0;};
				_ultNiv=_nivel;
				//if(_Num[_nivel]==undefined){_Num[_nivel]=0;}
				_Num[_nivel]++;
				_oitems[_ni].setAttribute('num',_Num[_nivel]);
				_nivstr='';
				_nivstr=_Num[_nivel];
				_nn=_nivel-1;
				while(_nn>0){
					_nivstr=_Num[_nn]+'.'+_nivstr;
					_nn--;
				}
				_oitems[_ni].querySelector('#num').innerHTML=_nivstr;
			}

		}
			
		function numerarB(){
			_cont=document.querySelector('#contenidoextenso > .hijos');
			_cc=0;
			_nivel=0;
			_Num=Array();
			_Num[_nivel]=0;
			
			explorarSubitem(_cont,_nivel);
		}
		
		function explorarSubitem(_cont,_denivel){
			_nivel=_denivel+1;
			_oitems=_cont.querySelectorAll('.item[nivel="'+_nivel+'"]');
			
			if(_Num[_nivel]==undefined){_Num[_nivel]=0;}
			console.log(_oitems);
			for(_ni in _oitems){
				
				if(typeof _oitems[_ni] != 'object'){continue;}
				_Num[_nivel]++;
				alert(_nivel+" : "+_Num[_nivel]);
				_oitems[_ni].setAttribute('num',_Num[_nivel]);
				
				_nivstr='';
				_nivstr=_Num[_nivel];
				_nn=_nivel-1;
				while(_nn>0){
					_nivstr=_Num[_nn]+'.'+_nivstr;
					_nn--;
				}
				_oitems[_ni].querySelector('#num').innerHTML=_nivstr;
				
				_hijos=_oitems[_ni].querySelector('.hijos');
				console.log(_nivel);
				alert('hola');
				if(_nivel==2){continue;}
				explorarSubitem(_hijos,_nivel);
				
			}
		}
		
		function ajustarTamanosItems(){
			_oitems=document.querySelectorAll('#contenidoextenso .item');
			_nivel=0;
			_Num=Array();
			
			_ultNiv=0;
			
			_n1porrenglo=2;			
			
			for(_ni in _oitems){
				if(typeof _oitems[_ni] != 'object'){continue;}
											
				_nivel= _oitems[_ni].getAttribute('nivel');
				//alert(_nivel);
				if(_nivel>_ultNiv){_Num[_nivel]=0;};
				_ultNiv=_nivel;
				
				if(_nivel>=parseInt(2)){					
					
					//alert(_oitems[_ni].clientHeight);
					if(_oitems[_ni].clientHeight>150){
						//_oitems[_ni].previousSibling.setAttribute('representacion','entero');
						_oitems[_ni].setAttribute('representacion','entero');
						//_prev=_oitems[_ni].previousSibling;
						//if(typeof _prev != 'object'){_prev=_prev.previousSibling;}
						//_prev.setAttribute('representacion','entero');
					}else{
						_oitems[_ni].setAttribute('representacion','normal');
					}
					
				}
				
			}
		}
		
/////////////////////	

/////////////////////
///funciones para interaccion drag y drop de archivos
////////////////////		
	function dragFile(_event){
		//alert(_event.target.getAttribute('idit'));
		_event.stopPropagation();
		_arr=Array();
		_arr={
			'id':_event.target.getAttribute('idfi'),
			'tipo':'archivo'
		};
		_arb = JSON.stringify(_arr);
		_event.dataTransfer.setData("text", _arb);
	}
	
	function allowDropFile(_event,_this){
		_event.stopPropagation();
		//console.log(_this.parentNode.getAttribute('idit'));
		//console.log(_event.dataTransfer);
		if(_event.dataTransfer.items[0].kind=='file'){return;}
		if(_event.dataTransfer.getData("text")!=''){
			if(JSON.parse(_event.dataTransfer.getData("text")).tipo!='archivo'){
				return;
			}
		}
		
		limpiarAllowFile();
		_event.stopPropagation();
		_this.setAttribute('destinof','si');
		_event.preventDefault();
	}
	
	function limpiarAllowFile(){
		_dests=document.querySelectorAll('[destinof="si"]');
		for(_nn in _dests){
			if(typeof _dests[_nn]=='object'){
				_dests[_nn].removeAttribute('destinof');
			}
		}
	}
            
	function dropFile(_event,_this){// ajustado a geogec
        _event.stopPropagation();
        _event.preventDefault();

        if(_event.dataTransfer.getData("text")!=''){		
            if(JSON.parse(_event.dataTransfer.getData("text")).tipo!='archivo'){
            	return;
			}
        }

	    var _DragData = JSON.parse(_event.dataTransfer.getData("text")).id;
	    
	    _el=document.querySelector('.archivo[idfi="'+_DragData+'"]');
	    
	    //console.log(_DragData);
	   
	    if(_event.target.getAttribute('class')=='hijos'){	
	    	_tar=_event.target;
	    	 _idit=_this.parentNode.getAttribute('idit');
	    	_dest=_tar.parentNode.querySelector('.item[idit="'+_idit+'"] .documentos'); 
	    }else{
	    	 _idit=_this.getAttribute('idit');
	    	 _ViejoIdIt=_el.parentNode.parentNode.getAttribute('idfi');
	    	_dest=document.querySelector('.item[idit="'+_idit+'"] .documentos');
	    }
	    
	    _dest.appendChild(_el);
	    		    			    
	    _parametros={
	    	"idMarco":_IdMarco,
	    	'codMarco': _CodMarco,
	    	"id":_DragData,
	    	"id_anidadoen":_idit
	    };
	    
 		$.ajax({
			url:   './app_plan/app_plan_localizararchivo.php',
			type:  'post',
			data: _parametros,
			success:  function (response){
				var _res = $.parseJSON(response);
					console.log(_res);
				if(_res.res=='exito'){	
					cargaBase();
				}else{
					alert('error asdfdsf');
				}
			}
		});
	    
	  }
/////////////////////	



/////////////////////
///funciones para interaccion drag y drop de items
////////////////////		  
	function allowDrop(_event,_this){
		//console.log(_this.parentNode.getAttribute('idit'));
		
		console.log(_event.dataTransfer);
		
		if(JSON.parse(_event.dataTransfer.getData("text")).tipo!='item'){
			return;
		}
		
		limpiarAllow();
		
		_event.stopPropagation();
		_this.setAttribute('destino','si');
		_event.preventDefault();
		
	}
	
	function limpiarAllow(){
		_dests=document.querySelectorAll('[destino="si"]');
		for(_nn in _dests){
			if(typeof _dests[_nn]=='object'){
				_dests[_nn].removeAttribute('destino');
			}
		}
	}
	
	function resaltaHijos(_event,_this){
		//realta el div del item al que pertenese un título o una descripcion
		//_this.style.backgroundColor='lightblue';
		_dests=document.querySelectorAll('[destino="si"]');
		for(_nn in _dests){
			if(typeof _dests[_nn]=='object'){
				_dests[_nn].removeAttribute('destino');
			}
		}
		_this.setAttribute('destino','si');
		_event.stopPropagation();
		
	}
	function desaltaHijos(_this){
		//realta el div del item al que pertenese un título o una descripcion
		//_this.style.backgroundColor='#fff';
		_this.removeAttribute('destino');
		_this.parentNode.removeAttribute('destino');
	}
	
	
	function dragcaja(_event){			
		//alert(_event.target.getAttribute('idit'));
		_arr=Array();
		_arr={
			'id':_event.target.getAttribute('idit'),
			'tipo':'item'
		};		
		_arb = JSON.stringify(_arr);

		_event.dataTransfer.setData("text", _arb);
	}
	
	function bloquearhijos(_event,_this){			
		_idit=JSON.parse(_event.dataTransfer.getData("text")).id;
		_negados = _this.querySelectorAll('.item[idit="'+_idit+'"] .hijos, .item[idit="'+_idit+'"] .medio');   
		 		
		for(_nn in _negados){
			if(typeof _negados[_nn] == 'object'){
				_negados[_nn].setAttribute('destino','negado');
			}
		}
	}
	
	function desbloquearhijos(_this){
		_negados=document.querySelectorAll('[destino="negado"]');
		for(_nn in _negados){
			if(typeof _negados[_nn] == 'object'){
				_negados[_nn].removeAttribute('destino');
			}
		}
	}	
	
		
	function drop(_event,_this){//ajustado a geogec	
        _event.stopPropagation();
        _this.removeAttribute('style');
        _this.removeAttribute('destino');

        _event.preventDefault();

		if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='archivo'){
			dropFile(_event,_this);
			return;
		}
		
	    var _DragData = JSON.parse(_event.dataTransfer.getData("text")).id;
	    console.log(_event.dataTransfer.getData("text"));
	    
	    _el=document.querySelector('.item[idit="'+_DragData+'"]');
	    _ViejoIdIt=_el.parentNode.parentNode.getAttribute('idit');
	   
	        
	    _evitar='no';//evita destinos erronos por jerarquia.

	    
	    if(_event.target.getAttribute('class')=='medio'||_event.target.getAttribute('class')=='medioA'){
	    	_tar=_event.target.parentNode.previousSibling;
		    	
		    //console.log(_event.target.parentNode.previousSibling);
	    
		    _dest=_tar.parentNode; 
			_dest.insertBefore(_el,_tar);			    
	
		    
		    
	    }else if(_event.target.getAttribute('class')=='hijos'){
	    	
	   		_dest=_event.target;

		   	_dest.appendChild(_el);
	    	
	    	
	    }else{
	    	alert('destino inesperado');
	    	return;
	    	
	    }
	  
	    _niv=_el.parentNode.parentNode.getAttribute('nivel');
	    _niv++;
	    _el.setAttribute('nivel',_niv.toString());
	    		    
	    _NuevoIdIt=_dest.parentNode.getAttribute('idit');
	    
	    _enviejo=document.querySelectorAll('[idit="'+_ViejoIdIt+'"] > .hijos > .item');
	    _serieviejo='';
	    for(_ni in _enviejo){
	    	if(typeof _enviejo[_ni]=='object'){
	    		_serieviejo+=_enviejo[_ni].getAttribute('idit')+',';
	    	}
	    }
	    
	    console.log(_NuevoIdIt);
	    _ennuevo=document.querySelectorAll('[idit="'+_NuevoIdIt+'"] > .hijos > .item');
	    _serienuevo='';
	    for(_ni in _ennuevo){
	    	console.log(_ennuevo[_ni]);
	    	if(typeof _ennuevo[_ni]=='object'){
	    		_serienuevo+=_ennuevo[_ni].getAttribute('idit')+',';
	    	}
	    }
	   
	    _parametros={
	    	"idMarco":_IdMarco,
	    	"codMarco":_CodMarco,
	    	"id":_DragData,
	    	"id_anidado":_NuevoIdIt,
	    	"viejoAnidado":_ViejoIdIt,
	    	"viejoAserie":_serieviejo,
	    	"nuevoAnidado":_NuevoIdIt,
	    	"nuevoAserie":_serienuevo
	    };
	    
 		$.ajax({
			url:   './app_plan/app_plan_anidaritem.php',
			type:  'post',
			data: _parametros,
			success:  function (response){
				var _res = $.parseJSON(response);
					console.log(_res);
				if(_res.res=='exito'){	
					//cargaBase();
					nivelar();
					numerar();
				}else{
					alert('error asfffgh');
				}
			}
		});
		//envía los datos para editar el ítem
        cargaBase('actualizarprogreso');
	}
/////////////////////	
	
