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




function listarInstancias(){
	
	_cont=document.querySelector('#divSeleccionProcActivo #listaprocesosactivos');
	_cont.setAttribute('contenido','vacio');
	for(_idin in _Instancias){
		document.querySelector('#divSeleccionProcActivo').setAttribute('contenido','lleno');
		_cont.setAttribute('contenido','lleno');
		_idat=_Instancias[_idin];
		_pdat=_Proc[_idat['id_p_ref_proc_procesos']];
		_aaa=document.createElement('a');
		_aaa.innerHTML =_idat['id_p_ref_proc_procesos']+' . ';
		_aaa.innerHTML+=_pdat['nombre']+' . ';
		_aaa.innerHTML+=_idat['titulo']+' . ';
		_aaa.title=_pdat.descripcion;
		_aaa.setAttribute('idin',_idin);
		_aaa.setAttribute('onclick','formularInstancia(this.getAttribute("idin"))');
		_cont.appendChild(_aaa);
		
	}
            
 }        
 
 
function formularInstancia(_idin){
	 
	 _idat=_Instancias[_idin];
	 _pdat=_Proc[_idat['id_p_ref_proc_procesos']];

	 _form=document.querySelector('#formProcActivo');
	 if(_form.getAttribute('estado')=='procesando'){alert('procesamiento activo. No se puede consultar otro proceso');return;}
	 	 
	 _form.querySelector("[name='titulo']").value=_idat.titulo;
	 _form.querySelector("[name='idproc']").value=_pdat.id;	 
	 _form.querySelector("[name='idinst']").value=_idin;
	 
	 _divcomponentes=_form.querySelector("#componentes");
	 _divcomponentes.innerHTML='';
	 
	for(_nc in _idat.componentes){
		
		_idcomp=_nc;
		
		_form.setAttribute('contenido','completo');
		
		_compdat=_idat.componentes[_idcomp];
		_ccc=document.createElement('div');
		_divcomponentes.appendChild(_ccc);
		_ccc.setAttribute('class','componente');
		_ccc.setAttribute('onclick','toogle(this,"abierto")');
		_ccc.setAttribute('idco',_idcomp);
		_ccc.setAttribute('abierto','-1');
		
		_sp1=document.createElement('span');
		_sp1.setAttribute('class','tipo');
		_sp1.innerHTML=_idat.componentes[_idcomp].tipo;
		_ccc.appendChild(_sp1);

		_sp1b=document.createElement('span');
		_sp1b.setAttribute('class','idlocal');
		_sp1b.innerHTML=_idat.componentes[_idcomp].id_local;
		_ccc.appendChild(_sp1b);
		
		_sp2=document.createElement('span');
		_sp2.setAttribute('class','nombre');
		_sp2.innerHTML=_idat.componentes[_idcomp].nombre;
		_ccc.appendChild(_sp2);
		
		_sp3=document.createElement('span');
		_sp3.setAttribute('class','estado');
		_sp3.innerHTML=_idat.componentes[_idcomp].estado;
		_sp3.setAttribute('estado',_idat.componentes[_idcomp].estado.replace(' ',''));
		_ccc.appendChild(_sp3);
		
		if(
			_compdat.tabla=='ref_capasgeo'
		){
						
			_sp3s=document.createElement('span');
			_sp3s.setAttribute('id','idcapa');		
			
			_sp3s.innerHTML=_compdat.id_ref;
			_sp3.appendChild(_sp3s);
			_sp3.setAttribute('tabla',_compdat.tabla);
			
			_aaa=document.createElement('a');
			_aaa.setAttribute('class','botonDescargaCapa');			
			_aaa.setAttribute('idcapa',_compdat.id_ref);
			_aaa.setAttribute('onclick','event.stopPropagation();descargarSHP(this.getAttribute("idcapa"))');

			_aaa.innerHTML='<img src="./comun_img/descargar.png" alt="descargar">';
			
			_sp3.appendChild(_aaa);
			
			
			_sp7=document.createElement('a');
			_sp7.setAttribute('idcomp',_idcomp);
			_sp7.setAttribute('idin',_idin);
			_sp7.setAttribute('onclick','event.stopPropagation();desvincularCapa(this.getAttribute("idcomp"),this.getAttribute("idin"))');
			_sp7.setAttribute('class','desvincula');
			_sp7.innerHTML='X';
			
			_ccc.appendChild(_sp7);
		}else if(
			_compdat.tabla=='ref_01_documentos'
		){
			
			_sp3s=document.createElement('span');
			_sp3s.setAttribute('id','idcapa');		
			
			_sp3s.innerHTML=_compdat.id_ref;
			_sp3.appendChild(_sp3s);
			_sp3.setAttribute('tabla',_compdat.tabla);
			
			_aaa=document.createElement('a');
			_aaa.setAttribute('class','botonDescargaCapa');			
			_aaa.setAttribute('iddoc',_compdat.id_ref);
			_aaa.setAttribute('onclick','event.stopPropagation();alert("función en desarrollo!!! -\_8P_/-")');

			_aaa.innerHTML='<img src="./comun_img/descargar.png" alt="descargar">';
			
			_sp3.appendChild(_aaa);
			
			
			_sp7=document.createElement('a');
			_sp7.setAttribute('idcomp',_idcomp);
			_sp7.setAttribute('idin',_idin);
			_sp7.setAttribute('onclick','event.stopPropagation();desvincularCapa(this.getAttribute("idcomp"),this.getAttribute("idin"))');
			_sp7.setAttribute('class','desvincula');
			_sp7.innerHTML='X';
			
			_ccc.appendChild(_sp7);
			
		}
		
		_sp4=document.createElement('span');
		_sp4.setAttribute('class','campos');
		_sp4.innerHTML='campos: <span id="cantcamposlink"></span> / <span id="cantcampos"></span>';
		_ccc.appendChild(_sp4);
		
		if(_compdat.flujo_modo =='segundo orden'){			
			
			for(_alt in _compdat.precedentes){
			
				_spalt=document.createElement('span');
				_spalt.setAttribute('alternativa',_alt);
				_spalt.setAttribute('class','alternativa');
				_ccc.appendChild(_spalt);
		
			
				_sp5=document.createElement('a');
				_sp5.setAttribute('class','proceso');
				_sp5.setAttribute('estado','inviable');
				
				_img=document.createElement('img');
				_img.setAttribute('src','./comun_img/procesar_off.png');
				_img.setAttribute('id','img_off');
				_sp5.appendChild(_img);
				
				_img=document.createElement('img');
				_img.setAttribute('src','./comun_img/procesar.png');
				_img.setAttribute('id','img_on');
				_sp5.appendChild(_img);
				
				_spalt.appendChild(_sp5);
			
			}				
		}	
		
		
		for(_nr in _compdat.reportes){
			_sp6=document.createElement('a');
			_sp6.setAttribute('ruta',_compdat.reportes[_nr]);
			_sp6.setAttribute('onclick','event.stopPropagation();procesarReporte(this.getAttribute("ruta"),this.parentNode.getAttribute("idco"),"0","")');
			_sp6.setAttribute('class','reporte');
			//_sp6.setAttribute('estado','inviable');
			
			/*
			_img=document.createElement('img');
			_img.setAttribute('src','./comun_img/procesar_off.png');
			_img.setAttribute('id','img_off');
			_sp6.appendChild(_img);
			*/
			
			_img=document.createElement('img');
			_img.setAttribute('src','./comun_img/reporte.png');
			_img.setAttribute('id','img_on');
			_sp6.appendChild(_img);
			
			_ccc.appendChild(_sp6);				
				
		}
	
		
		
		_contcampos=0;
		_contcamposlink=0;
		for(_nca in _compdat.campos){
			_camdat=_compdat.campos[_nca];
			if(
				_camdat.nombre!=''
				&&
				_camdat.nombre!=null
				
				){
				//console.log(_camdat.nombre);
				_contcampos++;
				_divcpo=document.createElement('div');
				_divcpo.setAttribute('class','campo');
				_divcpo.setAttribute('onclick','event.stopPropagation()');
				_ccc.appendChild(_divcpo);
				
				_spc1=document.createElement('span');
				_spc1.setAttribute('id','nombrecampo');
				_spc1.innerHTML=_camdat.nombre;
				_divcpo.appendChild(_spc1);
				
				//console.log(_idat.componentes[_nc].tabla);
				
				
				if(_idat.componentes[_nc].tabla=='ref_capasgeo'){
					_sel=document.createElement('select');
					_sel.setAttribute('id','linkcampo');
					_sel.setAttribute('idin',_idin);
					_sel.setAttribute('idco',_compdat.id_p_ref_proc_componentes);
					_sel.setAttribute('campo',_nca);
					_sel.setAttribute('onchange','guardaCampoLink(this)');
					_divcpo.appendChild(_sel);
					//console.log('capa: '+_idat.componentes[_nc].id_ref);
					
					_op=document.createElement('option');
					_op.innerHTML='- elegir -';
					_op.value='';
					_sel.appendChild(_op);
					
					for(_no in _CapasDisponibles[_idat.componentes[_nc].id_ref]){
						console.log(_no);
						if(_no.substring(0, 4)=='nom_'){
							if(
								_CapasDisponibles[_idat.componentes[_nc].id_ref][_no]==''
								||
								_CapasDisponibles[_idat.componentes[_nc].id_ref][_no]==null
							){continue;}
							_op=document.createElement('option');
							_op.innerHTML=_CapasDisponibles[_idat.componentes[_nc].id_ref][_no];
							_op.value=_no;
							_sel.appendChild(_op);
							if(_no==_camdat.link){_op.selected = true;}
						}
					}
				}else{
					_spc2=document.createElement('span');
					_spc2.setAttribute('id','linkcampo');
					_spc2.innerHTML=_camdat.link;
					_divcpo.appendChild(_spc2);
				}
					
				if(_camdat.link!=''&&_camdat.link!=null){
					_contcamposlink++;
				}
			}
		}
		
		_sp4.querySelector('#cantcamposlink').innerHTML=_contcamposlink;
		_sp4.querySelector('#cantcampos').innerHTML=_contcampos;
		
		if(_contcamposlink==_contcampos){
			_sp4.setAttribute('estado','listo');
		}
		if(_contcamposlink==_contcampos){
			_sp4.setAttribute('estado','listo');
		}
		
		

		
	}
	
	
	//verifica precedentes de cada componente
	for(_nc in _idat.componentes){
		
		_compdat=_idat.componentes[_nc];
		
		if(_compdat.flujo_modo!='segundo orden'){continue;}
		
		
		for(_alt in _compdat.precedentes){
			
				
			_pres=0;
			_pres_listos=0;
			
			for(_idpre in _compdat.precedentes[_alt]){
				_pres++;
				
				
				_elink=document.querySelector('#componentes .componente[idco="'+_idpre+'"] .estado').getAttribute('estado');
				_ecampos=document.querySelector('#componentes .componente[idco="'+_idpre+'"] .campos').getAttribute('estado');
				
				if(_ecampos=='listo' && _elink=='vinculado'){_pres_listos++;}
				
				_str='#componentes .componente[idco="'+_nc+'"] .alternativa[alternativa="'+_alt+'"] .proceso';
				//console.log(_str);
				document.querySelector(_str).title+=_compdat.precedentes[_alt][_idpre].nombre+'\n';
				
				
			}
			
			if(_pres==_pres_listos){
				document.querySelector('#componentes .componente[idco="'+_nc+'"] .alternativa[alternativa="'+_alt+'"] .proceso').setAttribute('estado','viable');
				document.querySelector('#componentes .componente[idco="'+_nc+'"] .alternativa[alternativa="'+_alt+'"] .proceso').setAttribute('onclick','event.stopPropagation();procesarGenerarComponente(this.parentNode.parentNode.getAttribute("idco"),"0","",this.parentNode.getAttribute("alternativa"))');
				
				
			}
			
			document.querySelector('#componentes .componente[idco="'+_nc+'"] .alternativa[alternativa="'+_alt+'"] .proceso').title+=_pres+'/'+_pres_listos;
			
		}
		
		/*
		_ccc=document.createElement('div');
		_divcomponentes.appendChild(_ccc);
		_ccc.setAttribute('class','componente');
		_ccc.setAttribute('onclick','toogle(this,"abierto")');
		_ccc.setAttribute('idco',_compdat.id_p_ref_proc_componentes);
		_ccc.setAttribute('abierto','-1');
		
		_sp1=document.createElement('span');
		_sp1.setAttribute('class','tipo');
		_sp1.innerHTML=_idat.componentes[_nc].tipo;
		_ccc.appendChild(_sp1);
		*/
		
		_sp2=document.createElement('img');
		_sp2.setAttribute('src','./comun_img/cargando.gif');
		_sp2.setAttribute('class','procesandocomponente');
		_ccc.appendChild(_sp2);		
		
		
	}
}
