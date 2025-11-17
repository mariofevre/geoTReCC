function cargaBase(){
        document.querySelector('#contenidoextenso > .hijos').innerHTML='';			
        document.querySelector('#listadosubiendo').innerHTML='<label>archivos subiendo...</label>';
        document.querySelector('#listadoaordenar').innerHTML='<label>archivos subidos.</label>';

        _parametros = {
                'idMarco': _IdMarco,
                'codMarco': _CodMarco
        };

        _cn = consultasPHP_nueva('./app_docs/app_docs_consulta.php');  
        $.ajax({
                url:   './app_docs/app_docs_consulta.php',
                type:  'post',
                data: _parametros,

                beforeSend: function(request, settings){
                        request._data = {'cn':_cn};
                },
                error:  function (request, status, errorThrown){	
                        _cn = request._data.cn;
                        consultasPHP_respuesta("err",_cn);	
                },
                success:  function (response, status, request){			
                        var _res = $.parseJSON(response);            
                        _cn = request._data.cn;			
                        consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
                        if(_res.res!='exito'){return;}   
                        	
                        _Items=_res.data.psdir;
                        _Orden=_res.data.orden;

                        generarItemsHTML();		
                        generarArchivosHTML();
                        generarArchivoLinksHTML();
                                
                        //finalizada la carga inicial de datos
                        
                        _idpseudocarpeta=getParameterByName('idpseudocarpeta');
                        if(_idpseudocarpeta != ''){
                                if(_idpseudocarpeta>0){
                                        _caja=document.querySelector('#contenidoextenso .item[idit="'+_idpseudocarpeta+'"] > h3');
                                        if(_caja==null){
                                                alert('No hemos podido localizar la carpeta que está buscando. por favor verifique el link utilizado');
                                                return;
                                        }
                                        editarI(_caja);
                                }
                        }
                }
        });
}
    
function cargaBasePublica(){
        document.querySelector('#contenidoextenso > .hijos').innerHTML='';			
        document.querySelector('#listadosubiendo').innerHTML='<label>archivos subiendo...</label>';
        document.querySelector('#listadoaordenar').innerHTML='<label>archivos subidos.</label>';

        _parametros = {
                'idMarco': _IdMarco,
                'codMarco': _CodMarco
        };

        _cn = consultasPHP_nueva('./app_docs/app_docs_consulta_publica.php');  
        $.ajax({
                url:   './app_docs/app_docs_consulta_publica.php',
                type:  'post',
                data: _parametros,
                
                beforeSend: function(request, settings){
			request._data = {'cn':_cn};
		},
		error:  function (request, status, errorThrown){	
			_cn = request._data.cn;
			consultasPHP_respuesta("err",_cn);	
		},
		success:  function (response, status, request){			
			var _res = $.parseJSON(response);            
			_cn = request._data.cn;			
			consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
			if(_res.res!='exito'){return;}
                     	
                        _Items=_res.data.psdir;
                        _Orden=_res.data.orden;

                        generarItemsHTML();		
                        generarArchivosHTML();
                        generarArchivoLinksHTML();
                          	
                        _idpseudocarpeta=getParameterByName('idpseudocarpeta');
                        if(_idpseudocarpeta != ''){
                                if(_idpseudocarpeta>0){
                                        _caja=document.querySelector('#contenidoextenso .item[idit="'+_idpseudocarpeta+'"] > h3');
                                        if(_caja==null){
                                                alert('No hemos podido localizar la carpeta que está buscando. por favor verifique el link utilizado');
                                                return;
                                        }
                                        editarI(_caja);
                                }
                        }
                }
        });
}  
    
function guardarLink(_event,_this){
        _event.preventDefault();
        console.log(_this);
        var _this=_this;
        _parametros = {
                "idMarco":_IdMarco,
                'codMarco': _CodMarco,
                "id": _this.querySelector('input[name="id"]').value,
                "descripcion": _this.querySelector('[name="descripcion"]').value
        };

        _cn = consultasPHP_nueva('./app_docs/app_docs_cambiarlink.php');
        $.ajax({
                url:   './app_docs/app_docs_cambiarlink.php',
                type:  'post',
                data: _parametros,

                beforeSend: function(request, settings){
			request._data = {'cn':_cn};
		},
		error:  function (request, status, errorThrown){	
			_cn = request._data.cn;
			consultasPHP_respuesta("err",_cn);	
		},
		success:  function (response, status, request){			
			var _res = $.parseJSON(response);            
			_cn = request._data.cn;			
			consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
			if(_res.res!='exito'){return;}  

                        cerrar(_this.querySelector('#botoncierra'));
                        cargaBase();
                }
        });
        //envía los datos para editar el ítem
    }
	
