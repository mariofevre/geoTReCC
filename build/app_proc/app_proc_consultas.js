/**
*
* funciones js para ejecutar consultas
 * 
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
* 
*
*/



function consultarPermisos(){
	var _IdMarco = getParameterByName('id');
	var _CodMarco = getParameterByName('cod');
	_parametros = {
		'codMarco':_CodMarco	
	}
	$.ajax({
        url:   './app_capa/app_capa_consultar_permisos.php',
        type:  'post',
        data: _parametros,
        error:  function (response){alert('error al consultar el servidor');},
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg){alert(_res.mg[_nm]);}
            if(_res.res!='exito'){
            	alert('error al consultar la base de datos');
            }
        }
 	});	
}
consultarPermisos();



function consultarProc(){
    document.querySelector('#divSeleccionProcActivo #listaprocesosactivos').innerHTML='';
    //consultarElemento();//limpia residuos de visualización de elementos;

    var _parametros = {
        'codMarco':_CodMarco
    };

    $.ajax({
        url:   './app_proc/app_proc_consulta_procs.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg)
            {
                alert(_res.mg[_nm]);
            }
            //console.log(_res);
            _Proc=_res.data.Proc;
            _Instancias=_res.data.Instancias;
            _CapasDisponibles=_res.data.CapasDisponibles;
            
            console.log(_res.data.Instancias);
            
            listarInstancias();
        }
    });
}
consultarProc();





function guardaCampoLink(_this){
	
	_this.setAttribute('guarda','guardando');
	
    var _parametros = {
        'codMarco':_CodMarco,
		'idin':_this.getAttribute('idin'),
		'idco':_this.getAttribute('idco'),
		'campo':_this.getAttribute('campo'),
		'valor':_this.value
    };

    $.ajax({
        url:   './app_proc/app_proc_ed_guarda_campo_link.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg)
            {
                alert(_res.mg[_nm]);
            }
                        
            if(_res.res!='exito'){alert('error al guardar cambios');return;}
            
            _Instancias[_res.data.idin].componentes[_res.data.idco].campos[_res.data.campo].link=_res.data.valor;
            
            _str='#linkcampo[idin="'+_res.data.idin+'"][idco="'+_res.data.idco+'"][campo="'+_res.data.campo+'"]'
            console.log(_str);
            document.querySelector(_str).value=_res.data.valor;
            document.querySelector(_str).setAttribute('guarda','guardado');
        }
    });
}


function procesarReporte(_ruta,_idco,_avance,_encapa){
	_idproc=document.querySelector('#formProcActivo [name="idproc"]').value;
	_idinst=document.querySelector('#formProcActivo [name="idinst"]').value;
	document.querySelector('#formProcActivo').setAttribute('estado','procesando');
	
	var _parametros = {
        'codMarco':_CodMarco,
		'idproc':_idproc,
		'idinst':_idinst,
        'idco':_idco
    };
	
	_paddproc=_idproc.padStart(5, "0");
	_paddco=_idco.padStart(3, "0");
	
	$.ajax({
        url:   './app_proc/procesos/'+_paddproc+'/'+_paddco+'/'+_ruta,
        type:  'post',
        data: _parametros,
        success:  function (response){
			console.log('paso completo');
			document.querySelector('#formProcActivo').removeAttribute('estado');
            var _res = $.parseJSON(response);
            for(_nm in _res.mg)
            {
                alert(_res.mg[_nm]);
            }
                        
            if(_res.res!='exito'){alert('error al procesar');return;}
            
            _div=document.createElement('div');
            _div.setAttribute('class','reporte');
            document.querySelector('body').appendChild(_div);
            _cerr=document.createElement('a');
            _cerr.innerHTML='cerrar';
            _cerr.setAttribute('class','cerrar');
            _cerr.setAttribute('onclick','cerrar(this)');
            _div.appendChild(_cerr);
            
            _cont=document.createElement('div');
            _div.appendChild(_cont);
            _cont.innerHTML=_res.data.htmlstr;
            _div.setAttribute('class','reporte');
        }
    });		
}


