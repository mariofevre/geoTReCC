<?php 
    header("Cache-control: private");
    include_once("./_config_acc/claveunica.php");
    include('./_config_acc/db_settings.php');	
	
	if (session_status() === PHP_SESSION_NONE) {session_start();}
    if(!isset($_SESSION)){$_SESSION[$CU]=array();}
    
	include('./comun_general/pgqonect.php');	
	include_once('./comun_general/cadenas.php');				
	include_once('./comun_general/fechas.php');		