function guardarD(_event,_this){// ajustado geogec
        _event.preventDefault();
        var _this=_this;
        _parametros = {
                "idMarco":_IdMarco,
                'codMarco': _CodMarco,
                "id": _this.querySelector('input[name="id"]').value,
                "descripcion": _this.querySelector('[name="descripcion"]').value
        };

        _cn = consultasPHP_nueva('./app_docs/app_docs_cambiardoc.php');  
        $.ajax({
                url:   './app_docs/app_docs_cambiardoc.php',
                type:  'post',
                data: _parametros,
               	
                beforeSend: function(request, settings){
			request._data = {'cn':_cn};
		},
		error:  function (request, status, errorThrown){	
			_cn = request._data.cn;
			consultasPHP_respuesta("err",_cn);	
		},
		success:  function (response, status, request){			
			var _res = $.parseJSON(response);            
			_cn = request._data.cn;			
			consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
			if(_res.res!='exito'){return;}      

                        cerrar(_this.querySelector('#botoncierra'));
                        cargaBase();
                }
        });
        //envía los datos para editar el ítem
}
	
function guardarI(_event,_this){// ajustado geogec
        _event.preventDefault();
        console.log(_this);
        var _this=_this;
        _parametros = {
                "idMarco":_IdMarco,
                'codMarco': _CodMarco,
                "id": _this.querySelector('input[name="id"]').value,
                "nombre": _this.querySelector('input[name="nombre"]').value,
                "descripcion": _this.querySelector('[name="descripcion"]').value,
                "publica": _this.querySelector('[name="publica"]').value
        };

        _cn = consultasPHP_nueva('./app_docs/app_docs_cambiaritem.php');
        $.ajax({
                url:   './app_docs/app_docs_cambiaritem.php',
                type:  'post',
                data: _parametros,

                beforeSend: function(request, settings){
			request._data = {'cn':_cn};
		},
		error:  function (request, status, errorThrown){	
			_cn = request._data.cn;
			consultasPHP_respuesta("err",_cn);	
		},
		success:  function (response, status, request){			
			var _res = $.parseJSON(response);            
			_cn = request._data.cn;			
			consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
			if(_res.res!='exito'){return;}      

                        cerrar(_this.querySelector('#botoncierra'));
                        cargaBase();
                }
        });
        //envía los datos para editar el ítem

}

function anadirItem(_iditempadre){//ajustado a geogec
        _parametros = {
                "idMarco":_IdMarco,
                'codMarco': _CodMarco,
                'iditempadre':_iditempadre
        };

        _cn = consultasPHP_nueva('./app_docs/app_docs_crearitem.php');
        $.ajax({
                url:   './app_docs/app_docs_crearitem.php',
                type:  'post',
                data: _parametros,

                beforeSend: function(request, settings){
			request._data = {'cn':_cn};
		},
		error:  function (request, status, errorThrown){	
			_cn = request._data.cn;
			consultasPHP_respuesta("err",_cn);	
		},
		success:  function (response, status, request){			
			var _res = $.parseJSON(response);            
			_cn = request._data.cn;			
			consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
			if(_res.res!='exito'){return;}   

                        if(_iditempadre>0){
                                cerrar(document.querySelector('#editoritem #botoncierra'));
                        }	
                        cargaBase();
                }
        });
}
    
///funciones para gestionar drop en el tacho

