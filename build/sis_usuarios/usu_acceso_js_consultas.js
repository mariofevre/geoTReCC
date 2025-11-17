

function actualizarAccesos(){
	$.ajax({
		//data: _parametros,
		url:   './sis_usuarios/acc_consulta_ajax.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);			
			console.log(_res);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='err'){
			}else{
				//cargaContrato();	
				_form=document.querySelector('form#eliminarpermiso');
				_form.innerHTML='';
				
				for(_po in _res.data.permisosOrden){
					_pi = _res.data.permisosOrden[_po];
					_pdat=_res.data.permisos[_pi];
					
					_fil=document.createElement('div');
					_fil.setAttribute('accid',_pi);
					if(_pdat.zz_borrada=='1'){
						_fil.setAttribute('borrada','si');
					}
					_aaa=document.createElement('span');
					_aaa.setAttribute('id','nom');
					_aaa.innerHTML=_pdat.U_nom
					_fil.appendChild(_aaa);		
					
					_aaa=document.createElement('span');
					_aaa.setAttribute('id','tab');
					_aaa.innerHTML=_pdat.tabla
					_fil.appendChild(_aaa);		
					
					_aaa=document.createElement('span');
					_aaa.setAttribute('id','ele');
					_aaa.innerHTML=_pdat.elemento
					_fil.appendChild(_aaa);		
					
					_aaa=document.createElement('span');
					_aaa.setAttribute('id','acc');
					_aaa.innerHTML=_pdat.accion
					_fil.appendChild(_aaa);		
					
					_aaa=document.createElement('span');
					_aaa.setAttribute('id','niv');
					_aaa.innerHTML=_pdat.nivel
					_fil.appendChild(_aaa);		
		
					if(_pdat.zz_borrada!='1'){
						_aaa=document.createElement('input');
						_aaa.setAttribute('type','submit');
						_aaa.setAttribute('onclick','revocarPermiso(event,this)');
						_aaa.value='X revocar';
						_aaa.innerHTML=_pdat.nivel
						_fil.appendChild(_aaa);		
					}
												
					_form.appendChild(_fil);			
				}
			}
		}
	})			
}
	
	
	function consultarUsuaries(){
	_parametros = {
		'codMarco':_CodMarco,	
		'accion':'app_rele'
	}
    _cn = consultasPHP_nueva('./sis_usuarios/acc_consulta_compas.php');
	$.ajax({
        url:   './sis_usuarios/acc_consulta_compas.php',
        type:  'post',
        data: _parametros,
        beforeSend: function(request, settings) { 
			
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
            if(_res.res!='exito'){
            	alert('error al consultar la base de datos');
            	return;
            }
            _DataUsuaries=_res.data;
        }
 	});	
}
