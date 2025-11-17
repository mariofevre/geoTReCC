
function cargaAccesos(){
	_parametros = {
        'panid': _PanId
    };
    $.ajax({
        url:   './PAN/PAN_consulta_acceso.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
			_res = PreprocesarRespuesta(response);
			_Acc=_res.data.Acc;
			if(_Acc[0][0]=='administrador'||_Acc[0][0]=='editor'){
				_Habilitadoedicion='si';
			}
			consultarUsuarios();
        }
    })
}


function consultarUsuarios(){
	_parametros = {
	'zz_AUTOPANEL': _PanId
	};
	
	$.ajax({
    url:   './PAN/PAN_usuarios_consulta.php',
    type:  'post',
    data: _parametros,
    error: function (response){alert('error al intentar contatar el servidor');},
    success:  function (response){
        var _res = $.parseJSON(response);
        for(_nm in _res.mg){alert(_res.mg[_nm]);}
        if(_res.res!='exito'){alert('error durante la consulta en el servidor');return}
        
       	_DatosUsuarios=_res.data.usuarios;
       	llamarElementosIniciales();
       	
       	//document.querySelector('form#seguimiento select[name="id_p_usuarios_responsable"]').innerHTML='<option value="">- elegir -</option>';
       	//document.querySelector('form#accion select[name="id_p_usuarios_responsable"]').innerHTML='<option value="">- elegir -</option>';
       	/*
       	for(_nu in _DatosUsuarios.delPanelOrden){
       		_idusu = _DatosUsuarios.delPanelOrden[_nu];
       		_op=document.createElement('option');
       		_op.innerHTML=_DatosUsuarios.delPanel[_idusu].nombreusu;
       		_op.value=_idusu;
       		document.querySelector('form#seguimiento select[name="id_p_usuarios_responsable"]').appendChild(_op);
       		document.querySelector('form#accion select[name="id_p_usuarios_responsable"]').appendChild(_op.cloneNode(true));
       	}
		*/
		
		if(_DatosGrupos[0]!=undefined){
			//cargarPlan('','',''); 
		}
   }
   });
}



function consultarGrupos(){
    var parametros = {
    };			
    $.ajax({
        data:  parametros,
        url:   './PAN/PAN_grupos_consulta.php',
        type:  'post',
        error:   function (response) {alert('error al contactar el servidor');},
        success:  function (response) {
            //procesarRespuestaDescripcion(response, _destino);
            
            
            var _res = $.parseJSON(response);
            //console.log(_res);
            
            for(_nm in _res.mg){alert(_res.mg[_nm]);}
            for(_na in _res.acc){
                if(_res.acc[_na]=='loc'){window.location.assign(_res.loc);}
            }                
            
            if(_res.res=='exito'){
                _DatosGrupos=_res.data.grupos;
                _DatosGruposCargado='si';
                llamarElementosIniciales();
           
		        if(_DatosUsuarios.delPanel!=undefined){
					consultarListado(); 
	  			}
            }
        }
    });
}

function consultarCategorias(){
	var parametros = {
        'panid': _PanId,
     };
	 $.ajax({
        data:  parametros,
        url:   './app_plae/app_plae_consulta_categorias.php',
        type:  'post',
        error: function (response){alert('error al contactar al servidor');},
        success:  function (response) {
            var _res = PreprocesarRespuesta(response);
            //console.log(_res);
            
            
            _DatosCategorias=_res.data.categorias;
            _DatosCategoriasCargado='si';
           
           	cargarPlan('','','');
           	 
            _div=document.querySelector('#paquetecategorias #tiposcategorias #estandar');
            _div.innerHTML='';
            
            for(_idest in _DatosCategorias.estandar){
            	_dat=_DatosCategorias.estandar[_idest];
            	_aa=document.createElement('a');
            	_aa.innerHTML=_dat.codigo+': '+_dat.nombre;
            	_aa.setAttribute('idest',_idest);
            	_aa.setAttribute('onclick','crearCategoria(this.getAttribute("idest"),_DatosCategorias.estandar[this.getAttribute("idest")].nombre);');
            	_aa.setAttribute('onmouseover','describirEstandar(this.getAttribute("idest"))');
            	_aa.setAttribute('onmouseout','limpiarEstandar()');
            	_div.appendChild(_aa);
            }
        }
  });
        	
}




function describirEstandar(_idest){
	console.log(_idest);
	
	_dat=_DatosCategorias.estandar[_idest];

	
	_div=document.querySelector('#paquetecategorias #tiposcategorias #descripcion');
	_div.innerHTML ='<h4>'+_dat.codigo+'</h4>';
	_div.innerHTML+='<h4>'+_dat.nombre+'</h4>';
	_div.innerHTML+='<p>'+_dat.funcionamiento+'</p>';
}

function limpiarEstandar(){
	_div=document.querySelector('#paquetecategorias #tiposcategorias #descripcion');
	_div.innerHTML='';
}