function dropTacho(_event,_this){//ajustado geogec
        _event.stopPropagation();
        _event.preventDefault();    		

        limpiarAllowFile();

        if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='archivo'){

                if(confirm('¿Confirma que quiere eliminar el archivo del panel?')==true){

                        _parametros={
                                "idfi":JSON.parse(_event.dataTransfer.getData("text")).id,
                                "tipo":JSON.parse(_event.dataTransfer.getData("text")).tipo,
                                "idMarco":_IdMarco,
                                'codMarco': _CodMarco,
                                "accion":'borrar'
                            };

                        _cn = consultasPHP_nueva('./app_docs/app_docs_borrararchivo.php');  
                        $.ajax({
                                url:   './app_docs/app_docs_borrararchivo.php',
                                type:  'post',
                                data: _parametros,
                                beforeSend: function(request, settings){
                                        request._data = {'cn':_cn};
                                },
                                error:  function (request, status, errorThrown){	
                                        _cn = request._data.cn;
                                        consultasPHP_respuesta("err",_cn);	
                                },
                                success:  function (response, status, request){			
                                        var _res = $.parseJSON(response);            
                                        _cn = request._data.cn;			
                                        consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
                                        if(_res.res!='exito'){return;}  
                                        
                                        cargaBase();
                                }
                        });
                }
                return;

        }else if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='linkurl'){

                if(confirm('¿Confirma que quiere eliminar el link del panel?')==true){

                        _parametros={
                                "idfi":JSON.parse(_event.dataTransfer.getData("text")).id,
                                "tipo":JSON.parse(_event.dataTransfer.getData("text")).tipo,
                                "idMarco":_IdMarco,
                                'codMarco': _CodMarco,
                                "accion":'borrar'
                            };   
                            
                        _cn = consultasPHP_nueva('./app_docs/app_docs_borrarlink.php')
                        $.ajax({
                                url:   './app_docs/app_docs_borrarlink.php',
                                type:  'post',
                                data: _parametros,

                                beforeSend: function(request, settings){
                                        request._data = {'cn':_cn};
                                },
                                error:  function (request, status, errorThrown){	
                                        _cn = request._data.cn;
                                        consultasPHP_respuesta("err",_cn);	
                                },
                                success:  function (response, status, request){			
                                        var _res = $.parseJSON(response);            
                                        _cn = request._data.cn;			
                                        consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
                                        if(_res.res!='exito'){return;}  
                                        
                                        cargaBase();
                                }
                        });

                }
                return;

        }else if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='item'){
                if(confirm('¿Confirma que quiere eliminar el Item y todo su contenido?')==true){


                }
                return;

        }

            var _DragData = JSON.parse(_event.dataTransfer.getData("text")).id;
            console.log(_DragData);
            _el=document.querySelector('.archivo[idfi="'+_DragData+'"]');

            _ViejoIdIt=_el.parentNode.parentNode.getAttribute('idfi');
            _em=_el.nextSibling;
            _idit=_this.getAttribute('idit');
            _ref=document.querySelector('.item[idit="'+_idit+'"] .documentos');
            _ref.appendChild(_el);

}
         
function localizarArchivo(_parametros){
        _cn = consultasPHP_nueva('./app_docs/app_docs_localizararchivo.php');  
	$.ajax({
                url:   './app_docs/app_docs_localizararchivo.php',
                type:  'post',
                data: _parametros,
             
                beforeSend: function(request, settings){
			request._data = {'cn':_cn};
		},
		error:  function (request, status, errorThrown){	
			_cn = request._data.cn;
			consultasPHP_respuesta("err",_cn);	
		},
		success:  function (response, status, request){			
			var _res = $.parseJSON(response);            
			_cn = request._data.cn;			
			consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
			if(_res.res!='exito'){return;}

                        cargaBase();
                }
	});
}
  
