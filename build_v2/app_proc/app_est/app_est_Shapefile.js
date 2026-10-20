/**
*
* funciones para gestionar el envío y validación de archivos SHP
* 
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrolló sobre una publicación GNU 2017 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (GPL-3.0)
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
*/

function limpiarfomularioversion(){
    //document.querySelector('#divCargaCapa select#crs').options[1].selected;
    document.getElementById('crs').value = '';
    //document.querySelector('#divCargaCapa').setAttribute('idcapa','');
    document.querySelector('#divCargaCapa #txningunarchivo').style.display='block';
    document.querySelector('#divCargaCapa #archivoscargados').innerHTML='';
    document.querySelector('#divCargaCapa #camposident').innerHTML='';
    document.querySelector('#divCargaCapa #carga').style.display='none';
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev,_gg) {
    ev.preventDefault();
    
    var data = ev.dataTransfer.getData("text");
	
    _dest=ev.target;
    if(_dest.getAttribute('id')!='espacioshp'){
    	return;
    }
    
    if(_dest.querySelector('.enshp') != null){
    	return;
    }
    
    _obj=document.getElementById(data);
    _parent=_obj.parentNode.parentNode;
    
    if(_dest.parentNode.getAttribute('origen')=='aux'){
        _clon=_dest.parentNode.cloneNode(true);
        _parent.parentNode.appendChild(_clon);

        _dest.parentNode.setAttribute('origen','shp');
    }

    _dest.appendChild(_obj);    

    if(_parent.getAttribute('origen')=='shp'){
        _parent.parentNode.removeChild(_parent);
    }

    actualizarCadenaCampos();    	 
}

function actualizarNombreColumna(_event, _this){
    console.log(_event.keyCode);
    if(_event.keyCode==9){return;}//tab
    if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
    if(_event.keyCode==13){
        var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
        var idMarco = getParameterByName('id');
        var codMarco = getParameterByName('cod');

        var parametros = {
            'codMarco': codMarco,
            'idMarco': idMarco,
            'id': idCapa,
            'columna': _this.getAttribute('nom'),
            'nombrenuevo': _this.value
        };

        editarCapa(parametros);
    }        
}

var _Procesarcampos;
function actualizarCadenaCampos(){
    ValidarProcesarBoton();
    _Procesarcampos={};
    _filas=document.querySelectorAll('#divCargaCapa #carga #camposident .enshp');
    for(var _nf in _filas){
        if(typeof _filas[_nf] !='object'){continue;}
        _parent=_filas[_nf].parentNode.parentNode;
        if(_parent.getAttribute('origen')=='aux'){continue;}
        _nom=_filas[_nf].getAttribute('nom');
        if(_parent.querySelector('#entabla').getAttribute('nom')!=''){
            _Procesarcampos[_nom]={};
            _Procesarcampos[_nom]['acc']='asignar';
            _Procesarcampos[_nom]['nom']=_parent.querySelector('#entabla').getAttribute('nom');
        }
    }

    document.querySelector('#verproccampo').innerHTML=JSON.stringify(_Procesarcampos);
}

var _checkList={
    'prj':{'s':'no','mg':'sin prj definido'},
    'shp':{'s':'no','mg':'sin shapefile definido'},
    'dbf':{'s':'no','mg':'completamiento indefinido para algunas columnas d ela base'}
};

function procesarCapa(_this){
    _stop='no';
    for(var _comp in _checkList){
        if(_checkList[_comp].s=='no'){
            alert(_checkList[_comp].mg);
            _stop='si';	
        }		
    }
    if(_stop=='si'){
        return;
    }

    guardarCapa(_this,'si');
}

