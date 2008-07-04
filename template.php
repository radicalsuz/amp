<?php

$mod_id = isset( $_GET['temp'] ) && $_GET['temp'] ? intval( $_GET['temp']) : false;
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  
echo "<xml><item>content</item></xml>";
include("AMP/BaseFooter.php");  
?>
