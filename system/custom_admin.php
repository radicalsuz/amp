<?php
ob_start();
require("Connections/freedomrising.php");
$fileinfo= $dbcon->CacheExecute("Select * from custom where id = $file ");
$file = $fileinfo->Fields("file");

//declare vars
if ($fileinfo->Fields("mod_id")) {$mod_id = $fileinfo->Fields("mod_id");}
if ($fileinfo->Fields("modid")) {$modid = $fileinfo->Fields("modid");}
if ($_GET[modid]) {$modid = $_GET[modid]; }
if ($_GET[mod_id]) {$mod_id = $_GET[mod_id]; }

include("header.php"); 

include ($file);

include("footer.php");

?>
