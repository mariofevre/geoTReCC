/**
*
* funciones de representación HTML de contenidos asincrónicos
*  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2017 TReCC SA
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

function mostrarColeccion(_idcapa){
	
	_cont=document.querySelector('#colecciones_listado');
	for(_idr in _DataCapas[_idcapa].registros){
		_rdat=_DataCapas[_idcapa].registros[_idr];
		
		_campo=_DataCapas[_idcapa].coleccion.campo;
		_campob=_DataCapas[_idcapa].coleccion.campob;
		if(_campo==''){_campo ='texto1'}
		
		_a=document.createElement('a');
		_a.innerHTML=_idr+'<br>'+_rdat[_campo];
		
		if(_campob!=''&&_campob!=null){
			_a.innerHTML+='<br>'+_rdat[_campob];
		}
		
		//_a.title=_rdat[_campo];
		_a.setAttribute('idreg',_idr);
		
		_cont.appendChild(_a);
		_a.setAttribute('onclick','fijarObjeto('+_idcapa+','+_idr+')');

	}
}



function mostrarPanel(_idp,_accion){

	_idPanel = _idp;
	
	resetVarGlobales();
	
	borrarCapas();
	
	console.log(_idp);
	console.log(_DataPaneles[_idp]);
	
	_DataPanel=_DataPaneles[_idp];
	_DataConsultas=_DataPaneles[_idp].consultas; 
	_DataCapas=_DataPaneles[_idp].capas;
	_DataCapasRaster=_DataPaneles[_idp].capasraster; 
	_DataCapasColec=_DataPaneles[_idp].colecciones; 
	
	if(_accion=='formular'){formularPanel(_idp);}	
	
	document.querySelector('#encabezado_panel #titulo').innerHTML=_DataPanel.titulo;
	document.querySelector('#encabezado_panel #descripcion').innerHTML=_DataPanel.descripcion;
	
	for(_idcol in _DataPanel.colecciones){
		_idcapa=_DataPanel.colecciones[_idcol].id_p_ref_capasgeo;
		console.log(_idcol);
		console.log(_DataPanel.colecciones[_idcol]);
		
		if(_DataCapas[_idcapa]==undefined){_DataCapas[_idcapa]={};}
		
		_DataCapas[_idcapa]['coleccion']={
			'idcolec':_idcol,
			'simbologia':_DataPanel.colecciones[_idcol].simbologia,
			'campo': _DataPanel.colecciones[_idcol].campo,
			'campob':_DataPanel.colecciones[_idcol].campob,
		}
	}
	
	
	if(_DataPanel.mba=='Bing'){
		baseMapaaESRI();
	}else if(_DataPanel.mba=='Google'){
		baseMapaaGoogle();
	}else if(_DataPanel.mba=='IGN'){
		baseMapaaIGN();
	}else if(_DataPanel.mba=='OSM'){
		baseMapaaOSM();
	}else if(_DataPanel.mba=='OSMgris'){
		baseMapaaOSMgris();
	}else if(_DataPanel.mba=='ESRI'){
		baseMapaaESRI();
	}
	
	cargarCapas();
	
	console.log(_DataPanel.muestra_marco);
	if(_DataPanel.muestra_marco=='1'){		
		cargarCapaMarco();
	}
		
}

function mostrarMenuAdm(){
	
	_cont=document.querySelector('#menu_admin_flotante #listadito_paneles');
	_cont.innerHTML="";
	for(_idp in _DataPaneles){
		_a=document.createElement('a');
		_cont.appendChild(_a);
		_a.innerHTML=_idp+' '+_DataPaneles[_idp].titulo;
		_a.setAttribute('idpanel',_idp);
		_a.setAttribute('onclick','event.preventDefault();mostrarPanel('+_idp+',"formular")');
		
	}
	
	
	document.querySelector('#encabezado_panel #descripcion').innerHTML=_DataPanel.descripcion;
	
	
}


function formularPanel(_idp){
	
	
	//datos de la tabla /// ref_comu_paneles ///
	_form=document.querySelector("#menu_admin_flotante #formulario_panel");	
	_dat=_DataPanel;
	_form.querySelector("[name='idp']").value=_idp;	
	
	_form.querySelector("[name='descripcion']").value=_dat.descripcion;	
	_form.querySelector("[name='nombre']").value=_dat.titulo;	
	_form.querySelector("[name='mba']").value=_dat.mba;	
	_form.querySelector("[name='x']").value=_dat.x;	
	_form.querySelector("[name='y']").value=_dat.y;	
	_form.querySelector("[name='z']").value=_dat.z;	
	if(_dat.visible=='1'){_form.querySelector("[name='visible']").checked=true}else{_form.querySelector("[name='visible']").checked=false}
	if(_dat.muestra_marco=='1'){_form.querySelector("[name='muestra_marco']").cheked=true}else{_form.querySelector("[name='muestra_marco']").cheked=false}




	//datos de la tabla /// ref_comu_colecciones /// datos que son mostrados a consulta del usuario
	
	_li=_form.querySelector("#formulario_coleccion [name='id_p_ref_capasgeo']");
	_li.innerHTML='<option value="">- elegir -</option>';
	for(_nc in _Listado_Capas){
		_op=document.createElement('option');
		_op.value=_Listado_Capas[_nc].id;
		_op.innerHTML=_Listado_Capas[_nc].id+' - '+_Listado_Capas[_nc].nombre;
		_li.appendChild(_op);
	}
	
	_col=Object.values(_dat.colecciones)[0];	
	if(_col!==undefined){
		console.log(_col);
		if(_col.id_p_ref_capasgeo!=null&&_col.id_p_ref_capasgeo!=''){
			console.log(_col.id_p_ref_capasgeo);
			_form.querySelector('[name="modelocolec"][value="vectorial"]').checked=true;
			_form.querySelector('[name="modelocolec"][value="vectorial"]').onchange();
		}else{
			_form.querySelector('[name="modelocolec"][value="raster"]').checked=true;
			_form.querySelector('[name="modelocolec"][value="raster"]').onchange();
		}
		
		_li.value=_col.id_p_ref_capasgeo;
		cambiaModoDato(_li);
		
		_nameselect='campo';
		_idcapa=_col.id_p_ref_capasgeo;
		atualizarSelCampos(_nameselect,_idcapa);
		if(_col.campo==null){_col.campo='';}
		_form.querySelector('select[name="campo"]').value=_col.campo;
		
		
		_nameselect='campob';
		_idcapa=_col.id_p_ref_capasgeo;
		if(_col.campob==null){_col.campob='';}
		_form.querySelector('select[name="campob"]').value=_col.campob;


		_lir=_form.querySelector("#formulario_coleccion [name='fi_img_raster']");
		_lir.innerHTML='<option value="">- elegir -</option>';		
		_lirov=_form.querySelector("#formulario_coleccion [name='fi_img_raster_ovr']");
		_lirov.innerHTML='<option value="">- elegir -</option>';		
		for(_ndir in _DataDocs.psdir){
			_datadir=_DataDocs.psdir[_ndir];
			console.log(_datadir);
			for(_ndoc in _datadir.archivos){	
				_datdoc=_datadir.archivos[_ndoc];
				_noms=_datdoc.nombre.split('.');
				_ext=_noms[_noms.length-1];
				if(_ext=='tif'){
					_op=document.createElement('option');
					_op.value=_datdoc.archivo;
					_op.innerHTML=_datdoc.nombre;
					_lir.appendChild(_op);
				}else if(_ext=='ovr'){
					_op=document.createElement('option');
					_op.value=_datdoc.archivo;
					_op.innerHTML=_datdoc.nombre;
					_lirov.appendChild(_op);
				}
			}
		}	
		console.log(_col);
		console.log(_col.fi_img_raster);
		_lir.value=_col.fi_img_raster;
		_lirov.value=_col.fi_img_raster_ovr;
	}
	
	//datos de la tabla /// ref_comu_capas /// capas que son mostradas al usuario
	
	_li=_form.querySelector("#listadito_capas");
	_li.innerHTML='';
	
	
	_a_capa_o=null;
	for(_nc in _DataCapas){
		_idcapa=_nc;
		if(_idcapa==undefined){continue;}
		if(_idcapa==null){continue;}
		if(_idcapa==''){continue;}
		if(_idcapa=='null'){continue;}
		if(_idcapa=='undefined'){continue;}	
		console.log(_idcapa);
		_datc=_DataCapas[_idcapa];
		
		_a=document.createElement('a');
		_form.querySelector("#listadito_capas").appendChild(_a);
		
		_a.setAttribute('onclick','formularCapaPanel("'+_idcapa+'","'+_idp+'")');
		_a.innerHTML = _datc.id +' - '+ _datc.nombre;
		if(_col.id_p_ref_capasgeo == _idcapa){
			_a.innerHTML +=' [capa obligatoria] ';
			if(_a_capa_o==null){_a_capa_o=_a;}
		}else{
			_aa=document.createElement('a');
			_a.appendChild(_aa);
			_aa.innerHTML='Eliminar';
			_aa.setAttribute('class','elimiar');
		}
	}
	
	for(_nc in _DataCapasRaster){
		_idcapa=_nc;
		if(_idcapa==undefined){continue;}
		if(_idcapa==null){continue;}
		if(_idcapa==''){continue;}		
		if(_idcapa=='null'){continue;}
		if(_idcapa=='undefined'){continue;}	
		_datc=_DataCapasRaster[_idcapa];
		
		_a=document.createElement('a');
		_form.querySelector("#listadito_capas").appendChild(_a);
		
		_a.setAttribute('onclick','formularCapaPanel("'+_idcapa+'","'+_idp+'")');
		_noms=_datc.fi_raster.split('/');
		_nom=_noms[_noms.length-1];
		
		_a.innerHTML = _datc.id +' - '+ _nom;
		if(_col.id_p_ref_capasgeo == null && _col.id == _datc.id){
			_a.innerHTML +=' [capa obligatoria] ';
			if(_a_capa_o==null){_a_capa_o=_a;}
		}else{
			_aa=document.createElement('a');
			_a.appendChild(_aa);
			_aa.innerHTML='Eliminar';
			_aa.setAttribute('class','elimiar');
		}
	}
	
	if(_a_capa_o!==null){
		_a_capa_o.onclick();
	}
	
	_li=_form.querySelector("#formulario_capa [name='id_p_ref_capasgeo']");
	_li.innerHTML='<option value="">- elegir -</option>';
	for(_nc in _Listado_Capas){
		_op=document.createElement('option');
		_op.value=_Listado_Capas[_nc].id;
		_op.innerHTML=_Listado_Capas[_nc].id+' - '+_Listado_Capas[_nc].nombre;
		_li.appendChild(_op);
	}
	
	_lir=_form.querySelector("#formulario_capa [name='fi_raster']");
	_lir.innerHTML='<option value="">- elegir -</option>';		
	_lirov=_form.querySelector("#formulario_capa [name='fi_raster_ovr']");
	_lirov.innerHTML='<option value="">- elegir -</option>';		
	for(_ndir in _DataDocs.psdir){
		_datadir=_DataDocs.psdir[_ndir];
		console.log(_datadir);
		for(_ndoc in _datadir.archivos){	
			_datdoc=_datadir.archivos[_ndoc];
			_noms=_datdoc.nombre.split('.');
			_ext=_noms[_noms.length-1];
			if(_ext=='tif'){
				_op=document.createElement('option');
				_op.value=_datdoc.archivo;
				_op.innerHTML=_datdoc.nombre;
				_lir.appendChild(_op);
			}else if(_ext=='ovr'){
				_op=document.createElement('option');
				_op.value=_datdoc.archivo;
				_op.innerHTML=_datdoc.nombre;
				_lirov.appendChild(_op);
			}
		}
	}	
}

function atualizarSelCampos(_nameselect,_idcapa){
	
	_sel=document.querySelector("#menu_admin_flotante #formulario_panel select[name='"+_nameselect+"']");	
	_sel.innerHTML='';
	
	_op=document.createElement('option');
	_sel.appendChild(_op);
	_op.innerHTML= '- ninguno -';
	_op.value='';
	
	for(_p in _DataCapas[_idcapa]){
		if(_p.slice(0,7)!='nom_col'){
			console.log('no es nombre de campo campo');
			continue;
		}
		if(_DataCapas[_idcapa][_p]==''){continue;}
		
		_op=document.createElement('option');
		_sel.appendChild(_op);
		_op.innerHTML=_DataCapas[_idcapa][_p];
		_v=_p.replace('nom_col_','');
		_v=_v.replace('text','texto');
		_v=_v.replace('num','numero');
		_op.value=_v;
	}
}



function formularCapaPanel(_idcapacomu,_idp){
	
	_datc=null;
	if(_DataCapas[_idcapacomu]!=undefined){
		_datc=_DataCapas[_idcapacomu];	
		_modelocapa='vectorial';
	}else if(_DataCapasRaster[_idcapacomu]!=undefined){
		_datc=_DataCapasRaster[_idcapacomu];	
		_modelocapa='raster';
	}
	if(_datc==null){
		console.log('capa solicitada:'+_idcapacomu+', no enconetrada en el DOM');
	}
	
	console.log(_datc);
	_subform=document.querySelector("#menu_admin_flotante #formulario_panel #formulario_capa");	
	
	_subform.querySelector("[name='modelocapa'][value='"+_modelocapa+"']").checked=true;	
	_subform.querySelector("[name='modelocapa'][value='"+_modelocapa+"']").onchange();

	if(_modelocapa=='vectorial'){
		
		_subform.querySelector("[name='simbologia']").value=_datc.simbologia;	
		_subform.querySelector("[name='id_p_ref_capasgeo']").value=_datc.id_p_ref_capasgeo;	
	
	}
	if(_modelocapa=='raster'){
		
		_subform.querySelector("[name='simbologia_raster']").value=_datc.simbologia_raster;	
		_subform.querySelector("[name='etiquetas_raster']").value=_datc.etiquetas_raster;		
		_subform.querySelector("[name='fi_raster']").value=_datc.fi_raster;	
		_subform.querySelector("[name='fi_raster_ovr']").value=_datc.fi_raster_ovr;	
	}
		
	
}
