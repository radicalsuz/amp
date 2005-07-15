<?php 
require_once( 'AMP/System/BaseTemplate.php' );

$template = & AMPSystem_BaseTemplate::instance();
print $template->outputFooter();

ob_end_flush();
?>
