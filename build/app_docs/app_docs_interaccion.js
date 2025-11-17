    ///funciones para editar links	
    function editarLink(_event,_this){		
            //abre el formulario para editar link
            _event.preventDefault();
            _event.stopPropagation();
            _iddoc=_this.getAttribute('idfi');
            _form=document.querySelector('#editarlink');
            _form.style.display='block';
                        
            _form.querySelector('#nombre').innerHTML=_DocLinks[_iddoc].nombre;
            _form.querySelector('#linkUrl').innerHTML=_DocLinks[_iddoc].url;
            _form.querySelector('input[name="id"]').value=_DocLinks[_iddoc].id;
            _form.querySelector('[name="descripcion"]').value=_DocLinks[_iddoc].descripcion;
            
            _form.querySelector('#botondescarga').setAttribute('href',_DocLinks[_iddoc].url);
          
    }
    
    
    



    ///funciones para editar documentos	
    function editarD(_event,_this){	
        //abre el formulario para edittar item
        _event.preventDefault();
        _event.stopPropagation();
        _iddoc=_this.getAttribute('idfi');
        _form=document.querySelector('#editordoc');
        _form.style.display='block';
        _form.querySelector('#nombre').innerHTML=_Docs[_iddoc].nombre;
        
        if(_Docs[_iddoc].unombre==null&&_Docs[_iddoc].uapellido==null){
        	_n='desconocido';_a='';
        }else{
            if(_Docs[_iddoc].unombre==null){_n='';}else{_n=_Docs[_iddoc].unombre}
            if(_Docs[_iddoc].uapellido==null){_a='';}else{_a=_Docs[_iddoc].uapellido}
        }
        _form.querySelector('#autor #nombreapellido').innerHTML=_a+' '+_n;
        _form.querySelector('input[name="id"]').value=_Docs[_iddoc].id;
        _form.querySelector('[name="descripcion"]').value=_Docs[_iddoc].descripcion;
        
        _form.querySelector('#botondescarga').setAttribute('href',_Docs[_iddoc].archivo);
        _form.querySelector('#botondescarga').setAttribute('download',_Docs[_iddoc].nombre);
    }


///funciones para editar y crear items

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
            //abre el formulario para edittar item
            _idit=_this.parentNode.getAttribute('idit');
            _form=document.querySelector('#editoritem');
            _form.style.display='block';
            _form.querySelector('input[name="nombre"]').value=_Items[_idit].nombre;
            _form.querySelector('input[name="id"]').value=_Items[_idit].id;
            _form.querySelector('[name="descripcion"]').value=_Items[_idit].descripcion;
            
            
            _archivos=document.querySelectorAll('#contenidoextenso .item[idit="'+_idit+'"] > .documentos > a');
            
            _form.querySelector('#contenidos').innerHTML='';
            for(_na in _archivos){
            	console.log(_archivos[_na]);
            	if(typeof _archivos[_na] != 'object'){continue;}
            	_clon=_archivos[_na].cloneNode(true);
            	_form.querySelector('#contenidos').appendChild(_clon);
            }
            
            _form.querySelector('[name="publica"] option[value="'+_Items[_idit].publica+'"]').selected=true;
            
            
    }

    function cerrar(_this){
            //cierra el formulario que lo contiene
            _this.parentNode.style.display='none';
    }
    


  ///funciones para gestionar drag y drop de archivos

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
               
                
                /*
				console.log("v");
				console.log(typeof _event.dataTransfer.getData("text"));
				console.log("z");
				*/
                if(_event.dataTransfer.getData("text")
                    && JSON.parse(_event.dataTransfer.getData("text")).tipo
                    && (JSON.parse(_event.dataTransfer.getData("text")).tipo!='archivo') 
                    && (JSON.parse(_event.dataTransfer.getData("text")).tipo!='linkurl')){
                        return;
                }

				//console.log("y");
				
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

                if(_event.dataTransfer.getData("text")
                    && JSON.parse(_event.dataTransfer.getData("text")).tipo
                    && (JSON.parse(_event.dataTransfer.getData("text")).tipo!='archivo')
                    && (JSON.parse(_event.dataTransfer.getData("text")).tipo!='linkurl')){
                    return;
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

                if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='archivo'){
                    localizarArchivo(_parametros);
                } else if (JSON.parse(_event.dataTransfer.getData("text")).tipo=='linkurl'){
                    localizarLinkurl(_parametros);
                }
          }
          
          
          
          ///funciones para gestionar drag y drop de links

        function dragLinkurl(_event){
                //alert(_event.target.getAttribute('idit'));
                _event.stopPropagation();
                _arr=Array();
                _arr={
                        'id':_event.target.getAttribute('idfi'),
                        'tipo':'linkurl'
                    };
                _arb = JSON.stringify(_arr);
                _event.dataTransfer.setData("text", _arb);
        }
        
        
        
        
         ///funciones para gestjionar drag y drop de items

        function allowDrop(_event,_this){
                //console.log(_this.parentNode.getAttribute('idit'));

         //console.log(_event.dataTransfer);

		if(_event.dataTransfer.getData("text")!=''){
			if(JSON.parse(_event.dataTransfer.getData("text")).tipo!='item'){
				return;
			}
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
        
        
        
        function resDrFile(_event){
                //console.log(_event);
                document.querySelector('#archivos #contenedorlienzo').style.backgroundColor='lightblue';
        }	

        function desDrFile(_event){
                //console.log(_event);
                document.querySelector('#archivos #contenedorlienzo').removeAttribute('style');
        }
        
        
        
        function formcrearlink(_event,_this){				
            //abre el formulario para cargar link
            _event.preventDefault();
            _event.stopPropagation();
            _form=document.querySelector('#formcrearlink');
            _form.style.display='block';
            _form.querySelector('input[name="linkName"]').value=null;
            _form.querySelector('input[name="linkUrl"]').value=null;
        }

        function updateProgress(evt) {
            if (evt.lengthComputable) {
                var percentComplete = 100 * evt.loaded / evt.total;		   
                this.li.style.width="calc("+Math.round(percentComplete)+"% - ("+Math.round(percentComplete)/100+" * 6px))";
            } else {
                // Unable to compute progress information since the total size is unknown
            } 
        }
        


    function toogle(_elem){
        _nombre=_elem.parentNode.parentNode.getAttribute('class');

        elementos = document.getElementsByName(_nombre);
        for (x=0;x<elementos.length;x++){			
                    elementos[x].removeAttribute('checked');
            }
        _elem.previousSibling.setAttribute('checked','checked');		
    }