function procesarGenerarComponente(_idco,_avance,_encapa,_alt){
	console.log('ini:'+_alt);
	if(_avance=='0'){		
		if(document.querySelector('#formProcActivo').getAttribute('estado')=='procesando'){
			alert('ya estamos procesando un Compoennte. en este momento no tenemos habilitada la opcion de generar una lista de cálculos ni ncálculos simultáneos.');
			return;
		}
		
		if(document.querySelector('#formProcActivo .componente[idco="'+_idco+'"] .estado').getAttribute('estado')=='vinculado'){
			if(!confirm('Este componente ya fue calculado de forma completa. \n ¿Segure de volver a calcularlo generando una nueva capa?')){
				return;
			}
		}		
	}
	
	
	_idproc=document.querySelector('#formProcActivo [name="idproc"]').value;
	_idinst=document.querySelector('#formProcActivo [name="idinst"]').value;
	document.querySelector('#formProcActivo').setAttribute('estado','procesando');
	
	var _parametros = {
        'codMarco':_CodMarco,
		'idproc':_idproc,
		'idinst':_idinst,
        'idco':_idco,
		'avance':_avance,
		'encapa':_encapa
    };
    
    
	
	_paddproc=_idproc.padStart(5, "0");
	_paddco=_idco.padStart(3, "0");
	
	
	_altstr= _alt.toString().padStart(2, "0");
	
	_url= './app_proc/procesos/'+_paddproc+'/'+_paddco+'/proc_genera_'+_altstr+'.php';
	
	$.ajax({
        url:   _url,
        type:  'post',
        data: _parametros,
        success:  function (response){
			console.log('paso completo');
			document.querySelector('#formProcActivo').removeAttribute('estado');
            var _res = $.parseJSON(response);
            for(_nm in _res.mg)
            {
                alert(_res.mg[_nm]);
            }
                        
            if(_res.res!='exito'){alert('error al procesar');return;}
            
            
            if(_res.data.estado=='completo'){
				alert('Procesamiento completo');
			}else{
				_porc=100 * (parseInt(_res.data.avance)/parseInt(_res.data.totalpasos));
				_porc=Math.round(_porc*100)/100;
				document.querySelector('#formProcActivo #avance').innerHTML=_porc+'%';
				console.log('nuevo paso:'+_res.data.idco+','+_res.data.avance+','+_res.data.encapa);
				
				if(_res.data.alternativa==undefined){
					_alt=1;
				}else{
					_alt=parseInt(_res.data.alternativa);
				}
				console.log(_alt);
				procesarGenerarComponente(_res.data.idco,_res.data.avance,_res.data.encapa,_alt);
			}
        }
    });	
}


function actualizarPermisos(){
    //repite consultas y cargas en caso de actualizarse los permisos por acceso de usuario registrado
    consultarTablas();
}