function procesarCapa2(_this,_avance){
    var _this =_this;
    var _parametros = {
        'id': document.getElementById('divCargaCapa').getAttribute('idcapa'),
        'avance': _avance,
        'codMarco':_CodMarco
    };

    $.ajax({
        url:  './app_est/app_est_procesa.php',
        type: 'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            console.log(_res);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='exito'){
                if(_res.data.avance!='final'){
                    procesarCapa2(_this,_res.data.avance);
                    document.querySelector('#avanceproceso').style.display='block';
                    document.querySelector('#avanceproceso').innerHTML=_res.data.avanceP+"%";
                    document.querySelector('#avanceproceso').setAttribute('avance',_res.data.avanceP);
                }else{
                    alert("Shapefile procesado exitosamente");
                    document.querySelector('#avanceproceso').style.display='none';
                }
            }
        }
    });
}

function ValidarProcesarBoton(){
    _checkList.prj.s='si';
    _checkList.prj.ms='ok';
    actualizarestadoCampos();
    _stop='no';
    _bot=document.querySelector('a#procesarBoton');
    
    _bot.title='';
    _bot.removeAttribute('estado');
    for(var _comp in _checkList){
        if(_checkList[_comp].s=='no'){
            _bot.setAttribute('estado','inviable');
            _bot.title+=_checkList[_comp].mg;
            _stop='si';	
        }		
    }
    if(_stop=='no'){
        _bot.setAttribute('estado','viable');
        _bot.title+='listo para procesar versión';
    }
}

function actualizarestadoCampos(){
    console.log('actualizarestadoCampos()');	
    //actualiza en el checklist el estado de los campos asignados
    _divs=document.querySelectorAll('#camposident > div[origen="tabla"]');

    _checkList.dbf.s='si';
    _checkList.dbf.mg='ok';
    for(var _nd in _divs){
        if(typeof _divs[_nd]!='object'){continue;}
        if(_divs[_nd].querySelector('#espacioshp > .enshp') == null){
            //TODO: Anulado esto porque no nos importa cubrir todas las columnas. Mirar si hace algo mas y sino borrarlo
            
            //_checkList.dbf.s='no';
            //_checkList.dbf.mg='al menos un campo de la tabla carece de un campo asociado del shapefile.';
        }
    }	
}

function eliminarCandidatoCapa(_this){
 

    if(confirm("¿Confirma que quiere eliminar este candidato a versión? \n Si lo hace se eliminarán los archivos que haya subido y se guardará registro en la papelera de los datos cargados en el formulario.")){


        var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
        var _parametros = {
            'id': idCapa,
            'codMarco': _CodMarco
        };

        $.ajax({
        url:   './app_est/app_est_eliminar.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
                console.log(_res);	
                for(var _nm in _res.mg){
                    alert(_res.mg[_nm]);
                }
                if(_res.res=='exito'){
                    limpiarFormularioCapa();
                    accionCancelarCargarNuevaCapa();
                }
            }
        });
    }
}

