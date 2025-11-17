
/**
* indice_municipios.php
*
* aplicación para gestionar el envío y validación de archivos SHP
 * 
 *  
* @package    	geoTReCC
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar>
* @author    	http://www.trecc.com.ar
* @author		based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
* @copyright	2024 TReCC SA
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2018 - Universidad de Buenos Aires
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2017 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (GPL-3.0)
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
* 
*
*/
//operación del formulario central par ala carga de SHP

function limpiarfomularioversion(){
	document.querySelector('#formcargaverest select#crs').options[1].selected;
	document.querySelector('#formcargaverest').setAttribute('idver','');
	document.querySelector('#formcargaverest #txningunarchivo').style.display='block';
	document.querySelector('#formcargaverest #archivoscargados').innerHTML='';
	document.querySelector('#formcargaverest #camposident').innerHTML='';
	document.querySelector('#formcargaverest #carga').style.display='none';
	document.querySelector('#formcargaverest #carga').style.display='none';
	document.querySelector('#formcargaverest #botonformversion').style.display='block';
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

var _Procesarcampos;
function actualizarCadenaCampos(){
	ValidarProcesarBoton();
	_Procesarcampos={};
	_filas=document.querySelectorAll('#formcargaverest #carga #camposident .enshp');
	for(_nf in _filas){
		if(typeof _filas[_nf] !='object'){continue;}
		_parent=_filas[_nf].parentNode.parentNode;
		if(_parent.getAttribute('origen')=='aux'){continue;}
		_nom=_filas[_nf].getAttribute('nom');
		if(_parent.querySelector('#entabla').getAttribute('nom')==''){
			if(_parent.querySelector('#crear').checked){
				_Procesarcampos[_nom]={};
				_Procesarcampos[_nom]['acc']='crear';
				
				if(_parent.querySelector('#rename').value!=''){
					_Procesarcampos[_nom]['nom']=_parent.querySelector('#rename').value;
				}else{
					_Procesarcampos[_nom]['nom']=_nom;
				}
			}
		}else{
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


function procesarVersion(_this){
	_stop='no';
	for(_comp in _checkList){
		if(_checkList[_comp].s=='no'){
			alert(_checkList[_comp].mg);
			_stop='si';	
		}		
	}
	if(_stop=='si'){return;}
	
	guardarVer(_this,'si');
	
}

function procesarVersion2(_this,_avance){
	var _this =_this;
	var _parametros = {
		'tabla': _this.parentNode.getAttribute('tabla'),
		'accion': 'procesar versión',
		'id': document.querySelector('#formcargaverest #carga #idnv').innerHTML,
		'avance':_avance
	};
	
	$.ajax({
		url:   'ed_version_procesa.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='exito'){
				if(_res.data.avance!='final'){
					procesarVersion2(_this,_res.data.avance);
					document.querySelector('#avanceproceso').style.display='block';
					document.querySelector('#avanceproceso').innerHTML=_res.data.avanceP+"%";
					document.querySelector('#avanceproceso').setAttribute('avance',_res.data.avanceP);
				}else{
					document.querySelector('#avanceproceso').style.display='none';
					impiarfomularioversion();
					
				}
			}
		}
	});
}



function ValidarProcesarBoton(){
	actualizarestadoCampos();
	_stop='no';
	_bot=document.querySelector('#ProcesarBoton');
	_bot.title=''
	_bot.removeAttribute('estado');
	for(_comp in _checkList){
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
	for(_nd in _divs){
		if(typeof _divs[_nd]!='object'){continue;}
		if(_divs[_nd].querySelector('#espacioshp > .enshp') == null){
			_checkList.dbf.s='no';
			_checkList.dbf.mg='al menos un campo de la tabla carece de un campo asociado del shapefile.';
		}
	}	
}

function eliminarCandidatoVersion(_this){
	console.log('eliminarCandidatoVersion()');
	
	if(confirm("¿Confirma que quiere eliminar este candidato a versión? \n Si lo hace se eliminarán los archivos que haya subido y se guardará registro en la papelera de los datos cargados en el formulario.")){
		console.log('o');
		
		var _parametros = {
			'tabla': _this.parentNode.parentNode.getAttribute('tabla'),
			'accion': 'borrar candidato',
			'id':_this.parentNode.querySelector('#idnv').innerHTML
		}
			
		$.ajax({
		url:   'ed_version_borra.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			var _res = $.parseJSON(response);
				console.log(_res);				
				for(_nm in _res.mg){alert(_res.mg[_nm]);}
				if(_res.res=='exito'){
					
				}
			}
		});
				
	}
}

function formversion(_this){
	console.log('formversion()');
	
	limpiarfomularioversion();
	
	//intenta generar una nueva versión candidata para este usuario u esta capa
	var _parametros = {
		'tabla': _this.parentNode.getAttribute('tabla'),
		'accion': 'crear nueva versión'
	};
	$.ajax({
		url:   'ed_version_crea.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			var _res = $.parseJSON(response);
				console.log('a');
				console.log(_res);				
				for(_nm in _res.mg){alert(_res.mg[_nm]);}
				
				if(_res.res=='exito'){
					
					document.querySelector('#formcargaverest #carga').style.display='block';
					
					//_this.style.display='none';
					
					
					if(_res.data.nid!=undefined){
						document.querySelector('#formcargaverest #carga #idnv').innerHTML=_res.data.nid;
						document.querySelector('#formcargaverest').setAttribute('idver',_res.data.nid);
					}else{
						document.querySelector('#formcargaverest #carga #idnv').innerHTML=_res.data.version.id;
						document.querySelector('#formcargaverest').setAttribute('idver',_res.data.version.id);
						document.querySelector('#formcargaverest #carga #nomver').innerHTML='nombre: '+_res.data.version.nombre;
					}
					
					
					for(_na in _res.data.archivos){
						document.querySelector('#formcargaverest #txningunarchivo').style.display='none';
						_fil=document.createElement('p');
						_fil.innerHTML=_res.data.archivos[_na].nom;
						_fil.setAttribute('fileExt',_res.data.archivos[_na].ext);				
						document.querySelector('#formcargaverest #carga #archivoscargados').appendChild(_fil);						
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
						
						
					}else if(_res.data.prj.stat=='viableobs'){
						_checkList.prj.s='si';
						_checkList.prj.ms='se adoptará el prj del formulario que difiere del explicitado en el archivo subido';
						_sel=document.querySelector('#crs');
						for(_no in _sel.options){
							if(_sel.options[_no].value==_res.data.prj.def){
								_sel.options[_no].selected=true;
								
								_ppp=document.querySelectorAll('#archivoscargados [fileext="prj"], #archivoscargados [fileext="qpj"]');
								for(_np in _ppp){
									if(typeof _ppp[_np] == 'object'){
										_ppp[_np].setAttribute('estado','viableobs');
										_ppp[_np].title=_res.data.prj.mg;
									}
								}
							}
						}
					}else{
						_checkList.prj.s='no';
						_checkList.prj.ms=_res.data.prj.mg;
						_ppp=document.querySelectorAll('#archivoscargados [fileext="prj"], #archivoscargados [fileext="qpj"]');
						for(_np in _ppp){
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
						for(_np in _ppp){
							if(typeof _ppp[_np] == 'object'){
								_ppp[_np].setAttribute('estado','viable');
								_ppp[_np].title=_res.data.shp.mg;
							}
						}
					}else if(_res.data.prj.stat=='inviable'){
						_checkList.shp.s='no';
						_checkList.shp.ms=_res.data.shp.mg;
						_ppp=document.querySelectorAll('#archivoscargados [fileext="shp"], #archivoscargados [fileext="shx"], #archivoscargados [fileext="dbf"]');
						for(_np in _ppp){
							if(typeof _ppp[_np] == 'object'){
								_ppp[_np].setAttribute('estado','inviable');
								_ppp[_np].title=_res.data.shp.mg;
							}
						}
						alert(_res.data.shp.mg);
					}
					
					
					for(_col in _res.data.columnas){
						
						if(_col=='id'){continue;}
						if(_col=='geo'){continue;}
						if(_col=='id_sis_versiones'){continue;}
						if(_col=='zz_obsoleto'){continue;}
						
						_fil=document.createElement('div');
						_fil.setAttribute('origen','tabla');
						
						_nom=document.createElement('div');
						_nom.setAttribute('id','entabla');
						_nom.setAttribute('nom',_col);
						_nom.innerHTML=_col;						
						_fil.appendChild(_nom);
						
						_nom=document.createElement('div');
						_nom.setAttribute('id','espacioshp');	
						_nom.setAttribute('ondrop',"drop(event)");
						_nom.setAttribute('ondragover',"allowDrop(event)");
								
						_fil.appendChild(_nom);
						/*
						_nom=document.createElement('input');
						_nom.setAttribute('id','rename');	
						_fil.appendChild(_nom);
						*/
						/*
						_nom=document.createElement('input');
						_nom.setAttribute('type','checkbox');
						_nom.setAttribute('id','crear');	
						_fil.appendChild(_nom);
						*/
						
						document.querySelector('#formcargaverest #carga #camposident').appendChild(_fil);
						
					}
					
					_c=0;
					
					_Icampos={};
					if(_res.data.version.instrucciones!=''&&_res.data.version.instrucciones!=null){
						_Icampos = $.parseJSON(_res.data.version.instrucciones);
					}
					
					console.log(_Icampos);
					for(_col in _res.data.dbf.campos){
						
						_dat=_res.data.dbf.campos[_col];
						_norig=_dat.name;
						_nombre=_dat.name;
						
						if(_norig=='id'){continue;}
						if(_norig=='geo'){continue;}
						if(_norig=='id_sis_versiones'){continue;}
						if(_col=='zz_obsoleto'){continue;}
						
						
						_crear=false;
						if(_Icampos[_norig]!=null){
							_nombre=_Icampos[_norig].nom;
							if(_Icampos[_norig].acc=='crear'){_crear=true;}
						}
						console.log(_nombre+' '+_norig);
						_ref=document.querySelector('#formcargaverest #carga #camposident #entabla[nom="'+_nombre+'"]');
						
						
						if(_ref==null){
							
							_fil=document.createElement('div');
							_fil.setAttribute('id','entabla')
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
							
							_nom=document.createElement('input');
							_nom.setAttribute('id','rename');
							_nom.setAttribute('onkeyup','actualizarCadenaCampos()');
							if(_norig!=_nombre){_nom.value=_nombre;}	
							_fil.appendChild(_nom);
							
							_nom=document.createElement('input');
							_nom.setAttribute('type','checkbox');
							_nom.checked=_crear;
							_nom.setAttribute('id','crear');	
							_nom.setAttribute('onchange','actualizarCadenaCampos()');
							_fil.appendChild(_nom);
	
							
							document.querySelector('#formcargaverest #carga #camposident').appendChild(_fil);
							
						}else{
							
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
					
					_nom=document.createElement('input');
					_nom.setAttribute('id','rename');	
					_nom.setAttribute('onkeyup','actualizarCadenaCampos()');
					_fil.appendChild(_nom);
					
					_nom=document.createElement('input');
					_nom.setAttribute('type','checkbox');
					_nom.setAttribute('onchange','actualizarCadenaCampos()');
					_nom.setAttribute('id','crear');	
					_fil.appendChild(_nom);
					
					
					document.querySelector('#formcargaverest #carga #camposident').appendChild(_fil);
				
				
					actualizarCadenaCampos();		
					
				}
				
				
				
				/*
				_Tablas=_res.data.tablas;
				
				_cont=document.querySelector('#menutablas #lista');
				for(_nn in _Tablas['est']){					
					_aaa=document.createElement('a');
					_aaa.innerHTML=_Tablas['est'][_nn];
					_aaa.setAttribute('tabla',_Tablas['est'][_nn]);
					_aaa.setAttribute('onclick','cargarAtabla(this)');
					_cont.appendChild(_aaa);
				}*/
			document.querySelector('#formcargaverest #carga').style.display='block';
			document.querySelector('#formcargaverest #botonformversion').style.display='none';
		}
	});


	
}

function guardarVer(_this,_procesar){

	//intenta generar una nueva versión candidata para este usuario u esta capa
	var _this=_this;
	var _procesar=_procesar;
	var _parametros = {
		'tabla': _this.parentNode.parentNode.getAttribute('tabla'),
		'accion': 'guardar version',
		'instrucciones':_this.parentNode.querySelector('#verproccampo').innerHTML,
		'fi_prj':_this.parentNode.querySelector('select#crs').value,
		'id':_this.parentNode.querySelector('#idnv').innerHTML
	};
	$.ajax({
		url:   'ed_version_cambia.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			
			var _res = $.parseJSON(response);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			console.log(_res);
			
			if(_procesar=='si'){procesarVersion2(_this.parentNode,0);return;}
			formversion(_this.parentNode);
			
		}
	});
}	