function consultarTablas(){
    document.querySelector('#menutablas #lista').innerHTML='';
    consultarElemento();//limpia residuos de visualización de elementos;
    var _parametros = {
        'selecTabla':_SelecTabla,
        'selecElemCod':_SelecElemCod,
        'selecElemId':_SelecElemId		
    };

    $.ajax({
        url:   'consulta_tablas.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg)
            {
                alert(_res.mg[_nm]);
            }
            //console.log(_res);
            _Tablas=_res.data.tablas;
            _TablasConf=_res.data.tablasConf;
            _cont=document.querySelector('#menutablas #lista');
            for(var _nn in _Tablas['est']){			
                _aaa=document.createElement('a');
                _aaa.innerHTML=_Tablas['est'][_nn];
                if(_TablasConf[_Tablas['est'][_nn]] != undefined){
                    _aaa.innerHTML+=' - '+_TablasConf[_Tablas['est'][_nn]].nombre_humano;
                }
                _aaa.setAttribute('tabla',_Tablas['est'][_nn]);
                _aaa.setAttribute('class','nombretabla');
                _aaa.setAttribute('onclick','mostrartabla(this)');
                _cont.appendChild(_aaa);

                if(_TablasConf[_Tablas['est'][_nn]].acceso>=3){
                    //boton cargar version
                    _aaa=document.createElement('a');
                    _aaa.innerHTML='<img src="./comun_img/editar.png" alt="editar">';
                    _aaa.title='subir una nueva versión';
                    _aaa.setAttribute('tabla',_Tablas['est'][_nn]);
                    _aaa.setAttribute('onclick','cargarAtabla(this)');
                    _cont.appendChild(_aaa);
                }

                if(_TablasConf[_Tablas['est'][_nn]].acceso>=3){
                    //boton configurar
                    _aaa=document.createElement('a');
                    _aaa.innerHTML='<img src="./comun_img/configurar.png" alt="configurar">';
                    _aaa.title='confiturar capa';
                    _aaa.setAttribute('tabla',_Tablas['est'][_nn]);
                    _aaa.setAttribute('onclick','configurartabla(this)');
                    _cont.appendChild(_aaa);
                }

                _aaa=document.createElement('a');

                _standarSHP="ows?service=WFS&version=1.0.0&request=GetFeature&maxFeatures=1000000&outputFormat=SHAPE-ZIP";
                _capaSHP="&typeName=geogec:"+_Tablas['est'][_nn];

                _aaa.setAttribute('onclick','descargarSHP(this,event)');

                _host="http://190.111.246.33:8080/geoserver/geoGEC/";

                _aaa.setAttribute('link',_host+_standarSHP+_capaSHP);
                _aaa.setAttribute('link',_host+_standarSHP+_capaSHP);//retiramos el recorte para la descarga


                _aaa.innerHTML='<img src="./comun_img/descargar.png" alt="descargar">';
                _aaa.setAttribute('tabla',_Tablas['est'][_nn]);
                _cont.appendChild(_aaa);

                _aaa=document.createElement('br');
                _cont.appendChild(_aaa);
            }

            if(_Est!=''){
                mostrartabla(document.querySelector('#lista > a.nombretabla[tabla="'+_Est+'"]'));
            }
        }
    });
}
//consultarTablas();


var _Linkeables={};


function consultarCapasLinkeables(){
	

    var _parametros = {
        'codMarco':_CodMarco
    };
    
    
    document.querySelector('#formlinkcapa #lista').innerHTML='';
	document.querySelector('#formlinkcapa').style.display='none';
	
    $.ajax({
        url:   './app_capa/app_capa_consultar_listado_linkeable.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg)
            {
                alert(_res.mg[_nm]);
            }
            
            _Linkeables=_res.data.linkeables;
            
            
            for(_n in _Linkeables){
				_d=_Linkeables[_n];
				_op=document.createElement('a');
				_op.innerHTML=_d.nombre;
				_op.setAttribute('onclick','elijeCapaLink("'+_n+'")');
				_op.title=_d.descripcion;				
				document.querySelector('#formlinkcapa #lista').appendChild(_op);
			}
			
			
			document.querySelector('#formlinkcapa').style.display='block';
		}
	})
}






function consultarCamposExternosLinkeables(){	

    var _parametros = {
        'codMarco':_CodMarco
    };
    
    _va_li_ca=document.querySelector('#vinculaciones input[name="link_capa"]').value;
    if(_va_li_ca==''){
		alert('para elegir un campo de vinculación en la capa destino antes debe elegir cual seá la capa destino');
		return;
	}
      
    
    document.querySelector('#formlinkcampoexterno #lista').innerHTML='';
	document.querySelector('#formlinkcampoexterno').style.display='none';
	
    $.ajax({
        url:   './app_capa/app_capa_consultar_listado_linkeable.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            
            _Linkeables=_res.data.linkeables;
            
             _va_li_ca=document.querySelector('#vinculaciones input[name="link_capa"]').value;
            
            for(_n in _Linkeables[_va_li_ca]){
				_d=_Linkeables[_va_li_ca][_n];
				
				if(_n.substring(0,8)!='nom_col_'){continue;}
				
				_op=document.createElement('a');
				_op.innerHTML=_d;
				_op.setAttribute('onclick','elijeCampoExternoLink("'+_n+'")');
				document.querySelector('#formlinkcampoexterno #lista').appendChild(_op);
				
			}
			
			document.querySelector('#formlinkcampoexterno').style.display='block';
		}
	})			
}

