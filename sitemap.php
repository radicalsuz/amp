<?php
$mod_id = 13;
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  

echo $obj->print_full_menu_tree($MX_top); 
include("AMP/BaseFooter.php"); 
?>
