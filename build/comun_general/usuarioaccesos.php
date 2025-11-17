<?php
/**
* realiza consulta del usuario registrado y sus niveles de acceso para esta seccion
*
* @param integer $Id es el id del presupuesto analizado. vacio significa: 'todos los presupuestos'
* @return array de presupuestos descrito más adelante.
*/
function usuarioaccesos(){
	global $Conec1, $CU;
	// paquete identificación de usuario
	//print_r($_SESSION);
	$UsuarioI = $_SESSION[$CU]['UsuarioI'];	
	if($UsuarioI != "" && $UsuarioI != "-1"){
			$query = "
				SELECT 
					usuarios.*,
					usuariosacceso.acceso,
					usuariosacceso.seccion
				FROM 
					usuarios.usuarios 
				LEFT JOIN 
					usuarios.usuariosacceso 
					ON usuariosacceso.id_p_usuarios_id_nombre = usuarios.id
				WHERE 
					usuarios.id='$UsuarioI' 
				ORDER BY 
					usuarios.id DESC
			";
			
		$ConUsu = mysql_query($query,$Conec1);
		echo mysql_error($Conec1);
		//$Usuario['N'] = mysql_result($ConUsu,0,'nombre');
		//$Usuario['Acc'] = mysql_result($ConUsu,0,'acceso');
		
		while($row=mysql_fetch_assoc($ConUsu)){
			$Usuario['N']=$row['nombre'];
			$Usuario['NA']=$row['nombre']." ".$row['apellido'];
			$Usuario['Acc'][$row['seccion']]=$row['acceso'];
			$Usuario['mail']=$row['mail'];			
			$Usuario['id']=$row['id'];		
		}		
		$query="
			SELECT 
				* 
				FROM 
				usuarios.USUpreferencias 
				WHERE id_p_usuarios_id_nombre='$UsuarioI' ORDER BY id DESC
		";
		$ConUSUpref = mysql_query($query, $Conec1);
		echo mysql_error($Conec1);
		$Usuario['Preferencias'] =  mysql_fetch_assoc($ConUSUpref);
	}
	
	// FIN* paquete identificación de usuario *FIN
	
return $Usuario;	
}
	