function formversion(_this){
    console.log('formversion()');

    limpiarfomularioversion();

    var _parametros = {
        'id': document.getElementById('divCargaCapa').getAttribute('idcapa'),
        'codMarco':_CodMarco
    };
    $.ajax({
        url:   './app_est/app_est_validar.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            console.log('a');
            console.log(_res);
            
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }

            if(_res.res=='exito'){
                document.querySelector('#divCargaCapa #carga').style.display='block';

                for(var _na in _res.data.archivos){
                    document.querySelector('#divCargaCapa #txningunarchivo').style.display='none';
                    _fil=document.createElement('p');
                    _fil.innerHTML=_res.data.archivos[_na].nom;
                    _fil.setAttribute('fileExt',_res.data.archivos[_na].ext);				
                    document.querySelector('#divCargaCapa #carga #archivoscargados').appendChild(_fil);						
                }

                if(_res.data.prj.stat=='viable'){
                    _checkList.prj.s='si';
                    _checkList.prj.ms='ok';
                    _sel=document.querySelector('#crs');
                    for(_no in _sel.options){
                        if(_sel.options[_no].value==_res.data.prj.def){
                            _sel.options[_no].selected=true;

                            _ppp=document.querySelectorAll('#archivoscargados [fileext="prj"], #archivoscargados [fileext="qpj"]');
                            for(_np in _ppp){
                                if(typeof _ppp[_np] == 'object'){
                                    _ppp[_np].setAttribute('estado','viable');
                                    _ppp[_np].title=_res.data.prj.mg;
                                }
                            }
                        }
                    }
                } else if(_res.data.prj.stat=='viableobs'){
                    _checkList.prj.s='si';
                    _checkList.prj.ms='se adoptará el prj del formulario que difiere del explicitado en el archivo subido';
                    _sel=document.querySelector('#crs');
                    for(var _no in _sel.options){
                        if(_sel.options[_no].value==_res.data.prj.def){
                            _sel.options[_no].selected=true;

                            _ppp=document.querySelectorAll('#archivoscargados [fileext="prj"], #archivoscargados [fileext="qpj"]');
                            for(var _np in _ppp){
                                if(typeof _ppp[_np] == 'object'){
                                        _ppp[_np].setAttribute('estado','viableobs');
                                        _ppp[_np].title=_res.data.prj.mg;
                                }
                            }
                        }
                    }
                } else {
                    _checkList.prj.s='no';
                    _checkList.prj.ms=_res.data.prj.mg;
                    _ppp=document.querySelectorAll('#archivoscargados [fileext="prj"], #archivoscargados [fileext="qpj"]');
                    for(var _np in _ppp){
                        if(typeof _ppp[_np] == 'object'){
                            _ppp[_np].setAttribute('estado','inviable');
                            _ppp[_np].title=_res.data.prj.mg;
                        }
                    }

                    //alert("crs: ",_res.data.prj.mg);
                }

                if(_res.data.shp.stat=='viable'){
                    _checkList.shp.s='si';
                    _checkList.shp.ms='ok';
                    _ppp=document.querySelectorAll('#archivoscargados [fileext="shp"], #archivoscargados [fileext="shx"], #archivoscargados [fileext="dbf"]');
                    for(var _np in _ppp){
                        if(typeof _ppp[_np] == 'object'){
                            _ppp[_np].setAttribute('estado','viable');
                            _ppp[_np].title=_res.data.shp.mg;
                        }
                    }
                } else if(_res.data.prj.stat=='inviable'){
                    _checkList.shp.s='no';
                    _checkList.shp.ms=_res.data.shp.mg;
                    _ppp=document.querySelectorAll('#archivoscargados [fileext="shp"], #archivoscargados [fileext="shx"], #archivoscargados [fileext="dbf"]');
                    for(var _np in _ppp){
                        if(typeof _ppp[_np] == 'object'){
                            _ppp[_np].setAttribute('estado','inviable');
                            _ppp[_np].title=_res.data.shp.mg;
                        }
                    }
                    alert(_res.data.shp.mg);
                }

				
				
                for(var _col in _res.data.columnas){
                    if(_col=='id'){continue;}
                    if(_col=='geo'){continue;}
                    if(_col=='id_sis_versiones'){continue;}
                    if(_col=='zz_obsoleto'){continue;}

                    _fil=document.createElement('div');
                    _fil.setAttribute('origen','tabla');
                                        
                    _nom=document.createElement('input');
                    _nom.setAttribute('id','renombrar');
                    _nom.setAttribute('nom',_col);	
                    _nom.setAttribute('onkeyup','actualizarNombreColumna(event, this)');
                    var nombreColumna = 'nom_col_'+_col.replace("texto", "text").replace("numero", "num");
                    _nom.value = _res.data.version[nombreColumna];
                    _fil.appendChild(_nom);

                    _nom=document.createElement('div');
                    _nom.setAttribute('id','entabla');
                    _nom.setAttribute('nom',_col);
                    _nom.setAttribute('tipo',_col.substring(0,1));
                    _nom.setAttribute('estado','vacio');
                    _nom.innerHTML=_col;						
                    _fil.appendChild(_nom);

                    _nom=document.createElement('div');
                    _nom.setAttribute('id','espacioshp');	
                    _nom.setAttribute('ondrop',"drop(event)");
                    _nom.setAttribute('ondragover',"allowDrop(event)");

                    _fil.appendChild(_nom);

                    document.querySelector('#divCargaCapa #carga #camposident').appendChild(_fil);
                }

                _c = 0;

                _Icampos={};
                if(_res.data.version.instrucciones!=''&&_res.data.version.instrucciones!=null){
                    _Icampos = $.parseJSON(_res.data.version.zz_instrucciones);
                }
				console.log('icampos');
				console.log(_Icampos);
				
				
                for(_col in _res.data.dbf.campos){
                	_ref=null;
                    _dat=_res.data.dbf.campos[_col];
                    _norig=_dat.name;
                    _nombre=_dat.name;

                    if(_norig=='id'){continue;}
                    if(_norig=='geo'){continue;}
                    if(_norig=='id_sis_versiones'){continue;}
                    if(_col=='zz_obsoleto'){continue;}

					console.log('_col:'+_nombre);
					if(_Icampos[_nombre]!=null){
						console.log('_nombre: es key en icampos');
						_ref=document.querySelector('#divCargaCapa #carga #camposident #entabla[nom="'+_Icampos[_nombre].nom+'"]');
						
						if(_ref!=null){
	                    	console.log('_col: localizado en las instrucciones');
	                    	_ref.setAttribute('estado','lleno');
	                        _nome=_ref.parentNode.querySelector('#espacioshp');
	                        _nom=document.createElement('div');
	                        _c++;
	                        _nom.setAttribute('id',_c);
	                        _nom.setAttribute('class','enshp');
	                        _nom.setAttribute('nom',_norig);
	                        _nom.setAttribute("draggable","true");
	                        _nom.setAttribute("ondragstart","drag(event)");							
	                        _nom.innerHTML=_norig;			
	                        _nome.appendChild(_nom);
	                    }							
					}
				}
					
				for(_col in _res.data.dbf.campos){
                	_ref=null;
                    _dat=_res.data.dbf.campos[_col];
                    _norig=_dat.name;
                    _nombre=_dat.name;
		
                    if(_norig=='id'){continue;}
                    if(_norig=='geo'){continue;}
                    if(_norig=='id_sis_versiones'){continue;}
                    if(_col=='zz_obsoleto'){continue;}
                    
                    console.log('_col:'+_nombre);
                    if(_Icampos[_nombre]!=null){
						_ref=document.querySelector('#divCargaCapa #carga #camposident #entabla[nom="'+_Icampos[_nombre].nom+'"]');			
					}
											
                    if(_ref==null){
                    	console.log('_col: sin localizar en las instrucciones');                    	
                    	console.log(_dat.type);
                    	if(_dat.type=='N'){
                    		_ref=document.querySelector('#divCargaCapa #carga #camposident #entabla[estado="vacio"][tipo="n"]');
                    		console.log('es N');
                    		console.log(_ref);
                    		if(_ref==null){
                    			_fil=document.createElement('div');
		                        _fil.setAttribute('id','entabla');
		                        _fil.setAttribute('origen','shp');
		
		                        _nom=document.createElement('div');
		                        _nom.setAttribute('id','entabla');
		                        _nom.setAttribute('nom','');
		                        _fil.appendChild(_nom);
		
		                        _nome=document.createElement('div');
		                        _nome.setAttribute('id','espacioshp');	
		                        _nome.setAttribute('ondrop',"drop(event)");
		                        _nome.setAttribute('ondragover',"allowDrop(event)");
		                        _fil.appendChild(_nome);
		
		                        _nom=document.createElement('div');
		                        _c++;
		                        _nom.setAttribute('id',_c);
		                        _nom.setAttribute('class','enshp');
		                        _nom.setAttribute('nom',_norig);
		                        _nom.setAttribute("draggable","true");
		                        _nom.setAttribute("ondragstart","drag(event)");							
		                        _nom.innerHTML=_norig;			
		                        _nome.appendChild(_nom);
		
		                        document.querySelector('#divCargaCapa #carga #camposident').appendChild(_fil);
                    		
                    		}else{
                    		
	                    		_ref.setAttribute('estado','lleno');
	                    		_nome=_ref.parentNode.querySelector('#espacioshp');
		                        _nom=document.createElement('div');
		                        _c++;
		                        _nom.setAttribute('id',_c);
		                        _nom.setAttribute('class','enshp');
		                        _nom.setAttribute('nom',_norig);
		                        _nom.setAttribute("draggable","true");
		                        _nom.setAttribute("ondragstart","drag(event)");							
		                        _nom.innerHTML=_norig;			
		                        _nome.appendChild(_nom);	
		                        
                    		}
                    	}else{
                    		console.log('es S');
                    		console.log(_ref);
                    		_ref=document.querySelector('#divCargaCapa #carga #camposident #entabla[estado="vacio"][tipo="t"]');
                    		
                    		if(_ref==null){
                    			_fil=document.createElement('div');
		                        _fil.setAttribute('id','entabla');
		                        _fil.setAttribute('origen','shp');
		
		                        _nom=document.createElement('div');
		                        _nom.setAttribute('id','entabla');
		                        _nom.setAttribute('nom','');
		                        _fil.appendChild(_nom);
		
		                        _nome=document.createElement('div');
		                        _nome.setAttribute('id','espacioshp');	
		                        _nome.setAttribute('ondrop',"drop(event)");
		                        _nome.setAttribute('ondragover',"allowDrop(event)");
		                        _fil.appendChild(_nome);
		
		                        _nom=document.createElement('div');
		                        _c++;
		                        _nom.setAttribute('id',_c);
		                        _nom.setAttribute('class','enshp');
		                        _nom.setAttribute('nom',_norig);
		                        _nom.setAttribute("draggable","true");
		                        _nom.setAttribute("ondragstart","drag(event)");							
		                        _nom.innerHTML=_norig;			
		                        _nome.appendChild(_nom);
		
		                        document.querySelector('#divCargaCapa #carga #camposident').appendChild(_fil);
                    		
                    		}else{
                    		
	                     		_ref=document.querySelector('#divCargaCapa #carga #camposident #entabla[estado="vacio"][tipo="t"]');
	                     		_ref.setAttribute('estado','lleno');
	                    		_nome=_ref.parentNode.querySelector('#espacioshp');
		                        _nom=document.createElement('div');
		                        _c++;
		                        _nom.setAttribute('id',_c);
		                        _nom.setAttribute('class','enshp');
		                        _nom.setAttribute('nom',_norig);
		                        _nom.setAttribute("draggable","true");
		                        _nom.setAttribute("ondragstart","drag(event)");							
		                        _nom.innerHTML=_norig;			
		                        _nome.appendChild(_nom);	            
		                     }       		
                    	}
                    	  
                    } else{
                    	console.log('salteado');
                    }
                }

                _fil=document.createElement('div');	
                _fil.setAttribute('origen','aux');

                _nom=document.createElement('div');
                _nom.setAttribute('id','entabla');
                _nom.setAttribute('nom','');					
                _fil.appendChild(_nom);

                _nom=document.createElement('div');
                _nom.setAttribute('id','espacioshp');	
                _nom.setAttribute('ondrop',"drop(event)");
                _nom.setAttribute('ondragover',"allowDrop(event)");

                _fil.appendChild(_nom);

                document.querySelector('#divCargaCapa #carga #camposident').appendChild(_fil);

                actualizarCadenaCampos();
            }

            document.querySelector('#divCargaCapa #carga').style.display='block';
            //document.querySelector('#divCargaCapa #botonformversion').style.display='none';
        }
    });
}