function localizarLinkurl(_parametros){
    _cn = consultasPHP_nueva('./app_ind/app_ind_capa_consultar.php');      
    $.ajax({
		url:   './app_ind/app_ind_capa_consultar.php',
                type:  'post',
                data: _parametros,

                beforeSend: function(request, settings){
			request._data = {'cn':_cn};
		},
		error:  function (request, status, errorThrown){	
			_cn = request._data.cn;
			consultasPHP_respuesta("err",_cn);	
		},
		success:  function (response, status, request){			
			var _res = $.parseJSON(response);            
			_cn = request._data.cn;			
			consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
			if(_res.res!='exito'){return;}
                        cargaBase();
		}
    });
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

        if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='linkurl'){
            dropFile(_event,_this);
            return;
        }

        var _DragData = JSON.parse(_event.dataTransfer.getData("text")).id;
        console.log('u');
        console.log(_event.dataTransfer.getData("text"));

        _el=document.querySelector('.item[idit="'+_DragData+'"]');
        _ViejoIdIt=_el.parentNode.parentNode.getAttribute('idit');
        _em=_el.previousSibling;

        _evitar='no';//evita destinos erronos por jerarquia.


        if(_event.target.getAttribute('class')=='medio'||_event.target.getAttribute('class')=='submedio'){

            if(_event.target.getAttribute('class')=='submedio'){
                 _tar=_event.target.parentNode;
            }else{
                _tar=_event.target;
            }

            _dest=_tar.parentNode; 
            _dest.insertBefore(_el,_tar);			    
            _dest.insertBefore(_em,_el);

        }else if(_event.target.getAttribute('class')=='hijos'){

            _dest=_event.target;
            _dest.appendChild(_el);
            _dest.insertBefore(_em,_el);


        }else{
            alert('destino inesperado');
            return;

        }

        _niv=_dest.parentNode.getAttribute('nivel');
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

        _cn = consultasPHP_nueva('./app_docs/app_docs_anidaritem.ph');
        $.ajax({
                url:   './app_docs/app_docs_anidaritem.php',
                type:  'post',
                data: _parametros,

                beforeSend: function(request, settings){
                        request._data = {'cn':_cn};
                },
                error:  function (request, status, errorThrown){	
                        _cn = request._data.cn;
                        consultasPHP_respuesta("err",_cn);	
                },
                success:  function (response, status, request){			
                        var _res = $.parseJSON(response);            
                        _cn = request._data.cn;			
                        consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
                        if(_res.res!='exito'){return;}  
                        
                        cargaBase();
                }
        });
            //envía los datos para editar el ítem
    }
    


function cargarCmp(_this){

            var files = _this.files;

            for (i = 0; i < files.length; i++) {
                _nFile++;
                console.log(files[i]);
                var parametros = new FormData();
                parametros.append('upload',files[i]);
                parametros.append('nfile',_nFile);
                parametros.append('idMarco',_IdMarco);
                parametros.append('codMarco',_CodMarco);

                var _nombre=files[i].name;
                _upF=document.createElement('a');
                _upF.setAttribute('nf',_nFile);
                _upF.setAttribute('class',"archivo");
                _upF.setAttribute('size',Math.round(files[i].size/1000));
                _upF.innerHTML=files[i].name;
                document.querySelector('#listadosubiendo').appendChild(_upF);

                _nn=_nFile;
                xhr[_nn] = new XMLHttpRequest();
                xhr[_nn].open('POST', './app_docs/app_docs_guardaarchivo.php', true);
                xhr[_nn].upload.li=_upF;
                xhr[_nn].upload.addEventListener("progress", updateProgress, false);


                xhr[_nn].onreadystatechange = function(evt){
                    console.log(evt);

					if(evt.currentTarget!=null){
						if(evt.currentTarget.readyState!=null){
							//PARA MS EDGE
							var _res = $.parseJSON(evt.currentTarget.response);
							//console.log(_res);

							//alert('terminó '+_res.data.nf);

							if(_res.res=='exito'){							
								_file=document.querySelector('#listadosubiendo > a[nf="'+_res.data.nf+'"]');								
								document.querySelector('#listadoaordenar').appendChild(_file);
								_file.setAttribute('href',_res.data.ruta);
								_file.setAttribute('download',_file.innerHTML);
								_file.setAttribute('draggable',"true");
								_file.setAttribute('ondragstart',"dragFile(event)");
								_file.setAttribute('idfi',_res.data.nid);
								_aasub=document.createElement('a');
								_aasub.innerHTML='.!.';
								_aasub.setAttribute('onclick','editarD(event,this)');
								_file.appendChild(_aasub);
							} else {
								_file=document.querySelector('#listadosubiendo > a[nf="'+_res.data.nf+'"]');
								_file.innerHTML+=' ERROR';
								_file.style.color='red';
							}
							return;
						}
					}
					

                    if(evt.explicitOriginalTarget.readyState==4){
						// PARA FIREFOX
                        var _res = $.parseJSON(evt.explicitOriginalTarget.response);
                        //console.log(_res);

                        //alert('terminó '+_res.data.nf);

                        if(_res.res=='exito'){							
                            _file=document.querySelector('#listadosubiendo > a[nf="'+_res.data.nf+'"]');								
                            document.querySelector('#listadoaordenar').appendChild(_file);
                            _file.setAttribute('href',_res.data.ruta);
                            _file.setAttribute('download',_file.innerHTML);
                            _file.setAttribute('draggable',"true");
                            _file.setAttribute('ondragstart',"dragFile(event)");
                            _file.setAttribute('idfi',_res.data.nid);
                            _aasub=document.createElement('a');
                            _aasub.innerHTML='.!.';
                            _aasub.setAttribute('onclick','editarD(event,this)');
                            _file.appendChild(_aasub);
                        } else {
                            _file=document.querySelector('#listadosubiendo > a[nf="'+_res.data.nf+'"]');
                            _file.innerHTML+=' ERROR';
                            _file.style.color='red';
                        }
                        //cargaTodo();
                        //limpiarcargando(_nombre);
                    }
                };
                xhr[_nn].send(parametros);
            }			
}




