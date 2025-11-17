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
    document.querySelector('#cargarGeometrias #crs').value = '';
	document.querySelector('#cargarGeometrias #txningunarchivo').style.display='block';
    document.querySelector('#cargarGeometrias #cargando').innerHTML='';
}





/////////////////////////////////
//SUBE Y PROCESA GEOMETRÍAS
//////////////////////////////////


var _contUp=0;
var _Cargas={};

function enviarArchivosSHPDXF(_event,_this){
    limpiarfomularioversion();
    
    ValidarProcesarBotonSHP();
    ValidarProcesarBotonDXF();
    //Antes de copiar los archivos, borremos cualquier archiv de esa carpeta
    var parametros = new FormData();
    
    parametros.append("idcampa",_DataRele.id);
    parametros.append("codmarco",_CodMarco);
    
    $.ajax({
        data:  parametros,
        url:   './app_rele/app_rele_upload_limpiar_carpeta.php',
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
                enviarArchivosUpload(_event,_this);
            }else{
                archivoFallido(_res);
            }
        }
    });
}

function enviarArchivosUpload(_event,_this){
    var files = _this.files;	
    for (i = 0; i < files.length; i++) {
        _contUp++;
        _Cargas[_contUp]='subiendo';
        var parametros = new FormData();
        parametros.append("upload",files[i]);
        parametros.append("crs",_this.parentNode.parentNode.querySelector('#crs').value);
        parametros.append("cont",_contUp);
	    parametros.append("idcampa",_DataRele.id);
	    parametros.append("codmarco",_CodMarco);
	    
        cargando(files[i].name,_contUp);

        //Llamamos a los puntos de la actividad
        $.ajax({
            data:  parametros,
            url:   './app_rele/app_rele_upload.php',
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
                   ValidarProcesarBotonSHP();
                   ValidarProcesarBotonDXF();
                    
                }
            }
        });
    }
}



function enviarArchivoXLSX(){
	
	 var files = document.querySelector('#uploadxlsxinput').files;	
    
        var parametros = new FormData();
        parametros.append("upload",files[0]);
	    parametros.append("idcampa",_DataRele.id);
	    parametros.append("codmarco",_CodMarco);

        
        //Llamamos a los puntos de la actividad
        $.ajax({
            data:  parametros,
            url:   './app_rele/app_rele_upload_xlsx.php',
            type:  'post',
            processData: false,
            contentType: false
         })
		.done(function (_data, _textStatus, _jqXHR){
			_res = preprocesarRespuestaAjax(_data, _textStatus, _jqXHR);
			if(_res===false){return;}
			
			
			document.querySelector('#FormularioSubirPlanilla').setAttribute('archivocargado','si');
			document.querySelector('#FormularioSubirPlanilla input[name="filename"]').value=_res.data.archivo;
			document.querySelector('#FormularioSubirPlanilla #txarchivocargado').innerHTML=_res.data.nombreorig;
			
			_sel=document.querySelector('#asignanombre select');
			_sel.innerHTML='';
			for(_nc in _res.data.columnas){
				_nomcol=_res.data.columnas[_nc];
				if(_nomcol==''){continue;}				
				_op=document.createElement('option');
				_sel.appendChild(_op);
				_op.innerHTML=_nomcol;
				_op.value=_nc;			
			}
			
			
			_sel=document.querySelector('#FormularioSubirPlanilla #elegircamposgeom [name="columna_wkt_en_planillla"]');
			_sel.innerHTML='';
			for(_nc in _res.data.columnas){
				_nomcol=_res.data.columnas[_nc];
				if(_nomcol==''){continue;}				
				_op=document.createElement('option');
				_sel.appendChild(_op);
				_op.innerHTML=_nomcol;
				_op.value=_nc;			
			}
			
			_sel=document.querySelector('#FormularioSubirPlanilla [name="columna_link_en_planillla"]');
			_sel.innerHTML='';
			for(_nc in _res.data.columnas){
				_nomcol=_res.data.columnas[_nc];
				if(_nomcol==''){continue;}				
				_op=document.createElement('option');
				_sel.appendChild(_op);
				_op.innerHTML=_nomcol;
				_op.value=_nc;			
			}			
			
			
			
			_cont=document.querySelector('#asignacampos');
			_cont.innerHTML='';
			
			_div=document.querySelector('#FormularioSubirPlanilla #crear_campos #listado_campos');
			_div.innerHTML='';
			
			for(_nc in _res.data.columnas){
				
				_nomcol=_res.data.columnas[_nc];
				if(_nomcol==''){continue;}				
				
				_fila=document.createElement('div');
				_fila.setAttribute('class','mini_form_campo');
				_fila.setAttribute('num_col_crea',_nc);
				_div.appendChild(_fila);
				
				_chk=document.createElement('input');
				_chk.setAttribute('name','crear');
				_chk.setAttribute('type','checkbox');
				_chk.checked=true;
				_fila.appendChild(_chk);
				
				_tit=document.createElement('label');
				_tit.innerHTML=_nomcol;
				_fila.appendChild(_tit);

				_conf=document.createElement('div');
				_conf.innerHTML="<label>Nombre:</label><input name='nombre'><label>Tipo:</label><select name='tipo' onchange=''><option>- elegir -</option><option value='texto'>texto</option><option value='checkbox'>checkbox</option><option value='select'>menu desplegable</option><option value='numero'>numero</option><option value='fecha'>fecha</option><option value='coleccion_imagenes'>imagenes</option></select>";
				_fila.appendChild(_conf);
				
				_conf.querySelector('[name="nombre"]').value=_nomcol;
			}
			
			_cont=document.querySelector('#asignacampos');
			_cont.innerHTML='';
						
			
			for(_con in _DataRele.camposOrden){
				_idcampo = _DataRele.camposOrden[_con];
				_nom=_DataRele.campos[_idcampo].nombre;
								
				_div=document.createElement('div');
				_cont.appendChild(_div);
				
				_la=document.createElement('label');
				_div.appendChild(_la);
				_la.innerHTML=_nom;
								
				_sel=document.createElement('select');
				_sel.setAttribute('name','campo_'+_idcampo);
				_div.appendChild(_sel);
				
				_op=document.createElement('option');
				_sel.appendChild(_op);
				_op.innerHTML='- elegir columna -';
				_op.value='-1';
				
				for(_nc in _res.data.columnas){
					_nomcol=_res.data.columnas[_nc];
					if(_nomcol==''){continue;}
					
					_op=document.createElement('option');
					_sel.appendChild(_op);
					_op.innerHTML=_nomcol;
					_op.value=_nc;
				
				}
			}
		});	    

}