function guardarCapa(_this,_procesar){
    var _this=_this;
    var _procesar=_procesar;
    
    editarNombreCapa();
    editarDescripcionCapa();
    editarCamposCapa();
    guardarSLD();
    
    var _parametros = {
        'instrucciones': _this.parentNode.querySelector('#verproccampo').innerHTML, 
        'fi_prj': _this.parentNode.querySelector('select#crs').value,
        'id': document.getElementById('divCargaCapa').getAttribute('idcapa'),
        'codMarco': _CodMarco
    };
    $.ajax({
        url:  './app_est/app_est_editar_shapefile.php',
        type: 'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            console.log(_res);

            if(_procesar=='si')
            {
                procesarCapa2(_this.parentNode,0);
                return;
            }
            formversion(_this.parentNode);
        }
    });
}

var _contUp=0;
var _Cargas={};

function enviarArchivosSHP(_event,_this){
    document.getElementById('crs').value = '';
    document.querySelector('#divCargaCapa #txningunarchivo').style.display='block';
    document.querySelector('#divCargaCapa #archivoscargados').innerHTML='';
    document.querySelector('#divCargaCapa #cargando').innerHTML='';
    document.querySelector('#divCargaCapa #camposident').innerHTML='';
    
    ValidarProcesarBoton();
    
    //Antes de copiar los archivos, borremos cualquier archiv de esa carpeta
    var parametros = new FormData();
    parametros.append("id",document.getElementById('divCargaCapa').getAttribute('idcapa'));
    
    $.ajax({
            data:  parametros,
            url:   './app_est/app_est_shp_limpiar_carpeta.php',
            type:  'post',
            processData: false,
            contentType: false,
            success:  function (response) {
                var _res = $.parseJSON(response);
                for(var elem in _res.mg)
                {
                    alert(_res.mg[elem]);
                }

                if(_res.res=='exito'){
                    enviarArchivosSHPUpload(_event,_this);
                }else{
                    archivoFallido(_res);
                }
            }
        });
}