function cargarCmpLink(_event,_this){
            //Guardar Link url en la BD
            
            _event.preventDefault();
            console.log(_this);
            var _this=_this;
            
            var linkName = _this.querySelector('input[name="linkName"]').value;
            var urlLink = _this.querySelector('input[name="linkUrl"]').value;

            _nLink++;
            
            console.log(urlLink);
            var parametros = new FormData();
            parametros.append('urlLink',urlLink);
            parametros.append('linkName',linkName);
            parametros.append('nlink',_nLink);
            parametros.append('idMarco',_IdMarco);
            parametros.append('codMarco',_CodMarco);

            _upF=document.createElement('a');
            _upF.setAttribute('nf',_nLink);
            _upF.setAttribute('class',"archivo");
            _upF.innerHTML=linkName;
            document.querySelector('#listadosubiendo').appendChild(_upF);

            _nn=_nLink;
            xhr[_nn] = new XMLHttpRequest();
            xhr[_nn].open('POST', './app_docs/app_docs_guardalink.php', true);
            xhr[_nn].upload.li=_upF;
            xhr[_nn].upload.addEventListener("progress", updateProgress, false);

            xhr[_nn].onreadystatechange = function(evt){
                //console.log(evt);

                if(evt.explicitOriginalTarget.readyState==4){
                    var _res = $.parseJSON(evt.explicitOriginalTarget.response);
                    //console.log(_res);

                    if(_res.res=='exito'){							
                        _link=document.querySelector('#listadosubiendo > a[nf="'+_res.data.nf+'"]');								
                        document.querySelector('#listadoaordenar').appendChild(_link);
                        _link.setAttribute('href',_res.data.ruta);
                        _link.setAttribute('target','_blank');
                        _link.setAttribute('download',_link.innerHTML);
                        _link.setAttribute('draggable',"true");
                        _link.setAttribute('ondragstart',"dragLinkurl(event)");
                        _link.setAttribute('idfi',_res.data.nid);
                        _aasub=document.createElement('a');
                        _aasub.innerHTML='.!.';
                        _aasub.setAttribute('onclick','editarLink(event,this)');
                        _link.appendChild(_aasub);
                    } else {
                        _link=document.querySelector('#listadosubiendo > a[nf="'+_res.data.nf+'"]');
                        _link.innerHTML+=' ERROR';
                        _link.style.color='red';
                        alert('error cargarCmpLink');
                    }
                }
                
                cerrar(_this.querySelector('#botoncierra'));
            };
            xhr[_nn].send(parametros);
}



function eliminarI(_event,_this){
    if (confirm("¿Eliminar item y sus archivos asociados? \n (los ítems anidados quedarán en la raiz)")==true){

        _event.preventDefault();

        var _this=_this;

        _parametros = {
                "id": _this.parentNode.querySelector('input[name="id"]').value,
                "accion": "borrar",
                "tipo": "item",
                "idMarco": _IdMarco,
                'codMarco': _CodMarco
        };

        _cn = consultasPHP_nueva('./app_docs/app_docs_borraritem.php');  
        $.ajax({
            url:   './app_docs/app_docs_borraritem.php',
            type:  'post',
            data: _parametros,

            beforeSend: function(request, settings){
                request._data = {'cn':_cn};
            },
            error:  function (request, status, errorThrown){	
                _cn = request._data.cn;
                consultasPHP_respuesta("err",_cn);	
            },
            success:  function (response, status, request){			
                var _res = $.parseJSON(response);            
                _cn = request._data.cn;			
                consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
                if(_res.res!='exito'){return;}

                cerrar(_this);
                cargaBase();
            }
        });
        //envía los datos para editar el ítem
    }
}
