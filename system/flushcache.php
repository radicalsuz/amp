<?php
  require("Connections/freedomrising.php");?>
  <?php  include ("header.php"); ?>
      <?  
	  
	  system("rm -f `find ".$ADODB_CACHE_DIR." -name adodb_*.cache`"); 
 //$dbcon->CacheFlush() or DIE($dbcon->ErrorMsg()); //flushes adodb cache
 //$dbcon->CacheFlush();
 ?>
      The cache has beeen reset 
      <?php  include ("footer.php");?>
  