function mostrarCamposLocalesLinkeables(){	

    document.querySelector('#formlinkcampolocal #lista').innerHTML='';
	document.querySelector('#formlinkcampolocal').style.display='none';
	
            
	for(_n in _Capa){
		
		_d=_Capa[_n];
		
		if(_n.substring(0,8)!='nom_col_'){continue;}
		
		_op=document.createElement('a');
		_op.innerHTML=_d;
		_op.setAttribute('onclick','elijeCampoLocalLink("'+_n+'")');
		
		document.querySelector('#formlinkcampolocal #lista').appendChild(_op);	
	}
	document.querySelector('#formlinkcampolocal').style.display='block';
}


					
function cargarAtabla(_this){
    limpiarfomularioversion();
    document.getElementById('divCargaCapa').style.display='block';
    document.getElementById('divCargaCapa').setAttribute('tabla',_this.getAttribute('tabla'));
}


function mostrartabla(_this){	
    _lyrElemSrc.clear();
    //document.querySelector('#titulomapa').style.display='block';
    document.querySelector('#menuelementos').style.display='block';
    _tabla=_this.getAttribute('tabla');
    consultarElemento();//limpia datos ya consultados de elementos puntuales dentro de una tabla;
    document.querySelector('#titulomapa #tnombre').innerHTML=_tabla;
    if(_TablasConf[_tabla]!='undefined'){
        document.querySelector('#titulomapa #tnombre_humano').innerHTML=_TablasConf[_tabla].nombre_humano;
        document.querySelector('#titulomapa #tdescripcion').innerHTML=_TablasConf[_tabla].resumen;
    }

    _ExtraBaseWmsSource= new ol.source.TileWMS({
        url: 'http://190.111.246.33:8080/geoserver/geoGEC/wms',
        params: {
            'VERSION': '1.1.1',
            tiled: true,
            LAYERS: _tabla,
            STYLES: ''
        }
    });
    La_ExtraBaseWms.setSource(_ExtraBaseWmsSource);
    consultarCentroides(_this);
}

	
function consultarCentroides(_this){
    _parametros={
        'tabla': _this.getAttribute('tabla')
    };


    $.ajax({
        data: _parametros,
        url:   './consulta_centroides.php',
        type:  'post',
        success:  function (response){
            var _res = $.parseJSON(response);			
            console.log(_res);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='err'){
                
            } else {
                //cargaContrato();	
                _lyrCentSrc.clear();
                _cont=document.querySelector('#menuelementos #lista');
                _cont.innerHTML='';
                for(var _no in _res.data.centroidesOrden){
                    _nc=_res.data.centroidesOrden[_no];
                    _hayaux='no';						
                    _dat=_res.data.centroides[_nc];					
                    var format = new ol.format.WKT();				
                    var _feat = format.readFeature(_dat.geo, {
                        dataProjection: 'EPSG:3857',
                        featureProjection: 'EPSG:3857'
                    });
                    _feat.setId(_dat.id);
                    _feat.setProperties({
                        'nom':_dat.nom,
                        'cod':_dat.cod,
                        'id':_dat.id
                    });

                    _lyrCentSrc.addFeature(_feat);						
                    _lyrCent.setSource(_lyrCentSrc);

                    _MapaCargado='si';

                    _aaa=document.createElement('a');
                    _aaa.setAttribute('centid',_dat.id);
                    _aaa.setAttribute('onmouseover','resaltarcentroide(this)');
                    _aaa.setAttribute('onmouseout','desaltarcentroide(this)');
                    _aaa.setAttribute('cod',_dat.cod);
                    _aaa.innerHTML='<span class="nom">'+_dat.nom+"</span>"+'<span class="cod">'+_dat.cod+"</cod>";
                    _aaa.setAttribute('onclick','consultarElemento("0","'+_dat.cod+'","'+_res.data.tabla+'")');
                    _cont.appendChild(_aaa);
                }
                _ext= _lyrCentSrc.getExtent();
                mapa.getView().fit(_ext, { duration: 1000 });

                if(_Cod != ''){		
                    consultarElemento("0",_Cod,_Est);
                }
            }
        }
    });		
}

