<?php

require("AMP/System/Base.php");
require("AMP/System/BaseTemplate.php");

$template = &new AMPSystem_BaseTemplate();
$template->setToolName( 'system' );

$flush_command = "rm -f `find ". AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . 'cache' ." -name adodb_*.cache`"; 

system($flush_command);
//$dbcon->CacheFlush() or DIE($dbcon->ErrorMsg()); //flushes adodb cache
//$dbcon->CacheFlush();

$script = "
<script type = 'text/javascript'>
//<!--
history.go(-1);
alert('The cache has been reset');
//-->
</script>";

print $template->outputHeader();
print "The cache has been reset";
print $script;
print $template->outputFooter();

?>
  
