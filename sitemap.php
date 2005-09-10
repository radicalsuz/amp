<?php
$intro_id = 13;
require_once("AMP/BaseDB.php");
require_once("AMP/BaseTemplate.php");
require_once("AMP/BaseModuleIntro.php");  
require_once('Connections/menu.class.php');

$obj = &new Menu();
echo $obj->print_full_menu_tree($MX_top); 

require_once("AMP/BaseFooter.php"); 
?>