function resaltarcentroide(_this){
    var _src = _lyrCent.getSource();
    _centid=_this.getAttribute('centid');
    _feat=_src.getFeatureById(_centid);

    _feat.setStyle(_CentSelStyle);
    _pp=_feat.getProperties('nom');
    document.querySelector('#tseleccion').setAttribute('cod',_pp.cod);
    document.querySelector('#tseleccion').innerHTML=_pp.nom;
    document.querySelector('#tseleccion').style.display='inline-block';
}

function desaltarcentroide(_this){	
    var _src = _lyrCent.getSource();
    _centid=_this.getAttribute('centid');
    _feat=_src.getFeatureById(_centid);

    _feat.setStyle(_CentStyle);
    document.querySelector('#tseleccion').setAttribute('cod','');
    document.querySelector('#tseleccion').innerHTML='';
    document.querySelector('#tseleccion').style.display='none';
}

function descargarSHP(_this,_ev){	
    _ev.stopPropagation();
    _if=document.createElement('iframe');
    _this.appendChild(_if);

    _if.style.display='none';
    _if.onload = function() { alert('myframe is loaded'); }; 

    _im=document.createElement('img');
    //_this.appendChild(_im);
    _im.src='./comun_img/cargando.gif';

    _if.src=_this.getAttribute('link');
}

function consultarElemento(_idElem,_codElem,_tabla){
    document.querySelector('#menudatos #titulo').innerHTML='';
    document.querySelector('#menudatos #lista').innerHTML='';
    document.querySelector('#menudatos').removeAttribute('style');
    document.querySelector('#menuacciones #titulo').innerHTML='';
    document.querySelector('#menuacciones #lista').innerHTML='';
    document.querySelector('#menuacciones').removeAttribute('style');

    _elems = document.querySelectorAll('#menuelementos #lista a[cargado="si"]');
    if(_elems!=null){
    for(_nn in _elems){
            if(typeof _elems[_nn] != 'object'){continue;}
            _elems[_nn].removeAttribute('cargado');
    }
    }

    if(_codElem==null){return;}


    _parametros = {
            'id': _idElem,
            'cod': _codElem,
            'tabla':_tabla
    };

    $.ajax({
        data: _parametros,
        url:   './consulta_elemento.php',
        type:  'post',
        success:  function (response){
            var _res = $.parseJSON(response);
            console.log(_res);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='exito'){		
                _campocod=_res.data.tablasConf.campo_id_geo;
                _camponom=_res.data.tablasConf.campo_id_humano;

                document.querySelector('#menuacciones #titulo').innerHTML=_res.data.elemento.nombre;
                document.querySelector('#menuacciones #titulo').innerHTML="acciones disponibles";
                _lista=document.querySelector('#menuacciones #lista');

                for(_accnom in _res.data.tablasConf.acciones){
                    _accndata=_res.data.tablasConf.acciones[_accnom];

                    if(_res.data.elemento.accesoAccion[_accnom]>0){
                        document.querySelector('#menuacciones').style.display='block';
                        _li=document.createElement('a');
                        _li.setAttribute('href','./'+_accnom+'.php?cod='+_res.data.elemento[_campocod]);
                        _la=document.createElement('img');
                        _la.setAttribute('src','./comun_img/'+_accnom+'.png');
                        _la.setAttribute('alt',_accnom);
                        _la.setAttribute('title',_accndata.resumen);
                        _li.appendChild(_la);
                        _lista.appendChild(_li);
                    }
                }
                document.querySelector('#menudatos').style.display='block';

                document.querySelector('#menudatos #titulo').innerHTML=_res.data.elemento[_camponom];
                _lista=document.querySelector('#menudatos #lista');	
                for(var _nd in _res.data.elemento){
                    if(_nd == 'geo'){continue;}
                    if(_nd == 'accesoAccion'){continue;}
                    if(_nd == 'acceso'){continue;}
                    if(_nd == 'geotx'){continue;}
                    if(_nd == 'zz_obsoleto'){continue;}

                    _li=document.createElement('div');
                    _li.setAttribute('class','fila');
                    _la=document.createElement('label');
                    _la.setAttribute('class','variable');
                    _la.innerHTML=_nd+":";
                    _li.appendChild(_la);
                    _sp=document.createElement('div');
                    _sp.setAttribute('class','dato');
                    _sp.innerHTML=_res.data.elemento[_nd];
                    _li.appendChild(_sp);
                    _lista.appendChild(_li);
                }

                _lyrElemSrc.clear();
                var format = new ol.format.WKT();	
                var _feat = format.readFeature(_res.data.elemento.geotx, {
                    dataProjection: 'EPSG:3857',
                    featureProjection: 'EPSG:3857'
                });

                _feat.setId(_res.data.elemento.id);

                _feat.setProperties({
                    'nom':_res.data.elemento[_camponom],
                    'cod':_res.data.elemento[_campocod],
                    'id':_res.data.elemento.id
                });

                _lyrElemSrc.addFeature(_feat);

                _MapaCargado='si';


                document.querySelector('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').setAttribute('cargado','si');	

                _pe=$('#menuelementos #lista').offset().top;
                _sc=document.querySelector('#menuelementos #lista').scrollTop;
                console.log($('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').offset().top+_sc);

                $('#menuelementos #lista').animate({
                        scrollTop: ($('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').offset().top+_sc-_pe)
                 }, 2000);

                document.querySelector('#menudatos').style.display='block';

                _ext= _lyrElemSrc.getExtent();


                setTimeout(
                    function(){mapa.getView().fit(_ext, { duration: 1000 })},
                            200
                    );

                //generarItemsHTML();		
                //generarArchivosHTML();
            }else{
                alert('error dsfg');
            }
        }
    });	
}


