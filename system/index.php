<?php

$modid=38;
require_once("Connections/freedomrising.php");

$index_user_settings = $dbcon->GetAssoc("Select id, system_home from users where name = ".$dbcon->qstr($_SERVER['REMOTE_USER']));

if (isset($index_user_settings['system_home'])&&$index_user_settings['system_home']!='') {
	header('Location: '.$index_user_settings['system_home']);
} else {
	header('Location: articlelist.php');		
}
    
include ("footer.php"); 
?>