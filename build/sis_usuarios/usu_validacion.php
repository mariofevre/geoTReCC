<?php 

function validarUsuario(){
	global $ConecSIG;
	global $_POST;
	
	
	$USU['acc']['general']['general']['general']=0;

	
	$query="
		SELECT 
			codigo
		FROM 
			geogec.est_02_marcoacademico
		WHERE
			zz_accesolibre = '1'
	";
	$ConsultaUsu = pg_query($ConecSIG, $query);
	$fila=pg_fetch_assoc($ConsultaUsu);
	
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]=utf8_encode('error: '.pg_last_error($ConecSIG));
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);	
	}
	while($fila=pg_fetch_assoc($ConsultaUsu)){
		$USU['acc']['est_02_marcoacademico'][$fila['codigo']]['general']='2';
	}
	

	
	if(!isset($_SESSION["geogec"])){
		return $USU;
	}
	
	if(!isset($_SESSION["geogec"]["usuario"])){
		$_SESSION["geogec"]["usuario"]['id']='-1';
		return $USU;
	}
	
	if(!isset($_SESSION["geogec"]["usuario"]['id'])){
		$_SESSION["geogec"]["usuario"]['id']='-1';
		return $USU;
	}
		
	if($_SESSION["geogec"]["usuario"]['id']<1){
		return $USU;
	}
	
	
	$query="
		SELECT id, id_p_sis_usu_registro,tabla, elemento, accion, nivel
		FROM 
			geogec.sis_usu_accesos
		WHERE  
			id_p_sis_usu_registro='".$_SESSION["geogec"]["usuario"]['id']."'
			AND
			zz_borrada=0
		
		";
		
	$ConsultaUsu = pg_query($ConecSIG, $query);
	
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]=utf8_encode('error: '.pg_last_error($ConecSIG));
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);	
	}	

	if(pg_num_rows($ConsultaUsu)==0){
		$USU['acc']['general']['general']['general']=0;
	}

	while($fila=pg_fetch_assoc($ConsultaUsu)){
		$USU['acc'][$fila['tabla']][$fila['elemento']][$fila['accion']]=$fila['nivel'];
	}



	$query="
		SELECT 
			id,
			nombre,
			nombre_oficial,
			codigo,
			id_sis_versiones,
			zz_accesolibre,
			ST_AsText(geo) as geotx
		FROM 
			geogec.est_02_marcoacademico
			where
			zz_obsoleto='0'
			
	";
	$ConsultaMarcos = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]=utf8_encode('error: '.pg_last_error($ConecSIG));
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);	
	}
	
	
	while($fila=pg_fetch_assoc($ConsultaMarcos)){
		
		$USU['marcos'][$fila['codigo']]=$fila;
		$USU['marcos'][$fila['codigo']]['acc']['general']=$USU['acc']['general']['general']['general'];
		$USU['marcos'][$fila['codigo']]['maxacc']=$USU['acc']['general']['general']['general'];
		
		if(isset($USU['acc']['est_02_marcoacademico'][$fila['codigo']])){
			$USU['marcos'][$fila['codigo']]['acc']=$USU['acc']['est_02_marcoacademico'][$fila['codigo']];
			$USU['marcos'][$fila['codigo']]['maxacc']=max($USU['marcos'][$fila['codigo']]['maxacc'],$USU['acc']['est_02_marcoacademico'][$fila['codigo']]);
		}
		
		
		if($USU['acc']['general']['general']['general']>0){
			$USU['marcos'][$fila['codigo']]['acc']=$USU['acc']['general']['general'];
			$USU['marcos'][$fila['codigo']]['maxacc']=max($USU['marcos'][$fila['codigo']]['maxacc'],$USU['acc']['general']['general']['general']);
		}
	}
	
	//print_r( $_SESSION["geogec"]["usuario"]);
	
	
	if($_SESSION["geogec"]["usuario"]['ic_p_est_02_marcoacademico'] != ''){
		$arr=Array('acc'=> Array('general'=> '3'));		
		$USU['marcos'][$_SESSION["geogec"]["usuario"]['ic_p_est_02_marcoacademico']]=$arr;	//usuario externo, acceso total a un solo marco		
		$USU['acc']['est_02_marcoacademico'][$_SESSION["geogec"]["usuario"]['ic_p_est_02_marcoacademico']]['general']='3';
	}
	

	//niveles de acceso
	// 3:  equipo de dearrollo GEC
	// 2:  colaboradores GEC
	// 1:  colaboradores externos
	// 0:  sin privilegios
		
	return $USU;
	 	
}