function cargando(_nombre,_con){
	document.querySelector('#cargarGeometrias #txningunarchivo').style.display='none';
    _ppp=document.createElement('p');
    _ppp.innerHTML=_nombre;
    _ppp.setAttribute('ncont',_con);
    _ppp.setAttribute('class','carga');
    _ppp.setAttribute('file',_nombre);
    
    _e=_nombre.split('.');
    _ppp.setAttribute('extension',_e[(_e.length)-1]);
	
    document.querySelector('#cargarGeometrias #cargando').appendChild(_ppp);
}	
	
function archivoSubido(_res){
    document.querySelector('#cargarGeometrias #cargando p[ncont="'+_res.data.ncont+'"]').innerHTML+=' ...subido';
    document.querySelector('#cargarGeometrias #cargando p[ncont="'+_res.data.ncont+'"]').setAttribute('estado','subido');
}	

function archivoFallido(_res){
    document.querySelector('#cargarGeometrias #cargando p[ncont="'+_res.data.ncont+'"]').innerHTML+=' ...fallido';
    document.querySelector('#cargarGeometrias #cargando p[ncont="'+_res.data.ncont+'"]').setAttribute('estado','fallido');
}


var _CheckListSHP={
    'prj':{'s':'no','mg':'sin prj definido'},
    'shp':{'s':'no','mg':'sin shapefile definido'},
    'shx':{'s':'no','mg':'sin shapefile definido'},
    'dbf':{'s':'no','mg':'completamiento indefinido para algunas columnas d ela base'}
};

var _CheckListDXF={
    'dxf':{'s':'no','mg':'no hay archivo dxf'}
};


function procesarCampaSHP(_this){
    _stop='no';
    for(var _comp in _CheckListSHP){
        if(_CheckListSHP[_comp].s=='no'){
            alert(_CheckListSHP[_comp].mg);
            _stop='si';	
        }		
    }
    if(_stop=='si'){
        return;
    }

    guardarCapa(_this,'si');
}

