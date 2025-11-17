<?php 
    //$Path = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";
    header("Cache-control: private");
    include_once("./_config_acc/claveunica.php");
    include('./_config_acc/db_settings.php');	
	
    if(!isset($_SESSION)) { session_start(); $_SESSION[$CU]=array();}
    
	include('./comun_general/pgqonect.php');	
	include_once('./comun_general/cadenas.php');				
	include_once('./comun_general/fechas.php');		


