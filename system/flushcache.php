<?php
$mod_name = "system";
require("Connections/freedomrising.php");
include ("header.php"); 

system("rm -f `find ".$ADODB_CACHE_DIR." -name adodb_*.cache`"); 
//$dbcon->CacheFlush() or DIE($dbcon->ErrorMsg()); //flushes adodb cache
//$dbcon->CacheFlush();

echo "The cache has beeen reset";
include ("footer.php");
?>
  