function procesarCampaSHP2(_this,_avance){
    var _this =_this;
    var _parametros = {
        'id': document.getElementById('divCargaCapa').getAttribute('idcapa'),
        'avance': _avance,
        'codMarco':_CodMarco
    };

    $.ajax({
        url:  './app_capa/app_capa_procesa.php',
        type: 'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            console.log(_res);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='exito'){
            	if(_res.data.avance==0){
            		alert('error, el proceso no avanzó');
            		return;
            	}
                if(_res.data.avance!='final'){
                    procesarCapa2(_this,_res.data.avance);
                    document.querySelector('#avanceproceso').style.display='block';
                    document.querySelector('#avanceproceso').innerHTML=_res.data.avanceP+"%";
                    document.querySelector('#avanceproceso').setAttribute('avance',_res.data.avanceP);
                }else{
                    alert("Shapefile procesado exitosamente");
                    document.querySelector('#avanceproceso').style.display='none';
                    cargarValoresCapaExistQuery();
                }
            }
        }
    });
}

function procesarCampaDXF(_avance){
	
    _stop='no';
    for(var _comp in _CheckListDXF){
        if(_CheckListDXF[_comp].s=='no'){
            alert(_CheckListDXF[_comp].mg);
            _stop='si';	
        }		
    }
    if(_stop=='si'){
        return;
    }
	
	_file=document.querySelector('.carga[extension="dxf"][estado="subido"]').getAttribute('file');
	
    _parametros = {
        'idcampa': _DataRele.id,
        'avance': _avance,
        'codMarco':_CodMarco,
        'usu_crs':document.querySelector('#cargarGeometrias select#crs').value,
        'archivo_nom':_file
    };
    
    console.log(_parametros);
    
	//alert('u');
    $.ajax({
        url:  './app_rele/app_rele_procesa_dxf.php',
        type: 'post',
        data: _parametros,
        success:  function (response){
            _res = $.parseJSON(response);
            for(var _nm in _res.mg){alert(_res.mg[_nm]);}
            if(_res.res!='exito'){
            	alert('se produjo un error al consultar la base de datos;')
            	return;
            }
            
        	if(_res.data.avance==0){
        		alert('error, el proceso no avanzó');
        		return;
        	}
        	
            if(_res.data.avance!='final'){
                procesarCampaDXF(_res.data.avance);
                document.querySelector('#avanceproceso').style.display='block';
                document.querySelector('#avanceproceso').innerHTML=_res.data.avanceP+"%";
                document.querySelector('#avanceproceso').setAttribute('avance',_res.data.avanceP);              
            }else{
                document.querySelector('#avanceproceso').style.display='none';
                document.querySelector('#avanceproceso').innerHTML=_res.data.avanceP+"%";
                document.querySelector('#avanceproceso').setAttribute('avance',_res.data.avanceP);
                cargarDatosCampa(_DataRele.id);
            }
        
        }
    });
}

function ValidarProcesarBotonSHP(){
    _stop='no';
    
    _bot=document.querySelector('a#procesarBotonSHP');    
    _bot.title='';
    _bot.removeAttribute('estado');
    
    _cargas=document.querySelectorAll('#cargarGeometrias #cargando p.carga');
    
    for(_cn in _cargas){    	
    	if(typeof _cargas[_cn] != 'object'){continue;}
    	_comp=_cargas[_cn].getAttribute('extension');
    	if(_cargas[_cn].getAttribute('estado')=='subido'){
    		if(_CheckListSHP[_comp]===undefined){continue;}
    		_CheckListSHP[_comp].s='si';
    	}
    }
    
    for(_comp in _CheckListSHP){
    	if(_CheckListSHP[_comp]===undefined){continue;}
        if(_CheckListSHP[_comp].s=='no'){
            _bot.setAttribute('estado','inviable');
            _bot.title+=_CheckListSHP[_comp].mg;
            _stop='si';	
        }		
    }
    if(_stop=='no'){
        _bot.setAttribute('estado','viable');
        _bot.title+='listo para procesar versión';
    }

}

function ValidarProcesarBotonDXF(){
    _stop='no';
    _bot=document.querySelector('a#procesarBotonDXF');    
    _bot.title='';
    _bot.removeAttribute('estado');
    
    _cargas=document.querySelectorAll('#cargarGeometrias #cargando p.carga');
    
    for(_cn in _cargas){
    	if(typeof _cargas[_cn] != 'object'){continue;}
    	_comp=_cargas[_cn].getAttribute('extension');
    	if(_cargas[_cn].getAttribute('estado')=='subido'){
    		if(_CheckListDXF[_comp]===undefined){continue;}
    		_CheckListDXF[_comp].s='si';			
    	}
    }
    
    for(_comp in _CheckListDXF){
    	if(_CheckListDXF[_comp]===undefined){continue;}
        if(_CheckListDXF[_comp].s=='no'){
            _bot.setAttribute('estado','inviable');
            _bot.title+=_CheckListDXF[_comp].mg;
            _stop='si';	
        }		
    }
    
    if(_stop=='no'){
        _bot.setAttribute('estado','viable');
        _bot.title+='listo para procesar versión';
    }
}







