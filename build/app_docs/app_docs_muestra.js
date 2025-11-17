function generarArchivosHTML(){
	
    if(Object.keys(_Items[0].archivos).length>0){
        for(_na in _Items[0].archivos){
            _dat=_Items[0].archivos[_na];
            _Docs[_dat.id]=_dat;
            console.log(_dat);
            _aaa=document.createElement('a');
            _aaa.innerHTML=_dat.nombre;
            _aaa.setAttribute('draggable',"true");
            _aaa.setAttribute('onclick','editarD(event,this)');
            _aaa.setAttribute('ondragstart',"dragFile(event)");
            _aaa.setAttribute('idfi',_dat.id);
            _aaa.setAttribute('class','archivo');					
            document.getElementById('listadoaordenar').appendChild(_aaa);
            _aasub=document.createElement('a');
            
            _aasub.setAttribute('download',_dat.nombre);           
            _aasub.setAttribute('href',_dat.archivo);
            _aasub.setAttribute('target','blank');
            _aasub.setAttribute('onclick','event.stopPropagation()');
            _aasub.innerHTML='<img src="./comun_img/descargar_archivo.png" alt="descargar">';
            _aaa.appendChild(_aasub);
        }			
    }
}

function generarArchivoLinksHTML(){
    if(Object.keys(_Items[0].archivolinks).length>0){
        for(_na in _Items[0].archivolinks){
            _dat=_Items[0].archivolinks[_na];
            _DocLinks[_dat.id]=_dat;
			
			_aaa=document.createElement('a');

            _aaa.innerHTML=_dat.nombre;
            
            _aaa.setAttribute('target','_blank');
            _aaa.setAttribute('onclick','editarLink(event,this)');
            _aaa.setAttribute('draggable',"true");
            _aaa.setAttribute('ondragstart',"dragLinkurl(event)");
            _aaa.setAttribute('idfi',_dat.id);
            _aaa.setAttribute('class','archivo');
            document.getElementById('listadoaordenar').appendChild(_aaa);
            
            _aasub=document.createElement('a');
            _aasub.setAttribute('onclick','event.stopPropagation()');
            _aasub.setAttribute('href',_dat.url);
            _aasub.setAttribute('target','blank');
            _aasub.innerHTML='<img src="./comun_img/dirigir_link.png" alt="ir a link">';
            _aaa.appendChild(_aasub);
            
        }			
    }
}


function generarItemsHTML(){
    //genera un elemento html por cada instancia en el array _Items
    for(_nO in _Orden.psdir){

        _ni=_Orden.psdir[_nO];

        _dat=_Items[_ni];
        _clon=document.querySelector('#modelos .item').cloneNode(true);

        _clon.setAttribute('idit',_dat.id);
        _clon.setAttribute('zz_sis',_dat.zz_sis);
        _clon.setAttribute('publica',_dat.publica);
		
        if(_dat.nombre==null){_dat.nombre='- caja sin nombre -';}

        _clon.querySelector('h3').innerHTML=_dat.nombre;
        if(_dat.descripcion==null){_dat.descripcion='- caja sin descripción -';}
        _clon.querySelector('p').innerHTML=_dat.descripcion;
        _clon.setAttribute('nivel',"1");

        for(_na in _dat['ordenarchivos']){
			_ida=_dat['ordenarchivos'][_na];
            _dar=_dat['archivos'][_ida];
            _Docs[_dar.id]=_dar;
            
            console.log(_dar.nombre);
            _aa=document.createElement('a');
            _aa.innerHTML=_dar.nombre;
            _aa.setAttribute('draggable',"true");
            _aa.setAttribute('onclick','editarD(event,this)');
            _aa.setAttribute('ondragstart',"dragFile(event)");
            _aa.setAttribute('idfi',_dar.id);
            _aa.setAttribute('class','archivo');					
            _clon.querySelector('.documentos').appendChild(_aa);
            _aasub=document.createElement('a');
            
            _aasub.setAttribute('download',_dar.nombre);           
            _aasub.setAttribute('href',_dar.archivo);
            _aasub.setAttribute('target','blank');
            _aasub.setAttribute('onclick','event.stopPropagation()');
            _aasub.innerHTML='<img src="./comun_img/descargar_archivo.png" alt="descargar">';
            _aa.appendChild(_aasub);
        }
        
        for(_na in _dat['archivolinks']){
            _dar=_dat['archivolinks'][_na];
            _DocLinks[_dar.id]=_dar;
            _aa=document.createElement('a');

            _aa.innerHTML=_dar.nombre;
            
            _aa.setAttribute('target','_blank');
            _aa.setAttribute('onclick','editarLink(event,this)');
            _aa.setAttribute('draggable',"true");
            _aa.setAttribute('ondragstart',"dragLinkurl(event)");
            _aa.setAttribute('idfi',_dar.id);
            _aa.setAttribute('class','archivo');
            _clon.querySelector('.documentos').appendChild(_aa);
            _aasub=document.createElement('a');
            _aasub.setAttribute('href',_dar.url);
            _aasub.setAttribute('target','blank');
            _aasub.setAttribute('onclick','event.stopPropagation()');
            
            _aasub.innerHTML='<img src="./comun_img/dirigir_link.png" alt="ir a link">';
            _aa.appendChild(_aasub);
        }

        document.querySelector('#contenidoextenso > .hijos').appendChild(_clon);
    }

    //anida los itmes genereados unos dentro de otros
    for(_nO in _Orden.psdir){
        _ni=_Orden.psdir[_nO];
        _el=document.querySelector('#contenidoextenso > .hijos > .item[idit="'+_Items[_ni].id+'"]');
		
        if(_Items[_ni].id_p_ref_02_pseudocarpetas!='0'){
            //alert(_Items[_ni].id_p_ESPitems_anidado);
            _dest=document.querySelector('#contenidoextenso > .hijos .item[idit="'+_Items[_ni].id_p_ref_02_pseudocarpetas+'"] > .hijos');
            _niv=_dest.parentNode.getAttribute('nivel');
            _niv++;
            _el.setAttribute('nivel',_niv);
                _dest.appendChild(_el);
            }
        }
        _itemscargados=document.querySelectorAll('#contenidoextenso > .hijos .item[zz_sis=""]');
    for(_nni in _itemscargados){
        if(typeof _itemscargados[_nni]=='object'){
                _esp=document.createElement('div');				
                _esp.setAttribute('class','medio');
                _esp.innerHTML='<div class="submedio"></div>';
                _esp.setAttribute('ondragover',"allowDrop(event,this);resaltaHijos(event,this)");
                _esp.setAttribute('ondragleave',"desaltaHijos(this)");
                _esp.setAttribute('ondrop',"drop(event,this)");  

                _itemscargados[_nni].parentNode.insertBefore(_esp, _itemscargados[_nni]);
        }
    }
}

