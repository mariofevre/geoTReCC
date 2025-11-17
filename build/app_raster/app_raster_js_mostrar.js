/**
*
* funciones de operacion de la pagina 
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





function mostrarListadoRaster(){	
	
	_cont=document.querySelector('#cuadrovalores #listadoRaster');
	_cont.innerHTML='';
		
	for(_id_cob in  _DataRaster.listado.coberturas){
		document.querySelector('#cuadrovalores').setAttribute('listadoRaster','si');
		
		_datR=_DataRaster.listado.coberturas[_id_cob];
		_a=document.createElement('a');
		_a.setAttribute('onclick','cargarRaster("'+_id_cob+'")');
		_a.innerHTML=_datR.id+' - '+_datR.nombre+' '+_datR.tipo+' '+_datR.fecha_ano+' '+_datR.fecha_mes;
		_cont.appendChild(_a);
		
		//MOSTRAR GEOMETRIA
		
	}
	
	
	dibujarListadoRasterMapa();
	
}



function mostrarListadoCandidatosDocs(_data){

	document.querySelector('#cuadrovalores').setAttribute('divCandidatosDocRaster','si');
	
	_cont=document.querySelector('#divCandidatosDocRaster #listado_candidatos');
	_cont.innerHTML='<span id="nodato">- sin documentos compatibles - </span>';
	
	for(_np in _data.orden.psdir){
		
		_id_psdir=_data.orden.psdir[_np];
		_dat_psdir=_data.psdir[_id_psdir];
		
			
		_titulocreado='no'; //solo lo creamos si tene archivos candidatos.
		
		for(_id_arch in _dat_psdir.archivos){
			//console.log(_id_arch);
			_dat_arch=_dat_psdir.archivos[_id_arch];
			
			//console.log(_dat_arch.nombre);
			_cumpleinicio='no';
			
			_inicio='S2A_MSIL2A';
			if(_inicio == _dat_arch.nombre.substring(0, _inicio.length)){
				_cumpleinicio='si';
				console.log('cumple inicio');
			}
			
			_inicio='S2B_MSIL2A';
			if(_inicio == _dat_arch.nombre.substring(0, _inicio.length)){
				_cumpleinicio='si';
				console.log('cumple inicio');
			}
			
			_cumpleextension='no';
			
			_ext='zip';
			_s=_dat_arch.nombre.split('.');
			
			if(_ext == _s[_s.length-1]){
				_cumpleextension='si';
				console.log('cumple extension');
			}
			

			
			if(_cumpleextension=='si' && _cumpleinicio=='si'){
				
				_no=_cont.querySelector('#nodato');
				if(_no != null){_cont.removeChild(_no);}
				
				if(_titulocreado=='no'){
					_h3=document.createElement('h3');
					_h3.innerHTML=_dat_psdir.nombre;
					document.querySelector('#divCandidatosDocRaster #listado_candidatos').appendChild(_h3);
					_titulocreado='si';
				}
			
				_a=document.createElement('a');
				_a.setAttribute('onclick','procesarDocARaster("'+_id_arch+'","","undoc")');
				_a.setAttribute('proceso','no');
				_a.setAttribute('iddoc',_id_arch);
				_a.innerHTML='<img class="ok" src="./comun_img/check-sinborde.png">';
				_a.innerHTML+='<img class="cargando" src="./comun_img/cargando.gif">';
				_a.innerHTML+='<span id="nombre">'+_dat_arch.nombre+'</span>';
				_a.title=_dat_arch.nombre;
				
				for(_idc in _DataRaster.listado.coberturas){
					_datc = _DataRaster.listado.coberturas[_idc];
					console.log(_datc.doc_nombre +" vs "+ _dat_arch.nombre);
					if(_datc.doc_nombre == _dat_arch.nombre){
						// este doc ya tiene una cobertura
						_a.setAttribute('onclick','alert("este documento ya se encuentra incluido en la base raster y no será procesado")');
						_a.setAttribute('proceso','localizada');
						break;
					}
				}
				
				_cont.appendChild(_a);
			}
		}						
	}
}



function mostrarBanda(_idr,_id_tipo_banda){	
	
	_size=mapa.get('size');
	_ext=_view.calculateExtent(_size);	
	_datr=_DataRaster.listado.coberturas[_idr];	
	consultarSampleoRasterTCI(_ext,_size,_id_tipo_banda,_idr);
}

function mostrarRaster(_idr){	
	
	document.querySelector('#cuadrovalores').setAttribute('listadoRaster','no');
	document.querySelector('#cuadrovalores').setAttribute('divCandidatosDocRaster','no');
	document.querySelector('#cuadrovalores').setAttribute('formRaster','si');
	
	
	
	_form=document.querySelector('#cuadrovalores #formRaster');
	_form.setAttribute('idraster',_idr);
	
	_datr=_DataRaster.listado.coberturas[_idr];
	
	if(_datr['ft_dibujada']==true){
		_ft=_source_listaraster.getFeatureById(_idr);
		_ext=_ft.getGeometry().getExtent();
		_view.fit(_ext);		
		_size=mapa.get('size');		
		_id_tipo_banda='13';// TODO este parámetro que está fijo, debería definirse en la base de datos como banda por defecto para cada raster
		consultarSampleoRasterTCI(_ext,_size,_id_tipo_banda,_idr);
	}
	
	_d =new Date(_datr.fecha_ano+'-'+_datr.fecha_mes+'-'+_datr.fecha_dia);
	
	_form.querySelector('[name="fecha"]').valueAsDate=_d;
	_form.querySelector('[name="hora_utc"]').value=_datr.hora_utc;
	
	_form.querySelector('[name="doc_nombre"]').value=_datr.doc_nombre;
	_form.querySelector('#iddoc').innerHTML=_datr.id_p_ref_01_documentos;
		
	
	_form.querySelector('.estado').setAttribute('estado',_datr.zz_estado);
	
	 
	_oc= 'procesarDocARaster('+_datr.id_p_ref_01_documentos+','+_idr+',"uno")';
	document.querySelector('#formRaster .estado #procesar').setAttribute('onclick',_oc);
	
	_idtipo=_datr.id_p_ref_raster_tipos_diccionario;
	
	if(_DataRaster.listado.tipos[_idtipo]==undefined){return;}
	
	
	_datt=_DataRaster.listado.tipos[_idtipo];
	
	_form.querySelector('[id="tipo_nombre"]').innerHTML=_datt.nombre;
	_form.querySelector('[id="tipo_descripcion"]').innerHTML=_datt.descripcion;
	_form.querySelector('[id="tipo_url_consulta"]').innerHTML=_datt.url_consulta;
	_form.querySelector('[id="tipo_url_consulta"]').setAttribute('href',_datt.url_consulta);
	
	_form.querySelector('[id="titulobandas"]').setAttribute('estado','nolisto');
	
	_contb=_form.querySelector('[id="bandas"]');
	_contb.innerHTML='';
	
	_procesadas=0;
	_pendientes=0;
	for(_nb in _datt.bandas){
		
		_id_tipo_banda=_nb;
		_datb=_datt.bandas[_nb];
		_datrb=_datr.bandas[_datb.id];

		if(_datrb.estado=="sin cargar"){
			_pendientes++;
		}else if(_datrb.estado=="procesar_hecho"){
			_procesadas++;
		}
		
		_p=document.createElement('a');
		_p.setAttribute('class','banda');
		_p.setAttribute('id_tipo_banda',_id_tipo_banda);
		_p.setAttribute('estado',_datrb.estado);
		_p.setAttribute('onclick','mostrarBanda('+_idr+','+_id_tipo_banda+')');
		_contb.appendChild(_p);
		
		
		_l=document.createElement('label');
		_p.appendChild(_l);
		_l.innerHTML=_datb.numero+_datb.indice;
		
		_a=document.createElement('a');
		_l.appendChild(_a);
		_a.setAttribute('class','procesar');
		_oc= 'procesarDocARaster('+_datr.id_p_ref_01_documentos+','+_idr+',"uno","'+_id_tipo_banda+'")';
		_a.setAttribute('onclick','event.stopPropagation();'+_oc);
		_a.innerHTML="<img src='./comun_img/procesar.png'>";
		
		_i=document.createElement('img');
		_l.appendChild(_i);
		_i.setAttribute('src','./comun_img/procesar_hecho.png');
		_i.setAttribute('class','procesar_hecho');
		
		_i=document.createElement('img');
		_l.appendChild(_i);
		_i.setAttribute('src','./comun_img/cargando.gif');
		_i.setAttribute('class','procesar_cargando');
		
		
		_s=document.createElement('span');
		_p.appendChild(_s);
		_s.innerHTML=_datb.nombre;
		
		_l=document.createElement('label');
		_p.appendChild(_l);
		_l.innerHTML='resolucion';
		_s=document.createElement('span');
		_p.appendChild(_s);
		_s.innerHTML=_datb.resolucion;

		_l=document.createElement('label');
		_p.appendChild(_l);
		_l.innerHTML='descripcion';
		_s=document.createElement('span');
		_p.appendChild(_s);
		_s.innerHTML=_datb.descripcion;
				
		_l=document.createElement('label');
		_p.appendChild(_l);
		_l.innerHTML='longitud de onda (&micro;m)';
		_s=document.createElement('span');
		_p.appendChild(_s);
		_s.innerHTML=_datb.longitud_central;

		
		_d=document.createElement('div');
		
	}
	
	
	if(_datr.zz_estado=='localizada'){
		if(_pendientes==0 && _procesadas>0){
			_form.querySelector('[id="titulobandas"]').setAttribute('estado','hecho');
		}else if(_pendientes>0){
			_form.querySelector('[id="titulobandas"]').setAttribute('estado','procesable');		
			_a=_form.querySelector("#cuadrovalores #formRaster #titulobandas[estado='procesable'] a#procesar");
			_oc= 'procesarDocARaster('+_datr.id_p_ref_01_documentos+','+_idr+',"bandas","")';
			_a.setAttribute('onclick',_oc);			
		}
		
	}
	//DESTACAR EN MAPA
	
}