/////////////////////////////////
//SUBE FOTOD DE RELEVAMIENTO
//////////////////////////////////

var _contUpFoto=0;
var _CargasFotos={};

function cargarFotosCampo(_event,_this){
    var files = _this.files;	
    _idcampo=_this.parentNode.parentNode.parentNode.getAttribute('idcampo');
    for (i = 0; i < files.length; i++) {
        _contUpFoto++;
        _CargasFotos[_contUpFoto]='subiendo';
        var parametros = new FormData();
        parametros.append("upload",files[i]);
        parametros.append("nfile",_contUpFoto);
	    parametros.append("idcampa",_DataRele.id);
	    parametros.append("codMarco",_CodMarco);
	    parametros.append("modo","def_Rele");
	    
	    _rutaprelim='./comun_img/cargando.gif';
	    
        cargaDeFoto(files[i].name,_contUpFoto,_idcampo,'',_rutaprelim);

        //Llamamos a los puntos de la actividad
        $.ajax({
            data:  parametros,
            url:   './app_docs/app_docs_guardaarchivo.php',
            type:  'post',
            processData: false,
            contentType: false,
            success:  function (response) {
                var _res = $.parseJSON(response);
                for(var elem in _res.mg){alert(_res.mg[elem]);}

                if(_res.res=='exito'){
                    archivoFotoSubido(_res);
                }else{
                    archivoFotoFallido(_res);
                }
                _Cargas[_res.data.ncont]='terminado';
            }
        });
    }
}

function resInputUpload(_event,_this){
        //console.log(_event);
        _this.style.backgroundColor='#08afd9';
        _this.style.color='#000';
}	

function desInputUpload(_event,_this){
        //console.log(_event);
        _this.removeAttribute('style');
}

	
function archivoFotoSubido(_res){
	
    document.querySelector('#listadoDocumentos p[ncont="'+_res.data.nfile+'"]').setAttribute('estado','subido');
    
    document.querySelector('#listadoDocumentos p[ncont="'+_res.data.nfile+'"]').setAttribute('iddoc',_res.data.nid);
    document.querySelector('#listadoDocumentos p[ncont="'+_res.data.nfile+'"]').setAttribute('ruta',_res.data.ruta);
    document.querySelector('#listadoDocumentos p[ncont="'+_res.data.nfile+'"] img').setAttribute('src',_res.data.ruta);
    
    
    _idcampo=document.querySelector('#listadoDocumentos p[ncont="'+_res.data.nfile+'"]').parentNode.getAttribute('idcampo');
    actualizarInputFotos(_idcampo);
}	

function archivoFotoFallido(_res){
    document.querySelector('#listadoDocumentos p[ncont="'+_res.data.nfile+'"]').innerHTML+=' ...fallido';
    document.querySelector('#listadoDocumentos p[ncont="'+_res.data.nfile+'"]').setAttribute('estado','fallido');
    document.querySelector('#listadoDocumentos p[ncont="'+_res.data.nfile+'"] img').setAttribute('src','');
}

function actualizarInputFotos(_idcampo){
	_input=document.querySelector('#campospersonalizados input[name="'+_idcampo+'"]');
	_lista=document.querySelector('#listadoDocumentos[idcampo="'+_idcampo+'"]');
	 
	_carga=_lista.querySelectorAll('.carga');
	_cargasArr={};
	
	
	for(_cn in _carga){
		if(typeof _carga[_cn] != 'object'){continue;}
		_iddoc=_carga[_cn].getAttribute('iddoc');
		_datadoc={
			'iddoc':_iddoc,
			'ruta':_carga[_cn].getAttribute('ruta'),
			'nombre':_carga[_cn].getAttribute('nombre'),
		}
		_cargasArr[_iddoc]=_datadoc;
	}
	_valor=JSON.stringify(_cargasArr);
	_input=document.querySelector('#campospersonalizados input[name="'+_idcampo+'"]').value=_valor;
}