// FUNCION COPIADA DE app_capa/app_capa_consultas
// editar de forma conjunta.

function descargarSHP(_idcapa){
	_boton=document.querySelector('.botonDescargaCapa[idcapa="'+_idcapa+'"]');
	
	if(_boton.getAttribute('estado')=='generandoshp'){alert('ya estamos generando la descarga de esta capa... paciencia.');return;}
	_boton.setAttribute('estado','generandoshp');
	
    var _parametros = {
        'codMarco':_CodMarco,
        'idcapa': _idcapa
    };		
	$.ajax({
        data: _parametros,
        url:   './app_capa/app_capa_generar_SHP_descarga.php',
        type:  'post',
        success:  function (response){
            var _res = $.parseJSON(response);
            console.log(_res);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='exito'){	
				descargarSHPzip(_res.data.idcapa);
			}
		}
	})	
}


function descargarSHPzip(_idcapa){
	
    var _parametros = {		
        'codMarco':_CodMarco,
        'idcapa': _idcapa
    };		
    
	$.ajax({
        data: _parametros,
        url:   './app_capa/app_capa_generar_SHPzip_descarga.php',
        type:  'post',
        success:  function (response){
            var _res = $.parseJSON(response);
            console.log(_res);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='exito'){	
				
				_boton=document.querySelector('.botonDescargaCapa[idcapa="'+_res.data.idcapa+'"]');
				_boton.setAttribute('estado','generandoshp');
   
   
				console.log('descarga:'+_res.data.descarga);
				var file_path = _res.data.descarga;
				var a = document.createElement('a');
				a.href = file_path;
				a.download = file_path.substr(file_path.lastIndexOf('/') + 1);
				a.download =_res.data.capa.nombre+'.zip';
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
				
			}
		}
	})	
}

function desvincularCapa(_idco,_idin){
	
	
	if(!confirm('¿Segure de desvincular la referencia al componte '+_idco+' para la instancia '+_idin+'?')){return;}
	
    var _parametros = {		
        'codMarco':_CodMarco,
        'idco': _idco,
        'idin':_idin
    };		
    
	$.ajax({
        data: _parametros,
        url:   './app_proc/app_proc_ed_desvincular_capa_de_componente.php',
        type:  'post',
        success:  function (response){
            var _res = $.parseJSON(response);
            //console.log(_res);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='exito'){	
				
				formularInstancia(_res.data.idin);
				
			}
		}
	})	
	
}