function enviarArchivosSHPUpload(_event,_this){
    var files = _this.files;	
    for (i = 0; i < files.length; i++) {
        _contUp++;
        _Cargas[_contUp]='subiendo';
        var parametros = new FormData();
        parametros.append("upload",files[i]);
        parametros.append("id",document.getElementById('divCargaCapa').getAttribute('idcapa'));
        parametros.append("crs",_this.parentNode.parentNode.querySelector('#crs').value);
        parametros.append("cont",_contUp);

        cargando(files[i].name,_contUp);

        //Llamamos a los puntos de la actividad
        $.ajax({
            data:  parametros,
            url:   './app_est/app_est_shp_upload.php',
            type:  'post',
            processData: false,
            contentType: false,
            success:  function (response) {
                var _res = $.parseJSON(response);
                for(var elem in _res.mg)
                {
                    alert(_res.mg[elem]);
                }

                if(_res.res=='exito'){
                    archivoSubido(_res);
                }else{
                    archivoFallido(_res);
                }

                _Cargas[_res.data.ncont]='terminado';

                _pendientes=0;
                for(var _nn in _Cargas){
                    if(_Cargas[_nn]=='subiendo'){_pendientes++;}
                }
                if(_pendientes==0){
                    //alert(document.querySelector('#botonformversion').innerHTML);
                    formversion(document.querySelector('#botonformversion'));
                    alert('ok');
                }
            }
        });
    }
}

function cargando(_nombre,_con){
    _ppp=document.createElement('p');
    _ppp.innerHTML=_nombre;
    _ppp.setAttribute('ncont',_con);
    _ppp.setAttribute('class','carga');

    document.querySelector('#shp #cargando').appendChild(_ppp);
}	
	
function archivoSubido(_res){
    document.querySelector('#shp #cargando p[ncont="'+_res.data.ncont+'"]').innerHTML+=' ...subido';
    document.querySelector('#shp #cargando p[ncont="'+_res.data.ncont+'"]').setAttribute('estado','subido');
}	

function archivoFallido(_res){
    document.querySelector('#shp #cargando p[ncont="'+_res.data.ncont+'"]').innerHTML+=' ...fallido';
    document.querySelector('#shp #cargando p[ncont="'+_res.data.ncont+'"]').setAttribute('estado','fallido');
}
