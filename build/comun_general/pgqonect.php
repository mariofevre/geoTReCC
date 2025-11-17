<?php

include_once("./_config_acc/claveunica.php");
include_once('./_config_acc/db_settings.php');


if (!isset($_SESSION[$CU]['db_settings'])){	
	$_SESSION[$CU]['db_settings'] = new ApplicationSettings();	
}
$_SESSION[$CU]['db_settings'] = new ApplicationSettings();	

$set=$_SESSION[$CU]['db_settings'];

$ConecSIG = pg_connect("host=".$set->DATABASE_HOST." dbname=".$set->DATABASE_NAME." user=".$set->DATABASE_USERNAME." password=".$set->DATABASE_PASSWORD." port=5432")or die('connection failed');
echo pg_last_error($ConecSIG);	
